<?php
/*
    AE-ICT CODEX source module versie 1.6, 21 juli 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/07/03 15:40:13 $
    File Versie         : $Revision: 1.4 $

    $Log: normwegingperbeleggingscategorieEdit.php,v $
    Revision 1.4  2019/07/03 15:40:13  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = vt("norm weging per beleggingscategorie");
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "normwegingperbeleggingscategorieList.php";
$__funcvar['location'] = "normwegingperbeleggingscategorieEdit.php";

$object = new NormwegingPerBeleggingscategorie();


$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['body'] = "onLoad='javascript:portefeuilleChanged();'";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var ajax = new Array();
function portefeuilleChanged()
{
  getBeleggingscategorien(document.editForm.Portefeuille.value,document.editForm.Beleggingscategorie.value);
}

function getBeleggingscategorien(sel,value)
{
	var portefeuille = sel;
	if(portefeuille.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Beleggingscategorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=BeleggingscategorienPerPortefeuille&query='+portefeuille;	// Specifying which file to get
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

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");


if ($_GET['portefeuille'])
{
  $DB = new DB();
  $object->set('Portefeuille', $_GET['portefeuille']);
  $q = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . $_GET['portefeuille'] . "'";
  $DB->SQL($q);
  $vermogensbeheerder = $DB->lookupRecord();
  $object->set('Vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
  $editObject->verzendVermogensbeheerder=$vermogensbeheerder['Vermogensbeheerder'];
}


if($data['frame']==1)
{
  $editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
  $editObject->usetemplate = true;
  
  $htmlTemplate=$editObject->getTemplate();
  $htmlTemplate=str_replace('</form></div>','<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">rvv {verzendKnop}</div></form></div>' , $htmlTemplate);
  $editObject->formTemplate=$htmlTemplate;
  //foreach($object->data['fields'] as $fieldname=>$dat)
  //  echo "'$fieldname'".',';
  $readonlyFields=array('Portefeuille');
  foreach($readonlyFields as $field)
  {
    $object->setOption($field, 'form_type', 'text');
    $object->setOption($field, 'form_extra', 'READONLY');
  }
  
  if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0  )
  {
    $editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
  }
  else
    $editObject->formVars["verzendKnop"] ='Geen rechten om te verzenden.';
  echo $editObject->getOutput();
  
}
else
{
  echo $editObject->getOutput();
}

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
