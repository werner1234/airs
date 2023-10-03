<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 	Laatste aanpassing	: $Date: 2019/01/19 13:52:12 $
 	File Versie					: $Revision: 1.4 $

 	$Log: rapportExportEmail.php,v $
 	Revision 1.4  2019/01/19 13:52:12  rvv
 	*** empty log message ***
 	
 	Revision 1.3  2019/01/16 16:35:40  rvv
 	*** empty log message ***
 	
 	Revision 1.2  2018/08/18 12:40:14  rvv
 	php 5.6 & consolidatie
 	
 	Revision 1.1  2014/03/22 15:48:26  rvv
 	*** empty log message ***
 	
 	Revision 1.41  2010/05/02 10:16:05  rvv
 	*** empty log message ***

 	Revision 1.40  2009/05/03 10:22:58  rvv
 	*** empty log message ***

 	Revision 1.39  2009/01/30 07:10:23  cvs
 	PerfG toegevoegd in export

*/


//$AEPDF2=true;
$disable_auth = true;
include_once("wwwvars.php");

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_digidoc.php");

	function valid_email_quick($address)
  {
    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $address))
    {
      return false;
    }
    else
    {
      if (strlen($address)>0)
        return true;
      else
        return false;
    }
  }

$_POST = array_merge($_POST,$_GET);

if(!empty($_POST[type]))
{
	switch($_POST[type])
	{
		case "dag" :
			$rapportType = "dag";
		break;
		case "maand" :
			$rapportType = "maand";
		break;
		case "kwartaal" :
			$rapportType = "kwartaal";
			if($_POST[inclFactuur] == 1)
				$inclFactuur = 1;
		break;
		case "eMailKwartaal" :
			$rapportType = "kwartaal";
			$eMail = true;
			if($_POST['inclFactuur'] == 1)
				$inclFactuur = 1;
		break;
	  case "eDossierKwartaal" :
			$rapportType = "kwartaal";
			$eDossier = true;
			if($_POST['inclFactuur'] == 1)
				$inclFactuur = 1;
		break;
		default :
			echo "onbekend rapportagetype...";
			exit;
	}
	$_linebreak = "<br>";
	echo template($__appvar["templateContentHeader"],$content);
}
else
{
	switch($argv[1])
	{
		case "dag" :
			$rapportType = "dag";
		break;
		case "maand" :
			$rapportType = "maand";
		break;
		case "kwartaal" :
			$rapportType = "kwartaal";
		break;
		default :
			echo "onbekend rapportagetype...";
			exit;
	}
	$_linebreak = "\n";
}


if($rapportType == "maand")
  $YTDfield =	 " Vermogensbeheerders.maandRapportageYTD as YTD,	";
elseif($rapportType == "kwartaal")
	$YTDfield =	 " Vermogensbeheerders.kwartaalRapportageYTD as YTD, ";

include_once("rapport/PDFRapport.php");
include_once("rapport/RapportFront.php");
include_once("rapport/RapportHSE.php");
include_once("rapport/RapportMUT.php");
include_once("rapport/RapportMUT2.php");
include_once("rapport/RapportOIR.php");
include_once("rapport/RapportOIB.php");
include_once("rapport/RapportOIH.php");
include_once("rapport/RapportOIBS.php");
include_once("rapport/RapportOIBS2.php");
include_once("rapport/RapportOIV.php");
include_once("rapport/RapportPERF.php");
include_once("rapport/RapportTRANS.php");
include_once("rapport/RapportVHO.php");
include_once("rapport/RapportVOLK.php");
include_once("rapport/RapportHSEP.php");
include_once("rapport/RapportVOLKD.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/RapportMODEL.php");

//include_once("rapport/rapport/PDFFactuur.php");
//include_once("rapport/rapport/Factuur.php");
include_once("rapport/factuur/PDFFactuur.php");
include_once("rapport/factuur/Factuur.php");
include_once("rapport/RapportPERFG.php");

echo "start ".$rapportType." rapportage ".$_linebreak;

// set van tot datum.
if($_POST[datumTm])
{
	$vandatum = jul2sql(form2jul($_POST[datumTm]));
}
else
{
	$vandatum = getLaatsteValutadatum();
}

