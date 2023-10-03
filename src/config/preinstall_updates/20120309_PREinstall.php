<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();
$tst->changeField("Vermogensbeheerders","transactiemeldingWaarde",array("Type"=>"double","Null"=>false));
$tst->changeField("Vermogensbeheerders","transactiemeldingEmail",array("Type"=>"varchar(100)","Null"=>false));
$tst->changeField("ZorgplichtPerPortefeuille","norm",array("Type"=>"double","Null"=>false));
$tst->changeField("ZorgplichtPerRisicoklasse","norm",array("Type"=>"double","Null"=>false));

?>