<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/08 12:21:48 $
 		File Versie					: $Revision: 1.17 $

 		$Log: ubp_functies.php,v $
 		Revision 1.17  2020/06/08 12:21:48  cvs
 		call 8668
 		
 		Revision 1.16  2020/05/18 08:29:41  cvs
 		zonder call
 		
 		Revision 1.15  2020/02/11 14:42:07  cvs
 		call 5295
 		
 		Revision 1.14  2020/01/22 14:17:00  cvs
 		call 8196
 		
 		Revision 1.13  2019/10/25 15:12:21  cvs
 		call 8196
 		
 		Revision 1.12  2019/09/25 08:56:41  cvs
 		call 8132
 		
 		Revision 1.11  2019/04/15 14:33:07  cvs
 		call 7716
 		
 		Revision 1.10  2019/01/21 12:59:04  cvs
 		call 7505
 		
 		Revision 1.9  2018/10/15 12:50:58  cvs
 		call 7160
 		
 		Revision 1.8  2018/10/08 14:49:20  cvs
 		call 7160
 		
 		Revision 1.7  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/09/20 06:17:48  cvs
 		megaupdate 2722
 		
 		Revision 1.5  2017/05/15 14:34:10  cvs
 		call 5883
 		
 		Revision 1.4  2017/05/03 12:54:10  cvs
 		fxkruis aanpassing forx melding
 		
 		Revision 1.3  2017/04/03 13:37:43  cvs
 		call 5639
 		
 		Revision 1.2  2017/03/09 08:02:40  cvs
 		call 5639
 		
 		Revision 1.1  2016/12/05 12:46:35  cvs
 		call 5294
 		

*/

