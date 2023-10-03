<?php
/*
    AE-ICT sourcemodule created 07 aug. 2020
    Author              : Chris van Santen
    Filename            : gsTransactiecodesEdit.php

    call 8759

*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "KNOX transactiecodes muteren";

$__funcvar["listurl"]  = "knoxTransactiecodesList.php";
$__funcvar["location"] = "knoxTransactiecodesEdit.php";

$object = new knoxTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "knoxTransactiecodesEditTemplate.html";

$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
