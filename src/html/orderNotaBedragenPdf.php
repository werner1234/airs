<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/11/13 16:26:26 $
 		File Versie					: $Revision: 1.2 $

 		$Log: orderNotaBedragenPdf.php,v $
 		Revision 1.2  2016/11/13 16:26:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/11/09 17:10:27  rvv
 		*** empty log message ***
 		

*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");
include_once("../config/ordersVars.php");
include_once("rapport/rapportRekenClass.php");

function AlteredHeader()
{

}

class notaBedragenPrint
{
  function notaBedragenPrint()
  {
    $this->db = new DB();

  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde, $dec, ",", ".");
  }


  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {

    if ($VierDecimalenZonderNullen)
    {

      $getal = explode('.',$waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000' )
      {
        for ($i = strlen($decimaalDeel); $i >=0; $i--)
        {
          $decimaal = $decimaalDeel[$i-1];
          if ($decimaal != '0' && !$newDec)
          {
            //  echo $this->portefeuille." $waarde <br>";exit;
            $newDec = $i;
          }
        }
        return number_format($waarde,$newDec,",",".");
      }
      else
        return number_format($waarde,$dec,",",".");
    }
    else
      return number_format($waarde,$dec,",",".");
  }

  function prepareData()
  {

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

    $db = new DB();
    $db2 = new DB();
    $query = "SELECT  id,ISINCode,fondsValuta,fondsOmschrijving,transactieSoort,notaValutakoers FROM OrdersV2 WHERE 1 $extraWhere";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $orderData[$data['id']] = $data;
    }
    foreach ($orderData as $orderId => $details)
    {
      if (!isset($orderData[$orderId]['aantal']))
      {
        $orderData[$orderId]['aantal'] = 0;
      }

      $query = "SELECT id,portefeuille,rekening,aantal,brutoBedrag,kosten,brokerkosten,opgelopenRente,nettoBedrag,regelNotaValutakoers,
 (SELECT OrderUitvoeringV2.uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid limit 1) as fondskoers
FROM OrderRegelsV2 WHERE orderid='" . $orderId . "'";
      $db->SQL($query);
      $db->Query();
      while ($data = $db->nextRecord())
      {

        $db2->SQL("SELECT valuta FROM Rekeningen WHERE rekening='" . $data['rekening'] . "'");
        $rekeningData = $db2->lookupRecord();
        $data['rekeningValuta'] = $rekeningData['valuta'];
        $orderData[$orderId]['orderregels'][$data['id']] = $data;





        $orderData[$orderId]['aantal'] += $data['aantal'];
      }

    }

   return $orderData;


  }

  function createPDF($orderData)
  {
    global $__appvar;
    $this->pdf = new PDFbase('L', 'mm', 'A4');
    $this->pdf->SetAutoPageBreak(true, 15);
    $this->pdf->AddPage();
    $this->pdf->SetFont('Arial', '', 10);


    foreach ($orderData as $orderId => $orderDetail)
    {
      $this->pdf->SetWidths(array(45, 100));
      $this->pdf->SetAligns(array("L", "L"));
      $this->pdf->SetFont('Arial', 'B', 10);
      $this->pdf->row(array('OrderId', $__appvar["bedrijf"] . $orderId));
      $this->pdf->SetFont('Arial', '', 10);
      $this->pdf->row(array('ISIN-code', $orderDetail["ISINCode"]));
      $this->pdf->row(array('Valuta', $orderDetail["fondsValuta"]));
      $this->pdf->row(array('Fondsnaam', $orderDetail["fondsOmschrijving"]));
      $this->pdf->row(array('Aan/verkoop', $orderDetail["transactieSoort"]));
      $this->pdf->row(array('Totaal aantal', $orderDetail["aantal"]));
      $this->pdf->ln();
      $this->pdf->SetWidths(array(35,35,20,25,25,25,25,20,20,25,25));
      $this->pdf->SetAligns(array("L","L","L","R","R","R","R","R","R","R","R"));
      $this->pdf->row(array('Portefeuille', 'Rekeningnummer', 'Rek. valuta', 'Aantal', 'Koers', 'Brutobedrag', 'Wisselkoers', 'Rente', 'Kosten', 'Brokerkosten', 'Netto bedrag'));
      $totalen=array();
      foreach ($orderDetail['orderregels'] as $orderregelId => $regelData)
      {

        $this->pdf->row(array($regelData['portefeuille'],
                    $regelData['rekening'],
                    $regelData['rekeningValuta'],
                    $this->formatAantal($regelData['aantal'],4,true),
                    $this->formatGetal($regelData['fondskoers'],4),
                    $this->formatGetal($regelData['brutoBedrag'],2),
                    $this->formatGetal($regelData['regelNotaValutakoers'],4),
                    $this->formatGetal($regelData['opgelopenRente'],2),
                    $this->formatGetal($regelData['kosten'],2),
                    $this->formatGetal($regelData['brokerkosten'],2),
                    $this->formatGetal($regelData['nettoBedrag'],2)));
        $totalen['nettoBedrag']+=$regelData['nettoBedrag'];
      }
      $this->pdf->CellBorders=array('','','','','','','','','','','T');
      $this->pdf->row(array('','','','','','','','','','',$this->formatGetal($totalen['nettoBedrag'],2)));
      unset($this->pdf->CellBorders);
      $this->pdf->ln();
      $this->pdf->ln();

    }
  }

  function outputPdf()
  {
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    $this->pdf->Output("nota.pdf","D");//"D");
  }

}

$notaPrint=new notaBedragenPrint();
$orderData=$notaPrint->prepareData();
$notaPrint->createPDF($orderData);
$notaPrint->outputPdf();




?>