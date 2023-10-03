<?php
include_once("wwwvars.php");

session_start();
$_SESSION['submenu'] = "";
$_SESSION['NAV'] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);


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
</style>
<b><?=$PRG_NAME?> <?= vt('Gebruikers informatie!'); ?></b><br><br>

<div class="formblock">
	<div class="formlinks"><?= vt('Programma versie'); ?><a href="?more=2">.</a>
	</div>
	<div class="formrechts">
		<?=$PRG_VERSION?> (<?=$PRG_RELEASE?>) (<?=$__appvar['bedrijfsnummer']?>)  op PHP <?=PHP_VERSION?>
	</div>
</div>

<div class="formblock">
	<div class="formlinks"><?= vt('Geregistreerd als bedrijf'); ?>
	</div>
	<div class="formrechts">
		<?=$__appvar['bedrijf']?>
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
	<div class="formlinks"><?= vt('FIX module'); ?>
	</div>
	<div class="formrechts">
<?
  if (DBFix == 99)
  {
	  $msg = "aktief, code <b>".$__FIX["bedrijfscode"]."</b>, connectie is <b>";
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
		<a href="aeFilehistory.php?ext=php"><?= vt('programma'); ?></a> | <a href="aeFilehistory.php?ext=html"><?= vt('template'); ?></a> | <a href="aeFilehistory.php?ext=js"><?= vt('scripts'); ?></a>
	</div>
</div>
<a href="tmpManager.php">+</a>
<a href="help.php?action=testmail"><?= vt('testmail'); ?></a>
 
<BR><br><BR><br>
<div align="center">
<a href="aehelper.php" class="k" target="content"  ><?= vt('verstuur rapport naar helpdesk'); ?></a> <?= vt('(deze knop alleen op aanwijzing van de helpdesk gebruiken)'); ?>
</div>
<?
if ($_GET["more"] == 1)
{
  echo $__positieImportMap;
  listarray($__appvar);
  listarray($_SESSION);
}
if ($_GET["more"] == 2)
{
  phpinfo();
}
if ($_GET["more"] == 3)
{
  echo "__positieImportMap = ".$__positieImportMap;
  $dh  = opendir($__positieImportMap);
  while (false !== ($filename = readdir($dh)))
  {
    $files1[] = $filename;
  }
  listarray($files1);

}

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
<?  
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>