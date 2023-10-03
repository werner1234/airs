<?php
/* 	
    AE-ICT CODEX source module versie 1.2, 21 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: CRM_naw_faxEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.2  2005/11/23 09:29:48  cvs
    *** empty log message ***

    Revision 1.1  2005/11/21 16:35:06  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("faxvoorvel muteren");

$__funcvar['listurl']  = "CRM_naw_faxList.php";
$__funcvar['location'] = "CRM_naw_faxEdit.php";

$object = new Naw_fax();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "CRM_naw_faxEditTemplate.html";

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
$DB = new DB();
if ($action == "new")
{
  $DB->SQL("SELECT * FROM naw WHERE id =".$_GET['rel_id'] );
  $nawRec = $DB->lookupRecord();
  $DB->SQL("SELECT * FROM gebruikers where init='$USR'");
  $usrRec = $DB->lookupRecord();
  $object->set("rel_id",$_GET['rel_id']);
  $object->set("naam",$nawRec['naam']);
  $object->set("datum", jul2sql(time()));
  $object->set("fax", $nawRec['fax']);
  $object->set("text", nl2br($usrRec['documentvoet']));
  $mainHeader    = vtb("Faxvoorvel toevoegen, bij %s", array($nawRec['naam']));
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