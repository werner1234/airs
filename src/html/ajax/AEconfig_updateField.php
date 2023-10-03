<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:36:05 $
    File Versie         : $Revision: 1.2 $

    $Log: AEconfig_updateField.php,v $
    Revision 1.2  2018/07/24 06:36:05  cvs
    call 7041

    Revision 1.1  2017/12/15 07:44:19  cvs
    call 6205


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_config.php");
require("../../config/checkLoggedIn.php");

if (trim($_GET["value"]) == "")
{
  exit;
}

$value = $_GET["value"];


$field = trim($_GET['field']);
$value = $_GET['value'];

if ($field == "")
{
  exit;
}

$cfg = new AE_config();
$cfg->addItem($field, $value);

