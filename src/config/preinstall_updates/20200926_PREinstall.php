<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Portefeuilles","BeheerfeeAdminVergoedingJaarlijks",array("Type"=>"tinyint(3)","Null"=>false));


?>