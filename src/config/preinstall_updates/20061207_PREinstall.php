<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/12/07 16:10:48 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061207_PREinstall.php,v $
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");



$db = new DB;

$select = "SHOW TABLES LIKE 'ae_modulecfg'";
$db->SQL($select);
if (!$db->lookupRecord())
{
	$queries[] = "CREATE TABLE `ae_modulecfg` (
	`id` int(11) NOT NULL auto_increment,
	`moduleName` varchar(20) default NULL,
	`moduleChecksum` varchar(64) default NULL,
	`moduleExpires` date default NULL,
	`bedrijf` varchar(50) default NULL,
	`add_date` date default NULL,
	`add_user` varchar(15) default NULL,
	PRIMARY KEY  (`id`)
	) ENGINE=MyISAM;";	
}

$queries[] = "DELETE FROM ae_modulecfg WHERE bedrijf = 'HEK'";
$queries[] = "DELETE FROM ae_modulecfg WHERE bedrijf = 'VLC'";
$queries[] = "DELETE FROM ae_modulecfg WHERE bedrijf = 'THB'";


$queries[] = "INSERT INTO ae_modulecfg (moduleName, moduleChecksum, moduleExpires, bedrijf) VALUES  ('ORDER', '240834-32976-1177500', '2010-12-31', 'HEK');";
$queries[] = "INSERT INTO ae_modulecfg (moduleName, moduleChecksum, moduleExpires, bedrijf) VALUES  ('CRM', '240834-32318-1117500', '2010-12-31', 'VLC');";
$queries[] = "INSERT INTO ae_modulecfg (moduleName, moduleChecksum, moduleExpires, bedrijf) VALUES  ('ORDER', '240834-32747-1117500', '2010-12-31', 'VLC');";
$queries[] = "INSERT INTO ae_modulecfg (moduleName, moduleChecksum, moduleExpires, bedrijf) VALUES  ('CRM', '240834-31188-1110000' , '2010-12-31', 'THB');";
$queries[] = "INSERT INTO ae_modulecfg (moduleName, moduleChecksum, moduleExpires, bedrijf) VALUES  ('ORDER', '240834-31602-1110000', '2010-12-31', 'THB');";



for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20061206 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}



?>