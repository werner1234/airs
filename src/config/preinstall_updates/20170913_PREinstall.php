<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","millogicVerwerking",array("Type"=>" tinyint(1)","Null"=>false));

$tst->tableExist("millogic_fondsparameters",true);
$tst->changeField("millogic_fondsparameters","fonds",array("Type"=>" varchar(25)","Null"=>false));
$tst->changeField("millogic_fondsparameters","isShare",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("millogic_fondsparameters","nlFonds",array("Type"=>" tinyint","Null"=>false));

$tst->tableExist("millogic_rekeningen",true);
$tst->changeField("millogic_rekeningen","rekening",array("Type"=>" varchar(25)","Null"=>false));
$tst->changeField("millogic_rekeningen","nietParticulier",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("millogic_rekeningen","rekeningZonderKosten",array("Type"=>" tinyint","Null"=>false));

$tst->tableExist("millogic_transactieMapping",true);
$tst->changeField("millogic_transactieMapping","depotbank",array("Type"=>" varchar(15)","Null"=>false));
$tst->changeField("millogic_transactieMapping","bankcode",array("Type"=>" varchar(15)","Null"=>false));
$tst->changeField("millogic_transactieMapping","Millogic",array("Type"=>" varchar(6)","Null"=>false));
$tst->changeField("millogic_transactieMapping","omschrijving",array("Type"=>" varchar(60)","Null"=>false));

$tst->changeField("TijdelijkeRekeningmutaties","bankTransactieCode",array("Type"=>" varchar(15)","Null"=>false));

$tst->changeIndex("ae_config","fieldIndex",array("columns"=>"field"));


?>