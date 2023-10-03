<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/10/27 08:54:35 $
 		File Versie					: $Revision: 1.2 $

 		$Log: email_inlezen_automatisch.php,v $
 		Revision 1.2  2017/10/27 08:54:35  cvs
 		no message
 		
 		Revision 1.1  2017/06/30 11:12:59  cvs
 		call 5911 cmdline script
 		

 		
*/

/////////////////////////////////////////////
///
///   is verhuisd naar queueDigidocViaEmail.php
///
/////////////////////////////////////////////
$disable_auth = true;
include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once("../classes/AE_cls_Email.php");
ini_set('default_charset', 'utf-8');

$tmpl = new AE_template();
$fmt = new AE_cls_formatter();
if ( ! isset ($__appvar['office365']) ) {
  $mail = new AE_cls_Email();
} else {
  $mail = new AE_cls_ExchangeOnline();
}

$mail->initTables();  // tabellen aanmaken voor module

$mail->buildRouterTable();

$mail->getMessages();


$mail->matchMails();

$queue = $mail->populateQueue();

$qDelete = array();
$jsRoute = array();
foreach($queue["matchSingle"] as $msg)
{
  $jsRoute[$msg["id"]] = $msg["route"];
//  debug($msg);
  if ($mail->storeInDigidoc($msg["id"],  $msg["CRM_id"]))
  {
    $qDelete[] = $msg["id"];
    $mail->addCronLog($msg);
  }
}


//$mail->deleteFromQueue($qDelete);

exit;
