<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: advent_fondsmappingEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2013/12/11 10:06:26  cvs
    *** empty log message ***

    Revision 1.1  2013/11/15 10:22:21  cvs
    aanpassing tbv Adventexport

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");



$subHeader = "";
$mainHeader    = "Advent fondsmapping muteren";

$__funcvar[listurl]  = "advent_fondsmappingList.php";
$__funcvar[location] = "advent_fondsmappingEdit.php";

$object = new Advent_FondsMapping();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "advent_fondsmappingEditTemplate.html";


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