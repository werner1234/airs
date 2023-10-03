<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 25 september 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/12/04 15:08:51 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: doorkijk_koppelingPerVermogensbeheerderEdit.php,v $
    Revision 1.2  2017/12/04 15:08:51  cvs
    vt( verwijderen

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader  = vt("doorkijk koppeling Per Vermogensbeheerder");
$mainHeader = vt("muteren");

$AEJson = new AE_Json();
$__funcvar['listurl']  = "doorkijk_koppelingPerVermogensbeheerderList.php";
$__funcvar['location'] = "doorkijk_koppelingPerVermogensbeheerderEdit.php";

$object = new Doorkijk_KoppelingPerVermogensbeheerder();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$AETemplate = new AE_template();
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= $AETemplate->loadJs('doorkijkKoppelVBH');
$editcontent['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';//<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;

$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
//$editObject->formTemplate = "doorkijk_koppelingpervermogensbeheerderEditTemplate.html";

$editObject->controller($action,$data);


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
