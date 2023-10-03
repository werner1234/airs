<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/31 16:09:08 $
 		File Versie					: $Revision: 1.156 $

 		$Log: rapportFrontofficeClientAfdrukken.php,v $
 		Revision 1.156  2020/05/31 16:09:08  rvv
 		*** empty log message ***
 		
 		Revision 1.155  2020/04/22 15:39:13  rvv
 		*** empty log message ***
 		
 		Revision 1.154  2019/10/18 17:37:32  rvv
 		*** empty log message ***
 		
 		Revision 1.153  2019/10/16 05:35:16  rvv
 		*** empty log message ***
 		
 		Revision 1.152  2019/10/12 18:04:27  rvv
 		*** empty log message ***
 		
 		Revision 1.151  2019/10/12 17:08:44  rvv
 		*** empty log message ***
 		
 		Revision 1.150  2019/08/15 09:42:25  rvv
 		*** empty log message ***
 		
 		Revision 1.149  2019/08/14 16:30:09  rvv
 		*** empty log message ***
 		
 		Revision 1.148  2019/02/06 09:52:56  cvs
 		call 7488
 		
 		Revision 1.147  2019/01/06 12:42:11  rvv
 		*** empty log message ***
 		
 		Revision 1.146  2019/01/05 18:40:23  rvv
 		*** empty log message ***
 		
 		Revision 1.145  2018/11/28 17:15:17  rvv
 		*** empty log message ***
 		
 		Revision 1.144  2018/11/17 17:30:55  rvv
 		*** empty log message ***
 		
 		Revision 1.143  2018/11/07 17:05:52  rvv
 		*** empty log message ***
 		
 		Revision 1.142  2018/11/03 18:47:10  rvv
 		*** empty log message ***
 		
 		Revision 1.141  2018/10/29 10:54:18  rvv
 		*** empty log message ***
 		
 		Revision 1.140  2018/10/27 16:51:03  rvv
 		*** empty log message ***
 		
 		Revision 1.139  2018/10/17 15:36:45  rvv
 		*** empty log message ***
 		
 		Revision 1.138  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.137  2018/10/10 16:15:25  rvv
 		*** empty log message ***
 		
 		Revision 1.136  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.135  2018/04/15 07:33:58  rvv
 		*** empty log message ***
 		
 		Revision 1.134  2018/04/14 17:21:13  rvv
 		*** empty log message ***
 		
 		Revision 1.133  2018/04/07 15:25:37  rvv
 		*** empty log message ***
 		
 		Revision 1.132  2018/04/07 15:23:45  rvv
 		*** empty log message ***
 		
 		Revision 1.131  2018/02/24 18:31:44  rvv
 		*** empty log message ***
 		
 		Revision 1.130  2018/02/17 19:16:15  rvv
 		*** empty log message ***
 		
 		Revision 1.129  2018/01/19 14:12:17  rvv
 		*** empty log message ***
 		
 		Revision 1.128  2017/08/16 15:56:17  rvv
 		*** empty log message ***
 		
 		Revision 1.127  2017/07/16 10:50:44  rvv
 		*** empty log message ***
 		
 		Revision 1.126  2017/05/15 06:07:15  rvv
 		*** empty log message ***
 		
 		Revision 1.125  2017/05/06 17:22:56  rvv
 		*** empty log message ***
 		
 		Revision 1.124  2017/04/29 17:22:39  rvv
 		*** empty log message ***
 		
 		Revision 1.123  2017/04/16 10:32:40  rvv
 		*** empty log message ***
 		
 		Revision 1.122  2017/03/22 16:50:10  rvv
 		*** empty log message ***
 		
 		Revision 1.121  2017/01/21 17:11:18  rvv
 		*** empty log message ***
 		
 		Revision 1.120  2017/01/07 16:21:02  rvv
 		*** empty log message ***
 		
 		Revision 1.119  2016/09/18 08:45:04  rvv
 		*** empty log message ***
 		
 		Revision 1.118  2016/05/29 14:01:51  rvv
 		*** empty log message ***
 		
 		Revision 1.117  2016/05/24 04:13:55  rvv
 		*** empty log message ***
 		
 		Revision 1.116  2016/03/20 14:37:52  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2016/02/13 14:01:08  rvv
 		*** empty log message ***
 		
 		Revision 1.114  2016/01/03 09:12:52  rvv
 		*** empty log message ***
 		
 		Revision 1.113  2015/12/13 09:01:21  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2015/12/06 08:39:51  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2015/12/02 08:26:47  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2015/11/29 13:09:34  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2015/09/23 16:16:01  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2015/09/20 17:30:13  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2015/05/16 09:31:31  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2014/11/30 13:11:34  rvv
 		*** empty log message ***
 		
*/

