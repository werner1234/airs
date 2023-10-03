<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.3 $

 		$Log: kwartaalBriefEdit.php,v $
 		Revision 1.3  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/01/20 17:46:01  rvv
 		*** empty log message ***
*/
include_once("wwwvars.php");
session_start();
$cfg = new AE_config();
$data = array_merge($_POST,$_GET);

if($_GET['brief'])
  $brief= $_GET['brief'];
elseif ($_POST['briefType'])
  $brief= $_POST['briefType'];
else
 $brief='kwartaalBrief';

function addOrReplace($field)
{
  global $data;
  global $cfg;
  if (!$cfg->getData($field))
    $cfg->addItem($field,$data[$field]);
  else
    $cfg->putData($field,$data[$field]);
}

if ($data['action'] == "process")
{
  addOrReplace($_POST['briefType']);
  if($data['titel'])
   addOrReplace($data['titel']);
}

$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");

$editcontent['javascript'].=
"function loadEditor(textarea)
{
  var sBasePath = 'javascript/';
  var oFCKeditor = new FCKeditor( textarea ) ;
  //oFCKeditor.Config['CustomConfigurationsPath'] = sBasePath + 'ae_fckconfig.js';
  oFCKeditor.BasePath = sBasePath;
  oFCKeditor.Height	= 400;
  oFCKeditor.Width		= 600;
  oFCKeditor.ReplaceTextarea();
}";

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = 'rapportBackofficeClientSelectie.php';//rapportSelectie.php?type=kwartaalRapportage
$_SESSION['NAV']->addItem(new NavEdit("editForm",true,false,true));


$_SESSION['submenu'] = New Submenu();
if($brief == 'kwartaalBrief')
  $_SESSION['submenu']->addItem('Genereer voorbeeld','kwartaalBriefPreview.php',array('target'=>'_blank'));


$editcontent['body'].="onLoad=\"doEditorOnload()\"";
echo template($__appvar["templateContentHeader"],$editcontent);


?>

 <script type="text/javascript" src="javascript/fckeditor.js"></script>
<style>
.formlinks{
width: 200px;
}
</style>

<div class="form">
<form name="editForm" action="<?=$PHP_SELF?>" method="POST">
<input type="hidden" name="briefType" value="<?=$brief?>">
<input type="hidden" value="process" name="action">
<?
if($data['titel'])
{
?>
<input type="hidden" name="titel" value="<?=$data['titel']?>">
<br><br>
<div class="formblock">
<div class="formlinks"> Onderwerp</div>
<div class="formrechts">
<input size="80"  name="<?=$data['titel']?>" id="<?=$data['titel']?>" value="<?=$cfg->getData($data['titel'])?>">
</div>
</div>
<?
}
?>

<div class="formblock">
<div class="formlinks"> </div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="10" name="<?=$brief?>" id="<?=$brief?>" ><?=$cfg->getData($brief);?></textarea>
</div>
</div>



</form></div>
<script language="JavaScript" type="text/javascript">
function doEditorOnload()
{
 loadEditor('<?=$brief?>');
}
</script>



</form>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>