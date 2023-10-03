<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/01/20 12:13:03 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: valutaperregioEdit.php,v $
    Revision 1.3  2019/01/20 12:13:03  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Muteren valuta per regio");

$__funcvar['listurl']  = "valutaperregioList.php";
$__funcvar['location'] = "valutaperregioEdit.php";

$object = new ValutaPerRegio();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad='javascript:vermogensbeheerderChanged();'";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var ajax = new Array();
function vermogensbeheerderChanged()
{
  getRegio(document.editForm.Vermogensbeheerder.value,document.editForm.Regio.value);
 // getValuta(document.editForm.Vermogensbeheerder.value,document.editForm.Valuta.value);
}

function getRegio(sel,value)
{
	var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Regio';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|Regios';
		ajax[index].onCompletion = function(){ setValues(index,'Regio',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen Regio waarden mislukt.")."') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function getValuta(sel,value)
{
	var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Valuta';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|Valutas';
		ajax[index].onCompletion = function(){ setValues(index,'Valuta',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen Valuta waarden mislukt.")."') };
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
     var parts=elements[i].split('\\t');
     AddName('editForm',target,parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.getElementById(target).value=value;
}



function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";


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
