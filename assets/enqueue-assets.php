<?php

function tkSsoAddFiles() {
    wp_register_style('tk-sso-style', get_home_url() . '/wp-content/plugins/tk-sso/assets/style.css');
    wp_enqueue_style('tk-sso-style');
    wp_enqueue_script('tk-sso-script', get_home_url() . '/wp-content/plugins/tk-sso/assets/app.js', array("jquery"), false, true);
}

add_action('wp_enqueue_scripts', 'tkSsoAddFiles', 11);

function tkSsoAddAdminFiles() {
    wp_register_style('tk-sso-admin-style', get_home_url() . '/wp-content/plugins/tk-sso/assets/admin-style.css');
    wp_enqueue_style('tk-sso-admin-style');
}

add_action('admin_enqueue_scripts', 'tkSsoAddAdminFiles');
