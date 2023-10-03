<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/08 13:08:44 $
 		File Versie					: $Revision: 1.21 $

 		$Log: rabo_import.php,v $
 		Revision 1.21  2020/07/08 13:08:44  cvs
 		form omgezet van get naar post


==== velden SECURITYTRANS
[1]  = Soort bestand
[3]  = referenteie
[6]  = portefeille
[8]  = transactiecode
[11] = Transactiedatum
[12] = Settlementdatum
[14] = valutakoers
[15] = externe kosten
[16] = valuta externe kosten
[17] = interne kosten
[18] = valuta interne kosten
[19] = Fondscode
[21] = nota-bedrag
[22] = rekening-valuta
[23] = couponbedrag
[24] = valuta coupon
[28] = aantal
[29] = fondsvaluta
[30] = koers in valuta
[31] = belasting
[32] = valuta belasting
[33] = stornering als gevuld

==== velden CASHTRANS
[1]  = Soort bestand
[40] = Rekening
[41] = Transactiecode
[42] = Bedrag
[43] = Valutacode
[44] = Bedrag in afreken-valuta
[45] = Afreken-valuta
[46] = Wijzigingsdatum
[47] = Vrije tekst
*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("rabo_functies.php");
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("RABO");
$DB = new DB();
$DB->executeQuery("SELECT * FROM raboTransactieCodes ORDER BY bankCode");

$transactieCodes  = array();
$_transactieArray = array();

$content = array();

while ($codeRec = $DB->nextRecord())
{

  $tc = explode("_",$codeRec["bankCode"]);

  $transactieCodes[$tc[0]][$tc[1]] = $codeRec["doActie"];
  $_transactieArray[$tc[0]][$tc[1]] = $codeRec["bankCode"];
}

$data = $_REQUEST;



$skipFoutregels = array();

$doIt = $data["doIt"];

if ($doIt == "1")  // validatie mislukt, wat te doen?
{
	
  if ($doIt == "1")  // validatie mislukt, wat te doen?
  {
    $bestand = $data["bestand"];
    $bestand2 = $data["bestand2"];
    switch ($data["action"])
    {
      case "stop":
        echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
      	if (file_exists($bestand) ) unlink($bestand);
      	if (file_exists($bestand2) ) unlink($bestand2);
  		  exit();
        break;
      case "retry":
        $doIt = 0;
        $file = $bestand;
        $file2 = $bestand2;
        break;
      default: 
        $skipFoutregels = explode(",",$foutregels);
  		  array_shift($skipFoutregels);  // verwijder eerste lege key
  		  $file = $bestand; 
  		  $file2 = $bestand2; 
    }
  }
 
}


//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//



$content["style"] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);

if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ").")<br>
  <br>
  de import is geannuleerd ";
  	exit;
  }
}

//
// setup van de progressbar
//
$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar


