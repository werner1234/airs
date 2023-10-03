<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Portefeuilles","startdatumMeerjarenrendement",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));


?>