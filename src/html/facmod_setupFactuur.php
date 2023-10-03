<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/11/13 15:13:48 $
 		File Versie					: $Revision: 1.2 $

 		$Log: facmod_setupFactuur.php,v $
 		Revision 1.2  2019/11/13 15:13:48  cvs
 		call 7675
 		
 		Revision 1.1  2019/07/22 09:11:22  cvs
 		call 7675
 		

*/

include_once("wwwvars.php");
session_start();

if (!facmodAccess())
{
  return false;
}


$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->returnUrl = $PHP_SELF;
$_SESSION["NAV"]->addItem(new NavEdit("editForm",true,false,true));


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

if ($data["action"] == "process")
{
  addOrReplace("rubriek_1");
  addOrReplace("rubriek_2");
  addOrReplace("rubriek_3");
  addOrReplace("rubriek_4");
  addOrReplace("rubriek_5");
  addOrReplace("rubriek_6");
  addOrReplace("rubriek_7");
  addOrReplace("rubriek_8");
  addOrReplace("rubriek_9");
  addOrReplace("rubriek_10");

  addOrReplace("factuurvoorzet");
  addOrReplace("factuurnummer");
  addOrReplace("betalingstermijn");
  addOrReplace("adres_x");
  addOrReplace("adres_y");
  addOrReplace("voetnoot");
  addOrReplace("voetnootY");
  addOrReplace("kopietxt");
  addOrReplace("btw_H");
  addOrReplace("btw_L");
  addOrReplace("factuurPDFMethode");
  addOrReplace("factuurtxt");
//  addOrReplace("kopiefactuurBriefpapier");
  addOrReplace("bodyRegels");
  header("location: welcome.php?action=setup");

}

$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");


echo template($__appvar["templateContentHeader"],$editcontent);

?>

<script>
function IsNumeric(sText)
{
  var ValidChars = "0123456789.";
  var Char;
  var i;
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
    alert('factuurnummer MOET een getal zijn!');
    number.focus;
  }
}
</script>
<style>
.formlinks{
width: 200px;
}
</style>

<form action="<?=$PHP_SELF?>" method="POST" name="editForm">
<input type="hidden" value="process" name="action">
<br>
<b>&nbsp;&nbsp;factuurinstellingen aanpassen</b><br><br>

<div class="formblock">
	<div class="formlinks">factuurvoorzet
	</div>
	<div class="formrechts">
		<input type="text" name="factuurvoorzet" value="<?=$cfg->getData("factuurvoorzet");?>">
	</div>
</div>

<div class="formblock">
	<div class="formlinks">volgend factuurnummer
	</div>
	<div class="formrechts">
		<input type="text" name="factuurnummer" value="<?=$cfg->getData("factuurnummer");?>" onchange="checknumeric(this)">
	</div>
</div>

<div class="formblock">
	<div class="formlinks">voetnoot
	</div>
	<div class="formrechts">
	  afstand vanaf bovenkant papier: <input size="3" name="voetnootY" value="<?=$cfg->getData("voetnootY");?>"> mm<br><br>
		<textarea name="voetnoot" rows="4" cols="70" style="text-align:center;"><?=$cfg->getData("voetnoot");?></textarea>
		<br>
    <br>

	</div>
</div>

<div class="formblock">
	<div class="formlinks">methode PDF
	</div>
	<div class="formrechts">
		<select name="factuurPDFMethode">
		  <option value="0" <?=($cfg->getData("factuurPDFMethode") <> 1)?"SELECTED":""?>>Normaal</option>
		  <option value="1" <?=($cfg->getData("factuurPDFMethode") == 1)?"SELECTED":""?>>Afwijkend</option>
		</select>
	</div>
</div>


<div class="formblock">
	<div class="formlinks">betalingstermijn
	</div>
	<div class="formrechts">
		<input size="3" name="betalingstermijn" value="<?=$cfg->getData("betalingstermijn");?>"> dagen
		<br>
    <br>
    <br>

	</div>
</div>
<br>
<br>

<div class="formblock">
	<div class="formlinks">adrespositie
	</div>
	<div class="formrechts">
	  <input size="3" name="adres_x" value="<?=$cfg->getData("adres_x");?>"> mm horizontaal<br>
		<input size="3" name="adres_y" value="<?=$cfg->getData("adres_y");?>"> mm verticaal<br>
		<input size="3" name="bodyRegels" value="<?=$cfg->getData("bodyRegels");?>"> mm van onderkant<br>
		<br>
    <br>

	</div>
</div>
<div class="formblock">
	<div class="formlinks">BTW
	</div>
	<div class="formrechts">
		<input size="3" name="btw_H" value="<?=$cfg->getData("btw_H");?>"> % hoog tarief<br>
		<input size="3" name="btw_L" value="<?=$cfg->getData("btw_L");?>"> % laag tarief<br>
	</div>
</div>

<br>
<br>
  <fieldset>
    <legend>&nbsp;&nbsp;mailtemplate voor factuurmail</legend>
    <div class="formblock">
      <div class="formlinks">Tekst
      </div>
      <div class="formrechts">
        <textarea name="factuurtxt" rows="7" cols="80" style="text-align:left;"><?=$cfg->getData("factuurtxt");?></textarea>

      </div>
    </div>

  </fieldset>

  <div class="formblock">
    <div class="formlinks">Rubrieken
    </div>
    <div class="formrechts">
      <?
      for ($i=1; $i < 11; $i++)
      {
        echo "<br/>$i. &nbsp;<input name='rubriek_$i' value='".$cfg->getData("rubriek_$i")."'>";
      }
      ?>
    </div>
  </div>




</form>
<?

// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);

?>