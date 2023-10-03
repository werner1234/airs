<?php
/*
  Author                                                : Egon Verbakel
  Laatste aanpassing            : 02-08-2021
  File Versie                                   : 0.1

  Retrieve AE Config variable
*/

$baseDir =  realpath(dirname(__FILE__)."/../../");
include_once( $baseDir."/config/local_vars.php");
include_once( $baseDir."/config/applicatie_functies.php");
include_once( $baseDir."/classes/AE_cls_mysql.php");
include_once( $baseDir."/classes/AE_cls_config.php");
include_once( $baseDir."/config/checkLoggedIn.php");

if (count($_GET) == 0)
{
  exit;
}

error_reporting(0);

$config = new AE_Config();
$dataStr = $config->getData($_GET["field"]);
$returnData = array("data" => null);

if( !empty($dataStr) ) {
  $returnData["data"] = $dataStr;
  if (isSerialized($dataStr)) {
    $returnData["data"] = @unserialize($dataStr);
  }
}

header('Content-Type: application/json');
echo json_encode($returnData);