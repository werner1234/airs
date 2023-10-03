<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Fondsen","VPcode",array("Type"=>"varchar(26)","Null"=>false));
