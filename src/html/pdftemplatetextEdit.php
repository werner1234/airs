<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 juli 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: pdftemplatetextEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$theDir = realpath(dirname(__FILE__))."/PDF_templates/";

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "pdftemplatetextList.php";
$__funcvar['location'] = "pdftemplatetextEdit.php";

$object = new PdfTemplateText();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$files=array();
$dir = @opendir($theDir); // open the directory
if(empty($dir))
{
  mkdir($theDir);
  $dir = @opendir($theDir);
}
while($file = readdir($dir)) // loop once for each name in the directory
{
	// if the name is not a directory and the name is not the name of this program file
	if(is_file($theDir.$file))
	{
	  if(!in_array($file,array('.','..')))
  	{
	     $files[]=$file;
  	}
	}
}
$object->setOption('templateFile','form_options',$files);


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