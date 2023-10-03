<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("signaleringStortingen","passend",array("Type"=>"tinyint","Null"=>false));

