<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/23 13:32:26 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: abnV2transactiecodesEdit.php,v $
    Revision 1.1  2018/11/23 13:32:26  cvs
    call 7047

    Revision 1.1  2017/09/20 06:24:10  cvs
    no message


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "ABN v2 transactiecodes muteren";

$__funcvar[listurl]  = "abnV2transactiecodesList.php";
$__funcvar[location] = "abnV2transactiecodesEdit.php";

$object = new abnV2TransactieCodes();

$editObject = new editObject(&$object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "abnV2transactiecodesEditTemplate.html";

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
?>