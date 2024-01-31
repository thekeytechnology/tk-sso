<?php

function tkSsoAddAdminFiles() {
    wp_register_style('tk-sso-admin-style', get_home_url() . '/wp-content/plugins/tk-sso/assets/admin-style.css');
    wp_enqueue_style('tk-sso-admin-style');
}

add_action('admin_enqueue_scripts', 'tkSsoAddAdminFiles');

