<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$tst->changeField("BeleggingscategoriePerFonds","duurzaamEcon",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("BeleggingscategoriePerFonds","duurzaamSociaal",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("BeleggingscategoriePerFonds","duurzaamMilieu",array("Type"=>"tinyint(3)","Null"=>false,'Default'=>'default \'0\''));
