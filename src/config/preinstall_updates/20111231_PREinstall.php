<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$tables['tableLocks']="CREATE TABLE `tableLocks` (
  `id` int(11) NOT NULL auto_increment,
  `user` varchar(15) default '',
  `table` varchar(50) default '',
  `tableId` int(11) default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10)  default '',
  PRIMARY KEY  (`id`)
);";

$db=new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}


?>