<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 juli 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/05/18 14:57:27 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: appvertalingEdit.php,v $
branche vertaling_updateMaster
 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Applicatie vertaling muteren");

$__funcvar['listurl']  = "appvertalingList.php";
$__funcvar['location'] = "appvertalingEdit.php";

$object = new AppVertaling();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "appvertalingEditTemplate.html";

$editObject->controller($action,$data);


//echo $editObject->getTemplate();
echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$_SESSION["returnUrl"]["appVertaling"]);
}
else 
{
	echo $_error = $editObject->_error;
}
