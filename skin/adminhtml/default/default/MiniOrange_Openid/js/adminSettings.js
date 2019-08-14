var $m = jQuery.noConflict();
$m(document).ready(function() {
    $m("#phone").intlTelInput();
    $m("#query_phone").intlTelInput();
    $m("#additional_phone").intlTelInput();
    if ($email != "") {
        if ($showsocialsharing == "1") {
            voiddisplay("#socialsharing");
            setactive('social-sharing-setup');
        } else if ($showTwoFactorSettings == "1") {
            voiddisplay("#sociallogin");
            setactive('social-login-setup');
        } else {
            setactive('user_profile');
        }
    } else {
        setactive('account_setup');
    }
    if ($twofactortype != "") {
        $m(".active_method>b").empty();
        $selectedMethod = $twofactortype.toLowerCase().replace(/ /g, "_");
        $m(".active_method>b").append($twofactortype);
        $m(".mo2f_thumbnail").removeClass("method_active_box");
        $m("input[name='mo2f_selected_2factor_method']").removeAttr("checked");
        $m(".mo2f_click").removeClass("method_active");
        $m('#' + $selectedMethod).addClass("method_active_box");
        $m('#' + $selectedMethod).find(".mo2f_click").addClass("method_active");
        $m('#' + $selectedMethod).find("input").prop("checked", true);
        $m('#' + $selectedMethod).find("input").attr("disabled", true);
    } else {
        $m(".active_method>b").append("NONE");
    }
    if ($downloaded == "1") {
        $m('#showDownload').prop('checked', true);
        $m("#showDownload").val(1);
    }
    if ($m("#showDownload").is(":checked")) {
        $m("#configureqr").css("display", "block");
        $m("#downloadscreen").css("display", "none");
    } else {
        $m("#configureqr").css("display", "none");
        $m("#downloadscreen").css("display", "block");
    }
    $m(".navbar a").click(function() {
        $id = $m(this).parent().attr('id');
        setactive($id);
        $href = $m(this).data('method');
        voiddisplay($href);
    });
    $m(".btn-link").click(function() {
        $m(".collapse").slideUp("slow");
        if (!$m(this).next("div").is(':visible')) {
            $m(this).next("div").slideDown("slow");
        }
    });
    $m('#showDownload').change(function() {
        if ($m(this).attr('checked')) {
            $m(this).val(0);
            $m(this).attr('checked', false);
        } else {
            $m(this).val(1);
            $m(this).attr('checked', true);
            $m("#configmobilebutton").click();
        }
        $m("#downloadscreen").slideToggle();
        $m("#configureqr").slideToggle();
        document.location.href = "#displayQrCode";
    });
    $m('#preview1').click(function() {
        $m("a[data-method='#pricing']").click();
        $m("#offline-preview").click();
        document.location.href = "#slider3";
    });
    $m('#preview2').click(function() {
        $m("a[data-method='#pricing']").click();
        $m("#phonelost-preview").click();
        document.location.href = "#slider4";
    });
    $m('#preview3').click(function() {
        $m("a[data-method='#pricing']").click();
        $m("#reconfigure-preview").click();
        document.location.href = "#slider5";
    });
    $m('.preview4').click(function() {
        $m("a[data-method='#pricing']").click();
        $m("#register-preview").click();
        document.location.href = "#slider2";
    });
    $m('.preview5').click(function() {
        $m("a[data-method='#pricing']").click();
        $m("#reconfigure-preview-2").click();
        document.location.href = "#slider7";
    });
    $m('#error-cancel').click(function() {
        $error = "";
        $m(".error-msg").css("display", "none");
    });
    $m('#success-cancel').click(function() {
        $success = "";
        $m(".success-msg").css("display", "none");
    });
    $m('#cURL').click(function() {
        $m(".help_trouble").click();
        $m("#cURLfaq").click();
    });
    $m(".mo2f_click").click(function() {
        if (!$m(this).hasClass("method_active")) {
            $m(this).find("input").prop("checked", true);
            $m(".mo2f_thumbnail").hide();
            $m("#twofactorselect").show();
            $m("#mo2f_2factor_form").submit();
        }
    });
    $m(".reconfigure").click(function() {
        $m(".mo2f_thumbnail").hide();
        $m("#twofactorselect").show();
        if ($m(this).data("method") == "SMS" || $m(this).data("method") == "PHONE VERIFICATION") {
            $m("#phone_reconfigure").val($m(this).data("method"));
            $m("#mo2f_2factor_reconfigure_phone_form").submit();
        } else {
            $m("#reconfigure_mobile").val(1);
            $m("#mo2f_2factor_reconfigure_mobile_form").submit();
        }
    });
    $m(".test").click(function() {
        $m(".mo2f_thumbnail").hide();
        $m("#twofactorselect").show();
        $m("#test_2factor").val($m(this).data("method"));
        $m("#mo2f_2factor_test_form").submit();
    });
});

function setactive($id) {
    $m(".navbar-tabs>li").removeClass("active");
    $m("#minisupport").show();
    $id = '#' + $id;
    $m($id).addClass("active");
}

function voiddisplay($href) {
    $m(".page").css("display", "none");
    $m($href).css("display", "block");
}

function mo2f_valid(f) {
    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
}