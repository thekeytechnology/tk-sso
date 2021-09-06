<?php

function login()
{
    if (isset($_GET['name']) && isset($_GET['password'])) {
        require_once "../../../../wp-load.php";
        global $tkSsoBroker;
        $loggedIn = $tkSsoBroker->login($_GET['name'], $_GET['password']);
        echo json_encode($loggedIn);
    }

}

login();