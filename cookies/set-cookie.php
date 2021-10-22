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
    require_once "functions.php";
    $tkSsoTokenValue = $_GET[$cookieName];
    setCookieSameSite($cookieName, $tkSsoTokenValue, time() + 3600, '/');
}
setTkSsoTokenCookie();
?>
<pre><?php print_r($_COOKIE) ?></pre>
