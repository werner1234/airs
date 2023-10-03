<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Portefeuilles","feeToevoegMethode",array("Type"=>"tinyint","Null"=>false,'Default'=>'default \'0\''));


$db = new DB();

$tables['4']="INSERT INTO `RapportBuilderQueryAirs` VALUES ('4', 'Zorgplichtcontrole', 'opgeslagen d.d. 2.6.2012 om 09:54', 'beheer', '', 'standaard', 'a:9:{s:7:\"rapport\";s:18:\"Zorgplichtcontrole\";s:5:\"datum\";s:10:\"24-05-2012\";s:15:\"inactiefOpnemen\";N;s:4:\"step\";i:3;s:6:\"fields\";a:11:{i:0;s:18:\"Vermogensbeheerder\";i:1;s:9:\"Depotbank\";i:2;s:12:\"Portefeuille\";i:3;s:4:\"Naam\";i:4;s:6:\"Client\";i:5;s:14:\"Accountmanager\";i:6;s:8:\"Remisier\";i:7;s:12:\"Risicoklasse\";i:8;s:13:\"Risicoprofiel\";i:9;s:9:\"conclusie\";i:10;s:5:\"reden\";}s:6:\"where1\";a:0:{}s:6:\"where2\";a:0:{}s:6:\"where3\";a:0:{}s:4:\"naam\";s:18:\"Zorgplichtcontrole\";} ', 'beheer', '0000-00-00 00:00:00', 'beheer', '0000-00-00 00:00:00')";


foreach($tables as $id=>$query)
{
  if($db->QRecords("SELECT id FROM `RapportBuilderQueryAirs` WHERE id = '$id'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}








?>