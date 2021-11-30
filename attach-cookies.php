<?php

add_action('wp_footer', 'tkAttachCookiesToAllBrokers');

// Set Cookies for All other Brokers After Login or Logout
function tkAttachCookiesToAllBrokers() {
    if (!is_admin() && (isset($_GET['loggedIn']) || isset($_GET['loggedOut']))) {
        global $tkSsoBroker;
        $brokers = $tkSsoBroker->getAllBrokers();
        $token = $tkSsoBroker->getToken();
        $cookieName = $tkSsoBroker->getCookieName();
        $html = '';
        $html .= "<h1>TEST DANIEL</h1>";
        $html .= "<pre>" . print_r($brokers, true) . "</pre>";
        if (!empty($brokers) && is_array($brokers)) {
            foreach ($brokers as $broker) {
                if (get_home_url() != $broker) {
                    if (isset($_GET['loggedIn'])) {
                        $html .= "<img style='display: none; width:0px; opacity: 0;' src='$broker?$cookieName=$token'>";
                    } else {
                        $html .= "<img style='display: none; width:0px; opacity: 0;' src='$broker?$cookieName=-1'>";
                    }
                }
            }
        }
        echo $html;
    }
}
