<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

//$tst = new SQLman();
//$tst->changeField("uitsluitingenModelcontrole","Beleggingscategorie",array("Type"=>"varchar(15)","Null"=>false));


$tables['ParametersPerVermogensbeheerder'] ="CREATE TABLE `ParametersPerVermogensbeheerder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Vermogensbeheerder` varchar(10) NOT NULL DEFAULT '',
  `Categoriesoort` varchar(50) NOT NULL DEFAULT '',
  `Categorie` varchar(50) NOT NULL DEFAULT '',
  `Veld` varchar(50) NOT NULL DEFAULT '',
  `Datum` datetime DEFAULT NULL,
  `Waarde` varchar(50) NOT NULL DEFAULT '',
  `add_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL DEFAULT '',
  `change_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `Vermogensbeheerder` (`Vermogensbeheerder`),
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


