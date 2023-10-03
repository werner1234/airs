<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/03/07 15:46:24 $
 		File Versie					: $Revision: 1.5 $

 		$Log: dd_inlees_email.php,v $
 		Revision 1.5  2018/03/07 15:46:24  cvs
 		call 6695
 		
 		Revision 1.4  2018/03/07 15:10:06  cvs
 		call 6695
 		
 		Revision 1.3  2017/10/27 08:54:35  cvs
 		no message
 		

 		
*/


include("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");

$cfg=new AE_config();
$data=array();
$data['ddbMailServer']=$cfg->getData('ddbMailServer');
$data['ddbMailUser']=$cfg->getData('ddbMailUser');
$data['ddbMailPasswd']=$cfg->getData('ddbMailPasswd');



echo template($__appvar["templateContentHeader"],$content);
?>
<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
<style>
  fieldset{
    width: 400px;
  }
  button{
    width: 99%;
    padding:5px;
  }
  legend{
    background: rgba(20,60,90,1);
    color: #FFF;
    padding: 5px;

    margin:0;
    border-radius: 5px;
    width:97%
  }

</style>
<h1><?=vt("Digitale documenten via mailbox")?></h1>


<fieldset>
  <legend> <?= vt('Verwerken'); ?> </legend>
  <button class="btn-new btn-default"><i class="fa fa-map-o" aria-hidden="true"></i> <a href="email_queue.php"><?=vt("Bekijk wachtrij van ingelezen onverwerkte berichten")?></a></button><br/><br/>
  <button class="btn-new btn-default"><a href="email_inbox.php"><?=vt("Gedeelde mailbox bekijken, zonder inlezen")?></a></button><br/><br/>
  <button class="btn-new btn-default"><a href="email_inlezen.php"><?=vt("Gedeelde mailbox inlezen en in de wachtrij plaatsen")?></a></button><br/><br/>

<?

  if (get_eMailInlezenCheck() == 2)
  {
?>
    <button class="btn-new btn-default"><i class="fa fa-hourglass-end" aria-hidden="true"></i> <a href="dd_mailcronlogList.php"><?=vt("Log van automatisch ingelezen mails")?></a></button><br/><br/>
    <button class="btn-new btn-default btn-blue"><i class="fa fa-hourglass-end" aria-hidden="true"></i> <a href="queueDigidocViaEmail.php?from=airs"><?=vt("automatisch inlezen mails forceren")?></a></button><br/><br/>
<?
  }

?>

</fieldset>

<br/>
<br/>
<?
  if (GetCRMAccess(2))
  {
?>
<fieldset>
  <legend> <?=vt("Beheer")?> </legend>
  <button class="btn-new btn-default"><a href="email_setup.php"><i class="fa fa-cog" aria-hidden="true"></i> <?=vt("Mailbox instellingen")?></a></button><br/><br/>
  <button class="btn-new btn-default"><a href="email_setup.php?initdb=1"><i class="fa fa-database" aria-hidden="true"></i> <?=vt("Database bijwerken")?> </a></button><br/><br/>
</fieldset>
<?
  }


echo template($__appvar["templateRefreshFooter"],$content);
exit;


if(!function_exists('imap_open'))
{
  echo vt("De benodigde email functionaiteit is niet op deze server geinstalleerd").".<br>\n";
  exit;
}

$mb = imap_open($data['ddbMailServer'],$data['ddbMailUser'], $data['ddbMailPasswd']);
if(!$mb)
{
 echo vt("Niet gelukt om een verbinding naar")." ".$data['ddbMailServer']." ".vt("op te zetten").". ".imap_last_error()."<br>\n";
}
else
{
  echovt("Verbonden met")."  ".$data['ddbMailServer'].".<br>\n";
}

