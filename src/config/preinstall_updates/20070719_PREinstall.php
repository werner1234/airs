<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/07/25 07:24:22 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070719_PREinstall.php,v $
 		Revision 1.1  2007/07/25 07:24:22  rvv
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
$select = "SHOW TABLES LIKE 'RapportXlsQuery'";
$db->SQL($select);
if (!$db->lookupRecord())
{
	$queries[] = "CREATE TABLE `RapportXlsQuery` (
  `id` int(11) NOT NULL auto_increment,
  `Naam` varchar(35) NOT NULL default '',
  `Omschrijving` text NOT NULL,
  `Gebruiker` varchar(10) NOT NULL default '',
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `Type` varchar(15) NOT NULL default '',
  `Data` text NOT NULL,
  `add_user` varchar(15) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
  ) ;";
}

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20070719 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}

$tst = new SQLman();

$tst->changeField("Portefeuilles","BeheerfeePerformanceDrempelPercentage",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Portefeuilles","BeheerfeePerformanceDrempelBedrag",array("Type"=>"double","Null"=>false)); 
$tst->changeField("Portefeuilles","BeheerfeeSchijvenTarief",array("Type"=>"tinyint","Null"=>false));
$tst->changeField("Portefeuilles","BeheerfeePerformancefeeJaarlijks",array("Type"=>"tinyint","Null"=>false));
$tst->changeField("Portefeuilles","BeheerfeeFacturatieVanaf",array("Type"=>"date","Null"=>false));
$tst->changeField("Portefeuilles","BeheerfeeFacturatieVooraf",array("Type"=>"tinyint","Null"=>false));

?>