<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/02/03 17:09:40 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100124_PREinstall.php,v $
 		Revision 1.1  2010/02/03 17:09:40  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","bgkleur",array("Type"=>"varchar(6)","Null"=>false)); 
$tst->changeField("CRM_naw","part_inkomenSoort",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("CRM_naw","part_inkomenIndicatie",array("Type"=>"double(10,2)","Null"=>false)); 
$tst->changeField("CRM_naw","beleggingsDoelstelling",array("Type"=>"varchar(60)","Null"=>false)); 

$db=new DB();
$tables[] ="CREATE TABLE `agenda` (
  `id` bigint(4) NOT NULL auto_increment,
  `gebruiker` tinytext,
  `kop` tinytext,
  `txt` text,
  `soort` varchar(20) NOT NULL default '',
  `plandate` date default NULL,
  `alarm` char(1) default NULL,
  `done` tinyint(1) default NULL,
  `plantime` time default NULL,
  `rel_id` int(11) default NULL,
  `klant` varchar(40) default NULL,
  `duur` time default NULL,
  `add_user` varchar(15) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(15) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `soort` (`soort`)
);";

$tables[] ="CREATE TABLE `agenda_gebruiker` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` varchar(10) NOT NULL default '0',
  `agenda_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `agenda` (`user_id`,`agenda_id`)
);";

$tables[] ="
CREATE TABLE `taken` (
  `id` bigint(20) NOT NULL auto_increment,
  `rel_id` int(11) default NULL,
  `gebruiker` tinytext,
  `kop` tinytext NOT NULL,
  `txt` text,
  `afgewerkt` tinyint(1) NOT NULL default '0',
  `relatie` varchar(128) NOT NULL default '',
  `soort` varchar(40) NOT NULL default '',
  `spoed` tinyint(1) NOT NULL default '0',
  `zichtbaar` date default '0000-00-00',
  `add_date` datetime default NULL,
  `add_user` varchar(15) default NULL,
  `change_user` varchar(15) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`)
);";

$tables[] ="CREATE TABLE `taken_gebruiker` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` varchar(10) NOT NULL default '0',
  `taken_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `agenda` (`user_id`,`taken_id`)
);";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

?>