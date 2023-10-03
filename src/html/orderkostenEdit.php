<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 30 maart 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: orderkostenEdit.php,v $
    Revision 1.7  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "orderkostenList.php";
$__funcvar['location'] = "orderkostenEdit.php";

$object = new Orderkosten();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['body'] = "onLoad='javascript:initKostenFields();vermogensbeheerderChanged();'";
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
 if(document.editForm.vermogensbeheerder.value != '')
 {
  // getWaarden(document.editForm.vermogensbeheerder.value,'Beleggingscategorien','beleggingscategorie');
   getPortefeuilles(document.editForm.vermogensbeheerder.value,'Portefeuilles','portefeuille');
 }
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

function getPortefeuilles (sel,tabel,veld)
{
  var oldValue = document.getElementById(veld).value;
  var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=PortefeuillesPerVermogensbeheerder&query='+vermogensbeheerder+'| AND Einddatum > NOW() ORDER BY Portefeuille';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setWaarden(index,veld,oldValue)
{
 	var	Waarden = ajax[index].response;
 	var elements = Waarden.split('\\t\\n');
 	if(elements.length > 1)
 	{
 	  document.getElementById(veld).options.length=0;
 	  AddName('editForm',veld,'---','');
   	for(var i=0;i<elements.length;i++)
   	{
   	 if(elements[i] != '')
   	 {
 	     AddName('editForm',veld,elements[i],elements[i])
 	   }
    }
 	}
  else
  {
    document.getElementById(veld).options.length=0;
  }
 	document.getElementById(veld).value = oldValue;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";
$editObject->template = $editcontent;
$editObject->formTemplate = "orderkostenEditTemplate.html";

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;
$editObject->verzendVermogensbeheerder=$data['vermogensbeheerder'];
$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($data['frame']==1)
{
	if($_GET['portefeuille'])
	{
		$DB=new DB();
		$object->set('portefeuille', $_GET['portefeuille']);
		$q="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$_GET['portefeuille']."'";
		$DB->SQL($q);
		$vermogensbeheerder=$DB->lookupRecord();
		$object->set('vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
	}
	$object->setOption('vermogensbeheerder', 'form_type', 'text');
	$object->setOption('vermogensbeheerder', 'form_extra', 'READONLY');
	$object->setOption('portefeuille', 'form_type', 'text');
	$object->setOption('portefeuille', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
		$editObject->formVars["verzendKnop"] = vt('Geen rechten om te verzenden.');
	echo $editObject->getOutput();
}
else
  echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
