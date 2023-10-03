<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "beleggingscategorieperwegingscategorieList.php";
$__funcvar[location] = "beleggingscategorieperwegingscategorieEdit.php";

$object = new BeleggingscategoriePerWegingscategorie();

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

$DB->SQL("SELECT Beleggingscategorie FROM Beleggingscategorien ORDER BY Beleggingscategorie");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Beleggingscategorie"]["form_options"][] = $gb[Beleggingscategorie];
}

$object->data['fields']["Wegingscategorie"]["form_options"] = $__appvar["WegingscategorieOptions"];

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