<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2014/08/06 12:36:44 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: tijdelijkereconEdit.php,v $
    Revision 1.1  2014/08/06 12:36:44  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("tijdelijk recon item muteren");

$__funcvar['listurl']  = "tijdelijkereconList.php";
$__funcvar['location'] = "tijdelijkereconEdit.php";

$object = new TijdelijkeRecon();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();
$_SESSION['submenu'] = New Submenu();
if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>