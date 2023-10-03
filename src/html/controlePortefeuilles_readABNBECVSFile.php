<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/16 08:20:59 $
 		File Versie					: $Revision: 1.4 $

 		$Log: controlePortefeuilles_readABNBECVSFile.php,v $
 		Revision 1.4  2013/12/16 08:20:59  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2011/04/26 07:55:54  cvs
 		Aangepaste versie van Rouw en Ceulen opgehaald 26-4-2011
 		
 		Revision 1.1  2010/11/03 10:43:23  cvs
 		*** empty log message ***





 		functie in controlePortefeuilles.php
*/
include_once("controlePortefeuilles_ABNBE_functies.php");

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


function readABNBECvsFile($fileSAL,$filePOS)
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

    DeleteTempTable("TEMP_abnbeSaldi");
	$TempCreatequery = "
CREATE TABLE `TEMP_abnbeSaldi` (
  `id` int(11) NOT NULL auto_increment,
  `RekeneningNr` varchar(100) default NULL,
  `saldo` varchar(100) default NULL,
  `valuta` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
	if (!createTempTable($TempCreatequery,"TEMP_abnbeSaldi"))
	{
	  $error[] = "FOUT: kan geen tijdelijke tabel aanmaken";
	  return false;
	}
  $dbTmpS = new DB();
  
	$tmpDB = new DB();
  $db2 = new DB();
	$csvRegels = Count(file($fileSAL));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  

  $prb->setLabelValue('txt1','inlezen van Saldi bestand ('.$csvRegels.' records)');
  $prb->step = 0;
  $bankOutput = array();
  $ndx= 0;
  
  // inlezen saldi bestand
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;
    
    if (trim(textPart($dataRaw,1,15) <> "000000000000000"))
    {
       $error[] = "FOUT kolom 1 bevat geen geldige waarde";
	     return false;
    }

    $_valuta       = textPart($dataRaw,33,35);
    $_RekeneningNr = textPart($dataRaw,21,32);
    $_saldo        = textPart($dataRaw,60,77);

    $querySaldi = "INSERT INTO TEMP_abnbeSaldi SET 
    saldo       ='".$_saldo."',
    valuta      ='".$_valuta."',
    RekeneningNr='".$_RekeneningNr."' ";
    if (trim($_valuta) <> "")
      $dbTmpS->executeQuery($querySaldi);
  }
  
  $querySaldi = "SELECT sum(saldo) AS saldo, RekeneningNr, valuta FROM TEMP_abnbeSaldi GROUP BY RekeneningNr, valuta ORDER BY RekeneningNr";
  $dbTmpS->executeQuery($querySaldi);
  while($data = $dbTmpS->nextRecord())
  { 
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $reknr = getRekeningNr($data["RekeneningNr"],$data["valuta"]);

    if (empty($reknr))
    {
       if ($data["saldo"] == 0)
       {
          continue;
       }
       else
       {
         $reknr = "Geen Airs rek bij port ".$data["RekeneningNr"].", valuta ".$data["valuta"];
       }
    }
    $qqq = "SELECT Rekeningen.Rekening, Rekeningen.Valuta, Portefeuilles.Einddatum, Portefeuilles.Portefeuille FROM
            Rekeningen Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE
            Rekeningen.Rekening = '$reknr' AND Portefeuilles.Einddatum > NOW()" ;
    $db2->SQL($qqq);
    $rekNrCheck = $db2->lookupRecord();   // check of portefeuille niet is verlopen..
    if ($rekNrCheck["Rekening"] <> "")
    {
      $bankOutput[$ndx]["rekeningnr"] = $reknr;
      $bankOutput[$ndx]["Bsaldo"]     = $data["saldo"];
	  
      $bankOutput[$ndx]["Asaldo"]     = number_format(getAIRSvaluta($bankOutput[$ndx][rekeningnr]),2,".","");
      $ndx++;
    }


  }
  //listarray($bankOutput);
  //exit;
  fclose($handle);

  if (!$POShandle = @fopen($filePOS, "r"))
  {
	$error[] = "FOUT POS bestand $filePOS is niet leesbaar";
	return false;
  }

  $csvRegels = Count(file($filePOS));

 DeleteTempTable("TEMP_abnbePositie");
	$TempCreatequery = "
CREATE TABLE `TEMP_abnbePositie` (
  `id` int(11) NOT NULL auto_increment,
  `portefeuille` varchar(100) default NULL,
  `Fondscode` varchar(100) default NULL,
  `aantal` float default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
";
	if (!createTempTable($TempCreatequery,"TEMP_abnbePositie"))
	{
	  $error[] = "FOUT: kan geen tijdelijke tabel aanmaken";
	  return false;
	}
  $dbTmpS = new DB();

  $pro_multiplier = 100/$csvRegels;
  $pro_step = 0;
  $row = 0;


  $prb->setLabelValue('txt1','inlezen van postitie bestand ('.$csvRegels.' records)');

  $ludb =new DB();
  //inlezen positie bestand
  $ndx= 0;
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
      
    $queryPositie = "INSERT INTO TEMP_abnbePositie SET 
    portefeuille   ='".$data[3]."',
    Fondscode      ='".$data[5]."',
    aantal         ='".$data[7]."' ";
  
    $dbTmpS->executeQuery($queryPositie);
  }


  $queryPositie = "SELECT sum(aantal) AS aantal, portefeuille, Fondscode FROM TEMP_abnbePositie GROUP BY portefeuille, Fondscode ORDER BY portefeuille, Fondscode";
  $dbTmpS->executeQuery($queryPositie);
  while($data = $dbTmpS->nextRecord())
  { 
    $portefeuille = trim($data["portefeuille"]);
    $row++;

  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $aantal = $data["aantal"];
    $fonds = trim($data["Fondscode"]);
    $ludbq = "SELECT Portefeuille FROM Rekeningen WHERE Rekening LIKE '$portefeuille%'";
	  $ludb->SQL($ludbq);
	  $luRec = $ludb->LookupRecord();
	
	  if ($luRec["Portefeuille"] <> "")
	  {
      $query  = "INSERT INTO TEMP_portcon SET ";
  	  $query .= "  portefeuille = '".mysql_escape_string($luRec["Portefeuille"])."' ";
  	  $query .= ", rekeningnr = '$portefeuille' ";
  	  $query .= ", aantal = '".mysql_escape_string($aantal)."' ";
  	  $query .= ", fonds  = '".mysql_escape_string($fonds)."' ";
  	//echo "<br>".$query;
  	  $tmpDB->SQL($query);
  	  $tmpDB->Query();
	  }  
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

    $outputArray[$portefeuille][B][$ndx][aantal] = trim($tmpdata[aantal]);

    //$outputArray[$portefeuilleInCsv][B][$idx][match] = 0;
    $outputArray[$portefeuille][B][$ndx][portefeuille] = trim($tmpdata[portefeuille]);
    $_isin = trim($tmpdata[fonds]);
    $outputArray[$portefeuille][B][$ndx][isin] = $_isin;
  	if ( $_isin <> "")
    {
   	  $query = "SELECT * FROM Fondsen WHERE aabbeCode = '".$_isin."' LIMIT 1 ";
      $DB->SQL($query);
      $DB->Query();
      if (!$fonds = $DB->nextRecord())
        $outputArray[$portefeuille][B][$ndx][fonds] = "AAB BE code komt niet voor fonds tabel ($_isin)";
      else
      {
        $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
      }
    }
    else
    {
      $outputArray[$portefeuille][B][$ndx][fonds] = "Geen AAB BE code bij ".$portefeuille;
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