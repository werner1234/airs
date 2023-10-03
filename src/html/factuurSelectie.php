<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/11 17:29:42 $
 		File Versie					: $Revision: 1.84 $

moet naar RVV

 		$Log: factuurSelectie.php,v $
 		Revision 1.84  2020/07/11 17:29:42  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2019/08/21 15:31:44  rvv
 		*** empty log message ***
 		
 		Revision 1.82  2019/07/13 17:47:04  rvv
 		*** empty log message ***
 		
 		Revision 1.81  2018/10/14 13:03:19  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2018/05/05 19:15:14  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2018/02/22 13:53:01  cvs
 		snelstart
 		
 		Revision 1.78  2018/01/11 18:50:23  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2017/12/08 10:06:55  cvs
 		call 6363
 		
 		Revision 1.76  2017/12/04 14:40:59  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2017/12/03 12:43:11  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2017/11/11 18:22:43  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2017/11/05 13:42:54  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2017/11/04 17:37:52  rvv
 		*** empty log message ***
 		
 		Revision 1.71  2017/03/08 16:21:32  cvs
 		call 5462
 		
 		Revision 1.70  2016/10/16 15:04:38  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2016/10/12 16:16:21  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2016/05/25 15:58:23  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2016/01/17 18:07:00  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2015/04/11 17:07:57  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2015/02/25 17:24:49  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2014/12/20 22:00:14  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2014/12/17 09:03:20  cvs
 		dbs 3188 toevoegen Export naar Twinfield
 		
 		Revision 1.62  2014/09/13 14:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2014/07/07 10:17:37  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2014/07/06 12:33:09  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2014/04/26 16:40:33  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2014/03/29 16:21:29  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2014/01/08 17:02:51  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2013/12/22 16:04:27  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2013/11/13 15:50:39  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2013/10/05 15:56:28  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2013/05/26 13:52:44  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2012/11/21 15:11:46  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2012/07/25 15:59:25  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2012/07/11 15:48:47  rvv
 		*** empty log message ***

 		Revision 1.49  2012/06/30 14:35:35  rvv
 		*** empty log message ***

 		Revision 1.48  2011/12/11 10:57:35  rvv
 		*** empty log message ***

 		Revision 1.47  2011/11/05 16:03:45  rvv
 		*** empty log message ***

 		Revision 1.46  2011/07/05 18:08:04  rvv
 		*** empty log message ***

 		Revision 1.45  2011/05/05 15:44:12  rvv
 		*** empty log message ***

 		Revision 1.44  2011/04/17 09:11:14  rvv
 		*** empty log message ***

 		Revision 1.43  2011/03/30 13:56:41  rvv
 		*** empty log message ***

 		Revision 1.42  2011/03/13 18:40:35  rvv
 		*** empty log message ***

 		Revision 1.41  2010/01/13 16:57:28  rvv
 		*** empty log message ***

*/
//$AEPDF2=true;
include_once("wwwvars.php");


include_once('../classes/AE_cls_progressbar.php');

include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/backofficeAfdrukkenClass.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFRapport.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");




if($_GET['lookup']==1)
{
	$DB=new DB();
	$selectie=$_POST;
	$selectie['datumVan'] 							= form2jul($selectie['datumVan']);
	$selectie['datumTm'] 								= form2jul($selectie['datumTm']);
	$selectie['backoffice'] 						= true;

	$portefeuilleSelectie= new portefeuilleSelectie($selectie);
  $portefeuilles=$portefeuilleSelectie->getSelectie();
	$query="SELECT id,portefeuille FROM FactuurBeheerfeeHistorie WHERE portefeuille IN('".implode("','",array_keys($portefeuilles)) ."') AND periodeDatum='".date('Y-m-d',$selectie['datumTm'])."' ";
	$DB->SQL($query);
	$DB->Query();
	$records=$DB->records();
	$msg='leeg';
	if($records>0)
	{
		$aanwezig=array();
		while($data=$DB->nextRecord())
		{
			$aanwezig[]=$data['portefeuille'];
		}
		if(count($aanwezig)<10)
			$msg= vt('Er zijn al records aanwezig voor portefeuille(s)') . " (".implode(",",$aanwezig).") op ".$_POST['datumTm'].". " . vt('Deze records zullen worden overschreven. Doorgaan?');
		else
			$msg=  vt('Er zijn al') . ' ' . $records . ' ' . vt('records aanwezig op') . ' ' . $_POST['datumTm'] . ' '. vt('Deze records zullen worden overschreven. Doorgaan?');
		$status=1;
	}
	else
	{
		$msg='All okay';
		$status=0;
	}

	echo json_encode(array('status'=>$status,'msg'=>$msg));
	exit;
}


