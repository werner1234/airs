<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/02/21 17:21:12 $
 		File Versie					: $Revision: 1.9 $

 		$Log: bulkordersXLS.php,v $
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
include_once("../config/ordersVars.php");

$selectedPaginas=array();
foreach($_GET as $key=>$value)
{
  if(substr($key,0,7)=='pagina_')
  {
    $selectedPaginas[]=substr($key,7);
  }
}

if (!$_GET["xls"] )
{
  echo "foute aanroep";
  exit();
}

if($_GET["xls"]==2)
  $snsLayout=true;
else
  $snsLayout=false;


  $db = new DB();

$xls = new AE_xls();
$xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
$xls->excelOpmaak['kopl']=array('setAlign'=>'left','setBold'=>1);
$xls->excelOpmaak['kopr']=array('setAlign'=>'left');
$xls->excelOpmaak['aantal']=array('setAlign'=>'right','setNumFormat'=>39);
$xls->setColumn[] = array(0,0,4);
$xls->setColumn[] = array(1,1,9);
$xls->setColumn[] = array(2,2,16);
$xls->setColumn[] = array(3,3,4);
$xls->setColumn[] = array(4,4,8);
$xls->setColumn[] = array(5,5,25);
$xls->setColumn[] = array(6,6,7);
$xls->setColumn[] = array(7,7,8);
if($snsLayout==true)
{
  $xls->setColumn[] = array(8,8,20);
}  

$cfg=new AE_config();
$volgNummer=$cfg->getData('tmpbulkorderlast');
$cfg->addItem('tmpbulkorderlast',($volgNummer+1));

$xlsData[]=array(array('Doorgegeven aan:','kopl'),array('','kopl'),array('','kopl'),array('Nummer:','kopl'),array('','kopl'),array($volgNummer,'kopl'),array('Datum','kopl'),array(date('d-m-Y'),'kopl'));
$xlsData[]=array(array('Opgegeven door:','kopl'),array('','kopl'),array('','kopl'),array('','kopl'),array('','kopl'),array('','kopl'),array('Tijdstip','kopl'),array('','kopl'));

if($snsLayout==true)
{
  $xlsData[]=array('',array("portefeuille",'header'),
  array("client",'header'),
  array("tt",'header'),
  array("aantal",'header'),
  array("fonds",'header'),
  array("limiet",'header'),
  array("controle",'header'),
  array("ISINCode",'header'),
  array("fondssoort",'header'),
  array("Valuta",'header'));
}  
else
{
  $xlsData[]=array('',array("portefeuille",'header'),
  array("client",'header'),
  array("tt",'header'),
  array("aantal",'header'),
  array("fonds",'header'),
  array("limiet",'header'),
  array("controle",'header'));
}
//if($_GET["pagina"] <> 'alles')
//  $paginaWhere="AND pagina='".$_GET["pagina"]."' ";

$orderVersion=GetModuleAccess("ORDER");

if($orderVersion==2)
{
  if($snsLayout==true)
  {
  $query = "SELECT TijdelijkeBulkOrdersV2.regelNr, TijdelijkeBulkOrdersV2.portefeuille,client,transactieSoort,aantal,
  Fondsen.Omschrijving as fondsOmschrijving,koersLimiet,TijdelijkeBulkOrdersV2.controleStatus as checkResult,Fondsen.ISINCode,Fondsen.fondssoort,Fondsen.Valuta
  FROM TijdelijkeBulkOrdersV2 
  JOIN Fondsen ON TijdelijkeBulkOrdersV2.fonds=Fondsen.Fonds 
  WHERE 1 $paginaWhere $userWhere ORDER BY fondssoort,fondsOmschrijving";
  }
  else
  {
   $query = "SELECT TijdelijkeBulkOrdersV2.regelNr , TijdelijkeBulkOrdersV2.portefeuille,client,transactieSoort,aantal,
  Fondsen.Omschrijving as fondsOmschrijving,koersLimiet,TijdelijkeBulkOrdersV2.controleStatus as checkResult ,Fondsen.ISINCode,Fondsen.fondssoort,Fondsen.Valuta
  FROM TijdelijkeBulkOrdersV2
  JOIN Fondsen ON TijdelijkeBulkOrdersV2.fonds=Fondsen.Fonds 
  WHERE 1 $paginaWhere $userWhere  ORDER BY regelNr"; 
  }
}
else
{
  if (count($selectedPaginas) > 0)
    $paginaWhere=" AND pagina IN('".implode("','",$selectedPaginas)."')";

  if($_GET["user"] <> 'alles')
    $userWhere="AND TijdelijkeBulkOrders.add_user='".$_GET["user"]."' ";  

  if($snsLayout==true)
  {
  $query = "SELECT TijdelijkeBulkOrders.regelNr, TijdelijkeBulkOrders.portefeuille,client,transactieSoort,aantal,
  Fondsen.Omschrijving as fondsOmschrijving,koersLimiet,checkResult,Fondsen.ISINCode,Fondsen.fondssoort,Fondsen.Valuta
  FROM TijdelijkeBulkOrders 
  JOIN Fondsen ON TijdelijkeBulkOrders.fonds=Fondsen.Fonds 
  WHERE 1 $paginaWhere $userWhere ORDER BY fondssoort,fondsOmschrijving";
  }
  else
  {
  $query = "SELECT TijdelijkeBulkOrders.regelNr, TijdelijkeBulkOrders.portefeuille,client,transactieSoort,aantal,
  Fondsen.Omschrijving as fondsOmschrijving,koersLimiet,checkResult,Fondsen.ISINCode,Fondsen.fondssoort,Fondsen.Valuta
  FROM TijdelijkeBulkOrders 
  JOIN Fondsen ON TijdelijkeBulkOrders.fonds=Fondsen.Fonds 
  WHERE 1 $paginaWhere $userWhere ORDER BY regelNr"; 
  }
}
$db->SQL($query);
$db->query();

while ($row = $db->nextRecord())
{
 if($snsLayout==true) 
 {
  if(isset($lastFonds) && $row['fondsOmschrijving'] <> $lastFonds)
  {
    $xlsData[] = array('','','Totaal','',$totaal);
    $totaal=0;
  }
  
  $xlsData[] = array($row['regelNr'],$row['portefeuille'],$row["client"],$row['transactieSoort'],$row["aantal"],$row['fondsOmschrijving'],$row['koersLimiet'],$row['checkResult'],$row['ISINCode'],$row['fondssoort'],$row['Valuta']);
  $lastFonds=$row['fondsOmschrijving'];
  $totaal+=$row["aantal"];
 }
 else 
  $xlsData[] = array($row['regelNr'],$row['portefeuille'],$row["client"],$row['transactieSoort'],$row["aantal"],$row['fondsOmschrijving'],$row['koersLimiet'],$row['checkResult']);

}
if($snsLayout==true) 
  $xlsData[] = array('','','Totaal','',$totaal);



$xls->setData($xlsData);
if($snsLayout==false) 
  $xls->portrait=true;
$xls->OutputXls();

?>