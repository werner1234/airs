<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/03/12 10:02:21 $
 		File Versie					: $Revision: 1.8 $

 		$Log: controlePortefeuilles_readGilissenCvsFile.php,v $
 		Revision 1.8  2014/03/12 10:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2011/03/09 16:07:36  cvs
 		*** empty log message ***

 		Revision 1.6  2006/11/03 11:22:35  rvv
 		Na user update

 		Revision 1.5  2006/10/31 11:53:19  rvv
 		Voor user update.

 		Revision 1.4  2005/09/21 09:39:39  cvs
 		indexfout verwijderd en verwijderen tijdelijke bestanden

 		Revision 1.3  2005/09/13 12:00:35  cvs
 		voorloop nullen TGB code

 		Revision 1.2  2005/09/13 11:52:03  cvs
 		gilissen opgenomen in port controle

 		Revision 1.1  2005/09/09 14:51:11  cvs
 		aanpassing tbv inlezen gilissen



 		functie in controlePortefeuilles.php
*/

function getAIRSvaluta($rekeningnr)
{
  global $datum;
  $tmpDB = New DB();
echo  $query = "
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


function DeleteTempTable($tablename)
{
  $tempDB = new db();
  $query = "DROP TABLE IF EXISTS $tablename";
  $tempDB->SQL($query);

  if ($tempDB->Query())
    return true;
  else
    return "fout tijdens verwijderen tijdelijke tabel: $tablename";
}


function createTempTable($tabledef)
{
  $tempDB = new db();
  $tempDB->SQL($tabledef);
  if ($tempDB->Query())
    return true;
  else
    return "fout tijdens aanmaken tijdelijke tabel";
}


function readGilissenCvsFile($fileBAL,$filePOS)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd,$bankOutput,$__appvar, $USR;


	function InsertAIRSsection($portefeuilleInCsv)
	{
	  global $datum,$DB1,$outputArray,$verlopenPortefeuille;
	  $tmpDB = new DB();
	  // kijk of portefeuille is verlopen
	  $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND Einddatum > NOW()";
	  $DB1->SQL($query);
		if ($dummy = $DB1->lookupRecord()) //
		{
		  //
		  $fondswaarden =  berekenPortefeuilleWaardeQuick($portefeuilleInCsv, $datum);

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

		      if ($recordData[type] == "rekening")
		      {
		        /*
		        //$outputArray[$portefeuilleInCsv][A][$idx][match] = 0;
		        $outputArray[$portefeuilleInCsv][A][$idx][aantal]       = $recordData[actuelePortefeuilleWaardeInValuta];
		        $outputArray[$portefeuilleInCsv][A][$idx][fonds]        = "Liquiditeiten";
		        $outputArray[$portefeuilleInCsv][A][$idx][portefeuille] = trim($recordData[portefeuille]).trim($recordData[valuta]);
		        */
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
		  verwijderTijdelijkeTabel($portefeuilleInCsv,$datum);
		}
		else
		  $verlopenPortefeuille[] = $portefeuilleInCsv;  // push waarde in de te negeren portefeuilles
	}

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($fileBAL, "r"))
	{
		$error[] = "FOUT bestand $fileBAL is niet leesbaar";
		return false;
	}


	// tijdelijke table droppen en opnieuw aanmaken
	DeleteTempTable("TEMP_portcon");
	$TempCreatequery = "
CREATE TABLE `TEMP_portcon` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(100) default NULL,
  `rekeningnr` varchar(100) default NULL,
  `fonds` varchar(100) default NULL,
  `aantal` decimal(15,5) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
	if (!createTempTable($TempCreatequery,"TEMP_prtcon"))
	{
	  echo "FOUT: kan geen tijdelijke tabel aanmaken";
	  return false;
	}

	$tmpDB = new DB();

	$csvRegels = Count(file($fileBAL));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van EXIBAL bestand ('.$csvRegels.' records)');
  $prb->step = 0;
  $bankOutput = array();
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    if (!is_numeric($data[1])) continue;  // sla lege regels over
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);



    $aantal = str_replace(",",".",($data[8]));


    $bankOutput[$ndx][rekeningnr] = trim($data[5]).trim($data[6]);
    $bankOutput[$ndx][Bsaldo] = $aantal;
    $bankOutput[$ndx][Asaldo] = getAIRSvaluta(trim($data[5]).trim($data[6]));
    $ndx++;

