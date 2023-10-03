<?php
/*
    AE-ICT CODEX source module versie 1.6, 2 juni 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.8 $

    $Log: emailqueueEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "emailqueueList.php";
$__funcvar['location'] = "emailqueueEdit.php";

$object = new EmailQueue();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;




$data = array_merge($_GET,$_POST);
$action = $data[action];

$db=new DB();
$query="SELECT id,filename FROM emailQueueAttachments WHERE emailQueueId='".$_GET['id']."'";
$db->SQL($query);
$db->Query();
while($att=$db->nextRecord())
{
  $editObject->formVars["attachment"] .= "<a href=\"emailqueueList.php?verdwijderBijlage=".$att['id']."\"><img src=\"icon/16/delete.png\" class=\"simbisIcon\"></a> 
  <a href=\"showTempfile.php?show=2&id=".$att['id']."\"> ".$att['filename']." </a><br>";
}


$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->formTemplate = "emailQueueEditTemplate.html";
$editObject->usetemplate = true;


$editObject->controller($action,$data);


// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  $returnUrl='emailqueueList.php';

  for($i=0;$i<4;$i++)
  {
    if (!empty($_FILES['bijlage'.$i]['name']))
    {
      $name = $_FILES['bijlage'.$i]['name'];
      $content = bin2hex(file_get_contents($_FILES['bijlage'.$i]['tmp_name']));
      $query = "INSERT INTO emailQueueAttachments SET emailQueueId='" . $object->get('id') . "',filename='$name',attachment=unhex('$content'), add_date=NOW(),add_user='$USR'";
      $db->SQL($query);
      $db->Query();
      $returnUrl = 'emailqueueEdit.php?action=edit&id=' . $data['id'];
    }
  }
  
  
  if($action=='delete')
  {
    $db=new DB();
    $query="DELETE FROM emailQueueAttachments WHERE emailQueueId='".$data['id']."'";
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