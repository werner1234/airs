<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Rekeningmutaties","Aantal",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
  
?>