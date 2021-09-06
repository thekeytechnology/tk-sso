<?php

/**
 * set Sso cookie
 */
function setTkSsoTokenCookie() {
    if(!isset($_GET['tk_sso_token'])) return;
    require_once "functions.php";
    $tkSsoTokenValue = $_GET['tk_sso_token'];
    setCookieSameSite('tk_sso_token', $tkSsoTokenValue, time() + 3600, '/');
}
setTkSsoTokenCookie();
?>
<pre><?php print_r($_COOKIE) ?></pre>
