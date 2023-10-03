<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/13 13:57:56 $
 		File Versie					: $Revision: 1.9 $

 		$Log: jblux_functies.php,v $
 		Revision 1.9  2020/07/13 13:57:56  cvs
 		renob/renme boeken
 		
 		Revision 1.8  2020/06/29 11:46:27  cvs
 		call 7829
 		
 		Revision 1.7  2020/04/10 13:07:52  cvs
 		call 8554
 		
 		Revision 1.6  2020/03/30 08:48:25  cvs
 		call 7829
 		
 		Revision 1.5  2020/03/27 09:18:00  cvs
 		call 7829
 		
 		Revision 1.4  2020/03/09 14:17:00  cvs
 		call 8413
 		
 		Revision 1.3  2020/03/09 13:29:39  cvs
 		call 8413
 		
 		Revision 1.2  2020/02/24 15:26:51  cvs
 		call 7829
 		
 		Revision 1.1  2019/08/23 12:28:56  cvs
 		call 7829
 		



*/

function jbluxDate($in)
{
  return substr($in,0,4)."-".substr($in,4,2)."-".substr($in,6,2);
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

function ontnullen($t)
{

}

function getRekening($rekeningNr="-1", $depot="jblux")
{
  global $mr, $meldArray, $data, $row;

  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      logError($mr["regelnr"], "Rekening niet gevonden ($rekeningNr)");
      return false;
    }
    
  }
}

function RekeningNrFromIBAN($data)
{
  $data = str_replace(" ","", $data);
  return substr($data,-12);
}


function makeDate($datum)
{
  return substr($datum,0,4)."-".substr($datum,4,2)."-".substr($datum,6,2);
}


function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "JB|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
  global $data, $mr;

  if ($data["afrekenValuta"] == "EUR" AND $data["fondsValuta"] != "EUR")
  {
    return  -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
  else if ($data["afrekenValuta"] == $data["fondsValuta"] )
  {
    return -1 * $mr["Debet"];
  }
  else
  {
    return -99999;
  }
}

function _creditbedrag()
{
	global $data, $mr;

	if ($data["afrekenValuta"] == "EUR" AND $data["fondsValuta"] != "EUR")
  {
    return  ($mr["Credit"] * $mr["Valutakoers"]);
  }
	else if ($data["afrekenValuta"] == $data["fondsValuta"] )
	{
	  return $mr["Credit"];
  }
	else
  {
    return 99999;
  }
}

function logError($regel, $txt=0)
{
  global $meldArray;
  $meldArray[] = "regel {$regel}: {$txt}";
}

