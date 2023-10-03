<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/07 07:57:03 $
 		File Versie					: $Revision: 1.2 $

 		$Log: airsTempl_import.php,v $
 		Revision 1.2  2020/07/07 07:57:03  cvs
 		call 8728
 		
 		Revision 1.1  2020/06/29 13:55:59  cvs
 		call 8728
 		

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("airsTempl_functies.php");

$_transactiecodes = array(
  "Aankoop",
  "Bankkosten",
  "Beheerloon",
  "Bewaarloon",
  "Deponering",
  "Dividend",
  "Kruispost",
  "Lichting",
  "Onttrekking",
  "Storting",
  "Verkoop",
  "Coupon",
  "Rente"
);




$skipFoutregels = array();

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

$content = array();
$content["style"] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
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
include("airsTempl_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Airs template CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
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
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');


$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 1000, ";"))
{
	$row++;

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);

 	if ($data[0] == "Rekening" OR trim($data[0]) == "")
  {
    continue;
  }
  
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}
  
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);


  $data["rekening"]               = $data[1];
  $data["transactiecode"]         = $data[2];
  $data["isin"]                   = $data[3];
  $data["valuta"]                 = $data[4];
  $data["fonds"]                  = $data[5];
  $data["datum"]                  = $data[6];
  $data["aantal"]                 = cnvNumber($data[7]);
  $data["koers"]                  = cnvNumber($data[8]);
  $data["brutoFV"]                = cnvNumber($data[9]);
  $data["kostenFV"]               = cnvNumber($data[10]);
  $data["belastingFV"]            = cnvNumber($data[11]);
  $data["opgelopenRenteFV"]       = cnvNumber($data[12]);
  $data["kostenRekVal"]           = cnvNumber($data[13]);
  $data["belastingRekVal"]        = cnvNumber($data[14]);
  $data["opgelopenRenteRekVal"]   = cnvNumber($data[15]);
  $data["wisselkoers"]            = cnvNumber($data[16]);
  $data["nettoRekVal"]            = cnvNumber($data[17]);
  $data["omschrijving"]           = $data[18];

  $do_func = "do_".$data["transactiecode"];

  if ( function_exists($do_func) )
  {
    call_user_func($do_func);
  }
  else
  {
    do_error();
  }


}
fclose($handle);
unlink($file);

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

Records in Mabeltrans CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>