<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 augustus 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/01/22 16:02:33 $
    File Versie         : $Revision: 1.12 $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "contractueleuitsluitingenList.php";
$__funcvar['location'] = "contractueleuitsluitingenEdit.php";

$object = new ContractueleUitsluitingen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";


$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';//<link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">

$editcontent['body'] = "onLoad=\"javascript:selectieChanged('init'); \"";

$db=new DB();
$query="SELECT Valuta,Omschrijving FROM Valutas ORDER BY Valuta";
$db->SQL($query);
$db->Query();
$valutaJs='';

$n=0;
$valutaJs.= "var valutaArr = [";// ['AAND', 'Aandeel' ], [ 'OBL', 'Obligatie' ], [ 'OPT', 'Optie' ], [ 'STOCKDIV', 'Stockdividend' ], [ 'TURBO', 'Turbo' ], [ 'OVERIG', 'Overig' ], ['INDEX', 'Index'] ];
while($data=$db->nextRecord())
{
	if($n>0)
		$valutaJs.=",";
	$valutaJs.="['".$data['Valuta']."', '".$data['Omschrijving']."']";
	$n++;
}
$valutaJs.="];";


$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var Veld = '';
var ajax = new Array();
function selectieChanged(init)
{
  checkFonds();
  value = document.getElementById('categorie').value;
  getSelectie(document.editForm.categoriesoort.value,document.editForm.vermogensbeheerder.value);

  if(document.editForm.vermogensbeheerder.readOnly==false || document.editForm.vermogensbeheerder.readOnly == undefined)
  {
    getPortefeuilles(document.editForm.vermogensbeheerder.value,'Portefeuilles','portefeuille');
  }
}

function vulCategorieSoort()
{
    oldValue=  document.getElementById('categoriesoort').value;
   	if(document.editForm.fonds.value != '')
  	{
  	  var arr = [['Reservering', 'Reservering'] ];
  	  // document.getElementById('categorie').options.length=0;
  	  
    }
    else
    {
      var arr = [['Beleggingscategorien', 'Beleggingscategorien' ], [ 'Beleggingssectoren', 'Beleggingssectoren' ], [ 'Fondssoort', 'Fondssoort' ], [ 'Regios', 'Regios' ], [ 'afmCategorien', 'afmCategorien' ], [ 'Valuta', 'Valuta' ], ['Rating', 'Rating'], ['Zorgplicht', 'Zorgplichtcategorien'], ['Hoofdcategorien', 'Hoofdcategorien'], ['Reservering', 'Reservering'] ];
      
    }

   if(arr.length > 0)
   {
      document.getElementById('categoriesoort').options.length=0;
      AddName('editForm','categoriesoort','---','');
      for(var i=0;i<arr.length;i++)
      {
         if(arr[i] != '')
         {
           AddName('editForm','categoriesoort',arr[i][0],arr[i][1]);
         }
      }
  }
  else
  {
    document.getElementById('categoriesoort').options.length=0;
  }

  document.getElementById('categoriesoort').value = oldValue;
  
}


