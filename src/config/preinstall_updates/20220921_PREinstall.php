<?php


include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("CRM_evenementen","Aanwezig",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("CRM_evenementen","Afwezig",array("Type"=>"tinyint(3)","Null"=>false));
$tst->changeField("CRM_evenementen","Opmerking",array("Type"=>"text","Null"=>false));




