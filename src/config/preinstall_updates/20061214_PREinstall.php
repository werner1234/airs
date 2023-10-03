<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/12/14 11:52:34 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061214_PREinstall.php,v $
 		Revision 1.1  2006/12/14 11:52:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");



$queries[] = "
CREATE TABLE `ValutaPerRegio` (
  `id` int(11) NOT NULL auto_increment,
  `Valuta` varchar(4) default NULL,
  `Regio` varchar(15) default NULL,
  `Vermogensbeheerder` varchar(10) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
); ";

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