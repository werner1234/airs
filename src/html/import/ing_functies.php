<?
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.8 $

 		$Log: ing_functies.php,v $
 		Revision 1.8  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/09/27 11:29:41  cvs
 		call 6041
 		
 		Revision 1.6  2017/04/12 14:17:28  cvs
 		call 5785
 		
 		Revision 1.5  2017/04/03 12:14:31  cvs
 		call 5174
 		
 		Revision 1.4  2016/07/01 14:36:48  cvs
 		call 5005
 		
 		Revision 1.3  2016/04/04 14:26:10  cvs
 		do_renob
 		
 		Revision 1.2  2016/04/04 08:30:08  cvs
 		call 4712
 		
 		Revision 1.1  2016/03/25 10:41:08  cvs
 		call 3691
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/


function getFonds()
{
  global $data, $error, $row, $fonds;
  $db = new DB();

  if ($data[52] <> "" AND $data[3] <> "")
  {
      $fonds = array();
    $ISIN   = trim($data[3]);
    $VALUTA = trim($data[52]);

    $query = "
      SELECT
        *,
        case WHEN 
          EindDatum = '00000000' 
        THEN 
          '2099-12-31'
        ELSE 
          EindDatum 
        END AS ed
      FROM
        Fondsen
      WHERE 
        ISINcode = '$ISIN' AND 
        Valuta = '$VALUTA'
      ORDER BY 
        ed DESC
    ";

    if ($fondsRec = $db->lookupRecordByQuery($query))
    {
      $fonds = $fondsRec;
      return true;
    }
    else
    {
      $error[] = "$row: fonds $ISIN/$VALUTA niet gevonden ";
      return false;
    }
  }
//  elseif ($data[52] <> "")
//  {
//    $error[] = "$row: Fondsvaluta zonder ISIN gevonden, controleer fonds";
//    return false;
//  }
}



function getRekening()
{
  global $data, $error, $row;
  $IbanNotFound = false;
	$depot="ING";
  $db = new DB();
	$rekeningNr = trim($data[51]);
//debug($data);
	if ($rekeningNr <> "")
	{
		$query = "SELECT * FROM Rekeningen WHERE `consolidatie`= 0 AND `IBANnr` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

		if ($rec = $db->lookupRecordByQuery($query) )
		{
			return array("rekening" => $rec["Rekening"],
									 "valuta"   => $rec["Valuta"]);
		}
		else
		{
		  $error[] = "$row: IBAN $rekeningNr komt niet voor";
			return false;
		}
	}
	else
	{
		$IbanNotFound = true;
	}

	if ($IbanNotFound)
	{
		$rekeningNr = trim($data[1]).trim($data[9]);
		$query = "SELECT * FROM Rekeningen WHERE `consolidatie`= 0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";

		if ($rec = $db->lookupRecordByQuery($query) )
		{
			return array("rekening" => $rec["Rekening"],
									 "valuta"   => $rec["Valuta"]);
		}
		else
		{
      $error[] = "$row: Rekening $rekeningNr komt niet voor";
			return false;
		}

	}

}

function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "ING|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }
}

