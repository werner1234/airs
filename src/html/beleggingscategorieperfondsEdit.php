<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "beleggingscategorieperfondsList.php";
$__funcvar['location'] = "beleggingscategorieperfondsEdit.php";

$object = new BeleggingscategoriePerFonds();

$editcontent['javascript'] = '';
if( ! requestType('ajax') )
{
  /** 
   * Let op! javascript zit ook in beleggingsSectorDialogData
   */
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
  getWaarden(document.editForm.Vermogensbeheerder.value,'afmCategorien','afmCategorie');
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
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
  ";
}

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$editObject->formTemplate = "beleggingscategorieperfondsEditTemplate.html";
$editObject->usetemplate = true;

$data = $_GET;
$action = $data['action'];

if ($action == 'update')
{
  $grafiekKleuren = array ('R'=>array('value'=>$data['grafiekKleur_R']),
	  	  				 'G'=>array('value'=>$data['grafiekKleur_G']),
		  	  			 'B'=>array('value'=>$data['grafiekKleur_B']));
  $data['grafiekKleur']	=		 serialize($grafiekKleuren);
}



$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('BeleggingscategoriePerFonds', 'Fonds', 'Fonds');


$editObject->controller($action,$data);

/** als request type = ajax return json voor jquery bij update of verwijderen **/
if( requestType('ajax') && ($action == 'update' || $action == 'delete') ) {
  if ($editObject->object->error == false) {
      echo json_encode(array(
        'success' => true, 
        'saved'   => true,
        'Vermogensbeheerder' => $_GET['Vermogensbeheerder'],
      )); //let ajax know the request ended in success
      exit();
  } else {
    $currentErrors = $object->getErrors();
    $jsonReplaces = array(array("\\\\", "/", "\n", "\t", "\r", "\b", "\f"), array('\\', '/', '', '', '', '', ''));
    foreach ( $currentErrors as $currentErrorKey => $currentError ) {
      $currentErrors[$currentErrorKey]['message'] = str_replace($jsonReplaces[0], $jsonReplaces[1], $currentError['message']);
    }
    echo json_encode(array(
        'success'               => true,
        'saved'                 => false,
        'Vermogensbeheerder'    => $_GET['Vermogensbeheerder'],
        'message'               => $editObject->_error,
        'errors'                => $currentErrors
      )); //let ajax know the request ended in failure
  }
  exit();
}

$frafiekKleur = $object->get('grafiekKleur');
if ( empty($frafiekKleur) ){$frafiekKleur = '';}
  
  $grafiekKleuren = unserialize($frafiekKleur);
  $editObject->formVars["grafiekKleur"] = '';

$kleuren=array('R','G','B');
foreach ( $kleuren as $kleur )
{
  $editObject->formVars["grafiekKleur"] .= ' <input size="3" maxlength="3" type="text" value="' . $grafiekKleuren[$kleur]['value'] . '"
  class="colorp" id="grafiekKleur_' . $kleur . '" data-group="grafiekKleur" name="grafiekKleur_' . $kleur . '" >';
}
//$editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['R']['value']."\" id=\"grafiekKleur_R\" name=\"grafiekKleur_R\" > \n";
//  $editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['G']['value']."\" id=\"grafiekKleur_G\" name=\"grafiekKleur_G\" > \n";
//  $editObject->formVars["grafiekKleur"] .= "<input size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$grafiekKleuren['B']['value']."\" id=\"grafiekKleur_B\" name=\"grafiekKleur_B\" > \n";
$editObject->formVars["grafiekKleur"] .= '<div id="grafiekKleur-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option">
                <input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
/** if ajax disable header and footer **/
$editObject->formVars['formName'] = 'editForm';
if( requestType('ajax') ) {
  $editObject->includeHeaderInOutput = false;
  $editObject->formVars['formName'] = 'beleggingscategorie';
}

  $editObject->formVars['newFonds'] = (isset($data['newFonds'])? $data['newFonds']:0);
  if($data['frame']==1)
  {
    if(isset($_GET['Fonds']))
      $object->set('Fonds',$_GET['Fonds']);
    if(isset($_GET['Vermogensbeheerder']))
      $object->set('Vermogensbeheerder',$_GET['Vermogensbeheerder']);
    if($__appvar['master']==false)
      $editObject->object->data['fields']['Vermogensbeheerder']['form_visible']=false;
    
  $object->setOption('Fonds','form_type','text');
  $object->setOption('Fonds','form_extra','READONLY');

  if( ! requestType('ajax') )
  {
    if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
//    if($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0)
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a><input type="hidden" name="frame" value="1">';
    elseif(checkAccess())
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("sla de wijzigingen op").'" align="absmiddle">&nbsp;'.vt("Opslaan").'</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="'.vt("verwijder record").' align="absmiddle">&nbsp;'.vt("verwijder").'</a>
  <a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a>
  <input type="hidden" name="frame" value="1">';
    $frame="&frame=1";


    $html=$editObject->getOutput();
    $html=str_replace("</div>
</form>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
    echo $html;
  } else {
    /** set airs datepicker on ajax form fields **/
    $object->addClass('Vanaf', 'AIRSdatepicker');
    if ( isset ($object->data['fields']['Vanaf']['form_extra']) )
    {
      $object->data['fields']['Vanaf']['form_extra'] = $object->data['fields']['Vanaf']['form_extra'] . ' onchange=\"date_complete(this);\"';
    } 
    else 
    {
      $object->data['fields']['Vanaf']['form_extra'] = ' onchange="date_complete(this);"';
    }
    $html=$editObject->getOutput();
  }
  $returnUrl="blankFondsKoppeling.php";
  }
else
{
  if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
    $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>';
  $html=$editObject->getOutput();
  $html=str_replace("</div>
</form>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;

}


if ($result = $editObject->result)
{
  if( requestType('ajax') ) {echo json_encode(array('success' => true, 'Vermogensbeheerder' => $_GET['Vermogensbeheerder']));} //let ajax know the request ended in success
  if($editObject->message)
    $returnUrl .="?message=".urlencode($editObject->message);
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}


/** ajax functionaliteit voor jquery modal **/
$AETemplate = new AE_template();
if( requestType('ajax') ) {
  $actions = '';
  if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0) {
//  if($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0) {
    $actions .= '  
      <a href="#" id="beleggingsCategorieSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
      <a href="#" >
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a>
      <input type="hidden" name="frame" value="1">
    ';
  } elseif(checkAccess()) {
    $actions .= '<a href="#" id="beleggingsCategorieSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="'.vt("sla de wijzigingen op").'" align="absmiddle">&nbsp;'.vt("Opslaan").'</a>';
    if ( $action !== 'new' ) {
      $actions .= '<a href="#" id="beleggingsCategorieRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="'.vt("verwijder record").'" align="absmiddle">&nbsp;'.vt("verwijder").'</a>';
    }
    $actions .= '
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a>
      <input type="hidden" name="frame" value="1">
    ';
  }
  echo template('templates/ajax_head.inc', $editObject->template);
  echo $AETemplate->parseFile('jqueryDialog/beleggingsCategorieDialogData.html', array(
    'html'          => $html,
    'actions'       => $actions,
    'javascript'    => $editcontent['javascript']
  ));

  echo template('templates/ajax_voet.inc', $editObject->template);
}