$messageCount = imap_num_msg($mb);
for( $mailId = 1; $mailId <= $messageCount; $mailId++ )
{
   $header=imap_fetchheader ($mb, $mailId);
   $headerInfo=imap_rfc822_parse_headers($header); 
   $from=$headerInfo->from[0]->mailbox.'@'.$headerInfo->from[0]->host;
   $tmp=imap_mime_header_decode($headerInfo->subject); 
   $subject='';
   foreach($tmp as $textObject)
   {
     if($textObject->charset=='UTF-8' || $textObject->charset=='default')
       $subject.= $textObject->text;
     else
       $subject.= iconv($textObject->charset, "UTF-8", $textObject->text);
   }
   echo "Mail from $from  onderwerp '".$subject."' <br>\n";
   $body =imap_body($mb, $mailId);
  $fullMail=$header."\n\n".$body;
  debug($fullMail);
   $relatieId=zoekAdres($from);
   if($relatieId==0)
   {
     preg_match_all('/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})/', $body, $tmp,PREG_SET_ORDER);
     $emailOptions=array();
     foreach($tmp as $id=>$emailData)
       $emailOptions[$emailData[0]]++;
     asort($emailOptions);
     $besteId=0;
     foreach($emailOptions as $eMail=>$aantal)
     {
       $aanwezigeId=zoekAdres($eMail,false);
       if($aanwezigeId>0)
       {
         $besteId=$aanwezigeId;
         echo "email $eMail komt $aantal x voor in de email en is een relatie bij $aanwezigeId. <br>\n";
       }
       else
       {
         echo "email $eMail komt $aantal x voor in de email maar niet als relatie.<br>\n";
       }
       if($besteId>0)
       {
         $query="SELECT id,naam FROM CRM_naw WHERE id='$besteId'";
         $DB->SQL($query);
         $relatie=$DB->lookupRecord(); 
         echo "Beste match met $besteId ".$relatie['naam'].". <br>\n";
         $relatieId=$besteId;   
       }
     }  
   }
   if($relatieId>0)
   {
    $fullMail=$header."\n\n".$body;
     debug($fullMail);
//    $dd = new digidoc();
//    $rec ["filename"] =  preg_replace('/[^A-Za-z0-9_.-]/', "_", $subject).'.eml';
//    $rec ["filesize"] = strlen($fullMail);
//    $rec ["filetype"] = "text/plain";
//    $rec ["description"] = $subject;
//    $rec ["blobdata"] = $fullMail;
//    $rec ["keywords"] ='email';
//    $rec ["categorie"] ='email';
//    $rec ["module"] = 'CRM_naw';
//    $rec ["module_id"] = $relatieId;
//    $dd->useZlib = true;
//    if($dd->addDocumentToStore($rec) == true)
//    {
//      echo "Mail in archief opgeslagen.<br>\n";
//      if(imap_delete($mb, $mailId))
//      {
//
//        echo "Mail uit mailbox verwijderd.<br>\n";
//      }
//      else
//        echo "Mislukt om mail uit mailbox te verwijderen.<br>\n";
//    }
         
   }
   else
   {
//     if(imap_delete($mb, $mailId))
//     {
//        echo "Niet te koppelen mail $mailId  $from  onderwerp '".$subject."' uit mailbox verwijderd.<br>\n";
//     }
   }
   
  // listarray($potentialEmails);
  // listarray($headerInfo);
}
//imap_expunge($mb);
imap_close($mb);
echo vt("Inlezen klaar").".<br>\n";

function zoekAdres($email,$log=true)
{
   $DB=new DB();
   $fromEscaped=mysql_real_escape_string($email);
   $query="SELECT id,naam FROM CRM_naw WHERE email='".$fromEscaped."' OR emailZakelijk='".$fromEscaped."' OR emailPartner='".$fromEscaped."'";
   $DB->SQL($query);
   $relatie=$DB->lookupRecord();
   if($relatie['id']>0)
   {
      if($log==true)
        echo vt("Emailadres")." $from ".vt("gevonden bij relatie")." ".$relatie['id']." ".$relatie['naam']."<br>\n";
      return $relatie['id'];
   }
   else
   {
     if($log==true)
       echo vt("Emailadres")." $from ".vt("bij geen enkel NAW record gevonden. Poging het email adres bij de adressen te vinden").".<br>\n";
     $query="SELECT rel_id,naam FROM CRM_naw_adressen WHERE email='".$fromEscaped."'";
     $DB->SQL($query);
     $relatie=$DB->lookupRecord();
     if($relatie['rel_id']>0)
     {
        if($log==true)
          echo vt("Adres gevonden bij")." ".$relatie['rel_id']."  ".$relatie['naam']." ";
        return $relatie['rel_id'];
     }
     else
     {
       if($log==true)
         echo vt("Emailadres niet gevonden bij de naw_adressen").".<br>\n";
     }
     
   }
   return 0;
}

/*

listarray($mail);
$headers = imap_headers($mail);

listarray($headers);

$head = imap_rfc822_parse_headers(imap_fetchheader($mail, 1, FT_UID));
listarray($head);

*/



