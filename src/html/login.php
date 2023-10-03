<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/05/18 14:57:27 $
 		File Versie					: $Revision: 1.15 $
*/
$disable_auth = true;
include_once("wwwvars.php");
session_start();
include_once $__appvar["basedir"].'/classes/AE_cls_secruity.php';

$sec = new AE_cls_secruity($USR);
$tgc = new AE_cls_toegangsControle();


if ($tgc->blacklisted OR ($tgc->loginTry < $_SESSION["loginCount"]))
{
//  session_destroy();
//  session_write_close();
  header("Location: blocked.php?ref=login:".__LINE__);
  exit;
}

//debug($sec);
$content = array();

if ($_POST['login'] == "true")
{

  // username is max 10 lang in db en mag geen spaties bevatten
  $usernameParts = explode(" ",substr($_POST['username'],0,10));
  $username = $usernameParts[0];



	setcookie ( "username" , $username, time()+(86400*365) );

	if(login($username,$_POST['password'],$_POST["sms"],$_POST["smsCode"]))
	{
    header("BTR_SESSION_ID: ".session_id());
    $tgc->logEntry("login","Ingelogd door ".$_POST['username']);
    header("Location: index.php");
    $_SESSION["loginCount"] = 0;
	}
	else
	{
    $_SESSION["loginCount"]++;
    $_SESSION["loginNames"][] = $_POST['username'];
    $tgc->logEntry("login","Ongeldige login van gebruiker ".$_POST['username']);
    if (!$tgc->trackLogins($_SESSION["loginCount"]))
    {
      header("Location: blocked.php?ref=login:".__LINE__);
      exit;
    }

		header("Location: login.php?fout=true");
	}

}
else if ($_GET['logout'] == "true")
{

	session_start();
  $tgc->logEntry("logout","Uitgelogd door ".$_SESSION["usersession"]["user"]);
	session_destroy();
	session_write_close();
	header("Location: index.php");
}
else
{
  $cfg = new AE_config();
  $override = ($cfg->getData("wwBeleid_2factor_override") == "1");
  $twoWay = ($sec->twoFactor AND $_GET["su"] <> "true" AND !$override);  // SMS
  $tfa    = ($sec->tfa AND $_GET["su"] <> "true" AND !$override);        // Google
  $showMsg = false;



  if ($twoWay)
  {
      $twoWayText = vt("Houd uw mobiele telefoon bij de hand voor de SMScode");
      $showMsg = true;
      $smsSent = "true";
  }
  else if ($tfa)
  {
    $twoWayText = vt("Houd uw mobiele telefoon bij de hand met de Google Auth app");
    $showMsg = true;
    $smsSent = "true";
  }
  else
  {
    $twoWayText = "";
    $smsSent = "false";
  }

  if ($sec->twoFactor AND $override)
  {
    $twoWayText = vt("SMS diensten zijn wegens een storing offline");
    $showMsg = true;
  }

  if ($sec->tfa AND $override)
  {
    $twoWayText = vt("Twee factor diensten zijn wegens een storing offline");
    $showMsg = true;
  }

  if ($_GET["fout"] == true)
  {
    $twoWayText = vt("Verkeerde loginnaam en/of wachtwoord, probeer opnieuw");
    $showMsg = true;
  }


  $content["jsincludes"] = '
    <script type="text/javascript" src="javascript/jquery-min.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
    <script language=JavaScript src="javascript/algemeen.js" type=text/javascript></script>
';
	echo template($__appvar["templateContentHeader"],$content);
?>
<script type="text/javascript">
if (top.frames.length > 0)
{
	top.location="login.php";
}
</script>
<style>
  #loginBox{
    margin:0 auto;
    border: 1px solid #666;
    width: 400px;
    padding:10px;
    min-height: 240px;

  }
  .header{
    margin:0 auto;
    padding:3px;
    border: 1px solid #666;
    width: 414px;
    background: rgba(20,60,90,1);
    color: white;
    border-radius: 10px 10px 0 0;
    text-align: center;
    font-weight: bold;
    font-size: 1.2em;

  }
  .frmRow{
    float: left;
    margin: 1px;
    margin-left: 13px;
    width: 100%;
    padding:2pt;
    font-size:12px;
  }
  .frmLeft{
    float: left;
    width:110px;
    padding: 4px;
  }
  .frmRight{
    float: left;
    width: 80px;
  }
  .frmRight input{
    border:1px solid #999;
    box-shadow: 2px 2px 5px 0 #808080;
    padding: 4px;

  }
  .loginBtn{
    margin-top:15px;
    background: rgba(20,60,90,1);
    padding: 5px 15px;
    border: 0;
    color:white;
    min-width: 180px;
  }
  .loginBtn:hover{
    background: #666;
    color:white;
    cursor: pointer;

  }
  #message{
    border-radius:5px;
    width: 90%;
    padding:5px;
    background: beige;
    display:none;
    color:red;
    text-align: center;
    font-size: 1.2em;
  }
  #smsDialog{
    display:none;
  }
  #smsValid{
    display:none;
  }
  #smsOffline{
    display:none;
  }
</style>
<br/>
<br/>

<form action="login.php" method="post" id="loginForm">
		<input type="hidden" name="login" value="true"/>
		<input type="hidden" name="sms" id="sms" value="<?=$smsSent?>"/>

<div class="header">
  <?=vt("Vermogensbeheer applicatie login (poging")?> <?=$_SESSION["loginCount"]+1?>)
