<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 4 december 2010
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/05/08 15:01:04 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: benchmarkverdelingVanafEdit.php,v $
    Revision 1.3  2020/05/08 15:01:04  rm
    8593 Benchmarkverdeling: gebruik AJAX-lookup

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = "";
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "benchmarkverdelingVanafList.php";
$__funcvar["location"] = "benchmarkverdelingVanafEdit.php";

$object = new BenchmarkverdelingVanaf();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

// autocomplete inschakelen op benchmark en fonds veld
$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('Benchmarkverdeling', 'benchmark', 'benchmark');
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('Benchmarkverdeling', 'fonds', 'fonds');

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

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
