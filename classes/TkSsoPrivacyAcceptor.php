<?php

class TkSsoPrivacyAcceptor
{

    private TkSsoRequestManager $requestManager;
    private string $accessToken;
    private string $accountId;
    private string $base64BrandId;
    private string $acceptanceText;
    private string $endpointUrl;
    const ACCEPTANCE_TEXT = 'Datenschutz Akzeptieren';

    /**
     * Class constructor
     * @param TkSsoRequestManager $requestManager
     * @param string $endpointUrl
     * @param string $accessToken Access token string
     * @param string $accountId Account ID string
     * @param string $base64BrandId Base64 encoded brand ID string
     * @param string $acceptanceText Text for acceptance
     */
    public function __construct(TkSsoRequestManager $requestManager, string $endpointUrl, string $accessToken, string $accountId, string $base64BrandId, string $acceptanceText)
    {
        $this->requestManager = $requestManager;
        $this->accessToken = $accessToken;
        $this->accountId = $accountId;
        $this->base64BrandId = $base64BrandId;
        $this->acceptanceText = $acceptanceText;
        $this->endpointUrl = $endpointUrl;
    }

    /**
     * @return string[]
     */
    public function acceptPrivacy(): array
    {
        $method = 'POST';
        $data = [
            'accessToken' => $this->accessToken,
            'accountId' => urldecode($this->accessToken),
            'base64BrandId' => $this->base64BrandId,
            'acceptanceText' => self::ACCEPTANCE_TEXT
        ];

        $response = $this->requestManager->request($this->endpointUrl, $method, $data);

        if (isset($response['error'])) {
            return ['error' => 'Fehlerhafte Antwort vom Server.'];
        }

        return ['success' => '200'];
    }

}
