<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Fondsen","HSBCcode",array("Type"=>"varchar(50)","Null"=>false));


$tables['PortefeuillesGeconsolideerd'] ="CREATE TABLE `PortefeuillesGeconsolideerd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `VirtuelePortefeuille` varchar(24) NOT NULL,
  `Portefeuille` varchar(24) NOT NULL,
  `add_date` datetime DEFAULT '0000-00-00 00:00:00',
  `add_user` varchar(10) DEFAULT NULL,
  `change_date` datetime DEFAULT '0000-00-00 00:00:00',
  `change_user` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `VirtuelePortefeuille` (`VirtuelePortefeuille`),
  KEY `Portefeuille` (`Portefeuille`)
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


