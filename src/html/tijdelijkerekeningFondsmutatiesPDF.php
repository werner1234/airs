<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:48:19 $
 		File Versie					: $Revision: 1.21 $
 		
 		$Log: tijdelijkerekeningFondsmutatiesPDF.php,v $
 		Revision 1.21  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/11/16 14:55:43  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.18  2015/10/07 19:34:27  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2015/10/04 11:49:47  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2015/01/28 20:03:38  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/01/18 17:28:39  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/11/06 13:06:45  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2009/10/21 16:06:28  rvv
 		*** empty log message ***

 		Revision 1.5  2009/07/12 09:29:31  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/07/06 07:15:25  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2009/06/10 12:29:49  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/06/07 10:26:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/05/30 15:02:22  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2009/03/14 11:42:06  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2009/01/20 17:46:01  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:04:51  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/11/27 13:19:18  cvs
 		CRM
 		- verjaardaglijst
 		- velden omzetten van extra velden naar naw
 		- excel van tijdelijke rekening mutaties
 		
 	
*/

include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportVertaal.php");
include_once("rapport/PDFOverzicht.php");


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



$pdf = new PDFOverzicht('P','mm');
		
$pdf->SetAutoPageBreak(true,15);
$pdf->pagebreak = 280;
$pdf->__appvar = $__appvar;

$db=new DB();
if(checkAccess())
  $query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles ";
else
  $query="SELECT Portefeuilles.Portefeuille, VermogensbeheerdersPerGebruiker.Gebruiker 
  FROM VermogensbeheerdersPerGebruiker Join Portefeuilles ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
  WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ";

$db->SQL($query);
$portefeuille = $db->lookupRecord();

loadLayoutSettings($pdf, $portefeuille['Portefeuille']);	

$pdf->rapport_type = "Fondsmutaties";
$pdf->rapport_voettext='';
if($__appvar["bedrijf"] == 'RCN')
{
  $pdf->SetFont($pdf->rapport_font, 'B', 9);
}
else
{
  $pdf->SetFont($pdf->rapport_font, 'B', 8);
}
$pdf->AddPage();


$query = "
SELECT 
  Portefeuilles.Client as Client,
  Portefeuilles.Portefeuille as Portefeuille,
  TijdelijkeRekeningmutaties.Rekening as Rekening,
  TijdelijkeRekeningmutaties.Transactietype as Transactietype,
  TijdelijkeRekeningmutaties.Aantal as Aantal,
  TijdelijkeRekeningmutaties.Fonds as Fonds,
  TijdelijkeRekeningmutaties.Fondskoers as Fondskoers,
  TijdelijkeRekeningmutaties.Bedrag as Totaal,
  TijdelijkeRekeningmutaties.Valuta as Valuta,
  TijdelijkeRekeningmutaties.Omschrijving as FondsOmschrijving,
  TijdelijkeRekeningmutaties.Valutakoers  as Valutakoers,
  Fondsen.OptieBovenliggendFonds,
  IF(Fondsen.OptieBovenliggendFonds='',TijdelijkeRekeningmutaties.Fonds,CONCAT(Fondsen.OptieBovenliggendFonds,'Optie')) as sortering,
  TijdelijkeRekeningmutaties.bankTransactieId
FROM 
  (TijdelijkeRekeningmutaties, Portefeuilles)
JOIN 
  Rekeningen ON Rekeningen.Rekening = TijdelijkeRekeningmutaties.Rekening AND Rekeningen.consolidatie=0
LEFT JOIN Fondsen ON TijdelijkeRekeningmutaties.Fonds = Fondsen.Fonds
WHERE
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND TijdelijkeRekeningmutaties.change_user = '$USR' AND
  TijdelijkeRekeningmutaties.Grootboekrekening = 'FONDS'
ORDER BY 
  Client,sortering,Transactietype ASC";

$db = new DB();
$db2= new DB();
$db->SQL($query); 
$db->Query();

$trans=array('V/O'=>'V','V/S'=>'V','A/O'=>'A','A/S'=>'A');
  
$row = 1;
while ($record = $db->nextRecord())
{
  $query = "SELECT SUM(Bedrag) as kosten FROM TijdelijkeRekeningmutaties WHERE Omschrijving = '".mysql_real_escape_string($record['FondsOmschrijving'])."' AND 
                   TijdelijkeRekeningmutaties.Grootboekrekening IN('KOST','TOB','KOBU','RENOB','RENME') AND 
                   TijdelijkeRekeningmutaties.Rekening = '".mysql_real_escape_string($record['Rekening'])."' AND 
                   TijdelijkeRekeningmutaties.bankTransactieId = '".$record['bankTransactieId']."' AND
                   TijdelijkeRekeningmutaties.change_user = '$USR'";
  $db2->SQL($query);
  $kosten=$db2->lookupRecord();
  $record['Totaal'] += $kosten['kosten'];
  $record['Regel']=$row;
  
  $record['Rekening']=substr($record['Rekening'],0,strlen($record['Rekening'])-3);
  
  if($pdf->rapport_layout == 13)
  {
    if(key_exists($record['Transactietype'],$trans))
    {
      $record['Transactietype']=$trans[$record['Transactietype']];
    }
    $aantal = formatGetal($record['Aantal'],0);
  }
  else 
  $aantal = formatGetal($record['Aantal'],2);
  
  if(strlen($record['Client'])>13)
    $client=substr($record['Client'],0,11)."..";
  else 
    $client=$record['Client'];  
  
  $pdf->Row(array($record['Regel'],$client,$record['Portefeuille'],$record['Transactietype'],$aantal,$record['Fonds'],formatGetal($record['Fondskoers'],2),formatGetal($record['Totaal'],2),$record['Valuta'],formatGetal($record['Valutakoers'],4)));
  
  if($record['OptieBovenliggendFonds'])
  {
    
//    $pdf->Row(array('','','','','',$record['OptieBovenliggendFonds']));
  }
  
  $row++;
}

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	//	$pdf->Output("fondsmutaties.pdf","D");
$pdf->Output();
?>