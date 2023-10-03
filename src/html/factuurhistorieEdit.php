<?php
/*
    AE-ICT CODEX source module versie 1.6, 17 oktober 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.7 $

    $Log: factuurhistorieEdit.php,v $
    Revision 1.7  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.6  2012/02/19 16:11:27  rvv
    *** empty log message ***

    Revision 1.5  2010/03/03 15:50:28  rvv
    *** empty log message ***

    Revision 1.4  2010/02/17 11:28:09  rvv
    *** empty log message ***

    Revision 1.3  2009/12/23 14:58:48  rvv
    *** empty log message ***

    Revision 1.2  2009/12/13 17:26:06  rvv
    *** empty log message ***

    Revision 1.1  2009/10/17 16:00:33  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";
$db=new DB();

$__funcvar[listurl]  = "factuurhistorieList.php";
$__funcvar[location] = "factuurhistorieEdit.php";

$object = new FactuurHistorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent[jsincludes] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent[body] = "onLoad='javascript:portefeuilleChanged();'";
$editcontent[javascript] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var ajax = new Array();
function portefeuilleChanged()
{
  getBtw(document.editForm.portefeuille.value);
  statusChanged();
}

function getBtw (sel)
{
	var portefeuille = sel;
	if(portefeuille.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'btwProcent';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=btwPortefeuille&query='+portefeuille;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setBtw(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen btw percentage voor portefeuille mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setBtw(index)
{
 	var	btw = ajax[index].response;
 	var elements = btw.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   document.editForm.btwProcent.value=elements[i];
 	 }
 	}
}

function feeChanged()
{
  document.editForm.btw.value=round(document.editForm.fee.value*document.editForm.btwProcent.value/100,2);
  document.editForm.totaalIncl.value=round(parseFloat(document.editForm.fee.value)+parseFloat(document.editForm.btw.value),2);
}

function statusChanged()
{
  if(document.editForm.portefeuille.disabled == false)
  {
    document.editForm.factuurNr.value = '';
    if(document.editForm.status.value == 1 && document.editForm.portefeuille.value != '' && document.editForm.factuurDatum.value != '' )
    {
      getFactuurNr(document.editForm.portefeuille.value + '|' + document.editForm.factuurDatum.value);
    }
    else
    {
      document.editForm.status.value = 0;
    }
  }
}


function getFactuurNr (sel)
{
  if(document.editForm.factuurNr.value == '')
  {
	var velden = sel;
	if(velden.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'btwProcent';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=factuurNr&query='+velden;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setFactuurNr(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen btw percentage voor portefeuille mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	  }
	}
}

function setFactuurNr(index)
{
 	var	btw = ajax[index].response;
 	var elements = btw.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   document.editForm.factuurNr.value=elements[i];
 	 }
 	}
}

";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data[action];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "factuurhistorieTemplate.html";

//
$editObject->controller($action,$data);

$query="SELECT BeheerfeeBTW FROM Portefeuilles WHERE Portefeuille='".$object->get('portefeuille')."'";
$db->SQL($query);
$btw=$db->lookupRecord();

$editObject->formVars['btw']=$btw['BeheerfeeBTW'];
$object->setOption('portefeuille','form_extra',"onchange=\"javascript:portefeuilleChanged();\"");
$object->setOption('fee','form_extra',"onchange=\"javascript:feeChanged();\"");
$object->setOption('status','form_extra',"onchange=\"javascript:statusChanged();\"");
$object->setOption('factuurNr','form_extra',"READONLY");


$db=new db();
if($db->QRecords("SELECT id FROM FactuurHistorie WHERE id='".$object->get('id')."' AND status='1'"))
{
  $object->setOption('portefeuille','form_extra',"DISABLED");
  $object->setOption('factuurNr','form_extra',"READONLY");
  $object->setOption('periodeDatum','form_extra',"READONLY");
  $object->setOption('periodeDatum','form_type',"text");
  $object->set('periodeDatum',dbdate2form($object->get('periodeDatum')));
  $object->setOption('grondslag','form_extra',"READONLY");
  $object->setOption('fee','form_extra',"READONLY");
  $object->setOption('btw','form_extra',"READONLY");
  $object->setOption('totaalIncl','form_extra',"READONLY");
  $object->setOption('status','form_extra',"DISABLED");
  $object->setOption('omschrijving','form_extra',"READONLY");
  $object->setOption('factuurDatum','form_extra',"READONLY");
  $object->setOption('factuurDatum','form_type',"text");
  $object->set('factuurDatum',dbdate2form($object->get('factuurDatum')));
  if($object->get('betaald') > 0)
  {
    $object->setOption('betaald','form_extra',"DISABLED");
  }
}
else
{
  $object->setOption('betaald','form_extra',"DISABLED");
}


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