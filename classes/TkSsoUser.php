<?php

class TkSsoUser {
    private $data = [];
    private $loggedIn = false;

    public function getRole(): string {
        $role = $this->getData('role');
        return $role ?: TkSsoRoleManager::$ROLE_NOT_LOGGED_IN;
    }

    public function isLoggedIn(): bool {
        if ($this->loggedIn) {
            return true;
        } else {
            global $tkSsoBroker;

            $acceptWordpressLogin = get_option(TkSsoSettingsPage::$OPTION_ACCEPT_WORDPRESS_LOGIN);

            $this->loggedIn = $tkSsoBroker->isUserLoggedIn() || ($acceptWordpressLogin == "1" && is_user_logged_in());
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

        $value = "";
        if (key_exists($key, $this->data)) {
            $value = $this->data[$key];
        } else {
            $value = $tkSsoBroker->authenticate($key);
            if (is_string($value)) {
                $this->data[$key] = $value;
            } else {
                $value = "";
            }
        }

        if ($key == "salutation") {
            $value = str_replace("Mr", "Herr", $value);
            $value = str_replace("Mrs", "Frau", $value);
        }

        $value = str_replace("Unknown", "", $value);

        return $value;
    }

    public function hasRole(array $roles): bool {
        $roleManager = new TkSsoRoleManager();
        return $roleManager->userHasRole($roles);
    }
}

global /** @var TkSsoUser $tkSsoUser */
$tkSsoUser;
$tkSsoUser = new TkSsoUser();
