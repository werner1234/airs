<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("orderkosten","portefeuille",array("Type"=>"varchar(12)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Vermogensbeheerders","check_rekeningDepotbank",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));



?>