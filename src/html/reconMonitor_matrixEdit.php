<?php
/*
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/30 09:27:50 $
    File Versie         : $Revision: 1.1 $

    $Log: reconMonitor_matrixEdit.php,v $
    Revision 1.1  2019/08/30 09:27:50  cvs
    call 7934





*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "bedrijf - depotbank muteren";

$__funcvar["listurl"]  = "reconMonitor_matrixList.php";
$__funcvar["location"] = "reconMonitor_matrixEdit.php";

$object = new reconMonitor_matrix();

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

if ($data["action"] == "update")
{
  $data["door"] = $USR;
}

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