<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("SoortOvereenkomsten","adviesRelatie",array("Type"=>"varchar(1)","Null"=>false));
$tst->changeField("OrderRegelsV2","mailBevestigingVerzonden",array("Type"=>"datetime","Null"=>false));
$tst->changeField("OrderRegelsV2","mailBevestigingData",array("Type"=>"mediumtext","Null"=>false));
$tst->changeField("Vermogensbeheerders","orderAdviesNotificatie",array("Type"=>"tinyint(1)","Null"=>false));


?>