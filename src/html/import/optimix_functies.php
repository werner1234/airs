<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/08/19 11:17:39 $
 		File Versie					: $Revision: 1.11 $

 		$Log: optimix_functies.php,v $
 		Revision 1.11  2019/08/19 11:17:39  cvs
 		call 7964
 		
 		Revision 1.10  2019/01/28 13:25:12  cvs
 		rencp bedrag testen op 0
 		
 		Revision 1.9  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/06/04 12:35:06  cvs
 		call 6878
 		
 		Revision 1.7  2018/05/30 13:58:01  cvs
 		call 6878
 		
 		Revision 1.6  2018/05/18 10:54:18  cvs
 		call 6878
 		
 		Revision 1.5  2018/05/18 07:24:07  cvs
 		call 6878
 		
 		Revision 1.4  2018/05/17 12:57:33  cvs
 		call 6878
 		
 		Revision 1.3  2018/05/17 09:15:44  cvs
 		brutobedrag bij Dividend
 		
 		Revision 1.2  2018/05/16 13:31:29  cvs
 		call 6878
 		
 		Revision 1.1  2018/05/09 11:38:20  cvs
 		call 6878
 		
 		Revision 1.2  2018/02/02 12:24:41  cvs
 		call 6532
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/

function getRekening($rekeningNr="-1", $depot="OPT")
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
      $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> niet gevonden voor $depot ";
      return false;
    }
    
  }
}

function getFonds($isin="",$valuta,$optCode="")
{
  global $fonds;
  $fonds = array();
  $codeNotFound = true;
  $db = new DB();
  if ($optCode != "")
  {
    $query = "SELECT * FROM Fondsen WHERE OPTcode = '".trim($optCode)."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      $codeNotFound = false;
    }
  }

  if($codeNotFound AND trim($isin) != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isin."' AND Valuta ='".$valuta."' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  return false;
}

