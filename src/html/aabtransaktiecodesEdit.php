<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "aabtransaktiecodesList.php";
$__funcvar[location] = "aabtransaktiecodesEdit.php";

$object = new AABTransaktieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

//$editObject->usetemplate = true;
$editObject->controller($action,$data);
$object->setOption("actie","form_options",$__appvar["AABTransakties"]);  

echo $editObject->getOutput();
if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>