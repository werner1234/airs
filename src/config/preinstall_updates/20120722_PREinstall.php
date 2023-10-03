<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();

$tables['NormwegingPerBeleggingscategorie']="CREATE TABLE `NormwegingPerBeleggingscategorie` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) default NULL,
  `Beleggingscategorie` varchar(15) default NULL,
  `Normweging` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `beleggingscategorie` (`Beleggingscategorie`),
  KEY `portefeuille` (`Portefeuille`)
)";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

$tst->changeField("IndexPerBeleggingscategorie","vanaf",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));



?>