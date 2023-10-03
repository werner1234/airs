"
<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/04 11:10:13 $
 		File Versie					: $Revision: 1.9 $

 		$Log: kbc_functies.php,v $
 		Revision 1.9  2020/05/04 11:10:13  cvs
 		call 7598

 		Revision 1.8  2020/02/10 08:15:38  cvs
 		aantal * -1 bij verkopen

 		Revision 1.7  2020/02/05 11:49:10  cvs
 		call 7598

 		Revision 1.6  2020/01/29 10:23:47  cvs
 		call 7598

 		Revision 1.5  2020/01/29 10:17:14  cvs
 		call 7598

 		Revision 1.4  2020/01/27 10:00:50  cvs
 		call 7598

 		Revision 1.3  2020/01/15 14:33:37  cvs
 		call 7598

 		Revision 1.2  2019/10/04 14:03:54  cvs
 		call 7598

 		Revision 1.1  2019/10/04 07:45:13  cvs
 		call 8024

 		Revision 1.4  2018/09/03 13:28:01  cvs
 		call 7131

 		Revision 1.3  2018/08/11 09:00:02  rvv
 		*** empty log message ***

 		Revision 1.2  2018/02/02 12:24:41  cvs
 		call 6532

 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***





*/

function kbcDatum($dat)
{
  return (substr($dat,0,4)."-".substr($dat,4,2)."-".substr($dat,6,2));
}
function kbcCheckDate($in)
{
  return checkdate(substr($in,4,2),substr($in,6,2), substr($in,0,4));
}


function kbcGetFonds($bankCode="",$ISIN="",$valuta="")
{

  $db = new DB();

  if ($bankCode <> "")
  {
    $query = "SELECT * FROM Fondsen WHERE KBCcode = '".$bankCode."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rec;
    }
  }

  if ($ISIN == ""  OR $valuta == "")
  {
    return false;
  }

  $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$ISIN."' AND Valuta = '$valuta' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec;
  }
  return false;

}


function getRekening($rekeningNr="-1", $depot="KBC")
{
  global $meldArray, $mr;
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "regel ".$mr["regelnr"].": ".$rekeningNr." --> niet gevonden voor $depot ";

      return false;
    }

  }


}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;

  $value = "KBC|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
  global $data, $mr, $valutaLookup;

  if ($valutaLookup == true)
    return -1 * $mr["Debet"];
  else
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
  global $data, $mr, $valutaLookup;
  $valuta = $data[9];
  if ($valutaLookup == true)
    return $mr["Credit"];
  else
    return $mr["Credit"] * $mr["Valutakoers"];
}

function _valutakoersAIRS()
{
  global $data, $mr;
  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data[8]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}


function _valutakoers()
{
  global $fonds, $data, $mr, $valutaLookup, $DB;
  $rekValuta = $data["rekValuta"];
  $valutaLookup = false;
  if ($rekValuta <> "EUR" AND $mr["Valuta"] == $rekValuta)
  {
    $mr["Valuta"] = $rekValuta;
    if ($data["valutaKoers"] != 1)
    {
      $valutaLookup = true;

      return 1/$data["valutaKoers"];
    }
    else
    {
      $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$rekValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $laatsteKoers = $DB->lookupRecord();
      $valutaLookup = true;
      return $laatsteKoers["Koers"];
    }
  }
  else
    return 1/$data["valutaKoers"];
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
    bankTransactieId = '".$mr["bankTransactieId"]."' AND 
    Rekening         = '".$mr["Rekening"]."' AND
    Boekdatum        = '".$mr["Boekdatum"]."'
    ";

  if ($rec = $db->lookupRecordByQuery($query) AND $mr["bankTransactieId"] != "")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"].")";
    return true;
  }
  return false;
}

