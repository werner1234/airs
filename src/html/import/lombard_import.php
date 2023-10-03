<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/10/16 12:27:15 $
 		File Versie					: $Revision: 1.8 $

 		$Log: lombard_import.php,v $
 		Revision 1.8  2017/10/16 12:27:15  cvs
 		call 6170
 		
 		Revision 1.7  2017/09/20 06:16:53  cvs
 		call 6115
 		
 		Revision 1.6  2017/02/22 07:40:42  cvs
 		cal 5571
 		
 		Revision 1.5  2016/11/10 07:20:16  cvs
 		call 5402
 		
 		Revision 1.4  2016/10/21 13:49:49  cvs
 		call 3856
 		
 		Revision 1.3  2016/10/21 10:55:49  cvs
 		call 3856
 		
 		Revision 1.2  2016/04/04 14:27:18  cvs
 		no message
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***

*/
/*  data index
  0 = Accn nb 
  1 = Accn cur 
  2 = Client cur 
  3 = Accn type 
  4 = Contract nb 
  5 = Countprty id 
  6 = Countprty desc 
  7 = Trans desc 
  8 = Trans type 
  9 = External id 
  10 = Trans date 
  11 = Value date 
  12 = Trade date 
  13 = Matur date 
  14 = DBCR indic 
  15 = Revsl indic 
  16 = Amt accn cur 
  17 = Amt trade cur 
  18 = Amt client cur 
  19 = Int rate 
  20 = Exch rate 
  21 = Stock qty 
  22 = Price 
  23 = Trade cur 
  24 = Price cur 
  25 = Oper code 
  26 = ISIN code 
  27 = Sec code 
  28 = Stock desc 
  29 = Sec type 
  30 = Price unit 
  31 = Appl code 
  32 = Cash mvt code 
  33 = Amt ref cur 
  34 = Ref cur 
  35 = Internal Id 
  36 = Perf date 
  37 = Int Trans Id 
  38 = Fid contract 
  39 = MIC code 
  40 = Amt VAT 
  41 = Portf Id Spec 
  42 = Amt Ref Tax  
  43 = Stamp duty 
  44 = Accr. int. 
  45 = Taxes 
  46 = LODH broker fees 
  47 = Foreign broker fees 
  48 = withholding tax 
  49 = US/EU backup withholding tax 
  50 = LODH ticket fee 
  51 = Investment fond fees 
  52 = Other fees 

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("lombard_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("LOM");

$skipFoutregels = array();
$meldArray = array();

if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"LOM");
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
include("lombard_validate.php");

if ($__appvar["bedrijf"] != "WMP")
{
  $accTypeSkipArray = array(
    "DA",
    "MA",
    "1L",
    "1R",
    "AV",
    "FA",
  );
}
else
{
  $accTypeSkipArray = array(
    "DA",
    "1L",
    "1R",
    "AV",
    "FA",
  );
}


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Lombard CSV bestand');

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

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"LOM");
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

 $query = "SELECT LOMcode,doActie FROM lombardTransactieCodes  ";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["LOMcode"]] = $row["doActie"];
 }
debug($transactieMapping, "transactie mapping");
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 2000, "\t"))
{
	$row++;
  
  if ($row < 2) continue;  // headers overslaan

//  debug($data);
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
//debug($data, "regel $row");
   
   do_algemeen();

   $num = count($data);
   $val = $transactieMapping[trim($data[9])."-".trim($data[26])];

   /*
    * LET OP ook $accTypeSkipArray bijwerken bij uitsluitingen!!!
    */

   if ($val == "")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." onbekende transactiecode :".trim($data[9])."-".trim($data[26])." --overgeslagen-- ";
     continue;
   }

   if (trim(strtoupper($data[4])) == "FT")
   {
     $data[2] .= "XXX";

   }
   if (trim(strtoupper($data[4])) == "DA")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." betreft een toekenning --overgeslagen-- ";
     continue;
   }

   if (trim(strtoupper($data[4])) == "MA" AND $__appvar["bedrijf"] != "WMP" )
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." betreft een margin-rekening --overgeslagen-- ";
     continue;
   }

   if ( trim(strtoupper($data[4])) == "1L" OR
        trim(strtoupper($data[4])) == "1R" OR
        trim(strtoupper($data[4])) == "AV")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." betreft een lening/rekening (1L/1R/AV) --overgeslagen-- ";
     continue;
   }

  if (trim(strtoupper($data[4])) == "FA")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." betreft een prepayment --overgeslagen-- ";
     continue;
   }
   
   if (trim(strtoupper($data[16])) == "R")
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." bevat STORNO --overgeslagen-- ";
     continue;
   }  
   
   $isinValuta = ($data[24] <> "")?$data[24]:$data[25];
   if (!getFonds())
   {
     $meldArray[] = "regel ".$row.": ".$mr["Rekening"]." Fonds niet gevonden (".$data[27].$isinValuta."/".$data[28].") --overgeslagen-- ";
     continue;
   }


   $do_func = "do_$val";
   //debug($do_func);
   if ( function_exists($do_func) )
     call_user_func($do_func);
   else
     do_error();
     //debug($data);
  

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

Records in Lombard CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>