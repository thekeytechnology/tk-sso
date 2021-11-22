<?php


class TkSsoFrontEndCache
{

    private string $sessionIndex;

    private int $expiration;

    public function __construct()
    {

        $this->sessionIndex = 'TkSsoAuthenticationData';
        $this->expiration = 300; //  5 Minutes
    }

    /*
     * return true if authentication data is cached
     */
    public function isAuthenticationDataCached(): bool
    {
        if (
            isset($_SESSION[$this->sessionIndex])
            && !empty($_SESSION[$this->sessionIndex])
            && (time() - $_SESSION[$this->sessionIndex]['CREATED'] < $this->expiration)
        ) {
            return true;
        }
        return false;
    }

    public function cacheAuthenticationData($authenticationData)
    {
        // Unset current TkSsoAuthenticationData
        if (isset($_SESSION[$this->sessionIndex])) {
            unset($_SESSION[$this->sessionIndex]);
        }

        $authenticationData['CREATED'] = time();
        $_SESSION[$this->sessionIndex] = $authenticationData;
    }

    public function getCachedAuthenticationData() {
        if(isset($_SESSION[$this->sessionIndex]) && !empty($_SESSION[$this->sessionIndex])) {
            return $_SESSION[$this->sessionIndex];
        }
    }

    public function unsetAuthenticationData() {
        if(isset($_SESSION[$this->sessionIndex]) && !empty($_SESSION[$this->sessionIndex])) {
            unset($_SESSION[$this->sessionIndex]);
        }
    }
}
