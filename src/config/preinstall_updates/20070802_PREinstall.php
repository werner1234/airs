<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/08/02 14:36:18 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070802_PREinstall.php,v $
 		Revision 1.1  2007/08/02 14:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/21 16:10:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");




$db = new DB;
$select = "SHOW TABLES LIKE 'GeconsolideerdePortefeuilles'";
$db->SQL($select);
if (!$db->lookupRecord())
{
	$queries[] = "CREATE TABLE `GeconsolideerdePortefeuilles` (
  `id` int(11) NOT NULL auto_increment,
  `VirtuelePortefeuille` varchar(12) default NULL,
  `Portefeuille1` varchar(12) default NULL,
  `Portefeuille2` varchar(12) default NULL,
  `Portefeuille3` varchar(12) default NULL,
  `Portefeuille4` varchar(12) default NULL,
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `Client` varchar(10) NOT NULL default '',
  `Naam` varchar(50) default NULL,
  `Naam1` varchar(50) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);";
}

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20070725 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}


?>