<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/06/19 08:45:34 $
    File Versie         : $Revision: 1.1 $
 		

*/

include_once("wwwvars.php");

session_start();
$query = $_SESSION["queryTRL"];

$db = new DB();
$db->executeQuery($query);
$out = "";
while ($rec = $db->nextRecord())
{
  $values = array();
  $row = array();
  if ($out == "")
  {
    foreach ($rec as $k=>$v)
    {
      $values[] = $v;
      if (strstr($k, "."))
      {
        $s = explode(".",$k);
        $row[] = $s[1];
      }
      else
      {
        $row[] = $k;
      }
    }
    $out = "\"".implode("\",\"", $row)."\"\r\n";
    $out .= "\"".implode("\",\"", $values)."\"\r\n";
    $vbdepot = $values[1]."-".$values[2];
    continue;
  }

  foreach ($rec as $k=>$v)
  {
    $values[] = $v;
  }
  $out .= "\"".implode("\",\"", $values)."\"\r\n";


}

$filename="TijdelijkeRecon_".$vbdepot."_".date("Ymd-His").".csv";

header("Content-type: text/csv");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Disposition: attachment; filename="'.$filename.'"');

echo $out;


