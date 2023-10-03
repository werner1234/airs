<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/01 12:17:26 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: reconV3LogEdit.php,v $
    Revision 1.1  2020/07/01 12:17:26  cvs
    call 7937


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "reconV3LogList.php";
$__funcvar["location"] = "reconV3LogEdit.php";

$object = new reconV3Log();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

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