<?
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : _template_functies.php


*/

///////////////////////////////////////////////////////////////////////////////
///
/// TEMPLATE file voor bankimport, dit bestand niet aanpassen
/// maar opslaan als html/import/{fileprefix}_functies.php
///
///////////////////////////////////////////////////////////////////////////////

function mapDataFields($fileType)
{
  global $data;

//  debug($data);

  switch (strtolower($fileType))
  {
    case "fts":
      $data["rekening"]         = $data[3];
      $data["afrekenValuta"]    = $data[4];
      $data["portefeuille"]     = $data[2];
      $data["transactieId"]     = $data[17];
      $data["transactieCode"]   = $data[7];
      $data["omschrijving"]     = $data[8];
      $data["storno"]           = $data[14];
      $data["boekdatum"]        = _cnvDate($data[5]);
      $data["settledatum"]      = _cnvDate($data[6]);
      $data["bedrag"]           = $data[11];
      $data["DC"]               = $data[12];

      break;
    case "exp":
      $data["transactieId"]     = $data[1];
      $data["transactieCode"]   = $data[2];
      $data["kostenSoort"]      = $data[3];
      $data["bedragRekVal"]     = $data[4];
      $data["bedragTransactie"] = $data[5];
      $data["storno"]           = $data[7];
      break;
    case "aat":
      $data["rekening1"]        = $data[6];
      $data["valuta1"]          = $data[8];
      $data["bedrag1"]          = $data[20];


      $data["rekening2"]        = $data[9];
      $data["valuta2"]          = $data[11];
      $data["bedrag2"]          = $data[25];
      $data["fxKoers"]          = $data[31];
      $data["boekdatum"]        = _cnvDate($data[16]);
      $data["settledatum"]      = _cnvDate($data[17]);
      $data["portefeuille"]     = $data[2];
      $data["fxOmschrijving"]   = $data[19];
      $data["transactieId"]     = $data[33];
      $data["transactieCode"]   = $data[18];
      $data["valutakoers"]      = $data[30];
      $data["DC"]               = $data[32];
      $data["fondsValuta"]      = $data[22];
      break;
    case "zav":
      $data["transactieId"]     = $data[1];
      $data["omschrijving"]     = trim($data[14])." ".trim($data[15]);
      $data["DC"]               = $data[24];
      $data["valutakoers"]      = $data[30];
      $data["valuta"]           = $data[4];
      $data["bedrag"]           = $data[3];
      $data["kosten"]           = $data[26];
      $data["nettoCash"]        = ($data["DC"] == "D")?-1*$data["bedrag"]:$data["bedrag"];
      break;
    case "sxt":
      $data["rekening"]         = $data[4];
      $data["afrekenValuta"]    = $data[6];
      $data["portefeuille"]     = $data[2];
      $data["transactieId"]     = $data[33];
      $data["transactieCode"]   = abs($data[10]);
      $data["omschrijving"]     = $data[11];
      $data["bankCode"]         = $data[16];
      $data["isin"]             = $data[18];
      $data["boekdatum"]        = _cnvDate($data[8]);
      $data["settledatum"]      = _cnvDate($data[9]);

      $data["aantal"]           = $data[24];
      $data["valutakoers"]      = 1/$data[32];
      $data["storno"]           = $data[14];

      $data["fondsValuta"]      = $data[23];
      $data["koers"]            = $data[25];
      $data["brutoBedrag"]      = $data[26];
      $data["opgelopenRente"]   = abs($data[27]);
      $data["kosten"]           = $data[28];
      $data["intPer"]           = $data[45];
      break;
  }


}

