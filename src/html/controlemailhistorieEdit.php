<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 10 mei 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: controlemailhistorieEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2015/02/11 16:44:19  rvv
    *** empty log message ***

    Revision 1.1  2014/05/10 13:53:42  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "controlemailhistorieList.php";
$__funcvar['location'] = "controlemailhistorieEdit.php";

$object = new ControleEmailHistorie();



$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

if($action=='email')
{
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  if($mailserver <> '')
  {
    $object->getById($data['id']);
    include_once('../classes/AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $data['email'];
    $mail->FromName = "Airs";
    $mail->Body    = $object->data['fields']['body']['value'];
    $mail->AltBody = html_entity_decode(strip_tags($object->data['fields']['body']['value']));
   	$mail->AddAddress($data['email']);
    $mail->Subject = $object->data['fields']['onderwerp']['value'];
    $mail->Host=$mailserver;
    
    if(!$mail->Send())
    {
      echo vt("Verzenden van e-mail mislukt.");
    }
    else
    {
      echo vt("E-mail verzonden.");
    }
  }
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

$body=$editObject->getOutput();

echo str_replace('</form></div>',
'
</form></div>

<div>
<form>
<input type="hidden" name="action" value="email">
<input type="hidden" name="id" value="'.$object->get('id').'">
<div class="formblock">
<div class="formlinks">' . vt('Verzenden aan') . '</div>
<div class="formrechts">
<input class="" type="text" size="20" value="" name="email" id="email" value="'.$_SESSION['usersession']['gebruiker']['emailAdres'].'">
<input type="submit" value="Verzenden">
</div>
</div>
</form></div>

',$body);



if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>