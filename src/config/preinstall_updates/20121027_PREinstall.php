<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("ZorgplichtPerPortefeuille","extra",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
for($i=1;$i<11;$i++)
  $tst->changeField("GeconsolideerdePortefeuilles","Portefeuille$i",array("Type"=>"varchar(12)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("GeconsolideerdePortefeuilles","Risicoprofiel",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));



?>