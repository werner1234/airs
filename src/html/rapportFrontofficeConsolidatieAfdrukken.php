<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/27 18:31:14 $
 		File Versie					: $Revision: 1.106 $

 		$Log: rapportFrontofficeConsolidatieAfdrukken.php,v $
 		Revision 1.106  2019/04/27 18:31:14  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2019/02/06 07:31:45  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2019/01/05 18:40:23  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2018/11/16 16:39:19  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2018/10/10 16:15:25  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2018/10/06 17:19:09  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2018/09/12 14:46:13  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2018/09/02 11:58:56  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.96  2018/05/19 16:22:32  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2018/05/16 15:29:40  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2018/05/06 11:31:30  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2018/04/21 17:54:13  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2018/03/07 16:51:10  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2018/01/16 06:56:04  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2018/01/14 12:37:31  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2018/01/03 16:24:37  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2017/12/20 16:59:57  rvv
 		*** empty log message ***
 		
 		Revision 1.87  2017/11/01 16:48:13  rvv
 		*** empty log message ***
 		
 		Revision 1.86  2017/09/02 17:18:56  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2017/07/19 19:24:02  rvv
 		*** empty log message ***
 		
 		Revision 1.84  2017/07/15 16:11:15  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2017/07/08 17:15:51  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2017/06/18 09:16:54  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2017/05/31 16:14:10  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2017/04/29 17:22:39  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2017/02/25 17:57:14  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2017/02/22 17:14:01  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2017/02/19 11:00:41  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2017/01/21 17:11:18  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2016/12/21 16:32:06  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2016/03/19 17:09:45  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2016/02/13 14:01:08  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2015/11/22 14:29:16  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2015/11/18 17:04:20  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2015/10/18 13:45:01  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2015/05/16 09:31:31  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2015/03/14 17:02:46  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2015/02/25 17:24:49  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2015/02/11 16:44:19  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2015/02/07 20:32:39  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2015/01/24 19:51:53  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2014/12/31 18:12:34  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2014/12/29 14:03:10  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2014/12/24 15:24:07  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2014/12/20 22:00:14  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2014/12/13 19:12:11  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2014/11/23 14:11:47  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2014/11/19 16:41:12  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2014/11/12 16:40:11  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2014/10/25 14:38:30  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2014/09/09 04:09:28  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2014/09/08 10:44:18  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2014/09/06 15:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2014/08/30 16:28:19  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2014/06/18 15:46:48  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2014/05/10 13:53:42  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2014/03/19 16:34:15  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2014/01/08 17:02:51  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2013/10/23 15:53:15  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2013/07/17 15:50:29  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2013/05/12 11:17:29  rvv
 		*** empty log message ***
 	

*/

//$AEPDF2=true;
include_once("wwwvars.php");

if($_GET['lookup']==1)
{
	$portefeuilles=array();
	if(is_array($_POST['selectedPortefeuilles']))
  	$portefeuilles=$_POST['selectedPortefeuilles'];
	$DB = new DB();
	$query="SELECT id,add_date,add_user,Portefeuille FROM Rekeningen WHERE consolidatie=2 AND Rekening IN (SELECT Rekening FROM Rekeningen WHERE portefeuille IN('".implode("','",$portefeuilles)."')) limit 1";//Portefeuille = '".$portefeuille."' AND
	$DB->SQL($query);
	$DB->Query();
	$aanwezig=$DB->lookupRecord();
	$msg='leeg';
	if($aanwezig['id']>0)
	{

		if($aanwezig['add_user']==$USR)
		{
			$msg="Voor rekening (" . $aanwezig['Portefeuille'] . ") wordt door u al een geconsolideerde rapportage geproduceerd (" . $aanwezig['add_date'] . "/" . $aanwezig['add_user'] . "). Wilt u dit proces afbreken en opnieuw starten?";
			$status=1;
		}
		elseif($aanwezig['add_user']=='SYS')		{
			$msg = "De tabellen met pre-calculated gegevens worden nu gevuld. U kunt geen geconsolideerde rapportage produceren. Probeer het later nog eens. (" . $aanwezig['Portefeuille'] . ") aanwezig. (" . $aanwezig['add_date'] . "/" . $aanwezig['add_user'] . ")";
			$status=2;
		}
		else
		{
			$msg = "Voor de rekening (" . $aanwezig['Portefeuille'] . ") wordt al een geconsolideerde rapportage geproduceerd (" . $aanwezig['add_date'] . "/" . $aanwezig['add_user'] . "). U dient te wachten tot dit proces afgerond is.";
			$status=2;
		}

	}
	else
	{
		logit("Consolidatie mogelijk. Geen records gevonden met : $query");
		$msg='All okay';
		$status=0;
	}

	echo json_encode(array('status'=>$status,'msg'=>$msg));
	exit;
}
if($_GET['verwijder']==1 || $_POST['verwijder']==1)
{
	$queries = array();
	$queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND add_user='$USR'";
	$queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND add_user='$USR'";
	$queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND add_user='$USR'";
	$DB=new DB();
	foreach ($queries as $query)
	{
		$DB->SQL($query);
		$DB->Query();
	}
}

