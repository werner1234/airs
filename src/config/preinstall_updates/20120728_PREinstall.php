<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Rekeningen","Depotbank",array("Type"=>"varchar(10)","Null"=>false,'Default'=>'default \'\''));


?>