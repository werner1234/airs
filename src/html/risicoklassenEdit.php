<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar["listurl"]  = "risicoklassenList.php";
$__funcvar["location"] = "risicoklassenEdit.php";

$object = new Risicoklassen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][] = $gb['Vermogensbeheerder'];
}

$editObject->formVars["kleur"] = '
	<table>
		<tr>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">'.vt("Kleur").'</td>
		</tr>
		<tr>
			<td colspan="4">
';

$kleuren=array('R','G','B');
$export=array();
if($action == "update")
{
  for ($a = 0; $a < count($kleuren); $a++)
  {
    $export['kleur'][$a] = $data['kleur_' . $kleuren[$a]];
  }
  $data['kleur']=serialize($export["kleur"]);
}

$editObject->controller($action,$data);
$export['kleur'] 			= unserialize($object->get("kleur"));
for($a=0; $a < count($kleuren); $a++)
{
  $editObject->formVars["kleur"] .= ' <input size="3" maxlength="3" type="text" value="'.$export['kleur'][$a].'" class="colorp" id="kleur_'.$kleuren[$a].'" data-group="kleur" name="kleur_'.$kleuren[$a].'" >';
}

$editObject->formVars["kleur"] .= '
<div id="kleur-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option">
   <input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span>
</div>
</table>';

$html=$editObject->getOutput();

$html=str_replace('</form></div>',"<div class=\"formblock\">
<div class=\"formlinks\">".vt("Kleur")."</div>
<div class=\"formrechts\">
".$editObject->formVars["kleur"]."</div>
</div>

</form></div>

<script>
  AEColor.initColors();
  AEColor.initColorChange();
  $(document).ready(function()
  {
  
  });
</script>
",$html);

echo $html;

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
