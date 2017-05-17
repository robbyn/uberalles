gotaxi = {
    positionAvailable: false,
    latitude: 0,
    longitude: 0,
    busy: false,
    requestsTimestamp: 0,
    displayedRequests: [],
    ignored: {},
    msg: {},

    dms: function(d) {
        var r = "";
        if (d < 0) {
            d = -d;
            r = "-";
        }
        var v = Math.floor(d);
        r += v.toString() + "°";
        d = (d-v)*60;
        v = Math.floor(d);
        if (v < 10) {
            r += "0";
        }
        r += v.toString() + "'";
        d = (d-v)*60;
        v = Math.round(d);
        if (v < 10) {
            r += "0";
        }
        r += v.toString() + '"';
        return r;
    },

    playSound: (function() {
        var sounds = {
            newreq: new Howl({urls: ["med/newreq.mp3", "med/newreq.ogg"]}),
            canreq: new Howl({urls: ["med/canreq.mp3", "med/canreq.ogg"]}),
            gotit: new Howl({urls: ["med/gotit.mp3", "med/gotit.ogg"]})
        };
        return function(name) {
            var sound = sounds[name];
            if (sound && sound.play) {
                sound.play();
            }
        };
    })(),

    refreshList: function() {
        $("#req-list").listview("refresh", true);
        if ($("#req-list li").size() === 0) {
            $("#list-empty").show();
        } else {
            $("#list-empty").hide();
        }
    },

    cleanupList: function() {
        var req, list = gotaxi.displayedRequests, i = list.length;
        while (i > 0) {
            --i;
            req = list[i];
            if (req.sts === 'can' || req.sts === 'don') {
                list.splice(i, 1);
            }
        }
        gotaxi.doRefresh();
    },

    updateRequests: function(timestamp, from) {
        var to = gotaxi.displayedRequests, prevLength = to.length,
                prevIgnored = gotaxi.ignored, newIgnored = {};
        $.each(to, function(index, req) {
            req.sts = 'can';
        });
        $.each(from, function(index, req) {
            var i = 0, ttl;
            while (i < to.length && to[i].id !== req.id) {
                ++i;
            }
            if (prevIgnored[req.id]) {
                newIgnored[req.id] = true;
            } else {
                to[i] = req;
                if (req.sts === 'req') {
                    ttl = 1000*(req.ctm + 24 - timestamp);
                    if (ttl < 0) {
                        ttl = 0;
                    }
                    if (ttl < gotaxi.refreshPeriod) {
                        console.log("time to live for " + req.id +
                                ": " + ttl + "ms");
                        setTimeout(function() {
                            console.log("Mark as expired " + req.id);
                            gotaxi.setRequestStatus(req.id, 'can');
                        }, ttl);
                    }
                }
            }
        });
        gotaxi.ignored = newIgnored;
        if (to.length > prevLength) {
            gotaxi.playSound("newreq");
        }
    },

    setRequestStatus: function(id, sts) {
        $.each(gotaxi.displayedRequests, function(index, req) {
            if (req.id === id) {
                if (sts === 'acc') {
                    req.acd = true;
                } else {
                    req.sts = sts;
                }
                gotaxi.doRefresh();
                return false;
            }
        });
    },

    updateList: function() {
        if (!gotaxi.positionAvailable) {
            console.log("Position unavailable, can't refresh list");
        } else if (gotaxi.busy) {
            console.log("Overrun: a refresh is already in progress");
        } else {
            console.log("Start refresh list");
            $.ajax({
                url: "ws/getrequests.php",
                data: {lat: gotaxi.latitude, lng: gotaxi.longitude},
                dataType: 'json'
            }).done(function (data) {
                gotaxi.busy = false;
                gotaxi.updateRequests(data.ts, data.dt);
                gotaxi.requestsTimestamp = data.ts;
                gotaxi.doRefresh();
            }).fail(function() {
                gotaxi.busy = false;
            });
            gotaxi.busy = true;
        }
    },

    doRefresh: function() {
        var prevRequest = gotaxi.request, data = gotaxi.displayedRequests;
        console.log("Requests displayed: " + data.length);
        gotaxi.request = null;
        $("#req-list").empty();
        $.each(data, function(index, req) {
            if (!gotaxi.ignored[req.id]) {
                gotaxi.processRequest(req);
                if (req.sts === 'att') {
                    gotaxi.request = req;
                }
            }
        });
        gotaxi.refreshList();
        if (gotaxi.request && !prevRequest) {
            gotaxi.playSound("gotit");
            changePage("#done");
        } else if (!gotaxi.request && prevRequest) {
            gotaxi.playSound("canreq");
            changePage("#list");
            $("#list").trigger("canceled");
        }
    },

    processRequest: function(req) {
        var html = gotaxi.msg.listRequest(req);
        $("#req-list").append(html);
    },

    acceptRequest: function(id) {
        console.log("Accept request " + id);
        $.ajax({
            url: "ws/accept.php",
            data: {id: id},
            dataType: 'json'
        }).done(function (data) {
            if (data.res) {
                gotaxi.setRequestStatus(id, 'acc');
            } else {
                $("#error-msg").text("Trop tard");
                changePage("#error", {reverse: true});
            }
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error", {reverse: true});
        });
    },

    unacceptRequest: function(id) {
        console.log("Unaccept request " + id);
        $.ajax({
            url: "ws/unaccept.php",
            data: {id: id},
            dataType: 'json'
        }).done(function (data) {
            if (data.res) {
                gotaxi.ignoreRequest(id);
            } else {
                $("#error-msg").text("Trop tard");
                changePage("#error", {reverse: true});
            }
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error", {reverse: true});
        });
    },

    ignoreRequest: function(id) {
        console.log("Ignore request " + id);
        gotaxi.ignored[id] = true;
        gotaxi.setRequestStatus(id, 'can');
        gotaxi.cleanupList();
    }
};