function optDate($in)
{
  $d = explode("-",$in);
  return "20".$d[2]."-".$d[0]."-".$d[1];
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "OPT|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $mr, $valutaLookup;
  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $mr;
	return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers()
{
	global $data, $mr;
	return ($mr["Valuta"] != "EUR")?$data[17]:1;
}

function do_algemeen()
{
	global $mr, $row, $data, $_file;

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = trim($data[2]).$data[8].$row;
  $mr["Rekening"]          = trim($data[2])."EUR";
	$mr["Boekdatum"]         = optDate($data[8]);
  $mr["settlementDatum"]   = optDate(($data[9]!=""?$data[9]:$data[8]));
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

	$mr["Rekening"]          = getRekening($mr["Rekening"]);
  
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[7];

	$mr["Fondskoers"]        = ($data[10] == "EUR")?$data[21]:($data[12] / $fonds["Fondseenheid"]);
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($data[13] + $data[14]);
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
	$mr["Debet"]             = abs($data[15]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


	if ($data[16] != 0)  // aankoop obligatie
	{
	  $mr["Grootboekrekening"] = "RENME";
	  $mr["Valuta"]            = $fonds["Valuta"];
	  $mr["Valutakoers"]       = _valutakoers();
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Debet"]             = abs($data[16]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag         += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

	}
	$cb = ($data[18] + ($data[16] * $data[17]));
  checkControleBedrag($controleBedrag,$cb*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()  // Verkoop van stukken
{
  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[7];
  $mr["Fondskoers"]        = ($data[10] == "EUR")?$data[21]:($data[12] / $fonds["Fondseenheid"]);
  $mr["Credit"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]            = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  //$mr[Fonds]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[13] + $data[14]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         -= $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data[15]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         -= $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  if ($data[16] != 0)  // aankoop obligatie
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]             = abs($data[16]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  $cb = ($data[18] + ($data[16] * $data[17]));
  checkControleBedrag($controleBedrag,$cb);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DV";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $brutoBedrag = ($data[10] == "EUR")?($data[18]+$data[15]):($data[11] + $data[15]);
  if ($data[17] < 0)  // als veld negatief betreft een correctie Dividend
  {
    $mr["Debet"]             = abs($brutoBedrag);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($brutoBedrag);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[18] < 0)  // als veld negatief betreft een correctie Dividend
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[15]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]             = abs($data[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[18]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENCP()  //Rente / coupond
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENCP";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if (trim($mr["Fonds"]) != "")
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Bedrag"]            = _creditbedrag();
    $bedrag = ($data[11] != 0)?$data[11]:$data[18];
    if ($bedrag > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($bedrag);
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Credit"]             = 0;
      $mr["Debet"]            = abs($bedrag);
      $mr["Bedrag"]            = _debetbedrag();
    }
    $controleBedrag       += $mr["Bedrag"];
  }
  else
  {
    $mr["Valuta"] = $data[10];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Grootboekrekening"] = "RENTE";
    $mr["Omschrijving"]      = "Rente ".$data[6];

    if ($data[18] > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[18]);
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Credit"]             = 0;
      $mr["Debet"]            = abs($data[18]);
      $mr["Bedrag"]            = _debetbedrag();
    }

    $controleBedrag       += $mr["Bedrag"];
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data[18]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KO()  //Kosten algemeen
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"]              = "KO";
  do_algemeen();
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = $data[6];
  switch (trim(strtolower($data[6])))
  {
    case "beheervergoeding":
      $mr["Grootboekrekening"] = "BEH";
      break;
    default:
      $mr["Grootboekrekening"] = "KNBA";
  }

  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[18]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
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

function do_OP()  //opname
{
  global $data, $mr, $output, $fonds;
  $mr = array();
  $mr["aktie"]              = "OP";
  do_algemeen();



  if ($data[5] == "")
  {
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[6];
    $mr["Grootboekrekening"] = "ONTTR";

    $mr["Valuta"]            = $data[10];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[18]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  else
  {

    $mr["Rekening"] = trim($data[2]) . "MEM";
    $mr["Rekening"] = getRekening($mr["Rekening"]);
    $mr["Omschrijving"] = "Lichting " . $fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"] = $fonds["Valuta"];
    $mr["Valutakoers"] = _valutakoers();
    $mr["Fonds"] = $fonds["Fonds"];
    $mr["Aantal"] = -1 * $data[7];
    $mr["Fondskoers"] = $data[21];
    $mr["Debet"] = 0;
    $mr["Credit"] = abs($data[7] * $data[21] * $fonds["Fondseenheid"]);
    $mr["Bedrag"] = $mr["Credit"] * $mr["Valutakoers"];
    $mr["Transactietype"] = "L";
    $mr["Verwerkt"] = 0;
    $mr["Memoriaalboeking"] = 1;
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
    if ($mr["Bedrag"] <> 0)
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Rekening"] = getRekening($mr["Rekening"]);
      $mr["Valuta"] = "EUR";
      $mr["Valutakoers"] = 1;
      $mr["Fonds"] = "";
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = abs($mr["Bedrag"]);
      $mr["Credit"] = 0;
      $mr["Bedrag"] = -1 * ($mr["Debet"]);
      $mr["Transactietype"] = "";
      $output[] = $mr;
    }


    if ($data[16] > 0)  // toegevoegd meenemen opgelopen rente
    {
      $mr["Rekening"] = trim($data[2]) . "MEM";
      $mr["Rekening"] = getRekening($mr["Rekening"]);
      $mr["Omschrijving"] = "Lichting " . $fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "RENOB";
      $mr["Valuta"] = $fonds["Valuta"];
      $mr["Valutakoers"] = _valutakoers();
      $mr["Fonds"] = $fonds["Fonds"];
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = 0;
      $mr["Credit"] = abs($data[16]);
      $mr["Bedrag"] = $mr["Credit"] * $mr["Valutakoers"];
      $mr["Transactietype"] = "";
      $mr["Verwerkt"] = 0;
      $mr["Memoriaalboeking"] = 1;
      $controleBedrag += $mr["Bedrag"];
      $output[] = $mr;

      $mr["Grootboekrekening"] = "ONTTR";

      $mr["Valuta"] = "EUR";
      $mr["Valutakoers"] = 1;
      $mr["Fonds"] = "";
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = abs($mr["Bedrag"]);
      $mr["Credit"] = 0;
      $mr["Bedrag"] = -1 * ($mr["Debet"]);
      $mr["Transactietype"] = "";

      $output[] = $mr;
    }
    checkControleBedrag($controleBedrag, $data[18] );
  }
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_ST()  //Storting
{
  global $data, $mr, $output;
  $mr = array();
  $mr["aktie"]             = "ST";
  do_algemeen();
  if ($data[5] == "")
  {
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[6];
    $mr["Grootboekrekening"] = "STORT";
    $mr["Valuta"]            = $data[10];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[18]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  else
  {
    $mr["Rekening"] = trim($data[2]) . "MEM";
    $mr["Rekening"] = getRekening($mr["Rekening"]);
    $mr["Omschrijving"] = "Deponering " . $fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"] = $fonds["Valuta"];
    $mr["Valutakoers"] = _valutakoers();
    $mr["Fonds"] = $fonds["Fonds"];
    $mr["Aantal"] = $data[7];
    $mr["Fondskoers"] = $data[21];
    $mr["Debet"] = abs($data[7] * $data[21] * $fonds["Fondseenheid"]);
    $mr["Credit"] = 0;
    $mr["Bedrag"] = -1 * $mr["Debet"] * $mr["Valutakoers"];
    $mr["Transactietype"] = "D";
    $mr["Verwerkt"] = 0;
    $mr["Memoriaalboeking"] = 1;
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
    if ($mr["Bedrag"] <> 0)
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Rekening"] = getRekening($mr["Rekening"]);
      $mr["Valuta"] = "EUR";
      $mr["Valutakoers"] = 1;
      $mr["Fonds"] = "";
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = 0;
      $mr["Credit"] = abs($mr["Bedrag"]);
      $mr["Bedrag"] = ($mr["Credit"]);
      $mr["Transactietype"] = "";
      $output[] = $mr;
    }


    if ($data[16] > 0)  // toegevoegd meenemen opgelopen rente
    {
      $mr["Rekening"] = trim($data[2]) . "MEM";
      $mr["Rekening"] = getRekening($mr["Rekening"]);
      $mr["Omschrijving"] = "Deponering " . $fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "RENME";
      $mr["Valuta"] = $fonds["Valuta"];
      $mr["Valutakoers"] = _valutakoers();
      $mr["Fonds"] = $fonds["Fonds"];
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = abs($data[16]);
      $mr["Credit"] = 0;
      $mr["Bedrag"] = -1 * $mr["Debet"] * $mr["Valutakoers"];
      $mr["Transactietype"] = "";
      $mr["Verwerkt"] = 0;
      $mr["Memoriaalboeking"] = 1;
      $controleBedrag += $mr["Bedrag"];
      $output[] = $mr;

      $mr["Grootboekrekening"] = "STORT";

      $mr["Valuta"] = "EUR";
      $mr["Valutakoers"] = 1;
      $mr["Fonds"] = "";
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Debet"] = 0;
      $mr["Credit"] = abs($mr["Bedrag"]);
      $mr["Bedrag"] = ($mr["Credit"]);
      $mr["Transactietype"] = "";

      $output[] = $mr;
    }
    checkControleBedrag($controleBedrag, $data[18] );
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OA()  //Aankoop openen bij opties en futures
{

  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]             = "OA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "A/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]*-1);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OV()  //Verkoop openen bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "OV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V/O";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SA()  //Aankoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]             = "SA";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV()  //Verkoop sluiten bij opties en futures
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "SV";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_TS()  //Expiratie Time Short bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "TS";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_TL()  //Expiratie Time Long bij opties en futures
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "TL";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
	$mr[Transactietype]    = "V/S";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_E()  //Emissie van stukken of claims
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "E";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  if ($data[8] == 0)
  {
  	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
    $mr[Transactietype]    = "D";
  }
  else
  {
	  $mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
    $mr[Transactietype]    = "A";
  }
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();


	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	$mr[aktie]              = "L";
	do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = -1 * $data[5];
	$mr[Fondskoers]        = $data[8];
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

	$mr[Grootboekrekening] = "KOST";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = abs($data[11]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;
	$mr[Grootboekrekening] = "KOBU";
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[12]);
	$mr[Bedrag]            = _debetbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

	$mr[Grootboekrekening] = "RENOB";  //obligatie rente
	$mr[Valuta]            = $data[9];
  if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  //$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Credit]            = 0;
	$mr[Debet]             = abs($data[7]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];

	$mr[Transactietype]    = "";
	if ($mr[Bedrag] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DO()  //Stock dividend
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
	$mr[aktie]              = "DO";
	do_algemeen();
	$mr[Rekening]          = trim($data[1])."MEM";
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[5];
	$mr[Fondskoers]        = $data[8];
	$mr[Debet]             = abs($data[5] * $data[8] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * ($mr[Debet] * $mr[Valutakoers]);
	$mr[Transactietype]    = "D";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 1;

	$output[] = $mr;

	$mr[Grootboekrekening] = "STORT";
	if ($mr[Valuta] == "EUR")      $mr[Valutakoers]  = 1;
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Debet]             = 0;
	$mr[Credit]            = abs($data[14]);
	$mr[Bedrag]            = ($mr[Credit] * $mr[Valutakoers]);  //2008-04-17 cvs correctie valutafout
	$mr[Transactietype]    = "";
  if ($mr[Bedrag] <> 0)
    $output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VM()  //Variation margin  toegevoegd d.d. 8-7-2014
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
  
  do_algemeen();
	$mr[Rekening]          = trim($data[1]).trim($data[9]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
	$mr[Omschrijving]      = "Variation Margin: ".$data[3];
  $mr[Grootboekrekening] = "VMAR";
	$mr[Valuta]            = $data[9];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  if ($data[14] > 0)
  {
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($data[14]);
    $mr[Bedrag]            = _creditbedrag();
  } 
  else
  {
    $mr[Debet]             = abs($data[14]);
    $mr[Credit]            = 0;
    $mr[Bedrag]            = _debetbedrag();
  }  
	
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
	$output[] = $mr;

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
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>