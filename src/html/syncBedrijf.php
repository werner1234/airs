<?php
include_once("wwwvars.php");
include_once("../classes/bedrijfSync.php");


$sync=new bedrijfSync();
$verschillen=$sync->bepaalVerschil();
//$sync->maakVerschillenHtml($verschillen);
$sync->maakVerschillenCsv($verschillen);


?>
