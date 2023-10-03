<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/23 06:22:58 $
 		File Versie					: $Revision: 1.2 $

 		$Log: 20061023_PREinstall.php,v $
 		Revision 1.2  2006/10/23 06:22:58  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/10/23 06:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$queries[] = "CREATE TABLE `Regios` (
  `id` int(11) NOT NULL auto_increment,
  `Regio` varchar(15) default NULL,
  `Omschrijving` varchar(50) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
 ) ENGINE=MyISAM; ";


$queries[] = "DROP TABLE IF EXISTS tmpOrder; ";
$queries[] = "DROP TABLE IF EXISTS Orders; ";
$queries[] = "DROP TABLE IF EXISTS OrderRegels; ";
$queries[] = 
"CREATE TABLE `Orders` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Ordernummer',
  `vermogensBeheerder` varchar(5) NOT NULL default '',
  `orderid` varchar(16) NOT NULL default '',
  `aantal` double NOT NULL default '0',
  `fondsCode` varchar(25) NOT NULL default '',
  `fonds` varchar(50) NOT NULL default '',
  `transactieType` varchar(4) NOT NULL default '' COMMENT 'L-limiet B-bestens SL-Stoploss SLIM-StopLimiet',
  `transactieSoort` char(2) NOT NULL default '' COMMENT 'A-aankoop B-verkoop',
  `tijdsLimiet` date NOT NULL default '0000-00-00',
  `tijdsSoort` char(3) NOT NULL default '',
  `koersLimiet` double(12,5) NOT NULL default '0.00000',
  `status` text NOT NULL COMMENT 'Serialied overzicht statussen',
  `laatsteStatus` varchar(15) NOT NULL default '' COMMENT 'laatst bereikte status',
  `memo` text NOT NULL,
  `controle_datum` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$queries[] =
 "CREATE TABLE `OrderRegels` (
  `id` int(11) NOT NULL auto_increment,
  `orderid` varchar(16) NOT NULL default '',
  `positie` int(3) unsigned NOT NULL default '0',
  `portefeuille` varchar(20) NOT NULL default '',
  `rekeningnr` varchar(20) NOT NULL default '',
  `valuta` varchar(6) NOT NULL default '',
  `aantal` double(12,4) NOT NULL default '0.0000',
  `client` varchar(60) NOT NULL default '',
  `status` varchar(60) NOT NULL default '',
  `controle_regels` text,
  `controle` tinyint(4) default NULL,
  `memo` varchar(100) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060621 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","grafiek_kleur",array("Type"=>"text", "Null"=>false));
$tst->changeField("Fondsen","Lossingsdatum",array("Type"=>"date NOT NULL default '0000-00-00'","Null"=>false) ); 
$tst->changeField("TijdelijkeRapportage","Regio",array("Type"=>"varchar(15)","Null"=>false)); 
$tst->changeField("TijdelijkeRapportage","Lossingsdatum",array("Type"=>"date NOT NULL default '0000-00-00'","Null"=>false)); 
$tst->changeField("BeleggingssectorPerFonds","Regio",array("Type"=>"varchar(15)","Null"=>false)); 
$tst->changeField("Regios","Afdrukvolgorde",array("Type"=>" tinyint(4)","Null"=>false)); 

$tst->changeField("Vermogensbeheerders","order_controle",array("Type"=>"text","Null"=>false)); 

?>