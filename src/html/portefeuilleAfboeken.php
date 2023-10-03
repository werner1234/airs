<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2019/02/04 10:52:46 $
  File Versie					: $Revision: 1.4 $

  $Log: portefeuilleAfboeken.php,v $
  Revision 1.4  2019/02/04 10:52:46  cvs
  consolidatie='0'  toegevoegd

  Revision 1.3  2018/02/16 10:20:43  cvs
  call 6612

  Revision 1.2  2016/07/18 12:32:07  cvs
  update 20160718

  Revision 1.1  2016/06/14 06:17:14  cvs
  call 4564 naar TEST


 */

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();
//$content = array();
global $USR;


if ($_GET["delTemp"] == 1)
{
  $DB = new DB();
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
  $DB->executeQuery($query);
}

// if poster
if ($_POST['posted'])
{
  debug($_POST);
  $memRekening = $_POST["portefeuille"]."MEM";  //
  $out = getAirsPortefeuilleWaarde($_POST["portefeuille"],$_POST["afboekdatum"],$_POST["depot"]);

  $db = new DB();
  $query = "SELECT Rekening, Memoriaal FROM Rekeningen WHERE Portefeuille = '".$_POST["portefeuille"]."'";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $rekeningen[$rec["Rekening"]] = getAIRSvaluta($rec["Rekening"],$_POST["afboekdatum"]);
    if ($rec["Memoriaal"] == 1)
    {
      $memRekening = $rec["Rekening"];
    }

  }

  foreach ($rekeningen as $rekening)
  {
    do_geld($rekening);
    echo "<li> geldrekening ".$rekening["Rekening"].", saldo: ".$rekening["totaal"]."</li>";
  }
  foreach($out as $fondsregel)
  {
    do_stukken($fondsregel);
    echo "<li> fond ".$fondsregel["fonds"].", aantal: ".$fondsregel["totaalAantal"]."</li>";
  }

  reset($output);
  for ($ndx=0;$ndx < count($output);$ndx++)
  {

    $_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
    $sep = " ";
    while (list($key, $value) = each($output[$ndx]))
    {
      if ($manualBoekdatum AND $key == "Boekdatum")
      {
        $value = $manualBoekdatum;
      }

      $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'\n";
      $sep = ",";
    }
    $_query .= ", add_date = NOW()";
    $_query .= ", add_user = '".$USR."'";
    $_query .= ", change_date = NOW()";
    $_query .= ", change_user = '".$USR."'";

    if (!$db->executeQuery($_query))
    {
      echo "<li>fout: ".mysql_error()."</li>";
      exit();
    }
  }

  echo "<li>Klaar</li>";
  echo "<hr/> <a href=\"tijdelijkerekeningmutatiesList.php\">Ga naar tijdelijk importbestand</a>";
  exit;
}
//$content = array("javascript"=>'<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">');
$content['style2'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
echo template($__appvar["templateContentHeader"], $content);


$db = new DB();
$query = "SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ";

if ($db->QRecords($query) > 0)
{
?>

  <style>
    .fout{
      margin: 25px;
      background: red;
      color: white;
      padding: 20px;
      width: 400px;
      text-align: center;
    }


  </style>
  <div class="fout">
    Tijdelijke rekeningmutaties gevonden voor <?=$USR?><br/><br />

    <a href="<?=$PHP_SELF?>?delTemp=1"><button> verwijder tijdelijke rekeningmutaties </button></a>
  </div>
<?
  exit;
}
?>

<br/>
<div>Portefeuille afboeken</div>
<br/>
<br/>
<form method="post" >
  <input type="hidden" name="posted" value="1">
  <input type="hidden" name="depot" id="depot" value="<?=$_GET["depot"]?>">

  <div class="formblock">
    <div class="formlinks">portefeuille </div>
    <div class="formrechts">
      <input type="text" name="portefeuille"  size="10" id="portefeuille" value="<?=$_GET["portefeuille"]?>" autocomplete="off" >
      <b><span id="portefeuilleInfo"></span></b>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">afboekdatum </div>
    <div class="formrechts">
      <input  class="AIRSdatepicker" type="text"  size="14" value="<?=date("d-m-Y");?>" name="afboekdatum" id="afboekdatum" onChange='date_complete(this);' >
    </div>
  </div>

  <div class="formblock">
    <br/>
    <br/>
    <input type="submit" value="maak tijdelijke rekeningmutaties">
  </div>
</form>

<script>
  $(document).ready(function(){
    $("#portefeuille").select();


    $("#portefeuille").autocomplete(
      {

        source: "lookups/getPortefeuille.php",                // link naar lookup script
        create: function(event, ui)                           // onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
        {

        },
        close: function(event, ui)                            // controle of ID gevuld is anders reset naar onCreate waarden
        {

        },
        search: function(event, ui)                           // als zoeken gestart het ID veld leegmaken
        {
          $("#rel_id").val("none")                               // reset koppel pointer
        },
        select: function(event, ui)                           // bij selectie clientside vars updaten
        {
          $("#portefeuille").val(ui.item.portefeuille);
          $("#depot").val(ui.item.depot);
          $("#portefeuilleInfo").html(ui.item.info + "(" + ui.item.depot + ")");

        },
        minLength: 2,                                         // pas na de tweede letter starten met zoeken
        delay: 0,
        autoFocus: true

      });
  });
</script>
<?
echo template($__appvar["templateRefreshFooter"], $content);


function getAirsPortefeuilleWaarde($portefeuille, $datum, $depotbank)
{
  $split = explode("-",$datum);
  $datum = $split[2]."-".$split[1]."-".$split[0];
  $db = new DB();

  switch($depotbank)
  {
//      case "AAB BE":
//
//        break;
    case "BIN";
      $depotSearch = "(Portefeuilles.Depotbank = 'BIN'  OR Portefeuilles.Depotbank = 'BINB') ";
      break;
    case "CS";
      $depotSearch = "(Portefeuilles.Depotbank = 'CS'  OR Portefeuilles.Depotbank = 'CS AG') ";
      break;
    case "AAB";
      $depotSearch = "(Portefeuilles.Depotbank = 'AAB'  OR Portefeuilles.Depotbank = 'AABIAM') ";
      break;
    default:
      $depotSearch = "Portefeuilles.Depotbank = '".$depotbank."' ";
  }


  $query = "
      SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds as fonds,
        SUM(Rekeningmutaties.Aantal) AS totaalAantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening  
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      WHERE
        Rekeningen.consolidatie='0' AND
        Portefeuilles.consolidatie = '0' AND 
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".substr($datum, 0, 4)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= '".$datum."' AND 
        Portefeuilles.Portefeuille='".$portefeuille."' AND
        $depotSearch 
      GROUP BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(totaalAantal,4) <> 0
      ORDER BY 
        Rekeningen.Portefeuille,Rekeningmutaties.Fonds; ";

//  debug($query);
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {

    $out[] = $rec;

  }
  return $out;
}

function getAIRSvaluta($rekeningnr, $datum)
{
  $split = explode("-",$datum);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $tmpDB = New DB();

  $qExtra = "Rekeningmutaties.boekdatum <= '".$datum."' ";

  $query = "
    SELECT 
      Rekeningen.Valuta, 
      round(SUM(Rekeningmutaties.Bedrag),12) as totaal,
      Rekeningmutaties.Rekening
    FROM 
      Rekeningmutaties, Rekeningen
    WHERE
      Rekeningen.consolidatie='0' AND
    	Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    	Rekeningmutaties.boekdatum >= '".substr($datum, 0, 4)."' AND
      Rekeningmutaties.Rekening = '".$rekeningnr."' AND
      
    	$qExtra
    GROUP BY 
      Rekeningen.Valuta
    ORDER BY 
      Rekeningen.Valuta";
//debug($query);
  if ($data = $tmpDB->lookupRecordByQuery($query))
  {
    return $data;
  }
  else
  {
    return false;
  }
}


function _valutakoers()
{
  global $mr;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta='".$mr["Valuta"]."' AND 
        Datum <= '".$mr["Boekdatum"]."' 
      ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  else
  {
    return 1;
  }


}

function _fondskoers()
{
  global $mr, $fonds;
  $db = new DB();
  $query = "
    SELECT 
      * 
    FROM 
      Fondskoersen
    WHERE 
      Fonds = '".$fonds["Fonds"]."' AND 
      Datum <= '".$mr["Boekdatum"]."' 
    ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}

function do_stukken($data)
{
  global $fonds, $output, $mr, $memRekening;

  $db = new DB();
  $query = "SELECT * FROM Fondsen WHERE Fonds = '".$data["fonds"]."'";

  $fonds = $db->lookupRecordByQuery($query);

  $split = explode("-",$_POST["afboekdatum"]);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $mr = array();
  $mr["Boekdatum"]         = $datum;
  $mr["settlementDatum"]   = $datum;

  $mr["Rekening"]          = $memRekening;

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Fondskoers"]        = _fondskoers();
  if ($data["totaalAantal"] > 0)
  {
    $mr["aktie"]             = "L";
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Aantal"]            = -1 * $data["totaalAantal"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $mr["Transactietype"]    = "L";
    $grootboek = "ONTTR";
  }
  else
  {
    $mr["aktie"]             = "D";
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Aantal"]            = -1 * $data["totaalAantal"];
    $mr["Credit"]             = 0;
    $mr["Debet"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
    $mr["Transactietype"]    = "D";
    $grootboek = "STORT";
  }

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $output[] = $mr;

  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fonds"]             = "";
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = $grootboek;
  if ($mr["Bedrag"] < 0)
  {
    $mr["Credit"]           = abs($mr["Bedrag"]);
    $mr["Debet"]            = 0;
    $mr["Bedrag"]           = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";

  $output[] = $mr;

}


function do_geld($data)
{
  global $mr, $output;

  if (abs($data["totaal"]) < 0.005)
  {
    return false;
  }

//debug($data);
  $split = explode("-",$_POST["afboekdatum"]);
  $datum = $split[2]."-".$split[1]."-".$split[0];

  $mr = array();
  $mr["Rekening"]          = $data["Rekening"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Boekdatum"]         = $datum;
  $mr["settlementDatum"]   = $datum;
  $mr["Valuta"]            = $data["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();

  if ($data["totaal"] < 0)
  {
    $mr["Omschrijving"]      = "Storting";
    $mr["aktie"]              = "S";
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            =abs($data["totaal"] );
    $mr["Bedrag"]            = $mr["Credit"];

  }
  else
  {
    $mr["Omschrijving"]      = "Ontrekking";
    $mr["aktie"]              = "O";
    $mr["Grootboekrekening"]  = "ONTTR";
    $mr["Credit"]             = 0;
    $mr["Debet"]              = abs($data["totaal"] );
    $mr["Bedrag"]             = -1 * $mr["Debet"];
  }
//debug($mr);
  $output[] = $mr;

}



?>