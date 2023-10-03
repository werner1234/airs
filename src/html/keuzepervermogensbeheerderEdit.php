<?php
/*
    AE-ICT CODEX source module versie 1.6, 1 december 2010
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.11 $

    $Log: keuzepervermogensbeheerderEdit.php,v $
    Revision 1.11  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie



*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "keuzepervermogensbeheerderList.php";
$__funcvar["location"] = "keuzepervermogensbeheerderEdit.php";

$object = new KeuzePerVermogensbeheerder();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad='javascript:selectieChanged();'";

$IXP_SoortOvereenkomst='';
$IXP_Beleggingscategorie='';
foreach($__appvar["IXP_SoortOvereenkomst"] as $soortOvereenkomst)
	$IXP_SoortOvereenkomst.="AddName('editForm','categorieIXP','$soortOvereenkomst','$soortOvereenkomst');\n";
foreach($__appvar["IXP_Beleggingscategorie"] as $soortOvereenkomst)
	$IXP_Beleggingscategorie.="AddName('editForm','categorieIXP','$soortOvereenkomst','$soortOvereenkomst');\n";



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
  value = document.getElementById('waarde').value;
  getSelectie(document.editForm.categorie.value,document.editForm.vermogensbeheerder.value);
}

function getSelectie(tabel,vermogensbeheerder)
{

	if(tabel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'waarde';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel+'|vermogensbeheerder';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen waarden mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	document.getElementById('waarde').options.length=0;
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   //AddName('editForm','waarde',elements[i],elements[i])
     var parts=elements[i].split('\\t');
     AddName('editForm','waarde',parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.editForm.waarde.value = value;
 	setIXP();
 	setGrootboek();
}

function setGrootboek()
{
  if(document.editForm.categorie.value=='Grootboekrekeningen')
  {
    document.getElementById('AfmKostensoort').disabled=false;
  }
  else
  {
    document.getElementById('AfmKostensoort').disabled=true;
  }
}
function setIXP()
{
  var currentValue=document.editForm.categorieIXP.value;
  document.getElementById('categorieIXP').options.length=0;
  if(document.editForm.categorie.value=='SoortOvereenkomsten')
  {
  document.getElementById('categorieIXP').disabled=false;
  AddName('editForm','categorieIXP','---','');
  $IXP_SoortOvereenkomst
  }
  else if(document.editForm.categorie.value=='Beleggingscategorien')
  {
  AddName('editForm','categorieIXP','---','');
  document.getElementById('categorieIXP').disabled=false;
  $IXP_Beleggingscategorie
  }
  else
  {
    document.getElementById('categorieIXP').disabled=true;
  }
  document.editForm.categorieIXP.value=currentValue;

}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

if($object->get('waarde'))
  $object->setOption('waarde','form_options',array($object->get('waarde')=>$object->get('waarde')));

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
