<?php
/*
    AE-ICT sourcemodule created 23 Mar 2020
    Author              : Chris van Santen
    Filename            : wwwvars.php

    $Log: wwwvars.php,v $
    Revision 1.1  2020/03/23 13:04:14  cvs
    call 3205

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
