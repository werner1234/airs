<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/11 15:57:20 $
 		File Versie					: $Revision: 1.17 $

 		$Log: pictet_functies.php,v $
 		Revision 1.17  2020/02/11 15:57:20  cvs
 		call 8411
 		
 		Revision 1.16  2019/10/09 09:57:31  cvs
 		call 8061
 		
 		Revision 1.15  2019/07/08 14:25:46  cvs
 		call 7927
 		
 		Revision 1.14  2019/06/28 06:54:55  cvs
 		call 7917
 		
 		Revision 1.13  2019/06/25 15:09:30  cvs
 		call 7917
 		
 		Revision 1.12  2018/11/23 13:46:04  cvs
 		call 7362
 		
 		Revision 1.11  2018/10/15 13:21:55  cvs
 		call 7227
 		
 		Revision 1.10  2018/10/03 15:32:40  cvs
 		no message
 		
 		Revision 1.9  2018/10/01 06:42:53  cvs
 		call 7173
 		
 		Revision 1.8  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/05/30 13:11:53  cvs
 		call 6888
 		
 		Revision 1.6  2018/05/16 13:32:19  cvs
 		call 6888
 		
 		Revision 1.5  2018/05/16 11:28:19  cvs
 		no message
 		
 		Revision 1.4  2018/03/27 09:59:30  cvs
 		call 6768
 		
 		Revision 1.3  2018/03/14 16:41:09  cvs
 		call 6686
 		
 		Revision 1.2  2018/01/22 11:06:45  cvs
 		call 4125
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/
function convertRecord($data)
{
  //$data[19] = stripAlpha($data[19]);
  $data[20] = stripAlpha($data[20]);
  $data[20] = stripAlpha($data[20]);
  $data[22] = stripAlpha($data[22]);
  $data[23] = stripAlpha($data[23]);
  $data[24] = stripAlpha($data[24]);
  $data[25] = stripAlpha($data[25],true);
  $data[26] = stripAlpha($data[26]);
  $data[30] = stripAlpha($data[30]);
  $data[31] = stripAlpha($data[31]);
  $data[32] = stripAlpha($data[32]);
  $data[33] = stripAlpha($data[33]);
  $data[35] = stripAlpha($data[35]);
  $data[36] = stripAlpha($data[36]);
  $data[37] = stripAlpha($data[37]);
  $data[38] = stripAlpha($data[38]);
  $data[40] = stripAlpha($data[40],true);
  $data[41] = stripAlpha($data[41]);
  $data[42] = stripAlpha($data[42]);
  $data[43] = stripAlpha($data[43]);
  $data[44] = stripAlpha($data[44]);
  $data[45] = stripAlpha($data[45]);
  $data[46] = stripAlpha($data[46]);
  $data[47] = stripAlpha($data[47]);
  $data[48] = stripAlpha($data[48]);
  $data[49] = stripAlpha($data[49]);
  $data[50] = stripAlpha($data[50]);
  for ($x=0;$x < count($data); $x++)
  {
    $data[$x] = trim($data[$x]);
  }
  return $data;
}

function stripAlpha($bedrag,$returnFloat=false)  // haal alpha numerieke tekens uit bedrag
{
  $out = "";
  $valid = array("-",".","0","1","2","3","4","5","6","7","8","9");
  for ($x=0; $x < strlen($bedrag);$x++)
  {
    $char = $bedrag[$x];
    if (in_array($char,$valid))
    {
      $out .= $char;
    }
  }
  if ($returnFloat)
  {
    return (float) $out;
  }
  else
  {
    return $out;
  }
  
}


function getRekening($rekeningNr="-1", $depot="PIC")
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

function checkVoorDubbelInRM($mr, $action="import")
{
  global $meldArray, $error, $row;
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
    if ($action == "import")
    {
      $meldArray[] = "regel ".$mr["regelnr"].": rekenmutatie is al aanwezig (oa.RMid ".$rec["id"]."/ bankTrId ".$mr["bankTransactieId"].")";
      return true;
    }
    else
    {
      $error[] = "$row :rekenmutatie is al aanwezig (oa.RMid ".$rec["id"]."/ bankTrId ".$mr["bankTransactieId"].")";
      return true;
    }

  }
  return false;
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "PIC|".$portefeuille."|".$valuta;
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
	
	if ($valutaLookup == true)
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;

	$valuta = $data[18];
	$valutaLookup = false;
	if ($valuta <> "EUR" AND $mr["Valuta"] == $valuta)
	{
    $mr["Valuta"] = $valuta;
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    $valutaLookup = true;
    return $laatsteKoers["Koers"];
	}
	else
	  return ($data[26] == 100)?1:$data[26];
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $data[0];
	$mr["bankTransactieId"]  = $data[1]."-".$data[40];

	$mr["Boekdatum"]         = substr($data[13],0,4)."-".substr($data[13],4,2)."-".substr($data[13],6,2);
  $mr["settlementDatum"]   = substr($data[14],0,4)."-".substr($data[14],4,2)."-".substr($data[14],6,2);
  $mr["Rekening"]    = str_replace(".","-",$data[41]).$data[18];

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

