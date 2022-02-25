<?php

require_once("TkSsoFrontEndCache.php");

abstract class TkSsoBroker
{
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
    private $get_option;

    /**
     * Class constructor
     *
     * @param int $cookie_lifetime
     */
    public function __construct($cookie_lifetime = 3600) {
        $this->get_option = TkSsoUtil::getApiUrl();
        $this->url = $this->get_option;
        $this->cookie_lifetime = $cookie_lifetime;
        $this->tkSsoFrontEndCache = new TkSsoFrontEndCache();
    }


    /**
     * @param $name
     * @param $password
     * @return array
     */
    abstract public function login($name, $password);


    /**
     * @param $token
     * Logs the user out of the browser and the server
     */
    abstract public function logout($token);

    /**
     * @param string $token
     * Sets the cookie. Token should be retrieved automatically, if it is not passed
     */
    public function setCookie(string $token) {
        $this->setCookieSameSite($this->getCookieName(), $token, time() + 3600, '/');
    }


    /**
     * @param string $userVar
     * @param string $token
     * @return array|mixed|object|string|string[]
     */
    abstract public function authenticate($userVar, $token);


    abstract public function getAllBrokers();


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
    protected function request($endpoint, string $method, string $command, $data = null)
    {
        $url = !empty($endpoint) ? $endpoint : $this->url;
        $data['command'] = $command;
        add_filter('https_ssl_verify', '__return_false');
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
        if(is_wp_error($response) ) {
            return ['error' => 'Fehler: ' . $response->get_error_message()];
        }
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
     * Returns true if user is logged in
     */
    public function isUserLoggedIn(): bool
    {
        if (isset($_COOKIE[$this->getCookieName()])) {
            return true;
        } else return false;
    }


    /**
     * @param $token
     */
    public function successfullyAuthenticated($token)
    {
        $this->setCookieSameSite($this->getCookieName(), $token, time() + 3600, '/');
        $developmentMode = get_option("tk-development-mode");
        if($developmentMode) {
            // $this->setCookieSameSite do not work locally with no SSL
            setcookie($this->getCookieName(), $token, time() + 3600, '/');
        }
    }


    public function setCookieSameSite(
        string $name, string $value,
        int    $expire, string $path = "", string $domain = "",
        bool   $secure = true, bool $httponly = false, string $samesite = 'None'
    ) {
        $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $host = $this->getDomain($fullUrl);
        $domain = $domain ?: $host;
        $_COOKIE[$this->getCookieName()] = $value;
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

    // https://gist.github.com/Xeoncross/1561096
    private function getDomain($url) {
        $domain = parse_url((strpos($url, '://') === FALSE ? 'http://' : '') . trim($url), PHP_URL_HOST);
        if (preg_match('/[a-z0-9][a-z0-9\-]{0,63}\.[a-z]{2,6}(\.[a-z]{1,2})?$/i', $domain, $match)) {
            return $match[0];
        }
    }

    /**
     * @param $authenticationData
     */
    protected function cacheAuthenticationDataIfNotAlreadyCached($authenticationData) {
        if (!$this->tkSsoFrontEndCache->isAuthenticationDataCached()) $this->tkSsoFrontEndCache->cacheAuthenticationData($authenticationData);
    }

}