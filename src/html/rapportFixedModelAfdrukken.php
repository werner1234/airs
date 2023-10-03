<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.5 $

 		$Log: rapportFixedModelAfdrukken.php,v $
 		Revision 1.5  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.4  2018/02/24 18:31:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/08 07:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/06/04 16:12:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/04/27 17:54:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/25 10:52:21  rvv
 		*** empty log message ***

 		Revision 1.1  2010/03/14 17:33:53  rvv
 		*** empty log message ***



*/

include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/RapportGRAFIEK.php");
include_once("rapport/RapportDOORKIJK.php");
include_once("rapport/RapportBerekeningen.php");

$_SESSION['lastPost'] = $_GET;
if(!empty($_GET['Datum']))
{
	$dd = explode($__appvar["date_seperator"],$_GET['Datum']);
}
else
{
	echo "<b>Fout: geen datum opgegeven!</b>";
	exit;
}
$portefeuille = $_GET['Portefeuille'];
if(empty($portefeuille))
{
	echo "<b>Fout: geen portefeuille opgegeven </b>";
	exit;
}



	$rapportageDatum = $dd[0]."-".$dd[1]."-".$dd[2];
	$__appvar['fixedPortefeuilleDatum']=$rapportageDatum;

	//listarray(berekenFixedModelPortefeuille($portefeuille,$rapportageDatum));
	vulTijdelijkeTabel(berekenFixedModelPortefeuille($portefeuille,$rapportageDatum),'m'.$portefeuille, $rapportageDatum);
	$pdf = new PDFRapport('L','mm');
	loadLayoutSettings($pdf, $portefeuille);

	$pdf->SetAutoPageBreak(true,15);
	$pdf->pagebreak = 190;
	$pdf->__appvar = $__appvar;
	$pdf->extra = $_GET['extra'];
  $pdf->lastPOST = $_GET;
  //$pdf->clientGegevens=$clientGegevens;

  if($_GET['rapport']=='DOORKIJK')
	{
		if (file_exists("rapport/include/RapportDOORKIJK_L" . $pdf->rapport_layout . ".php"))
		{
			include_once("rapport/include/RapportDOORKIJK_L" . $pdf->rapport_layout . ".php");
			$classString = 'RapportDOORKIJK_L' . $pdf->rapport_layout;
			$rapport = new $classString($pdf, 'm' . $portefeuille, $rapportageDatum, $rapportageDatum);
		}
		else
		{
			$rapport = new RapportDOORKIJK($pdf, 'm' . $portefeuille, $rapportageDatum, $rapportageDatum);
		}
	}
  else
	{
		if (file_exists("rapport/include/RapportGRAFIEK_L" . $pdf->rapport_layout . ".php"))
		{
			include_once("rapport/include/RapportGRAFIEK_L" . $pdf->rapport_layout . ".php");
			$classString = 'RapportGRAFIEK_L' . $pdf->rapport_layout;
			$rapport = new $classString($pdf, 'm' . $portefeuille, $rapportageDatum, $rapportageDatum);
		}
		else
		{
			$rapport = new RapportGRAFIEK($pdf, 'm' . $portefeuille, $rapportageDatum, $rapportageDatum);
		}
	}
	$rapport->writeRapport();
	verwijderTijdelijkeTabel('m'.$portefeuille, $rapportageDatum);

	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	$pdf->Output($portefeuille.$rapportnaam.".pdf","D");

//	$pdf->Output();

?>