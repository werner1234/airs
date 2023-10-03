<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/17 12:27:17 $
 		File Versie					: $Revision: 1.27 $

 		$Log: stroeve_import.php,v $
 		Revision 1.27  2018/07/17 12:27:17  cvs
 		call 6734
 		
 		Revision 1.26  2017/09/20 06:17:33  cvs
 		megaupdate 2722
 		
 		Revision 1.25  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.24  2014/12/16 07:30:30  cvs
 		*** empty log message ***
 		
 		Revision 1.23  2014/07/08 12:43:24  cvs
 		*** empty log message ***
 		
 		Revision 1.22  2012/05/08 15:27:04  cvs
 		nota controle
 		
 		Revision 1.21  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008
 		
 		Revision 1.20  2008/04/23 10:57:00  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.18  2005/12/19 16:27:14  cvs
 		*** empty log message ***

 		Revision 1.17  2005/12/16 15:56:11  jwellner
 		no message

 		Revision 1.16  2005/11/09 10:15:59  cvs
 		overrule datum

 		Revision 1.15  2005/09/27 14:57:45  cvs
 		controle dubbel inlezen

 		Revision 1.14  2005/09/21 09:04:21  cvs
 		setlocale weggehaald

 		Revision 1.13  2005/09/21 07:53:48  cvs
 		nieuwe commit 21-9-2005






*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");

include_once ("algemeneImportFuncties.php");

include_once("stroeve_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("TGB");

$skipFoutregels = array();
$meldArray = array();

if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"TGB");
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
include("stroeve_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Stroeve CSV bestand');

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
<?
 if (count($_SESSION["importFoutFile"]) > 0)
 {
   echo "<br/><a href='wwwFoutenBestand.php?bank=TGB' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
 }
?>
    <br>
		<b>Vervolg aktie?</b>
  <form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
    <div id="kopje"></div>
     <select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
    	<option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
<?

    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"TGB");
      $rac->getStyles();

      $rekArray = $_SESSION["rekeningAddArray"];
      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
      {
         $rac->makeInputRow($rekArray[$rNdx]);
      }
      echo $rac->getHTML();
    }
?>

	  <input type="hidden" name="doIt" id="doIt" value="1">
	  <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<br/>
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


$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 1000, ","))
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
   
   $StroeveNotFound = true;
   
   if ($data[22] <> "" AND $data[3] <> "")
   {
     
     $fonds = array();

     $_stroeveCode = substr("00000000".trim($data[22]),-7);
     $query = "SELECT * FROM Fondsen WHERE stroeveCode = '".$_stroeveCode."' ";
     $DB->SQL($query);
     if ($fonds = $DB->lookupRecord())  $StroeveNotFound = false;

   }

   $num = count($data);
   $val = trim($data[4]);


   $do_func = "do_$val";
   if ( function_exists($do_func) )
     call_user_func($do_func);
   else
     do_error();

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
    if (checkForDoubleImport($output[$ndx]) AND !$__develop)
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

include_once "verschillenLijst.html";
?>

Records in Stroeve CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
  <a href="../transaktieImport.php">Opnieuw selecteren</a>
<br/>
<br/>
<br/>
<br/>
<br/>

<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>