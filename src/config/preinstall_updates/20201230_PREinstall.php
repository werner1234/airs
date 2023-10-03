<?php

include("wwwvars.php");

include_once("../classes/AE_cls_SQLman.php");

$tables['laatstePortefeuilleWaardeQueue']="CREATE TABLE `laatstePortefeuilleWaardeQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `portefeuille` varchar(25) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `pid` int(11) NOT NULL,
  `change_user` varchar(10) NOT NULL,
  `change_date` datetime NOT NULL,
  `add_user` varchar(10) NOT NULL,
  `add_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `portefeuille` (`portefeuille`)
) ENGINE=MyISAM";

$db=new DB();
foreach ($tables as $table => $query)
{
  if ($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}



?>