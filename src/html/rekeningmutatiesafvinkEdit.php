<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 31 oktober 2016
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: rekeningmutatiesafvinkEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2016/12/02 14:05:30  cvs
    call 5086

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Afvinklijst muteren");

$__funcvar['listurl']  = "rekeningmutatiesafvinkList.php";
$__funcvar['location'] = "rekeningmutatiesafvinkEdit.php";

$object = new RekeningmutatiesAfvink();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
$editObject->formTemplate = "rekeningmutatiesafvinkEditTemplate.html";

$editObject->controller($action,$data);


//echo $editObject->getTemplate();
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