function checkFonds(object)
{
    vulCategorieSoort();
    if(document.editForm.fonds.value != '')
    {
      var enableCategorie=true;
    }
    else
    {
      var enableCategorie=true;
    }

    if(enableCategorie==false)
    {
      $('#categorie').prop('disabled', true);
    }
    else
    {
    
      $('#categorie').prop('disabled', false);
    }
    
  checkReservering(document.editForm.categoriesoort.value);
   
  
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

function getRekeningen (sel,veld)
{
  var oldValue = document.getElementById(veld).value;
  var portefeuille = sel;
	if(portefeuille.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query=rekeningen|'+portefeuille;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden2(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function portefeuilleChanged()
{
  getRekeningen(document.editForm.portefeuille.value,'geldrekening');
  
}

function checkReservering(tabel)
{

  if(tabel=='Reservering' || tabel=='')
  {
    $('#categorie').prop('value', '');
    $('#categorie').prop('disabled', true);
  
    $('#soortReservering').prop('disabled', false);
    $('#geldrekening').prop('disabled', false);
    $('#bedrag').prop('disabled', false);
  }
  else
  {
    $('#categorie').prop('disabled', false);
    $('#soortReservering').prop('disabled', true);
    $('#geldrekening').prop('disabled', true);
    $('#bedrag').prop('disabled', true);
  
    $('#soortReservering').prop('value', '');
    $('#geldrekening').prop('value', '');
    $('#bedrag').prop('value', '');
  }
}

function getSelectie(tabel,vermogensbeheerder)
{

  checkReservering(tabel);
  
  if(tabel=='Reservering')
  {

  }
  else
  {
  	if(tabel.length>0)
  	{
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Categorie';
		var oldValue = document.getElementById('categorie').value;
		if( tabel == 'Fondssoort' )
		{
		  var arr = [['AAND', 'Aandeel' ], [ 'OBL', 'Obligatie' ], [ 'OPT', 'Optie' ], [ 'STOCKDIV', 'Stockdividend' ], [ 'TURBO', 'Turbo' ], [ 'OVERIG', 'Overig' ], ['INDEX', 'Index'] ];
		  document.getElementById('categorie').options.length=0;
      for(var i=0;i<arr.length;i++)
      {
        AddName('editForm','categorie',arr[i][0]+' - '+arr[i][1],arr[i][0]);
      }
		}
		else if( tabel == 'Valuta' )
		{
		  $valutaJs
		  document.getElementById('categorie').options.length=0;
      for(var i=0;i<valutaArr.length;i++)
      {
        AddName('editForm','categorie',valutaArr[i][0]+' - '+valutaArr[i][1],valutaArr[i][0]);
      }
		}
		else
		{
      if( tabel == 'Zorgplichtcategorien' )
      {
	  	  ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel+'|vermogensbeheerder';	// Specifying which file to get
  		}
      else
      {
        if(tabel=='Hoofdcategorien'){tabel='Hoofdcategorien2';}
        ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+vermogensbeheerder+'|'+tabel;	// Specifying which file to get      
      }  
      ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
	  	ajax[index].onError = function(){ alert('Ophalen waarden mislukt.') };
	  	ajax[index].runAJAX();		// Execute AJAX function
		}
		document.getElementById('categorie').value = oldValue;
	}
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
                ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
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
             AddName('editForm',veld,parts[0]+' - '+parts[1],parts[0]);
           }
    }
        }
        document.getElementById(veld).value = oldValue;
}



function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	document.getElementById('categorie').options.length=0;
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
     var parts=elements[i].split('\\t');
     AddName('editForm','categorie',parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.editForm.categorie.value = value;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";


$editObject->template = $editcontent;

$data = $_GET;
if($data['fonds']<>'')
{
	$data['categorie'] = '';
	if($data['categoriesoort']<>'Reservering')
	  $data['categoriesoort'] = '';
}
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;

if ($_GET['portefeuille'])
{
	$DB = new DB();
	$object->set('portefeuille', $_GET['portefeuille']);
	$q = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . $_GET['portefeuille'] . "'";
	$DB->SQL($q);
	$vermogensbeheerder = $DB->lookupRecord();
	$object->set('vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
	$editObject->verzendVermogensbeheerder=$vermogensbeheerder['Vermogensbeheerder'];
}
//listarray($data);exit;
$editObject->controller($action,$data);
$htmlTemplate=$editObject->getTemplate();
$htmlTemplate=str_replace('</form></div>','<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">{verzendKnop}</div></form></div>' , $htmlTemplate);
$editObject->formTemplate=$htmlTemplate;
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($data['frame']==1)
{

	$object->setOption('portefeuille', 'form_type', 'text');
	$object->setOption('portefeuille', 'form_extra', 'READONLY');
	$object->setOption('vermogensbeheerder', 'form_type', 'text');
	$object->setOption('vermogensbeheerder', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
		$editObject->formVars["verzendKnop"] =vt('Geen rechten om te verzenden.');
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