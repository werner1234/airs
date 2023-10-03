<?php
global $__appvar;
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "bedrijfsgegevensList.php";
$__funcvar['location'] = "bedrijfsgegevensEdit.php";



$object = new Bedrijfsgegevens();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

//$editObject->usetemplate = true;
$editObject->controller($action,$data);

if ($action == "edit" && $__appvar['master'])
{
	session_start();
	$_SESSION['submenu'] = New Submenu();
	$_SESSION['submenu']->addItem("Vermogensbeheerder","bedrijfVermogensbeheerderKoppel.php?Bedrijf=".$object->get("Bedrijf"));
	session_write_close();
  
  if($object->get("Bedrijf") <> '')
    $object->setPropertie('LeidendeVBH','select_query','SELECT Vermogensbeheerder,Vermogensbeheerder FROM VermogensbeheerdersPerBedrijf WHERE Bedrijf="'.$object->get("Bedrijf").'" ORDER BY Vermogensbeheerder');

}

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>