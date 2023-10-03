<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/14 18:41:12 $
 		File Versie					: $Revision: 1.112 $

 		$Log: rapportBackofficeClientAfdrukken.php,v $
 		Revision 1.112  2020/03/14 18:41:12  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2019/11/16 17:36:02  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.109  2015/12/09 16:59:59  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2015/11/04 16:49:53  rvv
 		*** empty log message ***
 		
 		Revision 1.107  2015/10/18 13:45:01  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2015/09/20 17:30:13  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2015/07/01 15:28:52  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2015/05/16 09:31:31  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2014/12/13 19:12:11  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2014/12/03 17:28:25  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2014/10/11 16:21:09  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2014/06/04 13:09:12  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2014/05/07 15:37:41  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2014/03/26 18:25:04  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2014/01/15 14:56:07  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2013/11/30 14:21:56  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2013/11/13 15:50:39  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2013/10/16 15:33:35  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2013/10/05 15:56:28  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2013/07/06 15:59:55  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2013/05/19 11:00:37  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2013/01/13 13:33:33  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2012/08/01 16:56:08  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2012/07/14 13:19:04  rvv
 		*** empty log message ***

 		Revision 1.87  2012/06/06 18:17:15  rvv
 		*** empty log message ***

 		Revision 1.86  2012/06/06 18:08:05  rvv
 		*** empty log message ***

 		Revision 1.85  2012/02/26 15:16:38  rvv
 		*** empty log message ***

 		Revision 1.84  2012/01/11 12:26:13  rvv
 		*** empty log message ***

 		Revision 1.83  2011/10/23 13:32:25  rvv
 		*** empty log message ***

 		Revision 1.82  2011/08/11 15:37:19  rvv
 		*** empty log message ***

 		Revision 1.81  2011/07/30 16:37:29  rvv
 		*** empty log message ***

 		Revision 1.80  2011/07/10 14:18:28  rvv
 		*** empty log message ***

 		Revision 1.79  2011/06/29 18:03:14  rvv
 		*** empty log message ***

 		Revision 1.78  2011/06/29 16:55:33  rvv
 		*** empty log message ***

 		Revision 1.77  2011/05/14 10:50:00  rvv
 		*** empty log message ***

 		Revision 1.76  2011/05/08 09:34:53  rvv
 		*** empty log message ***

 		Revision 1.75  2011/04/13 14:16:57  rvv
 		*** empty log message ***

 		Revision 1.74  2010/12/05 09:52:09  rvv
 		*** empty log message ***

 		Revision 1.73  2010/11/17 17:15:58  rvv
 		*** empty log message ***

 		Revision 1.72  2010/11/14 10:49:33  rvv
 		*** empty log message ***

 		Revision 1.71  2010/07/11 17:05:25  rvv
 		*** empty log message ***

*/
//$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/backofficeAfdrukkenClass.php");
include_once("../classes/portefeuilleVerdieptClass.php");
include_once("../classes/templateEmail.php");
include_once('../classes/AE_cls_phpmailer.php');
include_once("../classes/pdfMailing.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFRapport.php");
include_once("rapport/RapportFront.php");
include_once("rapport/RapportMODEL.php");
include_once("rapport/factuur/PDFFactuur.php");
include_once("rapport/factuur/Factuur.php");
include_once('../classes/excel/Writer.php');

session_start();
$_SESSION['submenu'] = New Submenu();
$_SESSION['NAV'] = "";
session_write_close();


session_start();
if(!is_array($_SESSION['backofficeSelectie']))
  $_SESSION['backofficeSelectie']=array();

//foreach(array('vvgl','perc','opbr','kost','GB_STORT_ONTTR','GB_overige','TRANS_RESULT','PERFG_totaal','PERFG_perc','perfBm','perfPstart') as $var)
//  if(isset($_SESSION['backofficeSelectie'][$var]))
//    unset($_SESSION['backofficeSelectie'][$var]);
//foreach($_SESSION['backofficeSelectie'] as $var=>$data)
//  if(substr($var,0,4)=='MUT_')
//    unset($_SESSION['backofficeSelectie'][$var]);
      
  
$_SESSION['backofficeSelectie']=array_merge($_SESSION['backofficeSelectie'],$_POST,$_GET);
$_SESSION['backofficeSelectie']['portefeuilleIntern']=$_SESSION['portefeuilleIntern'];

echo template($__appvar["templateContentHeader"],$content);

