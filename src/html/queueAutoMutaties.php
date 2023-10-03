<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/11/17 17:25:16 $
 		File Versie					: $Revision: 1.12 $

 		$Log: queueAutoMutaties.php,v $
 		Revision 1.12  2018/11/17 17:25:16  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/05/06 11:31:30  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/12/15 07:11:38  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/01/11 12:47:44  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/11/23 14:11:47  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/09/24 15:50:25  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/09/04 16:16:25  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/03/09 16:20:10  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/01/27 13:59:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/07/16 13:31:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/04/25 15:47:21  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/30 15:33:21  rvv
 		*** empty log message ***


*/

$disable_auth = true;
include_once("wwwvars.php");
include_once("../classes/AE_tableSync.php");
include_once("../classes/klantMutatiesVerwerken.php");
include_once("../classes/updateHistorySync.php");
include_once("../classes/AIRS_consolidatie.php");

$nieuweRecords=false;
$syncKlantMutaties = new AE_tableSync('klantMutaties');
$syncKlantMutaties->copyRecords();
if($syncKlantMutaties->getRecordNr() > 0)
  $nieuweRecords=true;

$sync = new AE_tableSync('VoorlopigeRekeningmutaties');
$sync->copyRecordsNoId();
$sync->emailErrors();
if($sync->getRecordNr() > 0)
  $nieuweRecords=true;

$sync = new AE_tableSync('VoorlopigeRekeningafschriften');
$sync->copyRecordsNoId();
$sync->emailErrors();
if($sync->getRecordNr() > 0)
  $nieuweRecords=true;

$sync = new AE_tableSync('fondsAanvragen');
$sync->copyRecordsNoId();
$sync->emailErrors();
if($sync->getRecordNr() > 0)
  $nieuweRecords=true;

$sync = new AE_tableSync('fondskoersAanvragen');
$sync->copyRecordsNoId();
$sync->emailErrors();
if($sync->getRecordNr() > 0)
  $nieuweRecords=true;

$nieuweRecords=true;
if($nieuweRecords==true) //Zijn er nieuwe regels? Mogen deze automatisch worden verwerkt?
{
  $log = array();
  $verwerk = new klantMutatiesVerwerken();
  $verwerk->automatischVerwerken();
  if($verwerk->counter > 0)
  {
    $verwerk->createQueueUpdates();
    $verwerk->sendEmail();
    $con=new AIRS_consolidatie();
    $con->bijwerkenConsolidaties();
  }
  $log   = $verwerk->getLog();
  if($verwerk->counter >0)
  {
    $log[] = $verwerk->counter . " regels verwerkt. <br>\n";
  }
  $error = $verwerk->getError();

  $db = new DB();
  if(count($error)>0)
  {
    $query= "INSERT INTO ae_log SET txt = '".addslashes(serialize($error))."' ,date = now()";
    $db->SQL($query);
    $db->Query();
  }
  if(count($log)>0)
  {
    $query= "INSERT INTO ae_log SET txt = '".addslashes(serialize($log))."',date = now()";
    $db->SQL($query);
    $db->Query();
  }
}

$cfg=new AE_config();
$lastSync=$cfg->getData('LastQueuePoll');
if($lastSync < time()-(60*15))
{
  $sync = new updateHistorySync();
  $sync->syncRecords();
  $sync->savelog();
  $cfg->addItem('LastQueuePoll',time());
}

$InCompleteCheck=$cfg->getData('LastQueuePollInComplete');
if($InCompleteCheck < time()-3540)
{
  $checkUren=array(10,13,16);
  $uur=date('G'); 
  if(in_array($uur,$checkUren))
  {
    $sync = new updateHistorySync();
    $sync->checkQueue();
    $sync->savelog();
    $cfg->addItem('LastQueuePollInComplete',time());
  }
}


if(date('G') == 3)
{
  $min=date('i');
  if($min > 10 && $min < 20)
  {
    $db=new DB();
    $query="DELETE FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.add_date < NOW() - INTERVAL 1 DAY";
    $db->SQL($query);
    $db->Query();
    $query="OPTIMIZE TABLE TijdelijkeRapportage";
    $db->SQL($query);
    $db->Query();
  }
}


?>
