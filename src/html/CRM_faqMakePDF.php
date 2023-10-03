<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/11/23 14:51:31 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: CRM_faqMakePDF.php,v $
 		Revision 1.2  2014/11/23 14:51:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/01/05 16:06:05  cvs
 		eerste CRM test
 		
 		Revision 1.2  2005/12/14 12:35:13  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2005/11/22 15:21:43  jwellner
 		aanpassing pdf
 		
 		Revision 1.1  2005/11/22 14:31:07  cvs
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");

$DB = new DB();
$DB->SQL("SELECT * FROM CRM_faq WHERE id=".$_GET[id]);
$record = $DB->lookupRecord();

$pdf = new PDFbase('P','mm','A4');
//$pdf->mutlipageHeader = false;
$pdf->noStrip=true;
$pdf->SetAutoPageBreak(true,15); 
$pdf->pagebreak = 190;
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Body("test");
$pdf->SetFont('Arial','',10);

$message = "Kennisbankitem :".$record[kop]."\nLaatste mutatie :".kdbdatum($record[change_date])."\n\n";

$pdf->addBodyText($message);
$pdf->WriteHTML($record[txt]);
$pdf->Output();
?>