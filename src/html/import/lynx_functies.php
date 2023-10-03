<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/08 11:45:44 $
 		File Versie					: $Revision: 1.18 $

 		$Log: lynx_functies.php,v $
 		Revision 1.18  2020/06/08 11:45:44  cvs
 		call 8670
 		
 		Revision 1.17  2019/06/05 11:57:35  cvs
 		no message
 		
 		Revision 1.16  2019/06/05 11:52:22  cvs
 		call 7841
 		
 		Revision 1.15  2019/01/28 15:24:00  cvs
 		call 6999
 		
 		Revision 1.14  2019/01/28 15:05:08  cvs
 		call 7206
 		
 		Revision 1.13  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/06/20 12:44:18  cvs
 		call 6999
 		
 		Revision 1.11  2018/05/09 15:05:32  cvs
 		call 6224
 		
 		Revision 1.10  2018/02/02 12:25:20  cvs
 		call 6556
 		
 		Revision 1.9  2018/01/11 07:38:08  cvs
 		no message
 		
 		Revision 1.8  2017/12/06 12:33:45  cvs
 		omschrijving valautatransactie aangepast
 		
 		Revision 1.7  2017/12/05 12:15:47  cvs
 		call 6224
 		
 		Revision 1.6  2017/11/27 10:01:29  cvs
 		call 6224
 		
 		Revision 1.5  2017/11/24 16:28:10  cvs
 		call 6224
 		
 		Revision 1.4  2017/11/17 15:13:20  cvs
 		call 6224
 		
 		Revision 1.3  2017/10/25 13:59:18  cvs
 		call 6224 Lynx import
 		
 		Revision 1.2  2017/10/20 10:15:10  cvs
 		call 6224
 		
 		Revision 1.1  2017/09/29 12:15:48  cvs
 		call 6224
 		


*/

function convertDataRow($dataRaw)
{
  $split = explode('"', $dataRaw);
  $items = explode(",",$split[1]);
  return trimRecord($items);
}


function trimRecord($data)
{
  foreach ($data as $key => $value)
  {
    if (trim($value) == ".00")
      $dataOut[] = 0;
    else
      $dataOut[] = trim($value);
  }
  return $dataOut;
}

function getRekening($rekeningNr="-1", $depot="LYNX")
{
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
      return false; 
    }
    
  }
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "LYNX|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

  return -1 * ($mr["Debet"]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;

  return $mr["Credit"];
}


function _valutakoers($val="")
{
	global $data, $mr;
	$db = new DB();
	if ($val == "")
  {
    $val = $data[10];
  }

	if (trim($data[11]) == "0" OR trim($data[11]) == "")
  {
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$val."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";

    $laatsteKoers = $db->lookupRecordByQuery($query);

    return $laatsteKoers["Koers"];
  }
  else
  {
    return $data[11];
  }


}

function stripRecordNA($data)
{
  $out = array();
  foreach ($data as $k=>$v)
  {
    $out[$k] = ($v == "N/A")?0:trim($v);
  }
  return $out;
}


function _valutakoersDIV()  // tbv dbs2742
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[9];
	$valutaLookup = false;
  if ($data[10] == 1)
  {
    $mr[Valuta]  = $valuta;
  }
  
	if ($valuta <> "EUR" )
	{
    
	   if ($data[23] > 0)
     {
       $valutaLookup = true;
       
       return $data[23];
     }
     else
     {
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
       $DB->SQL($query);
       $laatsteKoers = $DB->lookupRecord();
       $valutaLookup = true;
       return $laatsteKoers[Koers];
     }
	}
	else
	  return $data[10];
}

