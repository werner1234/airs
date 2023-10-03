<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.5 $
 		
 		$Log: printNotaPDF.php,v $
 		Revision 1.5  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.4  2009/11/29 15:16:59  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/10/14 15:47:23  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/09/12 11:41:49  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/09/12 11:16:05  rvv
 		*** empty log message ***
 		
 	
*/

include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportVertaal.php");
include_once("rapport/PDFRapport.php");


function formatGetal($waarde, $dec)
{
	return number_format($waarde,$dec,",",".");
}
$cfg=new AE_config();
$pdf = new PDFRapport('P','mm',"A4");

$pdf->SetAutoPageBreak(true,15);
$pdf->pagebreak = 280;
$pdf->__appvar = $__appvar;

$db=new DB();
if(checkAccess())
  $query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles limit 1";
else
  $query="SELECT Portefeuilles.Portefeuille, VermogensbeheerdersPerGebruiker.Gebruiker 
  FROM VermogensbeheerdersPerGebruiker Join Portefeuilles ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";

$db->SQL($query);
$portefeuille = $db->lookupRecord();

loadLayoutSettings($pdf, $portefeuille['Portefeuille']);	
$pdf->SetMargins(25,8);

$pdf->rapport_type = "BRIEF";
$pdf->rapport_voettext='';
$pdf->SetFont($pdf->rapport_font,'',8);

if(!$_GET['regelId'])
{
  $lastPrintDate=$cfg->getData('lastNotaPrint');
  $now=date("Y-m-d H:i:s");
  $cfg->addItem('lastNotaPrint',$now);
  $query="SELECT id FROM OrderRegels WHERE definitief='1' AND (printDate < '$lastPrintDate' OR PrintDate = '0000-00-00 00:00:00')";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
    $notas[]=$data['id'];
}
else 
$notas[] = $_GET['regelId'];



foreach ($notas as $regelId)
{

$pdf->AddPage();
$logopos = 80;
		if(is_file($pdf->rapport_logo))
		{
		  if($pdf->rapport_layout == 12 || $pdf->rapport_layout == 5 )
		  {
			  $pdf->Image($pdf->rapport_logo, $logopos -33, 5, 108, 15);
		  }	
		  elseif($pdf->rapport_layout == 14 )
		  {
			  $pdf->Image($pdf->rapport_logo, 220, 5, 65, 20);
		  }	
		  elseif ($pdf->rapport_layout == 16 )
		  {
       	$logopos = 100;
  			$pdf->Image($pdf->rapport_logo, $logopos, 5, 101, 12);//duis 1050,125
		  }
		  elseif ($pdf->rapport_layout == 17 )
		  {
			  $pdf->Image($pdf->rapport_logo, 242, 191, 45, 10);
		  }
		  elseif($pdf->rapport_layout == 1)
		  {
			  $pdf->Image($pdf->rapport_logo, $logopos, 7, 43, 15);
		  }	
		  else 
		    $pdf->Image($pdf->rapport_logo, $logopos, 5, 43, 15);	
		}

		
$query="SELECT * FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$pdf->portefeuilledata['Vermogensbeheerder']."'";
$db->SQL($query);
$vermogensbeheerder = $db->lookupRecord();

$query="SELECT * FROM OrderRegels WHERE id = '$regelId'";
$db->SQL($query);
$orderRegel = $db->lookupRecord();

$query="SELECT * FROM Orders WHERE orderid = '".$orderRegel['orderid']."'";
$db->SQL($query);
$order = $db->lookupRecord();

$query="SELECT * FROM Clienten WHERE Client = '".$orderRegel['client']."'";
$db->SQL($query);
$client = $db->lookupRecord();

$pdf->SetWidths(array(100,100));
$pdf->SetAligns(array("L","L","L","L"));
  
$pdf->Row(array($vermogensbeheerder['Naam']));
$pdf->Row(array($vermogensbeheerder['Adres']));
$pdf->Row(array($vermogensbeheerder['Woonplaats']));
$pdf->Row(array($vermogensbeheerder['Telefoon']));
  
$pdf->Row(array(""));

$pdf->SetY(60);
$pdf->Row(array($client['Naam']));
if($client['Naam1'] <> "")
  $pdf->Row(array($client['Naam1']));
$pdf->Row(array($client['Adres']));
$pdf->Row(array($client['pc']." ".$client['Woonplaats']));
$pdf->Row(array($client['Land']));

$pdf->SetY(95);
$parts=explode(' ',$vermogensbeheerder['Woonplaats']);
$pdf->Row(array($parts[count($parts)-1].", ".date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y")));

$pdf->SetY(120);
$pdf->SetWidths(array(40,100));
$pdf->Row(array("Valuta",$orderRegel['valuta']));
$pdf->Row(array("Order ID positie",$order['orderid'].' '.$orderRegel['positie']));
$pdf->Row(array("Bonnr",$orderRegel['interneNummer']));
$pdf->Row(array("Rekeningnummer",$orderRegel['rekeningnr']));


$pdf->SetY(160);
$transactieSoortConversie= array('V'=>'V','VO'=>'V','VS'=>'V','A'=>'A','AO'=>'A','AS'=>'A','I'=>'E');
$transactieTeksten=array('V'=>'Wij hebben voor u verkocht:','A'=>'Wij hebben voor u gekocht:','E'=>'Wij hebben voor u uit emissie verkregen:');
$pdf->SetWidths(array(200));
$pdf->Row(array($transactieTeksten[$transactieSoortConversie[$order['transactieSoort']]]));
$pdf->Row(array(""));
$pdf->SetWidths(array(40,40,40,40));
$pdf->SetAligns(array("L","L","R","R"));
$pdf->Row(array("Aantal","Fonds","Koers","Bedrag"));

  

if($order['fondsOmschrijving'] != '')
  $fonds=$order['fondsOmschrijving'];
else 
  $fonds=$order['fonds'];

$pdf->Row(array($orderRegel['aantal'],$fonds,number_format($orderRegel['fondsKoers'],4,",","."),number_format($orderRegel['brutoBedrag'],2,",",".")));
$pdf->Row(array(""));
$pdf->Row(array(""));
$pdf->Row(array("","","Opgelopen rente",number_format($orderRegel['opgelopenRente'],2,",",".")));
$pdf->Row(array("","","Provisie",number_format($orderRegel['kosten'],2,",",".")));
$pdf->Row(array("","","Nettobedrag",number_format($orderRegel['nettoBedrag'],2,",",".")));
$pdf->Row(array(""));
$pdf->Row(array(""));

$pdf->Row(array(""));
$pdf->Row(array(""));



$pdf->Row(array("Transactiedatum",$orderRegel['handelsDag']." ".$orderRegel['handelsTijd']));
$pdf->Row(array("Valutadatum",$orderRegel['handelsDag']));
$pdf->Row(array("Beurs",$orderRegel['beurs']));
if($orderRegel['aanvullendeInfo'] != '')
  $pdf->Row(array("Extra info",$orderRegel['aanvullendeInfo']));


if(!$_GET['regelId'])
{
  $query="UPDATE OrderRegels SET printDate='$now' WHERE id = '$regelId'";
  $db->SQL($query);
  $db->Query();
}

}



		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$pdf->Output("fondsmutaties.pdf","D");

?>