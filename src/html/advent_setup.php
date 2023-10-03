<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/11/15 10:22:21 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_setup.php,v $
 		Revision 1.1  2013/11/15 10:22:21  cvs
 		aanpassing tbv Adventexport
 		
 	
*/

include_once("wwwvars.php");
include_once("../config/advent_functies.php");
session_start();

$admin = getRights("admin");

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = $PHP_SELF;
if ($admin)
  $_SESSION['NAV']->addItem(new NavEdit("editForm",true,false,false));
else  
  $_SESSION['NAV']->addItem(new NavEdit("editForm",false,false,false));

$_SESSION['submenu'] = "";

$cfg = new AE_config();

$data = $_POST;
if ($_POST)
{
  //listarray($data);
  // extra bewerkingen om path geldig te maken
  //
  $tmp = $data["outputDir"];
  $tmp = str_replace("\\","/",$tmp );  // windows slashes vervangen
  if (substr($tmp,-1) <> "/") $tmp .= "/";                         // controle eindslash
  $data["outputDir"] = $tmp;
  
}


function addOrReplace($field)
{
  global $data;
  global $cfg;
  if (!$cfg->getData($field))
    $cfg->addItem($field,$data[$field]);
  else
    $cfg->putData($field,$data[$field]);
}

if ($data[action] == "process")
{
  addOrReplace("advent_outputDir");
  addOrReplace("advent_deleteAfterDownload");
/*  addOrReplace("fileFormat");
  addOrReplace("straOutput");
  addOrReplace("ctraOutput");
  addOrReplace("codeAfname");
  addOrReplace("codeToename");
  addOrReplace("defaultSubtype");
  addOrReplace("defaultSubtype_SECTRANS");
*/  
  header("location: welcome.php?action=setup");

}

$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");


echo template($__appvar["templateContentHeader"],$editcontent);

$headTxt = $admin?"aanpassen":"bekijken";

?>


<style>
.formlinks{
width: 200px;
}
</style>

<form action="<?=$PHP_SELF?>" method="POST" name="editForm">
<input type="hidden" value="process" name="action">
<br>
<b>&nbsp;&nbsp;<?= vt('instellingen'); ?> <?=$headTxt?></b><br><br>

<div class="formblock">
	<div class="formlinks"><?= vt('uitvoer directory'); ?>
	</div>
	<div class="formrechts">
		<input size="65" name="advent_outputDir" value="<?=$cfg->getData("advent_outputDir");?>" /> 
	</div>
</div>
<div class="formblock">
	<div class="formlinks"><?= vt('bestanden verwijderen na download?'); ?>
	</div>
	<div class="formrechts">
		<input  type="radio" name="advent_deleteAfterDownload" value="ja" <?=($cfg->getData("advent_deleteAfterDownload") == "ja")?"checked":""; ?>/> <?= vt('Ja'); ?>
		<input  type="radio" name="advent_deleteAfterDownload" value="nee" <?=($cfg->getData("advent_deleteAfterDownload") == "nee")?"checked":""; ?>/> <?= vt('Nee'); ?>
	</div>
</div>
<div class="formblock">
<!--
<div class="formlinks">Bestand formaat
	</div>
	<div class="formrechts">
		<input  type="radio" name="fileFormat" value="csv" <?=($cfg->getData("fileFormat") <> "fixed")?"checked":""; ?>/> .csv file 
		<input  type="radio" name="fileFormat" value="fixed" <?=($cfg->getData("fileFormat") == "fixed") ?"checked":""; ?>/> vaste breedte versie 5 </div>
</div>

<div class="formblock">
<div class="formlinks">CTRA output
	</div>
	<div class="formrechts">
		<input  type="radio" name="ctraOutput" value="file" <?=($cfg->getData("ctraOutput") == "file")?"checked":""; ?>/> naar file 
		<input  type="radio" name="ctraOutput" value="db" <?=($cfg->getData("ctraOutput") == "db")?"checked":""; ?>/> naar tussen tabel
	</div>
</div>
</div>
<div class="formblock">
<div class="formlinks">STRA output
	</div>
	<div class="formrechts">
		<input  type="radio" name="straOutput" value="file" <?=($cfg->getData("straOutput") == "file")?"checked":""; ?>/> naar file 
		<input  type="radio" name="straOutput" value="db" <?=($cfg->getData("straOutput") == "db")?"checked":""; ?>/> naar tussen tabel
	</div>
</div>
</div>

<div class="formblock">
<div class="formlinks">Code voor afname
	</div>
	<div class="formrechts">
		<input  type="text" name="codeAfname" value="<?=$cfg->getData("codeAfname")?>"  size="10" maxlength="10" /> 
	</div>
</div>
</div>
<div class="formblock">
<div class="formlinks">Code voor toename
	</div>
	<div class="formrechts">
		<input  type="text" name="codeToename" value="<?=$cfg->getData("codeToename")?>"  size="10" maxlength="10" /> 
	</div>
</div>
</div>
<fieldset>
  <legend>CASHTRANS specifiek</legend>

<div class="formblock">
<div class="formlinks">Code voor default subtype
	</div>
	<div class="formrechts">
		<input  type="text" name="defaultSubtype" value="<?=$cfg->getData("defaultSubtype")?>"  size="4" maxlength="4" /> 
	</div>
</div>
</div>
</fieldset>
<br />
<fieldset>
  <legend>SECURITYTRANS specifiek</legend>

<div class="formblock">
<div class="formlinks">Code voor default subtype
	</div>
	<div class="formrechts">
		<input  type="text" name="defaultSubtype_SECTRANS" value="<?=$cfg->getData("defaultSubtype_SECTRANS")?>"  size="4" maxlength="4" /> 
	</div>
</div>
</div>
</fieldset>
-->

</form>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>