function do_BAV()
{
  global $data;
  if ($data[21] < 0)
  {
    do_V();
  }
  else
  {
    do_A();
  }
}


function do_A()  // Aankoop van stukken
{
 
  global $fonds, $data, $mr, $output,$meldArray;

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "A";
	do_algemeen();
  checkVoorDubbelInRM($mr);
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[21];
//	$mr["Fondskoers"]        = round(abs($data[23]/$data[21]/$fonds["Fondseenheid"]),6);
	$mr["Fondskoers"]        = $data[24];
	$mr["Credit"]             = 0;
	$mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _debetbedrag();
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[22];
  if ($mr[Valuta] == "EUR") $mr["Valutakoers"]  = 1;
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs( $data[36]) +
                             abs( $data[37]) +
                             abs( $data[38]) +
                             abs( $data[46]) +
                             abs( $data[48]) +
                             abs( $data[50]) ;  // diverse kosten verzameld
  
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	if ($fonds["fondssoort"] == "OBL" AND $fonds["inflatieGekoppeld"] == 0 )
  {
    $mr["Grootboekrekening"] = "RENME";
    $mr["Valuta"]            = $data[22];
    if ($mr[Valuta] == "EUR") $mr["Valutakoers"]  = 1;
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = round(abs($data[23]) - ( abs($data[21] * $data[24] * $fonds["Fondseenheid"])) ,2 );

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }


  checkControleBedrag($controleBedrag,$data[27]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray;

	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "V";
	do_algemeen();
  checkVoorDubbelInRM($mr);
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[21];
//	$mr["Fondskoers"]        = round(abs($data[23]/$data[21]/$fonds["Fondseenheid"]),6);
	$mr["Fondskoers"]        = $data[24];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[22];
  if ($mr[Valuta] == "EUR") $mr["Valutakoers"]  = 1;
  //$mr[Fonds]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs( $data[36]) +
                             abs( $data[37]) +
                             abs( $data[38]) +
                             abs( $data[46]) +
                             abs( $data[48]) +
                             abs( $data[50]) ;  // diverse kosten verzameld
  
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if ($fonds["fondssoort"] == "OBL" AND $fonds["inflatieGekoppeld"] == 0 )
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $data[22];
    if ($mr[Valuta] == "EUR") $mr["Valutakoers"]  = 1;
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]            = 0;
    $mr["Credit"]             = round(abs($data[23]) - ( abs($data[21] * $data[24] * $fonds["Fondseenheid"])) ,2 );

    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }
  checkControleBedrag($controleBedrag,$data[27]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENTE()  //Rente of couponrente
{

  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "R";
	do_algemeen();

  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  checkVoorDubbelInRM($mr);

  if (count($fonds) != 0)
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Fonds"]             = $fonds["Fonds"];
  }
  else
  {
    if ($data[23] > 0)
    {
      $mr["Omschrijving"]      = "Creditrente";
    }
    else
    {
      $mr["Omschrijving"]      = "Debetrente";
    }

    $mr["Grootboekrekening"] = "RENTE";
    $mr["Fonds"]             = "";
  }

  $mr["Valuta"]            = $data[22];
  $mr["Valutakoers"]       = _valutakoers();

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
		
  if ($data[23] > 0)
  {
    $mr["Debet"]       = 0;
    $mr["Credit"]      = abs($data[23]);
    $mr["Bedrag"]      = _creditbedrag();
    $controleBedrag    = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]           = abs($data[23]);
    $mr["Credit"]          = 0;
    $mr["Bedrag"]          = _debetbedrag();
    $controleBedrag        = $mr["Bedrag"];

  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data[27]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "L";
  do_algemeen();

  $mr["Rekening"]          = substr($mr["Rekening"],0,-3)."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  if (checkVoorDubbelInRM($mr))
  {
   return true;
  }
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[21];
  $mr["Fondskoers"]        = round(abs($data[23]/$data[21]/$fonds["Fondseenheid"]),6);
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  $output[] = $mr;

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

  checkControleBedrag($controleBedrag,55555);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_Lnul()  //Lossing van obligaties
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "L";
  do_algemeen();

  $mr["Rekening"]          = substr($mr["Rekening"],0,-3)."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  if (checkVoorDubbelInRM($mr))
  {
    return true;
  }
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[21];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "VKSTO";
  $mr["Rekening"]    = str_replace(".","-",$data[41]).$data[18];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = "FractieVer. ".$fonds["Omschrijving"];
  $mr["Aantal"]            = 0;
  if ($data[27] < 0)
  {
    $mr["Debet"]             = abs($data[27]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Credit"]             = abs($data[27]);
    $mr["Debet"]              = 0;
    $mr["Bedrag"]             = _creditbedrag();
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data[27]);

}

function do_D()  //Deponering
{

  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "D";
  do_algemeen();

  $mr["Rekening"]          = substr($mr["Rekening"],0,-3)."MEM";
//  debug($mr, $mr["Rekening"]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if (checkVoorDubbelInRM($mr))
  {
    return true;
  }
  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[21];
  $mr["Fondskoers"]        = round(abs($data[23]/$data[21]/$fonds["Fondseenheid"]),6);
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "STORT";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fonds"]             = "";
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]             = abs($mr["Bedrag"]);
  $mr["Debet"]            = 0;
  $mr["Bedrag"]            =  $mr["Credit"];
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,55555);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_Dnul()  //Deponering
{

  global $fonds, $data, $mr, $output, $meldArray;

  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "D";
  do_algemeen();

  $mr["Rekening"]          = substr($mr["Rekening"],0,-3)."MEM";
//  debug($mr, $mr["Rekening"]);
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if (checkVoorDubbelInRM($mr))
  {
    return true;
  }

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[21];
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,0);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "DV";
	do_algemeen();
  checkVoorDubbelInRM($mr);
	$mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "DIV";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             =  $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	if ($data[25] < 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr["Debet"]             = abs($data[21] * $data[24]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[21] * $data[24]);
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
	if ($data[25] < 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr["Debet"]           = 0;
	  $mr["Credit"]          = abs($data[37]);
	  $mr["Bedrag"]          = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
	}
	else
	{
	  $mr["Debet"]           = abs($data[37]);
	  $mr["Credit"]          = 0;
	  $mr["Bedrag"]          = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	}
	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


	$mr["Grootboekrekening"] = "KNBA";

  if ($data[25] < 0)  // als veld negatief betreft een correctie Dividend
  {
    $mr["Debet"]           = 0;
    $mr["Credit"]          = abs($data[36] + $data[38]);
    $mr["Bedrag"]          = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]           = abs($data[36] + $data[38]);
    $mr["Credit"]          = 0;
    $mr["Bedrag"]          = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,$data[27]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_CA()  //Corp action
{

  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;
  // 2018-01-222: geldt nu alleen nog voor de ETRBL later switch case maken voor nieuwe codes
  $mr["aktie"]              = "CA";
  do_algemeen();
  checkVoorDubbelInRM($mr);
  switch ($fonds["fondssoort"])
  {
    case "OBL":
      $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "RENOB";
      break;
    default:
      $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
      $mr["Grootboekrekening"] = "DIV";
  }

  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             =  $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($data[27] < 0)
  {
    $mr["Debet"]             = abs($data[25]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[25]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,$data[27]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX($legA, $legB, $key)
{
  global $mr, $output, $meldArray, $data;
  
  
//  debug($legA, $key);
//  debug($legB, $key);
  $data = $legA;
  do_algemeen();
  if (checkVoorDubbelInRM($mr))
  {
    return true;
  }
  $mr["Fonds"]               = "";
  $mr["aktie"]             = "Mut.";
  $controleBedrag = 0;

  $mr["Grootboekrekening"] = "KRUIS";
  if ($legA[25] < 0)
  {
    $DebRec = $legA;
    $CredRec = $legB;
  }
  else
  {
    $DebRec = $legB;
    $CredRec = $legA;
  }
  $oms = explode("(",$data[20]);
  $oms = substr($oms[1],0,-1);
//    debug($CredRec,"Cred");
//    debug($DebRec,"Deb");
//    debug($oms);
  if ( $DebRec[18] == "EUR" AND $CredRec[18] <> "EUR" )
  {
   
    $mr["Valuta"]            = $CredRec[18];
    $mr["Valutakoers"]       = abs($DebRec[27] / $CredRec[27]);
    
    
    $mr["Omschrijving"]      = "Valutatransactie ".$oms;
//    $mr["Omschrijving"]      = $DebRec[20];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$DebRec[41]).$DebRec[18];

    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($CredRec[27]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($mr["Debet"] * $mr["Valutakoers"]) * -1;

    $output[] = $mr;
    $controleBedrag += $mr["Debet"];

//    $mr["Omschrijving"]      = $CredRec[20];
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$CredRec[41]).$CredRec[18];
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[27]);
    $mr["Bedrag"]            = $mr["Credit"];

    $output[] = $mr;

    $controleBedrag -= $mr["Credit"];

  }
  elseif ($DebRec[18] <> "EUR" AND $CredRec[18] == "EUR" )
  {
   
    $mr["Valuta"]            = $DebRec[18];
    $mr["Valutakoers"]       =  abs($CredRec[27] / $DebRec[27]);
    $mr["Omschrijving"]      = "Valutatransactie ".$oms;
//    $mr["Omschrijving"]      = $DebRec[20];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$DebRec[41]).$DebRec[18];
    
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[27]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[27]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

//    $mr["Omschrijving"]      = $CredRec[20];
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$CredRec[41]).$CredRec[18];
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($DebRec[27]);
    $mr["Bedrag"]            = abs($mr["Credit"] * $mr["Valutakoers"]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;

  }
  elseif ($DebRec[18] <> $CredRec[18] )
  {
   
    $mr["Valuta"]            = $DebRec[22];
    $mr["Valutakoers"]       = 0;
    $mr["Omschrijving"]      = "Valutatransactie ".$oms;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$DebRec[41]).$DebRec[18];
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[25]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[25]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Valuta"]            = $CredRec[22];
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$CredRec[41]).$CredRec[18];
    if (!getRekening($mr["Rekening"]))
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[6]);
    $mr["Bedrag"]            = abs($CredRec[6]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }
  else
  {
   
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $DebRec[22];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Omschrijving"]      = $DebRec[20];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$DebRec[41]).$DebRec[18];
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }

    $mr["Debet"]             = abs($DebRec[25]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = abs($DebRec[25]) * -1;
    $controleBedrag += $mr["Debet"];
    $output[] = $mr;

    $mr["Grootboekrekening"] = "STORT";
    $mr["Omschrijving"]      = $CredRec[20];
    $mr["Valuta"]            = $CredRec[22];
    $mr["bankTransactieId"]  = $key;
    $mr["Rekening"]          = str_replace(".","-",$CredRec[41]).$CredRec[18];
    if (!getRekening($mr["Rekening"])) 
    {
      return false;
    }
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($CredRec[25]);
    $mr["Bedrag"]            = abs($CredRec[25]);
    $controleBedrag -= $mr["Credit"];
    $output[] = $mr;
  }
  checkControleBedrag($controleBedrag,0);
  
    
}

function do_MUT()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "OP";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("MUTONTTR",$mr);
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  { 
    $mr["aktie"]              = "ST";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "STORT";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[27]);
  }
}

