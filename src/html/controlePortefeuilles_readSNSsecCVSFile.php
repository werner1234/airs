<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/03/04 07:14:58 $
 		File Versie					: $Revision: 1.2 $

 		$Log: controlePortefeuilles_readSNSsecCVSFile.php,v $
 		Revision 1.2  2011/03/04 07:14:58  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/06/14 15:18:24  cvs
 		*** empty log message ***




 		functie in controlePortefeuilles.php
*/
include_once("controlePortefeuilles_SNSsec_functies.php");




function getAIRSvaluta($rekeningnr,$gisteren=false)
{
  global $datum;
  $tmpDB = New DB();

  if ($gisteren)
    $qExtra = "Rekeningmutaties.boekdatum <= DATE_SUB('".$datum."',INTERVAL 1 DAY) ";
  else
    $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";

  $query = "
SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal
FROM Rekeningmutaties, Rekeningen
WHERE
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningmutaties.boekdatum >= '".substr($datum,0,4)."' AND
  Rekeningmutaties.Rekening = '".$rekeningnr."' AND
	$qExtra
GROUP BY Rekeningen.Valuta
ORDER BY Rekeningen.Valuta";
// echo "<PRE>$query</PRE><hr>";

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


function readSNSsecCvsFile($fileSAL,$filePOS)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd,$bankOutput,$__appvar;


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




    ////// EINDE interne functies

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($fileSAL, "r"))
	{
		$error[] = "FOUT Saldi bestand $fileSAL is niet leesbaar";
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
	  $error[] = "FOUT: kan geen tijdelijke tabel aanmaken";
	  return false;
	}

	$tmpDB = new DB();

	$csvRegels = Count(file($fileSAL));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van Saldi bestand ('.$csvRegels.' records)');
  $prb->step = 0;
  $bankOutput = array();
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);
    if (trim($data[1]) <> "CASHPOS")
    {
       $error[] = "FOUT kolom 1 bevat geen CASHPOS";
	   return false;
    }
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    //$reknr = getRekeningNr($data[11],$data[6]);

    $reknr = trim($data[11].$data[6]);
    if (empty($reknr))
    {
       if ($data[5] == 0)
       {
          continue;
       }
       else
       {
         $reknr = "Geen Airs rek bij port $data[11], valuta $data[6]";
       }
    }
    $bankOutput[$ndx]["rekeningnr"]  = $reknr;
    $bankOutput[$ndx]["Bsaldo"]      = str_replace(",",".",$data[5]);
    $bankOutput[$ndx]["Asaldo"]      = number_format(getAIRSvaluta($reknr),2,".","");
    $bankOutput[$ndx]["AsaldoG"]     = number_format(getAIRSvaluta($reknr,true),2,".","");
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

  fclose($handle);
  if (!$POShandle = @fopen($filePOS, "r"))
  {
	$error[] = "FOUT POS bestand $filePOS is niet leesbaar";
	return false;
  }

  $csvRegels = Count(file($filePOS));

  $pro_multiplier = 100/$csvRegels;
  $pro_step = 0;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van postitie bestand ('.$csvRegels.' records)');


  while (!feof($POShandle))
  {
    $dataRaw = fgets($POShandle, 4096);
    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);
    if (trim($data[1]) <> "SECURITYPOS")
    {
       $error[] = "FOUT kolom 1 bevat geen SECURITYPOS";
	   return false;
    }

    $portefeuille = trim($data[3]);
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $aantal = $data[7];
    $fonds = trim($data[5]);

    $query  = "INSERT INTO TEMP_portcon SET ";
  	$query .= "  portefeuille = '".mysql_escape_string($portefeuille)."' ";
  	$query .= ", rekeningnr = '".mysql_escape_string($portefeuille)."' ";
  	$query .= ", aantal = '".mysql_escape_string($aantal)."' ";
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
    if ($row == 0)
    {
      $portefeuilleInCsv = $tmpdata[portefeuille];
      $portefeuilleArray[] = $tmpdata[portefeuille];
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
   		 $query = "SELECT * FROM Fondsen WHERE snsSecCode = '".$_isin."' LIMIT 1 ";
    	 $DB->SQL($query);
    	 $DB->Query();
    	 if (!$fonds = $DB->nextRecord())
    	   $outputArray[$portefeuille][B][$ndx][fonds] = "SNS SEC code komt niet voor fonds tabel ($_isin)";
    	 else
    	 {
      	   $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
    	 }

      }
      else
      {
    	 $outputArray[$portefeuille][B][$ndx][fonds] = "Geen SNS SEC code bij ".$portefeuille;
      }
    }
    $ndx++;
  }


  InsertAIRSsection($portefeuilleInCsv);
  $prb->hide();
  $tijd = mktime() - $start;

  unlink($fileSAL);
  unlink($filePOS);

  if (Count($error) == 0)
  {
  	return true;
  }
  else
  {
    return false;
  }
}
?>