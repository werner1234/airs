<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/01/05 16:06:05 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: CRM_naw_faxMakePDF.php,v $
 		Revision 1.1  2006/01/05 16:06:05  cvs
 		eerste CRM test
 		
 		Revision 1.2  2005/12/14 12:35:13  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/22 14:31:07  cvs
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");

$DB = new DB();
$DB->SQL("SELECT * FROM naw_fax WHERE id=".$_GET[id]);
$record = $DB->lookupRecord();

$pdf = new PDFbase('P','mm','A4');
$pdf->SetAutoPageBreak(true,15); 
$pdf->pagebreak = 190;
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Body("test");


$pdf->SetTableWidths(array(45,5,100));
$pdf->SetTableAligns(array("L","C","L"));
$pdf->SetX(10);
$pdf->SetY(35);
$pdf->SetFont('Arial','',15);
$pdf->Cell(190,10,"Begeleidend schrijven",0,0,"C");
$pdf->SetY(45);
$pdf->SetFont('Arial','',10);
$pdf->AddTableRow(array("Faxnummer",":",$record[fax]));
$pdf->AddTableRow(array("Datum",":",kdbdatum($record[datum])));
$pdf->AddTableRow(array("Aan",":",$record[naam]));
$pdf->AddTableRow(array("T.a.v.",":",$record[tav]));
$pdf->AddTableRow(array("Betreft",":",$record[onderwerp]));
$pdf->AddTableRow(array("Pagina's",":",$record[paginas]." (inclusief dit blad)"));
$pdf->line(10,$pdf->GetY()+2,200,$pdf->GetY()+2);
$pdf->addBodyText();
$pdf->WriteHTML($record[text]);
$pdf->Output();
?>