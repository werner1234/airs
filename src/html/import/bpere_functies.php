<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/05/03 08:14:19 $
 		File Versie					: $Revision: 1.13 $

 		$Log: bpere_functies.php,v $
 		Revision 1.13  2012/05/03 08:14:19  cvs
 		*** empty log message ***
 		
 		Revision 1.12  2012/04/25 09:58:18  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2011/10/26 06:43:33  cvs
 		*** empty log message ***
 		
 		Revision 1.10  2011/10/24 12:25:00  cvs
 		*** empty log message ***
 		
 		Revision 1.9  2011/07/19 14:31:58  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2010/12/09 15:01:52  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2010/11/30 12:59:15  cvs
 		*** empty log message ***

 		Revision 1.6  2010/11/12 10:10:21  cvs
 		*** empty log message ***

 		Revision 1.5  2010/11/03 11:04:28  cvs
 		*** empty log message ***

 		Revision 1.4  2010/11/03 10:43:23  cvs
 		*** empty log message ***

 		Revision 1.3  2010/10/12 14:18:11  cvs
 		*** empty log message ***

 		Revision 1.2  2010/09/24 05:44:21  cvs
 		*** empty log message ***

 		Revision 1.1  2010/09/21 11:36:08  cvs
 		*** empty log message ***





*/

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


function _cashvalutakoers()
{
	global $data;
  return $data[12]/$data[11];
}

function _valutakoers()
{
	global $data;

  switch ($data[11])
  {
    case "NOK":
    case "DKK":
    case "SEK":
    case "JPY" :
      $output = $data[17] / 100;
      break;
    default:
      $output = $data[17];

  }
  return $output;
}