function _debetbedrag()
{
	global $data, $mr;

	if ($data[9] == $mr["Valuta"] )
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr;

  if ($data[9] == $mr["Valuta"] )
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers()
{
	global $data;
	
	return 1/$data[10];
}

function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;
	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[22];

  $datum = explode(".",$data[15]);

	$mr["Boekdatum"]         = $datum[2]."-".$datum[1]."-".$datum[0];

  $datum = explode(".",$data[16]);

  $mr["settlementDatum"]   = $datum[2]."-".$datum[1]."-".$datum[0];

  $mr["Rekening"]          = trim($data[51]);
	if ($rekRec  = getRekening() )
  {
    $mr["Rekening"] = $rekRec["rekening"];
    $mr["Valuta"]   ="EUR";
  }
	else
	{
		$mr["Rekening"] = "reknr onbekend: ".trim($data[1]);
	}

  
  $data["memRek"] = $data[1]."MEM";
  
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
  $mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = $data[6];
	$mr["Fondskoers"]        = $data[8];
  $mr["Debet"]             = abs($data[6] * $data[8] * $fonds["Fondseenheid"]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Debet"]             = abs(($data[11] + $data[12])*$data[10]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-KOST");
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[13]*$data[10]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       += $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-KOBU");
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

	if ($data[7] <> 0)  // aankoop obligatie
	{
	  $mr["Grootboekrekening"] = "RENME";
	  $mr["Aantal"]            = 0;
	  $mr["Fondskoers"]        = 0;
	  $mr["Debet"]             = abs($data[7]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
		//debug($controleBedrag,$fonds["Omschrijving"]."-RENME");
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
	    $output[] = $mr;

	}
  checkControleBedrag($controleBedrag,$data[14]*-1);
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
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * $data[6];
	$mr["Fondskoers"]        = $data[8];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($data[6] * $data[8] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag       += $mr["Bedrag"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
//debug($controleBedrag,$fonds["Omschrijving"]."-FONDS");
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[11] + $data[12]) * $data[10];
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       -= $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-KOST");
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[13] * $data[10]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag       -= $mr["Bedrag"];
	//debug($controleBedrag,$fonds["Omschrijving"]."-KOBU");
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

	if ($data[7] <> 0 )
	{
	  $mr["Grootboekrekening"] = "RENOB";
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = abs($data[7]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
		//debug($controleBedrag,$fonds["Omschrijving"]."-RENOB");
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
		  $output[] = $mr;
	}
  checkControleBedrag($controleBedrag,$data[14]*-1);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_RENOB()  //Rente of couponrente
{

  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "R";
	do_algemeen();

	if ($data[3])
	{

	  if ($data[14] > 0)  // als veld negatief betreft correctie rente
	  {


  		$mr["Omschrijving"] = "Coupon " . $fonds["Omschrijving"];

		  $mr["Grootboekrekening"] = "RENOB";
		  $mr["Valuta"]            = $fonds["Valuta"];
		  $mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             = $fonds["Fonds"];
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = abs(($data[5] * $data[8]) * $fonds["Fondseenheid"]);
		  $mr["Credit"]            = 0;
		  $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  $mr["Verwerkt"]          = 0;
		  $mr["Memoriaalboeking"]  = 0;

		  $output[] = $mr;

		  $mr["Grootboekrekening"] = "DIVBE";
	    $mr["Valuta"]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr["Valutakoers"]       = _valutakoers();
	    else
	      $mr["Valutakoers"]       = 1;

	    $mr["Aantal"]            = 0;
	    $mr["Fondskoers"]        = 0;
	    $mr["Debet"]             = 0;
	    $mr["Credit"]            = abs($data[13] * $data[10]);
	    $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];

	    if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;


			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             = "";
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs($data[11]);
		  $mr["Bedrag"]            = $mr["Credit"];
      $controleBedrag       += $mr["Bedrag"];

      if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;

		  $mr["Grootboekrekening"] = "KOBU";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
  	  $mr["Fonds"]             = "";
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs($data[12]) * $data[10];
		  $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  if ($mr["Bedrag"] <> 0)
			  $output[] = $mr;
	  }
	  else
  	{


      $mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];

		  $mr["Grootboekrekening"] = "RENOB";
		  $mr["Valuta"]            = $fonds["Valuta"];
		  $mr["Valutakoers"]       = _valutakoers();
		  $mr["Fonds"]             =  $fonds["Fonds"];
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = 0;
		  $mr["Credit"]            = abs(($data[5] * $data[8]) * $fonds["Fondseenheid"]);
		  $mr["Bedrag"]            = _creditbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  $mr["Verwerkt"]          = 0;
		  $mr["Memoriaalboeking"]  = 0;

		  $output[] = $mr;

      // 2008-04-17 cvs start toegvoeging
		  $mr["Grootboekrekening"] = "DIVBE";
	    $mr["Valuta"]            = $data[9];
	    if ($data[9] <> "EUR")
	      $mr["Valutakoers"]       = _valutakoers();
	    else
	      $mr["Valutakoers"]       = 1;
	    //$mr["Fonds"]             = "";
	    $mr["Aantal"]            = 0;
	    $mr["Fondskoers"]        = 0;
	    $mr["Debet"]             = abs($data[13] * $data[10]);
	    $mr["Credit"]            = 0;
	    $mr["Bedrag"]            = -1 * $mr["Debet"];
      $controleBedrag       += $mr["Bedrag"];

	    if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;
		  // 2008-04-17 cvs einde toegvoeging

		  $mr["Grootboekrekening"] = "KNBA";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Debet"]             = abs($data[11] * $data[10]);
		  $mr["Credit"]            = 0;
		  $mr["Bedrag"]            = -1 * $mr["Debet"];
      $controleBedrag       += $mr["Bedrag"];

      if ($mr["Bedrag"] <> 0)
		    $output[] = $mr;

		  $mr["Grootboekrekening"] = "KOBU";
			$mr["Valuta"]            = $fonds["Valuta"];
			$mr["Valutakoers"]       = _valutakoers();
		  $mr["Aantal"]            = 0;
		  $mr["Fondskoers"]        = 0;
		  $mr["Credit"]            = 0;
		  $mr["Debet"]             = abs($data[12] * $data[10]);
		  $mr["Bedrag"]            = _debetbedrag();
      $controleBedrag       += $mr["Bedrag"];

		  $mr["Transactietype"]    = "";
		  if ($mr["Bedrag"] <> 0)
			  $output[] = $mr;
  	}
	}
	else
	{
    $mr["Omschrijving"]      = $data[53];

    if (trim($data[53]) == "")
		{
			$mr["Omschrijving"]      = "Creditrente";
		}

		$mr["Grootboekrekening"] = "RENTE";
		$mr["Valuta"]            = $data[9];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = "";
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[14]);
		$mr["Bedrag"]            = _creditbedrag();

		if ($data[14] > 0)
		{
			if (trim($data[53]) == "")
			{
				$mr["Omschrijving"] = "Debetrente";
			}
			$mr["Debet"]             = abs($data[14]);
			$mr["Credit"]            = 0;
			$mr["Bedrag"]            = _debetbedrag();
		}

		$controleBedrag        = $mr["Bedrag"];
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
		$output[] = $mr;
	}

  checkControleBedrag($controleBedrag,-1 * $data[14]);
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
	$afrekenValuta = $mr["Valuta"];
	$mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "DIV";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             =  $fonds["Fonds"];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;


	if ($data[14] > 0)  // als veld negatief betreft een correctie Dividend
	{
    $mr["Debet"]             = abs($data[5] * $data[8]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[5] * $data[8]);
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

	$mr["Grootboekrekening"] = "DIVBE";
	$mr["Valutakoers"]       = _valutakoers();

	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;

	if ($data[14] > 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[13] * $data[10] );
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Debet"]             = abs($data[13] * $data[10]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

	$mr["Grootboekrekening"] = "KNBA";

	//$mr["Fonds"]             = "";
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	//FIXME: 3712 let op bedragen in file staan contra!!
	if ($data[14] > 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[11] * $data[10]);
	  $mr["Bedrag"]            = _creditbedrag() ;
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Debet"]             = abs($data[11] * $data[10]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
  if ($mr["Bedrag"] <> 0)
	  $output[] = $mr;

	$mr["Grootboekrekening"] = "KOBU";


	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	//FIXME: 3712 let op bedragen in file staan contra!!
	if ($data[14] > 0)  // als veld negatief betreft een correctie Dividend
	{
	  $mr["Credit"]            = abs($data[12] * $data[10]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Credit"]            = 0;
	  $mr["Debet"]             = abs($data[12] * $data[10]);
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

  checkControleBedrag($controleBedrag,$data[14]*-1);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_MUT()  //mutatie geld/stukken
{

  global $fonds, $data, $mr, $output;

  $mr = array();
  $mr["aktie"]              = "KO";
  do_algemeen();
  if (count($fonds) == 0)
  {

    $mr["Omschrijving"]    = $data[53];

    $mr["Valuta"]            = $data[9];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    if ($data[4] == "OP")
    {
      $mr["Grootboekrekening"] = "ONTTR";
      $mr["Debet"]             = abs($data[14]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * $mr["Debet"];
    }
    else
    {
      $mr["Grootboekrekening"] = "STORT";
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[14]);
      $mr["Bedrag"]            = $mr["Credit"];
    }


    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag         += $mr["Bedrag"];
    if ($mr["Bedrag"] <> 0)
      $output[] = $mr;
  }
  else
  {
/////////////////////

    $mr["Valuta"]            = $data[9];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = $data[5];
    $mr["Fondskoers"]        = $data[8];
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 1;
    $mr["Rekening"]          = trim($data[1])."MEM";
    $mr["Grootboekrekening"] = "FONDS";


    if ($data[5] > 0)
    {
      $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];


      $mr["Debet"]             = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
      $mr["Credit"]            = 0;
      $mr["Bedrag"]            = -1 * ($mr["Debet"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "D";

      $output[] = $mr;

      if ($mr["Bedrag"] <> 0)
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
        $mr["Bedrag"]            = ($mr["Credit"] *  $mr["Valutakoers"]);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }
    }
    else
    {
      $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];


      $mr["Credit"]            = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
      $mr["Debet"]             = 0;
      $mr["Bedrag"]            = ($mr["Credit"] * $mr["Valutakoers"]);
      $controleBedrag         += $mr["Bedrag"];

      $mr["Transactietype"]    = "L";

      $output[] = $mr;

      if ($mr["Bedrag"] <> 0)
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Credit"]             = 0;
        $mr["Debet"]            = abs($data[5] * $data[8] * $fonds["Fondseenheid"]);
        $mr["Bedrag"]            = -1 * ($mr["Debet"] *  $mr["Valutakoers"]);
        $controleBedrag         += $mr["Bedrag"];
        $mr["Transactietype"]    = "";
        $output[] = $mr;
      }
    }


    checkControleBedrag($controleBedrag,$data[14]*-1);




    ///////////////////////////
  }

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