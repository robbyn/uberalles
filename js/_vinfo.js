$(document).on("pagecreate", "#vinfo", function() {
    var $page = $(this), prevPage = null;
    console.log("pagecreate#vinfo");

    $page.on("show", function(e, pp) {
        prevPage = pp;
    });

    $page.on("hide", function(e) {
        $(this).remove();
    });

    $("#vinfo-hasacar", $page).on("change", function(e) {
        e.preventDefault();
        e.stopPropagation();
        if ($(this).is(":checked")) {
            $("#vinfo-vehicle").show();
        } else {
            $("#vinfo-vehicle").hide();
        }
    });

    $("#vinfo-form", $page).on("submit", function(event) {
        event.preventDefault();
        $.mobile.loading('show', {
            text: "Enregistrement en cours...",
            textVisible: true
        });
        $.ajax({
            url: "ws/putvinfo.php",
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function (data) {
            console.log("Vehicle info saved");
            changePage(prevPage, {reload: true, reverse: true});
        }).fail(function() {
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error");
        }).always(function() {
            $.mobile.loading('hide');
        });
        console.log("Settings submitted");
        return false;
    });
});
