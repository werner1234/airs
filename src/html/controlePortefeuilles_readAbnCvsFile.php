<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/03/12 10:02:21 $
 		File Versie					: $Revision: 1.14 $

 		$Log: controlePortefeuilles_readAbnCvsFile.php,v $
 		Revision 1.14  2014/03/12 10:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.13  2013/12/16 08:20:59  cvs
 		*** empty log message ***

 		Revision 1.12  2011/12/05 12:43:06  cvs
 		Beta release 0.9 1 december 2012

 		Revision 1.11  2011/03/04 07:14:58  cvs
 		*** empty log message ***

 		Revision 1.10  2010/05/06 13:50:20  cvs
 		*** empty log message ***

 		Revision 1.9  2008/04/03 08:17:16  cvs
 		telling stukken aangepast

 		Revision 1.7  2008/04/02 09:13:35  cvs
 		volgende poging meerdere record optellen

 		Revision 1.6  2008/04/01 14:10:45  cvs
 		meerdere regels 35B optellen in MT571 records

 		Revision 1.5  2007/07/06 06:37:01  cvs
 		controle wel AIRS geen BANK bij geldrekening

 		Revision 1.4  2006/11/03 11:22:35  rvv
 		Na user update

 		Revision 1.3  2006/10/31 11:53:19  rvv
 		Voor user update.

 		Revision 1.2  2005/10/04 11:03:50  cvs
 		*** empty log message ***

 		Revision 1.1  2005/09/30 11:09:34  cvs
 		*** empty log message ***

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


function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}


function convertMt571($record)
{
  $data = array();
  $dnx = 0;
  $_data = explode(chr(10),$record[txt]);
  //listarray($_data);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);

    switch ($_r[0])
    {
      case "83a":
        $wr[$subRecord][portefeuille] = intval($_r[1]);
        break;
      case "72":
        $subRecord++;
        $wr[$subRecord][portefeuille] = $wr[$subRecord-1][portefeuille];
        break;
      case "33B":
        $wr[$subRecord][fondsValuta] = substr($_r[1],0,3);
        break;
      case "35B":
        $wr[$subRecord][AABcode] = substr($_r[1],5,6);
        break;
      case "35H":
        if (substr($_r[1],0,1) == "N")
          $sign = -1;
        else
          $sign = 1;

        for($xx=0;$xx < strlen($_r[1]);$xx++)
        {
          $_l = 	substr($_r[1],$xx,1);
          if ($_l >= "0" AND $_l <= "9")
            $wr[$subRecord][aantal] .= $_l;
          elseif ($_l == ",")
            $wr[$subRecord][aantal] .= ".";
        }
        $wr[$subRecord][aantal] = $wr[$subRecord][aantal] * $sign;
        break;
    }
  }

  for ($ndx=0; $ndx < count($wr); $ndx++)  // ontdubbelen en aantallen optellen
  {
    $a = $wr[$ndx];
    $tmpWR[$a["portefeuille"]][$a["AABcode"]]["aantal"] += $a["aantal"];
    $tmpWR[$a["portefeuille"]][$a["AABcode"]]["fondsValuta"] = $a["fondsValuta"];
  }
  $teller = 0;
  $wr = array();

  foreach ($tmpWR as $portefeuille => $fondsArray) // oude Array layout
  {
   	 foreach ($fondsArray as $AABCode => $aantalArray)
   	 {
   	   $aantal = $aantalArray["aantal"];
       $fondsValuta = $aantalArray["fondsValuta"];
   	   if ($aantal <> 0)
   	   {
   	     $wr[] = array("portefeuille" => $portefeuille,
   	                   "aantal"       => $aantal,
   	                   "AABcode"      => $AABCode,
                       "fondsValuta"  => $fondsValuta);
   	   }
   	 }
  }
  //listarray($wr);
  return $wr;  // geeft arrayset met deelrecords terug

}

function convertMt940($record)
{
  $data = array();
  $dnx = 0;
  $_data = explode(chr(10),$record[txt]);
  $wr = array();
  $subRecord = 0;
  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    switch ($_r[0])
    {
      case "25":
        $wr[rekeningnr] = intval($_r[1]);
        break;
      case "62F":
        if (substr($_r[1],0,1) == "D")
          $sign = -1;
        else
          $sign = 1;

        $_tmp = substr($_r[1],10);
        $wr[bedrag]      = cnvBedrag($_tmp) * $sign;
        $wr[valuta]      = substr($_r[1],7,3);
        break;
    }
  }
  $data[$dnx] = $wr;
  return $data;  // geeft arrayset met deelrecords terug

}

