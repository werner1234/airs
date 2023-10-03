<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2014/12/24 09:54:51 $
File Versie					: $Revision: 1.4 $

$Log: advent_positie_convertFuncties.php,v $
Revision 1.4  2014/12/24 09:54:51  cvs
call 3105

Revision 1.3  2014/04/04 08:55:16  cvs
*** empty log message ***

Revision 1.2  2014/03/12 11:18:50  cvs
*** empty log message ***

Revision 1.1  2013/12/16 08:21:00  cvs
*** empty log message ***

Revision 1.4  2012/02/14 14:22:48  cvs
update 14-2-2012
uitvoer via TMP_571 tabel, om fractie telling mogelijk te maken

Revision 1.3  2011/11/11 12:55:06  cvs
veld ASof toeveogen, update 11-11-2011

Revision 1.2  2011/11/08 15:43:12  cvs
verschillende datum formaten, update 8 november 2011

Revision 1.1  2011/10/26 12:20:43  cvs
versie 1.00 eerste commit


*/

// datumFormat
// 1 = mmddjjjj
// 2 = mm/dd/jjjj
//

function cnvBedrag($txt)
{
	return number_format(str_replace(',','.',$txt),2,".","");
}

function str2num($in)
{
  return str_replace(',','.',$in);
}

function signBedrag($value,$sign)
{
  if ( strtoupper($sign) == "D" )
    return "-".$value;
  else
    return $value;
}

function kostenBedrag($wr,$kostenPost,$factor=1)
{
  $_r = $wr["kosten"];
  if (substr($_r,0,1) == "N")
  {
    $wr[$kostenPost."Valuta"] = substr($_r,1,3);
	  $wr[$kostenPost] = str2num(substr($_r,4))*-1*$factor;
  }
  else
  {
    $wr[$kostenPost."Valuta"] = substr($_r,0,3);
	  $wr[$kostenPost] = str2num(substr($_r,3))*$factor;
  }
  return $wr;
}

function convertMt940($record)
{
  global $datumFormat;
  $data = array();
  $dnx = -1;
  $_data = explode(chr(10),$record["txt"]);
  $wr = array();

  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    switch ($_r[0])
    {
      case "25":
        $wr["rekeningnr"] = intval($_r[1]);
        break;
      case "60F":
        $wr["valutaCode60"]  = substr($_r[1],7,3);
        $wr["oudSaldo60"]       = signBedrag(cnvBedrag(substr($_r[1],10)),substr($_r[1],0,1));
        break;
      case "61":
        $dnx++;
        if ($datumFormat == 2)
          $datum = substr($_r[1],6,2)."/".substr($_r[1],8,2)."/".substr($_r[1],0,2);
        else
          $datum = substr($_r[1],6,4)."20".substr($_r[1],0,2);

        $wr["mutatie"][$dnx]["boekdatum"]    = $datum;
        $wr["mutatie"][$dnx]["valutadatum"]  = $datum;
        $_tmp = explode("N",substr($_r[1],11));
        $wr["mutatie"][$dnx]["bedrag"]  = signBedrag(cnvBedrag($_tmp[0]),substr($_r[1],10,1));
        break;
      case "86":
        $wr["mutatie"][$dnx]["omschrijving"] = str_replace(chr(13)," ",$_r[1]);
        break;
      case "62F":
        $wr["valutacode62"] = substr($_r[1],7,3);
        $wr["nieuwSaldo62"]   = signBedrag(cnvBedrag(substr($_r[1],10)),substr($_r[1],0,1)) ;
        if ($datumFormat == 2)
          $datum = substr($_r[1],3,2)."/".substr($_r[1],5,2)."/".substr($_r[1],1,2);
        else
          $datum = substr($_r[1],3,4)."20".substr($_r[1],1,2);
        $wr["asOf"] = $datum;
        break;
    }
  }

  return $wr;  // geeft arrayset met deelrecords terug

}

function convertMt571($record)
{
  global $datumFormat;
  $db = new DB();
  $data = array();
  $dnx = -1;
  $_data = explode(chr(10),$record["txt"]);
  $wr = array();

  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
  {
    $_r = explode("&&",$_data[$subLoop]);
    $_tempRec[$_r[0]] = $_r[1];
    switch ($_r[0])
    {
      case "83a":
        $portefeuille = intval($_r[1]);
        break;
      case "67A":
        if ($datumFormat == 2)
          $datum = substr($_r[1],2,2)."/".substr($_r[1],4,2)."/".substr($_r[1],0,2);
        else
          $datum = substr($_r[1],2,4)."20".substr($_r[1],0,2);
        $asOf = $datum;
      case "35H":
        $dnx++;
        $wr[$dnx]["portefeuille"] = $portefeuille;
        $wr[$dnx]["asOf"] = $asOf;
        for($xx=0;$xx < strlen($_r[1]);$xx++)
        {
				  $_l = 	substr($_r[1],$xx,1);
				  if ($_l >= "0" AND $_l <= "9")
				    $wr[$dnx]["aantal"] .= $_l;
				  elseif ($_l == ",")
				    $wr[$dnx]["aantal"] .= ".";
			  }
        if (substr($_r[1],0,1) == "N") $wr[$dnx]["aantal"] = "-".$wr[$dnx]["aantal"];
        break;
      case "35B":
        $fondArray = explode(chr(13),trim(substr($_r[1],17)));
        $wr[$dnx]["fondsnaam"] = $fondArray[0];
        $wr[$dnx]["aabcode"]   = trim(substr($_r[1],3,14));
        $query = "SELECT ISINCode FROM Fondsen WHERE ABRCode='".$wr[$dnx]["aabcode"]."' OR AABCode='".$wr[$dnx]["aabcode"]."'";
        if (!$isinRec = $db->lookupRecordByQuery($query))
        {
          $wr[$dnx]["isincode"] = "AABcode onbekend";
          printStatus("<font color='Maroon'><b>FOUT: geen fondsRecord gevonden bij (".$wr[$dnx]["aabcode"].") </b></font>");
        }
        else
        {
          if ($isinRec["ISINCode"] <> "")
          {
            $wr[$dnx]["isincode"] = substr($isinRec["ISINCode"],0,12);
          }
          else
          {
            $wr[$dnx]["isincode"] = "Geen ISIN";
          }
        }
        break;
    }
  }

  return $wr;  // geeft arrayset met deelrecords terug

}

