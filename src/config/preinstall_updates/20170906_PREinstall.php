<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$db=new DB();
$query="desc CRM_naw";
$db->SQL($query);
$db->query();
$velden=array();
while($data=$db->nextRecord())
{
  $velden[]=strtolower($data['Field']);
}


if(!in_array('leinr',$velden))
{
  $tst = new SQLman();
  $tst->changeField("CRM_naw", "LEInr", array("Type" => "varchar(25)", "Null" => false));
}



?>