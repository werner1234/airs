<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","OrderStandaardType",array("Type"=>"tinyint(4)","Null"=>false));
$tst->changeField("Vermogensbeheerders","OrderStandaardMemo",array("Type"=>"varchar(255)","Null"=>false));
$tst->changeField("Vermogensbeheerders","OrderOrderdesk",array("Type"=>"tinyint(4)","Null"=>false));
unlink("../html/ordersPDF_L5.php");
unlink("../html/ordersPDF_L8.php");

?>