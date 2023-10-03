<?php
/*
    AE-ICT CODEX source module versie 1.6, 23 juli 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $

    $Log: help_tekstEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2011/07/23 17:24:57  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$returnUrl='handleiding.php';

if($_GET['key'])
{
  $db=new DB();
  $query="SELECT id FROM help_tekst WHERE titel='".$_GET['key']."'";
  $db->SQL($query);
  $record=$db->lookupRecord();
  if($record['id'])
  {
    $_GET['action']='edit';
    $_GET['id']=$record['id'];
  }
  else
  {
    $_GET['action']='new';
  }
}

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "help_tekstList.php";
$__funcvar['location'] = "help_tekstEdit.php";

$object = new Help_tekst();



$_SESSION['NAV']->returnUrl='handleiding.php';
$editObject = new editObject($object);
$editObject->formMethod="POST";
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST,$_GET);
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($_GET['key'])
 $object->set('titel',$_GET['key']);
if($_GET['url'])
 $object->set('url',$_GET['url']);

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