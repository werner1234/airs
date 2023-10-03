<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("fondsOptieSymbolen","optieSAXOcode",array("Type"=>"varchar(26)","Null"=>false));

