<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeField("Portefeuilles","BeheerfeeTransactiefeeKosten",array("Type"=>"double","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Vermogensbeheerders","TransactiefeeBtw",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("CRM_eigenVelden","relatieSoort",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("Gebruikers","CRM_relatieSoorten",array("Type"=>"mediumtext","Null"=>false,'Default'=>'default \'\''));



?>