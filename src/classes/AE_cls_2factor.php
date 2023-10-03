<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/07 06:47:06 $
 		File Versie					: $Revision: 1.3 $

 		$Log: AE_cls_2factor.php,v $
 		Revision 1.3  2020/05/07 06:47:06  cvs
 		no message
 		
 		Revision 1.2  2020/03/06 15:08:00  cvs
 		call 8437
 		


*/


/* implementatie

veld aanmaken in gebruikers tabel
ALTER TABLE `gebruikers` ADD COLUMN `secretTwoFactor` varchar(100) NOT NULL;

plaatsen in index.php direct na include wwwvars.php
if (!$_SESSION["2factor"]["passed"])
{
  include_once "../classes/AE_cls_2factor.php";
  $uId = $_SESSION["usersession"]["gebruiker"]["id"];
  $twof = new AE_cls_2factor($uId);
  //$twof->postUrl = "_a.php";
//debug($_POST);

  if ($_POST["action"] == "2factor")
  {

    if (!$twof->checkLogin($_POST))
    {
      header("location:".$PHP_SELF);
      exit;
    }
  }
  else
  {
    echo template($__appvar["templateContentHeader"],$content);
    echo $twof->pushLogin();
    echo template($__appvar["templateRefreshFooter"],$content);
    exit;
  }
}
*/


include_once('googleAuth/GoogleAuthenticator.php');

class AE_cls_2factor extends PHPGangsta_GoogleAuthenticator
{
  var $appName           = "AIRS ";
  var $userTable         = "Gebruikers";
  var $userField         = "Gebruiker";
  var $googleSecretField = "secretTwoFactor";
  var $postUrl           = "index.php";
  var $uId;
  var $uRec;
  var $uSecret = "";
  var $uName   = "";
  var $templ;
  var $cssTemplate = '
  <style>
    #twoFactorContainer{
      font-family: Verdana, Arial, sans-serif;
      width: 640px;
      min-height: 100px;
      margin: 0 auto;
      margin-top: 20px;
      background: #EEE;
      border: 2px solid #999;
      padding: 20px;
    
    }
    #twoFactorMsg{
      display: none;
      background: red;
      color: white;
      padding: 20px;
      width: 400px;
      margin: 0 auto;
    }
    .twoFactorBlock{
      padding: 20px;
      width: 90%;
      background: #666;
      color: #FFF;
    }
    .twoFactorRight{
      display: inline-block;
      width: 100%;
      text-align: center;
    }
    .twoFactorInput{
      font-size: 1.2rem; 
      text-align: center;
      width: 90px;
      padding: 5px;
    }
    #btnTwoFactorSubmit{
      
      padding: 10px 50px ;
    }
    .twoFactorBtnBar{
      width: 100%;
      text-align: center;
    }
    .twoFactorRegister{
      width: 50%;
      padding: 10px;
      margin: 0 auto;
      text-align: center;
    }
  </style>
  ';
  var $registerTemplate = '
  <div class="twoFactorRegister">
    <img src="{qrCode}"><br/>
    scan de QR code met uw telefoon app <br/>om de 2 factor te registeren.
    of registreer handmatig met code <b>{secretTwoFactor}</b>
  </div>
  ';
  var $loginTemplate = '
  
  <div id="twoFactorContainer">
    <h2>Twee factor, login (poging {poging})</h2>
    <form method="POST" action="{postUrl}" id="twoFactorForm">
      <input type="hidden" name="action" value="2factor" />
      <input type="hidden" name="pga" value="{secretTwoFactor}" />
      <input type="hidden" name="register"  value="{register}" />

      <div id="twoFactorMsg"></div>
      <br />
      {registerBlock}
      
      <div class="twoFactorBlock">
        <div class="twoFactorRight">
        Vul hier uw code in <input class="twoFactorInput" type="text" name="twoFactorVerify" id="twoFactorVerify" value=""/>
        </div>
      </div>
      <br />
      <div class="twoFactorBtnBar">
        <button id="btnTwoFactorSubmit">  Log in  </button> 
      </div>
    
  </form>