/*
    $fonds = "Liquiditeiten";
    $query  = "INSERT INTO TEMP_portcon SET ";
  	$query .= "  rekeningnr = '".mysql_escape_string(trim($data[5]).trim($data[6]))."' ";
  	$query .= ", portefeuille = '".mysql_escape_string(trim($data[5]))."' ";
  	$query .= ", aantal = '".mysql_escape_string($aantal)."' ";
  	$query .= ", fonds  = '".mysql_escape_string($fonds)."' ";

  	//echo "<br>".$query;
  	$tmpDB->SQL($query);
  	$tmpDB->Query();
*/
  }
  //listarray($bankOutput);
  fclose($handle);
	if (!$POShandle = @fopen($filePOS, "r"))
	{
		$error[] = "FOUT bestand $filePOS is niet leesbaar";
		return false;
	}

	$csvRegels = Count(file($filePOS));

  $pro_multiplier = 100/$csvRegels;
  $pro_step = 0;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van EXIPOS bestand ('.$csvRegels.' records)');


  while ($data = fgetcsv($POShandle, 1000, ";"))
  {
    if (!is_numeric($data[1])) continue;  // sla lege regels over
    $portefeuille = trim($data[10]);
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $aantal = str_replace(",",".",($data[18]));
    $fonds = trim($data[5]);
    $query  = "INSERT INTO TEMP_portcon SET ";
  	$query .= "  portefeuille = '".mysql_escape_string($portefeuille)."' ";
  	$query .= ", rekeningnr = '".mysql_escape_string($portefeuille)."' ";
  	$query .= ", aantal = '".mysql_escape_string($aantal)."' ";
  	$fonds = substr("00000000000".$fonds,-6);
  	$query .= ", fonds  = '".mysql_escape_string($fonds)."' ";
  	//echo "<br>".$query;
  	$tmpDB->SQL($query);
  	$tmpDB->Query();
  }
  fclose($POShandle);
  // data staat in tijdelijke tabel nu koppelen met AIRS info
  $tmpQuery = "SELECT * FROM TEMP_portcon ORDER BY rekeningnr";
  $tmpDB->SQL($tmpQuery);
  $tmpDB->Query();


  $pro_multiplier = 100/$tmpDB->records();
  $pro_step = 0;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','verwerken uitvoer ('.$tmpDB->records().' records)');

  $row = 0;
  $ndx= 0;
  while ($tmpdata = $tmpDB->nextRecord())
  {
    $portefeuille = $tmpdata[portefeuille];
    if ($row == 0) $portefeuilleInCsv = $tmpdata[portefeuille];
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

    $outputArray[$portefeuille][B][$ndx][aantal] = trim($tmpdata[aantal]);
     if (trim($tmpdata[fonds]) == "Liquiditeiten")
    {
    	$outputArray[$portefeuille][B][$ndx][fonds] = "Liquiditeiten";
    	$outputArray[$portefeuille][B][$ndx][portefeuille] = $tmpdata[rekeningnr];


    }
    else
    {
    //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
      $outputArray[$portefeuille][B][$ndx][portefeuille] = trim($tmpdata[portefeuille]);
      $_isin = trim($tmpdata[fonds]);
      $outputArray[$portefeuille][B][$ndx][isin] = $_isin;
  	 if ( $_isin <> "")
     {
   		 $query = "SELECT * FROM Fondsen WHERE TGBCode = '".$_isin."' LIMIT 1 ";
    	 $DB->SQL($query);
    	 $DB->Query();
    	 if (!$fonds = $DB->nextRecord())
    	   $outputArray[$portefeuille][B][$ndx][fonds] = "TGB code komt niet voor fonds tabel ($_isin)";
    	 else
    	 {
      	 $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
    	 }

      }
      else
      {
    	 $outputArray[$portefeuille][B][$ndx][fonds] = "Geen TGB code bij ".$portefeuille;
      }
    }
    $ndx++;
  }


  InsertAIRSsection($portefeuilleInCsv);
  $prb->hide();
  $tijd = mktime() - $start;

  unlink($fileBAL);
  unlink($filePOS);

  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>