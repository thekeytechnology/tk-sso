<?php

class TkSsoAuthenticator
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->handleRedirectAfterLogin();
        $this->authenticate();
    }

    public function handleRedirectAfterLogin()
    {
        $accountIdName = TkSsoUtils::ACCOUNT_ID_NAME;
        $refreshTokenName = TkSsoUtils::REFRESH_TOKEN_NAME;

        if (isset($_GET[$accountIdName], $_GET[$refreshTokenName]) &&
            !empty($_GET[$accountIdName]) && !empty($_GET[$refreshTokenName])) {
            $accountId = sanitize_text_field($_GET[$accountIdName]);
            $refreshToken = sanitize_text_field($_GET[$refreshTokenName]);
            $cookieManager = new TkSsoCookieManager();
            $cookieManager->setCookie($accountIdName, $accountId);
            $cookieManager->setCookie($refreshTokenName, $refreshToken);
        }
    }

    /**
     * @return array
     */
    public function authenticate(): array
    {
        $accountId = TkSsoUtils::getAccountId();
        $refreshToken = TkSsoUtils::getRefreshToken();

        if (!$accountId || !$refreshToken) {
            return ['error' => 'Refresh-Token oder KontoID sind nicht gesetzt.'];
        }

        $requestManager = new TkSsoRequestManager();
        $url = TkSsoUtils::getServerUrl() . TkSsoUtils::AUTHENTICATE_API;
        $method = 'POST';
        $data = [
            'refreshToken' => $refreshToken,
            'accountId' => urldecode($accountId)
        ];
        $response = $requestManager->request($url, $method, $data);

        if (!is_array($response) || isset($response['error'])) {
            return ['error' => 'Fehlerhafte Antwort vom Server.'];
        }

        $cookieManager = new TkSsoCookieManager();

        if (isset($response['accessToken']) && !empty($response['accessToken'])) {
            $accessToken = $response['accessToken'];
            $cookieManager->setCookie(TkSsoUtils::ACCESS_TOKEN_NAME, $accessToken);
            return ['success', 'Benutzer ist angemeldet'];
        }
        else {
            $cookieManager->setCookie(TkSsoUtils::ACCESS_TOKEN_NAME, '', -1);
            unset($_COOKIE[TkSsoUtils::ACCESS_TOKEN_NAME]);
        }

        return ['error' => 'Benutzer konnte nicht authentifiziert werden.'];

    }

}

$tkSsoAuthenticator = new TkSsoAuthenticator();
