<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","orderLiqVerkopen",array("Type"=>"tinyint(3)","Null"=>false));