$jaar = date("Y",db2jul($vandatum));

$dateArray = explode("-",$vandatum);
// startdatum van rapportage kwartaal vaststellen

switch (intval("$dateArray[1]"))
{
	case 1:
	case 2:
	case 3:
	  $endPrevQ = form2jul("31-12-".$jaar-1);
		$startQ = form2jul("1-1-".$jaar);
		break;
	case 4:
	case 5:
	case 6:
	  $endPrevQ = form2jul("31-3-".$jaar);
		$startQ = form2jul("1-4-".$jaar);
		break;
	case 7:
	case 8:
	case 9:
	  $endPrevQ = form2jul("30-6-".$jaar);
		$startQ = form2jul("1-7-".$jaar);
		break;
	case 10:
	case 11:
	case 12:
	  $endPrevQ = form2jul("30-9-".$jaar);
		$startQ = form2jul("1-10-".$jaar);
		break;
}

// check of datum 2 niet ouder is dan 2 dagen!
$rapportageDatum[a] = jul2sql(form2jul("1-1-".$jaar));
$rapportageDatum[b] = $vandatum;

// check of datum 2 niet ouder is dan 2 dagen!

// controle op einddatum portefeuille

	$selectData[datumVan] 							= form2jul($_POST['datumVan']);
	$selectData[datumTm] 								= form2jul($_POST['datumTm']);
	$selectData[vermogensbeheerderVan] 	= $_POST['vermogensbeheerderVan'];
	$selectData[vermogensbeheerderTm]  	= $_POST['vermogensbeheerderTm'];
	$selectData[accountmanagerVan] 			= $_POST['accountmanagerVan'];
	$selectData[accountmanagerTm] 			= $_POST['accountmanagerTm'];
	$selectData[portefeuilleVan] 				= $_POST['portefeuilleVan'];
	$selectData[portefeuilleTm] 				= $_POST['portefeuilleTm'];
	$selectData[depotbankVan] 					= $_POST['depotbankVan'];
	$selectData[depotbankTm] 						= $_POST['depotbankTm'];
	$selectData[AFMprofielVan] 					= $_POST['AFMprofielVan'];
	$selectData[AFMprofielTm] 					= $_POST['AFMprofielTm'];
	$selectData[RisicoklasseVan] 				= $_POST['RisicoklasseVan'];
	$selectData[RisicoklasseTm] 				= $_POST['RisicoklasseTm'];
	$selectData[SoortOvereenkomstVan] 	= $_POST['SoortOvereenkomstVan'];
	$selectData[SoortOvereenkomstTm] 		= $_POST['SoortOvereenkomstTm'];
	$selectData[RemisierVan] 						= $_POST['RemisierVan'];
	$selectData[RemisierTm] 						= $_POST['RemisierTm'];
	$selectData[GrootboekVan] 					= $_POST['GrootboekVan'];
	$selectData[GrootboekTm] 						= $_POST['GrootboekTm'];
	$selectData[backoffice] 						= true;
	$selectData[maandrapportage] 				= $_POST['maandrapportage'];
	$selectData[kwartaalrapportage] 		= $_POST['kwartaalrapportage'];
	$selectData[afdrukSortering] 				= $_POST['afdrukSortering'];
	$selectData[inclFactuur] 						= $_POST['inclFactuur'];
	$selectData['clientTm'] 						= $_POST['clientTm'];
	$selectData['clientVan'] 						= $_POST['clientVan'];
	$factuurnummer							        = $_POST['factuurnummer'];
	$drempelPercentage                  = $_POST['drempelPercentage'];

	$extraquery  = " Portefeuilles.Einddatum > '".$rapportageDatum[b]."' AND";

	if($selectData[portefeuilleTm])
		$extraquery .= " (Portefeuilles.Portefeuille >= '".$selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$selectData[portefeuilleTm]."') AND";
	if($selectData[vermogensbeheerderTm])
		$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$selectData[vermogensbeheerderTm]."') AND ";
	if($selectData[accountmanagerTm])
		$extraquery .= " (Portefeuilles.Accountmanager >= '".$selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$selectData[accountmanagerTm]."') AND ";
	if($selectData[depotbankTm])
		$extraquery .= " (Portefeuilles.Depotbank >= '".$selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$selectData[depotbankTm]."') AND ";
	if($selectData[AFMprofielTm])
		$extraquery .= " (Portefeuilles.AFMprofiel >= '".$selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$selectData[AFMprofielTm]."') AND ";
	if($selectData[RisicoklasseTm])
		$extraquery .= " (Portefeuilles.Risicoklasse >= '".$selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$selectData[RisicoklasseTm]."') AND ";
	if($selectData[SoortOvereenkomstTm])
		$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$selectData[SoortOvereenkomstTm]."') AND ";
	if($selectData[RemisierTm])
		$extraquery .= " (Portefeuilles.Remisier >= '".$selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$selectData[RemisierTm]."') AND ";

	if($selectData[kwartaalrapportage] == 1)
		$extraquery .= " Portefeuilles.kwartaalAfdrukken > 0 AND ";
	elseif($selectData[maandrapportage] == 1)
	  $extraquery .= " Portefeuilles.MaandAfdrukken > 0 AND ";

	if($selectData['clientTm'])
		$extraquery .= " (Portefeuilles.Client >= '".$selectData['clientVan']."' AND Portefeuilles.Client <= '".$selectData['clientTm']."') AND ";

