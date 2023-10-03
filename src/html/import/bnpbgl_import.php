<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/07 08:00:28 $
 		File Versie					: $Revision: 1.7 $

 		$Log: bnpbgl_import.php,v $
 		Revision 1.7  2020/07/07 08:00:28  cvs
 		call 7605
 		
 		Revision 1.6  2020/05/20 13:05:35  cvs
 		call 7605
 		
 		Revision 1.5  2020/05/06 08:07:42  cvs
 		call 7605
 		
 		Revision 1.4  2020/04/29 14:16:26  cvs
 		call 7605
 		
 		Revision 1.3  2020/03/30 06:43:33  cvs
 		call 7605
 		
 		Revision 1.2  2019/10/30 13:12:17  cvs
 		call 7605
 		
 		Revision 1.1  2019/07/18 07:53:10  cvs
 		call 7605

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("bnpbgl_functies.php");
include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("BGL");
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
include("bnpbgl_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van BNP BGL bestand');

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

<?
    if (count($_SESSION["importFoutFile"]) > 0)
    {
      echo "<br/><a href='wwwFoutenBestand.php?bank=BGL' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
    }
?>

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

 $query = "SELECT bankCode,doActie FROM bnpbglTransactieCodes";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["bankCode"]] = $row["doActie"];
 }

