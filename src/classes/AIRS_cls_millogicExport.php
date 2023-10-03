<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: AIRS_cls_millogicExport.php,v $
 		Revision 1.2  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2017/09/20 06:11:45  cvs
 		megaupdate 2722
 		
 		Revision 1.5  2016/03/16 12:53:16  cvs
 		call 4124
 		
 		Revision 1.4  2014/05/02 08:45:55  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/04 09:03:41  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2014/02/05 15:29:46  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2013/12/11 10:09:14  cvs
 		*** empty log message ***



*/


class millogicExport
{
  var $fixedRow;
  var $outputArray = array();
  var $rowLength = 275;
  var $depotbank = "";

  function millogicExport()
  {
    $this->clearFixedRow();
  }

  function clearFixedRow()
  {
    $this->fixedRow = array_fill(0, $this->rowLength," ");  // lege regel van goede lengte aanmaken
  }

  function pushValue2Row($value,$start,$length=0)
  {
    if ($length == 0) $length = strlen($value);
    for ($x = 0; $x < $length; $x++)
    {
      $idx = $x + ($start - 1);
      $this->fixedRow[$idx] = $value[$x];
    }
  }

  function pushRowToOutput()
  {
    $this->outputArray[] = implode("",$this->fixedRow);
    $this->clearFixedRow();
  }

  function getRekeningParameters($rekening)
  {
    $db = new DB();
    $rPara = array();
    $rPara["rekening"] = substr($rekening, 0, -3);
    $query = "
    SELECT
      Rekeningen.Depotbank,
      Rekeningen.Rekening,
      IFNULL(millogic_rekeningen.rekening, '') AS mRekening,
      IFNULL(millogic_rekeningen.nietParticulier, 0) AS nietParticulier,
      IFNULL(millogic_rekeningen.rekeningZonderKosten, 0) AS rekeningZonderKosten
    FROM
      Rekeningen
    LEFT JOIN millogic_rekeningen ON 
      Rekeningen.Rekening = millogic_rekeningen.rekening
    WHERE 
      Rekeningen.consolidatie = 0 AND
      Rekeningen.Rekening ='".$rekening."' 
    ";
    $rec = $db->lookupRecordByQuery($query);

    $rPara["clientsoort"]  = ($rec["nietParticulier"] != 1)?"01":"05";
    $rPara["zonderkosten"] = ($rec["rekeningZonderKosten"] == 1);
    if ($this->depotbank == "")
    {
      $this->depotbank = $rec["Depotbank"];
    }

    return $rPara;
  }

  function getFondsParameters($fonds)
  {
    $db = new DB();
    $query = "
      SELECT
        Fondsen.Fonds,
        Fondsen.Omschrijving,
        Fondsen.ISINCode,
        Fondsen.Valuta,
        IFNULL(millogic_fondsparameters.isShare,0) AS isShare,
        IFNULL(millogic_fondsparameters.nlFonds,0) AS nlFonds
      FROM
        Fondsen
      LEFT JOIN millogic_fondsparameters ON 
        Fondsen.Fonds = millogic_fondsparameters.fonds
      WHERE
        Fondsen.Fonds = '".$fonds."'
     ";

    $fPara = $db->lookupRecordByQuery($query);

    return $fPara;
  }

  function getFondsMapping($transactieCode, $fPara)
  {
    $db = new DB();
    $query = "
    SELECT 
      * 
    FROM 
      millogic_transactieMapping
    WHERE 
      depotbank = '".$this->depotbank."' AND 
      bankcode = '".$transactieCode."' ";

    $transRec     = $db->lookupRecordByQuery($query);
    $omschrijving = $transRec["omschrijving"]." ".$fPara["Fonds"];
    $dienstsoort  = ($transRec["Millogic"] > 0)?$transRec["Millogic"]:"99999";

      switch ($dienstsoort)
      {
        case "00010":
          if ($fPara["nlFonds"] == 1 AND $fPara["isShare"]  == 0)  $dienstsoort = "00030";
          if ($fPara["nlFonds"] == 1 AND $fPara["isShare"]  == 1)  $dienstsoort = "00010";
          if ($fPara["nlFonds"] == 0 AND $fPara["isShare"]  == 0)  $dienstsoort = "00040";
          if ($fPara["nlFonds"] == 0 AND $fPara["isShare"]  == 1)  $dienstsoort = "00020";
          break;
        case "00015":
          if ($fPara["nlFonds"] == 1 AND $fPara["isShare"]  == 0)  $dienstsoort = "00035";
          if ($fPara["nlFonds"] == 1 AND $fPara["isShare"]  == 1)  $dienstsoort = "00015";
          if ($fPara["nlFonds"] == 0 AND $fPara["isShare"]  == 0)  $dienstsoort = "00045";
          if ($fPara["nlFonds"] == 0 AND $fPara["isShare"]  == 1)  $dienstsoort = "00025";
          break;
        case "00100":
          if ($fPara["nlFonds"] == 0 )  $dienstsoort = "00110";
          break;
        case "00150":
          if ($fPara["nlFonds"] == 0 )  $dienstsoort = "00160";
          break;

        default:

    }
    return array(
      "dienstsoort"  => $dienstsoort,
      "omschrijving" => $omschrijving
    );
  }


  function formatbedrag2Text($bedrag, $length=15, $decimals=0)
  {
    $parts = explode(".",$bedrag);
    return substr(str_repeat("0",$length).$parts[0],-1*($length-$decimals)).
      substr($parts[1].str_repeat("0",$decimals),0,$decimals);
  }

