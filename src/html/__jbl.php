<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/06/26 04:42:47 $
  File Versie					: $Revision: 1.1 $

  $Log: __jbl.php,v $
  Revision 1.1  2020/06/26 04:42:47  cvs
  test


 */

include_once("wwwvars.php");

$path = $__appvar["basedir"]."/IMPORTDATA/JBLUX";

$headers = array();
$data    = array();
$out     = array();

if ($handle = opendir($path))
{
  $stamp = date("Ymd_Hi")."-";
  $ndx = 0;
  while ( ($file = readdir($handle)) !== false )
  {
    $ndx++;
    if (substr(strtolower($file),-4) == ".csv")
    {
      $fi = explode("_", $file);


      $fData = explode("\n",file_get_contents($path."/".$file));

      if ($headers[$fi[0]] == "")
      {
        $headers[$fi[0]] = $fData[0];
      }
      if (count($fData)>1)
      {
         for ($x=1; $x<count($fData); $x++)
         {
           if (trim($fData[$x]) != "")
           {
             $data[$fi[0]][] = $fData[$x];
           }
         }
      }

    }

  }

  closedir($handle);
//debug($headers);
//debug($data);
  echo "<li> gevonden bestanden .csv -> ".$ndx;
  foreach($headers as $key=>$head)
  {
    echo "<li> filetype $key -> ".count($data[$key])." regels";
    $out[] = $head;
    if (count($data[$key]) > 0)
    {

      foreach($data[$key] as $row)
      {
        $out[] = $row;
      }
    }
  }

  file_put_contents($path."/JBL_OUTPUT.txt", implode("\n", $out));
  echo "<hr><b>outputfile ".$path."/JBL_OUTPUT.txt weggeschreven</b>";
  debug($out);
}
else
{
  echo  "fout bij openen CreditSwiss Map";
  exit();
}