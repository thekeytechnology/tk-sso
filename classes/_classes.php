<?php
require_once "TkSsoFrontEndCache.php";
require_once "TkSsoBroker.php";
require_once "TkDrupalSsoBroker.php";
require_once "TkUsSsoBroker.php";
require_once "TkSsoUser.php";

global $tkSsoBroker;
$tkUseSsoV2 = get_option("tkt_use_sso_v2");
$tkSsoBroker = $tkUseSsoV2 ? new TkUsSsoBroker() : new TkDrupalSsoBroker();