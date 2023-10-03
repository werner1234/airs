<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Fondsen","optCode",array("Type"=>"varchar(20)","Null"=>false));


?>