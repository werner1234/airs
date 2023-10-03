<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "kortingenperdepotbankList.php";
$__funcvar[location] = "kortingenperdepotbankEdit.php";

$object = new KortingenPerDepotbank();

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

$DB->SQL("SELECT Depotbank FROM Depotbanken ORDER BY Depotbank");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Depotbank"]["form_options"][] = $gb[Depotbank];
}

$DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Grootboekrekening"]["form_options"][] = $gb[Grootboekrekening];
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