/*
  0 => 'COMMON_NBR_CO',
  1 => 'COMMON_NBR_CLIENT',
  2 => 'COMMON_NBR_ACCT',
  3 => 'COMMON_CODE_FAM_OPE',
  4 => 'COMMON_CODE_TYPE_OPE',
  5 => 'CODE_TYPE_POS',
  6 => 'CODE_NAT_OPE',
  7 => 'CODE_TYPE_DEP',
  8 => 'NBR_SECUR_TK',
  9 => 'NBR_SUB_SECUR',
  10 => 'NBR_PORTFOLIO',
  11 => 'NBR_DEPTRY',
  12 => 'CODE_INSTRUMENT',
  13 => 'NBR_REF',
  14 => 'MVT_QUANTITY',
  15 => 'KEY_OPE_ACTG',
  16 => 'KEY_HLDG',
  17 => 'KEY_OPE_DET',
  18 => 'KEY_OPE_CLIENT',
  19 => 'DATE_REF',
  20 => 'NAME_ACCTG_ENT_LG',
  21 => 'COMMON_DATE_ACCOUNTING',
  22 => 'COMMON_DATE_VALUE',
  23 => 'COMMON_DATE_TRADING',
  24 => 'COMMON_DATE_OPERATION',
  25 => 'COMMON_CODE_RVRSL',
  26 => 'FX_CODE_CUR_NET',
  27 => 'FX_NBR_DEAL',
  28 => 'FX_DATE_TRADING',
  29 => 'FX_DATE_OPERATION',
  30 => 'FX_DATE_VALUE',
  31 => 'FX_DATE_VALUE_PIVOT',
  32 => 'FX_DATE_MATR_DEAL',
  33 => 'FX_RATE_EXCH_MKT',
  34 => 'FX_RATE_OPERATION',
  35 => 'FX_RATE_SPOT_FX',
  36 => 'FX_RATE_DEAL',
  37 => 'FX_AMT_NET',
  38 => 'SX_DATE_VALUE',
  39 => 'SX_TYPE_OPE_DESC',
  40 => 'SX_ID_SECUR_TK',
  41 => 'SX_ID_SUB_SECUR',
  42 => 'SX_NAME_SECUR',
  43 => 'SX_ISINNUMBER',
  44 => 'SX_QTY_TOT_TS_CLIENT',
  45 => 'SX_NBR_OPER_SX',
  46 => 'SX_NAME_ACCTG_ENT_LG',
  47 => 'SX_DEVISE_OPE',
  48 => 'SX_NET_AMOUNT_OPE',
  49 => 'SX_NET_AMOUNT',
  50 => 'SX_DEVISE_DECOMPTE',
  51 => 'SX_GROSS_AMOUNT',
  52 => 'SX_GROSS_CUR',
  53 => 'SX_COMMISSION_A',
  54 => 'SX_COMMISSION_B',
  55 => 'SX_COMMISSION_ETR',
  56 => 'SX_FEDERAL_STAMP_DUTY',
  57 => 'SX_TURNOVER_FEES',
  58 => 'SX_REDEMPTION_FEES',
  59 => 'SX_EU_RETENTION_TAX',
  60 => 'SX_FTT',
  61 => 'SX_INTERESTS',
  62 => 'TR_DATE_OPERATION',
  63 => 'TR_DATE_TRADING',
  64 => 'TR_NBR_PORTFOLIO',
  65 => 'TR_DATE_VALUE',
  66 => 'TR_NBR_OPER_TS_SX',
  67 => 'TR_NBR_SUB_SECUR',
  67 => 'TR_NBR_SUB_SECUR',
  68 => 'TR_NAME_SECUR',
  69 => 'TR_QTY_SECUR_13_I',
  70 => 'TR_CODE_CUR_PRICE',
  71 => 'TR_PRICE_SECUR',
  72 => 'TR_CODE_ACT_REGTD',
  73 => 'TR_NBR_OPER_SD',
  74 => 'TR_NBR_SECUR_TK',
  75 => 'TR_ISINNUMBER',
  76 => 'CPN_NBR_OPER_CPN',
  77 => 'CPN_NBR_CPN_ANN',
  78 => 'CPN_DATE_MATR_COUPON',
  79 => 'CPN_DATE_DIVIDENT_PMT',
  80 => 'CPN_DATE_VALUE',
  81 => 'CPN_NBR_DAYS',
  82 => 'CPN_QTY_TOT_DEPTRY',
  83 => 'CPN_DATE_TRADING',
  84 => 'CPN_DATE_OPERATION',
  85 => 'CPN_AMT_COMM_CLI',
  86 => 'CPN_ID_SECUR_TK',
  87 => 'CPN_ID_SUB_SECUR',
  88 => 'CPN_DATE_EX',
  89 => 'CPN_CODE_TYPE_ANN',
  90 => 'CPN_CODE_TYPE_DIV',
  91 => 'CPN_NAME_SECUR',
  92 => 'CPN_DATE_PMT_CPN',
  93 => 'CPN_CODE_CUR_PMT_CPN',
  94 => 'CPN_AMT_CPN',
  95 => 'CPN_CODE_TYPE_AMT_CPN',
  96 => 'CPN_CODE_TYPE_AMT_CPN_TXBLE',
  97 => 'CPN_DATE_RCPN_ANN',
  98 => 'CPN_CODE_STAT_MANDATORY',
  99 => 'CPN_NBR_PROCESS_ANN',
  100 => 'CPN_GROSS_AMT_CPN',
  101 => 'CPN_TAX_AMT',
  102 => 'CPN_ISINNUMBER',
  103 => 'CPN_AMT_AVNPP',
  104 => 'CPN_AMT_AVPP',
  105 => 'CRP_CODE_STAT_CLI_CORP',
  106 => 'CRP_AMT_CHRG_SX',
  107 => 'CRP_AMT_CODE_CHRG_SX',
  108 => 'CRP_AMT_CODE_COMM_SX',
  109 => 'CRP_AMT_CODE_FED_STMP',
  110 => 'CRP_AMT_CODE_TAX_SX',
  111 => 'CRP_AMT_COMM_SX',
  112 => 'CRP_AMT_DEAL_STKEX_NET',
  113 => 'CRP_AMT_FED_STMP_SX',
  114 => 'CRP_AMT_NET_CUR_TS_SX',
  115 => 'CRP_AMT_RETENT_EUTX',
  116 => 'CRP_AMT_TAX_SX',
  117 => 'CRP_AMT_WGTNG',
  118 => 'CRP_DATE_OPERATION',
  119 => 'CRP_DATE_TRADING',
  120 => 'CRP_DATE_VALUE',
  121 => 'CRP_NBR_OPER_TS_SX',
  122 => 'CRP_NBR_SUB_SECUR',
  123 => 'CRP_NBR_SUB_SECUR_NEW',
  124 => 'CRP_PRICE_SECUR_OLD',
  125 => 'CRP_PRICE_SECUR_NEW',
  126 => 'CRP_QTY_SECUR_OLD',
  127 => 'CRP_QTY_SECUR_NEW',
  128 => 'CRP_CODE_ISO_CUR',
  129 => 'CRP_ISINNUMBER',
  130 => 'CRP_NBR_SECUR_TK',
  131 => 'CRP_ISINNUMBER_NEW',
  132 => 'CRP_NBR_SECUR_TK_NEW',
  133 => 'AMO_CODE_CUR',
  134 => 'AMO_AMT_OPER_ACCT_CUR',
  135 => 'AMO_CODE_DR_CR',
  136 => 'AMO_CODE_TYPE_MVT_LN',
  137 => 'AMO_DATE_VALUE',
  138 => 'AMO_NAME_ACCTG_ENT_LG',
  139 => 'AMO_NAME_BOOK_ENT',
  140 => 'AMO_NAME_OPER_ACCTG_ENT',
  141 => 'RATE_OPERATION',
  142 => 'PRICE_SECUR',
  143 => 'TPA_AMT_OPE_GROSS',
  144 => 'TPA_AMT_COMM_FEE',
  145 => 'TPA_AMT_TVA_TAX',
  146 => 'TPA_AMT_OPE_NET',
  147 => 'TPA_CODE_INSTR_CUR',
  148 => 'TPA_CODE_DR_CR',
 *
 */

function decodeDate($date)
{
  return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
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
    bankTransactieId = '".substr($mr["bankTransactieId"],0,25)."' AND 
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


function UBP_getfonds($BankFondscode="",$isin="", $valuta="" )
{
  global $fonds, $meldArray, $fondsLookupResults, $mr;
  $db = new DB();
  $fondsLookupResults = array();

  // bankfonds code
  $fondsNotFound = true;


  if ($BankFondscode <> "")
  {
    $fondsLookupResults["bankcode"] = $BankFondscode;
    $query = "SELECT * FROM Fondsen WHERE UBPcode = '" .$BankFondscode . "' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      $fondsNotFound = false;
    }
  }

  if ($fondsNotFound)        // isin/val
  {
    if ($isin <> "" AND $valuta <> "")
    {
      $fondsLookupResults["fonds"] = $isin;
      $fondsLookupResults["valutra"] = $valuta;
      $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$isin."' AND Valuta = '".$valuta."'  ";

      if (!$fonds = $db->lookupRecordByQuery($query))
      {
        $meldArray[] = $mr["regelnr"].": Fonds niet gevonden ".$isin."/".$valuta;
        $fondsLookupResults["notFound"] = true;
      }
      else
      {
        $fondsNotFound = false;
      }
    }
    else
    {
      $meldArray[] = $mr["regelnr"].": Fonds niet gevonden via bankcode:  ".$BankFondscode;
    }
  }
  return !$fondsNotFound;

}

