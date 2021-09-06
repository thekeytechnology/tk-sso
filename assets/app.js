jQuery(function ($) {
    $('#tkSsoLogIn').click(function () {

        setTimeout(function () {
            $('.tkSSoSpinner').addClass('active');
        }, 100);

        setTimeout(function () {
            if ($('#userName').val() != '' && $('#password').val() != '') {
                $.ajax(
                    {
                        url: "/wp-content/plugins/tkt-sso/ajax/login.php",
                        type: 'GET',
                        async: false,
                        data: {
                            name: $('#userName').val(),
                            password: $('#password').val()
                        },
                        success: function (result) {
                            result = jQuery.parseJSON(result);

                            if(result == '') {
                                tkSsoErrorMessage('Leider gibt es aktuell technische Probleme. Wir arbeiten bereits an einer Lösung.')
                                console.log('Error: Server sent empty response')
                            }

                            if ('error' in result) {
                                $('.tkSSoSpinner').removeClass('active');
                                tkSsoErrorMessage(result.error);
                            }

                            else {
                                window.location.href = window.location.href.split('?')[0] + "?loggedIn=true";
                            }
                        },
                    }
                );
            } else {
                tkSsoErrorMessage('Bitte füllen Sie alle Felder aus ');
                $('.tkSSoSpinner').removeClass('active');
            }
        }, 400)

    })

    $('#tkSsoLogOut').click(function () {
        $.ajax(
            {
                url: "/wp-content/plugins/tkt-sso/ajax/logout.php",
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

})

