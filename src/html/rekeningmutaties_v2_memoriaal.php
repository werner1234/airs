<?php
//include files
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();

//Load record
$object = new Rekeningmutaties_v2();
$editcontent['javascript'] = '';
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

//get data
$data = array_merge($_GET, $_POST);
$action = 'new';//$data['action'];
if ( !isset($data['Fonds']) )
{
  $data['Fonds'] = '';
}

include_once 'rekeningmutaties_v2_get_data.php';
$editObject->formVars['mutation_type'] = 'memoriaal';

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
$object->setValue('kostenBuitenland_Valutakoers', '1');
$object->setValue('rente_Valutakoers', '1');
$object->setPropertie('Transactietype', 'form_options', array());

/** set form size **/
$object->setPropertie('value', 'form_size', 17);
$object->setPropertie('rente_Input', 'form_size', 8);
$object->setPropertie('rente_Valutakoers', 'form_size', 10);
$object->setPropertie('divident_value', 'form_size', 19);
$object->setPropertie('Boekdatum', 'form_size', 8);
$object->setPropertie('settlementDatum', 'form_size', 8);
$object->setPropertie('Valutakoers', 'form_size', 10);
$object->setPropertie('Fondskoers', 'form_size', 10);
$object->setPropertie('Omschrijving', 'form_size', 35);


/** set input filters for fields **/
$object->addClass('Aantal', 'maskRekeningMutatieAantal');
$object->addClass('Fondskoers', 'maskFondsKoers');
$object->addClass('Valutakoers', 'maskValutaKoers');
$object->addClass('rente_Bedrag', 'maskValuta2digits');
$object->addClass('rente_Valutakoers', 'maskValutaKoers');
$object->addClass('rente_Input', 'maskValuta2digitsPositive');
$object->setPropertie('rente_Bedrag', 'form_extra', 'READONLY');


/** set reload values **/
//if ( isset ($_SESSION['reload']['memoriaal']['Omschrijving']) ) {
//  $object->data['fields']['Omschrijving']['value'] = $_SESSION['reload']['memoriaal']['Omschrijving'];
//  unset($_SESSION['reload']['memoriaal']['Omschrijving']);
//}
/** end set reload values **/


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
    'source_data' => array(
      'name' => array(
        'Boekdatum'
      )
    ),
    'value' => 'ISINCode', //value from table of join
    'actions' => array(
      'select' => '
      event.preventDefault();
        $("#Fonds").val(ui.item.field_value);
        $("#Fonds_hidden").val(ui.item.value);
        $("#fonds_omschrijving").val(ui.item.data.Omschrijving);
        $("#fonds_fonds").val(ui.item.data.Fonds);
        $("#fondseenheid").val(ui.item.data.Fondseenheid);
        $("#Valuta").val(ui.item.data.Valuta);
        $("#Valuta").trigger("change");
        
        fondsChanged(\'Fonds\');
        
        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');

        $("#Omschrijving").val("Aan/verkoop");
        
        $("#Transactietype").html("");
        if ( ui.item.data.fondssoort == "OPT" ) {
          $("#Transactietype").html("");
          $("#Transactietype").append(\'<option value="B">B</option><option value="A/S">A/S</option><option value="V/S">V/S</option><option value="A/O">A/O</option><option value="V/O">V/O</option>\');
        } else {
          $("#Transactietype").html("");
          $("#Transactietype").append(\'<option value="B">B</option><option value="D">D</option><option value="L">L</option>\');
        }
        
        $("#opgelopenRente :input").attr("disabled", false);
        if ( $.inArray(ui.item.data.fondssoort, ["AAND", "TURBO", "OPT"]) != -1 ) {
          $("#opgelopenRente :input").attr("disabled", true);
          $("#opgelopenRente :input").val("");
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


$editObject->formVars['save_with_counter_rule'] = ' <button id="submitCounterRule" type="submit" class="btn btn-gray" name="submitCounterRule" value="CounterRule">' . maakKnop('disk_blue.png') . ' ' . vt('Opslaan met tegenregel') . '</button>';
$editObject->formVars['save_without_counter_rule'] = ' <button id="submitNoCounterRule" type="submit" class="btn btn-gray" name="submitNoCounterRule" value="NoCounterRule">' . maakKnop('disk_blue.png') . ' ' . vt('Opslaan zonder tegenregel') . '</button>';


//$editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-primary" value="opslaan">opslaan</button>';

/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Transactietype');
$object->addRequired('Aantal');
$object->addRequired('Valuta');

$object->addRequired('Grootboekrekening');
$object->addRequired('value');
$object->addRequired('Omschrijving');

$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften_v2.js\" type=text/javascript></script>\n";
$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/memoriaal.js');

$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/memoriaal.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it