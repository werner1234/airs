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

function mapDataFields()
{
  global $data, $row;


  $data["rekening"]         = $data[2];
  $data["afrekenValuta"]    = $data[10];
  $data["portefeuille"]     = $data[2];

  $data["isin"]             = $data[4];
  $data["bankCode"]         = $data[3];
//  $data["eenheid"]          = $data[13];
  $data["transactieCode"]   = $data[16];
  $data["boekdatum"]        = _cnvDate($data[12]);
  $data["settledatum"]      = _cnvDate($data[14]);
  $data["aantal"]           = _cnvNumber($data[17]);
  $data["koers"]            = _cnvNumber($data[18]);
  $data["transactieId"]     = $data[37];
  $data["omschrijving"]     = $data[27];
  $data["fondsValuta"]      = $data[10];
  $data["row"]              = $row;
  $data["brutoBedrag"]      = _cnvNumber($data[19]);
  $data["nettoBedrag"]      = _cnvNumber($data[23]);
  $data["valutakoers"]      = _cnvNumber($data[28]);
  // $data["storno"]           = ($data[40] == );
  //$data["stornoId"]         = $data[9];

//  $data["nettoCash"]        = _cnvNumber($data[14]);
////  $data["valutakoers"]      = 999;
//  $data["valutakoers"]      = 1/_cnvNumber($data[10]);

////  $data["bedragCashMut"]    = $data[32];

  $data["kostenPosten"][0]  = array(
    "categorie"     => "Commission",
    "valuta"        => $data[10],
    "bedrag"        => $data[21],
    "omschrijving"  => "" );

  $data["kostenPosten"][1]  = array(
    "categorie"     => "SECFee",
    "valuta"        => $data[10],
    "bedrag"        => $data[20],
    "omschrijving"  => "" );

  $data["kostenPosten"][2]  = array(
    "categorie"     => "Tax",
    "valuta"        => $data[10],
    "bedrag"        => $data[22],
    "omschrijving"  => "" );



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

  $fonds = array();
  if (trim($data["bankCode"]) != "")
  {

    $query = "SELECT * FROM Fondsen 
              WHERE {$set["bankCode"]} = '".trim($data["bankCode"])."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data["isin"]);

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE 
              ISINCode = '{$ISIN}' AND Valuta ='{$data["fondsValuta"]}' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $meldArray[] = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds {$ISIN}/{$data["fondsValuta"]} niet gevonden </span>";
      $error[]     = "$row: fonds $ISIN/".$data["fondsValuta"]." niet gevonden ";
      return false;
    }
  }
  else
  {
    $meldArray[]  = "<span style='color:maroon; font-weight: bold;'>regel {$row}: fonds bankcode {$data["bankCode"]} (zonder ISIN) niet gevonden </span>";
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
                  `Depotbank` = '".$depotBank."' ";


  if ($rec = $db->lookupRecordByQuery($query) )
  {
    return $rekeningNr;
//    return array("rekening" => $rec["Rekening"],
//                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE 
              consolidatie = 0 AND 
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

function _debetbedrag()
{
	global $data, $mr;

	if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return -1 * $mr["Debet"];
  }
	else
  {
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return $mr["Credit"];
  }
	else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

}


function _valutakoers($rekValuta)
{
  global $data, $mr;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if (  ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR") OR
        ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta) )
  {
    if ($data["valutakoers"] == 1)
    {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='{$mr["Valuta"]}' AND Datum <= '{$mr["Boekdatum"]}' ORDER BY Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
    else
    {
      return $data["valutakoers"];
    }

  }
  return 1/$data["valutakoers"];

//  if ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta)
//  {
//      return 1/$data["valutakoers"];
//  }
//  else
//  {
//    return 9999;
//  }

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

function kostenPosten($gbMap = "", $FXcase=false)
{
  global $meldArray, $mr, $data, $controleBedrag, $output;
  if ($gbMap == "")
  {
    $gbMap = array(
      "Commission"   => "KOST",
      "SECFee"       => "KOBU",
      "Tax"          => "TOB",
    );
  }

  $tel = 0;
  foreach ($data["kostenPosten"] as $kp)
  {
//    debug($kp,$kp["categorie"]);

    if ($FXcase)
    {
      $kp["valuta"] = $data[11];
    }

    $tel++;
    if ($kp["categorie"] == "")
    {
      continue;
    }

    $gb = $gbMap[$kp["categorie"]];
//    debug($kp, $gb);

    if ($gb == "")
    {
      $gb = "KNBA";
      $meldArray[] = "<span style='color: maroon; font-weight: bold'>{$mr["regelnr"]}: Geen grootboek mapping voor kostencategorie {$kp["categorie"]}</span>";
    }

    $mr["Valuta"]             = $kp["valuta"];

    $mr["Valutakoers"]        = _valutakoers($kp["valuta"]);


    $mr["Grootboekrekening"]  = $gb;
    $mr["Aantal"]             = 0;
    $mr["Fondskoers"]         = 0;
    if ($kp["bedrag"] < 0)
    {
      $mr["Credit"]           = 0;
      $mr["Debet"]            = abs($kp["bedrag"]);
      if ($FXcase)
      {
        $mr["Bedrag"] = -1 * $mr["Debet"];
      }
      else
      {
        $mr["Bedrag"] = _debetbedrag();
      }
    }
    else
    {
      $mr["Debet"]            = 0;
      $mr["Credit"]           = abs($kp["bedrag"]);
      if ($FXcase)
      {
        $mr["Bedrag"] = $mr["Credit"];
      }
      else
      {
        $mr["Bedrag"] = _creditbedrag();
      }
    }

    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "";
//    debug($mr, $data["row"]);
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
//  debug($data);

  if ($data[9] == "CASH")
  {
    do_FX();
    return;
  }
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data["aantal"];
	$mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
//  debug($mr, $data["row"]);
	$output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Vernkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  checkVoorDubbelInRM($mr);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

//  $bBedrag = $data["aantal"] * $data["koers"];
  $bBedrag = $data["brutoBedrag"];
  if ($data["brutoBedrag"] > 0)
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

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

function do_KOST()
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;


  if (stristr($data["omschrijving"], "DIVIDEND"))
  {
    do_DIVBE();
  }
  else
  {
    do_KOBU();
  }
//  debug($data["omschrijving"]);
}

function do_KOBU()  //kosten buitenland
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "KOBU";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "KOBU";

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["brutoBedrag"]);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;



  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

function do_DIVBE()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIVBE";

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["brutoBedrag"]);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;



  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Coupon
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  if ($data["brutoBedrag"] > 0)
  {
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Debet"]            = abs($data["brutoBedrag"]);
    $mr["Credit"]             = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }

  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();

//  debug($data);
  $valuta1  = $data[10];
  $valuta2  = $data[11];

  $rek1 = $data["rekening"];
  $rek2 = $data["rekening"];
  if ($valuta1 != "EUR" AND $valuta2 != "EUR")
  {
    $meldArray[] = "regel ".$data["row"].": Forex in VV {$valuta1}/{$valuta2}, deze handmatig boeken";
    return;
  }

  if ($valuta1 != "EUR")
  {
    $pootEUR = array(
      "rekening" => $rek1,
      "valuta"  => $valuta1,//$data[20],
      "bedrag" => $data[19]//$data[32],
    );
    $pootVV = array(
      "rekening" => $rek2,
      "valuta"  => $valuta2,//$data[41],
      "bedrag" => $data[17]//$data[43],
    );
  }

//  $wKoers = abs($pootEUR["bedrag"]/$pootVV["bedrag"]);
  $wKoers =1/$data[18];
//  debug($pootEUR,"pootEUR");
//  debug($pootVV,"pootVV");
// poot 1 boeken

  $mr["Rekening"]          = $data["rekening"].$data[10];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = ($mr["Valuta"] == "EUR")?1:$wKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "KRUIS";

  if ($data[19] < 0)
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]);
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[19]);
    $mr["Bedrag"]            = $mr["Credit"]  ;
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // poot 2 boeken

  $mr["Rekening"]          = $data["rekening"].$data[11];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valuta"]            = $data[11];
  $mr["Valutakoers"]       = ($mr["Valuta"] == "EUR")?1:$wKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";

  if ($data[17] < 0)
  {
    $mr["Debet"]             = abs($data[17]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"] ;
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = $mr["Credit"]  ;
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Rekening"]          = $data["rekening"].$data[11];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  kostenPosten("",true);

  checkControleBedrag($controleBedrag,abs($data[17])+abs($data[19])-abs($data[21]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_R()  //rente
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($mr["Valuta"] == $data["afrekenValuta"])
  {
    $factor = 1;
  }
  else
  {
    $factor = $mr["Valutakoers"];
  }


  if ($data["brutoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"] * $factor;
  }
  else
  {
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"] * $factor;
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_BEH()  //beheerfee
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  $mr = array();
  $mr["aktie"]              = "BEH";
  do_algemeen();
  checkVoorDubbelInRM($mr);

  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["bedragCashMut"] < 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoCash"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_CORP()
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
//  debug($data);
  $meldArray[] = "<span style='color: maroon; font-weight: bold'>regel {$data["row"]}: FOUT functie CORP niet ingeregeld</span>";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_STUKMUT()  //mutatie stukken
{
  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;
  $meldArray[] = "<span style='color: maroon; font-weight: bold'>regel {$data["row"]}: FOUT functie STUKMUT niet ingeregeld</span>";
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $controleBedrag;

  if ($data["bankCode"] != "")
  {
    do_STUKMUT();
  }
  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  checkVoorDubbelInRM($mr);
//debug($data);
  $controleBedrag = 0;
  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data["brutoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["brutoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["brutoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

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


