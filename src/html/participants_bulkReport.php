<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2016/04/08 14:18:51 $
  File Versie         : $Revision: 1.2 $

 */
include_once("wwwvars.php");

$data = array_merge($_POST, $_GET);
$AETemplate = new AE_template();
$AEParticipanten = new AE_Participants();
$AEParticipantsReport = new AE_ParticipantsReport();
$AENumbers = new AE_Numbers();
$AEPortal = new AE_Portal();


$data['action'] = 'edit';
$action = $data['action'];

if ( isset($data['viewType']) && $data['viewType'] === 'bulk_pdf') {
  $AEParticipantsReport->makePdfBulk($data);
  
  exit();
}

$fondsObject = new Fonds ();

/**
 * setup fonds selection
 */
$editObject = new editObject($fondsObject);
$editObject->template = $editcontent;
//$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = "classTemplates/bulkReport/bulkReportForm.html";
$editObject->usetemplate = true;

//debug($fondsObject->data['table']);
$fondsObject->data['fields']['Fonds']['key_field'] = false;
$fondsObject->data['fields']['DateStart'] = array(
  "description"  => "Coupondatum",
  "form_type"    => "calendar",
  "form_class"   => "AIRSdatepicker",
  "form_visible" => true, 
  "list_width"   => "150",
);
$fondsObject->set('DateStart', date('Y') . '-01-01');


$fondsObject->data['fields']['DateEnd'] = array(
  "description"  => "Coupondatum",
  "form_type"    => "calendar",
  "form_class"   => "AIRSdatepicker",
  "form_visible" => true, 
  "list_width"   => "150",
);
$fondsObject->set('DateEnd', date('Y-m-d'));


$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('client_from');
$editObject->formVars['client_from'] = $autocomplete->addVirtuelField('client_from', array(
  'autocomplete' => array(
    'table' => 'participanten, CRM_naw',
    'label' => array(
      'zoekveld'
    ),
    'searchable' => array(
      'zoekveld'
    ),
    'field_value' => array(
      'registration_number'
    ),
    'extra_fields' => array(
      'crm_id'
    ),
    'conditions' => array(
      'participanten`.`crm_id' => '`CRM_naw`.`id`',
    ),
    'order' => 'zoekveld ASC',
    'group' => 'crm_id',
    'value' => 'zoekveld', 
    'actions' => array(
      'select' => '
        $("#client_from").val(ui.item.data.zoekveld);
        $("#client_from_id").val(ui.item.data.crm_id);
      '
    )
  ),
  'form_size' => '30',
));
$editObject->template['script_voet'] = $autocomplete->getAutoCompleteVirtuelFieldScript('client_from');

$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('client_to');
$editObject->formVars['client_to'] = $autocomplete->addVirtuelField('client_to', array(
  'autocomplete' => array(
    'table' => 'participanten, CRM_naw',
    'label' => array(
      'zoekveld'
    ),
    'searchable' => array(
      'zoekveld'
    ),
    'field_value' => array(
      'registration_number'
    ),
    'extra_fields' => array(
      'crm_id'
    ),
    'source_data' => array(
      'name' => array(
        'client_from'
      )
    ),
    'conditions' => array(
      'participanten`.`crm_id' => '`CRM_naw`.`id`',
      'AND' => array(
        '`zoekveld` > "{$get:client_from}"'
      )
    ),
    'order' => 'zoekveld ASC',
    'group' => 'crm_id',
    'value' => 'zoekveld', 
    'actions' => array(
      'select' => '
        $("#client_to").val(ui.item.data.zoekveld);
        $("#client_to_id").val(ui.item.data.crm_id);
      '
    )
  ),
  'form_size' => '30',
  ));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('client_to');


$queryHouseFonds = 'SELECT * from ' . $fondsObject->data['table'] . ' WHERE `HuisFonds` = 1';

$db = new DB();
$db->QRecords($queryHouseFonds);

/** get Fonds list to dropdown **/
$fondsSelect = array();
while ($row = $db->nextRecord() ) {
  $fondsSelect[$row['Fonds']] = $row['Fonds'];
}

$fondsObject->data['fields']['Fonds']['form_options'] = $fondsSelect;
$fondsObject->data['fields']['Fonds']['form_type'] = 'selectKeyed';

$editObject->controller($action, $data);

$editObject->formVars['buttonToPdf'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf" id="bulkPdf">'.maakKnop('pdf.png').' ' . vt('Pdf') . '</button>';
$editObject->formVars['buttonToPortal'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf_portal" id="bulkPortal">'.maakKnop('pdf.png').' ' . vt('Portaal') . '</button>';

//$editObject->formVars['generateBulkPdf'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf" id="generateBulkPdf">'.maakKnop('pdf.png').' Bulk rapportage</button>';
//$editObject->formVars['generateBulkPdfDve'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf_dve" id="generateBulkPdf">'.maakKnop('pdf.png').' Bulk rapportage Dve</button>';
//$editObject->formVars['generateBulkPdfPortal'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf_portal" id="generateBulkPdf">'.maakKnop('pdf.png').' Naar portaal</button>';

echo $editObject->getOutput();

/** create pdf to portal and display messages **/
if ( isset($data['viewType']) && $data['viewType'] === 'bulk_pdf_portal') {
  $AEParticipantsReport->makePdfBulk($data, true);
  
  echo $AEPortal->messages;
}



$_SESSION['NAV'] = '';