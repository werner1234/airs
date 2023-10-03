<?php
/*
    AE-ICT sourcemodule created 17 feb. 2022
    Author              : Chris van Santen
    Filename            : socgenTransactiecodesEdit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "SocGen transactiecodes muteren";

$__funcvar["listurl"]  = "socgenTransactiecodesList.php";
$__funcvar["location"] = "socgenTransactiecodesEdit.php";

$object = new socgenTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "socgenTransactiecodesEditTemplate.html";

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
