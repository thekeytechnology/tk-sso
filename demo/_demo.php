<?php

require_once "sso-login-page.php";

function tkSsoAddDemoFiles()
{
    wp_enqueue_style('tk-demo-login-page-style', get_home_url() . '/wp-content/plugins/tk-sso/demo/assets/css/demo-login-page.css');
    wp_enqueue_script('tk-demo-login-page-js', get_home_url() . '/wp-content/plugins/tk-sso/demo/assets/js/demo-login-page.js', array("jquery"), false, true);
}

add_action('wp_enqueue_scripts', 'tkSsoAddDemoFiles', 11);


// Add user bar if user is logged in
function tkSsoUserBar($content)
{
    global $tkSsoBroker;
    if (!isset($_COOKIE['tk_sso_token'])) return $content;
    $userName = $tkSsoBroker->authenticate("firstname", $_COOKIE['tk_sso_token']);
    $userbar = '
        <div class="tk-userbar">
            <div class="tk-d-flex tk-justify-content-between">
            <h4>Hallo ' . $userName . '</h4>
            <a id="tkSsoLogOut">Log out</a>
            </div>
        </div>
        ';
    return $userbar . $content;
}

add_filter('the_content', 'tkSsoUserBar');