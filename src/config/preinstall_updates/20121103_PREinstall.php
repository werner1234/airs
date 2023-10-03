<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("TijdelijkeRapportage","fondspaar",array("Type"=>"int(11)","Null"=>false));

?>