define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");

include_once("rapport/PDFRapport.php");
include_once("rapport/RapportFrontC.php");
include_once("rapport/RapportHSE.php");
include_once("rapport/RapportMUT.php");
include_once("rapport/RapportMUT2.php");
include_once("rapport/RapportOIH.php");
include_once("rapport/RapportOIB.php");
include_once("rapport/RapportOIBS.php");
include_once("rapport/RapportOIBS2.php");
include_once("rapport/RapportOIV.php");
include_once("rapport/RapportPERF.php");
include_once("rapport/RapportTRANS.php");
include_once("rapport/RapportVHO.php");
include_once("rapport/RapportVOLK.php");
include_once("rapport/RapportHSEP.php");
include_once("rapport/RapportVOLKD.php");
include_once("rapport/RapportOIR.php");
include_once("rapport/RapportGRAFIEK.php");
include_once("rapport/RapportATT.php");
include_once("rapport/RapportCASH.php");
include_once("rapport/RapportCASHY.php");
include_once("rapport/RapportMODEL.php");
include_once("rapport/RapportZORG.php");
include_once("rapport/RapportPERFD.php");
include_once("rapport/RapportPERFG.php");
include_once("rapport/RapportVKM.php");
include_once("rapport/RapportSMV.php");
include_once("rapport/RapportTRANSFEE.php");



