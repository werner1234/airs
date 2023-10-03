<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/08 10:43:40 $
 		File Versie					: $Revision: 1.11 $

 		$Log: kasbank_functies.php,v $
 		Revision 1.11  2019/07/08 10:43:40  cvs
 		call 7867
 		
 		Revision 1.10  2019/06/17 08:31:55  cvs
 		call 7869
 		
 		Revision 1.8  2019/05/03 07:32:01  cvs
 		call 7733
 		
 		Revision 1.7  2019/04/29 11:52:55  cvs
 		call 7733
 		
 		Revision 1.6  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/01/22 10:26:12  cvs
 		call 6529
 		
 		Revision 1.4  2017/03/17 13:12:16  cvs
 		no message
 		
 		Revision 1.3  2016/11/30 14:17:47  cvs
 		call 5407
 		
 		Revision 1.2  2016/10/21 13:52:19  cvs
 		call 5346
 		
 		Revision 1.1  2014/11/05 12:51:52  cvs
 		dbs 2751
 		
 	

*/

function useAltCode($portefeuille)
{
  global $transActieRec;
  if ($transActieRec["actieAlternatief"] <> "")     // er is een alternatieve actie
  {
    $p = explode(";", $transActieRec["portefeuillesAltActies"]);
    foreach ($p as $t)
    {
      if (trim($t) <> "")  // is array item <> ""
      {
        $portefeuilleArray[] = trim($t);
      }
    }
    if (in_array($portefeuille, $portefeuilleArray))  // komt portefeuille voor in array
    {
      return true;
    }
  }
  return false;
}



function getTAcode($code)
{
  global $transActieRec;
  $db = new DB();
  $query = "SELECT * FROM kasBankTransactieCodes WHERE kasbankCode = '".$code."'";
  if ($transActieRec = $db->lookupRecordByQuery($query))
  {
    return $transActieRec;
  }
  else
  {
    return false;  
  }  
}

function textPart($str, $start, $stop)
{
  $len = $stop - $start + 1;
  return trim(substr($str, $start-1,$len));
}

function ontnullen($in)
{
  while (substr($in,0,1) == "0")
  {
    $in = substr($in,1);
  }
  return $in;
}

function decodeDate($date)
{
  return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
}  

function decodeCur($value)
{
  return round($value/100,2);
}

