<?php


include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","Layout",array("Type"=>" int","Null"=>false));
