<?php

include_once("../classes/TkSsoBroker.php");

/**
 *  Remove Sso cookie
 */
function removeTkSsoTokenCookie() {
    global $tkSsoBroker;

    if (!isset($_GET[$tkSsoBroker->getCookieName()])) return;

    require_once "functions.php";
    $tkSsoTokenValue = '';
    $tkSsoBroker->setCookie($tkSsoTokenValue);
}
removeTkSsoTokenCookie();
?>
<pre><?php print_r($_COOKIE) ?></pre>
