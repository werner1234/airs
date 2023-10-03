<?
/*
    AE-ICT sourcemodule created 21 apr. 2021
    Author              : Chris van Santen
    Filename            : saxo_functies.php


*/

function mapDataFields()
{
  global $data, $row;
  $data["rekening"]               = $data[1];
  $data["afrekenValuta"]          = $data[3];
  $data["portefeuille"]           = $data[1];
  $data["isin"]                   = $data[26];
  $data["symbool"]                = $data[27];
  $data["bankCode"]               = $data[28];
  $data["omschrijving"]           = ($data[46] != "")?$data[46]:(($data[29] != "")?$data[29]:$data[22]);
  $data["boekdatum"]              = _cnvDate($data[8]);
  $data["settledatum"]            = _cnvDate($data[9]);
  $data["aantal"]                 = _cnvNumber($data[16]);
  $data["nettoBedrag"]            = _cnvNumber($data[18]);
  $data["nettoCash"]              = _cnvNumber($data[19]);
  $data["nettoControle"]          = _cnvNumber($data[19]);
  $data["valutakoers"]            = (_cnvNumber($data[35]) == 0)?_cnvNumber($data[14]):_cnvNumber($data[35]);
  $data["soort"]                  = $data[24]; //
  $data["transactieId"]           = $data[23];
  $data["transactieCode"]         = $data[12]; //
  $data["transactieType"]         = $data[13]; //
  $data["transactieCodeDetail"]   = trim($data[21]); //[21] is most specific , creates alot of Transaction Codes
  $data["storno"]                 = $data[38];
  $data["stornoId"]               = $data[37];
  $data["fondsValuta"]            = $data[15];
  $data["koers"]                  = _cnvNumber($data[17]);
  $data["row"]                    = $row;
  $data["eenheid"]                = $data[41];
  $data["optieOC"]                = $data[39];
  $data["optieCallPut"]           = $data[30];



  $soortArray = array(
    "Shares",
    "Mutual Funds"
  );
  if (in_array($data["soort"], $soortArray) AND $data["fondsValuta"] == "GBP")
  {
    $data["koers"] = $data["koers"] * $data["eenheid"];   // als koers in Pence
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
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
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

function getFonds()
{
  global $set, $data, $error, $row, $fonds,$meldArray;
  $db = new DB();

  if ($data["bankCode"] != "" AND
    $data["bankCode"] != "0" AND
    strtolower($data["soort"])  != "cash"  AND
    $data["soort"] != "" )
  {

    $fonds = array();
    $query = "SELECT * FROM Fondsen WHERE {$set["bankCode"]} = '".trim($data["bankCode"])."' ";
//debug($query);
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }


    $ISIN = trim($data["isin"]);

    if($ISIN != "" )
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$ISIN}' AND Valuta ='{$data["fondsValuta"]}' ";
  //    debug($query, "ISIN");

      if ($fonds = $db->lookupRecordByQuery($query))
      {
        return true;
      }
      else
      {
        $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$data["row"]}: fonds {$ISIN}/{$data["fondsValuta"]} niet gevonden </span>";
        $error[]     = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
        return false;
      }
    }
    else
    {
      $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$data["row"]}: fonds bankcode {$data["bankCode"]} (zonder ISIN) niet gevonden </span>";
      $error[]      = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
      return false;
    }
  }

}

function getAirsRekeningen()
{
  global $rekeningenAirs;
  $rekeningenAirs = array();
  $query = "
    SELECT 
 		 CASE WHEN rekeningDepotbank <> '' THEN 
				rekening 
			ELSE 
			  rekening 
			END AS 'importRek',
			Portefeuille
		
  FROM 
    `Rekeningen` 
  WHERE 
    `consolidatie` = 0 AND
    (`Depotbank` = 'SAXO'  OR `Depotbank` = 'SAXOB') 
";
  $db = new DB();
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    if ($rec["importRek"] != "")
    {
      $rekeningenAirs[] = $rec["importRek"];
    }
  }
//debug($rekeningenAirs);
}

function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank, $meldArray, $rekeningenAirs;

  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }

  if (in_array($rekeningNr, $rekeningenAirs))
  {
    return $rekeningNr;
  }
  else
  {
    $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$data["row"]}: rekening {$rekeningNr} niet gevonden </span>";
    $error[]     = "{$row}: rekening {$rekeningNr} niet gevonden ";
    return false;
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

