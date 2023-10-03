<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/03 08:21:43 $
 		File Versie					: $Revision: 1.2 $

 		$Log: orders2xls_RCN.php,v $
 		Revision 1.2  2018/12/03 08:21:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/12/01 19:48:44  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2018/10/24 15:59:19  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2018/10/14 12:37:03  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2018/10/14 11:11:55  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/10/14 10:08:09  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.17  2018/07/08 11:59:58  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/07/07 17:32:35  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/02/21 17:11:49  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/11/12 13:25:34  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/07/05 16:04:34  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/06/28 15:19:10  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/06/25 10:33:30  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/06/24 16:33:47  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/05/06 17:22:56  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/05/03 14:33:36  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/03/20 06:57:10  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2017/03/18 20:28:27  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2017/03/15 16:34:28  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2017/03/12 08:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2017/02/15 16:36:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/08/27 16:54:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/02/28 17:20:32  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/02/21 17:21:12  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/02/18 17:08:08  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/08/02 15:22:50  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/03/10 17:25:28  rvv
 		*** empty log message ***
 	
*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_xls.php");
require_once("../classes/AE_cls_pdfBase.php");
include_once("rapport/PDFRapport.php");
include_once("../config/ordersVars.php");

if (!$_GET["xls"])
{
  echo "foute aanroep";
  exit();
}

$ids = array();
foreach ($_POST as $key => $value)
{
  if (substr($key, 0, 3) == 'id_')
  {
    $ids[] = substr($key, 3);
  }
}
$extraWhere = '';

if (count($ids) > 0)
{
  $extraWhere .= " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
}
else
{
  echo "Er zijn geen orders geselecteerd. (" . implode(",", $ids) . ")";
  exit;
}

$fix=new AE_FIXtransport();
$db = new DB();
$query = "SELECT OrdersV2.depotbank, Depotbanken.orderLayout, OrdersV2.OrderSoort FROM OrdersV2 
         LEFT JOIN Depotbanken ON OrdersV2.depotbank=Depotbanken.depotbank WHERE 1 $extraWhere GROUP BY OrdersV2.depotbank,OrdersV2.OrderSoort ORDER BY OrdersV2.add_date";
$db->SQL($query);
$db->Query();
$depotbanken = array();
$layoutPerDepotbank = array();
$ordersoorten=array();
while ($data = $db->nextRecord())
{
  $depotbanken[$data['depotbank']] = $data['depotbank'];
  $layoutPerDepotbank[$data['depotbank']] = $data['orderLayout'];
  $ordersoorten[$data['OrderSoort']] = $data['OrderSoort'];
}

if(count($ordersoorten)>1 && isset($ordersoorten['N']))
{
  echo "Meerdere ordersoorten in selectie. (" . implode(",", $ordersoorten) . ").";
  exit;
}

if (count($depotbanken) > 1)
{
  echo "Meerdere depotbanken in selectie, excel uitvoer afgebroken. (" . implode(",", $depotbanken) . ")";
  exit;
}

if(isset($ordersoorten['N']))
{
  $aantalTxt='bedrag';
}
else
{
  $aantalTxt='aantal';
}

$cfg = new AE_config();
$volgNummer = $cfg->getData('tmpbulkorderlast');
$cfg->addItem('tmpbulkorderlast', ($volgNummer + 1));

$xls = new AE_xls('');
$xls->excelOpmaak['header'] = array('setAlign' => 'centre', 'setBgColor' => '22', 'setBorder' => '1');
$xls->excelOpmaak['kopl'] = array('setAlign' => 'left', 'setBold' => 1);
$xls->excelOpmaak['kopr'] = array('setAlign' => 'left');
$xls->excelOpmaak['aantal'] = array('setAlign' => 'right', 'setNumFormat' => 39);
$xls->excelOpmaak['totaal'] = array('setAlign' => 'left', 'setBold' => 1);
$xls->excelOpmaak['totaalAantal'] = array('setAlign' => 'right', 'setBold' => 1);
$pdf=new PDFRapport('L','mm');

