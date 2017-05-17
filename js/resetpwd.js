
$(document).on("pagecreate", "#resetpwd", function() {
    var $page = $(this);

    function checkPwd() {
        if ($("#resetpwd-pwd").val() === $("#resetpwd-con").val()) {
            if ($("#resetpwd-pwd").val() === "") {
                $("#resetpwd-pwd,#resetpwd-con")
                        .removeClass("valid")
                        .addClass("invalid");
                return false;
            } else {
                $("#resetpwd-pwd,#resetpwd-con")
                        .removeClass("invalid")
                        .addClass("valid");
                return true;
            }
        } else {
            $("#resetpwd-pwd,#resetpwd-con")
                    .removeClass("valid")
                    .addClass("invalid");
            return false;
        }
    }

    function checkValid() {
        var ok = true;
        if (!checkPwd()) {
            ok = false;
        }
        return ok;
    }

    $page.on("show", function(e) {
        checkValid();
    });

    $page.on("change input", "#resetpwd-pwd,#resetpwd-con", function(e) {
        checkPwd();
    });

    $page.on("submit", "#resetpwd-form", function(e) {
        var $form = $(this);
        e.preventDefault();
        e.stopPropagation();
        if (checkValid()) {
            $.mobile.loading('show', {
                text: "Envoi en cours...",
                textVisible: true
            });
            $.ajax({
                url: "ws/resetpwd.php",
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