  function formatText2Text($tekst, $length=5)
  {
    return substr($tekst.str_repeat(" ",$length),0,$length);
  }
  
///////////////////////////////////////////////
/// 

 

  function fixedRowOutput()
  {
    

    for ($x=0 ;$x < count($this->fixedRow); $x++)
    {
      $out .= $this->fixedRow[$x];
    }
    return $out;
  }




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
    $db = new DB();
    for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
    {
      $_r = explode("&&",$_data[$subLoop]);
      $_tempRec[$_r[0]] = $_r[1];
      switch ($_r[0])
      {
        case "20":
          $parts = explode("/",$_r[1]);
          $wr["volgnr"] = $parts[1];
          break;
        case "25":
          $wr["rekeningnr"] = intval($_r[1]);
          $query = "SELECT * FROM rekeningen WHERE nummer ='".$wr["rekeningnr"]."' ";
          $lkupRec = $db->lookupRecordByQuery($query);
          $wr["clientsoort"] = ($lkupRec["nummer"] == $wr["rekeningnr"])?"05":"01";
          break;
        case "60F":
          $wr["valutaCode60"]  = substr($_r[1],7,3);
          $wr["oudSaldo60"]       = signBedrag(cnvBedrag(substr($_r[1],10)),substr($_r[1],0,1));
          break;
        case "61":
          $dnx++;

          $datum = substr($_r[1],0,2).substr($_r[1],6,4);

          $wr["mutatie"][$dnx]["boekdatum"]    = $datum;
          $wr["mutatie"][$dnx]["valutadatum"]  = $datum;
          $_tmp = explode("N",substr($_r[1],11));
          $wr["mutatie"][$dnx]["bedrag"]  = signBedrag(cnvBedrag($_tmp[0]),substr($_r[1],10,1));
          break;
        case "86":
          // $wr["mutatie"][$dnx]["omschrijving"] = str_replace(chr(13)," ",$_r[1]);

          /* SEPA aanpassing */

          $wr["mutatie"][$dnx][omschrijving] = "";
          $parts = explode(chr(13),$_r[1]);
          if (stristr($parts[0],"sepa"))            // als eerste regel 1 sepa bevat dan omschrijving vanaf regel 2
          {
            $wr["mutatie"][$dnx][omschrijving] = substr($parts[0],(strpos($parts[0],"/BIC/")+5))." ";
            for ($reb=1; $reb < count($parts);$reb++)
            {
              $wr["mutatie"][$dnx][omschrijving] .= $parts[$reb]." ";
            }
          }
          else
          {
            $wr["mutatie"][$dnx][omschrijving] = str_replace(chr(13)," ",$_r[1]);
          }
          $wr["mutatie"][$dnx][omschrijving] = str_replace("SEPA INCASSO ALGEMEEN DOORLOPEND","",$wr["mutatie"][$dnx][omschrijving]);

          break;
        case "62F":
          $wr["valutacode62"] = substr($_r[1],7,3);
          $wr["nieuwSaldo62"]   = signBedrag(cnvBedrag(substr($_r[1],10)),substr($_r[1],0,1)) ;
          if ($datumFormat == 2)
            $datum = substr($_r[1],3,2)."/".substr($_r[1],5,2)."/".substr($_r[1],1,2);
          else
            $datum = substr($_r[1],3,4).substr($_r[1],1,2);
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
            $datum = substr($_r[1],2,4).substr($_r[1],0,2);
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
          $query = "SELECT ISINCode FROM Fondsen WHERE AABCode='".$wr[$dnx]["aabcode"]."'";
          $isinRec = $db->lookupRecordByQuery($query);
          if ($isinRec["ISINCode"] <> "")
          {
            $wr[$dnx]["isincode"] = $isinRec["ISINCode"];
          }
          else
          {
            $wr[$dnx]["isincode"] = "Geen ISIN";
            printStatus("<font color='Maroon'><b>FOUT: geen ISINcode bij (".$wr[$dnx]["aabcode"].") ".$wr[$dnx]["fondsnaam"]."</b></font>");
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
          $datum = substr($_r[1],0,6);
          $wr["transactiedatum"] = $datum;
          $_r[1] = str_replace(chr(13),"",$_r[1]);
          $wr["volgnr"] = substr($_r[1],6);
          break;
        case "53a":
          $wr["rekeningnr"] = intval($_r[1]);
          $query = "SELECT * FROM rekeningen WHERE nummer ='".$wr["rekeningnr"]."' ";
          $lkupRec = $db->lookupRecordByQuery($query);
          $wr["clientsoort"] = ($lkupRec["nummer"] == $wr["rekeningnr"])?"05":"01";
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
          $wr["fondsnaam"] = substr($fondArray[0],1);
          $wr["isincode"]   = trim(substr($_r[1],3,14));
          $query = "SELECT * FROM Fondsen WHERE AABCode='".$wr["isincode"]."'";
          if ($isinRec = $db->lookupRecordByQuery($query))
          {
            $wr["binnenland"] = $isinRec["binnenland"];
            $wr["aandeel"] = $isinRec["aandeel"];
            $wr["skipMapping"] = false;
          }
          else
          {
            $wr["skipMapping"] = true;
            printStatus("Fonds niet gevonden in Fondstabel AAB code = ".$wr["isincode"]." / ".$wr["fondsnaam"]);
          }
          $wr["isincode"] =   $isinRec["ISINCode"];
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


}
?>