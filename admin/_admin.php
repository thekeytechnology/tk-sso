<?php

require_once "TkSsoSettingsPage.php";

add_action("wp_loaded", function () {
    $settingsPage = new TkSsoSettingsPage();
    $settingsPage->init();
});
