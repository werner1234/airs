<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw","ondernemingsvorm",array("Type"=>"varchar(30)","Null"=>false));
$tst->changeField("ae_log","bron",array("Type"=>"varchar(100)","Null"=>false));



$tables['eDosierQueue']="CREATE TABLE `PortefeuilleHistorischeParameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `portefeuille` varchar(26) NOT NULL DEFAULT '',
  `tot` date DEFAULT '0000-00-00',
  `veld` varchar(50) NOT NULL DEFAULT '',
  `waarde` varchar(50) NOT NULL DEFAULT '',
  `add_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL DEFAULT '',
  `change_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `portefeuille` (`portefeuille`)
) ";

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