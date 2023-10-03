<?php
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");




$db = new DB();
$query="SHOW tables like 'dd_datastore%'";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord('num'))
{

  $datastore=$data[0];
  $query="UPDATE $datastore SET filename=replace(filename,'11Q2.pdf','11Q1.pdf'),change_date=now(),change_user='sys' WHERE filename like '%11Q2.pdf'";
  echo "$query <br>\n";
  $db->SQL($query);
  $db->Query();
}
 
$query="UPDATE dd_reference SET filename=replace(filename,'11Q2.pdf','11Q1.pdf'),change_date=now(),change_user='sys' WHERE filename like '%11Q2.pdf'";
$db->SQL($query);
echo "$query <br>\n";
$db->Query();



?>