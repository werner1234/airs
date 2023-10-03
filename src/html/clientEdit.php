<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar["listurl"]  = "clientList.php";
$__funcvar["location"] = "clientEdit.php";

$subHeader    = vt("Client");
$mainHeader   = vt("muteren");
$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$object = new Client();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;



$data = $_GET;
$action = $data["action"];

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
