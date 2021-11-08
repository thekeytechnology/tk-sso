<?php

include_once("doccheck-util.php");

if (tkSsoIsDocCheckInstalled()) {
    include_once("remove-doccheck-meta-box.php");
    include_once("doccheck-roles.php");
}