$csvRegels = 1;
include("rabo_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Rabo CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
  
  $error = array();
//  if ($_GET["rabo1"] == "1")
//  {
    validateCvsFile($file, "single");
//  }
//  else
//  {
//    validateCvsFile($file, "STRA");
//    validateCvsFile($file2, "CTRA");
//  }
  if (count($error) > 0)
	{
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van CSV bestand<br>
     	 Bestandsnaam :<?=$file?>
    	</td>
  	</tr>
<?
	$foutregels = "";
	$_vsp = "";
	for ($x=0;$x < count($error);$x++)
	{
		$_spA = explode(":",$error[$x]);
		$_sp = trim($_spA[0]);
		if ( $_vsp <> $_sp )
		$foutregels .= ",".$_sp;
		$_vsp = $_sp;
?>
  	<tr>
    	<td bgcolor="#BBBBBB"><?=$x?></td>
    	<td>&nbsp;&nbsp;
	      <?=$error[$x];?>
  	  </td>
  	</tr>

<?

	}
?>
	</table>
	<br>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="post">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="bestand2" value="<?=$file2?>">
  	<input type="hidden" name="rabo1" value="<?=$rabo1?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
    	<option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ');

debug($transactieCodes);
echo "<hr/>Verwerken van $file <br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";

///////////////



//debug($transactieCodes);
//debug($_transactieArray);
$transactieCodesSkipped = array();
$kol = 0;
while ($data = fgetcsv($handle, 4096, "|"))
{
  $row++;

  if ($data[0] == null )  // skip lege regels
  {
    continue;
  }

  array_unshift($data, "leeg");

  if (substr($data[1],0,1) == ";")  // in een door airs samengevoegd bestand starten lege regels met een ;
  {
    continue;
  }


  $kol = count($data);
  if (stristr($data[$kol-1],";"))
  {
    $laatsteKols = explode(";",$data[$kol-1]);   // laatste veld bevat evt ; bestandsnaam deze opsplitsen in afzonderlijke kolommen
    $data[$kol-1] = $laatsteKols[0];
    $data[$kol]   = $laatsteKols[1];
  }


//debug($data);
  $fonds = array();
  $fData =array();
  $fData["row"]  = $row;
  $fData["file"] = $file;

  $tcParts = explode("-",$data[2]);
  $transCode = $tcParts[0]."-".$tcParts[1]."-".$tcParts[2];

  switch (strtoupper(substr($data[1],0,9)))
  {
    case "CASHTRANS":
      $recType = "geld";
      $transId = $data[2];

      $fData["transactieId"]     = $transCode; //  Reference
      $fData["transidCash"]      = $data[2];
      $fData["rekening"]         = $data[3]; //  Accountnr
      $fData["rekeningtype"]     = $data[4]; //  AccSubType
      $fData["transactiecode"]   = $data[8]; //  TransCode
      $fData["boekdatum"]        = raboDatum($data[10]); //  Date
      $fData["settlementdatum"]  = raboDatum($data[11]); //  Valuedate
      $fData["storno"]           = $data[12]; //  Status (2=storno)
      $fData["bedrag"]           = $data[13]; //  Amount
      $fData["valuta"]           = $data[14]; //  AmountCcy
      $fData["nettoBedrag"]      = $data[15]; //  AmountinAccCCY
      $fData["wisselkoers"]      = 1/$data[18]; //  ExhRate
      $fData["omschrijving"]     = $data[21]; //  Freetext
      $fData["subfile"]          = $data[33];



      break;
    case "SECURITYT":
      $recType = "stukken";
      $transId = $data[2];

      $fData["transactieId"]         = $transCode; //  Tr Id
      $fData["transidSec"]           = $data[2];
      $fData["portefeuille"]         = $data[5]; //  AccNr
      $fData["rekening"]             = $data[5]; //  AccNr
      $fData["rekeningtype"]         = $data[6]; //  AccSbType
      $fData["valuta"]               = $data[22];
      $fData["transactiecode"]       = $data[7]; //  TransCode
      $fData["boekdatum"]            = raboDatum($data[10]); //  Boekdatum
      $fData["settlementdatum"]      = raboDatum($data[11]); //  ValDat
      $fData["storno"]               = $data[12]; //  Status (2=storno)
      $fData["wisselkoers"]          = 1/$data[13]; //  Exh Rate
      $fData["externeKosten"]        = $data[14]; //  Ext Cost
      $fData["externeKostenValuta"]  = $data[15]; //  ExtCostCcy
      $fData["interneKosten"]        = $data[16]; //  INtCost
      $fData["interneKostenValuta"]  = $data[17]; //  IntCostCCy
      $fData["bankcode"]             = $data[19]; //  ProdCodeValue
      $fData["nettoBedrag"]          = $data[21]; //
      $fData["stukmutNetto"]         = $data[21] * $fData["wisselkoers"]; //
      $fData["divCoupBedrag"]        = $data[23]; //  CoupAmoun
      $fData["divCoupValuta"]        = $data[24]; //  CoupCCy
      $fData["aantal"]               = $data[28]; //  NOmQuantity
      $fData["koersValuta"]          = $data[29]; //  PriceCcy
      $fData["koers"]                = $data[30]; //  Price/koers
      $fData["belastingen"]          = $data[31]; //  Taxes
      $fData["belastingenValuta"]    = $data[32]; //  TaxesCcy
      $fData["omschrijving"]         = $data[36]; //  FreeText
      $fData["subfile"]              = $data[54];

      $fonds = raboCheckFonds($fData["bankcode"]);

      break;
    case "TRANSACTI":
      $recType = "kosten";
      $transId = $data[2];
      $fData["transactieId"]       = $transCode; //  RefId Cash/Eff
      $fData["transidCash"]        = $data[5]; //  TRansId Costrecord
      $fData["transidSec"]         = $data[2];
      $fData["rekening"]           = $data[6]; //  AccountNr
      $fData["rekeningtype"]       = $data[7]; //  AcctCubType
      $fData["transactiecode"]     = $data[9]; //  TransCode
      $fData["bedrag"]             = $data[10]; //  Bedrag
      $fData["valuta"]             = $data[11]; //  Valuta
      $fData["subfile"]            = $data[13];

      break;
    default:
        $transId = "";

  }

  if ($transId != "")
  {
    $dataSet[$transCode][$recType][] = $fData;
  }

}
$prb->hide();
fclose($handle);
//debug($dataSet);

$sectransIdArray = array();

//debug($skipFoutregels, "transactie ids met validatie fouten");

foreach ($dataSet as $key => $rec)
{
//  debug($rec,'dataSet');
  if (in_array($key , $skipFoutregels)) // call; 8208
  {
    continue; // als er een validatie mislukt is dan de hele transactie ID skippen
  }


  $globFonds = "";
  $globOmschrijving = "";
//  $chk = (in_array($key , $skipFoutregels))?" skip ":" -- ";
//  debug($rec, $key.$chk);
  $koppelArray = array();
  $fonds = array();
  if (count($rec["geld"]) > 0)
  {
    foreach ($rec["geld"] as $data)
    {
      foreach ($rec["kosten"] as $kostRec)
      {
        if ($data["transidCash"] == $kostRec["transidCash"])
        {
          $data["kostenRows"][$kostRec["transactiecode"]]["bedrag"] = $kostRec["bedrag"];
          $data["kostenRows"][$kostRec["transactiecode"]]["valuta"] = $kostRec["valuta"];
          $koppelArray[] = $kostRec["transidCash"];
//          debug($data["kostenRows"][$kostRec["transactiecode"]], $kostRec["transactiecode"]);
        }
      }
      $tc = $transactieCodes["C"][$data["transactiecode"]];

      if ($tc != "" )
      {
        $do_func = "do_".$tc;

        if ( function_exists($do_func) )
        {
          call_user_func($do_func);
        }
        else
        {
          $skipped .= "- regel functie $do_func bestaat niet <br>";
        }
      }
      else
      {
        $skipped .= "- regel {$data["row"]} onbekende transactiecode {$data["transactiecode"]}<br>";
      }
    }
    //$data = $rec["geld"][0];



  }

  if (count($rec["stukken"]) > 0)
  {
    foreach ($rec["stukken"] as $data)
    {

      foreach ($rec["kosten"] as $kostRec)
      {
        if ($data["transidSec"] == $kostRec["transidSec"] AND !in_array($kostRec["transidCash"], $koppelArray))
        {
          $data["kostenRows"][$kostRec["transactiecode"]]["bedrag"] = $kostRec["bedrag"];
          $data["kostenRows"][$kostRec["transactiecode"]]["valuta"] = $kostRec["valuta"];

        }
      }
      $sectransIdArray[] = $data["transidSec"];
      $tc = $transactieCodes["S"][$data["transactiecode"]];
//      debug($data,$tc." STUKKEN *****");
      if (count($rec["geld"]) > 0)
      {
        $tempGeld = array();
        foreach ($rec["geld"] as $gRec)
        {
          if ($gRec["transactiecode"] == "CW")  // CW records overslaan call 8208
          {
            continue;
          }
          $tempGeld[] = $gRec;
        }
        $rec["geld"] = $tempGeld;
      }

      if ($tc != "")
      {
        $data["cashTransactie"] = (count($rec["geld"])>0);
        $data["rekening"]       = $rec["geld"][0]["rekening"];
        $data["rekValuta"]      = $rec["geld"][0]["valuta"];
        $data["nettoBedrag"]    = $rec["geld"][0]["nettoBedrag"] + $rec["geld"][1]["nettoBedrag"] + $rec["geld"][2]["nettoBedrag"];
        $data["kosten"]         = $rec["kosten"];
        if ($tc == "DIV" AND $data["rekValuta"] != "EUR")
        {
          $data["wisselkoers"]  = $rec["geld"][0]["wisselkoers"];
        }



        $fonds = getRaboFonds($data);
        $do_func = "do_" . $tc;

        if (function_exists($do_func))
        {
          call_user_func($do_func);
        }
        else
        {
          $skipped .= "- regel functie $do_func bestaat niet <br>";
        }
      }
      else
      {
        $skipped .= "- regel {$data["row"]} onbekende transactiecode {$data["transactiecode"]}<br>";
      }
    }
  }

//  if (count($rec["kosten"]) > 0)
//  {
//
//    foreach($rec["kosten"] as $kostRow)
//    {
//      debug($rec["geld"],"");
////      if (in_array($kostRow["transidSec"],$sectransIdArray))
////      {
////        continue;
////      }
//      foreach ($rec["geld"] as $data)
//      {
//         if ($kostRow["transidCash"] == $data["transidCash"])
//      {
//           break;
//         }
//      }
//      foreach ($rec["stukken"] as $stukRec)
//      {
//        if ($kostRow["transidSec"] == $stukRec["transidSec"])
//        {
//          break;
//        }
//      }
//debug($data, "geldRec");
//debug($stukRec, "stukRec");
//      $data["stukRec"] = $stukRec;
//      $data["fonds"]        = $globFonds;
//      //$data["omschrijving"] = $globOmschrijving;
//      $data["bedrag"] = $kostRow["bedrag"];
//      $data["nettoBedrag"] = $kostRow["bedrag"];
//      $data["transactiecode"] = $kostRow["transactiecode"];
//      $tc = $transactieCodes["T"][$data["transactiecode"]];
//
//      debug($data,"KOSTEN: ".$tc);
//      if ($tc != "" )
//      {
//        $do_func = "do_".$tc;
//        if ( function_exists($do_func) )
//        {
//          call_user_func($do_func);
//        }
//        else
//        {
//          $skipped .= "- regel functie $do_func bestaat niet <br>";
//        }
//      }
//      else
//      {
//        $skipped .= "- regel {$data["row"]} onbekende transactiecode {$data["transactiecode"]}<br>";
//      }
//    }
//
//  }


//
//  $pro_step += $pro_multiplier;
//  $prb->moveStep($pro_step);
//  if (in_array($row , $skipFoutregels))
//  {
//    $skipped .= "- regel $row overgeslagen<br>";
//    continue; // rest overslaan, lees nieuwe regel
//  }
//
//
//  if($recType == "stukken") //Transacties
//  {
//    $portefeuille = $data[6];
//
//    if ($data[19])
//    {
//      $fonds = getRaboFonds($data);
//    }
//
//    $transactieCode = $data[8];
//
//    $val = $transactieCodes[$transactieCode];
//
//  }
//  else
//  {
//    $val = 'Mutatie';
//  }
//
//  $do_func = "do_$val";
//
//  if ( function_exists($do_func) )
//    call_user_func($do_func);
//  else
//    $skipped .= "- transaktie ".$data[6]." ".$data[8]." overgeslagen<br>";
//
//  // echo $skipped;
//  //exit;
}
//debug($skipped);

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

if ($_GET["rabo1"] <> "1")
{
  if (count($meldArray) > 0) listarray($meldArray);
?>
  Records in Rabo STRA bestand :<?=$row?><br>
  Aangemaakte mutatieregels : <?=count($output)?><BR>
<?
  //////////////////////////////////

  $file = $file2;
  $meldArray = array();
  //$output = array();

  echo "<hr/>Verwerken van $file <br/>";

  $row = 0;
  $handle = fopen($file, "r");
  $pro_multiplier = (100/$csvRegels);
  $_tfile = explode("/",$file);
  $_file = $_tfile[count($_tfile)-1];
  $skipped = "";


  while ($data = fgetcsv($handle, 4096, "|"))
  {
    array_unshift($data,"leeg");
    $row++;
    $recType = (stristr($data[1],"SECURITYTRANS"))?"stukken":"geld";

   // $data = cleanRow($data);
//debug($data,"2");
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    if (in_array($row , $skipFoutregels))
    {
      $skipped .= "- regel $row overgeslagen<br>";
      continue; // rest overslaan, lees nieuwe regel
    }


    if($recType == "stukken") //Transacties
    {
      $portefeuille = $data[6];

      if ($data[19])
      {
        $fonds = getRaboFonds($data);
      }

      $transactieCode = $data[8];

      $val = $transactieCodes[$transactieCode];

    }
    else
    {
      $val = 'Mutatie';
    }

    $do_func = "do_$val";

    if ( function_exists($do_func) )
      call_user_func($do_func);
    else
      $skipped .= "- transaktie ".$data[6]." ".$data[8]." overgeslagen<br>";

   // echo $skipped;
   //exit;

  }
  fclose($handle);
}
//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  if ($ndx == 1)
  {
     if (checkForDoubleImport($output[$ndx]))
     {
       $prb->hide();
       Echo "<hr> <h1>FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie</h1>";
	     exit();
     }
   }
   $pro_step += $pro_multiplier;
   $prb->moveStep($pro_step);

	$_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	$sep = " ";
	while (list($key, $value) = each($output[$ndx]))
	{
	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'
";
   $sep = ",";
	}
  $_query .= ", add_date = NOW()";
  $_query .= ", add_user = '".$USR."'";
	$_query .= ", change_date = NOW()";
  $_query .= ", change_user = '".$USR."'";
  $DB->SQL($_query);
	if (!$DB->Query())
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}

$prb->hide();

if (count($meldArray) > 0) listarray($meldArray);

?>

  Records in Rabo bestand :<?=$row?><br>
Aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<b>Klaar met inlezen <br></b>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>