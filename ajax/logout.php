<?php

function logout()
{
    require_once "../../../../wp-load.php";
    global $tkSsoBroker;
    if ($tkSsoBroker->isUserLoggedIn()) {
        $token = $tkSsoBroker->getToken();
        $tkSsoBroker->logout($token);
    }
}

logout();
