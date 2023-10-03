<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw","laatsteRapDatumSignalering",array("Type"=>"date","Null"=>false));
$tst->changeField("laatstePortefeuilleWaarde","SignRapDatumRend",array("Type"=>"double","Null"=>false));
$tst->changeField("tempLaatstePortefeuilleWaarde","SignRapDatumRend",array("Type"=>"double","Null"=>false));


