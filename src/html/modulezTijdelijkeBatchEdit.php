<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/07 12:20:46 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: modulezTijdelijkeBatchEdit.php,v $
    Revision 1.1  2018/11/07 12:20:46  cvs
    call 7300


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "ModuleZ batch muteren";

$__funcvar["listurl"]  = "modulezTijdelijkeBatchList.php";
$__funcvar["location"] = "modulezTijdelijkeBatchEdit.php";

$object = new modulezTijdelijkeBatch();

$editObject = new editObject($object);

$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = false;
//$editObject->formTemplate = "moduleztransactiecodesEditTemplate.html";

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