<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/04/03 11:31:56 $
 		File Versie					: $Revision: 1.26 $

 		$Log: abnv2_functies.php,v $
 		Revision 1.26  2020/04/03 11:31:56  cvs
 		call 8529
 		
 		Revision 1.25  2019/12/09 09:03:44  cvs
 		call 8286
 		
 		Revision 1.24  2019/12/02 14:03:37  cvs
 		call 8228
 		
 		Revision 1.23  2019/11/29 15:35:14  cvs
 		do_L en do_D mem rekening naar EUR
 		
 		Revision 1.22  2019/10/04 13:48:12  cvs
 		cal 8151
 		
 		Revision 1.21  2019/10/04 12:19:50  cvs
 		call 8151
 		
 		Revision 1.20  2019/08/28 10:07:24  cvs
 		dubbele regelnummers in meldarray
 		
 		Revision 1.19  2019/08/19 11:21:48  cvs
 		call 7992
 		
 		Revision 1.18  2019/07/10 13:18:22  cvs
 		call 7946
 		
 		Revision 1.17  2019/07/10 12:44:45  cvs
 		call 7946
 		
 		Revision 1.16  2019/07/08 12:02:30  cvs
 		call 7749
 		
 		Revision 1.15  2019/06/17 10:02:54  cvs
 		call 7885
 		
 		Revision 1.14  2019/06/17 08:57:16  cvs
 		call 7885
 		
 		Revision 1.13  2019/06/14 11:07:26  cvs
 		call 7881
 		
 		Revision 1.12  2019/06/05 12:16:38  cvs
 		call 7842
 		
 		Revision 1.11  2019/05/14 13:29:41  cvs
 		call 7047
 		
 		Revision 1.10  2019/04/29 11:52:08  cvs
 		call 7736
 		
 		Revision 1.9  2019/04/29 10:33:24  cvs
 		call 7746
 		
 		Revision 1.8  2019/04/17 13:38:53  cvs
 		call 7047
 		
 		Revision 1.7  2019/04/17 13:26:24  cvs
 		call 7047
 		
 		Revision 1.6  2019/04/15 14:27:30  cvs
 		call 7047
 		
 		Revision 1.5  2019/04/12 11:52:59  cvs
 		call 7047
 		
 		Revision 1.4  2019/04/03 15:11:22  cvs
 		call 7047
 		
 		Revision 1.3  2019/03/22 12:32:54  cvs
 		call 7047
 		
 		Revision 1.2  2019/01/16 13:18:38  cvs
 		call 7352
 		
 		Revision 1.1  2018/11/23 13:34:06  cvs
 		call 7047
 		

*/

function getRekening($rekeningNr="-1", $depot="AAB")
{
  global $meldArray, $mr, $data;
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `RekeningDepotbank` LIKE '%{$rekeningNr}' AND `Depotbank` IN ('AAB','AABIAM','TRI' ) ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `Rekening` LIKE '%{$rekeningNr}' AND `Depotbank` IN ('AAB','AABIAM','TRI' ) ";

    if ($rec = $db->lookupRecordByQuery($query))
    {
      return $rekeningNr;
    }
    else
    {
      $meldArray[] = "regel ".$mr["regelnr"].": Rekening: ".$rekeningNr." --> niet gevonden voor $depot ";
      return false;
    }
  }

}

function valutaCorrectie($valutaCode, $wisselkoers)
{

  if ($wisselkoers == 0)  // als wisselkoers = 0 daan AIRS koers ophalen
  {
    return _valutakoersAIRS();
  }
  else
  {
    $DB = New DB;
    $query = "SELECT * FROM Valutas WHERE AABgrens > 0 AND Valuta = '".$valutaCode."'";

    if ($aab = $DB->lookupRecordByQuery($query))
    {
      if ($wisselkoers > $aab["AABgrens"] )
      {
        return  $wisselkoers/$aab["AABcorrectie"];
      }
    }
    return $wisselkoers;
  }

}


function abnV2_getFonds($bankcode="", $ISIN="", $valuta="")
{
  global $fonds;
  $fonds = array();
  $db = new DB();

  $query = "SELECT * FROM Fondsen WHERE AABCode = '{$bankcode}' OR ABRCode = '{$bankcode}'";
  
  if (!$fonds = $db->lookupRecordByQuery($query) OR $bankcode == "")
  {
//    $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$ISIN}' AND Valuta = '{$valuta}'";
//    if (!$fonds = $db->lookupRecordByQuery($query) OR $ISIN == "" OR $valuta == "")
//    {
      return "Fonds niet gevonden bankcode={$bankcode}, ISIN={$ISIN}, valuta={$valuta}";
//    }
  }
  return "fondsLoaded";

}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "AAB|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

  $rekValuta = $data["valuta"];

  if ($rekValuta <> "EUR" AND $mr["Valuta"] == $rekValuta)
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
  $rekValuta = $data["valuta"];
  if ($rekValuta <> "EUR" AND $mr["Valuta"] == $rekValuta)
  {
    return $mr["Credit"];
  }
	else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

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

