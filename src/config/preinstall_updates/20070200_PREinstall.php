<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/03/27 15:02:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070200_PREinstall.php,v $
 		Revision 1.1  2007/03/27 15:02:40  rvv
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


$queries[] = "CREATE TABLE `DepositoRentepercentages` (
  `id` int(11) NOT NULL auto_increment,
  `Rekening` varchar(20) default NULL,
  `DatumVan` datetime default NULL,
  `DatumTot` datetime default NULL,
  `Rentepercentage` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Rekening` (`Rekening`) )";



$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: Toeveoegen tabel uit regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}

$tst = new SQLman();

$tst->changeField("Portefeuilles","WerkelijkeDagen",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Fondsen","Rente30_360",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Rekeningen","RenteBerekenen",array("Type"=>"tinyint(4)","NULL"=>false));




?>