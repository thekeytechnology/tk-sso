<?php

function tkSsoGetUserDataShortcode($atts = [])
{
    $key = $atts['key'] ?? "";

    if ($key) {
        global $tkSsoUser;
        return $tkSsoUser->getData($key);
    }

    return "";
}

add_shortcode("tk-sso-user-data", "tkSsoGetUserDataShortcode");


/**
 * Doccheck Login Button
 * Usage:
 * custom values: [tk-doccheck-login-btn btnValue="test" classes="class1 class2" target="_self"]
 * default values: [tk-doccheck-login-btn]
 */
add_shortcode("tk-doccheck-login-btn", "tkDoccheckLoginBtn");
function tkDoccheckLoginBtn($attr)
{
    global $tkSsoBroker;
    if ($tkSsoBroker->isUserLoggedIn()) return '';

    $args = shortcode_atts(array(
        "btnValue" => "Login mit Doccheck",
        "classes" => "",
    ), $attr);

    $loginUrl = $tkSsoBroker->createUrl();
    return '
        <a  id="tkDocCheckLoginBtn" class="' . $args["classes"] . '" href="' . $loginUrl . '" >' . $args["btnValue"] . '</a>';
}

