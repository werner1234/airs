<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/14 09:55:51 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: airsKoppelingenEdit.php,v $
    Revision 1.1  2018/09/14 09:55:51  cvs
    Naar VRY omgeving ter TEST

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/09/20 06:23:01  cvs
    megaupdate 2722


 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = "Airs koppeling muteren";

$__funcvar["listurl"]  = "airsKoppelingenList.php";
$__funcvar["location"] = "airsKoppelingenEdit.php";

$object = new airsKoppelingen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "airsKoppelingenEditTemplate.html";

$editObject->controller($action,$data);
$extra = unserialize($object->get("externExtra"));
if (count($extra) > 0)
{
  $editObject->formVars["externExtra"] = "<pre>".var_export($extra,true)."</pre>";
}
else
{
  $editObject->formVars["externExtra"] = "<h3>geen extra data</h3>";
}


// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

//echo $editObject->getTemplate();
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