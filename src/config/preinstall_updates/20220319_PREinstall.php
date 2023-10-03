<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("uitsluitingenModelcontrole","Beleggingscategorie",array("Type"=>"varchar(15)","Null"=>false));


$tables['fondskoersAanvragen'] ="CREATE TABLE `fondskoersAanvragen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Fonds` varchar(25) DEFAULT NULL,
  `Datum` datetime DEFAULT NULL,
  `Koers` double DEFAULT NULL,
  `emailAdres` varchar(50) NOT NULL DEFAULT '',
  `verwerkt` tinyint(3) NOT NULL DEFAULT '0',
  `add_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL DEFAULT '',
  `change_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `Fonds` (`Fonds`),
  KEY `change_date` (`change_date`)
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


