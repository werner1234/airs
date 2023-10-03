<?php
/*
    AE-ICT sourcemodule created 12 jun. 2020
    Author              : Chris van Santen
    Filename            : help.php

*/

include_once("wwwvars.php");
debugSpecial("test");
session_start();
$_SESSION["submenu"] = "";
$_SESSION["NAV"] = "";
session_write_close();

$buildInfoFile = "../config/buildinfo.txt";
$build = array();
if (file_exists($buildInfoFile))
{
  $bRaw = file($buildInfoFile);

  $exp = explode("=",$bRaw[0]);
  $branch = trim($exp[1]);
  $exp = explode("=",$bRaw[1]);
  $branchdate = trim($exp[1]);
}
$buildInfo =  "datum: <b>{$branchdate}</b>";
$buildInfo .= "<br>image: <b>{$branch}</b>";
$buildInfo .= "<br>sc: <b>".session_cache_expire()."</b>";

$content = array();
echo template($__appvar["templateContentHeader"],$content);

if ($_GET["bCode"])
{
  file_put_contents('../config/bedrijf.ini', $_GET["bCode"]);
  echo "<script>window.parent.location.reload();</script>";
  exit;
}

if ($_GET["tCode"])
{
  session_start();
  $_SESSION["appTaal"] = $_GET["tCode"];
  session_commit();
  fillVT();
}

if($_GET['action']=='testmail')
{
  include_once('../classes/AE_cls_phpmailer.php');
  logScherm("Testmail",true);
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->SMTPDebug=9;
  $mail->From     = 'info@airs.nl';
  $mail->FromName = 'testmail '.$__appvar['bedrijf'];
  $mail->Body    = "Testmail";
  $mail->AltBody = "Testmail";
  $mail->AddAddress('info@airs.nl','Theo');
  $mail->Subject = 'testmail '.date('d-m-Y H:i');
  $mail->Send();
  echo $mail->ErrorInfo;
  listarray($mail->smtp);
     
}


if($_GET['action']=='debuglogs')
{
	$db=new DB();
	$query="SELECT id,txt,`date`,add_user FROM ae_log WHERE `date` > (now()- interval 48 hour) ORDER BY `date` desc limit 5000";
	$db->SQL($query);
	$db->query();
	$html="PHP ".PHP_VERSION."<br>\n<table><tr><td>id</td><td>txt</td><td>date</td><td>add_user</td></tr>\n";
	while($data=$db->nextRecord())
	{
		$html.="<tr><td>".$data['id']."</td><td>".$data['txt']."</td><td>".$data['date']."</td><td>".$data['add_user']."</td></tr>";
	}
	$html.='</table>';
	include_once('../classes/AE_cls_phpmailer.php');
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->From     = 'info@airs.nl';
	$mail->FromName = 'testmail '.$__appvar['bedrijf'];
	$mail->Body    = $html;
	$mail->AltBody = strip_tags($html);
	$mail->AddAddress('info@airs.nl','Airs');
	$mail->Subject = 'Debug log '.$__appvar['bedrijf']." ".date('d-m-Y H:i');
	if($mail->Send())
		logScherm("Logs verzonden.");
	else
  {
    logScherm("Niet gelukt om logs te verzenden.");
    echo $html;
  }
	echo "<br>\n";
}




?>

<style>

.k:active,
.k:link,
.k:visited{
	display: block;
	width: 120px;
	color: Black;
	font: 11px 'Arial';
	text-decoration: NONE;
	background-color: #FFFFF0;
	border: 1px solid;
	border-color: #DCDCDC #DCDCDC #AAAAAA #AAAAAA;
	text-align: center;
}

.k:hover {
	display: block;
	width: 120px;
	border: 1px solid;
	border-color: #AAAAAA #AAAAAA #DCDCDC #DCDCDC;
	background-color: #FFDEAD;
	color: Black;
}
.helpLink{
  border: 1px #999 solid;
  padding: 3px;
  border-radius: 3px;
  background: #EEE;
}
  .helpLink:hover{
    cursor: pointer;
  }

</style>

<?php
//  $ms = new AE_cls_Morningstar();
//  echo $ms->allowed(1,3,6)?"access":"denied";

?>

<b><?=$PRG_NAME?> <?= vt('systeem informatie'); ?>!</b><br><br>

<div class="formblock">
	<div class="formlinks"><?= vt('Programma versie'); ?>
	</div>
	<div class="formrechts">
		<?=$PRG_VERSION?> (<?=$PRG_RELEASE?>) (<?=$__appvar['bedrijfsnummer']?>)  <?= vt('op PHP'); ?> <?=PHP_VERSION?>
	</div>
</div>

  <div class="formblock">
    <div class="formlinks">Buildinfo
    </div>
    <div class="formrechts">
      <?=$buildInfo?>
    </div>
  </div>

<div class="formblock">
	<div class="formlinks"><?= vt('Geregistreerd als bedrijf'); ?>
	</div>
	<div class="formrechts">
		<?=$__appvar['bedrijf']?> / <?=getVermogensbeheerderField("Vermogensbeheerder")?>
	</div>
</div>


