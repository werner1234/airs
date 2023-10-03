<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/27 18:31:14 $
 		File Versie					: $Revision: 1.20 $

 		$Log: CRM_mailer.php,v $
 		Revision 1.20  2019/04/27 18:31:14  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/01/19 13:52:12  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2019/01/16 16:35:40  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2017/03/22 16:50:10  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/02/13 14:01:08  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/09/14 15:14:59  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2014/07/30 15:33:10  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/03/22 15:48:26  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/10/12 15:49:59  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/08/04 10:47:37  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/07/28 10:14:52  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/06/12 18:45:49  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/09/09 17:34:13  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/09/06 13:39:09  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/09/05 18:10:27  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/07/25 15:59:25  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/04/11 17:14:52  rvv
 		*** empty log message ***

 		Revision 1.3  2011/10/23 13:32:25  rvv
 		*** empty log message ***

 		Revision 1.2  2011/05/04 16:28:41  rvv
 		*** empty log message ***

 		Revision 1.1  2011/04/30 16:23:58  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
include_once('../classes/AE_cls_phpmailer.php');

$customTemplate = new AE_CustomTemplate('crmEmailLos');

function valid_email_quick($address)
{
  $multipleEmail=explode(";",$address);
  foreach ($multipleEmail as $address)
  {
    $address=trim($address);
    if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $address) || (strlen($address)==0))
      return false;
  }
  return true;
}

$input=$_GET;
foreach ($_POST as $key=>$value)
{
  if($value <> '')
    $input[$key]=$value;
}

$db=new DB();
$query="SELECT CRM_naw.portefeuille,Vermogensbeheerders.Naam FROM CRM_naw
JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.portefeuille
JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder
WHERE CRM_naw.id='".$input['relid']."'";
$db->SQL($query);
$portefeuilleVermogensbeheerder=$db->lookupRecord();

$query="SELECT Vermogensbeheerders.Naam,VermogensbeheerdersPerGebruiker.Gebruiker,Gebruikers.Naam as naamGebruiker
FROM Vermogensbeheerders 
Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
INNER JOIN Gebruikers ON VermogensbeheerdersPerGebruiker.Gebruiker = Gebruikers.Gebruiker
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='".$_SESSION['usersession']['gebruiker']['Gebruiker']."'  AND Vermogensbeheerders.Naam <> '' ORDER BY Vermogensbeheerders.Naam limit 1";
$db->SQL($query);
$vermogensbeheerder=$db->lookupRecord();
$naam=($portefeuilleVermogensbeheerder['Naam']<>''?$portefeuilleVermogensbeheerder['Naam']:$vermogensbeheerder['Naam'])." | ".$vermogensbeheerder['naamGebruiker'];

if($input['id'] || $input['relid'])
{
  if($input['id']=='' && $input['relid']>0)
    $crmId=$input['relid'];
  else
    $crmId=$input['id'];
  $db=new DB();
  $query="SELECT email,id FROM CRM_naw WHERE id='".$crmId."'";
  $db->SQL($query);
  $crmData=$db->lookupRecord();
  $input['relId']=$crmData['id'];
}
elseif($input['portefeuille'])
{
  $db=new DB();
  $query="SELECT id,email FROM CRM_naw WHERE portefeuille='".$input['portefeuille']."'";
  $db->SQL($query);
  $crmData=$db->lookupRecord();
  $input['relId']=$crmData['id'];
}

$path=$__appvar['tempdir'];

