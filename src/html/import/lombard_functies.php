<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/02/11 15:56:30 $
 		File Versie					: $Revision: 1.13 $

 		$Log: lombard_functies.php,v $
 		Revision 1.13  2020/02/11 15:56:30  cvs
 		call 8414
 		
 		Revision 1.12  2020/02/11 15:35:34  cvs
 		call 8414
 		
 		Revision 1.11  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/06/15 07:41:22  cvs
 		call 6063
 		
 		Revision 1.9  2018/01/17 16:26:32  cvs
 		call 6521
 		
 		Revision 1.8  2018/01/17 15:50:44  cvs
 		call 6520
 		
 		Revision 1.7  2017/10/16 12:27:15  cvs
 		call 6170
 		
 		Revision 1.6  2017/09/20 06:16:53  cvs
 		call 6115
 		
 		Revision 1.5  2016/11/30 14:18:54  cvs
 		5402/5387/5135
 		
 		Revision 1.4  2016/10/21 10:55:41  cvs
 		call 5220
 		
 		Revision 1.3  2016/04/04 14:27:18  cvs
 		no message
 		
 		Revision 1.2  2016/03/25 10:40:47  cvs
 		no message
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:43:06  cvs
 		*** empty log message ***
 		




*/

function getRekening($rekeningNr="-1", $depot="LOM")
{
  $db = new DB();
  $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `RekeningDepotbank` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $rec["Rekening"]; 
  }
  else
  {
    $query = "SELECT * FROM Rekeningen WHERE `consolidatie`=0 AND `Rekening` = '".$rekeningNr."' AND `Depotbank` = '".$depot."' ";
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


function getFonds()
{
  global $fonds, $data, $meldArray;
  $DB = new DB();
	$fonds = array();
  $isinValuta = ($data[24] <> "")?$data[24]:$data[25];
  if ($data[28] <> "" OR ($data[27] <> "" AND $isinValuta <> "") )
  {

    $query = "SELECT * FROM Fondsen WHERE LOMcode = '".$data[28]."' ";
     
    if (!$fonds = $DB->lookupRecordByQuery($query))  
    {
      $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$data[27]."' AND Valuta ='".$isinValuta."' ";
      if (!$fonds = $DB->lookupRecordByQuery($query))  
      {
        return false; 
      }
    }
  }  
  return true;
}


function addToRekeningAdd($portefeuille,$valuta)
{
  global $rekeningAddArray;
  
  $value = "LOM|".$portefeuille."|".$valuta;
  if (!in_array($value,$rekeningAddArray))
  {
    $rekeningAddArray[] = $value;
  }

}

function _debetbedrag()
{
	global $data, $mr, $valutaLookup;

	if ($valutaLookup == true)
	  return -1 * $mr["Debet"];
	else
	  return -1 * ($mr["Debet"] * $mr["Valutakoers"]);
}

function _creditbedrag()
{
	global $data, $mr, $valutaLookup;
	$valuta = $data[9];
	if ($valutaLookup == true)
	  return $mr["Credit"];
	else
	  return $mr["Credit"] * $mr["Valutakoers"];
}


function _valutakoers()
{
	global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = substr(trim($data[2]),0,3);
	$valutaLookup = false;
  $mrvaluta = substr($mr["Valuta"],0,3);
	if ( ($valuta <> "EUR" AND  $mrvaluta == $valuta) OR $valuta == "")
	{
    if ($valuta <> "")
    {
      $mr["Valuta"] = $valuta;
      $mr["Valuta"] = $valuta;
    }
    
    $query = "SELECT * FROM Valutakoersen WHERE Valuta='".$mrvaluta."' AND Datum <= '".$mr["Boekdatum"]."' ORDER BY Datum DESC";
    
    $laatsteKoers = $DB->lookupRecordByQuery($query);
    $valutaLookup = true;
     return $laatsteKoers["Koers"];
 
	}
	elseif ($valuta == "EUR" AND $mrvaluta <> $valuta)
  {
	  return 1/$data[21];
  }
  
  return 1;
}

function _valutakoersKruis()  
{
  global $fonds, $data, $mr, $valutaLookup, $DB;
	$valuta = $data[2];
	$valutaLookup = false;
	if ($valuta <> "EUR" )
	{
	  return 1/$data[23];
  }
  else
  {
    return 1;
  }
  
  
  
}




function do_algemeen()
{
	global $mr, $row, $volgnr, $data, $_file;

	$mr["bestand"]           = $_file;
	$mr["regelnr"]           = $row;
	$mr["bankTransactieId"]  = $data[38];  // is deze uniek genoeg???
	$mr["Boekdatum"]         = substr($data[11],0,4)."-".substr($data[11],4,2)."-".substr($data[11],6,2);
  $mr["settlementDatum"]   = substr($data[12],0,4)."-".substr($data[12],4,2)."-".substr($data[12],6,2);
  $mr["Rekening"]    = substr($data[1],0,8).$data[2];
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

function do_V()
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "V";
	do_algemeen();
	//debug($fonds,"fondsInfo");
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Verkoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	if ($fonds["fondssoort"] == "OPT")
  {
    $mr["Aantal"]            = -1 * $data[22]/$fonds["Fondseenheid"];
  }
  else
  {
    $mr["Aantal"]            = -1 * $data[22];
  }

	$mr["Fondskoers"]        = $data[23];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
    //debug($controleBedrag, "verkoop");
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[47] +
                                 $data[48] +          
                                 $data[51] +          
                                 $data[52] +          
                                 $data[53]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)  
  {  
    //debug($controleBedrag, "KOST");
		$output[] = $mr;
  }  

	$mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[43]+
                                 $data[44]+
                                 $data[46]+
                                 $data[49]+
                                 $data[50]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {  
    //debug($controleBedrag, "KOBU");
	  $output[] = $mr;
  }  

  
  
	if ($data[45] <> 0 )
	{
	  $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $fonds["Valuta"];
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = abs($data[45]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {  
      //debug($controleBedrag, "RENOB");
		  $output[] = $mr;
    }  
	}
  checkControleBedrag($controleBedrag,$data[17]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_LOS()
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "LOS";
	do_algemeen();
	//debug($fonds,"fondsInfo");
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Lossing ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
	$mr["Aantal"]            = -1 * $data[22];
	$mr["Fondskoers"]        = $data[23];
	$mr["Debet"]             = 0;
	$mr["Credit"]            = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _creditbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "V";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
    //debug($controleBedrag, "verkoop");
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[47] +
                                 $data[48] +          
                                 $data[51] +          
                                 $data[52] +          
                                 $data[53]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)  
  {  
    //debug($controleBedrag, "KOST");
		$output[] = $mr;
  }  

	$mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[43]+
                                 $data[44]+
                                 $data[46]+
                                 $data[49]+
                                 $data[50]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {  
    //debug($controleBedrag, "KOBU");
	  $output[] = $mr;
  }  

  
  
	if ($data[45] <> 0 )
	{
	  $mr["Grootboekrekening"] = "RENOB";
    $mr["Valuta"]            = $fonds["Valuta"];
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Credit"]            = abs($data[45]);
	  $mr["Debet"]             = 0;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {  
      //debug($controleBedrag, "RENOB");
		  $output[] = $mr;
    }  
	}
  checkControleBedrag($controleBedrag,$data[17]);
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_A()
{
  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr[aktie]              = "A";
	do_algemeen();
	//debug($fonds,"fondsInfo");
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Aankoop ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "FONDS";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();
	$mr["Fonds"]             = $fonds["Fonds"];
  if ($fonds["fondssoort"] == "OPT")
  {
    $mr["Aantal"]            = $data[22]/$fonds["Fondseenheid"];
  }
  else
  {
    $mr["Aantal"]            = $data[22];
  }

	$mr["Fondskoers"]        = $data[23];
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($mr["Aantal"] * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "A";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;
  //debug($controleBedrag, "aankoop");
  //debug($mr,"MR");
	$output[] = $mr;

	$mr["Grootboekrekening"] = "KOST";
	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[47] +
                                 $data[48] +          
                                 $data[51] +          
                                 $data[52] +          
                                 $data[53]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)  
  {  
    //debug($controleBedrag, "KOST");
		$output[] = $mr;
  }  

	$mr["Grootboekrekening"] = "KOBU";
  $mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	$mr["Credit"]            = 0;
	$mr["Debet"]             = abs($data[43]+
                                 $data[44]+
                                 $data[46]+
                                 $data[49]+
                                 $data[50]);
	$mr["Bedrag"]            = _debetbedrag();
  $controleBedrag         += $mr["Bedrag"];
	$mr["Transactietype"]    = "";
	if ($mr["Bedrag"] <> 0)
  {  
    //debug($controleBedrag, "KOBU");
	  $output[] = $mr;
  }  

  
  
	if ($data[45] <> 0 )
	{
	  $mr["Grootboekrekening"] = "RENME";
    $mr["Valuta"]            = $fonds["Valuta"];
	  $mr["Aantal"]            = 0;
  	$mr["Fondskoers"]        = 0;
	  $mr["Debet"]            = abs($data[45]);
	  $mr["Credit"]             = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];
	  $mr["Transactietype"]    = "";
	  if ($mr["Bedrag"] <> 0)
    {  
      //debug($controleBedrag, "RENME");
		  $output[] = $mr;
    }  
	}
  checkControleBedrag($controleBedrag,-1 * $data[17]);
}







/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_R()  //Rente of couponrente
{
  global $fonds, $data, $mr, $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;
  $mr["aktie"]              = "RENTE";
  do_algemeen();

//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
  $mr["Omschrijving"]      = $data[8];
  $mr["Grootboekrekening"] = "RENTE";
  $mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;

  if (strtoupper($data[15]) == "D")
  {
    $mr["Debet"]             = abs($data[17] - $data[47]);
    $mr["Credit"]            = 0;
    $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       -= $mr["Bedrag"];

  }
  else
  {
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17] + $data[47]);
    $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

  }
  $mr["Transactietype"]    = "";
  $mr["Verwerkt"]          = 0;
  $mr["Memoriaalboeking"]  = 0;


  $output[] = $mr;

  $mr["Grootboekrekening"] = "KNBA";
  $mr["Fonds"]             = "";
  $mr["Aantal"]            = 0;
  $mr["Fondskoers"]        = 0;
  $mr["Debet"]             = abs($data[47]);
  $mr["Credit"]            = 0;
  $mr["Bedrag"]            = -1 * $mr["Debet"];
  $controleBedrag       += $mr["Bedrag"];

  if ($mr["Bedrag"] <> 0)
    $output[] = $mr;


