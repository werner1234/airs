<?
/*
    AE-ICT sourcemodule created 26 feb. 2021
    Author              : Chris van Santen
    Filename            : cacaeis_functies.php


*/

//function CACcnvNumber($in)
//{
//  $in = str_replace(".","",$in);
//  return str_replace(",",".",$in);
//}

function cnvDate($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
}

function stkCnvDate($in)
{
  $e = explode ("-",$in);
  return "{$e[2]}-{$e[1]}-{$e[0]}";
}

function mapDataFields()
{
  global $data;


  if (count(explode("-",$data[3])) == 3)  // datumveld
  {
    // stukken
    $data["stukken"]          = true;
    $data["unsettled"]        = ($data[8] == "Unsettled");
    $data["isin"]             = trim($data[4]);
    $data["fondsValuta"]      = $data[25];
    $data["bankCode"]         = trim($data[5]);
    $data["afrekenValuta"]    = trim($data[16]);
    $data["portefeuille"]     = trim($data[1]);
    $data["omschrijving"]     = "";
    $data["boekdatum"]        = stkCnvDate($data[9]);
    $data["settledatum"]      = ($data[11] == "01-01-1900")?stkCnvDate($data[10]):stkCnvDate($data[11]);
    $data["aantal"]           = CACcnvNumber($data[14]);
    $data["nettoBedrag"]      = CACcnvNumber($data[15]);
    $data["valutakoers"]      = 1/CACcnvNumber($data[18]);
    $data["transactieId"]     = $data[6];
    $data["transactieCode"]   = trim($data[13]);

    $data["koers"]            = CACcnvNumber($data[19]);

    $data["feesBedrag"]      = CACcnvNumber($data[28]);
    $data["feesValuta"]      = CACcnvNumber($data[29]);

    $data["opgelopenRenteBedrag"] = CACcnvNumber($data[30]);
    $dBta["opgeenRenteValuta"]    = CACcnvNumber($data[31]);

    $data["brutoBedrag"]      = CACcnvNumber($data[26]);
  }
  else
  {
    // cash
    $data["stukken"]          = false;
    $data["afrekenValuta"]    = trim($data[5]);
    $data["portefeuille"]     = trim($data[1]);
    $data["rekening"]         = trim($data[4]);
    $data["omschrijving"]     = trim($data[20]);
    $data["boekdatum"]        = cnvDate($data[6]);
    $data["settledatum"]      = ($data[7] != "")?cnvDate($data[7]):cnvDate($data[6]);
    $data["debetBedrag"]      = CACcnvNumber($data[8]);
    $data["creditBedrag"]     = CACcnvNumber($data[9]);
    $data["nettoBedrag"]      = CACcnvNumber($data[9]) - CACcnvNumber($data[8]);
    $data["valutakoers"]      = 999;
    $data["transactieId"]     = $data[16];
    $data["transactieCode"]   = trim($data[14]);
    $data["saldoRegel"]       = ($data["nettoBedrag"] == 0);

  }

}

function getFonds($isin="", $val="")
{
  global $data, $error, $row, $fonds;
  $DB = new DB();


  $INGCodeNotFound = true;
  $fonds = array();
  if (trim($data["bankCode"]) != "")
  {
//aetodo: Bankcode nog omzetten naar SS code
//    $query = "SELECT * FROM Fondsen WHERE HHBcode = '".trim($data["bankCode"])."' ";
//    if ($fonds = $DB->lookupRecordByQuery($query))
//    {
//      return true;
//    }
  }

  $ISIN = trim($data["isin"]);
  $VAL  = $data["fondsValuta"];

  if ($isin != "")
  {
    $ISIN = $isin;
    $VAL  = $val;
  }


  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN' AND Valuta ='".$VAL."' ";
//debug($query);
    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/".$VAL." niet gevonden ";
    }
  }
  else
  {
    $error[] = "$row: fonds bankcode ".$data["bankCode"]." (zonder ISIN) niet gevonden ";
  }

}


