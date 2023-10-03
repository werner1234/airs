<?php
/*

    Author              : Lennart Poot
    Filename            : quintetTransactiecodesEdit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "Quintet transactiecodes muteren";

$__funcvar["listurl"]  = "quintetTransactiecodesList.php";
$__funcvar["location"] = "quintetTransactiecodesEdit.php";

$object = new quintetTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "quintetTransactiecodesEditTemplate.html";

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
