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



add_shortcode('tk-logout-link', 'tkLogoutLink');
function tkLogoutLink(){
    global $tkSsoBroker;
    return $tkSsoBroker->createUrl('logout');
}


add_shortcode('tk-display-dynamic-privacy-content', 'tkDisplayDynamicPrivacyContent');

function tkDisplayDynamicPrivacyContent()
{
    $requestManager = new TkSsoRequestManager();
    $url = TkSsoUtils::getServerUrl() . TkSsoUtils::GET_LATEST_PRIVACY;
    $brandId = TkSsoUtils::getBrandId();
    $method = 'POST';
    $data = [
        'base64BrandId' => $brandId,
    ];
    $response = $requestManager->request($url, $method, $data);
    $errorText = "<p>Oops! Wir konnten die benötigten Datenschutzinformationen gerade nicht laden. Stellen Sie sicher, dass Ihre Internetverbindung aktiv ist und versuchen Sie es in einigen Momenten erneut.</p>";

    if (!is_array($response) || isset($response['error'])) {
        return $errorText;
    }

    if (isset($response['content'])) {
        return $response['content'];
    }

    return $errorText;
}
