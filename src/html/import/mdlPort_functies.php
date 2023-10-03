<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/02/03 11:21:46 $
 		File Versie					: $Revision: 1.3 $

 		$Log: mdlPort_functies.php,v $
 		Revision 1.3  2017/02/03 11:21:46  cvs
 		verkoop aantal * -1
 		
 		Revision 1.2  2016/07/18 12:25:09  cvs
 		update 20160718
 		
 		Revision 1.1  2016/06/22 12:55:42  cvs
 		call 5064
 		


*/

function _debetbedrag()
{
	global $mr;
  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $mr;
  return $mr["Credit"] * $mr["Valutakoers"];
}



function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;


	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;

	$mr["Boekdatum"]         = $data[7];
  $mr["settlementDatum"]   = $data[7];
  $mr["Rekening"]          = $data[8];
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()  // Aankoop van stukken
{
  global $data, $mr, $output;

	$mr = array();
	$mr["aktie"]             = "A";
	do_algemeen();

	$mr["Omschrijving"]      = "Aankoop ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = $data[9];
	$mr["Fonds"]             = $data[12];
	$mr["Aantal"]            = $data[5];
	$mr["Fondskoers"]        = $data[10];
  $mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $data[13]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
  
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_V()
{
  global $data, $mr, $output;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "V";
	do_algemeen();
	$mr["Omschrijving"]      = "Verkoop ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = $data[9];
	$mr["Fonds"]             = $data[12];
	$mr["Aantal"]            = abs($data[6]) * -1;
	$mr["Fondskoers"]        = $data[10];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $data[13]);
	$mr[Bedrag]            = _creditbedrag();

	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

}


function do_L()  //Lossing van obligaties
{
	global  $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "L";
	do_algemeen();
	$mr["Omschrijving"]      = "Lichting  ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = $data[9];
	$mr["Fonds"]             = $data[12];
  $mr["Aantal"]            = abs($data[6]) * -1;
	$mr["Fondskoers"]        = $data[10];
	$mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
	$mr["Debet"]             = 0;
	$mr["Bedrag"]            = _creditbedrag();
	$mr["Transactietype"]    = "L";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
	$output[] = $mr;

	if  ($mr["Bedrag"] <> 0)
	{
		$mr["Grootboekrekening"] = "ONTTR";
		$mr["Fonds"]             = "";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Credit"]            = 0;
		$mr["Debet"]             = abs($mr["Bedrag"]);
		$mr["Bedrag"]            = -1 * $mr["Debet"];
		$mr["Transactietype"]    = "";
		$output[] = $mr;
	}
}
function do_LB()  //beginboeking
{
  global  $data, $mr, $output;
  $mr = array();
  $mr["aktie"]              = "LB";
  do_algemeen();
  $mr["Omschrijving"]      = "Inbreng  ".$data[11];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[4];
  $mr["Valutakoers"]       = $data[9];
  $mr["Fonds"]             = $data[12];
  $mr["Aantal"]            = abs($data[6]) * -1;
  $mr["Fondskoers"]        = $data[10];
  $mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
  $mr["Debet"]             = 0;
  $mr["Bedrag"]            = _creditbedrag();
  $mr["Transactietype"]    = "B";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "VERM";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fonds"]             = "";
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]            = 0;
    $mr["Debet"]             = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
    $mr["Transactietype"]    = "B";
    $output[] = $mr;
  }
}


function do_D()
{
	global $data, $mr, $output;
	$mr = array();
	$mr["aktie"]              = "D";
	do_algemeen();

	$mr["Omschrijving"]      = "Deponering  ".$data[11];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $data[4];
	$mr["Valutakoers"]       = $data[9];
	$mr["Fonds"]             = $data[12];
	$mr["Aantal"]            = $data[5];
	$mr["Fondskoers"]        = $data[10];
	$mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
	$mr["Credit"]            = 0;
	$mr["Bedrag"]            = _debetbedrag();
	$mr["Transactietype"]    = "D";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
	$output[] = $mr;

	if  ($mr["Bedrag"] <> 0)
	{
		$mr["Grootboekrekening"] = "STORT";
		$mr["Fonds"]             = "";
		$mr["Valuta"]            = "EUR";
		$mr["Valutakoers"]       = 1;
		$mr["Aantal"]            = 0;
		$mr["Fonds"]             = "";
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($mr["Bedrag"]);
		$mr["Bedrag"]            = $mr["Credit"];
		$mr["Transactietype"]    = "";
		$output[] = $mr;
	}

}


function do_DB() // beginboekig
{
  global $data, $mr, $output;
  $mr = array();
  $mr["aktie"]              = "DB";
  do_algemeen();

  $mr["Omschrijving"]      = "Inbreng  ".$data[11];
  $mr["Grootboekrekening"] = "FONDS";
  $mr["Valuta"]            = $data[4];
  $mr["Valutakoers"]       = $data[9];
  $mr["Fonds"]             = $data[12];
  $mr["Aantal"]            = $data[5];
  $mr["Fondskoers"]        = $data[10];
  $mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"]  * $data[13]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = _debetbedrag();
  $mr["Transactietype"]    = "B";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;
  $output[] = $mr;

  if  ($mr["Bedrag"] <> 0)
  {
    $mr["Grootboekrekening"] = "VERM";
    $mr["Fonds"]             = "";
    $mr["Valuta"]            = "EUR";
    $mr["Valutakoers"]       = 1;
    $mr["Aantal"]            = 0;
    $mr["Fonds"]             = "";
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($mr["Bedrag"]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "B";
    $output[] = $mr;
  }

}


function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>