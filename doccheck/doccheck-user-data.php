<?php

function tkSsoDocCheckUserDataFilter($value, $key) {
    //Prevent loop
    if ($key == "role") {
        return $value;
    }

    global $tkSsoUser;
    if ($tkSsoUser->hasRole([TkSsoRoleManager::$ROLE_DOCCHECK_LOGGED_IN])) {
        if ($key == "e-mail") {
            return "";
        }

        if ($key == "onekeyId") {
            return "DocCheck Nutzer";
        }
    }

    return $value;
}

add_filter(TkSsoUser::FILTER_DATA, "tkSsoDocCheckUserDataFilter", 10, 2);