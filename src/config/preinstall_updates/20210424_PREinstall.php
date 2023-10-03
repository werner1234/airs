<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("BICcodes","landcode",array("Type"=>"varchar(3)","Null"=>false));
$tst->changeField("BICcodes","correspondent",array("Type"=>"varchar(25)","Null"=>false));
