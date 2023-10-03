<?php

include_once("wwwvars.php");
//include_once("../classes/AE_cls_SQLman.php");
//
//$tst = new SQLman();
//$tst->changeField("FondsenFundInformatie","koersFrequentie",array("Type"=>"varchar(20)","Null"=>false));
$db = new DB();
$query = "ALTER TABLE `Portefeuilles` MODIFY COLUMN `BeheerfeeBTW` decimal(8, 1) NOT NULL DEFAULT 21";
$db->executeQuery($query);