function getPositie($portefeuille, $isin)
{
  global $uitkDataSet, $row, $error, $meldArray;

  $db = new DB();
  $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds AS Fonds,
        Left(Fondsen.ISINcode,12) AS ISIN,
        SUM(Rekeningmutaties.Aantal) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      JOIN Fondsen ON 
        Rekeningmutaties.Fonds = Fondsen.Fonds            
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) >= '".(date("Y")-1)."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= NOW() AND
        
        ( 
          (Portefeuilles.Portefeuille = '{$portefeuille}' AND LEFT(Portefeuilles.Depotbank,3) = 'KAS') OR
          (Portefeuilles.PortefeuilleDepotbank = '{$portefeuille}' AND LEFT(Portefeuilles.Depotbank,3) = 'KAS')
        ) AND
        Left(Fondsen.ISINcode,12) = '{$isin}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds,
        Left(Fondsen.ISINcode,12)
      ORDER BY   
        SUM(Rekeningmutaties.Aantal) ASC
      
    ";

  $db->executeQuery($query);
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Fonds"];
  }
  else
  {
    $meldArray[] = "$row: positie met ISIN {$isin} niet gevonden ";
  }



}


function getRekening($rekeningNr="")
{
  global $data, $error, $row, $depotBank;

  $db = new DB();
  if ($rekeningNr == "")
  {
    $rekeningNr = trim($data["rekening"]).trim($data["afrekenValuta"]);
  }


  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depotBank."' ";

  if ($rec = $db->lookupRecordByQuery($query) )
  {
//    return array("rekening" => $rec["Rekening"],
//                 "valuta"   => $rec["Valuta"]);
    $data["posPortefeuille"] = $rec["Portefeuille"];
    return $rekeningNr;
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depotBank."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      $data["posPortefeuille"] = $rec["Portefeuille"];
      return $rekeningNr;
    }
    else
    {
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
  global $data, $mr, $valutaLookup;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if (
    ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR") OR
    ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta) )
  {
    if ($data["valutakoers"] == 999)
    {
      $db = new DB();
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
      $laatsteKoers = $db->lookupRecordByQuery($query);
      return $laatsteKoers["Koers"];
    }
    else
    {
      return $data["valutakoers"];
    }

  }

}


function _valutakoersEffecten()
{
  global $data, $mr, $valutaLookup;

  $rekValuta = "EUR";

  if ($data["valutakoers"] != 1)
  {
    return $data["valutakoers"];
  }
  else
  {
     $db = new DB();
     $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
     $laatsteKoers = $db->lookupRecordByQuery($query);
     return $laatsteKoers["Koers"];
  }

}

