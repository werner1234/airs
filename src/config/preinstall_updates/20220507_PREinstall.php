<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("FondsenFundInformatie","koersFrequentie",array("Type"=>"varchar(20)","Null"=>false));
