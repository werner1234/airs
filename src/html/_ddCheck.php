<?php
/*
    AE-ICT sourcemodule created 19 okt. 2022
    Author              : Chris van Santen
    Filename            : _ddCheck.php


*/

include_once("wwwvars.php");
include_once("../config/JSON.php");
session_start();

//$content = array();
global $USR;

$hdr = array();

if ($_GET["export"] == "csv")
{
  $data = $_SESSION["ddCheck"];
  $filename = "ddcheck_".date("Ymd-His").".csv";
  $fp = fopen("/tmp/".$filename, 'w');
  foreach ($data[0] as $k=>$v)
  {
    $hdr[] = $k;
  }
  array_unshift($data, $hdr);

  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private",false);
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename={$filename};" );
	header("Content-Transfer-Encoding: binary");
  foreach ($data as $row)
  {
    echo '"'.implode('","',$row).'"'."\n";
  }
  exit;
}

$content['style2'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
echo template($__appvar["templateContentHeader"], $content);


?>
<style>

 .pContainer{
 margin:0 auto;
 width: 1610px;
 min-height: 150px;
 border: 1px black solid;

 }
 .head{
   margin:0 auto;
   width: 1600px;
   background: #333;
   color: white;
   text-align: center;
   padding: 5px;
   font-size: 2em;
 }
.k0{ display: inline-block; width: 80px;}
.k1{ display: inline-block; width: 1000px;}
.k2{ display: inline-block; width: 49px;  text-align: center }
.k3{ display: inline-block; width: 290px;  text-align: center }
.k3{ display: inline-block; width: 50px;  text-align: center }
 .header{
 background: #DDD;
   padding: 5px;

}
 td{
   padding:4px 6px;
 }
 .rowContainer td{
   background: #333;
   color: whitesmoke;
 }
 .rowClr{
   padding: 5px;
 }
 /*.rowClr:nth-child(even) {background: #EEE}*/
 /*.rowClr:nth-child(odd) {background: #FFF}*/
</style>
<br/>
<div class="head">Digidock check</div>
<br/>
<br/>
  <div class="pContainer">


<table>
<tr class="rowContainer">
    <td>ref_id</td>
    <td>ref_filename</td>
    <td>ref_datastore / id</td>
    <td>ref_categorie</td>
    <td>sto_filename</td>
    <td>sto_referenceId</td>
    <td>matchFilename</td>
    <td>matchRefId</td>
</tr>

<?
  $tables   = array();
  $out      = array();
  $db       = new DB();
  $db2      = new DB();
  $query    = "SELECT * FROM `dd_reference` ORDER BY `id` DESC";
  $db->executeQuery($query);

  while ($rec = $db->nextRecord())
  {
//    debug($rec);
    $outRow = array(
      "ref_id"          => $rec["id"],
      "ref_filename"    => $rec["filename"],
      "ref_datastore"   => $rec["datastore"],
      "ref_dd_id"       => $rec["dd_id"],
      "ref_categorie"   => $rec["categorie"],
      "sto_filename"    => "",
      "sto_referenceId" => "",
      "matchFilename"   => "false",
      "matchRefId"      => "false",

    );
    if (checkTable($rec["datastore"]))
    {
      $query = "SELECT filename, referenceId FROM `{$rec["datastore"]}` WHERE `id` = {$rec["dd_id"]}";
      if ($ddSto = $db2->lookupRecordByQuery($query))
      {
        $outRow["sto_filename"]     = $ddSto["filename"];
        $outRow["sto_referenceId"]  = $ddSto["referenceId"];
        $outRow["matchFilename"]    = ($ddSto["filename"] == $rec["filename"])?"true":"false";
        $outRow["matchRefId"]       = ($ddSto["referenceId"] == $rec["id"])?"true":"false";
      }
    }
    else
    {
      $outRow["sto_filename"] = "-- datastore missing --";
    }

    if ($outRow["matchFilename"] == "true" AND $outRow["matchRefId"] == "true")
    {
      continue;
    }




    echo "
    <tr class=''>
      <td >{$outRow["ref_id"]}</td>
      <td >{$outRow["ref_filename"]}</td>
      <td >{$outRow["ref_datastore"]} -> {$outRow["ref_dd_id"]}</td>
      <td >{$outRow["ref_categorie"]}</td>
      <td >{$outRow["sto_filename"]}</td>
      <td >{$outRow["sto_referenceId"]}</td>
      <td >{$outRow["matchFilename"]}</td>
      <td >{$outRow["matchRefId"]}</td>
    </tr>";
    flush();
    ob_flush();
    $out[] = $outRow;
  }
  $_SESSION["ddCheck"] = $out;
?>
  </table>
  <br/>
  <br/>
  <br/>
  <form>&nbsp;&nbsp;&nbsp;
    <input type='submit' value='maak .CSV'>
    <input type='hidden' name='export' value='csv'>
  </form>

<?php

echo template($__appvar["templateRefreshFooter"], $content);

function checkTable($table)
{
  global $database;
  if (in_array($table, $database))
  {
    return true;
  }
  $db = new DB();
  $query  = "SHOW TABLE STATUS LIKE '".$table."' ";
  $db->executeQuery($query);
  if ($rec = $db->nextRecord())
  {
    $database[] = $table;
    return true;
  }
  return false;
}