$db->SQL("SELECT OrderRegelsV2.portefeuille FROM OrderRegelsV2
JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
WHERE 1 $extraWhere LIMIT 1");
$db->Query();
$portefeuille = $db->nextRecord();

loadLayoutSettings($pdf,$portefeuille['portefeuille']);
$pdf->marge=4;
$pdf->SetLeftMargin($pdf->marge);
$pdf->SetRightMargin($pdf->marge);
$pdf->SetTopMargin($pdf->marge);

$pdf->rapport_type='ORDERL';
$pdf->rapport_voettext='';
$pdf->SetFont('arial','',9);
$pdf->addPage();

$tmp=array_values($depotbanken);
$layout = $layoutPerDepotbank[$tmp[0]];


  $xls->setColumn[] = array(0, 0, 6);
  $xls->setColumn[] = array(1, 1, 9);
  $xls->setColumn[] = array(2, 2, 16);
  $xls->setColumn[] = array(3, 3, 4);
  $xls->setColumn[] = array(4, 4, 8);
  $xls->setColumn[] = array(5, 5, 25);
  $xls->setColumn[] = array(6, 6, 7);
  $xls->setColumn[] = array(7, 7, 8);
  $xls->setColumn[] = array(8, 8, 15);
  $xls->setColumn[] = array(9,9, 10);
  $xls->setColumn[] = array(10, 10, 8);
  $xls->setColumn[] = array(11, 11, 10);
  $xls->setColumn[] = array(12, 12, 12);
  $pdf->setWidths(array(14,20,32,8,16,50,14,16,30,20,16,20,26));


  $xlsData[] = array(array('Doorgegeven aan:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Nummer:', 'kopl'), array('', 'kopl'), array($volgNummer, 'kopl'), array('Datum', 'kopl'), array(date('d-m-Y'), 'kopl'));
  $xlsData[] = array(array('Opgegeven door:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Tijdstip', 'kopl'), array('', 'kopl'));
  $xlsData[] = array(array("regel", 'header'), array("portefeuille", 'header'),
    array("client", 'header'),
    array("tt", 'header'),
    array($aantalTxt, 'header'),
    array("fonds", 'header'),
    array("limiet", 'header'),
    array("controle", 'header'),
    array("ISINCode", 'header'),
    array("fondssoort", 'header'),
    array("Valuta", 'header'),
    array("Datum", 'header'),
    array("transactieType", 'header'),
    array("Beurs", 'header')
    
  );
  $pdf->setY(50);
  $widthsBackup=$pdf->widths;
  $pdf->setWidths(array(50,50,50,50,50));
  $pdf->setFont('arial','b',9);
  $pdf->row(array('Doorgegeven aan:','Nummer:',$volgNummer,'Datum',date('d-m-Y')));
  $pdf->row(array('Opgegeven door:','','','Tijdstip',''));
  $pdf->ln(2);
  $pdf->widths=$widthsBackup;
  $pdf->row(array('regel','portefeuille','client','tt',$aantalTxt,'fonds','limiet','controle','ISINCode','fondssoort','Valuta','Datum','transactieType'));
  $pdf->setFont('arial','',9);


$query = "SELECT OrderRegelsV2.positie,OrderRegelsV2.id as orderregelId,
OrderRegelsV2.portefeuille,
OrderRegelsV2.client,
OrdersV2.transactieSoort,
OrderRegelsV2.aantal,
OrderRegelsV2.bedrag,
OrdersV2.fondsOmschrijving,
OrdersV2.koersLimiet,
OrderRegelsV2.controleStatus,
OrdersV2.ISINCode,
OrdersV2.fondssoort,
OrdersV2.fondsValuta,
OrdersV2.id,
OrdersV2.tijdsLimiet,
OrdersV2.transactieType,
OrdersV2.orderStatus,
OrderRegelsV2.orderregelStatus,
OrdersV2.tijdsSoort,
OrdersV2.fonds,
OrdersV2.depotbank,
OrdersV2.memo,
OrdersV2.beurs,
Vermogensbeheerders.OrderCheckClientNaam 
FROM
OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
LEFT JOIN Portefeuilles ON OrderRegelsV2.portefeuille=Portefeuilles.Portefeuille
LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
  WHERE 1 $extraWhere ";


    $groupvelden = array('orderStatus', 'portefeuille');
    $query .= " ORDER BY orderStatus,OrderRegelsV2.id, OrderRegelsV2.portefeuille,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";


$db->SQL($query);
$db->query();
$dataArray=array();
while ($row = $db->nextRecord())
{
  $dataArray[$row['orderStatus']][$row['portefeuille']][$row['orderregelId']]=$row;
}

$totaal=0;
$lastValue=array();
$TGBdata[]=array('BUY/SELL','Stuks','Nominaal','BV Fondsnr','Rekening','','','Fixed','client','fondsnaam','isincode','fondsvaluta','limietdatum','limietkoers');
foreach($dataArray as $orderStatus=>$portefeuilleData)
{
  foreach($portefeuilleData as $portefeuille=>$rows)
  {
    foreach($rows as $row)
    {
      $crmNaam = getCrmNaam($row['portefeuille'], true);
      if ($crmNaam['naam'] <> '')
      {
        $row["client"] = $crmNaam['naam'];
      }
      if ($row['transactieType'] == 'B')
      {
        $row['koersLimiet'] = '';
      }
      if ($row['tijdsSoort'] == 'GTC')
      {
        $row['tijdsLimiet'] = 'Tot ann.';
      }
      if (isset($ordersoorten['N']))
      {
        $row["aantal"] = $row["bedrag"];
      }
  
  
      $totaal = checkSubTotaal($groupvelden, $lastValue, $row, $layout, $totaal);
      checkStatus($row, $lastValue);
      $xlsData[] = array($row['id'] . "-" . $row['positie'], $row['portefeuille'], $row["client"], $row['transactieSoort'], $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'], $row['tijdsLimiet'], $row['transactieType'],$row['beurs']);
      $pdf->row(array($row['id'] . "-" . $row['positie'], $row['portefeuille'], $row["client"], $row['transactieSoort'], $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'], $row['tijdsLimiet'], $row['transactieType']));
  
      foreach ($groupvelden as $veld)
      {
        $lastValue[$veld] = $row[$veld];
      }
      $totaal += $row["aantal"];
    }
  }
}


  $totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal,true);



function checkStatus($row,$lastValue)
{
  global $xlsData,$pdf,$__ORDERvar;

  if($row['orderStatus'] <> $lastValue['orderStatus'])
  {
    $xlsData[] = array(array($__ORDERvar['orderStatus'][$row['orderStatus']], 'kopl'));
    $widthsBackup= $pdf->widths;
    $pdf->setWidths(array(100));
    $pdf->setFont('arial','b',9);
    $pdf->row(array($__ORDERvar['orderStatus'][$row['orderStatus']]));
    $pdf->setFont('arial','',9);
    $pdf->widths=$widthsBackup;
  }
}

function checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal,$last=false)
{
  global $xlsData,$pdf;


  if(count($lastValue)>0)
  {
    foreach ($groupvelden as $veld)
    {
      if ($lastValue[$veld] != $row[$veld] || $last==true)
      {

     
         // $xlsData[] = array('', '', array('Totaal','totaal'), '',array($totaal,'totaalAantal'));
          $xlsData[] = array();
         // $pdf->row(array('','','Totaal','',$totaal));
          $pdf->ln();
          $totaal = 0;
          break;
      }
    }
  }
  return $totaal;
}

if($_GET["xls"]==2)
{
  $pdf->output("order.pdf", 'D');
}
else
{

  $xls->writetab('orders',$xlsData);
  /*
  if ($layout == 3)
  {
    $xls->excelOpmaak=array();
    $xls->setColumn=array();
    $xls->writetab('TGB', $TGBdata);
  }
  */
 // $xls->OutputXls();

  $xls->workbook->send('orders.xls');
  $xls->workbook->close();

}
?>