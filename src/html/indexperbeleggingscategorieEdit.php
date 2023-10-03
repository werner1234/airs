<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 6 mei 2008
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.8 $
 		
    $Log: indexperbeleggingscategorieEdit.php,v $
    Revision 1.8  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar["listurl"]  = "indexperbeleggingscategorieList.php";
$__funcvar["location"] = "indexperbeleggingscategorieEdit.php";

$object = new IndexPerBeleggingscategorie();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['body'] = "onLoad=\"javascript:selectieChanged();\"";
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
  //getPortefeuilles(document.editForm.Vermogensbeheerder.value,'Portefeuilles','Portefeuille');
 // getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingscategorien','Beleggingscategorie');
  getWaarden(document.editForm.Vermogensbeheerder.value,'BeleggingsEnHoofdcategorien','Beleggingscategorie');
  

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
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.")."') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function getSelectie(tabel,vermogensbeheerder)
{

	if(tabel.length>0){
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
                ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.")."') };
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
$action = $data["action"];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('IndexPerBeleggingscategorie', 'Fonds', 'Fonds');
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('IndexPerBeleggingscategorie', 'Portefeuille', 'Portefeuille');
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
