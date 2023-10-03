<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/11/15 16:45:32 $
 		File Versie					: $Revision: 1.1 $

 		$Log: dd_setup.php,v $
 		Revision 1.1  2009/11/15 16:45:32  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/05/29 11:22:05  cvs
 		*** empty log message ***

 		Revision 1.1  2008/12/11 15:49:13  cvs
 		*** empty log message ***

 		Revision 1.3  2008/01/23 16:21:32  cvs
 		diverse kleine bugs en aanpassingen



*/
$pushTypes[] = array("value"=>"inline","description"=>"Openen in browser");
$pushTypes[] = array("value"=>"attachment","description"=>"Als bijlage");
include_once("wwwvars.php");
include_once("../classes/AE_cls_digidoc.php");
session_start();


$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->returnUrl = $PHP_SELF;
$_SESSION["NAV"]->addItem(new NavEdit("editForm",true,false,false));


$_SESSION["submenu"] = "";

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


  addOrReplace("dd_maxFileSizeMb");
  addOrReplace("dd_currentDataStore");
  addOrReplace("dd_filePushType");
  header("location: welcome.php?action=setup");
}


$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");


echo template($__appvar["templateContentHeader"],$editcontent);
if ($data["action"] == "process")
{
  echo "<h3> ongeldige mutatiecode, instellingen worden niet opgeslagen! </h3>";
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
    alert('<?=vt("moet een numeriek getal zijn")?>');
    number.focus;
  }
}
</script>

<form action="<?=$PHP_SELF?>" method="POST" name="editForm">
<input type="hidden" value="process" name="action">
<br>
<b>&nbsp;&nbsp;<?=vt("DigiDoc instellingen")?></b><br><br>
<fieldset>
<legend> <?=vt("mutatie code")?></legend>

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
<legend> <?=vt("Instellingen")?> </legend>
<div class="formblock">
	<div class="formlinks"><?=vt("Huidige datastore")?>
	</div>
	<div class="formrechts">
		<input type="text" name="dd_currentDataStore" maxlength="2" size="2" value="<?=$cfg->getData("dd_currentDataStore");?>" onchange="checknumeric(this)" > (00-99)
	</div>
</div>
<div class="formblock">
	<div class="formlinks"><?=vt("max grootte datastore")?>
	</div>
	<div class="formrechts">
		<input type="text" name="dd_maxFileSizeMb" maxlength="5" size="5" value="<?=$cfg->getData("dd_maxFileSizeMb");?>" onchange="checknumeric(this)" > (in megabytes)
	</div>
</div>
<div class="formblock">
	<div class="formlinks"><?=vt("Uitvoer document")?>
	</div>
	<div class="formrechts">
		<select name="dd_filePushType" >
<?
     $pt = $cfg->getData("dd_filePushType");
     for ($x=0; $x < count($pushTypes);$x++)
     {
       $v = $pushTypes[$x]["value"];
       $d = $pushTypes[$x]["description"];
       $selected = ($pt == $v)?"SELECTED":"";
       echo "<OPTION value=\"$v\" $selected>$d</OPTION>\n";
     }
?>
    </select>
	</div>
</div>

</fieldset>
<br>



</form>

<?
$dd = new digidoc();
$db = new DB();
$query = "SHOW TABLE STATUS LIKE  'dd_%' ";
$db->executeQuery($query);

echo "
<table border=1 cellpadding=2 cellspacing=2 >
  <tr bgcolor=#eeeeee>
    <td>".vt("Naam")."</td>
    <td>".vt("Aangemaakt")."</td>
    <td>".vt("Grootte")."</td>
    <td>".vt("Items")."</td>
  </tr>";
while ($rec = $db->nextRecord())
{
  $db_info     = $dd->tableStatus($rec["Name"]);
  $db_date     = $dd->tableStatus($rec["Name"],"create");
  $db_size     = $dd->tableStatus($rec["Name"],"size");
  $db_records  = $dd->tableStatus($rec["Name"],"records");
  echo "<tr><td>".$db_info["Name"]."</td><td>".$db_date."</td><td>".$db_size."</td><td>".$db_records."</td></tr>";
}
echo "</table>";
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>