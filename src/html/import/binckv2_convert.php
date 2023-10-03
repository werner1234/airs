<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/07/16 09:52:45 $
 		File Versie					: $Revision: 1.1 $

 		$Log: binckv2_convert.php,v $
 		Revision 1.1  2011/07/16 09:52:45  cvs
 		*** empty log message ***
 		

*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("binckv2_functies_convert.php");

function makeNr($value)
{
  return str_replace(",",".",$value);
}

$skipFoutregels = array();


if ($doIt == "1")  // validatie mislukt, wat te doen?
{
	if ($action == "stop")
	{
		 // file wissen
		 echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
		 if (file_exists($bestand) ) unlink($bestand);
		 exit();
	}
	else
	{
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
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);

if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
{
	echo "<br>
<br>
De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties").")<br>
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


$csvRegels = 1;
include("binckv2_validate_convert.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Binck V2 CSV bestand');

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
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');



$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$data = fgetcsv($handle, 1000, ";");  //eerste regel overslaan veldnamen
$row = 1;
while ($data = fgetcsv($handle, 1000, ";"))
{
	$row++;

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();

/*
** 15 aug 2007 cvs
** hieronder aanpassingen aan de array om de nieuwe structuur van de cvs file aan te passen naar de oude standaard
*/
  $t7 = $data[7];
  $data[7]  = $data[6];
  $data[6]  = $t7;
  $data[4]  = str_replace("/","",$data[4]);
  $data[16] = str_replace("/","",$data[16]);
  $data[8] = makeNr($data[8]);
  $data[10] = makeNr($data[10]);
  $data[11] = makeNr($data[11]);
  $data[12] = makeNr($data[12]);
  $data[13] = makeNr($data[13]);
  $data[14] = makeNr($data[14]);
  $data[15] = makeNr($data[15]);
  $data[16] = makeNr($data[16]);
  $data[33] = makeNr($data[33]);




   if ($data[19] <> "")
   {
   	 $_isin = trim($data[19]);
     $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' ";

     $DB->SQL($query);
     $fonds = $DB->lookupRecord();
   }
   else
   {
     $_binckCode = trim($data[32]);
     $query = "SELECT * FROM Fondsen WHERE binckCode = '".$_binckCode."' ";

     $DB->SQL($query);
     $fonds = $DB->lookupRecord();
   }
   $num = count($data);
   $val = str_replace(" ","_",$data[6]); // vervang spatie door underscore
   $val = str_replace("-","_",$data[6]); // vervang - door underscore

   $do_func = "do_$val";
   if ( function_exists($do_func) )
     call_user_func($do_func);
   else
     $skipped .= "- transaktie $data[6] overgeslagen<br>";
//listarray($data);

}
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
     if (checkForDoubleImport($output[$ndx]))
     {
       $prb->hide();
       Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
       listarray($output[$ndx]);
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
Records in Binck CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>