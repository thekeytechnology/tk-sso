<?php

class TkSsoRoleManager {

    public static string $ROLE_LOGGED_IN = "Logged In";
    public static string $FILTER_ROLES_FOR_RESTRICTION = "tk_sso_roles_for_restriction";
    public static string $FILTER_SYSTEM_ROLES_FOR_CURRENT_USER = "tk_sso_system_roles_for_current_user";
    public static string $FILTER_CUSTOM_ROLES = "tk_sso_custom_roles";

    public function getRolesForRestriction() {
        $roles = [];

        $roles[] = $this::$ROLE_LOGGED_IN;

        $customRoles = $this->getCustomRoles();
        foreach ($customRoles as $customRole) {
            $roles[] = $customRole->name;
        }

        $roles = apply_filters($this::$FILTER_ROLES_FOR_RESTRICTION, $roles);
        //print_a(array_combine($roles, $roles));
        return array_combine($roles, $roles);
    }

    /**
     * @deprcated [TkSso] Use TkSsoUser->hasRoles instead
     * @param array $roles List of Roles to check for
     * @return bool Will return true if the users role matches any of the roles passed via $roles. Will also check for logged in, not logged in and DocCheck
     */
    public function userHasRole($roles): bool {
        global $tkSsoUser;

        return $tkSsoUser->hasRole($roles);
    }

    /**
     * @return TkSsoCustomRole[]
     */
    private function getCustomRoles(): array {
        return apply_filters(TkSsoRoleManager::$FILTER_CUSTOM_ROLES, []);
    }

    /**
     * @return string[]
     */
    public function getSystemRolesForCurrentUser(): array {
        global $tkSsoUser;

        $roles = [];

        if ($tkSsoUser->isLoggedIn()) {
            $roles[] = $this::$ROLE_LOGGED_IN;
        }

        $customRoles = $this->getCustomRoles();
        foreach ($customRoles as $customRole) {
            if ($customRole->shouldGrantRole) {
                $roles[] = $customRole->name;
            }
        }

        return apply_filters($this::$FILTER_SYSTEM_ROLES_FOR_CURRENT_USER, $roles);
    }
}
