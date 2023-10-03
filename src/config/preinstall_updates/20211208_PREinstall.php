<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("TijdelijkeBulkOrdersV2","afwijkingsbedrag",array("Type"=>"double(16,2) ","Null"=>false));


