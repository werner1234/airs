<?php
/*
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/29 15:36:06 $
    File Versie         : $Revision: 1.1 $

    $Log: MONITOR_importMatrixEdit.php,v $
    Revision 1.1  2018/10/29 15:36:06  cvs
    call 7245


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "bedrijf - depotbank muteren";

$__funcvar["listurl"]  = "MONITOR_importMatrixList.php";
$__funcvar["location"] = "MONITOR_importMatrixEdit.php";

$object = new MONITOR_importMatrix();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = false;
//$editObject->formTemplate = "biltransactiecodesEditTemplate.html";

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>