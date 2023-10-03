<?php

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "referentieportefeuilleperbeleggingscategorieList.php";
$__funcvar['location'] = "referentieportefeuilleperbeleggingscategorieEdit.php";

$object = new ReferentieportefeuillePerBeleggingscategorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad=\"javascript:selectieChanged();\"";

if($_GET['frame']==1)
{
  $setPortefeuille='';
}
else
{
  $setPortefeuille='getPortefeuilles(document.editForm.Vermogensbeheerder.value,\'Portefeuilles\',\'Portefeuille\');';
}

$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var Veld = '';
var ajax = new Array();
function selectieChanged()
{
  value = document.getElementById('Categorie').value;
  getSelectie(document.editForm.Categoriesoort.value,document.editForm.Vermogensbeheerder.value);
  $setPortefeuille
  getPortefeuilles(document.editForm.Vermogensbeheerder.value,'Portefeuilles','Referentieportefeuille');
 // getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingscategorien','Beleggingscategorie');
 // getWaarden(document.editForm.Vermogensbeheerder.value,'BeleggingsEnHoofdcategorien','Beleggingscategorie');
  

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
		ajax[index].onCompletion = function(){ setWaarden2(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function getSelectie(tabel,vermogensbeheerder)
{

	if(tabel.length>0){
	
	  if(tabel=='Algemeen')
	  {
	   document.getElementById('Categorie').options.length=0;
	    return false;
	  }
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Categorie';
    if( tabel == 'Zorgplichtcategorien' )
    {
		  ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel+'|vermogensbeheerder';	// Specifying which file to get
		}
    else
    {
      ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel;	// Specifying which file to get      
    }  
    ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen waarden mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
	else
	{
	 document.getElementById('Categorie').options.length=0;
	}
}	
  function getPortefeuilles (sel,tabel,veld)
  {
  var oldValue = document.getElementById(veld).value;
  var vermogensbeheerder = sel;
        if(vermogensbeheerder.length>0){
                var index = ajax.length;
                ajax[index] = new sack();
                ajax[index].element = veld;
                ajax[index].requestFile = 'lookups/ajaxLookup.php?module=PortefeuillesPerVermogensbeheerder&query='+vermogensbeheerder+'|indexPerBeleggingscategorie'; // Specifying which file to get
                ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };       // Specify function that will be executed after file has been found
                ajax[index].onError = function(){ alert('Ophalen portefeuilles mislukt.') };
                ajax[index].runAJAX();          // Execute AJAX function
                 }
  }


function setWaarden(index,veld,oldValue)
{
        var     Waarden = ajax[index].response;
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

function setWaarden2(index,veld,oldValue)
{
        var     Waarden = ajax[index].response;
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



function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	document.getElementById('Categorie').options.length=0;
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
     var parts=elements[i].split('\\t');
     AddName('editForm','Categorie',parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.editForm.Categorie.value = value;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;

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


$editObject->controller($action,$data);


$htmlTemplate=$editObject->getTemplate();
$htmlTemplate=str_replace('</form></div>','<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">{verzendKnop}</div></form></div>' , $htmlTemplate);
$editObject->formTemplate=$htmlTemplate;


// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($data['frame']==1)
{
  
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
?>