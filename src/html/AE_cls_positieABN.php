<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:08:56 $
 		File Versie					: $Revision: 1.1 $

 		$Log: AE_cls_positieABN.php,v $
 		Revision 1.1  2012/03/09 09:08:56  cvs
 		*** empty log message ***
 		
*/

class positieABN{
  
  var $datum;
  var $error = array();
  var $bestand;
  var $batchid;
  
  function positieABN()
  {

    $db = NEW DB();
    
    $this->datum = date("Y-m-d");
    $this->DeleteTempTable("TEMP_portcon");
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

	if (!$this->createTempTable($TempCreatequery,"TEMP_prtcon"))
	{
	  $this->error("FOUT: kan geen tijdelijke tabel aanmaken");
	  return false;
	}
    //$this->readFile($filename);
    
  }
  
  function getAIRSvaluta($rekeningnr,$gisteren=false)
  {
    $datum = $this->datum;
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
  
  function InsertAIRSsection($portefeuilleInCsv)
  {
    global $outputArray,$verlopenPortefeuille,$__appvar;
    $datum = $this->datum;
    
    $DB1 = new DB();
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
		      }
		      else
		      {
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
     //listarray($outputArray);   
  }
  
  
  
  function readFile($filename)
  {
    global $batchError, $ndx, $csvRegels,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;
    global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd,$bankOutput,$__depotBank,$__vermogensbeheerder;

    $start = mktime();
    
    if (!$handle = @fopen($filename, "r"))
	  {
	    $this->error("FOUT bestand $filename is niet leesbaar");
      return false;
	  }
	  $csvRegels = Count(file($filename));
    $pro_multiplier = 100/$csvRegels;
    $row = 0;
    $ndx= 0;

    $dataSet = array();
//////////////gfhdfhdgf/h////////////////////
  while ($data = fgets($handle, 4096))
  {
    if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
  
	  $regtel++;
	  
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
        $skipToNextRecord = true;
   	    break;
   	  case "940":  //type record
        $mtRec = 940;
        $skipToNextRecord = false;
   	    $dataSet["type"] = $data;
   	    break;
   	  case "571":
         $mtRec = 571;
   	    $skipToNextRecord = false;
   	    $dataSet["type"] = $data;
 			  break;
   	  case "-":  // einde record
   	    $skipToNextRecord = false;
        if ($mtRec == "571")
          $dataSet571[] = $dataSet;
        else
          $dataSet940[] = $dataSet;
        $dataSet = array();    
        $ndx++;
 			  break;
  	  default:
  	    if ($skipToNextRecord == true OR !isset($dataSet["type"]))
  	      break;
 	      if (substr($data,0,1) <> ":")
   	    {
   	      $dataSet["txt"] = substr($dataSet["txt"],0,-1)." ".$data;
   	    }
   	    else
   	    {
   	  	  $_regel = explode(":",$data);
   	  	  $_prevKey = $_regel[1];
   	  	  $dataSet[txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	    }
   		  break;
     }
    
  }


///
/// verwerken MT940
///
reset($dataSet940);
$ndx = 0;
$rekeningInBank = array();
foreach ($dataSet940 as $data)
{
  $rec = $this->convertMt940($data);
  /*
  $bankOutput[$ndx][rekeningnr] = $rec[0][rekeningnr].$rec[0][valuta];
  $bankOutput[$ndx][Bsaldo] = nf($rec[0][bedrag]);
  $bankOutput[$ndx][Asaldo] = nf($this->getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta]));
  $bankOutput[$ndx][AsaldoG] = nf($this->getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta],true));
  */
  $airsSaldo = nf($this->getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta]));
  $bankSaldo = nf($rec[0][bedrag]);
  if ($airsSaldo <> $bankSaldo AND trim($airsSaldo) <> "Einddatum")
  {
    addRecord(array("bank"         => "aab",
                    "portefeuille" => $rec[0][rekeningnr].$rec[0][valuta],
                     "fonds"        => "Liquiditeiten",
                     "aantal_airs"  => nf($this->getAIRSvaluta($rec[0][rekeningnr].$rec[0][valuta])),
                     "aantal_bank"  => nf($rec[0][bedrag]),
                     "file"         => $this->bestand,
                     "batchid"      => $this->batchid));
  }                   
  $rekeningInBank[] = $rec[0][rekeningnr];
  $ndx++;
}

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
      /*
      $bankOutput[$ndx][rekeningnr] = $rekeningInAIRSinDB[$tel];
      $bankOutput[$ndx][Bsaldo] = "Bestaat niet";
      $bankOutput[$ndx][Asaldo] = nf($this->getAIRSvaluta($rekeningInAIRSinDB[$tel]));
      $rekeningInBank[]         = "";
      */
      $airsSaldo = nf($this->getAIRSvaluta($rekeningInAIRSinDB[$tel]));
      if ( (int)$airsSaldo <> 0 AND $airsSaldo <> "Einddatum")
      {
        addRecord(array("bank"          => "aab",
                        "portefeuille"  => $rekeningInAIRSinDB[$tel],
                         "fonds"        => "Liquiditeiten",
                         "aantal_airs"  => nf($this->getAIRSvaluta($rekeningInAIRSinDB[$tel])),
                         "aantal_bank"  => "Bestaat niet",
                         "file"         => $this->bestand,
                         "batchid"      => $this->batchid));
      }                   
      $ndx++;
    }
  }
  
  
  
  
