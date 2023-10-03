<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
#$tst->changeField("VermogensbeheerdersPerBedrijf","LeidendeVBH",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("Bedrijfsgegevens","LeidendeVBH",array("Type"=>"varchar(10)","Null"=>false));
