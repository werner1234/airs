<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/29 09:42:49 $
 		File Versie					: $Revision: 1.5 $

 		$Log: millogicExport.php,v $
 		Revision 1.5  2019/07/29 09:42:49  cvs
 		call 7816
 		
 		Revision 1.4  2019/07/03 13:48:10  cvs
 		call 7816
 		
 		Revision 1.3  2019/05/20 11:21:55  cvs
 		call 7816
 		
 		Revision 1.2  2019/05/20 09:34:27  cvs
 		call 7816
 		
 		Revision 1.1  2017/09/20 06:25:12  cvs
 		megaupdate 2722
 		

*/

error_reporting(E_ALL);
include_once("wwwvars.php");
include_once("../classes/AIRS_cls_millogicExport.php");
include_once("../classes/AE_cls_lookup.php");
session_start();

$db = new DB();

$export = new millogicExport();

include_once("convertFuncties.php");

$mt940_skip2= Array(
  "  CONT.DIV. ",
  "  CASH DIVIDEND  ");

// omschrijving begint met
$mt940_skip = Array(
  "KOOP ",
  "VERKOOP ",
  "DIV",
  "COUPON",
  "ANN.KOOP",
  "ANN.VERKOOP",
  "LOSSING",
  "OPHEFFING  ",
  "HERBELEGG. " ,
  "STORTING  ");



$output = "";


$row = 1;
$ndx=0;
$dataSet = Array();
$row = Array();
$_tempRow = Array();

$query = "
  SELECT 
    * 
  FROM 
    TijdelijkeRekeningmutaties 
  WHERE 
    TijdelijkeRekeningmutaties.change_user = '$USR' 
  ";

$db->executeQuery($query);
if ($db->records() == 0)
{
  echo "export afgebroken, geen items in tijdelijke tabel";
  exit;
}
$volgnr = 0;
while ($rec = $db->nextRecord())
{
  $volgnr++;
  $rPara = $export->getRekeningParameters($rec["Rekening"]);

  if ($rec["bankTransactieCode"] == "") // geldmutaties
  {
    $rowB = $row["mutatie"][$y];

    $skipRow = false;
    for ($xx=0; $xx < count($mt940_skip);$xx++)
    {
      $arrValue = $mt940_skip[$xx];
      $arrLen = strlen($arrValue);
      if (substr(strtoupper($rec["Omschrijving"]),0,$arrLen) == $arrValue)
      {
        $skipRow = true;
      }
    }
    for ($xx=0; $xx < count($mt940_skip2);$xx++)
    {
      $arrValue = $mt940_skip2[$xx];
      if (stristr($rec["Omschrijving"],$arrValue))
      {
        $skipRow = true;
      }
    }

    if ($skipRow) continue;
    $fixedRow = array();
//debug($rec);
    $export->pushValue2Row($export->formatText2Text(substr(str_replace("-","",$rec["Boekdatum"]),0,8),8),2);
    $export->pushValue2Row($export->formatbedrag2Text($volgnr,7,0) ,10);
    $export->pushValue2Row($export->formatText2Text("",2),17);  //systeem
    $export->pushValue2Row($export->formatText2Text("",9),19);  //klant
    $export->pushValue2Row($export->formatText2Text("",9),19);  //klant
    $export->pushValue2Row($export->formatText2Text($rPara["clientsoort"],2),28);
    $export->pushValue2Row($export->formatText2Text("00",2),30);
    $export->pushValue2Row($export->formatbedrag2Text($rPara["rekening"],11,0),32);
    $export->pushValue2Row($export->formatText2Text("",52),43);
    $export->pushValue2Row($export->formatText2Text($rec["Valuta"],3),95);
    $export->pushValue2Row($export->formatText2Text($rec["Omschrijving"],60),98);
    $export->pushValue2Row($export->formatbedrag2Text(0,15,0),158);
    $export->pushValue2Row($export->formatbedrag2Text(0,15,0),173);
    $export->pushValue2Row($export->formatbedrag2Text(99999,5,0),188);
    $debcre = ( $rec["Bedrag"] < 0 )?"D":"C";
    $export->pushValue2Row($export->formatText2Text($debcre,1),193);
    $export->pushValue2Row($export->formatbedrag2Text(abs($rec["Bedrag"]),17,3),194);
    $export->pushValue2Row($export->formatText2Text($rec["Valuta"],3),211);
    $export->pushValue2Row($export->formatbedrag2Text(99999,5,0),214);
    $export->pushValue2Row($export->formatbedrag2Text(substr($rec["Omschrijving"],60,40),40),221);
    $export->pushValue2Row("0",276);
    $export->pushValue2Row("|",277);
    $export->pushValue2Row(substr($rec["Omschrijving"],100),278);
//    $export->pushValue2Row(substr($rec["OmschrijvingOrg"],50),278);
  }
  else                                  // effectenmutaties
  {
    $fPara   = $export->getFondsParameters($rec["Fonds"]);
    $mapping = $export->getFondsMapping($transactieCode, $fPara);
    $export->pushValue2Row($export->formatText2Text(substr(str_replace("-","",$rec["Boekdatum"]),0,8),8),2);
    $export->pushValue2Row($export->formatbedrag2Text($volgnr,7,0) ,10);
    $export->pushValue2Row($export->formatText2Text("",2),17);  //systeem
    $export->pushValue2Row($export->formatText2Text("",9),19);  //klant
    $export->pushValue2Row($export->formatText2Text($rPara["clientsoort"],2),28);
    $export->pushValue2Row($export->formatText2Text("00",2),30);
    $export->pushValue2Row($export->formatbedrag2Text($rPara["rekening"],11,0),32);
    $export->pushValue2Row($export->formatText2Text("",52),43);
    $export->pushValue2Row($export->formatText2Text($rec["Valuta"],3),95);
    $export->pushValue2Row($export->formatText2Text($mapping["omschrijving"],60),98);
    $isin = ($fPara["ISINCode"] <> "")?"*".substr($fPara["ISINCode"],0,2):"*??";
    $export->pushValue2Row($export->formatText2Text($isin,3),155);
    $export->pushValue2Row($export->formatbedrag2Text($rec["Aantal"],15,4),158);
    $export->pushValue2Row($export->formatbedrag2Text($rec["Fondskoers"],15,4),173);
    $export->pushValue2Row($export->formatbedrag2Text($mapping["dienstsoort"],5,0),188);
    $debcre = ( $rec["Bedrag"] < 0 )?"D":"C";
    $export->pushValue2Row($export->formattext2Text($debcre,1),193);
    $export->pushValue2Row($export->formatbedrag2Text(abs($rec["Bedrag"]),17,3),194);
    $export->pushValue2Row($export->formatText2Text($rec["Valuta"],3),211);
    $export->pushValue2Row($export->formatbedrag2Text(1,5,0),214);
    $export->pushValue2Row($export->formatbedrag2Text(substr($rowB["omschrijving"],60,40),40),221);

    $export->pushValue2Row($fPara["ISINCode"],261);
    $export->pushValue2Row("1",276);
    $export->pushValue2Row("|",277);
    $export->pushValue2Row(substr($rec["Omschrijving"],100),278);

  }
  $export->pushRowToOutput();

}

$file = "mill_".date("Ymd_His")."_mutatie.txt";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename='. $file);
header('Pragma: no-cache');
header("Expires: 0");
echo implode("\r\n",$export->outputArray);
