var news = {
    filter: "all",
    current: null,
    msg: {}
};

$(function() {
    $('script[type="text/x-handlebars-template"]').each(function() {
        var $this = $(this);
        console.log("Compiling handlebars template: " + $this.attr("id"));
        news.msg[$this.attr("id")] = Handlebars.compile($this.html());
    });
    $("#menu").enhanceWithin().panel();
});

$(document).on("pagecreate", "#list", function() {
    var $page = $(this), offs = 0, fetching = false;

    function extractFilter(href) {
        var parts = /^.*#(.*)$/.exec(href);
        return parts ? parts[1] : "all";
    }

    function extractPostId(href) {
        var parts = /^post-(.*)$/.exec(href);
        return parts ? parts[1] : null;
    }

    function fetchNextBatch() {
        if (!fetching) {
            $.mobile.loading('show', {
                text: "Saving post...",
                textVisible: true
            });
            fetching = true;
            $.ajax({
                url: "ws/fetchposts.php",
                data: {
                    filter: news.filter,
                    offs: offs,
                    count: 20
                },
                dataType: 'json'
            }).done(function (data) {
                console.log("Fetched " + data.length + " posts");
                offs += data.length;
                var html = news.msg.postList(data);
                $page.find(".sentinel").before(html);
                $("#post-list").listview("refresh");
            }).always(function() {
                fetching = false;
                $.mobile.loading('hide');
            });
        }
    }

    function refresh() {
        offs = 0;
        $page.find("li").not(".sentinel").remove();
        fetchNextBatch();
    }

    function showDetail(postId) {
        $.mobile.loading('show', {
            text: "Loading post...",
            textVisible: true
        });
        $.ajax({
            url: "ws/fetchpost.php",
            data: {id: postId},
            dataType: 'json'
        }).done(function (data) {
            console.log("Fetched post " + data.id);
            news.current = data;
            changePage("#detail")
        }).always(function() {
            $.mobile.loading('hide');
        });
    }

    $page.on("beforeshow", function(e) {
        refresh();
    });

    $page.on("click", ".post", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = extractPostId($(this).attr("id"));
        if (id) {
            showDetail(id);
        }
    });

    $page.on("click", ".sentinel", function(e) {
        e.preventDefault();
        e.stopPropagation();
        fetchNextBatch();
    });

    $("#menu").on("click", ".post-filter", function(e) {
        e.preventDefault();
        e.stopPropagation();
        $page.find("header h1").text($(this).text());
        $("#menu").find(".post-filter").removeClass("ui-disabled");
        $(this).addClass("ui-disabled");
        news.filter = extractFilter($(this).attr("href"));
        refresh();
        $("#menu").panel("close");
    });
});

$(document).on("pagecreate", "#detail", function() {
    var $page = $(this), post = null;

    $page.on("beforeshow", function() {
        post = news.current;
        if (post) {
            $("#detail-title").val(post.ttl);
            $("#detail-summary").val(post.sum);
            $("#detail-content").val(post.con);
            $page.find("header h1").text(post.ttl ? post.ttl : "Untitled");
        } else {
            $("#detail-title").val("");
            $("#detail-summary").val("");
            $("#detail-content").val("");
            $page.find("header h1").text("Untitled");
        }
    });

    $page.on("show", function() {
        $("detail-title").focus();
    });

    $page.on("submit", "form", function(e) {
        var url = "ws/newpost.php",
            data = {
                title: $("#detail-title").val(),
                summary: $("#detail-summary").val(),
                content: $("#detail-content").val()
            };
        if (post) {
            url = "ws/savepost.php";
            data.id = post.id;
        }
        e.preventDefault();
        e.stopPropagation();
        $.mobile.loading('show', {
            text: "Saving post...",
            textVisible: true
        });
        $.ajax({
            url: url,
            data: data,
            dataType: 'json'
        }).done(function (data) {
            console.log("News post saved");
            changePage("#list", {reverse: true});
        }).always(function() {
            $.mobile.loading('hide');
        });
    });
});

$(document).on("panelcreate", "#menu", function() {
    var $menu = $(this);

    $menu.on("click", ".new-post", function(e) {
        e.preventDefault();
        e.stopPropagation();
        news.current = null;
        changePage("#detail");
    });
});
