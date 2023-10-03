<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("TijdelijkeBulkOrdersV2","bedrag",array("Type"=>"double","Null"=>false));
$tst->changeField("usageLog","filename",array("Type"=>"varchar(100)","Null"=>false));