function convertMt554($record)  //554 records
{
	global $data, $datumFormat;
  $db = new DB();
	$_data = explode(chr(10),$record[txt]);
	$wr = array();
	for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
	{
		$_r = explode("&&",$_data[$subLoop]);
		$_tempRec[$_r[0]] = $_r[1];
		switch ($_r[0])
		{
			case "23":
			  $wr["transactie-id"] = str_replace(chr(13),"",$_r[1]);

        if ($datumFormat == 2)
          $datum = substr($_r[1],2,2)."/".substr($_r[1],4,2)."/".substr($_r[1],0,2);
        else
          $datum = substr($_r[1],2,4)."20".substr($_r[1],0,2);
			  $wr["transactiedatum"] = $datum;
			  break;
			case "53a":
			  $wr["rekeningnr"] = intval($_r[1]);
			  break;
			case "83a":
			  $wr["portefeuille"] = intval($_r[1]);
		    break;
			case "72":
        if (trim($wr["transactie-code"]) <> "") break;
			  $tmp = explode("/",$_r[1]);
			  $wr["transactie-code"] = $tmp[0];
			  $wr["fondsnaam"] = trim($tmp[1]);
			  break;
			case "35A":
        if (trim($_r[1]) == "") break;  // skip als regel leeg is..
			  for($xx=0;$xx < strlen($_r[1]);$xx++)
			  {
				  $_l = 	substr($_r[1],$xx,1);
				  if ($_l >= "0" AND $_l <= "9")
				    $wr["aantal"] .= $_l;
				  elseif ($_l == ",")
				    $wr["aantal"] .= ".";
			  }
			  break;
			case "35B":
        if (trim($_r[1]) == "") break;
        //$wr["aabcode"]   = trim(substr($_r[1],3,14));
        $fondArray = explode(chr(13),trim(substr($_r[1],17)));
        $wr["fondsnaam"] = $fondArray[0];
        $wr["aabcode"]   = trim(substr($_r[1],3,14));
        $query = "SELECT ISINCode FROM Fondsen WHERE AABCode='".$wr["aabcode"]."'";
        if (!$isinRec = $db->lookupRecordByQuery($query))
        {
              $wr["isincode"] = "AABcode onbekend";
              printStatus("<font color='Maroon'><b>FOUT: geen fondsRecord gevonden bij (".$wr["aabcode"].") </b></font>");
        }
        else
        {
          if ($isinRec["ISINCode"] <> "")
          {
            $wr["isincode"] = $isinRec["ISINCode"];
          }
          else
          {
            $wr["isincode"] = "Geen ISIN";
          }
        }
        //listarray();
			  break;
			case "35U":
        if (trim($_r[1]) == "") break;
        $wr["fondsvaluta"]  = substr($_r[1],0,3);
        $wr["prijsPerStuk"] = cnvBedrag(substr($_r[1],3));
			  break;
			case "36":
        if (trim($_r[1]) == "") break;
			  $wr["valutakoers"] = cnvBedrag($_r[1]);
		    break;
			case "34A":
        if (trim($_r[1]) == "") break;
        $wr["valutadatum"]    = substr($_r[1],0,8);
		    if (substr($_r[1],8,1) == "N")
				{
				  $wr["rekeningValuta"]  = substr($_r[1],9,3);
				  $wr["notaBedrag"]      = cnvBedrag(substr($_r[1],12))*-1;
          $factor = -1;
				}
		    else
		    {
 		      $wr["rekeningValuta"]  = substr($_r[1],8,3);
					$wr["notaBedrag"]      = cnvBedrag(substr($_r[1],11));
          $factor = 1;
				}
			  break;
			case "32G":
			   $wr["kosten"] = $_r[1];
			   break;
			case "71C":
			   $_kosten = trim($_r[1]);
         if ($_kosten == "15")   $wr = kostenBedrag($wr,"gekochteRente",$factor) ;
         if ($_kosten == "17")   $wr = kostenBedrag($wr,"dividendBelasting");
         if ($_kosten == "16" OR $_kosten == "18")  $wr = kostenBedrag($wr,"transactieKosten");
         if ($_kosten == "19")   $wr = kostenBedrag($wr,"kostenCorrespondent");


		}
	}
  unset($wr["kosten"]);
	return $wr;

}

?>