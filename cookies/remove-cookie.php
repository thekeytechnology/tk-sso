<?php

/**
 *  Remove Sso cookie
 */
function removeTkSsoTokenCookie() {
    if(!isset($_GET['tk_sso_token'])) return;

    require_once "functions.php";
    $tkSsoTokenValue = '';
    setCookieSameSite('tk_sso_token', $tkSsoTokenValue, time() - 3600, '/');
}
removeTkSsoTokenCookie();
?>
<pre><?php print_r($_COOKIE) ?></pre>