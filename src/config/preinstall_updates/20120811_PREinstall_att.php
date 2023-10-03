<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();
$query="TRUNCATE Gebruikers";
$db->SQL($query);
$db->Query();

?>