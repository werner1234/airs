<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/10/17 08:33:26 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20061017_PREinstall.php,v $
 		Revision 1.1  2006/10/17 08:33:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/06/28 12:20:30  jwellner
 		*** empty log message ***
 		
*/
include("wwwvars.php");

$queries[] = "ALTER table Vermogensbeheerders ADD order_controle  text AFTER  Export_dag_pad; ";
//$queries[] = "ALTER table OrderRegels ADD controle tinyint AFTER status; ";
//$queries[] = "ALTER table OrderRegels ADD controle_regels text AFTER status; ";
//$queries[] = "ALTER table Orders ADD controle_datum datetime NOT NULL default '0000-00-00 00:00:00' AFTER memo; ";
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20060621 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}
?>