$(function() {
    Handlebars.registerHelper("requestClass", function(req) {
        if (req.sts === 'att') {
            return "assigned";
        } else if (req.sts === 'can') {
            return "expired";
        } else if (req.sts === 'don') {
            return "expired";
        } else if (req.acd) {
            return "accepted";
        } else if (req.sts === 'req') {
            return "active";
        } else {
            return "inactive";
        }
    });
    Handlebars.registerHelper("requestMins", function(req) {
        return Math.round(req.etm/60);
    });
    Handlebars.registerHelper("isIgnorable", function(req) {
        if (req.sts === 'att'|| req.sts === 'can' || req.sts === 'don') {
            return false;
        } else if (req.acd || req.sts === 'req') {
            return true;
        } else {
            return false;
        }
    });
    $('script[type="text/x-handlebars-template"]').each(function() {
        var $this = $(this);
        console.log("Compiling handlebars template: " + $this.attr("id"));
        gotaxi.msg[$this.attr("id")] =
                Handlebars.compile($this.html());
    });

    $("#menu").enhanceWithin().panel();
});

$(window).on('beforeunload', function() {
    // stop geolocation watch
    var id = gotaxi.watchId;
    if (id) {
        delete gotaxi.watchId;
        console.log("clearWatch");
        navigator.geolocation.clearWatch(id);
    }
    id = gotaxi.timerId;
    if (id) {
        clearInterval(id);
        delete gotaxi.timerId;
    }
});

$(document).on("panelbeforeopen", "#menu", function() {
    var page = $("body").pagecontainer("getActivePage").attr("id");
    $(this).find("li a").removeClass("current-page");
    $(this).find("li a[href='#" + page + "']").addClass("current-page");
    if (gotaxi.request) {
        $(".menu-current").removeClass("ui-disabled");
    } else {
        $(".menu-current").addClass("ui-disabled");
    }
});

// list page events
$(document).on("pagecreate", "#list", function() {
    console.log("pagecreate#list");
    var canceled = false, shown = false;

    $(this).on("beforeshow", function(e) {
        if (!gotaxi.watchId) {
            gotaxi.watchId = navigator.geolocation.watchPosition(function(position) {
                gotaxi.latitude = position.coords.latitude;
                gotaxi.longitude = position.coords.longitude;
                gotaxi.accuracy = position.coords.accuracy;
                gotaxi.altitude = position.coords.altitude;
                gotaxi.positionAvailable = true;
                var coords = gotaxi.dms(gotaxi.latitude) + "," +
                        gotaxi.dms(gotaxi.longitude) + " (\u00B1" +
                        Math.round(gotaxi.accuracy) + "m)";
                $("#list-coords").text(coords);
                console.log("Position available: " + coords + " " +
                        new Date().toString());
            }, function(error) {
                console.log("GPS error: " + error.code);
            }, {
                enableHighAccuracy: true,
                maximumAge: 0
            });

            gotaxi.updateList();
            gotaxi.timerId = setInterval(function() {
                gotaxi.updateList();
            }, gotaxi.refreshPeriod);
        }
    });

    $(this).on("show", function() {
        console.log("List page shown");
        gotaxi.busy = false;
        gotaxi.cleanupList();
        gotaxi.doRefresh();
        if (canceled) {
            canceled = false;
            $("#list-can").popup("open");
        }
        shown = true;
    });

    $(this).on("hide", function() {
        console.log("List page hidden");
        shown = false;
    });

    $(this).on("canceled", function() {
        if (shown) {
            $("#list-can").popup("open");
        } else {
            canceled = true;
        }
    });

    $("#req-list").on("click", "li.active a.ok", function() {
        console.log("Active click");
        if (!gotaxi.request) {
            var li = $(this).parentsUntil("ul","li");
            var id = parseInt(li.attr("id").substring(1));
            gotaxi.acceptRequest(id);
        }
    });

    $("#req-list").on("click", "li.active a.ignore", function() {
        console.log("Active ignore click");
        if (!gotaxi.request) {
            var li = $(this).parentsUntil("ul","li");
            var id = parseInt(li.attr("id").substring(1));
            gotaxi.ignoreRequest(id);
        }
    });

    $("#req-list").on("click", "li.accepted a.ignore", function() {
        console.log("Accepted ignore click");
        if (!gotaxi.request) {
            var li = $(this).parentsUntil("ul","li");
            var id = parseInt(li.attr("id").substring(1));
            gotaxi.unacceptRequest(id);
        }
    });

    $("#req-list").on("click", "li.expired a.ok", function() {
        console.log("Expired click");
        gotaxi.cleanupList();
    });

    $("#req-list").on("click", "li.assigned a.ok", function() {
        console.log("Assigned click");
        if (gotaxi.request) {
            changePage("#done");
        }
    });
});

