<?php

// Add user bar if user is logged in

function tkSsoUserBar($content) {
    global $tkSsoBroker;

    if( !isset($_COOKIE['tk_sso_token'])) return $content;

    $userName = $tkSsoBroker->authenticate($_COOKIE['tk_sso_token'], 'fullname');
        $userbar = '
        <div class="tk-userbar">
            <div class="tk-d-flex tk-justify-content-between">
            <h4>Hallo '. $userName .'</h4>
            <a id="tkSsoLogOut">Log out</a>
            </div>
        </div>
        ';
        return  $userbar . $content ;
}
add_filter('the_content', 'tkSsoUserBar');