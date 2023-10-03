<?php
/*
    AE-ICT sourcemodule created 01 feb. 2021
    Author              : Chris van Santen
    Filename            : sarTransactiecodesEdit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$prefix                = "caceis";
$bankTransactieTable   = $prefix."TransactieCodes";
$bankNaam              = "Caceis";


$subHeader = "";
$mainHeader    = $bankNaam." transactiecodes muteren";

$__funcvar["listurl"]  = $prefix."TransactiecodesList.php";
$__funcvar["location"] = $prefix."TransactiecodesEdit.php";

$object = new caceisTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = $prefix."TransactiecodesEditTemplate.html";

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
