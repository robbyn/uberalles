
// Map page
$(document).on("pagecreate", "#ad-page", function() {
    console.log("pagecreate#ad-page");
    var sizeTimer = null, oldw, oldh;
    var $area = $(this).find('#ad-area'),
            $header = $(this).find('[data-role="header"]'),
            $wrapper = $(this).find('#ad-wrapper');

    function adjustWindow() {
        console.log("adjustWindow enter");
        if (oldw !== $(window).width() || oldh !== $(window).height()) {
            var contentHeight = $(window).height() - $header.outerHeight()
                    - $("#ad-close").outerHeight()
                    - $wrapper.outerHeight(true) + $wrapper.height();
            $area.height(contentHeight);
            oldw = $(window).width();
            oldh = $(window).height();
        }
        console.log("adjustWindow exit");
    }

    $(this).on("show", function() {
        oldw = 0;
        oldh = 0;
        adjustWindow();
        sizeTimer = setInterval(function() {
            adjustWindow();
        }, 200);
    });

    $(this).on("hide", function() {
        if (sizeTimer) {
            window.clearInterval(sizeTimer);
        }
    });
});
