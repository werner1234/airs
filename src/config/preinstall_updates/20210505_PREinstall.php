<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Fondsen","KNOXcode",array("Type"=>"varchar(26)","Null"=>false));
$tst->changeField("Fondsen","GScode",array("Type"=>"varchar(26)","Null"=>false));
$tst->changeField("Fondsen","Sarasincode",array("Type"=>"varchar(26)","Null"=>false));
$tst->changeField("Fondsen","Dierickscode",array("Type"=>"varchar(26)","Null"=>false));
