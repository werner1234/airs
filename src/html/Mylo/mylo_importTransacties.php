<?php
/*
    AE-ICT sourcemodule created 13 jul. 2020
    Author              : Chris van Santen
    Filename            : mylo_importTransacties.php

    $Log: mylo_importTransacties.php,v $
    Revision 1.3  2020/07/29 09:59:10  cvs
    call 8750
naar RVV 20201123

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
session_start();
$_SESSION["NAV"] = "";
session_write_close();

global $__appvar;
$myloDepotbank  = "BIN";
$gRow           = 0;
$cfg            = new AE_config();
$db             = new DB();

$content["style"] = '
<link rel="stylesheet" href="../widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="../widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="../style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="../style/dropzone.css"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="../javascript/jquery-min.js"></script>
<script type="text/javascript" src="../javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="../javascript/algemeen.js"></script>
<script type="text/javascript" src="../javascript/dropzone.js"></script>
  
';
echo template("../".$__appvar["templateContentHeader"], $content);
?>
<style>
  .spinner{
    position: absolute;
    top:100px;
    left:100px;
    text-align: center;
    display: none;
    padding: 2rem;
    background: white;
    border:1px solid #999;
    border-radius: 10px;
  }
</style>
 <div class="spinner">
   <img src="../images/loading.gif"/><br/><br/>
   Moment geduld a.u.b.
 </div>
<?php

if ($_POST['posted'])
{
  ?>
  <script>
    $(".spinner").show();
  </script>
  <?php
  ob_flush();flush();
  $output         = array();
  $geldImport     = false;
  $stukkenImport  = false;

  // check filetype
  if ($_FILES['geldBestand']["error"] == 0)
  {
    if (  $_FILES['geldBestand']["type"] != "text/comma-separated-values" &&
          $_FILES['geldBestand']["type"] != "text/x-csv" &&
          $_FILES['geldBestand']["type"] != "text/csv" &&
          $_FILES['geldBestand']["type"] != "application/octet-stream" &&
          $_FILES['geldBestand']["type"] != "application/vnd.ms-excel" &&
          $_FILES['geldBestand']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd geld bestandstype(".$_FILES['geldBestand']["type"]."), alleen tekst bestanden zijn toegestaan.";
    }
    else
    {
      $geldImport = true;
    }

  }

  if ($_FILES['stukkenBestand']["error"] == 0)
  {
    if (  $_FILES['stukkenBestand']["type"] != "text/comma-separated-values" &&
      $_FILES['stukkenBestand']["type"] != "text/x-csv" &&
      $_FILES['stukkenBestand']["type"] != "text/csv" &&
      $_FILES['stukkenBestand']["type"] != "application/octet-stream" &&
      $_FILES['stukkenBestand']["type"] != "application/vnd.ms-excel" &&
      $_FILES['stukkenBestand']["type"] != "text/plain")
    {
      $_error = "FOUT: verkeerd stukken bestandstype(".$_FILES['stukkenBestand']["type"]."), alleen tekst bestanden zijn toegestaan.";
    }
    else
    {
      $stukkenImport = true;
    }

  }

  if (empty($_error))
  {
    session_start();

    if ($geldImport)
    {
      $filename = $_FILES['geldBestand']["tmp_name"];
      if (!$handle = @fopen($filename, "r"))
      {
        $error[] = "FOUT bestand $filename is niet leesbaar";
        return false;
      }

      $row      = 0;
      while ($data = fgetcsv($handle, 4096, ","))
      {
        $row++;
        if ($row == 1)
        {
          if ($data[0] != "Portfolio" OR $data[2] != "Direction")
          {
            $error[] = "Geen geldige geld bestand";
            break;
          }
          continue;
        }
        else
        {
          if (trim($data[0] == ""))  // sla lege regels over
          {
            continue;
          }

//          $data[3] = cnvNumber($data[3]);
//          $data[5] = cnvNumber($data[5]);
//          $data[6] = cnvNumber($data[6]);
//          $data[7] = cnvNumber($data[7]);
          $data[8] = cnvDate($data[8]);
          $data[9] = cnvDate($data[9]);
//          debug($data, "do_mut");
          do_MUT();

        }
      }
      $gRow = $row;
    }

    if ($stukkenImport)
    {

      $filename = $_FILES['stukkenBestand']["tmp_name"];
      if (!$handle = @fopen($filename, "r"))
      {
        $error[] = "FOUT bestand $filename is niet leesbaar";
        return false;
      }

      $row      = 0;
      while ($data = fgetcsv($handle, 4096, ","))
      {
        $row++;
        if ($row == 1)
        {
          if ($data[0] != "Portfolio" OR $data[2] != "TransactionCode")
          {
            $error[] = "Geen geldige stukken bestand";
            break;
          }
          //continue;

        }
        else
        {
//          debug($data);
          if (trim($data[0] == ""))  // sla lege regels over
          {
            continue;
          }
//          $data[5] = cnvNumber($data[5]);
//          $data[6] = cnvNumber($data[6]);
//          $data[7] = cnvNumber($data[7]);
//          $data[8] = cnvNumber($data[8]);
          $data[9] = cnvDate($data[9]);
          $data[10] = cnvDate($data[10]);
//          debug($data, "do_koop");
          do_STUKMUT();
        }

      }
    }
  }

  if (count($_error) > 0)
  {
    foreach($_error as $item)
    {
      echo "<li>$item</li>";
    }
  }


  if (count($output) > 0)
  {

    $prb                = new ProgressBar();	// create new ProgressBar
    $prb->pedding       = 2;	// Bar Pedding
    $prb->brd_color     = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
    $prb->setFrame();          	                // set ProgressBar Frame
    $prb->frame['left'] = 50;	                  // Frame position from left
    $prb->frame['top']  =	80;	                  // Frame position from top
    $prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
    $prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
    $prb->show();

    $prb->moveStep(0);
    $prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
    $pro_step = 0;
    $pro_multiplier = 100/count($output);
    reset($output);
    for ($ndx=0;$ndx < count($output);$ndx++)
    {
      $pro_step += $pro_multiplier;
      $prb->moveStep($pro_step);

      $_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
      $sep = " ";

      foreach ($output[$ndx] as $key=>$value)
      {
        $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'\n";
        $sep = ",";
      }
      $_query .= "
      , add_date = NOW()
      , add_user = '".$USR."'
      , change_date = NOW()
      , change_user = '".$USR."'";


      if (!$db->executeQuery($_query))
      {
        echo mysql_error();
        Echo "<br> FOUT bij het wegschrijven naar de database!";
        exit();
      }
    }
    $prb->hide();
?>

    <script> $(".spinner").hide(200); </script>
    <b>Klaar met inlezen <br></b>
    <?
    listarray($meldArray);
    ?>

    Records in geld bestand :<?=$gRow?><br>
    Records in stukken bestand :<?=$row?><br>
    Aangemaakte mutatieregels : <?=count($output)?><BR>
    <?=$skipped?>
    <hr>
    <a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
    <hr>
<?php
  }



//  unlink($filename);
  echo template("../".$__appvar["templateRefreshFooter"], $content);
  exit;
}




if (!$_FILES['importfile']['name'])
{

  // TRM leegmaken
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
  $db->executeQuery($query);
  ?>
  <style>
    #bestand2{
      display: none;


    }
    #feedback{
      display: none;
      padding: 2em;
      background: maroon;
      color: white;
      border-radius: 10px;
    }

    legend{
      padding: 5px;
      background: rgba(20,60,90,1);
      color: white;
    }
  </style>
  <script>

  </script>

  <form enctype="multipart/form-data" action="<?= $PHP_SELF ?>" method="POST"  name="editForm">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <!-- Name of input element determines name in $_FILES array -->
    <br />
    <?php
    if ($_error)
      echo "<b style=\"color:red;\">".$_error."</b>";


?>



    <br/>
    <h2>Moka, inlezen Transacties</h2>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">&nbsp; </div>
      <div class="formrechts">
        <div id="feedback"></div>
      </div>
    </div>

    <div class="form">
      <div class="formblock">
        <div class="formlinks"><span id="geldBestand">Geld bestand</span> </div>
        <div class="formrechts">
          <input type="file" name="geldBestand" size="50" value="">
        </div>
      </div>





      <div class="formblock">
        <div class="formlinks">Stukken bestand</div>
        <div class="formrechts">
          <input type="file" name="stukkenBestand" size="50" value="">
        </div>
      </div>


      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts"><br/>
          <input type="button" value="importeren" onclick="submitter();">
        </div>
      </div>

    </div>

        <br/>
        <br/>

  </form>




  <script>
    function feedback(txt,greenColor)
    {
      if (greenColor)
      {
        $("#feedback").css("background","rgba(20,90,20,1)");
      }
      else
      {
        $("#feedback").css("background","maroon");
      }

      if (txt != "")
      {
        $("#feedback").html(txt);
        $("#feedback").show(300);
      }
      else
      {
        $("#feedback").html("");
        $("#feedback").hide();
      }
    }

    function submitter()
    {
      if (document.editForm.geldBestand.value == '' && document.editForm.stukkenBestand.value == '')
      {
        feedback("Selecteer eerst een importbestand");
        return;
      }

      document.editForm.submit();
    }


    $(document).ready(function()
    {


    });

    </script>
        <?
      }
      echo template("../".$__appvar["templateRefreshFooter"], $content);


function cnvNumber($in)
{
  return str_replace(",",".",$in);
}

function cnvDate($in)
{
  $ds = explode("-", $in);
  if (count($ds) == 3 AND !strstr($in, "T"))
  {
    return "{$ds[2]}-{$ds[1]}-{$ds[0]}";
  }

  $ds = explode("/", $in);
  if (count($ds) == 3)
  {
    return "20{$ds[2]}-{$ds[0]}-{$ds[1]}";
  }
  else
  {
    return substr($in,0,10);
  }
}

function getRekening($rekeningNr="-1")
{

  global $myloDepotbank, $meldArray;
  $db = new DB();
//  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '{$myloDepotbank}' ";
//  debug($query);
//  if ($rec = $db->lookupRecordByQuery($query))
//  {
//    return $rec["Rekening"];
//  }
//  else
//  {
    $query = "SELECT * FROM `Rekeningen` WHERE `consolidatie`=0 AND `Rekening` = '{$rekeningNr}' AND `Depotbank` = '{$myloDepotbank}' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }

    $meldArray[] = "Rekening {$rekeningNr} niet gevonden";
    return false;


}

function getFonds($ISIN, $fondsValuta)
{
  global $fonds, $meldArray;
  $fonds = array();
  $db = new DB();

  if(trim($ISIN) != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$ISIN."' AND Valuta ='".$fondsValuta."' ";
    return ($fonds = $db->lookupRecordByQuery($query));
  }

  $meldArray[] = "Fonds {$ISN} / {$fondsValuta} niet gevonden";
  return false;
}

function checkControleBedrag($controleBedrag,$notabedrag,$filetype="C")
{
  global $meldArray, $data, $mr;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $prefix = "regel {$filetype}-{$mr["regelnr"]}: {$mr["Rekening"]} --> notabedrag sluit ";
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = $prefix."niet aan nota= {$notabedrag} / controle = {$controleBedrag} / verschil = ".round($notabedrag - $controleBedrag,2);
  }
  else
  {

  }

}

function checkVoorDubbelInRM($mr)
{
  global $meldArray;

  $db = new DB();
  $query = "
  SELECT 
    id 
  FROM 
    Rekeningmutaties 
  WHERE 
    bankTransactieId = '".substr($mr["bankTransactieId"],0,25)."' AND 
    Rekening         = '".$mr["Rekening"]."' 
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

function _debetbedrag()
{
  global $mr;

  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
  global $mr;

  return $mr["Credit"] * $mr["Valutakoers"];
}

function do_STUKMUT()
{
  global $fonds, $data, $mr, $output,$meldArray,$row, $_error;

  $controleBedrag = 0;

  $mr = array();

  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $data[11];
  $mr["Boekdatum"]         = $data[9];
  $mr["settlementDatum"]   = $data[10];
  $mr["Rekening"]          = getRekening($data[0].$data[1]);
  $mr["orderId"]           = $data[11];
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $fonds = getFonds($data[3], $data[4]);
  if (!$fonds)
  {
    $meldArray[] = "regel S-{$row}: Fonds niet gevonden voor {$data[3]}/{$data[4]}";
  }

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];

  $mr["Fondskoers"]        = $data[5];
  $mr["Valutakoers"]       = $data[7];
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  switch (strtoupper($data[2]))
  {
    case "SELL":
      $mr["Aantal"]            = (float)$data[6] * -1;
      $mr["aktie"]             = "V";
      $mr["Omschrijving"]      = "Sell ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag         += $mr["Bedrag"];
      $mr["Transactietype"]    = "V";
      $output[] = $mr;
      break;
    case "BUY":
      $mr["Aantal"]            = (float)$data[6];
      $mr["aktie"]             = "A";
      $mr["Omschrijving"]      = "Buy ".$fonds["Omschrijving"];
      $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag         -= $mr["Bedrag"];
      $mr["Transactietype"]    = "A";
      $output[] = $mr;
      break;
    case "WTHD":
      $mr["Rekening"]          = getRekening($data[0]."MEM");
      $mr["Aantal"]            = (float)$data[6] * -1;
      $mr["aktie"]             = "L";
      $mr["Omschrijving"]      = "Withdrawl ".$fonds["Omschrijving"];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag         += $mr["Bedrag"];
      $mr["Transactietype"]    = "L";
      $output[] = $mr;


      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Fonds"]             = "";
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = abs($mr["Bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = $mr["Debet"] * -1;

      $mr["Transactietype"]    = "";
      $output[] = $mr;

      break;
    case "DEPO":
      $mr["Rekening"]          = getRekening($data[0]."MEM");
      $mr["Aantal"]            = (float)$data[6];
      $mr["aktie"]             = "D";
      $mr["Omschrijving"]      = "Investment ".$fonds["Omschrijving"];
      $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag         -= $mr["Bedrag"];
      $mr["Transactietype"]    = "D";
      $output[] = $mr;


      $mr["Grootboekrekening"] = "STORT";
      $mr["Fonds"]             = "";
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];

      $mr["Transactietype"]    = "";
      $output[] = $mr;
      break;
    default:
      $_error[] = $row.": do_STUKMUT(), geen BUY/SELL/WTHD/DEPO (".strtoupper($data[2]).") overgeslagen";
      break;
  }





  checkControleBedrag($controleBedrag,$data[8],"S");


}


function do_MUT()  // geld mutaties
{

  global $row, $data, $mr, $output,$i, $meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "MUT";

  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $data[11];
  $mr["Boekdatum"]         = $data[8];
  $mr["settlementDatum"]   = $data[9];
  $mr["Rekening"]          = getRekening($data[0].$data[1]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[4];
  $mr["Valutakoers"]       = $data[5];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data[10];
  if ($data[2] != "WITH")
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[3]);
    $mr["Bedrag"]          = _creditbedrag();
    $controleBedrag += $mr["Bedrag"];
  }
  else
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data[3]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag -= $mr["Bedrag"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
//  debug($mr);
  if ($mr["Bedrag"] != 0)
  {

    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,$data[6]);
}