$selectie=new selectOptie();

$editcontent['javascript'] .= $selectie->getSelectJava();
$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent[calendar] = $kal->get_load_files_code();

$html='<form name="selectForm">';
$html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
$html.=$selectie->getInternExternHTML($PHP_SELF);
$html .="<br>";
if(method_exists($selectie,'getConsolidatieHTML'))
  $html.=$selectie->getConsolidatieHTML($PHP_SELF);
$html.="</form>";

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");


$query  = "SELECT max(check_module_FACTUURHISTORIE) as check_module_FACTUURHISTORIE, 
max(FACTUURHISTORIE_gebruikLaatsteWaarde) as FACTUURHISTORIE_gebruikLaatsteWaarde,
AfdrukSortering,CrmPortefeuilleInformatie FROM (Vermogensbeheerders)
INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '".$_SESSION["USR"]."' GROUP BY Vermogensbeheerders.Vermogensbeheerder limit 1 ";
$DB = new DB();
$DB->SQL($query);
$row = $DB->lookupRecord();
$afdrukSortering=$row;

if($row['check_module_FACTUURHISTORIE'] == 1)
{
  $extraAfdrukKnop='<input type="button" onclick="javascript:concept();" 			value=" Concept " style="width:100px"><br>';
  $extraInvoerVeld='<div class="formblock">
<div class="formlinks"> [vt]Factuur omschrijving[/vt] </div>
<div class="formrechts">
<input type="text" name="omschrijving" size="30">
</div>
</div>';

$kal = new DHTML_Calendar();
$inp = array ('name' =>"facturatiemaand",'value' =>date("d-m-Y"),'size'  => "11");


$facturatieMaand='<div class="formblock">
<div class="formlinks"> [vt]Facturatiemaand[/vt] </div>
<div class="formrechts">
'.$kal->make_input_field("",$inp,"").'
</div>
</div>';

  $_POST['facturatiemaand'] = jul2sql(form2jul($_POST['facturatiemaand']));
  $factuurNummerInput='';
}
else
{
  $extraAfdrukKnop='';
  $extraInvoerVeld='';
  $facturatiemaand='';
  $factuurNummerInput='<div class="formblock">
<div class="formlinks"> ' . vt('Factuurnummer') . ' </div>
<div class="formrechts">
<input type="text" name="factuurnummer" size="4">
</div>
</div>';
}
echo template($__appvar["templateContentHeader"],$editcontent);
$totdatum = getLaatsteValutadatum();

