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
            if ($tkSsoUser->getData('status') !== 'Finished' || $tkSsoUser->getData('userStatus') !== 'Active') {
                $cookieManager = new TkSsoCookieManager();
                if ($tkSsoUser->getUserCountry() !== 'Deutschland') {
                    $cookieManager->setCookie("showAccessDeniedATPopUp", true);
                } else {
                    $cookieManager->setCookie("showAccessDeniedPopUp", true);
                }
                wp_redirect(get_home_url());
                exit;
            }
        }

        $whitelistRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_WHITELIST, true);

        if (
            (!$tkSsoUser->hasRole($whitelistRoles))
        ) {

            if ($tkSsoUser->isLoggedIn()) {
                $cookieManager = new TkSsoCookieManager();
                if ($tkSsoUser->getUserCountry() !== 'Deutschland') {
                    $cookieManager->setCookie("showAccessDeniedATPopUp", true);
                } else {
                    $cookieManager->setCookie("showAccessDeniedPopUp", true);
                }
                wp_redirect(get_home_url());
                exit;
            }
            global $tkSsoBroker;
            $loginUrl = $tkSsoBroker->createUrl();
            wp_redirect($loginUrl);
            exit;
        }
    }
});
