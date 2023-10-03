<?php
/*
    AE-ICT sourcemodule created 11 jul. 2022
    Author              : Chris van Santen
    Filename            : reconCashVerwerk.php


*/

include_once "wwwvars.php";
session_start();
//debug($_REQUEST);
//debug($_SESSION["huisfonds"]);

if ($_REQUEST["delTemp"] == "1")
{
  $db = new DB();
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '{$USR}' ";
  $db->executeQuery($query);
}

if ($_REQUEST["opboeken"] == "1")
{
  $db = new DB();
  $query = "SELECT `id` FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '{$USR}'";
  if ($db->lookupRecordByQuery($query))
  {
    echo template($__appvar["templateContentHeader"], $content);
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
      <?=vt("Tijdelijke rekeningmutaties gevonden voor")?>" <?=$USR?><br/><br />

      <a href="<?=$PHP_SELF?>?delTemp=1&opboeken=1"><button> <?=vt("verwijder tijdelijke rekeningmutaties")?> </button></a>
    </div>
    <?
    exit;
  }
}

if ($_REQUEST["verwerk"] == "1")
{
  include_once "huisFondsFuncties.php";
  $dataset = $_SESSION["huisfonds"]["dataset"];
  foreach($dataset as $data)
  {

//    debug($data);
    if (strtolower($data[3]) == "liq")
    {
      do_GELDMUT();
    }
    else
    {
      if ($data["aantal"] > 0)
      {
        do_D();
      }
      else
      {
        do_L();
      }
    }
  }

  $db = new DB();

  foreach ($output as $row)
  {

    $_query = "INSERT INTO TijdelijkeRekeningmutaties SET ";
    $set = array(
      "`add_date`    = NOW()",
      "`add_user`    = '{$USR}'",
      "`change_date` = NOW()",
      "`change_user` = '{$USR}'",
    );
    foreach ($row as $key=>$value)
    {
      $set[] = "`{$key}` = '".mysql_escape_string($value)."'";
    }


    $query = $_query.implode("\n, ", $set);
//    debug($query);
    if (!$db->executeQuery($query))
    {
      echo mysql_error();
      Echo "<br> FOUT bij het wegschrijven naar de database!";
      exit();
    }

  }
  echo template($__appvar["templateContentHeader"], $content);
  echo "<br/><br/><br/> <h3>klaar met verwerken</h3><div><a href='tijdelijkerekeningmutatiesList.php'>Ga naar de tijdelijke rekeningmutaties</a></div>";
  echo template($__appvar["templateRefreshFooter"], $content);
  exit;
}

if ($_REQUEST["opboeken"] != "1")
{
  $filename = $_SESSION["huisfonds"]["file"];

  if (!file_exists($filename))
  {
    echo "Huisfonds bestand bestaat niet meer";
  }
  if (!$handle = @fopen($filename, "r"))
  {
    $error[] = "FOUT bestand $filename is niet leesbaar";
    return false;
  }


  $rekeningenChecked    = array();
  $fondsenChecked       = array();
  $portefeuillesChecked = array();
  $portefeuilleDepot    = array();
  $accessCounters       = array();
  $errorArray           = array();
  $dataRaw = array();

  $depot          = strtoupper($bank);

  while ($data = fgetcsv($handle, 4096, ";"))
  {


    $count++;
    $data[0] = stripBOM($data[0]);
    if (trim($data[0]) == "")
    {
      continue;
    }  // sla lege regels over
    array_unshift($data,"leeg");

    $data[5] = cnvGetal($data[5]);
    $data[6] = cnvGetal($data[6]);
    $data[7] = cnvGetal($data[7]);
    $data[8] = cnvGetal($data[8]);

    if ( ($data[5] - $data[6]) == 0)
    {
      continue;
    }

    $valutakoers = (float) $data[8];
    $fondskoers  = $data[7];
    if (trim($data[3]) != "LIQ")
    {
      $fondsRec = fondsCheck($data[3], $data[4]);

      $data["type"]     = "sec";
      $data["aantal"]   = $data[5] - $data[6];
      $data["prtFound"] = portefeuilleCheck($data[1]);
      $data["rekFound"] = rekeningCheck($data[1].$data[2]);
      $data["fndFound"] = ($fondsRec != false);
      $data[11] = $fondsRec["Omschrijving"];
      $data[12] = $fondsRec["Fonds"];
      $data[13] = $fondsRec["Fondseenheid"];
    }
    else
    {
      $data["type"]     = "cash";
      $data["rekFound"] = rekeningCheck($data[1].$data[2]);

      $data["aantal"]   = $data[5] - $data[6];


    }

    $dataRaw[] = $data;



  }
  unlink($filename);
  ksort($accessCounters);
}

