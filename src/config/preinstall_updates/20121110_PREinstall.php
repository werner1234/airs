<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("IndexPerBeleggingscategorie","Portefeuille",array("Type"=>"varchar(12)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("TijdelijkeRekeningmutaties","bankTransactieId",array("Type"=>"varchar(25)","Null"=>false));
$tst->changeField("Rekeningmutaties","bankTransactieId",array("Type"=>"varchar(25)","Null"=>false)); 
  
?>