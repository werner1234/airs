<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","check_rekeningATT",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","check_rekeningCat",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


?>