<?php

class TkSsoUser
{
    private array $data = [];
    private TkSsoRoleManager $roleManager;
    public static string $FILTER_DATA = "";

    public function __construct()
    {
        $this->roleManager = new TkSsoRoleManager();
    }

    public function getRole(): string
    {
        $role = $this->getData('role');
        return $role ?: false;
    }

    public function getRoles(): array
    {
        $systemRoles = $this->roleManager->getSystemRolesForCurrentUser();

        $userRole = $this->getRole();

        return array_unique(array_merge($systemRoles, [$userRole]));
    }

    public function hasRole($roles): bool
    {

        $return = false;
        if (empty($roles)) {
            return true;
        }

        $userRoles = $this->getRoles();
        foreach ($userRoles as $role) {
            if (in_array($role, $roles)) {
                $return = true;
                break;
            }
        }

        if ($return) {
            return $this->validateLand($roles);
        }

        return false;
    }

    public function getUserCountry() {
        global $tkSsoBroker;
        $authenticate = $tkSsoBroker->authenticate();
        $roleApplicationProcesses = $authenticate['roleApplicationProcesses'][0] ?? null;

        if (!$roleApplicationProcesses) {
            return false;
        }

        $address = $roleApplicationProcesses['address'] ?? null;
        return $address['country'] ?? false;
    }

    public function validateLand($roles): bool
    {

        $country = [];
        if (in_array('Deutschland', $roles)) {
            $country[] = 'Deutschland';
        }
        if (in_array('Oesterreich', $roles)) {
            $country[] = 'Oesterreich';
        }


        if (empty($country)) {
            return true;
        }

        $userCountry = $this->getUserCountry();

        if(!$userCountry) {
            return false;
        }

        return in_array($userCountry, $country);
    }


    public function isLoggedIn(): bool
    {
        global $tkSsoBroker;

        $acceptWordpressLogin = get_option(TkSsoSettingsPage::$OPTION_ACCEPT_WORDPRESS_LOGIN);

        $this->loggedIn = $tkSsoBroker->isUserLoggedIn();
        return $this->loggedIn;
    }

    public function isActive(): bool
    {
        return $this->getData('globalUserStatus') != 'inactive';
    }

    /**
     * @param string $key
     * @return array|string
     */
    public function getData($key = '')
    {
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
