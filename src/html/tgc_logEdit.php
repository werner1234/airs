<?php
/*
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/01/04 13:24:11 $
    File Versie         : $Revision: 1.1 $

    $Log: tgc_logEdit.php,v $
    Revision 1.1  2017/01/04 13:24:11  cvs
    call 5542, uitrol WWB en TGC


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$fmt = new AE_cls_formatter();

$subHeader = "";
$mainHeader    = vt("Logging inzien");

$__funcvar["listurl"]  = "tgc_logList.php";
$__funcvar["location"] = "tgc_logEdit.php";

$object = new Tgc_log();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "tgc_logEditTemplate.html";


$editObject->controller($action,$data);

$editObject->formVars["memo"] = nl2br($object->get("memo"));
$editObject->formVars["stamp"] = $fmt->format("@D {D} {d}-{m} om {H}:{i}:{s} uur",$object->get("stamp"));


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