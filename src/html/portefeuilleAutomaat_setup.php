<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:08:56 $
 		File Versie					: $Revision: 1.1 $

 		$Log: portefeuilleAutomaat_setup.php,v $
 		Revision 1.1  2012/03/09 09:08:56  cvs
 		*** empty log message ***
 		

*/
include_once("wwwvars.php");
session_start();


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->returnUrl = $PHP_SELF;
$_SESSION['NAV']->addItem(new NavEdit("editForm",true,false,false));


$_SESSION['submenu'] = "";

$cfg = new AE_config();

$data = $_POST;
function addOrReplace($field)
{
  global $data;
  global $cfg;
  if (!$cfg->getData($field))
    $cfg->addItem($field,$data[$field]);
  else
    $cfg->putData($field,$data[$field]);
}

if ($data['action'] == "process" AND $data["mutatiecode"] == "airs")
{


  addOrReplace("portAutomaat_importmap");
  header("location: welcome.php?action=setup");
}


$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");


echo template($__appvar["templateContentHeader"],$editcontent);
if ($data[action] == "process")
{
  echo "<h3> " . vt('ongeldige mutatiecode, instellingen worden niet opgeslagen!') . " </h3>";
}
?>


<style>
.formlinks{
width: 200px;
}
</style>

<script>
function IsNumeric(sText)
{
  var ValidChars = "0123456789.";
  var Char;
  for (i = 0; i < sText.length; i++)
  {
    Char = sText.charAt(i);
    if (ValidChars.indexOf(Char) == -1)
    {
      return false;
    }
  }
  return IsNumber;
}



function checknumeric(number)
{
  if (IsNumeric(number.value) != true)
  {
    alert('moet een numeriek getal zijn');
    number.focus;
  }
}
</script>

<form action="<?=$PHP_SELF?>" method="POST" name="editForm">
<input type="hidden" value="process" name="action">
<br>
<b>&nbsp;&nbsp;<?= vt('Portefeuille Automaat instellingen'); ?></b><br><br>
<fieldset>
<legend> mutatie code</legend>

<div class="formblock">
	<div class="formlinks">&nbsp;
	</div>
	<div class="formrechts">
	 <input type="password" name="mutatiecode" size="10" maxlength="10">
		<br>
    <br>
 	</div>
</div>
</fieldset>

<fieldset>
<legend> <?= vt('Instellingen'); ?> </legend>
<div class="formblock">
	<div class="formlinks">importmap
	</div>
	<div class="formrechts">
		<input type="text" name="portAutomaat_importmap"  size="70" value="<?=$cfg->getData("portAutomaat_importmap");?>" />
	</div>
</div>

</fieldset>
<br>



</form>

<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>