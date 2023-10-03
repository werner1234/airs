<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();

$tables['Owners']="CREATE TABLE `Owners` (
  `id` int(11) NOT NULL auto_increment,
  `Owner` varchar(16) NOT NULL default '',
  `Naam` varchar(50) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
)";

$tables['OwnersPerPortefeuille']="CREATE TABLE `OwnersPerPortefeuille` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) default NULL,
  `Owner` varchar(16) NOT NULL default '',
  `percentage` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `Portefeuille` (`Portefeuille`) 
)";

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}
?>