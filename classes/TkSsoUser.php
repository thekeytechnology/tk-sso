<?php

class TkSsoUser {
    private $data = [];
    private $loggedIn = false;

    public function getRole(): string {
        return $this->isLoggedIn() ? $this->getData('role') : 'not_logged_in';
    }

    public function isLoggedIn(): bool {
        if ($this->loggedIn) {
            return true;
        } else {
            global $tkSsoBroker;
            $this->loggedIn = $tkSsoBroker->isUserLoggedIn();
            return $this->loggedIn;
        }

    }

    /**
     * @param string $key
     * @return array|string
     */
    public function getData($key = '') {
        global $tkSsoBroker;

        if (empty($key)) {
            return $tkSsoBroker->authenticate();
        }

        if (key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            $value = $tkSsoBroker->authenticate($key);
            if (is_string($value)) {
                $this->data[$key] = $value;
                return $value;
            }
        }

        return "";
    }
}

global /** @var TkSsoUser $tkSsoUser */
$tkSsoUser;
$tkSsoUser = new TkSsoUser();
