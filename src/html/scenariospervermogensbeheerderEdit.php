<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 november 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: scenariospervermogensbeheerderEdit.php,v $
    Revision 1.4  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.3  2014/07/02 16:03:16  rvv
    *** empty log message ***

    Revision 1.2  2013/12/21 18:30:19  rvv
    *** empty log message ***

    Revision 1.1  2013/11/17 13:16:20  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "scenariospervermogensbeheerderList.php";
$__funcvar['location'] = "scenariospervermogensbeheerderEdit.php";

$object = new ScenariosPerVermogensbeheerder();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->formTemplate = "scenariospervermogensbeheerderTemplate.html";



// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if(isset($data['kleurcode']) && is_array($data['kleurcode']))
  $data['kleurcode']=serialize($data['kleurcode']);

$editObject->controller($action,$data);

$huidigeKleurcode=$object->get('kleurcode');
$huidigeKleurcode=unserialize($huidigeKleurcode);
$editObject->formVars["kleurcode"]='';
for($i=0;$i<3;$i++)
{
	$editObject->formVars["kleurcode"] .= "<input name=\"kleurcode[]\" value=\"".$huidigeKleurcode[$i]."\" size=\"3\">";
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