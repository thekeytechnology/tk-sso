jQuery(function ($) {

    const tkSsoForm = $(".tk-sso-login-form");

    const tkSsoFormSubmit = () => {
        setTimeout(function () {
            $('.tkSSoSpinner').addClass('active');
        }, 100);

        setTimeout(function () {
            if ($('#userName').val() != '' && $('#password').val() != '') {
                $.ajax(
                    {
                        url: "/wp-content/plugins/tk-sso/ajax/login.php",
                        type: 'GET',
                        async: false,
                        data: {
                            name: $('#userName').val(),
                            password: $('#password').val()
                        },
                        success: function (result) {
                            result = jQuery.parseJSON(result);

                            if (result == '') {
                                tkSsoErrorMessage('Leider gibt es aktuell technische Probleme. Wir arbeiten bereits an einer Lösung.')
                                console.log('Error: Server sent empty response')
                            }

                            if ('error' in result) {
                                $('.tkSSoSpinner').removeClass('active');
                                tkSsoErrorMessage(result.error);
                            } else {
                                const redirect = getRedirectUrlParam();
                                if (redirect) {
                                    window.location.href = window.location.href.split('?')[0] + "?loggedIn=true&redirectTo=" + redirect
                                } else {
                                    window.location.href = window.location.href.split('?')[0] + "?loggedIn=true"
                                }
                            }
                        },
                    }
                );
            } else {
                tkSsoErrorMessage('Bitte füllen Sie alle Felder aus ');
                $('.tkSSoSpinner').removeClass('active');
            }
        }, 400)
    }

    $(tkSsoForm).find('.tk-sso-login-submit').click(function () {
        tkSsoFormSubmit();
    })

    const tkSsoInputFields = $(tkSsoForm).find('input');

    tkSsoInputFields.bind("enterKey", function (e) {
        tkSsoFormSubmit();
    });
    tkSsoInputFields.keyup(function (e) {
        if (e.keyCode == 13) {
            $(this).trigger("enterKey");
        }
    });

    $('.tk-sso-logout-link').click(function () {
        $.ajax(
            {
                url: "/wp-content/plugins/tk-sso/ajax/logout.php",
                type: 'GET',
                async: false,
                success: function (result) {
                    $('.tkSSoSpinner').removeClass('active');
                    window.location.href = window.location.href.split('?')[0] + "?loggedOut=true";
                },
            }
        );
    })

    $("input").keyup(function () {
        $('#tkSsoError').removeClass('active');
    })

    function tkSsoErrorMessage(message) {
        $('#tkSsoError').removeClass();
        $('#tkSsoError').html(message);
        $('#tkSsoError').addClass('active');
    }

    const getRedirectUrlParam = () => {
        const urlSearchParams = new URLSearchParams(window.location.search);

        let redirectTo = urlSearchParams.get("redirectTo");
        if (redirectTo) {
            redirectTo = decodeURI(redirectTo)
        } else {
            redirectTo = tkSsoSettings.redirectUrl
        }

        return redirectTo;
    }

    const redirectAfterLogin = () => {
        const urlSearchParams = new URLSearchParams(window.location.search);
        const params = Object.fromEntries(urlSearchParams.entries());
        if ((params.loggedIn === "true") && params.redirectTo) {
            const redirectTo = getRedirectUrlParam();
            if (redirectTo) {
                window.location.href = redirectTo;
            } else {
                window.location.href = window.location.href.split('?')[0];
            }
        }
    }
    $(window).on("load", redirectAfterLogin);

})