function transidSpecial()
{
  global $mr, $data;
  $mr["bankTransactieId"]  = trim($data[2]).trim($data[4]).substr(trim($data[20]),-5);
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $meldArray;

  $data = stripRecordNA($data);
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[22];
  $mr["Boekdatum"]         = $data[20];
  $mr["settlementDatum"]   = $data[21];
  $mr["Rekening"] = trim($data[2]).trim($data[10]);

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

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr[regelnr].": ".$mr[Rekening]." --> notabedrag sluit aan ";
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KNBA()  //Kosten algemeen
{

  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "KNBA";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = "Other fees";
  $mr["Grootboekrekening"] = "KNBA";
  $mr["Valuta"]            = trim($data[10]);
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[19] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[19]);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[19]);
}/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FTT()  //Kosten tax
{

  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "FTT";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = $data[23]." ".$data[4];
  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = trim($data[10]);
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[17] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[17]);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($data[17]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[17]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()  //Kosten advies
{

  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "BEH";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = "Advisor fees";
  $mr["Grootboekrekening"] = "BEH";
  $mr["Valuta"]            = trim($data[10]);
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[19] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[19]);
    $mr["Bedrag"]          = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[19]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  // geld mutaties
{

  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[19] > 0)
  {
    $mr["Omschrijving"]      = "Storting";
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[19]);
    $mr["Bedrag"]          = _creditbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = "Ontrekking";
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[19]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  if ($data[29] == "CASH")
  {
    $valuta["afr"] = substr($data[24],0,3);
    $valuta["instr"] = $data[5];
    $valuta["comm"] = $data[30];

    $controleBedrag = 0;

    $mr["aktie"]              = "KRUIS";

    $mr["Rekening"]          = trim($data[2]).$valuta["afr"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);

    $mr["Valuta"]            = $valuta["afr"];
    $mr["Valutakoers"]       = _valutakoers($valuta["afr"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = "Valutatransactie ".$data[6]." ".$data[7]." ".$data[24];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data[7] > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[7]);
      $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"] ;
    }
    else
    {
      $mr["Debet"]             = abs($data[7]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"] ;
    }
    $mr["Transactietype"]    = "FX";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] != 0)
    {
      $controleBedrag += $mr["Bedrag"];

      $output[] = $mr;
    }

    $mr["Rekening"]          = trim($data[2]).$valuta["instr"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);

    $mr["Valuta"]            = $valuta["instr"];
    $mr["Valutakoers"]       = 1/$data[9];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = "Valutatransactie ".$data[6]." ".$data[7]." ".$data[24];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data[7] < 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[12]);
      $mr["Bedrag"]            = $mr["Credit"];
    }
    else
    {
      $mr["Debet"]             = abs($data[12]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    $mr["Transactietype"]    = "FX";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] != 0)
    {
      $controleBedrag += $mr["Bedrag"];
      $output[] = $mr;
    }

    $mr["Rekening"]          = trim($data[2]).$valuta["comm"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Valuta"]            = $valuta["comm"];
    $mr["Valutakoers"]       = _valutakoers($valuta["comm"]);
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[13]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

      checkControleBedrag(0,0);

  }
  else
  {
    $controleBedrag = 0;


    $mr["aktie"]             = "A";

    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    if ($data[29] == "FUT")
    {
      $mr["Fondskoers"] = 0;
    }
    else
    {
      $mr["Fondskoers"] = $data[9];
    }
    $mr["Aantal"]            = $data[7];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag         += $mr["Bedrag"];
    if ($data[29] == "OPT" OR $data[29] == "FUT")
    {
      $mr["Transactietype"]    = (trim($data[31]) == "O")?"A/O":"A/S";
    }
    else
    {
      $mr["Transactietype"]    = "A";
    }

    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    $output[] = $mr;

    $mr["Grootboekrekening"] = "KOST";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
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
    $mr["Debet"]             = abs($data[16]);
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
    $mr["Debet"]             = abs($data[18]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }



    if ($data[8] <> 0)  // aankoop obligatie
    {
      $mr["Grootboekrekening"] = "RENME";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers();
      //$mr[Fonds]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = abs($data[8]);
      $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag         += $mr["Bedrag"];
      $mr["Transactietype"]    = "";
      if ($mr["Bedrag"] <> 0)
        $output[] = $mr;

    }
    checkControleBedrag($controleBedrag,$data[19]*-1);
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
///

function do_STUKMUT()  // Deponnering/Lichting van stukken
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $meldArray[] = "regel ".$mr["regelnr"].": stukkenmutatie: controleer fondskoers";

  $controleBedrag = 0;

  $mr["aktie"]             = "STUKMUT";

  $mr["Rekening"]          = getRekening($data[2]."MEM");

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[7];
  $mr["Fondskoers"]        = ($data[32] != 0)?$data[32]/$mr["Aantal"]:0;



  if ($data[7] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Transactietype"]    = "D";
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Bedrag"]            = $mr["Bedrag"] * $mr["Valutakoers"];
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Transactietype"]    = "L";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Bedrag"]            = $mr["Bedrag"] * $mr["Valutakoers"];
  }
  $controleBedrag         += $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,$data[32] * -1 * $mr["Valutakoers"]);


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT0()  // Deponnering/Lichting van stukken
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $controleBedrag = 0;

  $mr["aktie"]             = "STUKMUT";

  $mr["Rekening"]          = getRekening($data[2]."MEM");

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[7];
  $mr["Fondskoers"]        = 0;

  if ($data[7] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Transactietype"]    = "D";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Transactietype"]    = "L";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = 0;
  }
  $controleBedrag         += $mr["Bedrag"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  checkControleBedrag($controleBedrag,$data[32]);


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $mr = array();
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  if ($data[29] == "CASH")
  {
    $valuta["afr"] = substr($data[24],0,3);
    $valuta["instr"] = $data[5];
    $valuta["comm"] = $data[30];

    $controleBedrag = 0;

    $mr["aktie"]              = "KRUIS";

    $mr["Rekening"]          = trim($data[2]).$valuta["afr"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);

    $mr["Valuta"]            = $valuta["afr"];
    $mr["Valutakoers"]       = _valutakoers($valuta["afr"]);
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = "Valutatransactie ".$data[6]." ".$data[7]." ".$data[24];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data[7] > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[7]);
      $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"] ;
    }
    else
    {
      $mr["Debet"]             = abs($data[7]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"] ;
    }
    $mr["Transactietype"]    = "FX";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] != 0)
    {
      $controleBedrag += $mr["Bedrag"];
      $output[] = $mr;
    }


    do_algemeen();
    $mr["Rekening"]          = trim($data[2]).$valuta["instr"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);

    $mr["Valuta"]            = $valuta["instr"];
    $mr["Valutakoers"]       = 1/$data[9];
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Omschrijving"]      = "Valutatransactie ".$data[6]." ".$data[7]." ".$data[24];
    $mr["Grootboekrekening"] = "KRUIS";
    if ($data[7] < 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[12]);
      $mr["Bedrag"]            = $mr["Credit"];
    }
    else
    {
      $mr["Debet"]             = abs($data[12]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    $mr["Transactietype"]    = "FX";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    if ($mr["Bedrag"] != 0)
    {
      $controleBedrag += $mr["Bedrag"];
      $output[] = $mr;
    }

    $mr["Rekening"]          = trim($data[2]).$valuta["comm"];
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Valuta"]            = $valuta["comm"];
    $mr["Valutakoers"]       = _valutakoers($valuta["comm"]);
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[13]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag         += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    checkControleBedrag(0,0);

  }
  else
  {
    $controleBedrag = 0;

    $mr = array();
    $mr["aktie"] = "V";
    do_algemeen();
    $mr["Rekening"] = getRekening($mr["Rekening"]);

    $mr["Omschrijving"] = "Verkoop " . $fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"] = $fonds["Valuta"];
    $mr["Valutakoers"] = _valutakoers();
    $mr["Fonds"] = $fonds["Fonds"];
    $mr["Aantal"]            = $data[7];
    if ($data[29] == "FUT")
    {
      $mr["Fondskoers"] = 0;
    }
    else
    {
      $mr["Fondskoers"] = $data[9];
    }

    $mr["Debet"] = 0;
    $mr["Credit"] = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"] = _creditbedrag();
    $controleBedrag += $mr["Bedrag"];

    if ($data[29] == "OPT" OR $data[29] == "FUT")
    {
      $mr["Transactietype"]    = (trim($data[31]) == "O")?"V/O":"V/S";
    }
    else
    {
      $mr["Transactietype"] = "V";
    }

    $mr["Verwerkt"] = 0;
    $mr["Memoriaalboeking"] = 0;

    $output[] = $mr;

    $mr["Grootboekrekening"] = "KOST";
    $mr["Aantal"] = 0;
    $mr["Fondskoers"] = 0;
    $mr["Credit"] = 0;
    $mr["Debet"] = abs($data[14]);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];

    $mr["Transactietype"] = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


    $mr["Grootboekrekening"] = "KOBU";
    $mr["Aantal"] = 0;
    $mr["Fondskoers"] = 0;
    $mr["Credit"] = 0;
    $mr["Debet"] = abs($data[16]);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];

    $mr["Transactietype"] = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }

    $mr["Grootboekrekening"] = "KOBU";
    $mr["Aantal"] = 0;
    $mr["Fondskoers"] = 0;
    $mr["Credit"] = 0;
    $mr["Debet"] = abs($data[18]);
    $mr["Bedrag"] = _debetbedrag();
    $controleBedrag += $mr["Bedrag"];

    $mr["Transactietype"] = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


    if ($data[8] <> 0)  // aankoop obligatie
    {
      $mr["Grootboekrekening"] = "RENOB";
      $mr["Valuta"] = $fonds["Valuta"];
      $mr["Valutakoers"] = _valutakoers();
      //$mr[Fonds]             = "";
      $mr["Aantal"] = 0;
      $mr["Fondskoers"] = 0;
      $mr["Credit"] = abs($data[8]);
      $mr["Debet"] = 0;
      $mr["Bedrag"] = _creditbedrag();
      $controleBedrag += $mr["Bedrag"];

      $mr["Transactietype"] = "";
      if ($mr["Bedrag"] <> 0)
      {
        $output[] = $mr;
      }


    }
    checkControleBedrag($controleBedrag, $data[19]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Dividend
{
  global $fonds, $data, $mr, $output,$meldArray;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "DIV";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  transidSpecial();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  if ($data[19] > 0)
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[19]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  else
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Debet"]             = abs($data[14]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }



  checkControleBedrag($controleBedrag,$data[19]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIVBE()  //Dividend belasting
{
  global $fonds, $data, $mr, $output,$meldArray;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "DIVBE";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  transidSpecial();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  if ($data[19] > 0)
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[19]);
    $mr["Bedrag"]            = _creditbedrag();

  }
  else
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Debet"]             = abs($data[14]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }



  checkControleBedrag($controleBedrag,$data[19]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENTE()  // rente
{

  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]              = "RENTE";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Valuta"]            = trim($data[10]);
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = "Rente ".$mr["Rekening"];
  $mr["Grootboekrekening"] = "RENTE";
  if ($data[19] > 0)
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[19]);
    $mr["Bedrag"]          = $mr["Credit"];
  }
  else
  {
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[19]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_COUP()
{
  global $fonds, $data, $mr, $output,$meldArray;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "COUP";
  do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Valuta"]            = $data[10];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  transidSpecial();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = (trim($data[6]) == "24")?"RENME":"RENOB";
  if ($data[19] > 0)
  {
  $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[19]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  else
  {
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    $mr["Debet"]             = abs($data[19]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();

  }
  $controleBedrag         += $mr["Bedrag"];

  $output[] = $mr;

//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Debet"]             = abs($data[14]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag         += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }



  checkControleBedrag($controleBedrag,$data[19]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_ASS()  //assignment opties
{

  global $fonds, $data, $mr, $output, $meldArray;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "ASS";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"] = trim($data[2])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Omschrijving"]      = "Assignment ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[7];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data[19]*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EXE()  //exercise opties
{

  global $fonds, $data, $mr, $output, $meldArray;

  $controleBedrag = 0;

  $mr = array();
  $mr["aktie"]             = "EXE";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Rekening"] = trim($data[2])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  $mr["Omschrijving"]      = "Exercise ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[7];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/S";

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data[19]*-1);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_error($do_func)
{

	echo "<BR>LYNX transactiecode $do_func bestaat niet!";
}


?>