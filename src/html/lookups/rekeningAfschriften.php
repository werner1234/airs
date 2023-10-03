<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:42:52 $
 		File Versie					: $Revision: 1.2 $
*/

/**
 * Ajax Lookup
 * 
 * @author RM
 * @since 16-9-2014
 * 
 * Loads local data including vars and databases
 * 
 * Loads the ajax class
 * 
 */
include_once('../../config/local_vars.php');
include_once('../../config/vars.php');
include_once('../../config/applicatie_functies.php');
include_once('../../classes/AE_cls_mysql.php');
include_once('../../classes/mysqlTable.php');


$data = array_merge($_POST, $_GET);

require("../../config/checkLoggedIn.php");

if (trim($data["type"]) == "")
{
  exit;
}

$rekeningAfschriften = new AE_RekeningMutatiesAjax ($data);

if (
  method_exists($rekeningAfschriften, $data['type'])
  && is_callable(array($rekeningAfschriften, $data['type'])))
{
    call_user_func(array(
      $rekeningAfschriften, 
      $data['type']
    ));
}