<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/12/24 09:54:51 $
 		File Versie					: $Revision: 1.7 $

 		$Log: controlePortefeuilles_readBinckCVSFile.php,v $
 		Revision 1.7  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.6  2013/12/16 08:20:59  cvs
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

function readBinckCvsFile($filename)
{
	global $batchError, $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;
  
  if (!function_exists(InsertAIRSsection))
  {
	  function InsertAIRSsection($portefeuilleInCsv)
	  {
	    global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar;

	    // kijk of portefeuille is verlopen
	    $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = ".$portefeuilleInCsv." AND Einddatum > NOW()";
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
  while ($data = fgetcsv($handle, 1000, ","))
  {
    //listarray($data);
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
      
      InsertAIRSsection($portefeuilleInCsv);
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
   	 		echo "<li>()".$query = "SELECT * FROM Fondsen WHERE $fcode = '".mysql_real_escape_string($_binck)."' LIMIT 1 ";
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
  InsertAIRSsection($portefeuilleInCsv);

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