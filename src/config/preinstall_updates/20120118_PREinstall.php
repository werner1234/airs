<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderStatusKeuze",array("Type"=>"text","Null"=>false));
$tst->changeField("Orders","controle_datum",array("Type"=>"datetime","Null"=>false));

$tables['updateInformatie']="CREATE TABLE `updateInformatie` (
  `id` int(11) NOT NULL auto_increment,
  `versie` double default '0',
  `informatie` text default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
)";

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