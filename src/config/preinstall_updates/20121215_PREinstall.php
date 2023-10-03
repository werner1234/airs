<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$tst->changeField("Portefeuilles","BeheerfeeHuisfondsenOvernemen",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Portefeuilles","BeheerfeeLiquiditeitenAnderPercentage",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Portefeuilles","BeheerfeeLiquiditeitenPercentage",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
