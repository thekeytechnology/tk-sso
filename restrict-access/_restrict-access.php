<?php

require_once("elementor-add-on/_elementor-add-on.php");
require_once("post-widget/_post-widgets.php");
require_once("restrict-util.php");

function tkSsoShouldRestrict() {
    return !(is_admin() || is_user_logged_in());
}