if($_POST['posted'])
{
$fondsregelsSamenvoegen = 1;


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
    if($_GET['type']=='email')
    {
      echo "<script> alert('Geen rapport bijgevoegd.');</script>";
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
	

	if(empty($_POST['selectedFields']) && empty($_POST['Portefeuille']) )	{
		echo "<b>Fout: geen portefeuille(s) opgegeven </b>";
		exit;
	}

	$rapportageDatum['a'] = jul2sql(form2jul($_POST['datum_van']));
	$rapJul=form2jul($_POST['datum_tot']);
	$valutaDatum = getLaatsteValutadatum();
	$valutaJul = db2jul($valutaDatum);
	if($rapJul > $valutaJul + 86400)
	{
		echo "<b>Fout: kan niet in de toekomst rapporteren.</b>";
		exit;
	}

	$hoofdPortefeuille = $postData['selectedFields'][0];
  
  $rapportageDatum['a'] = jul2sql(form2jul($_POST['datum_van']));
  $rapportageDatum['b'] = jul2sql($rapJul);

	if(isset($_POST['Portefeuille'])&& $_POST['Portefeuille'] <> '')
	{
		$portefeuille = $_POST['Portefeuille'];//$consolidatie['portefeuille'];
		$DB = new DB();
		$DB->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$portefeuille."'");
		$DB->Query();
		$pdata = $DB->nextRecord();
		$consolidatiePaar=$pdata;
		for($i=1;$i<41;$i++)
			if($pdata['Portefeuille'.$i] <> '')
				$portefeuilles[] = $pdata['Portefeuille'.$i];
	
	}
	else
	{
		$consolidatie = consolidatieAanmaken($_POST, $rapportageDatum['a'], $rapportageDatum['b']);
		$portefeuille = $consolidatie['portefeuille'];
		$rapportageDatum['a'] = $consolidatie['rapportageStart'];
		$rapportageDatum['b'] = $consolidatie['rapportageEind'];
		$portefeuilles = $consolidatie['portefeuilles'];
		$consolidatiePaar = $consolidatie['consolidatiePaar'];
		$hoofdPdata = $consolidatie['hoofdPdata'];
		$Pdata2 = $consolidatie['Pdata2'];
		$pdata = $consolidatie['pdata'];
	}

	if(checkAccess($type))
		$join = "";
	else
	{
		$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ".
			" VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
						JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
		$beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";

	}
	$query = "SELECT Portefeuilles.Startdatum, ".
		"Portefeuilles.Einddatum,		".
		"Portefeuilles.RapportageValuta, ".
		"Vermogensbeheerders.layout, ".
		"Vermogensbeheerders.attributieInPerformance,	".
		"Vermogensbeheerders.Vermogensbeheerder,	".
		"Vermogensbeheerders.Export_data_frontOffice	".
		" FROM (Portefeuilles, Vermogensbeheerders) ".$join." WHERE Portefeuilles.Portefeuille = '".$portefeuille."'".
		" AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder";

	verwijderTijdelijkeTabel($portefeuille);
	// asort
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$pdata = $DB->nextRecord();

	$frontOfficeData=unserialize($pdata['Export_data_frontOffice']);



	if(db2jul($rapportageDatum['a']) < db2jul($pdata['Startdatum']))
	{
		$rapportageDatum['a'] = $pdata['Startdatum'];
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
  

  $hoofdRapportageDatumVanaf = substr($rapportageDatum['a'],0,10);
	
	$rapportageDatumVanaf = $hoofdRapportageDatumVanaf;
	$hoofdRapportageDatum = substr($rapportageDatum['b'],0,10);
	
	$julrapport = db2jul($rapportageDatumVanaf);
	$rapportMaand = date("m",$julrapport);
	$rapportDag = date("d",$julrapport);
	$rapportJaar = date("Y",$julrapport);
	
	if($rapportMaand == 1 && $rapportDag == 1)
	{
		$startjaar = true;
	}
	else
	{
		$startjaar = false;
		$extrastart="$rapportJaar-01-01";
	}
	$rapportageDatum = 	$hoofdRapportageDatum ;


	if($_POST['doorkijk']==1)
	{
		$tmp = bepaalHuidfondsenVerdeling($portefeuille, $rapportageDatum, $rapportageDatumVanaf, $pdata['RapportageValuta']);
		vulTijdelijkeTabel($tmp, 'd_' . $portefeuille, $rapportageDatum);
	}
	$fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatumVanaf,$startjaar,$pdata['RapportageValuta'],$rapportageDatumVanaf);
	$fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum,false,$pdata['RapportageValuta'],$rapportageDatumVanaf);
	//verwijderTijdelijkeTabel($portefeuille,$rapportageDatumVanaf);
	//verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);
  if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
    vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,(substr($rapportageDatumVanaf,0,4)-1).'-12-31');
	vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$rapportageDatumVanaf);

	vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$rapportageDatum);

	if($extrastart)
	{
		$fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $extrastart,true,$pdata['RapportageValuta'],$extrastart);
		vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$extrastart);
	}


//rvv Extra query om resultaten van vorige jaar op te halen.
	$RapStartJaar = date("Y", db2jul($rapportageDatumVanaf));
	$RapStopJaar = date("Y", db2jul($rapportageDatum));
	$RapJaar = $RapStopJaar;
	if ($RapJaar != $RapStartJaar)
	{
     $fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $RapStartJaar."-12-31");
	   vulTijdelijkeTabel($fondswaarden[c] ,$portefeuille,$RapStartJaar."-12-31");
	}
