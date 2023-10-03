<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/08 18:23:43 $
  File Versie         : $Revision: 1.4 $

 */
include_once("wwwvars.php");
$AEMessage = new AE_Message();
$AETemplate = new AE_template();
$AENumber = new AE_Numbers();

$data = array_merge($_GET, $_POST);/** merge data * */
$data['action'] = 'edit';
$action = $data['action'];

$AEParticipants = new AE_Participants();


/** if we did an ajax post combine data **/
if ( isset ($data['saveRow']) ) {
  $fieldErrors = '';
  $aantalError = false;
  /** first lets validate the data if empty show message kill script **/
  if ( in_array($data['postData']['transactietype'], array('B', 'A', 'D', 'BK', 'H'))) {
    if ( (float)$data['postData']['aantal'] < 0 ) {
      $fieldErrors['aantal'] = array('description' => 'aantal', 'message' => vt('Aantal dient positief ingevoerd te worden!'));
      $aantalError = true;
    }
  } else {
    if ( (float)$data['postData']['aantal'] > 0 ) {
      $fieldErrors['aantal'] = array('description' => 'aantal', 'message' => vt('Aantal dient negatief ingevoerd te worden!'));
      $aantalError = true;
    }
  }


  if ( empty ($data['postData']['Fonds']) || empty ($data['postData']['participanten_id']) || $aantalError == true ) {

    $AEMessage->setMessage(vt('U heeft niet alle velden ingevoerd!'), 'info');
    //check required fields



    if ( empty ($data['postData']['Fonds']) ) {$fieldErrors['Fonds'] = array('description' => 'Fonds', 'message' => vt('Mag niet leeg zijn!'));}
    if ( empty ($data['postData']['participanten_id']) ) {$fieldErrors['client'] = array('description' => 'client', 'message' => vt('Mag niet leeg zijn!'));}
    
    echo json_encode(array(
      'template' => '',
      'message' => $AEMessage->getMessage(),
      'fieldErrors' => $fieldErrors
    ));
    exit;
  }
  $data = array_merge($data, $data['postData']);
}

$fondsObject = new Fonds ();
$participantenFondsVerloop = new ParticipantenFondsVerloop();

/**
 * setup fonds selection
 */
$editObject = new editObject($participantenFondsVerloop);
$editObject->template = $editcontent;
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = "participants_inputMutationsTemplate.html";
$editObject->usetemplate = true;


/** before we set some fields check if we need a save **/

/** we have all the date here, if we have postdate lets save the record **/
if ( isset ($data['saveRow']) && $data['saveRow'] == true ) {
  $template = '';
  $fieldErrors = array();
  
  //unset data before savind
  unset($data['postData']);
  unset($data['saveRow']);
  unset($data['action']);
  
  $data['aantal'] = $AEParticipants->formatAantal($data['aantal'], $data['transactietype']);

  $editObject->controller('update', $data);//save $data
  
  if ($editObject->object->error != true) {
    $data['id'] = $participantenFondsVerloop->get('id');
    $AEMessage->setMessage(vt('Uw mutatie is opgeslagen.'), 'success');
    
    $data['waarde'] = $AEParticipants->formatNumber(($data['aantal'] * $data['koers']), $data['transactietype']);
    $data['aantal'] = $AEParticipants->formatAantalField($data['aantal']);
    
    
    $data['aantal'] = $AENumber->viewFormat2Decimals($data['aantal']);
    $data['waarde'] = $AENumber->viewFormat2Decimals($data['waarde']);
    $data['koers'] = $AENumber->viewFormat2Decimals($data['koers']);
    
    $template = $AETemplate->parseBlockFromFile('participantsInputOverview/mutationEditListRow.html', $data );
  } else {
    foreach ($editObject->form->object->data['fields'] as $fieldId => $fieldData)
    {
      if ( isset ($fieldData['error']) )
      {
        $fieldErrors[$fieldId] = array('description' => $fieldData['description'], 'message' => $fieldData['error']);
      }
    }
    $AEMessage->setMessage(vt('Niet alle velden zijn correct ingevoerd.'), 'info');
  }
  
    $jsonReplaces = array(array("\\\\", "/", "\n", "\t", "\r", "\b", "\f"), array('\\', '/', '', '', '', '', ''));
    $template = str_replace($jsonReplaces[0], $jsonReplaces[1], $template);

    echo json_encode(array(
      'template' => $template,
      'message' => $AEMessage->getMessage(),
      'fieldErrors' => $fieldErrors
    ));

  exit;
}

$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('client');
$editObject->formVars['client'] = $autocomplete->addVirtuelField('client', array(
  'autocomplete' => array(
    'table' => 'participanten, CRM_naw',
    'label' => array(
      'zoekveld',
      'registration_number'
    ),
    'searchable' => array(
      'zoekveld',
      'registration_number'
    ),
    'field_value' => array(
      'registration_number'
    ),
    'extra_fields' => array(
      'fonds_fonds',
      'crm_id',
      'participanten.id'
    ),
    'conditions' => array(
      'participanten`.`crm_id' => '`CRM_naw`.`id`',
      'fonds_fonds'
    ),
    'value' => 'zoekveld', 
    'actions' => array(
      'select' => '
        event.preventDefault();
        $("#participantenId").val(ui.item.data.id);
        $("#registrationNumber").val(ui.item.data.registration_number);
        $("#clientId").val(ui.item.data.crm_id);
        $("#crm_id").val(ui.item.data.crm_id);
        $("#client").val(ui.item.label);
        $("#zoekveld").html(ui.item.data.zoekveld);
        
      '
    )
  ),
  'form_size' => '30',
  ));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('client');

$queryHouseFonds = 'SELECT * from ' . $fondsObject->data['table'] . ' WHERE `HuisFonds` = 1 AND (`Fondsen`.`EindDatum`  >=  NOW() OR `Fondsen`.`EindDatum` = "0000-00-00")';

$db = new DB();
$db->QRecords($queryHouseFonds);

/** get Fonds list to dropdown **/
$fondsSelect = array();
while ($row = $db->nextRecord() ) {
  $fondsSelect[$row['Fonds']] = $row['Fonds'];
}

$fondsObject->data['fields']['Fonds']['form_options'] = $fondsSelect;
$fondsObject->data['fields']['Fonds']['form_type'] = 'selectKeyed';
$fondsObject->data['fields']['Fonds']['keyIn'] = null;
$fondsObject->data['fields']['Fonds']['key_field'] = null;


$participantenFondsVerloop->addClass('koers', 'maskValuta');
//$participantenFondsVerloop->addClass('aantal', 'maskNumeric');


$participantenFondsVerloop->data['fields']['Fonds'] = $fondsObject->data['fields']['Fonds'];

$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
//$editObject->template['jsincludes'] .= $AETemplate->loadJs('rekeningmutaties_v2');
$editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

$editObject->formVars['script'] .= $AETemplate->parseBlockFromFile('shortPositions/js/shortPositionMessage.js');


$editObject->controller($action, $data);


$editObject->formVars['btn_submit'] = ' <button class="button buttonSubmit saveRow" >'.drawButton('save').' ' . vt('Regel opslaan') . '</button>';


echo $editObject->getOutput();

$_SESSION['NAV'] = '';