<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "sectorperhoofdsectorList.php";
$__funcvar[location] = "sectorperhoofdsectorEdit.php";

$object = new SectorPerHoofdsector();

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

$DB->SQL("SELECT Beleggingssector FROM Beleggingssectoren ORDER BY Beleggingssector");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Beleggingssector"]["form_options"][] = $gb[Beleggingssector];
	$object->data['fields']["Hoofdsector"]["form_options"][] = $gb[Beleggingssector];
}

$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>