<?php
class TkSsoJwtDecoder
{
    /**
     * Decode a JWT token without using libraries
     *
     * @param string $token The JWT token to decode
     *
     * @return array|object The decoded payload data
     */
    public static function decodeToken(string $token)
    {
        list(, $encodedPayload,) = explode('.', $token);

        $encodedPayload = str_replace('_', '/', str_replace('-', '+', $encodedPayload));
        return json_decode(base64_decode($encodedPayload), true);
    }

}