//$AEPDF2=true;

$apiCall = false;

if (isset($_SESSION["mdzPOSTdata"]) && count($_SESSION["mdzPOSTdata"])>1)
{
  $apiCall = true;

  $disable_auth = true;
  unset($_GET);
  $_POST = $_SESSION["mdzPOSTdata"];

  foreach ($_POST as $k=>$v)
  {
    $$k = $v;
  }

  $p = explode("/",getcwd());
  array_pop($p);
  $__appvar["basedir"] = implode("/",$p);
  $rootPath = implode("/",$p);
  $path = $__appvar["basedir"]."/html/";

  /*
  normale app
*/
  include_once("AE_lib2.php3");

  include_once($__appvar["basedir"]."/config/local_vars.php");

//echo $rootPath."/config/vars.php";
  include_once($rootPath."/config/vars.php");
  //include_once("../../config/auth.php");


//  include_once($path."../classes/AE_cls_progressbar.php");

  $USR = "mdzApi";
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once($path."../classes/AE_cls_fpdf.php");

  include_once($path."rapport/rapportVertaal.php");
  include_once($path."rapport/PDFRapport.php");
  include_once($path."rapport/RapportFRONT.php");
  include_once($path."rapport/rapportRekenClass.php");

//  include_once("rapport/RapportHSE.php");
//  include_once("rapport/RapportMUT.php");
//  include_once("rapport/RapportMUT2.php");
//  include_once("rapport/RapportOIH.php");
//  include_once("rapport/RapportOIB.php");
//  include_once("rapport/RapportOIS.php");
//  include_once("rapport/RapportOIBS.php");
//  include_once("rapport/RapportOIBS2.php");
//  include_once("rapport/RapportOIV.php");
  include_once($path."rapport/RapportPERF.php");

//  include_once("rapport/RapportPERFD.php");
//  include_once("rapport/RapportTRANS.php");
//  include_once("rapport/RapportVHO.php");
  include_once($path."rapport/RapportVOLK.php");

//  include_once("rapport/RapportHSEP.php");
//  include_once("rapport/RapportVOLKD.php");
//  include_once("rapport/RapportOIR.php");
//  include_once("rapport/RapportGRAFIEK.php");
//  include_once("rapport/RapportATT.php");
//  include_once("rapport/RapportCASH.php");
//  include_once("rapport/RapportCASHY.php");
//  include_once("rapport/RapportMODEL.php");
//  include_once("rapport/RapportSMV.php");
//  include_once("rapport/RapportPERFG.php");
//  include_once("rapport/RapportRISK.php");
//  include_once("rapport/RapportZORG.php");
//  include_once("rapport/RapportFISCAAL.php");
//  include_once("rapport/RapportSCENARIO.php");
//  include_once("rapport/RapportSCENARIO2.php");
//  include_once("rapport/RapportTRANSFEE.php");
//  include_once("rapport/RapportPORTAAL.php");
//  include_once("rapport/RapportORDERS.php");
//  include_once("rapport/RapportJOURNAAL.php");
//  include_once("rapport/RapportVKM.php");
//  include_once("rapport/RapportVKMD.php");
//  include_once("rapport/RapportVKMS.php");
  include_once($path."rapport/RapportRESTRICTIES.php");
//  include_once("rapport/RapportDOORKIJK.php");

}
else
{
  include_once("wwwvars.php");
  include_once("../classes/AE_cls_progressbar.php");
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("../classes/AE_cls_fpdf.php");
  include_once("rapport/PDFRapport.php");
  include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");
  include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
}


