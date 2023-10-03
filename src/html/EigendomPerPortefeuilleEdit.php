<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $

    $Log: EigendomPerPortefeuilleEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("Eigendom Per Portefeuille");
$mainHeader   = vt("muteren");

$__funcvar["listurl"]  = "EigendomPerPortefeuilleList.php";
$__funcvar["location"] = "EigendomPerPortefeuilleEdit.php";

$object = new EigendomPerPortefeuille();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

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