function _debetbedrag()
{
  global $data, $mr;

  if ($data["afrekenValuta"] == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return  -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return -1 * $mr["Debet"];
  }
  else
  {
    return  ($mr["Debet"] * 1/$mr["Debet"]);
  }

}

function _creditbedrag()
{

  global $data, $mr;


  if ($data["afrekenValuta"] == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return  ($mr["Credit"] * $mr["Valutakoers"]);
  }
  else if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return $mr["Credit"];
  }
  else
  {
    return  ($mr["Credit"] * 1/$mr["Valutakoers"]);
  }
}

function _valutakoersCash($rekValuta = "")
{
  global $data, $mr, $valutaLookup, $meldArray;

  $fondsValuta = $mr["Valuta"];
  if ($rekValuta == "") {$rekValuta = $data["afrekenValuta"]; }
  if ($rekValuta == "EUR" )
  {
    return 1;
  }

  if ($rekValuta != "EUR" AND $fondsValuta == "EUR" )
  {
    return 1/$data[35];
  }

  return $data["valutakoers"];

}

function _valutakoers2($rekValuta="")
{
  global $data, $mr, $valutaLookup, $meldArray;

  $fondsValuta = $mr["Valuta"];
  if ($rekValuta == "") {$rekValuta = $data["afrekenValuta"]; }
  if ($rekValuta == "EUR" AND $fondsValuta == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $fondsValuta != "EUR" )
  {
    return $data[35];
  }

  if ($rekValuta != "EUR" AND $fondsValuta == "EUR" )
  {
    $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]},EUR boeking t.l.v. VV, graag controleren </span>";
    return $data[35];
  }

  if ($rekValuta == $fondsValuta)
  {
    if ($data[14] != 0)
    {
      return $data[14];
    }
    else
    {
      $db = new DB();
      $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta = '{$rekValuta}' AND Datum <= '{$mr["Boekdatum"]}' 
      ORDER BY 
        Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
  }

  return $data[14];

}

function _valutakoersAirs()
{
  global $data, $mr, $meldArray;
  $db = new DB();
  $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta = '{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' 
      ORDER BY 
        Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}

function _valutakoers()
{
  global $data, $mr;
  return $data["valutakoers"];
}




function checkVoorDubbelInRM_saxo($mr)
{
  global $meldArray, $mr;
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
    $meldArray[] = "regel {$mr["regelnr"]}: rekenmutatie is al aanwezig (AIRSid = {$rec["id"]} / bankId= {$mr["bankTransactieId"]})";
    return true;
  }
  return false;
}

function do_algemeen()
{
	global $mr, $row, $data, $_file, $fonds;
//debug($data);
  if (trim($data["transactieId"]) == "")
  {
    $data["transactieId"] = "{$data["transactieCodeDetail"]}::{$data["nettoBedrag"]}-{$data["transactieCode"]}";
  }

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $data["row"];
	$mr["bankTransactieId"]  = $data["transactieId"];
	$mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];

	if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }

	if ($data["isin"] != "")
  {
    $fonds = getFonds();
  }

}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;

  $prefix         = "regel {$mr["regelnr"]}: {$mr["Rekening"]} --> bedrag";
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;

  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "{$prefix} sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".($notabedrag - $controleBedrag);
  }
  else
  {
    $meldArray["verschil"][] = "{$prefix} sluit niet aan bank= {$notabedrag} / AIRS = {$controleBedrag} / verschil = ".($notabedrag - $controleBedrag);
  }


}





function kostenPosten($grootboek = "", $rekening="")
{
  global $meldArray, $mr, $data, $controleBedrag, $output, $fonds;


  if ($rekening != "")
  {
    if (!$rekRec = getRekening($rekening))
    {
      return;
    }
    else
    {
      $mr["Rekening"]      = $rekening;
    }
  }

  if ($grootboek == "")
  {
    return;
  }


  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers2();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $mr["Grootboekrekening"]  = $grootboek;
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Credit"]           = 0;
    $mr["Debet"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]           = _debetbedrag();
  }
  else
  {
    $mr["Debet"]            = 0;
    $mr["Credit"]           = abs($data["nettoBedrag"]);
    $mr["Bedrag"]           = _creditbedrag();
  }

  $controleBedrag           = $mr["Bedrag"];

  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {

    $output[] = $mr;
    checkControleBedrag($controleBedrag, $data["nettoControle"]);
  }
