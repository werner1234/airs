<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/08/18 14:42:58 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: api_loggingEdit.php,v $
    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$subHeader = "";
$mainHeader    = vt("API logs muteren");



$__funcvar['listurl']  = "api_loggingList.php";
$__funcvar['location'] = "api_loggingEdit.php";

$object = new API_logging();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "api_loggingEditTemplate.html";

$editObject->controller($action,$data);
$res = utf8_encode( $object->get("results"));

$res = str_replace("{", "\n\n{", $res);
$res = str_replace("}", "\n}", $res);
debug($res, "results");
$editObject->formVars["request"] = "<pre>".var_export(json_decode(utf8_encode($object->get("request")),true),true)."</pre>";
$editObject->formVars["errors"] = "<pre>".var_export(json_decode(utf8_encode($object->get("errors")),true),true)."</pre>";
//$editObject->formVars["results"] = "<pre>".var_export(json_decode($res,true),true)."</pre>";

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