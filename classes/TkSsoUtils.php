<?php


class TkSsoUtils
{

    const PRODUCTION_SERVER_URL = 'https://api.identity.infectopharm.com/api/';
    const PRODUCTION_FRONTEND_URL = 'https://identity.infectopharm.com/';

    const STAGING_SERVER_URL = 'https://staging.api.identity.infectopharm.com/api/';
    const STAGING_FRONTEND_URL = 'https://staging.identity.infectopharm.com/';

    const LOGOUT_API = 'logout';
    const LOGIN_API = 'login';
    const AUTHENTICATE_API = 'refresh-token';
    const ACCEPT_PRIVACY_API = 'accept-privacy';

    const ACCOUNT_ID_NAME = 'accountId';
    const REFRESH_TOKEN_NAME = 'refreshToken';
    const ACCESS_TOKEN_NAME = 'tkAccessToken';

    const HAS_UNACCEPTED_PRIVACY_COOKIE_NAME = 'hasUnacceptedPrivacy';
    const ACCEPTANCE_TEXT = 'Datenschutz Akzeptieren';

    const COOKIE_LIFETIME = 86400;


    public static function getServerUrl()
    {
        return self::useStagingApi() ? self::STAGING_SERVER_URL : self::PRODUCTION_SERVER_URL;
    }

    public static function getFrontEndUrl()
    {
        return self::useStagingApi() ? self::STAGING_FRONTEND_URL : self::PRODUCTION_FRONTEND_URL;
    }

    public static function getCookie($cookieName)
    {
        return $_COOKIE[$cookieName] ?? false;
    }

    public static function getAccountId()
    {
        return self::getCookie(self::ACCOUNT_ID_NAME);
    }

    public static function getRefreshToken()
    {
        return self::getCookie(self::REFRESH_TOKEN_NAME);
    }

    public static function getAccessToken()
    {
        return self::getCookie(self::ACCESS_TOKEN_NAME);
    }

    public static function getBrandId()
    {
        return get_option('tkt_broker_id');
    }

    public static function useStagingApi()
    {
        return get_option('tkt_use_staging_api');
    }
}


