<?
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/03/08 08:18:40 $
 		File Versie					: $Revision: 1.4 $

 		$Log: kasbankv2_functies.php,v $
 		Revision 1.4  2019/03/08 08:18:40  cvs
 		call 7620
 		
 		Revision 1.3  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2017/05/10 15:13:29  cvs
 		call 5872
 		
 		Revision 1.1  2017/04/03 13:01:07  cvs
 		call 5406
 		
 		Revision 1.2  2016/10/21 13:52:19  cvs
 		call 5346
 		
 		Revision 1.1  2014/11/05 12:51:52  cvs
 		dbs 2751
 		
 	

*/

function useAltCode($portefeuille)
{
  global $transActieRec;
//  debug($transActieRec,$portefeuille);
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
//    debug($portefeuilleArray, $portefeuille);
    if (in_array($portefeuille, $portefeuilleArray))  // komt portefeuille voor in array
    {
      return true;
    }
  }
  return false;
}


function getFondsKoers($fonds, $datum)
{
  $db = new DB();
  $query = "SELECT * FROM Fondskoersen WHERE Fonds = '".$fonds."' AND Datum <= '".$datum."' ORDER BY Datum DESC";

  if ($out = $db->lookupRecordByQuery($query))
  {
    return $out["Koers"];
  }
  else
  {
    return false;
  }
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

function decodeCur($value, $dec=2)
{
  $div = pow(10,$dec);
  return round($value/$div,$dec);
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
1:  16  1   16  message identification ccyymmdd + nr
2:  16  17  32  message refers to
3:  5   33  37  page number
4:  2   38  39  continuation indicator
5:  34  40  73  deposit account nr Kas Bank deposit account
6:  105 74  178 nar deposit account nr 3 * 35
7:  8   179 186 beginning of period Ccyymmdd
8:  8   187 194 end of period Ccyymmdd
9:  8   195 202 creation date Ccyymmdd
10: 4   203 206 type of securities code ISIN or Kas Bank code
11: 12  207 218 securities code
12: 35  219 253 description of securities
13: 1   254 254 securities type A / O / E
14: 7   255 261 interest rate (2.5) 5 decimals
15: 2   262 263 stock exchange country code
16: 1   264 264 quotation P / E
17: 3   265 267 securities currency code ISO format
18: 7   268 274 type of securities
19: 3   275 277 balance type
20: 2   278 279 balance code
21: 6   280 285 filler Spaces
22: 4   286 289 correspondent nr
23: 2   290 291 circuit code CB or spaces
24: 8   292 299 opening balance date Ccyymmdd
25: 1   300 300 sign opening balance (+ or -)
26: 14  301 314 opening balance
27: 2   315 316 number of decimal places
28: 1   317 317 sign movement (+ or -)
29: 14  318 331 movement balance
30: 2   332 333 number of decimal places
31: 2   334 335 transaction type
32: 16  336 351 your reference
33: 16  352 367 KAS BANK reference
34: 8   368 375 (contractual) settlement date Ccyymmdd
35: 1   376 376 counterparty's debit/credit code (+ or -)
36: 34  377 410 counterparty's deposit account nr
37: 4   411 414 type of code
38: 11  415 425 counterparty code
39: 105 426 530 nar counterparty
40: 3   531 533 securities price currency code
41: 11  534 544 securities price (6.5) 5 decimals
42: 3   545 547 number of days of accrued interest
43: 1   548 548 sign accrued interest (+ or -)
44: 3   549 551 currency code accrued interest ISO format
45: 15  552 566 amount accrued interest (13.2) 2 decimals
46: 1   567 567 sign amount (+ or -)
47: 3   568 570 amount currency code ISO format
48: 15  571 585 amount (13.2) 2 decimals
49: 1   586 586 sign closing balance (+ or -)
50: 14  587 600 Closing balance
51: 2   601 602 Number of decimal places
52: 210 603 812 Additional information 6 * 35
53: 35  813 847 Counterparty
54: 36  848 883 Transaction description
55: 157 884 1040 Filler Spaces
*/
function convertFixedLine($rawData,$debug=false)
{
  
    $data[1]  = textPart($rawData,1,16);                    // transid
    $data[2]  = textPart($rawData,17,32);
    $data[3]  = textPart($rawData,33,37);
    $data[4]  = textPart($rawData,38,39);
    $data[5]  = ontnullen(textPart($rawData,40,73));                   // portefeuille
    $data[6]  = textPart($rawData,74,178);
    $data[7]  = decodeDate(textPart($rawData,179,186));     // boekdatum
    $data[8]  = decodeDate(textPart($rawData,187,194));
    $data[9]  = decodeDate(textPart($rawData,195,202));
    $data[10] = textPart($rawData,203,206);
    $data[11] = textPart($rawData,207,218);                 // ISIN
    $data[12] = textPart($rawData,219,253);
    $data[13] = textPart($rawData,254,254);                 // fondssoort A=aandeel O=obligatie
    $data[14] = decodeCur(textPart($rawData,255,261),5);
    $data[15] = textPart($rawData,262,263);
    $data[16] = textPart($rawData,264,264);                 // koerseenheid   P= % E=stuks
    $data[17] = textPart($rawData,265,267);                 // fondsvaluta
    $data[18] = textPart($rawData,268,274);                 // gedetaileerd fondssoort
    $data[19] = textPart($rawData,275,277);                 // balance type
    $data[20] = textPart($rawData,278,279);
    $data[21] = textPart($rawData,280,285);
    $data[22] = textPart($rawData,286,289);
    $data[23] = textPart($rawData,290,291);
    $data[24] = decodeDate(textPart($rawData,292,299));
    $data[25] = textPart($rawData,300,300);
    $data[26] = textPart($rawData,301,314);
    $data[27] = textPart($rawData,315,316);
    $data[28] = textPart($rawData,317,317);                 // richting mutatie (dep/licht)
    $data[29] = textPart($rawData,318,331);                 // aantal stuks
    $data[30] = textPart($rawData,332,333);                 // aantal decimalen
    $data[31] = textPart($rawData,334,335);                 // transactietype
    $data[32] = textPart($rawData,336,351);                 // ordernummer
    $data[33] = textPart($rawData,352,367);                 // kasbank referentie
    $data[34] = decodeDate(textPart($rawData,368,375));     // valuta data
    $data[35] = textPart($rawData,376,376);
    $data[36] = textPart($rawData,377,410);
    $data[37] = textPart($rawData,411,414);
    $data[38] = textPart($rawData,415,425);
    $data[39] = textPart($rawData,426,530);
    $data[40] = textPart($rawData,531,533);                  // Valuta van de koers (nog geen vulling gezien)
    $data[41] = decodeCur(textPart($rawData,534,544),5);     // Koers (nog geen vulling gezien)
    $data[42] = textPart($rawData,545,547);
    $data[43] = textPart($rawData,548,548);
    $data[44] = textPart($rawData,549,551);
    $data[45] = decodeCur(textPart($rawData,552,566));
    $data[46] = textPart($rawData,567,567);                  // richting waarde
    $data[47] = textPart($rawData,568,570);                  // valuta waarde
    $data[48] = decodeCur(textPart($rawData,571,585));       // waarde
    $data[49] = textPart($rawData,586,586);
    $data[50] = textPart($rawData,587,600);
    $data[51] = textPart($rawData,601,602);
    $data[52] = textPart($rawData,603,812);                  // omschrijving
    $data[53] = textPart($rawData,813,847);
    $data[54] = textPart($rawData,848,883);
    $data[55] = textPart($rawData,884,1040);

    //$data[37] = decodeCur(textPart($rawData,892,908));
    //$data[38] = textPart($rawData,909,920);
    //$data[39] = deSpace(textPart($rawData,921,1040));
    
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

function _valutakoersEff()
{
	global  $mr;
  $db = new DB();
	$query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];

}

function getRekeningNr($port,$valuta)
{
  $DB = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie= 0 AND Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'SNS' ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
    $output = $record["Rekening"];
  else
  {
    // rekeningnr bijzoeken via portnr+mem methode (tnt 30-1-2013)
    $query = "SELECT * FROM Rekeningen WHERE consolidatie= 0 AND Rekening = '".trim($port)."MEM' ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();

    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie= 0 AND Portefeuille = '".$tempRec["Portefeuille"]."' AND Memoriaal = 0 AND Valuta='$valuta' AND Inactief = 0 AND Depotbank = 'SNS' ";
    $DB->SQL($query);
    $tempRec = $DB->lookupRecord();
    $output = $tempRec["Rekening"];
  }  
  return $output;
}

function do_algemeenEFF()
{
	global $mr, $row, $fonds, $data, $_file, $valutaLookup, $controleBedrag;
  $db = new DB();
	$mr["Boekdatum"]         = $data[7];
	$mr["settlementDatum"]   = $data[34];
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
  $mr["aktie"]             = $data[31];
  $mr["Rekening"]          = trim(ontnullen($data[5]))."MEM";

  unset($fonds);
  if ($data[11])
  {
    $ISIN = $data[11];
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$ISIN."' AND Valuta = '".$data[17]."'";
    $fonds = $db->lookupRecordByQuery($query);
  }
  $mr["fondsRec"] = $fonds;
  $controleBedrag        = 0;

}

function do_effStukmut()
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"] = "stukmut";
  do_algemeenEFF();

  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"] = $mr["fondsRec"]["Valuta"];
  $mr["Valutakoers"] = _valutakoersEff();
  $mr["Fonds"] = $mr["fondsRec"]["Fonds"];
  $mr["Fondskoers"] = getFondsKoers($mr["fondsRec"]["Fonds"], $mr["Boekdatum"] );


  $mr["Verwerkt"] = 0;
  $mr["Memoriaalboeking"] = 1;
  if ($data[28] == "+")
  {
    $mr["Omschrijving"] = "Deponering " .$mr["fondsRec"]["Omschrijving"];
    $mr["Aantal"] = $data[29];
    $mr["Transactietype"] = "D";
    $mr["Debet"] = abs($mr["Aantal"] * $mr["Fondskoers"] * $mr["fondsRec"]["Fondseenheid"]);
    $mr["Credit"] = 0;
    $mr["Bedrag"] = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
    $grootboek = "STORT";
  }
  else
  {
    $mr["Omschrijving"] = "Lichting " . $mr["fondsRec"]["Omschrijving"];
    $mr["Aantal"] = -1 * $data[29];
    $mr["Transactietype"] = "L";
    $mr["Debet"] = 0;
    $mr["Credit"] = abs($mr["Fondskoers"] * $mr["Aantal"] * $mr["fondsRec"]["Fondseenheid"]);
    $mr["Bedrag"] = $mr["Credit"] * $mr["Valutakoers"];
    $grootboek = "ONTTR";
  }

  unset($mr["fondsRec"]);

  $output[] = $mr;

  $mr["Grootboekrekening"] = $grootboek;
  if ($mr["Valuta"] == "EUR")
  {
    $mr["Valutakoers"] = 1;
  }
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($grootboek == "STORT")
  {
    $mr["Credit"]            = abs($mr["Debet"]);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = -1 * $mr["Bedrag"];
  }
  else
  {

    $mr["Debet"]            = abs($mr["Credit"]);
    $mr["Credit"]           = 0;
    $mr["Bedrag"]           = -1 * $mr["Bedrag"];
  }

  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

}



function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}

?>