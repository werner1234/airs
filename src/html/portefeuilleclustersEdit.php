<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 9 juli 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/07 12:24:58 $
    File Versie         : $Revision: 1.6 $
 		
    $Log: portefeuilleclustersEdit.php,v $
    Revision 1.6  2019/08/07 12:24:58  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$subHeader     = vt("portefeuille clusters");
$mainHeader = vt("muteren");

$__funcvar["listurl"]  = "portefeuilleclustersList.php";
$__funcvar["location"] = "portefeuilleclustersEdit.php";

$object = new PortefeuilleClusters();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$db=new DB();
$query="SELECT check_module_PORTAAL,Vermogensbeheerder FROM Vermogensbeheerders WHERE check_module_PORTAAL=1";
$db->SQL($query);
$db->query();
while($portaal=$db->nextRecord())
{
  $portaalJavascript.="portaalVermogensbeheerder['".$portaal['Vermogensbeheerder']."'] = 1;\n";
}


$editcontent['javascript'].='

var ajax = new Array();
var portaalVermogensbeheerder = new Array();
'.$portaalJavascript.'

function buildQueryArray(theFormName)
{
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
    if (theForm.elements[e].name!=\'\') {
    	qs[theForm.elements[e].name] = theForm.elements[e].value;
      }
    }
  return qs;
}

function vermogensbeheerderChanged()
{
  getSelectie(document.editForm.vermogensbeheerder.value);
  checkVermogensbeheerder();
}

function checkVermogensbeheerder()
{
  if(portaalVermogensbeheerder[document.editForm.vermogensbeheerder.value] == 1)
  {
    $(\'#portaalDiv\').show();
  }
  else
  {
    $(\'#portaalDiv\').hide();
  }
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
  var aOptionPairs = valueTextStr.split(\'|\');

  for( var i = 0; i < aOptionPairs.length; i++ ){
    if (aOptionPairs[i].indexOf(\'~\') != -1) {
      var aOptions = aOptionPairs[i].split(\'~\');
      oItem = new Option;
      oItem.value = aOptions[1];
      oItem.text = aOptions[0];
      selField.options[selField.options.length] = oItem;
    }
  }

  selField.options.selectedIndex = 0;
}



'.
	"


function getSelectie(sel)
{

	if(sel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'portefeuille1';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=BedrijfPortefuilles&query='+sel+'|vp';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen waarden mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	for(var n=1;n<31;n++)
  {
  	document.getElementById('portefeuille'+n).options.length=0;
  }
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	     tmp=elements[i].split('\\t');
 	 	   AddNamePortefeuille('editForm','portefeuille1',tmp[1],tmp[0]);
 	 }
 	}
 	var options = $('#portefeuille1').html();
 	for(var n=1;n<31;n++)
  {
    $('#portefeuille'+n).html(options);
  }
 
}

function AddNamePortefeuille(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{  
  if(p_OptionText=='---'){p_OptionValue='';}  
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";

$editcontent['body'] = "onload=\"checkVermogensbeheerder();\" ";
$editObject->template = $editcontent;

//if($_GET['action'] == "new")


$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "portefeuilleclustersEditTemplate.html";

$portefeuile=array();
if($_GET['portefeuille']<>'')
{
	$db = new DB();
	$query = "(SELECT Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . mysql_real_escape_string($_GET['portefeuille']) . "')
	UNION (SELECT VirtuelePortefeuille as Portefeuille,Vermogensbeheerder FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='" . mysql_real_escape_string($_GET['portefeuille']) . "')";
	$db->SQL($query);
	$db->query();
	$portefeuile = $db->nextRecord();
	$editObject->verzendVermogensbeheerder=$portefeuile['Vermogensbeheerder'];
}

$editObject->controller($action,$data);

if($_GET['portefeuille']<>'')
{
	$object->set('vermogensbeheerder',$portefeuile['Vermogensbeheerder']);
	$object->setOption('vermogensbeheerder','form_extra','readonly');
	$object->setOption('vermogensbeheerder', 'form_type', 'text');
	$alreadySet=false;
	for($i=1;$i<31;$i++)
	{
	  if($object->get('portefeuille'.$i)==$portefeuile['Portefeuille'])
		{
			$alreadySet=true;
			$object->setOption('portefeuille'.$i,'form_extra','readonly');
			$object->setOption('portefeuille'.$i,'form_type', 'text');
			break;
		}
	}
	if($alreadySet==false)
	{
		$object->set('portefeuille1', $portefeuile['Portefeuille']);
		$object->setOption('portefeuille1','form_extra','readonly');
		$object->setOption('portefeuille1', 'form_type', 'text');
	}
}


if($object->get('vermogensbeheerder'))
{
	$portefeuilles=array();
	$DB=new DB();
	$query="(SELECT
Portefeuilles.Portefeuille, if(Portefeuilles.consolidatie=1,concat(Portefeuilles.Portefeuille,' (CON)'),Portefeuilles.Portefeuille) as Omschrijving
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Bedrijf IN(SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='".$object->get('vermogensbeheerder')."') ORDER BY Portefeuille
) ";
	/*
 UNION
(SELECT
GeconsolideerdePortefeuilles.VirtuelePortefeuille as Portefeuille, concat(GeconsolideerdePortefeuilles.VirtuelePortefeuille,\" (CON)\") as Omschrijving
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN GeconsolideerdePortefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = GeconsolideerdePortefeuilles.Vermogensbeheerder
WHERE Bedrijf IN(SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='".$object->get('vermogensbeheerder')."')
AND GeconsolideerdePortefeuilles.Einddatum > NOW()) ORDER BY Portefeuille
 
 ";
*/
	$DB->SQL($query);
	$DB->Query();
	while($rec=$DB->nextRecord())
	{
		$portefeuilles[$rec['Portefeuille']]=$rec['Omschrijving'];
	}
}

for($i=1;$i<31;$i++)
{
	if(count($portefeuilles) > 0)
		$object->setOption('portefeuille'.$i,'form_options',$portefeuilles);
	else
	{

		$waarde=$object->get('portefeuille'.$i);
		if($waarde <> '')
		{
			$object->setOption('portefeuille'.$i,'form_options',array($waarde=>$waarde));
		}
	}
}

if($data['frame']==1)
{

	$editObject->formVars["portefeuilleFrame"]=$_GET['portefeuille'];
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
else 
{
	echo $_error = $editObject->_error;
}
