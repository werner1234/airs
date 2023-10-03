<?php
//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();
//Load record
$object = new Rekeningmutaties_v2();
$editcontent['javascript'] = '';
$editObject = new editObject($object);
$editObject->__appvar = $__appvar;

//get data
$data = $_GET;
$action = 'new';//$data['action'];
if ( !isset($data['Fonds']) )
{
  $data['Fonds'] = '';
}

include_once 'rekeningmutaties_v2_get_data.php';
$editObject->formVars['mutation_type'] = 'kostenboeking';

/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';

/** Clone fields **/

$object->data['fields']['value'] = $object->data['fields']['Bedrag'];

//limit Grootboekrekening
$kostGrootboeken= array('KOBU','KNBA','KOST','BEW','BEH');
$object->data['fields']['Grootboekrekening']['form_options'] = array();
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$kostGrootboeken;
foreach($kostGrootboeken as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Grootboekrekening']['form_options'][]=$grootboek;
}



/** set input filters for fields **/
$object->setPropertie('Valutakoers', 'form_size', 12);


$object->addClass('value', 'maskValuta2digitsPositive');
$object->addClass('Bedrag', 'maskValuta2digits');

$object->addClass('Valutakoers', 'maskNumeric10Digits');

$object->setPropertie('Bedrag', 'form_extra', 'READONLY');

/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Grootboekrekening');
$object->addRequired('Valutakoers');
$object->addRequired('value');
$object->addRequired('Bedrag');

//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften.js\" type=text/javascript></script>\n";

$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/kostenboeking.js');


$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/kostenboeking.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it