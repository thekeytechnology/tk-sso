<?php

class TkUsSsoBroker extends TkSsoBroker {
    public static string $LOGIN_API = "/sso/login";
    public static string $LOGOUT_API = "/sso/logout";
    public static string $AUTHENTICATE_API = "/sso/authenticate";
    public static string $ALL_BROKERS_API = "/sso/brokers";

    public function getToken(): string {
        return $_COOKIE[$this->getCookieName()] ?? $_GET['dcToken'] ?? "";
    }

    /**
     * @param $name
     * @param $password
     * @return array
     */
    public function login($name, $password) {
        if (!empty($name) && !empty($password)) {
            $response = $this->request(TkSsoUtil::getApiUrl() . $this::$LOGIN_API, 'POST', 'login', ['name' => $name, 'password' => $password]);

            if (isset($response['error'])) {
                if ($response['error'] == 'unspecified-auth-exception') {
                    return ['error' => 'Die angegebenen Zugangsdaten sind nicht korrekt.'];
                }
                return ['error' => $response['error']];
            }

            /**
             * successfully authenticated
             */
            if ($response['authenticated'] == 1 && !empty($response['token'])) {
                $this->successfullyAuthenticated($response['token']);
                $brokers = $this->getAllBrokers();
                return apply_filters("tk-sso-login-success-return-array", [
                    'authenticated' => true,
                    'brokers' => $brokers
                ]);
            } /**
             * authentication error
             */
            else {
                return ['error' => 'Authentifizierungsfehler'];
            }
        } /**
         *  username or password is empty
         */
        else {
            return ['error' => 'Bitte überprüfen sie ihre Eingaben'];
        }
    }

    protected function request($endpoint, string $method, string $command, $data = null) {
        $url = !empty($endpoint) ? $endpoint : $this->url;
//        $data['command'] = $command;
        $data = json_encode($data);
        add_filter('https_ssl_verify', '__return_false');
        $response = wp_remote_post($url, array(
                'method' => $method,
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                'body' => $data,
                'cookies' => array()
            )
        );

        /**
         * If the request has failed, show the error message
         */
        if (is_wp_error($response)) {
            return ['error' => 'Fehler: ' . $response->get_error_message()];
        }
        if ($response['response']['code'] !== 200) {
            return ['error' => 'Fehler: ' . $response['response']['code'] . '. Leider gibt es aktuell technische Probleme. Wir arbeiten bereits an einer Lösung.'];
        } /**
         *  No request errors
         */
        else {
            return json_decode(wp_remote_retrieve_body($response), true);
        }

    }


    /**
     * @param $token
     * Logs the user out of the browser and the server
     */
    public function logout($token) {
        if (isset($_COOKIE[$this->getCookieName()])) {
            $this->setCookie("");
        }
        $this->request(TkSsoUtil::getApiUrl() . $this::$LOGOUT_API, 'POST', 'logout', ['token' => $token]);
        $this->tkSsoFrontEndCache->unsetAuthenticationData();
        unset($_COOKIE[$this->getCookieName()]);
    }

    /**
     * @param $userVar
     * @param $token
     * @return array|mixed|object|string|string[]|void
     */
    public function authenticate($userVar = "", $token = "") {
        $token = !empty($token) ? $token : $this->getToken();

        /**
         * cannot authenticate users who are not logged in
         */
        if (empty($token)) {
            $this->tkSsoFrontEndCache->unsetAuthenticationData();
            return ['error' => 'Bitte melden Sie sich erneut an'];
        }

        /**
         * Use Cache if user data are cached else send Post request
         */
        if ($this->tkSsoFrontEndCache->isAuthenticationDataCached()) {
            $response = $this->tkSsoFrontEndCache->getCachedAuthenticationData();
            $response['cached'] = 'cached';
        } else {
            $response = $this->request(TkSsoUtil::getApiUrl() . $this::$AUTHENTICATE_API, 'POST', 'authenticate', ['token' => $token]);
        }

        /**
         * authentication error
         */
        if (!is_array($response)) {
            return ['error' => 'Bitte melden Sie sich erneut an'];
        }

        /**
         * if isset $userVar => return just the value of this var
         * example: authenticate(token, userName) returns just the username that has this token
         */

        if (!empty($userVar)) {
            $userVal = "";
            if ($userVar == "role") {
                $roleProcesses = $response['roleApplicationProcesses'] ?? [];
                foreach ($roleProcesses as $role) {
                    if ($role['status'] == "Finished" && $role['userStatus'] == "Active") {
                        $userVal = $role['targetRole'];
                    }
                }
            } else {
                $userVal = $this->tkSearchArray($userVar, $response);
            }
            if ($userVal) {
                $this->cacheAuthenticationDataIfNotAlreadyCached($response);
                return $userVal;
            }
        }

        /**
         * Cache authentication data from the response if user data is not already cached
         */
        $this->cacheAuthenticationDataIfNotAlreadyCached($response);
        return $response;
    }


    public function getAllBrokers() {
        $token = $this->getToken();
        $response = $this->request(TkSsoUtil::getApiUrl() . $this::$ALL_BROKERS_API, 'GET', 'getAllBrokers', ['token' => $token]);
        if (!empty($response) && !isset($response['error'])) {
            return $response;
        }
    }

    /**
     * @param $key
     * @param $array
     * @return string
     */
    public function tkSearchArray($key, $array): string {
        if (is_array($array)) {
            $keyToLower = strtolower($key);
            $arrayToLower = array_change_key_case($array, CASE_LOWER);
            if (isset($arrayToLower[$keyToLower])) {
                return $arrayToLower[$keyToLower];
            }
            foreach ($arrayToLower as $subarray) {
                $subarrayToLower = array_change_key_case($subarray, CASE_LOWER);
                if (isset($subarrayToLower[$keyToLower]) && is_string($subarrayToLower[$keyToLower])) {
                    return $subarrayToLower[$keyToLower];
                }
            }
        }
        return "";
    }

    public function isUserLoggedIn(): bool {
        $role = $this->authenticate('role');
        if ($role == "Doccheck" || isset($role['error'])) {
            return false;
        }

        return !!$role;
    }

}
