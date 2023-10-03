<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/06 07:25:12 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: cawTransactiecodesEdit.php,v $
    Revision 1.1  2019/11/06 07:25:12  cvs
    update 6-11-2019

    Revision 1.1  2019/10/09 09:53:51  cvs
    call 8025


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "CapAtWork (CAW) transactiecodes muteren";

$__funcvar["listurl"]  = "cawTransactiecodesList.php";
$__funcvar["location"] = "cawTransactiecodesEdit.php";

$object = new cawTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "cawTransactiecodesEditTemplate.html";

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