function checkIfdata20isE()
{
  global $mr, $data;
  if (strtolower($data[20]) == "e")
  {
    $swap         = $mr["Debet"];
    $mr["Debet"]  = $mr["Credit"];
    $mr["Credit"] = $swap;
    $mr["Bedrag"] = $mr["Bedrag"] * -1;
    $mr["Aantal"] = $mr["Aantal"] * -1;
  }

}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["Boekdatum"]         = substr($data[2],0,4)."-".substr($data[2],4,2)."-".substr($data[2],6,2);
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();
   
  if (trim($data[11]) =="BRL")  // uitzondering voor BRL valuta
  {
    $rekeningValuta = "USD";  
    $valutaKoers    = 1/$data[28];
    $BRLexception   = true;
  }
  else
  {
    $rekeningValuta = trim($data[11]);
    $valutaKoers    = _valutakoers();
    $BRLexception   = false;
  }
  
	$mr["Rekening"]          = trim($data[1]).$rekeningValuta;
  $mr["Valutakoers"]       = $valutaKoers;
  
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[11];
	
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[9];
	$mr["Fondskoers"]        = $data[10];
  $mr["Debet"]             = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
	$mr["Credit"]            = 0;
  if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
  	$mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  checkIfdata20isE();

	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = round(abs($data[21]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[26] + $data[22] + $data[24]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  $mr["Grootboekrekening"] = "TOB";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[23]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENME";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = round(abs($data[13] - $data[12] - ($data[21] + $data[22] + $data[23] + $data[24] + $data[25] + $data[26] )),2);
  if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
  $mr["Transactietype"]    = "";
  checkIfdata20isE();
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;



}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "V";
	do_algemeen();

  if (trim($data[11]) =="BRL")  // uitzondering voor BRL valuta
  {
    $rekeningValuta = "USD";  
    $valutaKoers    = 1/$data[28];
    $BRLexception   = true;
  }
  else
  {
    $rekeningValuta = trim($data[11]);
    $valutaKoers    = _valutakoers();
    $BRLexception   = false;
  }
  
	$mr["Rekening"]          = trim($data[1]).$rekeningValuta;
  $mr["Valutakoers"]       = $valutaKoers;
    
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[11];

	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * $data[9];
	$mr["Fondskoers"]        = $data[10];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Credit"] * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Credit"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  checkIfdata20isE();
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[21]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
  $mr["Debet"]             = round(abs($data[26] + $data[22]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  $mr["Grootboekrekening"] = "TOB";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[23]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  $mr["Grootboekrekening"] = "RENOB";
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  $mr["Credit"]            = round(abs($data[13] - $data[12] + ($data[21] + $data[22] + $data[23] + $data[24] + $data[25] + $data[26] )),2);
  $mr["Debet"]             = 0;
  if ($BRLexception)
    $mr["Bedrag"]            = $mr["Credit"] * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Credit"];
  $mr["Transactietype"]    = "";
  checkIfdata20isE();
  if ($mr["Bedrag"] <> 0)
  $output[] = $mr;


}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  //Rente of couponrente
{
  global $fonds, $data, $mr, $output;

    $mr = array();
    $mr["aktie"]              = "R";
    do_algemeen();
    $mr["Rekening"]          = trim($data[1]).trim($data[11]);
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $data[11];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = round(abs($data[12]),2);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    checkIfdata20isE();

    $output[] = $mr;

    $mr["Rekening"]          = trim($data[1]).trim($data[11]);
    $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
    $mr["Grootboekrekening"] = "DIVBE";
    $mr["Valuta"]            = $data[11];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = $data[24];
    $mr["Credit"]            = 0 ;
    $mr["Bedrag"]            = round($mr["Debet"] * -1,2);
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    checkIfdata20isE();
    if ($mr["Bedrag"] <> 0)
        $output[] = $mr;



    $mr["Grootboekrekening"] = "KNBA";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = round(abs($data[25]),2);
    $mr["Bedrag"]            = $mr["Debet"] * -1;
    $mr["Transactietype"]    = "";
    checkIfdata20isE();
    if ($mr["Bedrag"] <> 0)
        $output[] = $mr;



  return;
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_L()  //Lossing van obligaties
{
  global $fonds, $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "L";
	do_algemeen();
 
  if (trim($data[11]) =="BRL")  // uitzondering voor BRL valuta
  {
    $rekeningValuta = "USD";  
    $valutaKoers    = 1/$data[28];
    $BRLexception   = true;
  }
  else
  {
    $rekeningValuta = trim($data[11]);
    $valutaKoers    = _valutakoers();
    $BRLexception   = false;
  }
  
	$mr["Rekening"]          = trim($data[1]).$rekeningValuta;
  $mr["Valutakoers"]       = $valutaKoers;
  
	$mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[11];
	
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * $data[9];
	$mr["Fondskoers"]        = $data[10];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
	
  if ($BRLexception)
    $mr["Bedrag"]            = $mr["Credit"] * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Credit"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  checkIfdata20isE();
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[21]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[26] + $data[22]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

  $mr["Grootboekrekening"] = "TOB";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = round(abs($data[23]),2);
	if ($BRLexception)
    $mr["Bedrag"]            = $mr["Debet"] * -1 * $valutaKoers;
  else
    $mr["Bedrag"]            = $mr["Debet"] * -1;
	$mr["Transactietype"]    = "";
  checkIfdata20isE();
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;




}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_DV()  //Contant dividend
{
  global $fonds, $data, $mr, $output;

  $mr = array();
  $mr["aktie"]              = "DV";
  do_algemeen();
  $mr["Rekening"]          = trim($data[1]).trim($data[11]);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIV";
  $mr["Valuta"]            = $data[11];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
  $mr["Bedrag"]            = $mr["Credit"];
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  checkIfdata20isE();

  $output[] = $mr;

  $mr["Rekening"]          = trim($data[1]).trim($data[11]);
  $mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "DIVBE";
  $mr["Valuta"]            = $data[11];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = round($data[24],2);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $mr["Debet"] * -1;
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  checkIfdata20isE();
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

  $mr["Grootboekrekening"] = "KNBA";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = round(abs($data[25]),2);
  $mr["Bedrag"]            = $mr["Debet"] * -1;
  $mr["Transactietype"]    = "";
  checkIfdata20isE();
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;
  return;
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_ST()  // Storting van geld of stukken
{
  global $fonds, $data, $mr, $output;

  $mr = array();
  $mr["aktie"]             = "ST";
  do_algemeen();
  $mr["Rekening"]          = trim($data[1])."MEM";
  $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[11];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = $data[9];
  $mr["Fondskoers"]        = $data[10];
  $mr["Debet"]             = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
  $mr["Transactietype"]    = "D";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "STORT";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = 0;
  $mr["Credit"]            = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
  $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"];
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OP()  // Opname van geld of stukken
{
  global $fonds, $data, $mr, $output;
  $mr = array();
  $mr["aktie"]              = "OP";
  do_algemeen();
  $mr["Rekening"]          = trim($data[1])."MEM";
  $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[11];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = $fonds["Fonds"];
  $mr["Aantal"]            = -1 * $data[9];
  $mr["Fondskoers"]        = $data[10];
  $mr["Debet"]             = 0;
  $mr["Credit"]            = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
  $mr["Bedrag"]            = $mr["Credit"] * $mr["Valutakoers"] ;
  $mr["Transactietype"]    = "L";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;

  $output[] = $mr;

  $mr["Grootboekrekening"] = "ONTTR";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Credit"]            = 0;
  $mr["Debet"]             = round(abs($data[9] * $data[10] * $fonds["Fondseenheid"]),2);
  $mr["Bedrag"]            = $mr["Debet"] * $mr["Valutakoers"] * -1;
  $mr["Transactietype"]    = "";
  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_OPC()  // Opname van geld uit 131 bestand
{
  global $fonds, $data, $mr, $output, $_file, $row;
	$mr = array();
	$mr["aktie"]              = "OPC";
  $mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["Boekdatum"]         = substr($data[3],0,4)."-".substr($data[3],4,2)."-".substr($data[3],6,2);
  if ($data[4] == "403")
    $mr["Rekening"]          = $data[1]."DEP";
  else
	  $mr["Rekening"]          = $data[1].$data[2];
	$mr["Omschrijving"]      = $data[8];

  if (  stristr($data[8]," SPOT ")     OR 
        substr($data[8],0,5)== "EMP.E" OR 
        substr($data[8],0,4)== "CAT."  OR 
        substr($data[8],0,4)== "RBT."    )
        
    $mr["Grootboekrekening"] = "KRUIS";
  else
	  $mr["Grootboekrekening"] = "ONTTR";

  //$mr["Grootboekrekening"] = grootboekFromText(DEPOTBANK, $data[8], $mr["Grootboekrekening"]);

/*
  $omschr = strtoupper($data[8]);
  if (substr($omschr,0,6)  == "KOSTEN")      $mr["Grootboekrekening"] = "KOST";
  if (substr($omschr,0,8)  == "ROERENDE")    $mr["Grootboekrekening"] = "ROER";
  if (substr($omschr,0,11) == "BEWAARLONEN") $mr["Grootboekrekening"] = "BEW";
  if (substr($omschr,0,8)  == "RECHT OP")    $mr["Grootboekrekening"] = "KNBA";
  if (substr($omschr,0,8)  == "INTEREST")    $mr["Grootboekrekening"] = "RENTE";
*/

	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _cashvalutakoers();
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs($data[11]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = -1 * $mr["Debet"];
	$mr["Transactietype"]    = "";

	$output[] = $mr;

}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_STC()  // Storting van geld uit 131 bestand
{
  global $fonds, $data, $mr, $output, $_file, $row;
	$mr = array();
	$mr["aktie"]              = "STC";
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["Boekdatum"]         = substr($data[3],0,4)."-".substr($data[3],4,2)."-".substr($data[3],6,2);
    if ($data[4] == "403")
    $mr["Rekening"]          = $data[1]."DEP";
  else
  	$mr["Rekening"]          = $data[1].$data[2];
	$mr["Omschrijving"]      = $data[8];

  if (   stristr($data[8]," SPOT ")     OR 
         substr($data[8],0,5)== "EMP.E" OR 
         substr($data[8],0,4)== "CAT."  OR 
         substr($data[8],0,4)== "RBT."     )
    $mr["Grootboekrekening"] = "KRUIS";
  elseif (substr($data[8],0,4)== "INT." )
  	$mr["Grootboekrekening"] = "RENTE";
  else  
  	$mr["Grootboekrekening"] = "STORT";


//  $mr["Grootboekrekening"] = grootboekFromText(DEPOTBANK, $data[8], $mr["Grootboekrekening"]);
/*
  $omschr = strtoupper($data[8]);
  if (substr($omschr,0,6)  == "KOSTEN")      $mr["Grootboekrekening"] = "KOST";
  if (substr($omschr,0,8)  == "ROERENDE")    $mr["Grootboekrekening"] = "ROER";
  if (substr($omschr,0,11) == "BEWAARLONEN") $mr["Grootboekrekening"] = "BEW";
  if (substr($omschr,0,8)  == "RECHT OP")    $mr["Grootboekrekening"] = "KNBA";
  if (substr($omschr,0,8)  == "INTEREST")    $mr["Grootboekrekening"] = "RENTE";
*/

	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _cashvalutakoers();
	$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($data[11]);
	$mr["Bedrag"]            = $mr["Credit"];
	$mr["Transactietype"]    = "";

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_skip()  //transactie welke niet verwerkt hoeven te worden
{
  return;
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