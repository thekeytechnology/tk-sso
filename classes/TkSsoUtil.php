<?php

class TkSsoUtil {

    public static function getApiVersion(): string {
        return get_option("tkt_use_sso_v2") ? "2" : "1";
    }

    public static function getApiUrl(): string {
        return get_option('tkt_sso_server_url');
    }
}