// Confirmation page
$(document).on("pagecreate", "#done", function() {
    console.log("pagecreate#list");

    $(this).on("show", function() {
        var html = gotaxi.msg.doneRequest(gotaxi.request);
        $("#done-req", this).html(html).enhanceWithin();
    });

    $("#done-release").on("click", function(event) {
        event.preventDefault();
        if (gotaxi.request) {
            var request = gotaxi.request;
            $.ajax({
                url: "ws/release.php",
                data: {id: request.id},
                dataType: 'json'
            }).done(function (data) {
                if (data.res) {
                    gotaxi.request = null;
                    gotaxi.setRequestStatus(request.id, 'don');
                    changePage("#list");
                } else {
                    $("#error-msg").text("Trop tard");
                    changePage("#error", {reverse: true});
                }
            }).fail(function() {
                $("#error-msg").text("La requête au serveur a échoué");
                changePage("#error", {reverse: true});
            });            
        }
    });
});

// Map page
$(document).on("pagecreate", "#map", function() {
    console.log("pagecreate#map");
    var gmap = null, sizeTimer = null, markers = {},
            oldw = 0, oldh = 0;
    var $area = $(this).find('#map-area'),
            $header = $(this).find('[data-role="header"]'),
            $wrapper = $(this).find('#map-wrapper');

    function setMarker(name,z,lat,lng,title) {
        var pos = new google.maps.LatLng(lat, lng), marker = null;
        if (!gmap) {
            gmap = new google.maps.Map(
                    document.getElementById("map-area"), {
                        zoom: 16,
                        center: pos,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });
        }
        marker = markers[name];
        if (marker) {
            marker.setPosition(pos);
            marker.setVisible(true);
            marker.setTitle(title);
        } else {
            marker = new google.maps.Marker({
                position: pos,
                icon: "css/images/" + name +".png",
                shadow: "css/images/shadow.png",
                title: title,
                zIndex: z
            });
            marker.setMap(gmap);
            markers[name] = marker;
        }
        return marker;
    }

    function adjustPlan() {
        var req = gotaxi.request, marker = null;
        if (gotaxi.positionAvailable) {
            marker = setMarker("taxi", 1, gotaxi.latitude, gotaxi.longitude,
                    "Vous êtes ici");
        } else if (markers.taxi) {
            markers.taxi.setVisible(false);
        }
        if (req) {
            marker = setMarker("client", 2, req.lat, req.lng,
                    req.adr);
        } else if (markers.client) {
            markers.client.setVisible(false);
        }
        if (oldw !== $(window).width() || oldh !== $(window).height()) {
            var contentHeight = $(window).height() - $header.outerHeight()
                    - $("#map-back").outerHeight()
                    - $wrapper.outerHeight(true) + $wrapper.height();
            $area.height(contentHeight);
            oldw = $(window).width();
            oldh = $(window).height();
            if (gmap) {
                google.maps.event.trigger(gmap, 'resize');
                if (marker) {
                    gmap.panTo(marker.getPosition());
                }
            }
        }
    }

    $(this).on("show", function() {
        oldw = 0;
        oldh = 0;
        adjustPlan();
        sizeTimer = setInterval(function() {
            adjustPlan();
        }, 1000);
    });

    $(this).on("hide", function() {
        if (sizeTimer) {
            window.clearInterval(sizeTimer);
        }
    });
});
