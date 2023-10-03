<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 18 april 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/29 15:56:45 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: uitsluitingenmodelcontroleEdit.php,v $
    Revision 1.3  2020/04/29 15:56:45  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("uitsluitingen modelcontrole");
$mainHeader   = vt("muteren");

$__funcvar['listurl']  = "uitsluitingenmodelcontroleList.php";
$__funcvar['location'] = "uitsluitingenmodelcontroleEdit.php";

$object = new UitsluitingenModelcontrole();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editcontent['body'] = "onLoad=\"javascript:selectieChanged('init'); \"";


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

  if(document.editForm.portefeuille.value != '')
  {
    getRekeningen(document.editForm.portefeuille.value,'rekening');
  }
  if(document.editForm.rekening.value != '')
  {
    rekeningChanged();
  }
// /getSelectie(document.editForm.categoriesoort.value,document.editForm.vermogensbeheerder.value);
//  if(document.editForm.vermogensbeheerder.readOnly==false || document.editForm.vermogensbeheerder.readOnly == undefined)
//  {
//   getPortefeuilles(document.editForm.vermogensbeheerder.value,'Portefeuilles','portefeuille');
//  }
}



function checkFonds(object)
{
    if(document.editForm.fonds.value == '')
    {
       $('#rekening').prop('disabled', false);
       $('#bedrag').prop('disabled', false);
       $('#Beleggingscategorie').prop('disabled', false);
    }
    else
    {
       $('#rekening').prop('value', '');
       $('#bedrag').prop('value', '');
       $('#Beleggingscategorie').prop('value', '');
       $('#rekening').prop('disabled', true);
       $('#bedrag').prop('disabled', true);
       $('#Beleggingscategorie').prop('disabled', true);
    }
}