function checkRowType ($data, $row)
{
  global $fileTypeArray, $ftRowsSkipped;

  $fileTypeArray = array(
    "aat"     => array("type" => "date",    "columns" => array(15,16)),
    "fts"     => array("type" => "date",    "columns" => array(4,5)),
    "sxt"     => array("type" => "date",    "columns" => array(7,8)),
    "zav"     => array("type" => "number",  "columns" => array(1,2,27)),
    "exp"     => array("type" => "number",  "columns" => array(3,4,5)),
    "pos"     => array("type" => "valuta",  "columns" => array(1,3,5) ),
  );

  foreach ($fileTypeArray as $type => $checks)
  {

    l("--skip check", (int)$checks["skipped"]);
    $fileType  = $type;
    $check     = true;
    l("testing rowtype", $checks['type']);
    switch($checks['type'])
    {
      case "date":
        foreach ($checks['columns'] as $d)
        {
          l("$type",$d,$data[$d]);
          if (!vpCheckIsDate($data[$d]))
          {
            $check = false;
          }
        }
        break;
      case "number":
        foreach ($checks['columns'] as $d)
        {
          l("$type",$d,$data[$d]);
          if (!is_numeric($data[$d]))
          {
            $check = false;
          }
        }
        break;
      case "valuta":
        foreach ($checks['columns'] as $d)
        {
          l("$type",$d,$data[$d]);
          if (strlen($data[$d]) != 3)
          {
            $check = false;
          }
        }
        break;
    }
    if ($check) { break; }

  }

  // als skipped dan niet inlezen
  if ($fileType != "" AND $check AND $checks["skipped"])
  {
    l("skipped",$fileType);
    return false;
  }

  if ($fileType != "" AND $check) // geldige regel gevonden
  {
    l("found",$fileType);
    return $fileType;
  }
  else // ongeldige regel met vermelding
  {
    l("invalid",$fileType);
    $ftRowsSkipped[] = $row." overgeslagen onbekende transactieregel";
    return false;
  }
}

function l($v1, $v2="", $v3="")
{
  global $vpDebug;
  if ($vpDebug)
  {
    echo "<br/>$v1 --> $v2 --> $v3";
  }
}

function _cnvNumber($in)
{
  global $set;
  $in = str_replace($set["thousandSign"], "", $in);
  return str_replace($set["decimalSign"], ".", $in);
}

function _cnvDate($in)
{
  $j = (int) substr($in, 0, 4);
  $m = (int) substr($in, 4, 2);
  $d = (int) substr($in, 6, 2);
  return $j."-".$m."-".$d;
}

function vpCheckIsDate($in)
{
  $j = (int) substr($in, 0, 4);
  $m = (int) substr($in, 4, 2);
  $d = (int) substr($in, 6, 2);
  return checkdate($m, $d, $j);
}

function getTransactieMapping()
{
  global $set, $transactieCodes, $transactieMapping;
  $db = new DB();
  $query = "SELECT bankCode,doActie FROM {$set["transactieCodes"]}";

  $db->executeQuery($query);
  while ($row = $db->nextRecord())
  {
    $transactieCodes[]                   = $row["bankCode"];
    $transactieMapping[$row["bankCode"]] = $row["doActie"];
  }
  $transactieCodes = array_unique($transactieCodes);
  sort($transactieCodes);
}

function getFonds($bankcode,$isin, $valuta)
{
  global $set, $data, $error, $row, $fonds,$meldArray;
  $db = new DB();

  $fonds = array();
  if (trim($data["bankCode"]) != "")
  {

    $query = "SELECT * FROM Fondsen 
              WHERE {$set["bankCode"]} = '{$bankcode}' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($isin);

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE 
              ISINCode = '{$ISIN}' AND Valuta ='{$valuta}' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds {$ISIN}/{$valuta} niet gevonden </span>";
      $error[]     = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
      return false;
    }
  }
  else
  {
    $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$bankcode} (zonder ISIN) niet gevonden </span>";
    $error[]      = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
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

  $query = "SELECT * FROM Rekeningen 
            WHERE consolidatie = 0 AND 
                  `RekeningDepotbank` = '{$rekeningNr}' AND 
                  `Depotbank` = '{$depotBank}' ";


  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rekeningNr;
