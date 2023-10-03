<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/19 14:59:20 $
 		File Versie					: $Revision: 1.13 $

 		$Log: externequerierun.php,v $
 		Revision 1.13  2020/02/19 14:59:20  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2019/01/19 18:04:35  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/11/16 16:39:19  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.9  2018/05/02 16:06:03  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/04/28 18:34:19  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/02/12 11:20:39  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/01/06 16:31:26  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/12/22 06:05:23  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/12/20 16:44:58  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/09/17 15:07:39  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/08/09 15:05:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/04 04:10:05  rvv
 		*** empty log message ***
 		


*/

include_once("wwwvars.php");
require_once("../classes/AE_cls_xls.php");
include_once('../classes/excel/Writer.php');
include_once("../config/ordersVars.php");


if (isset($_GET["queryid"]))
{
  $uitvoer=new externeQueryRun();
  $uitvoer->verzamelData($_GET["queryid"]);
  $uitvoer->exportOne($_GET["queryid"]);
}


class externeQueryRun
{
  function externeQueryRun()//$id
  {
    /*
global $USR;

$db = new DB();

$query = "SELECT titel, query FROM externeQueries WHERE id='".$id."'";
$db->SQL($query);
$this->queryRecord=$db->lookupRecord();

$query = "UPDATE externeQueries SET run_date=now(),run_user='$USR' WHERE id='".$id."'";
$db->SQL($query);
$db->Query();
*/
    $this->xlsData=array();
    $this->queryRecord=array();
  }
  
  function verzamelData($id)
  {
    global $USR;
    
    $db = new DB();
    $query = "SELECT id,titel, query, controlekolommen, autoEmailadres,uitvoer FROM externeQueries WHERE id='".$id."'";
    $db->SQL($query);
    $this->queryRecord=$db->lookupRecord();
    $this->queryInfo[$this->queryRecord['id']]=$this->queryRecord;

    $query = "UPDATE externeQueries SET run_date=now(),run_user='$USR' WHERE id='".$id."'";
    $db->SQL($query);
    $db->Query();
    
    $db->SQL($this->queryRecord['query']);
    $db->Query(); 
    $headerPrinted=false;
    while ($row = $db->nextRecord())
    {
      if($headerPrinted==false)
      {
        if($this->queryRecord['controlekolommen']==1)
          $header=array(array('Actie','header'),array('Wie','header'),array('Opmerking','header'));
        else
          $header=array();
        foreach($row as $key=>$value)
        {
          array_push($header,array($key,'header'));
        }
        $this->xlsData[$id][] = $header;
        $headerPrinted=true;
      }
      if($this->queryRecord['controlekolommen']==1)
        $tmp=array('','','');
      else
        $tmp=array();
      foreach($row as $key=>$value)
        $tmp[]=$value;
     
      $this->xlsData[$id][] = $tmp;
    }
    $this->xlsData[$id][]=array();
    $this->xlsData[$id][]=array($USR,date('d-m-Y'),date("H:i"));
    
  }
  
  function exportOne($id)
  {
    $xls = new AE_xls();
    $xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $xls->setData($this->xlsData[$id]);
    $xls->OutputXls($this->queryRecord['titel'].'.xls');
   // $xls->OutputXls('test.xlsx',true,'xlsx');
  
  }
  
  function fillXlsSheet($id,$worksheet,$workbook='')
	{
	  for($regel = 0; $regel < count($this->xlsData[$id]); $regel++ )
	  {
		  for($col = 0; $col < count($this->xlsData[$id][$regel]); $col++)
		  {
		    if (is_array($this->xlsData[$id][$regel][$col]))
		    {
		      $celOpmaak = $this->xlsData[$id][$regel][$col][1]; //1=opmaak
		      $worksheet->write($regel, $col, $this->xlsData[$id][$regel][$col][0],$this->opmaak[$celOpmaak]);	//0=waarde
		    }
		    else
		    {
		      $worksheet->write($regel, $col, $this->xlsData[$id][$regel][$col]);
		    }
		  }
	  }
	}
  
