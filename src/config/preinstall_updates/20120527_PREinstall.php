<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","orderCheckMaxAge",array("Type"=>"int","Null"=>false,'Default'=>'default \'0\''));


?>