//    return array("rekening" => $rec["Rekening"],
//                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * 
              FROM Rekeningen 
              WHERE consolidatie = 0 AND 
              `Rekening` = '{$rekeningNr}' AND 
              `Depotbank` = '{$depotBank}' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: rekening {$rekeningNr} niet gevonden </span>";
      $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
      return false;
    }
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray, $depotBank;

  $value = "{$depotBank}|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag($afrekenValuta)
{
  global $data, $mr;

  if ($afrekenValuta == $mr["Valuta"] )
  {
    return -1 * $mr["Debet"];
  }
  else
  {
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
}

function _creditbedrag($afrekenValuta)
{
  global $data, $mr;

  if ($afrekenValuta == $mr["Valuta"] )
  {
    return $mr["Credit"];
  }
  else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

}


function _valutakoers($rekValuta, $valutakoers=0)
{
  global $data, $mr;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR")

  {

    return $valutakoers;
  }

  if ( $mr["Valuta"] == $rekValuta)
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];

  }

  return 999;
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

function do_algemeen()
{
  global $mr, $row, $data, $_file;
  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $data["transactieId"];
  $mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];

  if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }


}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $prefix = "regel {$mr["regelnr"]}: {$mr["Rekening"]} --> bedrag";
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".($notabedrag - $controleBedrag);
  }
  else
  {
    $meldArray[] = "{$prefix} sluit aan ";
  }

}

