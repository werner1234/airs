<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/09/15 12:52:37 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20060915_PREinstall.php,v $
 		Revision 1.1  2006/09/15 12:52:37  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2006/07/26 07:42:38  cvs
 		*** empty log message ***

 		Revision 1.9  2006/06/09 07:05:42  cvs
 		*** empty log message ***

 		Revision 1.8  2006/06/08 14:47:14  cvs
 		*** empty log message ***

 		Revision 1.7  2006/02/01 12:22:21  cvs
 		*** empty log message ***

 		Revision 1.6  2006/02/01 10:06:29  cvs
 		*** empty log message ***

 		Revision 1.5  2006/01/31 11:22:53  cvs
 		*** empty log message ***

 		Revision 1.4  2006/01/25 11:50:00  jwellner
 		export update

 		Revision 1.3  2006/01/25 11:50:17  cvs
 		*** empty log message ***

 		Revision 1.2  2006/01/06 16:35:44  cvs
 		*** empty log message ***

 		Revision 1.1  2006/01/05 16:06:58  cvs
 		*** empty log message ***


*/
echo "start CRM/ORDER configuratie<hr>";
include("wwwvars.php");

$tablesDefs = array("RapportBuilderQuery" =>"CREATE TABLE `RapportBuilderQuery` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
",

                "RapportBuilderQueryAirs" =>"CREATE TABLE `RapportBuilderQueryAirs` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
"

);


$defaults = array(

'INSERT INTO RapportBuilderQueryAirs
  (id, Naam, Omschrijving, Gebruiker, Vermogensbeheerder, Type, Data, add_user, add_date, change_user, change_date)
VALUES
  (1, "Fonds Overzicht", "opgeslagen d.d. 24.8.2006 om 14:36", "beheer", "", "standaard", "a:13:{s:7:""rapport"";s:14:""Fondsoverzicht"";s:5:""datum"";s:0:"""";s:4:""step"";s:1:""1"";s:6:""fields"";a:9:{i:0;s:6:""Client"";i:1;s:12:""Portefeuille"";i:2;s:9:""Depotbank"";i:3;s:14:""Accountmanager"";i:4;s:4:""Naam"";i:5;s:5:""Naam1"";i:6;s:9:""Kostprijs"";i:7;s:21:""AandeelTotaalvermogen"";i:8;s:20:""AantalInPortefeuille"";}s:5:""where"";a:0:{}s:6:""where1"";a:0:{}s:7:""orderby"";a:1:{i:0;a:2:{s:5:""field"";s:6:""Client"";s:5:""order"";s:3:""ASC"";}}s:7:""groupby"";a:0:{}s:4:""naam"";s:15:""Fonds Overzicht"";s:6:""where2"";a:0:{}s:6:""where3"";a:0:{}s:16:""standaardFondsen"";i:1;s:22:""standaardPortefeuilles"";i:1;} ", "beheer", "24-8-2006 14:36:46", "beheer", "24-8-2006 14:36:46");
'
,
'INSERT INTO RapportBuilderQueryAirs
  (id, Naam, Omschrijving, Gebruiker, Vermogensbeheerder, Type, Data, add_user, add_date, change_user, change_date)
VALUES
  (2, "Geaggregeerd portefeuille overzicht", "opgeslagen d.d. 24.8.2006 om 14:49", "beheer", "", "standaard", "a:8:{s:7:""rapport"";s:35:""Geaggregeerd-portefeuille-overzicht"";s:5:""datum"";s:0:"""";s:4:""step"";s:1:""1"";s:6:""fields"";a:8:{i:0;s:19:""Beleggingscategorie"";i:1;s:6:""Valuta"";i:2;s:12:""Omschrijving"";i:3;s:6:""Aantal"";i:4;s:10:""Fondskoers"";i:5;s:11:""Fondstotaal"";i:6;s:14:""FondstotaalEUR"";i:7;s:16:""PercentageTotaal"";}s:5:""where"";a:0:{}s:7:""orderby"";a:3:{i:0;a:2:{s:5:""field"";s:19:""Beleggingscategorie"";s:5:""order"";s:3:""ASC"";}i:1;a:2:{s:5:""field"";s:6:""Valuta"";s:5:""order"";s:3:""ASC"";}i:2;a:2:{s:5:""field"";s:12:""Omschrijving"";s:5:""order"";s:3:""ASC"";}}s:7:""groupby"";a:0:{}s:4:""naam"";s:18:""Geaggregeerd Fonds"";} ", "beheer", "24-8-2006 14:49:52", "beheer", "24-8-2006 14:49:52");
'
,
'INSERT INTO RapportBuilderQueryAirs
  (id, Naam, Omschrijving, Gebruiker, Vermogensbeheerder, Type, Data, add_user, add_date, change_user, change_date)
VALUES
  (3, "Management Overzicht", "opgeslagen d.d. 22.8.2006 om 13:27", "beheer", "", "standaard", "a:12:{s:7:""rapport"";s:19:""Managementoverzicht"";s:5:""datum"";s:10:""12-07-2006"";s:4:""step"";s:1:""1"";s:6:""fields"";a:6:{i:0;s:4:""Naam"";i:1;s:12:""Portefeuille"";i:2;s:9:""Depotbank"";i:3;s:14:""totaalvermogen"";i:4;s:15:""inprocenttotaal"";i:5;s:11:""performance"";}s:5:""where"";a:0:{}s:6:""where1"";a:0:{}s:7:""orderby"";a:1:{i:0;a:2:{s:5:""field"";s:4:""Naam"";s:5:""order"";s:3:""ASC"";}}s:7:""groupby"";a:0:{}s:4:""naam"";s:20:""Management Overzicht"";s:6:""where2"";a:0:{}s:6:""where3"";a:0:{}s:22:""standaardPortefeuilles"";i:1;}", "beheer", "22-8-2006 13:26:37", "beheer", "22-8-2006 13:27:31");
'
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
    echo "<br>table $key niet gevonden<br>";
    $db->SQL($value);
    if ($db->Query())
      echo "table $key toegevoegd";
    else
      echo "table $key toevoegen MISLUKT"  ;
  }
}
  echo "<hr>standaard selecties inlezen<br>";
  for($x=0;$x < count($defaults);$x++)
  {
    $db->SQL($defaults[$x]);
    if ($db->Query())
      echo "v";
    else
      echo "X";
  }


echo "<hr>script klaar";

?>