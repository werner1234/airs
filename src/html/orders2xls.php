<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/29 13:40:46 $
 		File Versie					: $Revision: 1.37 $

 		$Log: orders2xls.php,v $
 		Revision 1.37  2020/05/29 13:40:46  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2020/05/23 16:37:07  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2020/05/23 16:36:21  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2020/05/21 07:49:19  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2020/04/22 15:39:13  rvv
 		*** empty log message ***

 		Revision 1.31  2020/04/20 10:28:05  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2020/04/19 06:36:04  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2020/04/18 17:04:33  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/10/30 17:05:57  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2019/03/29 06:54:19  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/03/28 14:22:31  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/03/27 16:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/03/10 14:09:00  rvv
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
include_once("../classes/AE_cls_FIXtransport.php");

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
$db2 = new DB();
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

$meerVoudigInSelectie=(isset($ordersoorten['N']) || isset($ordersoorten['O']));
$alleenMeervoudig=(isset($ordersoorten['N']) && isset($ordersoorten['O']) && count($ordersoorten)==2);

if((count($ordersoorten)>1 && $meerVoudigInSelectie) && !$alleenMeervoudig)
{
  echo "Meerdere ordersoorten in selectie. (" . implode(",", $ordersoorten) . ").";
  exit;
}

if (count($depotbanken) > 1)
{
  echo "Meerdere depotbanken in selectie, excel uitvoer afgebroken. (" . implode(",", $depotbanken) . ")";
  exit;
}

if(isset($ordersoorten['N'])||isset($ordersoorten['O']))
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
$xls->excelOpmaak['kop2'] = array('setAlign' => 'left','setSize'=>'12','setBold' => 1);
$xls->excelOpmaak['kopr'] = array('setAlign' => 'left');
$xls->excelOpmaak['aantal'] = array('setAlign' => 'right', 'setNumFormat' => 39);
$xls->excelOpmaak['totaal'] = array('setAlign' => 'left', 'setBold' => 1);
$xls->excelOpmaak['totaalAantal'] = array('setAlign' => 'right', 'setBold' => 1);
$pdf=new PDFRapport('L','mm');

