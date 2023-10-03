<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "valutakoersenList.php";
$__funcvar[location] = "valutakoersenEdit.php";

$object = new Valutakoersen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];


// selecteer en vul valuta options in
$query = "SELECT Valuta FROM Valutas ORDER BY Valuta";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$object->data['fields']["Valuta"]["form_options"][] ="";
while($clientdata = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $clientdata[Valuta];
}

if($action == "new")
{
	// selecteer juiste valuta
	$object->data['fields']['Valuta']['value'] = $Valuta;
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