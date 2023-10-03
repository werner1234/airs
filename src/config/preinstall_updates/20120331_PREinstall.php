<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","Accountmanager",array("Type"=>"varchar(15)","Null"=>false,'Default'=>'default \'\''));


?>