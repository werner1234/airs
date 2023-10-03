<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("TijdelijkeRapportage","afmCategorieVolgorde",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'127\''));

?>