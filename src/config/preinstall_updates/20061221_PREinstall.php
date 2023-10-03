<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/12/21 16:10:31 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061221_PREinstall.php,v $
 		Revision 1.1  2006/12/21 16:10:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("BeleggingssectorPerFonds","AttributieCategorie",array("Type"=>"varchar(15)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","Attributie",array("Type"=>"char(1)","Null"=>false)); 



$queries[] = "CREATE TABLE `AttributieCategorien` (
  `id` int(11) NOT NULL auto_increment,
  `AttributieCategorie` varchar(15) default NULL,
  `Omschrijving` varchar(50) default NULL,
  `Afdrukvolgorde` tinyint(4) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ; ";

$queries[] = "CREATE TABLE `AttributiePerGrootboekrekening` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `AttributieCategorie` varchar(15) default NULL,
  `Grootboekrekening` varchar(5) default NULL,
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