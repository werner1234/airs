<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("OrderUitvoering","nettokoers",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("OrderUitvoering","opgelopenrente",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("OrderRegels","brokerkosten",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));



$tables['orderkosten'] ="CREATE TABLE `orderkosten` (
  `id` int(11) NOT NULL auto_increment,
  `vermogensbeheerder` varchar(10) NOT NULL default '',
  `beleggingscategorie` varchar(15) default NULL,
  `kostenpercentage` double NOT NULL default '0',
  `kostenminimumbedrag` double NOT NULL default '0',
  `brokerkostenpercentage` double NOT NULL default '0',
  `brokerkostenminimumbedrag` double NOT NULL default '0',
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `vermogensbeheerderBelcat` (`vermogensbeheerder`,`beleggingscategorie`)
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