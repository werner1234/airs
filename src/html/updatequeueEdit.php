<?php
/*
    AE-ICT sourcemodule created 28 sep. 2022
    Author              : Chris van Santen
    Filename            : updatequeueEdit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar["listurl"]  = "updatequeueList.php";
$__funcvar["location"] = "updatequeueEdit.php";

$subHeader = "";
$mainHeader    = vt("Updateserver status muteren");

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";


$object = new UpdateQueue();
$object->dbId = 2;

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->usetemplate = true;
$editObject->formTemplate = "updatequeueEditTemplate.html";

$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