if($_POST['posted'])
{
  if($__debug == true && function_exists('xhprof_enable'))
    xhprof_enable();
  
  $afdruk = new backofficeAfdrukken($_SESSION['backofficeSelectie']);
  $selectData=$afdruk->validate();
  $afdruk->getPortefeuilles();
	$afdruk->startdatum = jul2sql($selectData['datumVan']);
	$afdruk->einddatum = jul2sql($selectData['datumTm']);
	if($selectData['type']=='pdf' || $selectData['type']=='xls' || $selectData['type']=='alleenFactuur' || $selectData['type']=='xlsRapport')
	{
    $afdruk->initPrb(count($afdruk->portefeuilles));
    $afdruk->initPdf();
  	$exportStamp = mktime();
  	foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
  	{
  	  $afdruk->pdf->templateVars = array(); // 4187
  	  $afdruk->portefeuilles[$portefeuille]=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);
  	  verwijderTijdelijkeTabel($portefeuille);
  	  $afdruk->pro_step += $afdruk->pro_multiplier;
	  	$afdruk->prb->moveStep($afdruk->pro_step);
	  	logScherm("Portefeuille: $portefeuille");

	    $afdruk->setVolgorde($portefeuille);
      $afdruk->getCrmRapport($portefeuille);
      $afdruk->loadPdfSettings($portefeuille);
      $afdruk->addReports($portefeuille);
      if($afdruk->portefeuilleSelectie->selectData['CRM_extraAdres'])
      {
        $afdruk->getExtraAdres($portefeuille);
        foreach ($afdruk->extraAdres as $extraAdres)
          $afdruk->addReports($portefeuille,$extraAdres);
      }
      $afdruk->verwijderTijdelijkeRapportage($portefeuille);
      unset($afdruk->portefeuilles[$portefeuille]);
    }
    
     if($afdruk->selectie['type']=='alleenFactuur')
     {
        if($afdruk->selectie['factuurType']=='xls')
          $selectData['type']='xls';
     }
     
     
    if($selectData['type']=='xlsRapport')
    {
      $file=$afdruk->createXlsZip();
    }    
    elseif($selectData['type']=='xls')
    {
      $afdruk->filename = $USR.$exportStamp."BACKOF.xls";
      $afdruk->pdf->OutputXLS($__appvar['tempdir'].$afdruk->filename,'F');
     }
    else
    {
      $afdruk->filename = $USR.$exportStamp."BACKOF.pdf";
      $afdruk->pdf->Output($__appvar['tempdir'].$afdruk->filename,"F");
    }
    
    
    $afdruk->prb->hide();
    if($selectData['type']=='xlsRapport')
    {
      $afdruk->filename='export.zip';
      $afdruk->selectie['save']=1;
    }

    $afdruk->pushPdf();
	}
	elseif($selectData['type']=='export' || $selectData['type']=='eMail' || $selectData['type']=='eDossier' || $selectData['type']=='portaal' )
	{
	  if($selectData['testrun'])
	    $initPdf=true;

    $exportFiles=array();
	  $afdruk->initPrb(count($afdruk->portefeuilles));
    foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
  	{
  	  $afdruk->portefeuilles[$portefeuille]=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);
  	  verwijderTijdelijkeTabel($portefeuille);
  	  $afdruk->pro_step += $afdruk->pro_multiplier;
	  	$afdruk->prb->moveStep($afdruk->pro_step);
	  	flush();

	  	if($selectData['testrun'] == false)
      {
	  	  $afdruk->initPdf();
      }
	  	elseif($initPdf)
	  	{
	  	  $afdruk->initPdf();
	  	  $initPdf=false;
	  	}

      $afdruk->setVolgorde($portefeuille);
      $afdruk->getCrmRapport($portefeuille);
      if(count($afdruk->rapport_type) > 0)
      {
        $afdruk->loadPdfSettings($portefeuille);
        $afdruk->addReports($portefeuille);
        if($selectData['testrun'] == false)
        {
          $afdruk->filename=$afdruk->getFilename($portefeuille); //echo "<br>\n|".$afdruk->filename."|<br>\n";exit;
          $afdruk->filePath=$afdruk->getFilePath($portefeuille);
          $afdruk->pdf->Output($afdruk->filePath.$afdruk->filename,"F");
          logScherm("PDF klaarmaken voor ".$selectData['type']);
          if($selectData['type']=='export')
          {
            $file=$afdruk->pdfBriefAanmaken($portefeuille,$afdruk->filePath);
            if($file <> '')
              $exportFiles[]=$file;
          }
          if($selectData['type']=='eMail' && $afdruk->pdf->stopOutput==false)
          {
            if($selectData['losseFactuur']==true)
              $afdruk->sendByEmailLosseFactuur($portefeuille);
            else
              $afdruk->sendByEmail($portefeuille,$afdruk->filePath.$afdruk->filename);
          }
          if($selectData['type']=='eDossier')
              $afdruk->sendToDossier($portefeuille,$afdruk->filePath.$afdruk->filename);
          if($selectData['type']=='portaal')
              $afdruk->sendToPortaal($portefeuille,$afdruk->filePath.$afdruk->filename);
          if(is_file($afdruk->filePath.$afdruk->filename))
            logScherm("PDF voor $portefeuille aangemaakt");
          if($selectData['type']=='export')
             $exportFiles[]=$afdruk->filePath.$afdruk->filename;   
          //logScherm("Gebruikt geheugen na pdf verzenden: ".round(memory_get_usage()/1024/1024,3)." MB.");
        }
        if($afdruk->portefeuilleSelectie->selectData['CRM_extraAdres'])
        {
          $afdruk->getExtraAdres($portefeuille);
          foreach ($afdruk->extraAdres as $index=>$extraAdres)
          {
            if($selectData['testrun'] == false)
            {
              $afdruk->initPdf();
              $afdruk->getCrmRapport($portefeuille);
            }
            $afdruk->loadPdfSettings($portefeuille);
            $afdruk->addReports($portefeuille,$extraAdres);
            if($selectData['testrun'] == false)
            {
              $afdruk->filename="$index".$afdruk->getFilename($portefeuille);
              $afdruk->filePath=$afdruk->getFilePath($portefeuille);
              $afdruk->pdf->Output($afdruk->filePath.$afdruk->filename,"F");
              if($selectData['type']=='export')
                $exportFiles[]=$afdruk->filePath.$afdruk->filename;
              if($selectData['type']=='eMail')
              {
                if($selectData['losseFactuur']==true)
                  $afdruk->sendByEmailLosseFactuur($portefeuille,$extraAdres);
                else
                  $afdruk->sendByEmail($portefeuille,$afdruk->filePath.$afdruk->filename,$extraAdres);
              }
              //if($selectData['type']=='eDossier')
              //  $afdruk->sendToDossier($portefeuille,$afdruk->filePath.$afdruk->filename,$extraAdres);
            }
          }
        }
        $afdruk->verwijderTijdelijkeRapportage($portefeuille);
      }
      else
        logScherm("Voor $portefeuille geen rapportage aanmaken.");
      unset($afdruk->portefeuilles[$portefeuille]);
      //logScherm("Gebruikt geheugen na vrijgeven portefeuille gegevens : ".round(memory_get_usage()/1024/1024,3)." MB.");
  	}
  	$afdruk->prb->hide();
   


  	if($selectData['testrun'] == true)
  	{
  	  $afdruk->filename = $USR.$exportStamp."BACKOF.pdf";
      $afdruk->pdf->Output($__appvar['tempdir'].$afdruk->filename,"F");
      $afdruk->pushPdf();
  	}
    elseif ($selectData['type']=='eMail')
    	echo "<a href=\"javascript:parent.location.href='emailqueueList.php';\"><b>Naar email wachtrij.</b></a>\n";
    elseif($selectData['type']=='portaal')
      echo "<a href=\"javascript:parent.location.href='portaalqueueList.php';\"><b>Naar portaal wachtrij.</b></a>\n";

    if($selectData['type']=='export' && substr(php_uname('n'),-8)=='.airs.nl' || 1)
    {       
      include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
      $zipfile=$__appvar['tempdir']."export.zip";
      $zip=new PclZip($zipfile);
      $zip->create($exportFiles,PCLZIP_OPT_REMOVE_ALL_PATH);
      echo "<br>\n<a href='showTempfile.php?show=1&filename=export.zip&unlink=1' ><b>Download export.</b></a>";
      foreach($exportFiles as $file)
        unlink($file);
    }
	}


  if(count($afdruk->pdf->excelData2)> 1)
  {
    $afdruk->pdf->excelDataBackup=$afdruk->pdf->excelData;
    $afdruk->pdf->excelData=$afdruk->pdf->excelData2;  
    $afdruk->pdf->OutputXls($__appvar['tempdir'].'geenFactuur.xls');
    $afdruk->pdf->excelData=$afdruk->pdf->excelDataBackup;
    echo "<br>\n<a href='showTempfile.php?show=1&filename=geenFactuur.xls&unlink=1' >Download geen factuur XLS file.</a>";
  }
  
  /*
  if(count($afdruk->pdf->excelData)> 1)
  {
  	$afdruk->pdf->OutputXls($__appvar['tempdir'].'factuur.xls');
		echo "<br>\n<a href='showTempfile.php?show=1&filename=factuur.xls&unlink=1' >Download XLS file.</a>";
  }
  */
  if($__debug == true && function_exists('xhprof_disable'))
  {
    $xhprof_data = xhprof_disable();
    file_put_contents('run_backoffice1_'.date('Ymd_his'), serialize($xhprof_data));
  }

  // push javascript de PDF te openen in een nieuw window en daarna het bestand verwijderen.
echo template($__appvar["templateContentFooter"],$content);
exit();
}
?>