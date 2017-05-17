var user = null, msg = {};

$(function() {
    $('script[type="text/x-handlebars-template"]').each(function() {
        var $this = $(this);
        console.log("Compiling handlebars template: " + $this.attr("id"));
        msg[$this.attr("id")] = Handlebars.compile($this.html());
    });
});

$(document).on("pagecreate", "#reg", function() {
    var $page = $(this);

    function checkUsernameAvailable(username) {
        $.ajax({
            url: "ws/useravailable.php",
            data: {usn: username},
            dataType: 'json'
        }).done(function (data) {
            setUsernameValid(data.sts === "true");
        }).fail(function() {
            setUsernameValid(false);
        });
    }

    function setUsernameValid(valid) {
        if (valid) {
            $("#reg-usn").addClass("valid").removeClass("invalid");
        } else {
            $("#reg-usn").addClass("invalid").removeClass("valid");
        }
    }

    function checkPwd() {
        if ($("#reg-pwd").val() === $("#reg-con").val()) {
            if ($("#reg-pwd").val() === "") {
                $("#reg-pwd,#reg-con").removeClass("valid").addClass("invalid");
                return false;
            } else {
                $("#reg-pwd,#reg-con").removeClass("invalid").addClass("valid");
                return true;
            }
        } else {
            $("#reg-pwd,#reg-con").removeClass("valid").addClass("invalid");
            return false;
        }
    }

    function checkUsername() {
        var val = $("#reg-usn").val(),
                re = /^\w{2,}$/;
        if (!re.test(val)) {
            setUsernameValid(false);
            return false;
        }
        setUsernameValid(false);
        checkUsernameAvailable(val);
        return true;
    }

    function checkEmail() {
        var val = $("#reg-mal").val(),
                re = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
        if (!re.test(val)) {
            $("#reg-mal").addClass("invalid").removeClass("valid");
            return false;
        }
        $("#reg-mal").addClass("valid").removeClass("invalid");
        return true;
    }

    function checkPhone() {
        var val = $("#reg-tel").val(),
                re = /^\+?[0-9 ]{7,14}$/;
        if (!re.test(val)) {
            $("#reg-tel").addClass("invalid").removeClass("valid");
            return false;
        }
        $("#reg-tel").addClass("valid").removeClass("invalid");
        return true;
    }

    function checkTerms() {
        return $("#reg-terms").is(":checked");
    }

    function checkValid() {
        var ok = true;
        if (!checkUsername()) {
            ok = false;
        }
        if (!checkEmail()) {
            ok = false;
        }
        if (!checkPhone()) {
            ok = false;
        }
        if (!checkPwd()) {
            ok = false;
        }
        if (!checkTerms()) {
            ok = false;
        }
        if (ok) {
            $("#reg-save").removeClass("ui-disabled");
        } else {
            $("#reg-save").addClass("ui-disabled");
        }
        return ok;
    }

    $page.on("show", function(e, pp) {
        checkValid();
    });

    $page.on("change", "#reg-hasacar", function(e) {
        e.preventDefault();
        e.stopPropagation();
        if ($(this).is(":checked")) {
            $("#reg-vehicle").show();
        } else {
            $("#reg-vehicle").hide();
        }
    });

    $page.on("change input", "#reg-usn", function(e) {
        checkValid();
    });

    $page.on("change input", "#reg-mal", function(e) {
        checkValid();
    });

    $page.on("change input", "#reg-tel", function(e) {
        checkValid();
    });

    $page.on("change input", "#reg-pwd,#reg-con", function(e) {
        checkValid();
    });

//    $page.on("click", "#reg-terms-lk", function(e) {
//        e.stopPropagation();
//        e.preventDefault();
//        window.open("/terms.php", "_blank");
//    });

    $(this).on("change", "#reg-terms", function() {
        checkValid();
    });

    $page.on("submit", "#reg-form", function(e) {
        var $form = $(this);
        e.preventDefault();
        e.stopPropagation();
        if (checkValid()) {
            $.mobile.loading('show', {
                text: "Enregistrement en cours...",
                textVisible: true
            });
            $.ajax({
                url: "ws/register.php",
                data: $form.serialize(),
                dataType: 'json'
            }).done(function (data) {
                console.log("user created");
                $form[0].reset();
                user = data;
                changePage("#done");
            }).fail(function() {
                checkValid();
            }).always(function() {
                $.mobile.loading('hide');
            });
        }
    });
});

$(document).on("pagecreate", "#done", function() {
    var $page = $(this);

    $page.on("show", function(e, pp) {
        var html = msg.doneContent(user);
        $("#done-content").html(html);
    });
});
