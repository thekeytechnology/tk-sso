<?php


class TkSsoBroker
{

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->handleLogout();
    }


    public function validateAccessToken(): array
    {
        $accessToken = TkSsoUtils::getAccessToken();

        if (!$accessToken) return ['error' => 'Zugriffstoken ist nicht gesetzt.'];

        $decodedAccessToken = TkSsoJwtDecoder::decodeToken($accessToken);

        if (!isset($decodedAccessToken['exp']) || !isset($decodedAccessToken['isLoggedIn'])) {
            return ['error' => 'Ungültige Zugriffstoken-Struktur.'];
        }

        $currentTimestamp = time();
        if ($decodedAccessToken['exp'] <= $currentTimestamp) {
            return ['error' => 'Das Token ist abgelaufen.'];
        }

        if ($decodedAccessToken['isLoggedIn'] != 1) {
            $this->logout();
            return ['error' => 'Der Benutzer ist ausgeloggt.'];
        }
        return $decodedAccessToken;
    }


    public function getUserData($userVar = "")
    {
        $decodedAccessToken = $this->validateAccessToken();
        if (isset($decodedAccessToken['error'])) return '';
        if (!empty($userVar)) {
            $userVal = "";
            if ($userVar == "role") {
                $roleProcesses = $decodedAccessToken['roleApplicationProcesses'] ?? [];
                foreach ($roleProcesses as $role) {
                    if ($role['status'] == "Finished" && $role['userStatus'] == "Active") {
                        $userVal = $role['targetRole'];
                    }
                }
            } else {
                $userVal = $this->tkSearchArray($userVar, $decodedAccessToken);
            }
            if ($userVal) {
                return $userVal;
            }
        }
        return $decodedAccessToken;
    }

    public function isUserLoggedIn(): bool
    {
        $validateAccessToken = $this->validateAccessToken();
        return !isset($validateAccessToken['error']);
    }

    public function handleLogout()
    {
        if (isset($_GET['loggedOut']) && $_GET['loggedOut'] == true) {
            $this->logout();
        }
    }


    /**
     * Logs the user out of the browser and the server
     */
    public function logout()
    {

        $cookieManager = new TkSsoCookieManager();
        $cookieManager->setCookie(TkSsoUtils::ACCESS_TOKEN_NAME, '', -1);
        $cookieManager->setCookie(TkSsoUtils::ACCOUNT_ID_NAME, '', -1);
        $cookieManager->setCookie(TkSsoUtils::REFRESH_TOKEN_NAME, '', -1);

        unset($_COOKIE[TkSsoUtils::ACCESS_TOKEN_NAME]);
        unset($_COOKIE[TkSsoUtils::ACCOUNT_ID_NAME]);
        unset($_COOKIE[TkSsoUtils::REFRESH_TOKEN_NAME]);

    }


    public function createUrl($action = "login", $customRedirect = ""): string
    {
        $currentUrl = $this->cleanupUrl(
            $customRedirect ?: (get_home_url() . $_SERVER['REQUEST_URI'])
        );
        $base64EncodedUrl = base64_encode($currentUrl);
        $brand = TkSsoUtils::getBrandId();
        $url = TkSsoUtils::getFrontEndUrl();
        $urlSuffix = $action === 'logout' ? TkSsoUtils::LOGOUT_API : TkSsoUtils::LOGIN_API;
        return "{$url}{$urlSuffix}/{$brand}/{$base64EncodedUrl}";
    }

    private function cleanupUrl($url): string
    {
        $urlComponents = parse_url($url);

        if (!empty($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParams);

            // Remove specific parameters from the query
            unset($queryParams['loggedOut'], $queryParams['accountId'], $queryParams['refreshToken']);

            $urlComponents['query'] = http_build_query($queryParams);
        }

        // Reassemble the URL without the unwanted query parameters
        return $urlComponents['scheme'] . '://' . $urlComponents['host']
            . ($urlComponents['path'] ?? '')
            . (!empty($urlComponents['query']) ? '?' . $urlComponents['query'] : '');
    }

    /**
     * @param $key
     * @param $array
     * @return string
     */
    public function tkSearchArray($key, $array): string
    {
        if (is_array($array)) {
            $keyToLower = strtolower($key);
            $arrayToLower = array_change_key_case($array, CASE_LOWER);
            if (isset($arrayToLower[$keyToLower])) {
                return $arrayToLower[$keyToLower];
            }
            $value = "";
            foreach ($arrayToLower as $subarray) {
                $value .= $this->tkSearchArray($key, $subarray);
                if ($value != "") {
                    return $value;
                }
            }
        }
        return "";
    }




}

global $tkSsoBroker;
$tkSsoBroker = new TkSsoBroker();