if($_POST['posted'])
{
  $_POST['inclFactuur']=1;
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/factuur/PDFFactuur.php");
	include_once("rapport/factuur/Factuur.php");
	include_once('../classes/excel/Writer.php');

	$factuurnummer = $_POST['factuurnummer'];
	$periodeSeconden = form2jul($_POST['datum_tot']) - form2jul($_POST['datum_van']);
  //$_POST['periode']='factuur';
  $_POST['rapport_type']=array('factuur');
	$afdruk = new backofficeAfdrukken($_POST);
  $selectData=$afdruk->validate();
  $afdruk->getPortefeuilles();
	$afdruk->startdatum = jul2sql(form2jul($_POST['datumVan']));
	$afdruk->einddatum = jul2sql(form2jul($_POST['datumTm']));

  $afdruk->initPrb(count($afdruk->portefeuilles));
  $afdruk->initPdf();
	$exportStamp = mktime();

	switch($_POST['filetype'])
	{
	 case "PDF" :
   case "cvs" :
   case "xls" :
   case "beleggersgiro" :
   case "twinfield" :
   case "exact" :
   case "exactOnline" :
   case "snelstart" :
   case "sql" :
   case "database" :
   case "beheerfeeHistorie" :
	foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
 	{
 	  $afdruk->portefeuilles[$portefeuille]=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);
   	$afdruk->pro_step += $afdruk->pro_multiplier;
  	$afdruk->prb->moveStep($afdruk->pro_step);
    flush();
    $afdruk->setVolgorde($portefeuille);
    $afdruk->getCrmRapport($portefeuille);
    $afdruk->loadPdfSettings($portefeuille);
    $afdruk->addReports($portefeuille);
    $afdruk->verwijderTijdelijkeRapportage($portefeuille);
    $afdruk->filename = $USR.$exportStamp."FACTUUR.pdf";
    unset($afdruk->portefeuilles[$portefeuille]);
  }
  $afdruk->prb->hide();
    break;
    default:
    
    break;
  }
  
  $factuurbase = 'factuur_'.date('Y\mmd\tHis');
	switch($_POST['filetype'])
	{
		case "PDF" :
			$filename = $USR.mktime()."FAC.pdf";
			$filetype = "pdf";
			$afdruk->pdf->Output($__appvar['tempdir'].$filename,"F");
			$afdruk->pdf->OutputXls($__appvar['tempdir'].$factuurbase.'.xls');
			echo "<a href='showTempfile.php?show=1&filename=".$factuurbase.".xls&unlink=1' >Download XLS file.</a>";
		break;
		case "cvs" :
			$filename = $USR.mktime()."FAC.csv";
			$filetype = "csv";
			if ($_POST['nullenOnderdrukken'])
			  $afdruk->pdf->nullenOnderdrukken = 1;
			$afdruk->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			$filename = $USR.mktime()."FAC.xls";
			$filetype = "xls";
		  $afdruk->pdf->OutputXls($__appvar['tempdir'].$filename,"F");
		break;
    case "beleggersgiro" :
      $filename = $USR.mktime()."facBelGir.xls";
      $filetype = "xls";
      $afdruk->pdf->OutputBeleggersgiro($__appvar['tempdir'].$filename,"F");
      break;
		case "twinfield" :
			$filename = $USR.mktime()."_twinfield.xls";
			$filetype = "csv";
		  $afdruk->pdf->OutputTwinfield($__appvar['tempdir'].$filename,"F");
		break;
		case "exact" :
			$filename = $USR.mktime()."_exactGlobe.csv";
			$filetype = "xls";
		  $afdruk->pdf->OutputExact($__appvar['tempdir'].$filename,"F");
		break;
		case "exactOnline" :
			$filename = $USR.mktime()."_exactOnline.xls";
			$filetype = "xls";
		  $afdruk->pdf->OutputExactOnline($__appvar['tempdir'].$filename,"F");
		break;
    case "snelstart" :
      $filename = $USR.mktime()."_snelstart.xls";
      $filetype = "csv";
      $afdruk->pdf->OutputSnelstart($__appvar['tempdir'].$filename,"F");
      break;
	  case "sql" :
	    $conversie=array();
	    $facturatieDatum = $_POST['facturatiemaand'];
		  foreach ($afdruk->pdf->concept as $portefeuille=>$factuurData)
		  {    
          if($row['FACTUURHISTORIE_gebruikLaatsteWaarde'] > 0)
          {
		        $query="SELECT fee,btw,totaalIncl FROM FactuurHistorie WHERE portefeuille = '".$portefeuille."' ORDER BY periodeDatum desc limit 1";
            if($DB->QRecords($query))
            {
              $laatsteFactuur=$DB->nextRecord();
              $factuurData['beheerfeeBetalen']=$laatsteFactuur['fee'];
              $factuurData['btw']=$laatsteFactuur['btw'];
              $factuurData['beheerfeeBetalenIncl']=$laatsteFactuur['totaalIncl'];
            }
          }

          $query="SELECT id FROM FactuurHistorie WHERE portefeuille = '".$portefeuille."' AND periodeDatum = '".$facturatieDatum."'";
          if($DB->QRecords($query))
          {
	          $idData=$DB->nextRecord();
	          $query="UPDATE FactuurHistorie SET ";
	          $where ="WHERE id = '".$idData['id']."' AND status <> '1'";
          }
          else
          {
            $query= "INSERT INTO FactuurHistorie SET ";
            $query.="add_date = NOW(),add_user = '$USR',";
            $where='';
          }
				  $query.="portefeuille = '".$portefeuille."'";
          $query.=",factuurNr = ''"; //".$factuurData['factuurNummer']."
          $query.=",periodeDatum = '".$facturatieDatum."'";
          $query.=",grondslag = '".$factuurData['rekenvermogen']."'";
          $query.=",fee = '".$factuurData['beheerfeeBetalen']."'";
          $query.=",btw = '".$factuurData['btw']."'";
          $query.=",totaalIncl = '".$factuurData['beheerfeeBetalenIncl']."'";
          $query.=",omschrijving = '".$_POST['omschrijving']."'";
          $query.=",factuurDatum = '$facturatieDatum'";
          $query.=",status = '0'";
          $query.=",change_user = '$USR',change_date = NOW()";
          $query.=$where;
          $DB->SQL($query);
          $DB->Query();

		  }
		  exit();
		break;
		case "beheerfeeHistorie" :
	    $conversie=array();
	    $facturatieDatum = $_POST['facturatiemaand'];
		  foreach ($afdruk->pdf->concept as $portefeuille=>$factuurData)
			{
				$query="SELECT id FROM FactuurBeheerfeeHistorie WHERE portefeuille = '".$portefeuille."' AND periodeDatum = '".$afdruk->einddatum."'";
				if($DB->QRecords($query))
				{
					$idData=$DB->nextRecord();
					$query="UPDATE FactuurBeheerfeeHistorie SET ";
					$where ="WHERE id = '".$idData['id']."' ";
				}
				else
				{
					$query= "INSERT INTO FactuurBeheerfeeHistorie SET ";
					$query.="add_date = NOW(),add_user = '$USR',";
					$where='';
				}
				//listarray($factuurData);
				$query.="portefeuille = '".$portefeuille."'";
				$query.=",factuurNr = '".$factuurData['factuurNummer']."'";
				$query.=",periodeDatum = '".$afdruk->einddatum."'";
				$query.=",grondslag = '".$factuurData['rekenvermogen']."'";
				$query.=",beheerfee = '".$factuurData['beheerfeeBetalen']."'";
				$query.=",btw = '".$factuurData['btw']."'";
				$query.=",bedragBuitenBtw = '".$factuurData['BeheerfeeBedragBuitenBTW']."'";
				$query.=",bedragVerrekendeHuisfondsen = '".round($factuurData['huisfondsFeeJaar']*$factuurData['periodeDeelVanJaar'],2)."'";
				$query.=",bedragTotaal = '".$factuurData['beheerfeeBetalenIncl']."'";
				$query.=",change_user = '$USR',change_date = NOW()";
				$query.=$where;
				$DB->SQL($query);
				//echo $query;exit;
				$DB->Query();

			}
		  exit();
		break;
		case "database" :
			$filetype = "database";
			$afdruk->pdf->dbWaarden=$afdruk->factuurWaarden;
			$afdruk->pdf->dbTable="CREATE TABLE `reportbuilder_$USR` (
`id` INT NOT NULL AUTO_INCREMENT ,
`Rapport` VARCHAR( 20 ) NOT NULL ,
`client` VARCHAR( 50 ) NOT NULL ,
`clientNaam` VARCHAR( 150 ) NOT NULL ,
`clientNaam1` VARCHAR( 150 ) NOT NULL ,
`clientAdres` VARCHAR( 150 ) NOT NULL ,
`clientPostcode` VARCHAR( 150 ) NOT NULL ,
`clientWoonplaats` VARCHAR( 150 ) NOT NULL ,
`clientTelefoon` VARCHAR( 150 ) NOT NULL ,
`clientFax` VARCHAR( 150 ) NOT NULL ,
`clientEmail` VARCHAR( 150 ) NOT NULL ,
`datumTot` date NOT NULL ,
`datumVan` date NOT NULL ,
`factuurNummer` VARCHAR( 50 ) NOT NULL ,
`Portefeuille` VARCHAR( 24 ) NOT NULL ,
`RapportageValuta` VARCHAR( 3 ) NOT NULL ,
`totaalWaardeVanaf` DOUBLE NOT NULL ,
`totaalWaarde` DOUBLE NOT NULL ,
`gemiddeldeVermogen` DOUBLE NOT NULL ,
`huisfondsWaarde` DOUBLE NOT NULL ,
`maandsWaarde_1` DOUBLE NOT NULL ,
`maandsWaarde_2` DOUBLE NOT NULL ,
`maandsWaarde_3` DOUBLE NOT NULL ,
`maandsWaarde_4` DOUBLE NOT NULL ,
`maandsGemiddelde` DOUBLE NOT NULL ,
`beheerfeeOpJaarbasis` DOUBLE NOT NULL ,
`performancefee` DOUBLE NOT NULL ,
`administratieBedrag` DOUBLE NOT NULL ,
`BeheerfeeTeruggaveHuisfondsenPercentage` DOUBLE NOT NULL ,
`BeheerfeeRemisiervergoedingsPercentage` DOUBLE NOT NULL ,
`totaalTransactie` DOUBLE NOT NULL ,
`beheerfeeBetalen` DOUBLE NOT NULL ,
`btw` DOUBLE NOT NULL ,
`beheerfeeBetalenIncl` DOUBLE NOT NULL ,
`stortingenOntrekkingen` DOUBLE NOT NULL ,
`resultaat` DOUBLE NOT NULL ,
`performancePeriode` DOUBLE NOT NULL ,
`performanceJaar` DOUBLE NOT NULL ,
`rekenvermogen` DOUBLE NOT NULL ,
`BeheerfeePercentageVermogenDeelVanJaar` DOUBLE NOT NULL ,
`nettoVermogenstoenameYtd` DOUBLE NOT NULL ,
`beginwaardeJaar` DOUBLE NOT NULL ,
`periodeDeelVanJaar` DOUBLE NOT NULL ,
`add_date` datetime ,
`debiteurnr` varchar(20),
PRIMARY KEY ( `id` ),
KEY `Portefeuille` (`Portefeuille`)) ";
			$afdruk->pdf->OutputDatabase();
			?>
	   <script type="text/javascript">
	   	parent.document.location = 'reportBuilder2.php';
	   </script>
     <?
     exit;
		break;
    case "export" :
    case "eMail" :
    case "eDossier" :
    case "portaal" :
