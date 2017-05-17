gotaxi = {
    geocoder: new google.maps.Geocoder(),
    currentRequest: null,
    taxi: null,
    msg: {},

    geolocate: function(callback, errorCallback) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                var acc = position.coords.accuracy;
                var alt = position.coords.altitude;
                var pos = new google.maps.LatLng(lat,lon);
                gotaxi.geocoder.geocode({latLng: pos}, 
                        function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            callback({
                                types: results[0].types,
                                latitude: lat,
                                longitude: lon,
                                accuracy: acc,
                                altitude: alt,
                                address: gotaxi.address(results[0]),
                                country: gotaxi.shortName(results[0],
                                        "country"),
                                state: gotaxi.shortName(results[0],
                                        "administrative_area_level_1"),
                                locality: gotaxi.locality(results[0])
                            });
                        } else if (errorCallback) {
                            errorCallback("Erreur de recherche de l'adresse");
                        }
                    } else if (errorCallback) {
                       errorCallback(
                               "'Erreur de recherche de l'adresse: " + status);
                    }
                });
            }, function(error) {
                if (errorCallback) {
                    errorCallback(error.message);
                }
            }, {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 0
            });
        } else if (errorCallback) {
            errorCallback('Géolocalisation impossible');
        }
    },

    isOfType: function(item, type) {
        return $.inArray(type, item.types) >= 0;
    },

    shortName: function(item, type) {
        var result = null;
        $.each(item.address_components, function(i, val) {
            if (gotaxi.isOfType(val, type)) {
                result = val.short_name;
                return false;
            }
        });
        return result;
    },

    address: function(item) {
        var route = gotaxi.shortName(item, "route");
        var number = gotaxi.shortName(item, "street_number");
        return number === null ? route : route + " " + number;
    },

    locality: function(item) {
        var zip = gotaxi.shortName(item, "postal_code");
        var city = gotaxi.shortName(item, "locality");
        return zip === null ? city : zip + " " + city;
    },

    placeRequest: function(req) {
        if (gotaxi.requesting) {
            return;
        }
        var data = gotaxi.currentRequest;
        if (req) {
            data = {
                lat: req.latitude,
                lng: req.longitude,
                acc: 0,
                alt: 0,
                adr: req.address,
                loc: req.locality,
                cny: req.country,
                sta: req.state
            };
        }
        if (!data) {
            return;
        }
        gotaxi.requesting = true;
        $.ajax({
            url: "ws/request.php",
            data: data,
            dataType: 'json'
        }).done(function (data) {
            console.log("request registered with id: " + data.id);
            gotaxi.setTaxi(null);
            gotaxi.currentRequest = data;
            localStorage.setItem("gt_rid", data.id);
            changePage("#wait", {changeHash: false});
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error", {reverse: true});
        }).always(function() {
            gotaxi.requesting = false;
        });
    },

    setTaxi: function(taxi) {
        gotaxi.taxi = taxi;
    }
};

$(function() {
    $('script[type="text/x-handlebars-template"]').each(function() {
        var $this = $(this);
        console.log("Compiling handlebars template: " + $this.attr("id"));
        gotaxi.msg[$this.attr("id")] = Handlebars.compile($this.html());
    });

    $("#menu").enhanceWithin().panel();
});

$(document).on("panelbeforeopen", "#menu", function() {
    var page = $("body").pagecontainer("getActivePage").attr("id");
    if (gotaxi.taxi) {
        $(".menu-taxi").removeClass("ui-disabled");
    } else {
        $(".menu-taxi").addClass("ui-disabled");
    }
    if (gotaxi.currentRequest) {
        $(".menu-request").removeClass("ui-disabled");
    } else {
        $(".menu-request").addClass("ui-disabled");
    }
    $(this).find("li a[href='#" + page + "']").addClass("ui-disabled");
});

$(document).on("pagecreate", "#splash", function() {
});

