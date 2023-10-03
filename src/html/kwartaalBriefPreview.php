<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.2 $

 		$Log: kwartaalBriefPreview.php,v $
 		Revision 1.2  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.1  2008/05/16 08:09:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/01/06 12:26:08  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/28 07:31:48  cvs
 		*** empty log message ***



*/
//$AEPDF2=true;
include_once("wwwvars.php");
//define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/rapportBrief.php");

$pdf = new PDFRapport('P','mm');
//$pdf->SetFont('times');
	
$brief = new kwartaalBrief($pdf,'kwartaalBrief');
$brief->maakBrief();

$pdf->Output();


?>