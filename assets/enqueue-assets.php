<?php

function tkSsoAddFiles() {
    wp_register_style('tk-sso-style', get_home_url() . '/wp-content/plugins/tk-sso/assets/style.css');
    wp_enqueue_style('tk-sso-style');
    wp_enqueue_script('tk-sso-script', get_home_url() . '/wp-content/plugins/tk-sso/assets/app.js', array("jquery"), false, true);

    $defaultLoginRedirect = get_option(TkSsoSettingsPage::$OPTION_LOGIN_REDIRECT_URL);
    wp_localize_script("tk-sso-script", "tkSsoSettings", ["redirectUrl" => $defaultLoginRedirect]);
}

add_action('wp_enqueue_scripts', 'tkSsoAddFiles', 11);

function tkSsoAddAdminFiles() {
    wp_register_style('tk-sso-admin-style', get_home_url() . '/wp-content/plugins/tk-sso/assets/admin-style.css');
    wp_enqueue_style('tk-sso-admin-style');
}

add_action('admin_enqueue_scripts', 'tkSsoAddAdminFiles');