//  debug($output, "output row {$grootboek}");

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OPT_A()  // Aankoop van opties
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $mr = array();
  $mr["aktie"]             = "OPTA";
  do_algemeen();
  getFonds();

  switch (strtolower($data["optieOC"]))
  {
    case "open":
      $mr["Omschrijving"]      = "Aankoop open ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "A/O";
      break;
    case "close":
      $mr["Omschrijving"]      = "Aankoop sluiten ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "A/S";

      break;
    default:
      $mr["Omschrijving"]      = "Aankoop open ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "A/O";
      $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$data["row"]}: 
      Open/Close onbekend, geboekt als aankoop open </span>";
  }
//  debug($fonds, "Fonds");
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers2();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"] ;
  $mr["Debet"]             = $data["aantal"] * $data["koers"] * $fonds["Fondseenheid"];
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag          = $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag, $data["nettoControle"]);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OPT_V()  // Verkoop van opties
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $mr = array();
  $mr["aktie"]             = "OPTV";
  do_algemeen();
  getFonds();

  switch (strtolower($data["optieOC"]))
  {
    case "open":
      $mr["Omschrijving"]      = "Verkoop open ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "V/O";
      break;
    case "close":
      $mr["Omschrijving"]      = "Verkoop sluiten ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "V/S";

      break;
    default:
      $mr["Omschrijving"]      = "Verkoop open ".$fonds["Omschrijving"];
      $mr["Transactietype"]    = "V/O";
      $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$data["row"]}: 
      Open/Close onbekend, geboekt als verkoop open </span>";
  }

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers2();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"] ;
  $mr["Credit"]            = abs($data["aantal"] * $data["koers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag          = $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag, $data["nettoControle"]);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;

  $dataSet = $data;

  foreach ($dataSet as $data)
  {
    $skipLoop =false;
    switch ($data["soort"])
    {
      case "Cash":
        $skipLoop = true;
        do_GELDMUT();
        break;
      default:
    }
    if ($skipLoop)
    {
      continue;
    }
    $mr = array();
    $mr["aktie"]             = "A";
    do_algemeen();
    if (checkVoorDubbelInRM_saxo()) { return; }
    getFonds();
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];

    $grootboek = "";
    switch ($data["transactieCodeDetail"])
    {
      case "1":
        $meldArray[] = "
          <span style='color: maroon; font-weight: bold'>{$data["row"]}:afgebroken, 
          (do_A) Future transactie handmatig boeken</span>";
        continue;
        break;
      case "12":
        do_OPT_A();
        continue;
        break;
      case "2":
      case "89":
      case "304":
      case "131":
        $grootboek = "FONDS";
        break;
      case "90":
        $grootboek = "RENME";
        break;
      case "4":
      case "518":
        kostenPosten("KOST");
        break;
      case "21":
        do_GELDMUT("Partner commission");
        break;
      case "0":
      case "13":
      case "30":
      case "154":
      case "155":
      case "156":
      case "160":
      case "161":
      case "164":
      case "165":
      case "169":
      case "291":
      case "373":
      case "391":
      case "392":
      case "393":
      case "406":
        kostenPosten("KOBU");
        break;
      case "":
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:overgeslagen, (do_A) BKamount type is leeg </span>";
        break;
      default:
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_A) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        kostenPosten("KOBU");
        continue;
    }


    if ($grootboek != "")
    {
      $mr["Grootboekrekening"] = $grootboek;
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers2();
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = $data["aantal"];
      $mr["Fondskoers"]        = $data["koers"];
      switch($grootboek)
      {
        case "RENME":
          $mr["Debet"]       = abs($data["nettoBedrag"]);
          $mr["Aantal"]      = 0;
          $mr["Fondskoers"]  = 0;
          break;
        default:
          $mr["Debet"] = abs($mr["Aantal"]* $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      }
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag          = $mr["Bedrag"];
      $mr["Transactietype"]    = "A";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;

      $output[] = $mr;

      checkControleBedrag($controleBedrag, $data["nettoControle"]);
    }

  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Vernkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;

  $dataSet = $data;

  foreach ($dataSet as $data)
  {

    if ($data["soort"] == "Cash")
    {
      do_GELDMUT();
      continue;
    }
    $mr = array();
    $mr["aktie"]             = "V";
    do_algemeen();
    getFonds();
    if (checkVoorDubbelInRM_saxo()) { return; }
    $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];

    $grootboek = "";
    switch ($data["transactieCodeDetail"])
    {
      case "1":
        $meldArray[] = "
          <span style='color: maroon; font-weight: bold'>{$data["row"]}:afgebroken, 
          (do_V) Future transactie handmatig boeken</span>";
        continue;
        break;

      case "12":
        do_OPT_V();
        continue;
        break;
      case "2":
      case "89":
      case "304":
      case "131":
        $grootboek = "FONDS";
        break;
      case "90":
        $grootboek = "RENOB";
        break;
      case "4":
      case "518":
        kostenPosten("KOST");
        break;
      case "21":
        do_GELDMUT("Partner commission");
        break;
      case "0":
      case "13":
      case "30":
      case "154":
      case "155":
      case "156":
      case "160":
      case "161":
      case "164":
      case "165":
      case "169":
      case "291":
      case "373":
      case "391":
      case "392":
      case "393":
      case "406":
        kostenPosten("KOBU");
        break;
      case "":
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:overgeslagen, (do_V) BKamount type is leeg </span>";
        break;
      default:
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_V) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        kostenPosten("KOBU");
        continue;
    }


    if ($grootboek != "")
    {

      $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = $grootboek;
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers2();
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = $data["aantal"];
      $mr["Fondskoers"]        = $data["koers"];
      switch($grootboek)
      {
        case "RENOB":
          $mr["Credit"]      = abs($data["nettoBedrag"]);
          $mr["Aantal"]      = 0;
          $mr["Fondskoers"]  = 0;
          break;
        default:
          $mr["Credit"] = abs($mr["Aantal"]* $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      }
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag          = $mr["Bedrag"];
      $mr["Transactietype"]    = "V";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;

      $output[] = $mr;

      checkControleBedrag($controleBedrag, $data["nettoControle"]);
    }
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  // Lichting van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "L";
  do_algemeen();
  if (!$rekRec  = getRekening($data["rekening"]."MEM") )
  {
    return false;
  }
  $mr["Rekening"] = $rekRec;
  getFonds();
  if (checkVoorDubbelInRM_saxo()) { return; }

  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers2("EUR");
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $data["eenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  if ($mr["Aantal"] != 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fonds"]             = "";
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($mr["Bedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  switch ($data["transactieCodeDetail"])
  {
    case "57":
      kostenPosten("KNBA", $data["rekening"].$data["afrekenValuta"]);
      break;
    case "406":
      kostenPosten("KOBU", $data["rekening"].$data["afrekenValuta"]);
      break;
    case "":
      break;
    default:
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_D) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
      kostenPosten("KOBU", $data["rekening"].$data["afrekenValuta"]);
      continue;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EXCASS()  // Exercise-Assignment
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;

  $dataSet = $data;
//  debug($data);
  reset($dataSet);
  $data = current($dataSet);

  $mr = array();
  $mr["aktie"]             = "EXCASS";
  do_algemeen();
  if (!$rekRec  = getRekening($data["rekening"]."MEM") )
  {
    return false;
  }
  $mr["Rekening"] = $rekRec;
  getFonds();
  if (checkVoorDubbelInRM_saxo()) { return; }

  $mr["Omschrijving"]      = $data["transactieCode"]." ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoersAirs();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = ($mr["Aantal"] < 0)?"V/S":"A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  if ($data["transactieCodeDetail"] != "21")
  {
    $output[] = $mr;
  }


  $rekRec  = getRekening();
  $mr["Rekening"] = $rekRec;
  switch ($data["transactieCodeDetail"])
  {
    case "4":
      kostenPosten("KOST");
      break;
    case "21":
      do_GELDMUT("Partner commission");
      break;
    case "30":
    case "154":
    case "155":
    case "160":
    case "164":
    case "391":
      kostenPosten("KOBU");
      break;
    default:
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_EXCASS) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
      kostenPosten("KOBU");
      break;
  }


  checkControleBedrag($controleBedrag,$data["nettoControle"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EXP()  // Expiratie
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;

  $dataSet = $data;

  reset($dataSet);
  $data = current($dataSet);

  $mr = array();
  $mr["aktie"]             = "EXP";
  do_algemeen();
  if (!$rekRec  = getRekening($data["rekening"]."MEM") )
  {
    return false;
  }
  $mr["Rekening"] = $rekRec;
  getFonds();
  if (checkVoorDubbelInRM_saxo()) { return; }

  $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers2("EUR");
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = ($mr["Aantal"] < 0)?"V/S":"A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  // Lichting van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;


    $mr = array();
    $mr["aktie"] = "D";
    do_algemeen();

    if (!$rekRec = getRekening($data["rekening"] . "MEM"))
    {
      return false;
    }

    $mr["Rekening"] = $rekRec;
    getFonds();
    if (checkVoorDubbelInRM_saxo())
    {
      return;
    }

    $mr["Omschrijving"] = "Deponering " . $fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"] = $fonds["Valuta"];
    $mr["Valutakoers"] = _valutakoers2("EUR");
    $mr["Fonds"] = $fonds["Fonds"];
    $mr["Aantal"] = $data["aantal"];
    $mr["Fondskoers"] = $data["koers"];
    $mr["Credit"] = 0;
    $mr["Debet"] = abs($mr["Aantal"] * $mr["Fondskoers"] * $data["eenheid"]);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];
    $mr["Transactietype"] = "D";
    $mr["Verwerkt"] = 0;
    $mr["Memoriaalboeking"] = 1;

    if ($mr["Aantal"] != 0)
    {
      $output[] = $mr;
    }


    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"] = "";
    $mr["Valuta"] = "EUR";
    $mr["Valutakoers"] = 1;
    $mr["Aantal"] = 0;
    $mr["Fonds"] = "";
    $mr["Fondskoers"] = 0;
    $mr["Debet"] = 0;
    $mr["Credit"] = abs($mr["Bedrag"]);
    $mr["Bedrag"] = $mr["Credit"];
    $controleBedrag += $mr["Bedrag"];
    $mr["Transactietype"] = "";

    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

  switch ($data["transactieCodeDetail"])
  {
    case "57":
      kostenPosten("KNBA", $data["rekening"].$data["afrekenValuta"]);
      break;
    case "406":
      kostenPosten("KOBU", $data["rekening"].$data["afrekenValuta"]);
      break;
    case "":
      break;
    default:
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_D) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
      kostenPosten("KOBU", $data["rekening"].$data["afrekenValuta"]);
      continue;
  }


  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $dataSet = $data;


  foreach ($dataSet as $data)
  {
    $mr = array();
    $controleBedrag = 0;

    $mr["aktie"] = "DIV";
    do_algemeen();
    getFonds();
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    if (checkVoorDubbelInRM_saxo()) { return; }

    $grootboek = "";
    switch ($data["transactieCodeDetail"])
    {

      case "137":
        $grootboek = "ROER";
        break;
      case "56":
        $grootboek = "DIV";
        break;
      case "55":
      case "166":
        kostenPosten("DIVBE");
        break;
      default:
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_DIV) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        kostenPosten("KOBU");
        break;
    }
  }

  if ($grootboek != "")
  {

    $mr["Grootboekrekening"] = $grootboek;

    $mr["Valuta"]            = $data["fondsValuta"];
    $mr["Valutakoers"]       = _valutakoers2();
    $mr["Fonds"]             =  $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $bBedrag = $data["nettoBedrag"];
    if ($bBedrag > 0)
    {
      $mr["Credit"]            = abs($bBedrag);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Debet"]            = abs($bBedrag);
      $mr["Credit"]             = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }

    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data["nettoControle"]);
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CORP()
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $dataSet = $data;

  foreach ($dataSet as $data)
  {
    $mr = array();
    $controleBedrag = 0;

    $mr["aktie"] = "RENOB";
    do_algemeen();

    if (checkVoorDubbelInRM_saxo()) { return; }
    getFonds();
    $grootboek = "";
    switch ($data["transactieCodeDetail"])
    {

      case "57":
        $grootboek = "VKSTO";
        $mr["Omschrijving"] = "Fractieverrekening  ". $fonds["Omschrijving"];
        break;
      default:
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_CORP) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        kostenPosten("KOBU");
        break;
    }
  }

  if ($grootboek != "")
  {

    $mr["Grootboekrekening"] = $grootboek;

    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = $data[35];
    $mr["Fonds"]             =  $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;


    if ($data["nettoBedrag"] > 0)
    {
      $mr["Credit"]            = abs($data["nettoBedrag"]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Debet"]            = abs($data["nettoBedrag"]);
      $mr["Credit"]             = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }

    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;

    checkControleBedrag($controleBedrag,$data["nettoControle"]);
  }
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Coupon
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $dataSet = $data;

  foreach ($dataSet as $data)
  {
    $mr = array();
    $controleBedrag = 0;

    $mr["aktie"] = "RENOB";
    do_algemeen();
    $mr["Omschrijving"] = "Coupon " . $fonds["Omschrijving"];
    if (checkVoorDubbelInRM_saxo()) { return; }
    getFonds();
    $grootboek = "";
    switch ($data["transactieCodeDetail"])
    {

      case "91":
      case "97":
        $grootboek = "RENOB";
        break;
      case "109":
        kostenPosten("KOBU");
        break;
      case "328":
        kostenPosten("ROER");
        break;
      default:
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_RENOB) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        kostenPosten("KOBU");
        break;
    }
  }

  if ($grootboek != "")
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = $grootboek;

    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers2();
    $mr["Fonds"]             =  $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;


    if ($data["nettoBedrag"] > 0)
    {
      $mr["Credit"]            = abs($data["nettoBedrag"]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Debet"]            = abs($data["nettoBedrag"]);
      $mr["Credit"]             = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }

    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;

    checkControleBedrag($controleBedrag,$data["nettoControle"]);
  }
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

//function do_FX()
//{
//  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
//  $controleBedrag = 0;
//  $mr = array();
//
//
//  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
//  do_algemeen();
//
////  debug($data);
//  $valuta1  = $data[20];
//  $valuta2  = $data[41];
//  $rp = explode("/", $data[19]);
//  $rek1 = $rp[0];
//  $rp = explode("/", $data[40]);
//  $rek2 = $rp[0];
//  if ($valuta1 != "EUR" AND $valuta2 != "EUR")
//  {
//    $meldArray[] = "regel ".$data["row"].": Forex in VV {$valuta1}/{$valuta2}, deze handmatig boeken";
//    return;
//  }
//
//  if ($valuta1 == "EUR")
//  {
//    $pootEUR = array(
//      "rekening" => $rek1,
//      "valuta"  => $data[20],
//      "bedrag" => $data[32],
//    );
//    $pootVV = array(
//      "rekening" => $rek2,
//      "valuta"  => $data[41],
//      "bedrag" => $data[43],
//    );
//  }
//  else
//  {
//    $pootEUR = array(
//      "rekening" => $rek2,
//      "valuta"  => $data[41],
//      "bedrag" => $data[43],
//    );
//    $pootVV = array(
//      "rekening" => $rek1,
//      "valuta"  => $data[20],
//      "bedrag" => $data[32]
//    );
//  }
//  $wKoers = abs($pootEUR["bedrag"]/$pootVV["bedrag"]);
////  debug($pootEUR,"pootEUR");
////  debug($pootVV,"pootVV");
//// poot 1 boeken
//
//  $mr["Rekening"]          = $pootEUR["rekening"].$pootEUR["valuta"];
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
//  if ( checkVoorDubbelInRM_saxo($mr) )
//  {
//    return true;
//  }
//  $mr["Valuta"]            = $pootVV["valuta"];
//  $mr["Valutakoers"]       = $wKoers;
//  $mr["Fonds"]             = "";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Omschrijving"]      = $data["omschrijving"]." {$data[20]}/{$data[41]}";
//  $mr["Grootboekrekening"] = "KRUIS";
//
//  if ($pootEUR["bedrag"] < 0)
//  {
//    $mr["Debet"]             = abs($pootVV["bedrag"]);
//    $mr["Credit"]            = 0;
//    $mr["Bedrag"]            = (-1 * $mr["Debet"]) * $mr["Valutakoers"];
//  }
//  else
//  {
//    $mr["Debet"]             = 0;
//    $mr["Credit"]            = abs($pootVV["bedrag"]);
//    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
//  }
//
//  $mr["Transactietype"]    = "";
//  $mr["Verwerkt"]          = 0;
//  $mr["Memoriaalboeking"]  = 0;
//  $controleBedrag         += abs($mr["Bedrag"]);
//
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  // poot 2 boeken
//
//  $mr["Rekening"]          = $pootVV["rekening"].$pootVV["valuta"];
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
//  $mr["Valuta"]            = $pootVV["valuta"];
//  $mr["Valutakoers"]       = $wKoers;
//  $mr["Fonds"]             = "";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Grootboekrekening"] = "KRUIS";
//
//  if ($pootEUR["bedrag"] > 0)
//  {
//    $mr["Debet"]             = abs($pootVV["bedrag"]);
//    $mr["Credit"]            = 0;
//    $mr["Bedrag"]            = -1 * $mr["Debet"];
//  }
//  else
//  {
//    $mr["Debet"]             = 0;
//    $mr["Credit"]            = abs($pootVV["bedrag"]);
//    $mr["Bedrag"]            = $mr["Credit"];
//  }
//
//  $mr["Transactietype"]    = "";
//  $mr["Verwerkt"]          = 0;
//  $mr["Memoriaalboeking"]  = 0;
//  $controleBedrag         += abs($mr["Bedrag"]);
//
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  checkControleBedrag($controleBedrag,abs($data[32])+abs($data[43]));
//
//}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////




function do_R()  //rente
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $dataSet = $data;

  foreach ($dataSet as $data)
  {
    $mr = array();
    $controleBedrag = 0;

    $mr["aktie"] = "RENTE";
    do_algemeen();

    if (checkVoorDubbelInRM_saxo()) { return; }
    getFonds();
    if ($fonds["Omschrijving"] != "")
    {
      $mr["Omschrijving"] = "Coupon " . $fonds["Omschrijving"];
    }
    else
    {
      $mr["Omschrijving"] = $data["omschrijving"];
    }
    switch ($data["transactieCodeDetail"])
    {

      case "47":
      case "262":
        $grootboek = "RENTE";
        $mr["Omschrijving"] = $data["omschrijving"];
        break;
      default:
        $grootboek = "RENTE";
        $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$data["row"]}:controleren, (do_R) BKamount type niet ingeregeld ({$data["transactieCodeDetail"]}) </span>";
        break;
    }

    $controleBedrag = 0;

    $mr["Valuta"]            = $data["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers2();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($data["nettoCash"] < 0)
    {
      $mr["Grootboekrekening"] = $grootboek;
      $mr["Debet"]             = abs($data["nettoCash"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }
    else
    {
      $mr["Grootboekrekening"] = $grootboek;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["nettoCash"]);
      $mr["Bedrag"]            = _creditbedrag();
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];

    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data["nettoControle"]);

  }



}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_BEH()   { do_MUT("BEH");  }
function do_KNBA()  { do_MUT("KNBA"); }
function do_BEW()   { do_MUT("BEW");  }
function do_KOBU()  { do_MUT("KOBU"); }