if($_GET && !isset($_GET['counter']))
{
  if($_GET['datum'])
    $laatsteDatumJul = form2jul($_GET['datum']);
  else
    $laatsteDatumJul = db2jul(getLaatsteValutadatum());

  $_POST['scenario_portefeuilleWaardeGebruik']=1;
  $_POST['datum_tot'] =  date('d-m-Y',$laatsteDatumJul);
  if(isset($_GET['datum_van']))
    $_POST['datum_van'] = $_GET['datum_van'];
  else
    $_POST['datum_van'] = "01-01-".date('Y',$laatsteDatumJul);
  $_POST['Portefeuille'] = $_GET['portefeuille'];
  $rapport_types = $_GET['rapport'];
  $_POST['posted'] = 1;
  $_POST['crmAfdruk'] =true;

	$query = "SELECT CRM_naw.rapportageVinkSelectie FROM CRM_naw WHERE CRM_naw.portefeuille='".$_POST['Portefeuille']."'";
	$db=new DB();
	$db->SQL($query);
	$vink = $db->lookupRecord();
	$vink=unserialize($vink['rapportageVinkSelectie']);
	if($_GET['periodeSettings']<>'')
		$periode=$_GET['periodeSettings'];
	else
		$periode='k';
	if(is_array($vink['opties'][$periode]))
	{
		foreach($vink['opties'][$periode] as $rap=>$variabelen)
			foreach($variabelen as $var=>$value)
		    	$_POST[$var]=$value;
	}

}
if($_POST['crmInstellingen']==1)
{
	unset($_POST['rapport_types']);
	$rapport_types='';
	$query = "SELECT CRM_naw.rapportageVinkSelectie FROM CRM_naw WHERE CRM_naw.portefeuille='".$_POST['Portefeuille']."'";
	$db=new DB();
	$db->SQL($query);
	$vink = $db->lookupRecord();
	$vink=unserialize($vink['rapportageVinkSelectie']);
	if(is_array($vink['opties']['k']))
		foreach($vink['opties']['k'] as $rap=>$variabelen)
			foreach ($variabelen as $var => $value)
				$_POST[$var] = $value;
	if(is_array($vink['rap_k'])&&count($vink['rap_k'])>0)
		foreach($vink['rap_k'] as $rap)
			$rapport_types.="|".$rap;
	elseif(is_array($vink['rap_m'])&&count($vink['rap_m'])>0)
		foreach($vink['rap_m'] as $rap)
			$rapport_types.="|".$rap;
}

