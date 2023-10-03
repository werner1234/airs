<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/07/05 12:47:57 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070705_PREinstall.php,v $
 		Revision 1.1  2007/07/05 12:47:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");



$db = new DB;

$select = "SHOW TABLES LIKE 'HistorischePortefeuilleIndex'";
$db->SQL($select);

if (!$db->lookupRecord())
{
	$queries[] = "CREATE TABLE `HistorischePortefeuilleIndex` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) default NULL,
  `Datum` date default NULL,
  `IndexWaarde` decimal(7,4) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Portefeuille` (`Portefeuille`),
  KEY `Datum` (`Datum`)
)";	
}


for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20070705 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}



?>