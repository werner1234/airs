<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/09/24 12:03:24 $
    File Versie         : $Revision: 1.3 $

    $Log: orderIndekrapport.php,v $
    Revision 1.3  2016/09/24 12:03:24  rvv
    *** empty log message ***

    Revision 1.2  2016/09/21 18:43:42  rvv
    *** empty log message ***

    Revision 1.1  2016/09/14 16:10:15  rvv
    *** empty log message ***


*/    
include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");


$format='xls';

function getBrokerinstructies($vermogensbeheerder='',$valuta='EUR')
{
  $db=new DB();
  $query="SELECT portefeuille,iban FROM Brokerinstructies WHERE vermogensbeheerder='$vermogensbeheerder' AND vvSettlement='$valuta' AND depotbank='KAS'";
  $db->SQL($query);
  $db->Query();
  $data=$db->nextRecord();
  return $data;
}

$db=new DB();

  if (strpos($_SESSION['lastListQuery'], 'OrdersV2.id as id') > 0)
  {
    if (strpos($_SESSION['lastListQuery'], 'enkeleOrderRegels') > 0)
    {
      $query = "CREATE TEMPORARY TABLE enkeleOrderRegels
        SELECT OrderRegelsV2.*
        FROM OrdersV2 INNER JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid 
        WHERE OrdersV2.OrderSoort <> 'M'
        GROUP BY OrdersV2.id  ";
      $db->SQL($query);
      $db->Query();
      $query = "ALTER TABLE enkeleOrderRegels ADD INDEX( orderid ); ";
      $db->SQL($query);
      $db->Query();
    }
    $tmp = explode("LIMIT", $_SESSION['lastListQuery']);
    $ids = array();
    $db->SQL($tmp[0]);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $ids[] = $data['id'];
    }
    $extraWhere = " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }
  $ids = array();
  foreach ($_POST as $key => $value)
  {
    if (substr($key, 0, 3) == 'id_')
    {
      $ids[] = substr($key, 3);
    }
  }
  if (count($ids) > 0)
  {
    $extraWhere .= " AND OrdersV2.id IN('" . implode("','", $ids) . "')";
  }


$query="SELECT
OrdersV2.depotbank,
OrdersV2.id,
OrderRegelsV2.id as orderregelId,
OrdersV2.orderSoort,
OrderRegelsV2.positie,
Rekeningen.Valuta as rekeningValuta,
max(OrderUitvoeringV2.uitvoeringsDatum),
OrdersV2.settlementdatum,
OrdersV2.fondsOmschrijving,
OrdersV2.fondsValuta,
SUM(OrderUitvoeringV2.uitvoeringsPrijs*OrderUitvoeringV2.uitvoeringsAantal*OrdersV2.fondseenheid) as uitvoeringsbedrag,
OrderRegelsV2.brutoBedrag,
OrderRegelsV2.opgelopenRente
FROM
OrdersV2
JOIN OrderRegelsV2 ON OrdersV2.id = OrderRegelsV2.orderid
INNER JOIN Rekeningen ON OrderRegelsV2.rekening = Rekeningen.Rekening
LEFT JOIN OrderUitvoeringV2 ON OrdersV2.id = OrderUitvoeringV2.orderid
WHERE OrdersV2.orderstatus=2 $extraWhere
GROUP BY OrderRegelsV2.id
ORDER BY OrdersV2.depotbank, 
OrdersV2.fondsValuta, 
rekeningValuta,
OrdersV2.id,
OrderRegelsV2.positie, 
OrdersV2.settlementdatum,
OrderRegelsV2.id ";

$db=new DB();
$db->sql($query);
$db->query();
$xlsdata=array();
$xlsdata[]=array('Depotbank','OrderId','Valuta Rekening','Valutadatum','Fonds','Fondsvaluta','BrutoBedragInFondsvaluta','OpgelopenRente','Bruto+OPgelopen rente');
while($data=$db->nextRecord())
{
  if($data['orderSoort']=='M')
    $orderId=$__appvar["bedrijf"].'-'.$data['id'].'-'.$data['positie'];
  else
    $orderId=$__appvar["bedrijf"].'-'.$data['id'];

  $brutoEnRente=$data['opgelopenRente']+$data['brutoBedrag'];

  if(isset($lastDepotbank) && ($lastDepotbank!=$data['depotbank'] || $lastDatum!=$data['settlementdatum'] || $lastFondsValuta!=$data['fondsValuta'] || $lastRekeningValuta!=$data['rekeningValuta']))
  {
    $xlsdata[]=array($lastDepotbank,
      '',
      $lastRekeningValuta,
      $lastDatum,
      '',
      $lastFondsValuta,
      $totalen['brutoBedrag'],
      $totalen['opgelopenRente'],
      $totalen['brutoBedrag']+$totalen['opgelopenRente']);
    $xlsdata[]=array();
    $totalen=array();
  }

  $xlsdata[]=array($data['depotbank'],
    $orderId,
    $data['rekeningValuta'],
    $data['settlementdatum'],
    $data['fondsOmschrijving'],
    $data['fondsValuta'],
    $data['brutoBedrag'],
    $data['opgelopenRente'],
    $brutoEnRente);

  $lastDepotbank=$data['depotbank'];
  $lastDatum=$data['settlementdatum'];
  $lastFondsValuta=$data['fondsValuta'];
  $lastRekeningValuta=$data['rekeningValuta'];

  $totalen['brutoBedrag']+=$data['brutoBedrag'];
  $totalen['opgelopenRente']+=$data['opgelopenRente'];


}
$xlsdata[]=array($lastDepotbank,
  '',
  '',
  $lastDatum,
  '',
  $lastFondsValuta,
  $totalen['brutoBedrag'],
  $totalen['opgelopenRente'],
  $totalen['brutoBedrag']+$totalen['opgelopenRente']);


    if($format=='xls')
    {
      $filename='export.xls';
  	  include_once('../classes/excel/Writer.php');
	    $workbook = new Spreadsheet_Excel_Writer();
      $worksheet =& $workbook->addWorksheet();
	    for($regel = 0; $regel < count($xlsdata); $regel++ )
	    {
		    for($col = 0; $col < count($xlsdata[$regel]); $col++)
		    {
		      $worksheet->write($regel, $col, $xlsdata[$regel][$col]);
		    }
	    }
      
      $workbook->send($filename);
	    $workbook->close();
    }
    else
    {
			//$csvdata = generateCSV($xlsdata);
      $csvdata='';
      for ($a=0;$a<count($xlsdata);$a++)
      {
        for($b=0;$b<count($xlsdata[$a]);$b++)
        {
          $csvdata .= str_replace("\n","",$xlsdata[$a][$b]).',';
        }
        $csvdata = substr($csvdata,0,-1);
        $csvdata .= "\r\n";
      }
            
      $filename='export.csv';
      $appType = "text/comma-separated-values";
      header('Content-type: ' . $appType);
    	header("Content-Length: ".strlen($csvdata));
    	header("Content-Disposition: inline; filename=\"".$filename."\"");
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      echo $csvdata;
  
		}

?>