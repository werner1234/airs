<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 18 augustus 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/03/01 08:57:02 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: api_extern_loggingEdit.php,v $
    Revision 1.1  2019/03/01 08:57:02  cvs
    call 7364

    Revision 1.1  2017/08/18 14:42:58  cvs
    call 5815

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$subHeader = "";
$mainHeader    = "API extern logs muteren";



$__funcvar['listurl']  = "api_extern_loggingList.php";
$__funcvar['location'] = "api_extern_loggingEdit.php";

$object = new API_extern_logging();

$outP = new editObject($object);
$outP->__funcvar = $__funcvar;
$outP->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$outP->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$outP->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$outP->usetemplate = true;
$outP->formTemplate = "api_extern_loggingEditTemplate.html";

$outP->controller($action, $data);
$res = (utf8_encode( $object->get("results")));

$res = str_replace("{", "\n\n{", $res);
$res = str_replace("}", "\n}", $res);
debug($res, "results");
$outP->formVars["request"] = "<pre>".var_export(json_decode(utf8_encode($object->get("request")), true), true)."</pre>";
$outP->formVars["errors"] = "<pre>".var_export(json_decode(utf8_encode($object->get("errors")), true), true)."</pre>";
$outP->formVars["results"] = "<pre>".var_export(json_decode(utf8_encode($object->get("results")), true), true)."</pre>";

echo $outP->getOutput();

if ($result = $outP->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $outP->_error;
}
?>