function UBP_getfondsOld($data, $BankFondscode="")
{
  global $fonds, $fonds2, $meldArray, $fondsLookupResults;

  $db = new DB();
  $fondsLookupResults = array();

  // bankfonds code
  $fondsNotFound = true;

  if ($BankFondscode <> "")
  {
    $query = "SELECT * FROM Fondsen WHERE UBPcode = '".$BankFondscode."' ";

    if ($fonds = $db->lookupRecordByQuery($query))
    {
      $fondsNotFound = false;
    }
  }

  if ($fondsNotFound)        // isin/val
  {


    $combiArray = array(
      44   => 48,
      76   => 71,
      103  => 94,
      130  => 129,
    );

    $fonds  = array();
    $fonds2 = array();
    $_ISIN  = "";
    $_ISIN2 = "";
    $_VAL   = "";
    $_VAL2  = "";

    foreach( $combiArray as $isin=>$val)
    {

      if (trim($data[$isin]) <> "")
      {
        $_ISIN = trim($data[$isin]);
        $_VAL  = trim($data[$val]);

        if ($isin == 130)
        {
          $_ISIN2 = trim($data[132]);
          $_VAL2  = trim($data[$val]);
        }
        break;
      }
    }

    if ($fondsNotFound AND $_ISIN <> "" AND $_VAL <> "")
    {
      $fondsLookupResults["fonds"] = $_ISIN;
      $fondsLookupResults["valutra"] = $_VAL;
      $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$_ISIN."' AND Valuta = '".$_VAL."'  ";

      if (!$fonds = $db->lookupRecordByQuery($query))
      {
        $meldArray[] = "Fonds niet gevonden ".$_ISIN."/".$_VAL;
        $fondsLookupResults["notFound"] = true;
      }
    }

    if ($_ISIN == "" OR $_VAL == "")
    {
      $fondsLookupResults["noCodes"] = true;
    }
    else
    {
      if ($_ISIN2 <> "")
      {
        $query = "SELECT * FROM Fondsen WHERE ISINcode = '".$_ISIN2."' AND Valuta = '".$_VAL2."'  ";
        if (!$fonds2 = $db->lookupRecordByQuery($query))
        {
          $meldArray[] = "Fonds niet gevonden ".$_ISIN2."/".$_VAL2;
        }
      }
    }



    return !$fondsNotFound;
  }
}

function ontnullen($in)
{
  while (substr($in,0,1) == "0")
  {
    $in = substr($in,1);
  }
  return $in;
}

function trimRecord($record)
{
  foreach($record as $k=>$v)
  {
    $out[$k] = trim($v);
  }
  return $out;
}

function getRekeningMem($rekeningDeel)
{
  $db = new DB();
  $depot = "UBP";
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '" . ontnullen($rekeningDeel)."MEM' AND `Depotbank` = '{$depot}'";
debug($query);
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"];
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '" . ontnullen($rekeningDeel)."MEM' AND  `Depotbank` = '{$depot}' ";
    debug($query);
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

function getRekening($data, $val, $rekeningDeel="",$outputRec=false)
{
  global $mr, $tmpRekNr;
  $tmpRekNr = "";
  $rec = "";
  if ($rekeningDeel == "")
  {
    $rekeningDeel = (trim($data[3]) <> "")?trim($data[3]):trim($data[14]);
    $rekeningNr = ontnullen($rekeningDeel).$val;
  }
  else
  {
    $rekeningNr = ontnullen($rekeningDeel).$val;
  }

  $tmpRekNr = $rekeningNr;

  $IbanNotFound = false;
	$depot="UBP";
  $db = new DB();

	if ($rekeningNr <> "")
	{
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '{$rekeningNr}' AND `Depotbank` = '{$depot}'  ";
		if ($rec = $db->lookupRecordByQuery($query) )
		{
			if ($outputRec)
      {
        return $rec;
      }
      else
      {
        return $rec["Rekening"];
      }
		}
		else
		{
      $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '{$rekeningNr}' AND  `Depotbank` = '{$depot}' ";
      if ($rec = $db->lookupRecordByQuery($query))
      {
        if ($outputRec)
        {
          return $rec;
        }
        else
        {
          return $rekeningNr;
        }
      }
      else
      {
        return false;
      }
		}
	}
	return false;
}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "UBP|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}


function checkUnknownnModules($data, $knowns)
{
  global $meldArray;

  foreach ($data as $k=>$v)
  {
    $tac = trim($v[3])."-".trim($v[4]);
    if (!in_array($k, $knowns))
    {
      $meldArray[] = $v["row"].": transactie: $tac module $k onbekend";
    }
  }
}

function checkStorno($data)
{
  global $meldArray;
  if ($data[26] == 1)
  {
    $meldArray[] = $data["row"].": Storno overgeslagen";
    return true;
  }
  return false;
}

function _debetbedrag2($rekValuta, $fondsValuta)
{
  global $data, $mr;


  if ($rekValuta == $fondsValuta)
  {
    return -1 * $mr["Debet"];
  }
  else
  {
    return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
  }

}


function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

	return -1 * ($mr["Debet"] );
}

function _creditbedrag2($rekValuta, $fondsValuta)
{
  global $data, $mr;
  if ($rekValuta == $fondsValuta)
  {
    return $mr["Credit"];
  }
  else
  {
    return $mr["Credit"] * $mr["Valutakoers"];
  }

}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[9];
  return $mr["Credit"] ;
}


function _valutakoers()
{

	global $mr;
  $db = new DB();
  $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
  $laatsteKoers = $db->lookupRecordByQuery($query);
  return $laatsteKoers["Koers"];

}

function _valutakoers2($stex)
{
  global $fonds, $data, $mr, $valutaLookup, $DB;
  $fondsValuta = $mr["Valuta"];
  $rekValuta   = $stex[51];

  if ($rekValuta == "EUR" AND $fondsValuta != "EUR")
  {
     return round(1/$stex[142],7);
  }
  else
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
}

