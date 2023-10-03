<?php
/*
    AE-ICT CODEX source module versie 1.6, 18 mei 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $

    $Log: bestandsvergoedingperportefeuilleEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2012/07/14 13:19:04  rvv
    *** empty log message ***

    Revision 1.1  2011/05/18 16:50:14  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar[listurl]  = "bestandsvergoedingperportefeuilleList.php";
$__funcvar[location] = "bestandsvergoedingperportefeuilleEdit.php";

$object = new BestandsvergoedingPerPortefeuille();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->formTemplate ="bestandsvergoedingperportefeuilleEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$editObject->formVars["knoppen"] = '<input type="button" onclick="document.editForm.returnUrl.value=\'bestandsvergoedingperportefeuilleEdit.php?action=new\';submitForm();" value="opslaan en nieuw">';

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