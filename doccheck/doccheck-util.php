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
