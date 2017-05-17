
$(document).on("pagecreate", "#forgotten", function() {
    var $page = $(this);

    function checkEmail() {
        var val = $("#forgotten-email").val(),
                re = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
        if (!re.test(val)) {
            $("#forgotten-email").addClass("invalid").removeClass("valid");
            return false;
        }
        $("#forgotten-email").addClass("valid").removeClass("invalid");
        return true;
    }

    function checkValid() {
        var ok = true;
        if (!checkEmail()) {
            ok = false;
        }
        return ok;
    }

    $page.on("show", function(e) {
        checkValid();
    });

    $page.on("submit", "#forgotten-form", function(e) {
        var $form = $(this);
        e.preventDefault();
        e.stopPropagation();
        if (checkValid()) {
            $.mobile.loading('show', {
                text: "Envoi en cours...",
                textVisible: true
            });
            $.ajax({
                url: "ws/resetreq.php",
                data: $form.serialize(),
                dataType: 'json'
            }).done(function () {
                console.log("reset request sent");
                $form[0].reset();
                changePage("#ad-page", {reverse: true});
            }).fail(function() {
                console.log("reset request failed");
                $form[0].reset();
                changePage("#ad-page", {reverse: true});
            }).always(function() {
                $.mobile.loading('hide');
            });
        }
    });
});
