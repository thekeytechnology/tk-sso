<?php

add_action("template_redirect", function () {
    $post = get_queried_object();

    if ($post instanceof WP_Post && tkSsoShouldRestrict()) {

        global $tkSsoUser;
        $iqviaCheck = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_IQVIA_CHECK, true);

        if (intval($iqviaCheck) === 1) {
            if (!$tkSsoUser->isLoggedIn()) {
                global $tkSsoBroker;
                $loginUrl = $tkSsoBroker->createUrl();
                wp_redirect($loginUrl);
                exit;
            }
            if ($tkSsoUser->getData('status') !== 'Finished') {
                wp_redirect(get_home_url() . '/404', 301);
                exit;
            }
        }

        $whitelistRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_WHITELIST, true);

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