function addAndCheck($soort="554")
{
  global $mr, $output;

  $output[] = $mr;
  return true;
}


function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup;
	$db = new DB();
	$rekValuta = $data[9];
	$valutaLookup = false;
	if ($rekValuta <> "EUR" AND $mr["Valuta"] == $rekValuta)
	{
    $mr["Valuta"] = $rekValuta;
	   if ($data[23] > 0)
     {
       $valutaLookup = true;
       return $data[23];
     }
     else
     {
		   $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$rekValuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
       $laatsteKoers = $db->lookupRecordByQuery($query);
       $valutaLookup = true;
       return $laatsteKoers["Koers"];
     }
	}
	else
  {
    return $data[10];
  }
}

function _valutakoersDIV()  // tbv dbs2742
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[9];
	$valutaLookup = false;
  if ($data[10] == 1)
  {
    $mr["Valuta"]  = $valuta;
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


function modifyDescription($omschr)
{
  global $__appvar, $data;

  $capitalize = false;

  if (strstr($omschr, "BEA   NR" ))
  {
    $txt = explode(",PAS", $omschr);
    $txt = explode("/", $txt[0]);
    $txt = substr($txt[1],5);
    $omschr = "BEA: ".$txt;
  }

  if (stristr($omschr, "/EREF/"))
  {
    $exp = explode("/EREF/",$omschr);
    $omschr = $exp[0];
  }

  if ($capitalize )
  {
    $omschr = strtoupper(substr($omschr,0,1)).strtolower(substr($omschr,1));
  }

  // navraag rubr (buitenlandse overboeking) call 6313
  if (stristr($omschr,"navraagrubr") )
  {
    if (substr($omschr,0,6) != "KOSTEN")
    {
      $s = explode (",", $omschr);
      $s2 = explode(" NAVRAAGRUBR",trim(substr($s[1],2)));
      $omschr = trim(substr($s2[0],strpos($s2[0]," ")));
    }
    else
    {
      $s = explode("NAVRAAGRUBR",$omschr);
      $omschr = $s[0];
    }
  }

  $omschOut = $omschr;

  if (stristr($omschr,"/TRTP/SEPA")  OR
    stristr($omschr,"/TRTP/ACCEPTGIROBETALING") OR
    stristr($omschr,"/TRTP/IDEAL") )
  {
    $parts = explode("/NAME/",$omschr);
    $omschOut = $parts[1];
  }
  if ($__appvar["bedrijf"] != "RCN")
  {
    $omschOut = ucwords(strtolower($omschOut));
  }

  if (substr(strtoupper($omschOut),0,27) == "KOOP/VERKOOP VREEMDE VALUTA" )
  {
    $oms = explode("\r", $omschOut);
    $kRaw = $oms[4];
    while (strstr($kRaw, "  "))
    {
      $kRaw = str_replace("  "," ", $kRaw);
    }
    $koers = explode(" ",$kRaw);
    $data["omsKoers"] = round(1/str_replace(",",".",$koers[2]),8);
  }

  while (strstr($omschOut, "  "))
  {
    $omschOut = str_replace("  "," ", $omschOut);
  }
  return trim($omschOut);
}

function _valutakoersAIRS()
{
  global $data, $mr;
  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];
}

