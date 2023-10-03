<?php

include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","CRM_GesprVerslagVerwWijz",array("Type"=>"tinyint(4)","Null"=>false));