$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 4096, ";"))
{
	$row++;

	if ($row == 1)  // header overslaan
  {
    continue;
  }

  array_unshift($data,"leeg");
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//  debug ($data);

  if (strtolower($data[39]) != "accounted")  // alleen verwerkt regels meenemen
  {
    $meldArray[] = "regel $row: overgeslagen status boeking <> accounted ";
    continue;
  }

  $data[5] = bnpbglDate($data[5]);

  $isin = "";
  $fondsVal = "";
  if (strlen($data[16] )== 15)
  {
    $isin = substr($data[16],0,12);
    $fondsVal = substr($data[16],-3);
  }


//debug($data);
  $fData = $data;
  $fData["transactieId"]         = $data[12];
  $fData["portefeuille"]         = (int)$data[34]; //  AccNr
  //$fData["rekening"]             = $data[34]; //  AccNr

  $fData["fondsValuta"]          = $fondsVal;
  $fData["rekeningValuta"]       = $data[52];
  if ($data[46] == "Forex Spot")
  {
    $fData["transactiecode"]       = "FX-".$data[24]; //  TransCode
  }
  else
  {
    $fData["transactiecode"]       = $data[24]; //  TransCode
  }

  $rek = explode("_", $data[16]);
  $fData["rekening1"] = $rek[0];
  $fData["valuta1"]   = $data[43];

  $rek = explode("_", $data[55]);
  $fData["rekening2"] = $rek[0];
  $fData["valuta2"]   = $data[52];



  if ($data[43] == "EUR" AND $data[52] != "EUR")
  {
    $fData["fxValuta"] = $data[52];
    $fData["fxBedrag"] = bnpbglNumber($data[51]); // bij BUY +
  }
  else if ($data[43] != "EUR" AND $data[52] == "EUR")
  {
    $fData["fxValuta"] = $data[43];
    $fData["fxBedrag"] = bnpbglNumber($data[129]); // bij BUY +
  }
  else
  {
    $fData["fxBedrag"] = 0;
    $error[] = "$row: FX transactie vv/vv handmatig boeken";
  }

  $fData["renteboeking"] = ($data[16] == $data[55]);

  $fData["fxKoers"] = 1/bnpbglNumber($data[1]);

  $fData["boekdatum"]            = bnpbglDate($data[25]); //  Boekdatum
  $fData["settlementdatum"]      = bnpbglDate($data[49]); //  ValDat
  $fData["storno"]               = (strtolower($data[152]) == "reverse"); //  Status (2=storno)
  $fData["gestorneerd"]          = (strtolower($data[152]) == "reversed"); //  Status (2=storno)
  $fData["instAccWisselkoers"]   = bnpbglNumber($data[1]); //  Exh Rate
  $fData["operWisselkoers"]      = bnpbglNumber($data[139]); //  Exh Rate
  $fData["operBruto"]            = bnpbglNumber($data[140]); //  Exh Rate
  $fData["operValuta"]           = $data[138];

  // call 9895
  $fData["stukValuta"]           = $data[144];
  // end

  $fData["isin"]                 = $isin;
  $fData["nettoBedrag"]          = bnpbglNumber($data[51]); //
  $fData["divCoupBedrag"]        = bnpbglNumber($data[47]); //  CoupAmoun
  $fData["aantal"]               = bnpbglNumber($data[148]); //  NOmQuantity
  if ($fData[146] == "Quote/100")
  {
    $fData["koers"]              = bnpbglNumber($data[147])*100; //  Price/koers
  }
  else
  {
    $fData["koers"]              = bnpbglNumber($data[147]); //  Price/koers
  }

  $fData["omschrijving"]         = trim($data[150]); //  FreeText

  $fData["opgelopenRente"]       = bnpbglNumber($data[58]);

  $fData["kost_1_bedrag"]        = bnpbglNumber($data[84]);
  $fData["kost_1_valuta"]        = bnpbglNumber($data[85]);
  $fData["kost_1_type"]          = bnpbglNumber($data[86]);

  $fData["kost_10_bedrag"]        = bnpbglNumber($data[87]);
  $fData["kost_10_valuta"]        = bnpbglNumber($data[88]);
  $fData["kost_10_type"]          = bnpbglNumber($data[89]);

  $fData["kost_2_bedrag"]        = bnpbglNumber($data[90]);
  $fData["kost_2_valuta"]        = bnpbglNumber($data[91]);
  $fData["kost_2_type"]          = bnpbglNumber($data[92]);

  $fData["kost_3_bedrag"]        = bnpbglNumber($data[93]);
  $fData["kost_3_valuta"]        = bnpbglNumber($data[94]);
  $fData["kost_3_type"]          = bnpbglNumber($data[95]);

  $fData["kost_4_bedrag"]        = bnpbglNumber($data[96]);
  $fData["kost_4_valuta"]        = bnpbglNumber($data[97]);
  $fData["kost_4_type"]          = bnpbglNumber($data[98]);

  $fData["kost_5_bedrag"]        = bnpbglNumber($data[99]);
  $fData["kost_5_valuta"]        = bnpbglNumber($data[100]);
  $fData["kost_5_type"]          = bnpbglNumber($data[101]);

  $fData["kost_6_bedrag"]        = bnpbglNumber($data[102]);
  $fData["kost_6_valuta"]        = bnpbglNumber($data[103]);
  $fData["kost_6_type"]          = bnpbglNumber($data[104]);

  $fData["kost_7_bedrag"]        = bnpbglNumber($data[105]);
  $fData["kost_7_valuta"]        = bnpbglNumber($data[106]);
  $fData["kost_7_type"]          = bnpbglNumber($data[107]);

  $fData["kost_8_bedrag"]        = bnpbglNumber($data[108]);
  $fData["kost_8_valuta"]        = bnpbglNumber($data[109]);
  $fData["kost_8_type"]          = bnpbglNumber($data[110]);

  $fData["kost_9_bedrag"]        = bnpbglNumber($data[111]);
  $fData["kost_9_valuta"]        = bnpbglNumber($data[112]);
  $fData["kost_9_type"]          = bnpbglNumber($data[113]);


//  debug($fData);

  //$fData["subfile"]              = $data[54];
  if ($fData["isin"] != "" AND $fondsVal != "")
  {
    if (!$fonds = bnpbglCheckFonds($fData["isin"], $fondsVal))
    {
      $meldArray[] = "regel $row}: Fonds niet gevonden ({$fData["isin"]}/{$fondsVal}) ";
    }
  }

	// bestand bevat ook de recon regels overslaan als recon regels

  if ($fData["storno"])
  {
    $skipped .= "- regel $row Storno overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }

  if ($fData["gestorneerd"])
  {
    $skipped .= "- regel $row Gestorneerde regel overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }


 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}


  $num = count($data);
  $val = $transactieMapping[trim($fData["transactiecode"])];


  $do_func = "do_$val";
//  debug($do_func);
  if ( function_exists($do_func) )
    call_user_func($do_func);
  else
    do_error($row, $fData["transactiecode"]);

}
fclose($handle);


//debug($transactieMapping);

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
include_once "verschillenLijst.html";
?>

Records in BNP BGL bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>