<div class="formblock">
	<div class="formlinks"><?= vt('Installatie directory'); ?>
	</div>
	<div class="formrechts">
		<?=$__appvar['basedir']?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Uw IP adres'); ?>
	</div>
	<div class="formrechts">
		<?=$_SERVER["REMOTE_ADDR"]?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Uw login naam'); ?>
	</div>
	<div class="formrechts">
		<?=$USR?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Geladen modules'); ?>
	</div>
	<div class="formrechts">
		<?=implode(", ",$__modules);?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Toegangcontrole'); ?></div>
	<div class="formrechts">
<?
  if (PHP_OS == "Linux")
	{
		echo ($__appvar["tgc"] == "enabled")?"ingeschakeld":"uitgeschakeld";
	}
  else
	{
		echo vt("Alleen beschikbaar op een Linux server");
	}

?>
	</div>

<?
// call 7043
if(checkAccess("superapp") || $_SESSION['usersession']['gebruiker']['Gebruikersbeheer'])
{
?>
</div>
  <div class="formblock">
    <div class="formlinks"><?= vt('IP toegang'); ?>
    </div>
    <div class="formrechts">
      <a href="helpIPacess.php" class="helpLink"><?= vt('lijst van vertrouwde IP adressen'); ?></a>

  </div>
</div>
<?
}
?>
<div class="formblock">
	<div class="formlinks"><?= vt('HTML rapportage'); ?>:
	</div>
	<div class="formrechts">
     <?=getVermogensbeheerderField("HTMLRapportage")?vt("ingeschakeld"):vt("niet actief");?>
	</div>
</div>

<div class="formblock">
    <div class="formlinks"><?= vt('Morning star'); ?> </div>
    <div class="formrechts">
<?
        echo getVermogensbeheerderField("morningstar");
?>
    </div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('FIX module'); ?>
	</div>
	<div class="formrechts">
<?
  if (DBFix == 99)
  {
	  $msg = vt("aktief, code <b>").$__FIX["bedrijfscode"]."</b>, " . vt('connectie is') . " <b>";
//	  $fix = new AE_FIXtransport();
//	  $msg .=  ($fix->testConnection())?"<span style='color: green'>online</span>":"<span style='color: red'>offline</span>";
	  echo $msg;
  }
  else
  {
	  echo vt("niet aktief");
  }
  
?>	
	</div>
</div>




<div class="formblock">
	<div class="formlinks"><?= vt('bestanden'); ?>
	</div>
	<div class="formrechts">
		<a class="helpLink" href="aeFilehistory.php?ext=php"><?= vt('programma'); ?></a> | <a class="helpLink" href="aeFilehistory.php?ext=html"><?= vt('template'); ?></a> | <a class="helpLink" href="aeFilehistory.php?ext=js"><?= vt('scripts'); ?></a>
	</div>
</div>
<a href="tmpManager.php">+</a>
<a href="help.php?action=testmail"><?= vt('testmail'); ?></a>
 
<BR><br><BR><br>
<div align="center">
<?php
	if($__appvar["bedrijf"] == "TEST")
	 echo '<a href="aehelper.php?rapportageInstellingenInlezen=1" class="helpLink" target="content"  >' . vt('rapportage instellingen inlezen') . '</a> <br/><br/>'
?>
	<a href="aehelper.php?rapportageInstellingenVerzenden=1" class="helpLink" target="content"  ><?= vt('verstuur rapportage instellingen'); ?></a> <br/><br/>
	<a href="help.php?action=debuglogs" class="helpLink" target="content"  ><?= vt('verstuur debug logs'); ?></a> <br/><br/>
	<a href="aehelper.php" class="helpLink" target="content"  ><?= vt('verstuur rapport naar helpdesk'); ?></a> <br/><br/>(<?= vt('deze knoppen alleen op aanwijzing van de helpdesk gebruiken'); ?>)
</div>
<?

if (file_exists('../config/bedrijf.ini'))
{
?>
<style>
  .bsel{
    margin:10px;
    padding: 10px;
    background: #eee;
    line-height: 20px;
  }
  .bsel:hover{
  
    background: beige;
    
  }
</style>
  <fieldset style='width:300px'>
     <legend style='background: #333; color:white; padding:5px;'> <?= vt('schakel bedrijf'); ?> </legend>
     <br/>
     <?= vt('Huidig bedrijf'); ?>: <b><?=$__appvar["bedrijf"]?></b><br/>
     <br/>
     <br/>
    <a class='bsel' href="help.php?bCode=HOME"><?= vt('Naar HOME'); ?></a> &nbsp;&nbsp;&nbsp;&nbsp;    <a class='bsel' href="help.php?bCode=TEST"><?= vt('Naar TEST'); ?></a><br/><br/>
  </fieldset>

  <fieldset style='width:300px'>
    <legend style='background: #333; color:white; padding:5px;'> <?= vt('schakel taal'); ?> </legend>
    <br/>
    <?= vt('Huidige taal'); ?>: <b><?=$_SESSION["appTaal"]?></b><br/>
    <br/>
    <br/>
    <a class='bsel' href="help.php?tCode=NL"><?= vt('Nederlands'); ?></a><br/><br/><br/>
    <a class='bsel' href="help.php?tCode=EN"><?= vt('Engels'); ?></a><br/><br/><br/>
    <a class='bsel' href="help.php?tCode=FR"><?= vt('Frans'); ?></a><br/><br/><br/>
  </fieldset>
<?  
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>