////    


	  if($_POST['testrun'])
	    $initPdf=true;
    $exportFiles=array();
	  $afdruk->initPrb(count($afdruk->portefeuilles));
    foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
  	{
  	  verwijderTijdelijkeTabel($portefeuille);
  	  $afdruk->pro_step += $afdruk->pro_multiplier;
	  	$afdruk->prb->moveStep($afdruk->pro_step);
	  	flush();

	  	if($_POST['testrun'] == false)
	  	  $afdruk->initPdf();
	  	elseif($initPdf)
	  	{
	  	  $afdruk->initPdf();
	  	  $initPdf=false;
	  	}
      
      $afdruk->portefeuilles[$portefeuille]=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);
     	$afdruk->pro_step += $afdruk->pro_multiplier;
    	$afdruk->prb->moveStep($afdruk->pro_step);
      flush();
      $afdruk->setVolgorde($portefeuille);
      $afdruk->getCrmRapport($portefeuille);
      

      if(count($afdruk->rapport_type) > 0)
      {
        $afdruk->loadPdfSettings($portefeuille);
        $afdruk->addReports($portefeuille);
        if($afdruk->factuurAangemaakt==false)
        {
          logScherm("Geen factuur voor $portefeuille aangemaakt?");
          continue;
        }
        if($_POST['testrun'] == false)
        {

          $afdruk->filename=$afdruk->getFilename($portefeuille);
          $afdruk->filePath=$afdruk->getFilePath($portefeuille);
          $afdruk->pdf->Output($afdruk->filePath.$afdruk->filename,"F");
          if($_POST['filetype']=='eMail')
              $afdruk->sendByEmail($portefeuille,$afdruk->filePath.$afdruk->filename);
          if($_POST['filetype']=='eDossier')
              $afdruk->sendToDossier($portefeuille,$afdruk->filePath.$afdruk->filename);
          if($_POST['filetype']=='portaal')
              $afdruk->sendToPortaal($portefeuille,$afdruk->filePath.$afdruk->filename);
          if($selectData['filetype']=='export')
             $exportFiles[]=$afdruk->filePath.$afdruk->filename;          
          if(is_file($afdruk->filePath.$afdruk->filename))
            logScherm("PDF voor $portefeuille aangemaakt");
        }
        if($afdruk->portefeuilleSelectie->selectData['CRM_extraAdres'])
        {
          $afdruk->getExtraAdres($portefeuille);
          foreach ($afdruk->extraAdres as $index=>$extraAdres)
          {
            if($_POST['testrun'] == false)
              $afdruk->initPdf();
            $afdruk->loadPdfSettings($portefeuille);
            $afdruk->addReports($portefeuille,$extraAdres);
            if($_POST['testrun'] == false)
            {
              $afdruk->filename="$index".$afdruk->getFilename($portefeuille);
              $afdruk->filePath=$afdruk->getFilePath($portefeuille);
              $afdruk->pdf->Output($afdruk->filePath.$afdruk->filename,"F");
              if($_POST['filetype']=='eMail')
                $afdruk->sendByEmail($portefeuille,$afdruk->filePath.$afdruk->filename,$extraAdres);
              if($_POST['filetype']=='eDossier')
                $afdruk->sendToDossier($portefeuille,$afdruk->filePath.$afdruk->filename,$extraAdres);
              if($selectData['filetype']=='export')
                 $exportFiles[]=$afdruk->filePath.$afdruk->filename;                  
            }
          }
        }
        $afdruk->verwijderTijdelijkeRapportage($portefeuille);
      }
      else
        logScherm("Voor $portefeuille geen rapportage aanmaken.");
      unset($afdruk->portefeuilles[$portefeuille]);
  	}
  	$afdruk->prb->hide();

  	if($selectData['testrun'] == true)
  	{
  	  $afdruk->filename = $USR.$exportStamp."BACKOF.pdf";
      $afdruk->pdf->Output($__appvar['tempdir'].$afdruk->filename,"F");
      $afdruk->pushPdf();
  	}
    elseif ($selectData['filetype']=='eMail')
    	echo "<a href=\"javascript:parent.location.href='emailqueueList.php';\"><b>Naar email wachtrij.</b></a>\n";
    elseif($selectData['filetype']=='portaal')
      echo "<a href=\"javascript:parent.location.href='portaalqueueList.php';\"><b>Naar portaal wachtrij.</b></a>\n";
      
    if($selectData['filetype']=='export' ) //&& php_uname('n')=='appie.airs.nl'
    { 
      include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
      $zipfile=$__appvar['tempdir']."export.zip";
      $zip=new PclZip($zipfile);
      $zip->create($exportFiles,PCLZIP_OPT_REMOVE_ALL_PATH);
      echo "<br>\n<a href='showTempfile.php?show=1&filename=export.zip&unlink=1' ><b>Download export.</b></a>";
      foreach($exportFiles as $file)
        unlink($file);
    }      
      
    exit;
    
