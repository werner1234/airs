<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 23 mei 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/24 16:14:20 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: fondsenfundinformatieEdit.php,v $
    Revision 1.2  2020/05/24 16:14:20  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = "";
$mainHeader   = vt("muteren");

$__funcvar["listurl"]  = "fondsenfundinformatieList.php";
$__funcvar["location"] = "fondsenfundinformatieEdit.php";

$object = new FondsenFundInformatie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

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
