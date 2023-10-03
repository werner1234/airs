<?php
/*
    AE-ICT CODEX source module versie 1.6, 16 april 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $

    $Log: bestandsvergoedingenEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2011/05/18 16:50:14  rvv
    *** empty log message ***

    Revision 1.1  2011/04/17 08:56:16  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "bestandsvergoedingenList.php";
$__funcvar['location'] = "bestandsvergoedingenEdit.php";

$object = new Bestandsvergoedingen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

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
  if($action=='delete' && checkAccess())
  {
    $db=new DB();
    $query="DELETE FROM BestandsvergoedingPerPortefeuille WHERE bestandsvergoedingId='".$data['id']."'";
    $db->SQL($query);
    $db->Query();
  }

	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>