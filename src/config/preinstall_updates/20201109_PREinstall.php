<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst = new SQLman();
$tst->tableExist("externeOrders",true);
$tst->changeField("externeOrders","externOrderId",array("Type"=>" varchar(40)","Null"=>false));
$tst->changeField("externeOrders","ISIN",array("Type"=>" varchar(12)","Null"=>false));
$tst->changeField("externeOrders","valuta",array("Type"=>" varchar(3)","Null"=>false));
$tst->changeField("externeOrders","fonds",array("Type"=>" varchar(25)","Null"=>false));
$tst->changeField("externeOrders","aantal",array("Type"=>" double","Null"=>false));
$tst->changeField("externeOrders","datum",array("Type"=>" date","Null"=>false));
$tst->changeField("externeOrders","settlementdatum",array("Type"=>" date","Null"=>false));
$tst->changeField("externeOrders","beurs",array("Type"=>" varchar(30)","Null"=>false));
$tst->changeField("externeOrders","uitvoeringskoers",array("Type"=>" double","Null"=>false));
$tst->changeField("externeOrders","verwerkt",array("Type"=>" tinyint","Null"=>false));
$tst->changeField("externeOrders","nettobedrag",array("Type"=>" double","Null"=>false));
$tst->changeField("externeOrders","executor",array("Type"=>" varchar(15)","Null"=>false ) );
