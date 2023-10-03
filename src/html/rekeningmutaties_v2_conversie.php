<?php
//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();
$editcontent['javascript'] = '';

//Load record
$object = new Rekeningmutaties_v2();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

include_once 'rekeningmutaties_v2_get_data.php';
$editObject->template = $editcontent;


$editObject->formVars['mutation_type'] = 'conversie';
//limit Grootboekrekening

$object->setPropertie('Grootboekrekening', 'form_type', 'text');
$object->setPropertie('Grootboekrekening', 'value', 'FONDS');
$object->setPropertie('Grootboekrekening', 'form_extra', 'READONLY');




/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$editObject->formVars['conversie_fonds'] = $autocomplete->addVirtuelField('Fonds', array(
  'autocomplete' => array(
    'table' => 'Fondsen',
    'label' => array(
      'Fonds',
      'ISINCode'
    ),
    'searchable' => array(
      'Fonds',
      'ISINCode',
      'Omschrijving',
      'FondsImportCode'
    ),
    'field_value' => array(
      'Fonds'
    ),
    'extra_fields' => array(
      'Valuta',
      'Fondseenheid',
      'fondssoort'
    ),
    'value' => 'ISINCode', //value from table of join
    'actions' => array(
      'select' => '
      event.preventDefault();
      '
    )
  ),
  'form_extra' => '',
  'form_class' => 'fondsLookup requiredField',
  'form_size' => '35',
  ));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');





//$editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-primary" value="opslaan">opslaan</button>';

$editObject->template['jsincludes'] .= $AETemplate->loadJs('rekeningAfschriften');//"<script language=JavaScript src=\"javascript/rekeningAfschriften.js\" type=text/javascript></script>\n";
$editObject->template['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/conversie.js');



/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Transactietype');
$object->addRequired('Aantal');
$object->addRequired('Valuta');

$object->addRequired('Grootboekrekening');
$object->addRequired('value');
$object->addRequired('Omschrijving');


$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/conversie.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it