<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.6 $

 		$Log: tijdelijkerekeningmutatiesEdit.php,v $
 		Revision 1.6  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.5  2016/06/20 08:10:40  cvs
 		call 5027 invulinstructies
 		
 		Revision 1.4  2005/12/16 14:42:16  jwellner
 		classes aangepast

 		Revision 1.3  2005/05/06 16:51:02  cvs
 		einde dag


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$__funcvar[listurl]  = "tijdelijkerekeningmutatiesList.php";
$__funcvar[location] = "tijdelijkerekeningmutatiesEdit.php";

$inst = new AIRS_invul_instructies();

$object = new TijdelijkeRekeningmutaties();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent = array(
	"jsincludes" => '
     <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
     <script type="text/javascript" src="javascript/jquery-min.js"></script>
     <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
     <link rel="stylesheet" href="style/AE-jqueryPluginInvulinstructie.css">
     <script type="text/javascript" src="javascript/AE-jqueryPluginInvulinstructie.js"></script>
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

$editObject->formVars["invulScr"] = $inst->getMessageDiv();


$editObject->formTemplate = "tijdelijkerekeningmutatiesEditTemplate.html";
$editObject->usetemplate = true;
$editObject->controller($action,$data);


$inst->getBeheerderViaRekening($object->get("Rekening"));

$editObject->formVars["VB"] = $inst->vermogensBeheerder;
echo $editObject->getOutput();


if ($result = $editObject->result)
{
	header("Location: ".$_SESSION["TRMListUrl"]);
}
else
{
	echo $_error = $editObject->_error;
}
?>