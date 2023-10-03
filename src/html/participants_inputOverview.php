<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2015/01/28 13:12:32 $
  File Versie         : $Revision: 1.1 $

 */
include_once("wwwvars.php");
$AEMessage = new AE_Message();
$AETemplate = new AE_template();

$data = array_merge($_POST, $_GET);
$action = $data['action'];

$AETemplate = new AE_template();
$AEParticipants = new AE_Participants();


/** if we did an ajax post combine data **/
if ( isset ($data['postData']) ) {
  $data = array_merge($data, $data['postData']);
}

/** first lets validate the data if empty show message kill script **/
if ( empty ($data['Fonds']) || empty ($data['participanten_id']) ) {
  $AEMessage->setMessage('U heeft niet alle velden ingevoerd!', 'info');
  echo $AEMessage->getMessage();
  exit;
}


$__funcvar['location'] = 'participants_inputOverview.php?participanten_id=' . $data['participanten_id'] .'';

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

$editObject->formTemplate = 'classTemplates/participantsInputOverview/mutationForm.html';
$editObject->usetemplate = true;

/** we have all the date here, if we have postdate lets save the record **/
if ( isset ($data['saveRow']) && $data['saveRow'] == true ) {
  $template = '';
  $fieldErrors = array();
  
  //unset data before savind
  unset($data['postData']);
  unset($data['saveRow']);
  unset($data['action']);
  
  $data['aantal'] = $AEParticipants->formatAantal($data['aantal'], $data['transactietype']);
  $data['aantal'] = $AEParticipants->formatAantalField($data['aantal']);
  $editObject->controller('update', $data);//save $data
  
  if ($editObject->object->error != true) {
    $data['id'] = $participantenFondsVerloop->get('id');
    $AEMessage->setMessage('Uw mutatie is opgeslagen.', 'success');
    
    $data['waarde'] = $AEParticipants->formatNumber(($data['aantal'] * $data['koers']), $data['transactietype']);
    $template = $AETemplate->parseBlockFromFile('participanten/participanten_mutaties_edit_list_form_row.html', $data );
  } else {
    foreach ($editObject->form->object->data['fields'] as $fieldId => $fieldData)
    {
      if ( isset ($fieldData['error']) )
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

/** set fonds and registration number in form **/
$editObject->formVars['client'] = $data['client'];
$editObject->formVars['registration_number'] = $data['registration_number'];
$editObject->formVars['fonds_fonds'] = $data['Fonds'];
$editObject->formVars['participanten_id'] = $data['participanten_id'];



$editObject->formVars['btn_submit'] = ' <button class="button buttonSubmit saveRow" >'.drawButton('save').' ' . vt('Regel opslaan') . '</button>';


$_SESSION['nav']->returnUrl = 'participantsEdit.php?nawId='.$participantenObject->get('crm_id');


echo $editObject->getOutput();