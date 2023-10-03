<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2010/05/19 06:39:19 $
 		File Versie					: $Revision: 1.5 $

 		$Log: controlePortefeuilles_readRaboExcelMap.php,v $
 		Revision 1.5  2010/05/19 06:39:19  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2010/05/18 07:07:19  cvs
 		*** empty log message ***

 		Revision 1.3  2009/09/01 13:30:12  cvs
 		*** empty log message ***

 		Revision 1.2  2009/07/29 13:00:28  cvs
 		*** empty log message ***

 		Revision 1.1  2009/07/29 09:15:34  cvs
 		rabo positie via excel sheets in map

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
    return "Geen CAIRS info";
}

function readRaboExcelMap()
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd,$__positieImportMap;


	function InsertAIRSsection($portefeuilleInCsv)
	{
	  global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar;

	  // kijk of portefeuille is verlopen
	  $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = ".$portefeuilleInCsv." AND Einddatum > NOW()";
	  $DB1->SQL($query);
		if ($dummy = $DB1->lookupRecord()) //
		{
		  //
		  //$fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuilleInCsv, $datum);
		  $fondswaarden =  berekenPortefeuilleWaarde($portefeuilleInCsv, $datum);
		  //listarray($fondswaarden);
		  if(count($fondswaarden) > 0 )
		  {

        verwijderTijdelijkeTabel($portefeuilleInCsv,$datum);
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

		      if ($recordData[type] <> "fondsen")
		      {
		        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
		    //    $outputArray[$portefeuilleInCsv][A][$idx][aantal]       = getAIRSvaluta(trim($recordData[portefeuille]).trim($recordData[valuta]));
		    //    $outputArray[$portefeuilleInCsv][A][$idx][fonds]        = "Liquiditeiten";
        //    $outputArray[$portefeuilleInCsv][A][$idx][portefeuille] = trim($recordData[portefeuille]).trim($recordData[valuta]);
		      }
		      else
		      {
		        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
		        $outputArray[$portefeuilleInCsv][A][$idx][aantal]       = $recordData[totaalAantal];
		        $outputArray[$portefeuilleInCsv][A][$idx][fonds]        = $recordData[fondsOmschrijving];
		        $outputArray[$portefeuilleInCsv][A][$idx][portefeuille] = trim($recordData[portefeuille]);
		        $idx++;

		      }

		    }
		  }
		}
		else
		  $verlopenPortefeuille[] = $portefeuilleInCsv;  // push waarde in de te negeren portefeuilles
	}

	$start = mktime();
	$error = array();

	///////////////////////////////////
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

      }
    }
	 closedir($handle);
  }
  else
    $error[] = "fout bij openen Excel Map";

	///////////////////////////////////

	$csvRegels = Count($rawData);

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $ndx2= 0;
  $outputArray = array();
  $prb->setLabelValue('txt1','inlezen van Excel bestanden ('.$csvRegels.' records)');
  for ($x=0; $x < count($rawData); $x++)
  {


    $data = $rawData[$x];
    //listarray($data);
    $aantal = $data["aantal"];
    //if (!is_numeric(trim($data[2]))) continue;  // sla lege regels over
    $portefeuille = trim($data["portefeuille"]);
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

    $outputArray[$portefeuille][B][$ndx][aantal] = $aantal;
    //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
  	$outputArray[$portefeuille][B][$ndx][portefeuille] = $portefeuille;

 	  $_isin = trim($data["isin"]);
 	  $_raboCode = trim($data["fonds"]);

    $_raboCodeCnv = $_raboCode;
    $_raboCodeCnv = str_replace("12-","DEC",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("11-","NOV",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("10-","OKT",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("09-","SEP",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("08-","AUG",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("07-","JUL",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("06-","JUN",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("05-","MEI",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("04-","APR",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("03-","MAR",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("02-","FEB",$_raboCodeCnv);
    $_raboCodeCnv = str_replace("01-","JAN",$_raboCodeCnv);

   	$outputArray[$portefeuille][B][$ndx][isin] = $_isin;
   	$_isinFound = false;
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
   		  $_isinFound = true;
   	  	$outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
   		}
  	}

  	if (!$_isinFound)
    {
      $fcode = "raboCode";
   	 	$query = "SELECT * FROM Fondsen WHERE $fcode LIKE '".$_raboCode."%' LIMIT 1 ";
   		$DB->SQL($query);
   		$DB->Query();
   		if (!$fonds = $DB->nextRecord())
   		{
   		  $query = "SELECT * FROM Fondsen WHERE $fcode LIKE '".$_raboCodeCnv."%' LIMIT 1 ";
   		  $DB->SQL($query);
   		  $DB->Query();
   		  if (!$fonds = $DB->nextRecord())
   			  $outputArray[$portefeuille][B][$ndx][fonds] = "$fcode code komt niet voor fonds tabel ($_raboCode/$_raboCodeCnv)";
   			else
   			  $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
   		}
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
      $outputArray[$portefeuille][A][$ndx][portefeuille] = $portefeuille;
      $ndx2++;
    }
  	$ndx++;
  }
  InsertAIRSsection($portefeuilleInCsv);
//listarray($outputArray["19422342"]);
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