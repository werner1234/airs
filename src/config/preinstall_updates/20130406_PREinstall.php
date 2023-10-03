<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw","beleggingsDoelstelling",array("Type"=>"varchar(120)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_selectievelden","omschrijving",array("Type"=>"varchar(120)","Null"=>false,'Default'=>'default \'\''));



?>