function getAIRSvaluta($rekeningnr,$gisteren=false)
{
  global $datum;
  $DB1 = New DB();
  $tmpDB = New DB();
  if ($gisteren)
    $qExtra = "Rekeningmutaties.boekdatum <= DATE_SUB('".$datum."',INTERVAL 1 DAY) ";
  else
    $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";

  // kijk of portefeuille is verlopen
	$query = "
SELECT `Portefeuilles`.`Einddatum`, `Portefeuilles`.`Portefeuille`, `Rekeningen`.`Rekening`
FROM `Portefeuilles`
INNER JOIN `Rekeningen` ON `Rekeningen`.`Portefeuille` = `Portefeuilles`.`Portefeuille`
WHERE
  `Rekeningen`.`Rekening` = '".$rekeningnr."' AND
  `Portefeuilles`.`Einddatum` > '$datum'
 ";

	$DB1->SQL($query);
	if ($dummy = $DB1->lookupRecord()) //
	{

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


    $tmpDB->SQL($query);
    if( $data = $tmpDB->lookupRecord())
      return $data[totaal];
    else
      return "Geen AIRS info";
	}
	else
	  return "Einddatum";
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


function readAbnCvsFile($file5XX,$file940)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd,$bankOutput,$__depotBank,$__vermogensbeheerder;


	function InsertAIRSsection($portefeuilleInCsv)
	{
	  global $datum,$DB1,$outputArray,$verlopenPortefeuille,$__appvar;
	  $tmpDB = new DB();
	  // kijk of portefeuille is verlopen
	  $query = "SELECT Einddatum, Portefeuille FROM Portefeuilles WHERE  Portefeuille = '".$portefeuilleInCsv."' AND Einddatum > '".$datum."'";
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
		        $outputArray[$portefeuilleInCsv][A][$idx][valuta]       = trim($recordData[valuta]);
		        $idx++;
		      }

		    }
		  }
		  verwijderTijdelijkeTabel($portefeuilleInCsv,$datum);
		}
		else
		  $verlopenPortefeuille[] = $portefeuilleInCsv;  // push waarde in de te negeren portefeuilles
	}  // einde functie

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($file5XX, "r"))
	{
		$error[] = "FOUT bestand $file5XX is niet leesbaar";
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
  `valuta` varchar(10) default NULL,
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

	$csvRegels = Count(file($file5XX));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van MT5XX bestand ('.$csvRegels.' records)');
  $prb->step = 0;
  $bankOutput = array();
  while ($data = fgets($handle, 4096))
  {
  if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen

	$regtel++;
	$prb->moveNext();
	$skipToNextRecord = false;
  switch (trim($data))
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "500":
   	case "501":
   	case "510":
   	case "554":
   	case "940":  //type record
   	  $skipToNextRecord = true;
   	  break;
   	case "571":
   	  $skipToNextRecord = false;
   	  $dataSet[$ndx][type] = $data;
 			break;
   	case "-":  // einde record
   	  $skipToNextRecord = false;
      $ndx++;
 			break;
  	default:
  	  if ($skipToNextRecord == true OR !isset($dataSet[$ndx][type]))
  	    break;
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	  }
   	  else
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];
   	  	$dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }
   		break;
   }

}

  $dataSet571 = $dataSet;

	if (!$handle = @fopen($file940, "r"))
	{
		$error[] = "FOUT bestand $file940 is niet leesbaar";
		return false;
	}

  $dataSet = array();
  $csvRegels = Count(file($file940));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;
  $regtel = 0;
  $prb->setLabelValue('txt1','inlezen van MT940 bestand ('.$csvRegels.' records)');
  $prb->step = 0;
  $bankOutput = array();
  while ($data = fgets($handle, 4096))
  {
    if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
	$regtel++;
	$prb->moveNext();
	$skipToNextRecord = false;
	switch (trim($data))
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "500":
   	case "501":
   	case "510":
   	case "554":
 	  case "571":
 	    $geent940++;
   	  $skipToNextRecord = true;
   	  break;
   	case "940":  //type record
   	  $t940++;
   	  $skipToNextRecord = false;
   	  $dataSet[$ndx][type] = $data;
 			break;
   	case "-":  // einde record
   	  $skipToNextRecord = false;
      $ndx++;
 			break;
  	default:
  	  if ($skipToNextRecord == true OR !isset($dataSet[$ndx][type]))
  	    break;
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	  }
   	  else
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];
   	  	$dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }
   		break;
   }

}

$dataSet940 = $dataSet;
unset($dataSet);



reset($dataSet940);
$ndx = 0;
$rekeningInBank = array();
foreach ($dataSet940 as $data)
{
  $rec = convertMt940($data);
  $bankOutput[$ndx][rekeningnr] = $rec[0][rekeningnr].$rec[0][valuta];
  $bankOutput[$ndx][Bsaldo] = nf($rec[0][bedrag]);
  $bankOutput[$ndx][Asaldo] = nf(getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta]));
  $bankOutput[$ndx][AsaldoG] = nf(getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta],true));

  $rekeningInBank[] = $rec[0][rekeningnr];
  $ndx++;
}

/*
* aanpassing juli 2007
*/
$DB3 = new DB();