</div>

<script>
  $(document).ready(function(){
     {loginFailed}
     $("#twoFactorVerify").focus();
     $("#btnTwoFactorSubmit").click(function(e){
       
       var msg = "";
       e.preventDefault();
       if ($("#twoFactorVerify").val().trim() == "")      
       {  
         var msg = "<b>Foutmelding:</b><br/>geef de code vanuit de app";  
         $("#twoFactorMsg").html(msg);
         $("#twoFactorMsg").show();
       }
       else
       {
         $("#twoFactorForm").submit();
       }
         
     });
  });
</script>
  
  ';

  function AE_cls_2factor($uId = 0)
  {
    if ($uId == 0)
    {
      if ($_SESSION["2factor"]["uId"] > 0)
      {
        $this->uId = (int)$uId;
      }
    }
    else
    {
      $_SESSION["2factor"]["passed"] = false;
      $_SESSION["2factor"]["uId"] = (int)$uId;
      $this->uId = (int)$uId;
    }

    $this->getUserById($this->uId);
    $this->uSecret = $this->uRec[$this->googleSecretField];
    $this->uName   = $this->uRec[$this->userField];
    $this->templ = new AE_template();
    $this->templ->loadTemplateFromString($this->loginTemplate, "login");
    $this->templ->loadTemplateFromString($this->cssTemplate, "css");
    $this->templ->loadTemplateFromString($this->registerTemplate, "twoFactor");
  }

  function logout()
  {
    unset($_SESSION["2factor"]);
  }

  function pushLogin(){
    echo $this->templ->parseBlock("css");

    if ($this->uSecret != "")
    {

      echo $this->templ->parseBlock("login", array(
        "secretTwoFactor" => "",
        "register"        => "0",
        "uId"             => $this->uId,
        "registerBlock"   => "<h3>Geef de Google Autenticator code voor '".$this->appName."'</h3><br/><br/>",
        "postUrl"         => $this->postUrl,
        "poging"          => ++$_SESSION["2factor"]["poging"]
      ));
    }
    else
    {
      $secret = $this->createSecret();
//      $qr_code =  $this->getQRCodeGoogleUrl($this->uName, $secret, $this->appName);
      $qr_code =  $this->getQRCodeGoogleUrl($this->appName."(".$this->uName.")", $secret);
      $tf = $this->templ->parseBlock("twoFactor", array("qrCode" => $qr_code, "secretTwoFactor" =>$secret));
      echo $this->templ->parseBlock("login", array(
        "secretTwoFactor" => $secret,
        "register"        => "1",
        "registerBlock"   => $tf,
        "uId"             => $this->uId,
        "postUrl"         => $this->postUrl
      ));
    }
  }

  function checkLogin($req)
  {
    $secret = ($this->uSecret == "" )?$req["pga"]:$this->uSecret;
    if(!$this->verifyCode($secret, $req["twoFactorVerify"], 2))
    {
      $_SESSION["2factor"]["passed"] = false;
      return false;
    }
    else
    {
      if ($req["register"] == 1)
      {
        $this->writeSecretToUseraccount($req["pga"]);
      }
      $_SESSION["2factor"]["passed"] = true;
      return true;
    }


  }

  function getUserById($uId)
  {
    $db = new DB();
    $query = "SELECT * FROM `{$this->userTable}` WHERE `id` = '{$uId}'";
    $this->uRec = $db->lookupRecordByQuery($query);
  }

  function writeSecretToUseraccount($secret)
  {
    $db = new DB();

    $query = "UPDATE `{$this->userTable}` SET `{$this->googleSecretField}` = '{$secret}' WHERE`id` = '{$this->uId}'";
    $db->executeQuery($query);
  }

  function resetSecretToUseraccount()
  {
    $db = new DB();

    $query = "UPDATE `{$this->userTable}` SET `{$this->googleSecretField}` = '' WHERE`id` = '{$this->uId}'";
    $db->executeQuery($query);
  }


}