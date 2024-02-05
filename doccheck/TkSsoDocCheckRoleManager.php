<?php

class TkSsoDocCheckRoleManager
{
    public static string $ROLE_DOCCHECK_LOGGED_IN = "DocCheck Logged In";

    /**
     * @param TkSsoCustomRole[] $roles
     * @return TkSsoCustomRole[]
     */
    function addCustomRoles(array $roles): array
    {
        $roles[] = new TkSsoCustomRole(
            TkSsoDocCheckRoleManager::$ROLE_DOCCHECK_LOGGED_IN,
            function () {
                global $tkSsoUser;
                return $tkSsoUser->getRole() == "Doccheck";
            }
        );

        return $roles;
    }
}

add_filter(TkSsoRoleManager::$FILTER_CUSTOM_ROLES, array(new TkSsoDocCheckRoleManager(), "addCustomRoles"), 0, 1);
