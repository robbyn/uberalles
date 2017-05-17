$(document).on("pagecreate", "#pwd", function() {
    var prevPage = null;
    console.log("pagecreate#pwd");

    function checkValid() {
        if ($("#pwd-new").val() === $("#pwd-con").val()) {
            $("#pwd-new,#pwd-con").removeClass("invalid");
            if ($("#pwd-new").val() === "") {
                $("#pwd-new,#pwd-con").removeClass("valid");
            } else {
                $("#pwd-new,#pwd-con").addClass("valid");
            }
        } else {
            $("#pwd-new,#pwd-con").removeClass("valid");
            $("#pwd-new,#pwd-con").addClass("invalid");
        }
    }

    $(this).on("show", function(e, pp) {
        prevPage = pp;
        checkValid();
    });

    $(this).on("hide", function(e, pp) {
        $(this).remove();
    });

    $("#pwd-new,#pwd-con").on("change input", function() {
        checkValid();
    });

    $("#pwd-form").on("submit", function(event) {
        event.preventDefault();
        var form = this;
        $.mobile.loading('show', {
            text: "Enregistrement en cours...",
            textVisible: true
        });
        $.ajax({
            url: "ws/chgpwd.php",
            data: $(form).serialize(),
            dataType: 'json'
        }).done(function (data) {
            if (data.sts === 'ok') {
                console.log("password changed");
                form.reset();
                checkValid();
                changePage(prevPage);
            } else if (data.sts === 'ko') {
                alert("L'ancien mot de passe est incorrect");
                $("#pwd-old").focus();
            } else if (data.sts === 'dif') {
                alert("Les nouveaux mots de passe sont différents");
                $("#pwd-new").focus();
            }
        }).fail(function() {
            form.reset();
            checkValid();
            $("#error-msg").text("La requête au serveur a échoué");
            changePage("#error");
        }).always(function() {
            $.mobile.loading('hide');
        });
        console.log("Settings submitted");
        return false;
    });
});
