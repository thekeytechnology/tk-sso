<?php
require_once("TkSsoFrontEndCache.php");


class TkSsoBroker {
    /**
     * Url of SSO server
     * @var string
     */
    protected $url;


    /**
     * Cookie lifetime
     * @var int
     */
    protected int $cookie_lifetime;


    /**
     * @var TkSsoFrontEndCache
     */
    protected TkSsoFrontEndCache $tkSsoFrontEndCache;

    /**
     * Class constructor
     *
     * @param int $cookie_lifetime
     */
    public function __construct($cookie_lifetime = 3600) {
        $this->url = get_option('tkt_sso_server_url');
        $this->cookie_lifetime = $cookie_lifetime;
        $this->tkSsoFrontEndCache = new TkSsoFrontEndCache();
    }


    /**
     * Get the cookie name.
     * @return string
     */
    public function getCookieName(): string {
        return 'tk_sso_token';
    }

    public function getToken(): string {
        return $_COOKIE[$this->getCookieName()] ?? "";
    }


    /**
     * Execute on SSO server.
     * @param string $method HTTP method: 'GET', 'POST', 'DELETE'
     * @param string $command Command
     * @param array|string $data Query or post parameters
     * @return array|object
     */
    protected function request(string $method, string $command, $data = null) {
        $url = $this->url;
        $data['command'] = $command;
        $response = wp_remote_post($url, array(
                'method' => $method,
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                'body' => json_encode($data),
                'cookies' => array()
            )
        );

        /**
         * If the request has failed, show the error message
         */
        if ($response['response']['code'] !== 200) {
            return ['error' => 'Fehler: ' . $response['response']['code'] . '. Leider gibt es aktuell technische Probleme. Wir arbeiten bereits an einer Lösung.'];
        } /**
         *  No request errors
         */
        else {
            return json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', wp_remote_retrieve_body($response)), true);
        }

    }


    /**
     * @param $name
     * @param $password
     * @return array
     */
    public function login($name, $password): array {
        if (!empty($name) && !empty($password)) {
            $response = $this->request('POST', 'login', ['name' => $name, 'password' => $password]);

            if (isset($response['error'])) {
                return ['error' => $response['error']];
            }

            /**
             * successfully authenticated
             */
            if ($response['authenticated'] == 1 && !empty($response['token'])) {
                $this->successfullyAuthenticated($response['token']);
                return apply_filters("tk-sso-login-success-return-array", ['authenticated' => true]);
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
     * Returns true if user is logged in
     */
    public function isUserLoggedIn(): bool {
        if (isset($_COOKIE[$this->getCookieName()])) {
            return true;
        } else return false;
    }


    /**
     * @param $token
     * Logs the user out of the browser and the server
     */
    public function logout($token) {
        if (isset($_COOKIE[$this->getCookieName()])) {
            $this->setCookieSameSite($this->getCookieName(), '', time() - 3600, '/');
        }
        $this->request('POST', 'logout', ['token' => $token]);
        $this->tkSsoFrontEndCache->unsetAuthenticationData();
    }

    /**
     * @param $token
     */
    private function successfullyAuthenticated($token) {
        $this->setCookieSameSite($this->getCookieName(), $token, time() + 3600, '/');
    }


    /**
     * @param string $userVar
     * @param string $token
     * @return array|mixed|object|string|string[]
     */
    public function authenticate($userVar = '', $token = '') {

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
            $response = $this->request('POST', 'authenticate', ['token' => $token]);
        }

        /**
         * authentication error
         */
        if (!$response['authenticated']) {
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

    public function getAllBrokers() {
        $token = $this->getToken();
        $response = $this->request('GET', 'getAllBrokers', ['token' => $token]);
        if (!empty($response) && !isset($response['error'])) {
            return $response;
        }
    }

    private function setCookieSameSite(
        string $name, string $value,
        int $expire, string $path = "", string $domain = "",
        bool $secure = true, bool $httponly = false, string $samesite = 'None'
    ) {
        if (PHP_VERSION_ID < 70300) {
            setcookie($name, $value, $expire, $path . '; samesite=' . $samesite, $domain, $secure, $httponly);
            return;
        }
        setcookie($name, $value, [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'samesite' => $samesite,
            'secure' => $secure,
            'httponly' => $httponly
        ]);
    }

    /**
     * @param $authenticationData
     */
    protected function cacheAuthenticationDataIfNotAlreadyCached($authenticationData) {
        if (!$this->tkSsoFrontEndCache->isAuthenticationDataCached()) $this->tkSsoFrontEndCache->cacheAuthenticationData($authenticationData);
    }

}

global /** @var TkSsoBroker $tkSsoBroker */
$tkSsoBroker;
$tkSsoBroker = new TkSsoBroker();