if($_POST['posted'])
{

  $_SESSION['lastPost'] = $_POST;

	include_once("rapport/rapportRekenClass.php");

	if(!empty($_POST['datum_van']) && !empty($_POST['datum_tot']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datum_van']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>Fout: ongeldige datum opgegeven!</b>";
			exit;
		}

		$dd = explode($__appvar["date_seperator"],$_POST['datum_tot']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>Fout: ongeldige datum opgegeven!</b>";
			exit;
		}
	}
	else
	{
		echo "<b>Fout: geen datum opgegeven!</b>";
		exit;
	}

	if( strlen($rapport_types) <= 1)
  {
    if($_GET['type']=='email' || $_GET['type'] == 'emailLos')
    {
      if ( $_GET['type']=='email' ) {
        echo "<script> alert('Geen rapport bijgevoegd.');</script>";
      }
      include('CRM_mailer.php');
		}
    else  
      echo "<b>Fout: geen rapport type opgegeven </b>";
		exit;
	}

	$rapport_type = explode("|",$rapport_types);
	if(count($rapport_type) < 1)
	{
		echo "<b>Fout: geen rapport type opgegeven </b>";
		exit;
	}

	// selecteer rapportage volgorde
	$portefeuille = $_POST['Portefeuille'];

	if(empty($portefeuille))	{
		echo "<b>Fout: geen portefeuille opgegeven </b>";
		exit;
	}

  if ($apiCall)
  {
    $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '{$portefeuille}'";
  }
  else
  {
    // controle of gebruiker bij vermogensbeheerder mag
    if(checkAccess($type))
      $join = "";
    else
    {
      $join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ".
        " VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
						JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
      $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";

    }
    // check begin datum rapportage!
    $query = "SELECT Portefeuilles.Startdatum, ".
      "Portefeuilles.Einddatum,		".
      "Portefeuilles.consolidatie,
    Portefeuilles.accountmanager,
    Portefeuilles.client,
    Clienten.Naam,
    Clienten.Naam1,
    Portefeuilles.tweedeAanspreekpunt,	".
      "Portefeuilles.RapportageValuta, ".
      "Vermogensbeheerders.layout, ".
      "Vermogensbeheerders.attributieInPerformance,	".
      "Vermogensbeheerders.Vermogensbeheerder,	".
      "Vermogensbeheerders.Export_data_frontOffice	".
      " FROM (Portefeuilles, Vermogensbeheerders) ".$join." LEFT JOIN Clienten ON Portefeuilles.Client=Clienten.Client WHERE Portefeuilles.Portefeuille = '".$portefeuille."'".
      " AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder $beperktToegankelijk";

  }

	verwijderTijdelijkeTabel($portefeuille);
	// asort

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$pdata = $DB->nextRecord();

	// todo : sorteer rapporttypes
	// todo : controlleer of datum in data bereik zit!!
	$frontOfficeData=unserialize($pdata['Export_data_frontOffice']);

	$rapportageDatum['a'] = jul2sql(form2jul($_POST['datum_van']));
	$rapJul=form2jul($_POST['datum_tot']);
	$valutaDatum = getLaatsteValutadatum();
  $valutaJul = db2jul($valutaDatum);
	if($rapJul > $valutaJul + 86400 && $__appvar["bedrijf"] <> 'VRY')
	{
		echo "<b>Fout: kan niet in de toekomst rapporteren.</b>";
		exit;
	}
	$rapportageDatum['b'] = jul2sql($rapJul);

	if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
	{
		$rapportageDatum['a'] = $pdata[Startdatum];
	}

	if($pdata['Startdatum']=='0000-00-00 00:00:00')
	{
		echo vtb("Portefeuille %s heeft geen startdatum.", array($portefeuille));
		exit;
	}

	if(db2jul($rapportageDatum['b']) > db2jul($pdata['Einddatum']))
	{
		echo "<b>Fout: Deze portefeille heeft een einddatum  (".date("d-m-Y",db2jul($pdata['Einddatum'])).")</b>";
		exit;
	}

	// controlleer of datum a niet groter is dan datum b!
	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	{
		echo "<b>Fout: Van datum kan niet groter zijn dan  T/m datum! </b>";
		exit;
	}


	$query = "SHOW TABLES like 'GrootboekPerVermogensbeheerder'";
  $DB->SQL($query);
  $DB->Query();
  if($DB->records() > 0)
  {
    $query = "SELECT count(id) as aantal FROM GrootboekPerVermogensbeheerder WHERE Vermogensbeheerder = '".$pdata['Vermogensbeheerder']."' AND StartDatum < '".$rapportageDatum['b']."'  ";
    $DB->SQL($query);
    $DB->Query();
    $records = $DB->lookupRecord();
    if($records['aantal'] > 0)
      $GrootboekPerVermogensbeheerder = true;
    else
      $GrootboekPerVermogensbeheerder = false;
  }
  else
    $GrootboekPerVermogensbeheerder = false;


	$julrapport = db2jul($rapportageDatum[a]);
	$rapportMaand = date("m",$julrapport);
	$rapportDag = date("d",$julrapport);
	$rapportJaar = date("Y",$julrapport);

//if($pdata['layout'] == 26)
//  $vulKwartalen=true;

if($vulKwartalen)
{
  $eerste=1;
  $index=new indexHerberekening();
  $datum=$index->getKwartalen(db2jul($rapportageDatum['a']),db2jul($rapportageDatum['b']));
  foreach ($datum as $periode)
  {
    if(substr($periode['start'],5,5)=='01-01')
      $startjaar=true;
    else
      $startjaar=false;
    if(isset($eerste))
    {
      $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$periode['start'],(substr($periode['start'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$periode['start']);
      vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['start']);
      unset($eerste);
    }
    $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$periode['stop'],(substr($periode['stop'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$periode['start']);
    vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['stop']);
  }
}
elseif($pdata['layout'] == 13)
{
  $beginjaar = substr($rapportageDatum['a'],0,4);
  $eindjaar = substr($rapportageDatum['b'],0,4);
	if($beginjaar < 2008)
	{
	  $fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille,$beginjaar.'-01-01',1,$pdata['RapportageValuta'],$beginjaar.'-01-01');
	  $rapportageDatum['a'] = $beginjaar.'-01-01';
	  $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],(substr($rapportageDatum['b'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$eindjaar.'-01-01');
	}
	else
	{
	  if($beginjaar <> $eindjaar)
	    $vergelijkDatum=$eindjaar.'-01-01';
    else
      $vergelijkDatum=$rapportageDatum['a'];
	  $fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],(substr($rapportageDatum['a'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$rapportageDatum['a']);
		$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],(substr($rapportageDatum['b'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$rapportageDatum['a']);
	}
	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
	vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
}
else
{
	if($rapportMaand == 1 && $rapportDag == 1)
	{
		$startjaar = true;
		$extrastart = false;
	}
	else
	{
		$startjaar = false;
		// 1 dag eraf is de startdatum!
		$julrapport = db2jul($rapportageDatum['a']);
		$rapportageDatum['a'] = jul2sql($julrapport);

		$extrastart = mktime(0,0,0,1,1,$rapportJaar);
		if($extrastart < 	db2jul($pdata['Startdatum']))
			$extrastart = substr($pdata['Startdatum'],0,10);
		else
			$extrastart = date("Y-m-d",$extrastart);
	}
	if($_POST['doorkijk']==1)
	{
		$tmp = bepaalHuidfondsenVerdeling($portefeuille, $rapportageDatum['b'], $rapportageDatum['a'], $pdata['RapportageValuta']);
		vulTijdelijkeTabel($tmp, 'd_' . $portefeuille, $rapportageDatum['b']);
	}
//echo "berekenPortefeuilleWaarde($portefeuille,".$rapportageDatum[a].",$startjaar,".$pdata['RapportageValuta'].",".$rapportageDatum[a]."); fo<br>";
	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['a'],(substr($rapportageDatum['a'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$rapportageDatum['a']);
	$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum['b'],(substr($rapportageDatum['b'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$rapportageDatum['a']);

//	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum[a]);
//	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum[b]);
  if($rapportageDatum['a']==$rapportageDatum['b'] && substr($rapportageDatum['a'],5,5)=='01-01')
    vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,(substr($rapportageDatum['a'],0,4)-1).'-12-31');
	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatum['a']);
	vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum['b']);
	if($extrastart)
	{
	 	//verwijderTijdelijkeTabel($portefeuille,$extrastart);
		$fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $extrastart,(substr($extrastart, 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$extrastart);
		vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$extrastart);
	}

//rvv Extra query om resultaten van vorige jaar op te halen.
	$RapStartJaar = date("Y", db2jul($rapportageDatum['a']));
	$RapStopJaar = date("Y", db2jul($rapportageDatum['b']));
	$RapJaar = $RapStopJaar;
	if ($RapJaar != $RapStartJaar)
	{
     $fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $RapStartJaar."-12-31",0,$pdata['RapportageValuta']);
	   vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$RapStartJaar."-12-31");
	}
//endrvv
}


	$rapportageDatumVanaf = $rapportageDatum['a'];
	$rapportageDatum = $rapportageDatum['b'];

	$pdf = new PDFRapport('L','mm');

	$pdf->SetAutoPageBreak(true,15);
	$pdf->pagebreak = 190;
	$pdf->__appvar = $__appvar;
	$pdf->extra = $_POST['extra'];

	if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
	{
	  $pdf->rapportageValuta = $pdata['RapportageValuta'];
	  $pdf->ValutaKoersBegin = getValutaKoers($pdf->rapportageValuta,$rapportageDatumVanaf);
	  $pdf->ValutaKoersEind  = getValutaKoers($pdf->rapportageValuta,$rapportageDatum);
	  $pdf->ValutaKoersStart = getValutaKoers($pdf->rapportageValuta,$RapStartJaar."-01-01");//$rapportageDatumVanaf);
	}
	else
	{
	  $pdf->rapportageValuta = "EUR";
	  $pdf->ValutaKoersEind  = 1;
	  $pdf->ValutaKoersStart = 1;
	  $pdf->ValutaKoersBegin = 1;
	}

	$pdf->PortefeuilleStartdatum = $pdata['Startdatum'];
	$pdf->GrootboekPerVermogensbeheerder = $GrootboekPerVermogensbeheerder;

	$pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
	$pdf->rapport_datum = db2jul($rapportageDatum);

	$pdf->perfSettings = array('vvgl'=>$_POST['vvgl'],'perc'=>$_POST['perc'],'opbr'=>$_POST['opbr'],'kost'=>$_POST['kost']);
  //$pdf->mutSettings = array('GB_STORT_ONTTR'=>$_POST['GB_STORT_ONTTR'],'GB_overige'=>$_POST['GB_overige']);
  $pdf->transSettings = array('TRANS_RESULT'=>$_POST['TRANS_RESULT']);
  $pdf->ModelSettings = array('modelcontrole_level'=>$_POST['modelcontrole_level']);
  $pdf->lastPOST = $_POST;


	loadLayoutSettings($pdf, $portefeuille);

  $layouts=array();
  if($_POST['layout'])
  {
    $range=explode("-",$_POST['layout']);
    if(count($range)==2)
    {
      if($range[0] < $range[1])
      {
        for($n=$range[0];$n<=$range[1];$n++)
        {
          $layouts[]=$n;
        }
      }
    }
    else
     $layouts[]=$_POST['layout'];
  }
  else
    $layouts[]=$pdf->rapport_layout;

	if($_POST['logoOnderdrukken'])
	  $pdf->rapport_logo='';

	if($_POST['debug'])
	 $pdf->debug=1;


	if($_POST['voorbladWeergeven'] )
	{
	  $rapport_type[]    = 'FRONT';
	}
  
  $volgorde=array();
  foreach($__appvar["Rapporten"] as $rapCode=>$rapNaam)
  {
    $volgorde[$rapCode]='';
  }
  if(is_array($frontOfficeData))
  {
    foreach ($frontOfficeData as $rap=>$rapData)
    {
      if($rapData['volgorde'] == '')
        $rapData['volgorde']=999;
      $volgorde[$rap]=$rapData['volgorde'];
    }
  }
  foreach($__appvar["Rapporten"] as $rapCode=>$rapNaam)
  {
    if($volgorde[$rapCode]=='')
      $volgorde[$rapCode]=999;
  }
  
	$pdf->rapport_typen=$rapport_type;
  $pdf->volgorde=$volgorde;
  
  if($pdata['consolidatie'] == 1)
  {
    if ($_POST['extra'] == 'order')
    {
      echo "Oder uitvoer voor consolidatie niet mogelijk.";
      exit;
    }
    $DB = new DB();
    $DB->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='" . $portefeuille . "'");
    $DB->Query();
    $vpdata = $DB->nextRecord();
    $consolidatiePaar = $vpdata;
    for ($i = 1; $i < 41; $i++)
    {
      if ($vpdata['Portefeuille' . $i] <> '')
      {
        $portefeuilles[] = $vpdata['Portefeuille' . $i];
      }
    }
    $query = "SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='" . $portefeuille . "' ORDER BY Portefeuille";
    $DB->SQL($query);
    $DB->Query();
    while ($vpdata = $DB->nextRecord())
    {
      $portefeuilles[] = $vpdata['Portefeuille'];
    }
    
    $pdf->portefeuilles =$portefeuilles;

    $__appvar['consolidatie']['portefeuillenaam1']=$pdata['Naam'];
    $__appvar['consolidatie']['portefeuillenaam2']=$pdata['Naam1'];
    $crmNaam=getCrmNaam($portefeuille);
    if(count($crmNaam)>0)
    {
      $__appvar['consolidatie']['portefeuillenaam1']=$crmNaam['naam'];
      $__appvar['consolidatie']['portefeuillenaam2']=$crmNaam['naam1'];
    }
    
    $__appvar['consolidatie']['accountmanager']=$pdata['accountmanager'];
    $__appvar['consolidatie']['tweedeAanspreekpunt']=$pdata['tweedeAanspreekpunt'];
    $pdf->__appvar = $__appvar;
  }
  
  


asort($volgorde, SORT_NUMERIC);

$i=0;
if($_POST['extra']=='xls')
{
  //
  if(in_array($__appvar["bedrijf"],array('TRI', 'PAS', 'ANT')))//'WMP','WPS',
    $xlsType='xlsx';
  else
    $xlsType='xls';
  
  if($xlsType=='xls')
  {
    include_once('../classes/excel/Writer.php');
    $workbook = new Spreadsheet_Excel_Writer();
  }
  else
  {
    require_once $__appvar["basedir"] . '/classes/PHPExcel.php';
    require_once $__appvar["basedir"] . '/classes/PHPExcel/Writer/Excel2007.php';
    require_once $__appvar["basedir"] . '/classes/PHPExcel/ZipArchive.php';
    require_once $__appvar["basedir"] . '/classes/PHPExcel/Style.php';
    require_once $__appvar["basedir"] . '/classes/PHPExcel/Style/Font.php';
    require_once $__appvar["basedir"] . '/classes/PHPExcel/Style/Color.php';
    $objPHPExcel = new PHPExcel();
    $fontObj = new PHPExcel_Style_Font();
    $fontObj->setBold(true);
    $styleObj = new PHPExcel_Style();
    $styleObj->setFont($fontObj);
    $styleObjecten=array('header'=>$styleObj);
    $styleObj = new PHPExcel_Style();
    $tmp=$styleObj->getNumberFormat();
    $tmp->setFormatCode('dd-mm-yyyy');
    $styleObjecten['date']=$styleObj;
    $activeSheetObject = $objPHPExcel->getActiveSheet();
    $sheetIndex=$objPHPExcel->getIndex($activeSheetObject);
 
  }
}
foreach($layouts as $layout)
{
  $pdf->rapport_layout=$layout;
  if (file_exists("rapport/include/PreProcessor_L".$pdf->rapport_layout.".php"))
  {
    include_once("rapport/include/PreProcessor_L".$pdf->rapport_layout.".php");
    $classString = 'PreProcessor_L'.$pdf->rapport_layout;
    $processor= new $classString($portefeuille,'',$pdf);
    $classString='';
  }
  elseif (file_exists("rapport/include/layout_".$pdf->rapport_layout."/PreProcessor_L".$pdf->rapport_layout.".php"))
    {
      include_once("rapport/include/layout_".$pdf->rapport_layout."/PreProcessor_L".$pdf->rapport_layout.".php");
      $classString = 'PreProcessor_L'.$pdf->rapport_layout;
      $processor= new $classString($portefeuille,'',$pdf);
      $classString='';
    }

  foreach($volgorde as $key=>$value)
	{
		if(in_array($key,$rapport_type))
		{
		  $pdf->excelData 	= array();
      
      if ($key=='PERF' && $pdata['attributieInPerformance'] == 1)
      {
        $key='ATT';
      }
      if($key=='FRONT' && $pdata['consolidatie'] == 1)
      {
        //if (file_exists("rapport/include/layout_".$pdf->rapport_layout."/RapportFRONTC_L".$pdf->rapport_layout.".php") || file_exists("rapport/include/RapportFRONTC_L".$pdf->rapport_layout.".php"))
        $key='FRONTC';
      }
      if($key=='CASHFLOW-Y')
      {
        $key='CASHY';
      }
      if($key=='CASHFLOW')
      {
        $key='CASH';
      }
      $classString='';
      if (file_exists("rapport/include/layout_".$pdf->rapport_layout."/Rapport".$key."_L".$pdf->rapport_layout.".php"))
      {
        include_once("rapport/include/layout_".$pdf->rapport_layout."/Rapport".$key."_L".$pdf->rapport_layout.".php");
        $classString = 'Rapport'.$key.'_L'.$pdf->rapport_layout;
      }
      elseif (file_exists("rapport/include/Rapport".$key."_L".$pdf->rapport_layout.".php"))
      {
        include_once("rapport/include/Rapport".$key."_L".$pdf->rapport_layout.".php");
        $classString = 'Rapport'.$key.'_L'.$pdf->rapport_layout;
      }
      else
      {
        if(file_exists("rapport/Rapport".$key.".php"))
        {
          include_once("rapport/Rapport".$key.".php");
          $classString = 'Rapport'.$key;
        }
      }
    //  echo "$key $classString <br>\n";ob_flush();
      if(class_exists($classString))
      {
        $rapport = new $classString($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
        $rapport->writeRapport();
      }
      else
      {
        logit("Class $classString for $key not found.");
      }

      if($_POST['extra']=='xls')
      {
        if(isset($rapport->rapport_xls_titel))
          $xlsTitel=$rapport->rapport_xls_titel;
        else
          $xlsTitel=$key;
        
        if($xlsType=='xls')
        {
          $worksheet[$i] =& $workbook->addWorksheet($xlsTitel);
          if (is_object($rapport))
          {
            $rapport->pdf->fillXlsSheet($worksheet[$i], $workbook);
          }
        }
        else
        {
          if (is_object($rapport))
          {
            if($sheetIndex>0) {
              $activeSheetObject = $objPHPExcel->createSheet($sheetIndex);
            }

            $tmp = array();
            for ($regel = 0; $regel < count($rapport->pdf->excelData); $regel++)
            {
              for ($col = 0; $col < count($rapport->pdf->excelData[$regel]); $col++)
              {
                if (is_array($rapport->pdf->excelData[$regel][$col]))
                {
                  $tmp[$regel][$col] = utf8_encode($rapport->pdf->excelData[$regel][$col][0]);
                  $celOpmaak = $rapport->pdf->excelData[$regel][$col][1]; //1=opmaak
                  if(isset($styleObjecten[$celOpmaak]))
                  {
                    //echo "$col,$regel ->". $xlsObject->stringFromColumnIndex($col).($regel+1)."<br>\n";
                    $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));
                  }
                }  //0=waarde
                else
                {
                  $waarde = $rapport->pdf->excelData[$regel][$col];
                  $datum = '';
                  if (substr($waarde, 2, 1) == '-' && substr($waarde, 5, 1) == '-' && (substr($waarde, 6, 1) == '1' || substr($waarde, 6, 1) == '2') && strlen($waarde) == 10)
                  {
                    if ($waarde <> '')
                    {
                      $datum = round((adodb_form2jul($waarde) + (86400 * 25569)) / 86400);
                    }
                    $tmp[$regel][$col] = $rapport->pdf->excelData[$regel][$col] = $datum;
                    $celOpmaak='date';
                    $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));
          
                  }
                  elseif (substr($waarde, 4, 1) == '-' && substr($waarde, 7, 1) == '-' && (substr($waarde, 0, 1) == '1' || substr($waarde, 0, 1) == '2') && strlen($waarde) == 10)
                  {
                    if ($waarde <> '')
                    {
                      $datum = round((adodb_db2jul($waarde) + (86400 * 25569)) / 86400);
                    }
                    $tmp[$regel][$col] = $rapport->pdf->excelData[$regel][$col] = $datum;
                    $celOpmaak='date';
                    $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));
                  }
                  else
                  {
                    $tmp[$regel][$col] = utf8_encode($waarde);
                  }
                }
              }
            }
          

            $activeSheetObject->setTitle($xlsTitel);
            $activeSheetObject->fromArray($tmp);
            $sheetIndex++;
          
            unset($tmp);
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            
          }
        }
      }
      $i++;
		}
	}
}