$(document).on("pagecreate", "#addr", function() {
    var $search = $("#addr-search"), $ul = $("#addr-list"), places = [];

    function addLine(i, val, text) {
        $("#addr-msg").hide();
        var html = gotaxi.msg.addrLine({
            index: i, val: val, text: text
        });
        $ul.append(html);
    }

    function addRoute(i, val) {
        $("#addr-msg").hide();
        var html = gotaxi.msg.addrRoute({
            index: i, val: val
        });
        $ul.append(html);
    }

    function autocomplete() {
        var text = $search.val();
        if (text.length < 1) {
            $("#addr-msg").show();
            $ul.empty();
            $ul.listview("refresh");
        } else {
            gotaxi.geocoder.geocode({address: text, region: 'CH',  
                componentRestrictions: {
                    country: 'CH'
                }
            }, function(results, status) {
                $("#addr-msg").show();
                $ul.empty();
                if (status === google.maps.GeocoderStatus.OK) {
                    places = $.map(results, function(item) {
                        return {
                            types: item.types,
                            address:  gotaxi.address(item),
                            locality: gotaxi.locality(item),
                            country: gotaxi.shortName(item, "country"),
                            state: gotaxi.shortName(item,
                                    "administrative_area_level_1"),
                            latitude: item.geometry.location.lat(),
                            longitude: item.geometry.location.lng()
                        };
                    });
                    $.each(places, function(i, val) {
                        if (gotaxi.isOfType(val, "street_address")) {
                            addLine(i, val);
                        } else if (gotaxi.isOfType(val, "route")) {
                            addRoute(i, val);
                        }
                    });
                }
                $ul.listview("refresh");
            });
        }
    }

    $ul.on("click", "a", function() {
        var ix = parseInt($(this).attr("id").substring(1));
        var data = places[ix];
        if (gotaxi.isOfType(data, "street_address")) {
            gotaxi.placeRequest(data);
        } else if (gotaxi.isOfType(data, "route")) {
            var adr = data.address + " ";
            $search.val(adr);
            if ($search[0].setSelectionRange) {
                $search[0].setSelectionRange(adr.length, adr.length);
            }
            $search.focus();
            autocomplete();
        }
    });

    $search.on("input", function() {
        autocomplete();
    });

    function recover() {
        $.mobile.loading('show', {
            text: "Récupération en cours...",
            textVisible: true
        });
        $.ajax({
            url: "ws/recoverreq.php",
            data: {id: localStorage.getItem("gt_rid")},
            dataType: 'json'
        }).done(function (data) {
            console.log("request registered with id: " + data.id);
            gotaxi.setTaxi(data.taxi);
            gotaxi.currentRequest = data;
            localStorage.setItem("gt_rid", data.id);
            if (gotaxi.taxi) {
                changePage("#taxi");
            } else {
                localStorage.removeItem("gt_rid");
                gotaxi.currentRequest = null;
            }
        }).fail(function() {
            console.log("La requête au serveur a échoué");
            localStorage.removeItem("gt_rid");
        }).always(function() {
            $.mobile.loading("hide");
        });
    }

    $(this).on("show", function() {
        if (localStorage.getItem("gt_rid")) {
            recover();
        }
    });

    $("#addr-geoloc").on("click", function() {
        console.log("Start geolocation");
        $("#addr-search").val("");
        autocomplete();
        $.mobile.loading('show', {
            text: "Géolocalisation en cours...",
            textVisible: true
        });
        gotaxi.geolocate(function(data) {
            $.mobile.loading('hide');
            $("#addr-search").val(data.address + ", " + data.locality);
            places = [data];
            $ul.empty();
            addLine(0, data, "Go...");
            $ul.listview("refresh");
        }, function(error) {
            $.mobile.loading('hide');
            $("#addr-search").val("");
            $("#addr-nogeo").popup("open");
        });
    });
});

