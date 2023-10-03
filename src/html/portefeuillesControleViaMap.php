<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2010/06/09 15:20:23 $
File Versie					: $Revision: 1.2 $

$Log: portefeuillesControleViaMap.php,v $
Revision 1.2  2010/06/09 15:20:23  cvs
*** empty log message ***

Revision 1.1  2009/07/29 09:15:34  cvs
rabo positie via excel sheets in map




*/
include_once("wwwvars.php");

$rawData = array();

function readXLS($xlsFile)
{
  global $__appvar;
  include_once($__appvar["basedir"].'/classes/excel/XLSreader.php');
  $xls = new Spreadsheet_Excel_Reader();
  $xls->setOutputEncoding('CP1252');
  $xls->read($xlsFile);
  return $xls->sheets[0]['cells'];
}


if ($handle = opendir($__positieImportMap))
{
  $ndx = 0;
  while (false !== ($file = readdir($handle)))
  {
	  if (substr(strtolower($file),-4) == ".xls")
    {
      $sheet = readXLS($__positieImportMap."/".$file);
      $bankdata = array();
      $bankdata["bestand"]      = $file;
      $bankdata["client"]       = $sheet[1][1];
      if (trim($bankdata["client"]) == "")
        $bankdata["client"] = "geen protNr";
      $bankdata["portefeuille"] = $sheet[2][5];
      $bankdata["datum"]        = substr($sheet[1][8],0,10);
      for ($x=0;$x < count($sheet);$x++)
      {
        $datarow = $sheet[$x+8];
        $pos = "positie_".($x+1);
        if (count($datarow) > 0)
        {
          $rawData[$ndx]["client"]       = $bankdata["client"];
          $rawData[$ndx]["portefeuille"] = $bankdata["portefeuille"];
          $rawData[$ndx]["datum"]        = $bankdata["datum"];
          $rawData[$ndx]["isin"]         = $datarow[2];
          $rawData[$ndx]["fonds"]        = $datarow[3];
          $rawData[$ndx]["aantal"]       = $datarow[4];
          $ndx++;
        }
      }
      $client[] = $bankdata;
    }
  }
	closedir($handle);
}

listarray($rawData);


?>