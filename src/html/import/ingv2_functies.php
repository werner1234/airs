<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/24 09:43:29 $
 		File Versie					: $Revision: 1.15 $

 		$Log: ingv2_functies.php,v $
 		Revision 1.15  2020/06/24 09:43:29  cvs
 		call 8713
 		
 		Revision 1.14  2020/06/08 13:11:01  cvs
 		call 8669
 		
 		Revision 1.13  2019/11/20 13:41:09  cvs
 		call 8236
 		
 		Revision 1.12  2019/09/18 10:56:11  cvs
 		call 8108
 		
 		Revision 1.11  2019/09/18 10:07:39  cvs
 		call 8097
 		
 		Revision 1.10  2019/08/23 08:25:54  cvs
 		call 8020
 		
 		Revision 1.9  2019/08/19 14:23:04  cvs
 		call 8000
 		
 		Revision 1.8  2019/05/28 07:31:24  cvs
 		no message
 		
 		Revision 1.7  2019/01/28 14:39:00  cvs
 		call 7507
 		
 		Revision 1.6  2018/10/15 12:48:26  cvs
 		call 7174
 		
 		Revision 1.5  2018/09/03 13:30:27  cvs
 		call 7129
 		
 		Revision 1.4  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/06/15 06:40:55  cvs
 		call 6986
 		
 		Revision 1.2  2018/06/12 13:12:45  cvs
 		kosten in EUR
 		
 		Revision 1.1  2018/05/07 08:32:38  cvs
 		call 6620
 		
 		Revision 1.7  2017/09/27 11:29:41  cvs
 		call 6041
 		
 		Revision 1.6  2017/04/12 14:17:28  cvs
 		call 5785
 		
 		Revision 1.5  2017/04/03 12:14:31  cvs
 		call 5174
 		
 		Revision 1.4  2016/07/01 14:36:48  cvs
 		call 5005
 		
 		Revision 1.3  2016/04/04 14:26:10  cvs
 		do_renob
 		
 		Revision 1.2  2016/04/04 08:30:08  cvs
 		call 4712
 		
 		Revision 1.1  2016/03/25 10:41:08  cvs
 		call 3691
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/


function getFonds()
{
  global $data, $error, $row, $fonds;
  $DB = new DB();


  $INGCodeNotFound = true;
  $fonds = array();
  if (trim($data[3]) <> 0 AND $data[6] != "CASH")
  {
    $INGcode = trim($data[3]);
    $query = "SELECT * FROM Fondsen WHERE INGCode = '".trim($INGcode)."' ";
    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
  }

  $ISIN = trim($data[4]);
  if (strstr($ISIN,':'))
  {
    $p = explode(":",$ISIN);
    $ISIN = trim($p[1]);
  }

  if($ISIN != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '$ISIN' AND Valuta ='".$data[8]."' ";

    if ($fonds = $DB->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/".$data[8]." niet gevonden ";
    }
  }
  else
  {
    $error[] = "$row: fonds INGcode ".$data[3]." (zonder ISIN) niet gevonden ";
  }



}



