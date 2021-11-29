<?php

require_once "TkSsoSettingsPage.php";
//require_once "user-bar.php";

add_action("wp_loaded", function () {
    $settingsPage = new TkSsoSettingsPage();
    $settingsPage->init();
});
