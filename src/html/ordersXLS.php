<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/12/13 17:57:40 $
 		File Versie					: $Revision: 1.6 $

 		$Log: ordersXLS.php,v $
 		Revision 1.6  2015/12/13 17:57:40  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/11/22 16:19:27  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/01/22 13:44:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/07/23 17:24:57  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/15 15:37:48  rvv
 		*** empty log message ***

 		Revision 1.1  2009/01/20 17:49:36  rvv
 		*** empty log message ***

 		Revision 1.5  2007/03/27 14:57:19  rvv
 		VreemdeValutaRapportage

 		Revision 1.4  2007/03/22 07:34:23  rvv
 		*** empty log message ***

 		Revision 1.3  2006/06/09 11:28:56  cvs
 		*** empty log message ***

 		Revision 1.2  2006/06/09 09:57:26  cvs
 		*** empty log message ***

 		Revision 1.1  2006/06/08 14:47:14  cvs
 		*** empty log message ***

 		Revision 1.1  2006/01/05 16:06:05  cvs
 		eerste CRM test

 		Revision 1.2  2005/12/14 12:35:13  cvs
 		*** empty log message ***

 		Revision 1.1  2005/11/23 09:29:48  cvs
 		*** empty log message ***


*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_xls.php");
include_once("../config/ordersVars.php");


if (!$_GET["orderid"] )
{
  echo "foute aanroep";
  exit();
}
$ordermoduleAccess=GetModuleAccess("ORDER");

  $db = new DB();
  $db2 = new DB();
  $query = "SELECT * FROM Vermogensbeheerders Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";
  $db->SQL($query);
  $beheerderRec = $db->lookupRecord();
  if($ordermoduleAccess==2)
    $query = "SELECT sum(aantal) as totaal FROM OrderRegelsV2 WHERE orderid='".$_GET["orderid"]."' ";
  else
    $query = "SELECT sum(aantal) as totaal FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $tmp = $db->lookupRecord();
  if($ordermoduleAccess==2)
    $query = "SELECT OrdersV2.*,OrdersV2.id as orderid, OrdersV2.depotbank as Depotbank  FROM OrdersV2 WHERE id='".$_GET["orderid"]."' ";
  else
    $query = "SELECT * FROM Orders WHERE orderid='".$_GET["orderid"]."' ";
  $db->SQL($query);
  $orderRec = $db->lookupRecord();
  $query = "SELECT * FROM Fondsen WHERE fonds='".$orderRec["fonds"]."' ";
  $db->SQL($query);
  $fondsenRec = $db->lookupRecord();

  if ($orderRec["transactieType"] == "L" or
      $orderRec["transactieType"] == "SL")
  {
    $trType = '   (koers '.$orderRec["koersLimiet"].')';

  }
  if ($tmp["totaal"] <> $orderRec["aantal"])
  {
    $verschilTxt = " >>> LET OP verschil = ".($tmp["totaal"] - $orderRec["aantal"]);
  }

  if ($orderRec["tijdsSoort"] == "DAT")
  {
    $looptijd = "  (".$orderRec["tijdsLimiet"].")";
  }

  $kop[] = array("field" =>"order kenmerk"    ,"value" =>$orderRec["orderid"]);
  $kop[] = array("field" =>"fonds / ISIN"     ,"value" =>$orderRec["fonds"].' / '.$orderRec["fondsCode"]);
  $kop[] = array("field" =>"aantal"           ,"value" =>$orderRec["aantal"].$verschilTxt);
  if($beheerderRec['OrderLoggingOpNota']==1)
    $kop[] = array("field" =>"fondsvaluta"      ,"value" =>$fondsenRec['Valuta']);
  $kop[] = array("field" =>"transactieType"   ,"value" =>$__ORDERvar["transactieType"][$orderRec["transactieType"]].$trType);
  $kop[] = array("field" =>"transactieSoort"  ,"value" =>$__ORDERvar["transactieSoort"][$orderRec["transactieSoort"]]);
  $kop[] = array("field" =>"looptijd"         ,"value" =>$__ORDERvar["tijdsSoort"][$orderRec["tijdsSoort"]].$looptijd);
  $kop[] = array("field" =>"depotbank"    ,"value" =>$orderRec["Depotbank"]);



$xls = new AE_xls();
$xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
$xls->excelOpmaak['kopl']=array('setAlign'=>'left','setBgColor'=>'22');
$xls->excelOpmaak['kopr']=array('setAlign'=>'left');
$xls->setColumn[] = array(0,1,10);
$xls->setColumn[] = array(2,2,15);
$xls->setColumn[] = array(3,3,40);
$xls->setColumn[] = array(4,4,20);
if($beheerderRec['OrderLoggingOpNota']==1)
  $xls->setColumn[] = array(5,5,20);

for ($x=0;$x < count($kop);$x++)
{
   $xlsData[] = array(array($kop[$x]['field'],'kopl'),'',array($kop[$x]['value'],'kopr'));
   $xls->mergeCells[] = array($x,0,$x,1);
   $xls->mergeCells[] = array($x,2,$x,3);
}

$xlsData[]=array('');
if($beheerderRec['OrderLoggingOpNota']==1)
  $xlsData[]=array(array("pos",'header'),array("aantal",'header'),array("portefeuille",'header'),array("client",'header'),array("rekeningnr",'header'),array('geschat orderbedrag','header'));
else
  $xlsData[]=array(array("pos",'header'),array("aantal",'header'),array("portefeuille",'header'),array("client",'header'),array("rekeningnr",'header'));

if($ordermoduleAccess==2)
  $query = "SELECT positie,aantal,portefeuille,client,rekening as rekeningnr FROM OrderRegelsV2 WHERE orderid='".$_GET["orderid"]."' ORDER BY positie";
else
  $query = "SELECT * FROM OrderRegels WHERE orderid='".$_GET["orderid"]."' ORDER BY positie";
$db->SQL($query);
$db->query();




while ($row = $db->nextRecord())
{
  if ($row["aantal"] == intval($row["aantal"]))
    $aantal = number_format($row["aantal"],0,",",".");
  else
    $aantal = number_format($row["aantal"],4,",",".");

  if($beheerderRec['OrderLoggingOpNota']==1)
    $xlsData[] = array($row['positie'],$row["aantal"],$row['portefeuille'],$row['client'],$row['rekeningnr'].$row['valuta'],$row['brutoBedrag']);
  else
    $xlsData[] = array($row[positie],$row["aantal"],$row[portefeuille],$row[client],$row[rekeningnr].$row[valuta]);
}


if($beheerderRec['OrderLoggingOpNota']==1)
{
  $logregels=explode("\n",$orderRec['status']);
  $logregels=array_reverse($logregels);
   $xlsData[]=array();
  foreach ($logregels as $regel)
  {
    $pos=strpos($regel," ");
    $xlsData[] = array(substr($regel,0,$pos),'','', str_replace("laatsteStatus","Status",substr($regel,$pos)));
  }
  $xlsData[]=array();
  $xlsData[]=array("Printinformatie:",'','',$USR,date('d-m-Y'),date("h:i"));
}

$xls->setData($xlsData);

$xls->OutputXls();

?>