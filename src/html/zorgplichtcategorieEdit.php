<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "zorgplichtcategorieList.php";
$__funcvar[location] = "zorgplichtcategorieEdit.php";

$object = new Zorgplichtcategorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($vm = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][]  = $vm['Vermogensbeheerder'];
}


//$editObject->usetemplate = true;
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