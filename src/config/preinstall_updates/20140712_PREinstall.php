<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/09 15:05:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140712_PREinstall.php,v $
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$db = new DB();
$query="DELETE FROM Gebruikers WHERE Gebruiker IN('TNT','EVH','DPL','LPG','JTN','MHM','FEGT','KRL','VKS','SEYN')";
$db->SQL($query);
$db->Query();
$query="DELETE FROM VermogensbeheerdersPerGebruiker WHERE Gebruiker IN('TNT','EVH','DPL','LPG','JTN','MHM','FEGT','KRL','VKS','SEYN')";
$db->SQL($query);
$db->Query();

?>