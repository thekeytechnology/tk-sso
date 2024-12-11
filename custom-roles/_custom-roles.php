<?php

add_filter(TkSsoRoleManager::$FILTER_CUSTOM_ROLES, function($roles) {
    $roles[] = new TkSsoCustomRole("UG1", function() {
        global $tkSsoUser;
        $role = $tkSsoUser->getRole();
        $catRoles = [
            "Doctor",
            "Dentist",
            "Apothecary",
            "PTA",
        ];
        return in_array($role, $catRoles);
    });

    $roles[] = new TkSsoCustomRole("UG2", function() {
        global $tkSsoUser;
        $role = $tkSsoUser->getRole();
        $catRoles = [
            "Doctor",
            "Dentist",
            "Apothecary",
            "Midwife",
            "PTA"
        ];
        return in_array($role, $catRoles);
    });

    $roles[] = new TkSsoCustomRole("Deutschland", function() {
        return false;
    });

    $roles[] = new TkSsoCustomRole("Oesterreich", function() {
        return false;
    });

    $roles[] = new TkSsoCustomRole("Abgemeldet", function() {
        global $tkSsoUser;
        return !$tkSsoUser->isLoggedIn();
    });

    return $roles;
}, 10, 1);
