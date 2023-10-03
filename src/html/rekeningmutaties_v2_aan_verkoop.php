<?php

//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
//Load record

$data = array_merge($_GET, $_POST);

$action = $data['action'];

$object = new Rekeningmutaties_v2();
$editcontent['javascript'] = '';
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

include_once 'rekeningmutaties_v2_get_data.php';
$editObject->formVars['action'] = $action;
$editObject->formVars['mutation_type'] = 'aanverkoop';
listarray( $object->data['fields']["Grootboekrekening"]["form_options"]);
/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';
$object->data['fields']['Fonds']['form_extra'] = '';
$object->data['fields']['Valutakoers']['form_extra'] = '';
$object->data['fields']['Aantal']['form_extra'] = '';

/** Clone fields **/
$object->data['fields']['divident_valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['kosten_valuta'] = $object->data['fields']['Valuta'];

$object->data['fields']['value'] = $object->data['fields']['Bedrag'];
$object->data['fields']['divident_value'] = $object->data['fields']['Bedrag'];
$object->data['fields']['kosten_value'] = $object->data['fields']['Bedrag'];

$object->data['fields']['divident_Valutakoers'] = $object->data['fields']['Valutakoers'];

//kosten
$object->data['fields']['Kosten_Input'] = $object->data['fields']['Bedrag'];
$object->data['fields']['Kosten_Valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['Kosten_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['Kosten_Bedrag'] = $object->data['fields']['Bedrag'];
$object->data['fields']['Kosten_Grootboekrekening'] = $object->data['fields']['Grootboekrekening'];
$object->data['fields']['Kosten_Grootboekrekening']['form_select_option_notempty'] = true;

$grootboekKosten=  array('KOST', 'KNBA','TOB','ROER');
$object->data['fields']['Kosten_Grootboekrekening']['form_options']=array();
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$grootboekKosten;
foreach($grootboekKosten as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Kosten_Grootboekrekening']['form_options'][]=$grootboek;
}
$object->data['fields']['Kosten_Grootboekrekening']['value'] = 'KOST';

//kosten1
$object->data['fields']['Kosten1_Input'] = $object->data['fields']['Bedrag'];
$object->data['fields']['Kosten1_Valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['Kosten1_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['Kosten1_Bedrag'] = $object->data['fields']['Bedrag'];
$object->data['fields']['Kosten1_Grootboekrekening'] = $object->data['fields']['Grootboekrekening'];
$object->data['fields']['Kosten1_Grootboekrekening']['form_select_option_notempty'] = true;
$object->data['fields']['Kosten1_Grootboekrekening']['form_options'] = array();
foreach($grootboekKosten as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Kosten1_Grootboekrekening']['form_options'][]=$grootboek;
}
$object->data['fields']['Kosten1_Grootboekrekening']['value'] = 'KOST';

//kostenBuitenland 
$object->data['fields']['kostenBuitenland_Input'] = $object->data['fields']['Bedrag'];
$object->data['fields']['kostenBuitenland_Valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['kostenBuitenland_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['kostenBuitenland_Bedrag'] = $object->data['fields']['Bedrag'];
//kostenBuitenland 
$object->data['fields']['rente_Input'] = $object->data['fields']['Bedrag'];
$object->data['fields']['rente_Valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['rente_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['rente_Bedrag'] = $object->data['fields']['Bedrag'];



//$object->data['fields']['Fonds']['form_type'] = 'autocomplete';

/** set values **/
$object->setValue('Omschrijving', 'Aan/verkoop');

$object->setValue('Valutakoers', '1.00');
$object->setValue('Kosten_Valutakoers', '1');
$object->setValue('Kosten1_Valutakoers', '1');

$object->setValue('kostenBuitenland_Valutakoers', '1');
$object->setValue('rente_Valutakoers', '1');

$object->setPropertie('Transactietype', 'form_options', array());

/** set form size **/
$object->setPropertie('value', 'form_size', 17);

$object->setPropertie('Kosten_Input', 'form_size', 13);
$object->setPropertie('Kosten1_Input', 'form_size', 13);

$object->setPropertie('kostenBuitenland_Input', 'form_size', 13);
$object->setPropertie('rente_Input', 'form_size', 13);

$object->setPropertie('Kosten_Valutakoers', 'form_size', 10);
$object->setPropertie('Kosten1_Valutakoers', 'form_size', 10);

$object->setPropertie('kostenBuitenland_Valutakoers', 'form_size', 10);
$object->setPropertie('rente_Valutakoers', 'form_size', 10);

$object->setPropertie('divident_value', 'form_size', 19);
$object->setPropertie('kosten_value', 'form_size', 19);
$object->setPropertie('Boekdatum', 'form_size', 8);
$object->setPropertie('settlementDatum', 'form_size', 8);
$object->setPropertie('Valutakoers', 'form_size', 10);
$object->setPropertie('divident_Valutakoers', 'form_size', 15);
$object->setPropertie('Fondskoers', 'form_size', 10);
$object->setPropertie('Omschrijving', 'form_size', 50);

$object->setPropertie('Kosten_Bedrag', 'form_size', 10);
$object->setPropertie('Kosten1_Bedrag', 'form_size', 10);

$object->setPropertie('kostenBuitenland_Bedrag', 'form_size', 10);
$object->setPropertie('rente_Bedrag', 'form_size', 10);

/** set input filters for fields **/
$object->addClass('Aantal', 'maskRekeningMutatieAantal');
$object->addClass('Fondskoers', 'maskFondsKoers');

$object->addClass('Valutakoers', 'maskNumeric10Digits');

$object->addClass('Kosten_Valutakoers', 'maskNumeric10Digits');
$object->addClass('Kosten1_Valutakoers', 'maskNumeric10Digits');

$object->addClass('kostenBuitenland_Valutakoers', 'maskNumeric10Digits');
$object->addClass('rente_Valutakoers', 'maskNumeric10Digits');

$object->addClass('Kosten_Input', 'maskValuta2digitsPositive');
$object->addClass('Kosten1_Input', 'maskValuta2digitsPositive');

$object->addClass('kostenBuitenland_Input', 'maskValuta2digitsPositive');
$object->addClass('rente_Input', 'maskValuta2digitsPositive');

$object->addClass('Kosten_Bedrag', 'maskValuta2digitsPositive');
$object->addClass('Kosten1_Bedrag', 'maskValuta2digitsPositive');

$object->addClass('kostenBuitenland_Bedrag', 'maskValuta2digits');
$object->addClass('rente_Bedrag', 'maskValuta2digits');

$object->setPropertie('Kosten_Bedrag', 'form_extra', 'READONLY');
$object->setPropertie('Kosten1_Bedrag', 'form_extra', 'READONLY');

$object->setPropertie('kostenBuitenland_Bedrag', 'form_extra', 'READONLY');
$object->setPropertie('rente_Bedrag', 'form_extra', 'READONLY');

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
        $("#Valuta").val(ui.item.data.Valuta);
        $("#Valuta").trigger("change");
        
        $("#fonds_omschrijving").val(ui.item.data.Omschrijving);
        $("#fonds_fonds").val(ui.item.data.Fonds);
        
        fondsChanged(\'Fonds\');
        
        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');

        $("#Omschrijving").val("Aan/verkoop");
        
        $("#Transactietype").html("");
        if ( ui.item.data.fondssoort == "OPT" ) {
          $("#Transactietype").append(\'<option value="A/S">A/S</option><option value="V/S">V/S</option><option value="A/O">A/O</option><option value="V/O">V/O</option>\');
        } else {
          $("#Transactietype").append(\'<option value="A">A</option><option value="V">V</option>\');
        }
        
        $("#opgelopenRente :input").attr("disabled", false);
        if ( $.inArray(ui.item.data.fondssoort, ["AAND", "TURBO", "OPT"]) != -1 ) {
          $("#opgelopenRente :input").attr("disabled", true);
        }

        

        setDescription ();
        checkFondsAantal("Fonds");
        checkShortPositions("Fonds");
        $("#fondssoort").val(ui.item.data.fondssoort);
        if (typeof getRente == "function") {
          getRente(); 
        }
      '
    ),
    'conditions' => array(
      'AND' => ' (Fondsen.EindDatum  >=  "{$get:Boekdatum}" OR Fondsen.EindDatum = "0000-00-00")'
    ),
  ),
  'form_extra' => '',
  'form_class' => 'fondsLookup requiredField',
  'form_size' => '26',
  ));
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');


/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Transactietype');
$object->addRequired('Aantal');
$object->addRequired('Valuta');

$object->addRequired('Grootboekrekening');
$object->addRequired('value');
$object->addRequired('Omschrijving');


/** acties voor voorlopigerekeningmutaties **/
if ( isset($data['type']) && $data['type'] === 'temp' ) {
  //unset settlementDatum
  unset($editObject->object->data['fields']['settlementDatum']);
}



//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften_v2.js\" type=text/javascript></script>\n";

$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/aan_verkoop.js');

$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/aan_verkoop.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it