function JBlux_getfonds($BankFondscode="",$isin="", $valuta="" )
{
  global $fonds, $mr, $row;
  $db = new DB();

  // bankfonds code
  $fondsNotFound = true;

  if ($BankFondscode <> "")
  {
    $query = "SELECT * FROM Fondsen WHERE  JBLuxcode = '" . trim($BankFondscode) . "' ";
    if ($fonds = $db->lookupRecordByQuery($query))
    {
      debug($fonds, $query);
      $fondsNotFound = false;
    }
  }

  if ($fondsNotFound)        // isin/val
  {
    if ($isin <> "" AND $valuta <> "")
    {
      $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$isin."' AND Valuta = '".$valuta."'  ";
      if (!$fonds = $db->lookupRecordByQuery($query))
      {
        logError($row,"Fonds niet gevonden ".$isin."/".$valuta);
      }
    }
    else
    {
      logError($row, "Fonds niet gevonden via bankcode:  ".$BankFondscode);
    }
  }
  return true;
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

function _valutakoers()
{
  global $data, $mr;
  $db = new DB();

  if ($data["afrekenValuta"] == "EUR" AND $mr["Valuta"] != "EUR" )
  {
    return  $data["valutakoersRekFonds"];
  }
  else if ($data["afrekenValuta"] != "EUR" AND $data["afrekenValuta"] == $mr["Valuta"] AND $data["valutakoersFondsEur"] == 1)
  {
    $query = "
      SELECT 
        * 
      FROM 
        Valutakoersen 
      WHERE 
        Valuta='{$mr["Valuta"]}' 
      AND 
        Datum <= '{$mr["Boekdatum"]}' 
      ORDER BY Datum DESC";

    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  else if ($data["afrekenValuta"] == $mr["Valuta"] )
  {
    return $data["valutakoersFondsEur"];
  }
  else
  {
    return 999;
  }
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

  $mr["bestand"]           = $_file;
  $mr["regelnr"]           = $data["row"];
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
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan: bank= ".$notabedrag." / AIRS = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EXPOPT()
{
  global $fonds, $data, $mr, $output;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "EXPOPT";
  do_algemeen();

  $mr["Rekening"]          = $data["portefeuille"]."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoersFondsEur"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  if ($mr["Aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
    $mr["Transactietype"]    = "A/S";
  }
  else
  {
    $mr["Omschrijving"]      = "Expiratie ".$fonds["Omschrijving"];
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
    $mr["Transactietype"]    = "V/S";
  }

  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  checkControleBedrag(0,0);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT()
{
  global $fonds, $data, $mr, $output, $afw;
  debug($data);
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "STUKMUT";
  do_algemeen();

  $mr["Rekening"]          = $data["portefeuille"]."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["valutakoersFondsEur"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 1;

  if ($mr["Aantal"] > 0)
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
    $mr["Transactietype"]    = "D";
  }
  else
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
    $mr["Transactietype"]    = "L";
  }

  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Fonds"] = "";

  $mr["Fondskoers"] = 0;
  $mr["Transactietype"] = "";


  if ($mr["Aantal"] > 0)   // standaard tegenboeking
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"]  = "STORT";
    $mr["Debet"]              = 0;
    $mr["Credit"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]             = $mr["Credit"];

  }
  else
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = -1 * $mr["Debet"] ;


  }
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  if ($data["aantal"] < 0)
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"]  = "RENOB";
    $mr["Debet"]              = 0;
    $mr["Credit"]             = abs($data["opgelopenRente"]);
    $mr["Bedrag"]             = $mr["Credit"];
  }
  else
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"] = "RENME";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["opgelopenRente"]);
    $mr["Bedrag"]            = -1 * $mr["Debet"] ;
  }
  if ($mr["Bedrag"] <> 0)
  {
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
  }

  if ($data["aantal"] > 0)  // tegenboeking rente
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"]  = "STORT";
    $mr["Debet"]              = 0;
    $mr["Credit"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]             = $mr["Credit"];
  }
  else
  {
    $mr["Aantal"] = 0;
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = -1 * $mr["Debet"] ;
  }
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag, $data["sysNettobedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VERW()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]             = "VERW";
  do_algemeen();
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $fonds1 = JBlux_getfonds($data[8], $data[11], $data[9]);
  $fonds2 = JBlux_getfonds($data[57], $data[59], $data[62]);

  if ($fonds1 == $fonds2 AND abs($data[54]) == abs($data[28]))
  {
    $meldArray[] = "regel ".$data["row"].": verwisseling 1 fonds: overgeslagen";
  }
  else
  {
    $meldArray[] = "regel ".$data["row"].": verwisseling fonds: nog in te regelen ";
  }
  return;

//  $mr["Rekening"]          = $data["portefeuille"]."MEM";
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
//
//
//  $mr["Grootboekrekening"] = "FONDS";
//  $mr["Valuta"]            = $fonds["Valuta"];
//  $mr["Valutakoers"]       = $data["valutakoersFondsEur"];
//  $mr["Fonds"]             = $fonds["Fonds"];
//  $mr["Aantal"]            = $data["aantal"];
//  $mr["Fondskoers"]        = $data["koers"];
//
//  $mr["Verwerkt"]          = 0;
//  $mr["Memoriaalboeking"]  = 1;
//
//  if ($mr["Aantal"] > 0)
//  {
//    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
//    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
//    $mr["Credit"]            = 0;
//    $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
//    $mr["Transactietype"]    = "D";
//  }
//  else
//  {
//    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
//    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
//    $mr["Debet"]             = 0;
//    $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
//    $mr["Transactietype"]    = "L";
//  }
//
//  $controleBedrag       += $mr["Bedrag"];
//  $output[] = $mr;
//
//  $mr["Fonds"] = "";
//  $mr["Aantal"] = 0;
//  $mr["Fondskoers"] = 0;
//  $mr["Transactietype"] = "";
//
//  if ($mr["Aantal"] > 0)
//  {
//    $mr["Grootboekrekening"]  = "STORT";
//    $mr["Debet"]              = 0;
//    $mr["Credit"]             = abs($mr["Bedrag"]);
//    $mr["Bedrag"]             = $mr["Credit"];
//  }
//  else
//  {
//    $mr["Grootboekrekening"] = "ONTTR";
//    $mr["Credit"]            = 0;
//    $mr["Debet"]             = abs($mr["Bedrag"]);
//    $mr["Bedrag"]            = -1 * $mr["Debet"] ;
//  }
//  if ($mr["Bedrag"] <> 0)
//  {
//    $output[] = $mr;
//  }
//
//  checkControleBedrag($controleBedrag, $data["sysNettobedrag"]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FXVV()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();

  $rek1     = $data[43];
  $rek2     = $data[8];
  $valuta1  = $data[42];
  $valuta2  = $data[9];
  $bedrag1  = $data[40];
  $bedrag2  = $data[28];
  $valKoers1 = 1/($data[53]/$data[40]);
  $valKoers2 = $data[46];

  $mr["Rekening"]          = $rek1.$valuta1;
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Valuta"]            = $valuta1;
  $mr["Valutakoers"]       = abs(1/$valKoers1);
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
  $mr["Valutakoers"]       = abs(1/$valKoers2);
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


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FX()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();


  $valuta1  = $data[9];
  $valuta2  = $data[42];
  if ($valuta1 != "EUR" AND $valuta2 != "EUR")
  {
    //$meldArray[] = "regel ".$data["row"].": Forex in VV {$valuta1}/{$valuta2}, deze handmatig boeken";
    do_FXVV();
    return;
  }

  if ($valuta1 == "EUR")
  {
    $pootEUR = array(
      "rekening" => $data[8],
      "valuta"  => $data[9],
      "bedrag" => $data[28],
    );
    $pootVV = array(
      "rekening" => $data[43],
      "valuta"  => $data[42],
      "bedrag" => $data[40],
    );
  }
  else
  {
    $pootEUR = array(
      "rekening" => $data[43],
      "valuta"  => $data[42],
      "bedrag" => $data[40],
    );
    $pootVV = array(
      "rekening" => $data[8],
      "valuta"  => $data[9],
      "bedrag" => $data[28],
    );
  }
  debug($pootEUR,"pootEUR");
  debug($pootVV,"pootVV");
// poot 1 boeken
  $mr["Rekening"]          = $pootEUR["rekening"].$pootEUR["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = 1/$data[41];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "KRUIS";

  if ($pootEUR["bedrag"] < 0)
  {
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = (-1 * $mr["Debet"]) * $mr["Valutakoers"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($pootVV["bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  debug($mr, "MR EUR");
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  // poot 2 boeken

  $mr["Rekening"]          = $pootVV["rekening"].$pootVV["valuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Valuta"]            = $pootVV["valuta"];
  $mr["Valutakoers"]       = 1/$data[41];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "KRUIS";

  if ($pootEUR["bedrag"] > 0)
  {
    $mr["Debet"]             = abs($pootVV["bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($pootVV["bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag         += $mr["Bedrag"];
  debug($mr, "MR VV");
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  //checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_EFF_KNBA()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();

  $mr["aktie"]              = "effknba";
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];

  if ($data["nettoBedrag2"] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($data["nettoBedrag2"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["nettoBedrag2"]);
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

  checkControleBedrag($controleBedrag,$data["nettoBedrag2"]);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function geldMutatie($grootboek="", $omschrijving="")
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
  $controleBedrag = 0;
  $mr = array();
  if ($omschrijving == "")
  {
    $omschrijving = $data["omschrijving"];
  }


  $mr["aktie"]              = ($grootboek == "")?"geldmut":$grootboek;
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $omschrijving;

  if ($data["nettoBedrag"] < 0)
  {
    $mr["Grootboekrekening"] = ($grootboek == "")?"ONTTR":$grootboek;
    $mr["Debet"]             = abs($data["nettoBedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
    $mr = $afw->reWrite("ONTTR",$mr);
  }
  else
  {
    $mr["Grootboekrekening"] = ($grootboek == "")?"STORT":$grootboek;
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
function do_BEH()
{
  global $fonds, $data, $mr, $output, $meldArray;
  $controleBedrag = 0;
  $mr = array();

  $mr["aktie"]              = "BEH";
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valuta"]            = $data["afrekenValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Omschrijving"]      = $data["omschrijving"];
  $mr["Grootboekrekening"] = "BEH";
  if ($data["nettoBedrag"] < 0)
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
function do_BEW()   { geldMutatie("BEW"); }
function do_MUT()   { geldMutatie();      }
function do_RENTE() { geldMutatie("RENTE"); }
function do_KNBA() { geldMutatie("KNBA"); }

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_V()  // Verkoop van stukken
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
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

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]            = 0;
  $mr["Credit"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["provisie"]);
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
  $mr["Debet"]             = abs($data["brokerKosten"] + $data["taxes"] + $data["FTT"]);
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
  $mr["Debet"]             = abs($data["OverigeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FORW()  // Forward
{

  global $fonds, $data, $mr, $output,$meldArray;
  debug($data);
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

  JBlux_getfonds($data[12],"XXXX",$data["fondsValuta"]);

  if ($data[31] == "EUR" OR $data[29] != "EUR")
  {
    $meldArray[] = "{$data["row"]}: Onbekende forward transactie";
    return;
  }


  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data["fondsValuta"];
  $mr["Valutakoers"]       = $data["valutakoersFondsEur"];
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[38];

  switch ($data["transactieCode"])
  {
    case "3_S_FORWARD":
      $mr["Rekening"]          = $data[43]."EUR";
      $mr["Transactietype"]    = "A";
      $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
      break;
    case "3_B_FORWARD":
      $mr["Rekening"]          = $data[13]."EUR";
      $mr["Transactietype"]    = "V";
      $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
      break;
    default:
      $meldArray[] = "{$data["row"]}: Onbekende ({$data["transactieCode"]}) forward transactie";
      return;
  }


  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = 0;

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag(1,1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_OPTV()  // Optie Verkoop
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = (strtoupper($data[49]) == "CLOSE")?"V/S":"V/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]            = 0;
  $mr["Credit"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["provisie"]);
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
  $mr["Debet"]             = abs($data["brokerKosten"] + $data["taxes"] + $data["FTT"]);
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
  $mr["Debet"]             = abs($data["OverigeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_OPTA()  // Optie Aankoop
{

  global $fonds, $data, $mr, $output,$meldArray;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "A";
  do_algemeen();

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = (strtoupper($data[49]) == "CLOSE")?"A/S":"A/O";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["provisie"]);
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
  $mr["Debet"]             = abs($data["brokerKosten"] + $data["taxes"] + $data["FTT"]);
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
  $mr["Debet"]             = abs($data["OverigeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
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

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["opgelopenRente"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["provisie"]);
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
  $mr["Debet"]             = abs($data["brokerKosten"] + $data["taxes"] + $data["FTT"]);
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
  $mr["Debet"]             = abs($data["OverigeKosten"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
//	debug($data);
  $controleBedrag = 0;
  do_algemeen();
	$mr["aktie"]              = "DIV";

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";


  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["koers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();

	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  $controleBedrag       += $mr["Bedrag"];
	$output[] = $mr;

	$mr["Grootboekrekening"] = "DIVBE";
	$mr["Debet"]             = abs($data["taxes"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Debet"]             = abs($data["brokerKosten"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

	if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_RENOB()  //renob
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
//	debug($data);
  $controleBedrag = 0;
  do_algemeen();
  $mr["aktie"]              = "RENOB";

  $mr["Rekening"]          = $data["rekening"].$data["afrekenValuta"];
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";


  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["koers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $controleBedrag       += $mr["Bedrag"];
  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Debet"]             = abs($data["taxes"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,$data["nettoBedrag"]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////




/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT($txt)
{
  global $row;
  echo "<BR>$row: NVT transactie $txt overgeslagen!";
  return true;
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>