<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("TijdelijkeBulkOrdersV2","aantalInPositie",array("Type"=>"DECIMAL(14,6)","Null"=>false));
$tst->changeField("TijdelijkeBulkOrdersV2","nieuwAantal",array("Type"=>"DECIMAL(14,6)","Null"=>false));