function deSpace($value)
{
  while (strstr($value,"  "))
  {
    $value = str_replace("  ", " ", $value);
  }
  return $value;
}
/*
F  L    from  to  M   dataNr   Description                       Particulars 
===========================================================================
N  5    1     5       [1]      client number   
X  35   6     40      [2]      account number   
N  8    41    48      [3]      date of bank statement             ccyymmdd 
N  5    49    53      [4]      bank statement number   
X  16   54    69      [5]      electronic message number   
N  8    70    77      [6]      date opening balance                ccyymmdd 
X  3    78    80      [7]      currency code opening balance       ISO format 
N  1    81    81      [8]      sign opening balance                + or - 
N  15   82    96      [9]      opening balance (1)                 2 decimals 
X  16   97    112     [10]     reference KAS BANK   
X  16   113   128     [11]     reference cli�nt   
X  4    129   132     [12]     initiating message   
N  8    133   140     [13]     value date                          ccyymmdd 
N  8    141   148     [14]     entry date                          ccyymmdd 
X  1    149   149     [15]     reversal indication                 0 or 1 
X  3    150   152     [16]     currency code movement              ISO-formaat 
N  1    153   153     [17]     sign movement                       + or - 
N  15   154   168     [18]     amount of movement  (1)             2 decimals 
X  35   169   203     [19]     account nr counterparty   
X  35   204   238     [20]     name counterparty   
X  35   239   273     [21]     address counterparty   
X  35   274   308     [22]     residence counterparty   
X  35   309   343     [23]     country counterparty   
X  4    344   347     [24]     transaction type   
X  50   348   397     [25]     specification of type   
X  390  398   787     [26]     specification of entry   
N  8    788   795     [27]     processing date closing balance       ccyymmdd 
N  7    796   802     [28]     processing time closing balance       hhmmssm 
X  3    803   805     [29]     currency code closing balance         ISO format 
N  1    806   806     [30]     sign closing balance                  + or - 
N  15   807   821     [31]     closing balance  (1)                  2 decimals 
X  16   822   837     [32]     transaction reference number   
X  16   838   853     [33]     related reference   
X  4    854   857     [34]     transaction code swift   
N  17   858   874     [35]     opening balance (2)                   2 decimals 
N  17   875   891     [36]     amount of movement  (2)               2 decimals 
N  17   892   908     [37]     closing balance   (2)                 2 decimals 
X  12   909   920     [38]     securities code                       ISIN 
X  120  921   1040    [39]     filler                                Spaces 
*/
function convertFixedLine($rawData,$debug=false)
{
  
    $data[1]  = textPart($rawData,1,5);
    $data[2]  = textPart($rawData,6,40) * 1;
    $data[3]  = decodeDate(textPart($rawData,41,48));
    $data[4]  = textPart($rawData,49,53) * 1;
    $data[5]  = textPart($rawData,54,69);
    $data[6]  = decodeDate(textPart($rawData,70,77));
    $data[7]  = textPart($rawData,78,80);
    $data[8]  = textPart($rawData,81,81);
    $data[9]  = decodeCur(textPart($rawData,82,96));
    $data[10] = textPart($rawData,97,112);
    $data[11] = textPart($rawData,113,128);
    $data[12] = textPart($rawData,129,132);
    $data[13] = decodeDate(textPart($rawData,133,140));
    $data[14] = decodeDate(textPart($rawData,141,148));
    $data[15] = textPart($rawData,149,149);
    $data[16] = textPart($rawData,150,152);
    $data[17] = textPart($rawData,153,153);
    $data[18] = decodeCur(textPart($rawData,154,168));
    $data[19] = textPart($rawData,169,203) * 1;
    $data[20] = textPart($rawData,204,238);
    $data[21] = textPart($rawData,239,273);
    $data[22] = textPart($rawData,274,308);
    $data[23] = textPart($rawData,309,343);
    $data[24] = textPart($rawData,344,347);
    $data[25] = textPart($rawData,348,397);
    $data[26] = deSpace(textPart($rawData,398,787));
    $data[27] = decodeDate(textPart($rawData,788,795));
    $data[28] = textPart($rawData,796,802);
    $data[29] = textPart($rawData,803,805);
    $data[30] = textPart($rawData,806,806);
    $data[31] = decodeCur(textPart($rawData,807,821));
    $data[32] = textPart($rawData,822,837);
    $data[33] = textPart($rawData,838,853);
    $data[34] = textPart($rawData,854,857);
    $data[35] = decodeCur(textPart($rawData,858,874));
    $data[36] = decodeCur(textPart($rawData,875,891));
    $data[37] = decodeCur(textPart($rawData,892,908));
    $data[38] = textPart($rawData,909,920);
    $data[39] = deSpace(textPart($rawData,921,1040));
    
  return $data;
}


function addmeldarray($controleBedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($data[18],2);
  
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit niet aan nota= ".$notabedrag." / controle = ".$controleBedrag." / verschil = ".round($notabedrag - $controleBedrag,2);
  else
    $meldArray[] = "regel ".$mr["regelnr"].": ".$mr["Rekening"]." --> notabedrag sluit aan ";
}

function doOutput($mr)
{
  global $output, $data;

  // controle storneringen
  if ($data[33] <> "")
  {
    $tmp            = $mr["Debet"];
    $mr["Debet"]    = $mr["Credit"];
    $mr["Credit"]   = $tmp;
    $mr["Bedrag"]   = $mr["Bedrag"] * -1;
    $mr["Omschrijving"] = "STORNO: ".$mr["Omschrijving"];
  }

  $output[] = $mr;

}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[22];
	if ($valutaLookup == true)
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[22];
	if ($valutaLookup == true)
	  return $mr["Credit"];
	else
	  return $mr["Credit"]  * $mr["Valutakoers"];
}