//endrvv

	$pdf = new PDFRapport('L','mm');
  
  if($_POST['metWachtwoord']==1)
  {
    $query="SELECT wachtwoord FROM CRM_naw WHERE portefeuille='".$portefeuilles[0]."'";
    $DB->SQL($query); 
    $wdata=$DB->lookupRecord();

      if(strlen($wdata['wachtwoord']) < 6)
      {
        echo "Het ingestelde wachtwoord is te kort.";
        exit;
      }
      else
      {
         $pdf->SetProtection(array('print'),$wdata['wachtwoord'],'!airs2011!a');//
      }
  }
	$pdf->SetAutoPageBreak(true,15);
	$pdf->pagebreak = 190;
	$pdf->perfSettings = array('vvgl'=>$_POST['vvgl'],'perc'=>$_POST['perc'],'opbr'=>$_POST['opbr'],'kost'=>$_POST['kost']);
  //$pdf->mutSettings = array('GB_STORT_ONTTR'=>$_POST['GB_STORT_ONTTR'],'GB_overige'=>$_POST['GB_overige']);
  $pdf->transSettings = array('TRANS_RESULT'=>$_POST['TRANS_RESULT']);
  $pdf->ModelSettings = array('modelcontrole_level'=>$_POST['modelcontrole_level']);
  $pdf->lastPOST = $_POST;
  $pdf->rapport_datum=db2jul($rapportageDatum);
  $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
	$__appvar['consolidatie']['rekeningOnderdrukken'] = true;

  if(count($portefeuilles)<3)
  {
  	$__appvar['consolidatie']['portefeuillenaam1']=$hoofdPdata['naam'];
	  $__appvar['consolidatie']['portefeuillenaam2']=$Pdata2['naam'];
  }
  else
  {
   	$__appvar['consolidatie']['portefeuillenaam1']='';
	  $__appvar['consolidatie']['portefeuillenaam2']='';   
  }
  
  if(isset($consolidatiePaar))
  {
    if($consolidatiePaar['Naam'] <>'')
    {
      $__appvar['consolidatie']['portefeuillenaam1']=$consolidatiePaar['Naam'];
      $__appvar['consolidatie']['portefeuillenaam2']=$consolidatiePaar['Naam1'];
    }
  }
	$__appvar['consolidatie']['accountmanager']=$hoofdPdata['accountmanager'];
	$__appvar['consolidatie']['tweedeAanspreekpunt']=$hoofdPdata['tweedeAanspreekpunt'];

	$pdf->__appvar = $__appvar;
	$pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);


	if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
	{
	  $pdf->rapportageValuta = $pdata['RapportageValuta'];
	  $pdf->ValutaKoersBegin  = getValutaKoers($pdf->rapportageValuta,$rapportageDatumVanaf);
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


	// set volgorde
	$volgorde["OIH"] 		= $pdata["AfdrukvolgordeOIH"];
	$volgorde["OIS"] 		= $pdata["AfdrukvolgordeOIS"];
	$volgorde["HSE"] 		= $pdata["AfdrukvolgordeHSE"];
	$volgorde["OIB"] 		= $pdata["AfdrukvolgordeOIB"];
	$volgorde["OIV"] 		= $pdata["AfdrukvolgordeOIV"];
	$volgorde["PERF"] 	= $pdata["AfdrukvolgordePERF"];
	$volgorde["VOLK"] 	= $pdata["AfdrukvolgordeVOLK"];
	$volgorde["VHO"] 		= $pdata["AfdrukvolgordeVHO"];
	$volgorde["TRANS"] 	= $pdata["AfdrukvolgordeTRANS"];
	$volgorde["MUT"]	 	= $pdata["AfdrukvolgordeMUT"];
	$volgorde["HSEP"] 	= $pdata["AfdrukvolgordeHSE"];
	$volgorde["VOLKD"] 	= $pdata["AfdrukvolgordeVOLK"];
	$volgorde["OIR"] 		= $pdata["AfdrukvolgordeOIR"];
	$volgorde["GRAFIEK"] 		= $pdata["AfdrukvolgordeGRAFIEK"];
	$volgorde["ATT"] 		= $pdata["AfdrukvolgordeATT"];
	$volgorde["CASH"]= $pdata["AfdrukvolgordeCASHFLOW"];
  $volgorde["CASHY"]= $pdata["AfdrukvolgordeCASHFLOW-Y"];
  $volgorde["MODEL"]= $pdata["AfdrukvolgordeMODEL"];
  $volgorde["VOLKD"] = $pdata["AfdrukvolgordeVOLKD"];
  $volgorde["ATT"] = $pdata["AfdrukvolgordeATT"];
  $volgorde["ZORG"] = 18;
  $volgorde["KERNV"] = 18;
  $volgorde["KERNZ"] = 18;
	$volgorde["VKM"] = 18;
	$volgorde["VKMS"] = 18;
	$volgorde["VKMD"] = 18;
	$volgorde["TRANSFEE"] = 18;

  $pdf->lastPOST=$_POST;

	if($_POST['logoOnderdrukken'])
	  $pdf->rapport_logo='';

	if($_POST['debug'])
	 $pdf->debug=1;

	if($_POST['voorbladWeergeven'] )//&& ($pdf->rapport_layout == 14 || $pdf->rapport_layout == 16 || $pdf->rapport_layout == 17|| $pdf->rapport_layout == 18)
	{
	  $rapport_type[]    = 'FRONT';
	  $volgorde['FRONT'] = 0;
	}
	$frontOfficeData=unserialize($pdata['Export_data_frontOffice']);
  if(is_array($frontOfficeData))
  {
    foreach ($frontOfficeData as $rap=>$rapData)
    {
      if($rapData['volgorde'] == '')
        $rapData['volgorde']=99;
      $volgorde[$rap]=$rapData['volgorde'];
    }
  }
	asort($volgorde, SORT_NUMERIC);

	loadLayoutSettings($pdf, $portefeuille);

	if($_POST['logoOnderdrukken'])
	  $pdf->rapport_logo='';

	if($_POST['layout'])
	  $pdf->rapport_layout=$_POST['layout'];

	if($_POST['debug'])
	 $pdf->debug=1;

	$pdf->portefeuilles =$portefeuilles;
  $pdf->rapport_typen=$rapport_type;

	if (file_exists("rapport/include/PreProcessor_L".$pdf->rapport_layout.".php"))
	{
		include_once("rapport/include/PreProcessor_L".$pdf->rapport_layout.".php");
		$classString = 'PreProcessor_L'.$pdf->rapport_layout;
		$processor= new $classString($portefeuille,'',$pdf);
    $classString='';
	}
  
  if($_POST['extra']=='xls')
  {
    include_once('../classes/excel/Writer.php');
    //if($rapportnaam=='')
    //$rapportnaam='export.xls';
    $workbook = new Spreadsheet_Excel_Writer();//$__appvar['tempdir'].$rapportnaam
  }

	while (list($key, $value) = each($volgorde))
	{
    $pdf->excelData 	= array();
		if(in_array($key,$rapport_type))
		{
     
      if ($key=='PERF' && $pdata['attributieInPerformance'] == 1)
      {
        $key='ATT';
      }
      elseif($key=='FRONT')
			{
        $key='FRONTC';
			}
			elseif($key=='CASHFLOW-Y')
      {
        $key='CASHY';
      }
			elseif($key=='CASHFLOW')
      {
        $key='CASH';
      }
      
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
        //if(file_exists("rapport/Rapport".$key.".php"))
        //{
          include_once("rapport/Rapport".$key.".php");
          $classString = 'Rapport'.$key;
        //}
      }
      
      $rapport = new $classString($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
      $rapport->writeRapport();
      
      if($_POST['extra']=='xls')
      {
        if(isset($rapport->rapport_xls_titel))
          $xlsTitel=$rapport->rapport_xls_titel;
        else
          $xlsTitel=$key;
        $worksheet[$i] =& $workbook->addWorksheet($xlsTitel);
        if(is_object($rapport))
          $rapport->pdf->fillXlsSheet($worksheet[$i],$workbook);
      }
		}
	}
  
  
  if (file_exists("rapport/include/layout_".$pdf->rapport_layout."/RapportTemplate_L".$pdf->rapport_layout.".php") || file_exists("rapport/include/RapportTemplate_L".$pdf->rapport_layout.".php"))
  {
    if (file_exists("rapport/include/layout_" . $pdf->rapport_layout . "/RapportTemplate_L" . $pdf->rapport_layout . ".php"))
    {
      include_once("rapport/include/layout_" . $pdf->rapport_layout . "/RapportTemplate_L" . $pdf->rapport_layout . ".php");
    }
    if (file_exists("rapport/include/RapportTemplate_L" . $pdf->rapport_layout . ".php"))
    {
      include_once("rapport/include/RapportTemplate_L" . $pdf->rapport_layout . ".php");
    }
    include_once("rapport/include/RapportTemplate_L" . $pdf->rapport_layout . ".php");
    $classString = 'RapportTemplate_L' . $pdf->rapport_layout;
    $rapport = new $classString($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  }
  
  verwijderConsolidatie($portefeuille);
	
	verwijderTijdelijkeTabel($portefeuille,$rapportageDatumVanaf);
	verwijderTijdelijkeTabel($portefeuille,$rapportageDatum);
	 if ($RapJaar != $RapStartJaar)
	 {
	 verwijderTijdelijkeTabel($portefeuille,$RapStartJaar."-12-31");
	 }
  
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
    $workbook->send($rapportnaam);
    $workbook->close();
    
  }
  else
	{
  if($_GET['passwd'])
  {
    $db=new DB();
    $query="SELECT id,wachtwoord FROM CRM_naw WHERE Portefeuille='$portefeuille'";
    $db->SQL($query);
    $wachtwoord=$db->lookupRecord();
    if(strlen($wachtwoord['wachtwoord']) < 6)
      echo "Ingestelde wachtwoord < 6 tekens.";
    $pdf->SetProtection(array('print'),$wachtwoord['wachtwoord'],'!airs2011!a');//
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
		if(count($rapport_type) == 2)
		{
			$rapportnaam = $rapport_type[1];
		}
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$pdf->Output($portefeuille.$rapportnaam.".pdf","D");
	}
	else
	{
		$pdf->Output();
	}
  }
	exit();
}
?>
