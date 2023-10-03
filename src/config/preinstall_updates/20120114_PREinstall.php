<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Fondsen","forward",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Bedrijfsgegevens","crypted",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Fondsen","forwardReferentieKoers",array("Type"=>"double","Null"=>false));
$tst->changeField("Fondsen","forwardAfloopDatum",array("Type"=>"date","Null"=>false));

?>