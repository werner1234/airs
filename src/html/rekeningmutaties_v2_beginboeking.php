<?php
//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();
//Load record
$editcontent['javascript'] = '';

$object = new Rekeningmutaties_v2();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

//get data
$data = $_GET;
$action = 'new';//$data['action'];
if ( ! isset ($data['Fonds']) ) {$data['Fonds'] = '';}

include_once 'rekeningmutaties_v2_get_data.php';

/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';
$object->data['fields']['Valutakoers']['form_extra'] = '';


$editObject->formVars['mutation_type'] = 'beginboeking';

/** Clone fields **/
$object->data['fields']['value_input'] = $object->data['fields']['Bedrag'];



//limit Grootboekrekening
//$object->data['fields']['Grootboekrekening']['form_type'] = 'text';
//$object->data['fields']['Grootboekrekening']['value'] = 'VERM';

$grootboekBegin= array('VERM','FONDS');
$object->data['fields']['Grootboekrekening']['form_options']=array();
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$grootboekBegin;
foreach($grootboekBegin as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Grootboekrekening']['form_options'][]=$grootboek;
}


//$object->data['fields']['Grootboekrekening']['form_extra'] = 'READONLY';

//limit transtype
$object->data['fields']['Transactietype']['form_type'] = 'text';
$object->data['fields']['Transactietype']['value'] = '';
$object->data['fields']['Transactietype']['form_extra'] = 'READONLY';

$object->data['fields']['Valuta']['form_extra'] = '';

$object->set('Valutakoers', '1');
$object->setValue('Omschrijving', 'Inbreng');

/** set input filters for fields **/
$object->addClass('value_input', 'maskNumeric6Digits');
$object->addClass('Aantal', 'maskRekeningMutatieAantal');
$object->addClass('value', 'maskValuta2digits');

$object->addClass('Valutakoers', 'maskNumeric10Digits');
$object->addClass('Fondskoers', 'maskFondsKoers');

/** set form size **/
$object->setPropertie('value_input', 'form_size', 14);
$object->setPropertie('Fondskoers', 'form_size', 14);
$object->setPropertie('Transactietype', 'form_size', 1);
$object->setPropertie('Omschrijving', 'form_size', 50);


/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$editObject->formVars['Fonds'] = $autocomplete->addVirtuelField('Fonds', array(
  'autocomplete' => array(
    'table' => 'Fondsen',
    'label' => array(
      'Fonds',
      'FondsImportCode'
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
    'source_data' => array(
      'name' => array(
        'Boekdatum'
      )
    ),
    'actions' => array(
      'select' => '
      event.preventDefault();
        $("#Fonds").val(ui.item.field_value);
        $("#Fonds_hidden").val(ui.item.value);
        $("#fondseenheid").val(ui.item.data.Fondseenheid);
        
        fondsChanged(\'Fonds\');
        waardesum();//set totals

        $(\'#Omschrijving\').val(\'Inbreng \' + ui.item.data.Fonds);

        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');
        checkFondsAantal("Fonds");
      '
    ),
    'conditions' => array(
      'AND' => ' (Fondsen.EindDatum  >=  "{$get:Boekdatum}" OR Fondsen.EindDatum = "0000-00-00")'
    ),
  ),
  'form_extra' => '',
  'form_class' => 'fondsLookup ',
  'form_size' => '24',
  ));
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');



/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('value_input');
//$object->addRequired('Aantal');
//$object->addRequired('Valuta');
//
$object->addRequired('Grootboekrekening');
//$object->addRequired('value');
//$object->addRequired('Omschrijving');

//$editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-primary" value="opslaan">opslaan</button>';

/** acties voor voorlopigerekeningmutaties **/
if ( isset($data['type']) && $data['type'] === 'temp' ) {
  //unset settlementDatum
  unset($editObject->object->data['fields']['settlementDatum']);
}


$editcontent['javascript'] = '';

$editObject->template['jsincludes'] .= $AETemplate->loadJs('rekeningAfschriften');//"<script language=JavaScript src=\"javascript/rekeningAfschriften.js\" type=text/javascript></script>\n";
$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/beginboeking.js');

$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/beginboeking.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it