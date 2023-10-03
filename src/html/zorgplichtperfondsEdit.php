<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "zorgplichtperfondsList.php";
$__funcvar['location'] = "zorgplichtperfondsEdit.php";

$object = new ZorgplichtPerFonds();


$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br>";

$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";


if( ! requestType('ajax') )
{
  if($_GET['action'] == "new")
    $editcontent['body'] = " onLoad=\"javascript:zorgplichtChanged();\" ";
  $editcontent['javascript'] .= '
  function zorgplichtChanged()
  {
    jsrsExecute("selectRS.php", populateZorgplicht, "getZorgplicht",
    buildQueryArray("editForm"), false);
  }
  function populateZorgplicht (valueTextStr)
  {
    populateDropDown(document.editForm.Zorgplicht,valueTextStr);
  }

  function buildQueryArray(theFormName) {
    var theForm = document.forms[theFormName];
    var qs = new Object();
    for (e=0;e<theForm.elements.length;e++) {
      if (theForm.elements[e].name!="") {
        qs[theForm.elements[e].name] = theForm.elements[e].value;
        }
      }
    return qs;
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
    var aOptionPairs = valueTextStr.split("|");

    for( var i = 0; i < aOptionPairs.length; i++ ){
      if (aOptionPairs[i].indexOf("~") != -1) {
        var aOptions = aOptionPairs[i].split("~");
        oItem = new Option;
        oItem.value = aOptions[1];
        oItem.text = aOptions[0];
        selField.options[selField.options.length] = oItem;
      }
    }

    selField.options.selectedIndex = 0;
  }
  function buildQueryArray(theFormName) {
    var theForm = document.forms[theFormName];
    var qs = new Object();
    for (e=0;e<theForm.elements.length;e++) {
      if (theForm.elements[e].name!="") {
        qs[theForm.elements[e].name] = theForm.elements[e].value;
        }
      }
    return qs;
  }
  ';
}
else
  $editcontent['javascript'] = '';


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen

$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('ZorgplichtPerFonds', 'Fonds', 'Fonds');

if( requestType('ajax') ) {
  $editObject->includeHeaderInOutput = false;
  $object->formId = 'zorgplichtPerFondsForm';
  $object->formName = 'zorgplichtperfondsEdit';
} 

// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Fonds FROM Fondsen ORDER BY Fonds");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Fonds"]["form_options"][] = $gb[Fonds];
}

$object->data['fields']["Vermogensbeheerder"]["form_extra"] = " onChange=\"javascript:zorgplichtChanged();\" ";

$DB->SQL("SELECT Vermogensbeheerder FROM Vermogensbeheerders ORDER BY Vermogensbeheerder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Vermogensbeheerder"]["form_options"][] = $gb[Vermogensbeheerder];
}

if($id)
{
	$q = "SELECT Zorgplichtcategorien.Zorgplicht, Zorgplichtcategorien.Omschrijving FROM Zorgplichtcategorien, ZorgplichtPerFonds WHERE Zorgplichtcategorien.Vermogensbeheerder = ZorgplichtPerFonds.Vermogensbeheerder AND ZorgplichtPerFonds.id = '".$id."'";
	$DB->SQL($q);
	$DB->Query();
	while($zp = $DB->nextRecord())
	{
		$object->data['fields']["Zorgplicht"]["form_options"][$zp['Zorgplicht']] = $zp['Omschrijving'];
	}
}

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



  if($data['frame']==1)
  {
    if($_GET['Fonds'])
      $object->set('Fonds',$_GET['Fonds']);
    if($_GET['Vermogensbeheerder'])
      $object->set('Vermogensbeheerder',$_GET['Vermogensbeheerder']);
    if($__appvar['master']==false)
      $editObject->object->data['fields']['Vermogensbeheerder']['form_visible']=false;

  $object->setOption('Fonds','form_type','text');
  $object->setOption('Fonds','form_extra','READONLY');

  if( ! requestType('ajax') )
  {
    if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a><input type="hidden" name="frame" value="1">';
    elseif(checkAccess())
        $editObject->formVars["submit"]='<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>
    <a href="#" onClick="editForm.action.value=\'delete\';editForm.submit();parent.VermogensbeheerderChanged();"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>
  <a href="#" onClick="window.history.back();"><img src="images//16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
  <input type="hidden" name="frame" value="1">';


    $frame="&frame=1";

    $html=$editObject->getOutput();
  $html=str_replace("</form></div>",'<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">'.$editObject->formVars["submit"]."</div></div></form></div>",$html);
  echo $html;
  
  } else {
    $html=$editObject->getOutput();
  }
  $returnUrl='blankFondsKoppeling.php';
  }
else
  echo $editObject->getOutput();

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
  echo template('templates/ajax_head.inc', array(
    'jsincludes' => '<script language="JavaScript" src="javascript/jsrsClient.js" type="text/javascript"></script>'
  ));
  
  $actions = '';
  if($_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0) {
//  if($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0) {
    $actions .= '  
      <a href="#" id="zorgplichtSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
      <a href="#" >
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
      <input type="hidden" name="frame" value="1">
    ';
  } elseif(checkAccess()) {
    $actions .= '<a href="#" id="zorgplichtSaveData"><img src="images/16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>';
    if ( $action !== 'new' ) {
      $actions .= '<a href="#" id="zorgplichtRemoveData"><img src="images//16/delete.gif" width="16" height="16" border="0" alt="verwijder record" align="absmiddle">&nbsp;verwijder</a>';
    }
    $actions .= '
      <a href="#" id="closeModal"><img src="images/16/terug.gif" width="16" height="16" border="0" alt="Ga terug zonder opslaan" align="absmiddle">&nbsp;terug</a>
      <input type="hidden" name="frame" value="1">
    ';
  }
  
  
  echo $AETemplate->parseFile('jqueryDialog/zorgplichtDialogData.html', array(
    'html'          => $html,
    'actions'       => $actions,
    //'javascript'    => $editcontent['javascript']
  ));

  echo template('templates/ajax_voet.inc', array());
}