$db->SQL("SELECT OrderRegelsV2.portefeuille,
 Vermogensbeheerders.Naam as VermNaam,
Vermogensbeheerders.Adres as VermAdres,
Vermogensbeheerders.Woonplaats as VermWoonplaats,
Vermogensbeheerders.Telefoon as VermTelefoon,
Vermogensbeheerders.Email as VermEmail
 FROM OrderRegelsV2
JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
LEFT JOIN Portefeuilles ON  OrderRegelsV2.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
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

//echo $layout;exit;
//$layout=9;
if ($layout == 1)//0 = Default / 3 = TGB (als het goed is) / 7 = Engels Default / 4 = CACEIS
{
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
}
elseif ($layout == 2)
{
  $xls->setColumn[] = array(0, 0, 7 );
  $xls->setColumn[] = array(1, 1, 20);
  $xls->setColumn[] = array(2, 2, 7 );
  $xls->setColumn[] = array(3, 3, 7 );
  $xls->setColumn[] = array(4, 4, 15);
  $xls->setColumn[] = array(5, 5, 25);
  $xls->setColumn[] = array(6, 6, 15);
  $xls->setColumn[] = array(7, 7, 10);
  $xls->setColumn[] = array(8, 8, 10);
  $xls->setColumn[] = array(9, 9, 10);
  $xls->setColumn[] = array(10,10,13);
  $xls->setColumn[] = array(11,11,15);
  $pdf->setWidths(array(14,40,14,14,25,50,30,18,18,18,18,26));
  $pdf->setAligns(array('L','L','L','L','R','L','L','L','L','L','L','L'));
}
elseif ($layout == 4) //CACEIS
{
  $xls->setColumn[] = array(0, 0, 7 );
  $xls->setColumn[] = array(1, 1, 10);
  $xls->setColumn[] = array(2, 2, 20);
  $xls->setColumn[] = array(3, 3, 20);
  $xls->setColumn[] = array(4, 4, 20);
  $xls->setColumn[] = array(5, 5, 20);
  $xls->setColumn[] = array(6, 6, 15);
  $xls->setColumn[] = array(7, 7, 15);
  $xls->setColumn[] = array(8, 8, 15);
  $xls->setColumn[] = array(9, 9, 15);
  $xls->setColumn[] = array(10,10, 15);
  $xls->setColumn[] = array(11,11, 16);
  $pdf->setWidths(array(20,20,30,40,30,20,22,22,22,22,26));
  $pdf->setAligns(array('R','R','L','L','L','R','R','L','L','L','L'));
}
elseif ($layout == 5) //Kasbank
{
  $xls->setColumn[] = array(0, 1, 12);
  $xls->setColumn[] = array(1, 2, 20);
  $xls->setColumn[] = array(2, 0, 25);
  $xls->setColumn[] = array(3, 1, 10);
  $xls->setColumn[] = array(4, 2, 20);
  $xls->setColumn[] = array(5, 3, 15);
  $xls->setColumn[] = array(6, 4, 12);
  $xls->setColumn[] = array(7, 5, 12);
  $xls->setColumn[] = array(8, 6, 12);
  $xls->setColumn[] = array(9, 7, 12);
  $pdf->setWidths(array(25,25,50,15,35,35,25,25,25,25));
  $pdf->setAligns(array('L','L','L','L','R','L','L','L','L','R'));
}
elseif ($layout == 6) //Puilaetco
{
  $xls->setColumn[] = array(0, 0, 15);
  $xls->setColumn[] = array(1, 1, 15);
  $xls->setColumn[] = array(2, 2, 12);
  $xls->setColumn[] = array(3, 3, 20);
  $xls->setColumn[] = array(4, 4, 20);
  $xls->setColumn[] = array(5, 5, 15);
  $xls->setColumn[] = array(6, 6, 12);
  $xls->setColumn[] = array(7, 7, 12);
  $xls->setColumn[] = array(8, 8, 12);
  $xls->setColumn[] = array(9, 9, 12);
  $pdf->setWidths(array(25,25,20,45,40,30,25,25,25,25));
  $pdf->setAligns(array('L','L','L','L','R','L','L','L','L','R'));
}
elseif ($layout == 8) //KBC
{

  $pdf->setWidths(array(25,25,20,45,40,30,25,25,25,25));
  $pdf->setAligns(array('L','L','L','L','R','L','L','L','L','R'));
}
elseif ($layout == 9) //AAB
{
  $xls->setColumn[] = array(0, 0, 6);
  $xls->setColumn[] = array(1, 1, 9);
  $xls->setColumn[] = array(2, 2, 16);
  $xls->setColumn[] = array(3, 3, 16);
  $xls->setColumn[] = array(4, 4, 4);
  $xls->setColumn[] = array(5, 5, 8);
  $xls->setColumn[] = array(6, 6, 25);
  $xls->setColumn[] = array(7, 7, 7);
  $xls->setColumn[] = array(8, 8, 8);
  $xls->setColumn[] = array(9, 9, 15);
  $xls->setColumn[] = array(10,10, 10);
  $xls->setColumn[] = array(11, 11, 10);
  $xls->setColumn[] = array(12, 12, 10);
  $xls->setColumn[] = array(13, 13, 10);
  $xls->setColumn[] = array(14, 14, 12);
  $pdf->setWidths(array(14,20,18,32,8,16,45,14,16,30,20,16,20,22));
}
else //layout 0 en 3 en 7 en 8
{
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
}


if ($layout == 1) //SNS
{
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
    array("transactieType", 'header')
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

}
elseif ($layout == 2) //UBP
{
  $xlsData[] = array(array('', 'kopl'), array('', 'kopl'), array('', 'kopl'),array('', 'kopl'),array('', 'kopl'),array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'),  array('Date', 'kopl'), array(date('d-m-Y'), 'kopl'));
  $xlsData[] = array(array('Orderid:', 'header'),
    array("Client name", 'header'),
    array("Bank", 'header'),
    array('Trade', 'header'),
    array("Amount/Number", 'header'),
    array("Security name", 'header'),
    array("ISIN Nr.", 'header'),
    array("Limit", 'header'),
    array("Valid until", 'header'),
    array("Trade date", 'header'),
    array("Execution price", 'header'),
    array("Remarks", 'header')
  );
  $pdf->setY(50);
  $pdf->setFont('arial','b',9);
  $pdf->row(array('','','','','','','','','','','Date:',date('d-m-Y')));
  $pdf->ln(2);
  $pdf->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
  $pdf->row(array('Orderid','Client name','Bank','Trade',"Amount/\nNumber",'Security name','ISIN Nr','Limit','Valid until','Trade date','Execution price','Remarks'));
  $pdf->setFont('arial','',9);
}
elseif ($layout == 4) //???
{
  $xlsData[] = array(array('', 'kopl'), array('', 'kopl'), array('', 'kopl'),array('', 'kopl'),array('', 'kopl'),array('', 'kopl'),array('', 'kopl'),array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Date', 'kopl'), array(date('d-m-Y'), 'kopl'));
  $xlsData[] = array(array('Position', 'header'),
    array("Order number", 'header'),
    array("Portefeuille", 'header'),
    array("Fund description", 'header'),
    array('ISIN-code', 'header'),
    array('Memo', 'header'),
    array("Currency", 'header'),
    array("Number/Nominal", 'header'),
    array("Transaction type", 'header'),
    array("market / limit", 'header'),
    array("limit price", 'header'),
    array("valid until", 'header')
  );
  $pdf->setY(50);
  $pdf->setFont('arial','b',9);
  $pdf->row(array('','','','','','','','','','Date:',date('d-m-Y')));
  $pdf->ln(2);
  $pdf->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
  $pdf->row(array('Position','Order number','Portefeuille','Fund description','ISIN-code',"Currency",'Number/Nominal','Transaction type','market / limit','limit price','valid until'));
  $pdf->setFont('arial','',9);
}
elseif ($layout == 5) //Kasbank
{
  $xlsData[] = array(array('', 'kopl'), array('', 'kopl'), array('Date', 'kop2'), array(' '.date('d-m-Y'), 'kop2'));
  $xlsData[] = array(array('EXCHANGE MIC Code', 'header'),array('ISIN', 'header'),array('SECURITY NAME', 'header'),
    array("SIDE", 'header'),
    array("ORDER QUANTITY", 'header'),
    array("ORDERTYPE", 'header'),
    array('LIMIT', 'header'),
    array('CURRENCY', 'header'),
    array("VALIDITY", 'header'),
    array("ACCOUNT", 'header')
    //					"
    //"
  );
  $pdf->setY(50);
  $pdf->setFont('arial','b',9);
  $pdf->row(array('','','','','','','Date:',date('d-m-Y')));
  $pdf->ln(2);
  $pdf->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
  $pdf->row(array('EXCHANGE MIC','ISIN','SECURITY NAME','SIDE','ORDER QUANTITY','ORDERTYPE','LIMIT',"CURRENCY",'VALIDITY','ACCOUNT'));
  $pdf->setFont('arial','',9);
}
elseif ($layout == 6) //Puilaetco
{
  $xlsData[] = array(array('Equities Orders Details', 'kopl'));
  $xlsData[] = array(array('Type of Order', 'header'),array('Quantity or Amount', 'header'),array('Currency Deal', 'header'),
    array("ISIN Code", 'header'),
    array("Securities Account", 'header'),
    array("Currency Cash account", 'header'),
    array('Modality', 'header'),
    array('Price', 'header'),
    array("Validity", 'header'),
    array("Reference", 'header')
    //					"
    //"
  );
  $pdf->setY(50);
  $pdf->setFont('arial','b',9);
  $pdf->row(array('Equities Orders Details','','','','','','Date:',date('d-m-Y')));
  $pdf->ln(2);
  $pdf->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
  $pdf->row(array('Type','Quantity','Currency','ISIN','Account','Currency cash','Modality',"Price",'Validity','Reference'));
  $pdf->setFont('arial','',9);
}
elseif ($layout == 8) //KBC
{
  
  $header=array();
  $header[]=array(array('Managing firm', 'kopl'), '','',array('Contact person', 'kopl'),'','',array('Comments', 'kopl'));
  $header[]=array(array($portefeuille['VermNaam'], 'kopl'), '','', array('', 'kopl'));
  $header[]=array(array($portefeuille['VermAdres'], 'kopl'), '','', array('', 'kopl'));
  $header[]=array(array($portefeuille['VermWoonplaats'], 'kopl'));
  $header[]=array(array($portefeuille['VermTelefoon'].' '.$portefeuille['VermEmail'], 'kopl'));
  
  $xlsData = $header;
  $xlsDataKBCOptie = $header;
  $xlsData[] = array(array('Account', 'header'),array('Buy/Sell', 'header'),array('Quantity', 'header'),
    array("Currency", 'header'),array("Ticker / Description", 'header'),array("ISIN", 'header'),array('Market', 'header'),array('Type', 'header'),
    array("Limit", 'header'),array("Stop price", 'header'),array("Validity", 'header'),array("Accounting Currency", 'header'));
  $xlsDataKBCOptie[] = array(array('Account', 'header'),array('Combination/Single', 'header'),array('Open/Close', 'header'),array('Buy/Sell', 'header'),array('Quantity', 'header'),
    array("Call/Put", 'header'),array("Ticker", 'header'), array("Expiry", 'header'), array('Strike', 'header'), array('Type', 'header'),
    array("Price", 'header'), array("Debit / Credit", 'header'),array("Validity", 'header'),array("Accounting Currency", 'header'));
  $pdf->setY(50);
  $pdf->setFont('arial','b',9);
  $pdf->row(array('','','','','','','Date:',date('d-m-Y')));
  $pdf->ln(2);
  $pdf->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U','R'));
  $pdf->row(array('Account','Buy/Sell','Quantity','Currency','Ticker / Description','ISIN','Market',"Type",'Limit','Stop price','Validity','Accounting Currency'));
  $pdf->setFont('arial','',9);
}
elseif($layout==9) //AAB
{
  $xlsData[] = array(array('Doorgegeven aan:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Nummer:', 'kopl'), array('', 'kopl'), array($volgNummer, 'kopl'), array('Datum', 'kopl'), array(date('d-m-Y'), 'kopl'));
  $xlsData[] = array(array('Opgegeven door:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Tijdstip', 'kopl'), array('', 'kopl'));
  $xlsData[] = array(array("regel", 'header'), array("portefeuille", 'header'), array("rekening", 'header'), array("client", 'header'), array("tt", 'header'), array($aantalTxt, 'header'),
    array("fonds", 'header'), array("limiet", 'header'), array("controle", 'header'), array("ISINCode", 'header'),array("bankCode", 'header'), array("fondssoort", 'header'), array("Valuta", 'header'), array("Datum", 'header'), array("transactieType", 'header'));
  $pdf->setY(50);
  $widthsBackup=$pdf->widths;
  $pdf->setWidths(array(50,50,50,50,50));
  $pdf->setFont('arial','b',9);
  $pdf->row(array('Doorgegeven aan:', 'Nummer:', $volgNummer, 'Datum', date('d-m-Y')));
  $pdf->row(array('Opgegeven door:', '', '', 'Tijdstip', ''));
  $pdf->ln(2);
  $pdf->widths = $widthsBackup;
  $pdf->row(array('regel', 'portefeuille','rekening', 'client', 'tt', $aantalTxt, 'fonds', 'limiet', 'controle', 'ISINCode', 'fondssoort', 'Valuta', 'Datum', 'trans.Type'));
  $pdf->setFont('arial', '', 9);
}
else //layout 0 en 3 en 7
{
  if($layout==7)
  {
    if(isset($ordersoorten['N'])||isset($ordersoorten['O']))
      $aantalTxt='Value';
    else
      $aantalTxt='Quantity';
    $xlsData[] = array(array('To:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Number:', 'kopl'), array('', 'kopl'), array($volgNummer, 'kopl'), array('Date', 'kopl'), array(date('d-m-Y'), 'kopl'));
    $xlsData[] = array(array('From:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Time', 'kopl'), array('', 'kopl'));
    $xlsData[] = array(array("line", 'header'), array("Client account", 'header'),      array("Client name", 'header'),      array("Buy/Sell", 'header'),      array($aantalTxt, 'header'),array("Name Security", 'header'),array("limit price", 'header'),array("Check", 'header'),array("ISINCode", 'header'),array("Assetclass", 'header'),array("Currency", 'header'),array("Date till", 'header'),array("Order type", 'header'),array('stock exchange','header'));
  }
  else
  {
    $xlsData[] = array(array('Doorgegeven aan:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Nummer:', 'kopl'), array('', 'kopl'), array($volgNummer, 'kopl'), array('Datum', 'kopl'), array(date('d-m-Y'), 'kopl'));
    $xlsData[] = array(array('Opgegeven door:', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('', 'kopl'), array('Tijdstip', 'kopl'), array('', 'kopl'));
    $xlsData[] = array(array("regel", 'header'), array("portefeuille", 'header'), array("client", 'header'), array("tt", 'header'), array($aantalTxt, 'header'), array("fonds", 'header'), array("limiet", 'header'), array("controle", 'header'), array("ISINCode", 'header'), array("fondssoort", 'header'), array("Valuta", 'header'), array("Datum", 'header'), array("transactieType", 'header'));
  }
  $pdf->setY(50);
  $widthsBackup=$pdf->widths;
  $pdf->setWidths(array(50,50,50,50,50));
  $pdf->setFont('arial','b',9);
  if($layout==7)
  {
    $pdf->row(array('To:', 'Number:', $volgNummer, 'Date', date('d-m-Y')));
    $pdf->row(array('From:', '', '', 'Time', ''));
    $pdf->ln(2);
    $pdf->widths = $widthsBackup;
    $pdf->row(array('Line', 'client account', 'client name', 'Buy/Sell', $aantalTxt, 'Name Security', 'Limit', 'Check', 'ISINCode', 'Assetclass', 'Currency', 'Date till', 'Order type'));
    $pdf->setFont('arial', '', 9);
  }
  else
  {
    $pdf->row(array('Doorgegeven aan:', 'Nummer:', $volgNummer, 'Datum', date('d-m-Y')));
    $pdf->row(array('Opgegeven door:', '', '', 'Tijdstip', ''));
    $pdf->ln(2);
    $pdf->widths = $widthsBackup;
    $pdf->row(array('regel', 'portefeuille', 'client', 'tt', $aantalTxt, 'fonds', 'limiet', 'controle', 'ISINCode', 'fondssoort', 'Valuta', 'Datum', 'transactieType'));
    $pdf->setFont('arial', '', 9);
  }
}

$query = "SELECT OrderRegelsV2.positie,
OrderRegelsV2.portefeuille,
OrderRegelsV2.client,
OrdersV2.transactieSoort,
OrderRegelsV2.aantal,
OrderRegelsV2.bedrag,
OrderRegelsV2.rekening,
OrdersV2.fondsOmschrijving,
OrdersV2.koersLimiet,
OrderRegelsV2.controleStatus,
OrdersV2.ISINCode,
OrdersV2.beurs,
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
OrdersV2.optieSymbool,
OrdersV2.optieType,
OrdersV2.optieUitoefenprijs,
OrdersV2.optieExpDatum,
Vermogensbeheerders.OrderCheckClientNaam
FROM
OrderRegelsV2
INNER JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id
LEFT JOIN Portefeuilles ON OrderRegelsV2.portefeuille=Portefeuilles.Portefeuille
LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
  WHERE 1 $extraWhere ";

if ($layout == 1)
{
  if($_GET["xls"]==3)
  {
    $groupvelden=array('orderStatus','portefeuille');
    $query .= " ORDER BY orderStatus,portefeuille,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";//ORDER BY fondssoort,fondsOmschrijving,transactieSoort
  }
  else
  {
    $groupvelden=array('orderStatus','fonds','transactieSoort','transactieType','koersLimiet');
    $query .= " ORDER BY orderStatus,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";//ORDER BY fondssoort,fondsOmschrijving,transactieSoort
  }
}
elseif ($layout == 2)
{
  $groupvelden=array();
  if($_GET["xls"]==3)
    $query .= " ORDER BY orderStatus,portefeuille,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";//ORDER BY fondssoort,fondsOmschrijving,transactieSoort
  else
    $query .= " ORDER BY orderStatus,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";//ORDER BY fondssoort,fondsOmschrijving,transactieSoort
}
else
{
  if($_GET["xls"]==3)
  {
    $groupvelden = array('orderStatus', 'portefeuille');
    $query .= " ORDER BY orderStatus,portefeuille,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";
  }
  else
  {
    $groupvelden = array('orderStatus', 'fonds', 'transactieSoort', 'transactieType');
    $query .= " ORDER BY orderStatus,OrdersV2.fonds,transactieSoort,transactieType,koersLimiet,positie";
  }
}

$db->SQL($query);
$db->query();

$totaal=0;
$lastValue=array();
$TGBdata[]=array('BUY/SELL','Stuks','Nominaal','BV Fondsnr','Rekening','','','Fixed','client','fondsnaam','isincode','fondsvaluta','limietdatum','limietkoers');
$KASdata[]=array('EXCHANGE MIC','ISIN','SIDE','ORDER QUANTITY','ORDERTYPE','LIMIT',"CURRENCY",'VALIDITY','ACCOUNT','SECURITY NAME','ORDER ID');
while ($row = $db->nextRecord())
{
  $crmNaam = getCrmNaam($row['portefeuille'],true);
  
  if ($crmNaam['naam'] <> '')
  {
    $row["client"] = $crmNaam['naam'];
  }
  if($row['transactieType']=='B')
    $row['koersLimiet']='';
  if($row['tijdsSoort']=='GTC')
    $row['tijdsLimiet']='Tot ann.';

  if(isset($ordersoorten['N'])||isset($ordersoorten['O']))
  {
    $row["aantal"]=$row["bedrag"];
  }

  if ($layout == 1)
  {
    $totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    checkStatus($row,$lastValue);
    $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    $xlsData[] = array($row['id']."-".$row['positie'], $row['portefeuille'], $row["client"], $row['transactieSoort'], $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$row['transactieType']);
    $pdf->row(array($row['id']."-".$row['positie'], $row['portefeuille'], $row["client"], $row['transactieSoort'], $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$row['transactieType']));
  }
  elseif ($layout == 2)
  {
   //$totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
   // checkStatus($row,$lastValue);
    if($row['transactieSoort']=='A')
      $trade='Buy';
    elseif($row['transactieSoort']=='V')
      $trade='Sell';
    else
      $trade=$row['transactieSoort'];

    if($row['transactieType']=='B')
      $limiet='at market';
    else
     $limiet=$row['koersLimiet'];


    $row["client"]=getOrderName($row['portefeuille'],$row['OrderCheckClientNaam']);
    
    if(db2jul($row['tijdsLimiet']) >2)
    {

      $datumLimietPdf = date('d-m-y', db2jul($row['tijdsLimiet']));
      $datumLimietXls=$row['tijdsLimiet'];
    }
    else
    {
      $datumLimietPdf='';
      $datumLimietXls='';
    }
    $xlsData[] = array($row['id']."-".$row['positie'],$row["client"], $row['depotbank'],$trade, $row["aantal"], $row['fondsOmschrijving'], $row['ISINCode'], $limiet,$datumLimietXls,'','',$row['memo']);
    $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'),);
    $pdf->row(array($row['id']."-".$row['positie'],$row["client"], $row['depotbank'],$trade, number_format($row["aantal"],2,'.','\''), $row['fondsOmschrijving'], $row['ISINCode'], $limiet,$datumLimietPdf,'','',$row['memo']));
  }
  elseif ($layout == 4)
  {
    $totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    //checkStatus($row,$lastValue);
    $transactieSoorten=array('A'=>'Buy','V'=>'Sell','AO'=>'Open Buy','AS'=>'Close Buy','VO'=>'Open Sell','VS'=>'Close Sell');
    
    if(isset($transactieSoorten[$row['transactieSoort']]))
      $trade=$transactieSoorten[$row['transactieSoort']];
    else
      $trade=$row['transactieSoort'];
  
    if($row['transactieType']=='B')
      $limiet='at market';
    else
      $limiet=$row['koersLimiet'];
  
    if($row['transactieType']=='B')
      $transactieType='Market';
    else
      $transactieType='Limit';
    
    $row["client"]=getOrderName($row['portefeuille'],$row['OrderCheckClientNaam']);
  
    if(db2jul($row['tijdsLimiet']) >2)
    {
      $datumLimietPdf = date('d-m-y', db2jul($row['tijdsLimiet']));
      $datumLimietXls=$row['tijdsLimiet'];
    }
    else
    {
      $datumLimietPdf='';
      $datumLimietXls='';
    }
    $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    $xlsData[] = array($row['positie'],$row['id'],$row["portefeuille"],$row['fondsOmschrijving'], $row['ISINCode'],'',$row['fondsValuta'],$row["aantal"],$trade, $transactieType,$limiet,$datumLimietXls);
    $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'),);
    $pdf->row(array($row['positie'],$row['id'],$row["portefeuille"],$row['fondsOmschrijving'], $row['ISINCode'],$row['fondsValuta'],number_format($row["aantal"],2,'.','\''),$trade,$transactieType, $limiet,$datumLimietPdf));
  }
  elseif ($layout == 5)
  {

    //$totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    //checkStatus($row,$lastValue);
    $transactieSoorten=array('A'=>'buy','V'=>'sell','AO'=>'open buy','AS'=>'close buy','VO'=>'open sell','VS'=>'close sell');

    if(isset($transactieSoorten[$row['transactieSoort']]))
      $trade=$transactieSoorten[$row['transactieSoort']];
    else
      $trade=$row['transactieSoort'];

    if($row['transactieType']=='B')
      $limiet='at market';
    else
      $limiet=$row['koersLimiet'];

    if($row['transactieType']=='B')
      $transactieType='market';
    else
      $transactieType='limit';

    $row["client"]=getOrderName($row['portefeuille'],$row['OrderCheckClientNaam']);

    if(db2jul($row['tijdsLimiet']) >2)
    {
      $datumLimietPdf = date('d-m-y', db2jul($row['tijdsLimiet']));
      $datumLimietXls=$row['tijdsLimiet'];
    }
    else
    {
      $datumLimietPdf=$row['tijdsSoort'];
      $datumLimietXls=$row['tijdsSoort'];
    }
    $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    $xlsData[] = array($row['beurs'],$row['ISINCode'],$row['fondsOmschrijving'],$trade,$row["aantal"],$transactieType, $row['koersLimiet'],$row['fondsValuta'],$datumLimietXls,$row['portefeuille']);
    if($row['id']<>$lastId)
    {
      $n++;
    }
    $lastId=$row['id'];
    $aantal=$KASdata[$n][3]+$row["aantal"];
    $KASdata[$n] = array($row['beurs'],$row['ISINCode'],$trade,$aantal,$transactieType, $row['koersLimiet'],$row['fondsValuta'],$datumLimietXls,'',$row['fondsOmschrijving'],$row['id']);

    $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'),);
    $pdf->row(array($row['beurs'],$row['ISINCode'],$row['fondsOmschrijving'],$trade, $row["aantal"],$transactieType,$row['koersLimiet'],$row['fondsValuta'],$datumLimietPdf,$row['portefeuille']));

  }
  elseif ($layout == 6)
  {

    //$totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    //checkStatus($row,$lastValue);
    $transactieSoorten=array('A'=>'BUY','V'=>'SELL','AO'=>'OPEN BUY','AS'=>'CLOSE BUY','VO'=>'OPEN SELL','VS'=>'CLOSE SELL');

    $query="SELECT valuta FROM Rekeningen WHERE rekening='".$row['rekening']."'";
    $db2->SQL($query);
    $rekValuta=$db2->lookupRecord();

    if(isset($transactieSoorten[$row['transactieSoort']]))
      $type=$transactieSoorten[$row['transactieSoort']];
    else
      $type=$row['transactieSoort'];

    if($row['transactieType']=='B')
      $limiet='';
    else
      $limiet=$row['koersLimiet'];
    if($limiet==0)
      $limiet='';

    if($row['transactieType']=='B')
      $transactieType='MKT';
    else
      $transactieType='LIM';

    $row["client"]=getOrderName($row['portefeuille'],$row['OrderCheckClientNaam']);

    if(db2jul($row['tijdsLimiet']) >2)
    {
      $datumLimietPdf = date('d-m-Y', db2jul($row['tijdsLimiet']));
      $datumLimietXls=$row['tijdsLimiet'];
    }
    else
    {
      $datumLimietPdf=$row['tijdsSoort'];
      $datumLimietXls=$row['tijdsSoort'];
    }
    $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    $xlsData[] = array($type,$row["aantal"],$row['fondsValuta'],$row['ISINCode'],$row['portefeuille'],$rekValuta['valuta'],$transactieType,$limiet,$datumLimietXls,$__appvar["bedrijf"].$row['id']);
    if($row['id']<>$lastId)
    {
      $n++;
    }
    $lastId=$row['id'];
    $aantal=$KASdata[$n][4]+$row["aantal"];

    $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'),);
    $pdf->row(array($type,$row["aantal"],$row['fondsValuta'],$row['ISINCode'],$row['portefeuille'],$rekValuta['valuta'],$transactieType,$limiet,$datumLimietPdf,$__appvar["bedrijf"].$row['id']));

  }
  elseif ($layout == 8)
  {
  
    //$totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    //checkStatus($row,$lastValue);
    $transactieSoorten=array('A'=>'buy','V'=>'sell','AO'=>'open buy','AS'=>'close buy','VO'=>'open sell','VS'=>'close sell');
  
    if(isset($transactieSoorten[$row['transactieSoort']]))
      $trade=$transactieSoorten[$row['transactieSoort']];
    else
      $trade=$row['transactieSoort'];
  
    if($row['transactieType']=='B')
      $limiet='';
    else
      $limiet=$row['koersLimiet'];
  
    if($row['transactieType']=='B')
      $transactieType='market';
    else
      $transactieType='limit';
  
    $row["client"]=getOrderName($row['portefeuille'],$row['OrderCheckClientNaam']);
  
    if(db2jul($row['tijdsLimiet']) >2)
    {
      $datumLimietPdf = date('d-m-y', db2jul($row['tijdsLimiet']));
      $datumLimietXls=$row['tijdsLimiet'];
    }
    else
    {
      $datumLimietPdf=$row['tijdsSoort'];
      $datumLimietXls=$row['tijdsSoort'];
    }
    if($row['tijdsSoort']=='GTC')
      $datumLimietXls='GTC';
  
    $parts=explode(" ",$trade);
    $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    if($row['fondssoort']=='OPT')
      $xlsDataKBCOptie[] =  array($row['portefeuille'],'single',$parts[0],$trade,$row["aantal"],$row['optieType'],$row['optieSymbool'],$row['optieExpDatum'],$row['optieUitoefenprijs'],$transactieType,$limiet,'',$datumLimietXls,'');
    else
      $xlsData[] = array($row['portefeuille'],$trade,$row["aantal"],$row['fondsValuta'],$row['fondsOmschrijving'],$row['ISINCode'],$row['beurs'],$transactieType,$limiet,'',$datumLimietXls,'');// $trade,$row["aantal"], ,$row['fondsValuta'],$datumLimietXls);
    if($row['id']<>$lastId)
    {
      $n++;
    }
    $lastId=$row['id'];


    $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'));
    $pdf->row(array($row['portefeuille'],$trade,$row["aantal"],$row['fondsValuta'],$row['fondsOmschrijving'],$row['ISINCode'],$row['beurs'],$transactieType,$limiet,'',$datumLimietPdf,''));
  
  }
  elseif ($layout == 9)
  {
    $trade=$row['transactieSoort'];
    $totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    checkStatus($row,$lastValue);
    $bankCode=  $fix->getFondscode($row["depotbank"],$row["fonds"]);
    $rekening=substr($row['rekening'],0,-3);
    $xlsData[] = array($row['id']."-".$row['positie'], $row['portefeuille'],$rekening, $row["client"], $trade, $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'],$bankCode, $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$transactieType);
    $pdf->row(array($row['id']."-".$row['positie'], $row['portefeuille'],$rekening, $row["client"],$trade, $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$transactieType));
  
  }
  else
  {
    if($layout==3)
    {
      if(substr($row['transactieSoort'],0,1)=='A')
        $trade='Buy';
      elseif(substr($row['transactieSoort'],0,1)=='V')
        $trade='Sell';
      else
        $trade=$row['transactieSoort'];
      $bankCode=  $fix->getFondscode($row["depotbank"],$row["fonds"]);
      $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
      $TGBdata[] = array(strtoupper($trade), ($row["bedrag"] <> 0?'':$row["aantal"]), ($row["bedrag"]<>0?$row["bedrag"]:''), $bankCode, $row['portefeuille'], '', '', 'X', $row["client"], $row['fondsOmschrijving'], $row['ISINCode'], $row['fondsValuta'], $row['tijdsLimiet'], $row['koersLimiet']);
    }
    else
    {
      $trade=$row['transactieSoort'];
      $beurs='';
      if($layout==7)
      {
        if(substr($row['transactieSoort'],0,1)=='A')
          $trade='Buy';
        elseif(substr($row['transactieSoort'],0,1)=='V')
          $trade='Sell';
  
        if($row['tijdsLimiet']=='Tot ann.')
          $row['tijdsLimiet']='GTC';
  
        if($row['transactieType']=='B')
          $transactieType='Market';
        elseif($row['transactieType']=='Limiet')
          $transactieType='Limit';
        else
          $transactieType=$row['transactieType'];
        
        $beurs=$row['beurs'];
      }
      $row['portefeuille']=$fix->getDepotbankPortefeuille($row['rekening'],$row['portefeuille'],1);
    }
    $totaal=checkSubTotaal($groupvelden,$lastValue,$row,$layout,$totaal);
    checkStatus($row,$lastValue);
    $xlsData[] = array($row['id']."-".$row['positie'], $row['portefeuille'], $row["client"], $trade, $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$transactieType,$beurs);
    $pdf->row(array($row['id']."-".$row['positie'], $row['portefeuille'], $row["client"],$trade, $row["aantal"], $row['fondsOmschrijving'], $row['koersLimiet'], $row['controleStatus'], $row['ISINCode'], $row['fondssoort'], $row['fondsValuta'],$row['tijdsLimiet'],$transactieType));

  }
  foreach($groupvelden as $veld)
    $lastValue[$veld]=$row[$veld];
  $totaal+=$row["aantal"];
}

if ($layout <> 8)
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

        if($_GET["xls"]==3)
        {
          $xlsData[] = array();
          $pdf->row(array(''));
          $totaal = 0;
          break;
        }
        elseif ($layout == 1)
        {
          $xlsData[] = array('', '', array('Totaal','totaal'), '',array($totaal,'totaalAantal'));
          $xlsData[] = array();
          $pdf->row(array('','','Totaal','',$totaal));
          $pdf->ln();
          $totaal = 0;
          break;
        }
        elseif ($layout == 5||$layout == 6)
        {
          /*
          $xlsData[] = array('','','', array('Totaal','totaal'), array($totaal,'totaalAantal'));
          $xlsData[] = array();
          $pdf->row(array('','','','Totaal',$totaal,'','','','',''));
          $pdf->ln();
          $totaal = 0;
          */
          break;
        }
        elseif ($layout == 4)
        {
          $xlsData[] = array('', '', '',array('Totaal','totaal'), '','','',array($totaal,'totaalAantal'));
          $xlsData[] = array();
  
          $backupCellBorders=$pdf->CellBorders;
          $pdf->CellBorders=array(array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','U'),array('L','R','U'),);
          $pdf->row(array('','','','Totaal','','',number_format($totaal,2,'.','\'')));
          $pdf->CellBorders=array('U','U','U','U','U','U','U','U','U','U','U');
          if($last==false)
            $pdf->row(array('','','','','','','','','','',''));
          $pdf->CellBorders=$backupCellBorders;
          $totaal = 0;
          break;
        }
        else
        {
          if($layout==7)
          {
            $xlsData[] = array('', '', array('Total', 'totaal'), '', array($totaal, 'totaalAantal'));
            $pdf->row(array('', '', 'Total', '', $totaal));
          }
          if($layout==9)
          {
            $xlsData[] = array('', '','', array('Totaal', 'totaal'), '', array($totaal, 'totaalAantal'));
            $pdf->row(array('', '','', 'Totaal', '', $totaal));
          }
          else
          {
            $xlsData[] = array('', '', array('Totaal', 'totaal'), '', array($totaal, 'totaalAantal'));
            $pdf->row(array('', '', 'Totaal', '', $totaal));
          }
          $xlsData[] = array();
          $pdf->ln();
          $totaal = 0;
          break;
        }
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
  if ($layout == 3)
  {
    $xls->excelOpmaak=array();
    $xls->setColumn=array();
    $xls->writetab('TGB', $TGBdata);
  }
  if ($layout == 5)
  {
    $xls->excelOpmaak=array();
    $xls->setColumn=array();
    $xls->writetab('KAS', $KASdata);
  }
  if ($layout == 8)
  {
  //  $xls->excelOpmaak=array();
    $xls->setColumn=array();
    $xls->writetab('OPT', $xlsDataKBCOptie);
  }
  
 // $xls->OutputXls();

  $xls->workbook->send('orders.xls');
  $xls->workbook->close();
  /*
  exit;

// ;

  $xls->setData($xlsData);
//if ($snsLayout == false)
//{
  // $xls->portrait = true;
//}
  $xls->OutputXls();
   */
}
?>