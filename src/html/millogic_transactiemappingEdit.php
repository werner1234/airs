<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 11 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: millogic_transactiemappingEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/09/20 06:25:12  cvs
    megaupdate 2722

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Millogic transactieMapping muteren");

$__funcvar['listurl']  = "millogic_transactiemappingList.php";
$__funcvar['location'] = "millogic_transactiemappingEdit.php";

$object = new Millogic_transactieMapping();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
$editObject->formTemplate = "millogic_transactiemappingEditTemplate.html";

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