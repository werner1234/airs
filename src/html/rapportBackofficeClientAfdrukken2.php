<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/06 14:56:43 $
 		File Versie					: $Revision: 1.69 $

 		$Log: rapportBackofficeClientAfdrukken2.php,v $
 		Revision 1.69  2020/05/06 14:56:43  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2020/05/02 15:55:33  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2020/04/29 15:56:45  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2020/04/18 17:04:33  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2019/11/16 17:36:02  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2019/08/28 15:41:48  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2019/08/17 18:09:25  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2019/08/10 17:26:01  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2019/07/31 14:36:48  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2019/07/28 09:23:44  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2019/07/20 16:31:57  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2019/01/30 16:44:37  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.56  2018/07/11 16:14:44  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2018/01/11 18:49:41  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2017/12/23 18:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2017/12/13 17:02:47  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2017/12/09 17:53:02  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2017/10/11 14:52:38  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2017/10/05 07:03:26  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2017/10/04 16:07:15  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2017/09/20 13:09:27  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2017/09/18 17:30:42  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2017/09/18 06:50:37  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2017/09/17 14:59:44  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2017/09/16 18:05:29  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2017/09/13 15:46:15  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2017/08/20 16:31:00  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2017/08/19 18:16:21  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2017/08/16 15:56:17  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2017/07/24 05:46:51  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2017/07/22 18:20:50  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2017/07/16 10:50:44  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2017/04/26 16:10:59  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2017/04/22 16:42:29  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2017/04/09 10:12:56  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2017/03/25 15:54:47  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2017/02/12 11:20:39  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2016/12/07 16:51:35  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2016/11/27 11:07:26  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2016/10/19 15:35:43  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2016/10/19 11:35:31  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2016/10/16 15:01:35  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2016/10/05 16:16:56  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2016/09/18 08:45:56  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2016/08/13 16:52:24  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/05/25 15:58:23  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/02/06 16:44:49  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/01/10 08:51:53  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/12/27 16:31:46  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/11/18 17:05:01  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/11/08 16:33:57  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2015/11/07 16:43:17  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2015/11/04 16:49:53  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2015/10/26 16:10:33  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2015/10/25 15:11:22  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/10/22 06:27:47  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/10/21 16:13:47  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/10/21 08:12:50  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/10/18 13:45:01  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/10/04 11:49:46  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/09/26 15:56:47  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/07/29 16:08:40  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/07/05 08:26:35  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/06/10 15:58:53  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/05/16 09:31:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/04/11 17:07:30  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/03/24 16:30:44  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/03/22 10:39:17  rvv
 		*** empty log message ***
 		
 	

