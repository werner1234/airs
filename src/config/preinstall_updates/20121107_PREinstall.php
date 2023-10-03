<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","fondsenmeldingEmail",array("Type"=>"varchar(200)","Null"=>false,'Default'=>'default \'\''));

?>