<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("laatstePortefeuilleWaarde","rendement",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("AutoRun","gebruikersnaam",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("AutoRun","wachtwoord",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));


?>