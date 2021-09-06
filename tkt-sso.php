<?php
/*
Plugin Name: TKT SSO
Plugin URI:  https://www.thekey.technology
Version:     1
Author:      the key technology
Author URI:  https://www.thekey.technology
License:     proprietary
Text Domain: tkt
*/


require_once "assets/enqueue-assets.php";
require_once "admin/_admin.php";
require_once "elementor-add-on/_elementor-add-on.php";
require_once "classes/_classes.php";
require_once "functions.php";
require_once "shortcodes/_shortcodes.php";
if(! is_admin()) {
    require_once "attach-cookies.php";
}





