<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/05/12 08:37:39 $
    File Versie         : $Revision: 1.12 $

    $Log: orderExportAIRS.php,v $
    Revision 1.12  2018/05/12 08:37:39  rvv
    *** empty log message ***

    Revision 1.11  2016/11/13 16:26:26  rvv
    *** empty log message ***

    Revision 1.10  2016/10/16 15:04:38  rvv
    *** empty log message ***

    Revision 1.9  2016/09/23 16:00:14  rvv
    *** empty log message ***

    Revision 1.8  2016/09/14 13:04:14  rvv
    *** empty log message ***

    Revision 1.7  2016/07/24 09:27:22  rvv
    *** empty log message ***

    Revision 1.6  2016/02/28 17:08:44  rvv
    *** empty log message ***

    Revision 1.5  2016/02/17 19:29:53  rvv
    *** empty log message ***

    Revision 1.4  2016/02/13 14:01:08  rvv
    *** empty log message ***

    Revision 1.3  2016/01/13 17:07:23  rvv
    *** empty log message ***

    Revision 1.2  2014/12/24 09:54:51  cvs
    call 3105

    Revision 1.1  2014/05/02 08:45:00  cvs
    *** empty log message ***


*/    
include_once("wwwvars.php");
include_once("../classes/AE_cls_adventExport.php");
$export = new adventExport();

$mapTransacties["A"]  = "A";
$mapTransacties["V"]  = "V";
$mapTransacties["AO"] = "OA";
$mapTransacties["AS"] = "SA";
$mapTransacties["VO"] = "OV";
$mapTransacties["VS"] = "SV";

if($_GET['orderVersie']==2)
  $orderVersie=2;
else
  $orderVersie=1;

$db=new DB();
$db2=new DB();
$extraWhere='';
if($orderVersie==2)
{
  if(strpos($_SESSION['lastListQuery'],'OrdersV2.id as id') > 0)
  {
    if(strpos($_SESSION['lastListQuery'],'enkeleOrderRegels') > 0)
    {
      $query="CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegelsV2.*
        FROM OrdersV2 INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        WHERE OrdersV2.OrderSoort <> 'M'
        GROUP BY OrdersV2.id  ";
      $db->SQL($query);
      $db->Query();
      $query="ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
    }
    $tmp=explode("LIMIT",$_SESSION['lastListQuery']);
    $ids=array();
    $db->SQL($tmp[0]);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $ids[]=$data['id'];
    }
    $extraWhere= " AND OrdersV2.id IN('".implode("','",$ids)."')";
  }
  $ids=array();
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,3)=='id_')
      $ids[]=substr($key,3);
  }
  if(count($ids)>0)
    $extraWhere .= " AND OrdersV2.id IN('".implode("','",$ids)."')";


  $query = "
SELECT 
  OrdersV2.id as id , 
  OrdersV2.id as orderid, 
  OrdersV2.fondsOmschrijving, 
  OrdersV2.transactieSoort,
  OrdersV2.fondsValuta,
  OrdersV2.Depotbank , 
  OrderRegelsV2.portefeuille, 
  OrderRegelsV2.rekening as rekeningnrOld, 
  REPLACE(OrderRegelsV2.rekening,Rekeningen.valuta,'') as rekeningnr,
  if(OrderRegelsV2.rekening <> '',Rekeningen.valuta, Fondsen.valuta) as valuta, 
  OrderRegelsV2.aantal, 
  OrderRegelsV2.kosten, 
  OrderRegelsV2.brokerkosten, 
  OrderRegelsV2.opgelopenRente, 
  OrderRegelsV2.brutoBedrag, 
  OrderRegelsV2.nettoBedrag,
  OrderUitvoeringV2.uitvoeringsPrijs ,
  OrderUitvoeringV2.uitvoeringsDatum ,
  OrdersV2.ISINCode as fondsCode , 
	Fondsen.Fonds,
	Fondsen.ISINCode,
  OrderRegelsV2.client , 
  OrderRegelsV2.regelNotaValutakoers as valutakoers,
  BbLandcodes.settlementDays
FROM (OrdersV2) 
  LEFT JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
  LEFT JOIN OrderUitvoeringV2 ON OrdersV2.id = OrderUitvoeringV2.orderid 
  LEFT JOIN Fondsen ON OrdersV2.fonds = Fondsen.Fonds
  LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
  LEFT JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.rekening