$(document).on("pagecreate", "#wait", function() {
    var timer = null, counter = 24;

    $(this).on("show", function(event) {
        console.log("#wait show");
        counter = 24;
        $("#countdown").text(counter);
        timer = setInterval(function() {
            if (counter > 0) {
                --counter;
                $("#countdown").text(counter);
            } else {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
                $.ajax({
                    url: "ws/selecttaxi.php",
                    data: {id: localStorage.getItem("gt_rid")},
                    dataType: 'json',
                    success: function (data) {
                        console.log("request status: " + data.sts);
                        if (data.sts === 'ok') {
                            gotaxi.setTaxi(data);
                            changePage("#taxi");
                        } else if (data.sts === 'cc') {
                            changePage("#notaxi", {reverse: true});
                            var html = gotaxi.msg.ccInfo(data);
                            $("#notaxi-cc").html(html);
                            $("#notaxi-cc").show();
                        } else {
                            changePage("#notaxi", {reverse: true});
                        }
                    },
                    error: function() {
                        $("#error-msg").text("La requête au serveur a échoué");
                        changePage("#error", {reverse: true});
                        $("#notaxi-cc").hide();
                    }
                });
            }
        }, 1000);
        return true;
    });

    $(this).on("hide", function(event) {
        console.log("#wait hide");
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    });

    $("#wait-cancel").on("click", function(event) {
        $.ajax({
            url: "ws/cancel.php",
            data: {id: localStorage.getItem("gt_rid")},
            dataType: 'text'
        }).done(function (data) {
            console.log("request canceled");
            changePage("#ad-page", {reverse: true});
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error", {reverse: true});
        });
    });
});

$(document).on("pagecreate", "#taxi", function() {
    var timer = null;

    $(this).on("show", function() {
        var taxi = gotaxi.taxi, html;
        if (taxi) {
            html = gotaxi.msg.taxiInfo(taxi);
            $("#taxi-info").html(html).enhanceWithin();
            taxi.mns = Math.round(taxi.etm/60);
            var dur = taxi.mns;
            if (!isNaN(dur)) {
                $("#taxi-dur").text(dur);
                if (dur > 1) {
                    timer = setInterval(function() {
                        --dur;
                        if (dur > 0) {
                            $("#taxi-dur").text(dur);
                        } else {
                            clearInterval(timer);
                            timer = null;
                        }
                    }, 60000);
                }
            }
        }
    });

    $(this).on("hide", function() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    });

    $("#taxi-cancel").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $.ajax({
            url: "ws/cancel.php",
            data: {id: localStorage.getItem("gt_rid")},
            dataType: 'text'
        }).done(function (data) {
            console.log("request canceled");
            changePage("#ad-page", {reverse: true});
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error", {reverse: true});
        });
    });

    $("#taxi-done").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        gotaxi.setTaxi(null);
        localStorage.removeItem("gt_rid");
        gotaxi.currentRequest = null;
        changePage("#ad-page", {reverse: true});
    });
});

$(document).on("pagecreate", "#notaxi", function() {
    $("#notaxi-call").on("click", function(event) {
        console.log("Retry request");
        gotaxi.placeRequest();
    });
});

// Map page
$(document).on("pagecreate", "#map", function() {
    console.log("pagecreate#map");
    var gmap = null, sizeTimer = null,
            taxiTimer = null, oldw = 0, oldh = 0, markers = {};
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
        console.log("adjustPlan enter");
        var req = gotaxi.currentRequest, taxi = gotaxi.taxi, marker = null;
        if (req) {
            marker = setMarker("client", 1, req.lat, req.lng, req.adr);
        } else if (markers.client) {
            markers.client.setVisible(false);
        }
        if (taxi) {
            marker = setMarker("taxi", 2, taxi.lat, taxi.lng, "La chariote");
        } else if (markers.taxi) {
            markers.taxi.setVisible(false);
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
        console.log("adjustPlan exit");
    }

    function refreshTaxi() {
        var reqId = localStorage.getItem("gt_rid");
        if (!reqId || gotaxi.requesting) {
            return;
        }
        gotaxi.requesting = true;
        $.ajax({
            url: "ws/requesttaxi.php",
            data: {
                id: reqId
            },
            dataType: 'json'
        }).done(function (data) {
            console.log("Taxi id: " + data.tid);
            gotaxi.setTaxi(data);
        }).fail(function() {
            console.log("La requête au serveur a échoué");
        }).always(function() {
            gotaxi.requesting = false;
        });
    }

    $(this).on("show", function() {
        oldw = 0;
        oldh = 0;
        adjustPlan();
        sizeTimer = setInterval(function() {
            adjustPlan();
        }, 1000);
        refreshTaxi();
        taxiTimer = setInterval(function() {
            refreshTaxi();
        }, 5000);
    });

    $(this).on("hide", function() {
        if (taxiTimer) {
            window.clearInterval(taxiTimer);
        }
        if (sizeTimer) {
            window.clearInterval(sizeTimer);
        }
    });
});
