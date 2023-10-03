<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/10/27 08:54:35 $
 		File Versie					: $Revision: 1.2 $
*/
include_once("wwwvars.php");
ini_set('default_charset', 'utf-8');

$_SESSION['NAV']='';
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>$mainHeader</b> $subHeader</div><br><br>";
$msg = "stel hier uw mailbox in";
if ($_GET["initdb"] == 1)
{
  include_once("../classes/AE_cls_Email.php");
  $mail = new AE_cls_Email();
  $mail->initTables();
  $msg = "Database is bijgerwerkt";
}


$cfg=new AE_config();
if($_POST)
{
  $data=$_POST;
  $cfg->addItem('ddbMailServer',$data['ddbMailServer']);
  $cfg->addItem('ddbMailUser',$data['ddbMailUser']);
  $cfg->addItem('ddbMailPasswd',$data['ddbMailPasswd']);
  $cfg->addItem('ddbOwnDomain',$data['ddbOwnDomain']);

  header("location: dd_inlees_email.php");
  exit;
}
else
{
  $data['ddbMailServer']=$cfg->getData('ddbMailServer');
  $data['ddbMailUser']=$cfg->getData('ddbMailUser');
  $data['ddbMailPasswd']=$cfg->getData('ddbMailPasswd');
  $data['ddbOwnDomain']=$cfg->getData('ddbOwnDomain');
}
echo template($__appvar["templateContentHeader"],$content);
?>

  <style>
  legend{
    background: #333;
    color: white;
    width:25%;
    padding:3px;
  }
</style>
<h1>Instellingen mailbox</h1>

<?php

if ( isset ($__appvar['office365']) ) {
  echo '<p>' . vt('E-mail is ingesteld via Exchange Online.') .' </p>';

  ?>
  <form method="POST">

    <fieldset style="width: 600px;">
      <legend>Instellingen</legend>

      <div class="formblock">
        <div class="formlinks"><label for="body" title="body">Mailbox</label></div>
        <div class="formrechts">
          <input type="text" value="<?=$__appvar['office365']['ddbExchangeMailbox']?>" readonly style="background-color: lightgray;">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body">Eigen domein</label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['ddbOwnDomain']?>" name="ddbOwnDomain" id="ddbOwnDomain">
        </div>
      </div>
    </fieldset>
    <br/>
    <br/>

    <input type="submit" value="Opslaan">
  </form>


  <?

} else {

?>
<div style="color:red; background: beige; padding: 10px; max-width: 605px"><?=$msg?></div>
<form method="POST">

  <fieldset style="width: 600px;">
    <legend>IMAP</legend>
    <div class="formblock">
      <div class="formlinks"><label for="body" title="body">documenten MailServer</label></div>
      <div class="formrechts">
        <input type="text" value="<?=$data['ddbMailServer']?>" name="ddbMailServer" id="ddbMailServer" style="width: 300px;">
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks"><label for="body" title="body">Mailbox gebruikersnaam</label></div>
      <div class="formrechts">
        <input type="text" value="<?=$data['ddbMailUser']?>" name="ddbMailUser" id="ddbMailUser">
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks"><label for="body" title="body">Mailbox wachtwoord</label></div>
      <div class="formrechts">
        <input type="text" value="<?=$data['ddbMailPasswd']?>" name="ddbMailPasswd" id="ddbMailPasswd">
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks"><label for="body" title="body">Eigen domein</label></div>
      <div class="formrechts">
        <input type="text" value="<?=$data['ddbOwnDomain']?>" name="ddbOwnDomain" id="ddbOwnDomain">
      </div>
    </div>
  </fieldset>
  <br/>
  <br/>

  <input type="submit" value="Opslaan">
  </form>


  <?
}

echo template($__appvar["templateRefreshFooter"],$content);

function vulOptions($srcArray, $value="")
{
  $out = "";
  foreach ($srcArray as $k=>$v)
  {
    $selected = ($k == $value)?"SELECTED":"";
    $out .= "\n\t<option value='$k' $selected>$v</option>";
  }
  return $out;
}
?>