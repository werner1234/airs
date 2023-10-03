<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.4 $

 		$Log: rapportXlsAfdrukken.php,v $
 		Revision 1.4  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.3  2010/11/17 17:15:58  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/05/06 09:30:02  rvv
 		*** empty log message ***

 		Revision 1.1  2007/08/02 14:39:32  rvv
 		*** empty log message ***

*/

//listarray($_POST);
// listarray($_SESSION);
  include_once("../classes/AE_cls_progressbar.php");
  include_once("../classes/AE_cls_fpdf.php");
  include_once('../classes/excel/Writer.php');
  include_once("../classes/portefeuilleSelectieClass.php");

 	include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOverzicht.php");
	include_once("rapport/MutatievoorstelFondsen.php");
	include_once("rapport/Fondsen.php");
	include_once("rapport/Geaggregeerdoverzicht.php");
	include_once("rapport/Modelcontrole.php");
	include_once("rapport/Modelrapport.php");
	include_once("rapport/PDFRapport.php");
	include_once("rapport/RapportMOD.php");

	include_once("rapport/PDFRapport.php");
	include_once("rapport/CashPositie.php");
	include_once("rapport/Managementoverzicht.php");
	include_once("rapport/ManagementoverzichtHAR.php");
	include_once("rapport/Valutarisicooverzicht.php");
	include_once("rapport/Risicometing.php");
	include_once("rapport/Risicoanalyse.php");
	include_once("rapport/Zorgplichtcontrole.php");
	include_once("rapport/PortefeuilleIndex.php");
	include_once("rapport/PortefeuilleParameters.php");


  include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOptieOverzicht.php");
	include_once("rapport/PDFRapport.php");
	include_once("rapport/OptieExpiratieLijst.php");
	include_once("rapport/OptieGeschrevenPositie.php");
	include_once("rapport/OptieOngedektePositie.php");
	include_once("rapport/OptieLiquideRuimte.php");
	include_once("rapport/OptieVrijePositie.php");


	global $__appvar;

	$rapportnaam = 'excel';
 	$filename = $rapportnaam.".xls";
// $filename = '/share/webroot/rvv/AIRS/temp/test.xls';

  echo template($__appvar["templateContentHeader"],$content);



$workbook = new Spreadsheet_Excel_Writer($__appvar['tempdir'].$filename);

