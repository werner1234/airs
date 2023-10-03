<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

unlink("../html/rapport/include/RapportVHO_L12.php");
unlink("../html/rapport/include/RapportOIV_L12.php");

$tst->changeField("Portefeuilles","BeheerfeeBedragVast",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","geenStandaardSector",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","orderControleEmail",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));

?>