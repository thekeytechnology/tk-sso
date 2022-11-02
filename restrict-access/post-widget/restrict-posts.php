<?php

add_action("template_redirect", function () {
    $post = get_queried_object();

    if ($post instanceof WP_Post && tkSsoShouldRestrict()) {
        $whitelistRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_WHITELIST, true);
        $blacklistRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_BLACKLIST, true);
        global $tkSsoUser;

        if (
            (!$tkSsoUser->hasRole($whitelistRoles)) ||
            (!empty($blacklistRoles) && $tkSsoUser->hasRole($blacklistRoles))
        ) {
            if(tkIsLoggedInViaAnySource()){
                wp_redirect( get_home_url() . '/404', 301 );
                exit();
            }
            global $wp;
            $currentUrl = home_url($wp->request);
            $customRedirect = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_REDIRECT, true);
            if ($customRedirect) {
                $targetUrl = str_replace(TkSsoRestrictToRolesMetaBox::$STRING_REPLACE_URL, urlencode($currentUrl), $customRedirect);
            } else {
                $targetUrl = tkSsoGenerateLoginLink($currentUrl);
            }

            wp_redirect($targetUrl);
        }
    }
});
