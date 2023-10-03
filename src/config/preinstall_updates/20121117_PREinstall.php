<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

unlink("../html/rapport/include/RapportOIV_L12.php");

$tst->changeField("Portefeuilles","BeheerfeeBedragVast",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));

?>