<?php
/*
    AE-ICT sourcemodule created 08 nov. 2021
    Author              : Chris van Santen
    Filename            : gs_import.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/import/{fileprefix}_import.php
///
///////////////////////////////////////////////////////////////////////////////

// settings voor import
$set = array(
  "banknaam"        => "Goldman Sachs",         //  volledige banknaam
  "depot"           => "GMS",                   //  depotbankcode v/d bank
  "filePrefix"      => "gs",                    //  fileprefix
  "fileDelimit"     => "|",                     //  CSV delimter
  "decimalSign"     => ".",                     //  decimaalteken in getallen
  "thousandSign"    => "",                      //  duizend scheidingsteken
  "headerRow"       => false,                    //  is de eerste regel een header?
  "transactieCodes" => "gsTransactieCodes",     //  tabelnaam van de transactiecodes
  "bankCode"        => "GScode"                 //  veldnaam bankcode in de Fondsentabel
);



// todo in html/transaktieImport.php
// aanmaken van een bankkeuze voor nieuwe partij
// todo in html/import/{fileprefix}_functies.php
// ------------------------------------------------------------------------------------------------
// mapDataFields() hier worden de kolomen gemapped en conditioned
// _cnvNumber($in) converteer nummers uit CSV naar PHP getalsnotatie NNNNN.dd
// _cnvDate($in)   converteer datums uit CSV naar mysql formaat DDDD-MM-YY
//

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("algemeneImportFuncties.php");
include_once("{$set["filePrefix"]}_functies.php");

$afw = new AIRS_import_afwijkingen($set["depot"]);

$depotBank          = $set["depot"];
$doIt               = $_REQUEST["doIt"];
$action             = $_REQUEST["action"];
$bestand            = $_REQUEST["bestand"];
$foutregels         = $_REQUEST["foutregels"];
$manualBoekdatum    = $_REQUEST["manualBoekdatum"];
$file               = $_REQUEST["file"];
$skipFoutregels     = array();
$meldArray          = array();
$transactieMapping  = array();
$transactieCodes    = array();
$fonds              = array();
$output             = array();

getTransactieMapping();
//debug($transactieMapping, 'transactieMapping');

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
$db               = new DB();
$rekeningAddArray = array();
$error            = array();
$content          = array();

$content["style"] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>
  ';

echo template("../".$__appvar["templateContentHeader"],$content);

if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '{$USR}' ";
	$db->executeQuery($query);
}
else
{
  $tempRecords = $db->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '{$USR}'");
  if ($tempRecords > 0)
  {
  	echo "
  <br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ({$USR}) ({$tempRecords})<br>
  <br>
  de import is geannuleerd ";
  	exit;
  }  
}


//
// setup van de progressbar
//
$prb = new ProgressBar();	                  // create new ProgressBar
$prb->pedding       = 2;	                  // Bar Pedding
$prb->brd_color     = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top']  = 	80;	                // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar

$csvRegels  = 1;
include("{$set["filePrefix"]}_validate.php");

if ($doIt != "1")  // validatie is al gebeurd dus skippen
{
	if (!validateCvsFile($file))
	{
    $prb->setLabelValue('txt1','');
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van <?=$set["depot"]?> bestand<br>
     	 Bestandsnaam :<?=$file?>
    	</td>
  	</tr>
<?
	$foutregels = "";
	$_vsp       = "";
	for ($x=0;$x < count($error);$x++)
	{
		$_spA = explode(":",$error[$x]);
		$_sp = trim($_spA[0]);
		if ( $_vsp <> $_sp )
    {
      $foutregels .= ",".$_sp;
    }
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

      const checkBoxes = $('input[type="checkbox"]:checked').length;
      let checkbox;
      let errorTxt;
      $("#btnSubmit").click(function()
      {
        errorTxt = "";
        if (checkBoxes > 0)
        {
          for (let n=100; n <= indexCount; n++ )
          {
            checkbox = n+"_check";
            if ($("#"+checkbox).is(':checked'))
            {
              var field = $('input[name='+n+'_rekNr]').attr("name");
              var test  = $('input[name='+field+']').val();
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": rekeningnr mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_portefeuille]').attr("name");
              var test  = $('select[name='+field+']').val();
             
              if (test.length < 1)
              {
                errorTxt = errorTxt + "\nrij "+ eval(n-99)+": portefeuille mag niet leeg zijn";
              }
             
              var field = $('select[name='+n+'_valuta]').attr("name");
              var test  = $('select[name='+field+']').val();
             
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
      if (checkBoxes > 0)
      {
        $("#frmAction").hide();
        $("#addRekening").val("1");
        $("#kopje").html("<b>Rekeningen toevoegen</b>");
      }
      $('input[type="checkbox"]').change(function()
      {
        const checkBoxes = $('input[type="checkbox"]:checked').length;
        if (checkBoxes > 0)
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
$prb->setLabelValue('txt1','Inlezen regels ('.$csvRegels.' records)');

$row            = 0;
$handle         = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$pro_step       = 0;
$_tfile         = explode("/",$file);
$_file          = $_tfile[count($_tfile)-1];
$skipped        = "";

while ($data = fgetcsv($handle, 4096, $set["fileDelimit"]))
{
	$row++;

  if ($data[0] == "")
  {
    continue;  // lege regels overslaan
  }

  if ($data[0] == "Header" OR $data[0] == "Trailer")
  {
    continue;  //skip als headerregel
  }

 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

  $data["regelnr"] = $row;
  array_unshift($data,"leeg");
  mapDataFields();


  if (!empty($data["isin"]) OR !empty($data["bankCode"]))
  {
    getFonds();
  }

  $val        = $transactieMapping[trim($data["transactieCode"])];
  $transcode  = trim($data["transactieCode"]);  // tbv  errormelding
  $do_func    = "do_$val";


  if ( function_exists($do_func) )
  {
    call_user_func($do_func);
  }
  else
  {
    do_error();
  }


}
$prb->hide();
fclose($handle);

//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;
$pro_multiplier = 100/count($output);
//debug($output);
reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  if ($ndx == 0)
  {
//    if (algCheckForDoubleImport($output[$ndx]) AND !$__develop )
//    {
//      $prb->hide();
//      Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
//	    exit();
//    }
  }
  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);

	$query = "
    INSERT INTO TijdelijkeRekeningmutaties SET
      add_date     = NOW()
    , add_user     = '{$USR}'
	  , change_date  = NOW()
    , change_user  = '{$USR}'
    ";

	foreach ( $output[$ndx] as $key=>$value )
	{
 	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }
   $query .= ", {$key} = '".mysql_real_escape_string($value)."' ";
	}

	if (!$db->executeQuery($query))
	{
	  echo "{$db->errorstr}<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}
$prb->hide();
?>

<br/>
<br/>
<b>Klaar met inlezen <br></b>
<?
//listarray($meldArray);
include_once "verschillenLijst.html";
?>

Records in <?=$set["banknaam"]?> bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
