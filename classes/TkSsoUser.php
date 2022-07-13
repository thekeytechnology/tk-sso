<?php

class TkSsoUser {
    private array $data = [];
    private TkSsoRoleManager $roleManager;
    public static string $FILTER_DATA = "";

    public function __construct() {
        $this->roleManager = new TkSsoRoleManager();
    }

    public function getRole(): string {
        $role = $this->getData('role');
        return $role ?: TkSsoRoleManager::$ROLE_NOT_LOGGED_IN;
    }

    public function getRoles(): array {
        $systemRoles = $this->roleManager->getSystemRolesForCurrentUser();

        $userRole = $this->getRole();

        return array_merge($systemRoles, [$userRole]);
    }

    public function hasRole($roles): bool {
        if (empty($roles)) {
            return true;
        }

        $userRoles = $this->getRoles();
        foreach ($userRoles as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }

        return false;
    }

    public function isLoggedIn(): bool {
        global $tkSsoBroker;

        $acceptWordpressLogin = get_option(TkSsoSettingsPage::$OPTION_ACCEPT_WORDPRESS_LOGIN);

        $this->loggedIn = $tkSsoBroker->isUserLoggedIn() || ($acceptWordpressLogin == "1" && is_user_logged_in());
        return $this->loggedIn;
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
            $value = str_replace("Mrs", "Frau", $value);
            $value = str_replace("Mr", "Herr", $value);
        }

        $value = str_replace("Unknown", "", $value);


        return apply_filters(TkSsoUser::$FILTER_DATA, $value, $key);
    }
}

global /** @var TkSsoUser $tkSsoUser */
$tkSsoUser;
$tkSsoUser = new TkSsoUser();
