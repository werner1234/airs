<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/10/30 14:37:26 $
 		File Versie					: $Revision: 1.10 $

 		$Log: abnv2_import.php,v $
 		Revision 1.10  2019/10/30 14:37:26  cvs
 		call 8217
 		
 		Revision 1.9  2019/08/28 10:08:20  cvs
 		dubbele regelnummers in meldarray
 		
 		Revision 1.8  2019/07/10 12:44:45  cvs
 		call 7946
 		
 		Revision 1.7  2019/07/08 12:11:40  cvs
 		call 7749
 		
 		Revision 1.6  2019/07/08 12:02:30  cvs
 		call 7749
 		
 		Revision 1.5  2019/06/05 12:16:38  cvs
 		call 7842
 		
 		Revision 1.4  2019/04/29 14:00:43  cvs
 		call 7746
 		
 		Revision 1.3  2019/04/03 15:11:22  cvs
 		call 7047
 		
 		Revision 1.2  2019/03/22 12:32:54  cvs
 		call 7047
 		
 		Revision 1.1  2018/11/23 13:34:06  cvs
 		call 7047
 		

*/

/*
    0 => 'leeg',
  1 => 'PortfolioID',
  2 => 'SecurityID',
  3 => 'SecurityCurrency',
  4 => 'TradeReference',
  5 => 'TradeType',
  6 => 'TradeNature',
  7 => 'TradeDate',
  8 => 'TradeQuantity',
  9 => 'TradePrice',
  10 => 'TradeAccruedInterest',
  11 => 'SettlementCurrency',
  12 => 'TradeFXSecurityCurrToSettlementCurr',
  13 => 'TradeCommission',
  14 => 'TradeExternalCosts',
  15 => 'TradeWithholdingTax',
  16 => 'TradeStampDuty',
  17 => 'TradeExchangeTax',
  18 => 'TradeNetAmount',
  19 => 'TradeDebitCredit',
  20 => 'SettlementDate',
  21 => 'SettlementAccountID',
  22 => 'FullName',

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("abnv2_functies.php");
include_once "../../classes/AIRS_import_zoekVervang.php";
include_once "../../classes/AIRS_import_afwijkingen.php";

$skipFoutregels = array();
$meldArray = array();

$afw = new AIRS_import_afwijkingen("AAB");
$zv = new AIRS_import_zoekVervang("AAB");



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
include("abnv2_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van ABN v2 CSV bestand');

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

 $query = "SELECT bankCode,omschrijving,doActie FROM abnV2TransactieCodes";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["bankCode"]] = $row["doActie"];
   $transactieMappingOms[$row["bankCode"]] = $row["omschrijving"];
 }
// debug($transactieMappingOms);
//debug($transactieMapping);
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 8192, ";"))
{
	$row++;
  if ($data[0] == "PortfolioID")
  {
    continue;  //header regels overslaan
  }

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
 	$r = abs($row);
	if (in_array($r , $skipFoutregels))
 	{
 		$skipped .= "- regel $r overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}


   $fileType = $data[9] == ""?"geld":"stukken";

	//////////////////////////
//
  $data["bankcode"]         = trim($data[1]);
  $data["transactieCode"]   = $data[9];
  $data["wisselkoers"]      = $data[16];
  $data["aantal"]           = $data[13];
  $data["fondskoers"]       = $data[14];
  $data["valuta"]           = $data[15];
  $data["nettoBedrag"]      = $data[31];
  $data["kosten_16"]        = $data[18];
  $data["valuta_16"]        = $data["valuta"];
  $data["kosten_17"]        = $data[20] - $data[28] - $data[29];

  $data["valuta_17"]        = $data["valuta"];
  //$data["kosten_18"]        = $data[21];
  $data["valuta_18"]        = $data["valuta"];
  $data["kosten_19"]        = $data[22] - $data[27] - $data[30];
  $data["valuta_19"]        = $data["valuta"];
  $data["kosten_15"]        = $data[17];
  $data["valuta_15"]        = $data["valuta"];
  $data["bankTransactieId"] = $data[11];
  $data["Boekdatum"]        = $data[12];
  $data["settlementDatum"]  = $data[33];
  $data["omschrijving"]     = $data[10];
  $data["rekening"]         = (int)$data[34];
  $data["portefeuille"]     = (int)$data[0];
  $data["DC"]               = $data[32];

  $data["kosten_27"]        = $data[27];
  $data["kosten_20"]        = $data[28];
  $data["kosten_21"]        = $data[29];
  $data["kosten_22"]        = $data[30];
  $data["dripKoers"]        = $data[37];
  $data["depotnr"]          = $data["portefeuille"];

  $data["ISIN"]             = $data[39];


  /// //////////////////////

  if ($fileType == "geld")
  {
    do_MUT();
  }
  else
  {

    $bankcodeNotFound = true;

    $fonds = array();

    if ($data["bankcode"] <> "" )
    {
      $query = "SELECT * FROM Fondsen WHERE AABCode = '".$data["bankcode"]."' OR ABRCode = '".$data["bankcode"]."'";
      $DB->SQL($query);
      if ($fonds = $DB->lookupRecord())
      {
        $bankcodeNotFound = false;
      }
    }

    $data["isOptie"] = ($fonds["fondssoort"] == "OPT");

    $num = count($data);
    $tc = (int)trim($data["transactieCode"]);
    $val = $transactieMapping[$tc];


    $do_func = "do_$val";
    if ( function_exists($do_func) )
    {
      call_user_func($do_func);
    }
    else
    {
      do_error($tc);
    }
  }

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
//listarray($meldArray);
include_once "verschillenLijst.html";
?>

Records in ABNv2 CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>