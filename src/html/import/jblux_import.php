  <?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/30 06:12:55 $
 		File Versie					: $Revision: 1.7 $

 		$Log: jblux_import.php,v $
 		Revision 1.7  2020/06/30 06:12:55  cvs
 		call 7829
 		
 		Revision 1.6  2020/04/10 13:07:52  cvs
 		call 8554
 		
 		Revision 1.5  2020/04/06 09:10:51  cvs
 		call 7829
 		
 		Revision 1.4  2020/03/27 09:18:00  cvs
 		call 7829
 		
 		Revision 1.3  2020/03/09 13:29:39  cvs
 		call 8413
 		
 		Revision 1.2  2020/02/24 15:27:42  cvs
 		call 7829
 		
 		Revision 1.1  2019/08/23 12:28:56  cvs
 		call 7829

*/



include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("jblux_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("JBLUX");

$skipFoutregels = array();
$meldArray = array();

if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"JBLUX");
  $rac->addRekeningen($_POST);
  $doIt = 0;
  $file = $bestand;
}


if ($doIt == "1")  // validatie mislukt, wat te doen?
{
  switch ($action)
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



//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$DB = new DB();
$rekeningAddArray = array();


$content = array();
$content["style"] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>
  ';

echo template("../".$__appvar["templateContentHeader"],$content);
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  $tempRecords = $DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR'");
  if ($tempRecords > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$tempRecords.")<br>
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
include("jblux_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van JBLux CSV bestand');

if ($doIt <> "1")  
{
  
	if (!validateCvsFile($file))
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
	<form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
    <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
    <br/>
<?

    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"JBLUX");
      $rac->getStyles();

      $rekArray = $_SESSION["rekeningAddArray"];
      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
      {
        $rac->makeInputRow($rekArray[$rNdx]);
      }
      echo $rac->getHTML();
    }
?>
    <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<button id="btnSubmit"> Uitvoeren </button>
	</form>
  
  <script>
<?
    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {
      echo $rac->getJS();
    }
?>
  </script>
<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');

 $query = "SELECT bankCode,doActie FROM jbluxTransactieCodes  ";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["bankCode"]] = $row["doActie"];
 }
debug($transactieMapping, "transactie mapping");
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;
  if ($row == 1)
  {
    continue; // skip header
  }


  // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
  $data["row"] = $row;

  $data["rekening"]             = $data[43];
  $data["portefeuille"]         = $data[4];
  $data["afrekenValuta"]        = $data[42];
  $data["omschrijving"]         = $data[45];
  $data["boekdatum"]            = jbluxDate($data[2]);
  $data["settledatum"]          = jbluxDate($data[3]);
  $data["isin"]                 = $data[11];
  $data["bankCode"]             = $data[8];
  $data["fondsValuta"]          = ($data[31] != "")?$data[31]:$data[9];
  $data["aantal"]               = $data[28];
  $data["nettoBedrag"]          = $data[40];
  $data["nettoBedrag2"]         = $data[39];
  $data["valutakoersRekFonds"]  = $data[41];
  $data["valutakoersFondsEur"]  = ($data[46] != 0)?1/$data[46]:1;
  $data["transactieId"]         = $data[48];
  $data["transactieCode"]       = $data[22];
  $data["sysNettobedrag"]       = $data[53];
  $data["koers"]                = $data[30];
  $data["opgelopenRente"]       = $data[32];
  $data["provisie"]             = $data[33];
  $data["brokerKosten"]         = $data[34];
  $data["taxes"]                = $data[35];
  $data["OverigeKosten"]        = $data[36];
  $data["FTT"]                  = $data[37];
  $data["storno"]               = $data[26];
  $data["gestorneerdId"]        = $data[27];
  $data["geldRekening"]         = ($data[6] == 4);
  if (  !$data["geldRekening"] AND !strstr($data["transactieCode"], "_FORWARD"))
  {
    JBlux_getfonds($data["bankCode"],$data["isin"],$data["fondsValuta"]);
  }

  if ($data["storno"] == "R")
  {
    $skipped .= "- regel $row <span style='color: red; font-weight: bold'>overgeslagen storno</span> <br>";
    continue; // rest overslaan, lees nieuwe regel
  }


  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);
  if (in_array($row, $skipFoutregels))
  {
    $skipped .= "- regel $row overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }

  $val = $transactieMapping[$data["transactieCode"]];


  $do_func = "do_$val";
//    $data = $v[$transcode];

  if ($do_func == "do_NVT")
  {
     do_NVT($data["transactieCode"]);
  }
  else
  {
    if ( function_exists($do_func) )
    {
      call_user_func($do_func);
    }
    else
    {
      $meldArray[] = $row.": transaktieccode ({$data["transactieCode"]}) niet gevonden";
    }
  }





}
$prb->hide();

// DBEW --> effectent
// KBEW --> cash


fclose($handle);

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
    if (checkForDoubleImport($output[$ndx]) AND !$__develop )
    {
      $prb->hide();
      Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
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
?>


<b>Klaar met inlezen <br></b>
<?
listarray($meldArray);
?>

Records in JBLUX CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>