if($USR != '' && !checkAccess())
{
		$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	  $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
}

// check begin datum rapportage!
$query = " SELECT 																	 ".
				 " Portefeuilles.Portefeuille, 							 ".
				 " Portefeuilles.Client as Naam, 							         ".
				 " Portefeuilles.Startdatum, 								 ".
				 " Portefeuilles.RapportageValuta,           ".
				 " Portefeuilles.BeheerfeeAantalFacturen,
			  	 Accountmanagers.Naam as Accountmanager,
				  Vermogensbeheerders.Email as VermogensbeheerdersEmail,
				    CRM_naw.email as Email,
				    CRM_naw.wachtwoord,
				    CRM_naw.rapportageVinkSelectie,
				    CRM_naw.naam,
				    CRM_naw.naam1,
				    CRM_naw.rapportageVinkSelectie,  ".
				 " Vermogensbeheerders.Export_dag_pad,		   ".
				 " Vermogensbeheerders.Export_maand_pad,	   ".
				 " Vermogensbeheerders.Export_kwartaal_pad,	 ".
				 " Vermogensbeheerders.Export_data_kwartaal, ".
				 " Vermogensbeheerders.Export_data_maand,	   ".
				 " Vermogensbeheerders.Export_data_dag,			 ".
" $YTDfield ".
				 " Vermogensbeheerders.naamInExport 			 ".
				 " FROM (Portefeuilles, Vermogensbeheerders, VermogensbeheerdersPerBedrijf)
          LEFT JOIN Clienten on Portefeuilles.Client = Clienten.Client
          LEFT JOIN CRM_naw on Portefeuilles.Portefeuille = CRM_naw.Portefeuille
          LEFT JOIN Accountmanagers on Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
				 ".$join." WHERE ".$extraquery.
				 " Portefeuilles.Vermogensbeheerder 			 = Vermogensbeheerders.Vermogensbeheerder".
				 " AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
				   $beperktToegankelijk " ;
// asort

$DB = new DB();
$DB->SQL($query);
$DB->Query();
$records = $DB->records();

if($records <= 0)		{
	echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
	exit;
}
// todo : sorteer rapporttypes

// controlleer of datum a niet groter is dan datum b!
if(db2jul($rapportageDatum[a]) > db2jul($rapportageDatum[b]))
{
	echo "<b>Fout: Van datum kan niet groter zijn dan  T/m datum! </b>";
	exit;
}

echo $records." portefeuilles binnen de selectie".$_linebreak;
flush();
$teller = 0;
$lb = 0;

$jaarstart = $rapportageDatum[a];