function checkVoorDubbelInRM($mr)
{
//  global $meldArray;
//  $db = new DB();
//  $query = "
//  SELECT
//    id
//  FROM
//    Rekeningmutaties
//  WHERE
//    bankTransactieId = '".$mr["bankTransactieId"]."' AND
//    Rekening         = '".$mr["Rekening"]."' AND
//    Boekdatum        = '".$mr["Boekdatum"]."'
//    ";
//
//  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
//  {
//    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
//    return true;
//  }
  return false;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data["transactieId"];
	$mr["Boekdatum"]         = $data["boekdatum"];
  $mr["settlementDatum"]   = $data["settledatum"];




}  

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag )
  {
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  }
  else
  {
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUTPOS()
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"] = "stukmut";
  do_algemeen();
  if ($rekRec  = getRekening($data["portefeuille"]."MEM") )
  {
    $mr["Rekening"] = $rekRec;
  }
  $mr["Grootboekrekening"]  = "FONDS";
  $mr["Valuta"]             = $fonds["Valuta"];
  $mr["Valutakoers"]        = $_valutakoersEffecten();
  $mr["Fonds"]              = $fonds["Fonds"];
  $mr["Fondskoers"]         = $data["koers"];
  $mr["Omschrijving"]       = "Deponering " .$fonds["Omschrijving"];
  $mr["Aantal"]             = $data["aantal"];
  $mr["Verwerkt"]           = 0;
  $mr["Memoriaalboeking"]   = 1;
  $mr["Debet"]              = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]             = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  $mr["Transactietype"]     = "D";
  $output[] = $mr;

  $mr["Grootboekrekening"]  = "STORT";
  $mr["Valuta"]             = "EUR";
  $mr["Valutakoers"]        = 1;
  $mr["Fonds"]              = "";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;
  $mr["Credit"]             = abs($mr["Debet"]);
  $mr["Debet"]              = 0;
  $mr["Bedrag"]             = $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUTNEG()
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"] = "stukmut";
  do_algemeen();
  if ($rekRec  = getRekening($data["portefeuille"]."MEM") )
  {
    $mr["Rekening"] = $rekRec;
  }
  $mr["Grootboekrekening"]  = "FONDS";
  $mr["Valuta"]             = $fonds["Valuta"];
  $mr["Valutakoers"]        = _valutakoersEffecten();
  $mr["Fonds"]              = $fonds["Fonds"];
  $mr["Fondskoers"]         = $data["koers"];
  $mr["Omschrijving"]       = "Lichting " .$fonds["Omschrijving"];
  $mr["Aantal"]             = -1 * $data["aantal"];
  $mr["Verwerkt"]           = 0;
  $mr["Memoriaalboeking"]   = 1;
  $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]              = 0;
  $mr["Bedrag"]             = ($mr["Debet"] * $mr["Valutakoers"]);
  $mr["Transactietype"]     = "L";
  $output[] = $mr;

  $mr["Grootboekrekening"]  = "ONTTR";
  $mr["Valuta"]             = "EUR";
  $mr["Valutakoers"]        = 1;
  $mr["Fonds"]              = "";
  $mr["Aantal"]             = 0;
  $mr["Fondskoers"]         = 0;
  $mr["Debet"]              = abs($mr["Debet"]);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]             = -1 * $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIVCOUP()  //Coupon
{
  global $fonds, $data, $mr, $output,$meldArray, $controleBedrag, $row;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";

  do_algemeen();
  checkVoorDubbelInRM($mr);

  if (stristr($data[20], "Our Ref : " ))
  {
    $divSpecial = true;
    $split  = explode("Our Ref : ", $data[20]);
    $parts  = explode(" ", $split[1]);
    $ISIN   = $parts[0];

    $split  = explode("Gross : ", $data[20]);
    $parts  = explode(" ", $split[1]);
    $bruto  = CACcnvNumber($parts[0]);
    $VAL    = trim(substr($split[0], -4));

    $split  = explode("Tax : ", $data[20]);
    $parts  = explode(" ", $split[1]);
    $taxPerc = CACcnvNumber($parts[0]);


    $tax = ($bruto * ($taxPerc/100));








    if ($rekRec  = getRekening() )
    {
      $mr["Rekening"] = $rekRec;
    }

    $posFonds = getPositie($data["posPortefeuille"], $ISIN);

//    debug(array(
//            $ISIN,$bruto, $tax, $VAL
//          ));
//    if ($parts[0] <> $parts[3])  // valuta's gelijk
//    {
//      $divSpecial = false;
//    }
//
//    $wisselkoers = 1/(str_replace(",",".",$parts[5]));
//
////    if (round($parts[1] * $wisselkoers,2) <> $data[18])
////    {
////      $divSpecial = false;
////    }

  }


  $mr["Boekdatum"]         = $mr["settlementDatum"];

  if (substr($data[20],4,3) == "DIV")
  {
    $mr["Omschrijving"]      = "Dividend ".$posFonds;
    $mr["Grootboekrekening"] = "DIV";
  }
  else
  {
    $mr["Omschrijving"]      = "Coupon ".$posFonds;
    $mr["Grootboekrekening"] = "RENOB";
  }


  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data['afrekenValuta']);
  $mr["Fonds"]             =  $posFonds;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  $mr["Credit"]            = abs($bruto);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();



  $controleBedrag         += $mr["Bedrag"];
//debug($mr);
  $output[] = $mr;


  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Debet"]            = abs($tax);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
  if($mr["Bedrag"] != 0)
  {
    $output[] = $mr;
  }



