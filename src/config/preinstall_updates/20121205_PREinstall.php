<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","OrderStandaardTijdsSoort",array("Type"=>"varchar(3)","Null"=>false,'Default'=>'default \'\''));

?>