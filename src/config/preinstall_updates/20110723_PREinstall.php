<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/07/23 17:21:11 $
 		File Versie					: $Revision: 1.1 $

 		$Log: 20110723_PREinstall.php,v $
 		Revision 1.1  2011/07/23 17:21:11  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/06/25 20:07:27  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderLoggingOpNota",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

$db = new DB();

$tables['help_tekst']="CREATE TABLE `help_tekst` (
  `id` int(11) NOT NULL auto_increment,
  `titel`  varchar(150) NOT NULL default '',
  `url`  varchar(150) NOT NULL default '',
  `txt` text,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `titel` (`titel`)
) ;
";

$tables['help_velden']="CREATE TABLE `help_velden` (
  `id` int(11) NOT NULL auto_increment,
  `veld`  varchar(150) NOT NULL default '',
  `txt` text,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `veld` (`veld`)
) ;
";

foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


?>