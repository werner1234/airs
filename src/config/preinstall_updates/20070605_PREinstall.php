<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/06/06 15:08:18 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070605_PREinstall.php,v $
 		Revision 1.1  2007/06/06 15:08:18  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/05/07 14:52:26  rvv
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

$tablesDefs = array("ae_config" =>"CREATE TABLE `ae_config` (
  `id` bigint(20) NOT NULL auto_increment,
  `field` varchar(30) NOT NULL default '',
  `value` text,
  `lock` tinyint(3) default '0',
  `memo` text,
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "ae_modulecfg" =>"CREATE TABLE `ae_modulecfg` (
  `id` int(11) NOT NULL auto_increment,
  `moduleName` varchar(20) default NULL,
  `moduleChecksum` varchar(64) default NULL,
  `moduleExpires` date default NULL,
  `bedrijf` varchar(50) default NULL,
  `add_date` date default NULL,
  `add_user` varchar(15) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "ae_log" => "CREATE TABLE `ae_log` (
  `id` int(11) NOT NULL auto_increment,
  `txt` text,
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

                "CRM_naw" =>"CREATE TABLE `CRM_naw` (
  `id` bigint(20) NOT NULL auto_increment,
  `naam` tinytext,
  `zoekveld` varchar(50) default NULL,
  `portefeuille` varchar(20) default NULL,
  `titel` varchar(25) default NULL,
  `voorvoegsel` varchar(15) default NULL,
  `voorletters` varchar(15) default NULL,
  `voornamen` varchar(35) default NULL,
  `roepnaam` varchar(25) default NULL,
  `tussenvoegsel` varchar(15) default NULL,
  `achternaam` varchar(64) default NULL,
  `beroep` varchar(15) default NULL,
  `geboortedatum` date default NULL,
  `geslacht` varchar(5) default NULL,
  `huwelijkseStaat` varchar(40) default NULL,
  `nationaliteit` varchar(15) default NULL,
  `ingezetene` tinyint(4) default NULL,
  `legitimatie` varchar(15) default NULL,
  `nummerID` varchar(15) default NULL,
  `landID` varchar(15) default NULL,
  `plaatsID` varchar(25) default NULL,
  `part_naam` tinytext,
  `part_zoekveld` varchar(50) default NULL,
  `part_titel` varchar(25) default NULL,
  `part_voorvoegsel` varchar(15) default NULL,
  `part_voorletters` varchar(15) default NULL,
  `part_voornamen` varchar(35) default NULL,
  `part_roepnaam` varchar(25) default NULL,
  `part_tussenvoegsel` varchar(15) default NULL,
  `part_achternaam` varchar(64) default NULL,
  `part_beroep` varchar(15) default NULL,
  `part_geboortedatum` date default NULL,
  `part_geslacht` varchar(5) default NULL,
  `part_nationaliteit` varchar(15) default NULL,
  `part_ingezetene` tinyint(4) default NULL,
  `part_legitimatie` varchar(15) default NULL,
  `part_nummerID` varchar(15) default NULL,
  `part_landID` varchar(15) default NULL,
  `part_plaatsID` varchar(25) default NULL,
  `adres` tinytext,
  `pc` varchar(17) default NULL,
  `plaats` varchar(30) default NULL,
  `land` varchar(25) default NULL,
  `tel1_oms` varchar(20) default NULL,
  `tel1` varchar(20) NOT NULL default '',
  `tel2_oms` varchar(20) default NULL,
  `tel2` varchar(20) default NULL,
  `fax` varchar(20) default NULL,
  `debiteur` tinyint(4) default NULL,
  `crediteur` tinyint(4) default NULL,
  `crediteurnr` varchar(20) default NULL,
  `debiteurnr` varchar(20) default NULL,
  `prospect` tinyint(4) default NULL,
  `overige` tinyint(4) default NULL,
  `website` tinytext,
  `email` tinytext,
  `kvknr` varchar(15) default NULL,
  `btwnr` varchar(15) default NULL,
  `ondernemingsvorm` varchar(15) default NULL,
  `accountant` text,
  `belastingadviseur` text,
  `assurantietussenpersoon` text,
  `overigeAdviseurs` text,
  `tag` tinyint(4) default NULL,
  `tag_date` date default NULL,
  `mut_dat` date default NULL,
  `mut_usr` varchar(5) default NULL,
  `memo` text,
  `aktief` tinyint(4) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "CRM_naw_documenten"=>"CREATE TABLE `CRM_naw_documenten` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` int(11) default '0',
  `bestandsnaam` varchar(100) default NULL,
  `omschrijving` text,
  `add_user` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "CRM_naw_dossier"=>"CREATE TABLE `CRM_naw_dossier` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `datum` datetime default NULL,
  `kop` tinytext,
  `txt` text,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1",

                "CRM_naw_kontaktpersoon"=>"CREATE TABLE `CRM_naw_kontaktpersoon` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `naam` tinytext,
  `sortering` varchar(20) default NULL,
  `functie` varchar(30) default NULL,
  `tel1` varchar(20) default NULL,
  `tel1_oms` varchar(20) default NULL,
  `tel2` varchar(20) default NULL,
  `tel2_oms` varchar(20) default NULL,
  `email` tinytext,
  `crm_password` varchar(50) NOT NULL default '',
  `crm_login` tinyint(1) NOT NULL default '0',
  `crm_lastseen` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `memo` text,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1",

                "CRM_selectievelden"=>"CREATE TABLE `CRM_selectievelden` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(40) default NULL,
  `waarde` varchar(40) default NULL,
  `omschrijving` varchar(60) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "CRM_faq" => "CREATE TABLE `CRM_faq` (
  `id` int(11) NOT NULL auto_increment,
  `kop` tinytext,
  `sectie` int(11) default NULL,
  `txt` text,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                "CRM_faq_ow" => "CREATE TABLE `CRM_faq_ow` (
  `id` int(11) NOT NULL auto_increment,
  `onderwerp` varchar(30) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1",

                  "CRM_naw_cf"=>"CREATE TABLE `CRM_naw_cf` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `verzendAdres` tinytext,
  `verzendPc` varchar(17) default NULL,
  `verzendPlaats` varchar(30) default NULL,
  `verzendLand` varchar(25) default NULL,
  `custodian` text,
  `custodianRekeningNr` varchar(40) default NULL,
  `custodianAfwijkendeAfspraak` text,
  `verzendFreq` varchar(30) default NULL,
  `kvkInDossier` tinyint(4) default NULL,
  `statutenInDossier` tinyint(4) default NULL,
  `rekeningActiefSinds` date default NULL,
  `tripartieteInDossier` tinyint(4) default NULL,
  `tripartieteDatum` date default NULL,
  `vermogenbeheerOvereenkomstInDossier` tinyint(4) default NULL,
  `vermogenbeheerOvereenkomstDatum` date default NULL,
  `kinderen` tinyint(4) default NULL,
  `inkomenSoort` varchar(20) default NULL,
  `inkomenIndicatie` double(10,2) default NULL,
  `vermogenOnroerendGoed` double(10,2) default NULL,
  `vermogenHypotheek` double(10,2) default NULL,
  `vermogenOverigVermogen` double(10,2) default NULL,
  `vermogenOverigSchuld` double(10,2) default NULL,
  `vermogenTotaalBelegbaar` double(10,2) default NULL,
  `vermogenBelegdViaDC` double(10,2) default NULL,
  `vermogenHerkomst` text,
  `vermogenVerplichtingen` text,
  `ervaringBelegtSinds` date default NULL,
  `ervaringBelegtInEigenbeheer` tinyint(4) default NULL,
  `ervaringBelegtInVermogensadvies` tinyint(4) default NULL,
  `ervaringBelegtInProducten` tinyint(4) default NULL,
  `ervaringMetVastrentende` tinyint(4) default NULL,
  `ervaringMetVastrentendeDatum` date default NULL,
  `ervaringMetBeleggingsFondsen` tinyint(4) default NULL,
  `ervaringMetBeleggingsFondsenDatum` date default NULL,
  `ervaringMetIndividueleAandelen` tinyint(4) default NULL,
  `ervaringMetIndividueleAandelenDatum` date default NULL,
  `ervaringMetOpties` tinyint(4) default NULL,
  `ervaringMetOptiesDatum` date default NULL,
  `ervaringMetFutures` tinyint(4) default NULL,
  `ervaringMetFuturesDatum` date default NULL,
  `beleggingsHorizon` varchar(20) default NULL,
  `beleggingsDoelstelling` varchar(30) default NULL,
  `risicoprofielFinancieleGegevens` text,
  `risicoprofielGesprek` text,
  `risicoprofielAfwijkendeAfspraak` text,
  `risicoprofielOverig` text,
  `risicoprofiel` varchar(20) default NULL,
  `profielAandelenBinnenland` char(1) default NULL,
  `profielAandelenBuitenland` char(1) default NULL,
  `profielObligatiesEuro` char(1) default NULL,
  `profielObligatiesOverigeValuta` char(1) default NULL,
  `profielWarrants` char(1) default NULL,
  `profielOptiesKopenCalls` char(1) default NULL,
  `profielOptiesOngedektVerkopenCalls` char(1) default NULL,
  `profielOptiesGedektVerkopenCalls` char(1) default NULL,
  `profielOptiesKopenPuts` char(1) default NULL,
  `profielOptiesVerkopenPuts` char(1) default NULL,
  `profielTermijnFutures` char(1) default NULL,
  `profielValutasInclOTC` char(1) default NULL,
  `profielEdelmetalen` char(1) default NULL,
  `profielVerleentToestemmingDebetstanden` char(1) default NULL,
  `profielNietTerbeurzeBeleggingsfondsen` char(1) default NULL,
  `profielInsiderRegeling` text,
  `profielOverigeBeperkingen` text,
  `huidigesamenstellingAandelen` double(10,2) default NULL,
  `huidigesamenstellingObligaties` double(10,2) default NULL,
  `huidigesamenstellingOverige` double(10,2) default NULL,
  `huidigesamenstellingLiquiditeiten` double(10,2) default NULL,
  `huidigesamenstellingTotaal` double(10,2) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

",
                "OrderRegels" => "CREATE TABLE `OrderRegels` (
  `id` int(11) NOT NULL auto_increment,
  `orderid` varchar(16) NOT NULL default '',
  `positie` int(3) unsigned NOT NULL default '0',
  `portefeuille` varchar(20) NOT NULL default '',
  `rekeningnr` varchar(20) NOT NULL default '',
  `valuta` varchar(6) NOT NULL default '',
  `aantal` double(12,4) NOT NULL default '0.0000',
  `client` varchar(60) NOT NULL default '',
  `status` varchar(60) NOT NULL default '',
  `memo` varchar(100) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
",
                "Orders" => "CREATE TABLE `Orders` (
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
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;"

);


