<?php

include("wwwvars.php");

include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("CRM_blanco_mutatieQueue","md5",array("Type"=>"varchar(100)","Null"=>false));

