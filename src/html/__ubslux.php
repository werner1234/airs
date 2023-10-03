<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/07/08 07:00:13 $
  File Versie					: $Revision: 1.2 $

  $Log: __ubslux.php,v $
  Revision 1.2  2020/07/08 07:00:13  cvs
  call 8715

  Revision 1.1  2020/06/26 06:50:20  cvs
  call 8715

  Revision 1.1  2020/06/26 04:42:47  cvs
  test


 */

include_once("wwwvars.php");

//$path = ""."/IMPORTDATA/UBSLUX";
$path = "/mnt/importdata/UBSLUX";

echo "<hr>$path<hr>";

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
  echo "<br/> output  bevat ".count($out)." regels<br/>";

  $file_name = "/tmp/UBSLUX_OUTPUT.csv";
  file_put_contents($file_name, implode("\n", $out));
  echo "<br/> <a href='tmpUBS/UBSLUX_OUTPUT.csv'>download</a>";
}
else
{
  echo  "fout bij openen UBS Map";
  exit();
}