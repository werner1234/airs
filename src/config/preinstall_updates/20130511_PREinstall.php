<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Gebruikers","titel",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Gebruikers","overigePortefeuilles",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));

$tst->changeField("laatstePortefeuilleWaarde","beginWaarde",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","Stortingen",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","Onttrekkingen",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","Opbrengsten",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","Kosten",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","gerealiseerd",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","ongerealiseerd",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("laatstePortefeuilleWaarde","rente",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));

?>