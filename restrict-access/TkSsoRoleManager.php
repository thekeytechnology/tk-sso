<?php

class TkSsoRoleManager {

    public static $ROLE_LOGGED_IN = "Logged In";
    public static $ROLE_NOT_LOGGED_IN = "Not Logged In";
    public static $ROLE_WP_LOGGED_IN = "Wordpress Logged In";
    public static $ROLE_WP_NOT_LOGGED_IN = "Wordpress Not Logged In";
    public static $ROLE_DOCCHECK_LOGGED_IN = "DocCheck Logged In";
    public static $ROLE_DOCCHECK_NOT_LOGGED_IN = "DocCheck Not Logged In";
    public static $FILTER_ROLES_FOR_RESTRICTION = "tk_sso_roles_for_restriction";
    public static $FILTER_SYSTEM_ROLES_FOR_CURRENT_USER = "tk_sso_system_roles_for_current_user";

    public function getRolesForRestriction() {
        $roles = [];

        $roles[] = $this::$ROLE_LOGGED_IN;
        $roles[] = $this::$ROLE_NOT_LOGGED_IN;
        $roles[] = $this::$ROLE_WP_LOGGED_IN;
        $roles[] = $this::$ROLE_WP_NOT_LOGGED_IN;

        $roles = apply_filters($this::$FILTER_ROLES_FOR_RESTRICTION, $roles);

        return array_combine($roles, $roles);
    }

    /**
     * @param array $roles List of Roles to check for
     * @return bool Will return true if the users role matches any of the roles passed via $roles. Will also check for logged in, not logged in and DocCheck
     */
    public function userHasRole($roles): bool {

        if (empty($roles)) {
            return true;
        }

        global $tkSsoUser;

        $systemRoles = $this->getSystemRolesForCurrentUser();

        $userRole = $tkSsoUser->getRole();

        $combinedRoles = array_merge($systemRoles, [$userRole]);
        foreach ($combinedRoles as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }

        return false;
    }

    public function getSystemRolesForCurrentUser() {
        global $tkSsoUser;

        $roles = [];

        if ($tkSsoUser->isLoggedIn()) {
            $roles[] = $this::$ROLE_LOGGED_IN;
        } else {
            $roles[] = $this::$ROLE_NOT_LOGGED_IN;
        }

        if (is_user_logged_in()) {
            $roles[] = $this::$ROLE_WP_LOGGED_IN;
        } else {
            $roles[] = $this::$ROLE_WP_NOT_LOGGED_IN;
        }

        return apply_filters($this::$FILTER_SYSTEM_ROLES_FOR_CURRENT_USER, $roles);
    }
}
