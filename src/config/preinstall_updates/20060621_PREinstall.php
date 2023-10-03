<?php
/*
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2006/06/28 12:20:30 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20060621_PREinstall.php,v $
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
*/
include("wwwvars.php");

$queries[] = " ALTER TABLE `ZorgplichtPerFonds` 	ADD `Percentage` 		INT NOT NULL DEFAULT '100' AFTER `Fonds`; ";
$queries[] = " ALTER TABLE `Fondsen` 							ADD `EindDatum` 		DATE NOT NULL; ";

$db = new DB;

$select = " SHOW TABLES LIKE 'Querydata' ";
$db->SQL($select);
if (!$db->lookupRecord())
{
	$table = "CREATE TABLE `Querydata` (
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
	);";
	$db->SQL($table);
	$db->Query();
}

$select = " SHOW COLUMNS FROM Querydata LIKE 'Type'; ";
$db->SQL($select);
if (!$db->lookupRecord())
{
	$q = " ALTER TABLE `Querydata` 						ADD `Type` 					VARCHAR( 15 ) NOT NULL; ";	
	$db->SQL($q);
	$db->Query();
}


for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060621 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}
?>