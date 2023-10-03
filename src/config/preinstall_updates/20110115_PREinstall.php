<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/01/26 17:18:16 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20110115_PREinstall.php,v $
 		Revision 1.1  2011/01/26 17:18:16  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
		 
$tst = new SQLman();

$tst->changeField("Gebruikers","verzendrechten",array("Type"=>"tinyint","Null"=>false)); 

$bedrijven=array('FCM','ECO','WWO','AEI');
if(in_array(strtoupper($__appvar["bedrijf"]),$bedrijven))
{
  $db=new DB();
  $query="ALTER TABLE Fondsen AUTO_INCREMENT = 100000000";
  $db->SQL($query);
  $db->Query();
  $query="ALTER TABLE Fondskoersen AUTO_INCREMENT = 100000000";
  $db->SQL($query);
  $db->Query();
} 





?>