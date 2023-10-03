<?php


//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Clienten","extraInfo",array("Type"=>"text","Null"=>false));
$tst->changeField("Rekeningmutaties","orderId",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("TijdelijkeRekeningmutaties","orderId",array("Type"=>"varchar(25)","Null"=>false));


