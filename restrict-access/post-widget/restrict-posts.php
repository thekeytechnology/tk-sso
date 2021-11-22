<?php

add_action("template_redirect", function () {
    $post = get_queried_object();

    if ($post instanceof WP_Post && (!current_user_can('editor') || current_user_can('administrator'))) {
        $restrictToRoles = get_post_meta($post->ID, TkSsoRestrictToRolesMetaBox::$META_KEY, true);
        $roleManager = new TkSsoRoleManager();

        if (!($roleManager->userHasRole($restrictToRoles))) {
            global $wp;
            $currentUrl = home_url($wp->request);
            $loginUrl = get_option(TkSsoSettingsPage::$OPTION_LOGIN_URL);

            $loginWithRedirect = $loginUrl . "?redirectTo=" . urlencode($currentUrl);

            wp_redirect($loginWithRedirect);
        }
    }
});
