<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("FondsExtraInformatie","FondsRapportagenaam",array("Type"=>"varchar(50)","Null"=>false));


?>