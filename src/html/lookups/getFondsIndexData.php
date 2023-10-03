<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/02/06 09:59:46 $
 		File Versie					: $Revision: 1.6 $

 		$Log: getFondsIndexData.php,v $
 		Revision 1.6  2019/02/06 09:59:46  cvs
 		call 7166
 		
 		Revision 1.5  2018/07/24 06:41:13  cvs
 		call 7041
 		
 		Revision 1.4  2017/01/12 15:13:35  cvs
 		call 4830 eerste commit
 		
 		Revision 1.3  2016/11/21 12:47:51  cvs
 		call 3856
 		
 		Revision 1.2  2016/09/30 06:36:13  cvs
 		call 4848: derde bestand Kasbankl
 		
 		Revision 1.1  2016/09/02 13:39:33  cvs
 		no message
 		


*/
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
include_once("../../classes/AE_cls_formatter.php");
include_once("../indexBerekening.php");
require("../../config/checkLoggedIn.php");
if (strlen(trim($_POST["fonds"])) < 2)
{
  exit;
}

$DB = new DB();
$json = new AE_Json();


if ($_POST["soort"] == "fonds")
{
  $fondsRec = $DB->lookupRecordByQuery("SELECT * FROM Fondsen WHERE Fonds='".$_POST["fonds"]."'");

  $_SESSION["htmlATT"]["fonds"] = $_POST["fonds"];
  $perf = getPerformance($_POST["fonds"],$_POST["start"],$_POST["stop"]);
  $ind = 0;
  $cumPrev = 1;

  foreach ($perf as $k=>$v)
  {
    $cumMulti = (  ( 100 + $v ) / 100 ) * $cumPrev ;
    $cum = ( $cumMulti  - 1 ) * 100;
    $cumPrev = $cumMulti;
    $out[] = array("label"=> $k, "x"=> $ind, "y" => round($v,2));
    $outCum[] = array("label"=> $k, "x"=> $ind, "y" => round($cum,2) );
    $ind++;

  }
  echo $json->json_encode(array($out,$outCum,$fondsRec["Omschrijving"]));
}
else
{
//  debug($_POST);
  $portefeuille = $_POST["fonds"];
  $rapportDatum = $_POST["stop"];
  $rapportStart = $_POST["start"];

  include_once("../rapportRekenClass.php");
  include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
  include_once($__appvar["basedir"]."/html/indexBerekening.php");
  $db = new DB();
  $portRec = $db->lookupRecordByQuery("SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'");

  $query = "SELECT Vermogensbeheerders.PerformanceBerekening	".
    " FROM Portefeuilles JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuilles.Portefeuille = '".$portefeuille."'";
  $db->SQL($query);
  $db->Query();
  $vdata = $db->nextRecord();


  $index = new indexHerberekening();
  $julstart = d2j($_POST["start"]);
  $julstop  = d2j($_POST["stop"]);
  $kwartalen = _mkDatums($index->getKwartalen($julstart,$julstop));
  $jaren     = _mkDatums($index->getJaren($julstart,$julstop));

  $indexData = $index->getWaarden( $rapportStart,$rapportDatum ,$portefeuille);
//  debug($indexData);
  $cumPerfArray = array();
  $ind = 0;
  $out = array(array("label"=> 0, "x"=> 0, "y" => 0));
  $outCum = array(array("label"=> 0, "x"=> 0, "y" => 0));
  foreach($indexData as $item)
  {
    $ind++;
    $v = $item["performance"];
    $k = $item["datum"];
    $cum = $item["index"]-100;
    $out[] = array("label"=> $k, "x"=> $ind, "y" => round($v,2));
    $outCum[] = array("label"=> $k, "x"=> $ind, "y" => round($cum,2) );

  }
  echo $json->json_encode(array($out,$outCum,"port: $portefeuille"));
}


exit;

function d2j($dbdate="")
{
  if($dbdate == "")
  {
    return -1;
  }
  else
  {
    $p = explode("-",$dbdate);
  }

  return mktime(0,0,0,$p[1],$p[2],$p[0]);
}

function _mkDatums($in)
{
  foreach($in as $item)
  {
    $out[] = $item["stop"];
  }
  return $out;
}

function getPerformance($fonds,$vanaf,$tot)
{


  $fmt = new AE_cls_formatter();
  $att = new indexHerberekening();


//  debug($_POST);





  switch ($_POST["interval"])
  {
    case "btnKwartaal":
      $maanden = $att->getKwartalen(db2jul($vanaf),db2jul($tot));
      break;
    case "btnJaar":
      $maanden = $att->getJaren(db2jul($vanaf),db2jul($tot));
      break;
    default:
      $maanden = $att->getMaanden(db2jul($vanaf),db2jul($tot));
      break;
  }

  $januari=substr($tot,0,4)."-01-01";

  $totalPerf = 100;
  //debug($maanden);
  $started = false;
  foreach($maanden as $maand)
  {
    $indexData =array(
                       'fondsKoers_eind'=>getFondsKoers($fonds,$maand['stop']),
                       'fondsKoers_begin'=>getFondsKoers($fonds,$maand['start'])
                      );

    $out["begin"] = 0;
    if ($maand["stop"] == $_POST["grafdate"] OR $_POST["grafdate"] == "")
    {
      $started = true;
    }
    $indexKey = $fmt->format("@D{form}",$maand['stop']);
    if ($started)
    {
      $perf = ($indexData['fondsKoers_eind'] - $indexData['fondsKoers_begin']) / (($indexData['fondsKoers_begin']/100) );

//      $totalPerf = ($totalPerf* (100+$perf)/100);
//      $out[$fmt->format("@D{form}",$maand['stop'])] = round($totalPerf-100,2);
      $totalPerf = ($totalPerf* (100+$perf)/100);
      $out[$indexKey] = round(($perf),2);
    }
    else
    {
      $out[$indexKey] = 0;
    }

    //echo "m $fonds ".($jaarPerf-$voorPerf)." <br>\n";
  }

  return $out;
}

function getFondsKoers($fonds,$datum)
{
  $db=new DB();
  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc";
  $koers=$db->lookupRecordByQuery($query);
  return $koers['Koers'];
}

?>