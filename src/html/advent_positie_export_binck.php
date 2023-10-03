<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/11 10:06:26 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_positie_export_binck.php,v $
 		Revision 1.1  2013/12/11 10:06:26  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2012/09/26 16:04:38  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/03/19 12:05:04  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2009/03/17 15:17:09  cvs
 		*** empty log message ***

 		Revision 1.2  2008/09/26 14:59:26  cvs
 		Zoeken naar binckcodes als geen ISIN en valuta <> EUR en USD
 		DIV regels overslaan

 		Revision 1.1  2007/08/21 12:03:13  cvs
 		*** empty log message ***


*/




function readBinck($filename)
{
	global $batchError, $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;
  echo "<hr>file :".$filename;
	$start = mktime();
	$error = array();
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
  echo "<hr> regels ".$csvRegels;
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van CSV bestand ('.$csvRegels.' records)');
  while ($data = fgetcsv($handle, 1000, ","))
  {
    listarray($data);
    if (!is_numeric(trim($data[0]))) continue;  // sla lege regels over
    if (trim($data[4]) == "DIV") continue;  // DIV boekingen overslaan  2008-09-26
    $portefeuille = trim($data[0]);
    if ($row == 0)
    {
      $portefeuilleInCsv = $portefeuille;
      $portefeuilleArray[] = $portefeuille;
    }

    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


    if ($portefeuille <> $portefeuilleInCsv)
    {
      $ndx = 0;
      echo "<br>($portefeuille) $portefeuilleInCsv";
      
      $portefeuilleInCsv   = $portefeuille;
      $portefeuilleArray[] = $portefeuille;
    }

    $outputArray[$portefeuille][B][$ndx][aantal] = trim($data[9]);
    if (trim($data[3]) == "" AND (trim($data[2]) == "EUR" OR trim($data[2]) == "USD"))
    {
    	$outputArray[$portefeuille][B][$ndx][fonds] = "Liquiditeiten";
    	$outputArray[$portefeuille][B][$ndx][portefeuille] = trim($data[0]).trim($data[2]);

    }
    else
    {
      //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
    	$outputArray[$portefeuille][B][$ndx][portefeuille] = trim($data[0]);
   	  $_isin = trim($data[3]);

    	$outputArray[$portefeuille][B][$ndx][isin] = $_isin;
  		if ( $_isin <> "")
    	{
   	    $fcode = "ISINCode";
   	 		$query = "SELECT * FROM Fondsen WHERE $fcode = '".$_isin."' LIMIT 1 ";
     		$DB->SQL($query);
     		$DB->Query();
     		if (!$fonds = $DB->nextRecord())
     			$outputArray[$portefeuille][B][$ndx][fonds] = "$fcode code komt niet voor fonds tabel ($_isin)";
     		else
     		{
     	  	$outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
     		}

    	}
      elseif ( $_isin == "" AND (trim($data[2]) <> "EUR" AND trim($data[2]) <> "USD") )
      {
        // lees Binckcode veld 18 ($data[17]
        $_binck = $data[17];
        $fcode = "binckCode";
   	 		$query = "SELECT * FROM Fondsen WHERE $fcode = '".mysql_real_escape_string($_binck)."' LIMIT 1 ";
     		$DB->SQL($query);
     		$DB->Query();
     		if (!$fonds = $DB->nextRecord())
     			$outputArray[$portefeuille][B][$ndx][fonds] = "$fcode code komt niet voor fonds tabel ($_binck)";
     		else
     		{
     	  	$outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
     		}
      }
      else
      {
      	$outputArray[$portefeuille][B][$ndx][fonds] = "Geen $fcode code bij ".trim($data[3]).", regel ".($ndx+1);
      }

    }
  	$ndx++;
  }
  

  fclose($handle);
  $prb->hide();
  $tijd = mktime() - $start;
  //unlink($filename);
  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>