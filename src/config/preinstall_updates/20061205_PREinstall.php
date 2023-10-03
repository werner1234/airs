<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/03/27 15:02:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061205_PREinstall.php,v $
 		Revision 1.1  2007/03/27 15:02:40  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$queries[] = "CREATE TABLE `ModelPortefeuilles` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) default NULL,
  `Omschrijving` varchar(50) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM; ";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20061206 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}



?>