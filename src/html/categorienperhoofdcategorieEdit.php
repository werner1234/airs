<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "categorienperhoofdcategorieList.php";
$__funcvar['location'] = "categorienperhoofdcategorieEdit.php";

$object = new CategorienPerHoofdcategorie();

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
  getWaarden(document.editForm.Vermogensbeheerder.value,'Hoofdcategorien','Hoofdcategorie');
  getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingscategorien','Beleggingscategorie');
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
$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][] = $gb['Vermogensbeheerder'];
}
/*
$DB->SQL("SELECT Beleggingscategorien.Beleggingscategorie ,CategorienPerHoofdcategorie.Hoofdcategorie
FROM Beleggingscategorien  
LEFT JOIN CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie=CategorienPerHoofdcategorie.Hoofdcategorie
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Beleggingscategorien.Beleggingscategorie");
$DB->Query();
while($gb = $DB->NextRecord())
{
  if($gb['Hoofdcategorie']=='')
	  $object->data['fields']["Beleggingscategorie"]["form_options"][] = $gb['Beleggingscategorie'];
	$object->data['fields']["Hoofdcategorie"]["form_options"][] = $gb['Beleggingscategorie'];
}
*/


//$editObject->usetemplate = true;
$editObject->controller($action,$data);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>