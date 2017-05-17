if (!window.console) {
    console = {log: function(){}};
}

$(document).on("mobileinit", function () {
    $.mobile.defaultPageTransition = 'slide';
});

$(function () {
    $("html").removeClass("no-js");
    $("html").addClass("js");
    if (!$.mobile.gradeA()) {
        window.location = "unsupported.php";
        return;
    }
});

$(document).on("pagecontainerbeforeshow", "body", function (event, ui) {
    "use strict";
    ui.toPage.trigger("beforeshow", [ui.prevPage]);
});

$(document).on("pagecontainershow", "body", function (event, ui) {
    "use strict";
    ui.toPage.trigger("show", [ui.prevPage]);
});

$(document).on("pagecontainerhide", "body", function (event, ui) {
    "use strict";
    ui.prevPage.trigger("hide", [ui.nextPage]);
});

function changePage(to, opts) {
    $("body").pagecontainer("change", to, opts);
}
