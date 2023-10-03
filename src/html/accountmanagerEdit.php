<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "accountmanagerList.php";
$__funcvar[location] = "accountmanagerEdit.php";

$object = new Accountmanager();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = array_merge($_GET,$_POST);
$action = $data[action];

if($_FILES['Handtekening']['tmp_name'] <> '')
{
  $filename=$_FILES['Handtekening']['tmp_name'];
  $file=$_FILES['Handtekening']['name'];
  $img=resize_pngimage($filename,500,250); 
  ob_start();
  imagepng($img);
  $image_data = ob_get_contents();
  ob_end_clean();
  $data['Handtekening']=base64_encode($image_data);
}

$editObject->formTemplate ="accountmanagerEditTemplate.html";
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->controller($action,$data);


if($object->get('Handtekening') <> '')
  $editObject->formVars['handtekeningPlaatje']='<img src="data:image/png;base64,'.$object->get('Handtekening').'">';

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>