<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/24 13:35:57 $
 		File Versie					: $Revision: 1.11 $

 		$Log: bedrijfInstellingen.php,v $
 		Revision 1.11  2019/07/24 13:35:57  rvv
 		*** empty log message ***


naar RVV 20210120


*/
include_once("wwwvars.php");

$_SESSION["NAV"] = '';
$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

echo template($__appvar["templateContentHeader"], $content);

$velden = array(
  'fondskoersLockDatum',
  'smtpServer',
  'fondsEmail',
  'smtpUser',
  'smtpPwd',
  'tmpbulkorderlast',
  'ddbMailServer',
  'ddbMailUser',
  'ddbMailPasswd',
  'ddbOwnDomain',
  'smtpSecure',
  'smtpPort'
);

$cfg = new AE_config();
if ($_POST)
{
  $db = new DB();
  $data = $_POST;
  foreach ($velden as $veld)
  {
    if ($veld == 'fondskoersLockDatum')
    {
      $data['fondskoersLockDatum'] = jul2sql(form2jul($data['fondskoersLockDatum']));
    }

    $query = "SELECT ae_config.id,ae_config.value FROM ae_config WHERE field='$veld'";
    $db->SQL($query);
    $oldData = $db->lookupRecord();
    if ($oldData['value'] <> $data[$veld])
    {
      addTrackAndTrace('ae_config', $oldData['id'], 'value|' . $veld, $oldData['value'], $data[$veld], $USR);
    }
    $cfg->addItem($veld, $data[$veld]);
  }

}
else
{
  foreach ($velden as $veld)
  {
    $data[$veld] = $cfg->getData($veld);
  }
}
$data['fondskoersLockDatum'] = dbdate2form($data['fondskoersLockDatum']);

?>
  <form method="POST">


    <fieldset style="width: 600px;">
      <div class="formblock">
        <div class="formlinks"><label for="fondsEmail" title="fondsEmail"><?=vt("Fondskoers blokkeer datum")?>.</label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['fondskoersLockDatum']?>" name="fondskoersLockDatum"
                 id="fondskoersLockDatum"> (dd-mm-yyyy)
        </div>
      </div>
    </fieldset>

    <fieldset style="width: 600px;">
      <div class="formblock">
        <div class="formlinks"><label for="fondsEmail" title="fondsEmail"><?=vt("email adres voor nieuwe Fondsen")?>.</label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['fondsEmail']?>" name="fondsEmail" id="fondsEmail">
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?=vt("mailserver (SMTP)")?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['smtpServer']?>" name="smtpServer" id="smtpServer">
        </div>
      </div>

      <?
      if ($__appvar["bedrijf"] != "HOME")
      {
        ?>
        <div class="formblock">
          <div class="formlinks"><label for="body" title="body"><?=vt("mailserver user (SMTP)")?></label></div>
          <div class="formrechts">
            <input type="text" value="<?=$data['smtpUser']?>" name="smtpUser" id="smtpUser">
          </div>
        </div>
        <div class="formblock">
          <div class="formlinks"><label for="body" title="body"><?=vt("mailserver password (SMTP)")?></label></div>
          <div class="formrechts">
            <input type="password" value="<?=$data['smtpPwd']?>" name="smtpPwd" id="smtpPwd">
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="body" title="body"><?=vt("mailserver secure")?> </label></div>
          <div class="formrechts">
            <input type="text" value="<?=$data['smtpSecure']?>" name="smtpSecure" id="smtpSecure">&nbsp;&nbsp;<?=vt("(tls, ssl of leeg)")?>
          </div>
        </div>

        <div class="formblock">
          <div class="formlinks"><label for="body" title="body"><?=vt("mailserver poort")?>.</label></div>
          <div class="formrechts">
            <input type="text" value="<?=$data['smtpPort']?>" name="smtpPort" id="smtpPort">
          </div>
        </div>
        <?
      }
      ?>
    </fieldset>
    <fieldset style="width: 600px;">
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?=vt("Laatste bulkorder nummer")?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['tmpbulkorderlast']?>" name="tmpbulkorderlast" id="tmpbulkorderlast">
        </div>
      </div>
    </fieldset>

    <fieldset style="width: 600px;">
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?=vt("documenten MailServer")?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['ddbMailServer']?>" name="ddbMailServer" id="ddbMailServer">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?=vt("Mailbox gebruikersnaam")?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['ddbMailUser']?>" name="ddbMailUser" id="ddbMailUser">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?=vt("Mailbox wachtwoord")?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['ddbMailPasswd']?>" name="ddbMailPasswd" id="ddbMailPasswd">
        </div>
      </div>
      <div class="formblock">
        <div class="formlinks"><label for="body" title="body"><?= vt('Eigen domein'); ?></label></div>
        <div class="formrechts">
          <input type="text" value="<?=$data['ddbOwnDomain']?>" name="ddbOwnDomain" id="ddbOwnDomain">
        </div>
      </div>
    </fieldset>

  <br/>
  <br/>

    <input type="submit" value="<?=vt("Opslaan")?>">
  </form>
<?


echo template($__appvar["templateRefreshFooter"], $content);


?>