$(document).on("pagecreate", "#home", function() {
    var $page = $(this),
        newreq = new Howl({urls: ["../med/newreq.mp3", "../med/newreq.ogg"]}),
        canreq = new Howl({urls: ["../med/canreq.mp3", "../med/canreq.ogg"]}),
        gotit = new Howl({urls: ["../med/gotit.mp3", "../med/gotit.ogg"]});

    $page.on("click", ".play-newreq", function() {
        newreq.play();
    });

    $page.on("click", ".play-canreq", function() {
        canreq.play();
    });

    $page.on("click", ".play-gotit", function() {
        gotit.play();
    });
});