function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[22];

	$valutaLookup = false;
	if ($valuta <> "EUR" AND $mr["Valuta"] == $valuta)
	{
		 $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC LIMIT 1";
     $DB->SQL($query);
     $laatsteKoers = $DB->lookupRecord();
     $valutaLookup = true;
     return $laatsteKoers["Koers"];
	}
	else
	  return $data[14];
}

function modifyDescription($omschr)
{
  global $__appvar, $data;

  $capitalize = false;

  if (strstr($omschr, "BEA   NR" ))
  {
    $txt = explode(",PAS", $omschr);
    $txt = explode("/", $txt[0]);
    $txt = substr($txt[1],6);
    $omschr = "BEA: ".$txt;
  }

  if (stristr($omschr, "/REMI/"))
  {
    $exp = explode("/REMI/",$omschr);
    $omschr = $exp[1];
  }
  if (stristr($omschr, "/IREF/"))
  {
    $exp = explode("/IREF/",$omschr);
    $omschr = $exp[0];
    if (trim(strtoupper($omschr)) == "NOTPROVIDED")
    {
      $omschr = $data[11];
    }
  }
  if ($capitalize )
  {
    $omschr = strtoupper(substr($omschr,0,1)).strtolower(substr($omschr,1));
  }

  $omschOut = $omschr;

  while (strstr($omschOut, "  "))
  {
    $omschOut = str_replace("  "," ", $omschOut);
  }
  return trim($omschOut);
}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'SNS' ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
    $output = $record["Rekening"];
  else
  {
    // rekeningnr bijzoeken via portnr+mem methode (tnt 30-1-2013)
    $query = "SELECT * FROM Rekeningen WHERE consolidatie= 0 AND Rekening = '".trim($port)."MEM' ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();

    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$tempRec["Portefeuille"]."' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'SNS' ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();
    $output = $tempRec["Rekening"];
  }  
  return $output;
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file, $valutaLookup, $controleBedrag;
  $mr["OmschrijvingOrg"]   = $data[26];
	$mr["Boekdatum"]         = $data[14];
	$mr["settlementDatum"]   = $data[13];
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
  $mr["aktie"]             = $data[24];
  $controleBedrag        = 0;

	$valutaAanduidingen = array(7,16,29);
	$valutaVertalingen = array('DKK'=>'DKR');
  $valutaLookup = false;
	foreach ($valutaAanduidingen as $id)
	{
	  if (array_key_exists($data[$id],$valutaVertalingen))
	    $data[$id] = $valutaVertalingen[$data[$id]];
	}

}



