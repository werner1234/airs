<?php
/*
    AE-ICT CODEX source module versie 1.6, 13 juni 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/29 15:56:45 $
    File Versie         : $Revision: 1.5 $

    $Log: portaalqueueEdit.php,v $
    Revision 1.5  2020/04/29 15:56:45  rvv
    *** empty log message ***

    Revision 1.4  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.3  2017/02/12 11:20:39  rvv
    *** empty log message ***

    Revision 1.2  2016/11/16 16:50:08  rvv
    *** empty log message ***

    Revision 1.1  2012/11/21 15:03:37  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "portaalqueueList.php";
$__funcvar['location'] = "portaalqueueEdit.php";

$object = new PortaalQueue();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data['action'];

$editObject->formTemplate = "portaalqueueEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;


$editObject->controller($action,$data);

$editObject->formVars["attachment"] .= "<a href=showTempfile.php?show=3&id=".$object->get('id')."> ".$object->get('filename')." </a><br>";

if($object->get('id')>0)
{
	$db = new DB();
	$query = "SELECT length(pdfFactuurData) as lengte, length(pdfData) as lengteRapport  FROM portaalQueue WHERE id='" . $object->get('id') . "'";
	$db->SQL($query);
	$dbData=$db->lookupRecord();
	if($dbData['lengte']>0)
  	$editObject->formVars["attachmentFactuur"] .= "<a href=showTempfile.php?show=4&id=" . $object->get('id') . "> factuur_" . $object->get('filename') . " </a><br>";
  if($dbData['lengteRapport']==0)
    $editObject->formVars["attachment"] ='';
    
}

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{

	if(!empty($_FILES['bijlage']['name']) && $object->get('id') > 0)
	{
		$db=new DB();
		$name=$_FILES['bijlage']['name'];
		$content=bin2hex(file_get_contents($_FILES['bijlage']['tmp_name']));
		$query="UPDATE portaalQueue SET filename='".mysql_real_escape_string($name)."',pdfData=unhex('$content'), add_date=NOW(),add_user='$USR' WHERE id='".$object->get('id')."'";
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