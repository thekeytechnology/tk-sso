<?php

function tkAcceptPrivacy()
{
    require_once "../../../../wp-load.php";
    require_once "../classes/TkSsoUtils.php";
    require_once "../classes/TkSsoRequestManager.php";
    require_once "../classes/TkSsoPrivacyAcceptor.php";

    $tkSsoRequestManager = new TkSsoRequestManager();
    $endpointUrl = TkSsoUtils::getServerUrl() . TkSsoUtils::ACCEPT_PRIVACY_API;
    $accessToken = TkSsoUtils::getAccessToken();
    $accountId = TkSsoUtils::getAccountId();
    $base64BrandId = TkSsoUtils::getBrandId();
    $acceptanceText = TkSsoUtils::ACCEPTANCE_TEXT;

    $tkPrivacyAcceptor = new TkSsoPrivacyAcceptor(
        $tkSsoRequestManager,
        $endpointUrl,
        $accessToken,
        $accountId,
        $base64BrandId,
        $acceptanceText
    );

    $acceptPrivacy = $tkPrivacyAcceptor->acceptPrivacy();

    if (isset($acceptPrivacy['error'])) {
        wp_send_json_error(['message' => $acceptPrivacy['error']]);
        return;
    }

    wp_send_json_success(['message' => 'Privacy accepted successfully.']);
}

tkAcceptPrivacy();