//  kostenPosten();

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FX($legA, $legB)
{


  global $mr, $output, $meldArray, $data, $row;
  $data = $legB;
  mapDataFields();
  $legB = $data;
//  debug($data,"legB");
  $data = $legA;
  mapDataFields();
  $legA = $data;

//  debug($data, "legA");
  $mr = array();
  do_algemeen();

  $mr["Fonds"]             = "";
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "KRUIS";

  if ($legA["afrekenValuta"] == "EUR" AND $legB["afrekenValuta"] !=  "EUR")
  {
    $EurRec = $legA;
    $VvRec = $legB;
    $mr["Valuta"]            = $VvRec["afrekenValuta"];
    $mr["Valutakoers"]       = abs($EurRec["nettoBedrag"] / $VvRec["nettoBedrag"]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $EurRec["transactieId"];
    $mr["Rekening"]          = $EurRec["rekening"] . $EurRec["afrekenValuta"];
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }

    $mr["Debet"]             = abs($VvRec["debetBedrag"]);
    $mr["Credit"]            = abs($VvRec["creditBedrag"]);
    $mr["Bedrag"]            = ( abs($mr["Debet"] * $mr["Valutakoers"]) * -1)  +
                               ( abs($mr["Credit"] * $mr["Valutakoers"]) );
    $output[] = $mr;

    $controleBedrag += $mr["Bedrag"];
    $mr["Rekening"]          = $VvRec["rekening"] . $VvRec["afrekenValuta"];
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }
    $mr["Debet"]             = abs($VvRec["debetBedrag"]);
    $mr["Credit"]            = abs($VvRec["creditBedrag"]);
    $mr["Bedrag"]            = $VvRec["nettoBedrag"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;
  }

  if ($legA["afrekenValuta"] != "EUR" AND $legB["afrekenValuta"] ==  "EUR")
  {
    $EurRec = $legB;
    $VvRec = $legA;
    $mr["Valuta"]            = $VvRec["afrekenValuta"];
    $mr["Valutakoers"]       = abs($EurRec["nettoBedrag"] / $VvRec["nettoBedrag"]);
    $mr["Omschrijving"]      = "Valutatransactie";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $EurRec["transactieId"];
    $mr["Rekening"]          = $EurRec["rekening"] . $EurRec["afrekenValuta"];
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }

    $mr["Debet"]             = abs($VvRec["debetBedrag"]);
    $mr["Credit"]            = abs($VvRec["creditBedrag"]);
    $mr["Bedrag"]            = ( abs($mr["Debet"] * $mr["Valutakoers"]) * -1)  +
      ( abs($mr["Credit"] * $mr["Valutakoers"]) );
    $output[] = $mr;

    $controleBedrag += $mr["Bedrag"];
    $mr["Rekening"]          = $VvRec["rekening"] . $VvRec["afrekenValuta"];
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }
    $mr["Debet"]             = abs($VvRec["debetBedrag"]);
    $mr["Credit"]            = abs($VvRec["creditBedrag"]);
    $mr["Bedrag"]            = $VvRec["nettoBedrag"];
    $controleBedrag         += $mr["Bedrag"] * $mr["Valutakoers"];
    $output[] = $mr;
  }

  if (
       ($legA["afrekenValuta"] != "EUR" AND $legB["afrekenValuta"] !=  "EUR") OR
       ($legA["afrekenValuta"] == "EUR" AND $legB["afrekenValuta"] ==  "EUR")     )

  {
    $row = $legA["row"];
    $meldArray[] = "$row: <b>Valutatransactie: geboekt als storting/ontrekking </b>";
    $data = $legA;
    do_GELDMUT();
    $row = $legB["row"];
    $meldArray[] = "$row: <b>Valutatransactie: geboekt als storting/ontrekking </b>";
    $data = $legB;
    do_GELDMUT();
  }




  //addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], 0);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();

  if (substr($data["omschrijving"],0,6) == "PRIME ")
  {
    $meldArray[] = "regel {$mr["regelnr"]}: overgeslagen: optie transactie";
    return;
  }

  if (substr($data["omschrijving"],0,15) == "110-Ext.Ref:FDX" AND $data["nettoBedrag"] < 0)
  {
    $meldArray[] = "regel {$mr["regelnr"]}: overgeslagen: kosten optie transactie";
    return;
  }


  if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }
  checkVoorDubbelInRM($mr);


    $mr["Omschrijving"]    = $data["omschrijving"];

    $mr["Valuta"]            = $data["afrekenValuta"];
    $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($data["nettoBedrag"] < 0 )
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($data["nettoBedrag"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
      $mr = $afw->reWrite("ONTTR",$mr);
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data["nettoBedrag"]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr = $afw->reWrite("STORT",$mr);
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_R()  //rente
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $mr["aktie"]              = "R";
  do_algemeen();
  if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec;
  }
  checkVoorDubbelInRM($mr);


  $mr["Omschrijving"]    = $data["omschrijving"];

  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers($data["afrekenValuta"]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "RENTE";
  if ($data["nettoBedrag"] < 0 )
  {
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function CACcnvNumber($in)
{
  return str_replace(",", "", $in);
}

function do_NVT()
{
  return true;
}

function do_error()
{
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


?>