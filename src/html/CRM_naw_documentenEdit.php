<?php
/*
    AE-ICT CODEX source module versie 1.2, 23 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.4 $

    $Log: CRM_naw_documentenEdit.php,v $
    Revision 1.4  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.3  2011/02/26 16:00:39  rvv
    *** empty log message ***

    Revision 1.2  2008/02/20 12:04:30  rvv
    GET->POST omzetting

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.2  2005/11/23 19:23:08  cvs
    *** empty log message ***

    Revision 1.1  2005/11/23 09:29:48  cvs
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "Document koppelingen muteren";

$__funcvar[listurl]  = "CRM_naw_documentenList.php";
$__funcvar[location] = "CRM_naw_documentenEdit.php";

$object = new Naw_documenten();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "CRM_naw_documentenEditTemplate.html";

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if ($action == "new")
{
  $editObject->object->data['fields']['rel_id']['value'] = $_GET['rel_id'];
}
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