if (file_exists("rapport/include/layout_".$pdf->rapport_layout."/RapportTemplate_L".$pdf->rapport_layout.".php") || file_exists("rapport/include/RapportTemplate_L".$pdf->rapport_layout.".php"))
{
  if(file_exists("rapport/include/layout_".$pdf->rapport_layout."/RapportTemplate_L".$pdf->rapport_layout.".php"))
    include_once("rapport/include/layout_".$pdf->rapport_layout."/RapportTemplate_L".$pdf->rapport_layout.".php");
  if( file_exists("rapport/include/RapportTemplate_L".$pdf->rapport_layout.".php"))
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
	$rapport = new $classString($template, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	$rapport->writeRapport();
	if($template->IndexPage > 0)
	  $pdf->pages[$pdf->IndexPage] = $template->pages[$template->IndexPage];

  }
  else
  {
   	$classString = 'RapportTemplate_L'.$pdf->rapport_layout;
	  $rapport = new $classString($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
}
//global $teksten;listarray($teksten);
//$pdf->SetProtection(array('print'),'123456','12345678');
	//rvv
	verwijderTijdelijkeTabel($portefeuille,$rapportageDatumVanaf);
	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);//$rapportageDatum
	if($extrastart)
	 	verwijderTijdelijkeTabel($portefeuille,$extrastart);

  if(db2jul($pdf->PortefeuilleStartdatum) > db2jul( "$RapStartJaar-01-01"))
    $startDatum =  $pdf->PortefeuilleStartdatum;
  else
    $startDatum = "$RapStartJaar-01-01";
  verwijderTijdelijkeTabel($portefeuille,$startDatum);

	if ($RapJaar != $RapStartJaar)
	{
	verwijderTijdelijkeTabel($portefeuille,$RapStartJaar."-12-31");
	}
	//endrvv

if($vulKwartalen)
{
   foreach ($datum as $periode)
   {
     verwijderTijdelijkeTabel($portefeuille,$periode['start']);
     verwijderTijdelijkeTabel($portefeuille,$periode['stop']);
   }
}
  
  /*
      ksort($teksten);
      foreach($teksten as $tekst=>$aantal)
        if(trim($tekst)<> '')
          echo "$aantal|$tekst<br>\n";
      */
if($_POST['extra']=='xls')
{
  if($rapportnaam=='')
	{
		$rapportnaam = 'export.xls';
		//$pdf->OutputXLS($rapportnaam,'S');//,"F"
		$rapportnaam='';
		foreach($rapport_type as $rapport)
		{
			if($rapport <> '')
			  $rapportnaam .= $rapport . "_";
		}
		$rapportnaam.=$portefeuille.".xls";
	}
  if($xlsType=='xls')
  {
    $workbook->send($rapportnaam);
    $workbook->close();
  }
  else
  {
    header("Content-disposition: attachment; filename=\"" . $rapportnaam . "x\"");
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $objWriter->save('php://output');
    exit;
  }
}
else
{
  $settings=array_merge($_GET,$_POST);
  if($settings['passwd'])
  {
    $db=new DB();
    $query="SELECT id,wachtwoord FROM CRM_naw WHERE Portefeuille='$portefeuille'";
    $db->SQL($query);
    $wachtwoord=$db->lookupRecord();
    if(strlen($wachtwoord['wachtwoord']) < 6)
    {
      echo "Ingestelde wachtwoord < 6 tekens.";
      exit;
    }
    $pdf->SetProtection(array('print'),$wachtwoord['wachtwoord'],'!airs2011!a');//
  }

	if($_GET['digiDoc'] == 1)
	{
		$db=new DB();
		$query="SELECT id as relId FROM CRM_naw WHERE Portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$pdfData=$pdf->Output('',"S");
		$file='rapportage_'.date('Ymd_',db2jul($rapportageDatum)).$portefeuille.'.pdf';
		$dd = new digidoc();
		$rec=array("filename"=>$file,"filesize"=>strlen($pdfData),"filetype"=>'application/pdf','description'=>'Rapportage '.$portefeuille.' '.date('d-m-Y',db2jul($rapportageDatum)),
		           "blobdata" => $pdfData,"keywords"=>$file,"categorie"=>'rapportage',"module"=>'CRM_naw',"module_id"=>$crmData['relId']);
		$dd->useZlib = false;
		$dd->addDocumentToStore($rec);
	}



  if($_GET['type']=='email')
  {
    $filePath=$__appvar['tempdir'].$portefeuille."_".$rapportageDatum.".pdf";
    $pdf->Output($filePath,"F");
    $_GET['filename']=$filePath;
    $_GET['id']=$wachtwoord['id'];
    include('CRM_mailer.php');
  }
	elseif($save == 1)
	{
    $savedFilename = "";
		if(count($rapport_type) == 2)
		{
			$rapportnaam = $rapport_type[1];
		}

		if ($_POST["downloadPath"] != "")
    {
      $savedFilename = $portefeuille.$rapportnaam."_".rand(11111,9999).".pdf";
      $pdf->Output($_POST["downloadPath"].$savedFilename,"F");
    }
		else
    {
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      $pdf->Output($portefeuille.$rapportnaam.".pdf","D");
    }

	}
	else
	{
		$pdf->Output();
	}
}
	if (!$apiCall)
  {
    exit();
  }
}
?>