<?php
require_once "TkSsoFrontEndCache.php";
require_once "TkSsoBroker.php";
require_once "TkDrupalSsoBroker.php";
require_once "TkUsSsoBroker.php";
require_once "TkSsoRoleManager.php";
require_once "TkSsoUser.php";
require_once "TkSsoUtil.php";

global $tkSsoBroker;
$tkSsoBroker = TkSsoUtil::getApiVersion() == "2" ? new TkUsSsoBroker() : new TkDrupalSsoBroker();