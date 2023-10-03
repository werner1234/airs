<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/04/28 15:50:26 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100428_PREinstall.php,v $
 		Revision 1.1  2010/04/28 15:50:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tables['CRM_faq'] ="CREATE TABLE `CRM_faq` (
  `id` int(11) NOT NULL auto_increment,
  `kop` tinytext,
  `sectie` int(11) default NULL,
  `txt` text,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
);";

$tables['CRM_faq_ow'] ="CREATE TABLE `CRM_faq_ow` (
  `id` int(11) NOT NULL auto_increment,
  `onderwerp` varchar(30) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";

$tables['CRM_naw'] ="CREATE TABLE `CRM_naw` (
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
  `beroep` varchar(60) default NULL,
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
  `part_beroep` varchar(60) default NULL,
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
  `tel3_oms` varchar(20) default NULL,
  `tel3` varchar(20) default NULL,
  `tel4_oms` varchar(20) default NULL,
  `tel4` varchar(20) default NULL,
  `tel5_oms` varchar(20) default NULL,
  `tel5` varchar(20) default NULL,
  `tel6` varchar(20) NOT NULL default '',
  `tel6_oms` varchar(20) NOT NULL default '',
  `fax` varchar(20) default NULL,
  `debiteur` tinyint(4) default NULL,
  `crediteur` tinyint(4) default NULL,
  `crediteurnr` varchar(20) default NULL,
  `debiteurnr` varchar(20) default NULL,
  `prospect` tinyint(4) default NULL,
  `overige` tinyint(4) default NULL,
  `website` tinytext,
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
  `naam1` varchar(60) NOT NULL default '',
  `relatieSinds` date NOT NULL default '0000-00-00',
  `afgifteID` date NOT NULL default '0000-00-00',
  `SofiNr` varchar(15) NOT NULL default '',
  `achtervoegsel` varchar(15) NOT NULL default '',
  `verjaardagLijst` tinyint(4) NOT NULL default '0',
  `part_afgifteID` date NOT NULL default '0000-00-00',
  `part_SofiNr` varchar(15) NOT NULL default '',
  `part_achtervoegsel` varchar(15) NOT NULL default '',
  `part_verjaardagLijst` tinyint(4) NOT NULL default '0',
  `faxZakelijk` varchar(20) NOT NULL default '',
  `email` tinytext,
  `emailZakelijk` tinytext NOT NULL,
  `emailPartner` tinytext,
  `emailPartnerZakelijk` tinytext NOT NULL,
  `verzendAanhef` tinytext NOT NULL,
  `verzendAdres` tinytext NOT NULL,
  `verzendPc` varchar(17) NOT NULL default '',
  `verzendPlaats` varchar(30) NOT NULL default '',
  `verzendLand` varchar(25) NOT NULL default '',
  `verzendPaAanhef` tinytext NOT NULL,
  `enOfRekening` tinyint(1) NOT NULL default '0',
  `notaris` text NOT NULL,
  `tel_toev1` varchar(20) NOT NULL,
  `tel_toev2` varchar(20) NOT NULL,
  `tel_toev3` varchar(20) NOT NULL,
  `tel_toev4` varchar(20) NOT NULL,
  `tel_toev5` varchar(20) NOT NULL,
  `tel_toev6` varchar(20) NOT NULL,
  `custodian` text NOT NULL,
  `custodianRekeningNr` varchar(40) NOT NULL,
  `custodianAfwijkendeAfspraak` text NOT NULL,
  `verzendFreq` varchar(30) NOT NULL,
  `kvkInDossier` tinyint(4) NOT NULL,
  `statutenInDossier` tinyint(4) NOT NULL,
  `rekeningActiefSinds` date NOT NULL,
  `tripartieteInDossier` tinyint(4) NOT NULL,
  `tripartieteDatum` date NOT NULL,
  `vermogenbeheerOvereenkomstInDossier` tinyint(4) NOT NULL,
  `vermogenbeheerOvereenkomstDatum` date NOT NULL,
  `kinderen` tinyint(4) NOT NULL,
  `inkomenSoort` varchar(20) NOT NULL,
  `inkomenIndicatie` double(10,2) NOT NULL,
  `vermogenOnroerendGoed` double(10,2) NOT NULL,
  `vermogenHypotheek` double(10,2) NOT NULL,
  `vermogenOverigVermogen` double(10,2) NOT NULL,
  `vermogenOverigSchuld` double(10,2) NOT NULL,
  `vermogenTotaalBelegbaar` double(10,2) NOT NULL,
  `vermogenBelegdViaDC` double(10,2) NOT NULL,
  `vermogenHerkomst` text NOT NULL,
  `vermogenVerplichtingen` text NOT NULL,
  `ervaringBelegtSinds` date NOT NULL,
  `ervaringBelegtInEigenbeheer` varchar(20) NOT NULL,
  `ervaringBelegtInVermogensadvies` varchar(20) NOT NULL,
  `ervaringInExecutionOnly` varchar(20) default NULL,
  `ervaringMetVastrentende` varchar(20) NOT NULL,
  `ervaringMetVastrentendeDatum` date NOT NULL,
  `ervaringMetBeleggingsFondsen` varchar(20) NOT NULL,
  `ervaringMetBeleggingsFondsenDatum` date NOT NULL,
  `ervaringMetIndividueleAandelen` varchar(20) NOT NULL,
  `ervaringMetIndividueleAandelenDatum` date NOT NULL,
  `ervaringMetOpties` varchar(20) NOT NULL,
  `ervaringMetOptiesDatum` date NOT NULL,
  `ervaringMetFutures` varchar(20) NOT NULL,
  `ervaringMetFuturesDatum` date NOT NULL,
  `beleggingsHorizon` varchar(20) NOT NULL,
  `beleggingsDoelstelling` varchar(30) NOT NULL,
  `risicoprofielFinancieleGegevens` text NOT NULL,
  `risicoprofielGesprek` text NOT NULL,
  `risicoprofielAfwijkendeAfspraak` text NOT NULL,
  `risicoprofielOverig` text NOT NULL,
  `risicoprofiel` varchar(20) NOT NULL,
  `profielAandelenBinnenland` char(1) NOT NULL,
  `profielAandelenBuitenland` char(1) NOT NULL,
  `profielObligatiesEuro` char(1) NOT NULL,
  `profielObligatiesOverigeValuta` char(1) NOT NULL,
  `profielWarrants` char(1) NOT NULL,
  `profielOptiesKopenCalls` char(1) NOT NULL,
  `profielOptiesOngedektVerkopenCalls` char(1) NOT NULL,
  `profielOptiesGedektVerkopenCalls` char(1) NOT NULL,
  `profielOptiesKopenPuts` char(1) NOT NULL,
  `profielOptiesVerkopenPuts` char(1) NOT NULL,
  `profielTermijnFutures` char(1) NOT NULL,
  `profielValutasInclOTC` char(1) NOT NULL,
  `profielEdelmetalen` char(1) NOT NULL,
  `profielVerleentToestemmingDebetstanden` char(1) NOT NULL,
  `profielNietTerbeurzeBeleggingsfondsen` char(1) NOT NULL,
  `profielInsiderRegeling` text NOT NULL,
  `profielOverigeBeperkingen` text NOT NULL,
  `huidigesamenstellingAandelen` double(10,2) NOT NULL,
  `huidigesamenstellingObligaties` double(10,2) NOT NULL,
  `huidigesamenstellingOverige` double(10,2) NOT NULL,
  `huidigesamenstellingLiquiditeiten` double(10,2) NOT NULL,
  `huidigesamenstellingTotaal` double(10,2) NOT NULL,
  `inContactDoor` varchar(30) NOT NULL,
  `provisieAfspraak` text NOT NULL,
  `verplichtingenBelasting` double(10,2) NOT NULL,
  `verplichtingenAssurantien` double(10,2) NOT NULL,
  `verplichtingenKrediet` double(10,2) NOT NULL,
  `verplichtingenAlimentatie` double(10,2) NOT NULL,
  `verplichtingenStudieKinderen` double(10,2) NOT NULL,
  `toekomstigErfenis` double(10,2) NOT NULL,
  `toekomstigKapitaalsverz` double(10,2) NOT NULL,
  `toekomstigVerkoopZaak` double(10,2) NOT NULL,
  `toekomstigOptieregeling` double(10,2) NOT NULL,
  `toekomstigPensioenopbouw` double(10,2) NOT NULL,
  `ervaringMetGestructureerdeProducten` varchar(20) NOT NULL,
  `ervaringMetGestructureerdeProductenDatum` date NOT NULL,
  `opleidingsniveau` varchar(20) NOT NULL,
  `maandrapportage` text NOT NULL,
  `kwartaalrapportage` text NOT NULL,
  `halfjaarrapportage` text NOT NULL,
  `jaarrapportage` text NOT NULL,
  `ervaringInVermogensbeheer` varchar(17)  NOT NULL,
  `ervaringInVermogensbeheerSinds` date NOT NULL,
  `ervaringBelegtInEigenbeheerSinds` date NOT NULL,
  `ervaringBelegtInVermogensadviesSinds` date NOT NULL,
  `ervaringInExecutionOnlySinds` date NOT NULL,
  `clientenclassificatie` varchar(40)  NOT NULL,
  `Relatie1` tinyint(1) NOT NULL,
  `Relatie2` tinyint(1) NOT NULL,
  `Relatie3` tinyint(1) NOT NULL,
  `Relatie4` tinyint(1) NOT NULL,
  `Relatie5` tinyint(1) NOT NULL,
  `Relatie6` tinyint(1) NOT NULL,
  `Relatie7` tinyint(1) NOT NULL,
  `Relatie8` tinyint(1) NOT NULL,
  `Relatie9` tinyint(1) NOT NULL,
  `Relatie10` tinyint(1) NOT NULL,
  `prospectStatus` varchar(30) NOT NULL,
  `contactTijd` int(11) NOT NULL,
  `prospectStatusChange` datetime NOT NULL default '0000-00-00 00:00:00',
  `prospectEigenaar` varchar(10) NOT NULL,
  `part_inkomenSoort` varchar(20) NOT NULL,
  `part_inkomenIndicatie` double(10,2) NOT NULL,
  `kaartVerstuurd` date NOT NULL,
  `kaartVerstuurdPartner` date NOT NULL,
  PRIMARY KEY  (`id`)
);";

