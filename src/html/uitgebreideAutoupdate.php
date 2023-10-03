<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/10 15:20:46 $
 		File Versie					: $Revision: 1.18 $
 		
 		$Log: uitgebreideAutoupdate.php,v $
 		Revision 1.18  2020/06/10 15:20:46  rvv
 		*** empty log message ***

*/
include_once("wwwvars.php");


function checkLastRun($row)
{
  $vandaag=date('Y-m-d');
  $vandaagJul=db2jul($vandaag);
  $dagVanMaand=date('d',$vandaagJul);
  $huidigeMaand=date('m',$vandaagJul);
  $huidigeJaar=date('Y',$vandaagJul);

  $uitvoeren=array();
    $laatsteJul=db2jul($row['autoLaatste']);
    $vanafJul=db2jul($row['autoVanaf']);
    $dagenGeleden=round(($vandaagJul-$laatsteJul)/86400);
    if($laatsteJul < db2jul($vandaag))
    {
      if($row['frequentie']=='1')//dag
      {
        if($dagenGeleden>=1)
          $uitvoeren[]=$row['id'];
      }
      elseif($row['frequentie']=='2')//week
      {
        $huidigeWeek=date('W',$vandaagJul);
        $laatsteWeek=date('W',$laatsteJul);
        $gewensteDagVanWeek=date('w',$vanafJul);
        $dagVanWeek=date('w',$vandaagJul);
        // $restDagenInWeek=(6-$dagVanWeek);
        //echo " $gewensteDag $dagVanWeek $restDagenInWeek $laatsteWeek $huidigeWeek";
        if($dagVanWeek>=$gewensteDagVanWeek && ($laatsteWeek!=$huidigeWeek)) //(($dagenGeleden>=$restDagenInWeek && $laatsteWeek==$huidigeWeek) ||
          $uitvoeren[]=$row['id'];
      }
      elseif($row['frequentie']=='3')//maand
      {
        $gewensteDag=date('d',$vanafJul);
        $laatsteMaand=date('m',$laatsteJul);
        //$restDagenInMaand=round((mktime(0,0,0,$huidigeMaand+1,0,$huidigeJaar)-$vanafJul)/86400);
        if($dagVanMaand>=$gewensteDag && ($huidigeMaand != $laatsteMaand))//&& (($dagenGeleden>=$restDagenInMaand) ||
          $uitvoeren[]=$row['id'];
      }
      elseif($row['frequentie']=='4')//kwartaal
      {
        $huidigeKwartaal = ceil(date("n",$vandaagJul)/3);
        $laatsteKwartaal = ceil(date("n",$laatsteJul)/3);
        $gewensteDag=date('d',$vanafJul);
        if($dagVanMaand>=$gewensteDag && $huidigeKwartaal!=$laatsteKwartaal)
          $uitvoeren[]=$row['id'];
      }
      elseif($row['frequentie']=='5')//jaar
      {
        $gewensteDag=date('d',$vanafJul);
        $gewensteMaand=date('m',$vanafJul);
        $laatsteJaar=date('Y',$laatsteJul);
        //$restDagenInJaar=round((db2jul('31-12'.$huidigeJaar)-$vandaagJul)/86400);
        if(($dagVanMaand>=$gewensteDag && $huidigeMaand>=$gewensteMaand) && ($huidigeJaar != $laatsteJaar ))//($dagenGeleden>=$restDagenInJaar ||
          $uitvoeren[]=$row['id'];
      }
    }

  return count($uitvoeren);
}

