<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/06 12:41:36 $
 		File Versie					: $Revision: 1.17 $

 		$Log: hsbc_functies.php,v $
 		Revision 1.17  2020/04/06 12:41:36  cvs
 		call 6991
 		
 		Revision 1.16  2020/01/22 11:42:38  cvs
 		call 8367
 		
 		Revision 1.15  2020/01/14 14:27:09  cvs
 		call 8335
 		
 		Revision 1.14  2020/01/14 14:20:51  cvs
 		call 8335
 		
 		Revision 1.13  2020/01/14 14:06:10  cvs
 		call 8300
 		
 		Revision 1.12  2019/10/04 13:59:22  cvs
 		call 8017
 		
 		Revision 1.11  2019/08/23 10:04:58  cvs
 		call 8017
 		
 		Revision 1.10  2019/08/19 14:17:44  cvs
 		call 8017
 		
 		Revision 1.9  2019/07/08 12:22:38  cvs
 		call 6991
 		





*/

function getRekening($rekeningNr="-1", $depot="HSBC")
{
  global $meldArray, $mr;
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "regel ".$mr["regelnr"].": ".$rekeningNr." --> niet gevonden voor $depot ";
      return $rekeningNr;
    }
    
  }
}

function hsbcGetFonds($bankcode, $ISIN, $fondsValuta)
{
  global $fonds;
  $fonds = array();
  $codeNotFound = true;
  $db = new DB();
  if ($bankcode != "")
  {
    $query = "SELECT * FROM Fondsen WHERE HSBCcode = '".trim($bankcode)."' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      $codeNotFound = false;
      return true;
    }

  }

  if($codeNotFound AND trim($ISIN) != "" )
  {
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$ISIN."' AND Valuta ='".$fondsValuta."' ";

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
	global $mr, $data;
	if ($data["rekValuta"] != $data["fondsVal"])
  {
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }
	else
  {
    return -1 * ($mr["Debet"]);
  }

}


function _creditbedrag()
{
	global $mr, $data;
  if ($data["rekValuta"] != $data["fondsVal"])
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    return $mr["Credit"];
  }
}

function _valutaKoersAirs()
{
  global $data, $mr;

  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data["fondsVal"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];

}

function _valutakoersCPDV()
{
  global $data, $mr;


  if ($data["rekValuta"] == "EUR" AND $data["fondsVal"] == $data["rekValuta"])
  {
    return 1;
  }
  else if ($data["rekValuta"] == "EUR" AND $data["fondsVal"] != $data["rekValuta"])
  {
    return $data["wisKrsFvRekv"];
  }
  else if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == "EUR")
  {
    return (($data["koers"] * $data["aantal"]) - $data["tax"])/$data["nettoBedrag"];
  }
  else if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == $data["rekValuta"])
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data["fondsVal"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  else
  {
    return 999;
  }
}


function _valutakoers()
{
  global $data, $mr;


  if ($data["rekValuta"] == "EUR" AND $data["fondsVal"] == $data["rekValuta"])
  {
    return 1;
  }
  else if ($data["rekValuta"] == "EUR" AND $data["fondsVal"] != $data["rekValuta"])
  {
    return $data["wisKrsFvRekv"];
  }
  else if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == $data["rekValuta"])
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data["fondsVal"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
  else
  {
    return 999;
  }
}

