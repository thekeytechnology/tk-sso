<?php

// Add user bar if user is logged in

function tkSsoUserBar($content) {
    global $tkSsoUser;

    if (!$tkSsoUser->isLoggedIn()) return $content;

    $userName = $tkSsoUser->getData('fullname');
    $userbar = '
        <div id="tkSsoDemoLoginForm">
            <div class="tk-userbar">
                <div class="tk-d-flex tk-justify-content-between">
                <h4>Hallo ' . $userName . '</h4>
                <a id="tkSsoLogOut" class="tk-sso-logout-link">Log out</a>
                </div>
            </div>
        </div>
        ';
        return  $userbar . $content ;
}
add_filter('the_content', 'tkSsoUserBar');
