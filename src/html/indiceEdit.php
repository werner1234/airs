<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "indiceList.php";
$__funcvar[location] = "indiceEdit.php";

$object = new Indice();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][] = $gb[Vermogensbeheerder];
}



$editObject->usetemplate = true;
$editObject->formTemplate = "indiceTemplate.html";

$editObject->controller($action,$data);

$orFondsen='';
if($object->get('Beursindex') <> '')
  $orFondsen=" OR Fondsen.Fonds='".$object->get('Beursindex')."'";

$DB->SQL("SELECT Fonds FROM Fondsen WHERE EindDatum > now() $orFondsen OR  EindDatum = '0000-00-00' ORDER BY Fonds");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Beursindex"]["form_options"][] = $gb[Fonds];
}

if ($action == 'update' && $editObject->result)
{
$grafiekKleuren = array ('R'=>array('value'=>$data['grafiekKleur_R']),
						 'G'=>array('value'=>$data['grafiekKleur_G']),
						 'B'=>array('value'=>$data['grafiekKleur_B']));
$object->set('grafiekKleur',serialize($grafiekKleuren));	
$object->save();
}

$grafiekKleuren = unserialize($object->get('grafiekKleur'));
$editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['R']['value']."\" id=\"grafiekKleur_R\" name=\"grafiekKleur_R\" > \n";
$editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['G']['value']."\" id=\"grafiekKleur_G\" name=\"grafiekKleur_G\" > \n";
$editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['B']['value']."\" id=\"grafiekKleur_B\" name=\"grafiekKleur_B\" > \n";

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>