<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/04/25 15:17:34 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20090425_PREinstall.php,v $
 		Revision 1.1  2009/04/25 15:17:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
		 
$tst = new SQLman();

$query[]="CREATE TABLE `VoorlopigeRekeningmutaties` (
  `id` int(11) NOT NULL auto_increment,
  `Rekening` varchar(20) default NULL,
  `Afschriftnummer` double default NULL,
  `Volgnummer` double default NULL,
  `Omschrijving` varchar(50) default NULL,
  `Boekdatum` datetime default NULL,
  `Grootboekrekening` varchar(5) default NULL,
  `Valuta` varchar(4) default NULL,
  `Valutakoers` double default NULL,
  `Fonds` varchar(25) default NULL,
  `Aantal` decimal(12,4) NOT NULL default '0.0000',
  `Fondskoers` double default NULL,
  `Debet` double NOT NULL default '0',
  `Credit` double NOT NULL default '0',
  `Bedrag` double NOT NULL default '0',
  `Transactietype` varchar(5) default NULL,
  `Verwerkt` tinyint(4) default NULL,
  `Memoriaalboeking` tinyint(4) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  `Bewaarder` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `Rekening` (`Rekening`),
  KEY `Afschriftnummer` (`Afschriftnummer`),
  KEY `Fonds` (`Fonds`),
  KEY `Valuta` (`Valuta`)
) ";

$query[]="CREATE TABLE `VoorlopigeRekeningafschriften` (
  `id` int(11) NOT NULL auto_increment,
  `Rekening` varchar(20) default NULL,
  `Afschriftnummer` double default NULL,
  `Datum` datetime default NULL,
  `Saldo` double default NULL,
  `NieuwSaldo` double default NULL,
  `Verwerkt` tinyint(4) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Rekening` (`Rekening`)
)";
  
$db = new DB();
for ($a=0; $a < count($query); $a++)
{
	$db->SQL($query[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20090425 $query ".$a." mislukt, neem aub contact op met AIRS.";
	}
}

$tst->changeField("Vermogensbeheerders","check_module_BOEKEN",array("Type"=>"tinyint(4)","Null"=>false)); 

?>