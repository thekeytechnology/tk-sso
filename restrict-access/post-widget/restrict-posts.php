<?php

add_action("template_redirect", function () {
    $post = get_queried_object();

    if ($post instanceof WP_Post && tkSsoShouldRestrict()) {
        $restrictToRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY, true);
        $roleManager = new TkSsoRoleManager();

        if (!($roleManager->userHasRole($restrictToRoles))) {
            global $wp;
            $currentUrl = home_url($wp->request);
            $customRedirect = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY_REDIRECT, true);
            if ($customRedirect) {
                $loginUrl = str_replace(TkSsoRestrictToRolesMetaBox::$STRING_REPLACE_URL, urlencode($currentUrl), $customRedirect);
            } else {
                $loginUrl = get_option(TkSsoSettingsPage::$OPTION_LOGIN_URL) . "?redirectTo=" . urlencode($currentUrl);
            }

            wp_redirect($loginUrl);
        }
    }
});
