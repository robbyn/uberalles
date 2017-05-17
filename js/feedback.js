
$(document).on("pagecreate", "#feedback", function() {
    var $page = $(this);

    function checkName() {
        var val = $("#fb-name").val(),
                re = /^.{2,}$/i;
        if (!re.test(val)) {
            $("#fb-name").addClass("invalid").removeClass("valid");
            return false;
        }
        $("#fb-name").addClass("valid").removeClass("invalid");
        return true;
    }

    function checkEmail() {
        var val = $("#fb-email").val(),
                re = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
        if (!re.test(val)) {
            $("#fb-email").addClass("invalid").removeClass("valid");
            return false;
        }
        $("#fb-email").addClass("valid").removeClass("invalid");
        return true;
    }

    function checkValid() {
        var ok = true;
        if (!checkName()) {
            ok = false;
        }
        if (!checkEmail()) {
            ok = false;
        }
        return ok;
    }

    $page.on("show", function(e, pp) {
        checkValid();
    });

    $page.on("change input", "#fb-name", function(e) {
        checkName();
    });

    $page.on("change input", "#fb-email", function(e) {
        checkEmail();
    });

    $page.on("submit", "#fb-form", function(e) {
        var $form = $(this);
        e.preventDefault();
        e.stopPropagation();
        if (checkValid()) {
            $.mobile.loading('show', {
                text: "Envoi en cours...",
                textVisible: true
            });
            $.ajax({
                url: "ws/feedback.php",
                data: $form.serialize(),
                dataType: 'json'
            }).done(function () {
                console.log("feedback sent");
                $form[0].reset();
                changePage("#ad-page", {reverse: true});
            }).fail(function() {
                checkValid();
            }).always(function() {
                $.mobile.loading('hide');
            });
        }
    });
});