if($input['action']=='verzenden')
{
  if(!valid_email_quick($input['aan']))
  {
    $afbreken=true;
    $message.="Ongeldig email adres opgegeven.";
  }
  if($afbreken==false)
  {
    if($input['dosierId'] > 0 && $input['relid'] > 0)
    {
      $_GET['relid']=$input['relid'];
      $_GET['id']=$input['dosierId'];
      $filename=$input['dosierId'].'_gespreksverslag.pdf'; 
      $_GET['outputFilename']=$path.'/'.$filename;
      include('CRM_naw_dossierPrint.php');
    }  


    if($input['docRefId'] > 0)
    {
      $dd = new digidoc();
      $filename=$dd->retrieveDocumentToFile($input['docRefId'],$path);
    }
    if($input['filename'])
      $filename=basename($input['filename']);
      

    $emailAddesses=explode(";",$_SESSION['usersession']['gebruiker']['emailAdres']);
    
    
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $emailAddesses[0];
    $mail->FromName = $naam;
    $mail->Body    = $mail->encodeInlineImages($input['body']);
    $mail->AltBody = html_entity_decode(strip_tags($input['body']));
    $mail->AddAddress($input['aan'],$input['aan']);
    if($input['CC'] <> '')
      $mail->AddCC($input['CC'],$input['CC']);
    if($input['BCC'] <> '')
      $mail->AddBCC($input['BCC'],$input['BCC']);
    $mail->Subject = $input['onderwerp'];
    if(is_file($path.$filename))
    {
      $mail->AddAttachment($path.$filename,$filename);
      $message.= "$filename aan email toegevoegd. <br>";
    }

    for($i=2;$i<5;$i++)
    {
      if(is_file($_FILES['filename'.$i]['tmp_name']) && isset($_FILES['filename'.$i]['name']))
      {
        $mail->AddAttachment($_FILES['filename'.$i]['tmp_name'],$_FILES['filename'.$i]['name']);
        $message.= $_FILES['filename'.$i]['name']." aan email toegevoegd. <br>";
      }
    }
 
    if(!$mail->Send())
    {
      $message.= "Fout bij het zenden van email naar " .$input['aan']. "<br>";
      echo $mail->ErrorInfo;
    }
    else
    {
      $message.= "Email is verzonden naar ".$input['aan'].". <br>";
      $emailSend=true;
    }

    if($emailSend && $input['DDB'] == 1)
    {
       if($input['relId']=='' && $input['relid'] > 0)
         $input['relId'] = $input['relid'];
       $file='email_'.date('Ymd_Hi_').$input['onderwerp'].'.eml';
       $filesize = strlen($mail->fullEmail);
       $filetype = 'text/plain';
       $dd = new digidoc();
       $rec ["filename"] = $file;
       $rec ["filesize"] = "$filesize";
       $rec ["filetype"] = "$filetype";
       $rec ["description"] = $input['onderwerp'];
       $rec ["blobdata"] = $mail->fullEmail;
       $rec ["keywords"] =$file;
       $rec ["categorie"] ='email';
       $rec ["module"] = 'CRM_naw';
       $rec ["module_id"] = $input['relId'];
       $dd->useZlib = false;
       $dd->addDocumentToStore($rec);
       $message.= "Email is opgeslagen. <br>";

    }
    $mail->ClearAddresses();
    if($filename)
      unlink($path.$filename);
  }
}
//  function loadEditor(textarea,h,w)
//{
//  CKEDITOR.replace( textarea ,
//	{
//		skin : 'office2003',
//		DIALOG_RESIZE_NONE : true,
//		height: h,
//		width: w,
//		toolbar :
//   [
//     ['Source','-','Save','NewPage','Preview','-','Templates'],
//     ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
//     ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
//     '/',
//     ['Font','Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
//     ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
//     ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
//   ],
//   font_names : 'Arial/Arial, Helvetica, sans-serif; Comic Sans MS/Comic Sans MS, cursive; Courier New/Courier New, Courier, monospace; Georgia/Georgia, serif; Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif; Tahoma/Tahoma, Geneva, sans-serif; Times New Roman/Times New Roman, Times, serif; Trebuchet MS/Trebuchet MS, Helvetica, sans-serif; Calibri/Calibri, Verdana, Geneva, sans-serif; Verdana/Verdana, Geneva, sans-serif'
//
//	});
//}

//  function doEditorOnload()
//  {
//    loadEditor('body',400,700);
//  }
$editcontent['javascript'].="

function submitForm()
{
	document.editForm.submit();
}
 ";
//$editcontent['body']='onLoad="doEditorOnload();"';

if($input['body']=='')
  $input['body']=$_SESSION['usersession']['gebruiker']['emailHandtekening'];

$editcontent['pageHeader'] = "<div class='formblock'>
  <b>eMail verzenden.</b> $subHeader
