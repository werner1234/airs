<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/09 15:05:04 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20140802_PREinstall.php,v $
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

 		
	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_selectievelden","extra",array("Type"=>"text","Null"=>false));

$tables=array();
$tables['externeQueries']="CREATE TABLE `externeQueries` (
  `id` int(11) NOT NULL auto_increment,
  `titel` varchar(100) NOT NULL default '',
  `omschrijving` varchar(255) NOT NULL default '',
  `query` mediumtext NOT NULL,
  `homeOnly` tinyint(4) NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ";

$tables['VragenIngevuld']="CREATE TABLE `VragenIngevuld` (
  `id` int(11) NOT NULL auto_increment,
  `relatieId` int(11) NOT NULL default '0',
  `vragenlijstId` int(11) NOT NULL default '0',
  `vraagId` int(11) NOT NULL default '0',
  `antwoordId` int(11) NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NULL default '',
  PRIMARY KEY  (`id`)
) ";

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