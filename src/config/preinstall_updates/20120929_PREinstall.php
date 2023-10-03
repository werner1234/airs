<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Fondsen","ISINCode",array("Type"=>"varchar(26)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","TGBCode",array("Type"=>"varchar(25)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","AABCode",array("Type"=>"varchar(26)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","ABRCode",array("Type"=>"varchar(26)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Fondsen","stroeveCode",array("Type"=>"varchar(25)","Null"=>false,'Default'=>'default \'\''));


?>