$defaults = array(
"INSERT INTO `CRM_selectievelden` VALUES ('1', 'burgelijke staat', '', 'Gehuwd', 'beheer', '2006-01-23 15:02:57', 'beheer', '2006-01-23 15:02:57');",
"INSERT INTO `CRM_selectievelden` VALUES ('2', 'burgelijke staat', '', 'Gescheiden', 'beheer', '2006-01-23 15:03:06', 'beheer', '2006-01-23 15:03:06');",
"INSERT INTO `CRM_selectievelden` VALUES ('3', 'burgelijke staat', '', 'Ongehuwd', 'beheer', '2006-01-23 15:03:19', 'beheer', '2006-01-23 15:03:19');",
"INSERT INTO `CRM_selectievelden` VALUES ('4', 'burgelijke staat', '', 'Weduwe', 'beheer', '2006-01-23 15:03:31', 'beheer', '2006-01-23 15:03:31');",
"INSERT INTO `CRM_selectievelden` VALUES ('5', 'burgelijke staat', '', 'Wedunaar', 'beheer', '2006-01-23 15:03:52', 'beheer', '2006-01-23 15:03:37');",
"INSERT INTO `CRM_selectievelden` VALUES ('6', 'legitimatie', '', 'paspoort', 'beheer', '2006-01-23 15:04:02', 'beheer', '2006-01-23 15:04:02');",
"INSERT INTO `CRM_selectievelden` VALUES ('7', 'legitimatie', '', 'rijbewijs', 'beheer', '2006-01-23 15:04:11', 'beheer', '2006-01-23 15:04:11');",
"INSERT INTO `CRM_selectievelden` VALUES ('8', 'legitimatie', '', 'ID kaart', 'beheer', '2006-01-23 15:04:22', 'beheer', '2006-01-23 15:04:22');",
"INSERT INTO `CRM_selectievelden` VALUES ('9', 'rechtsvorm', 'BV', 'besloten vennootschap', 'beheer', '2006-01-23 15:05:44', 'beheer', '2006-01-23 15:04:41');",
"INSERT INTO `CRM_selectievelden` VALUES ('10', 'rechtsvorm', 'particulier', 'Particulier', 'beheer', '2006-01-23 15:05:01', 'beheer', '2006-01-23 15:05:01');",
"INSERT INTO `CRM_selectievelden` VALUES ('11', 'rechtsvorm', 'VOF', 'vennootschap onder firma', 'beheer', '2006-01-23 15:05:21', 'beheer', '2006-01-23 15:05:21');",
"INSERT INTO `CRM_selectievelden` VALUES ('12', 'rechtsvorm', 'eenmans', 'eenmanszaak', 'beheer', '2006-01-23 15:05:35', 'beheer', '2006-01-23 15:05:35');",
"INSERT INTO `CRM_selectievelden` VALUES ('13', 'telefoon', '', 'algemeen', 'beheer', '2006-01-23 15:05:59', 'beheer', '2006-01-23 15:05:59');",
"INSERT INTO `CRM_selectievelden` VALUES ('14', 'telefoon', '', 'prive', 'beheer', '2006-01-23 15:06:08', 'beheer', '2006-01-23 15:06:08');",
"INSERT INTO `CRM_selectievelden` VALUES ('15', 'telefoon', '', 'mobiel', 'beheer', '2006-01-23 15:06:13', 'beheer', '2006-01-23 15:06:13');",
"INSERT INTO `CRM_selectievelden` VALUES ('16', 'telefoon', '', 'doorkies', 'beheer', '2006-01-23 15:06:19', 'beheer', '2006-01-23 15:06:19');",
"INSERT INTO `CRM_selectievelden` VALUES ('17', 'risicoprofiel', '', 'offensief', 'beheer', '2006-01-23 15:06:46', 'beheer', '2006-01-23 15:06:46');",
"INSERT INTO `CRM_selectievelden` VALUES ('18', 'risicoprofiel', '', 'speculatief', 'rw', '2006-01-24 14:43:48', 'rw', '2006-01-24 14:43:48');",
"INSERT INTO `CRM_selectievelden` VALUES ('19', 'risicoprofiel', '', 'neutraal', 'rw', '2006-01-24 14:44:21', 'rw', '2006-01-24 14:43:58');",
"INSERT INTO `CRM_selectievelden` VALUES ('20', 'risicoprofiel', '', 'vastrenetend', 'rw', '2006-01-24 14:44:08', 'rw', '2006-01-24 14:44:08');",
"INSERT INTO `CRM_selectievelden` VALUES ('21', 'risicoprofiel', '', 'defensief', 'rw', '2006-01-24 14:44:50', 'rw', '2006-01-24 14:44:50');",
"INSERT INTO `CRM_selectievelden` VALUES ('22', 'beleggingshorizon', '', '3-5 jaar', 'rw', '2006-01-24 14:58:39', 'rw', '2006-01-24 14:50:05');",
"INSERT INTO `CRM_selectievelden` VALUES ('23', 'beleggingshorizon', '', 'Langer dan 5 jaar', 'rw', '2006-01-24 14:58:53', 'rw', '2006-01-24 14:50:46');",
"INSERT INTO `CRM_selectievelden` VALUES ('31', 'soort inkomen', '', 'loondienst', 'rw', '2006-01-24 15:51:55', 'rw', '2006-01-24 15:51:55');",
"INSERT INTO `CRM_selectievelden` VALUES ('25', 'beleggingsdoelstelling', '', 'Inkomen zekerstellen', 'rw', '2006-01-24 14:55:38', 'rw', '2006-01-24 14:55:14');",
"INSERT INTO `CRM_selectievelden` VALUES ('26', 'beleggingsdoelstelling', '', 'Vermogen opbouwen voor pensioen', 'rw', '2006-01-24 14:55:57', 'rw', '2006-01-24 14:55:57');",
"INSERT INTO `CRM_selectievelden` VALUES ('27', 'beleggingsdoelstelling', '', 'Vermogensoverdracht aan volgende generatie', 'rw', '2006-01-24 14:57:07', 'rw', '2006-01-24 14:57:07');",
"INSERT INTO `CRM_selectievelden` VALUES ('28', 'beleggingsdoelstelling', '', 'Vermogensgroei', 'rw', '2006-01-24 14:57:26', 'rw', '2006-01-24 14:57:26');",
"INSERT INTO `CRM_selectievelden` VALUES ('29', 'beleggingsdoelstelling', '', 'Liquide middelen direct beschikbaar', 'rw', '2006-01-24 14:57:51', 'rw', '2006-01-24 14:57:51');",
"INSERT INTO `CRM_selectievelden` VALUES ('30', 'beleggingsdoelstelling', '', 'Andere doelstelling', 'rw', '2006-01-24 14:58:15', 'rw', '2006-01-24 14:58:06');",
"INSERT INTO `CRM_selectievelden` VALUES ('32', 'soort inkomen', '', 'zelfstandig', 'rw', '2006-01-24 15:52:05', 'rw', '2006-01-24 15:52:05');",
"INSERT INTO `CRM_selectievelden` VALUES ('33', 'soort inkomen', '', 'pensioen', 'rw', '2006-01-24 15:52:11', 'rw', '2006-01-24 15:52:11');",
"INSERT INTO `CRM_selectievelden` VALUES ('34', 'soort inkomen', '', 'overig inkomen', 'rw', '2006-01-24 15:52:22', 'rw', '2006-01-24 15:52:22');",
"INSERT INTO `CRM_selectievelden` VALUES ('35', 'verzend freq rapportage', '', 'maandelijks', 'beheer', '2006-01-25 14:42:50', 'beheer', '2006-01-25 14:42:50');",
"INSERT INTO `CRM_selectievelden` VALUES ('36', 'verzend freq rapportage', '', 'kwartaal', 'beheer', '2006-01-25 14:42:59', 'beheer', '2006-01-25 14:42:59');"
);


