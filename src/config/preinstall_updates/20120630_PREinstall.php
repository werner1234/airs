<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();

$tables['HistorischeSpecifiekeIndex']="CREATE TABLE `HistorischeSpecifiekeIndex` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(12) default NULL,
  `specifiekeIndex` varchar(25) default NULL,
  `tot` date default NULL,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ";

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