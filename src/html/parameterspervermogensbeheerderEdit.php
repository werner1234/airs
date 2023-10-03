<?php

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "parameterspervermogensbeheerderList.php";
$__funcvar['location'] = "parameterspervermogensbeheerderEdit.php";

$object = new ParametersPerVermogensbeheerder();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad='javascript:selectieChanged();'";

$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var ajax = new Array();
function selectieChanged()
{
  value = document.getElementById('Categorie').value;
  getSelectie(document.editForm.Categoriesoort.value,document.editForm.Vermogensbeheerder.value);
}

function getSelectie(tabel,vermogensbeheerder)
{
	if(tabel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Categorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel+'';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen waarden mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	document.getElementById('Categorie').options.length=0;
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   //AddName('editForm','Categorie',elements[i],elements[i])
     var parts=elements[i].split('\\t');
     AddName('editForm','Categorie',parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.editForm.Categorie.value = value;
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
?>