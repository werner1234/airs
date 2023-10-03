<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20100907_PREinstall.php,v $
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2010/09/13 13:34:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/08/04 15:22:36  rvv
 		*** empty log message ***
 		
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$silent=true;

$db=new DB();
$query="show tables";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord('num'))
{
 $tables[]=$data[0];
}

foreach ($tables as $table)
{
  $query="SHOW CREATE TABLE `$table`";
  $db->SQL($query);
  $data=$db->lookupRecord('num');
  $createTables[$table]=$data[1];

}

$melding['tables']=$tables;
$melding['createTables']=$createTables;

  $log = new  DB(2);

  $query = "INSERT INTO terugmelding SET ";
  $query  .= "  datum = NOW()";
  $query  .= ", bedrijf = '".$__appvar['bedrijf']."'";
  $query  .= ", txt = '".mysql_escape_string(serialize($melding))."'";
  $query  .= ", dbinfo = '".mysql_escape_string(serialize($dbinfo))."'";
  $query  .= ", dbfields = '".mysql_escape_string(serialize($dbfields))."'";
  $query  .= ", fileinfo = '".addslashes(serialize($fileInfo))."'";
  $query  .= ", local_vars = '".mysql_escape_string($buffer)."'";

  $log->SQL($query);
  if ($log->query())
  {
    if(!$silent)
      echo "Meldingen zijn verstuurd!";
  }



$silent = true;
include_once("../html/aehelper.php");




?>