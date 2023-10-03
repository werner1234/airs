<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("appVertaling","veld",array("Type"=>"text"));
$tst->changeField("appVertaling","nl",array("Type"=>"text"));
$tst->changeField("appVertaling","en",array("Type"=>"text"));
$tst->changeField("appVertaling","fr",array("Type"=>"text"));
$tst->changeField("appVertaling","du",array("Type"=>"text"));

