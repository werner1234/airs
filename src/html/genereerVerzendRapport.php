<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/07/30 10:17:58 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: genereerVerzendRapport.php,v $
 		Revision 1.3  2017/07/30 10:17:58  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/03/29 16:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/05/23 15:27:56  rvv
 		*** empty log message ***
 		
 	
*/
define('FPDF_FONTPATH',"./font/");
include_once("wwwvars.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportVertaal.php");
include_once("rapport/PDFRapport.php");

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

$rapportData=$_SESSION['rapportData'];

$pdf = new PDFRapport('L','mm');

$pdf->rapport_type = "FRONT";
$pdf->AddPage();

$pdf->SetFont('helvetica','B',14);
$pdf->MultiCell(285,4,"Verzendrapport voorlopige rekeningmutaties",0,'C');
$pdf->SetFont('helvetica','',10);
$pdf->SetWidths(array(50,50,50));
$pdf->ln(8);
$pdf->Row(array('verzenddatum:',$rapportData['verzendTijd']));
$pdf->Row(array('gebruiker:',$USR));
$pdf->ln(8);
$pdf->SetWidths(array(20,30,25,20,55,15,15,15,25,25,25));
$pdf->SetWidths(array(18,30,30,20,58,25,15,10,12,20,20,22));
$pdf->SetAligns(array('L','L','L','R','L','L','L','C','L','R','R','R','R','R'));
$pdf->SetFont('helvetica','B',8);
$pdf->Row(array("Afschrift\nnummer",'Rekening',"Client","Boek\ndatum","Omschrijving","Fonds\nImportCode","Groot\nboek",'Type','Valuta','Aantal',"Fonds\nkoers",'Bedrag'));
$pdf->SetFont('helvetica','',8);
$db = new DB();
//
/* test
$query = "SELECT * FROM VoorlopigeRekeningmutaties limit 2000";
$db->SQL($query);
$db->Query();
while($rekeningmutatieData = $db->NextRecord())
{
  $rapportData['mutaties'][$rekeningmutatieData['Afschriftnummer']][]=$rekeningmutatieData;
}
*/
//
foreach ($rapportData['mutaties'] as $afschrift=>$regels)
{
 // $pdf->Row(array($afschrift));
  foreach ($regels as $regel)
  {
    $query="SELECT Portefeuilles.Client FROM Portefeuilles Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille WHERE Rekeningen.Rekening = '".$regel['Rekening']."'";
    $db->SQL($query);
    $client=$db->lookupRecord();
    $query="SELECT Fondsen.FondsImportCode FROM Fondsen WHERE Fondsen.Fonds = '".$regel['Fonds']."'";
    $db->SQL($query);
    $fonds=$db->lookupRecord();

    $pdf->Row(array(
    $afschrift,
    $regel['Rekening'],
    $client['Client'],
    date("d-m-Y",
    db2jul($regel['Boekdatum'])),
    $regel['Omschrijving'],
    $fonds['FondsImportCode'],
    $regel['Grootboekrekening'],
    $regel['Transactietype'],
    $regel['Valuta'],
    number_format($regel['Aantal'],2),
    number_format($regel['Fondskoers'],4),
    number_format($regel['Bedrag'],2)));
  }
}

$pdf->Output();

?>