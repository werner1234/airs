<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 29 april 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/26 06:51:10 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: importafwijkingenEdit.php,v $
    Revision 1.4  2018/10/26 06:51:10  cvs
    call 7173

    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/09/22 14:37:28  cvs
    call 6202

    Revision 1.1  2017/03/24 09:35:57  cvs
    call 5731

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = vt("Transactie import uitzonderingen");
$mainHeader    = " " . vt('muteren') . "";

$__funcvar["listurl"]  = "importafwijkingenList.php";
$__funcvar["location"] = "importafwijkingenEdit.php";

$object = new ImportAfwijkingen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->formTemplate = "importafwijkingenEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;

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