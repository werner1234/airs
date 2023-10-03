<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: attributiepergrootboekrekeningEdit.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2017/11/29 16:16:06  rvv
    *** empty log message ***

    Revision 1.1  2006/12/21 16:12:04  rvv
    attributie

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Attributie per grootboekrekening muteren");

$__funcvar['listurl']  = "attributiepergrootboekrekeningList.php";
$__funcvar['location'] = "attributiepergrootboekrekeningEdit.php";

$object = new AttributiePerGrootboekrekening();



$editcontent['body'] = "onLoad='javascript:vermogensbeheerderChanged();'";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var Veld = '';
var ajax = new Array();

function vermogensbeheerderChanged()
{
  getWaarden(document.editForm.Vermogensbeheerder.value,'AttributieCategorien','AttributieCategorie');
}

function getWaarden (sel,tabel,veld)
{
  var oldValue = document.getElementById(veld).value;
  var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel+'';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setWaarden(index,veld,oldValue)
{
 	var	Waarden = ajax[index].response;
 	var elements = Waarden.split('\\t\\n');
 	if(elements.length >1)
 	{
 	  document.getElementById(veld).options.length=0;
    AddName('editForm',veld,'---','');
 	  for(var i=0;i<elements.length;i++)
 	  {
 	   if(elements[i] != '')
 	   {
 	     //AddName('editForm',veld,elements[i],elements[i])
       var parts=elements[i].split('\\t');
       AddName('editForm',veld,parts[0]+' - '+parts[1],parts[0]);
 	   }
 	  }
 	}
 	document.getElementById(veld).value = oldValue;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

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