WHERE 
  OrdersV2.orderStatus = '2' $extraWhere";
}
else
{
if(strpos($_SESSION['lastListQuery'],'Orders.id as id') > 0)
{
  if(strpos($_SESSION['lastListQuery'],'enkeleOrderRegels') > 0)
  {
     $query="CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegels.*
        FROM Orders INNER JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
        WHERE Orders.OrderSoort <> 'M'
        GROUP BY Orders.orderid  ";
      $db->SQL($query); 
      $db->Query();
      $query="ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
  }   
  $tmp=explode("LIMIT",$_SESSION['lastListQuery']);
  $ids=array();
  $db->SQL($tmp[0]);
  $db->Query();
  while($data=$db->nextRecord())
  {
   $ids[]=$data['id'];
  }
  $extraWhere= " AND Orders.id IN('".implode("','",$ids)."')";
}

$ids=array();
foreach ($_POST as $key=>$value)
{
  if(substr($key,0,3)=='id_')
    $ids[]=substr($key,3);
}
if(count($ids)>0)
  $extraWhere .= " AND Orders.id IN('".implode("','",$ids)."')";

 
$query = "
SELECT 
  Orders.id as id , 
  Orders.orderid, 
  Orders.aantal, 
  Orders.fondsOmschrijving, 
  Orders.transactieSoort,
  Orders.Depotbank , 
  OrderRegels.portefeuille, 
  OrderRegels.rekeningnr, 
  OrderRegels.valuta, 
  OrderRegels.aantal, 
  OrderRegels.kosten, 
  OrderRegels.brokerkosten, 
  OrderRegels.opgelopenRente, 
  OrderRegels.brutoBedrag, 
  OrderRegels.nettoBedrag,
  OrderUitvoering.uitvoeringsPrijs ,
  OrderUitvoering.uitvoeringsDatum ,
  Orders.fondsCode , 
	Fondsen.Fonds,
	Fondsen.ISINCode,
  OrderRegels.client , 
  OrderRegels.valutakoers,
  BbLandcodes.settlementDays
FROM (Orders) 
  LEFT JOIN OrderRegels ON Orders.orderid = OrderRegels.orderid 
  LEFT JOIN OrderUitvoering ON Orders.orderid = OrderUitvoering.orderid 
  INNER JOIN Fondsen ON Orders.fonds = Fondsen.Fonds
  LEFT JOIN BbLandcodes ON Fondsen.bbLandcode = BbLandcodes.bbLandcode
WHERE 
  Orders.laatsteStatus = '2' $extraWhere";
}
$export->fieldsPerLine = 23;
$export->outputType = "push";

$db->executeQuery($query);
$tel = 0;
while( $rec = $db->nextRecord() )
{
  //listarray($rec);
  $tel++;
  $parts = explode("-",substr($rec["uitvoeringsDatum"],0,10));
  $datum = $parts[2].".".$parts[1].".".$parts[0];
  
  
  $uitvoeringsJul=db2jul($rec['uitvoeringsDatum']);
  $dagvanweek=date('N',$uitvoeringsJul);
  $baseDays=2;
  
  if($rec['settlementDays'] > 0)
     $baseDays=$rec['settlementDays'];
      
  if($dagvanweek<=(5-$baseDays) && $dagvanweek<6)
    $extraDagen=0;
  elseif($dagvanweek<=(10-$baseDays) && $dagvanweek<6)
    $extraDagen=2;
  else
    $extraDagen=4;

  $settleDatum=date('d.m.Y',$uitvoeringsJul+(($baseDays+$extraDagen)*86400)+3605);
  if($orderVersie==2 && ($rec["valutakoers"]==0 || $rec['fondsValuta'] == $rec['valuta']) )
  {
    $query = "SELECT max(uitvoeringsDatum) as uitvoeringsDatum  FROM OrderUitvoeringV2 WHERE orderid = '".$rec['orderid']."'";
    $db2->SQL($query);
    $uitvoering = $db2->lookupRecord();
    if($uitvoering['uitvoeringsDatum']<>'')
      $uitvoeringsDatumWhere = " AND Datum<='".$uitvoering['uitvoeringsDatum']."' ";
    else
      $uitvoeringsDatumWhere='';
    $query = "SELECT koers,Valuta FROM Valutakoersen WHERE Valuta = '".$rec['fondsValuta']."' $uitvoeringsDatumWhere ORDER BY Datum DESC LIMIT 1";
    $db2->SQL($query);
    $valutaKoers = $db2->lookupRecord();
    $rec['valutakoers']=$valutaKoers['koers'];
  }
      
  //$export->addField(1,$rec["portefeuille"]);
  $export->addField(1,$rec["rekeningnr"]);
  $export->addField(3,"ISIN:".$rec["ISINCode"]);
  $export->addField(4,$mapTransacties[$rec["transactieSoort"]]);
  $export->addField(5,$rec["aantal"]);
  $export->addField(7,$rec["opgelopenRente"]);
  $export->addField(8,$rec["uitvoeringsPrijs"]);
  $export->addField(9,$rec["valuta"]);
  $export->addField(10,$rec["valutakoers"]);
  $export->addField(11,$rec["kosten"]);
  $export->addField(12,$rec["brokerkosten"]);
  $export->addField(14,$rec["nettoBedrag"]);
  $export->addField(15,$datum);
  $export->addField(16,$settleDatum);
  $export->addField(17,$__appvar["bedrijf"]);
  $export->addField(18,$rec["orderid"]);
  $export->addField(22,$rec["Fonds"]);
  $export->addField(23,$rec["valuta"]);
  $export->pushBuffer();
}

$export->makeCsv($__appvar["bedrijf"]."_AIRSexport");
  
?>
