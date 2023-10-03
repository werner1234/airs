<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 8 april 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: factuurregelsEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = vt("factuurregel");
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "factuurregelsList.php";
$__funcvar['location'] = "factuurregelsEdit.php";

$object = new Factuurregels();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$AETemplate = new AE_template();
$editcontent['jsincludes'] .= $AETemplate->loadJs('jsrsClient');
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->controller($action,$data);
$autocomplete = new Autocomplete();
$template = "factuurregelsTemplate.html";

$editObject->formTemplate = $template;
$editObject->usetemplate = true;

$editObject->template['script_voet'] = $autocomplete->getAutoCompleteScript('Factuurregels','portefeuille','portefeuille');

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
