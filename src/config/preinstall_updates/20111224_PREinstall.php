<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("historischeTenaamstelling","adres",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("historischeTenaamstelling","pc",array("Type"=>"varchar(17)","Null"=>false));
$tst->changeField("historischeTenaamstelling","woonplaats",array("Type"=>"varchar(50)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","afmCategorie",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("TijdelijkeRapportage","afmCategorieOmschrijving",array("Type"=>"varchar(50)","Null"=>false));
?>