<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 30 juni 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: dd_mailcronlogEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/12/13 13:42:30  cvs
    call 5911

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "log automatisch ingelezen mails muteren";

$__funcvar['listurl']  = "dd_mailcronlogList.php";
$__funcvar['location'] = "dd_mailcronlogEdit.php";

$object = new Dd_mailCronLog();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
$editObject->formTemplate = "dd_mailcronlogEditTemplate.html";

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