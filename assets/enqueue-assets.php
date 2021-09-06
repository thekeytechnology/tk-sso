<?php

function tkSsoAddFiles()
{
    wp_register_style( 'tk-sso-style',  get_home_url() . '/wp-content/plugins/tkt-sso/assets/sytle.css' );
    wp_enqueue_style( 'tk-sso-style' );
    wp_enqueue_script('tk-sso-script', get_home_url() . '/wp-content/plugins/tkt-sso/assets/app.js', array("jquery"), false, true);
}

add_action('wp_enqueue_scripts', 'tkSsoAddFiles', 11);