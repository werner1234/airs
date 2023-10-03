<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("CRM_naw_rekeningen","memo",array("Type"=>"text","Null"=>false,'Default'=>'default \'\''));

$tables['Owners']="RENAME table Owners to Eigenaars";
$tables['OwnersPerPortefeuille']="RENAME table OwnersPerPortefeuille to EigendomPerPortefeuille";

$tables['Eigenaars']="ALTER TABLE Eigenaars CHANGE Owner Eigenaar varchar(16)";
$tables['EigendomPerPortefeuille']="ALTER TABLE EigendomPerPortefeuille CHANGE Owner Eigenaar varchar(16)";

$db=new DB();
foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") > 0)
  {
    $db->SQL($query);
    $db->Query();
  }
}


?>