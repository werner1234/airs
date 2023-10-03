<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

if(file_exists('../html/CRM_include/CRM_nawEditTemplate_L14.html'))
{
  unlink('../html/CRM_include/CRM_nawEditTemplate_L14.html');
}

$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","FactuurMinimumPerTransactie",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
  
?>