<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2015/11/20 08:58:20 $
  File Versie         : $Revision: 1.2 $

 */
include_once("wwwvars.php");
$AEMessage = new AE_Message();
$AETemplate = new AE_template();
$AEDatum = new AE_datum();
$AENumbers = new AE_Numbers();

$data = array_merge($_GET, $_POST);/** merge data * */
$action = $data['action'];

$AETemplate = new AE_template();
$AEParticipants = new AE_Participants();

$__funcvar['location'] = 'participantenFondsVerloopLiveEdit.php?participanten_id=' . $data['participanten_id'] .'';

$fondsObject = new Fonds ();
$participantenObject = new Participanten();
$participantenFondsVerloop = new ParticipantenFondsVerloop();

/** get participant **/
$participantenEditObject = new editObject($participantenObject);
$participantenEditObject->controller('edit', array_merge($data, array('id' => $data['participanten_id']))); //get current participantenObject

$editObject = new editObject($participantenFondsVerloop);
$editObject->template = $editcontent;

$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = 'classTemplates/participanten/participanten_mutaties_edit_list_form.html';
$editObject->usetemplate = true;

/** save data on update only save registration number **/
if (in_array($data['action'], array('update', 'delete'))) {
  $participantenEditObject->controller($data['action'], $data);
  
  if ( $participantenEditObject->object->error === true ) {
    foreach ( $participantenEditObject->object->data['fields'] as $fieldId => $fieldData) {
      if ( isset($fieldData['error']) ) {
        $AEMessage->setFlash($fieldData['description'] . ' ' . $fieldData['error'], 'error');
        $fieldErrors[$fieldId] = array('description' => $fieldData['description'], 'message' => $fieldData['error']);
      }
    }
  } elseif ($data['action'] == 'update') {
    $AEMessage->setFlash(vt('Participatie is gewijzigd.'), 'succes');
  }
  /** Remove subrecords **/
  if ($data['action'] == 'delete')
  {
    $removeQuery = 'DELETE FROM `' . $participantenFondsVerloop->data['table'] . '` WHERE `participanten_id` = "' . $data['participanten_id'] . '"';
    $db = new DB();
    if ($db->executeQuery($removeQuery)){
      header("Location: " . 'participantsEdit.php?nawId=' . $participantenObject->get('crm_id'));
    }
    exit;
  }
}

/**
 * if $data->saveRow save the row and return ajax
 */
if ( isset ($data['saveRow']) && $data['saveRow'] == true ) {
  $template = '';
  $fieldErrors = array();
  
  $data = array_merge($data, $data['postData']);
 
  //unset data before savind
  unset($data['postData']);
  unset($data['saveRow']);
  unset($data['action']);
  
  $data['aantal'] = $AEParticipants->formatAantal($data['aantal'], $data['transactietype']);
  
  $editObject->controller('update', $data);//save $data
  
  if ($editObject->object->error != true) {
    $data['id'] = $participantenFondsVerloop->get('id');
    $AEMessage->setMessage('Uw mutatie is opgeslagen.', 'success');
    
    $data['waarde'] = $AEParticipants->formatNumber(($data['aantal'] * $data['koers']), $data['transactietype']);
    $data['aantal'] = $AEParticipants->formatAantal($data['aantal'], $data['transactietype']);
    
    $data['waarde'] = $AENumbers->viewFormat2Decimals($data['waarde']);
    $data['aantal'] = $AENumbers->viewFormatMinMaxDecimals($data['aantal'], 2,6);
    $data['koers'] = $AENumbers->viewFormatMinMaxDecimals($data['koers'], 2,6);
    
    
    $template = $AETemplate->parseBlockFromFile('participanten/participanten_mutaties_edit_list_form_row.html', $data );
  } else {
    foreach ($editObject->form->object->data['fields'] as $fieldId => $fieldData)
    {
      if ( isset($fieldData['error']) )
      {
        $fieldErrors[$fieldId] = array('description' => $fieldData['description'], 'message' => $fieldData['error']);
      }
    }
    $AEMessage->setMessage('Niet alle velden zijn correct ingevoerd.', 'info');
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
$editObject->controller('edit', $data); //get current participantenObject

$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

$editObject->formVars['script'] .= $AETemplate->parseBlockFromFile('participanten/js/mutations_edit_list_form.js');
$editObject->formVars['script'] .= $AETemplate->parseBlockFromFile('shortPositions/js/shortPositionMessage.js');


/** get fondsverloop**/
$queryParticipantenFondsVerloop = 'SELECT * from ' . $participantenFondsVerloop->data['table'] . ' WHERE `participanten_id` = "' . $data['participanten_id'] . '" ORDER BY `datum` DESC';
$db = new DB();
$db->QRecords($queryParticipantenFondsVerloop);

/** get Fonds list to dropdown **/
$editObject->formVars['dataRow'] = '';
while ($row = $db->nextRecord() ) {
  $row['waarde'] = $AEParticipants->formatNumber(($row['aantal'] * $row['koers']), $row['transactietype']);
  $row['datum'] = $AEDatum->dbToForm($row['datum']);
  $row['aantal'] = $AEParticipants->formatAantal($row['aantal'], $row['transactietype']);
  
  $row['waarde'] = $AENumbers->viewFormat2Decimals($row['waarde']);
  $row['aantal'] = $AENumbers->viewFormatMinMaxDecimals($row['aantal'], 2,6);
  $row['koers'] = $AENumbers->viewFormatMinMaxDecimals($row['koers'], 2,6);
  
  $editObject->formVars['dataRow'] .= $AETemplate->parseBlockFromFile('participanten/participanten_mutaties_edit_list_form_row.html', $row );
}

//$participantenObject
$editObject->formVars['participanten_id'] = $participantenObject->get('id');
$editObject->formVars['crm_id'] = $participantenObject->get('crm_id');
$editObject->formVars['Fonds_name'] = $participantenObject->get('fonds_fonds');
$editObject->formVars['registration_number'] = $participantenObject->get('registration_number');
$editObject->formVars['memo_inputfield'] = $participantenEditObject->form->makeInput('memo');
$editObject->formVars['memo_description'] = $participantenObject->getDescription('memo');

$editObject->formVars['messages'] = $AEMessage->getFlash();


//debug($participantenObject);
$editObject->formVars['btn_submit'] = ' <button class="button buttonSubmit saveRow" >'.drawButton('save').' ' . vt('Regel opslaan') . '</button>';
$editObject->formVars['participation_submit'] = ' <button type="submit" class="button buttonSubmit" >'.drawButton('save').' ' . vt('Wijziging opslaan') . '</button>';

$_SESSION['nav']->returnUrl = 'participantsEdit.php?nawId='.$participantenObject->get('crm_id');
$_SESSION['nav']->items['navedit']->buttonSave = false;
$_SESSION['nav']->items['navedit']->buttonDelete = false;
echo $editObject->getOutput();