///
/// verwerken MT571
///

$tmpDB = new DB();

reset($dataSet571);
foreach ($dataSet571 as $data)
{

  $rec571 = $this->convertMt571($data);

  reset($rec571);
  foreach ($rec571 as $data)
  {
    if (!isset($data[AABcode])) continue;  // sla lege regels over

    $portefeuille = trim($data[portefeuille]);
    $aantal       = $data[aantal];
    $fonds        = trim($data[AABcode]);

    $row++;

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    $query  = "INSERT INTO TEMP_portcon SET ";
    $query .= "  portefeuille = '".mysql_escape_string($portefeuille)."' ";
    $query .= ", rekeningnr = '".mysql_escape_string($portefeuille)."' ";
    $query .= ", aantal = '".mysql_escape_string($aantal)."' ";
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
      $this->InsertAIRSsection($portefeuilleInCsv);
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
    	$DB->SQL($query);
    	$DB->Query();
    	if (!$fonds = $DB->nextRecord())
    	   $outputArray[$portefeuille][B][$ndx][fonds] = "AAB/ABR code komt niet voor fonds tabel ($_isin)";
    	else
    	{
        $outputArray[$portefeuille][B][$ndx][fonds] = $fonds[Omschrijving];
    	}

    }
    else
    {
      $outputArray[$portefeuille][B][$ndx][fonds] = "Geen AAB/ABR code bij ".$portefeuille;
    }
   $ndx++;
  }


    $this->InsertAIRSsection($portefeuilleInCsv);

    fclose($handle);
  
    $tijd = mktime() - $start;
    //unlink($filename);
    if (Count($this->error) == 0)
  	  return true;
    else
  	  return false;
  }
  
  
  /// internal functions
  
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
  }
  $teller = 0;
  $wr = array();
  foreach ($tmpWR as $portefeuille => $fondsArray) // oude Array layout
  {
   	 foreach ($fondsArray as $AABCode => $aantalArray)
   	 {
   	   $aantal = $aantalArray["aantal"];
   	   if ($aantal <> 0)
   	   {
   	     $wr[] = array("portefeuille" => $portefeuille,
   	                   "aantal"       => $aantal,
   	                   "AABcode"      => $AABCode);
   	   }
   	 }
  }

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
        $wr[bedrag]      = $this->cnvBedrag($_tmp) * $sign;
        $wr[valuta]      = substr($_r[1],7,3);
        break;
    }
  }
  $data[$dnx] = $wr;
  return $data;  // geeft arrayset met deelrecords terug

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
  
  function error($txt)
  {
    $this->error[] = $txt;
  }  
  
} 		
?>