</div><br><br>";
//$editcontent['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor/ckeditor.js"></script>';
echo template($__appvar["templateContentHeader"],$editcontent);
?>

<?
if(!$emailSend)
{
?>
<form  enctype="multipart/form-data" method="POST" name="editForm">
    <input type="hidden" name="action" value="verzenden">
    <input type="hidden" name="MAX_FILE_SIZE" value="16777216" />
    <input type="hidden" name="docRefId" value="<?=$input['docRefId']?>">
    <input type="hidden" name="dosierId" value="<?=$input['dosierId']?>">
    <input type="hidden" name="relId" value="<?=$input['relId']?>">
    <input type="hidden" name="filename" value="<?=$input['filename']?>">

    <div class="formblock">
        <div class="formlinks"><label for="factuurnr">Templates</label></div>
        <div class="formrechts">
            <select name="template" id="templateSelect"><?=$customTemplate->getTemplateSelect();?></select>
        </div>
    </div>
    <br />

    <div class="formblock">
    <div class="formlinks">Afzender</div>
    <div class="formrechts">
    <input type="text" name="afzender" size="50" readonly value="<?=$naam." <".$_SESSION['usersession']['gebruiker']['emailAdres'].">"?>">
    </div>
    </div>


    <div class="formblock">
    <div class="formlinks">Aan</div>
    <div class="formrechts">
    <input type="text" name="aan" size="50" value="<?if($input['aan']=='')echo $crmData['email'];else echo $input['aan'];?>">
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks">CC</div>
    <div class="formrechts">
    <input type="text" name="CC" id="CC" size="50" value="<?=$input['CC'];?>">
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks">BCC</div>
    <div class="formrechts">
    <input type="text" name="BCC" id="BCC" size="50" value="<?=$input['BCC'];?>">
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks">Onderwerp</div>
    <div class="formrechts">
    <input type="text" name="onderwerp" id="onderwerp" size="50" value="<?=$input['onderwerp']?>">
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks">DDB toevoegen</div>
    <div class="formrechts">
      <input type="hidden" name="DDB" value="0">
      <input type="checkbox" name="DDB" value="1" <?if($input['DDB']==1 || !isset($input['DDB']))echo "checked";?> >
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks"><label for="body" title="body">Body tekst</label> </div>
    <div class="formrechts">
    <textarea class="form-control textEditor" cols="60"  rows="10" name="body" id="body"><?=$input['body']?></textarea>
    </div>
    </div>

    <div class="formblock">
    <div class="formlinks">bijlagen</div>
    <div class="formrechts">
    <?
    if($input['docRefId'])
    {
      echo "<a href='dd_push.php?show=1&docRefId=".$input['docRefId']."'  target='_blank' >DigiDoc</a>";
    }
    if($input['filename'])
    {
      echo "<a href='showTempfile.php?show=1&filename=".basename($input['filename'])."'  target='_blank' >Bestand: ".basename($input['filename'])."</a>";
    }
    if($input['dosierId'])
    {
      echo "<a href=\"CRM_naw_dossierPrint.php?relid=".$input['relid']."&id=".$input['dosierId']."\" target=\"_blank\" >".maakKnop('pdf.png',array('size'=>16))."</a>";
    }
    ?>
    </div>
    </div>

    <?
    for($i=2;$i<5;$i++)
    {
    ?>
    <div class="formblock">
    <div class="formlinks">Extra bijlage</div>
    <div class="formrechts">
    <input type="file" name="filename<?=$i?>" size="50" ">
    </div>
    </div>
    <?
    }
    ?>

    <div class="formblock">
    <div class="formlinks"> </div>
    <div class="formrechts">

    <input type="button" onclick="document.editForm.action.value='verzenden';document.editForm.submit();" value="verzenden">

    </div>
    </div>
    <?
    }
    ?>


</form>

<script type='text/javascript'>
  <?=$customTemplate->getTemplateSelectAjax(null, array('parseData' => true, 'crm_id' => $input['relId']));?>
  <?=$customTemplate->getTemplateHtmlEditorAjax();?>
</script>




<br>
<?

echo '<div data-field="message-field">' . $message . '</div>';

echo template($__appvar["templateRefreshFooter"],$editcontent);


?>
