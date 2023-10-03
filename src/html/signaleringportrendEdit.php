<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 november 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: signaleringportrendEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/11/25 20:22:26  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = "";
$mainHeader   = vt("muteren signalering");
$fmt          = new AE_cls_formatter();

$__funcvar['listurl']  = "signaleringportrendList.php";
$__funcvar['location'] = "signaleringportrendEdit.php";

$object = new SignaleringPortRend();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;

$editObject->formTemplate = "signaleringportrendEditTemplate.html";

$object->getById($data["id"]);

$statusNames = $object->data["fields"]["status"]["form_options"];

//debug($statusNames, $object->get("status"));
$editObject->formVars["datum"] = $fmt->format("@D{d}-{m}-{Y}", $object->get("datum"));
$editObject->formVars["status"] = $statusNames[$object->get("status")];
if (!GetCRMAccess(2))
{
  $editObject->formTemplate = "signaleringportrendEditTemplateMemoOnly.html";
}

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
