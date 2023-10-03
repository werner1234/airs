<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();


$tst->changeField("Vermogensbeheerders","IndexRisicovrij",array("Type"=>"varchar(25)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Vermogensbeheerders","CRM_alleenNAW",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


?>