<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Fondsen","SOCcode",array("Type"=>"varchar(26)","Null"=>false));