function do_algemeen($rekNr="")
{
	global $mr, $row, $volgnr, $data, $_file;

  $mr = array();
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data["bankTransactieId"];
  $mr["Boekdatum"]         = $data["Boekdatum"];
  $mr["settlementDatum"]   = $data["settlementDatum"];
  $mr["Omschrijving"]      = $data["omschrijving"];
  if ($rekNr != "")
  {
    $mr["Rekening"]       = getRekening($rekNr );
  }
  else
  {
    $mr["Rekening"]       = getRekening($data["rekening"].$data["valuta"] );
  }

}

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $verschil       = $notabedrag - $controleBedrag;
  $r  = abs($mr["regelnr"]);
  if ($verschil == 0)
  {
    $meldArray["gelijk"][] = "regel ".$r.": ".$mr["Rekening"]." --> notabedrag sluit aan ";
  }
  else if (abs($verschil) < 0.05 )
  {
    $meldArray["verschil005"][] = "regel ".$r.": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
  else
  {
    $meldArray["verschil"][] = "regel ".$r.": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($verschil,2);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_DRIP()
{
  global $output, $data, $mr, $meldArray, $_file,$row;

  if ($data[37] == 0)
  {
    $data["Boekdatum"] = $data["settlementDatum"];
    do_CD();
  }
  else
  {
//    $mr = array();
//    $mr["Valuta"] = $data[2];
//    $mr["Boekdatum"]         = $data["Boekdatum"];
//    $data["wisselkoers"] = _valutakoersAIRS();
    $mr = array();
    global $data,$fonds,$mr,$output,$afw;
    $mr["aktie"]              = "A";
    do_algemeen();
    $mr["Boekdatum"]         = $data[33];
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "FONDS";
    $mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valutakoers"]       = $data["wisselkoers"];
    $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);

    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data["aantal"];
    $mr["Fondskoers"]        = $data[37];
    $mr["Debet"]             = abs($data["aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "A";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;

    if (addAndCheck() )
    {
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

      //$mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = 0;
      $mr["Transactietype"]    = "";
      $mr["Grootboekrekening"] = "KOST";
      $mr = $afw->reWrite("KOST",$mr);
      if ($data[36] )
      {

        $mr["Debet"]             = abs($data[36]);
        //$mr["Valuta"]            = $data["valuta_16"];
        //if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
    }
//  $valutaCorrectie = ($data["valuta"] <> "EUR")?$mr["Valutakoers"]:1;
    checkControleBedrag($controleBedrag , $data["nettoBedrag"]);
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_MUT()
{
  global $output, $data, $mr, $zv, $afw, $_file,$row;

  $controleBedrag = 0;
  do_algemeen();
  $mr["regelnr"]        = $mr["regelnr"] * -1;  // regelnr negetief om colorcoding te sturen in html/tijdelijkerekeningmutatiesList.php
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

//  $omschrijving = str_replace("\r"," ",$d["omschrijving"]);
  $mr["OmschrijvingOrg"]  = $data["omschrijving"];
  $mr["bankOmschrijving"] = $data["omschrijving"];
  $mr["Omschrijving"]     = modifyDescription($data["omschrijving"]);
  $mr["Valuta"]           = $data["valuta"];

  $mr["Omschrijving"]     = ucfirst(trim($zv->reWrite($mr["Omschrijving"], $mr["Rekening"])));

  $mr["aktie"]            = "GELDMUT";

  $mr["Valuta"]           = $data["valuta"] ;

  $mr["Valutakoers"]      = _valutakoersAIRS();
  $mr["Fonds"]            = "";
  $mr["Aantal"]           = 0;
  $mr["Fondskoers"]       = 0;
  $mr["Transactietype"]   = "";




  if ( strtoupper(substr($data["omschrijving"],0,5)) == "RENTE" OR
       strtoupper(substr($data["omschrijving"],0,16)) == "AFSLUITING RENTE"
     )
  {
    $mr["Grootboekrekening"] = "RENTE";
    if ($data["DC"] == "C")
    {
      //$mr["Omschrijving"] = "Creditrente";
      $mr["Debet"]        = 0;
      $mr["Credit"]       = abs($data["nettoBedrag"]);
      $mr["Bedrag"]       = $mr["Credit"];
    }
    else
    {
      // $mr["Omschrijving"] = "Debetrente";
      $mr["Debet"]        = abs($data["nettoBedrag"]);
      $mr["Credit"]       = 0;
      $mr["Bedrag"]       = -1 * $mr["Debet"];
    }
    $mr = $afw->reWrite("RENTE",$mr);
  }
  elseif (  strtoupper(substr($data["omschrijving"],0,10)) == "BEWAARLOON" OR
            strtoupper(substr($data["omschrijving"],0,25)) == "ABNAMRO BELEGGEN  SERVICE"
         )
  {

    $mr["Grootboekrekening"] 	= "BEW";
    $mr["Debet"]        			= abs($data["nettoBedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= _debetbedrag($mr["Debet"]);
    $mr = $afw->reWrite("GLDBEW",$mr);
  }
  elseif (substr($mr["Omschrijving"],0,35) == "Koop/verkoop Vreemde Valutacontract"  )
  {
    $mr["Grootboekrekening"] = "KRUIS";
    if ($mr["Valuta"] != "EUR")
    {
      $part = explode("KOERS", $mr["OmschrijvingOrg"]);
      if (count($part) > 0)
      {
        $mr["Valutakoers"] = 1/str_replace(",",".",trim($part[1]));
      }
    }

    if ($data["DC"] == "C")
    {

      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($data["nettoBedrag"]);
      $mr["Bedrag"]       			= $mr["Credit"];
      $mr = $afw->reWrite("GLDSTORT",$mr);
    }
    else
    {

      $mr["Debet"]			        = abs($data["nettoBedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("GLDONTTR",$mr);
    }
  }
  else
  {
    if ($data["DC"] == "C")
    {
      $mr["Grootboekrekening"] 	= "STORT";
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($data["nettoBedrag"]);
      $mr["Bedrag"]       			= $mr["Credit"];
      $mr = $afw->reWrite("GLDSTORT",$mr);
    }
    else
    {
      $mr["Grootboekrekening"] 	= "ONTTR";
      $mr["Debet"]			        = abs($data["nettoBedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("GLDONTTR",$mr);
    }
  }

  if ($mr["Bedrag"] != 0)
  {
    $controleBedrag += $mr["Bedrag"];
    $output[] = $mr;
  }
 checkControleBedrag(ABS($controleBedrag),$data["nettoBedrag"]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A($record)  // aankoop van stukken
{
  global $data,$fonds,$mr,$output,$afw;


  if ($data[8] == "C" OR $data[8] == "P")
  {
    do_AO($record);
    return;
  }
  $mr["aktie"]              = "A";
  do_algemeen();

  if ((int)$data["rekening"] == 0)
  {
    $mr["Rekening"]       = getRekening($data["portefeuille"].$data["valuta"] );
  }
  else
  {
    $mr["Rekening"]       = getRekening($data["rekening"].$data["valuta"] );
  }

  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if (addAndCheck() )
  {
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

    //$mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = 0;
    $mr["Transactietype"]    = "";
    $mr["Grootboekrekening"] = "KOST";
    $mr = $afw->reWrite("KOST",$mr);
    if ($data["kosten_16"] )
    {

      $mr["Debet"]             = abs($data["kosten_16"]);
      $mr["Valuta"]            = $data["valuta_16"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    if ($data["kosten_17"] )
    {
      $mr["Debet"]             = abs($data["kosten_17"]);
      $mr["Valuta"]            = $data["valuta_17"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_17",$mr);
      addAndCheck();
    }
    if ($data["kosten_18"] )
    {
      $mr["Debet"]             = abs($data["kosten_18"]);
      $mr["Valuta"]            = $data["valuta_18"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_18",$mr);
      addAndCheck();
    }

    if ($data["kosten_19"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_19"]);
      $mr["Valuta"]            = $data["valuta_19"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_19",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_20"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_20"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_20",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_21"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_21"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_21",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_22"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_22"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_22",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_27"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_27"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_27",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////

    if ($data["kosten_15"])
    {
      $mr["Grootboekrekening"] = "RENME";
      $mr = $afw->reWrite("RENME",$mr);
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($data["kosten_15"]);
      $mr["Valuta"]            = $data["valuta_15"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Credit"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }




  }
//  $valutaCorrectie = ($data["valuta"] <> "EUR")?$mr["Valutakoers"]:1;
  checkControleBedrag($controleBedrag , $data["nettoBedrag"]);
}

function do_V($record)  // verkoop van stukken
{
  global $data,$fonds,$mr,$output,$afw;

  if ($data[8] == "C" OR $data[8] == "P")
  {
    do_VO($record);
    return;
  }

  $mr["aktie"]              = "V";
  do_algemeen();
  if ((int)$data["rekening"] == 0)
  {
    $mr["Rekening"]       = getRekening($data["portefeuille"].$data["valuta"] );
  }
  else
  {
    $mr["Rekening"]       = getRekening($data["rekening"].$data["valuta"] );
  }

  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if (addAndCheck())
  {
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]  = 1;
    }


    //$mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = 0;
    $mr["Transactietype"]    = "";
    $mr["Grootboekrekening"] = "KOST";
    $mr = $afw->reWrite("KOST",$mr);
    if ($data["kosten_16"] )
    {

      $mr["Debet"]             = abs($data["kosten_16"]);
      $mr["Valuta"]            = $data["valuta_16"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    if ($data["kosten_17"] )
    {
      $mr["Debet"]             = abs($data["kosten_17"]);
      $mr["Valuta"]            = $data["valuta_17"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_17",$mr);
      addAndCheck();
    }
    if ($data["kosten_18"] )
    {
      $mr["Debet"]             = abs($data["kosten_18"]);
      $mr["Valuta"]            = $data["valuta_18"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_18",$mr);
      addAndCheck();
    }

    if ($data["kosten_19"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_19"]);
      $mr["Valuta"]            = $data["valuta_19"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_19",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_20"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_20"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_20",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_21"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_21"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_21",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_22"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_22"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_22",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_27"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_27"]);
      $mr["Valuta"]            = $data["valuta"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_27",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////

    if ($data["kosten_15"])
    {
      $mr["Grootboekrekening"] = "RENOB";
      $mr = $afw->reWrite("RENOB",$mr);
      $mr["Credit"]            = abs($data["kosten_15"]);
      $mr["Debet"]             = 0;
      $mr["Valuta"]            = $data["valuta_15"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _creditbedrag($mr["Credit"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }




  }
//  $valutaCorrectie = ($data["valuta"] <> "EUR")?$mr["Valutakoers"]:1;
  checkControleBedrag(abs($controleBedrag) , $data["nettoBedrag"]);
}

function do_AO($record)  // aankoop van opties
{
  global $data,$fonds,$mr,$output, $afw;
  $mr["aktie"]              = "AO";
  do_algemeen();
  $controleBedrag          = 0;
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];

  if ($data["isOptie"])
  {
    $pos = getPositionByFonds($mr);
    if ($pos < 0)
    {
      $mr["Transactietype"] = "A/S";
    }
    else
    {
      $mr["Transactietype"] = "A/O";
    }
  }

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if (addAndCheck())
  {
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

    $mr["Credit"]            = 0;
    //$mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Bedrag"]            = $mr["Debet"];
    $mr["Transactietype"]    = "";
    $mr["Grootboekrekening"] = "KOST";

    if ($data["kosten_16"] )
    {
      $mr["Debet"]             = abs($data["kosten_16"]);
      $mr["Valuta"]            = $data["valuta_16"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();

    }

    if ($data["kosten_17"] )
    {
      $mr["Debet"]             = abs($data["kosten_17"]);
      $mr["Valuta"]            = $data["valuta_17"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_17",$mr);
      addAndCheck();
    }
    if ($data["kosten_18"] )
    {
      $mr["Debet"]             = abs($data["kosten_18"]);
      $mr["Valuta"]            = $data["valuta_18"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_18",$mr);
      addAndCheck();
    }
    if ($data["kosten_19"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_19"]);
      $mr["Valuta"]            = $data["valuta_19"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr = $afw->reWrite("KOSTEN_19",$mr);
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_20"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_20"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_20",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_21"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_21"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_21",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_22"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_22"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_22",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
  }
//  $valutaCorrectie = ($data[valuta] <> "EUR")?$mr["Valutakoers"]:1;
//  checkControleBedrag($controleBedrag * $valutaCorrectie);
  checkControleBedrag($controleBedrag , $data["nettoBedrag"]);
}

function do_VO($record)  // verkoop van opties
{
  global $data,$fonds,$mr,$output, $afw;
  $mr["aktie"]              = "VO";
  do_algemeen();
  $controleBedrag = 0;
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];

  if ($data["isOptie"])
  {
    $pos = getPositionByFonds($mr);
    if ($pos > 0)
    {
      $mr["Transactietype"] = "V/S";
    }
    else
    {
      $mr["Transactietype"] = "V/O";
    }
  }

  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if(addAndCheck())
  {
    $mr["Debet"]      = 0;
    $mr["Credit"]			= 0;
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

    //$mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Bedrag"]            = $mr["Debet"];
    $mr["Transactietype"]    = "";
    $mr["Grootboekrekening"] = "KOST";

    if ($data["kosten_16"] )
    {
      $mr["Debet"]             = abs($data["kosten_16"]);
      $mr["Valuta"]            = $data["valuta_16"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    if ($data["kosten_17"] )
    {
      $mr["Debet"]             = abs($data["kosten_17"]);
      $mr["Valuta"]            = $data["valuta_17"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_17",$mr);
      addAndCheck();
    }
    if ($data["kosten_18"] )
    {
      $mr["Debet"]             = abs($data["kosten_18"]);
      $mr["Valuta"]            = $data["valuta_18"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_18",$mr);
      addAndCheck();
    }
    if ($data["kosten_19"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr = $afw->reWrite("KOSTEN_19",$mr);
      $mr["Debet"]             = abs($data["kosten_19"]);
      $mr["Valuta"]            = $data["valuta_19"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_20"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_20"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_20",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_21"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_21"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_21",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    ///////////////////////////////////////////////////////
    if ($data["kosten_22"] )
    {
      $mr["Grootboekrekening"] = "KOBU";
      $mr["Debet"]             = abs($data["kosten_22"]);
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $mr = $afw->reWrite("KOSTEN_22",$mr);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
    if ($data["kosten_15"])
    {
      $mr["Grootboekrekening"] = "RENOB";
      $mr["Debet"]             = abs($data["kosten_15"]);
      $mr["Valuta"]            = $data["valuta_15"];
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
      $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
      $controleBedrag       += $mr["Bedrag"];
      addAndCheck();
    }
  }
//  $valutaCorrectie = ($data[valuta] <> "EUR")?$mr["Valutakoers"]:1;
//  checkControleBedrag($controleBedrag * $valutaCorrectie);
  checkControleBedrag(abs($controleBedrag) , $data["nettoBedrag"]);
}

function do_KOBU($record)  //
{
  global $data,$fonds,$mr,$output, $afw;
    debug($data);
  do_algemeen();
  $mr["aktie"]              = "KOBU";
  $mr["Omschrijving"]      = "Tax ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  if ($mr["Valutakoers"] <> 1 AND $mr["Valuta"] == "EUR")
  {
    $mr["Valuta"] = $data["valuta_16"];
  }


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = 0;
  $mr["Grootboekrekening"] = "KOBU";

  if ($data[20] < 0)
  {
    $mr["Debet"]             = abs($data[20]);
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Credit"]             = abs($data[20]);
    $mr["Bedrag"]            = _creditbedrag();
  }
  $controleBedrag = $mr["Bedrag"];
  addAndCheck();
  checkControleBedrag($controleBedrag , $data["nettoBedrag"]);

}

function do_CD($record)  // contant dividend
{
  global $data,$fonds,$mr,$output, $afw;
//    debug($data);
  do_algemeen();
  $mr["aktie"]              = "CD";
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  if ($mr["Valutakoers"] <> 1 AND $mr["Valuta"] == "EUR")
  {
    $mr["Valuta"] = $data["valuta_16"];

  }


  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($data["DC"] == "C")
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("DIV",$mr);
    if (addAndCheck())
    {
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

      $mr["Credit"]            = 0;
      //$mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;

      $mr["Transactietype"]    = "";

      if ($data["kosten_17"])
      {
        $mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
        $mr["Debet"]             = abs($data["kosten_17"]);
        $mr["Valuta"]            = $data["valuta_17"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();

//      if ($fonds["Valuta"] <> "EUR" AND $data["valuta_17"] == "EUR")
//      {
//        $mr["Debet"]           = abs($data["kosten_17"]) / $data["wisselkoers"];
//        $mr["Valutakoers"]       = valutaCorrectie($fonds["Valuta"], $data["wisselkoers"]);
//        $mr["Valuta"]            = $fonds["Valuta"];
//        $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["wisselkoers"];
//
//      }

        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }


      if ($data["kosten_16"] )
      {
        $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
        $mr["Debet"]             = abs($data["kosten_16"]);
        $mr["Valuta"]            = $data["valuta_16"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      if ($data["kosten_18"] )
      {

        $mr["Debet"]             = abs($data["kosten_18"]);
        $mr["Valuta"]            = $data["valuta_18"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
        addAndCheck();
      }
      if ($data["kosten_19"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
        $mr["Debet"]             = abs($data["kosten_19"]);
        $mr["Valuta"]            = $data["valuta_19"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_20"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_20"]);
        $mr["Bedrag"]            = _debetbedrag();
        $mr = $afw->reWrite("KOSTEN_20",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_21"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_21"]);
        $mr["Bedrag"]            = _debetbedrag();
        $mr = $afw->reWrite("KOSTEN_21",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_22"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_22"]);
        $mr["Bedrag"]            = _debetbedrag();
        $mr = $afw->reWrite("KOSTEN_22",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }

    }
  }
  else
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data["aantal"] * $data["fondskoers"]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"] * -1;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("DIV",$mr);
    if (addAndCheck())
    {
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

      $mr["Debet"]            = 0;
      //$mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;

      $mr["Transactietype"]    = "";

      if ($data["kosten_17"])
      {
        $mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
        $mr["Credit"]             = abs($data["kosten_17"]);
        $mr["Valuta"]            = $data["valuta_17"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();

//      if ($fonds["Valuta"] <> "EUR" AND $data["valuta_17"] == "EUR")
//      {
//        $mr["Debet"]           = abs($data["kosten_17"]) / $data["wisselkoers"];
//        $mr["Valutakoers"]       = valutaCorrectie($fonds["Valuta"], $data["wisselkoers"]);
//        $mr["Valuta"]            = $fonds["Valuta"];
//        $mr["Bedrag"]            = -1 * $mr["Debet"] * $data["wisselkoers"];
//
//      }

        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }


      if ($data["kosten_16"] )
      {
        $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
        $mr["Credit"]             = abs($data["kosten_16"]);
        $mr["Valuta"]            = $data["valuta_16"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      if ($data["kosten_18"] )
      {

        $mr["Credit"]             = abs($data["kosten_18"]);
        $mr["Valuta"]            = $data["valuta_18"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
        addAndCheck();
      }
      if ($data["kosten_19"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
        $mr["Credit"]             = abs($data["kosten_19"]);
        $mr["Valuta"]            = $data["valuta_19"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_20"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_20"]);
        $mr["Bedrag"]            = _creditbedrag();
        $mr = $afw->reWrite("KOSTEN_20",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_21"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_21"]);
        $mr["Bedrag"]            = _creditbedrag();
        $mr = $afw->reWrite("KOSTEN_21",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_22"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_22"]);
        $mr["Bedrag"]            = _creditbedrag();
        $mr = $afw->reWrite("KOSTEN_22",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }

    }
  }



  checkControleBedrag(abs($controleBedrag) , $data["nettoBedrag"]);
//  $valutaCorrectie = ($data[valuta] <> "EUR")?$mr["Valutakoers"]:1;
//  checkControleBedrag($controleBedrag * $valutaCorrectie);
}

function do_CR($record) // Coupon Rente
{
  global $data,$fonds,$mr,$output,$afw;
  do_algemeen();
  $mr["aktie"]              = "CR";
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($data["DC"] == "C")
  {
    $mr["Debet"]             = 0;

    if ($mr["Valuta"] == $data["valuta"] )
    {
      $factor = 100;
    }
    else
    {
      $factor = $data["fondskoers"] / ($data["nettoBedrag"] / $data["aantal"] / $mr["Valutakoers"]);
    }


    $mr["Credit"]            = abs(($data["aantal"] * $data["fondskoers"])/$factor);

    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("RENOB",$mr);

    if (addAndCheck())
    {
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

      //$mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Credit"] 					 = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";

      if ($data["kosten_17"])
      {
        $mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
        $mr["Debet"]             = abs($data["kosten_17"]);
        $mr["Valuta"]            = $data["valuta_17"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      if ($data["kosten_16"] )
      {
        $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
        $mr["Debet"]             = abs($data["kosten_16"]);
        $mr["Valuta"]            = $data["valuta_16"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];

        addAndCheck();
      }
      if ($data["kosten_18"] )
      {

        $mr["Debet"]             = abs($data["kosten_18"]);
        $mr["Valuta"]            = $data["valuta_18"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
        addAndCheck();
      }
      if ($data["kosten_19"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
        $mr["Debet"]             = abs($data["kosten_19"]);
        $mr["Valuta"]            = $data["valuta_19"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _debetbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_20"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_20"]);
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_20",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_21"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_21"]);
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_21",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_22"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Debet"]             = abs($data["kosten_22"]);
        $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_22",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }

    }
  }
  else
  {
    $mr["Credit"]             = 0;
    if ($mr["Valuta"] == $data["valuta"] )
    {
      $factor = 100;
    }
    else
    {
      $factor = $data["fondskoers"] / ($data["nettoBedrag"] / $data["aantal"] / $mr["Valutakoers"]);
    }

    $mr["Debet"]            = abs(($data["aantal"] * $data["fondskoers"])/$factor);

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag         += $mr["Bedrag"] * -1;
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $mr = $afw->reWrite("RENOB",$mr);

    if (addAndCheck())
    {
      if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;

      //$mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Debet"] 			  		 = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Transactietype"]    = "";

      if ($data["kosten_17"])
      {
        $mr["Grootboekrekening"] = "DIVBE";
        $mr = $afw->reWrite("DIVBE",$mr);
        $mr["Credit"]             = abs($data["kosten_17"]);
        $mr["Valuta"]            = $data["valuta_17"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag($mr["Debet"]);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      if ($data["kosten_16"] )
      {
        $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("KOSTEN_16",$mr);
        $mr["Credit"]             = abs($data["kosten_16"]);
        $mr["Valuta"]            = $data["valuta_16"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];

        addAndCheck();
      }
      if ($data["kosten_18"] )
      {

        $mr["Credit"]             = abs($data["kosten_18"]);
        $mr["Valuta"]            = $data["valuta_18"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_18",$mr);
        addAndCheck();
      }
      if ($data["kosten_19"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr = $afw->reWrite("KOSTEN_19",$mr);
        $mr["Credit"]             = abs($data["kosten_19"]);
        $mr["Valuta"]            = $data["valuta_19"];
        if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
        $mr["Bedrag"]            = _creditbedrag();
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_20"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_20"]);
        $mr["Bedrag"]            = _creditbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_20",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_21"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_21"]);
        $mr["Bedrag"]            = _creditbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_21",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }
      ///////////////////////////////////////////////////////
      if ($data["kosten_22"] )
      {
        $mr["Grootboekrekening"] = "KOBU";
        $mr["Credit"]             = abs($data["kosten_22"]);
        $mr["Bedrag"]            = _creditbedrag($mr["Debet"]);
        $mr = $afw->reWrite("KOSTEN_22",$mr);
        $controleBedrag       += $mr["Bedrag"];
        addAndCheck();
      }

    }
  }


//  $valutaCorrectie = ($data[valuta] <> "EUR")?$mr["Valutakoers"]:1;
//  checkControleBedrag($controleBedrag * $valutaCorrectie);
  checkControleBedrag(abs($controleBedrag) , $data["nettoBedrag"]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_RVP($record)  // Recieve versus Payment call 3738
{

  global $data,$fonds,$mr,$output;

  do_algemeen($data["depotnr"]."MEM");
  $mr["aktie"]              = "RVP";
  $mr["Omschrijving"]      = "Deponering  ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
//		$mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = _valutakoersAIRS();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    addAndCheck();
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_D($record)  // Deponering van stukken
{

  global $data,$fonds,$mr,$output, $meldArray;


  $mr["aktie"]              = "D";
  do_algemeen($data["depotnr"]."MEM");
  if ($data["aantal"] == 0)  // kostenregel onderdrukken
  {
    $meldArray[] = "regel {$mr["regelnr"]} overgeslagen: kostenregel bij Deponering";
    return;
  }

  $data["valuta"] = "EUR";
  $controleBedrag  = 0;
  $mr["Omschrijving"]      = "Deponering  ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
//    $mr["Valutakoers"]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Debet"];
  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    addAndCheck();
  }
  checkControleBedrag(abs($controleBedrag) , abs($data["nettoBedrag"]));
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_D_nul($record)  // Deponering van stukken van met nul waarde
{

  global $data,$fonds,$mr,$output;
  $mr["aktie"]              = "D";
  do_algemeen($data["depotnr"]."MEM");
  $mr["Omschrijving"]      = "Deponering  ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
//    $mr["Valutakoers"]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    addAndCheck();
  }

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L($record)  // Lichting van stukken
{
  global $data,$fonds,$mr,$output, $meldArray;

  $mr["aktie"]              = "L";
  do_algemeen($data["depotnr"]."MEM");

  $controleBedrag       = 0;
  if ($data["aantal"] == 0)  // kostenregel onderdrukken
  {
    $meldArray[] = "regel {$mr["regelnr"]} overgeslagen: kostenregel bij Lichting";
    return;
  }
  $data["valuta"] = "EUR";
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
//		$mr["Valutakoers"]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Credit"];

  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
    $mr["Transactietype"]    = "";

    addAndCheck();

  }

  checkControleBedrag(abs($controleBedrag) , abs($data["nettoBedrag"]));
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_DVP($record)  // Delivery versus Payment call 3738
{
  global $data,$fonds,$mr,$output;

  do_algemeen($data["depotnr"]."MEM");
  $mr["aktie"]              = "DVP";
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
//		$mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = _valutakoersAIRS();

  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = $data["fondskoers"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($data["aantal"] * $data["fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
    $mr["Transactietype"]    = "";
    addAndCheck();
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L_nul($record)  // Lichting van stukken met waarde 0
{
  global $data,$fonds,$mr,$output;
  $mr["aktie"]              = "L";
  do_algemeen($data["depotnr"]."MEM");
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = $data["wisselkoers"];
  $mr["Valutakoers"]       = valutaCorrectie($mr["Valuta"], $data["wisselkoers"]);
//		$mr["Valutakoers"]       = getValutaKoers($mr['Valuta'],$mr[Boekdatum]);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data["aantal"];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  addAndCheck();
  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag($mr["Debet"]);
    $mr["Transactietype"]    = "";
    addAndCheck();
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  global $meldArray, $data, $transactieMappingOms;
  $tac = (int)trim($data["transactieCode"]);
  $meldArray[] = "554: Transactie met ".$tac." (".$transactieMappingOms[$tac].") nettobedrag = ".$data["nettoBedrag"]." overgeslagen";
  return;
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_error($tc="--")
{
	global $do_func;
	echo "<BR>FOUT functie $do_func  ($tc) bestaat niet!";
}


?>