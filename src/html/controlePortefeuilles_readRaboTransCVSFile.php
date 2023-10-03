<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2010/06/09 08:08:34 $
 		File Versie					: $Revision: 1.7 $

 		$Log: controlePortefeuilles_readRaboTransCVSFile.php,v $
 		Revision 1.7  2010/06/09 08:08:34  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2009/06/17 14:04:27  cvs
 		*** empty log message ***

 		Revision 1.5  2009/06/17 13:40:06  cvs
 		*** empty log message ***

 		Revision 1.4  2009/06/17 13:39:10  cvs
 		*** empty log message ***

 		Revision 1.3  2009/06/17 13:18:32  cvs
 		*** empty log message ***

 		Revision 1.2  2009/06/17 12:57:59  cvs
 		*** empty log message ***

 		Revision 1.1  2009/06/17 09:30:27  cvs
 		*** empty log message ***

 		Revision 1.1  2008/11/27 10:14:52  cvs
 		controles uitbreiden met ANT en SNS




 		functie in controlePortefeuilles.php
*/


function getAIRSvaluta($rekeningnr)
{
  global $datum;
  $tmpDB = New DB();
 $query = "
SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal
FROM Rekeningmutaties, Rekeningen
WHERE
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND

	Rekeningmutaties.boekdatum >= '".substr($datum,0,4)."' AND
  Rekeningmutaties.Rekening = '".$rekeningnr."' AND
	Rekeningmutaties.boekdatum <= '".$datum."'
GROUP BY Rekeningen.Valuta
ORDER BY Rekeningen.Valuta";


   $tmpDB->SQL($query);
  if( $data = $tmpDB->lookupRecord())
    return $data[totaal];
  else
    return "Geen AIRS info";
}

function readRaboTransCvsFile($filename)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $ndx2= 0;

  $prb->setLabelValue('txt1','inlezen van CSV bestand ('.$csvRegels.' records)');
  while ($data = fgetcsv($handle, 4096, ";"))
  {
    $aantal = str_replace(",",".",($data[10]));
    //if (!is_numeric(trim($data[2]))) continue;  // sla lege regels over
    $portefeuille = trim($data[2]);
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
      //InsertAIRSsection($portefeuilleInCsv);
      $portefeuilleInCsv   = $portefeuille;
      $portefeuilleArray[] = $portefeuille;
    }

    $outputArray[$portefeuille][B][$ndx][aantal] = $aantal;
    //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
  	$outputArray[$portefeuille][B][$ndx][portefeuille] = trim($data[2]);

 	  $_isin = trim($data[4]);
 	  $_raboCode = trim($data[14]);



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
    else
    {
      $fcode = "raboCode";
   	 	$query = "SELECT * FROM Fondsen WHERE $fcode = '".$_raboCode."' LIMIT 1 ";
   		$DB->SQL($query);
   		$DB->Query();
   		if (!$fonds = $DB->nextRecord())
   			$outputArray[$portefeuille][B][$ndx][fonds] = "$fcode code komt niet voor fonds tabel ($_raboCode)";
   		else
   		{
   	  	$outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];

   		}
    }
    if ($fonds)
    {
      $fondsOpdatum = fondsAantalOpdatum($portefeuille, $fonds["Fonds"], $datum);
      $outputArray[$portefeuille][A][$ndx][aantal] = $fondsOpdatum["totaalAantal"];
      $outputArray[$portefeuille][A][$ndx][fonds]   = $fonds[Omschrijving];
      $outputArray[$portefeuille][A][$ndx][portefeuille] = trim($data[2]);
      $ndx2++;
    }
  	$ndx++;
  }
  //InsertAIRSsection($portefeuilleInCsv);

  fclose($handle);
  $prb->hide();
  $tijd = mktime() - $start;
  unlink($filename);
  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>