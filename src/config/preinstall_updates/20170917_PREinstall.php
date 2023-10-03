<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("portefeuilleClusters","portaal",array("Type"=>" tinyint(1)","Null"=>false));
$tst->changeField("portefeuilleClusters","emailAdres",array("Type"=>" varchar(50)","Null"=>false));
$tst->changeField("portefeuilleClusters","wachtwoord",array("Type"=>" varchar(12)","Null"=>false));
$tst->changeField("portefeuilleClusters","actief",array("Type"=>" tinyint(1)","Null"=>false));

?>