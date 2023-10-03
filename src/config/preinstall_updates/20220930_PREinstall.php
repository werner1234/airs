<?php


include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("dd_reference","filename",array("Type"=>" varchar(200)","Null"=>false));



