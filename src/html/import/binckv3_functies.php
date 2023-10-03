<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/05 08:47:57 $
 		File Versie					: $Revision: 1.42 $

 		$Log: binckv3_functies.php,v $
 		Revision 1.42  2020/03/05 08:47:57  cvs
 		bug in fondscode lookup
 		
 		Revision 1.41  2020/03/03 12:08:19  cvs
 		getfonds isin != ""
 		
 		Revision 1.40  2020/02/05 14:09:21  cvs
 		PNC koers
 		
 		Revision 1.39  2020/01/14 11:56:19  cvs
 		call 6223
 		
 		Revision 1.38  2019/12/16 11:22:12  cvs
 		call 8306
 		
 		Revision 1.37  2019/12/16 10:59:05  cvs
 		binck fondskoersen
 		
 		Revision 1.36  2019/10/25 12:30:05  cvs
 		tikfout
 		
 		Revision 1.35  2019/07/08 10:32:10  cvs
 		call 7910
 		
 		Revision 1.34  2019/06/05 11:42:44  cvs
 		call 7848
 		
 		Revision 1.33  2019/04/15 14:29:13  cvs
 		call 7715
 		
 		Revision 1.32  2019/04/10 12:45:50  cvs
 		call 7701
 		
 		Revision 1.31  2018/10/22 07:13:05  cvs
 		call 7255
 		
 		Revision 1.30  2018/10/22 06:30:43  cvs
 		call 7255
 		
 		Revision 1.29  2018/10/03 15:27:23  cvs
 		call 7111
 		
 		Revision 1.28  2018/10/02 10:21:52  cvs
 		call 7202
 		
 		Revision 1.27  2018/09/28 06:09:58  cvs
 		call 7193
 		
 		Revision 1.26  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2018/07/03 06:55:00  cvs
 		no message
 		
 		Revision 1.24  2018/05/23 07:45:26  cvs
 		call 4056
 		
 		Revision 1.23  2017/11/02 10:21:08  cvs
 		call 6315
 		
 		Revision 1.22  2017/10/02 13:10:35  cvs
 		call 5477, terugdraaien binckcode mapping
 		
 		Revision 1.21  2017/09/29 12:18:21  cvs
 		call 6223
 		
 		Revision 1.20  2017/09/20 06:16:18  cvs
 		megaupdate
 		

*/


$_transactiecodes = Array("K","V","OK","OV","SK","SV","EX C","EX P",
	                        "AS C","AS P","EMIS","RTDB","RTCR","AFL","LOSB",
	                        "UITK","UITK + DIV","O-G1","O-G","D","L",
                          "O","OMWL", "BYST","OV-F","SK-F","SV-F","OK-F");



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
        Rekeningmutaties.Boekdatum <= '{$mr["Boekdatum"]}' AND
        Rekeningen.Portefeuille = '{$portRec["Portefeuille"]}' AND
        Rekeningmutaties.Fonds = '{$mr["Fonds"]}'
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds
      HAVING 
        round(aantal,4) <> 0
    ";
  $positie = $db->lookupRecordByQuery($query);
//  debug($query);
  return (int) $positie["aantal"];
}

function getTOEKdata()
{
  global $toekDataSet;
  $toekArray = $_SESSION["toeK"];
  $toekDataSet = array();
  $isins          = array_unique($toekArray["isin"]);

  $db = new DB();
  $query = "
    SELECT
      *
    FROM
      Fondsen             
    WHERE
      Left(Fondsen.ISINcode,12) IN ('".implode("','",$isins)."')
    ";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $toekDataSet[$rec["ISINCode"]] = $rec;
  }
//debug($uitkDataSet);
}


function getUITKdata()
{
    global $uitkDataSet;
    $uitkArray = $_SESSION["uitK"];
    $uitkDataSet = array();
    $portefeuilles  = array_unique($uitkArray["portefeuille"]);
    $isins          = array_unique($uitkArray["isin"]);
//    debug($portefeuilles, "PRT".count($portefeuilles));
//    debug($isins, "ISIN");

    $db = new DB();
    $query = "
     SELECT
        Rekeningen.Portefeuille as portefeuille,
        Rekeningmutaties.Fonds AS Fonds,
        Left(Fondsen.ISINcode,12) AS ISIN,
        SUM(Rekeningmutaties.Aantal) AS aantal,
        Portefeuilles.PortefeuilleDepotbank
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
          (Portefeuilles.Portefeuille IN ('".implode("','",$portefeuilles)."') AND LEFT(Portefeuilles.Depotbank,3) = 'BIN') OR
          (Portefeuilles.PortefeuilleDepotbank IN ('".implode("','",$portefeuilles)."') AND LEFT(Portefeuilles.Depotbank,3) = 'BIN')
        ) AND
        Left(Fondsen.ISINcode,12) IN ('".implode("','",$isins)."')
      GROUP BY 
        portefeuille,Rekeningmutaties.Fonds,
        Left(Fondsen.ISINcode,12)
      ORDER BY   
        SUM(Rekeningmutaties.Aantal) ASC
      
    ";

    $db->executeQuery($query);
    while ($rec = $db->nextRecord())
    {

      $prt = $rec["portefeuille"];
      if (in_array($rec["PortefeuilleDepotbank"], $portefeuilles))
      {
        $prt = $rec["PortefeuilleDepotbank"];
      }
      $indx = $prt.$rec["ISIN"];
      $uitkDataSet[$indx] = $rec["Fonds"];
    }

}


