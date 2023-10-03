<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.3 $

 		$Log: backOfficeSelectieOpties.php,v $
 		Revision 1.3  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.2  2018/07/24 06:38:40  cvs
 		call 7041
 		
 		Revision 1.1  2010/11/14 10:56:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/08/06 16:35:32  rvv
 		*** empty log message ***

*/
if (!isset($_SESSION)) 
{
  session_start();
}
include_once("../../classes/selectOptieClass.php");

if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
$einddatumFilterVerwijderen = null;
if ( isset ($_GET['einddatumFilterVerwijderen']) ) {
  $einddatumFilterVerwijderen = ((int) $_GET['einddatumFilterVerwijderen'] === 1?true:false);
}

$selectie = new selectOptie(null, $einddatumFilterVerwijderen);
$theQuery = $selectie->getQuery($search);
$velden = array("num");
?>