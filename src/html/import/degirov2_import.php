<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/05 14:54:56 $
 		File Versie					: $Revision: 1.6 $

 		$Log: degirov2_import.php,v $
 		Revision 1.6  2020/02/05 14:54:56  cvs
 		call 8397
 		
 		Revision 1.5  2019/09/18 10:31:09  cvs
 		call 8103
 		
 		Revision 1.4  2019/09/18 09:39:31  cvs
 		call 8103
 		
 		Revision 1.3  2019/03/22 14:16:32  cvs
 		x
 		
 		Revision 1.2  2019/03/04 13:14:02  cvs
 		call 7243
 		
 		Revision 1.1  2018/10/15 15:11:01  cvs
 		call 7243
 		

*/
/*
=== V2 FILE =========================================
  1 => 'account',
  2 => 'id',
  3 => 'cashId',
  4 => 'productId',
  5 => 'tradeId',
  6 => 'caId',
  7 => 'date',
  8 => 'time',
  9 => 'cashType',
  10 => 'cashDescription',
  11 => 'quantity',
  12 => 'price',
  13 => 'value',
  14 => 'currency',
  15 => 'description',
  16 => 'orderId',
  17 => 'contractType',
  18 => 'exchange',
  19 => 'contractSize',
  20 => 'isin',
  21 => 'expiry',
  22 => 'putCall',
  23 => 'strike',
  24 => 'symbol',
  25 => 'underlyingIsin',
  26 => 'vwdIssueId',
  27 => 'bloombergId',
  28 => 'productCurrency',
  29 => 'productName',

*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once ("algemeneImportFuncties.php");

include_once("degirov2_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("GIRO");

// call 7189
$ISINskipLichtingDeponering = array(
  "LU0904783973",
  "LU0904783114",
  "LU0904784781",
  "LU0875333444",
  "LU1959429272"
);

//debug($_GET);
$DB = new DB();
$query = "SELECT giroCode,doActie,omschrijving FROM degiroTransactieCodes";
$DB->executeQuery($query);
while ($row = $DB->nextRecord())
{
  $transactieMapping[$row["giroCode"]] = $row["doActie"];
  $transactieOmschrijving[$row["giroCode"]] = $row["omschrijving"];
}

debug($transactieMapping);
$skipFoutregels = array();
if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"GIRO");
  $rac->addRekeningen($_POST);
  $doIt = 0;
  $file = $bestand;
  $file2 = $bestand2;
}

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
$rekeningAddArray = array();
$content = array();
$content["style"] .= '
<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">
    
	  <script type="text/javascript" src="../javascript/algemeen.js"></script>
	  <script type="text/javascript" src="../javascript/jquery-min.js"></script>
	  <script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>

';
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
include("degirov2_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van DeGiro CSV bestand');
$err1 = array();
$err2 = array();

if ($doIt <> "1")  
{
  if  (!validateCvsFile($file))
  {
    $error[] = "deGiro bestand ongeldig";
  }

	if ( count($error) > 0)
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
      echo "<br/><a href='wwwFoutenBestand.php?bank=GIRO' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
    }
?>

	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
    <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
    	<option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
    <?
//debug($_SESSION);
    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"GIRO");
      $rac->getStyles();

      $rekArray = $_SESSION["rekeningAddArray"];
      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
      {
        $rac->makeInputRow($rekArray[$rNdx]);
      }
      echo $rac->getHTML();
    }
    ?>



    <br/>

    <button id="btnSubmit"> Uitvoeren </button>
	</form>

    <script>
//test getJS
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
$prb->setLabelValue('txt1','Converteren records ');

echo "<hr/>1) Verwerken van $file <br/><br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
//debug($skipFoutregels);
$pro_step = 0;
//debug($transactieMapping);
while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;

  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);

  if (count($data) < 2)
  {
    debug("lege regel", $row);
    continue;
  }

  if ($row == 1)   // header overslaan
  {
    continue;
  }

  if ($data[0] == "account"  AND $data[1] == "id")
  {
    continue;  // header regels in samengevoegde bestanden
  }

	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);

  if ($data[9] == "CA3046" OR $data[9] == "CA3026")
  {
    $data["row"] = $row;
    $FXArray[$data[5]."-".$data[1]."-".$data[17]][] = $data;
    continue;
  }
  else if (trim($data[18]) <> "")
  {
    $data["row"] = $row;
    $FXArray[$data[18]][] = $data;
    continue;
  }


  $val = $transactieMapping[trim($data[9])];
  $actieOmschrijving = $transactieOmschrijving[trim($data[9])];
  if ($data[19] == "J" ) // TODO terugmelding storno skip 3 jun 15
  {
    do_error();
  }  
  elseif ($val == "")
  {
    do_error(trim($data[9]));
  }
  else
  {
    $fonds = array();
    $do_func = "do_$val";

    if ( function_exists($do_func))
      call_user_func($do_func);
    else
      do_error();

  }

  $num = count($data);

}
fclose($handle);



// koppels verwerken

debug($FXArray);

foreach ($FXArray as $FXKoppel)
{
  if (count($FXKoppel) == 1)
  {

    $data = $FXKoppel[0];

    do_FX($data);
  }
  else
  {
    $legA = $FXKoppel[0];
    $legB = $FXKoppel[1];
    $row = $legA["row"];
//  echo "<hr/>";
//  debug($legA, "leg A");
//  debug($legB, "leg B");
    do_KRUIS($legA, $legB);
  }

}

//

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;


reset($output);
//debug ($output[0]);
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

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'";
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



<?
include_once "verschillenLijst.html";
?>

Records in GIRO CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>