function do_KNBA()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;


	do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "KNBA";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("MUTKNBA",$mr);
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  {
    $mr["aktie"]              = "KNBA";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("MUTKNBA",$mr);
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[27]);
  }
}


function do_BEH()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "BEH";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "BEH";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("BEH",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  {
    $mr["aktie"]              = "BEH";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "BEH";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("BEH",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
}

function do_BEW()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "BEW";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "BEW";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("BEW",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  {
    $mr["aktie"]              = "BEW";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "BEW";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("BEW",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
}

function do_KOBU()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "KOBU";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("KOBU",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  {
    $mr["aktie"]              = "KOBU";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("KOBU",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
}

function do_KOST()
{
  global $fonds, $data, $mr, $output,$meldArray, $afw;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  checkVoorDubbelInRM($mr);
  if ( $data[27] < 0 )
  {
    $mr["aktie"]              = "KOST";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KOST";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[27]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("KOST",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
  else
  {
    $mr["aktie"]              = "KOST";
    $mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[20];
    $mr["Grootboekrekening"] = "KOST";
    $mr["Valuta"]            = $data[18];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[27]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("KOST",$mr);
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[27]);
  }
}

function do_NVT()
{
  global $meldArray, $data;
  $meldArray[] = "regel ".$data[0].":<b> met transactiecode ".$data[3]." overgeslagen</b>";
}

function do_error($kIndx, $func)
{
	global $do_func;
	echo "<BR>FOUT transactie $func niet gedefinieerd! ($kIndx)";
}


?>