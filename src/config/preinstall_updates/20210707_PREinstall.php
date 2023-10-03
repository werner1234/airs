<?php

include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Fondsen","JPMcode",array("Type"=>"varchar(30)","Null"=>false));