function getFonds($data)
{
  global $optieArray, $uitkDataSet, $meldArray, $row, $toekDataSet;
  $binck  = trim($data[30]);
  $isin   = trim($data[19]);
  $optie  = $data[20];
  $valuta = ($data[9] == "PNC")?"GBP":$data[9];
  $fonds  = false;
  $db = new DB();


  if ($isin <> "" AND ($data[6] == "TOEK" OR $data[6] == "UITK") )
  {
    if ($data[6] == "UITK")
    {
      if ($uitkDataSet[$data[1].$data[19]] != "")
      {
        $query = "SELECT * FROM Fondsen WHERE Fonds = '{$uitkDataSet[$data[1].$data[19]]}' ";
        $fonds = $db->lookupRecordByQuery($query);
      }
      else
      {
        // zoeken fonds op ISIN
        $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$isin}' ";
        $fonds = $db->lookupRecordByQuery($query);
      }
      if (!$fonds)
      {
        $meldArray[] = "regel {$row}: Fonds niet gevonden bij UITK ({$isin}/{$binck})";
      }

    }
    else if ($data[6] == "TOEK")
    {
      $fonds = count($toekDataSet[$isin]) > 0?$toekDataSet[$isin]:false;
      if (!$fonds)
      {
        $meldArray[] = "regel {$row}: Fonds niet gevonden bij TOEK ({$isin}/{$binck})";
      }
    }
    else
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '{$isin}' ";
      $fonds = $db->lookupRecordByQuery($query);
    }

    return $fonds;


  }
  else
  {
    if (in_array($optie, $optieArray))  // opties
    {
      $_binckCode = trim($data[31]);
      $query = "SELECT * FROM Fondsen WHERE binckCode = '".$_binckCode."' ";
      $fonds = $db->lookupRecordByQuery($query);
    }
    else  // aandelen etc
    {
      $binckNotFound = true;
      if ($binck <> "")
      {
        $query = "SELECT * FROM Fondsen WHERE binckCode='$binck' ";
        if ($fonds = $db->lookupRecordByQuery($query))
        {
          $binckNotFound = false;
          return $fonds;
        }
      }

      if ($binckNotFound AND $isin != "")
      {
        $query = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='".$valuta."'";
        $fonds = $db->lookupRecordByQuery($query);
      }
      else
      {
        return false;
      }
    }


  }
  return $fonds;
}

