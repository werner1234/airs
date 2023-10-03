<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/06/19 11:51:07 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: rabotransactiecodesEdit.php,v $
    Revision 1.1  2019/06/19 11:51:07  cvs
    call 7649

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/09/20 06:23:01  cvs
    megaupdate 2722


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "Rabo transactiecodes muteren";

$__funcvar["listurl"]  = "rabotransactiecodesList.php";
$__funcvar["location"] = "rabotransactiecodesEdit.php";

$object = new raboTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "rabotransactiecodesEditTemplate.html";

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