<?php

/*
  AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
  Author              : $Author: rm $
  Laatste aanpassing  : $Date: 2016/05/13 14:21:10 $
  File Versie         : $Revision: 1.5 $

 */
include_once("wwwvars.php");

$data = array_merge($_GET, $_POST);/** merge data * */
$data['action'] = 'edit';
$action = $data['action'];
$AETemplate = new AE_template();
$fondsObject = new Fonds ();

/**
 * setup fonds selection
 */
$editObject = new editObject($fondsObject);
$editObject->template = $editcontent;
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->formTemplate = "participants_overviewCourseTemplate.html";
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


/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('client');
$editObject->formVars['client'] = $autocomplete->addVirtuelField('client', array(
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
      'fonds_fonds',
      'crm_id'
    ),
    'conditions' => array(
      'participanten`.`crm_id' => '`CRM_naw`.`id`',
      'fonds_fonds'
    ),
    'group' => 'crm_id',
    'value' => 'zoekveld', 
    'actions' => array(
      'select' => '
        $("#clientId").val(ui.item.data.crm_id);
        limitFonds(ui.item.data.crm_id, null);
        var $fonds = null;
        if ($("#Fonds").val()) {var $fonds = $("#Fonds").val();}
        listRegistrationNumbers(ui.item.data.crm_id, $fonds);
      '
    )
  ),
  'form_size' => '30',
  ));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('client');

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


$sortableFields = array(
  'fonds_fonds'           => array('title' => vt('Fonds'), 'state' => 'checked'),
  'datum'                 => array('title' => vt('Datum'), 'state' => 'checked'),
  'registration_number'   => array('title' => vt('Registratienummer')),
  'zoekveld'              => array('title' => vt('Zoekveld'), 'state' => 'checked'),
  'aantal'                => array('title' => vt('Aantal'))
);


$sortableHtml['sortable'] = null;
foreach ( $sortableFields as $field => $fieldData ) {
  $sortDirection = ( isset ($fieldData['direction']) ? $fieldData['direction'] : 'asc' );
  $sortableHtml['sortable'] .= '
    <tr>
      <td><input type="checkbox" name="sort['. $field .'][active]"  value="1" '. ( isset ($fieldData['state']) ? $fieldData['state'] : '' ) .'></td>
      <td style="width: 130px;">' . $fieldData['title'] . '</td>
      <td class="sort-'. $field .'">
        <span class="sort-desc '. ( $sortDirection == 'desc' ? 'simbisIconGray' : '' ) .'" data-sort="desc">{icon=sort_desc}</span>
        <span data-sort="asc" class="sort-asc '. ( $sortDirection == 'asc' ? 'simbisIconGray' : '' ) .'">{icon=sort_asc}</span> 
        <input class="sort-'. $field .'" type="hidden" name="sort['. $field .'][order]" value="'. ( isset ($fieldData['direction']) ? $fieldData['direction'] : 'asc' ) .'">
      </td>
    </tr>
  ';
}
$editObject->formVars['overviewFilter'] = $AETemplate->parseBlockFromFile('participantenOverzichten/overviewCourceFilter.html', $sortableHtml);

$editObject->formVars['generateOverview'] = '<button type="submit" class="btn btn-gray" id="generateOverview">' . vt('Genereer overzicht') . '</button>';
$editObject->formVars['generateCsv'] = '<button  type="submit" class="btn btn-gray" name="viewType" value="csv" id="generateCsv">'.maakKnop('csv.png').' ' . vt('Genereer csv') . '</button>';
$editObject->formVars['generateXls'] = '<button  type="submit" class="btn btn-gray" name="viewType" value="xls" id="generateXls">'.maakKnop('csv.png').' ' . vt('Genereer xls') . '</button>';

$editObject->formVars['generatePdf'] = '<button type="submit" class="btn btn-gray" name="viewType" value="pdf" id="generatePdf">'.maakKnop('pdf.png').' ' . vt('Genereer pdf') . '</button>';
//$editObject->formVars['generateBulkPdf'] = '<button type="submit" class="btn btn-gray" name="viewType" value="bulk_pdf" id="generateBulkPdf">'.maakKnop('pdf.png').' Bulk rapportage pdf</button>';


echo $editObject->getOutput();
$_SESSION['NAV'] = '';