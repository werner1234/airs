<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: doorkijk_categorieWegingenPerFondsEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/12/04 14:48:17  cvs
    call 6349

    Revision 1.1  2017/12/04 10:40:51  cvs
    Update van Ben ingelezen dd 4-12-2017

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("doorkijk categorie Wegingen Per Fonds");
$mainHeader   = vt("muteren");;

$__funcvar['listurl']  = "doorkijk_categorieWegingenPerFondsList.php";
$__funcvar['location'] = "doorkijk_categorieWegingenPerFondsEdit.php";

$object = new doorkijk_categorieWegingenPerFonds();
//doorkijk_categorieWegingenPerFonds
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$AETemplate = new AE_template();
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= $AETemplate->loadJs('doorkijk_catWegingPerFonds');
$editcontent['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';//<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('doorkijk_categorieWegingenPerFonds', 'Fonds', 'Fonds');

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
//$editObject->formTemplate = "doorkijk_categoriewegingenperfondsEditTemplate.html";
$editObject->formTemplate = "";

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