////    
	}
  
 // getCsvHeader
 // $afdruk->portefeuilles[$portefeuille]'layout']      
    $afdruk->pdf->excelDataBackup=$afdruk->pdf->excelData;
    $afdruk->pdf->excelData=$afdruk->pdf->excelData2;  
    $afdruk->pdf->OutputXls($__appvar[tempdir].'geen'.$factuurbase.'.xls');
   echo "<br>\n<a href='showTempfile.php?show=1&filename=geen".$factuurbase.".xls&unlink=1' >Download geen factuur XLS file.</a>";
  
      
	?>
<script type="text/javascript">
function pushpdf(file,save)
{
	var width='800';
	var height='600';
	var target = '_blank';
	var location = 'pushFile.php?filetype=<?=$filetype?>&file=' + file;
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
pushpdf('<?=$filename?>',<?=$save?>);
</script>
<?
	exit();
}
else
{
	session_start();
	$_SESSION['NAV'] = "";
  $_SESSION['factuurNummers']=array();
	session_write_close();
?>
<script type="text/javascript">

function setIntern()
{
  <?=$selectie->getPortefeuilleInternJava()?>
  <?
  if(method_exists($selectie,'getConsolidatieJava'))
    echo $selectie->getConsolidatieJava()
  ?>
}

function print()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="PDF";
		document.selectForm.save.value="0";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}