function _valutakoersDIV($acct)
{
  global $fonds, $data, $mr, $valutaLookup, $DB;
  $fondsValuta = $mr["Valuta"];
  $rekValuta   = $acct[94];

  if ($rekValuta == "EUR" AND $fondsValuta != "EUR")
  {
    return round(1/$acct[142],7);
  }
  else
  {
    $db = new DB();
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mr["Valuta"]."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    $laatsteKoers = $db->lookupRecordByQuery($query);
    return $laatsteKoers["Koers"];
  }
}

function offsetArray($data)  // index de array vanaf 1 ipv 0
{
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
  return $data;
}

function do_algemeen($data)
{
	global $mr, $row, $volgnr, $_file;

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $data["row"];
	$mr["bankTransactieId"]  = $data[18];
  $mr["Portefeuille"]          = $data[2];


}  

function checkControleBedrag($controleBedrag,$notabedrag)
{
  global $meldArray, $data, $mr;
  
  $controleBedrag = round($controleBedrag,2);
  $notabedrag     = round($notabedrag,2);
  $rekening       = trim($mr["Rekening"]);
  if ( $controleBedrag <> $notabedrag ) 
    $meldArray[] = $mr["regelnr"].": ".$rekening." --> notabedrag sluit niet aan:  ".$notabedrag."(bestand) :: ".$controleBedrag."(controle) ::  ".round($notabedrag - $controleBedrag,2)."(verschil)";
  else
    $meldArray[] = $mr["regelnr"].": ".$rekening." --> notabedrag sluit aan ";
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  
  global $fonds, $data, $mr, $output,$meldArray, $tmpRekNr;


  if (count($data["ACCT"][0]) == 0 OR count($data["STEX"][0]) == 0)
  {
    foreach ($data as $k=>$v){}
    echo "<li>FOUT: regel ".$v[0]["row"]." incompleet koppel (".ontnullen($v[0][1])."/".$v[0][2]."/".$v[0][3]."-".$v[0][4].")";
    return false;
  }
  $acct = offsetArray($data["ACCT"][0]);
  $stex = offsetArray($data["STEX"][0]);

  $acct = trimRecord($acct);
  $stex = trimRecord($stex);

  checkUnknownnModules($data, array("ACCT","STEX"));
  if (checkStorno($stex))
  {
    return;
  }
  //debug($acct);
  //debug($stex);
  UBP_getfonds($acct[41], $acct[44], $acct[48]);


  $controleBedrag = 0;
	$mr = array();
	$mr["aktie"]             = "A";
  //debug($fonds);
	do_algemeen($acct);

  $mr["Boekdatum"]         = decodeDate($stex[64]);
  $mr["settlementDatum"]   = decodeDate($stex[66]);
  $mr["Valuta"]            = trim($stex[48]);
  if ($reknr = getRekening($stex, $stex[51]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  //debug($fonds);
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";

	$mr["Valutakoers"]       = _valutakoers2($stex);
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $stex[15];
	$mr["Fondskoers"]        = $stex[143];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($stex[55] + $stex[54]);
	$mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;
////
  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $bedrag = ($stex[56] + $stex[58] + $stex[59] + $stex[60] + $stex[61]) * -1;

  if ($bedrag < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
////
  $mr["Grootboekrekening"] = "TOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $bedrag = ($stex[57]) * -1;

  if ($bedrag < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($stex[62]);
  $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  _creditbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


  checkControleBedrag($controleBedrag,$acct[50]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray, $tmpRekNr;
  if (count($data["ACCT"][0]) == 0 OR count($data["STEX"][0]) == 0)
  {
    foreach ($data as $k=>$v){}
    echo "<li>FOUT: regel ".$v[0]["row"]." incompleet koppel (".ontnullen($v[0][1])."/".$v[0][2]."/".$v[0][3]."-".$v[0][4].")";
    return false;
  }
  $acct = offsetArray($data["ACCT"][0]);
  $stex = offsetArray($data["STEX"][0]);
  $acct = trimRecord($acct);
  $stex = trimRecord($stex);

  checkUnknownnModules($data, array("ACCT","STEX"));
  if (checkStorno($stex))
  {
    return;
  }
  //debug($acct);
  //debug($stex);

  UBP_getfonds($acct[41], $acct[44], $acct[48]);

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "V";
  //debug($fonds);
  do_algemeen($acct);

  $mr["Boekdatum"]         = decodeDate($stex[64]);
  $mr["settlementDatum"]   = decodeDate($stex[66]);
  $mr["Valuta"]            = $stex[48];
  if ($reknr = getRekening($stex, $stex[51]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  //debug($fonds);
  $mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";

  $mr["Valutakoers"]       = _valutakoers2($stex);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $stex[15];
  $mr["Fondskoers"]        = $stex[143];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
  _debetbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "V";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "KOST";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($stex[55] + $stex[54]);
  $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


  $mr["Grootboekrekening"] = "KOBU";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $bedrag = ($stex[56] +  $stex[58] + $stex[59] + $stex[60] + $stex[61]) * -1;

  if ($bedrag < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
    _creditbedrag2($stex[51], $mr["Valuta"]);
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
    _debetbedrag2($stex[51], $mr["Valuta"]);
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  $mr["Grootboekrekening"] = "TOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  $bedrag = ($stex[57]) * -1;

  if ($bedrag < 0)
  {
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($bedrag);
    $mr["Bedrag"]            = _debetbedrag2($stex[51], $mr["Valuta"]);
    _creditbedrag2($stex[51], $mr["Valuta"]);
  }
  else
  {
    $mr["Credit"]            = abs($bedrag);
    $mr["Debet"]             = 0;
    $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($stex[62]);
  $mr["Bedrag"]            = _creditbedrag2($stex[51], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


  checkControleBedrag($controleBedrag,$acct[50]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT()  // stukken mutatie
{

  global $fonds, $fonds2, $data, $mr, $output,$meldArray, $tmpRekNr;
  $stex = offsetArray($data["STEX"][0]);

  $stex = trimRecord($stex);
//  debug($stex);

  checkUnknownnModules($data, array("STEX"));
  if (checkStorno($stex))
  {
    return;
  }
  //debug($acct);
  //debug($stex);
  do_algemeen($stex);
  UBP_getfonds($stex[9]);

  if ($fonds2["Fonds"] <> "")
  {
    $fonds = $fonds2;
  }

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "D";
  //debug($fonds);

  $mr["Valuta"]            = $fonds["Valuta"];
  if ($reknr = getRekeningMem($stex[2]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $stex["regelnr"].": Rekeningnr (".ontnullen($stex[2])."MEM) niet gevonden";
  }



  $mr["Boekdatum"]         = decodeDate($stex[25]);
  $mr["settlementDatum"]   = decodeDate($stex[23]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Grootboekrekening"] = "FONDS";

  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $stex[15];
  $mr["Fondskoers"]        = $stex[72];
  if ($stex[15] < 0)
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  }
  else
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];;
  }

  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Fonds"]             = "";
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr[Transactietype]    = "";
  if ($stex[15] < 0)
  {
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = -1* ($mr["Debet"] *  $mr["Valutakoers"]);
  }
  else
  {
    $mr["Grootboekrekening"] = "STORT";
    $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
    $mr["Bedrag"]            = ($mr["Credit"] *  $mr["Valutakoers"]);
  }
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
//  debug($mr, "sub");
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
//    debug($mr, "sub");
  }

  //checkControleBedrag($controleBedrag,$stex[50]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STUKMUT0()  // stukken mutatie moet koers 0
{

  global $fonds, $fonds2, $data, $mr, $output,$meldArray;

  if (count($data["ACCT"][0]) == 0 OR count($data["STEX"][0]) == 0)
  {
    foreach ($data as $k=>$v){}
    echo "<li>FOUT: regel ".$v[0]["row"]." incompleet koppel (".ontnullen($v[0][1])."/".$v[0][2]."/".$v[0][3]."-".$v[0][4].")";
    return false;
  }
  $acct = offsetArray($data["ACCT"][0]);
  $stex = offsetArray($data["STEX"][0]);

  $acct = trimRecord($acct);
  $stex = trimRecord($stex);

  checkUnknownnModules($data, array("ACCT","STEX"));
  if (checkStorno($stex))
  {
    return;
  }
//  debug($acct);
//  debug($stex);

  do_algemeen($acct);
  UBP_getfonds($stex[9]);

  if ($fonds2["Fonds"] <> "")
  {
    $fonds = $fonds2;
  }

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "D";
  //debug($fonds);

  $mr["Valuta"]            = $fonds["Valuta"];
  if ($reknr = getRekeningMem($stex[2]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $stex["regelnr"].": Rekeningnr (".ontnullen($stex[2])."MEM) niet gevonden";
  }


  $mr["Boekdatum"]         = decodeDate($stex[25]);
  $mr["settlementDatum"]   = decodeDate($stex[23]);

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  if ($stex[15] < 0)
  {
    $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  }
  else
  {
    $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  }

  $mr["Grootboekrekening"] = "FONDS";

  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $stex[15];
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;



  checkControleBedrag($controleBedrag,$acct[50]);
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RENOB()  //Rente of couponrente
{

  global $fonds, $data, $mr, $output,$meldArray, $tmpRekNr;

  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);

  checkUnknownnModules($data, array("ACCT"));
  if (checkStorno($acct))
  {
    return;
  }
  //debug($acct);
  //debug($stex);
  UBP_getfonds($acct[87],$acct[103], $acct[134]);


  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "R";
  //debug($fonds);
  do_algemeen($acct);

  $mr["Boekdatum"]         = decodeDate($acct[84]);
  $mr["settlementDatum"]   = decodeDate($acct[81]);
  $mr["Valuta"]            = $acct[134];
  if ($reknr = getRekening($acct, $acct[94]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }


  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  //debug($fonds);
  $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "RENOB";

  $mr["Valutakoers"]       = _valutakoersDIV($acct);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($acct[101]);
  $mr["Bedrag"]            = _creditbedrag2($acct[94], $mr["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
debug($mr);
  $output[] = $mr;

//  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Credit"]            = 0;
//  $mr["Debet"]             = abs($stex[55]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag       += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//    $output[] = $mr;

  checkControleBedrag($controleBedrag,$acct[15]);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_DIV()  //Contant dividend
{
  global $fonds, $data, $mr, $output,$meldArray, $tmpRekNr;


  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);

  if ($acct[26] == 1)
  {
    $meldArray[] = "Storno overgeslagen";
    return;
  }
  //debug($acct);
  //debug($stex);
  UBP_getfonds($acct[87],$acct[103], $acct[94]);


  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "D";
  //debug($fonds);
  do_algemeen($acct);

  $mr["Boekdatum"]         = decodeDate($acct[84]);
  $mr["settlementDatum"]   = decodeDate($acct[81]);

  $mr["Valuta"]            = $acct[94];
  if ($reknr = getRekening($acct, $mr["Valuta"]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
//  debug($fonds);
  $mr["Valuta"]            = $fonds["Valuta"];
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";

  $mr["Valutakoers"]       = _valutakoersDIV($acct);
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($acct[101]); // was [101](12-2016) // was [95] (08-2017)
  $mr["Bedrag"]            = _creditbedrag2($acct[94], $fonds["Valuta"]);
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = abs($acct[102]);
  $mr["Bedrag"]            = _debetbedrag2($acct[94], $fonds["Valuta"]);
  $controleBedrag         += $mr["Bedrag"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
  {
    $output[] = $mr;
  }

////  $mr["Grootboekrekening"] = "KOST";
//  $mr["Aantal"]            = 0;
//  $mr["Fondskoers"]        = 0;
//  $mr["Debet"]             = abs($stex[55]);
//  $mr["Bedrag"]            = _debetbedrag();
//  $controleBedrag       += $mr["Bedrag"];
//  $mr["Transactietype"]    = "";
//  if ($mr["Bedrag"] <> 0)
//    $output[] = $mr;

  checkControleBedrag($controleBedrag,$acct[15]);


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_GELDMUT()  //mutatie geld
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr,$zv, $afw;


  if (count($data["ACCT"][0]) < 1)
  {
      $meldArray[] = $data["FORX"][0]["row"] . ": geen ACCT bij FORX record, overgeslagen";
      return true;
  }


  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);
// debug($acct);

  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen($acct);

  $mr["Valuta"]            = ($acct[148] !="")?$acct[148]:$acct[134];
  if ($reknr = getRekening($acct, $mr["Valuta"]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }


  $mr["OmschrijvingOrg"]     = trim(trim($acct[21])." ".trim($acct[140]));

  $mr["Omschrijving"]        = $mr["OmschrijvingOrg"];

  $words = explode (" ",$mr["Omschrijving"]);
  $ucWords = array();
  foreach ($words as $item)
  {
    $ucWords[] = $item[0].strtolower(substr($item,1));
  }

  $mr["Omschrijving"]      = implode(" ", $ucWords);
  $mr["Omschrijving"]      =  $zv->reWrite($mr["Omschrijving"], $mr["Rekening"] );

  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if ($acct[144] + $acct[145] == 0)
  {
    if ($acct[15] < 0)
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($acct[15]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($acct[15]);
      $mr["Bedrag"]            = $mr["Credit"];
    }
  }
  else
  {


    if ($acct[144] < 0)
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($acct[144]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($acct[144]);
      $mr["Bedrag"]            = $mr["Credit"];
    }

    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;


    $mr["Grootboekrekening"] = "KNBA";

    $mr["Omschrijving"]      = "Transferprovisie";

    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($acct[145] < 0)
    {
      $mr["Credit"]            = abs($acct[145]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = _creditbedrag();
    }
    else
    {
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($acct[145]);
      $mr["Bedrag"]            = _debetbedrag();
    }
  }
  $controleBedrag       += $mr["Bedrag"];
  $mr["Transactietype"]    = "";

  $mr = $afw->reWrite("GELDMUT",$mr);
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  checkControleBedrag($controleBedrag,$acct[15]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_LOAN()  // Leningen
{
  global $fonds, $data, $mr, $output, $meldArray;

  $acct0 = offsetArray($data["ACCT"][0]);
  $acct1 = offsetArray($data["ACCT"][1]);
  $acct0 = trimRecord($acct0);
  $acct1 = trimRecord($acct1);
//  debug($acct0, "ACCT0");
//  debug($acct1, "ACCT1");
  $scenario = 0;

  $rek0 = getRekening($acct0, $acct0[134],$acct0[14]);
  $rek1 = getRekening($acct1, "LEN",ontnullen($acct1[2]));

  if ($rek0 AND $rek1)
  {
    $scenario = 1;
  }

  $rek2 = getRekening($acct0, "LEN",ontnullen($acct0[2]));
  $rek3 = getRekening($acct1, $acct1[134],$acct1[14]);
  if ($scenario == 0 and $rek2 and $rek3)
  {
    $scenario = 2;
  }

  switch ($scenario)
  {
    case 1:
        do_LOANsub($acct0, true, true);
        do_LOANsub($acct1, false, true);
      break;
    case 2:
        do_LOANsub($acct0, false, true);
        do_LOANsub($acct1, true, true);
      break;
    default:
      if (!$rek0 and !$rek2 and !$rek1 and !$rek3)
      {
        $meldArray[] = $acct0["row"].": Rekeningnrs ".$acct0[14]." / ".$acct1[14]." onbekend inzake lening";
      }
//debug(
//  array(
//  "acct0: ".  $acct0[14].$acct0[134],
//  "acct1: ".  $acct1[14].$acct1[134],
//    "rek0: ".$rek0,
//    "rek1: ".$rek1,
//    "rek2: ".$rek2,
//    "rek3: ".$rek3,
//  )
//);
      if ($rek0)
      {
        do_LOANsub(trimRecord($acct0), true);
        $meldArray[] = $acct1["row"].": Rekeningnr ".$acct1[2]."LEN onbekend inzake lening";
        return;
      }


      if ($rek1)
      {
        do_LOANsub(trimRecord($acct1), false);
        $meldArray[] = $acct0["row"].": Rekeningnr ".$acct0[14].$acct0[134]." onbekend inzake lening";
        return;
      }

      if ($rek2)
      {
        do_LOANsub(trimRecord($acct0), false);
        $meldArray[] = $acct1["row"].": Rekeningnr ".$acct1[14].$acct1[134]." onbekend inzake lening";
        return;
      }

      if ($rek3)
      {
        do_LOANsub(trimRecord($acct1), true);
        $meldArray[] = $acct0["row"].": Rekeningnr ".$acct0[2]."LEN onbekend inzake lening";
        return;
      }



  }



}


function do_LOANsub($acct, $geldRekening=false, $kruis=false)
{
  global $fonds, $data, $mr, $output, $meldArray;
//  debug($acct);
  $mr = array();
  $mr["aktie"]              = "LOAN";
  do_algemeen($acct);

  if ($geldRekening)
  {
    $reknr = $acct[14];
    $val = $acct[134];
  }
  else
  {
    $reknr = $acct[2];
    $val = "LEN";
  }
  $mr["Rekening"]          = ontnullen($reknr).$val;

//  if (!getRekening($acct, $val,$acct[14]))
//  {
//    $meldArray[] = $acct["row"].": Rekeningnr ".$acct[14].$val." onbekend ******";
//    return;
//  }





  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Valuta"]            = $acct[134];
  $mr["Valutakoers"]       = _valutakoers();



  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;


  if ($acct[15] < 0)
  {

    if ($geldRekening)
    {
      $mr["Omschrijving"]      = "Overboeking naar Rekening courant";
    }
    else
    {
      $mr["Omschrijving"]      = "Overboeking naar lening";
    }
    $mr["Grootboekrekening"] = $kruis?"KRUIS":"ONTTR";
    $mr["Debet"]             = abs($acct[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    if ($geldRekening)
    {
      $mr["Omschrijving"] = "Overboeking van lening";
    }
    else
    {
      $mr["Omschrijving"] = "Overboeking van Rekening courant";
    }
    $mr["Grootboekrekening"] = $kruis?"KRUIS":"STORT";
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }



  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_RENTE()  //rente
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr;

  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);


  $mr = array();
  $mr["aktie"]              = "MUT";
  do_algemeen($acct);

  $mr["Omschrijving"]      = "Rente";

  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);

  $mr["Valuta"]            = $acct[134];
  if ($reknr = getRekening($acct, $mr["Valuta"]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "RENTE";
  if ($acct[15] < 0)
  {

    $mr["Debet"]             = abs($acct[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  checkControleBedrag($controleBedrag,$acct[15]);

}
  /////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_FEES()  //
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr;

  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);
  $tcCode = $acct[4]."-".$acct[5];
//debug($acct, $tcCode);

  $mr = array();
  $mr["aktie"]              = "FEES";
  do_algemeen($acct);

  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);

  $mr["Valuta"]            = $acct[134];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  if ($reknr = getRekening($acct, $mr["Valuta"]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  switch ($tcCode)
  {
    case "CC-IN":
      $mr["Omschrijving"] = "All-in fee";
      $mr["Grootboekrekening"] = "BEW";
      break;
    case "CC-AC":
      $mr["Omschrijving"] = "Accountfee";
      $mr["Grootboekrekening"] = "KNBA";
      break;
    default:
      $mr["Omschrijving"] = "Overige bankkosten";
      $mr["Grootboekrekening"] = "KNBA";
  }



  if ($acct[15] < 0)
  {

    $mr["Debet"]             = abs($acct[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {

    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  checkControleBedrag($controleBedrag,$acct[15]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_ILO()  //Interest loans
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr;

  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);

  if ($acct[15] == 0)
  {
    $meldArray[] = $acct["row"].": ILO bedrag 0, overgeslagen";
    return true;
  }

  $mr = array();
  $mr["aktie"]              = "ILO";
  do_algemeen();


  $mr["Valuta"]            = $acct[134];

  if ($reknr = getRekening($acct, $mr["Valuta"]) )
  {
    $mr["Rekening"] =$reknr;
  }
  else
  {
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";

  }

  $mr["Omschrijving"]    = "Rente betaling lening";
  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }

  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "RENTE";
  if ($acct[15] < 0)
  {
    $mr["Debet"]             = abs($acct[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function getRekeningZonderValuta($data)
{
  global $mr, $tmpRekNr;
  if ($rekeningDeel == "")
  {
    $rekeningDeel = (trim($data[3]) <> "")?trim($data[3]):trim($data[14]);
  }
  $tmpRekNr = ontnullen($rekeningDeel);
  $depot="UBP";
  $db = new DB();

  if ($tmpRekNr <> "")
  {
    $query = "SELECT * FROM Rekeningen WHERE consolidatie=0 AND SUBSTRING(`Rekening`,1,LENGTH(`Rekening`)-3) = '".$tmpRekNr."' AND `Depotbank` = '".$depot."' ";
    debug($query);
    if ($rec = $db->lookupRecordByQuery($query) )
    {
      return $rec["Rekening"];
    }
    else
    {
      return false;
    }
  }
  return false;
}

function do_CONV()  //verwisseling van Stukken
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr;
  if (count($data["ACCT"][0]) < 1)
  {
    $meldArray[] = $data["ACCT"][0]["row"] . ": overgeslagen";
    return true;
  }

  if (count($data["STEX"]) <> 2)
  {
    $meldArray[] = $data["STEX"][0]["row"] . ": STEX records <> 2 boekingen, overgeslagen";
    return true;
  }

  $stex1 = trimRecord(offsetArray($data["STEX"][0]));
  $stex2 = trimRecord(offsetArray($data["STEX"][1]));

  if ( ($stex1[15] < 0 AND $stex2[15] < 0) OR
       ($stex1[15] > 0 AND $stex2[15] > 0)    )
  {
    $meldArray[] = $data["STEX"][0]["row"]." conversie: STEX records aantallen zijn beiden positief/negatief, overgeslagen";
    return true;
  }

  if ($stex1[15] < 0)
  {
    $lichting   = $stex1;
    $deponering = $stex2;
  }
  else
  {
    $lichting   = $stex2;
    $deponering = $stex1;
  }

  do_algemeen($lichting);
  UBP_getfonds($lichting[9]);

  $controleBedrag = 0;
  $mr = array();
  $mr["aktie"]             = "L";

  $mr["Valuta"]            = $fonds["Valuta"];
  if ($reknr = getRekeningMem($lichting[2]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $lichting["regelnr"].": Rekeningnr (".ontnullen($lichting[2])."MEM) niet gevonden";
  }

  $mr["Boekdatum"]         = decodeDate($lichting[25]);
  $mr["settlementDatum"]   = decodeDate($lichting[23]);

  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Grootboekrekening"] = "FONDS";

  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $lichting[15];
  $mr["Fondskoers"]        = $lichting[125];
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  $controleBedrag         += $mr["Bedrag"];
  $output[] = $mr;
  $creditBedragLichting = $mr["Credit"];

  // deponering
  do_algemeen($deponering);
  UBP_getfonds($deponering[9]);
  $mr["aktie"]             = "D";
  $mr["Valuta"]            = $fonds["Valuta"];
  if ($reknr = getRekeningMem($deponering[2]) )
  {
    $mr["Rekening"] = $reknr;
  }
  else
  {
    $meldArray[] = $deponering["regelnr"].": Rekeningnr (".ontnullen($deponering[2])."MEM) niet gevonden";
  }

  $mr["Boekdatum"]         = decodeDate($deponering[25]);
  $mr["settlementDatum"]   = decodeDate($deponering[23]);


  $mr["Grootboekrekening"] = "FONDS";

  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $deponering["Fonds"];
  $mr["Aantal"]            = $deponering[15];
  $mr["Fondskoers"]        = $creditBedragLichting / $fonds["Fondseenheid"] /$mr["Aantal"];

  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];
  $controleBedrag         += $mr["Bedrag"];
  $output[] = $mr;

  checkControleBedrag($controleBedrag,0);

}

function do_FXKRUIS()  //FX naar kruispost
{
  global $fonds, $data, $mr, $output, $meldArray, $tmpRekNr;

  if (count($data["ACCT"][0]) < 1)
  {
    $meldArray[] = $data["FORX"][0]["row"] . ": overgeslagen";
    return true;
  }

  if (count($data["ACCT"]) <> 2)
  {
    $meldArray[] = $data["ACCT"][0]["row"] . ": ACCT records <> 2 boekingen, overgeslagen";
    return true;
  }

  $acct = offsetArray($data["ACCT"][0]);
  $acct = trimRecord($acct);
  $mr["Valuta"] = $acct[27];

  $acct2 = offsetArray($data["ACCT"][1]);
  $acct2 = trimRecord($acct2);
//debug($acct);
//  debug($acct2);
 // if ($acct[27] <> "EUR" AND $acct2[27] <> "EUR")
//  debug($data["FORX"][0]);
  if (!stristr($data["FORX"][0][12],"EUR") )
  {
    $meldArray[] = $data["ACCT"][0]["row"] . ": FX zonder EUR, graag handmatig corrigeren";

  }


  $mr["aktie"]              = "FXK";
  do_algemeen($acct);

  $mr["Omschrijving"]      = "Valutatransactie";

  $mr["Boekdatum"]         = decodeDate($acct[25]);
  $mr["settlementDatum"]   = decodeDate($acct[23]);
  $rekRec = null;
  $mr["Valuta"] = $acct[27];
  if ($rekRec = getRekening($acct, $mr["Valuta"], $acct[14] ,true))
  {
    $mr["Rekening"] = $rekRec["Rekening"];
    $mr["Valuta"] = $rekRec["Valuta"];
  }
  else
  {
    $mr["Rekening"] = "";
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }
  if ( checkVoorDubbelInRM($mr) )
  {
    return true;
  }
  $mr["Valutakoers"]       = ($mr["Valuta"] == "EUR")?1:1/$acct[37];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";

  if ($acct[15] < 0)
  {
    $mr["Debet"]             = abs($acct[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
  {
    if (!stristr($data["FORX"][0][12],"EUR"))
    {
      $mr["Rekening"] .= "XXX";
      $mr["Valuta"]   .= "X";
    }
    $output[] = $mr;
  }


  $mr["Boekdatum"]         = decodeDate($acct2[25]);
  $mr["settlementDatum"]   = decodeDate($acct2[23]);
  $rekRec = null;
  $mr["Valuta"] = $acct2[27];
  if ($rekRec = getRekening($acct2, $mr["Valuta"], $acct2[14], true ))
  {
    $mr["Rekening"] = $rekRec["Rekening"];
    $mr["Valuta"] = $rekRec["Valuta"];
  }
  else
  {
    $mr["Rekening"] = "";
    $meldArray[] = $mr["regelnr"].": Rekeningnr (".$tmpRekNr.") niet gevonden";
  }
  $mr["Valutakoers"]       = ($mr["Valuta"] == "EUR")?1:1/$acct[37];
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Grootboekrekening"] = "KRUIS";

  if ($acct2[15] < 0)
  {
    $mr["Debet"]             = abs($acct2[15]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = -1 * $mr["Debet"];
  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($acct2[15]);
    $mr["Bedrag"]            = $mr["Credit"];
  }

  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $controleBedrag       += $mr["Bedrag"];
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  if ($mr["Valuta"] == "EUR")
  {
    $vk = round($acct2[15] / $acct[15],4);
  }
  else  // acct == EUR
  {
    $vk = round($acct[15] / $acct2[15],4);
  }

  checkControleBedrag(abs($vk),abs(round(1/$acct[37],4)));


}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_NVT()
{
  global $meldArray, $data, $transcode;
//  debug($data);
  foreach ($data as $type)
  {
    foreach ($type as $item)
    {
      $regels[] .= $item["row"];
    }

  }
  $meldArray[] = "regel ".implode(", ",$regels).":<b> met transactiecode ".$transcode." overgeslagen</b>";
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_error()
{
	global $do_func,$transcode;
	echo "<BR>FOUT functie bij <b>$transcode</b> bestaat niet!";
}


?>