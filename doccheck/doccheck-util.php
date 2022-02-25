<?php

/**
 * @return bool
 */
function tkSsoIsDocCheckInstalled(): bool {
    return class_exists("DCL\Client\DCL_Client");
}

/**
 * @return \DCL\Client\DCL_Client
 */
function tkSsoGetDocCheckClient(): \DCL\Client\DCL_Client {
    return new DCL\Client\DCL_Client("dc-login", "1.1.0");
}

function tkSsoIsDocCheckLogin(): bool {
    return isset($_GET['dc']) && $_GET['dc'] == 1 && isset($_GET['dc_timestamp']);
}

function tkSsoIsV2DocCheckLogin(): bool {
    return isset($_GET['loggedIn']) && $_GET['loggedIn'] == "true" && isset($_GET['token']);
}

function tkSsoBase64UrlEncode($string) {
    $string = str_replace("/", "_", $string);
    $string = str_replace("+", "-", $string);
    return $string;
}

add_action("wp_loaded", function () {
    if (tkSsoIsDocCheckLogin()) {
        tkSsoGetDocCheckClient()->dcl_do_login();
    }

    if (tkSsoIsV2DocCheckLogin()) {
        global $tkSsoBroker;
        $token = $_GET['token'];
        $tkSsoBroker->setCookie($token);
    }
});

add_filter("wp_redirect", function ($location) {
    if ($location == false && tkSsoIsDocCheckLogin()) {
        return "/";
    }

    return $location;
});


add_shortcode('tk-sso-status', function () {

    $client = tkSsoGetDocCheckClient();
    $val = "<pre>";
    $val .= "DocCheck Status: " . tkSsoIsDocCheckInstalled() . PHP_EOL;
    $val .= "DocCheck Logged In: " . $client->dcl_has_logged_in_user() . PHP_EOL;
    $rm = new TkSsoRoleManager();
    $val .= json_encode($rm->getSystemRolesForCurrentUser());
    $val .= "</pre>";

    return $val;
});
