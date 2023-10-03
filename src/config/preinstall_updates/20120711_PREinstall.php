<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Portefeuilles","kleurcode",array("Type"=>"varchar(255)","Null"=>false,'Default'=>'default \'\''));


?>