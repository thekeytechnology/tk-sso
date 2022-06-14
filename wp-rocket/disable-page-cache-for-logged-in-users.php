<?php

add_action("init", function () {
    global $tkSsoUser;
    if ($tkSsoUser->isLoggedIn()) {
        add_filter('do_rocket_generate_caching_files', '__return_false');
    }
});