function do_R()  //Coupon rente
{
  global $fonds, $data, $mr, $rekNr, $output;

	$mr = array();
	$mr["aktie"]              = "R";
	do_algemeen();
  $mr["Rekening"]          = $rekNr;
  $mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  $mr["Bedrag"]            = $data[18];
  $mr["Valutakoers"]       = 1;
  $mr["Valuta"]            = $data[16];
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers[Koers];
  }
  
  if ($fonds["id"] < 1)  // cash rente
  {
    $mr["Omschrijving"]      = $data[25]." ".$data[26];
    
    $mr["Grootboekrekening"] 	= "RENTE";
    if($data[17] == "+")
    {
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($mr["Bedrag"]);
      $mr["Bedrag"]       			= $mr["Credit"];
    }
    else
    {

      $mr["Debet"]			        = abs($mr["Bedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= $mr["Debet"] * -1;
    }
    $controleBedrag       += abs($mr["Bedrag"]);
    
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;
  }
  else  // coupon
  {
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Fonds"]             = $fonds["Fonds"];
    
    if($data[17] == "+")
    {
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($mr["Bedrag"]);
      $mr["Bedrag"]       			= $mr["Credit"];
    }
    else
    {

      $mr["Debet"]			        = abs($mr["Bedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= $mr["Debet"] * -1;
    }
    $controleBedrag       += abs($mr["Bedrag"]);
    
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;
    
  }
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output, $rekNr;

  $mr = array();
	$mr["aktie"]              = "DV";
	do_algemeen();
  $mr["Rekening"]          = $rekNr;
  $mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  $mr["Valutakoers"]       = 1;
  $mr["Valuta"]            = $data[16];
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }


  // call 5333

  $divSpecial = false;
  if (stristr($data[26], "Netto bedrag" ))
  {
    $divSpecial = true;
    $split = explode("Netto bedrag: ", $data[26]);
    $parts = explode(" ", $split[1]);

    if ($parts[0] <> $parts[3])  // valuta's gelijk
    {
       $divSpecial = false;
    }

    $wisselkoers = 1/(str_replace(",",".",$parts[5]));

//    if (round($parts[1] * $wisselkoers,2) <> $data[18])
//    {
//      $divSpecial = false;
//    }

  }

  if ($divSpecial)
  {
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    $mr["Bedrag"]            = $parts[1];
    $mr["Valutakoers"]       = $wisselkoers;
    $mr["Valuta"]            = $parts[0];
    if($data[17] == "+")
    {
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($mr["Bedrag"]);
      $mr["Bedrag"]       			= round($mr["Credit"] * $mr["Valutakoers"],2);
    }
    else
    {
      $mr["Debet"]			        = abs($mr["Bedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= round($mr["Debet"] * $mr["Valutakoers"],2) * -1;
    }
  }
  else
  {
    $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
    $mr["Bedrag"]            = $data[18];
    if($data[17] == "+")
    {
      $mr["Debet"]        			=	0;
      $mr["Credit"]       			= abs($mr["Bedrag"]);
      $mr["Bedrag"]       			= $mr["Credit"];
    }
    else
    {
      $mr["Debet"]			        = abs($mr["Bedrag"]);
      $mr["Credit"]       			= 0;
      $mr["Bedrag"]       			= $mr["Debet"] * -1;
    }
  }

  $mr["Grootboekrekening"] = "DIV";


  $controleBedrag       += abs($mr["Bedrag"]);

  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_KST()  //kosten etc
{
 	global $data,$mr,$output, $rekNr, $transactieCodes;
  
	$mr = array();
	$mr["aktie"]           = "KST";
	do_algemeen();
	$mr["Rekening"]          = $rekNr;
	$mr["Omschrijving"]      = rclip($data[26],50);
	$mr["Valuta"]            = $data[16];
  $mr["Valutakoers"]       = 1;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }
  
  $mr["Grootboekrekening"] = "KNBA";
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';
	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = $data[18];
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  if (stristr($data[26],"beheerfee") OR
      stristr($data[26],"fee q")       ) 
  {
    $mr["Grootboekrekening"] = "BEH";
  }
  if (stristr($data[26],"bewaren") OR
      stristr($data[26],"safe custody")    )
  {
     $mr["Grootboekrekening"] = "BEW";
  }

  if($data[17] == "+")
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($mr["Bedrag"]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $controleBedrag           = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]			        = abs($mr["Bedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= $mr["Debet"] * -1;
    $controleBedrag           = $mr["Debet"];
  }
  
  $output[] = $mr;
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_BEW()  //Bewaarloon
{
  global $data,$mr,$output, $rekNr, $transactieCodes;

  $mr = array();
  $mr["aktie"]           = "BEW";
  do_algemeen();
  $mr["Rekening"]          = $rekNr;
  $mr["Omschrijving"]      = rclip($data[26],50);
  $mr["Valuta"]            = $data[16];
  $mr["Valutakoers"]       = 1;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }

  $mr["Grootboekrekening"] = "BEW";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $data[18];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  if($data[17] == "+")
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($mr["Bedrag"]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $controleBedrag           = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]			        = abs($mr["Bedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= $mr["Debet"] * -1;
    $controleBedrag           = $mr["Debet"];
  }

  $output[] = $mr;

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_VMAR()  //Bewaarloon
{
  global $data,$mr,$output, $rekNr, $transactieCodes;

  $mr = array();
  $mr["aktie"]           = "VMAR";
  do_algemeen();
  $mr["Rekening"]          = $rekNr;
  $mr["Omschrijving"]      = rclip($data[26],50);
  $mr["Valuta"]            = $data[16];
  $mr["Valutakoers"]       = 1;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }

  $mr["Grootboekrekening"] = "VMAR";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = '';
  $mr["Fondskoers"]        = '';
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $data[18];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  if($data[17] == "+")
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($mr["Bedrag"]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $controleBedrag           = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]			        = abs($mr["Bedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= $mr["Debet"] * -1;
    $controleBedrag           = $mr["Debet"];
  }

  $output[] = $mr;

  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_BEH()  //Beheersfee
{
 	global $data,$mr,$output, $rekNr, $transactieCodes;
  
	$mr = array();
	$mr["aktie"]           = "BEH";
	do_algemeen();
	$mr["Rekening"]          = $rekNr;
	$mr["Omschrijving"]      = rclip($data[26],50);
	$mr["Valuta"]            = $data[16];
  $mr["Valutakoers"]       = 1;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }
  
  $mr["Grootboekrekening"] = "BEH";
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';
	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = $data[18];
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  if($data[17] == "+")
  {
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($mr["Bedrag"]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $controleBedrag           = $mr["Bedrag"];
  }
  else
  {
    $mr["Debet"]			        = abs($mr["Bedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= $mr["Debet"] * -1;
    $controleBedrag           = $mr["Debet"];
  }
  
  $output[] = $mr;
  
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_MUT()
{

	global $data,$mr,$output, $rekNr, $transactieCodes, $afw;
  
  if (stristr($data[26],"beheerfee")    OR
      stristr($data[26],"bewaren")      OR
      stristr($data[26],"safe custody") OR
      stristr($data[26],"fee q")         )   // LET OP ook do_KST filter inbouwen
  {
    do_KST();
    return;
  }

  // call 5407
  $oms = $data[26];

  if (stristr($data[26],"Quantity") AND trim($data[11]) <> "")
  {
    $split = explode(" ", $data[26]);
    $split[0] = trim($data[11]);
    unset($split[2]);
    unset($split[4]);
    unset($split[5]);
    $test = str_replace(",",".",substr($split[6],0,strlen($split[7])));
    if ($test == $split[7])
    {
      unset($split[7]);
    }
    $oms = implode(" ", $split);
  }
  $oms = modifyDescription($oms);
	$mr = array();
	$mr["aktie"]           = "Mut.";
	do_algemeen();
	$mr["Rekening"]          = $rekNr;
	$mr["Omschrijving"]      = $oms;
	$mr["Grootboekrekening"] = "MUT";
	$mr["Valuta"]            = $data[16];
  $mr["Valutakoers"]       = 1;
  if ($mr["Valuta"] <> "EUR")
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC ";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    $mr["Valutakoers"] = $laatsteKoers["Koers"];
  }

	$mr["Fonds"]             = "";
	$mr["Aantal"]            = '';
	$mr["Fondskoers"]        = '';

	$mr["Debet"]             = 0;
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = $data[18];
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

  if($data[17] == "+")
  {
    $mr["Grootboekrekening"] 	= "STORT";
    $mr["Debet"]        			=	0;
    $mr["Credit"]       			= abs($mr["Bedrag"]);
    $mr["Bedrag"]       			= $mr["Credit"];
    $mr = $afw->reWrite("STORT",$mr);
  }
  else
  {

    if (stristr($mr["Omschrijving"],"BEWAARVERGOEDING"))
    {
      $mr["Grootboekrekening"] 	= "BEW";
    }
    else
    {
      $mr["Grootboekrekening"] 	= "ONTTR";
    }

    $mr["Debet"]			        = abs($mr["Bedrag"]);
    $mr["Credit"]       			= 0;
    $mr["Bedrag"]       			= $mr["Debet"] * -1;
    $mr = $afw->reWrite("ONTTR",$mr);
  }
  $controleBedrag += abs($mr["Bedrag"]);

  $output[] = $mr;
  addMeldarray($controleBedrag, $mr["regelnr"], $mr["Rekening"], $data[18]);
  
}

function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>