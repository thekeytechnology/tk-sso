<?php

function logout()
{
    require_once "../../../../wp-load.php";
    if (isset($_COOKIE['tk_sso_token'])) {
        global $tkSsoBroker;
        $token = $_COOKIE["tk_sso_token"];
        $tkSsoBroker->logout($token);
    }
}

logout();