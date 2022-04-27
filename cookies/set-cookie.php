<?php
include_once("../../../../wp-load.php");
include_once("../classes/TkSsoBroker.php");

/**
 * set Sso cookie
 */
function setTkSsoTokenCookie() {

    global $tkSsoBroker;
    $cookieName = $tkSsoBroker->getCookieName();

    if (!isset($_GET[$cookieName])) return;
    require_once "./functions.php";
    $tkSsoTokenValue = $_GET[$cookieName];
    $tkSsoBroker->setCookie($tkSsoTokenValue);

}

header('Content-type: image/png');
setTkSsoTokenCookie();
?>
<pre><?php print_r($_COOKIE) ?></pre>
