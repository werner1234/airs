<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/11/23 13:41:21 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: hsbcTransactiecodesEdit.php,v $
    Revision 1.1  2018/11/23 13:41:21  cvs
    call 6991

    Revision 1.1  2018/05/09 11:40:17  cvs
    call 6878

    Revision 1.1  2017/09/20 06:23:01  cvs
    megaupdate 2722


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "HSBC transactiecodes muteren";

$__funcvar["listurl"]  = "hsbcTransactiecodesList.php";
$__funcvar["location"] = "hsbcTransactiecodesEdit.php";

$object = new hsbcTransactieCodes();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "hsbcTransactiecodesEditTemplate.html";

$editObject->controller($action,$data);

//debug($object);
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