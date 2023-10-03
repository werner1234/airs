<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 9 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/05/04 18:19:09 $
    File Versie         : $Revision: 1.7 $
 		
    $Log: veldopmaakEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "veldopmaakList.php";
$__funcvar['location'] = "veldopmaakEdit.php";

$object = new Veldopmaak();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formMethod="POST";


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

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST,$_GET);
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
if($action=='update')
{
  $object->getById($data['id']);
  if($object->get('formExtraTxt')<>$data['formExtraTxt'])
  {
    $tmpParts=explode(';',$data['formExtraTxt']);
    $outputFunction='';
    foreach($tmpParts as $tmp)
    {
      $tmp = str_replace(array(' ', "\n"), array('', ''), $tmp);
      $crmObject = new Naw();
      foreach ($crmObject->data['fields'] as $field => $fieldData)
      {
        if (strpos($tmp, '{' . $field . '}=') !== false)
        {
          $tmp = str_replace('{' . $field . '}=', '$("#' . $field . '").val(', $tmp) . ')';
        }
      }
      foreach ($crmObject->data['fields'] as $field => $fieldData)
      {
        if (strpos($tmp, '{' . $field . '}') !== false)
        {
          $tmp = str_replace('{' . $field . '}', 'parseFloat($("#' . $field . '").val())', $tmp);
        }
      }
      $outputFunction.="$tmp;";
    }
    $data['formExtra']="onchange='$outputFunction'";
    //vermogenOnroerendGoed=vermogenHypotheek+vermogenOverigVermogen
  }
}


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