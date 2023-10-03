<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tables['ReferentieportefeuillePerBeleggingscategorie'] ="CREATE TABLE `ReferentieportefeuillePerBeleggingscategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Vermogensbeheerder` varchar(10) DEFAULT NULL,
  `Referentieportefeuille` varchar(24) DEFAULT NULL,
  `vanaf` date NOT NULL DEFAULT '0000-00-00',
  `Portefeuille` varchar(24) NOT NULL,
  `Categoriesoort` varchar(50) NOT NULL,
  `Categorie` varchar(30) NOT NULL,
  `add_date` datetime DEFAULT '0000-00-00 00:00:00',
  `add_user` varchar(10) DEFAULT NULL,
  `change_date` datetime DEFAULT '0000-00-00 00:00:00',
  `change_user` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Vermogensbeheerder` (`Vermogensbeheerder`)
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