function getRekening($rekeningNr="-1", $depot="BIN")
{
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND (`Depotbank` = 'BIN'  OR `Depotbank` = 'BINB' OR `Depotbank` = 'BINS') ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND  (`Depotbank` = 'BIN'  OR `Depotbank` = 'BINB' OR `Depotbank` = 'BINS') ";
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


//aetodo: deze wordt nog niet gebruikt en gaat gebruikt worden in toekomstige call (2 okt 2017)


function getBinckFonds()
{
  global $data, $fonds, $error;
  global $optieArray;
  return false;

  $fonds      = "";

  $_binck     = $data[30];
  $_optie     = $data[20];
  $_isin      = $data[18];
  $_fondsnaam = $data[31];
  if (in_array($_optie, $optieArray))  // opties
  {
    $split = explode(" ", $_fondsnaam);

    $end = count($split);
    $binckCode = $split[0]." %".$split[$end-4]." ".$split[$end-3]." ".$split[$end-2]." ".$split[$end-1];

    $q = "SELECT * FROM Fondsen WHERE binckCode LIKE '".$binckCode."' ";
    if ($fRec = $db->lookupRecordByQuery($q))
    {
      $fonds = $fRec;
    }
    else
    {
      $error[] = "$row :optie/future komt niet voor fonds tabel ($_binckCode)";
    }
  }
  else // aandelen etc
  {

    // eerst AIRS fondscode ophalen

    if ($binck <> "")
    {
      $q = "SELECT * FROM Fondsen WHERE binckCode='$binck' ";
      if ($fRec = $db->lookupRecordByQuery($q))
      {
        $record["fonds"] = $fRec['Fonds'];
      }
    }
    else
    {
      $q = "SELECT * FROM Fondsen WHERE ISINCode='$isin' AND Valuta ='".$valuta."'";
      if ($fRec = $db->lookupRecordByQuery($q) AND $isin <> "")
      {
        $record["fonds"] = $fRec['Fonds'];
      }

    }
  }

}

function setRekeningValuta()
{
  global $mr, $data;
  $mr["Valuta"] = $data[3];
  if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD")
  {
    $mr["Valuta"]  = "EUR";
  }  
  
  if ($mr["Valuta"] == "EUR") 
  {
    $mr["Valutakoers"]  = 1;
  }
    
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray, $portefeuilleAddArray;
  $portefeuilleAddArray[$portefeuille][] = $valuta;

  if (
       ($valuta == "MEM" AND in_array("EUR", $portefeuilleAddArray[$portefeuille]))  OR
       ($valuta == "EUR" AND in_array("MEM", $portefeuilleAddArray[$portefeuille]))
     )
  {
    return; // per portefeuille alleen MEM of EUR toevoegen tweede valuta wordt automatisch erbij aangemaakt
  }

  $value = "BIN|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;
	if ( ($data[3] == "EUR" AND $data[9] <> "EUR") OR strstr($mr["Rekening"],"MEM") )
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  else
    return -1 * $mr["Debet"];
}

function _creditbedrag()
{
	global $data, $mr;
	if ( ($data[3] == "EUR" AND $data[9] <> "EUR") OR strstr($mr["Rekening"],"MEM") )
   return $mr["Credit"] * $mr["Valutakoers"];
  else
	 return $mr["Credit"];
}

function _valutakoers()
{
	global $data;
	$valuta  = $data[9];
	$_bedrag = $data[8];

	if ($valuta <> "PNC")
		return (1/$_bedrag);
	else
	  return (1/($_bedrag/100));
}

function _fondskoers()
{
	global $data, $fonds;

	$valuta  = $data[9];
	$_bedrag = $data[11];

	if ($valuta <> "PNC")
		return $_bedrag;
	else
	  return $_bedrag/100;
}

// function addMeldarray verhuisd naar html/import/algemeneImportFuncties.php call 6734

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$datum = $data[4];
  $tijd  = $data[5];
	$mr[bestand]           = $_file;
	$mr[regelnr]           = $data[99];
  //$mr[Boekdatum]         = substr($datum,4,4)."-".substr($datum,2,2)."-".substr($datum,0,2)." ".$tijd;
  $mr[Boekdatum]         = substr($datum,0,4)."-".substr($datum,5,2)."-".substr($datum,8,2)." 00:00:00";
  $mr[Rekening]          = Trim($data[1]).Trim($data[3]);
  $mr[Rekening]          = getRekening($mr["Rekening"]);
  $mr[bankTransactieId]  = Trim($data[17]);
  $datum = $data[40];
  $mr[settlementDatum]   = substr($datum,0,4)."-".substr($datum,5,2)."-".substr($datum,8,2)." 00:00:00";

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V_OMWL()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw, $fndkrs;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";

  setRekeningValuta();

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[10] * $data[11]);
  if ($mr["Valuta"] == "EUR")
  {
    $mr["Valutakoers"]       = 1;
  }

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag  += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);

  $output[] = $mr;

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_K_OMWL()
{
  global $fonds, $data, $mr, $output, $meldArray, $afw, $fndkrs;
  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "K";
  do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = 0;
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";

  setRekeningValuta();

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[10] * $data[11]);
  if ($mr["Valuta"] == "EUR")
  {
    $mr["Valutakoers"]       = 1;
  }

  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag  += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr = $afw->reWrite("KOST",$mr);

  $output[] = $mr;

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_K()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output, $meldArray, $afw, $fndkrs;
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "K";
	do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];
	$mr["Fondskoers"]        = _fondskoers();
  $mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	//$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);

  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Transactietype"]    = "";

  if ($data[15] <> 0)
	{
		$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "RENME";
//		setRekeningValuta(); //call 7848

		$mr["Debet"]             = abs($data[15]);
		$mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
		$output[] = $mr;
	}

  if ($data[13] <> 0)
	{
    $mr["Grootboekrekening"] = "KOST";
    
		setRekeningValuta();

    $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[13]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[13]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
    $mr = $afw->reWrite("KOST",$mr);

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();
    
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  // moet deze vervangen door setRekeningValuta();??
    $mr["Valuta"]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[16]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[16]*$data[8]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr = $afw->reWrite("KOBU1",$mr);
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}


// extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  setRekeningValuta();
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[36]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[36]*$data[8]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
    $mr = $afw->reWrite("KOBU2",$mr);
	  $output[] = $mr;
	}

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output, $meldArray,$afw, $fndkrs;
  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]              = "V";


	do_algemeen();
  if ($data[111] <> "")
	  $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
	  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];
	$mr["Fondskoers"]        = _fondskoers();
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;
  //$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);

  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Transactietype"]    = "";
  if ($data[15] <> 0)
	{
		$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "RENOB";  // call 4320 renme gewijzigid naar renob
		// setRekeningValuta();   // call 7848
    
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[15]);
		$mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

		$output[] = $mr;
	}

  if ($data[13] <> 0)
	{
	  $mr["Grootboekrekening"] = "KOST";
	  setRekeningValuta();
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[13]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[13]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
    $mr = $afw->reWrite("KOST",$mr);
	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();
    
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[16]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[16]*$data[8]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
    $mr = $afw->reWrite("KOBU1",$mr);

	  $output[] = $mr;
	}

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  setRekeningValuta();
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Debet"]             = abs($data[36]);
	    $mr["Valutakoers"]       = 1;
	  }
	  else
	    $mr["Debet"]             = abs($data[36]*$data[8]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
    $mr = $afw->reWrite("KOBU1",$mr);
	  $output[] = $mr;
	}
  
  if ($data[97] == "stoploss")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> Stoploss  ";
  }
  else
  {
    addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
  }
  
  
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OK()  //Aankoop openen bij opties
{

  global $fonds, $data, $mr, $output, $meldArray, $fndkrs;
	$mr = array();
  $controleBedrag = 0;

	$mr["aktie"]             = "OK";
	do_algemeen();

	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];
	$mr["Fondskoers"]        = _fondskoers();
  $mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A/O";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;
  //$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);

  if ($data[13] <> 0)
	{
	  $mr["Grootboekrekening"] = "KOST";
	  setRekeningValuta();

    //$mr[Fonds]             = "";
  	$mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[13]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();
    
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
	{
		$mr["Grootboekrekening"] = "RENME";
		setRekeningValuta();
  	
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[15]);
		$mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";

		$output[] = $mr;
	}

   if ($data[16] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Valutakoers"]       = 1;
	  }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);
	  
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  setRekeningValuta();
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Valutakoers"]       = 1;
	  }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[36]);

	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

    addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OV()  //Verkoop openen bij opties
{
  global $fonds, $data, $mr, $output, $meldArray, $fndkrs;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "OV";
	do_algemeen();
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = _fondskoers();
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "V/O";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;
  //$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);

	if ($data[13] <> 0)
	{
	  $mr["Grootboekrekening"] = "KOST";
    setRekeningValuta();    
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($data[13]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();
    
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
	{
		$mr["Grootboekrekening"] = "RENME";
		setRekeningValuta();
  	
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[15]);
		$mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";

		$output[] = $mr;
	}

  if ($data[16] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Valutakoers"]       = 1;
	  }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);
    
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}
  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  setRekeningValuta();
    
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Valutakoers"]       = 1;
	  }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[36]);
    
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

    addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SK()  //Aankoop sluiten bij opties
{
  global $fonds, $data, $mr, $output, $meldArray, $fndkrs;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "SK";
  do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = _fondskoers();
  $mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  //$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);

  if ($data[13] <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[13]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr["Grootboekrekening"] = "RENME";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[15]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr[Valuta] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[36]);

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV()  //Verkoop sluiten bij opties
{
  global $fonds, $data, $mr, $output, $meldArray, $fndkrs;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "SV";
  do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = _fondskoers();
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;
  //$fndkrs->addToArray($mr["Fonds"], $mr["settlementDatum"], $mr["Fondskoers"]);


  if ($data[13] <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[13]);  // dbs 2749 kosten nu in valuta
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Debet"]             = abs($data[14]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr["Grootboekrekening"] = "RENME";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[15]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[36]);  //dbs 2749 kosten nu in valuta


    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_C()  //
{
  
  global $fonds, $data, $mr, $output, $meldArray;
  $data[111] = "Assignment";
  if ($data[98] == "-" ) 
  {
    if (strtolower($data[29]) <> "indexopties")
    {
      return do_V();
    }
      
  } 
  else
  {
    return do_SK();  
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_P()  //
{
  global $fonds, $data, $mr, $output, $meldArray;

  $data[111] = "Assignment";
  if ($data[98] == "-") 
  {
    if (strtolower($data[29]) <> "indexopties")
    {
      return do_K();
    }  
  } 
  else
  {
    return do_SK();  
  }
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_C()  //Exercise Call optie
{
  global $fonds, $data, $mr, $output, $meldArray;
  $data[111] = "Exercise";
  if ($data[98] == "-") 
  {
    if (strtolower($data[29]) <> "indexopties")
    {
      return do_K();
    }  
  } 
  else
  {
    return do_SV();  
  }
  //return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EX_P()  //Exercise Call optie
{
  global $fonds, $data, $mr, $output, $meldArray;
  $data[111] = "Exercise";
  if ($data[98] == "-") 
  {
    if (strtolower($data[29]) <> "indexopties")
    {
      return do_V();
    }  
  } 
  else
  {
    return do_SV();  
  }
  //return do_SV();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_C1()  //Exercise Call optie
{
  return do_SK();
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AS_P1()  //Exercise Call optie
{
  return do_SV();
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_EMIS()  // Emissie van stukken
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]             = "EMIS";
	do_algemeen();

	$mr[Omschrijving]      = "Emissie ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = _debetbedrag();
	$mr[Transactietype]    = "A";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_BYST()  //
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"]             = "BYST";
  do_algemeen();

  $mr["Omschrijving"]      = "Bijstelling ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "VMAR";
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($data[12] < 0)
  {
    $mr["Debet"]             = abs($data[12]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[12]);
    $mr["Bedrag"]            = _creditbedrag();
  }


  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RTDB()  //Geldrente debetboeking
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "RTDB";
	do_algemeen();
	if ($data[3])
	{
    $mr[Omschrijving]      = "Debetrente";
	  $mr[Grootboekrekening] = "RENTE";
		$mr[Valuta]            = $data[9];
		$mr[Valutakoers]       = (1/$data[8]);
		$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = abs($data[12]);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;

		$output[] = $mr;
	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RTCR()  //Geldrente debetboeking
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "RTCR";
	do_algemeen();
	if ($data[3])
	{
    $mr["Omschrijving"]      = "Creditrente";
	  $mr["Grootboekrekening"] = "RENTE";
		$mr["Valuta"]            = $data[9];
		$mr["Valutakoers"]       = (1/$data[8]);
		$mr["Fonds"]             = "";
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		if ($data[12] > 0)
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[12]);
      $mr["Bedrag"]            = _creditbedrag();
    }
		else
    {
      $mr["Debet"]             = abs($data[12]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;

		$output[] = $mr;
	}

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_AFL()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "AFL";
	do_algemeen();
	$mr[Omschrijving]      = "Lossing ".$fonds[Omschrijving];
	$mr[Grootboekrekening] = "FONDS";
	$mr[Valuta]            = $fonds[Valuta];
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = $fonds[Fonds];
	$mr[Aantal]            = $data[10];
	$mr[Fondskoers]        = _fondskoers();
  $mr[Debet]             = 0;
	$mr[Credit]            = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);
	$mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
	$mr[Transactietype]    = "V";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;
  if ($data[13] > 0)
  {
	  $mr[Grootboekrekening] = "KOST";
	  $mr[Valuta]            = $data[9];
	  if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
	  if ($mr[Valutakoers] == "EUR")
	    $mr[Debet]             = abs($data[13]);
	  else
	    $mr[Debet]             = abs($data[13]*$data[8]);

	  $mr[Credit]            = 0;
	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    $mr[Valuta]            = $data[3];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[14]);
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

	if( $data[15] <> 0)
	{
		$mr[Grootboekrekening] = "RENME";
		$mr[Valuta]            = $data[9];
		if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
  	if ($mr[Valuta] == "EUR") $mr[Valutakoers]  = 1;
  	//$mr[Fonds]             = "";
		$mr[Aantal]            = 0;
		$mr[Fondskoers]        = 0;
		$mr[Debet]             = 0;
		$mr[Credit]            = abs($data[15]);
		$mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
		$mr[Transactietype]    = "";

		$output[] = $mr;

	}

  if ($data[16] <> 0)
	{
    $mr[Grootboekrekening] = "KOBU";
	  $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr[Valuta] == "EUR")
	  {
	    $mr[Debet]             = abs($data[16]);
	    $mr[Valutakoers]       = 1;
	  }
	  else
	    $mr[Debet]             = abs($data[16]*$data[8]);

	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr[Grootboekrekening] = "KOBU";
	  $mr[Valuta]            = $data[3];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr[Aantal]            = 0;
	  $mr[Fondskoers]        = 0;
    $mr["Credit"]            = 0;
	  if ($mr[Valuta] == "EUR")
	  {
	    $mr[Debet]             = abs($data[36]);
	    $mr[Valutakoers]       = 1;
	  }
	  else
	    $mr[Debet]             = abs($data[36]*$data[8]);

	  $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
	  $mr[Transactietype]    = "";

	  $output[] = $mr;
	}

    addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}





/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_UITK()  //Rente obligaties /contant dividend
{
  global $fonds, $data, $mr, $output, $meldArray, $afw;
	$mr = array();
  $controleBedrag = 0;

	$_soort = trim($data[20]);
	$mr["aktie"]              = "UITK + ".$_soort;
	do_algemeen();

	if ($_soort == "COUP")
	{
	  $mr["Omschrijving"]      = "Rente ".$fonds["Omschrijving"];
	  $mr["Grootboekrekening"] = "RENOB";
//		$mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valuta"]            = ($data[9] == "PNC")?"GBP":$data[9];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
    if (($data[10] * $data[11]) * $fonds["Fondseenheid"] >= 0)  //call 4123
    {
      $mr["Debet"]             = abs(($data[10] * $data[11]) * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs(($data[10] * $data[11]) * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = _creditbedrag();
    }
		
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;

		$output[] = $mr;

	}
	else // CONTANT DIVIDEND
	{

	  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
	  $mr["Grootboekrekening"] = "DIV";
//		$mr["Valuta"]            = $fonds["Valuta"];
    $mr["Valuta"]            = ($data[9] == "PNC")?"GBP":$data[9];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
    if (($data[10] * $data[11]) * $fonds["Fondseenheid"] >= 0)  //call 4123
    {
      $mr["Debet"]             = abs(($data[10] * $data[11]) * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      if ($data[9] == "PNC")
        $mr["Debet"] = $mr["Debet"]/100;

      $mr["Bedrag"]            = _debetbedrag();
    }
    else
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs(($data[10] * $data[11]) * $fonds["Fondseenheid"]);
      if ($data[9] == "PNC")
        $mr["Credit"] = $mr["Credit"]/100;
      
      $mr[Bedrag]            = _creditbedrag();
    }
		
    $controleBedrag       += $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;



		$output[] = $mr;

	}
  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "DIVBE";
	  $mr["Valuta"]            = $data[3];
	  //if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    if ($mr["Valuta"] == "EUR") $mr["Valutakoers"]  = 1;
	  //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
//	  if ($mr[Valuta] == "USD")
//      $mr[Debet]             = abs($data[14] * $data[8]);
//	  else
    if ($data[14] < 0)  //call 4123
    {
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[14]);
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Debet"]             = abs($data[14]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = _debetbedrag();
    }
		
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("DIVBE",$mr);
	  $output[] = $mr;
  }

  if ($data[16] <> 0)
	{
    $mr["Grootboekrekening"] = "KOBU";
	  $mr["Valuta"]            = $data[3];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD")
    {
      $mr["Valuta"]  = "EUR";
    }
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);
	  if ($mr["Valuta"] == "EUR")
	  {
	    $mr["Valutakoers"]       = 1;
	  }
	  else
    {
      $mr["Valutakoers"]       = (1/$data[8]);
    }

    if ($data[16] < 0)//call 4123
    {
      $mr["Credit"]  = $mr["Debet"];
      $mr["Debet"]   = 0;
      $mr["Bedrag"]  = _creditbedrag();
    }
    else
    {
      $mr["Bedrag"]            = _debetbedrag();
    }
	  
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
	{
    $mr["Grootboekrekening"] = "KNBA";
	  $mr["Valuta"]            = $data[3];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($data[16]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    else
    {
      $mr["Valutakoers"]       = (1/$data[8]);
    }

    if ($data[36] < 0)  //call 4123
    {
      $mr["Credit"]  = $mr["Debet"];
      $mr["Debet"]   = 0;
      $mr["Bedrag"]  = _creditbedrag();
    }
    else
    {
      $mr["Bedrag"]            = _debetbedrag();
    }
	  
    $controleBedrag  += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";

	  $output[] = $mr;
	}

    addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_O_G1()  //Bewaarloon
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr[aktie]              = "O-GI";
	do_algemeen();
	$mr[Omschrijving]      = "Bewaarloon effecten";
	$mr[Grootboekrekening] = "BEW";
	$mr[Valuta]            = $data[9];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
	$mr[Valutakoers]       = _valutakoers();
	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
  $mr[Debet]             = abs($data[12]);
	$mr[Credit]            = 0;
	$mr[Bedrag]            = -1 * $mr[Debet];
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;

	$output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_O_G()  //Opname van Geld
{
  global $fonds, $data, $mr, $output, $afw, $meldArray;
  

  
	$mr = array();
	$mr[aktie]              = "O-G";
	do_algemeen();

  if ($data[97] == "stoploss")
  {
    $meldArray[] = "regel ".$mr["regelnr"].": rek ".$mr["Rekening"]."  stoploss overgeslagen";
    return false;
  }

	$mr[Valuta]            = $data[3];
	if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
	if ($mr[Valuta] <> "EUR")
	  $mr[Valutakoers]     = _valutakoers();
	else
	  $mr[Valutakoers]     = 1;

	$mr[Fonds]             = "";
	$mr[Aantal]            = 0;
	$mr[Fondskoers]        = 0;
	$mr[Transactietype]    = "";
	$mr[Verwerkt]          = 0;
	$mr[Memoriaalboeking]  = 0;
  $mr["Omschrijving"]      = trim( trim($data[24])." ".trim($data[25]) );

	if ($data[12] < 0)
	{
    if ($mr["Omschrijving"] == "")
    {
      $mr["Omschrijving"] = "Onttrekking ";
    }
	  switch (substr($mr[Omschrijving],0,18))
	  {
	    case "Kosten afschriften":
	      $mr["Grootboekrekening"] = "KNBA";
        $mr = $afw->reWrite("GLDKNBA",$mr);
	      break;
	    case "naar uw EUR-rekeni":
	    case "naar uw USD-rekeni":
	      if (trim($data[27]) != "")
        {
          $mr["Omschrijving"]      = ucfirst( trim($data[27])) ;
        }
	      else
        {
          $mr["Omschrijving"]      = "Valuta-transactie" ;
        }

      case "Naar uw vreemde va":
      $mr[Grootboekrekening] = "KRUIS";

	      break;
	    default:
	      $mr[Grootboekrekening] = "ONTTR";

	  }
    
    if (strtoupper(substr($mr[Omschrijving],3,3)) == "FTT")
    {
      $mr[Grootboekrekening] = "KOBU";
    }

		$mr[Debet]             = ($data[12] * -1);
		$mr[Credit]            = 0;
		$mr[Bedrag]            = _debetbedrag();
    $mr = $afw->reWrite("GLDONTTR",$mr);

	}
	else
	{
    if ($mr["Omschrijving"] == "")
    {
      $mr["Omschrijving"] = "Storting ";
    }
	  switch (substr($mr[Omschrijving],0,18))
	  {
	    case "naar uw EUR-rekeni":
	    case "naar uw USD-rekeni":
        if (trim($data[27]) != "")
        {
          $mr["Omschrijving"]      = ucfirst( trim($data[27])) ;
        }
        else
        {
          $mr["Omschrijving"]      = "Valuta-transactie" ;
        }
      case "Naar uw vreemde va":
	      $mr[Grootboekrekening] = "KRUIS";
	      break;
	    default:
	      $mr[Grootboekrekening] = "STORT";
	  }

		$mr[Debet]             = 0;
		$mr[Credit]            = $data[12];
		$mr[Bedrag]            = _creditbedrag();
    $mr = $afw->reWrite("GLDSTORT",$mr);
	}

	$output[] = $mr;


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_D()  // Deponering van stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "D";

  $data["isOptie"] = ($fonds["fondssoort"] == "OPT");

	do_algemeen();

	$mr["Rekening"]          = trim($data[1])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];

  $mr["Fondskoers"]        = _fondskoers();

	$mr["Debet"]             = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);

	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"] = "D";

  if ($data["isOptie"])
  {
    if ($data[24] == "Expiratie" OR $data[24] == "Expiry")
    {
      $mr["Transactietype"] = "V/S";
    }
    else
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
  }



	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 1;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "STORT";
	$mr["Fonds"]             = "";
	$mr["Valuta"]            = "EUR";
	$mr["Valutakoers"]       = 1;
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Bedrag"]);
	$mr["Bedrag"]            = $mr["Credit"];
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";

  if ($mr["Bedrag"] <> 0)
	 $output[] = $mr;

  if ($data[15] <> 0)
	{
    // 28-5-2014:  rente in valuta (v2 was altijd in EUR)
		$mr["Omschrijving"]      = "Meegekochte rente ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "RENME";
    $mr["Valuta"]            = $fonds["Valuta"];
		$mr["Valutakoers"]       = _valutakoers();
    $mr["Aantal"]            = 0;
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = abs($data[15]);
		$mr["Credit"]            = 0;
		$mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
    $controleBedrag       += $mr["Bedrag"];

		$output[] = $mr;

		$mr["Omschrijving"]      = "Opgelopen rente ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "STORT";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($mr["Bedrag"]);
		$mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $controleBedrag       += $mr["Bedrag"];

		$output[] = $mr;

	}



    addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_L()  // Lichting van stukken
{
  global $fonds;
	global $data;
	global $mr;

	global $output, $meldArray;
  
  
  
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "L";
	do_algemeen();
  $data["isOptie"] = ($fonds["fondssoort"] == "OPT");
	$mr["Rekening"]          = trim($data[1])."MEM";
  $mr["Rekening"]          = getRekening($mr["Rekening"]);

  if ($data[97] == "stoploss")
  {
    return do_V();
  }

  if ($data[20] == "FUT")
  {
    return do_SV_F();
  }
  
	$mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = _fondskoers();

	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Fondskoers"] * $mr["Aantal"] * $fonds["Fondseenheid"]);

	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];


  $mr["Transactietype"] = "L";

  if ($data["isOptie"])
  {
    if ($data[24] == "Expiratie" OR $data[24] == "Expiry")
    {
      $mr["Transactietype"] = "A/S";
    }
    else
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

  }


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
	  $output[] = $mr;

	if ($data[15] <> 0)
	{
    // 28-5-2014:  rente in valuta (v2 was altijd in EUR)
		$mr["Omschrijving"]      = "Meeverkochte rente ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "RENOB";
		$mr["Valuta"]            = $fonds["Valuta"];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = $fonds["Fonds"];
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[15]);
		$mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
    $controleBedrag       += $mr["Bedrag"];

		$output[] = $mr;

		$mr["Omschrijving"]      = "Opgelopen rente ".$fonds["Omschrijving"];
		$mr["Grootboekrekening"] = "ONTTR";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = abs($mr["Bedrag"]);
		$mr["Credit"]            = 0;
		$mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
    $controleBedrag       += $mr["Bedrag"];

		$output[] = $mr;

	}

  addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_TOEK()  // toekenning  van stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output, $meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "D";
	do_algemeen();

	if (!$fonds)
  {
    return false;
  }


  $_soort = trim($data[20]);
	if ($_soort == "")
  {

  	$mr[Rekening]          = trim($data[1])."MEM";
    $mr[Rekening]          = getRekening($mr["Rekening"]);
  	$mr[Omschrijving]      = "Deponering ".$fonds[Omschrijving];
  	$mr[Grootboekrekening] = "FONDS";
  	$mr[Valuta]            = $fonds[Valuta];
  	$mr[Valutakoers]       = _valutakoers();
  	$mr[Fonds]             = $fonds[Fonds];
  	$mr[Aantal]            = $data[10];
  	$mr[Fondskoers]        = _fondskoers();
  	$mr[Debet]             = abs($mr[Fondskoers] * $mr[Aantal] * $fonds[Fondseenheid]);

  	$mr[Credit]            = 0;
  	$mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

  	$mr[Transactietype]    = "D";
  	$mr[Verwerkt]          = 0;
  	$mr[Memoriaalboeking]  = 1;

  	$output[] = $mr;

  	$mr[Grootboekrekening] = "STORT";
  	$mr[Fonds]             = "";
  	$mr[Valuta]            = "EUR";
  	$mr[Valutakoers]       = 1;
  	$mr[Aantal]            = 0;
  	$mr[Fondskoers]        = 0;
  	$mr[Debet]             = 0;
  	$mr[Credit]            = abs($mr[Bedrag]);
  	$mr[Bedrag]            = $mr[Credit];
    $controleBedrag       += $mr[Bedrag];
  	$mr[Transactietype]    = "";

    if ($mr[Bedrag] <> 0)
  	 $output[] = $mr;


      addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
  }
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_O()  // toegevoeg 18 feb 2010
{
  global $data;
  if ($data[10] < 0)
  {
    do_L();
  }
  else
  {
    do_D();  
  }

}



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SK_F()  //Aankoop sluiten bij futures
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]             = "SKF";
  do_algemeen();
  if ($data[111] <> "")
    $mr["Omschrijving"]      = $data[111]." ".$fonds["Omschrijving"];
  else
    $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $fonds[Valuta];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds[Fonds];
  $mr["Aantal"]            = $data[10];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "A/S";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  if ($data[13] <> 0)
  {
    $mr["Grootboekrekening"] = "KOST";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[13]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr["Grootboekrekening"] = "TOB";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[14]);
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }

    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr["Grootboekrekening"] = "RENME";
    setRekeningValuta();

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[15]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr["Grootboekrekening"] = "KOBU";
    $mr["Valuta"]            = $data[9];
    if ($mr["Valuta"] <> "EUR" AND $mr["Valuta"] <> "USD") $mr["Valuta"]  = "EUR";
    //$mr[Fonds]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($mr["Valuta"] == "EUR")
    {
      $mr["Valutakoers"]       = 1;
    }
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[16]);
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag  += $mr["Bedrag"];
    $mr["Transactietype"]    = "";

    $output[] = $mr;
  }

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[36]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_SV_F()  //Verkoop sluiten bij futures
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr[aktie]              = "SV";
  do_algemeen();
  if ($data[111] <> "")
    $mr[Omschrijving]      = $data[111]." ".$fonds[Omschrijving];
  else
    $mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $fonds[Valuta];
  $mr[Valutakoers]       = _valutakoers();
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[10];
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
  $mr[Credit]            = 0;
  $mr[Bedrag]            = _creditbedrag();
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "V/S";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;

  $output[] = $mr;

  if ($data[13] <> 0)
  {
    $mr[Grootboekrekening] = "KOST";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Credit]            = 0;
    $mr[Debet]             = abs($data[13]);  // dbs 2749 kosten nu in valuta
    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }

    $mr[Debet]             = abs($data[14]);
    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr[Grootboekrekening] = "RENME";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($data[15]);
    $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[16]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[36]);  //dbs 2749 kosten nu in valuta


    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OK_F()  //Aankoop openen bij futures
{

  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $controleBedrag = 0;

  $mr[aktie]             = "OK";
  do_algemeen();

  $mr[Omschrijving]      = "Aankoop ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $fonds[Valuta];
  $mr[Valutakoers]       = _valutakoers();
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[10];
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
  $mr[Credit]            = 0;
  $mr[Bedrag]            = 0;
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "A/O";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;

  $output[] = $mr;

  if ($data[13] <> 0)
  {
    $mr[Grootboekrekening] = "KOST";
    setRekeningValuta();

    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[13]);
    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[14]);
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr[Grootboekrekening] = "RENME";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($data[15]);
    $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[16]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[36]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OV_F()  //Verkoop openen bij futures
{
  global $fonds, $data, $mr, $output, $meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr[aktie]              = "OV";
  do_algemeen();
  $mr[Omschrijving]      = "Verkoop ".$fonds[Omschrijving];
  $mr[Grootboekrekening] = "FONDS";
  $mr[Valuta]            = $fonds[Valuta];
  $mr[Valutakoers]       = _valutakoers();
  $mr[Fonds]             = $fonds[Fonds];
  $mr[Aantal]            = $data[10];
  $mr[Fondskoers]        = 0;
  $mr[Debet]             = 0;
  $mr[Credit]            = 0;
  $mr[Bedrag]            = 0;
  $controleBedrag       += $mr[Bedrag];
  $mr[Transactietype]    = "V/O";
  $mr[Verwerkt]          = 0;
  $mr[Memoriaalboeking]  = 0;

  $output[] = $mr;

  if ($data[13] <> 0)
  {
    $mr[Grootboekrekening] = "KOST";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Credit]            = 0;
    $mr[Debet]             = abs($data[13]);
    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];

    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[14] <> 0)
  {
    $mr[Grootboekrekening] = "TOB";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = abs($data[14]);
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if( $data[15] <> 0)
  {
    $mr[Grootboekrekening] = "RENME";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    $mr[Debet]             = 0;
    $mr[Credit]            = abs($data[15]);
    $mr[Bedrag]            = _creditbedrag();
    $controleBedrag       += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  if ($data[16] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    $mr[Valuta]            = $data[9];
    if ($mr[Valuta] <> "EUR" AND $mr[Valuta] <> "USD") $mr[Valuta]  = "EUR";
    //$mr[Fonds]             = "";
    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[16]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }
  // extra veld vanaf V3, 10-10-2013
  if ($data[36] <> 0)
  {
    $mr[Grootboekrekening] = "KOBU";
    setRekeningValuta();

    $mr[Aantal]            = 0;
    $mr[Fondskoers]        = 0;
    if ($mr[Valuta] == "EUR")
    {
      $mr[Valutakoers]       = 1;
    }
    $mr["Credit"]            = 0;
    $mr[Debet]             = abs($data[36]);

    $mr[Bedrag]            = _debetbedrag();
    $controleBedrag  += $mr[Bedrag];
    $mr[Transactietype]    = "";

    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr[regelnr], $mr[Rekening], $data[12]);
}





/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_LOSB()
{
  // dummy functie
}



function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>