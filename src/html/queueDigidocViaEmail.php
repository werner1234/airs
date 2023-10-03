<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/24 11:28:35 $
 		File Versie					: $Revision: 1.3 $

 		$Log: queueDigidocViaEmail.php,v $
 		Revision 1.3  2020/01/24 11:28:35  cvs
 		tikfout
 		
 		Revision 1.2  2017/12/13 13:43:52  cvs
 		call 5911
 		
 		Revision 1.1  2017/08/25 13:16:28  cvs
 		call 5911
 		

*/

$gui = ($_GET["from"] == "airs");

include("wwwvars.php");
$skipInlezen = false;
if ($gui)
{
  $rec["eMailInlezen"] = "via GUI";
}
else
{
  $disable_auth = true;

  global $__appvar;
  $db = new DB();
  $query = "SELECT eMailInlezen FROM `Vermogensbeheerders` ORDER BY id ";
  $rec = $db->lookupRecordByQuery($query);

  if ($rec["eMailInlezen"] != 2)  // alleen doorgaan als "automatisch" is ingesteld
  {
    $skipInlezen = true;
  }
}

if (!$skipInlezen)
{
  logIt('triggered: DigidocViaEmail = '.$rec["eMailInlezen"]);
  include_once("../classes/AE_cls_digidoc.php");
  include_once("../classes/AE_cls_Email.php");


  if ($gui)
  {
    echo template($__appvar["templateContentHeader"],$content);
    ?>
    <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
    <link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
    <?
  }

  $tmpl = new AE_template();
  $fmt = new AE_cls_formatter();
  if ( ! isset ($__appvar['office365']) ) {
    $mail = new AE_cls_Email();
  } else {
    $mail = new AE_cls_ExchangeOnline();
  }

  $mail->initTables();  // tabellen aanmaken voor module

  $mail->buildRouterTable();
  $tel = $mail->messageCount();
  $mail->getMessages();


  $mail->matchMails();

  $queue = $mail->populateQueue();

  $qDelete = array();
  $jsRoute = array();


  $match = 0;
  foreach($queue["matchSingle"] as $msg)
  {

    $jsRoute[$msg["id"]] = $msg["route"];
//  debug($msg);
    if ($mail->storeInDigidoc($msg["id"],  $msg["CRM_id"]))
    {
      $match++;
      $qDelete[] = $msg["id"];
      $mail->addCronLog($msg);
    }
  }

  if ($gui)
  {
    ?>
    <h1>geforceerd verwerken uitgevoerd.</h1>
    <ul>
      <li><?=(int) $tel?> items ingelezen</li>
      <li><?=(int) $match?> items gematched en gekoppeld</li>
    </ul>
    <br/>
    <button class="btn-new btn-default"><a href="dd_inlees_email.php"><i class="fa fa-angle-double-left" aria-hidden="true"></i> terug </a></button><br/><br/>
    <?
    echo template($__appvar["templateRefreshFooter"],$content);
  }

  $mail->deleteFromQueue($qDelete);

}