function kostenPosten($expData, $afrVal, $type="")
{
  global $meldArray, $mr, $data, $controleBedrag, $output, $gbMap;

  if ($type == "DIV")
  {
    $gbMap["82"] = "DIVBE";
  }


  $tel = 0;
  foreach ($expData as $kp)
  {
    $tel++;
    if ($kp["transactieCode"] == "")
    {
      continue;
    }

    $gb = $gbMap[$kp["transactieCode"]];

    if ($gb == "")
    {
      $gb = "KOBU";
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$mr["regelnr"]}: Geen grootboek mapping voor kostencategorie {$kp["transactieCode"]}</span>";
    }

    $mr["Grootboekrekening"]  = $gb;
    $mr["Aantal"]             = 0;
    $mr["Fondskoers"]         = 0;
    if ($kp["bedragTransactie"] < 0)
    {
      $mr["Credit"]           = 0;
      $mr["Debet"]            = abs($kp["bedragTransactie"]);
      $mr["Bedrag"]           = _debetbedrag($afrVal);
    }
    else
    {
      $mr["Debet"]            = 0;
      $mr["Credit"]           = abs($kp["bedragTransactie"]);
      $mr["Bedrag"]           = _creditbedrag($afrVal);
    }

    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();
//debug($data);

  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];
  //debug($item);
  $mr["aktie"]             = "A";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["portefeuille"]."MEM") )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Deponerering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $item["aantal"];
  $mr["Fondskoers"]        = $item["koers"];
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($item["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag($item["afrekenValuta"]);

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $controleBedrag         += $mr["Bedrag"];
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  kostenPosten($data["exp"], $item["afrekenValuta"]);

  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();
//debug($data);

  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];
//  debug($item);
  $mr["aktie"]             = "A";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $item["aantal"];
  $mr["Fondskoers"]        = $item["koers"];
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($item["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag($item["afrekenValuta"]);

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $controleBedrag         += $mr["Bedrag"];

  kostenPosten($data["exp"], $item["afrekenValuta"]);

  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AC()  // Aankoop counterparty
{


  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();
//debug($data);

  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];


//  debug($item);
  $mr["aktie"]             = "AC";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Uitgifte ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "STORT";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($item["brutoBedrag"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "AC";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VC()  // Verkoop counterparty
{


  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();
//debug($data);

  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];


//  debug($item);
  $mr["aktie"]             = "VC";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Inkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($item["brutoBedrag"]);
  $mr["Bedrag"]            = _debetbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "VC";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();


  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];

  $mr["aktie"]             = "V";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $item["aantal"];
  $mr["Fondskoers"]        = $item["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  $mr["Credit"]            = abs($item["opgelopenRente"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag($item["afrekenValuta"]);

  if ($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }

  $controleBedrag         += $mr["Bedrag"];

  kostenPosten($data["exp"], $item["afrekenValuta"]);

  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  // Dividend
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();


  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];

  $mr["aktie"]             = "DIV";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($item["aantal"] * $item["koers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  kostenPosten($data["exp"], $item["afrekenValuta"], "DIV");

  checkControleBedrag($controleBedrag,$item["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $_file, $row;
  $controleBedrag = 0;
  $mr = array();

  $item = $data["sxt"][0];
  $item["nettoBedrag"] = $data["fts"][0]["bedrag"];

  //debug($data);
  //debug($item);
  $mr["aktie"]             = "RENOB";

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $item["transactieId"];
  $mr["Boekdatum"]         = $item["boekdatum"];
  $mr["settlementDatum"]   = $item["settledatum"];
  $mr["Rekening"]          = "";
  if ($rekRec  = getRekening($item["rekening"].$item["afrekenValuta"]) )
  {
    $mr["Rekening"] = $rekRec;
  }
  getFonds($item["bankCode"],$item["isin"],$item["fondsValuta"]);

  if (checkVoorDubbelInRM($mr))
  {
    return;
  }
//  debug($mr);
//  debug($fonds);
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($item["afrekenValuta"], $item["valutakoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($item["brutoBedrag"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag($item["afrekenValuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  kostenPosten($data["exp"], $item["afrekenValuta"]);

  checkControleBedrag($controleBedrag, $item["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()  //
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "FX";
  do_algemeen();
//  debug($data);

  $data["aantal"] = ($data["DC"] == "C")?-10:10;

  checkVoorDubbelInRM($mr);

  if ($data["valuta1"] != "EUR" AND $data["valuta2"] != "EUR")
  {

    $valkrs1 = _valutakoersAIRS($data["valuta1"]);

    do_FXVV(array(
      "rek1"      => $data["rekening1"],
      "rek2"      => $data["rekening2"],
      "valuta1"   => $data["valuta1"],
      "valuta2"   => $data["valuta2"],
      "bedrag1"   => $data["bedrag1"],
      "bedrag2"   => $data["bedrag2"],
      "valkoers1" => $valkrs1,
      "valkoers2" => $valkrs1 * $data["fxKoers"]
    ));
    $meldArray[] = "regel ".$mr["regelnr"].": FX boeking zonder EUR handmatig boeken";
    return false;
  }

  if ($data["valuta1"] == "EUR" AND $data["valuta2"] != "EUR")
  {
    if ($rekRec  = getRekening($data["rekening1"].$data["valuta1"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] < 0)
    {
      $mr["Debet"]             = abs($data["bedrag1"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag1"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


    if ($rekRec  = getRekening($data["rekening2"].$data["valuta2"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = abs($data["bedrag1"]/$data["bedrag2"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] > 0)
    {
      $mr["Debet"]             = abs($data["bedrag2"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] ;
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag2"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"] ;
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
///////////////////////////////////////////////////////////////////////////////////////
  if ($data["valuta1"] != "EUR" AND $data["valuta2"] == "EUR")
  {
    if ($rekRec  = getRekening($data["rekening1"].$data["valuta1"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta1"];
    $mr["Valutakoers"]       = abs($data["bedrag2"]/$data["bedrag1"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] < 0)
    {
      $mr["Debet"]             = abs($data["bedrag1"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag1"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += ($mr["Bedrag"] );
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    if ($rekRec  = getRekening($data["rekening2"].$data["valuta2"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    $mr["Valuta"]            = $data["valuta2"];
    $mr["Valutakoers"]       = 1;
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = $data["fxOmschrijving"];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data["aantal"] > 0)
    {
      $mr["Debet"]             = abs($data["bedrag2"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["bedrag2"]);
      $mr["Bedrag"]            = $mr["Credit"] ;
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

  }





}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $afw, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $fts = $data["fts"][0];
//  debug($fts);
//  debug($data["zav"]);
  $controleBedrag = 0;

  $bankControle = 0;

  if (count($data["zav"]) > 0)
  {
    foreach ($data["zav"] as $zav)
    {
      $bankControle = $zav["nettoCash"];
      $row          = $zav["row"];
      $mr["aktie"]             = "GELDMUT";

      $mr["bestand"]           = $_file;
      $mr["regelnr"]           = $row;
      $mr["bankTransactieId"]  = $fts["transactieId"];
      $mr["Boekdatum"]         = $fts["boekdatum"];
      $mr["settlementDatum"]   = $fts["settledatum"];
      $mr["Rekening"]          = "";
      if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
      {
        $mr["Rekening"] = $rekRec;
      }

      if (checkVoorDubbelInRM($mr))
      {
        continue;
      }
      if ($zav["omschrijving"] != "")
      {
        $mr["Omschrijving"]    = $zav["omschrijving"];
      }
      else
      {
        $mr["Omschrijving"]    = $fts["omschrijving"];
      }


      $mr["Valuta"]            = $zav["valuta"];
      $mr["Valutakoers"]       = _valutakoers($zav["valuta"], $zav["valutakoers"]);
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      if ($zav["DC"] == "D")
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Debet"]             = abs($zav["bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr = $afw->reWrite("ONTTR",$mr);
      }
      else
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($zav["bedrag"]);
        $mr["Bedrag"]            = $mr["Credit"];
        $mr = $afw->reWrite("STORT",$mr);
      }


      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag         += $mr["Bedrag"];

      $output[] = $mr;

      $mr["Grootboekrekening"] = "KNBA";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($zav["kosten"]);
      $mr["Bedrag"]            = _debetbedrag($fts["afrekenValuta"]);
      $mr = $afw->reWrite("KNBA",$mr);
      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $controleBedrag         += $mr["Bedrag"];

    }
  }
  else
  {
//    debug($fts);
    $controleBedrag = 0;
    $bankControle = $fts["bedrag"];
    $row          = $fts["row"];
    $mr["aktie"]             = "GELDMUT";

    $mr["bestand"]           = $_file;
    $mr["regelnr"]           = $row;
    $mr["bankTransactieId"]  = $fts["transactieId"];
    $mr["Boekdatum"]         = $fts["boekdatum"];
    $mr["settlementDatum"]   = $fts["settledatum"];
    $mr["Rekening"]          = "";
    if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    if (checkVoorDubbelInRM($mr))
    {
      continue;
    }

    $mr["Omschrijving"]    = $fts["omschrijving"];



    $mr["Valuta"]            = $fts["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($fts["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($fts["DC"] == "D")
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($fts["bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
      $mr = $afw->reWrite("ONTTR",$mr);
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fts["bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr = $afw->reWrite("STORT",$mr);
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;



  }




  checkControleBedrag($controleBedrag,$bankControle);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_R()  //Rente
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $fts = $data["fts"][0];
//  debug($fts);
//  debug($data["zav"]);
  $controleBedrag = 0;

  $bankControle = 0;

  if (count($data["zav"]) > 0)
  {
    foreach ($data["zav"] as $zav)
    {
      $bankControle = $zav["nettoCash"];
      $row          = $zav["row"];
      $mr["aktie"]             = "RENTE";

      $mr["bestand"]           = $_file;
      $mr["regelnr"]           = $row;
      $mr["bankTransactieId"]  = $fts["transactieId"];
      $mr["Boekdatum"]         = $fts["boekdatum"];
      $mr["settlementDatum"]   = $fts["settledatum"];
      $mr["Rekening"]          = "";
      if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
      {
        $mr["Rekening"] = $rekRec;
      }

      if (checkVoorDubbelInRM($mr))
      {
        continue;
      }
      if ($zav["omschrijving"] != "")
      {
        $mr["Omschrijving"]    = $zav["omschrijving"];
      }
      else
      {
        $mr["Omschrijving"]    = $fts["omschrijving"];
      }


      $mr["Valuta"]            = $zav["valuta"];
      $mr["Valutakoers"]       = _valutakoers($zav["valuta"], $zav["valutakoers"]);
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      if ($zav["DC"] == "D")
      {
        $mr["Grootboekrekening"] = "RENTE";
        $mr["Debet"]             = abs($zav["bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
      }
      else
      {
        $mr["Grootboekrekening"] = "RENTE";
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($zav["bedrag"]);
        $mr["Bedrag"]            = $mr["Credit"];
      }


      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag         += $mr["Bedrag"];

      $output[] = $mr;

      $mr["Grootboekrekening"] = "KNBA";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($zav["kosten"]);
      $mr["Bedrag"]            = _debetbedrag($fts["afrekenValuta"]);

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $controleBedrag         += $mr["Bedrag"];

    }
  }
  else
  {
//    debug($fts);
    $controleBedrag = 0;
    $bankControle = $fts["bedrag"];
    $row          = $fts["row"];
    $mr["aktie"]             = "RENTE";

    $mr["bestand"]           = $_file;
    $mr["regelnr"]           = $row;
    $mr["bankTransactieId"]  = $fts["transactieId"];
    $mr["Boekdatum"]         = $fts["boekdatum"];
    $mr["settlementDatum"]   = $fts["settledatum"];
    $mr["Rekening"]          = "";
    if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    if (checkVoorDubbelInRM($mr))
    {
      continue;
    }

    $mr["Omschrijving"]    = $fts["omschrijving"];



    $mr["Valuta"]            = $fts["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($fts["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($fts["DC"] == "D")
    {
      $mr["Grootboekrekening"] = "RENTE";
      $mr["Debet"]             = abs($fts["bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Grootboekrekening"] = "RENTE";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fts["bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;



  }




  checkControleBedrag($controleBedrag,$bankControle);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_KNBA()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $fts = $data["fts"][0];
//  debug($fts);
//  debug($data["zav"]);
  $controleBedrag = 0;

  $bankControle = 0;

  if (count($data["zav"]) > 0)
  {
    foreach ($data["zav"] as $zav)
    {
      $bankControle = $zav["nettoCash"];
      $row          = $zav["row"];
      $mr["aktie"]             = "GELDMUT";

      $mr["bestand"]           = $_file;
      $mr["regelnr"]           = $row;
      $mr["bankTransactieId"]  = $fts["transactieId"];
      $mr["Boekdatum"]         = $fts["boekdatum"];
      $mr["settlementDatum"]   = $fts["settledatum"];
      $mr["Rekening"]          = "";
      if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
      {
        $mr["Rekening"] = $rekRec;
      }

      if (checkVoorDubbelInRM($mr))
      {
        continue;
      }
      if ($zav["omschrijving"] != "")
      {
        $mr["Omschrijving"]    = $zav["omschrijving"];
      }
      else
      {
        $mr["Omschrijving"]    = $fts["omschrijving"];
      }


      $mr["Valuta"]            = $zav["valuta"];
      $mr["Valutakoers"]       = _valutakoers($zav["valuta"], $zav["valutakoers"]);
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      if ($zav["DC"] == "D")
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Debet"]             = abs($zav["bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr = $afw->reWrite("ONTTR",$mr);
      }
      else
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($zav["bedrag"]);
        $mr["Bedrag"]            = $mr["Credit"];
        $mr = $afw->reWrite("STORT",$mr);
      }


      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag         += $mr["Bedrag"];

      $output[] = $mr;

      $mr["Grootboekrekening"] = "KNBA";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($zav["kosten"]);
      $mr["Bedrag"]            = _debetbedrag($fts["afrekenValuta"]);
      $mr = $afw->reWrite("KNBA",$mr);

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $controleBedrag         += $mr["Bedrag"];

    }
  }
  else
  {
//    debug($fts);
    $controleBedrag = 0;
    $bankControle = $fts["bedrag"];
    $row          = $fts["row"];
    $mr["aktie"]             = "KNBA";

    $mr["bestand"]           = $_file;
    $mr["regelnr"]           = $row;
    $mr["bankTransactieId"]  = $fts["transactieId"];
    $mr["Boekdatum"]         = $fts["boekdatum"];
    $mr["settlementDatum"]   = $fts["settledatum"];
    $mr["Rekening"]          = "";
    if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
    {
      $mr["Rekening"] = $rekRec;
    }

    if (checkVoorDubbelInRM($mr))
    {
      continue;
    }

    $mr["Omschrijving"]    = $fts["omschrijving"];



    $mr["Valuta"]            = $fts["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($fts["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "KNBA";
    if ($fts["DC"] == "D")
    {
      $mr["Debet"]             = abs($fts["bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fts["bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("KNBA",$mr);
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;



  }




  checkControleBedrag($controleBedrag,$bankControle);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_BEH()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $fts = $data["fts"][0];
//  debug($fts);
//  debug($data["zav"]);
  $controleBedrag = 0;

  $bankControle = 0;

  if (count($data["zav"]) > 0)
  {
    foreach ($data["zav"] as $zav)
    {
      $bankControle = $zav["nettoCash"];
      $row          = $zav["row"];
      $mr["aktie"]             = "GELDMUT";

      $mr["bestand"]           = $_file;
      $mr["regelnr"]           = $row;
      $mr["bankTransactieId"]  = $fts["transactieId"];
      $mr["Boekdatum"]         = $fts["boekdatum"];
      $mr["settlementDatum"]   = $fts["settledatum"];
      $mr["Rekening"]          = "";
      if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
      {
        $mr["Rekening"] = $rekRec;
      }

      if (checkVoorDubbelInRM($mr))
      {
        continue;
      }
      if ($zav["omschrijving"] != "")
      {
        $mr["Omschrijving"]    = $zav["omschrijving"];
      }
      else
      {
        $mr["Omschrijving"]    = $fts["omschrijving"];
      }


      $mr["Valuta"]            = $zav["valuta"];
      $mr["Valutakoers"]       = _valutakoers($zav["valuta"], $zav["valutakoers"]);
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      if ($zav["DC"] == "D")
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Debet"]             = abs($zav["bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr = $afw->reWrite("ONTTR",$mr);
      }
      else
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($zav["bedrag"]);
        $mr["Bedrag"]            = $mr["Credit"];
        $mr = $afw->reWrite("STORT",$mr);
      }


      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag         += $mr["Bedrag"];

      $output[] = $mr;

      $mr["Grootboekrekening"] = "KNBA";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($zav["kosten"]);
      $mr["Bedrag"]            = _debetbedrag($fts["afrekenValuta"]);
      $mr = $afw->reWrite("KNBA",$mr);

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $controleBedrag         += $mr["Bedrag"];

    }
  }
  else
  {
//    debug($fts);
    $controleBedrag = 0;
    $bankControle = $fts["bedrag"];
    $row          = $fts["row"];
    $mr["aktie"]             = "BEH";

    $mr["bestand"]           = $_file;
    $mr["regelnr"]           = $row;
    $mr["bankTransactieId"]  = $fts["transactieId"];
    $mr["Boekdatum"]         = $fts["boekdatum"];
    $mr["settlementDatum"]   = $fts["settledatum"];
    $mr["Rekening"]          = "";
    if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
    {
      $mr["Rekening"] = $rekRec;
    }


    if (checkVoorDubbelInRM($mr))
    {
      continue;
    }

    $mr["Omschrijving"]    = $fts["omschrijving"];



    $mr["Valuta"]            = $fts["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($fts["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "BEH";
    if ($fts["DC"] == "D")
    {
      $mr["Debet"]             = abs($fts["bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fts["bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("BEH",$mr);
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;



  }




  checkControleBedrag($controleBedrag,$bankControle);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_BEW()
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  $fts = $data["fts"][0];
//  debug($fts);
//  debug($data["zav"]);
  $controleBedrag = 0;

  $bankControle = 0;

  if (count($data["zav"]) > 0)
  {
    foreach ($data["zav"] as $zav)
    {
      $bankControle = $zav["nettoCash"];
      $row          = $zav["row"];
      $mr["aktie"]             = "GELDMUT";

      $mr["bestand"]           = $_file;
      $mr["regelnr"]           = $row;
      $mr["bankTransactieId"]  = $fts["transactieId"];
      $mr["Boekdatum"]         = $fts["boekdatum"];
      $mr["settlementDatum"]   = $fts["settledatum"];
      $mr["Rekening"]          = "";
      if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
      {
        $mr["Rekening"] = $rekRec;
      }

      if (checkVoorDubbelInRM($mr))
      {
        continue;
      }
      if ($zav["omschrijving"] != "")
      {
        $mr["Omschrijving"]    = $zav["omschrijving"];
      }
      else
      {
        $mr["Omschrijving"]    = $fts["omschrijving"];
      }


      $mr["Valuta"]            = $zav["valuta"];
      $mr["Valutakoers"]       = _valutakoers($zav["valuta"], $zav["valutakoers"]);
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      if ($zav["DC"] == "D")
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Debet"]             = abs($zav["bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * $mr["Debet"];
        $mr = $afw->reWrite("ONTTR",$mr);
      }
      else
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($zav["bedrag"]);
        $mr["Bedrag"]            = $mr["Credit"];
        $mr = $afw->reWrite("STORT",$mr);
      }


      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag         += $mr["Bedrag"];

      $output[] = $mr;

      $mr["Grootboekrekening"] = "KNBA";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;


      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($zav["kosten"]);
      $mr["Bedrag"]            = _debetbedrag($fts["afrekenValuta"]);
      $mr = $afw->reWrite("KNBA",$mr);

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $controleBedrag         += $mr["Bedrag"];

    }
  }
  else
  {
//    debug($fts);
    $controleBedrag = 0;
    $bankControle = $fts["bedrag"];
    $row          = $fts["row"];
    $mr["aktie"]             = "BEW";

    $mr["bestand"]           = $_file;
    $mr["regelnr"]           = $row;
    $mr["bankTransactieId"]  = $fts["transactieId"];
    $mr["Boekdatum"]         = $fts["boekdatum"];
    $mr["settlementDatum"]   = $fts["settledatum"];
    $mr["Rekening"]          = "";
    if ($rekRec  = getRekening($fts["rekening"].$fts["afrekenValuta"]) )
    {
      $mr["Rekening"] = $rekRec;
    }



    if (checkVoorDubbelInRM($mr))
    {
      continue;
    }

    $mr["Omschrijving"]    = $fts["omschrijving"];



    $mr["Valuta"]            = $fts["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($fts["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Grootboekrekening"] = "BEW";
    if ($fts["DC"] == "D")
    {
      $mr["Debet"]             = abs($fts["bedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($fts["bedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("BEW",$mr);
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;



  }




  checkControleBedrag($controleBedrag,$bankControle);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_FXVV($parm = array())
{
  /*
   * parm rek1, rek2, valuta1, valuta2, bedrag1, bedrag2, valkoers1, valkoers2
   */
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();

  $rek1     = $parm["rek1"];
  $rek2     = $parm["rek2"];
  $valuta1  = $parm["valuta1"];
  $valuta2  = $parm["valuta2"];
  $bedrag1  = $parm["bedrag1"];
  $bedrag2  = $parm["bedrag2"];
  $valKoers1 = $parm["valkoers1"];
  $valKoers2 = $parm["valkoers2"];

  $mr["Rekening"]          = $rek1.$valuta1;
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Valuta"]            = $valuta1;
  $mr["Valutakoers"]       = abs($valKoers1);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = "Valutatransactie $valuta1/$valuta2";
  $mr["Grootboekrekening"] = "KRUIS";

  if ($bedrag1 < 0)
  {
    $mr["Debet"]             = abs($bedrag1);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]);
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag1);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Rekening"]          = $rek2.$valuta2;
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $valuta2;
  $mr["Valutakoers"]       = abs($valKoers2);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = "Valutatransactie $valuta1/$valuta2";
  $mr["Grootboekrekening"] = "KRUIS";

  if ($bedrag2 < 0)
  {
    $mr["Debet"]             = abs($bedrag2);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]);
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag2);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,0);

}

function _valutakoersAIRS($fondsValuta)
{
  global $fonds, $data, $mr, $valutaLookup, $DB, $kostenFactor;

  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$fondsValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  $kostenFactor = 1;
  return $laatsteKoers["Koers"];
}


function do_NVT()
{
  return true;
}

function do_error()
{
  global $transcode;
  echo "<BR>FOUT transactiecode: <b>$transcode</b> is (nog) niet ingeregeld!";
}