function do_algemeen()
{
  global $mr, $row, $volgnr, $data, $_file, $fonds;


  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = ($data[4] != "")?$data[4]."-".$data[5]:$data[5]; // in TRNS is data[4] leeg


  $mr["Boekdatum"]         = $data[24];
  $mr["settlementDatum"]   = $data[26];

  $fonds = kbcGetFonds($data[11], $data[9], $data[13]);

}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;

  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;
  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
  else
  {
    $meldArray["verschil"][] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FX($fxData)
{
  global $output, $data, $mr, $_file,$row;


  if ($fxData[0][6] != "Wisseltransactie" OR $fxData[1][6] != "Wisseltransactie")
  {
    $data = $fxData[0];
    do_MUT();
    $data = $fxData[1];
    do_MUT();
    return true;
  }


  $mr = array();
  $mr["aktie"]              = "KRUIS";

  if ($fxData[0][$data[8]] == "EUR")
  {
    $eurPoot = $fxData[0];
    $vvPoot  = $fxData[1];
  }
  else
  {
    $eurPoot = $fxData[1];
    $vvPoot  = $fxData[0];
  }

  $data = $eurPoot;


  $controleBedrag = 0;
  $mr["bankTransactieId"]  = $data[11];

  $mr["Boekdatum"]         = $data[4];
  $mr["settlementDatum"]   = $data[5];
  $mr["Rekening"]          = getRekening($data[1].$data[8]);

  $mr["Omschrijving"]      = "Wisseltransactie ".$data[11];
  $mr["Valuta"]            = $data[8];
  $mr["Valutakoers"]       = 1;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data[7] < 0) // wordt contra geboekt
  {
    $mr["Debet"]             = abs($data[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[7]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag = $mr["Bedrag"];


//  if ( checkVoorDubbelInRM($mr) )
//  {
//    return true;
//  }

  $output[] = $mr;

  $data = $vvPoot;

  $mr["bankTransactieId"]  = $data[11];
  $mr["Boekdatum"]         = $data[4];
  $mr["settlementDatum"]   = $data[5];
  $mr["Rekening"]          = getRekening($data[1].$data[8]);
  $mr["Omschrijving"]      = "Wisseltransactie ".$data[11];
  $mr["Valuta"]            = $data[8];
  $mr["Valutakoers"]       = abs($eurPoot[7]/$vvPoot[7]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data[7] < 0) // wordt contra geboekt
  {
    $mr["Debet"]             = abs($data[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[7]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $controleBedrag += ($mr["Bedrag"] * $mr["Valutakoers"]);
//  if ( checkVoorDubbelInRM($mr) )
//  {
//    return true;
//  }
  $output[] = $mr;

  checkControleBedrag($controleBedrag, 0);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_MUT()
{
  global $fonds, $data, $mr, $_file,$row;

  $controleBedrag = 0;

  $mr = array();

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $row;
  $mr["bankTransactieId"]  = $data[11];

  $mr["Boekdatum"]         = $data[4];
  $mr["settlementDatum"]   = $data[5];
  $mr["Omschrijving"]      = $data[6];
  $mr["Rekening"]          = getRekening($data[1].$data[8]);

  if (strtolower(substr($mr["Omschrijving"],0,10)) == "bewaarloon")
  {
    do_GELD("BEW");

  }
  elseif (strtolower(substr($mr["Omschrijving"],0,18)) == "provisionering rec")
  {
    do_GELD("KNBA");
  }
  else
  {
    do_GELD();
  }

}


function do_GELD($grootboek="", $omschrijving="")  // geld mutaties
{

  global $fonds, $data, $mr, $output, $afw;
  $controleBedrag = 0;
  $mr["aktie"]  = "MUT";


  switch("$grootboek")
  {
    case "KNBA":
      $mr["Grootboekrekening"] = "KNBA";
      break;
    case "BEW":
      $mr["Grootboekrekening"] = "BEW";
      break;
    default:
      if ($data[7] < 0)
      {
        $mr["Grootboekrekening"] = "ONTTR";
      }
      else
      {
        $mr["Grootboekrekening"] = "STORT";
      }
      break;

  }


  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Valuta"]            = $data[8];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[7] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[7]);
    $mr["Bedrag"]          = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($data[7]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $mr = $afw->reWrite("GELD",$mr);

  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[7]);
}

function do_DIV()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DV";
  do_algemeen();

  $mr["bankTransactieId"]  = $data[1]."-".$data[7]."-".$data[5];
  $mr["Rekening"]          = getRekening($data[1].$data[19]);
  $mr["Boekdatum"]         = $data[7];
  $mr["settlementDatum"]   = $data[9];

  $fonds = kbcGetFonds("xxx", $data[5], $data[12]);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($data[11] * $data[20] );
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[14]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "ROER";
  //$mr["Valuta"]            = $data["belastingenValuta"];

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[15]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KNBA";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[16] + $data[17] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag(abs($controleBedrag),$data[18]);
}

function do_RENOB()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";
  do_algemeen();

  $mr["bankTransactieId"]  = $data[1]."-".$data[7]."-".$data[5];
  $mr["Rekening"]          = getRekening($data[1].$data[19]);
  $mr["Boekdatum"]         = $data[7];
  $mr["settlementDatum"]   = $data[9];

  $fonds = kbcGetFonds("xxx", $data[5], $data[12]);
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($data[11] * $data[20] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  //$mr["Valutakoers"]       = _valutakoers($data[8], $data["belastingenValuta"]);
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[14]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "ROER";
  //$mr["Valuta"]            = $data["belastingenValuta"];

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[15]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KNBA";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[16] + $data[17] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag(abs($controleBedrag),$data[18]);
}

function do_D()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "D";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1]."MEM");
  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;


  $mr["Grootboekrekening"] = "STORT";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Credit"]            = abs($mr["Bedrag"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = $mr["Credit"];

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_L()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "L";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1]."MEM");
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;


  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Debet"]             = abs($mr["Bedrag"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $mr["Debet"] * -1;

  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_A()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "A";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[39]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_AO()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "AO";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "A/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr = $afw->reWrite("KOBU",$mr);
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_AS()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "AS";
  do_algemeen();
//  debug($mr);
  debug($data);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_EXP_S()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "EXP_S";
  do_algemeen();
//  debug($mr);

  $mr["Rekening"]          = getRekening($data[1]."MEM");
  $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17];
  $mr["Fondskoers"]        = $data[18];
  $mr["Credit"]             = 0;
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "V";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17] * -1;
  $mr["Fondskoers"]        = $data[18];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENOB";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($data[39]);
  $mr["Debet"]             = 0;

  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_VO()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "VO";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17] * -1;
  $mr["Fondskoers"]        = $data[18];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "V/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag(abs($controleBedrag),$data[29]);
}


function do_VS()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "VS";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1].$data[30]);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17] * -1;
  $mr["Fondskoers"]        = $data[18];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_EXP_L()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "EXP_L";
  do_algemeen();
//  debug($mr);
  $mr["Rekening"]          = getRekening($data[1]."MEM");
  $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[17] * -1;
  $mr["Fondskoers"]        = $data[18];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[34] + $data[37]);  // diverse kosten verzameld

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[32] + $data[33] + $data[35] + $data[36]);

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOBU",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(abs($controleBedrag),$data[29]);
}

function do_NVT()
{
  global $meldArray, $data;
  $meldArray[] = "Transactie met {$data[14]} overgeslagen";
  return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error($f)
{
  global $do_func;
  echo "<BR>FOUT functie bij <b>$f</b> bestaat niet!";
}