//////


  checkControleBedrag(abs($controleBedrag),$data[17]);

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
	
//  $mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Dividend ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "DIV";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();   
	$mr["Fonds"]             =  $fonds[Fonds];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	if (strtoupper($data[15]) == "D")
	{
    $mr["Debet"]             = abs($data[22] * $data[23]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[22] * $data[23]);
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;


	$mr["Grootboekrekening"] = "DIVBE";
	$mr["Valuta"]            = $data[2];
  $mr["Valutakoers"]       = _valutakoers();
	
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
  
	if (strtoupper($data[15]) == "D")
	{
	  $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[49]);
	  $mr["Bedrag"]            = $mr["Credit"];
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
	  $mr["Debet"]             = abs($data[49]);
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = -1 * $mr["Debet"];
    $controleBedrag       += $mr["Bedrag"];

	}
	if ($mr["Bedrag"] <> 0)
		$output[] = $mr;

//	$mr["Grootboekrekening"] = "KNBA";
//	$mr["Valuta"]            = $data[9];
//	$mr["Valutakoers"]       = _valutakoers();
//	
//	
//	$mr["Aantal"]            = 0;
//	$mr["Fondskoers"]        = 0;
//	if ($data[14] < 0)  // als veld negatief betreft een correctie Dividend
//	{
//	  $mr["Debet"]             = 0;
//	  $mr["Credit"]            = abs($data[11]);
//	  $mr["Bedrag"]            = $mr["Credit"];
//    $controleBedrag       += $mr["Bedrag"];
//
//	}
//	else
//	{
//	  $mr["Debet"]             = abs($data[11]);
//	  $mr["Credit"]            = 0;
//	  $mr["Bedrag"]            = -1 * $mr["Debet"];
//    $controleBedrag       += $mr["Bedrag"];
//
//	}
//  if ($mr["Bedrag"] <> 0)
//	  $output[] = $mr;



  checkControleBedrag(abs($controleBedrag),$data[17]);

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_VKSTO()  //Stockjes
{
  global $fonds;
  global $data;
  global $mr;
  global $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "OP";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "VKSTO";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  {
    $mr["aktie"]              = "ST";
    //$mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "VKSTO";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = $fonds["Fonds"];
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[17]);
  }





}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_RENOB()  //Coupon
{

  global $fonds, $data, $mr, $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;
	$mr["aktie"]              = "CP";
	do_algemeen();
	
  //$mr["Rekening"]          = getRekening($mr["Rekening"]);
	$mr["Omschrijving"]      = "Coupon ".$fonds["Omschrijving"];
	$mr["Grootboekrekening"] = "RENOB";
	$mr["Valuta"]            = $fonds["Valuta"];
	$mr["Valutakoers"]       = _valutakoers();   
	$mr["Fonds"]             =  $fonds[Fonds];
	$mr["Aantal"]            = 0;
	$mr["Fondskoers"]        = 0;
	if (strtoupper($data[15]) == "D")
	{
    $mr["Debet"]             = abs($data[22] * $data[23])/100;
	  $mr["Credit"]            = 0;
	  $mr["Bedrag"]            = _debetbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	else
	{
    $mr["Debet"]             = 0;
	  $mr["Credit"]            = abs($data[22] * $data[23])/100;
	  $mr["Bedrag"]            = _creditbedrag();
    $controleBedrag       += $mr["Bedrag"];

	}
	$mr["Transactietype"]    = "";
	$mr["Verwerkt"]          = 0;
	$mr["Memoriaalboeking"]  = 0;

	$output[] = $mr;

  checkControleBedrag(abs($controleBedrag),$data[17]);

}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////



function do_STUKMUT()  // Opname van geld of stukken
{
  global $fonds;
	global $data;
	global $mr;
	global $output;
	$mr = array();
  $controleBedrag = 0;

	do_algemeen();
  $mr["Rekening"] = substr($data[1],0,8)."MEM";
  
  if (strtoupper($data[15]) == "D")
  {
    	$mr[aktie]              = "OP";
      //$mr["Rekening"]          = getRekening($mr["Rekening"]);
      $mr["Omschrijving"]      = "Lichting ".$fonds[Omschrijving];
      $mr["Grootboekrekening"] = "FONDS";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers();
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = -1 * $data[22];
      $mr["Fondskoers"]        = $data[23];
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($mr["Aantal"]  * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            =  $mr["Credit"] * $mr["Valutakoers"];
      $mr["Transactietype"]    = "L";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 1;
      
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
  
      if ($mr["Bedrag"] <> 0)
      {
        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Fonds"]             = "";
        $mr["Aantal"]            = 0;
        $mr["Valuta"]            = "EUR";   // call 5135 stortr / onttr in EUR
        $mr["Valutakoers"]       = 1;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = abs($mr["Bedrag"]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * ($mr["Debet"]);
        $mr["Transactietype"]    = "";

        $output[] = $mr;
      }

      if ($data[45] > 0)  // meenemen opgelopen rente
      {
        $mr["Omschrijving"]      = "Lichting ".$fonds["Omschrijving"];
        $mr["Grootboekrekening"] = "RENOB";
        $mr["Fonds"]             = $fonds["Fonds"];
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Debet"]             = 0;
        $mr["Credit"]            = abs($data[45]);
        $mr["Bedrag"]            =  $mr["Credit"] * $mr["Valutakoers"];
        $mr["Transactietype"]    = "";
        $mr["Verwerkt"]          = 0;
        $mr["Memoriaalboeking"]  = 1;
        $controleBedrag       += $mr["Bedrag"];

        $output[] = $mr;

        $mr["Grootboekrekening"] = "ONTTR";
        $mr["Fonds"]             = "";
        $mr["Debet"]             = abs($data[45]);
        $mr["Credit"]            = 0;
        $mr["Bedrag"]            = -1 * ($mr["Debet"] *  $mr["Valutakoers"]);  
        $mr["Transactietype"]    = "";

        $output[] = $mr;
      }
    
      
  }
  else
  {
    	$mr["aktie"]              = "ST";
      //$mr["Rekening"]          = getRekening($mr["Rekening"]);
      $mr["Omschrijving"]      = "Deponering ".$fonds[Omschrijving];
      $mr["Grootboekrekening"] = "FONDS";
      $mr["Valuta"]            = $fonds["Valuta"];
      $mr["Valutakoers"]       = _valutakoers();
      $mr["Fonds"]             = $fonds["Fonds"];
      $mr["Aantal"]            = $data[22];
      $mr["Fondskoers"]        = $data[23];
      $mr["Credit"]            = 0;
      $mr["Debet"]             = abs($mr["Aantal"]  * $mr["Fondskoers"] * $fonds["Fondseenheid"]);
      $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];
      $mr["Transactietype"]    = "D";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 1;
      
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
  
      if ($mr["Bedrag"] <> 0)
      {
        $mr["Grootboekrekening"] = "STORT";
        $mr["Fonds"]             = "";
        $mr["Valuta"]              = "EUR";  // call 5135 stortr / onttr in EUR
        $mr["Valutakoers"]       = 1;
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Credit"]             = abs($mr["Bedrag"]);
        $mr["Debet"]            = 0;
        $mr["Bedrag"]            =  ($mr["Credit"] );
        $mr["Transactietype"]    = "";

        $output[] = $mr;
      }

      if ($data[45] > 0)  // meenemen opgelopen rente
      {
        $mr["Omschrijving"]      = "Deponering ".$fonds["Omschrijving"];
        $mr["Grootboekrekening"] = "RENME";
        $mr["Fonds"]             = $fonds["Fonds"];
        $mr["Aantal"]            = 0;
        $mr["Fondskoers"]        = 0;
        $mr["Credit"]             = 0;
        $mr["Debet"]            = abs($data[45]);
        $mr["Bedrag"]            = -1 * $mr["Debet"] * $mr["Valutakoers"];
        $mr["Transactietype"]    = "";
        $mr["Verwerkt"]          = 0;
        $mr["Memoriaalboeking"]  = 1;
        $controleBedrag       += $mr["Bedrag"];

        $output[] = $mr;

        $mr["Grootboekrekening"] = "STORT";
        $mr["Fonds"]             = "";
        $mr["Credit"]             = abs($data[45]);
        $mr["Debet"]            = 0;
        $mr["Bedrag"]            = ($mr["Credit"] *  $mr["Valutakoers"]);  
        $mr["Transactietype"]    = "";

        $output[] = $mr;
      }
  }

	checkControleBedrag(abs($controleBedrag),$data[17]);

      

	
   
}


/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_BEW()
{
	global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray;
	$mr = array();
	$controleBedrag = 0;


	do_algemeen();
	if ( strtoupper($data[15]) == "D" )
	{
		$mr["aktie"]              = "OP";
		$mr["Omschrijving"]      = $data[8];
		$mr["Grootboekrekening"] = "BEW";
		$mr["Valuta"]            = $data[2];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = "";
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = abs($data[17]);
		$mr["Bedrag"]            = _debetbedrag();
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
		$controleBedrag       += $mr["Bedrag"];
		$output[] = $mr;
		checkControleBedrag($controleBedrag,-1 * $data[17]);
	}
	else
	{
		$mr["aktie"]              = "ST";
		//$mr["Rekening"]          = getRekening($mr["Rekening"]);
		$mr["Omschrijving"]      = $data[8];
		$mr["Grootboekrekening"] = "BEW";
		$mr["Valuta"]            = $data[2];
		$mr["Valutakoers"]       = _valutakoers();
		$mr["Fonds"]             = "";
		$mr["Aantal"]            = 0;
		$mr["Fondskoers"]        = 0;
		$mr["Debet"]             = 0;
		$mr["Credit"]            = abs($data[17]);
		$mr["Bedrag"]            = _creditbedrag();
		$mr["Transactietype"]    = "";
		$mr["Verwerkt"]          = 0;
		$mr["Memoriaalboeking"]  = 0;
		$controleBedrag       += $mr["Bedrag"];
		$output[] = $mr;
		checkControleBedrag($controleBedrag,$data[17]);
	}
}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_BEH()
{
  global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	
	do_algemeen();
  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "OP";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "BEH";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
		checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  { 
    $mr["aktie"]              = "ST";
    //$mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "BEH";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[17]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_MARMUT()
{
  global $fonds, $afw;
  global $data;
  global $mr;
  global $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();

  $mr["Rekening"]   = ($data[4] == "MA")?substr($data[1],0,8)."MAR".$data[2]:substr($data[1],0,8).$data[2];


  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "MAOP";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];

    $output[] = $mr;
    checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  {
    $mr["aktie"]              = "MAST";
    //$mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "STORT";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];

    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[17]);
  }

}
/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////


function do_MUT()
{
  global $fonds, $afw;
	global $data;
	global $mr;
	global $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	
	do_algemeen();
  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "OP";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "ONTTR";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("GLDONTTR",$mr);
    $output[] = $mr;
		checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  { 
    $mr["aktie"]              = "ST";
    //$mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "STORT";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $mr = $afw->reWrite("GLDSTORT",$mr);
    $output[] = $mr;
		checkControleBedrag($controleBedrag,$data[17]);
  }

}


function do_KNBA()
{
  global $fonds;
  global $data;
  global $mr;
  global $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "OP";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = _debetbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  {
    $mr["aktie"]              = "ST";
    //$mr["Rekening"]          = getRekening($mr["Rekening"]);
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "KNBA";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = _creditbedrag();
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[17]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_FX()  // call 5220 was eerst do_kruis()
{
  global $fonds;
	global $data;
	global $mr;
	global $output,$meldArray;
	$mr = array();
  $controleBedrag = 0;

	// call 8414 2020-02-11
  // $data[10] is om koppels te maken

	do_algemeen();
	if ($data[24] != "EUR" AND $data[25] != "EUR")
  {
    $wisselkoers = $data[19]/$data[18];
    if ( strtoupper($data[15]) == "D" )
    {
      $mr["aktie"]              = "FX-VV";
      $mr["Omschrijving"]      = $data[8];
      $mr["Grootboekrekening"] = "KRUIS";
      $mr["Valuta"]            = $data[24];
      $mr["Valutakoers"]       = $wisselkoers;
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]             = 0;
      $mr["Debet"]             = abs($data[17]);
      $mr["Bedrag"]            = -1 * $mr["Debet"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
      checkControleBedrag($controleBedrag,-1 * $data[17]);
    }
    else
    {
      $mr["aktie"]              = "FX-VV";
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      $mr["Omschrijving"]      = $data[8];
      $mr["Grootboekrekening"] = "KRUIS";
      $mr["Valuta"]            = $data[24];
      $mr["Valutakoers"]       = $wisselkoers;
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[17]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
      checkControleBedrag($controleBedrag,$data[17]);
    }

  }
	else
  {
    if ( strtoupper($data[15]) == "D" )
    {
      $mr["aktie"]              = "FX";
      $mr["Omschrijving"]      = $data[8];
      $mr["Grootboekrekening"] = "KRUIS";
      $mr["Valuta"]            = $data[2];
      $mr["Valutakoers"]       = _valutakoersKruis();
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Credit"]             = 0;
      $mr["Debet"]             = abs($data[17]);
      $mr["Bedrag"]            = -1 * $mr["Debet"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
      checkControleBedrag($controleBedrag,-1 * $data[17]);
    }
    else
    {
      $mr["aktie"]              = "FX";
      $mr["Rekening"]          = getRekening($mr["Rekening"]);
      $mr["Omschrijving"]      = $data[8];
      $mr["Grootboekrekening"] = "KRUIS";
      $mr["Valuta"]            = $data[2];
      $mr["Valutakoers"]       = _valutakoersKruis();
      $mr["Fonds"]             = "";
      $mr["Aantal"]            = 0;
      $mr["Fondskoers"]        = 0;
      $mr["Debet"]             = 0;
      $mr["Credit"]            = abs($data[17]);
      $mr["Bedrag"]            = $mr["Credit"];
      $mr["Transactietype"]    = "";
      $mr["Verwerkt"]          = 0;
      $mr["Memoriaalboeking"]  = 0;
      $controleBedrag       += $mr["Bedrag"];
      $output[] = $mr;
      checkControleBedrag($controleBedrag,$data[17]);
    }
  }



}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////
function do_KRUIS()  // call 5220 w
{
  global $fonds;
  global $data;
  global $mr;
  global $output,$meldArray;
  $mr = array();
  $controleBedrag = 0;


  do_algemeen();
  if ( strtoupper($data[15]) == "D" )
  {
    $mr["aktie"]              = "KRUIS";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "KRUIS";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Credit"]             = 0;
    $mr["Debet"]             = abs($data[17]);
    $mr["Bedrag"]            = -1 * $mr["Debet"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,-1 * $data[17]);
  }
  else
  {
    $mr["aktie"]              = "KRUIS";
    $mr["Omschrijving"]      = $data[8];
    $mr["Grootboekrekening"] = "KRUIS";
    $mr["Valuta"]            = $data[2];
    $mr["Valutakoers"]       = _valutakoers();
    $mr["Fonds"]             = "";
    $mr["Aantal"]            = 0;
    $mr["Fondskoers"]        = 0;
    $mr["Debet"]             = 0;
    $mr["Credit"]            = abs($data[17]);
    $mr["Bedrag"]            = $mr["Credit"];
    $mr["Transactietype"]    = "";
    $mr["Verwerkt"]          = 0;
    $mr["Memoriaalboeking"]  = 0;
    $controleBedrag       += $mr["Bedrag"];
    $output[] = $mr;
    checkControleBedrag($controleBedrag,$data[17]);
  }
}

/////////////////////////////////////////////////////////////////////////////////
//
/////////////////////////////////////////////////////////////////////////////////

function do_NVT()
{
  global $meldArray, $data;
  $meldArray[] = "regel ".$data[0].":<b> met transactiecode ".trim($data[9])."-".trim($data[26])." overgeslagen</b>";
}


function do_error()
{
	global $do_func;
	echo "<BR>FOUT functie $do_func bestaat niet!";
}


?>