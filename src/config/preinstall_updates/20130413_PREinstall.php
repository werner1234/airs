<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("Risicoklassen","verwachtMaxVerlies",array("Type"=>"float","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Risicoklassen","verwachtMaxWinst",array("Type"=>"float","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Risicoklassen","klasseStd",array("Type"=>"float","Null"=>false,'Default'=>'default \'0\''));




?>