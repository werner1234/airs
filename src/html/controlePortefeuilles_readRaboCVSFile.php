<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2010/08/25 14:39:42 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: controlePortefeuilles_readRaboCVSFile.php,v $
 		Revision 1.2  2010/08/25 14:39:42  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2009/04/22 11:40:46  rvv
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
  if($data = $tmpDB->lookupRecord())
    return $data[totaal];
  else 
    return "Geen AIRS info";
}

function readRaboCvsFile($filename,$filename1)
{
	global $ndx, $csvRegels,$prb,$bankOutput,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;
	
	function InsertAIRSsection($portefeuilleInCsv)
	{
	  global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar;
	  
	  // kijk of portefeuille is verlopen
	  $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = ".$portefeuilleInCsv." AND Einddatum > NOW()";
	  $DB1->SQL($query);
		if ($dummy = $DB1->lookupRecord()) //
		{
		  //
		  $fondswaarden =  berekenPortefeuilleWaarde($portefeuilleInCsv, $datum);
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
		        $outputArray[$portefeuilleInCsv][A][$idx]['rekening']        = $recordData['rekening'];
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
    $data[0]=str_replace('.','',$data[0]);

    if (!is_numeric($data[0])) continue;  // sla lege regels over
    $portefeuille = trim($data[0]);
    
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);	
  

    $rekening=$data[0].$data[1];   
    $portefeuille=$data[0].$data[1]; 
    
    $airsSaldo=getAIRSvaluta($rekening);
    
    $data[4]=str_replace('.','',$data[4]);
    $data[4]=str_replace(',','.',$data[4]);
    if ($data[4] <> 0)
    {
      if($data[5] != "CR")
        $csvSaldo = $data[4] * -1;
      else 
        $csvSaldo = $data[4] ;
    }
    else    
    	$csvSaldo = 0;
      
    $bankOutput[$ndx][rekeningnr] = $rekening;
    $bankOutput[$ndx][Bsaldo] = $csvSaldo;
    $bankOutput[$ndx][Asaldo] = $airsSaldo;
   
  	$ndx++;
  	
  	$row++;
 
  }	

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