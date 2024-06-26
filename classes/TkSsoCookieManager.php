<?php

class TkSsoCookieManager
{
    public function setCookie(
        string $name, string $value,
        int $expire = null, string $path = "/", string $samesite = 'None'
    ) {
        if ($expire === null) {
            $expire = time() + TkSsoUtils::COOKIE_LIFETIME;
        }

        $current_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $domain = $this->getDomain($current_url);

        $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

        $_COOKIE[$name] = $value;

        $cookieOptions = [
            'expires' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => false,
        ];

        if ($secure) {
            $cookieOptions['samesite'] = $samesite;
        }

        setcookie($name, $value, $cookieOptions);
    }

    private function getDomain(string $currentUrl): string
    {
        switch (true) {
            case str_contains($currentUrl, 'paedia'):
                return '.paedia.de';
            case str_contains($currentUrl, 'data-storage'):
                return '.data-storage.live';
            case str_contains($currentUrl, 'consilium'):
                return '.consilium.live';
            case str_contains($currentUrl, 'slenyto'):
                return '.slenyto.de';
            case str_contains($currentUrl, 'infectopharm'):
                return '.infectopharm.com';
            default:
                return '';
        }
    }
}


