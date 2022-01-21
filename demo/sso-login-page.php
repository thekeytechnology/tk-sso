<?php

/**
 * Generate SSO Login Page for Development purposes only
 */

function tkGenerateSsoLoginPage()
{
    $tkSsoLoginPage = get_page_by_title('Tk-SSO-Developer-Login-Page');
    if (is_null($tkSsoLoginPage)) {
        $tkSsoLoginPage = array(
            'post_title' => 'Tk-SSO-Developer-Login-Page',
            'post_content' => '[tk-sso-simple-login-form]',
            'post_author' => 1,
            'post_type' => 'page',
            'post_status' => 'private'
        );
        wp_insert_post($tkSsoLoginPage);
    }
}

add_action("init", "tkGenerateSsoLoginPage");