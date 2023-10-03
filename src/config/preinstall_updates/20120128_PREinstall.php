<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("UpdateHistory","tableDef",array("Type"=>"mediumtext","Null"=>false));


?>