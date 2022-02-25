<?php

/**
 * @param string[] $roles
 * @return string[]
 */
function tkSsoDocCheckAddRolesForRestriction(array $roles): array {
    $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_LOGGED_IN;
    $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_NOT_LOGGED_IN;

    return $roles;
}

add_filter(TkSsoRoleManager::$FILTER_ROLES_FOR_RESTRICTION, "tkSsoDocCheckAddRolesForRestriction");

/**
 * @param string[] $roles
 * @return string[]
 */
function tkSsoDocCheckAddSystemRolesForCurrentUser(array $roles): array {

    if (TkSsoUtil::getApiVersion() == "2") {
        global $tkSsoUser;
        if ($tkSsoUser->getRole() == "Doccheck") {
            $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_LOGGED_IN;
        } else {
            $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_NOT_LOGGED_IN;
        }
    } else {
        if (tkSsoIsDocCheckInstalled()) {
            $dclClient = tkSsoGetDocCheckClient();
            if ($dclClient->dcl_has_logged_in_user()) {
                $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_LOGGED_IN;
            } else {
                $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_NOT_LOGGED_IN;
            }
        } else {
            $roles[] = TkSsoRoleManager::$ROLE_DOCCHECK_NOT_LOGGED_IN;
        }
    }

    return $roles;
}

add_filter(TkSsoRoleManager::$FILTER_SYSTEM_ROLES_FOR_CURRENT_USER, "tkSsoDocCheckAddSystemRolesForCurrentUser");