function saveasfile()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="PDF";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}

function csv()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="cvs";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}

function beleggersgiro()
{
  if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
  {
    alert("Ongeldig factuurnummer!");
  }
  else
  {
    setIntern();
    document.selectForm.target = "generateFrame";
    document.selectForm.filetype.value="beleggersgiro";
    document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
    document.selectForm.submit();
  }
}

function xls()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="xls";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}

function twinfield()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="twinfield";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode']=='portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}

function snelstart()
{
  if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
  {
    alert("Ongeldig factuurnummer!");
  }
  else
  {
    setIntern();
    document.selectForm.target = "generateFrame";
    document.selectForm.filetype.value="snelstart";
    document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
    document.selectForm.submit();
  }
}

function exact()
{
  if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
  {
    alert("Ongeldig factuurnummer!");
  }
  else
  {
    setIntern();
    document.selectForm.target = "generateFrame";
    document.selectForm.filetype.value="exact";
    document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
    document.selectForm.submit();
  }
}

function exactOnline()
{
  if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
  {
    alert("Ongeldig factuurnummer!");
  }
  else
  {
    setIntern();
    document.selectForm.target = "generateFrame";
    document.selectForm.filetype.value="exactOnline";
    document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
    document.selectForm.submit();
  }
}

function database()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
	  setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="database";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
	}
}


