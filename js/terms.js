$(document).on("pagecreate", "#terms", function() {
    var $page = $(this);

    $page.on("click", "#terms-close", function(e) {
        e.stopPropagation();
        e.preventDefault();
        window.close();
    });
});
