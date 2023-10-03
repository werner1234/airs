<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/21 09:58:35 $
 		File Versie					: $Revision: 1.10 $

 		$Log: ubslux_import.php,v $
 		Revision 1.10  2020/07/21 09:58:35  cvs
 		call 7606
 		
naar RVV 20201113
 		
*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("ubslux_functies.php");
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("UBSL");
$DB = new DB();
$DB->executeQuery("SELECT * FROM ubsluxTransactieCodes ORDER BY bankCode");

$transactieCodes  = array();
$_transactieArray = array();
$ftRowsSkipped    = array();
$meldArray        = array();
$content          = array();

while ($codeRec = $DB->nextRecord())
{
  $transactieCodes[strtolower($codeRec["bankCode"])] = $codeRec["doActie"];
}

// let op datecheck kolomen vanaf 1 (ipv 0)
// extra check columns - 1 (soms wordt laatste ; weggelaten)
$fileTypeArray = array(
  "loan"        => array("dates" => array(5,6,7)    ,"columns"=>29 ),                    //
  "corpact"     => array("dates" => array(5,6)      ,"columns"=>25 ),                    // vpca + leeg veld
  "mmPos"       => array("dates" => array(5,6,7)    ,"columns"=>18, "skipped"=>true ),    // vpch + leeg veld
  "fxtrans"     => array("dates" => array(6,7,8)    ,"columns"=>21 ),                     // vpct+ leeg veld
  "onbekend"    => array("dates" => array(7,8,9)    ,"columns"=>30 ),                    //
  "cashmov"     => array("dates" => array(9,10)     ,"columns"=>29 ),                    // vpcm + leeg veld
  "cashPos"     => array("dates" => array(2)        ,"columns"=>29, "skipped"=>true ),    // vpcm? + leeg veld
  "sectrans"    => array("dates" => array(13,15)    ,"columns"=>38 ),                    // wps + leeg veld
  "sectrans2"    => array("dates" => array(13,15)    ,"columns"=>40 ),                    // wps + leeg veld  uitzondering voor WMP
  "sectrans3"    => array("dates" => array(13,15)    ,"columns"=>43 ),                    // wps + leeg veld  nieuw 2022?
  "secPos"      => array("dates" => array(20,21)    ,"columns"=>21, "skipped"=>true ),    // vpsh
);

$headerTitles = array(
  "corpact"     => 'event type',
  "cashmov/pos" => 'acc. no.',
  "secpos"      => 'Acc. Nr.',
  "sectrans"    => 'Product Type',
);

//debug($_REQUEST);
$rowInput = $_REQUEST;

$skipFoutregels = array();

$doIt = $rowInput["doIt"];

