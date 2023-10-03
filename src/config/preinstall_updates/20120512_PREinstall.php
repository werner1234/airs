<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","check_afmCategorie",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));


?>