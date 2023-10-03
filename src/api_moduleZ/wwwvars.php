<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/02/06 08:14:15 $
    File Versie         : $Revision: 1.3 $

    $Log: wwwvars.php,v $
    Revision 1.3  2019/02/06 08:14:15  cvs
    call 7488

    Revision 1.2  2018/09/14 09:32:24  cvs
    update 14-9-2018

    Revision 1.1  2018/03/16 11:13:50  cvs
    call 6710

    Revision 1.1  2017/08/18 14:41:16  cvs
    call 5815



*/

if (file_exists("apiVars.php"))
{
  $__appvar['date_seperator'] = "-";
  $p = explode("html/", getcwd());
  $__appvar['base_dir']       = $p[0];

  include_once "apiFunctions.php";
  include_once "apiVars.php";
	include_once "apiAuth.php";
	$jsonFile = "apiGlobals.json";
	$dayOfTheYear = date("z");
	if (file_exists($jsonFile))
  {
    $apiGlob = json_decode(file_get_contents($jsonFile));
  }
	else
  {
    $apiGlob = array(
      "created" => date("Y-m-d H:i:s"),
      "lastPurge" => date("z"),
    );
    saveApiGlob($apiGlob);
  }

//	if ($dayOfTheYear > $apiGlob["lastPurge"])
//  {
//    purgeContentFiles($apiGlob["lastPurge"]);
//  }


	include_once "../config/applicatie_functies_minimal.php";
  include_once "../classes/AE_cls_config.php";
  include_once "../classes/AE_cls_mysql.php";
  include_once "../classes/mysqlObject.php";
	include_once "../classes/mysqlTable.php";
//	include_once "../config/CRM_vars.php";
//	include_once "../classes/records/CRM_naw.php";

}
else
{
  header("HTTP/1.0 404 Not Found");
	exit;
}
