<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vertalingen","Term",array("Type"=>"varchar(255)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Vertalingen","Vertaling",array("Type"=>"varchar(255)","Null"=>false,'Default'=>'default \'\''));


?>