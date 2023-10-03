<?php

include_once("../classes/AE_cls_SQLman.php");


$tables=array();
$tables['afmCategorien']='afmCategorien_202110';
$db=new DB();
foreach ($tables as $table => $newTable)
{
 if ($db->QRecords("SHOW TABLE STATUS LIKE '$newTable'") < 1)
 {
 $query="CREATE TABLE $newTable LIKE $table;";
 $db->SQL($query);
 $db->Query();
 $query="INSERT INTO $newTable (SELECT * FROM $table)";
 $db->SQL($query);
 $db->Query();
 }
}
