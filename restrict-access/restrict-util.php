<?php

function tkSsoGenerateLoginLink($redirect = "") {
    $redirectTo = $redirect ? "?redirectTo=" . urlencode($redirect) : "";
    return get_option(TkSsoSettingsPage::$OPTION_LOGIN_URL) . $redirectTo;
}