//debug($dataRaw);
//debug($rekeningenChecked,"Rek");
//debug($fondsenChecked,"Fnd");
//debug($portefeuillesChecked,"Prt");
//debug($errorArray);

echo template($__appvar["templateContentHeader"], $content);

?>
  <style>
    table{
      font-family: Consolas;
      font-size: 12px;
      padding:5px;
      border:2px solid #999;
    }
    .tdH{
      background: #0a246a;
      color: white;
    }
    .td1, .td2, .td3{
      padding: 5px;
      border-bottom: #BBB 1px solid;
    }
    .ar{ text-align: right}
    .ac{ text-align: center}
    .rood{background: maroon; color: white}
    button{
      background: #0a246a;
      color: white;
      opacity: .8;
      cursor: pointer;
    }
    button:hover{
      opacity: 1;
    }

  </style>
<h3>Huisfonds posities</h3>

<?php

//debug($dataRaw);
if (count($errorArray) > 0)
{
  $row = 0;
?>
  <h3>Het bestand bevat onbekende onderdelen, verwerking gestopt</h3>
  <table>

<?
  sort($errorArray);
  foreach ($errorArray as $error)
  {
    $row++;
    echo "<tr><td>{$row}.</td><td>{$error}</td></tr>";
  }

?>

  </table>

  <h2>verwerking afgebroken..</h2>
  <br/>
  <br/>
  <br/>
<?
}
else
{
  if ($_REQUEST["opboeken"] != "1")
  {
    $_SESSION["huisfonds"]["dataset"] = $dataRaw;
    $_SESSION["huisfonds"]["depots"]  = $portefeuilleDepot;
    $now = $_SESSION["huisfonds"]["datum"];
//  debug($portefeuilleDepot);
    ?>
    <form method='get' action='huisfondsPortefeuilleAfboeken.php' id='afboekForm'>



    <input type='hidden' name='afboekdatum' value='<?=$now?>'>
    <table>
      <thead>
      <tr>
        <td class='tdH td1 '>portefeuille</td>
        <td class='tdH td1 '>depot</td>
        <td class='tdH td1 '>datum</td>
      </tr>
      </thead>
      <?
      foreach ($portefeuilleDepot as $portefeuille=>$depot)
      {
        $dep = $depot;
        $pot[] = $portefeuille;

        echo "
    <tr>
        <td class='td1 '>{$portefeuille}</td>
        <td class='td1 '>{$depot}</td>
        <td class='td1 '>{$now}</td>
        <td class='td1 '>
          
      </tr>
    ";
      }
      ?>
      <tbody>
      </tbody>
    </table>
      <br/>
      <br/>
      <input type='hidden' name='depot' value='<?=$dep?>'>
      <input type='hidden' name='portefeuille' value='<?=implode(",",$pot)?>'>
      <input type="button" id="btnSubmit" value="Afboeken">
    </form>
  <?php
    }
  else
  {
?>
    <table>
      <thead>
      <tr>
        <td class='tdH td1 '>portefeuille</td>
        <td class='tdH td1 '>valuta</td>
        <td class='tdH td1 '>ISIN</td>
        <td class='tdH td1 '>valuta</td>
        <td class='tdH td1 '>fonds</td>
        <td class='tdH td1 ar'>aantal</td>
        <td class='tdH td1 ar'>datum</td>
      </tr>
      </thead>
      <tbody>


      <?php



      foreach ($_SESSION["huisfonds"]["dataset"] as $item)
      {
        echo "
    <tr>
        <td class='td1 '>{$item[1]}</td>
        <td class='td1 '>{$item[2]}</td>
        <td class='td1 '>{$item[3]}</td>
        <td class='td1 '>{$item[4]}</td>
        
        <td class='td1 '>{$item[12]}</td>
        <td class='td1  ar'>{$item["aantal"]}</td>
        <td class='td1  ar'>{$item[9]}</td>
        
      </tr>
    ";
      }

      ?>
      <tr>
        <td colspan="12" class="ac">
          <br/>
          <form >
            <input type="hidden" name="verwerk" value="1"/>
            <button type="submit" > maak tijdelijke rekeningmutaties</button>
          </form>
        </td>
      </tr>


      </tbody>
    </table>
<?php
  }
?>

<br/>
<br/>
<br/>
<?
?>

  <br/>
  <br/>

  <script>
    $(document).ready(function(){
      $("#btnSubmit").click(function(e){
        e.preventDefault();
        $("#btnSubmit").val("Bezig met voorbereiden afboeken");
        $("#btnSubmit").prop("disabled",true);
        $("#afboekForm").submit();
      });
    });
  </script>
<?

}
//debug($accessCounters, "counters");
echo template($__appvar["templateRefreshFooter"], $content);