function uitgebreideAutoUpdateRapportage($Bedrijf,$handmatigeId=0)
{
  global $__appvar,$USR;
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("../classes/portefeuilleSelectieClass.php");
  include_once("../classes/AE_cls_fpdf.php");
  include_once("rapport/Fondslijst.php");
  include_once("rapport/FondslijstKlein.php");
	include_once("rapport/CashLijst.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOverzicht.php");
  include_once("rapport/PDFRapport.php");
  include_once("rapport/RapportAfmExport.php");
  include_once("rapport/Modelcontrole.php");
  include_once("rapport/Zorgplichtcontrole.php");
  include_once("rapport/koersControle.php");
  include_once("rapport/ouderdomsAnalyse.php");
  include_once("rapport/openFIXOrders.php");
  include_once("rapport/Mandaatcontrole.php");
  include_once("rapport/include/Mandaatcontrole_L79.php");
  
	$selectData['datumTm'] 	= db2jul(getLaatsteValutadatum()); // form2jul('10-10-2007');
	$selectData['bedrijf'] = $Bedrijf;
  $selectDataBackup=$selectData;

	$db=new DB();
	if($handmatigeId>0)
  {
    $query = "SELECT AutoRun.Vermogensbeheerder,AutoRun.Rapportage,AutoRun.Trigger, AutoRun.Export_pad,
                   AutoRun.BestandsNaam, AutoRun.gebruikersnaam, AutoRun.wachtwoord, AutoRun.instellingen,
                   AutoRun.id, AutoRun.frequentie,AutoRun.autoVanaf,AutoRun.autoLaatste,AutoRun.autoEmailadres
            FROM AutoRun
            JOIN Vermogensbeheerders ON AutoRun.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
            WHERE AutoRun.id = '$handmatigeId'";
  }
  else
  {
    $query = "SELECT AutoRun.Vermogensbeheerder,AutoRun.Rapportage,AutoRun.Trigger, AutoRun.Export_pad,
                   AutoRun.BestandsNaam, AutoRun.gebruikersnaam, AutoRun.wachtwoord, AutoRun.instellingen,
                   AutoRun.id, AutoRun.frequentie,AutoRun.autoVanaf,AutoRun.autoLaatste,AutoRun.autoEmailadres
            FROM AutoRun 
            JOIN Vermogensbeheerders ON AutoRun.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder 
            JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder	WHERE
                 VermogensbeheerdersPerBedrijf.Bedrijf = '$Bedrijf'";
  }
	$db->SQL($query);
	$db->Query();
  $autorunRapporten=array();
	while($data = $db->nextRecord())
  {
    $autorunRapporten[]=$data;
  }

	foreach($autorunRapporten as $data)
  {
    if($handmatigeId==0 && $data['frequentie']>0 && $data['frequentie']<6)
    {
      $run=checkLastRun($data);
      if($run==0) //Nu niet draaien.
      {
        logIt("Rapport ".$data['Rapportage']." nog niet draaien.");
        continue;
      }
    }
    $selectData=$selectDataBackup;
    $instellingen=unserialize($data['instellingen']);
    foreach($instellingen as $key=>$value)
      $selectData[$key]=$value;

	  $rapportType = $data['Rapportage'];
    if(is_dir($data['Export_pad']))
    {
      $path = $data['Export_pad'];
    }
    else 
    {
//      echo "Export_pad (".$data['Export_pad'].") bestaat niet? <br>"; 
      $path = $__appvar['tempdir'];
    }
    logIt('UitgebreideAutoupdate: '.$data['Rapportage']);
    if(file_exists('rapport/include/'.$data['Rapportage'].'.php') && !class_exists($data['Rapportage']))
    {
      include_once('rapport/PDFRapport.php');
      include_once('rapport/include/'.$data['Rapportage'].'.php');
      $pdf=new PDFRapport();
      $raportageNaam=$data['Rapportage'];
      $datum= getLaatsteValutadatum();
      $rap=new $raportageNaam($pdf,'',substr($datum,0,4).'-01-01',substr($datum,0,10));
      $fileLocatie=$rap->autorun($selectData,$data);
  
      if($data['autoEmailadres'] <> '')
      {
        $cfg=new AE_config();
        $mailserver=$cfg->getData('smtpServer');
        $body="Autorun $rapportType" ;
        storeControleMail('Autorun',"Autorun $rapportType: ".date("d-m-Y H:i"),$body);
        if($mailserver !='')
        {
          $emailAddesses=explode(";",$data['autoEmailadres']);
          include_once('../classes/AE_cls_phpmailer.php');
          $mail = new PHPMailer();
          $mail->IsSMTP();
          $mail->From     = $emailAddesses[0];
          $mail->FromName = "Airs";
          $mail->Body    = $body;
          $mail->AltBody = html_entity_decode(strip_tags($body));
          $mail->AddAttachment($fileLocatie,basename($fileLocatie));
          foreach ($emailAddesses as $emailadres)
          {
            $mail->AddAddress($emailadres,$emailadres);
          }
          $mail->Subject = "Autorun $rapportType: ".date("d-m-Y H:i");
          $mail->Host=$mailserver;
          //listarray($mail);exit;
          if(!$mail->Send())
          {
            logIt("Verzenden van e-mail mislukt.");
          }
          else
          {
            logIt("Rapport $rapportType verzonden.");
          }
        }
      }
    }
    else
    {
	    if(class_exists($rapportType))
      {
        $rapport = new $rapportType($selectData);
        $rapport->USR = $USR;
        $rapport->__appvar = $__appvar;
        $rapport->writeRapport();
        if ($data['BestandsNaam'] != '')
        {
          $re = '/^(.*)(\[.*\])(.*)/';
          preg_match($re, $data['BestandsNaam'], $match);
          if(isset($match[2]))
          {
            $datum=date(substr($match[2],1,-1));
            $filename=$match[1].$datum.$match[3];
          }
          else
          {
            $filename = $data['BestandsNaam'];
          }
        }
        else
        {
          $rapportnaam = date('Y-m-d') . '_' . $Bedrijf . "_$rapportType";
          if($selectData['filetype']<>'')
            $filename = $rapportnaam . ".".$selectData['filetype'];
          else
            $filename = $rapportnaam . ".csv";
        }
        logIt("Rapport $rapportType ".$selectData['filetype']." $path".$filename);
        if($selectData['filetype']==''||$selectData['filetype']=='csv')
          $rapport->pdf->OutputCSV($path . $filename, "F");
        elseif($selectData['filetype']=='xls')
          $rapport->pdf->OutputXLS($path . $filename, "F");
        elseif($selectData['filetype']=='pdf')
          $rapport->pdf->Output($path . $filename,"F");

        if ($rapportType == 'RapportAfmExport')
        {
          $rapport->iAMexport($data['gebruikersnaam'], $data['wachtwoord'], $path . $filename);
          unlink($path . $filename);
        }

        if($data['autoEmailadres'] <> '')
        {
          $cfg=new AE_config();
          $mailserver=$cfg->getData('smtpServer');
          $body="Autorun $rapportType" ;
          storeControleMail('Autorun',"Autorun $rapportType: ".date("d-m-Y H:i"),$body);
          if($mailserver !='')
          {
            $emailAddesses=explode(";",$data['autoEmailadres']);
            include_once('../classes/AE_cls_phpmailer.php');
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->From     = $emailAddesses[0];
            $mail->FromName = "Airs";
            $mail->Body    = $body;
            $mail->AltBody = html_entity_decode(strip_tags($body));
            $mail->AddAttachment($path . $filename,$filename);
            foreach ($emailAddesses as $emailadres)
            {
              $mail->AddAddress($emailadres,$emailadres);
            }
            $mail->Subject = "Autorun $rapportType: ".date("d-m-Y H:i");
            $mail->Host=$mailserver;
            //listarray($mail);exit;
            if(!$mail->Send())
            {
              logIt("Verzenden van e-mail mislukt.");
            }
            else
            {
              logIt("Rapport $rapportType verzonden.");
            }
          }
        }
      }
      else
      {
        logIt("Rapport $rapportType niet beschikbaar.");
      }
    }
    if($handmatigeId==0)
    {
      $db2 = new DB();
      $query = "UPDATE AutoRun SET autoLaatste=now() WHERE id='" . $data['id'] . "'";
      $db2->SQL($query);
      $db2->Query();
      logIt("Rapport $rapportType AutoRun autoLaatste bijgewerkt.");
    }
 	}
}

?>