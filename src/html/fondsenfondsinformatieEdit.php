<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2018
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: fondsenfondsinformatieEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2018/04/30 05:35:09  rvv
    *** empty log message ***

    Revision 1.1  2018/04/29 09:42:48  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "fondsenfondsinformatieList.php";
$__funcvar["location"] = "fondsenfondsinformatieEdit.php";

$object = new FondsenFondsinformatie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$autocomplete = new Autocomplete();
//$template = "factuurregelsTemplate.html";
//$editObject->formTemplate = $template;
//$editObject->usetemplate = true;

$editObject->template['script_voet'] = $autocomplete->getAutoCompleteScript('FondsenFondsinformatie','Fonds','Fonds');


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
