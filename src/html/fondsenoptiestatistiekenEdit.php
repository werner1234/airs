<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 29 december 2018
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/12/31 06:11:52 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: fondsenoptiestatistiekenEdit.php,v $
    Revision 1.2  2018/12/31 06:11:52  rvv
    *** empty log message ***

    Revision 1.1  2018/12/29 13:59:10  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "fondsenoptiestatistiekenList.php";
$__funcvar['location'] = "fondsenoptiestatistiekenEdit.php";

$object = new FondsenOptiestatistieken();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

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