  function setOpmaak($workbook)
  {
    $this->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
      if(!isset($this->opmaak[$opmaakSleutel]))
      {
        $this->opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $this->opmaak[$opmaakSleutel]->$eigenschap($value);
        }
      }
    }
  }
  
  function exportAll()
  {
    global $__appvar,$USR;
    $filename="Queries_".date('Y-W')."_".$USR.".xls";
    $workbook = new Spreadsheet_Excel_Writer($__appvar['tempdir'].$filename);
    $this->setOpmaak($workbook);
    $i=0;
    foreach($this->xlsData as $queryId=>$queryData)
    {
      $info=$this->queryInfo[$queryId];
      if($info['titel']=='')
        $info['titel']=$queryId;
      $worksheet[$i] =& $workbook->addWorksheet(substr($info['titel'],0,31));
      $this->fillXlsSheet($queryId,$worksheet[$i],$workbook);
      $i++;
    }
    $workbook->close();
    
    return $filename;
  }

  function getAutorunJobs()
  {
    $vandaag=date('Y-m-d');
    $vandaagJul=db2jul($vandaag);
    $dagVanMaand=date('j',$vandaagJul);
    $huidigeMaand=date('n',$vandaagJul);
    $huidigeJaar=date('Y',$vandaagJul);
    $huidgeUur=date('G');

    $db=new DB();
    $query="SELECT id,frequentie,autoVanaf,autoLaatste FROM externeQueries WHERE autoEmailadres<>'' AND autoVanaf<>'0000-00-00' AND autoVanafUur<='$huidgeUur' ";
    $db->SQL($query);
    $db->Query();

    $uitvoeren=array();
    while ($row = $db->nextRecord())
    {
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
          $gewensteDag=date('j',$vanafJul);
          $laatsteMaand=date('n',$laatsteJul);
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
          $gewensteDag=date('j',$vanafJul);
          $gewensteMaand=date('n',$vanafJul);
          $laatsteJaar=date('Y',$laatsteJul);
          //$restDagenInJaar=round((db2jul('31-12'.$huidigeJaar)-$vandaagJul)/86400);
          if(($dagVanMaand>=$gewensteDag && $huidigeMaand>=$gewensteMaand) && ($huidigeJaar != $laatsteJaar ))//($dagenGeleden>=$restDagenInJaar ||
            $uitvoeren[]=$row['id'];
        }
        elseif($row['frequentie']=='7')//halfjaar
        {
          $gewensteDag=date('j',$vanafJul);
          $gewensteMaandEen=date('n',$vanafJul);
          $laatsteMaand=date('n',$laatsteJul);

          if($gewensteMaandEen<7)
            $gewensteMaandTwee=$gewensteMaandEen+6;
          else
            $gewensteMaandTwee=$gewensteMaandEen-6;
          
          if($huidigeMaand==$gewensteMaandEen || $huidigeMaand==$gewensteMaandTwee)
          {
            if($dagVanMaand>=$gewensteDag && $huidigeMaand != $laatsteMaand)
            {
              $uitvoeren[]=$row['id'];
            }
          }
          

        }
      }
    }
    return $uitvoeren;
  }

  function sendXlsEmail($id)
  {
    global $__appvar;
    include_once('../classes/AE_cls_phpmailer.php');

    $this->xlsData=array();
    $this->verzamelData($id);
  
    $titel = ereg_replace("[^A-Za-z0-9-_ ]", "", $this->queryRecord['titel']);
    if($this->queryRecord['uitvoer']==0)//excel
    {
      $xls = new AE_xls();
      $xls->excelOpmaak['header'] = array('setAlign' => 'centre', 'setBgColor' => '22', 'setBorder' => '1');
      $xls->setData($this->xlsData[$id]);
      if (class_exists('XMLWriter'))
      {
        $filename = $__appvar['tempdir'] . $titel . '.xlsx';
        $xls->OutputXls($filename, false, 'xlsx');
        $extensie = '.xlsx';
      }
      else
      {
        $filename = $__appvar['tempdir'] . $titel . '.xls';
        $xls->OutputXls($filename, true);
        $extensie = '.xls';
      }
    }
    else
    {
      $filename = $__appvar['tempdir'] . $titel . '.csv';
      $extensie = '.csv';
      if($fp = fopen($filename,"w+"))
      {
        $csvdata = generateCSV($this->xlsData[$id]);
        fwrite($fp,$csvdata);
        fclose($fp);
      }
    }
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $body="Externe query ".$this->queryRecord['titel'];
    storeControleMail('externeQueries',"Externe query ".$this->queryRecord['titel']." ".date("d-m-Y H:i"),$body);
    if($this->queryRecord['autoEmailadres'] !="" && $mailserver !='')
    {
      $emailAddesses=explode(";",$this->queryRecord['autoEmailadres']);
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = $emailAddesses[0];
      $mail->FromName = "Airs";
      $mail->Body    = $body;
      $mail->AltBody = html_entity_decode(strip_tags($body));
      foreach ($emailAddesses as $emailadres)
      {
        $mail->AddAddress($emailadres,$emailadres);
      }
      $mail->Subject = "".$this->queryRecord['titel']." ".date("d-m-Y H:i");
      $mail->AddAttachment($filename,$this->queryRecord['titel'].$extensie);
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo "Verzenden van ".$this->queryRecord['titel']." e-mail mislukt.";
      }
      else
      {
        $db=new DB();
        $query="UPDATE externeQueries SET autoLaatste=now() WHERE id='$id'";
        $db->SQL($query);
        $db->Query();
      }
    }
  }
}

?>