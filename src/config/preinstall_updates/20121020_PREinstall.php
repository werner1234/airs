<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Accountmanagers","Titel",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Accountmanagers","Handtekening",array("Type"=>"mediumblob","Null"=>false,'Default'=>'default \'\''));



?>