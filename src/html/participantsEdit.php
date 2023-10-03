<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2017/12/08 18:23:43 $
  File Versie         : $Revision: 1.3 $

 */
include_once("wwwvars.php");
$AEMessage = new AE_Message();
$data = array_merge($_GET, $_POST);/** merge data * */
$data['action'] = 'edit';
$action = $data['action'];
$AETemplate = new AE_template();

$fondsObject = new Fonds ();
$participantenObject = new Participanten();


$editObject = new editObject($participantenObject);
$editObject->template = $editcontent;
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = 'classTemplates/participanten/participanten_edit_list_form.html';
$editObject->usetemplate = true;
$editObject->formVars['crm_id'] = $data['nawId'];

/**
 * setup fonds selection
 */
$queryHouseFonds = 'SELECT * from ' . $fondsObject->data['table'] . ' WHERE `HuisFonds` = 1 AND (`Fondsen`.`EindDatum`  >=  NOW() OR `Fondsen`.`EindDatum` = "0000-00-00")';
$db = new DB();
$db->QRecords($queryHouseFonds);

/** get Fonds list to dropdown **/
$editObject->formVars['Fonds_options'] = '';
while ($row = $db->nextRecord() ) {
  $editObject->formVars['Fonds_options'] .= '<option value="'.$row['Fonds'].'">'.$row['Fonds'].'</option>';
  $editObject->formVars['Fonds_list'][$row['Fonds']] = $row['Fonds'];
}

/**
 * if $data->saveRow save the row and return ajax
 */
if ( isset ($data['saveRow']) && $data['saveRow'] == true ) {
  $template = '';
  $fieldErrors = array();

  $AEParticipants = new AE_Participants();

  $data = array_merge($data, $data['postData']);
  //unset data before savind
  unset($data['postData']);
  unset($data['saveRow']);
  unset($data['action']);
  unset($data['selectedFonds']);

  if ($AEParticipants->isDuplicate($participantenObject, $data['fonds_fonds'], $data['registration_number']) === false) {
    $editObject->controller('update', $data);//save $data

    if ($editObject->object->error != true) {
      $data['id'] = $participantenObject->get('id');
      $data['selectedFonds'] = $data['fonds_fonds']; //set selected fonds

      $AEMessage->setMessage('Participatie is opgeslagen.', 'success');
      $template = $AETemplate->parseBlockFromFile('participanten/participanten_edit_list_form_row.html', array_merge($data, $editObject->formVars) );
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
  } else {
    $AEMessage->setMessage('Combinatie fonds/registratienummer bestaat al.', 'info');
  }

  /** replace function for json encode **/
  $jsonReplaces = array(array("\\\\", "/", "\n", "\t", "\r", "\b", "\f"), array('\\', '/', '', '', '', '', ''));
  $template = str_replace($jsonReplaces[0], $jsonReplaces[1], $template);
  /** end replace **/

  echo json_encode(array(
    'template' => $template,
    'message' => $AEMessage->getMessage(),
    'fieldErrors' => $fieldErrors
  ));
  exit;
}
//
if ( $participantenObject->get('id') === 0 ) {
  /** ophalen van CRM data **/
  $crmNaw = new Naw ();
  $crmData = $crmNaw->parseBySearch(array('id' => $data['nawId']));

  if ( $participantenObject->get('registration_number') === NULL ) {
    $editObject->formVars['CRMGebrNaam'] = $crmData['CRMGebrNaam'];
  }
}

/** num of rows in participenten **/
$editObject->formVars['numberOfRowsInForm'] = 0;
/** get participant**/
$queryParticipanten = 'SELECT * from ' . $participantenObject->data['table'] . ' WHERE `crm_id` = "' . $data['nawId'] . '" ORDER BY `add_date` DESC';
$db = new DB();
$db->QRecords($queryParticipanten);

/** get Fonds list to dropdown **/
$editObject->formVars['participantenRow'] = '';
while ($row = $db->nextRecord() ) {
  $row['selectedFonds'] = $editObject->formVars['Fonds_list'][$row['fonds_fonds']];
  $editObject->formVars['participantenRow'] .= $AETemplate->parseBlockFromFile('participanten/participanten_edit_list_form_row.html', array_merge($row, $editObject->formVars) );
}

/** add button to save row **/
$editObject->formVars['btn_submit'] = ' <button class="button buttonSubmit saveRow" >'.drawButton('save').' ' . vt('Regel opslaan') . '</button>';

$editObject->controller($action, $data);

$_SESSION['NAV'] = ''; //unset nav session we dont need it here!

echo $editObject->getOutput();
