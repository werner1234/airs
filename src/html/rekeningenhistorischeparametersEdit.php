<?php

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "rekeningenhistorischeparametersList.php";
$__funcvar['location'] = "rekeningenhistorischeparametersEdit.php";

$object = new RekeningenHistorischeParameters();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

if($_GET['action']=='kopieerRekeningparameters')
{
  $_GET['action']='edit';
  $_GET['id']='';
  $dateFields=array('date','datetime');
  foreach($object->data['fields'] as $key=>$fieldValues)
  {
    if(in_array($fieldValues['db_type'],$dateFields))
      $value=implode("-",array_reverse(explode("-",$_GET[$key])));
    else
      $value=$_GET[$key];
    
    if($key=='GebruikTot')
      $value=date("Y-m-d",time()-86400);
    
    $object->set($key,$value);
  }
}
$editcontent['javascript']=str_replace('//check values ?','var theForm = document.editForm.elements, z = 0;for(z=0; z<theForm.length;z++){if(theForm[z].disabled == true){theForm[z].disabled = false;}}',$editcontent['javascript']);

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

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
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>