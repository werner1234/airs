<?
/*
    AE-ICT sourcemodule created 01 nov. 2019
    Author              : Chris van Santen
    Filename            : caw_import.php

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("caw_functies.php");

$afw = new AIRS_import_afwijkingen("CAW");

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
include("caw_validate.php");

$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van ING CSV bestand');


if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
	if (!validateCvsFile($file))  //aetodo: wordt nu overgeslagen
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
   echo "<br/><a href='wwwFoutenBestand.php?bank=CAW' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
 }
?>
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

 $query = "SELECT bankCode,doActie FROM cawTransactieCodes";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   $transactieMapping[$row["bankCode"]] = $row["doActie"];
 }
 
//debug($transactieMapping);
//debug($file);
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";

$nrs = array(6,9,11,14,15,24,26);
while ($data = fgetcsv($handle, 4096, ";"))
{
	$row++;
  
  if ($row < 2 )  // headerregels overslaan
  {
    continue;

  }

  if(count($data) < 10)
  {
    continue; // skip lege regels
  }

  $prb->hide();
  
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

  // converteren van variabelen
//debug($data);


  foreach ($data as $k=>$v)
  {
    if ($v == "NULL")
    {
      $v = 0;
      $data[$k] = $v;
    }

  }


  if (strtolower(substr($data[3],0,4)) == "isin")
  {
    $data["isin"]         = substr($data[3],5);
  }

  if (strtolower(substr($data[3],0,8)) == "telekurs")
  {
    $data["bankCode"] = substr($data[3], 9);
  }



  $data["rekening"]             = $data[1];
  $data["afrekenValuta"]        = $data[9];
  $data["omschrijving"]         = "";  // wordt nagevraagd
  $data["boekdatum"]            = cawDate($data[15]);
  $data["settledatum"]          = cawDate($data[16]);
  $data["aantal"]               = cawNumber($data[5]);
  $data["nettoBedrag"]          = cawNumber($data[14]);
  $data["valutakoersAccount"]   = 1/cawNumber($data[10]);
  $data["valutakoersFondsEur"]  = cawNumber($data[23]);
  $data["valutakoers3"]         = 1/cawNumber($data[25]);
  $data["transactieId"]         = $data["rekening"].$data["boekdatum"].$data["nettoBedrag"];
  $data["transactieCode"]       = $data[4];
  $data["fondsValuta"]          = $data[64];
  $data["koers"]                = cawNumber($data[8]);
  $data["opgelopenRente"]       = cawNumber($data[7]);
  $data["roer"]                 = cawNumber($data[56]);
  $data["correspondantFees"]    = cawNumber($data[58]);   // KOBU
  $data["fees"]                 = cawNumber($data[59]);   // KOST
  $data["foreignBrokage"]       = cawNumber($data[60]);   // KOBU
  $data["foreignTax"]           = cawNumber($data[61]);   // KOBU
  $data["otherFees"]            = cawNumber($data[62]);   // KOBU
  $data["commissions"]          = cawNumber($data[63]);   // KOST

  $data["kobu"]                 = $data["correspondantFees"] + $data["foreignBrokage"] +  $data["foreignTax"] + $data["otherFees"];
  $data["kost"]                 = $data["fees"] + $data["commissions"] + cawNumber($data[13]);
  $data["tax"]                  = cawNumber($data[13]);


  if ($data["fondsValuta"] != "EUR" AND $data["afrekenValuta"] == "EUR")
  {
    $data["valutakoers"] = cawNumber($data[10]);
  }
  else
  {
    $data["valutakoers"] = cawNumber($data[23]);
  }
  getFonds();


//debug($data);
  $num = count($data);
  $val = $transactieMapping[trim($data["transactieCode"])];
  $transcode = trim($data["transactieCode"]);  // tbv  errormelding
  $do_func = "do_$val";

  if ($do_func == "do_")
  {
    continue;
  }

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

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;
//debug($output);
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

<br/>
<br/>
<b>Klaar met inlezen <br></b>
<?
listarray($meldArray);

//foreach ($meldArray as $item)
//{
//
//}

?>

Records in CAW CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>