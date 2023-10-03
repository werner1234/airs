<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 1 juni 2016
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: tgc_ipaccesslistEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/01/04 13:24:11  cvs
    call 5542, uitrol WWB en TGC

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("IP toegangslijst muteren");

$__funcvar['listurl']  = "tgc_ipaccesslistList.php";
$__funcvar['location'] = "tgc_ipaccesslistEdit.php";



$object = new Tgc_ipAccessList();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";


$editcontent = array(
	"jsincludes" => '
     <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
     <script type="text/javascript" src="javascript/jquery-min.js"></script>
     <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>

     <script type="text/javascript" src="javascript/algemeen.js"></script>
     ',
	"javascript" =>'
		function submitForm()
		{
		  document.editForm.submit();
		}
	'

);



$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "tgc_ipaccesslistEditTemplate.html";
if ($action == "new")
{
	$object->set("onlineDatum", date("Y-m-d"));
	$object->set("offlineDatum", "2029-12-31");
	$object->set("bedrijf",$__appvar["bedrijf"]);
}



$editObject->controller($action,$data);



// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");


echo $editObject->getOutput();

if ($result = $editObject->result)
{
	$tgc = new AE_cls_toegangsControle();
	$tgc->writeHTaccess();
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>