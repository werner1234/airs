<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.9 $

 		$Log: stroeveVT_import.php,v $
 		Revision 1.9  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.7  2008/06/05 07:04:32  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/02 12:33:12  cvs
 		Datum regels overgeslagen onderdrukken

 		Revision 1.5  2008/06/02 12:16:08  cvs
 		Post ipv get en $row offset naar 1

 		Revision 1.4  2008/05/29 15:31:19  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.3  2008/05/27 15:19:15  cvs
 		- SNS import do_V en do_DV
 		- StroeveVT import datum selecteerbaar

 		Revision 1.2  2007/11/02 14:56:56  cvs
 		VT contracten import, poging 2

 		Revision 1.1  2007/11/02 14:49:24  cvs
 		VT contracten import


*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("stroeveVT_functies.php");

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
if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR'") > 0)
{
	echo "<br>
<br>
De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR'").")<br>
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
$manualBoekdatum = $_GET['manualBoekdatum'];
$datum = ($manualBoekdatum <> "")?$manualBoekdatum:"";
include("stroeveVT_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van StroeveVT CSV bestand');

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
		if (!strstr($error[$x],"begindatum ongelijk aan opgegeven datum"))
		{
?>
  	<tr>
    	<td bgcolor="#BBBBBB"><?=$x?></td>
    	<td>&nbsp;&nbsp;
	      <?=$error[$x];?>
  	  </td>
  	</tr>

<?
		}
	}
?>
	</table>
	<br>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<input type="hidden" name="manualBoekdatum" value="<?=$manualBoekdatum?>">

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

$datum = ($manualBoekdatum <> "")?$manualBoekdatum:date("Y-m-d");

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$data = fgetcsv($handle, 1000, ";");   // lees eerste regel en sla die over (= header)
$row = 1;
while ($data = fgetcsv($handle, 1000, ";"))
{
	$row++;

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		// $skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();

   // zoek rekeningnr bij mutatie
   $DB = new DB();
   $valuta = (trim($data[5]) == "U$")?"USDF":"EUR";
   $PortNr  = trim($data[1]);
   $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$PortNr."' AND Valuta = '".$valuta."' AND Termijnrekening <> 0";
   $DB->SQL($query);
   $rekeningRec = $DB->lookupRecord();
   $VTwaarde = substr(trim($data[6]),1);
   $VTsign   = substr(trim($data[6]),0,1);
   $VTvaluta = (trim($data[5]) == "U$")?"USDF":"EUR";
   $VTtegenwaarde = trim($data[7]);
   $VTvalutakoers = $VTtegenwaarde / $VTwaarde;

 	 $mr = array();

 	 // boekregel 1
	  $mr[aktie]             = "VT";
  	$mr[bestand]           = $_file;
  	$mr[regelnr]           = $row;
	  $mr[Boekdatum]         = $datum;
   	$mr[Rekening]          = $rekeningRec["Rekening"];
	  $mr[Omschrijving]      = "VT contract ".$data[2];
	  $mr[Grootboekrekening] = "KRUIS";
	  $mr[Valuta]            = $VTvaluta;
	  $mr[Valutakoers]       = $VTvalutakoers;
	  if ($VTsign == "-")
	  {
	    $mr[Debet]             = $VTwaarde;
	    $mr[Credit]            = 0;
	    $mr[Bedrag]            = -1 * $VTwaarde;
	  }
	  else
	  {
      $mr[Debet]             = 0;
  	  $mr[Credit]            = $VTwaarde;
  	  $mr[Bedrag]            = $VTwaarde;
	  }

  	$mr[Transactietype]    = "VT";
	  $mr[Verwerkt]          = 0;
	  $mr[Memoriaalboeking]  = 0;
    if ($mr[Rekening] <> "")
	    $output[] = $mr;

	  // boekregel 2

	  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$PortNr."' AND Valuta = 'EUR' AND Termijnrekening <> 0";
    $DB->SQL($query);
    $rekeningRec = $DB->lookupRecord();
	  $mr[Rekening]          = $rekeningRec["Rekening"];
	  $mr[Valuta]            = "EUR";
	  $mr[Valutakoers]       = 1;
	  if ($VTsign <> "-")
	  {
	    $mr[Debet]             = $VTtegenwaarde;
	    $mr[Credit]            = 0;
	    $mr[Bedrag]            = -1 * $VTtegenwaarde;
	  }
	  else
	  {
      $mr[Debet]             = 0;
  	  $mr[Credit]            = $VTtegenwaarde;
  	  $mr[Bedrag]            = $VTtegenwaarde;
	  }
	  if ($mr[Rekening] <> "")
      $output[] = $mr;

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
Records in Stroeve CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>