<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2018
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/23 16:36:21 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: FondsenEMTdataEdit.php,v $
    Revision 1.1  2020/05/23 16:36:21  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader   = "";
$mainHeader  = vt("muteren");

$__funcvar["listurl"]  = "FondsenEMTdataList.php";
$__funcvar["location"] = "FondsenEMTdataEdit.php";

$object = new FondsenEMTdata();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;
//
$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$autocomplete = new Autocomplete();
//$template = "factuurregelsTemplate.html";
//$editObject->formTemplate = $template;
//$editObject->usetemplate = true;

$editObject->template['pageHeader'] .= '
<style>
.formlinks{
  width: 350px!important;
}
</style>
';
$editObject->template['script_voet'] = $autocomplete->getAutoCompleteScript('FondsenEMTdata','Fonds','Fonds');


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
