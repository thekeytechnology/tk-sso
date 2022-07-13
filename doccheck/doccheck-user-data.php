<?php

function tkSsoDocCheckUserDataFilter($value, $key) {

    $key = strtolower($key);
    // This prevents a loop, since we are querying the role in this function
    if ($key == "role") {
        return $value;
    }

    global $tkSsoUser;
    if ($tkSsoUser->hasRole([TkSsoDocCheckRoleManager::$ROLE_DOCCHECK_LOGGED_IN])) {
        if ($key == "email") {
            return "";
        }

        if ($key == "onekeyid") {
            return "DocCheck Nutzer";
        }
    }

    return $value;
}

add_filter(TkSsoUser::$FILTER_DATA, "tkSsoDocCheckUserDataFilter", 10, 2);