function getRekening($fxVal="")
{
  global $data, $error, $row;

	$depot = "ING";
  $db = new DB();
  if ($fxVal != "")
  {
    $rekeningNr = trim($data[1]).$fxVal;
  }
  else
  {
    $rekeningNr = trim($data[1]).trim($data[18]);
  }

  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return array("rekening" => $rec["Rekening"],
                 "valuta"   => $rec["Valuta"]);
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return array("rekening" => $rec["Rekening"],
                   "valuta"   => $rec["Valuta"]);
    }
    else
    {
      return false;
    }

  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "ING|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

	if ($data[18] == $mr["Valuta"] )
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data[18] == $mr["Valuta"] )
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers($rekValuta)
{
  global $data, $mr, $valutaLookup;

  if ($rekValuta == "EUR" AND $mr["Valuta"] == "EUR")
  {
    return 1;
  }

  if ($rekValuta == "EUR" AND $mr["Valuta"] != "EUR")
  {
    return $data[19];
  }

  if ($rekValuta != "EUR" AND $mr["Valuta"] == $rekValuta)
  {
    if ($data[19] != 1)
    {
      return 1/$data[19];
    }
    else
    {
      if ($data[30] != 0)
      {
        return 1/$data[30];
      }
      else
      {
        $db = new DB();
        $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
        $laatsteKoers = $db->lookupRecordByQuery($query);
        $valutaLookup = true;
        return $laatsteKoers["Koers"];
      }

    }
  }

  if ($rekValuta !=  $mr["Valuta"] )
  {
    return 0;
  }
	

}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[31];

	$mr["Boekdatum"]         = $data[12];

  $mr["settlementDatum"]   = $data[28];

	if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec["rekening"];
  }

}  

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  
  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers($data[18]);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[14];
	$mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[17]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


	$mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
	$mr["Debet"]             = abs(($data[20] + $data[21]));
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14] * -1;
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data[17]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AO()  // aankoop open
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "AO";
  do_algemeen();
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14];
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "A/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  // call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VO()  // verkoop openen
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "VO";
  do_algemeen();
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14] * -1;
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "V/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS()  // Aankoop sluiten
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "AS";
  do_algemeen();
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14];
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_ASS()  // Assignment opties
{

  global $fonds, $data, $mr, $output,$meldArray;

  if (stristr($data[6], "opties") OR stristr($data[6], "options"))
  {
    $mr = array();
    $mr["aktie"]              = "EO";
    do_algemeen();

    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers($data[18]);
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data[14];
    $mr["Fondskoers"]        = 0;
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;
    $mr["Rekening"]          = trim($data[1])."MEM";
    $mr["Grootboekrekening"] = "FONDS";

    $mr["Omschrijving"]      = "Assignment ".$fonds["Omschrijving"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;

    $mr["Transactietype"]    = "A/S";

    $output[] = $mr;

  }
  else
  {
    if ($data[11] == "AS P")
    {
      do_A();
    }
    else
    {
      do_V();
    }
  }
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VS()  // verkoop sluiten
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "VS";
  do_algemeen();
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14] * -1;
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_E()  // Emissie
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "E";
  do_algemeen();
  $mr["Omschrijving"]      = "Emissie ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14];
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[17]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RENOB()  //Rente of couponrente
{

  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "R";
	do_algemeen();

	if ($data[3])
	{

	  if ($data[14] > 0)  // als veld negatief betreft correctie rente
	  {


  		$mr["Omschrijving"] = "Coupon " . $fonds["Omschrijving"];

		  $mr["Grootboekrekening"] = "RENOB";
		  $mr["Valuta"]            = $fonds["Valuta"];
		  $mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             = $fonds["Fonds"];
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = abs(($data[5] * $data[8]) * $fonds["Fondseenheid"]);
		  $mr["Credit"]            = 0;
		  $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  $mr["Verwerkt"]          = 0;
		  $mr["Memoriaalboeking"]  = 0;

		  $output[] = $mr;

		  $mr["Grootboekrekening"] = "DIVBE";
	    $mr["Valuta"]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr["Valutakoers"]       = _valutakoers();
	    else
	      $mr["Valutakoers"]       = 1;

	    $mr["Aantal"]            = 0;
	    $mr["Fondskoers"]        = 0;
	    $mr["Debet"]             = 0;
	    $mr["Credit"]            = abs($data[13] * $data[10]);
	    $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];

	    if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;

			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             = "";
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs($data[11]);
		  $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];

      if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;

		  $mr["Grootboekrekening"] = "KOBU";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
  	  $mr["Fonds"]             = "";
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs($data[12]) * $data[10];
		  $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  if ($mr["Bedrag"] <> 0)
			  $output[] = $mr;
	  }
	  else
  	{
      $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];

		  $mr["Grootboekrekening"] = "RENOB";
		  $mr["Valuta"]            = $fonds["Valuta"];
		  $mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             =  $fonds["Fonds"];
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs(($data[5] * $data[8]) * $fonds["Fondseenheid"]);
		  $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  $mr["Verwerkt"]          = 0;
		  $mr["Memoriaalboeking"]  = 0;

		  $output[] = $mr;

      // 2008-04-17 cvs start toegvoeging
		  $mr["Grootboekrekening"] = "DIVBE";
	    $mr["Valuta"]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr["Valutakoers"]       = _valutakoers();
	    else
	      $mr["Valutakoers"]       = 1;
	    //$mr["Fonds"]             = "";
	    $mr["Aantal"]            = 0;
	    $mr["Fondskoers"]        = 0;
	    $mr["Debet"]             = abs($data[13] * $data[10]);
	    $mr["Credit"]            = 0;
	    $mr["Bedrag"]            = -1 * $mr["Debet"];
      $controleBedrag       += $mr["Bedrag"];

	    if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;
		  // 2008-04-17 cvs einde toegvoeging

		  $mr["Grootboekrekening"] = "KNBA";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = abs($data[11] * $data[10]);
		  $mr["Credit"]            = 0;
		  $mr["Bedrag"]            = -1 * $mr["Debet"];
      $controleBedrag       += $mr["Bedrag"];

      if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;

		  $mr["Grootboekrekening"] = "KOBU";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Credit"]            = 0;
		  $mr["Debet"]             = abs($data[12] * $data[10]);
		  $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  if ($mr["Bedrag"] <> 0)
			  $output[] = $mr;
  	}
	}
	else
	{
    $mr["Omschrijving"]      = $data[53];

    if (trim($data[53]) == "")
		{
			$mr["Omschrijving"]      = "Creditrente";
		}

		$mr["Grootboekrekening"] = "RENTE";
		$mr["Valuta"]            = $data[9];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = "";
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[14]);
		$mr["Bedrag"]            = _creditbedrag();

		if ($data[14] > 0)
		{
			if (trim($data[53]) == "")
			{
				$mr["Omschrijving"] = "Debetrente";
			}
			$mr["Debet"]             = abs($data[14]);
			$mr["Credit"]            = 0;
			$mr["Bedrag"]            = _debetbedrag();
		}

		$controleBedrag        = $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
		$output[] = $mr;
	}

  checkControleBedrag($controleBedrag,-1 * $data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIVCOUP()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "DIVCOUP";
	do_algemeen();
	$afrekenValuta = $data[18];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($afrekenValuta);
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

	if ($data[6] == "Coupons")
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "RENOB";
    if ($data[27] == "D")
    {
      $mr["Debet"]             = abs($data[14] * $data[15] * $data[16]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[14] * $data[15] * $data[16]);
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

    }
  }
  else
  {
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "DIV";
    if ($data[27] == "D")
    {
      $mr["Debet"]             = abs($data[14] * $data[15] );
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[14] * $data[15] );
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

    }
  }





	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;



	$mr["Grootboekrekening"] = "DIVBE";

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

	if ($data[27] == "D")
	{
	  $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[22]/$data[19]);
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Debet"]             = abs($data[22]/$data[19]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }


  $mr["Grootboekrekening"] = "KNBA";

	//$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

	if ($data[27] == "D")
	{
	  $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[20]);
	  $mr["Bedrag"]            = _creditbedrag() ;
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Debet"]             = abs($data[20]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


	$mr["Grootboekrekening"] = "KOBU";


	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

	if ($data[27] == "D")
	{
	  $mr["Credit"]            = abs($data[21]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($data[21]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_LOS()  // lossing
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "LOS";
  do_algemeen();
  $mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14] * -1;
  $mr["Fondskoers"]        = $data[15];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data[17]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

// call 8097 uitgeschakeld
  if ($data[18] == "EUR")
  {
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs(($data[20] + $data[21]));
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       -= $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[22] + $data[23] + $data[24] + $data[25]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       -= $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,abs($data[26])*(($data[27] == "D")?-1:1));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{

//  $data["sp_rekval"]      = $extra[0];
//  $data["sp_tegenval"]    = $extra[1];
//  $data["sp_bedrag"]      = $extra[2];
//  $data["sp_wisselkoers"] = $extra[3];
  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $mr["aktie"]              = "FX";
  do_algemeen();

  if ($rekRec  = getRekening($data["sp_rekval"]) )
  {
    $mr["Rekening"] = $rekRec["rekening"];
  }

  $mr["Omschrijving"]      = "Valutatransactie";
  $mr["Valuta"]            = $data["sp_rekval"];
  $mr["Valutakoers"]       = $data["sp_wisselkoers"];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  if ($data["sp_bedrag"] < 0)
  {
    $mr["Debet"]             = abs($data["sp_bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["sp_bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


  $bankBedrag = ($data[27] == "D")? -1*$data[26] :$data[26];
//  checkControleBedrag($controleBedrag * $data["sp_wisselkoers"],$bankBedrag);
  checkControleBedrag($controleBedrag,$bankBedrag);



}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  //mutatie geld/stukken
{

  global $fonds, $data, $mr, $output, $meldArray, $afw;

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  $controleBedrag = 0;


  if ($data[6] == "CASH")
  {

//    if (strtolower(substr($data[5],0,3)) == "tax" AND $data[4] == "")
//    {
//      $mr["Omschrijving"]      = $data[5];
//    }
//    else
//    {
//      $mr["Omschrijving"]      = $data[4];
//    }
    $mr["Omschrijving"]      = $data[5];

    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers($data[18]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($data["sp_rekval"] != "")
    {
      do_FX();
      return;
    }

    if ($data[11] == "OP")
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($data[26]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[26]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    $mr = $afw->reWrite("GLDMUT",$mr);
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;

    $bankBedrag = ($data[27] == "D")? -1* $data[26]:$data[26];
    checkControleBedrag($controleBedrag,$bankBedrag);
  }
  else
  {
/////////////////////

    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers("EUR");
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data[14];
    $mr["Fondskoers"]        = $data[15];
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;
    $mr["Rekening"]          = trim($data[1])."MEM";
    $mr["Grootboekrekening"] = "FONDS";

    $totaal                  = 0;
    if (  $data[11] == "ST" OR
         ($data[11] == "DO" AND substr($data[4],0,4) == "STCK")
    )
    {
      $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];


      $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "D";
      $totaal                  = $mr["Bedrag"];
      $output[] = $mr;



      $mr["Grootboekrekening"] = "RENME";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($data[17]);
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

      $mr["Transactietype"]    = "";

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }

      $totaal += $mr["Bedrag"];

      if ($totaal <> 0)
      {
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Grootboekrekening"] = "STORT";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($totaal);
        $mr["Bedrag"]            = abs($totaal);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }



    }
    else
    {
      if ($data[11] == "DO")
      {
        $meldArray[] = "regel ".$mr["regelnr"].": Toekenning dividenden overgeslagen";
        return ;
      }

      $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];

      $mr["Aantal"]            = $data[14] * -1;
      $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "L";
      $totaal                  = $mr["Bedrag"];
      $output[] = $mr;

      $mr["Grootboekrekening"] = "RENOB";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[17]);
      $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

      $mr["Transactietype"]    = "";
      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }


      $totaal += $mr["Bedrag"];

      if ($totaal <> 0)
      {
        $mr["Valuta"]            = "EUR";
        $mr["Valutakoers"]       = 1;
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Credit"]            = 0;
        $mr["Debet"]             =  abs($totaal);
        $mr["Bedrag"]            = -1 * ($totaal);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }
    }







    ///////////////////////////
  }


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EO()  //Expiratie opties
{

  global $fonds, $data, $mr, $output;

  $mr = array();
  $mr["aktie"]              = "EO";
  do_algemeen();

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers("EUR");
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[14];
  $mr["Fondskoers"]        = 0;
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;
  $mr["Rekening"]          = trim($data[1])."MEM";
  $mr["Grootboekrekening"] = "FONDS";

  if ($data[11] == "TS")
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;
    $controleBedrag         += $mr["Bedrag"];

    $mr["Transactietype"]    = "A/S";
  }
  else
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];

    $mr["Aantal"]            = $data[14] * -1;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = 0;

    $mr["Transactietype"]    = "V/S";
  }
  $output[] = $mr;
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KNBA()
{
  global $data, $mr, $output, $afw;

  $mr = array();
  $mr["aktie"]              = "KNBA";
  do_algemeen();

  $mr["Omschrijving"]    = $data[5];
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Valuta"]            = $data[18];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[27] == "D")
  {
    $mr["Debet"]             = abs($data[26]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[26]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("KNBA",$mr);
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()
{
  global $data, $mr, $output;

  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();

  $mr["Omschrijving"]    = $data[5];
  $mr["Grootboekrekening"] = "RENTE";
  $mr["Valuta"]            = $data[18];
  $mr["Valutakoers"]       = _valutakoers($data[18]);
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[27] == "D")
  {
    $mr["Debet"]             = abs($data[26]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[26]);
    $mr["Bedrag"]            = $mr["Credit"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
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
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


?>