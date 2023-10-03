<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.12 $

 		$Log: snssec_import.php,v $
 		Revision 1.12  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.11  2016/09/21 08:30:05  cvs
 		call 5200
 		
 		Revision 1.10  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2015/06/11 16:18:16  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2013/01/02 15:43:18  cvs
 		do_DV en do_R
 		
 		Revision 1.6  2012/06/05 12:50:14  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2012/05/15 15:02:19  cvs
 		controlebedrag
 		
 		Revision 1.4  2011/03/04 07:15:18  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2010/11/30 12:59:15  cvs
 		*** empty log message ***

 		Revision 1.2  2010/07/13 09:20:56  cvs
 		*** empty log message ***

 		Revision 1.1  2010/06/09 15:20:23  cvs
 		*** empty log message ***





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
include_once("snssec_functies.php");
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("NIBC");



$transactieCodes = array("s0dl" => "D",
                         "s0ds" => "LO",
                         "s0wl" => "L",
                         "s0ws" => "DS",
                         "s1bu" => "A",
                         "s1se" => "V",
                         "s1ob" => "A_O",
                         "s1os" => "V_O",
                         "s1cs" => "V_S",
                         "s1cb" => "A_S",
                         "s1cd" => "DV",  // XX erachter omdat functie nog niet werkt
                         "s1cp" => "R",   // XX erachter omdat functie nog niet werkt
                         "s1em" => "A",
                         "s1rd" => "V",
                         "s1ri" => "A",
                         "s1bs" => "A",
                         "s1ss" => "V",
                         "s1co" => "L",
                         "s1po" => "L",
                         "s1cn" => "D",
                         "s1pc" => "D",
                         "s1ps" => "D",
                         "s1ap" => "A_S",
                         "s1ac" => "A_S",
                         "s1ep" => "V_S",
                         "s1ec" => "V_S",
                         "s1ab" => "A",
                         "s1as" => "V",
                         "s1es" => "V",
                         "s1eb" => "A"
                           );


$skipFoutregels = array();


if ($doIt == "1")  // validatie mislukt, wat te doen?
{
	
  if ($doIt == "1")  // validatie mislukt, wat te doen?
  {
    switch ($action)
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
$DB = new DB();

$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
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
include("snssec_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van SNS CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
  
  $error = array();
  if ($_GET["sns1"] == "1")
  {
    validateCvsFile($file, "single");
  }
  else
  {
    validateCvsFile($file, "STRA");
    validateCvsFile($file2, "CTRA");
  }  
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
  	<input type="hidden" name="bestand2" value="<?=$file2?>">
  	<input type="hidden" name="sns1" value="<?=$sns1?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
<?
  if ($_GET["sns1"] == "1")
  {
?>    
  
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
<?
  }
?>
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

echo "<hr/>Verwerken van $file <br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";


while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;
    $data = convertFixedLine($dataRaw);

	$row++;

 // $data = cleanRow($data);

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}


  if($data[1] == "SECURITYTRANS") //Transacties
  {
    $portefeuille = $data[6];

    if ($data[19])
    {
      $_snscode = $data[19];
      $query = "SELECT * FROM Fondsen WHERE snsSecCode = '".$_snscode."' ";
      $DB->SQL($query);
      $fonds = $DB->lookupRecord();
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

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

if ($_GET["sns1"] <> "1")
{
  


  if (count($meldArray) > 0) listarray($meldArray);
  ?>
  Records in SNS/NIBC STRA bestand :<?=$row?><br>
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


  while (!feof($handle))
    {
      $dataRaw = fgets($handle, 4096);
      if (trim($dataRaw) == "") continue;
      $data = convertFixedLine($dataRaw);

    $row++;

   // $data = cleanRow($data);

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    if (in_array($row , $skipFoutregels))
    {
      $skipped .= "- regel $row overgeslagen<br>";
      continue; // rest overslaan, lees nieuwe regel
    }


    if($data[1] == "SECURITYTRANS") //Transacties
    {
      $portefeuille = $data[6];

      if ($data[19])
      {
        $_snscode = $data[19];
        $query = "SELECT * FROM Fondsen WHERE snsSecCode = '".$_snscode."' ";
        $DB->SQL($query);
        $fonds = $DB->lookupRecord();
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

  Records in SNS/NIBC bestand :<?=$row?><br>
Aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<b>Klaar met inlezen <br></b>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>