function do_MUT($grootboek)  //beheerfee
{

  global $data, $mr, $output, $meldArray, $controleBedrag, $__debug, $afw;

//  debug($data);
  // als dataset dan de eerste als $data gebruiken
  if (count($data) < 10)
  {
    reset($data);
    $data = current($data);
  }

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if (checkVoorDubbelInRM_saxo()) { return; }

  $controleBedrag = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = $grootboek;
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Grootboekrekening"] = $grootboek;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = _creditbedrag();
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr = $afw->reWrite($grootboek,$mr);

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data["nettoControle"]);

}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT()
{
  global $data, $mr, $output, $meldArray, $controleBedrag, $__debug, $afw;

  // als dataset dan de eerste als $data gebruiken
  if (count($data) < 10)
  {
    reset($data);
    $data = current($data);
  }

  if ($data["aantal"] > 0)
  {

    do_D();
  }
  else
  {

    do_L();
  }

}


function do_GELDMUT($oms="")  //mutatie geld
{

  global $data, $mr, $output, $meldArray, $controleBedrag, $__debug, $afw;

  // als dataset dan de eerste als $data gebruiken
  if (count($data) < 10)
  {
    reset($data);
    $data = current($data);
  }

  $mr = array();
  $mr["aktie"]              = "GELDMUT";
  do_algemeen();
  if (checkVoorDubbelInRM_saxo()) { return; }

  $controleBedrag = 0;
  if ($oms == "")
  {
    $mr["Omschrijving"]      = $data["omschrijving"];
  }
  else
  {
    $mr["Omschrijving"]      = $oms;
  }


  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoersCash($data[""]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["nettoCash"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoCash"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr = $afw->reWrite("ONTTR",$mr);
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoCash"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr = $afw->reWrite("STORT",$mr);
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data["nettoControle"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  return true;
}

function do_error()
{
	global $transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


