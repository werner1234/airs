<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2006/01/05 16:06:05 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: CRM_nawListMakePdf.php,v $
 		Revision 1.1  2006/01/05 16:06:05  cvs
 		eerste CRM test
 		
 		Revision 1.2  2005/12/14 12:35:13  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/23 09:29:48  cvs
 		*** empty log message ***
 		
 	
*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_pdfBase.php");

switch ($_GET[sql]) 
{
	case "deb":
		$setWhere = "debiteur = 1 AND aktief=1";
		$subHeader = ", Debiteuren";
		break;
  case "cre":
		$setWhere = "crediteur = 1 AND aktief=1";
		$subHeader = ", Crediteuren";
		break;
	case "inaktief":
		$setWhere = "aktief <> 1";
		$subHeader = ", inaktieve relaties";
		break;
	default:
	  $setWhere = "aktief = 1";
		$subHeader = ", alle aktieve relaties";
		break;
}
if ($_GET[selectie])
{
  $s = $_GET[selectie];
  $setSearch = " AND (naam LIKE '%$s%' OR a_plaats LIKE '%$s%') ";
}

$DB = new DB();
$query = "SELECT * FROM naw WHERE $setWhere $setSearch";
$DB->SQL($query);
$records = $DB->Query();

$pdf = new PDFbase('P','mm','A4');
//$pdf->mutlipageHeader = false;
$pdf->SetAutoPageBreak(true,15); 
$pdf->pagebreak = 190;
$pdf->AliasNbPages();
$pdf->AddPage();
//$pdf->Body("test");
$pdf->SetFont('Arial','',10);
$pdf->SetTableWidths(array("55","65","65"));
$pdf->SetTableAligns(array("L","L","L"));

while ($row = $DB->nextRecord())
{
  $pdf->AddTableRow(array($row[naam],$row[a_adres],$row[a_pc]." ".$row[a_plaats]));
}


$pdf->Output();

?>