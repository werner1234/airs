<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 21 augustus 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: normperrisicoprofielEdit.php,v $
    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2013/08/21 11:42:00  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "normperrisicoprofielList.php";
$__funcvar['location'] = "normperrisicoprofielEdit.php";

$object = new NormPerRisicoprofiel();


$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad='javascript:vermogensbeheerderChanged();'";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var lastZorgplicht = '';
var lastRisicoklasse = '';
var ajax = new Array();
function vermogensbeheerderChanged()
{
 getRisicoklasse(document.editForm.Vermogensbeheerder.value,document.editForm.Risicoklasse.value);
 getBeleggingscategorien(document.editForm.Vermogensbeheerder.value,document.editForm.Beleggingscategorie.value);

}


function getRisicoklasse(sel,value)
{
	var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Risicoklasse';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=RisicoklassePerVermogensbeheerder&query='+vermogensbeheerder;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setValues(index,'Risicoklasse',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen Zorgplicht waarden mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function getBeleggingscategorien(sel,value)
{
	var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Beleggingscategorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=BeleggingscategorienPerVermogensbeheerder&query='+vermogensbeheerder;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setValues(index,'Beleggingscategorie',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen Zorgplicht waarden mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setValues(index,target,value)
{
 	var	velden = ajax[index].response;
 	document.getElementById(target).options.length=0;
 	var elements = velden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   AddName('editForm',target,elements[i],elements[i])
 	 }
 	}
 	document.getElementById(target).value=value;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

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
