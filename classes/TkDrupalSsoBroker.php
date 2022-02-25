<?php

class TkDrupalSsoBroker extends TkSsoBroker
{
    /**
     * @param $name
     * @param $password
     * @return array
     */
    public function login($name, $password): array
    {
        if (!empty($name) && !empty($password)) {
            $response = $this->request('', 'POST', 'login', ['name' => $name, 'password' => $password]);

            if (isset($response['error'])) {
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
            $this->setCookie('');

            $developmentMode = get_option("tk-development-mode");
            if ($developmentMode) {
                // $this->setCookieSameSite do not work locally with no SSL
                setcookie($this->getCookieName(), '', time() - 3600, '/');
            }
        }
        $this->request('', 'POST', 'logout', ['token' => $token]);
        $this->tkSsoFrontEndCache->unsetAuthenticationData();
        unset($_COOKIE[$this->getCookieName()]);
    }


    /**
     * @param string $userVar
     * @param string $token
     * @return array|mixed|object|string|string[]
     */
    public function authenticate($userVar = '', $token = '')
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
            $response = $this->request('', 'POST', 'authenticate', ['token' => $token]);
        }

        /**
         * authentication error
         */
        if (!is_array($response) || !$response['authenticated']) {
            return ['error' => 'Bitte melden Sie sich erneut an'];
        }

        /**
         * if isset $userVar => return just the value of this var
         * example: authenticate(token, userName) returns just the username that has this token
         */
        if (!empty($userVar) && array_key_exists($userVar, $response['user'])) {
            $this->cacheAuthenticationDataIfNotAlreadyCached($response);
            return $response['user'][$userVar];
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
        $response = $this->request('', 'POST', 'getAllBrokers', ['token' => $token]);
        if (!empty($response) && !isset($response['error'])) {
            return $response;
        }
    }
}

