<?php

class TkUsSsoBroker extends TkSsoBroker
{
    public static string $LOGIN_API = "/sso/login";
    public static string $LOGOUT_API = "/sso/logout";
    public static string $AUTHENTICATE_API = "/sso/authenticate";
    public static string $ALL_BROKERS_API = "/sso/brokers";



    /**
     * @param $name
     * @param $password
     * @return array
     */
    public function login($name, $password)
    {
        if (!empty($name) && !empty($password)) {
            $response = $this->request(get_option('tkt_sso_server_url') . $this::$LOGIN_API, 'POST', 'login', ['name' => $name, 'password' => $password]);

            if (isset($response['error'])) {
                if ($response['error'] == 'unspecified-auth-exception') {
                    return ['error' => 'Authentifizierungsfehler'];
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


    /**
     * @param $token
     * Logs the user out of the browser and the server
     */
    public function logout($token)
    {
        if (isset($_COOKIE[$this->getCookieName()])) {
            $this->setCookieSameSite($this->getCookieName(), '', time() - 3600, '/');
            $developmentMode = get_option("tk-development-mode");
            if ($developmentMode) {
                // $this->setCookieSameSite do not work locally with no SSL
                setcookie($this->getCookieName(), '', time() - 3600, '/');
            }
        }
        $this->request(get_option('tkt_sso_server_url') . $this::$LOGOUT_API, 'POST', 'logout', ['token' => $token]);
        $this->tkSsoFrontEndCache->unsetAuthenticationData();
        unset($_COOKIE[$this->getCookieName()]);
    }


    /**
     * @param $userVar
     * @param $token
     * @return array|mixed|object|string|string[]|void
     */
    public function authenticate($userVar = "", $token = "")
    {
        $token = !empty($token) ? $token : $this->getToken();

        /**
         * cannot authenticate users who are not logged in
         */
        if (!$this->isUserLoggedIn() || empty($token)) {
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
            $response = $this->request(get_option('tkt_sso_server_url') . $this::$AUTHENTICATE_API, 'POST', 'authenticate', ['token' => $token]);
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
            $userVal = $this->tkSearchArray($userVar, $response);
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


    public function getAllBrokers()
    {
        $token = $this->getToken();
        $response = $this->request(get_option('tkt_sso_server_url') .$this::$ALL_BROKERS_API, 'POST', 'getAllBrokers', ['token' => $token]);
        if (!empty($response) && !isset($response['error'])) {
            return $response;
        }
    }

    /**
     * @param $key
     * @param $array
     * @return string
     */
    public function tkSearchArray($key, $array): string
    {
        if (is_array($array)) {
            $keyToLower = strtolower($key);
            $arrayToLower = array_change_key_case($array, CASE_LOWER);
            if (isset($arrayToLower[$keyToLower]) && is_string($arrayToLower[$keyToLower])) {
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
}
