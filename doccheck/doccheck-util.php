<?php

/**
 * @return bool
 */
function tkSsoIsDocCheckInstalled(): bool {
    return class_exists("DCL\Client\DCL_Client");
}

/**
 * @return \DCL\Client\DCL_Client
 */
function tkSsoGetDocCheckClient(): \DCL\Client\DCL_Client {
    return new DCL\Client\DCL_Client("dc-login", "1.1.0");
}
