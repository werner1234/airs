<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 27 april 2019
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/05/18 16:26:59 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: begrippenrapportEdit.php,v $
    Revision 1.3  2019/05/18 16:26:59  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "begrippenrapportList.php";
$__funcvar['location'] = "begrippenrapportEdit.php";

$object = new BegrippenRapport();

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
var Veld = '';
var ajax = new Array();

function vermogensbeheerderChanged()
{
  getWaarden(document.editForm.vermogensbeheerder.value,'Rapportcategorieen','categorieId');
}

function getWaarden (sel,tabel,veld)
{
  var oldValue = document.getElementById(veld).value;
  var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setWaarden(index,veld,oldValue)
{
 	var	Waarden = ajax[index].response;
 	var elements = Waarden.split('\\t\\n');
 	document.getElementById(veld).options.length=0;
 	if(elements.length > 1)
 	{

 	  AddName('editForm',veld,'---','');
   	for(var i=0;i<elements.length;i++)
   	{
   	 if(elements[i] != '')
   	 {
   	   var parts=elements[i].split('\\t');
       //alert(parts[0]);
 	     AddName('editForm',veld,parts[1],parts[0]);
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

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

if($data['vermogensbeheerder']<>'')
  $editObject->verzendVermogensbeheerder=$data['vermogensbeheerder'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");


if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
  $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>';

$html=$editObject->getOutput();
//listarray($html);
$html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></form></div></div>",$html);
echo $html;

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
