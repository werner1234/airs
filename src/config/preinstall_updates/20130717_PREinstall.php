<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("HistorischePortefeuilleIndex","periode",array("Type"=>"varchar(2)","Null"=>false,'Default'=>'default \'m\''));

$tables['StandaarddeviatiePerRisicoklasse'] ="CREATE TABLE `StandaarddeviatiePerRisicoklasse` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(10) default NULL,
  `Risicoklasse` varchar(50) default NULL,
  `Minimum` double default NULL,
  `Maximum` double default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  `norm` double NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `VermogensbeheerderRisicoklasse` (`Vermogensbeheerder`,`Risicoklasse`)
)";

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