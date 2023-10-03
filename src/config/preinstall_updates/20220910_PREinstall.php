<?php

include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$db = new DB();
$query="UPDATE afmCategorien SET afmCategorie='10aAandOntwSmlC' WHERE afmCategorie='11aAandOntwSmlC'";
$db->SQL($query);
$db->Query();


