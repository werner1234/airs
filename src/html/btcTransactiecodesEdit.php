<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/11/06 07:29:16 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: btcTransactiecodesEdit.php,v $
    Revision 1.1  2019/11/06 07:29:16  cvs
    update 6-11-2019


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "Bank Ten Cate transactiecodes muteren";

$__funcvar["listurl"]  = "btcTransactiecodesList.php";
$__funcvar["location"] = "btcTransactiecodesEdit.php";

$object = new btcTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "btcTransactiecodesEditTemplate.html";

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
