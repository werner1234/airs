<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:38:40 $
 		File Versie					: $Revision: 1.2 $

 		$Log: ajaxSetProfileName.php,v $
 		Revision 1.2  2018/07/24 06:38:40  cvs
 		call 7041
 		
 		Revision 1.1  2013/05/12 11:20:05  rvv
 		*** empty log message ***
 		


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_config.php");
session_start();
if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
session_start();

header('Content-type: text/plain');
$cfg= new AE_config();

if($_GET['veld'] <> '')
{
  $data=unserialize($cfg->getData($_GET['veld'])); 
  $data[$_GET['profile']]=$_GET['name'];
  $data=$cfg->addItem($_GET['veld'],serialize($data));
}

?>