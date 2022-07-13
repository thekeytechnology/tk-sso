<?php

/**
 * @return bool
 */
function tkSsoIsDocCheckInstalled(): bool {
    return class_exists("DCL\Client\DCL_Client") || TkSsoUtil::getApiVersion() == "2";
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

function tkSsoGenerateDocCheckLink($redirect = ""): string {

    $currentDomain = "https://$_SERVER[HTTP_HOST]";
    $login = rtrim(tkSsoGenerateLoginLink(""), "/") . '/';
    $redirectTo = $redirect ? "&redirectTo=" . urlencode($redirect) : "";
    $docCheckRedirect = tkSsoBase64UrlEncode(base64_encode($currentDomain . "$login?loggedIn=true$redirectTo"));

    $brand = TkSsoUtil::getBrandId();
    return "https://identity.infectopharm.com/doccheck/$brand/$docCheckRedirect";
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
    global $tkSsoUser;
    $val .= json_encode($tkSsoUser->getRoles());
    $val .= "</pre>";

    return $val;
});
