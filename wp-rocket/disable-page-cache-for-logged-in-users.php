<?php

/**
 * Filter the cookies rejected by WP Rocket.
 *
 * Add the SESStkssocookie to the list of cookies, to serve dynamic content when this cookie is present.
 *
 * @param array $cookies The current list of cookies rejected by WP Rocket.
 * @return array Updated list of cookies.
 */
add_filter('rocket_cache_reject_cookies', 'tkDisablePageCacheForLoggedInUsers');

function tkDisablePageCacheForLoggedInUsers($cookies)
{
    $refreshTokenName = TkSsoUtils::REFRESH_TOKEN_NAME;
    $accessTokenName = TkSsoUtils::ACCESS_TOKEN_NAME;
    $accountIdName = TkSsoUtils::ACCOUNT_ID_NAME;

    $cookies[] = $refreshTokenName;
    $cookies[] = $accessTokenName;
    $cookies[] = $accountIdName;

    return $cookies;
}