</div>
<div id="loginBox">

  <div id="message"><?=$twoWayText?></div>
  <div class="frmRow">
    <div class="frmLeft"><?=vt("gebruikersnaam")?> </div>
    <div class="frmRight">
      <input type="text" name="username" id="username" value="<?=$_COOKIE["username"]?>" size="20"/>
    </div>
  </div>

  <div class="frmRow">
    <div class="frmLeft"><?=vt("wachtwoord")?> </div>
    <div class="frmRight">
      <input type="password" name="password" id="password" value="" size="20"/>
    </div>
  </div>

  <div class="frmRow">
    <div class="frmLeft">&nbsp; </div>
    <div class="frmRight">
      <button class="loginBtn" id="btnFase1"><?=vt("aanmelden")?></button>
    </div>
  </div>
  <div id="smsDialog">
    <div class="frmRow">
    <div class="frmLeft"><?=vt("uw SMS code")?> </div>
    <div class="frmRight">
      <input type="text" name="smsCode" id="smsCode" value="" size="20"/>
    </div>
    </div>
    <div class="frmRow">
      <div class="frmLeft">&nbsp; </div>
      <div class="frmRight">
        <button class="loginBtn" id="btnFase2"><?=vt("inloggen")?> </button>
      </div>
    </div>
  </div>
  <div id="smsValid">
    <div class="frmRow">
      <div class="frmLeft">&nbsp; </div>
      <div >
        <?=vt("Uw eerdere SMS validatie is nog geldig, klik op inloggen")?>
      </div>
    </div>

    <div class="frmRow">
      <div class="frmLeft">&nbsp; </div>
      <div class="frmRight">
        <button class="loginBtn" id="btnFase3"> <?=vt("inloggen")?> </button>
      </div>
    </div>
  </div>
  <div id="smsOffline">
    <div class="frmRow">
      <div class="frmLeft">&nbsp; </div>
      <div >
        <?=vt("SMS dienst offline, klik op inloggen")?>
      </div>
    </div>

    <div class="frmRow">
      <div class="frmLeft">&nbsp; </div>
      <div class="frmRight">
        <button class="loginBtn" id="btnFase4"> <?=vt("inloggen")?> </button>
      </div>
    </div>
  </div>
</div>

</form>

<script>
  $(document).ready(function(){
    var fingerPrint = "<?=$sec->fingerPrint?>";
<?
    if ($showMsg)
    {
?>
    $("#message").show(300);
<?
    }
?>
    $("#btnFase1").click(function(e){
      e.preventDefault();
      $("#message").hide();
      if ($("#username").val() == "" || $("#password").val() == "" )
      {
        $("#message").html("<?=vt("gebruikersnaam en/of wachtwoord mogen niet leeg zijn!")?>").show(300);
      }
      else
      {
<?
         if ($twoWay )
         {
?>
        $("#btnFase1").text("<?=vt("moment aub")?>");
        $("#btnFase1").attr("disabled","disabled");
          $.ajax(
          {
            type: "POST",
            url: "lookups/wwb-smsRequest.php",
            data: {
              user: $("#username").val(),
              passwd: $("#password").val(),
              location: "<?=$__appvar["bedrijf"]?>",
              fingerPrint: fingerPrint,
              klassiek: "<?=($sec->beleid == "")?"on":"off"?>"
            }
          }).done(function( msg )
          {
            console.log(msg);
            switch (msg)
            {
              case "invalidLogin":
                $("#message").html("<?=vt("Verkeerde loginnaam en/of wachtwoord, probeer opnieuw")?>").show(300);
                $("#btnFase1").text("<?=vt("opnieuw aanmelden")?>");
                $("#btnFase1").removeAttr("disabled");
                $("#username").select().focus();
                break;
              case "validSMS":
                $("#btnFase1").text("<?=vt("SMS validatie")?>");
                //$("#message").html("SMS nog niet gestuurd naar: " + msg).show(300);
                $("#smsCode").val("");
                $("#smsValid").show(300);
                $("#btnFase3").focus();
                $("#sms").val("false");
                break;
              case "smsOffline":
                $("#btnFase1").text("<?=vt("SMS validatie")?>");
                //$("#message").html("SMS nog niet gestuurd naar: " + msg).show(300);
                $("#smsCode").val("");
                $("#smsOffline").show(300);
                $("#btnFase4").focus();
                $("#sms").val("false");
                break;
              case "reuseSMS":
                $("#btnFase1").text("<?=vt("Voer uw laatste SMS code in")?>");
                $("#message").html("<?=vt("Uw laatste SMS code is nog geldig")?>").show(300);
                $("#smsCode").val("");
                $("#smsDialog").show(300);
                $("#smsCode").select().focus();
                $("#sms").val("true");
                break;
              default:
                $("#btnFase1").text("<?=vt("wacht op SMS code")?>");
                $("#message").html("<?=vt("SMS gestuurd naar:")?> " + msg).show(300);
                $("#smsCode").val("");
                $("#smsDialog").show(300);
                $("#smsCode").select().focus();
                $("#sms").val("true");
                break;


            }
          });
<?
         }
         else
         {
?>
               $("#loginForm").submit();
<?
         }
?>
      }
    });
    $("#btnFase3").click(function(e){
      e.preventDefault();
      console.log("fase3");
      $("#loginForm").submit();
    });
    $("#btnFase4").click(function(e){
      e.preventDefault();
      console.log("fase4");
      $("#loginForm").submit();
    });
    $("#btnFase2").click(function(e){
      e.preventDefault();
      console.log("fase2");
      if ($("#smsCode").val() == "")
      {

        $("#message").hide(300).html("<?=vt("SMS code niet ingevuld")?> ").show(300);
        $("#smsCode").select().focus();
      }
      else
      {
        $("#loginForm").submit();
      }
    });

  });




</script>
<?php
	echo template($__appvar["templateContentFooter"],$content);
}
?>