<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/01/30 07:24:03 $
    File Versie         : $Revision: 1.1 $

    $Log: wwwvars.php,v $
    Revision 1.1  2020/01/30 07:24:03  cvs
    call 8380

    Revision 1.1  2018/09/28 11:33:34  cvs
    call 7097



*/

if (file_exists("apiVars.php"))
{

  $__appvar['date_seperator'] = "-";
  $p = explode("html/", getcwd());
  $__appvar['base_dir']       = $p[0];

  include_once "apiFunctions.php";
  include_once "apiVars.php";
  include_once "apiAuth.php";

  include_once $__appvar['base_dir']."/../config/applicatie_functies_minimal.php";
  include_once $__appvar['base_dir']."/../classes/AE_cls_config.php";
  include_once $__appvar['base_dir']."/../classes/AE_cls_mysql.php";
  include_once $__appvar['base_dir']."/../classes/mysqlObject.php";
  include_once $__appvar['base_dir']."/../classes/mysqlTable.php";
//	include_once "../config/CRM_vars.php";
//	include_once "../classes/records/CRM_naw.php";

}
else
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
