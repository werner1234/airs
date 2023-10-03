<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/09 15:05:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140720_PREinstall.php,v $
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/05/25 14:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/05/03 15:46:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/19 16:14:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/02 15:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/03/29 16:25:05  rvv
 		*** empty log message ***
 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
//$tst->changeField("GeconsolideerdePortefeuilles","SpecifiekeIndex",array("Type"=>"varchar(25)","Null"=>false));


$tables['VragenVragenlijsten'] ="CREATE TABLE `VragenVragenlijsten` (
  `id` int(11) NOT NULL auto_increment,
  `omschrijving` varchar(200) NOT NULL default '',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables['VragenVragen'] ="CREATE TABLE `VragenVragen` (
  `id` int(11) NOT NULL auto_increment,
  `vragenlijstId` int(11) NOT NULL default '0',
  `omschrijving` varchar(200) NOT NULL default '',
  `volgorde` tinyint(4) NOT NULL default '0',
  `vraagNummer` varchar(5) NOT NULL default '',
  `vraag` text NOT NULL default '',
  `factor` double NOT NULL default '0',
  `offline` tinyint(4) NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables['VragenAntwoorden'] ="CREATE TABLE `VragenAntwoorden` (
  `id` int(11) NOT NULL auto_increment,
  `vraagId` int(11) NOT NULL default '0',
  `omschrijving` varchar(200) NOT NULL default '',
  `punten` double NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

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