<?php

class TkSsoRequestManager {



    public function request($url, string $method, $data = null)
    {
        $data = $method == "GET" ? $data : json_encode($data);

        // Get the absolute path to the current directory
        $currentDirectory = __DIR__;

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_COOKIE, ''); // Add your cookie if needed

        if (TkSsoUtils::useStagingApi()) {
            $certificatePath = $currentDirectory . '/../cert.pem';
            if (file_exists($certificatePath)) {
                curl_setopt($ch, CURLOPT_SSLCERT, $certificatePath);
            }
        }

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = 'Fehler: ' . curl_error($ch);
            // Output additional debugging information
            echo "cURL Error: $error\n";
            return ['error' => $error];
        }

        // Check HTTP response code
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code !== 200) {
            $error = 'Fehler: ' . $http_code . '. Leider gibt es aktuell technische Probleme. Wir arbeiten bereits an einer Lösung.';
            return ['error' => $error];
        }

        // Close cURL session
        curl_close($ch);

        // Parse and return JSON response
        return json_decode($response, true);
    }
}