$db = new DB;
$query = "show tables like '{table}'";

reset($tablesDefs);
while(list($key,$value) = each($tablesDefs))
{
  $Q = str_replace("{table}",$key,$query);
  $db->SQL($Q);
  if (!$db->lookupRecord())
  {
    //echo "<br>table $key niet gevonden<br>";
    $db->SQL($value);
    if ($db->Query())
    {
      //echo "table $key toegevoegd";
    }  
    else
    {
      //echo "table $key toevoegen MISLUKT"  ;
    }  
  }
}
$query = "SELECT * FROM CRM_selectievelden";
$recs = $db->QRecords($query);
if ($recs < 1 )   // defaults inlezen wanneer de table nog leeg is!
{
  //echo "<hr>standaard selecties inlezen<br>";
  for($x=0;$x < count($defaults);$x++)
  {
    $db->SQL($defaults[$x]);
    if ($db->Query())
    {
      //echo "v";
    }  
    else
    {
      //echo "X";
    } 
  }
}

$tst = new SQLman();

$tst->changeField("CRM_naw_cf","verzendAanhef",array("Type"=>"tinytext","NULL"=>false));
$tst->changeField("CRM_naw","naam1",array("Type"=>"varchar(60)","NULL"=>false));
$tst->changeField("CRM_naw","relatieSinds",array("Type"=>"date","NULL"=>false));
$tst->changeField("CRM_naw","afgifteID",array("Type"=>"date","NULL"=>false));
$tst->changeField("CRM_naw","SofiNr",array("Type"=>"varchar(15)","NULL"=>false));
$tst->changeField("CRM_naw","achtervoegsel",array("Type"=>"varchar(15)","NULL"=>false));
$tst->changeField("CRM_naw","verjaardagLijst",array("Type"=>"tinyint","NULL"=>false));

$tst->changeField("CRM_naw","part_afgifteID",array("Type"=>"date","NULL"=>false));
$tst->changeField("CRM_naw","part_SofiNr",array("Type"=>"varchar(15)","NULL"=>false));
$tst->changeField("CRM_naw","part_achtervoegsel",array("Type"=>"varchar(15)","NULL"=>false));
$tst->changeField("CRM_naw","part_verjaardagLijst",array("Type"=>"tinyint","NULL"=>false));

$tst->changeField("CRM_naw","tel3",array("Type"=>"varchar(20)","NULL"=>false));
$tst->changeField("CRM_naw","tel3_oms",array("Type"=>"varchar(20)","NULL"=>false));
$tst->changeField("CRM_naw","faxZakelijk",array("Type"=>"varchar(20)","NULL"=>false));

$tst->changeField("CRM_naw","emailZakelijk",array("Type"=>"tinytext","NULL"=>false));

$tst->changeField("CRM_naw","notaris",array("Type"=>"text","NULL"=>false));

$tst->changeField("Vermogensbeheerders","check_module_CRM",array("Type"=>"tinyint","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_module_ORDER",array("Type"=>"tinyint","NULL"=>false));


?>