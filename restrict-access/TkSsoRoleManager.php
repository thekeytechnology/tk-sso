<?php

class TkSsoRoleManager {

    public static $ROLE_LOGGED_IN = "Logged In";
    public static $ROLE_NOT_LOGGED_IN = "Not Logged In";
    public static $ROLE_DOCCHECK = "DocCheck";
    public static $FILTER_ROLES = "tk_sso_roles_for_restriction";

    public function getRolesForRestriction() {
        $roles = [];

        $roles[] = $this::$ROLE_LOGGED_IN;
        $roles[] = $this::$ROLE_NOT_LOGGED_IN;
        $roles[] = $this::$ROLE_DOCCHECK;

        return apply_filters($this::$FILTER_ROLES, $roles);
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
        $userRole = $tkSsoUser->getRole();

        if ($tkSsoUser->isLoggedIn()) {
            if (in_array($this::$ROLE_LOGGED_IN, $roles)) {
                return true;
            }
        } else {
            if (in_array($this::$ROLE_NOT_LOGGED_IN, $roles)) {
                return true;
            }
        }

        return in_array($userRole, $roles);
    }
}
