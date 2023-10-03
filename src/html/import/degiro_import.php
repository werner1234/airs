<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/10/03 12:48:21 $
 		File Versie					: $Revision: 1.10 $

 		$Log: degiro_import.php,v $
 		Revision 1.10  2018/10/03 12:48:21  cvs
 		call 7189
 		
 		Revision 1.9  2018/06/19 06:56:46  cvs
 		call 3517
 		
 		Revision 1.8  2017/11/20 14:13:29  cvs
 		rekeningAdd
 		
 		Revision 1.7  2017/10/16 12:26:39  cvs
 		call 6170
 		
 		Revision 1.6  2016/12/13 12:19:05  cvs
 		aanpassing import bestandsindeling
 		
 		Revision 1.5  2016/09/12 13:42:16  cvs
 		tikfout
 		
 		Revision 1.4  2016/03/29 09:13:17  cvs
 		call 4419
 		
 		Revision 1.3  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/07/01 14:07:15  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/03 13:25:48  cvs
 		*** empty log message ***
 		




=== CASHFILE =========================================
 [1]  id,                 bankTransactieId
 [2]  int_account,        Rekening
 [3]  name,               n.v.t.
 [4]  cash_account_id,    Grootboek
 [5]  alphabetic_code,    Valuta
 [6]  amount,             Debet/Credit
 [7]  date,               Datum + Settlementdatum
 [8]  name,               n.v.t.
 [9]  bloomberg_id,       n.v.t.
 [10] product_id,         n.v.t.
 [11] contracttype,       n.v.t.
 [12] hiqkey,             n.v.t.
 [13] isin,               Fonds
 [14] alphabetic_code,    n.v.t.  
 [15] ca_id               n.v.t.
        
=== TRANSFILE =========================================
[1]  transaction_id       bankTransactieId	
[2]  int_account        	Rekening	i.c.m. currency
[3]  name                 n.v.t.	
[4]  bloomberg_id         n.v.t.	
[5]  product_id           Fonds	t.b.v. 1e Fondsbepaling
[6]  contracttype       	n.v.t.	
[7]  productkey           n.v.t.	
[8]  date                 Datum + Settlementdatum	
[9]  isin                 Fonds	t.b.v. Fondsbepaling (icm currency) na productID
[10] exchange             n.v.t.	
[11] currency             Valuta	ook t.b.v Rekening en Fonds
[12] buy_sell             Transactietype	(nog geen opties mogelijk)
[13] size                 Aantal	
[14] price                Koers	
[15] value                Waarde	
[16] storno             	Melden als deze = J dan overslaan

*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("degiro_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("GIRO");

// call 7189
$ISINskipLichtingDeponering = array(
  "LU0904783973",
  "LU0904783114",
  "LU0904784781",
  "LU0875333444",
);


$DB = new DB();
$query = "SELECT giroCode,doActie,omschrijving FROM degiroTransactieCodes";
$DB->executeQuery($query);
while ($row = $DB->nextRecord())
{
  $transactieMapping[$row["giroCode"]] = $row["doActie"];
  $transactieOmschrijving[$row["giroCode"]] = $row["omschrijving"];
}


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
$content[style] .= '
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
include("degiro_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van DeGiro CSV bestand');
$err1 = array();
$err2 = array();

if ($doIt <> "1")  
{
 if  (!validateCvsFile($file,"STRA"))
 {
   $error[] = "STRA bestand ongeldig";
 }    
 
 if  (!validateCvsFile($file2,"CTRA"))
 {
    $error[] = "CTRA bestand ongeldig";
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
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="bestand2" value="<?=$file2?>">
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

echo "<hr/>1) Verwerken van $file <br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
//debug($skipFoutregels);

debug($transactieMapping);
while ($data = fgetcsv($handle, 1000, ","))
{
  if (count($data) < 2)
  {
    debug("lege trans regel", $row+1);
    continue;
  }

  if ($row == 0)
  {
    $cashFile = ($data[0] == "id");   // bepaal soort bestand 
    $row = 1;
    continue;
  }
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



  $val = $transactieMapping[trim($data[13])];
  $actieOmschrijving = $transactieOmschrijving[trim($data[13])];
  if ($data[17] == "J" ) // TODO terugmelding storno skip 3 jun 15
  {
    do_error();
  }  
  elseif ($val == "")
  {

    do_error(trim($data[13]));
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

///////////  tweede file  CASH bestand
$file = $file2;

echo "<hr/>2) Verwerken van $file <br/>";

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
//debug($skipFoutregels);

$FXArray = array();
while ($data = fgetcsv($handle, 1000, ","))
{
  if (count($data) < 2)
  {
    debug("lege cash regel", $row+1);
    continue;
  }

  if ($row == 0)
  {
    $cashFile = ($data[0] == "id");   // bepaal soort bestand 
    $row = 1;
    continue;
  }
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

   
  if (trim($data[17]) <> "")
  {
    $FXArray[$data[17]][] = $data;
    continue;
  }
   //debug($data,$row);
     
  $val = $transactieMapping[trim($data[5])];
  $actieOmschrijving = $transactieOmschrijving[trim($data[5])];
  $fonds = array();

  if ($val == "")
  {

   do_error(trim($data[5]));
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

foreach ($FXArray as $FXKoppel)
{
  $legA = $FXKoppel[0];
  $legB = $FXKoppel[1];
//  echo "<hr/>";
//  debug($legA, "leg A");
//  debug($legB, "leg B");
  do_KRUIS($legA, $legB);
}



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


<b>Klaar met inlezen <br></b>
<?
listarray($meldArray);
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