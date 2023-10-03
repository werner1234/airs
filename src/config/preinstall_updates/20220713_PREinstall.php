<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
//$tst->changeField("uitsluitingenModelcontrole","Beleggingscategorie",array("Type"=>"varchar(15)","Null"=>false));

$tst->changeField("Rekeningmutaties","bankTransactieId",array("Type"=>" varchar(40)","Null"=>false));
$tst->changeField("TijdelijkeRekeningmutaties","bankTransactieId",array("Type"=>" varchar(40)","Null"=>false));
$tst->changeField("VoorlopigeRekeningmutaties","bankTransactieId",array("Type"=>" varchar(40)","Null"=>false));



//$db = new DB();
//foreach($tables as $table=>$query)
//{
//  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
//  {
//    $db->SQL($query);
//    $db->Query();
//  }
//}


