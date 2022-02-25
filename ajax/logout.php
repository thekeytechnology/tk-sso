<?php

function logout()
{
    require_once "../../../../wp-load.php";
    global $tkSsoBroker;
    if ($tkSsoBroker->getToken()) {
        $token = $tkSsoBroker->getToken();
        $tkSsoBroker->logout($token);
    }
}

logout();
