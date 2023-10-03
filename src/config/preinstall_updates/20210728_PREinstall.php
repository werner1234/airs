<?php

include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("dd_reference","month",array("Type"=>"int","Null"=>false));
$tst->changeField("dd_reference","quater",array("Type"=>"int","Null"=>false));
$tst->changeField("dd_reference","year",array("Type"=>"int","Null"=>false));
$tst->changeField("dd_reference","clientID",array("Type"=>"int","Null"=>false));
$tst->changeField("dd_reference","reportDate",array("Type"=>"date","Null"=>false));
$tst->changeField("dd_reference","portaalKoppelId",array("Type"=>"int","Null"=>false));
$tst->changeField("dd_reference","hash",array("Type"=>"varchar(50)","Null"=>false));

$tst->changeField("CRM_naw","tempPortefeuille",array("Type"=>"varchar(24)","Null"=>false));