*/
//$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/backofficeAfdrukken2Class.php");
include_once("../classes/portefeuilleVerdieptClass.php");
include_once("../classes/templateEmail.php");
include_once('../classes/AE_cls_phpmailer.php');
include_once("../classes/pdfMailing.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once('../classes/excel/Writer.php');

include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/RapportFront.php");
include_once("rapport/RapportMODEL.php");
include_once("rapport/factuur/PDFFactuur.php");
include_once("rapport/factuur/Factuur.php");

session_start();
$_SESSION['submenu'] = New Submenu();
$_SESSION['NAV'] = "";
session_write_close();


session_start();
if(!is_array($_SESSION['backofficeSelectie']))
  $_SESSION['backofficeSelectie']=array();
$_SESSION['backofficeSelectie']=array_merge($_SESSION['backofficeSelectie'],$_POST,$_GET);
$_SESSION['backofficeSelectie']['portefeuilleIntern']=$_SESSION['portefeuilleIntern'];

echo template($__appvar["templateContentHeader"],$content);

echo '<script>$("#afdrukkenStatus",parent.document).html(" ' . vt('Afdrukken') . '");</script>';
echo '<script>$("#factuurPrintStatus",parent.document).html("' . vt('Facturen printen') . '");</script>';
echo '<script>$("#factuurExportStatus",parent.document).html("' . vt('Facturen exporteren') . '");</script>';
echo '<script>$("#factuurExcelStatus",parent.document).html("' . vt('Factuurinfo naar Excel') . '");</script>';
echo '<script>$("#factuurDbStatus",parent.document).html("' . vt('Factuurinfo naar reportbuilder') . '");</script>';
if ($__exact["dagboek"] <> "")
  echo '<script>$("#factuurExactStatus",parent.document).html("' . vt('Factuurinfo naar ExactGlobe.') . '");</script>';
if ($__exactOnline["dagboek"] <> "")
  echo '<script>$("#factuurExactOnlineStatus",parent.document).html("' . vt('Factuurinfo naar ExactOnline.') . '");</script>';
if ($__twinfield["grootboek_debiteur"])
  echo '<script>$("#factuurTwinfieldStatus",parent.document).html("' . vt('Factuurinfo naar Twinfield.') . '");</script>';
if ($__snelstart["dagboek"] <> "")
  echo '<script>$("#factuurSnelstartStatus",parent.document).html("' . vt('Factuurinfo naar Snelstart.') . '");</script>';

echo '<script>$("#portaalStatus",parent.document).html("' . vt('Portaal') . '");</script>';
echo '<script>$("#exporterenStatus",parent.document).html("' . vt('Exporteren') . '");</script>';
echo '<script>$("#exporterenClusterStatus",parent.document).html("");</script>';

echo '<script>$("#emailenStatus",parent.document).html("' . vt('Emailen') . '");</script>';
echo '<script>$("#edossierStatus",parent.document).html("' . vt('Edosier') . '");</script>';

echo '<script>$("#rapportageInternStatus",parent.document).html("' . vt('Rapportage intern') . '");</script>';
echo '<script>$("#preRunStatus",parent.document).html("' . vt('PreRun') . '");</script>';

if($_SESSION['backofficeSelectie']['periode']=='Clienten')
{
  $toevoegenEmailDefault=1;
  $toevoegenDefault=1;
}
else
{
  $toevoegenEmailDefault=0;
  $toevoegenDefault=0;
}

if($_POST['posted'])
{
  $afdruk = new backofficeAfdrukken2($_SESSION['backofficeSelectie']);
  $selectData=$afdruk->validate();
  $afdruk->getPortefeuilles(true);
	$afdruk->startdatum = jul2sql($selectData['datumVan']);
	$afdruk->einddatum = jul2sql($selectData['datumTm']);

  $afdruk->initPrb(count($afdruk->portefeuilles));
  $afdruk->initPdf();
	$exportStamp = mktime();

  $pdfOpties=array();
  if($_POST['portaal']>0)
    $pdfOpties['lossePdf']=1;
  if($_POST['edossier']>0)
    $pdfOpties['lossePdf']=1;
  if($_POST['exporteren']>0)
    $pdfOpties['lossePdf']=1;    
  if($_POST['emailen']>0)
  {
    $pdfOpties['losseCryptedPdf']=1;
    if($_POST['losseFactuur']==1)
      $pdfOpties['losseCryptedFactuurPdf']=1;
  }
  if($_POST['factuurPrinten']>0)
  {
    $pdfOpties['facturenPdf']=1;
  }
  if($_POST['factuurExcel']>0||$_POST['factuurExact']>0)
  {
    $pdfOpties['facturenXls']=1;
  }
  
  if($_POST['afdrukken']<1 && $_POST['exporteren']<1 && $_POST['emailen']<1 && $_POST['edossier']<1 && $_POST['portaal']<1 && $_POST['factuurPrinten']<1 && $_POST['factuurExcel']<1 && $_POST['factuurDb']<1 && $_POST['factuurExact']<1 && $_POST['factuurWegschrijven'] < 1 && $_POST['exporterenCluster']<1 && $_POST['factuurExport']<1)
  {
    logscherm(vt("Geen uitvoer geselecteerd."));
    exit;
  }
  
  if($_POST['afdrukken']<1 && $_POST['exporteren']<1 && $_POST['emailen']<1 && $_POST['edossier']<1 && $_POST['portaal']<1 && $_POST['exporterenCluster']<1)
    $alleenFactuur=true;
  else
    $alleenFactuur=false;  

  $db=new DB();
	foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
 	{
    if($selectData['crmRapDatum']==1)
    {
      $naw=new NAW();
      $naw->getByField('Portefeuille',$portefeuille);
      if($naw->get('id')>0 && $naw->get('laatsteRapDatumSignalering') <> $afdruk->einddatum)
      {
        $naw->set('laatsteRapDatumSignalering', $afdruk->einddatum);
        $naw->save();
      }
      //$query = "UPDATE CRM_naw set laatsteRapDatumSignalering='" . $afdruk->einddatum . "' WHERE Portefeuille = '" . mysql_real_escape_string($portefeuille) . "'";
      //addTrackAndTrace('CRM_naw', $data['dossierId'], $data['veld'], $oldValue, $data['newValue'], $_SESSION['usersession']['user']);
      //$db->SQL($query);
      //$db->Query();
    }
    if($pdata['consolidatieToevoegen']==1)
    {
      $tmp = $afdruk->portefeuilleSelectie->createVirtuelePortefeuille($portefeuille, $pdata, true);
      if ($tmp['afgebroken'] == true)
      {
        logit(vtb("backoffice consolidatie voor %s afgebroken.", array($portefeuille)));
        continue;
      }
    }
 	  $afdruk->pdf->templateVars = array();
    $afdruk->portefeuilles[$portefeuille]=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);

	  verwijderTijdelijkeTabel($portefeuille);
	  $afdruk->pro_step += $afdruk->pro_multiplier;
  	$afdruk->prb->moveStep($afdruk->pro_step);
    $afdruk->setVolgorde($portefeuille);
    $afdruk->getCrmRapport($portefeuille);

    if(is_array($afdruk->portefeuilles[$portefeuille]['verzending']))
    {
      $verzending=$afdruk->portefeuilles[$portefeuille]['verzending'];
      $aantal=$afdruk->portefeuilles[$portefeuille]['aantal'];
      $overslaan=true;
      $melding='';
      foreach($verzending as $key=>$value)
        $melding.=" $key=$value";
      logscherm(vtb("Afdruk methode controleren %s. %s aantal:%s", array($portefeuille, $melding, $aantal)));

      if($verzending['geen']==1)
      {
        logscherm(vtb("Afdruk methode geen. Rapport generatie voor %s overslaan.", array($portefeuille)));
        continue;
      }
  

      if($_POST['afdrukken']==1 && $verzending['papier'] == 1)
      {
        logscherm(vtb("Pdf afdruk %s gewenst.", array($portefeuille)));
        $overslaan=false;
      }
      if($_POST['emailen']==1 && $verzending['email'] == 1)
      {
        logscherm(vtb("Email afdruk %s gewenst.", array($portefeuille)));
        $overslaan=false;
      }
      if($_POST['portaal']==1)// && ($verzending['portaal']==1 || $afdruk->portefeuilles[$portefeuille]['naarPortaalViaAantal']) )
      {
        logscherm(vtb("Portaal afdruk %s gewenst. (Rapportage altijd aanmaken.)", array($portefeuille)));
        $overslaan=false;
      }
      if($_POST['exporteren']==1 && (($_POST['exporterenEmail']==1 && $verzending['email']) || ($_POST['exporterenPdf']==1 && $verzending['papier']==1) || ($_POST['exporterenEmail']==0 && $_POST['exporterenPdf']==0)))
      {
        logscherm(vtb("Exporteren afdruk %s gewenst.", array($portefeuille)));
        $overslaan=false;
      }
      if($_POST['edossier']==1 && (($_POST['eDossierEmail']==1 && $verzending['email']) || ($_POST['eDossierPdf']==1 && $verzending['papier']==1) || ($_POST['eDossierEmail']==0 && $_POST['eDossierPdf']==0)))
      {
        logscherm(vtb("eDosier afdruk %s gewenst.", array($portefeuille)));
        $overslaan=false;
      }
      if($_POST['exporterenCluster']>0)
      {
        logscherm(vtb("Cluster afdruk %s gewenst.", array($portefeuille)));
        $overslaan=false;
      }
      if($overslaan==true)
      {
        logscherm(vtb("Voor %s geen rapport aanmaken.", array($portefeuille)));
        continue;
      }
    }
    else
    {
      logscherm(vtb("Geen afdruk instellingen gevonden, %s rapporten aanmaken.", array($portefeuille)));
     // continue;
    }

    if($alleenFactuur==true)
      $afdruk->rapport_type=array();
    
    $afdruk->loadPdfSettings($portefeuille);

    $afdruk->addReports($portefeuille);
    if($afdruk->portefeuilleSelectie->selectData['CRM_extraAdres'])
    {
       $afdruk->getExtraAdres($portefeuille);
       foreach ($afdruk->extraAdres as $extraAdres)
          $afdruk->addReports($portefeuille,$extraAdres);
    }
    $afdruk->verwijderTijdelijkeRapportage($portefeuille);
  }

  $afdruk->pdf->rapport_type = 'FRONT';
  $afdruk->pdf->addPage();//Add dummy page to close footer.

  if($selectData['type']=='xlsRapport')
  {
     $afdruk->createXlsZip();
     $afdruk->filename='export.zip';
     $afdruk->selectie['save']=1;
     $afdruk->pushPdf();
  }
  
  if($alleenFactuur==true)
    $opnieuwAanmaken=false;
  else
    $opnieuwAanmaken=true;

  if($_POST['preRun']>0)
    $afdruk->preRun=true;
  
  if($_POST['factuurExcel']>0||$_POST['factuurExact']>0||$_POST['factuurExactOnline']>0||$_POST['factuurTwinfield']>0||$_POST['factuurSnelstart']>0)
  {
    $afdruk->pdf->excelDataBackup=$afdruk->pdf->excelData;
    $afdruk->pdf->excelData=$afdruk->pdf->excelDataFactuur;
    $dateString=date('Y\mmd\tHis_');
    if(count($afdruk->pdf->excelData2)> 1)
    {
      $afdruk->pdf->OutputXls($__appvar['tempdir'].$dateString.'factuur.xls');
      $afdruk->pdf->excelData=$afdruk->pdf->excelData2;  
      $afdruk->pdf->OutputXls($__appvar['tempdir'].$dateString.'geenFactuur.xls');
      if($_POST['factuurExcel']>0)
        echo '<script>$("#factuurExcelStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuur.xls&action=attachment\'>' . vt('Factuur Excel') . ' <b>' . vt('download') . '</b></a><a href=\'pushFile.php?file='.$dateString.'geenFactuur.xls&action=attachment\'>Geen factuur Excel <b>download</b></a>");</script>';
    }
    else
    {
      $afdruk->pdf->OutputXls($__appvar['tempdir'].$dateString.'factuur.xls');
      if($_POST['factuurExcel']>0)
        echo '<script>$("#factuurExcelStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuur.xls&action=attachment\'>' . vt('Factuur Excel') . ' <b>' . vt('download') . '</b></a>");</script>';
    }
    if ($__exact["dagboek"] <> "" && $_POST['factuurExact']>0)
    {
      $afdruk->pdf->OutputExact($__appvar['tempdir'].$dateString.'factuurExact.csv');
      echo '<script>$("#factuurExactStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuurExact.csv&action=attachment\'>' . vt('Factuur Exact ExactGlobe') . '<b>' . vt('download') . '</b></a>");</script>';
    }
    if ($__exactOnline["dagboek"] <> "" && $_POST['factuurExactOnline']>0)
    {
      $afdruk->pdf->OutputExactOnline($__appvar['tempdir'].$dateString.'factuurExactOnline.xls');
      echo '<script>$("#factuurExactOnlineStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuurExactOnline.xls&action=attachment\'>' . vt('Factuur Exact Online') . ' <b>' . vt('download') . '</b></a>");</script>';
    }
    if ($__twinfield["grootboek_debiteur"] <> "" && $_POST['factuurTwinfield']>0)
    {
      $afdruk->pdf->OutputTwinfield($__appvar['tempdir'].$dateString.'factuurTwinfield.xls');
      echo '<script>$("#factuurTwinfieldStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuurTwinfield.xls&action=attachment\'>' . vt('Factuur Twinfield') . ' <b>' . vt('download') . '</b></a>");</script>';
    }
    if ($__snelstart["dagboek"] <> "" && $_POST['factuurSnelstart']>0)
    {
      $afdruk->pdf->OutputSnelstart($__appvar['tempdir'].$dateString.'factuurSnelstart.xls');
      echo '<script>$("#factuurSnelstartStatus",parent.document).html("<a href=\'pushFile.php?file='.$dateString.'factuurSnelstart.xls&action=attachment\'>' . vt('Factuur Snelstart') . ' <b>' . vt('download') . '</b></a>");</script>';
    }
    $afdruk->pdf->excelData=$afdruk->pdf->excelDataBackup;
  }
  
  if($_POST['factuurDb']>0)
  { 
    $afdruk->pdf->dbWaarden=$afdruk->factuurWaarden;
	  $afdruk->pdf->dbTable="CREATE TABLE `reportbuilder_$USR` (`id` INT NOT NULL AUTO_INCREMENT ,`Rapport` VARCHAR( 20 ) NOT NULL ,`client` VARCHAR( 50 ) NOT NULL ,`clientNaam` VARCHAR( 150 ) NOT NULL ,`clientNaam1` VARCHAR( 150 ) NOT NULL ,`clientAdres` VARCHAR( 150 ) NOT NULL ,`clientPostcode` VARCHAR( 150 ) NOT NULL ,`clientWoonplaats` VARCHAR( 150 ) NOT NULL ,`clientTelefoon` VARCHAR( 150 ) NOT NULL ,`clientFax` VARCHAR( 150 ) NOT NULL ,`clientEmail` VARCHAR( 150 ) NOT NULL ,`datumTot` date NOT NULL ,`datumVan` date NOT NULL ,`factuurNummer` VARCHAR( 50 ) NOT NULL ,`Portefeuille` VARCHAR( 24 ) NOT NULL ,`RapportageValuta` VARCHAR( 3 ) NOT NULL ,`totaalWaardeVanaf` DOUBLE NOT NULL ,`totaalWaarde` DOUBLE NOT NULL ,`gemiddeldeVermogen` DOUBLE NOT NULL ,`maandsWaarde_1` DOUBLE NOT NULL ,`maandsWaarde_2` DOUBLE NOT NULL ,`maandsWaarde_3` DOUBLE NOT NULL ,`maandsWaarde_4` DOUBLE NOT NULL ,`maandsGemiddelde` DOUBLE NOT NULL ,`huisfondsWaarde` DOUBLE NOT NULL ,`beheerfeeOpJaarbasis` DOUBLE NOT NULL ,`performancefee` DOUBLE NOT NULL ,`administratieBedrag` DOUBLE NOT NULL ,`BeheerfeeTeruggaveHuisfondsenPercentage` DOUBLE NOT NULL ,`BeheerfeeRemisiervergoedingsPercentage` DOUBLE NOT NULL ,`totaalTransactie` DOUBLE NOT NULL ,`beheerfeeBetalen` DOUBLE NOT NULL ,`btw` DOUBLE NOT NULL ,`beheerfeeBetalenIncl` DOUBLE NOT NULL ,`stortingenOntrekkingen` DOUBLE NOT NULL ,`resultaat` DOUBLE NOT NULL ,`performancePeriode` DOUBLE NOT NULL ,`performanceJaar` DOUBLE NOT NULL ,`rekenvermogen` DOUBLE NOT NULL ,`BeheerfeePercentageVermogenDeelVanJaar` DOUBLE NOT NULL ,`nettoVermogenstoenameYtd` DOUBLE NOT NULL ,`beginwaardeJaar` DOUBLE NOT NULL ,`periodeDeelVanJaar` DOUBLE NOT NULL,`BeheerfeeBedragBuitenBTWPeriode` DOUBLE NOT NULL ,`add_date` datetime, `debiteurnr` varchar(20) ,PRIMARY KEY ( `id` ),KEY `Portefeuille` (`Portefeuille`)) ";
	  $afdruk->pdf->OutputDatabase();
    logscherm('Reportbuilder gevuld met ('.count($afdruk->pdf->dbWaarden).') records.');
    echo '<script>$("#factuurDbStatus",parent.document).html("<a href=\"javascript:if(confirm(\'' . vt('U gaat dit scherm verlaten waardoor bestanden die nog niet gedownload zijn verloren gaan. Wilt u verdergaan?') . '\')){window.location =\'reportBuilder2.php\';}\"><b>Naar reportbuilder</b></a>");</script>';
  }

  if($_POST['factuurWegschrijven']>0)
  {
    $afdruk->sendToBeheerfeeHistorie();
  }
  //listarray($afdruk->paginas);
  //listarray($afdruk->pdf->pages);
  $exportFilesCluster=array();
  if($_POST['exporterenCluster'] > 0)
  {
    $clusters = $afdruk->getClusters(array_keys($afdruk->portefeuilles),$afdruk->portefeuilleSelectie->selectData);
    //listarray($clusters);exit;
    foreach ($clusters as $clusterData)
    {
      logScherm('Cluster pdf voor ' . $clusterData['clusterOmschrijving'] . ' met portefeuilles (\'' . implode("','", $clusterData['portefeulles']) . '\')');
      $afdruk->cloneInit('losseClusterPdf');
      foreach($clusterData['portefeulles'] as $clusterPortefeuille)
      {
        foreach ($afdruk->paginas as $portefeuille => $paginaData)
        {
          if ($portefeuille==$clusterPortefeuille)
          {
            logScherm(vtb('Van portefeuille "%s" pagina %s tot %s toegevoegd aan cluster "%s" pdf.', array($portefeuille, ($paginaData['begin'] + 1), $paginaData['eind'], $clusterData['cluster'])));
            $pages = $afdruk->cloneGetPages($paginaData, 'all');
            $afdruk->cloneAddPage('losseClusterPdf', $pages);
          }
        }
      }
      $file = $afdruk->cloneWriteFile('losseClusterPdf', 'los', $clusterData['cluster']);
      $dir = dirname($file);

      if($afdruk->selectie['bestandsnaamClusterEind']<>'')
      {
        $tailPart='_'.$afdruk->selectie['bestandsnaamClusterEind'];
      }
      else
      {
        $tailPart='';
      }
      $clusterFile = $dir . '/'.$afdruk->fixFilename($clusterData['cluster'].'_Cluster' .$tailPart. '.pdf');
      rename($file, $clusterFile);
      //logscherm("rename($file, $clusterFile);");
      $exportFilesCluster[] = $clusterFile;
    }
  }

  
  foreach($afdruk->paginas as $portefeuille=>$paginaData)
  {
    $afdruk->currentPortefeuille=$portefeuille;
    if($pdfOpties['facturenPdf']==1 || $pdfOpties['lossePdf']==1 || $pdfOpties['losseCryptedPdf']==1)
      logScherm(vtb('Losse pdf bestanden maken voor %s', array($portefeuille)));
    
    $toevoegenEmail=$toevoegenEmailDefault;  
    $verzending=$paginaData['verzending'];
    if(is_array($verzending))
      $toevoegenEmail=$verzending['email'];

    if($verzending['geen']==1)
      continue;
               
    if($pdfOpties['facturenPdf']==1)
    {
      if(!isset($afdruk->pdfclone['facturenPdf']))
        $afdruk->cloneInit('facturenPdf');
      
      $afdruk->cloneGetPages($paginaData,'FACTUUR');  
      $pages=$afdruk->cloneGetPages($paginaData,'FACTUUR');
      $afdruk->cloneAddPage('facturenPdf',$pages);
      logScherm(vtb('Factuur voor %s van pagina %s tot %s in factuur pdf.', array($portefeuille, $pages[0], $pages[count($pages)-1])));
    }
  
    if($_POST['factuurExport'])
    {
      $pages=$afdruk->cloneGetPages($paginaData,'FACTUUR');
      if(count($pages)==0)
      {
        logScherm("<b>" . vtb('Geen factuur voor %s gevonden.', array($portefeuille)) . "</b>");
      }
      elseif($_POST['factuurexportWachtwoord']==1)
      {
        $pdata = $afdruk->portefeuilles[$portefeuille];
        if ($pdata['wachtwoord'] <> '' && strlen($pdata['wachtwoord']) > 5)
        {
          $afdruk->cloneInit('losCryptExpPdf', $pdata['wachtwoord']);
          $afdruk->cloneAddPage('losCryptExpPdf', $pages);
          $file = $afdruk->cloneWriteFile('losCryptExpPdf', 'los', $portefeuille);
        }
        else
        {
          logScherm("<b>" . vtb('Geen wachtwoord voor %s geconfigureerd.', array($portefeuille)) . "</b>");
          unlink($file);
        }
      }
      else
      {
        $afdruk->cloneInit('facturenExportPdf');
        $afdruk->cloneAddPage('facturenExportPdf',$pages);
        $file = $afdruk->cloneWriteFile('facturenExportPdf', 'los', $portefeuille);
      }
      
      if(file_exists($file))
      {
        $alleenFactuurNr=$_POST['factuurexportBestandFactuurNr'];
        $fileExport = $afdruk->getFilePath($portefeuille, 'export') . $afdruk->getFilename($portefeuille,true,$alleenFactuurNr);
        rename($file, $fileExport);
        $exportFactuurFiles[] = $fileExport;
        logScherm('Factuur voor ' . $portefeuille . ' klaar voor export.');
      }
      if(file_exists($file))
        unlink($file);
    }


    if($pdfOpties['lossePdf']==1)
    {
      logScherm(vtb('Losse pdf voor %s van pagina %s tot %s.', array($portefeuille, ($paginaData['begin']+1), $paginaData['eind'])));
      $afdruk->cloneInit('lossePdf');
      $pages=$afdruk->cloneGetPages($paginaData,'all');
      $afdruk->cloneAddPage('lossePdf',$pages);
      $file=$afdruk->cloneWriteFile('lossePdf','los',$portefeuille);

      if($_POST['edossier'])
      {
        
        if(($_POST['eDossierEmail']==1 && $verzending['email']) || ($_POST['eDossierPdf']==1 && $verzending['papier']==1) || ($_POST['eDossierEmail']==0 && $_POST['eDossierPdf']==0 && $afdruk->portefeuilles[$portefeuille]['naarPortaalViaAantal']==false))
        {
          
          if($_POST['edossierLosseFactuur']==1)
          {
            logScherm(vtb('Losse factuur pdf voor %s naar eDossier.', array($portefeuille)));
            $pages=$afdruk->cloneGetPages($paginaData,'FACTUUR');
            $filter='';
            if(count($pages) > 0)
            {
              $afdruk->cloneInit('lossePdfEdossier');
              $afdruk->cloneAddPage('lossePdfEdossier',$pages);
              $fileFactuur=$afdruk->cloneWriteFile('lossePdfEdossier','Factuur',$portefeuille,'factuur_');
              $afdruk->sendToDossier($portefeuille, $fileFactuur,false);
              $filter='FACTUUR';
            }
            if($selectData['edossierLosseFactuurZonderRapportage']==false)
            {
              $afdruk->cloneInit('lossePdfEdossier');
              $pages=$afdruk->cloneGetPages($paginaData,'all',$filter);
              $afdruk->cloneAddPage('lossePdfEdossier',$pages);
              $file=$afdruk->cloneWriteFile('lossePdfEdossier','los',$portefeuille);
              $afdruk->sendToDossier($portefeuille, $file,false);
              //logScherm('Losse rapportage pdf voor ' . $portefeuille . ' naar eDossier.');
            }
          }
          else
          {
            logScherm(vtb('Losse pdf voor %s naar eDossier.', array($portefeuille)));
            $afdruk->sendToDossier($portefeuille, $file, false);
          }
        }
        else
        {
          if($afdruk->portefeuilles[$portefeuille]['naarPortaalViaAantal']==true)
            logScherm(vtb('Geen edossier voor %s, enkel portaal.', array($portefeuille)));
          else
            logScherm(vtb('Geen edossier instellingen voor  %s .', array($portefeuille)));
        }
      }
      if($_POST['exporteren'])
      {
        if(($_POST['exporterenEmail']==1 && $verzending['email']) || ($_POST['exporterenPdf']==1 && $verzending['papier']==1) || ($_POST['exporterenEmail']==0 && $_POST['exporterenPdf']==0 && $afdruk->portefeuilles[$portefeuille]['naarPortaalViaAantal']==false))
        {
          if($_POST['exportWachtwoord']==1)
          {
            $pdata = $afdruk->portefeuilles[$portefeuille];
            if ($pdata['wachtwoord'] <> '' && strlen($pdata['wachtwoord']) > 5)
            {
              $afdruk->cloneInit('losCryptExpPdf', $pdata['wachtwoord']);
              $pages = $afdruk->cloneGetPages($paginaData, 'all');
              $afdruk->cloneAddPage('losCryptExpPdf', $pages);
              $file = $afdruk->cloneWriteFile('losCryptExpPdf', 'los', $portefeuille);
            }
            else
            {
              logScherm("<b>" . vtb('Geen wachtwoord voor %s geconfigureerd.', array($portefeuille)) . "</b>");
              unlink($file);
            }
          }
          if(file_exists($file))
          {
            $fileExport = $afdruk->getFilePath($portefeuille, 'export') . $afdruk->getFilename($portefeuille);
            rename($file, $fileExport);
            $exportFiles[] = $fileExport;
            logScherm(vtb('Rapportage voor %s klaar voor export.', array($portefeuille)));
            $brief = $afdruk->pdfBriefAanmaken($portefeuille, $afdruk->getFilePath($portefeuille, 'export'));
            if ($brief <> '')
            {
              $exportFiles[] = $brief;
            }
          }
        }
        else
        {
          if($afdruk->portefeuilles[$portefeuille]['naarPortaalViaAantal']==true)
            logScherm(vtb('Geen export voor %s, enkel portaal.', array($portefeuille)));
          else
            logScherm(vtb('Geen export instellingen voor  %s .', array($portefeuille)));
        }
      }
      if(file_exists($file))
        unlink($file);
  
      if($_POST['portaal'])
      {
        $factuurPdfData='';
        if($_POST['portaalLosseFactuur']==1)
        {
          $pages=$afdruk->cloneGetPages($paginaData,'FACTUUR');
          if(count($pages) > 0)
          {
            $afdruk->cloneInit('lossePdfPortaal');
            $afdruk->cloneAddPage('lossePdfPortaal',$pages);
            $file=$afdruk->cloneWriteFile('lossePdfPortaal','Factuur',$portefeuille,'factuur_');
            $factuurPdfData = file_get_contents($file);
            $filter='FACTUUR';
            

          }
        }
        else
          $filter='';

        $afdruk->cloneInit('lossePdfPortaal');
        $pages = $afdruk->cloneGetPages($paginaData, 'all', $filter, 'email');
        $afdruk->cloneAddPage('lossePdfPortaal', $pages);
        $filePortaal = $afdruk->cloneWriteFile('lossePdfPortaal', 'los', $portefeuille);
        if ($afdruk->portefeuilles[$portefeuille]['check_portaalCrmVink'] == 1)
        {
          if ($verzending['portaal'] == 1)
          {
            logScherm(vtb('Losse pdf voor %s naar portaal via CRM portaal verzending.', array($portefeuille)));
            $afdruk->sendToPortaal($portefeuille, $filePortaal, '', $factuurPdfData);
          }
          else
          {
            logScherm(vtb('Portefeuille %s heeft geen portaal verzending in CRM.', array($portefeuille)));
          }
        }
        else
        {
          logScherm(vtb('Losse pdf voor %s naar portaal.', array($portefeuille)));
          $afdruk->sendToPortaal($portefeuille, $filePortaal, '', $factuurPdfData);
        }
      }
      if(file_exists($file))
        unlink($file);
      if(file_exists($filePortaal))
        unlink($filePortaal);
      
    }

    if($pdfOpties['losseCryptedPdf']==1 && $_POST['emailen']>0 && $toevoegenEmail==1) //||$selectData['losseFactuurZonderRapportage']))
    {
      $pdata=$afdruk->portefeuilles[$portefeuille];
      if($pdata['wachtwoord'] <> '' && strlen($pdata['wachtwoord'])>5)
      {
        logScherm(vtb("email pdf aanmaken voor %s", array($portefeuille)));
        $afdruk->cloneInit('losCryptPdf',$pdata['wachtwoord']);
        if($selectData['losseFactuur']==true)
          $filter='FACTUUR';
        else
          $filter='';
        
        $pages=$afdruk->cloneGetPages($paginaData,'all',$filter,'email');
  
        if($selectData['losseFactuurZonderRapportage']==false)
        {
          $afdruk->cloneAddPage('losCryptPdf', $pages);
          $file = $afdruk->cloneWriteFile('losCryptPdf', 'wachtwoord', $portefeuille);
          logScherm(vtb('Losse ww pdf voor %s van pagina %s tot %s.', array($portefeuille, ($paginaData['begin'] + 1), $paginaData['eind'])));
        }
        if($selectData['losseFactuur']==true)
        {
          $pages=$afdruk->cloneGetPages($paginaData,'FACTUUR');
          if(count($pages) > 0)
          {
            $afdruk->cloneInit('losCryptPdfF',$pdata['wachtwoord']);
            $afdruk->cloneAddPage('losCryptPdfF',$pages);
            $afdruk->resetVoet('losCryptPdfF');
            $factuurFile=$afdruk->cloneWriteFile('losCryptPdfF','wachtwoordFactuur',$portefeuille,'factuur_');
            
          }
          logScherm(vtb('Losse ww factuur voor %s. ', array($portefeuille)));
        }

        if($_POST['emailen'])
        {
          if($selectData['losseFactuur']==true)
          {
            if(count($pages)>0)
            {
              logscherm(vtb('losse factuur voor %s.', array($portefeuille)));
              $afdruk->sendByEmail($portefeuille, $file, '', $factuurFile);
            }
            else
            {
              logscherm(vtb('Geen factuur gevonden voor %s.', array($portefeuille)));
              if($selectData['losseFactuurZonderRapportage']==false)
              {
                $afdruk->sendByEmail($portefeuille, $file, $paginaData['adres']);
              }
            }
          }
          else
          {
            if($selectData['losseFactuurZonderRapportage']==false)
            {
              logscherm(vtb('emailQueue voor %s.', array($portefeuille)));
              $afdruk->sendByEmail($portefeuille, $file);
            }
          }
        }
  
        if(file_exists($file))
          unlink($file);
      }
      else
        logscherm(vtb('Geen wachtwoord voor %s ingesteld.', array($portefeuille)));
      

      foreach($paginaData['extra'] as $naam=>$paginaData)
      {
        if($paginaData['adres']['wachtwoord'] <> '' && strlen($paginaData['adres']['wachtwoord'])>5)
        { //extra adressen
          $afdruk->cloneInit('losCryptPdf',$paginaData['adres']['wachtwoord']);
          if($selectData['losseFactuur']==true)
            $filter='FACTUUR';
          else
            $filter='';  
          $pages=$afdruk->cloneGetPages($paginaData,'all',$filter,'email');
          $afdruk->cloneAddPage('losCryptPdf',$pages);
          $file=$afdruk->cloneWriteFile('losCryptPdf','wachtwoord',$portefeuille);

          if($selectData['losseFactuur']==true)
          {
            $factuurPaginas=$afdruk->cloneGetPages($paginaData,'FACTUUR');
            if(count($factuurPaginas) > 0)
            {
              $afdruk->cloneInit('losCryptPdfF',$paginaData['adres']['wachtwoord']);
              $afdruk->cloneAddPage('losCryptPdfF',$factuurPaginas);
              $afdruk->resetVoet('losCryptPdfF');
              $factuurFile=$afdruk->cloneWriteFile('losCryptPdfF','wachtwoordFactuur',$portefeuille,'factuur_');
            }
            logScherm(vtb('Losse ww factuur voor %s extra adres %s.', array($portefeuille, $naam)));
          }

          if($selectData['losseFactuur']==true)
          {
            if(count($factuurPaginas)>0) //file_exists($factuurFile))//
              $afdruk->sendByEmail($portefeuille,$file,$paginaData['adres'],$factuurFile);
            else
            {
              logscherm(vtb('Geen factuur gevonden voor %s.', array($portefeuille)));
              $afdruk->sendByEmail($portefeuille,$file,$paginaData['adres'],'');
            }
            $factuurPaginas=0;
          }
          else
          {
            $afdruk->sendByEmail($portefeuille,$file,$paginaData['adres'],'');
          }
          if(file_exists($file))
            unlink($file);
          if(file_exists($factuurFile))
            unlink($factuurFile);
        
        }
        else
          logscherm(vtb('Geen wachtwoord voor %s extra adres %s ingesteld.', array($portefeuille, $naam)));
        
      }
    }
  }
 
  if($pdfOpties['facturenPdf']==1 && $afdruk->preRun==false)
  {

    $afdruk->resetVoet('facturenPdf');
    $doelfile=$afdruk->cloneWriteFile('facturenPdf');
     echo '<script>$("#factuurPrintStatus",parent.document).html("<a href=\'pushFile.php?file='.basename($doelfile).'&action=attachment\'>' . vt('Facturen printen') . ' <b>' . vt('download') . '</b></a>");</script>';
  }

  if($_POST['portaal'] && $afdruk->preRun==false)
  {
    $extraStatus='';
    if($_POST['portaalMail']==1)
      $extraStatus='&nbsp; <a href=\"javascript:if(confirm(\'' . vt('U gaat dit scherm verlaten waardoor bestanden die nog niet gedownload zijn verloren gaan. Wilt u verdergaan?') . '\')){window.location =\'emailqueueList.php\';}\"><b>Naar email queue.</b></a>';
    
    echo '<script>$("#portaalStatus",parent.document).html("' . vt('Portaal export klaar.') . ' <a href=\"javascript:if(confirm(\'' . vt('U gaat dit scherm verlaten waardoor bestanden die nog niet gedownload zijn verloren gaan. Wilt u verdergaan?') . '\')){window.location =\'portaalqueueList.php\';}\"><b>Naar portaal queue.</b></a>'.$extraStatus.'");</script>';
  }
  if($_POST['exporteren'] && $afdruk->preRun==false)
  {
        if(substr(php_uname('n'),-8)=='.airs.nl' || 1)
        {       
          include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
          $zipfile=$__appvar['tempdir'].date('Y\mmd\tHis_')."export.zip";
          $zip=new PclZip($zipfile);
          $zip->create($exportFiles,PCLZIP_OPT_REMOVE_ALL_PATH);
          if($_POST['exporterenSftp']==1)
          {
            $afdruk->transferToSftp($zipfile,'TB.'.date('Y.m',$afdruk->selectie['datumTm']).'.ZIP');
          }
          echo '<script>$("#exporterenStatus",parent.document).html("<a href=\'showTempfile.php?show=1&filename='.basename($zipfile).'&unlink=1\'> ' . vt('Export voltooid') . ' <b>' . vt('download als zip') . '</b></a>");</script>';
          foreach($exportFiles as $file)
            unlink($file);
        }
        else
          echo '<script>$("#exporterenStatus",parent.document).html("' . vt('Export voltooid') . '");</script>';
        
  }
  
  if($_POST['factuurExport']==1 && $afdruk->preRun==false)
  {
    if(substr(php_uname('n'),-8)=='.airs.nl' || 1)
    {
      include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
      $zipfile=$__appvar['tempdir'].date('Y\mmd\tHis_')."exportFactuur.zip";
      $zip=new PclZip($zipfile);
      $zip->create($exportFactuurFiles,PCLZIP_OPT_REMOVE_ALL_PATH);
      echo '<script>$("#factuurExportStatus",parent.document).html("<a href=\'showTempfile.php?show=1&filename='.basename($zipfile).'&unlink=1\'> ' . vt('Factuur export voltooid') . ' <b>' . vt('download als zip') . '</b></a>");</script>';
      foreach($exportFactuurFiles as $file)
        unlink($file);
    }
    else
      echo '<script>$("#factuurExportStatus",parent.document).html("' . vt('Export voltooid') . '");</script>';

  }
  
  if($_POST['exporterenCluster'] > 0 && count($exportFilesCluster)>0)
  {
    if(substr(php_uname('n'),-8)=='.airs.nl' || 1)
    {
      include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
      $zipfile=$__appvar['tempdir'].date('Y\mmd\tHis_')."cluster_export.zip";
      $zip=new PclZip($zipfile);
      $zip->create($exportFilesCluster,PCLZIP_OPT_REMOVE_ALL_PATH);
      echo '<script>$("#exporterenClusterStatus",parent.document).html("<a href=\'showTempfile.php?show=1&filename='.basename($zipfile).'&unlink=1\'>  voltooid <b>download als zip</b></a>");</script>';
      foreach($exportFilesCluster as $file)
        unlink($file);
    }
    else
      echo '<script>$("#exporterenClusterStatus",parent.document).html("' . vt('Export voltooid') . '");</script>';
  }

if($_POST['emailen'] && $afdruk->preRun==false)
  echo '<script>$("#emailenStatus",parent.document).html("' . vt('Mails in wachtrij geplaatst.') . ' <a href=\"javascript:if(confirm(\'' . vt('U gaat dit scherm verlaten waardoor bestanden die nog niet gedownload zijn verloren gaan. Wilt u verdergaan?') . '\')){window.location =\'emailqueueList.php\';}\"><b>' . vt('Naar email queue') . '</b></a>");</script>';

  if($_POST['edossier'] && $afdruk->preRun==false)
    echo '<script>$("#edossierStatus",parent.document).html("' . vt('Documenten in wachtrij geplaatst.') . ' <a href=\"javascript:if(confirm(\'' . vt('U gaat dit scherm verlaten waardoor bestanden die nog niet gedownload zijn verloren gaan. Wilt u verdergaan?') . '\')){window.location =\'edossierqueueList.php\';}\"><b>' . vt('Naar eDossier queue') . '</b></a>");</script>';


  if($_POST['afdrukken']>0 && $opnieuwAanmaken > 0)
  {
     logScherm(vt('Nieuwe "afdrukken" pdf maken.'));
     $afdruk->cloneInit('totaalPdf');
     foreach($afdruk->paginas as $portefeuille=>$paginaData)
     { 
       $afdruk->currentPortefeuille=$portefeuille;
       $verzending=$paginaData['verzending'];
       $toevoegen=$toevoegenDefault;
       if(is_array($verzending))
         $toevoegen=$verzending['papier'];

       if($afdruk->portefeuilleSelectie->selectData['CRM_rapport_vink']==0)
         $toevoegen=1;
       logScherm(vtb('Aantal afdrukken : %s', array($afdruk->portefeuilles[$portefeuille]['aantal'])));
       if($toevoegen==1)
       {

         for($aantal=1;$aantal<=$afdruk->portefeuilles[$portefeuille]['aantal'];$aantal++)
         {
           logScherm("Portefeuille: ".$portefeuille.' afdruk '.$aantal.' toegevoegd.'); 
           $pages=$afdruk->cloneGetPages($paginaData,'all');
           $afdruk->cloneAddPage('totaalPdf',$pages);
         }
     
         foreach($paginaData['extra'] as $naam=>$paginaData)
         {
           logScherm(vtb('Portefeuille: %s extra adres %s afdruk toegevoegd.', array($portefeuille, $naam)));
           $pages=$afdruk->cloneGetPages($paginaData,'all');
           $afdruk->cloneAddPage('totaalPdf',$pages);
         }
       }
     }

     $doelfile=$afdruk->cloneWriteFile('totaalPdf','rapportage');
     if($afdruk->preRun==false)
       echo '<script>$("#afdrukkenStatus",parent.document).html("<a href=\'pushFile.php?file='.basename($doelfile).'&action=attachment\'> ' . vt('Afdrukken') . ' <b>' . vt('download') . '</b></a>");</script>';
     
  }

  
  if($afdruk->preRun==true)
  {
    $afdruk->preRun=false;
    $link='';
    $cloneNameVertaling=array(
      'losCryptPdfF'    =>  vt('eMail factuur'),
      'facturenPdf'     =>  vt('facturen'),
      'losCryptPdf'     =>  vt('eMail rapportage'),
      'totaalPdf'       =>  vt('Afdrukken rapportage'),
      'lossePdf'        =>  vt('Export rapportage')
    );
    foreach($afdruk->pdfclone as $cloneNaam=>$pdfObject)
    {
      $doelfile=$afdruk->cloneWriteFile($cloneNaam,'preRun');
      $link.='<a href=\'pushFile.php?file='.basename($doelfile).'&action=attachment\'>'.$cloneNameVertaling[$cloneNaam].' <b>' . vt('download') . '</b></a> &nbsp; ';
    }
    echo '<script>$("#preRunStatus",parent.document).html("Pre-run '.$link.'");</script>';

  }

  if($_POST['rapportageIntern']>0)
  {
    $dateString = date('Y\mmd\tHis_');
    $file = $__appvar['tempdir'] . "/" . $dateString . 'intern.pdf';
    $afdruk->pdf->Output($file, "F");
    echo '<script>$("#rapportageInternStatus",parent.document).html("<a href=\'showTempfile.php?show=1&filename='.basename($file).'&unlink=1\'> ' . vt('Rapportage intern') . ' <b>' . vt('download') . '</b></a>");</script>';
  }
  
  
  $afdruk->prb->hide();
logScherm(vt("Rapportage klaar."));

echo template($__appvar["templateContentFooter"],$content);
exit();

}
?>