function rekeningCheck($reknr)
{
  global $rekeningenChecked, $errorArray, $accessCounters;

//  debug(array($reknr,$rekeningenChecked, isset($rekeningenChecked[$reknr])),"debug");
  if (isset($rekeningenChecked[$reknr]))
  {
    $accessCounters["rekCached"] += 1;

    return $rekeningenChecked[$reknr];
  }

  $accessCounters["rek"] += 1;
  $accessCounters["rekCnt"][] = $reknr;
  $db = new DB();
  $query = "
  SELECT * FROM Rekeningen WHERE
  (
   Rekeningen.RekeningDepotbank = '{$reknr}' OR
   Rekeningen.Rekening = '{$reknr}'  
  )
  AND
    Rekeningen.consolidatie = 0 
  ";

  $rekRec = $db->lookupRecordByQuery($query);
  $rekeningenChecked[$reknr] = ($rekRec != false);
  if ($rekeningenChecked[$reknr] == false)
  {
    $errorArray[] = "Rekening {$reknr} niet gevonden";
  }
  return $rekeningenChecked[$reknr];
}

function portefeuilleCheck($portefeuille)
{
  global $portefeuilleDepot, $portefeuillesChecked, $errorArray, $accessCounters;

  if (isset($portefeuillesChecked[$portefeuille]))
  {
    $accessCounters["prtCached"] += 1;
    return $portefeuillesChecked[$portefeuille];
  }
  $accessCounters["prt"] += 1;
  $accessCounters["prtCnt"][] = $portefeuille;
  $db = new DB();
  $query = "
  SELECT * FROM Fondsen WHERE
  (
    `Portefeuille` = '{$portefeuille}' 
  )

  ";
  $prtRec =   $db->lookupRecordByQuery($query);
  $portefeuillesChecked[$portefeuille] = ( $prtRec != false);
  if ($portefeuillesChecked[$portefeuille] == false)
  {
    $errorArray[] = "Portefeuille {$portefeuille} niet gevonden als huisfonds";
  }
  else
  {
    $query = "SELECT * FROM `Portefeuilles` WHERE `Portefeuille` = '{$portefeuille}'  ";
    $pRec = $db->lookupRecordByQuery($query);
    $portefeuilleDepot[$portefeuille] = $pRec["Depotbank"];
  }
  return $portefeuillesChecked[$portefeuille];
}

function fondsCheck($isin, $valuta)
{
  global $fondsenChecked, $errorArray, $accessCounters;

  $idx = $isin."-".$valuta;

  if (isset($fondsenChecked[$idx]))
  {
    $accessCounters["fndCached"] += 1;
    return $fondsenChecked[$idx];
  }
  $accessCounters["fnd"] += 1;

  $db     = new DB();
  $query  = "
  SELECT `Fonds`,`Omschrijving`,`Fondseenheid` FROM `Fondsen` WHERE
  (
   `ISINCode` = '{$isin}' AND
   `Valuta`   = '{$valuta}'
  )

  ";
  $fondsRec = $db->lookupRecordByQuery($query);

  if ($fondsRec == false)
  {
    $errorArray[] = "Fonds {$idx} niet gevonden";
  }

  $fondsenChecked[$idx] = $fondsRec;
  return $fondsenChecked[$idx];
}



function cnvGetal($in)
{
  $out = str_replace(".","" ,$in );
  $out = str_replace(",","." ,$out );
  return floatval($out);
}

function stripBOM($field)
{
  $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $field);
  return $response;
}

function makeNumber($in)
{
  return str_replace(",", ".",trim($in));
}