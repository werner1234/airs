<?php
//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();
//Load record
$object = new Rekeningmutaties_v2();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

//get data
$data = $_GET;
$action = 'new';//$data['action'];
$editObject->formVars['mutation_type'] = 'geldtransacties';

/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';
$object->data['fields']['Valutakoers']['form_extra'] = '';

/** Clone fields **/
$object->data['fields']['value'] = $object->data['fields']['Bedrag'];

/** set values **/


/** set form size **/
$object->setPropertie('value', 'form_size', 8);
$object->setPropertie('Boekdatum', 'form_size', 8);
$object->setPropertie('settlementDatum', 'form_size', 8);
$object->setPropertie('Valutakoers', 'form_size', 10);
$object->setPropertie('Omschrijving', 'form_size', 25);
$object->setPropertie('Bedrag', 'form_size', 15);

if ( ! isset($data['Fonds']) ) {
  $data['Fonds'] = '';
}

include_once 'rekeningmutaties_v2_get_data.php';

$object->data['fields']['Omschrijving']['value'] = '';

/** set reload values **/
if ( isset ($_SESSION['reload']['geld_transactie']['Omschrijving']) ) {
  $object->data['fields']['Omschrijving']['value'] = $_SESSION['reload']['geld_transactie']['Omschrijving'];
  unset($_SESSION['reload']['geld_transactie']['Omschrijving']);
}
if ( isset ($_SESSION['reload']['geld_transactie']['Grootboekrekening']) ) {
  $object->set('Grootboekrekening', $_SESSION['reload']['geld_transactie']['Grootboekrekening']);
  unset($_SESSION['reload']['geld_transactie']['Grootboekrekening']);
}
/** end set reload values **/


//limit Grootboekrekening
$stortGrootboeken=array('STORT','ONTTR','VKSTO','RENTE','Kruis','KRUIS');
$object->data['fields']['Grootboekrekening']['form_options'] = array();
$object->data['fields']['Grootboekrekening']['form_extra'] = '';
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$stortGrootboeken;
foreach($stortGrootboeken as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Grootboekrekening']['form_options'][]=$grootboek;
}

//limit valuta
//$object->data['fields']['Valuta']['form_type'] = 'txt';
//$object->data['fields']['Valuta']['form_extra'] = 'READONLY tabindex=-1';

//$object->data['fields']['Valuta']['value'] = $editObject->formVars['aValuta'];

//$editObject->formVars['btn_submit'] = ' <input id="submit-form" type="submit" value="opslaan">';
//$editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-primary" value="opslaan">opslaan</button>';


/** set input filters for fields **/
$object->addClass('value', 'maskValuta2digits');
$object->addClass('Valutakoers', 'maskNumeric10Digits');


/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Grootboekrekening');
$object->addRequired('Valuta');
$object->addRequired('value');
$object->addRequired('Valutakoers');
//$object->addRequired('Omschrijving');

/** acties voor voorlopigerekeningmutaties **/
if ( isset($data['type']) && $data['type'] === 'temp' ) {
  //unset settlementDatum
  unset($editObject->object->data['fields']['settlementDatum']);
}

$editcontent['javascript'] = '';

$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/geld_transacties.js');

$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/geld_transacties.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it