function beheerfeeHistorie()
{
	if(document.selectForm.factuurnummer && document.selectForm.factuurnummer.value == "")
	{
		alert("Ongeldig factuurnummer!");
	}
	else
	{
		setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="beheerfeeHistorie";
		document.selectForm.save.value="1";
		<?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>


	//	var formdata = new FormData( document.selectForm );
  //console.log( $("#selectForm").serialize());
		$.ajax({
			type: "POST",
			url: "factuurSelectie.php?lookup=1",
			dataType: "json",
			async: false,
			data: $("#selectForm").serialize(),
			success: function(data, textStatus, jqXHR)
			{
				console.log(data);
				if(data.status==0)
				{
					document.selectForm.submit();
				}
				else if(data.status==1)
				{
					AEConfirm(data.msg, 'Records aanwezig', function () { document.selectForm.submit();}       );
				}
				else if(data.status==2)
				{
					AEMessage(data.msg, 'Records bijwerken', function ()
					{
					});
				}

			},
			error: function(jqXHR, textStatus, errorThrown)
			{
			}
		});

		//
	}
}

function concept()
{
    setIntern();
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="sql";
		document.selectForm.save.value="1";
    <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
		document.selectForm.submit();
}

function exportData()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value="export";
	document.selectForm.save.value="0";
  <?php if($_SESSION['selectieMethode'] == 'portefeuille') echo "selectSelected();\n";?>
	document.selectForm.submit();
}
</script>

<table border="0">
<tr>
<td width="540">

<form action="factuurSelectie.php" method="POST" target="_blank" name="selectForm" id="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
<input type="hidden" name="type" value="factuur" />

<table width="600" border="0">
<tr>
<td width="540" valign="top">
<fieldset id="Selectie" >
<legend accesskey="S"><?= vt('Selectie'); ?></legend>

<?
echo '
<div class="formblock">
<div class="formlinks"> ' . vt('Periode') . ' </div>
<div class="formrechts">
<input type="radio" name="periode" value="Clienten" CHECKED >' . vt('Alle clienten') . ' <br><br>
<input type="radio" name="periode" value="Maandrapportage">' . vt('Maandrapportage') . ' <br>
<input type="radio" name="periode" value="Kwartaalrapportage">' . vt('Kwartaalrapportage') . ' <br>
<input type="radio" name="periode" value="Halfjaarrapportage">' . vt('Halfjaarrapportage') . ' <br>
<input type="radio" name="periode" value="Jaarrapportage">' . vt('Jaarrapportage') . ' <br>
</div>
</div>';
echo $selectie->createDatumSelectie($_SESSION['backofficeSelectie']);
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<script language="Javascript">

</script>
<table cellspacing="0" border=1 >

<?
  $DB = new DB();
  if(checkAccess($type))
  {
  	$join = "";
  	$beperktToegankelijk = '';
  }
  else
  {
  	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
  	         JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  	$beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }
	$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client ";

  $DB->SQL($query);
  $DB->Query();
  while($gb = $DB->NextRecord())
    $data[$gb['Portefeuille']]=$gb;
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($data,$_SESSION['backofficeSelectie']);
  echo "<br><br>";
}
else
{
  $DB = new DB();
  $maxVink=25;
  $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
      echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    else
      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
  }
  $opties=array('Risicoklasse'=>'Risicoklasse','ModelPortefeuille'=>'ModelPortefeuille','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters','selectieveld1'=>'Selectieveld1','selectieveld2'=>'Selectieveld2');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if(count($data) > 1)
    {
      if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
        echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
      else
        echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    }
  }
}
?>
</fieldset>
<fieldset id="Selectie" >
<legend accesskey="O"><?= vt('Opties'); ?></legend>

