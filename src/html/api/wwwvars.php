<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/01/24 14:59:45 $
    File Versie         : $Revision: 1.2 $

    $Log: wwwvars.php,v $
    Revision 1.2  2018/01/24 14:59:45  cvs
    call 6527

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

if (file_exists("apiVars.php"))
{
  $skipLogScherm = true;
  $__appvar['date_seperator'] = "-";
  $p = explode("html/", getcwd());
  $__appvar['base_dir']       = $p[0];


  include_once "apiFunctions.php";
  include_once "apiVars.php";
	include_once "apiAuth.php";
  if (!isset($__glob["julOffset"]))  // defaults gebruikt door encodeJul/decodeJul
  {
    $__glob["julOffset"]  = 1577836800; // julian offset date 01-01-2020 GMT
  }
  if (!isset($__glob["julTimeOut"]))  // defaults gebruikt door encodeJul/decodeJul
  {
    $__glob["julTimeOut"] = 660; // sec link geldig in documentLink list
  }
  include_once ("../../classes/portefeuilleSelectieClass.php");
	include_once "../../config/applicatie_functies.php";
  include_once "../../classes/AE_cls_config.php";
  include_once "../../classes/AE_cls_mysql.php";
  include_once "../../classes/mysqlObject.php";
	include_once "../../classes/mysqlTable.php";
	include_once "../../config/CRM_vars.php";
	include_once "../../classes/records/CRM_naw.php";
}
else
{
  header("HTTP/1.0 404 Not Found");
	exit;
}
