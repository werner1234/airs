<?
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/07/26 07:42:38 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20060105_CRM_PREinstall.php,v $
 		Revision 1.1  2006/07/26 07:42:38  cvs
 		*** empty log message ***
 		
 		PRE installatie module, hier staan o.a. table wijzigingen in, bestand wordt eenmalig 
		uitgevoerd door vars.php en daarna automatisch gewist.
*/
$queries[] = "CREATE TABLE `Querydata` (
  `id` int(11) NOT NULL auto_increment,
  `Naam` varchar(35) NOT NULL default '',
  `Omschrijving` text NOT NULL,
  `Gebruiker` varchar(10) NOT NULL default '',
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `Data` text NOT NULL,
  `add_user` varchar(15) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
); ";

$queries[] = "CREATE TABLE `FondsParticipatieVerloop` (
  `id` int(11) NOT NULL auto_increment,
  `Fonds` varchar(25) NOT NULL default '',
  `Vermogensbeheerder` varchar(10) NOT NULL default '',
  `Datum` date NOT NULL default '0000-00-00',
  `Transactietype` varchar(5) NOT NULL default '',
  `Aantal` int(11) NOT NULL default '0',
  `add_user` varchar(15) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `Fonds` (`Fonds`)
); ";

$queries[] = "CREATE TABLE `AABTransaktieCodes` (
  `id` int(11) NOT NULL auto_increment,
  `code` varchar(10) default NULL,
  `actie` varchar(50) default NULL,
  `toelichting` tinytext,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);";

$queries[] = "ALTER TABLE `Fondsen` ADD `ABRCode` VARCHAR( 26 ) NOT NULL AFTER `AABCode` ;";

$queries[] = "ALTER TABLE `Fondsen` ADD `Portefeuille` VARCHAR( 12 ) NOT NULL AFTER `Huisfonds` ;";

$queries[] = "ALTER TABLE `Portefeuilles` ADD `InternDepot` TINYINT NOT NULL AFTER `SpecifiekeIndex` ;";

$queries[] = "ALTER TABLE `Valutas` ADD `TermijnValuta` TINYINT( 1 ) NOT NULL AFTER `AABcorrectie` ;";

$queries[] = "ALTER TABLE `Vermogensbeheerders` ADD `csvSeperator` VARCHAR( 1 ) DEFAULT ',' NOT NULL AFTER `AfdrukSortering` ;";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('1', '01010101', 'do_A', '', '2005-12-16 16:28:43', 'beheer', '2005-12-16 16:29:35', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('2', '01010401', 'do_A', '', '2005-12-16 16:30:04', 'beheer', '2005-12-16 16:30:04', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('3', '01010102', 'do_V', '', '2005-12-16 16:30:15', 'beheer', '2005-12-16 16:30:15', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('4', '01010402', 'do_V', '', '2005-12-16 16:30:28', 'beheer', '2005-12-16 16:30:28', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('5', '01010201', 'do_AO', '', '2005-12-16 16:30:46', 'beheer', '2005-12-16 16:30:46', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('6', '01010202', 'do_VO', '', '2005-12-16 16:30:57', 'beheer', '2005-12-16 16:30:57', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('7', '01040601', 'do_CD', '', '2005-12-16 16:31:07', 'beheer', '2005-12-16 16:31:07', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('8', '01040501', 'do_CR', 'Hiermee wordt de couponrente geboekt', '2005-12-16 16:31:21', 'beheer', '2005-12-19 13:56:31', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('9', '01040401', 'do_L', '', '2005-12-16 16:31:32', 'beheer', '2005-12-16 16:31:32', 'beheer');";

$queries[] = "INSERT INTO `AABTransaktieCodes` VALUES ('10', '01040402', 'do_D', '', '2005-12-16 16:31:45', 'beheer', '2005-12-16 16:31:45', 'beheer');";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20051219 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}
?>