<?=$factuurNummerInput?>

<div class="formblock">
<div class="formlinks"><?= vt('Sortering op naam'); ?> </div>
<div class="formrechts">
<input type="checkbox" name="orderNaam" value="1">
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('Lege kolommen verwijderen'); ?> </div>
<div class="formrechts">
<input type="checkbox" name="nullenOnderdrukken" value="1">
</div>
</div>

<div class="formblock">
<div class="formlinks"> <?= vt('Algemeen drempel percentage'); ?> </div>
<div class="formrechts">
<input type="text" name="drempelPercentage" size="4">
</div>
</div>

<?=$extraInvoerVeld.$facturatieMaand?>

</fieldset>

</td>
<td valign="top">
	<input type="button" onclick="javascript:print();" value=" Afdrukken " style="width:100px"><br><br>
	<input type="button" onclick="javascript:saveasfile();" value=" Opslaan " style="width:100px"><br><br>
	<input type="button" onclick="javascript:csv();" 					value=" CSV-export " style="width:100px"><br><br>
	<input type="button" onclick="javascript:xls();" 					value=" XLS-export " style="width:100px"><br><br>
  <input type="button" onclick="javascript:exportData();" 					value=" Export " style="width:100px"><br><br>
 	<input type="button" onclick="javascript:database();" 					value=" Reportbuilder " style="width:100px"><br><br>
	<?
	if($__appvar["bedrijf"] =='HOME'||$__appvar["bedrijf"] =='ANO')// || $__appvar["bedrijf"] =='TEST' || $__debug==true
	  echo '<input type="button" onclick="javascript:beheerfeeHistorie();" 					value=" Beheerfee wegschrijven " style="width:200px"><br><br>';

    if ($__twinfield["grootboek_debiteur"] <> "")
    {
?>
 	<input type="button" onclick="javascript:twinfield();" 					value=" Twinfield " style="width:100px"><br><br>
<?
    }
?>
<?
  if ($__exact["dagboek"] <> "")
  {
?>
  <input type="button" onclick="javascript:exact();" 					value=" ExactGlobe " style="width:100px"><br><br>
<?
  }
 if ($__exactOnline["dagboek"] <> "")
  {
?>
  <input type="button" onclick="javascript:exactOnline();" 					value=" ExactOnline " style="width:100px"><br><br>
<?
  }
// call 6555
if ($__snelstart["dagboek"] <> "")
{
  ?>
  <input type="button" onclick="javascript:snelstart();" 					value=" Snelstart " style="width:100px"><br><br>
  <?
}

//call 9413
if ($__beleggersgiro["grootboekrekening"] <> "")
{
  ?>
  <input type="button" onclick="javascript:beleggersgiro();" 					value=" Bel.giro " style="width:100px"><br><br>
  <?
}
?>
  <?=$extraAfdrukKnop?>

  	<input type="hidden" value="0" name="CRM_rapport_vink">
	<input type="checkbox" value="1" id="CRM_rapport_vink" name="CRM_rapport_vink"
	<?if($_SESSION['backofficeSelectie']['CRM_rapport_vink']==1 ||  (!isset($_SESSION['backofficeSelectie']['CRM_rapport_vink']) && $rdata['check_module_CRM'] && $rdata['CrmPortefeuilleInformatie']))
	  echo "checked";?>> <?= vt('CRM rapportage instellingen'); ?> <br>

	  <input type="radio" name="media" value="email"> <?= vt('email selectie'); ?> <br>
	  <input type="radio" name="media" value="pdf"> <?= vt('pdf selectie'); ?> <br>
  <?php
  if($__appvar["bedrijf"] =='HOME' || $__appvar["bedrijf"] =='TEST' || $__debug==true )
  {
    echo '<br><br><br>' . vt('Test opties') . ':<br><input type="hidden" value="0" name="testset">
  <input type="checkbox" value="1" id="testset" name="testset"> ' . vt('Test selectie') . ' <br>
  <input type="hidden" value="0" name="debug">
  <input type="checkbox" value="1" id="debug" name="debug"> ' . vt('Debug info') . '<br>';
  }
  ?>


</td>
</tr>
<tr>
	<td colspan="2">
<?echo progressFrame();?>
	</td>
</tr>
</table>
</form>
<?php
}
echo template($__appvar["templateRefreshFooter"],$content);
?>