function checkBeleggingscategorie(object)
{
    if(document.editForm.Beleggingscategorie.value == '')
    {
       $('#Beleggingscategorie').prop('disabled', false);
       $('#rekening').prop('disabled', false);
       $('#bedrag').prop('disabled', false);
       $('#Beleggingscategorie').prop('disabled', false);
       $('#fonds_autocompletefield').prop('disabled', false);
    }
    else
    {
       $('#rekening').prop('value', '');
       $('#bedrag').prop('value', '');
       $('#fonds').prop('value', '');
       $('#fonds_autocompletefield').prop('value', '');
       $('#rekening').prop('disabled', true);
       $('#bedrag').prop('disabled', true);
       $('#fonds_autocompletefield').prop('disabled', true);
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
		ajax[index].onCompletion = function(){ setWaarden2(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
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
		ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function portefeuilleChanged()
{
  getRekeningen(document.editForm.portefeuille.value,'rekening');

  
}

function rekeningChanged()
{
  if(document.editForm.rekening.value == 'alle')
  {
    document.editForm.bedrag.value=0;
    $('#bedrag').prop('disabled', true);
  }
  else
  {
    $('#bedrag').prop('disabled', false);
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
                ajax[index].onError = function(){ alert('".vt("Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt").".') };
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
          if(veld=='rekening')
          {
          AddName('editForm',veld,'Alle rekeningen','alle');
          }
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


$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;

if($data['fonds']<>'')
{
  $data['rekening']='';
  $data['bedrag']=0;
}
if($data['rekening']=='alle')
{
  $data['bedrag']=0;
}

if ($_GET['portefeuille'])
{
//	$DB = new DB();
	$object->set('portefeuille', $_GET['portefeuille']);
//	$q = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . $_GET['portefeuille'] . "'";
//	$DB->SQL($q);
//	$vermogensbeheerder = $DB->lookupRecord();
//	$object->set('vermogensbeheerder', $vermogensbeheerder['Vermogensbeheerder']);
//	$editObject->verzendVermogensbeheerder=$vermogensbeheerder['Vermogensbeheerder'];
}

$editObject->controller($action,$data);


/**
 * Portefeuille lookup
 */
$autocomplete = new Autocomplete();
$autocomplete->minLeng = 2;
$autocomplete->resetVirtualField('portefeuille_autocompletefield');
$editObject->formVars['portefeuille_autocompletefield'] = $autocomplete->addVirtuelField(
  'portefeuille_autocompletefield',
  array(
    'autocomplete' => array(
      'table' => 'Portefeuilles',
      'prefix' => true,
      'returnType' => 'expanded',
      'label' => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
      'searchable' => array('Portefeuilles.Portefeuille', 'Portefeuilles.Client'),
      'value' => 'Portefeuilles.Portefeuille',
      'conditions'   => array(
        'AND' => array(
          '(Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = "0000-00-00")',
        )
      ),
      'actions' => array(
        'select' => '
            event.preventDefault();
            $("#portefeuille").val(ui.item.value);
            portefeuilleChanged()
            $("#portefeuille_autocompletefield").val(ui.item.label);
        ',
        'change'  => '
          console.log("eventFired");
          if ( ui.item == null ) {
            $("#portefeuille").val("");
            $("#portefeuille_autocompletefield").val("");
            $("#rekening option").remove();
            portefeuilleChanged();
          }
        '
      )
    ),
    'form_extra' => '',
    'form_size' => '30',
    'validate'     => $object->data['fields']['portefeuille']['validate'],
    'form_value'   => $object->get('portefeuille'),
  )
);


/**
 * fondsLookup
 */
/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('fonds_autocompletefield');
$editObject->formVars['fonds_autocompletefield'] = $autocomplete->addVirtuelField('fonds_autocompletefield', array(
  'autocomplete' => array(
    'table' => 'Fondsen',
    'label' => array(
      'Fonds',
      'ISINCode'
    ),
    'searchable' => array(
      'Fonds',
      'ISINCode'
    ),
    'field_value' => array(
      'Fonds'
    ),
    'conditions' => array(
      'AND' => ' (Fondsen.EindDatum  >=  "' . date('Y-m-d') . '" OR Fondsen.EindDatum = "0000-00-00")'
    ),
    'value' => 'ISINCode', //value from table of join
    'actions' => array(
      'select' => '
        event.preventDefault();
        $("#fonds").val(ui.item.field_value);
        checkFonds(ui.item.value)
        $("#fonds_autocompletefield").val(ui.item.label);
        
      ',
      'change'  => '
          if ( ui.item == null ) {
            $("#fonds").val("");
            $("#fonds_autocompletefield").val("");
            checkFonds("")
          }
        '
    )
  ),
  'form_extra' => '',
  'form_size' => '30',
  'validate'     => $object->data['fields']['fonds']['validate'],
  'form_value'   => $object->get('fonds'),
));


$editcontent['javascript'] .= '$(function (){'.
  $autocomplete->getAutoCompleteVirtuelFieldScript('fonds_autocompletefield') .
  $autocomplete->getAutoCompleteVirtuelFieldScript('portefeuille_autocompletefield') .
'})';







$editObject->template = $editcontent;




$htmlTemplate=$editObject->getTemplate();
if ( (int) $data['frame'] === 1 ) {
  $htmlTemplate=str_replace('{portefeuille_inputfield}',
    '<input type="hidden" name="portefeuille" id="portefeuille" onchange="javascript:portefeuilleChanged();" value="'.$object->get('portefeuille').'" />
    <input class="" type="text" size="30" value=" '.$object->get('portefeuille').'" name="" id="" readonly="">', $htmlTemplate);
} else {
  $htmlTemplate=str_replace('{portefeuille_inputfield}', '<input type="hidden" name="portefeuille" id="portefeuille" onchange="javascript:portefeuilleChanged();" value="'.$object->get('portefeuille').'" />{portefeuille_autocompletefield}', $htmlTemplate);
}
$htmlTemplate=str_replace('{fonds_inputfield}', '<input type="hidden" name="fonds" id="fonds" onchange="checkFonds($(this));" value="'.$object->get('fonds').'" />{fonds_autocompletefield}', $htmlTemplate);
//debug(htmlspecialchars($htmlTemplate));

$htmlTemplate=str_replace('</form></div>','<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">{verzendKnop}</div></form></div>' , $htmlTemplate);
$editObject->formTemplate=$htmlTemplate;


// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($data['frame']==1)
{

	$object->setOption('portefeuille', 'form_type', 'text');
	$object->setOption('portefeuille', 'form_extra', 'READONLY');
//	$object->setOption('vermogensbeheerder', 'form_type', 'text');
//	$object->setOption('vermogensbeheerder', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
  {
    $editObject->formVars["verzendKnop"] = vt('Geen rechten om te verzenden');
  }
  
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