$tables['CRM_naw_documenten'] ="CREATE TABLE `CRM_naw_documenten` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` int(11) default '0',
  `bestandsnaam` varchar(100) default NULL,
  `omschrijving` text,
  `add_user` varchar(10) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);";

$tables['CRM_naw_dossier'] ="CREATE TABLE `CRM_naw_dossier` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `datum` datetime default NULL,
  `kop` tinytext,
  `txt` text,
  `duur` time default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";


$tables['CRM_naw_kontaktpersoon'] ="CREATE TABLE `CRM_naw_kontaktpersoon` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `naam` tinytext,
  `sortering` varchar(20) default NULL,
  `functie` varchar(30) default NULL,
  `tel1` varchar(20) default NULL,
  `tel1_oms` varchar(20) default NULL,
  `tel2` varchar(20) default NULL,
  `tel2_oms` varchar(20) default NULL,
  `fax_nr` varchar(20) default NULL,
  `email` tinytext,
  `crm_password` varchar(50) NOT NULL default '',
  `crm_login` tinyint(1) NOT NULL default '0',
  `crm_lastseen` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `memo` text,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  `adres` varchar(200) NOT NULL default '',
  `pc` varchar(17) NOT NULL default '',
  `plaats` varchar(30) NOT NULL default '',
  `land` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$tables['CRM_selectievelden'] ="CREATE TABLE `CRM_selectievelden` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(40) default NULL,
  `waarde` varchar(40) default NULL,
  `omschrijving` varchar(60) default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";


$tables['CRM_evenementen'] ="CREATE TABLE `CRM_evenementen` (
  `id` int(11) NOT NULL auto_increment,
  `rel_id` int(11) NOT NULL default '0',
  `evenement` varchar(30) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `rel_id` (`rel_id`),
  KEY `evenement` (`evenement`)
);";

$tables['CRM_naw_adressen'] ="CREATE TABLE `CRM_naw_adressen` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `naam` varchar(255) NOT NULL default '',
  `naam1` varchar(255) NOT NULL default '',
  `adres` varchar(200) NOT NULL default '',
  `pc` varchar(17) NOT NULL default '',
  `plaats` varchar(30) NOT NULL default '',
  `land` varchar(25) NOT NULL default '',
  `evenement` varchar(30) NOT NULL default '',
  `memo` text,
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
);";

$tables['CRM_naw_rekeningen'] ="CREATE TABLE `CRM_naw_rekeningen` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` bigint(20) default NULL,
  `rekening` varchar(20) NOT NULL default '',
  `bank` varchar(50) NOT NULL default '',
  `omschrijving` varchar(60) NOT NULL default '',
  `add_user` varchar(10) default NULL,
  `add_date` date default NULL,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
); ";

$db = new DB();

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}







?>