<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader  = vt("zorgplicht per portefeuille");
$mainHeader = vt("muteren");
$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$__funcvar["listurl"]  = "zorgplichtperportefeuilleList.php";
$__funcvar["location"] = "zorgplichtperportefeuilleEdit.php";

$object = new ZorgplichtPerPortefeuille();

if($_GET['action'] == "new")
	$editcontent["body"] = " onLoad=\"javascript:zorgplichtChanged();\" ";

	$editcontent["jsincludes"] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent["javascript"] .= '
function zorgplichtChanged()
{
	jsrsExecute("selectRS.php", populateZorgplicht, "getZorgplicht",
	buildQueryArray("editForm"), false);
}
function populateZorgplicht (valueTextStr)
{
	populateDropDown(document.editForm.Zorgplicht,valueTextStr);
}

function buildQueryArray(theFormName) {
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
    if (theForm.elements[e].name!="") {
    	qs[theForm.elements[e].name] = theForm.elements[e].value;
      }
    }
  return qs;
}

function clearDropDown (selField)
{
  while (selField.options.length > 0)
    selField.options[0] = null;
}

function populateDropDown (field, valueTextStr)
{
  var selField = field;
  clearDropDown(selField);

	// options in form "value~displaytext|value~displaytext|..."
  var aOptionPairs = valueTextStr.split("|");
  
  for( var i = 0; i < aOptionPairs.length; i++ ){
    if (aOptionPairs[i].indexOf("~") != -1) {
      var aOptions = aOptionPairs[i].split("~");
      oItem = new Option;
      oItem.value = aOptions[1];
      oItem.text = aOptions[0];
      selField.options[selField.options.length] = oItem;
    }  
  }
  
  selField.options.selectedIndex = 0;
}
function buildQueryArray(theFormName) {
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
    if (theForm.elements[e].name!="") {
    	qs[theForm.elements[e].name] = theForm.elements[e].value;
      }
    }
  return qs;
}
';

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

// Vermogensbeheerder ophalen
$DB = new DB();

/*
$DB->SQL("SELECT Portefeuille FROM Portefeuilles ORDER BY Portefeuille");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Portefeuille"]["form_options"][] = $gb['Portefeuille'];
}
*/
$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][] = $gb['Vermogensbeheerder'];
}

$object->data['fields']["Vermogensbeheerder"]["form_extra"] = " onChange=\"javascript:zorgplichtChanged();\" ";

if($id)
{
	$q = "SELECT Zorgplichtcategorien.Zorgplicht, Zorgplichtcategorien.Omschrijving FROM Zorgplichtcategorien, ZorgplichtPerPortefeuille WHERE Zorgplichtcategorien.Vermogensbeheerder = ZorgplichtPerPortefeuille.Vermogensbeheerder AND ZorgplichtPerPortefeuille.id = '".$id."'";
	$DB->SQL($q);	
	$DB->Query();	
	while($zp = $DB->nextRecord())
	{
		$object->data['fields']["Zorgplicht"]["form_options"][$zp['Zorgplicht']] = $zp['Omschrijving'];
	}
}
$editObject->usetemplate = true;

$editObject->formTemplate="zorgplichtperportefeuilleEditTemplate.html";


$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('ZorgplichtPerPortefeuille', 'Portefeuille', 'Portefeuille');

$editObject->controller($action,$data);

if($data['frame']==1)
{
	if($_GET['Portefeuille'])
	{
		$object->set('Portefeuille', $_GET['Portefeuille']);
		$q="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$_GET['Portefeuille']."'";
		$DB->SQL($q);
		$vermogensbeheerder=$DB->lookupRecord();
		$object->set('Vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
	}
	$object->setOption('Portefeuille', 'form_type', 'text');
	$object->setOption('Portefeuille', 'form_extra', 'READONLY');
	$object->setOption('Vermogensbeheerder', 'form_type', 'text');
	$object->setOption('Vermogensbeheerder', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
		$editObject->formVars["verzendKnop"] ='Geen rechten om te verzenden.';
	echo $editObject->getOutput();

}
else
  echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
