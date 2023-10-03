<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 10 januari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.12 $
 		
    $Log: fondsaanvragenEdit.php,v $
    Revision 1.12  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "fondsaanvragenList.php";
$__funcvar['location'] = "fondsaanvragenEdit.php";

$object = new FondsAanvragen();
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
  getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingscategorien','Beleggingscategorie');
  getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingssectoren','Beleggingssector');
  getWaarden(document.editForm.Vermogensbeheerder.value,'Regios','Regio');
  getWaarden(document.editForm.Vermogensbeheerder.value,'AttributieCategorien','AttributieCategorie');
  getWaarden(document.editForm.Vermogensbeheerder.value,'afmCategorien','afmCategorie');
  getZorgplicht(document.editForm.Vermogensbeheerder.value,document.editForm.Zorgplicht.value);
  
  getKoppelingen();
}


function getKoppelingen (sel)
{
	var velden = document.editForm.Vermogensbeheerder.value+'|none';
	if(velden.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		//ajax[index].element = 'Beleggingscategorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=KoppelingenPerFonds&query='+velden;	// Specifying which file to get
		ajax[index].onCompletion = function(){ toonKoppelingen(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function toonKoppelingen(index)
{
 	var	waarden = ajax[index].response;
  if(waarden.search('mislukt') > 1)
  {
    alert('".vt("Ophalen van Consistentie checks mislukt")."');
  }
  Gegevens = new Array();
  GegevensCheck = new Array();
  VertalingenChecks = new Array();
 	var tabellen = waarden.split('\\t');
 	for(var i=0;i<tabellen.length;i++)
 	{
 	  Gegevens[i]	= new Array();
 	  GegevensCheck[i]	= new Array();
 	  var elements = tabellen[i].split('|');

    for(var j=0;j<elements.length;j++)
    {
      Gegevens[i][j] = new Array();
      GegevensCheck[i][j]	=0;
      var item = elements[j].split('~');
      if(item.length > 1)
      {
        Gegevens[i][j]['0']=item[0];
        Gegevens[i][j]['1']=item[1];
      }
      else
      {
        Gegevens[i][j]['0']	= elements[j];
      }
    }
 	}

  if(typeof(Gegevens[3]) == 'object'){Checks=Gegevens[3];}
  else{Checks= new Array(0,0,0,0,0,0,0);}

  VertalingenChecks[0]='Beleggingscategorie';
  VertalingenChecks[1]='Beleggingssector';
  VertalingenChecks[2]='Zorgplicht';
  VertalingenChecks[3]='Regio';
  VertalingenChecks[4]='AttributieCategorie';
  VertalingenChecks[5]='afmCategorie'; 
  VertalingenChecks[6]='Duurzaamheid'; 
  
  for(var j=0;j<Checks.length;j++)
  {
      if(Checks[j]==0)
      {
       document.getElementById(VertalingenChecks[j]).disabled=true;
       //document.getElementById(VertalingenChecks[j]).style.color='#ccc';
       document.getElementById(VertalingenChecks[j]).style.backgroundColor='#ccc'; 
      }
      else
      {
        document.getElementById(VertalingenChecks[j]).disabled=false;
        //document.getElementById(VertalingenChecks[j]).style.color='#000';
        document.getElementById(VertalingenChecks[j]).style.backgroundColor='#fff';
      }
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

function getZorgplicht(sel,value)
{
	var vermogensbeheerder = sel;
	if(vermogensbeheerder.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Zorgplicht';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=ZorgplichtPerVermogensbeheerder&query='+vermogensbeheerder;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setValues(index,'Zorgplicht',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen Zorgplicht waarden mislukt").".') };
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
   	   var parts=elements[i].split('\\t');
       //alert(parts[0]);
 	     AddName('editForm',veld,parts[0]+' - '+parts[1],parts[0]);
 	   }
    }
 	}
 	document.getElementById(veld).value = oldValue;
  //alert(veld+' '+oldValue+' '+document.getElementById(veld).value);
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

if($__appvar["bedrijf"] != "HOME")
{
  $object->dbId=2;
}

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$subHeader=urldecode($_GET['message']);
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b><br>\n".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
//listarray($data);exit;
$conversieVelden=array('fondsOptieSymbolen'=>'OptieSymbool','optieOptieType'=>'OptieType','optieOptieUitoefenPrijs'=>'OptieUitoefenPrijs');//,'optieIdentifierVWD'=>'identifierVWD'
foreach($conversieVelden as $old=>$new)
{
  if(isset($data[$old]) && $data[$old]<>'')
    $data[$new] = $data[$old];
}
if(isset($data['optieexpiratieMaand']) && isset($data['optieexpiratieJaar']) && $data['optieexpiratieMaand'] <> '' && $data['optieexpiratieJaar'] <> '')
{
  $data['OptieExpDatum'] = $data['optieexpiratieJaar'].$data['optieexpiratieMaand'];
}
$action = $data['action'];

$AETemplate = new AE_template();

if($__appvar["bedrijf"] != "HOME")
{
  $editObject->extraNavSettings['save']['text']='versturen';
  $editObject->extraNavSettings['save']['tip']='versturen naar AIRS';
  $editObject->extraNavSettings['opslaanNietVerlaten']['hidden']=true;

}
else
{
   $db=new DB();
   $query="SELECT verwerkt FROM fondsAanvragen WHERE id='".$data['id']."'";
   $db->SQL($query);
   $db->query();
   $laatsteStatus=$db->nextRecord();
}


$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->formTemplate = "fondsaanvragenEditTemplate.html";
$editObject->usetemplate = true;
if($action=='delete')
{
  $data['verwerkt']=-1;
  $action='update';
}

$editObject->controller($action,$data);

/** Bij toevoegen nieuwe toon hier de turbo en optie symbolen **/
$jsData = array();

$OptieExpDatum = $object->data['fields']['OptieExpDatum']['value'] ;
$expJaarDb=substr($OptieExpDatum,0,4);
$expMaandDb=substr($OptieExpDatum,4,2);

$optieJaar=substr($object->get('OptieExpDatum'),0,4);
$OptieExpJaar='';
$huidigeJaar = date('Y') - 1; //get current year minus one for history
if($optieJaar<>'' && $optieJaar<$huidigeJaar)
  $OptieExpJaar .= "<option value=\"".$optieJaar."\" SELECTED>".$optieJaar."</option>";

for ($i;$i<10;$i++)
{
  $expJaar = $huidigeJaar + $i;
  if ($expJaar == $expJaarDb)
    $OptieExpJaar .= "<option value=\"".$expJaar."\" SELECTED>".$expJaar."</option>";
  else
    $OptieExpJaar .= "<option value=\"".$expJaar."\" >".$expJaar."</option>";
}
$editObject->formVars["OptieExpJaar"]=$OptieExpJaar;

$huidigeMaand= date('n');
for($i=1; $i<13; $i++)
{
  if ($i<10)
    $maandString='0'.$i;
  else
    $maandString=$i;

  if ($i == $expMaandDb)
    $OptieExpMaand .= "<option value=\"$maandString\" SELECTED>".$__appvar["Maanden"][$i]." </option>";
  else
    $OptieExpMaand .= "<option value=\"$maandString\" >".$__appvar["Maanden"][$i]." </option>";
}
$editObject->formVars["OptieExpMaand"]=$OptieExpMaand;

include_once('fondsEditOptie.php');
$editObject->formVars['createOption'] = ' <ul class="fieldset-nav nav nav-tabs">
    <li role="presentation">
      <a id="showOptionForm" href="#">
        <div class="openText" ><img class="simbisIcon" src="icon/16/add.png"> '.vt("Optie").'</div>
        <div class="closeText hideItem"><img class="simbisIcon" src="icon/16/delete.png"> '.vt("Optie").' </div>
      </a>
    </li>
  </ul> ';

$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

$editObject->template['javascript'] .= $AETemplate->parseFile('fondsEdit/js/fondsEdit.js', $jsData);
$editObject->formVars['fondsEditStyle'] = $AETemplate->parseFile('fondsEdit/css/fondsEdit.css');

if($object->data['fields']['OptieSymbool']['value'] == '')
  $editObject->formVars['optieFormStyle']='style="display:none"';

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
//echo $editObject->getTemplate();exit;
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($__appvar['bedrijf']<>'HOME')
  {
    header("Location: fondsaanvragenEdit.php?action=new&message=".urldecode(vt("Fondsaanvraag voor")." ".$_GET['ISINCode']." ".vt("verzonden").".<br>"));
    exit;
  }
  else
  {
    if($__appvar['bedrijf']=='HOME' && $object->get('id') > 0 && $action=='update')
    { 
      if($object->get('verwerkt') <> $laatsteStatus['verwerkt'])
      {
        $object->sendFondsaanvraagEmail();
      }
    }   
    
  	header("Location: ".$returnUrl);
  }
}
else 
{
	echo $_error = $editObject->_error;
}
?>