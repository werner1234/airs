<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("fixDepotbankenPerVermogensbeheerder","meervNominaalFIX",array("Type"=>"tinyint(3)","Null"=>false));

$tables['RekeningenHistorischeParameters'] ="CREATE TABLE `RekeningenHistorischeParameters` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `Rekening` varchar(25) NOT NULL,
  `GebruikTot` date DEFAULT '0000-00-00',
  `Depotbank` varchar(10) NOT NULL DEFAULT '',
  `add_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `change_user` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Rekening` (`Rekening`)
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
