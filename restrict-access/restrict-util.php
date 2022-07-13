<?php

function tkSsoGenerateLoginLink($redirect = "") {
    return get_option(TkSsoSettingsPage::$OPTION_LOGIN_URL) . "?redirectTo=" . urlencode($redirect);
}