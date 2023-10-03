<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Fondsen","SAXOcode",array("Type"=>"varchar(26)","Null"=>false));
$tst->changeField("Fondsen","Quintetcode",array("Type"=>"varchar(26)","Null"=>false));