$i=0;
 $selectDataTmp['modelcontrole_filter'] = $selectData['modelcontrole_filter'];
 $selectDataTmp["modelcontrole_rapport"] = $selectData["modelcontrole_rapport"];

 foreach ($_SESSION['xlsBatch'] as $rapport)
 {

   if($_POST['overRuleSelectie'] == 1)
   {
     $rapport['vermogensbeheerderVan']  = $_POST['vermogensbeheerderVan'];
     $rapport['vermogensbeheerderTm']   = $_POST['vermogensbeheerderTm'];
     $rapport['accountmanagerVan']      = $_POST['accountmanagerVan'];
     $rapport['accountmanagerTm']       = $_POST['accountmanagerTm'];
     $rapport['clientVan']              = $_POST['clientVan'];
     $rapport['clientTm']               = $_POST['clientTm'];
     $rapport['portefeuilleVan']        = $_POST['portefeuilleVan'];
     $rapport['portefeuilleTm']         = $_POST['portefeuilleTm'];
     $rapport['depotbankVan']           = $_POST['depotbankVan'];
     $rapport['depotbankTm']            = $_POST['depotbankTm'];
     $rapport['SoortOvereenkomstVan']   = $_POST['SoortOvereenkomstVan'];
     $rapport['SoortOvereenkomstTm']    = $_POST['SoortOvereenkomstTm'];
   }

   $rapport['geconsolideerd']           = $_POST['geconsolideerd'];

   if($_POST['overRuleDatum'] == 1)
   {
     $rapport['datumTm'] = $_POST['datumTm'];
     $rapport['datumVan'] = $_POST['datumVan'];
   }

 $selectData = $rapport;
 $selectData['datumTm'] = form2jul($selectData['datumTm']);
 $selectData['datumVan'] = form2jul($selectData['datumVan']);
  $prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();

	$type = array();
	$type = explode('__',$rapport['rapport']);

	//listarray($type);
	//if($selectData['modelcontrole_filter'] == "gekoppeld")


$selectData['modelcontrole_filter'] = '';
$selectData['modelcontrole_rapport'] = '';

$rapNaam = $i.'_'.substr($type[1],0,28);
$worksheet[$i] =& $workbook->addWorksheet($rapNaam);

if ($type[0] == 'fonds')
{
	switch($type[1])
	{
		case "Mutatievoorstel Fondsen" :
			$rapport = new MutatievoorstelFondsen( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MUT";
		break;
		case "Fondsoverzicht" :
			$rapport = new Fondsen( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_FON";
		break;
		case "Geaggregeerd Portefeuille Overzicht" :
			$rapport = new Geaggregeerdoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_GPO";
		break;
		case "Modelcontrole" :
		  $selectData['modelcontrole_filter']  = $selectDataTmp['modelcontrole_filter'];
		  $selectData['modelcontrole_rapport'] = $selectDataTmp['modelcontrole_rapport'];
			$rapport = new Modelcontrole( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MOD";
		break;
		case "MutatievoorstelPortefeuille" :
			$rapport = new Modelrapport( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MOD";
		break;
  }
}
if ($type[0] == 'management')
{
  	switch($type[1])
	{
		case "CashPosities" :
			$rapport = new CashPositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_CASH";
		break;
			case "Managementoverzicht" :
			if($userLayout == 1)
			{
				$selectData['title'] = "Overzicht portefeuille-opbouw ~ Hartfort & Co Asset Management B.V. ~";
				$selectData['userLayout'] = 1;
				$rapport = new ManagementoverzichtHAR( $selectData );
			}
			elseif ($userLayout == 12)
			{
				$selectData['title'] = "Overzicht portefeuille-opbouw";
				$selectData['userLayout'] = 12;
				$rapport = new ManagementoverzichtHAR( $selectData );
			}
			else
				$rapport = new Managementoverzicht( $selectData );

			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "Valuta Risico" :
			$rapport = new Valutarisicooverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "Risicometing" :
			$rapport = new Risicometing( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "Risicoanalyse" :
			$rapport = new Risicoanalyse( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "Zorgplichtcontrole" :
			$rapport = new Zorgplichtcontrole( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "PortefeuilleIndex" :
			$rapport = new PortefeuilleIndex( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "PortefeuilleParameters" :
			$rapport = new PortefeuilleParameters( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;

	}
 }

if ($type[0] == 'optietools')
{
  	switch($type[1])
	{
		case "OptieExpiratieLijst" :
			$rapport = new OptieExpiratieLijst( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "OptieGeschrevenPositie" :
			$rapport = new OptieGeschrevenPositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "OptieOngedektePositie" :
			$rapport = new OptieOngedektePositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "OptieVrijePositie":
			$rapport = new OptieVrijePositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;
		case "OptieLiquideRuimte":
			$rapport = new OptieLiquideRuimte( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
		break;

	}
 }



	$rapport->pdf->fillXlsSheet($worksheet[$i]);

$i++;
}

 $prb->hide();

$workbook->close();

?>
<script type="text/javascript">
function pushpdf(file,save)
{

	var width='800';
	var height='600';
	var target = '_blank';
	var location = 'pushFile.php?filetype=xls&file=' + file;
	if(save == 1)
	{
		// opslaan als bestand
		document.location = location + '&action=attachment';
	}
	else
	{
		// pushen naar PDF reader
		var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
		doc.document.location = location;
	}
}
pushpdf('<?=$filename?>','1');
</script>



<?
  echo template($__appvar["templateContentFooter"],$content);