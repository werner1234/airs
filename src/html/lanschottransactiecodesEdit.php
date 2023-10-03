<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: lanschottransactiecodesEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2015/06/02 06:51:53  cvs
    *** empty log message ***

    Revision 1.1  2015/04/13 13:24:08  cvs
    *** empty log message ***

    Revision 1.1  2014/11/06 09:26:59  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "FVL transactiecodes muteren";

$__funcvar[listurl]  = "lanschottransactiecodesList.php";
$__funcvar[location] = "lanschottransactiecodesEdit.php";

$object = new LanschotTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "lanschottransactiecodesEditTemplate.html";

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