<?php

add_action("template_redirect", function () {
    $post = get_queried_object();

    if ($post instanceof WP_Post && tkSsoShouldRestrict()) {
        $whitelistRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_WHITELIST, true);
        global $tkSsoUser;

        if (
            (!$tkSsoUser->hasRole($whitelistRoles))
        ) {
            if ($tkSsoUser->isLoggedIn()) {
                wp_redirect(get_home_url() . '/404', 301);
                exit;
            }
            global $tkSsoBroker;
            $loginUrl = $tkSsoBroker->createUrl();
            wp_redirect($loginUrl);
            exit;
        }
    }
});
