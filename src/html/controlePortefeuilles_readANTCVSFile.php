<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2008/11/27 10:14:52 $
 		File Versie					: $Revision: 1.1 $

 		$Log: controlePortefeuilles_readANTCVSFile.php,v $
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

function readANTCvsFile($filename)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;

	function InsertAIRSsection($portefeuilleInCsv)
	{
	  global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar;

	  // kijk of portefeuille is verlopen
	  $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND Einddatum > NOW()";
	  $DB1->SQL($query);
		if ($dummy = $DB1->lookupRecord()) //
		{
		  //
		  $fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuilleInCsv, $datum);
		  if(count($fondswaarden) > 0 )
		  {

		    vulTijdelijkeTabel($fondswaarden ,$portefeuilleInCsv,$datum);
		    $query = 	"
		  	  SELECT
            TijdelijkeRapportage.fondsOmschrijving,
            TijdelijkeRapportage.actueleValuta ,
            TijdelijkeRapportage.rekening ,
            TijdelijkeRapportage.type ,
            TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
            TijdelijkeRapportage.totaalAantal,
            TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille
          FROM
            TijdelijkeRapportage
          WHERE
            TijdelijkeRapportage.portefeuille = '$portefeuilleInCsv' AND
            TijdelijkeRapportage.rapportageDatum = '$datum'
            ".$__appvar['TijdelijkeRapportageMaakUniek']."
          ORDER BY TijdelijkeRapportage.valuta asc";

		    debugSpecial($query,__FILE__,__LINE__);
		    $DB1->SQL($query);
		    $DB1->Query();

		    $idx = 0;
		    while ($recordData = $DB1->nextRecord())
		    {

		      if ($recordData[type] == "rekening")
		      {
		        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
		        $outputArray[$portefeuilleInCsv][A][$idx][aantal]       = getAIRSvaluta(trim($recordData[portefeuille]).trim($recordData[valuta]));
		        $outputArray[$portefeuilleInCsv][A][$idx][fonds]        = "Liquiditeiten";
            $outputArray[$portefeuilleInCsv][A][$idx][portefeuille] = trim($recordData[portefeuille]).trim($recordData[valuta]);
		      }
		      else
		      {
		        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
		        $outputArray[$portefeuilleInCsv][A][$idx][aantal]       = $recordData[totaalAantal];
		        $outputArray[$portefeuilleInCsv][A][$idx][fonds]        = $recordData[fondsOmschrijving];
		        $outputArray[$portefeuilleInCsv][A][$idx][portefeuille] = trim($recordData[portefeuille]);
		      }
		      $idx++;
		    }
		  }
		}
		else
		  $verlopenPortefeuille[] = $portefeuilleInCsv;  // push waarde in de te negeren portefeuilles
	}

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

  $prb->setLabelValue('txt1','inlezen van CSV bestand ('.$csvRegels.' records)');
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $aantal = str_replace(",",".",($data[5]));
    if (!is_numeric($aantal)) continue;  // sla lege regels over
    $portefeuille = trim($data[0]);
    if ($row == 0) $portefeuilleInCsv = $portefeuille;
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);


    if ($portefeuille <> $portefeuilleInCsv)
    {
      $ndx = 0;
      InsertAIRSsection($portefeuilleInCsv);
      $portefeuilleInCsv   = $portefeuille;
      $portefeuilleArray[] = $portefeuille;
    }

    $outputArray[$portefeuille][B][$ndx][aantal] = $aantal;
    if (trim($data[8]) == "")
    {
    	$outputArray[$portefeuille][B][$ndx][fonds] = "Liquiditeiten";
    	$outputArray[$portefeuille][B][$ndx][portefeuille] = trim($data[0]);

    }
    else
    {
      //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
    	$outputArray[$portefeuille][B][$ndx][portefeuille] = trim($data[0]);

   	  $_isin = trim($data[8]);

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
      	$outputArray[$portefeuille][B][$ndx][fonds] = "Geen $fcode code bij ".trim($data[8]).", regel ".($ndx+1);
      }

    }
  	$ndx++;
  }
  InsertAIRSsection($portefeuilleInCsv);

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