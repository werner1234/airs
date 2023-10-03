<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Gebruikers","ordersNietAanmaken",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Gebruikers","ordersNietVerwerken",array("Type"=>"tinyint(4)","Null"=>false));
?>