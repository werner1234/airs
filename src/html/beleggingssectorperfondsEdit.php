<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.23 $

 		$Log: beleggingssectorperfondsEdit.php,v $
 		Revision 1.23  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("Beleggingssector per fonds muteren");

$__funcvar['listurl']  = "beleggingssectorperfondsList.php";
$__funcvar['location'] = "beleggingssectorperfondsEdit.php";

$object = new BeleggingssectorPerFonds();

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['javascript'] = '';
if( ! requestType('ajax') )
{
  /**
   * Let op! javascript zit ook in beleggingsSectorDialogData
   */
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
      getWaarden(document.editForm.Vermogensbeheerder.value,'Beleggingssectoren','Beleggingssector');
      getWaarden(document.editForm.Vermogensbeheerder.value,'Regios','Regio');
      getWaarden(document.editForm.Vermogensbeheerder.value,'AttributieCategorien','AttributieCategorie');
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
      if(elements.length >1)
      {
        document.getElementById(veld).options.length=0;
        AddName('editForm',veld,'---','');
        for(var i=0;i<elements.length;i++)
        {
         if(elements[i] != '')
         {
           //AddName('editForm',veld,elements[i],elements[i])
           var parts=elements[i].split('\\t');
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
if( requestType('ajax') ) {
  $object->formId = 'beleggingsSectorPerFondsForm';
  $object->formName = 'beleggingsSectorPerFonds';
}


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];


$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('BeleggingssectorPerFonds', 'Fonds', 'Fonds');

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

/** if ajax disable header and footer **/
if( requestType('ajax') ) {
  $editObject->includeHeaderInOutput = false;
}

$editObject->formVars['newFonds'] = (isset($data['newFonds'])? $data['newFonds']:0);
if( isset($_GET['frame']) && $_GET['frame'] == 1)
{
  $object->setOption('Fonds','form_type','text');
  $object->setOption('Fonds','form_extra','READONLY');
  if(isset($_GET['Fonds']))
    $object->set('Fonds',$_GET['Fonds']);
  if(isset($_GET['Vermogensbeheerder']))
    $object->set('Vermogensbeheerder',$_GET['Vermogensbeheerder']);
  if($__appvar['master']==false)
    $editObject->object->data['fields']['Vermogensbeheerder']['form_visible']=false;

  if( ! requestType('ajax') )
  {
    if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
        $editObject->formVars["submit"]='
   <a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
   <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;terug</a><input type="hidden" name="frame" value="1">';
    elseif(checkAccess())
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;'.vt("verwijder").'</a>
  <!--<a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>-->
  <input type="hidden" name="frame" value="1">';


    $html=$editObject->getOutput();
    $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);

    echo $html;
  }
  else
  {
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
    $html = $editObject->getOutput();
    $html=str_replace('<div class="form">','<div class="form"><input type="hidden" value="'.$editObject->formVars['newFonds'].'" name="newFonds" >', $html);
  }
  $returnUrl='blankFondsKoppeling.php';
}
else
{
  if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
    $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>';
  $html=$editObject->getOutput();
  $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
}

if ($result = $editObject->result)
{
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
      <a href="#" id="beleggingsSectorSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").' align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
      <a href="#" >
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a>
      <input type="hidden" name="frame" value="1">
    ';
  } elseif(checkAccess()) {
    $actions .= '<a href="#" id="beleggingsSectorSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="'.vt("sla de wijzigingen op").'" align="absmiddle">&nbsp;'.vt("Opslaan").'</a>';
    if ( $action !== 'new' ) {
      $actions .= '<a href="#" id="beleggingsSectorRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="'.vt("verwijder record").'" align="absmiddle">&nbsp;'.vt("verwijder").'</a>';
    }
    $actions .= '
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="'.vt("Ga terug zonder opslaan").'" align="absmiddle">&nbsp;'.vt("terug").'</a>
      <input type="hidden" name="frame" value="1">
    ';
  }
  echo template('templates/ajax_head.inc');
  echo $AETemplate->parseFile('jqueryDialog/beleggingsSectorDialogData.html', array(
    'html'          => $html,
    'actions'       => $actions,
    'javascript'    => $editcontent['javascript']
  ));
  echo template('templates/ajax_voet.inc', $editObject->template);
}
