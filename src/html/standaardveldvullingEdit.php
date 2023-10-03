<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 24 juli 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/04 17:42:01 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: standaardveldvullingEdit.php,v $
    Revision 1.4  2020/04/04 17:42:01  rvv
    *** empty log message ***

    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2013/11/27 16:28:02  rvv
    *** empty log message ***

    Revision 1.1  2013/07/24 15:47:04  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "standaardveldvullingList.php";
$__funcvar['location'] = "standaardveldvullingEdit.php";

$object = new StandaardVeldVulling();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formMethod='POST';

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad='javascript:laadVelden();'";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var Veld = '';
var ajax = new Array();

function laadVelden()
{
  getWaarden(document.editForm.tabel.value,'veld');
}

function getWaarden (tabel,veld)
{
  
  var oldValue = document.getElementById(veld).value;
	if(tabel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/veldenPerTabel.php?query='+tabel;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen velden mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function

	}
}

function laadWaarden()
{

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
 	     AddName('editForm',veld,elements[i],elements[i])
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

$data = array_merge($_GET,$_POST);
$data['waarde']=html_entity_decode($data['waarde']);
$action = $data['action'];
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
//$editObject->usetemplate = true;

$editObject->controller($action,$data);
$tmp=htmlentities($editObject->object->get('waarde'));
$editObject->object->set('waarde',$tmp);


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