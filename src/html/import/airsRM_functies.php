<?
/*
    AE-ICT sourcemodule created 30 apr. 2021
    Author              : Chris van Santen
    Filename            : airsRM_functies.php


*/

function getFonds($isin="", $val="")
{
  global $set, $data, $error, $row, $fonds,$meldArray;
  $db = new DB();

  if ($isin != "" AND $val != "")
  {
    $query = "SELECT * FROM 
                Fondsen 
              WHERE 
                ISINCode = '{$isin}' AND 
                Valuta ='{$val}' 
    ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[]     = "{$row}: fonds {$isin}/{$val} niet gevonden ";
      return false;
    }
  }
  else
  {
    return false;
  }
}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank, $meldArray;

  $db = new DB();
  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }

//  $query = "SELECT * FROM Rekeningen
//            WHERE consolidatie = 0 AND
//                  `RekeningDepotbank` = '{$rekeningNr}' AND
//                  `Depotbank` = '".$depotBank."' ";

  $query = "SELECT * FROM Rekeningen 
            WHERE consolidatie = 0 AND 
                  `RekeningDepotbank` = '{$rekeningNr}' ";


  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rekeningNr;
  }
  else
  {
//    $query = "SELECT * FROM Rekeningen WHERE
//              consolidatie = 0 AND
//              `Rekening` = '{$rekeningNr}' AND
//              `Depotbank` = '{$depotBank}' ";
    $query = "SELECT * FROM Rekeningen WHERE 
              consolidatie = 0 AND 
              `Rekening` = '{$rekeningNr}' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
      return false;
    }
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
    bankTransactieId = '{$mr["bankTransactieId"]}' AND 
    Rekening         = '{$mr["Rekening"]}' AND
    Boekdatum        = '{$mr["Boekdatum"]}'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel {$mr["regelnr"]}: rekenmutatie is al aanwezig (oa.RMid {$rec["id"]})";
    return true;
  }
  return false;
}




function do_NVT()
{
  return true;
}

function do_error()
{
	global $transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


