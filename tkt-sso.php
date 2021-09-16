<?php
/*
Plugin Name: TKT SSO
Plugin URI:  https://www.thekey.technology
Version:     5
Author:      the key technology
Author URI:  https://www.thekey.technology
License:     proprietary
Text Domain: tkt
*/


require_once "assets/enqueue-assets.php";
require_once "admin/_admin.php";
require_once "classes/_classes.php";
require_once "functions.php";
require_once "restrict-access/_restrict-access.php";
require_once "shortcodes/_shortcodes.php";
if(! is_admin()) {
    require_once "attach-cookies.php";
}


require 'plugin-update-checker-4.11/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/thekeytechnology/tk-sso',
    __FILE__,
    'tk-sso'
);
$updateChecker->setBranch('master');


