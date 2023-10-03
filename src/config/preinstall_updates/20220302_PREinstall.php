<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","NAW_inclDocumenten",array("Type"=>"tinyint(4)","Null"=>false));