if ($doIt == "1")  // validatie mislukt, wat te doen?
{
	
  if ($doIt == "1")  // validatie mislukt, wat te doen?
  {
    $bestand = $rowInput["bestand"];
    switch ($rowInput["action"])
    {
      case "stop":
        echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
      	if (file_exists($bestand) ) unlink($bestand);
  		  exit();
        break;
      case "retry":
        $doIt = 0;
        $file = $bestand;
        break;
      default: 
        $skipFoutregels = explode(",",$foutregels);
  		  array_shift($skipFoutregels);  // verwijder eerste lege key
  		  $file = $bestand; 

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

//$ubsLuxDebug = true;
$csvRegels = 1;
include("ubslux_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van UBS LUX CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
  
  $error = array();
  validateCvsFile($file);

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
	<form action="<?=$PHP_SELF?>">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
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

//debug($transactieCodes);
echo "<hr/>Verwerken van $file <br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";

///////////////
$ubsLuxDebug = false;




//debug($transactieCodes);
//debug($_transactieArray);
$transactieCodesSkipped = array();
$kol = 0;
$exe = 0;
$output = array();
$koppeltjes = array();
while ($rowInput = fgetcsv($handle, 4096, ";"))
{
  $fileType = "";
  $rowInput = trimFields($rowInput);
  $row++;
  // headers overslaan

  if ($rowInput[0] == 'acc. no.' OR
    $rowInput[0] == 'Product Type' OR
    $rowInput[0] == 'event type' OR
    $rowInput[0] == 'Acc. Nr.' OR
    $rowInput[0] == 'Account Nr.')
  {
    $ft= "(onbekend)";
    foreach($headerTitles as $k=>$v)
    {
      if ($rowInput[0] == $v)
      {
        $ft = "($k)";
      }
    }
    $meldArray[] = "{$row}: headerregel {$ft}";
    continue;
  }

  array_unshift($rowInput, "leeg");  /// datavelden vanaf index 1

  if (!$fileType = checkRowType($rowInput, $row))
  {
    //$meldArray[] = "{$row}: checkRowType() voldoet niet";
    continue;
  }

  //debug($rowInput, "filetype= ".$fileType."  ".$row);
  $fonds = array();
  $data = array("row" => $row);
  $skipRest = false;
  switch ($fileType)
  {
    case "cashmov":

      if ($rowInput[16] == "Y") // pending boekingen overslaan
      {
        $ftRowsSkipped[] = $row . " cashmov: pending boeking, overgeslagen ";
        $skipRest = true;
        break;
      }

      if (
        substr(strtolower($rowInput[8]), 0, 4) == "sale" or
        substr(strtolower($rowInput[8]), 0, 8) == "purchase"
      )
      {
        $ftRowsSkipped[] = $row . " cashmov: regel in andere file, overgeslagen ";
        $skipRest = true;
      }



      $data["rekening"]       = (int)$rowInput[1];
      $data["rekValuta"]      = $rowInput[6];
      $data["omschrijving"]   = $rowInput[8];
      $data["omschrijving2"]  = $rowInput[25];
      $data["transactieId"]   = $rowInput[7];
      $data["isin"]           = $rowInput[13];
      $data["boekdatum"]      = ubsluxDatum($rowInput[10]);
      $data["valutadatum"]    = ubsluxDatum($rowInput[9]);
      $data["wisselkoers"]    = ubsluxNumber($rowInput[19]);
      $data["afrekenBedrag"]  = ubsluxNumber($rowInput[5]);
      $data["tax"]            = ubsluxNumber($rowInput[17]);
      $data["transactiecode"] = "geldmut";
      $data["row"]            = $row;
      $data["koppelId"]       = $rowInput[21];

      if (strtolower($data["omschrijving"]) == "forex trade spot")
      {
        $koppeltjes[$data["koppelId"]][] = $data;
        $skipRest = true;
      }

      if (strtolower($rowInput[8]) == "distribution")
      {
        $data["transactiecode"] = "distribution";
      }

      break;
    case "sectrans3":
    case "sectrans2":
    case "sectrans":

      $data["rekening"]       = (int)$rowInput[2];
      $data["rekValuta"]      = $rowInput[17];
      $data["transactieId"]   = $rowInput[3];
      $data["isin"]           = $rowInput[9];
      $data["fondsValuta"]    = $rowInput[18];
      $data["boekdatum"]      = ubsluxDatum($rowInput[13]);
      $data["valutadatum"]    = ubsluxDatum($rowInput[15]);
      $data["wisselkoers"]    = 1/ubsluxNumber($rowInput[20]);
      $data["opgelopenRente"] = ubsluxNumber($rowInput[25]);
      $data["afrekenBedrag"]  = ubsluxNumber($rowInput[34]);
      $data["koers"]          = ubsluxNumber($rowInput[19]);
      $data["aantal"]         = ubsluxNumber($rowInput[22]);
      $data["ownFees"]        = ubsluxNumber($rowInput[26]);
      $data["brokerFees"]     = ubsluxNumber($rowInput[27]);
      $data["otherFees"]      = ubsluxNumber($rowInput[28]);
      $data["deliveryFees"]   = ubsluxNumber($rowInput[29]);
      $data["handlingFees"]   = ubsluxNumber($rowInput[30]);
      $data["tax"]            = ubsluxNumber($rowInput[31]);
      $data["exchangeFees"]   = ubsluxNumber($rowInput[32]);
      $data["transactiecode"] = strtolower($rowInput[6]);
      $fonds = getFonds();
      break;
    case "fxtrans":
      $data["rekening"] = (int)$rowInput[1];
      $data["omschrijving"] = $rowInput[4];
      $data["transactieId"] = $rowInput[2];
      $data["boekdatum"] = ubsluxDatum($rowInput[6]);
      $data["valutadatum"] = ubsluxDatum($rowInput[8]);
      $data["wisselkoers"] = 1 / ubsluxNumber($rowInput[16]);
      $data["afrekenBedrag"] = ubsluxNumber($rowInput[5]);

      if ($rowInput[11] == "EUR")
      {
        $data["valutaEUR"]    = $rowInput[11];
        $data["bedragEUR"]    = ubsluxNumber($rowInput[10]);
        $data["rekValutaEUR"] = $rowInput[11];

        $data["valutaVV"]     = $rowInput[13];
        $data["bedragVV"]     = ubsluxNumber($rowInput[12]);
        $data["rekValutaVV"]  = $rowInput[13];
      }
      else
      {
        $data["valutaEUR"]    = $rowInput[13];
        $data["bedragEUR"]    = ubsluxNumber($rowInput[12]);
        $data["rekValutaEUR"] = $rowInput[13];

        $data["valutaVV"]     = $rowInput[11];
        $data["bedragVV"]     = ubsluxNumber($rowInput[10]);
        $data["rekValutaVV"]  = $rowInput[11];
      }
      if ($data["valutaEUR"]  == $data["valutaVV"])
      {
        $meldArray[] = "$row: FX boeking overgeslagen: 2x zelfde valuta";
        $skipRest =true;;
      }

      if ($data["valutaEUR"] != "EUR" AND $data["valutaVV"] != "EUR")
      {
        $meldArray[] = "$row: FX boeking overgeslagen: geen EUR rekening";
        $skipRest =true;;
      }

      $data["transactiecode"] = "fx";
      break;
    case "corpact":  //VPCA
      $data["transactiecode"] = $rowInput[10];

      break;
    default:
      $data["transactiecode"] = $fileType;
      $meldArray[] = "{$row}: {$fileType} niet ingeregeld ({$data["transactiecode"]})";
      $skipRest =true;

  }

  if ($skipRest)
  {
    continue;
  }



  $tc = $transactieCodes[$data["transactiecode"]];
//  debug($data, "tc=".$tc);
  if ($tc != "")
  {
    $do_func = "do_" . $tc;

    if (function_exists($do_func))
    {
      call_user_func($do_func);
    }
    else
    {
      $skipped .= "- regel {$data["row"]} functie $do_func bestaat niet <br>";
    }
  }
  else
  {
    $skipped .= "- <span style='color:red; font-weight: bold'> regel {$data["row"]} onbekende transactiecode ({$fileType}/{$data["transactiecode"]})</span><br>";
  }

//  //debug($rowInput,$fileType);
//  continue;
//  if ($row == 1)  // skip header regel
//  {
//    continue;
//  }
//
//  if ($rowInput[0] == null)  // skip lege regels
//  {
//    continue;
//  }
//
////debug($rowInput);


}

foreach ($koppeltjes as $fxBoeking)
{

  do_FX($fxBoeking);
}




$prb->hide();
fclose($handle);
//debug($rowInputSet);

$sectransIdArray = array();


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

debug($ftRowsSkipped, "overgeslagen regels");
?>

  Records in UBS lux bestand :<?=$row?><br>
Aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<b>Klaar met inlezen <br></b>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>