<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:15:59 $
 		File Versie					: $Revision: 1.1 $

 		$Log: bil_import.php,v $
 		Revision 1.1  2017/09/20 06:15:59  cvs
 		megaupdate
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		







*/

//  0 => 'leeg',
//  1 => 'INPUT_DATE',
//  2 => 'OPE_NATURE',
//  3 => 'OPE_TYPE',
//  4 => 'INSTR_CODE',
//  5 => 'QUANTITY',
//  6 => 'ACCOUNT_AMOUNT',
//  7 => 'cash_portfolio',
//  8 => 'OPE_CODE',
//  9 => 'portfolio',
//  10 => 'OPE_STATUS',
//  11 => 'LAST_QUOTE',
//  12 => 'OPE_CURR',
//  13 => 'ACCOUNT',
//  14 => 'ACCOUNT_CURR',
//  15 => 'accounting_date',
//  16 => 'value_date',
//  17 => 'input_user',
//  18 => 'creation_date',
//  19 => 'last_modif_user',
//  20 => 'last_modif_date',
//  21 => 'BOLD',
//  22 => 'INSTR_NAME',
//  23 => 'ud_isin_code',
//  24 => 'ud_fee_1_type_code',
//  25 => 'ud_fee_1_type_denom',
//  26 => 'ud_fee_1_amount',
//  27 => 'ud_fee_1_curr',
//  28 => 'ud_fee_2_type_code',
//  29 => 'ud_fee_2_type_denom',
//  30 => 'ud_fee_2_amount',
//  31 => 'ud_fee_2_curr',
//  32 => 'ud_fee_3_type_code',
//  33 => 'ud_fee_3_type_denom',
//  34 => 'ud_fee_3_amount',
//  35 => 'ud_fee_3_curr',
//  36 => 'ud_fee_4_type_code',
//  37 => 'ud_fee_4_type_denom',
//  38 => 'ud_fee_4_amount',
//  39 => 'ud_fee_4_curr',
//  40 => 'ud_fee_5_type_code',
//  41 => 'ud_fee_5_type_denom',
//  42 => 'ud_fee_5_amount',
//  43 => 'ud_fee_5_curr',
//  44 => 'ud_fee_6_type_code',
//  45 => 'ud_fee_6_type_denom',
//  46 => 'ud_fee_6_amount',
//  47 => 'ud_fee_6_curr',
//  48 => 'ud_fee_7_type_code',
//  49 => 'ud_fee_7_type_denom',
//  50 => 'ud_fee_7_amount',
//  51 => 'ud_fee_7_curr',
//  52 => 'ud_fee_8_type_code',
//  53 => 'ud_fee_8_type_denom',
//  54 => 'ud_fee_8_amount',
//  55 => 'ud_fee_8_curr',
//  56 => 'ud_fee_9_type_code',
//  57 => 'ud_fee_9_type_denom',
//  58 => 'ud_fee_9_amount',
//  59 => 'ud_fee_9_curr',
//  60 => 'ud_fee_10_type_code',
//  61 => 'ud_fee_10_type_denom',
//  62 => 'ud_fee_10_amount',
//  63 => 'ud_fee_10_curr',
//  64 => 'ud_fee_11_type_code',
//  65 => 'ud_fee_11_type_denom',
//  66 => '',


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("bil_functies.php");

$skipFoutregels = array();
$meldArray = array();



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
include("bil_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van BIL CSV bestand');

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
	<form action="<?=$PHP_SELF?>" method="POST">
    <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
    
    <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<button id="btnSubmit"> Uitvoeren </button>
	</form>
  
  <script>
    $(document).ready(function(){
      
      var t = $('input[type="checkbox"]:checked').length;
      
      $("#btnSubmit").click(function(){
        errorTxt = "";
        if (t > 0)
        {
          for (n=100; n <= indexCount; n++ )
          {
            checkbox = n+"_check";
            if ($("#"+checkbox).is(':checked'))
            {
              
              var field = $('input[name='+n+'_rekNr]').attr("name");
              var test = $('input[name='+field+']').val();
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": rekeningnr mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_portefeuille]').attr("name");
              var test = $('select[name='+field+']').val();
             
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": portefeuille mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_valuta]').attr("name");
              var test = $('select[name='+field+']').val();
             
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": valuta mag niet leeg zijn";
              }
            }
            
          }
        }
        if (errorTxt.length > 0)
        {
          alert(errorTxt);
          return false;
        }
        
      });
      if (t > 0)
      {
        $("#frmAction").hide();
        $("#addRekening").val("1");
        $("#kopje").html("<b>Rekeningen toevoegen</b>");
      }
      $('input[type="checkbox"]').change(function(){
        var t = $('input[type="checkbox"]:checked').length;
        if (t > 0)
        {
          $("#frmAction").hide();
          $("#addRekening").val("1");
          $("#kopje").html("<b>Rekeningen toevoegen</b>");
        }
        else
        {
          $("#frmAction").show(200);
          $("#addRekening").val("0");
          $("#kopje").html("<b>Mutaties verwerken</b>");
        }
      });
      
    });
  </script>
<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');

 $query = "SELECT BILcode,doActie FROM bilTransactieCodes";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["BILcode"]] = $row["doActie"];
 }

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
debug($transactieMapping);
$skipped = "";
while ($data = fgetcsv($handle, 4096, ","))
{
	$row++;
  if ($row == 1 )
  {
    continue;
  }
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

	 $data = reformat($data);
//  debug($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();
   
   $bankCodeNotFound = true;
   
   if ($data[4] <> "")
   {
     
     $fonds = array();
     $query = "SELECT * FROM Fondsen WHERE BILcode = '".$data[4]."' ";
     if ($fonds = $DB->lookupRecordByQuery($query))  $bankCodeNotFound = false;

   }
   if ($bankCodeNotFound)
   {
     $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[23]."' AND Valuta = '".$data[12]."'";
     if (!$fonds = $DB->lookupRecordByQuery($query))
     {

     }
   }

   $num = count($data);
   $val = $transactieMapping[trim($data[3])];


   $do_func = "do_$val";
   if ( function_exists($do_func) )
     call_user_func($do_func);
   else
     do_error();

}
fclose($handle);

$prb->hide();


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

Records in FVL CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>