function _valutakoersMut()
{
  global $data, $mr;


  if ($data["rekValuta"] == "EUR" )
  {
    return 1;
  }
  else
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$data["rekValuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }

}

function getPositionByFonds($mr)
{
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `Rekening` = '".$mr["Rekening"]."'  ";
  $portRec = $db->lookupRecordByQuery($query);
  $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds,
        SUM(Rekeningmutaties.Aantal) AS aantal
      FROM 
        Rekeningmutaties
      JOIN Rekeningen ON  
        Rekeningmutaties.Rekening  = Rekeningen.Rekening AND Rekeningen.consolidatie = '0'
      JOIN Portefeuilles ON 
        Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie = '0'
      WHERE
        Rekeningmutaties.Grootboekrekening = 'FONDS' AND
        YEAR(Rekeningmutaties.Boekdatum) = '".date("Y")."' AND
        Rekeningmutaties.Verwerkt = '1' AND
        Rekeningmutaties.Boekdatum <= NOW() AND
        Rekeningen.Portefeuille = '{$portRec["Portefeuille"]}' AND
        Rekeningmutaties.Fonds = '{$mr["Fonds"]}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(aantal,4) <> 0
    ";
  $positie = $db->lookupRecordByQuery($query);
  return (int) $positie["aantal"];


}

function do_algemeen()
{
	global $mr, $row, $data, $_file, $fonds;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $data["row"];
	$mr["bankTransactieId"]  = $data["transId"].$row;
  $mr["Rekening"]          = getRekening(trim($data["rekening"]).trim($data["rekValuta"]));
	$mr["Boekdatum"]         = $data["boek"];
  $mr["settlementDatum"]   = $data["settle"];
  $fonds = $data["fonds"];
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

function hsbcGetFondskoers($fondscode, $datum="now")
{
  global $data;
  if ($data["koers"] !=0 )
  {
    return $data["koers"];
  }
  $sqlDatum = ($datum = "now")?" NOW() ":" '".$datum."' ";
  $query = "
    SELECT
      Fondskoersen.Fonds,
      Fondskoersen.Datum,
      Fondskoersen.Koers
    FROM
      `Fondskoersen`
    WHERE
      Fonds = '".$fondscode."'
    AND 
      Datum <= NOW()
    ORDER BY
      Datum DESC
";
  $db = new DB();
  if (!$rec = $db->lookupRecordByQuery($query))
  {
    return false;
  }
  else
  {
    return $rec["Koers"];
  }
}


function do_RENDV() //FondsAusschüttung --> op basis van Fondssoort bepalen of het RENOB of DIV
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  debug($fonds);
  debug($data);

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_D()  // deponering van stukken
{

  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();

  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "D";
  do_algemeen();

  $mr["Rekening"]          = getRekening(trim($data["portefeuille"])."MEM");
  $data["rekValuta"]       = "EUR";

  if ($data["boek"] == date("Y")."-01-01" )
  {
    $meldArray[] = "regel ".$data["row"].": ".$data["portefeuille"]." --> 1 jan boeking overgeslagen ";
    return;
  }

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutaKoersAirs();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = hsbcGetFondskoers($mr["Fonds"], $data["boek"]);
  $mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();

  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Grootboekrekening"] = "STORT";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = abs($mr["Bedrag"]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENME";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data["renob"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "STORT";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["renob"]);
  $mr["Bedrag"]            = _creditbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  checkControleBedrag(0,0);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L()  // lichting van stukken
{

  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();

  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "L";
  do_algemeen();

  $mr["Rekening"]          = getRekening(trim($data["portefeuille"])."MEM");
  $data["rekValuta"]       = "EUR";

  if ($data["boek"] == date("Y")."-01-01" )
  {
    $meldArray[] = "regel ".$data["row"].": ".$data["portefeuille"]." --> 1 jan boeking overgeslagen ";
    return;
  }

  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutaKoersAirs();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = hsbcGetFondskoers($mr["Fonds"], $data["boek"]);
  $mr["Credit"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Debet"]            = 0;
  $mr["Bedrag"]            = _creditbedrag();

  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]            = abs($mr["Bedrag"]);
  $mr["Credit"]             = 0;
  $mr["Bedrag"]            = _debetbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["renob"]);
  $mr["Bedrag"]            = _creditbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Valuta"]            = "EUR";
  $mr["Valutakoers"]       = 1;
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data["renob"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }
  checkControleBedrag(0,0);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_A()
{

  global $fonds, $data, $mr, $output;

  if ($fonds["fondssoort"] == "OPT")
  {
    do_AO();
    return;
  }
  $mr = array();
  $controleBedrag = 0;
  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "A";
  do_algemeen();
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-KOBU");
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten5"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  if ($data["renob"] <> 0)  // aankoop obligatie
  {

    if (trim($data["rekValuta"]) == "EUR")
    {
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
    }
    $mr["Grootboekrekening"] = "RENME";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["renob"]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    //debug($controleBedrag,$fonds["Omschrijving"]."-RENME");
    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }


  }


  $data["fondsVal"]        = $data["rekValuta"];

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $bedrag = ($data["kosten1"] + $data["kosten2"] + $data["kosten4"] );
  if ($bedrag > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Credit"];
  }



  $controleBedrag         += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-KOST");
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
function do_V()
{

  global $fonds, $data, $mr, $output;

  if ($fonds["fondssoort"] == "OPT")
  {
    do_VO();
    return;
  }
  $mr = array();
  $controleBedrag = 0;
  $r = $data["row"];
  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "V";

  do_algemeen();
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"] ;


  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
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
  $mr["Debet"]             = abs($data["kosten5"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }


  if ($data["renob"] <> 0)  // aankoop obligatie
  {

    if (trim($data["rekValuta"]) == "EUR")
    {
      $mr["Valuta"]            = "EUR";
      $mr["Valutakoers"]       = 1;
    }

    $mr["Grootboekrekening"] = "RENOB";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = abs($data["renob"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag         += $mr["Bedrag"];

    $mr["Transactietype"]    = "";
    if ($mr["Bedrag"] <> 0)
    {
      $output[] = $mr;
    }
  }

  $data["fondsVal"]        = $data["rekValuta"];

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $bedrag = ($data["kosten1"] + $data["kosten2"] + $data["kosten4"] );
  if ($bedrag > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $controleBedrag         += $mr["Bedrag"];

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
function do_AO()
{

  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "A";
  do_algemeen();
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
  $pos = getPositionByFonds($mr);
  if ($pos < 0)
  {
    $mr["Transactietype"] = "A/S";
  }
  else
  {
    $mr["Transactietype"] = "A/O";
  }
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-KOBU");
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten5"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $data["fondsVal"]        = $data["rekValuta"];

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $bedrag = ($data["kosten1"] + $data["kosten2"] + $data["kosten4"] );
  if ($bedrag > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Credit"];
  }



  $controleBedrag         += $mr["Bedrag"];
  //debug($controleBedrag,$fonds["Omschrijving"]."-KOST");
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
function do_VO()
{

  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $r = $data["row"];
  $fonds                   = $data["fonds"];
  $mr["aktie"]             = "V";

  do_algemeen();
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["koers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"] ;


  $pos = getPositionByFonds($mr);
  if ($pos > 0)
  {
    $mr["Transactietype"] = "V/S";
  }
  else
  {
    $mr["Transactietype"] = "V/O";
  }
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;


  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
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
  $mr["Debet"]             = abs($data["kosten5"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  $data["fondsVal"]        = $data["rekValuta"];

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $bedrag = ($data["kosten1"] + $data["kosten2"] + $data["kosten4"] );
  if ($bedrag > 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $controleBedrag         += $mr["Bedrag"];

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

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DIV";
  do_algemeen();


  $mr["Valuta"]            = $data["fondsVal"];
  $mr["Valutakoers"]       = _valutakoersCPDV();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;



  if (trim($mr["Fonds"]) != "")
  {
    $mr["Grootboekrekening"] = "DIV";
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == "EUR")
    {
      $bedrag        =  ($data["aantal"] * $data["koers"])/$mr["Valutakoers"];
      $mr["Valuta"]  = $data["rekValuta"];
      if ($bedrag > 0)
      {
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($bedrag);
        $mr["Bedrag"]            = $mr["Credit"];
      }
      else
      {
        $mr["Credit"]            = 0;
        $mr["Debet"]             = abs($bedrag);
        $mr["Bedrag"]            = $mr["Debet"] * -1;
      }
    }
    else
    {
      $bedrag  = $data["aantal"] * $data["koers"];
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
    }


    $controleBedrag       += $mr["Bedrag"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if (abs($mr["Credit"]) + abs($mr["Debet"]) + abs($mr["Bedrag"]) != 0)  // 8367 call null bedragen ontdrukken
  {
    $output[] = $mr;
  }


  $tax = hsbcNumber($data["tax"]);
  if ($tax != 0)
  {
    if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == "EUR")
    {
      $bedrag =  $tax/$mr["Valutakoers"];
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($bedrag);
      $mr["Bedrag"]            = $mr["Debet"] * -1;
    }
    else
    {
      $bedrag = $tax;
      $mr["Credit"]             = 0;
      $mr["Debet"]            = abs($bedrag);
      $mr["Bedrag"]            = _debetbedrag();
    }


    $controleBedrag       += $mr["Bedrag"];
    $mr["Grootboekrekening"] = "DIVBE";

    if ($mr["Bedrag"] != 0)
    {
      $output[] = $mr;
    }
  }

  $mr["Valuta"] = $data["rekValuta"];
  $kosten1= hsbcNumber($data["kosten1"]);

  if ($kosten1 != 0)
  {
    $mr["Grootboekrekening"] = "KNBA";

    if ($kosten1 != 0)
    {
      $bedrag = $kosten1;
      $mr["Credit"] = 0;
      $mr["Debet"] = abs($bedrag);
      $mr["Bedrag"] = _debetbedrag();

      $controleBedrag += $mr["Bedrag"];

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }
    }
  }

  $kosten2= hsbcNumber($data["kosten2"]);
  if ($kosten2 != 0)
  {
    $mr["Grootboekrekening"] = "KOBU";

    if ($kosten1 != 0)
    {
      $bedrag = $kosten2;

      $mr["Credit"] = 0;
      $mr["Debet"] = abs($bedrag);
      $mr["Bedrag"] = _debetbedrag();

      $controleBedrag += $mr["Bedrag"];

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }
    }
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }






  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DVCP()  //overige inkomsten
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "DVCP";
  do_algemeen();


  $mr["Valuta"]            = $data["fondsVal"];
  $mr["Valutakoers"]       = _valutakoersCPDV();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($fonds["fondssoort"] == "OBL")
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  }
  else
  {
    $mr["Grootboekrekening"] = "DIV";
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  }


  if (trim($mr["Fonds"]) != "")
  {
    if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == "EUR")
    {
      $bedrag        =  ($data["aantal"] * $data["koers"])/$mr["Valutakoers"];
      $mr["Valuta"]  = $data["rekValuta"];
      if ($bedrag > 0)
      {
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($bedrag);
        $mr["Bedrag"]            = $mr["Credit"];
      }
      else
      {
        $mr["Credit"]            = 0;
        $mr["Debet"]             = abs($bedrag);
        $mr["Bedrag"]            = $mr["Debet"] * -1;
      }
    }
    else
    {
      $bedrag  = $data["aantal"] * $data["koers"];
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
    }
    $controleBedrag       += $mr["Bedrag"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if (abs($mr["Credit"]) + abs($mr["Debet"]) + abs($mr["Bedrag"]) != 0)  // 8367 call null bedragen ontdrukken
  {
    $output[] = $mr;
  }

  $tax = hsbcNumber($data["tax"]);
  if ($tax != 0)
  {
    if ($data["rekValuta"] != "EUR" AND $data["fondsVal"] == "EUR")
    {
      $bedrag =  $tax/$mr["Valutakoers"];
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($bedrag);
      $mr["Bedrag"]            = $mr["Debet"] * -1;
    }
    else
    {
      $bedrag = $tax;
      $mr["Credit"]             = 0;
      $mr["Debet"]            = abs($bedrag);
      $mr["Bedrag"]            = _debetbedrag();
    }
    $mr["Grootboekrekening"] = "DIVBE";
    $controleBedrag       += $mr["Bedrag"];
    if ($mr["Bedrag"] != 0)
    {
      $output[] = $mr;
    }
  }

  $mr["Valuta"] = $data["rekValuta"];
  $kosten1= hsbcNumber($data["kosten1"]);

  if ($kosten1 != 0)
  {
    $mr["Grootboekrekening"] = "KNBA";

    if ($kosten1 != 0)
    {
      $bedrag = $kosten1;
      $mr["Credit"] = 0;
      $mr["Debet"] = abs($bedrag);
      $mr["Bedrag"] = _debetbedrag();

      $controleBedrag += $mr["Bedrag"];

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }
    }
  }

  $kosten2= hsbcNumber($data["kosten2"]);
  if ($kosten2 != 0)
  {
    $mr["Grootboekrekening"] = "KOBU";

    if ($kosten1 != 0)
    {
      $bedrag = $kosten2;

      $mr["Credit"] = 0;
      $mr["Debet"] = abs($bedrag);
      $mr["Bedrag"] = _debetbedrag();

      $controleBedrag += $mr["Bedrag"];

      if ($mr["Bedrag"] != 0)
      {
        $output[] = $mr;
      }
    }
  }

  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($data["kosten3"]);
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }



  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_RENOB()  //Rente / coupon
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENOB";
  do_algemeen();

  $mr["Valuta"]            = $data["fondsVal"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if (trim($mr["Fonds"]) != "")
  {
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $bedrag            = hsbcNumber($data["aantal"]) * hsbcNumber($data["koers"]);
    if ($bedrag > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($bedrag);
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($bedrag);
      $mr["Bedrag"]            = _debetbedrag();
    }
    $controleBedrag       += $mr["Bedrag"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if (abs($mr["Credit"]) + abs($mr["Debet"]) + abs($mr["Bedrag"]) != 0)  // 8367 call null bedragen ontdrukken
  {
    $output[] = $mr;
  }

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  //geldmutaties
{
  global $fonds, $data, $mr, $output, $afw;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "MUT";
  do_algemeen();

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       =  _valutakoersMut();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;




    $mr["Omschrijving"]  = $data["omschrijving"];
    $bedrag              = $data["nettoBedrag"];
    if ($bedrag > 0)
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($bedrag);
      $mr["Bedrag"]            = $mr["Credit"];
    }
    else
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($bedrag);
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }

    if (stristr($data["omschrijving"],"Variation-Margin"))
    {
      $mr["Grootboekrekening"] = "VMAR";
    }
    $controleBedrag       += $mr["Bedrag"];


    if ($data[2] == "SonstigerErtrag" )
    {
      if (stristr($data[11],"Konto-Belastung (052)"))
      {
        $mr["Grootboekrekening"] = "RENTE";
      }
      if (stristr($data[11],"Konto-Gutschrift (070)"))
      {
        $mr["Grootboekrekening"] = "KNBA";
      }

    }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("MUT",$mr);
  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KNBA()  //geldmutaties
{
  global $fonds, $data, $mr, $output, $afw;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "KNBA";
  do_algemeen();

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       =  _valutakoersMut();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;




  $mr["Omschrijving"]  = $data["omschrijving"];
  $bedrag              = $data["nettoBedrag"];
  if ($bedrag > 0)
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $mr = $afw->reWrite("KNBA",$mr);

  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEH()  //beheerfees
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "BEH";
  do_algemeen();

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       =  _valutakoersMut();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;




  $mr["Omschrijving"]  = $data["omschrijving"];
  $bedrag              = $data["nettoBedrag"];
  if ($bedrag > 0)
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEH";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BEW()  //bewaarloon
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "BEW";
  do_algemeen();

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       =  _valutakoersMut();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;




  $mr["Omschrijving"]  = $data["omschrijving"];
  $bedrag              = $data["nettoBedrag"];
  if ($bedrag > 0)
  {
    $mr["Grootboekrekening"] = "BEW";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Grootboekrekening"] = "BEW";
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()
{

  global $fonds, $data, $mr, $output;

  if ($data["a"][7] == "EUR")
  {
    $set1 = $data["a"];
    $set2 = $data["b"];
    $valutaKoers = abs( hsbcNumber($set1[3]) / hsbcNumber($set2[3]));
  }
  elseif ($data["b"][7] == "EUR")
  {
    $set1 = $data["b"];
    $set2 = $data["a"];
    $valutaKoers = abs(hsbcNumber($set1[3]) / hsbcNumber($set2[3]));
  }
  else
  {
    $set1 = $data["a"];
    $set2 = $data["b"];
    $valutaKoers = 9999;
  }

  // SET 1 verwerken
  $data = $set1;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "MUT";
  do_algemeen();

  $mr["Valuta"]            = $set2["rekValuta"];
  $mr["Valutakoers"]       = $valutaKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  $mr["Omschrijving"]  = $data["omschrijving"];
  $bedrag              = hsbcNumber($set2["nettoBedrag"]) * -1;
  if ($bedrag > 0)
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag);
    $mr["Bedrag"]            = $mr["Credit"] * $valutaKoers;
  }
  else
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = -1 * $mr["Debet"] * $valutaKoers;
  }
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));

  // SET 2 verwerken
  $data = $set2;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "MUT";
  do_algemeen();

  $mr["Valuta"]            = $data["rekValuta"];
  $mr["Valutakoers"]       = $valutaKoers;
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";
  $mr["Omschrijving"]      = $data["omschrijving"];
  $bedrag                  = hsbcNumber($data["nettoBedrag"]);
  if ($bedrag > 0)
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($bedrag);
    $mr["Bedrag"]            = $mr["Credit"];
  }
  else
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  checkControleBedrag($controleBedrag,hsbcNumber($data["nettoBedrag"]));


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////




function do_NVT()
{
  global $meldArray, $data;
  $meldArray[] = "regel ".$data["row"].": ".$data["transactieCode"]." --> overgeslagen (NVT) ";
  return true;
}

function hsbcDate($date)
{
  $d = explode(".",$date);
  return $d[2]."-".$d[1]."-".$d[0];
}

function hsbcNumber($in)
{
  $in = str_replace("-,", "-0,", $in);
  return (float) str_replace(",",".",$in);
}

function do_error($text)
{
	echo "<BR>FOUT $text bestaat niet!";
}