// zoek portefeuille bij rekeningnr
$q = "SELECT Portefeuille FROM Rekeningen WHERE Rekening = '".$rec[0][rekeningnr].$rec[0][valuta]."'";
$DB3->SQL($q);
$tRec = $DB3->lookupRecord();
// zoek nu in gevonden portefeuille naar vermogensbeheerder en Depotbank
$q = "SELECT Depotbank, Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille = '".$tRec['Portefeuille']."' ";
$DB3->SQL($q);
$tRec = $DB3->lookupRecord();
$__depotBank = $tRec['Depotbank'];
$__vermogensbeheerder = $tRec['Vermogensbeheerder'];
//haal nu alle portefeuilles op van de combinatie vermogensbeheerder/depotbank op die een Einddatum < datum hebben
  $rekeningInAIRSinDB = array();
  if ($__depotBank AND $__vermogensbeheerder)
  {
    $DB6 = new DB();
    $query = "
    SELECT
      Rekeningen.Rekening, Portefeuilles.Portefeuille, Portefeuilles.Einddatum
    FROM
      Portefeuilles
   JOIN
      Rekeningen on  Portefeuilles.Portefeuille =  Rekeningen.Portefeuille
    WHERE
       Rekeningen.Memoriaal <> 1 AND
       Portefeuilles.Einddatum >  '".$datum."' AND
       Portefeuilles.Vermogensbeheerder = '".$__vermogensbeheerder."' AND
       Rekeningen.Depotbank = '".$__depotBank."' AND
       Portefeuilles.Depotbank = '".$__depotBank."'";
    $DB6->SQL($query);
    $DB6->Query();
    while ($Arec = $DB6->nextRecord())
    {
      $rekeningInAIRSinDB[] = $Arec['Rekening'];
    }

  }

  for($tel=0; $tel < count($rekeningInAIRSinDB); $tel++)
  {
    $bankRek = ereg_replace("[^0-9]","",$rekeningInAIRSinDB[$tel]);
    if (!in_array($bankRek,$rekeningInBank))
    {
      $bankOutput[$ndx][rekeningnr] = $rekeningInAIRSinDB[$tel];
      $bankOutput[$ndx][Bsaldo] = "Bestaat niet";
      $bankOutput[$ndx][Asaldo] = nf(getAIRSvaluta($rekeningInAIRSinDB[$tel]));
      $rekeningInBank[]         = "";
      $ndx++;
    }
  }
/*
*  einde
*/




unset($dataSet940);

$csvRegels = count($dataSet571);
$pro_multiplier = 100/$csvRegels;
$pro_step = 0;
$prb->setLabelValue('txt1','converteren van MT571 records ('.$csvRegels.' records)');
reset($dataSet571);
foreach ($dataSet571 as $data)
{

  $rec571 = convertMt571($data);

  reset($rec571);
  foreach ($rec571 as $data)
  {

    if (!isset($data[AABcode])) continue;  // sla lege regels over

    $portefeuille = trim($data[portefeuille]);
    $aantal       = $data[aantal];
    $fonds        = trim($data[AABcode]);
    $valuta       = $data["fondsValuta"];
    $row++;

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $query  = "INSERT INTO TEMP_portcon SET ";
    $query .= "  portefeuille = '".mysql_escape_string($portefeuille)."' ";
    $query .= ", rekeningnr = '".mysql_escape_string($portefeuille)."' ";
    $query .= ", aantal = '".mysql_escape_string($aantal)."' ";
    $query .= ", valuta = '".mysql_escape_string($valuta)."' ";
    $query .= ", fonds  = '".mysql_escape_string($fonds)."' ";
//    echo "<br>".$query;
    $tmpDB->SQL($query);
    $tmpDB->Query();
  }
}



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
    $outputArray[$portefeuille][B][$ndx][portefeuille] = trim($tmpdata[portefeuille]);

    $_isin = trim($tmpdata[fonds]);
    $outputArray[$portefeuille][B][$ndx][isin] = $_isin;
    if ( $_isin <> "")
    {
   	  $query = "SELECT * FROM Fondsen WHERE AABCode = '".$_isin."' OR ABRCode = '".$_isin."' LIMIT 1 ";
      //listarray($query);
    	$DB->SQL($query);
    	$DB->Query();
    	if (!$fonds = $DB->nextRecord())
    	   $outputArray[$portefeuille][B][$ndx][fonds] = "AAB/ABR code komt niet voor fonds tabel ($_isin)";
    	else
    	{
        $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
        $outputArray[$portefeuille][B][$ndx][valuta] = $fonds[Valuta];
    	}

    }
    else
    {
      $outputArray[$portefeuille][B][$ndx][fonds] = "Geen AAB/ABR code bij ".$portefeuille;
    }
   $ndx++;
  }



  InsertAIRSsection($portefeuilleInCsv);
  $prb->hide();
  $tijd = mktime() - $start;

  unlink($file5XX);
  unlink($file940);

  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>