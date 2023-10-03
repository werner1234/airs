<?
/*
    AE-ICT sourcemodule created 11 mei 2022
    Author              : Chris van Santen
    Filename            : abnbe_import.php


*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("abnbe_functies.php");
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("AABBE");

$file = $_GET["file"];
$type = ($_GET["type"] == "s")?"s":"c";


$skipFoutregels = array();
$fileParts = Explode(".",$file);
$file2 =  $fileParts[0]."_2.".$fileParts[1];
//$file2 = $_GET["file2"];  
$skipFoutregels = array();
$error = array();

if ($doIt == "1")  // validatie mislukt, wat te doen?
{
  
	if ($action == "stop")
	{
		 // file wissen
		 echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
     if (file_exists($file) ) unlink($file);
		 if (file_exists($file2) ) unlink($file2);
		 exit();
	}
	else
	{

		 $skipFoutregels = explode(",",$foutregels);
		 //array_shift($skipFoutregels);  // verwijder eerste lege key
	}
}

if ( $type == "s" and (!file_exists($file) OR !file_exists($file2) ) )
{
  echo "<br> U moet 2 bestanden opgeven, import afgebroken!";
  exit();
}
//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$db = new DB();
$DB = new DB();

$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);

if ($db->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
{
	echo "<br>
<br>
De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$db->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ").")<br>
<br>
de import is geannuleerd ";
	exit;
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



include("abnbe_validate.php");
$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van AAB BE CSV bestand');

if ($type == "s")
{
  $fileArray = file($file2, FILE_SKIP_EMPTY_LINES);
  
  for ($xx = 0; $xx < count($fileArray); $xx++)
  {
    $rawData = $fileArray[$xx];
    $checkArray[textPart($fileArray[$xx],21,70)] = textPart($fileArray[$xx],71,105) ;
    $data = array();
    $data[39] = textPart($rawData,21,70);
    $data[40] = textPart($rawData,71,105);
    $data[41] = textPart($rawData,176,179);
    $data[42] = maakBedrag(textPart($rawData,222,239));
    $data[43] = textPart($rawData,240,242);
    $data[44] = maakBedrag(textPart($rawData,243,260));
    $data[45] = textPart($rawData,261,263);
    $data[46] = textPart($rawData,184,200);  // aangepast jan 2011 op verzoek tnt
    $data[47] = textPart($rawData,343,384);
    $data[48] = textPart($rawData,292,299);
    $data[49] = textPart($rawData,300,308);

    $data[55] = textPart($rawData,106,109);
    $cashRec[textPart($fileArray[$xx],21,70)] = $data;
  }
//  debug($cashRec);

  foreach($checkArray as $rekeningNr)
  {
    checkRekening($rekeningNr);
  }
  
}


//if ($doIt <> "1" OR count($error) > 0)  // validatie is al gebeurd dus skippen
if ($doIt <> "1" )  // validatie is al gebeurd dus skippen
{

//  if ( $type == "s")
//  {
//    validateCvsFile($file2);
//  }

	if (!validateCvsFile($file) OR count($error) > 0)
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
		$foutregels[] = $_sp;
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

  $foutregels = implode(",",$foutregels);
?>
	</table>

	<br>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="file" value="<?=$file?>">
    <input type="hidden" name="file2" value="<?=$file2?>">
    <input type="hidden" name="type" value="<?=$_GET["type"]?>" />
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ');

$meldArray = array();
$skipped = "";


$db->executeQuery("SELECT * FROM AABBETransactieCodes ORDER BY bankCode");

$transactieCodes  = array();
$_transactieArray = array();

while ($codeRec = $db->nextRecord())
{
  $transactieCodes[$codeRec["bankCode"]] = $codeRec["doActie"];
  $_transactieArray[] = $codeRec["bankCode"];
}

debug($transactieCodes);
//debug($_transactieArray);

verwerkFIle($file);

if ($type == "s")
{
  $type = "c";
  $skipFoutregels = array();
  verwerkFIle($file2);
}




function verwerkFIle($fileName)
{
  global $db, $row, $pro_multiplier, $prb, $skipFoutregels,
         $skipped, $meldArray, $data, $error, $checkArray, $cashRec,
         $transactieCodes, $_transactieArray, $fonds, $type;

  $transactieCodesSkipped = array();
  $row    = 0;
  $handle = fopen($fileName, "r");
  $pro_multiplier = (1);
//  $_tfile = explode("/",$file);
//  $_file = $_tfile[count($_tfile)-1];

  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);

    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);


    $row++;
//    debug($data, $row.$type);
    // $data = cleanRow($data);

//    $pro_step += $pro_multiplier;
//    $prb->moveStep($pro_step);

    if (in_array($row , $skipFoutregels))
    {
      $skipped .= "- regel $row overgeslagen (FR)<br>";
//      debug("Skipped", $row);
      continue; // rest overslaan, lees nieuwe regel
    }


    if($data[1] == "SECURITYTRANS") //Transacties
    {

      $data[35] =  $checkArray[$data[3]];  // rekeningnr via lookup uit cashfile;

      for ($xyz=39; $xyz<49; $xyz++)
      {
        $data[$xyz] = $cashRec[$data[3]][$xyz];
      }
//debug($data, $row);
      // call 4964 start
      $dep = ($transactieCodes[$data[8]] == "D" OR
              $transactieCodes[$data[8]] == "L" OR
              $transactieCodes[$data[8]] == "DV" OR
              $transactieCodes[$data[8]] == "STUKMUT");   //alleen deponeringen en lichtingen
      if ($data[35] == "" AND $dep)
      {
        $data[35] = $data[6]."MEM";
      }
      // call 4964 stop

      if (!checkRekening($data[35]))
      {
        $skipped .= "- regel $row overgeslagen onbekende rekening ({$data[35]})<br>";
        continue; // rest overslaan, lees nieuwe regel
      }

      $portefeuille = $data[6];

      if ($data[19])
      {
        $bankcode = $data[19];
        $query = "SELECT * FROM Fondsen WHERE aabbeCode = '{$bankcode}' ";

        if (!$fonds = $db->lookupRecordByQuery($query))
        {
          echo "<li> fonds met aabbeCode = {$bankcode} niet gevonden";
        }

      }

      $transactieCode = $data[8];

      $val = $transactieCodes[$transactieCode];
      $commissieParts = explode(" ",$data[34]);
      if ($commissieParts[0] <> 0 AND is_numeric($commissieParts[0]))
      {
        $data[34] = $commissieParts[0];
        $data[15] = $data[15] - $data[34];  // commissie aftrekken van KOBU
        $data[17] = $data[17] + $data[34];  // commissie optellen bij KOST
        $data[18] = trim($commissieParts[1]);  // Valuta tbv KOST
      }

    }
    else
    {
      $val = 'Mutatie';

      if ($data[55] == "0002") // call 10426 prefix L- bij leningen
      {
        $data[40] = "L-".$data[40];
      }
      if (in_array($data[41],$_transactieArray)) continue; // als geldige transactiecode dan is die al verwerkt en daarom overslaan
      if (is_numeric($data[40])) $data[40] .= $data[45];


      // call 10426 via C_ transactie codes mappen
      // als niet gevonden melden en do_mutatie()
      $tc = "C_".$data[41];
      if ($transactieCodes[$tc] != "")
      {
        $val = $transactieCodes[$tc];
      }
      else
      {
        $meldArray[] = "- regel {$row} cash transactieCode <b>".$data[41]."</b> onbekend via do_Mutatie geboekt<br>";
      }
    }
    if (!checkstorno())
    {
      $do_func = "do_$val";
      if ( function_exists($do_func) )
        call_user_func($do_func);
      else
      {
        if (!in_array($data[8],$transactieCodesSkipped))
        {
          $skipped .= "- regel {$row} transactieCode <b>".$data[8]."</b> overgeslagen<br>";
          $transactieCodesSkipped[] = $data[8];
        }
      }
    }

    // echo $skipped;
    //exit;

  }
//echo $skipped;
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
  if ($ndx == 0)
  {
     if (checkForDoubleImport($output[$ndx]))
     {
       $prb->hide();
       Echo "<br><h1 style='color: red'> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie</h1>";
	     //exit();
     }
   }
   $pro_step += $pro_multiplier;
   $prb->moveStep($pro_step);

	$_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	$sep = " ";
//	while (list($key, $value) = each($output[$ndx]))

  foreach ($output[$ndx] as $key=>$value)
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

	if (!$db->executeQuery($_query))
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}

$prb->hide();



?>
<hr/>meldarray<br/>
<?
  //if (count($meldArray)>0)
  {
    listarray($meldArray);
  }

?>


<b>Klaar met inlezen <br></b>
Records in AAB BE CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>