while($pdata = $DB->nextRecord())
{
  if($eMail == true)
  {
    if(strlen($pdata['wachtwoord']) < 6 || !valid_email_quick($pdata['Email']))
      $afbreken = true;
    else
      $afbreken = false;
  }


  if($pdata['YTD'] == 1)
    $rapportageDatum['a'] = $jaarstart;
  else
  {
    $julEind = db2jul($rapportageDatum[b]);
    $maand = date('m',$julEind);
    $jaar = date('Y',$julEind);
    if($rapportType == 'maand')
    {
      $laatsteDagVorigeMaand = date('Y-m-d',mktime(0,0,0,$maand,0,$jaar));
      $rapportageDatum[a] = $laatsteDagVorigeMaand;
    }
    if(	$rapportType == "kwartaal")
    {
      if($maand < 4)
       $rapportageDatum['a'] = date('Y-m-d',mktime(0,0,0,1,1,$jaar));
      elseif($maand < 7)
       $rapportageDatum['a'] = date('Y-m-d',mktime(0,0,0,4,0,$jaar));
      elseif ($maand < 10)
       $rapportageDatum['a'] = date('Y-m-d',mktime(0,0,0,7,0,$jaar));
      else
       $rapportageDatum['a'] = date('Y-m-d',mktime(0,0,0,10,0,$jaar));
    }
  }

	$exportData = unserialize($pdata["Export_data_".$rapportType]);

	reset($__appvar["Rapporten"]);


	// get / set rapport typen.
	while (list($key, $value) = each($__appvar["Rapporten"]))
	{
		if($exportData[$key]['checked'] > 0)
		{
			$rapport_typen[$key] = $exportData[$key]['volgorde'];
		}
	}

$CRMrapport=unserialize($pdata['rapportageVinkSelectie']);
if($rapportType == "kwartaal")
{
  if(is_array($CRMrapport['rap_k']))
  {
    $rapport_typen=array();
    foreach ($CRMrapport['rap_k'] as $key)
      $rapport_typen[$key] = $exportData[$key]['volgorde'];
  }
}


	if($_POST['voorbladWeergeven'])
	{
	$rapport_typen['FRONT']    = 0 ;
	}
	else
	  unset($rapport_typen['FRONT']);
	// get export path

  $exportPath = $__appvar['tempdir'];

	if(count($rapport_typen) >0 )
	{
		if(empty($exportPath))
		{
			echo "ongeldig export pad $exportPath ".$_linebreak;
			exit;
		}

		$portefeuille = $pdata[Portefeuille];
		$pdf->rapportCounter = $teller;

		if($selectData[datumVan] < db2jul($pdata[Startdatum]))
	   	$attStart = $pdata[Startdatum];
	  else
	   	$attStart = jul2sql($selectData[datumVan]);

		if(db2jul($rapportageDatum[a]) < db2jul($pdata[Startdatum]))
		{
			$startdatum = $pdata[Startdatum];
		}
		else
		{
			$startdatum = $rapportageDatum[a];
		}

		$julrapport 		= db2jul($startdatum);
		$rapportMaand 	= date("m",$julrapport);
		$rapportDag 		= date("d",$julrapport);
		$rapportJaar 		= date("Y",$julrapport);

		if($rapportMaand == 1 && $rapportDag == 1)
		{
			$startjaar = true;
			$extrastart = false;
		}
		else
		{
			$startjaar = false;
			$extrastart = mktime(0,0,0,1,1,$rapportJaar);

			if($extrastart < 	db2jul($pdata[Startdatum]))
			{
				$extrastart = $pdata[Startdatum];
			}
			else
			{
				$extrastart = jul2db($extrastart);
			}
		}

		$einddatum = $rapportageDatum[b];

		$fondswaarden[a] =  berekenPortefeuilleWaarde($portefeuille, $startdatum, $startjaar,$pdata['RapportageValuta'],$startdatum);
		$fondswaarden[b] =  berekenPortefeuilleWaarde($portefeuille, $einddatum,false,$pdata['RapportageValuta'],$startdatum);

		if ($rapportType == "kwartaal")
		{

		  //$qdat = jul2db($startQ);
		  $qdat = jul2db($endPrevQ);    // 2006-11-1 cvs startdatum van factuur klopte niet bij q export
		  verwijderTijdelijkeTabel($portefeuille,$qdat);
			$fondswaarden[c] =  berekenPortefeuilleWaarde($portefeuille, $qdat,true,$pdata['RapportageValuta'],$startdatum);
			vulTijdelijkeTabel($fondswaarden[c] ,$portefeuille,$qdat);
		}

		if($extrastart)
		{
			verwijderTijdelijkeTabel($portefeuille,$extrastart);
			$fondswaarden[c] =  berekenPortefeuilleWaarde($portefeuille, $extrastart,true,$pdata['RapportageValuta'],$startdatum);
			vulTijdelijkeTabel($fondswaarden[c] ,$portefeuille,$extrastart);
		}

		verwijderTijdelijkeTabel($portefeuille,$startdatum);
		verwijderTijdelijkeTabel($portefeuille,$einddatum);

		vulTijdelijkeTabel($fondswaarden[a] ,$portefeuille,$startdatum);
		vulTijdelijkeTabel($fondswaarden[b] ,$portefeuille,$einddatum);


		$pdata[kwartaalAfdrukken] = 1;

		// maak nieuwe PDF

		$pdf = new PDFRapport('L','mm');

		$pdf->SetAutoPageBreak(true,15);
		$pdf->pagebreak = 190;
		$pdf->__appvar = $__appvar;
		$pdf->FactuurDrempelPercentage = $drempelPercentage;

		$pdf->rapport_datumvanaf = $julrapport; //voor ATT voor 2008-01-01 $pdf->portefeuilledata['PerformanceBerekening'] = 1
		loadLayoutSettings($pdf, $portefeuille);

		if($_POST['logoOnderdrukken'])
	    $pdf->rapport_logo='';

	if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
	{
	  $pdf->rapportageValuta = $pdata['RapportageValuta'];
	  $pdf->ValutaKoersBegin  = getValutaKoers($pdf->rapportageValuta,$startdatum);
	  $pdf->ValutaKoersEind  = getValutaKoers($pdf->rapportageValuta,$einddatum);
	  $pdf->ValutaKoersStart = getValutaKoers($pdf->rapportageValuta,$rapportJaar."-01-01");//$rapportageDatumVanaf);
	}
	else
	{
	  $pdf->rapportageValuta = "EUR";
	  $pdf->ValutaKoersEind  = 1;
	  $pdf->ValutaKoersStart = 1;
	  $pdf->ValutaKoersBegin = 1;
	}
	$pdf->PortefeuilleStartdatum = $pdata['Startdatum'];


		$rapportNaam = "";


  $volgorde["ATT"] 		= $pdata["AfdrukvolgordeATT"];
  $volgorde["MODEL"]= $pdata["AfdrukvolgordeMODEL"];


		asort($rapport_typen, SORT_NUMERIC);

		reset($rapport_typen);

		while (list($key, $value) = each($rapport_typen))
		{
			switch($key)
			{

			  case "FRONT" :
			     if (file_exists("rapport/include/RapportFRONT_L".$pdf->rapport_layout.".php"))
				   {
				     include_once("rapport/include/RapportFRONT_L".$pdf->rapport_layout.".php");
				     $classString = 'RapportFRONT_L'.$pdf->rapport_layout;
	           $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
				   }
				   else
				     $rapport = new RapportFront($pdf, $portefeuille, $startdatum, $einddatum);
					 $rapport->writeRapport();
				break;
				case "HSE" :
					$rapport = new RapportHSE($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				case "MUT" :
					if($pdf->rapport_layout == 7)
					{
						$rapport = new RapportMUT2($pdf, $portefeuille, $startdatum, $einddatum);
					}
					else
					{
					  if($pdf->rapport_layout == 8 AND $rapportType == "kwartaal")
					  {
						  //$rapport = new RapportMUT($pdf, $portefeuille, jul2sql($endPrevQ), $einddatum);
						  $rapport = new RapportMUT($pdf, $portefeuille, $attStart, $einddatum);
					  }
						else
						  $rapport = new RapportMUT($pdf, $portefeuille, $startdatum, $einddatum);
					}
					$rapport->writeRapport();
				break;
				case "OIH" :
					$rapport = new RapportOIH($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				case "OIB" :
						if (file_exists("rapport/include/RapportOIB_L".$pdf->rapport_layout.".php"))
				    {
				     include_once("rapport/include/RapportOIB_L".$pdf->rapport_layout.".php");
				     $classString = 'RapportOIB_L'.$pdf->rapport_layout;
	           $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
				    }
				    else
				    {
							$rapport = new RapportOIB($pdf, $portefeuille, $startdatum, $einddatum);
				    }
					$rapport->writeRapport();
				break;
				case "OIS"  :
					if($pdf->rapport_layout == 11)
					{
						$rapport = new RapportOIBS2($pdf, $portefeuille, $startdatum, $einddatum);
					}
					else
					{
						$rapport = new RapportOIBS($pdf, $portefeuille, $startdatum, $einddatum);
					}

					$rapport->writeRapport();
				break;
				case "OIV" :
					$rapport = new RapportOIV($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				 case "PERF" :
					   if ($pdata['attributieInPerformance'] == 1)
					   {
					     $rapport = new RapportATT($pdf, $portefeuille, $startdatum, $einddatum);
					   }
					   else
					   {
					     if (file_exists("rapport/include/RapportPERF_L".$pdf->rapport_layout.".php"))
			         {
  			         include_once("rapport/include/RapportPERF_L".$pdf->rapport_layout.".php");
				         $classString = 'RapportPERF_L'.$pdf->rapport_layout;
	               $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
			         }
			         else
			           $rapport = new RapportPERF($pdf, $portefeuille, $startdatum, $einddatum);
					   }
					   $rapport->writeRapport();
					  break;
					  case "PERFG" :
			        if (file_exists("rapport/include/RapportPERFG_L".$pdf->rapport_layout.".php"))
			        {
  			       include_once("rapport/include/RapportPERFG_L".$pdf->rapport_layout.".php");
				       $classString = 'RapportPERFG_L'.$pdf->rapport_layout;
	             $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
			        }
			        else
			        {
				        $rapport = new RapportPERFG($pdf, $portefeuille, $startdatum, $einddatum);
			        }
					    $rapport->writeRapport();
					    break;
				/////////////////////////////
				case "TRANS" :
          if($pdf->rapport_layout == 8 AND $rapportType == "kwartaal")
          {
					//  $rapport = new RapportTRANS($pdf, $portefeuille, jul2sql($endPrevQ), $einddatum);
					$rapport = new RapportTRANS($pdf, $portefeuille, $attStart, $einddatum);
          }
					else
					  $rapport = new RapportTRANS($pdf, $portefeuille, $startdatum, $einddatum);

					$rapport->writeRapport();
				break;
				case "VHO" :
				  if (file_exists("rapport/include/RapportVHO_L".$pdf->rapport_layout.".php"))
	        {
			       include_once("rapport/include/RapportVHO_L".$pdf->rapport_layout.".php");
		         $classString = 'RapportVHO_L'.$pdf->rapport_layout;
             $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
			    }
			    else
					{
					 $rapport = new RapportVHO($pdf, $portefeuille, $startdatum, $einddatum);
					}
					$rapport->writeRapport();
				break;
				case "VOLK" :
				  if (file_exists("rapport/include/RapportVOLK_L".$pdf->rapport_layout.".php"))
	        {
			       include_once("rapport/include/RapportVOLK_L".$pdf->rapport_layout.".php");
 	           $classString = 'RapportVOLK_L'.$pdf->rapport_layout;
             $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
			    }
			    else
			    {
					  $rapport = new RapportVOLK($pdf, $portefeuille, $startdatum, $einddatum);
			    }
					$rapport->writeRapport();
				break;
				case "HSEP" :
					$rapport = new RapportHSEP($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				case "VOLKD" :
					$rapport = new RapportVOLKD($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				case "OIR" :
					$rapport = new RapportOIR($pdf, $portefeuille, $startdatum, $einddatum);
					$rapport->writeRapport();
				break;
				case "GRAFIEK" :
						  if (file_exists("rapport/include/RapportGRAFIEK_L".$pdf->rapport_layout.".php"))
			        {
  			        include_once("rapport/include/RapportGRAFIEK_L".$pdf->rapport_layout.".php");
				        $classString = 'RapportGRAFIEK_L'.$pdf->rapport_layout;
	              $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
			        }
			        else
			        {
					      $rapport = new RapportGRAFIEK($pdf, $portefeuille, $startdatum, $einddatum);
					    }
					    $rapport->writeRapport();
				break;
				case "ATT" :
				  	if($pdf->rapport_layout == 17)
						{
						  include_once("rapport/include/RapportATT_L17.php");
						  $rapport = new RapportATT_L17($pdf, $portefeuille, $startdatum, $einddatum);
						  $rapport->writeRapport();
						}
				    elseif ($pdata['attributieInPerformance'] == 1)
				    {
				     $rapport = new RapportATT($pdf, $portefeuille, $startdatum, $einddatum);
				     $rapport->writeRapport();
				    }
				break;
				case "MODEL" :
				     $rapport = new RapportMODEL($pdf, $portefeuille, $startdatum, $einddatum);
				     $rapport->writeRapport();
				break;
			}


		} // einde for loop

if (file_exists("rapport/include/RapportTemplate_L".$pdf->rapport_layout.".php"))
{
  include_once("rapport/include/RapportTemplate_L".$pdf->rapport_layout.".php");
  if($pdf->IndexPage > 0)
  {
  $template = new PDFRapport('L','mm');
  $template->SetAutoPageBreak(true,15);
	$template->pagebreak = 190;
	$template->__appvar = $__appvar;
	loadLayoutSettings($template, $portefeuille);
	$template->templateVars=$pdf->templateVars;
	$classString = 'RapportTemplate_L'.$pdf->rapport_layout;
	$rapport = new $classString($template, $portefeuille, $startdatum, $einddatum);
	$rapport->writeRapport();

	if($template->IndexPage > 0)
	  $pdf->pages[$pdf->IndexPage] = $template->pages[$template->IndexPage];
  }
	elseif ($pdf->rapport_layout == 8)
  {
   	$classString = 'RapportTemplate_L'.$pdf->rapport_layout;
	  $rapport = new $classString($pdf, $portefeuille, $startdatum, $einddatum);
 }
}


	// write PDF to file
		switch($rapportType)
		{
			case "dag" :
				$extra = "";
			break;
			case "maand" :
				// yymm
				$jr = date("y",db2jul($rapportageDatum[b]));
				$mm = date("m",db2jul($rapportageDatum[b]));
				$extra = $jr.$mm;
			break;
			case "kwartaal" :
				//yyqq
				$jr = date("y",db2jul($rapportageDatum[b]));
				$qq = floor((date("m",db2jul($rapportageDatum[b])) / 4))  + 1;
				$qq = "Q".$qq;
				$extra = $jr.$qq;
			break;
		}

		if($pdata['naamInExport'])
		{
		  $voorzet = str_replace('.','_',$pdata['Naam']).'_';
		}
		elseif(substr($portefeuille,0,3) == 211)
		{
			$voorzet = str_replace("211","",$portefeuille);
		}
		else
		{
			$voorzet = $portefeuille;
		}

				// FACTUUR ?

		if($inclFactuur == 1 &&
		    ($pdata['BeheerfeeAantalFacturen'] == 4 ||
		    ($pdata['BeheerfeeAantalFacturen'] == 1 &&  substr($einddatum,5,5) == '12-31') ||
		    ($pdata['BeheerfeeAantalFacturen'] == 2 && (substr($einddatum,5,5) == '06-30') || (substr($einddatum,5,5) == '12-31') )
		    )
		  )
		  {

	   $rapport = new Factuur($pdf, $portefeuille,$attStart, $einddatum, $extrastart);		//jul2sql($endPrevQ)
		 if($rapport->waarden['portefeuille'] == $portefeuille)
	    {
		    $rapport->factuurnummer = $factuurnummer;
		    $rapport->__appvar = $__appvar;
			  $rapport->writeRapport();
			  $factuurnummer++;
	    }
			  if($extrastart)
			  {
				  verwijderTijdelijkeTabel($portefeuille,$extrastart);
			  }
			}



		$filename = $voorzet."VOLK".$extra.".pdf";
		if (is_writable($exportPath))
		{

		  if($eMail == true)
		  {
		    $pdf->SetProtection(array('print'),$pdata['wachtwoord']);
		    echo "Portefeuille: ".$pdata['Portefeuille']." beveiligd met: ".$pdata['wachtwoord']." ";
		  }

			$pdf->Output($exportPath.$filename,"F");
			$lb++;
			if ($lb >= 49)
			{
			  echo "<br>";
			  $lb = 0;
			}
			echo "o ";
      flush();
      if($eMail == true)
      {
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();

        $data['mailBody'] = 'Beste '.$pdata['Naam'].",<br>\n";
        $data['mailBody'] .= 'Bijgevoegd rapportage '.$extra;

        $mail->From     = $pdata['VermogensbeheerdersEmail'];//'rvv@aeict.nl';
        $mail->FromName = $pdata['Accountmanager'];
        $mail->Body    = $data['mailBody'];
        $mail->AltBody = html_entity_decode(strip_tags($data['mailBody']));
        $mail->AddAddress($pdata['Email'],$pdata['Naam']);
        $mail->Subject = "Portefeuille: ".$pdata['Portefeuille'];
        $mail->AddAttachment($exportPath.$filename,$filename);

        if($__appvar["smtpUser"] && $__appvar["smtpPass"])
        {
          echo 'pass';
          $mail->SMTPAuth = true;
          $mail->Username = $__appvar["smtpUser"];
          $mail->Password = $__appvar["smtpPass"];
        }

        if($afbreken == true)
        {
          echo "Fout bij het zenden van ".$pdata['Portefeuille']." naar " .$pdata['Email'].". Geen geldig wachtwoord of emailadres ingesteld.<br>";
        }
        elseif(!$mail->Send())
        {
          echo "Fout bij het zenden van ".$pdata['Portefeuille']." naar " .$pdata['Email']. "<br>";
          echo $mail->ErrorInfo;
        }
        else
        {
          echo "Portefeuille ".$pdata['Portefeuille']." is verzonden naar ".$pdata['Email'].". <br>";
          $mail->ClearAddresses();
        }
        unlink($exportPath.$filename);
      }
      elseif ($eDossier == true) // Digitaal document
      {
        $table='CRM_naw';
        if($db->QRecords("SELECT id FROM $table WHERE portefeuille = '".$pdata['Portefeuille']."'")>0)
        {
          $store=true;
          $id=$db->nextRecord();
          $id=$id['id'];
        }
        else
          $store=false;

        if($store)
        {
          $filename = $exportPath.$filename;
          $filesize = filesize($filename);
          $filetype = mime_content_type($filename);
          $fileHandle = fopen($filename, "r");
          $docdata  = fread($fileHandle, $filesize);
          fclose($fileHandle);

          $dd = new digidoc();
          $rec ["filename"] = $file;
          $rec ["filesize"] = "$filesize";
          $rec ["filetype"] = "$filetype";
          $rec ["description"] = $file;
          $rec ["blobdata"] = $docdata;
          $rec ["keywords"] =$file;
          $rec ["module"] = $table;
          $rec ["module_id"] = $id;
          $dd->useZlib = false;
          $dd->addDocumentToStore($rec);

          unlink($filename);
        }
      }

		}
		else
		{
			echo "fout bij schrijven naar ".$exportPath.$filename."\n<br>";
      if (is_dir($exportPath))
      {
        if ($dh = opendir($exportPath))
	      {
          while (($file = readdir($dh)) !== false)
		      {
            echo "filename: $file <br>\n";
          }
          closedir($dh);
        }
      }
      else
			  echo "Leesactie op $exportPath mislukt.\n<br>";
			exit;
		}
		unset($pdf);

		verwijderTijdelijkeTabel($portefeuille,$startdatum);
		verwijderTijdelijkeTabel($portefeuille,$einddatum);
    if(db2jul($pdf->PortefeuilleStartdatum) > db2jul( "$rapportJaar-01-01"))
      $startDatum =  $pdf->PortefeuilleStartdatum;
    else
      $startDatum = "$rapportJaar-01-01";
    verwijderTijdelijkeTabel($portefeuille,$startDatum);

	}
}
// einde mysql loop
echo "- einde generatie dagrapporten".$_linebreak;
?>