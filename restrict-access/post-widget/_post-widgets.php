<?php

require_once("TkSsoRestrictToRolesMetaBox.php");
require_once("restrict-posts.php");

add_action("wp_loaded", function () {
    $metaBox = new TkSsoRestrictToRolesMetaBox();
    $metaBox->init();
});
