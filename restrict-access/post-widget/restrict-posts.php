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
                exit();
            }
            global $wp;
            $currentUrl = home_url($wp->request);
            $customRedirect = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_REDIRECT, true);
            if ($customRedirect) {
                $targetUrl = tkSsoGenerateLoginLink(str_replace(TkSsoRestrictToRolesMetaBox::$STRING_REPLACE_URL, urlencode($currentUrl), $customRedirect));
            } else {
                $targetUrl = tkSsoGenerateLoginLink($currentUrl);
                if (count($whitelistRoles) == 1 && $whitelistRoles[0] == 'DocCheck Logged In') {
                    $targetUrl .= '&just-doccheck=true';
                }
                if (count($whitelistRoles) >= 2 && in_array('DocCheck Logged In', $whitelistRoles)) {
                    $targetUrl .= '&with-doccheck=true';
                }
            }

            wp_redirect($targetUrl);
        }
    }
});
