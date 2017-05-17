$(document).on("pagecreate", "#pinfo", function() {
    var $page = $(this), prevPage = null;
    console.log("pagecreate#pinfo");

    $page.on("show", function(e, pp) {
        prevPage = pp;
    });

    $page.on("hide", function(e) {
        $(this).remove();
    });

    $("#pinfo-form", $page).on("submit", function(event) {
        event.preventDefault();
        $.mobile.loading('show', {
            text: "Enregistrement en cours...",
            textVisible: true
        });
        $.ajax({
            url: "ws/putpinfo.php",
            data: $(this).serialize(),
            dataType: 'json'
        }).done(function (data) {
            console.log("Personal info saved");
            changePage(prevPage, {reverse: true});
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
