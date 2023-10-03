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
$data = $_GET;
$action = 'new';//$data['action'];
if ( !isset($data['Fonds']) )
{
  $data['Fonds'] = '';
}

include_once 'rekeningmutaties_v2_get_data.php';
$editObject->formVars['mutation_type'] = 'overige';

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


//$object->data['fields']['Fonds']['form_type'] = 'autocomplete';

/** set values **/
$query="SELECT Vermogensbeheerder FROM Portefeuilles JOIN Rekeningen ON Portefeuilles.Portefeuille=Rekeningen.Portefeuille WHERE Rekeningen.Rekening='".$object->get('Rekening')."'";
$DB->SQL($query);
$vermogensbeheerder=$DB->lookupRecord();

$query="SELECT waarde as Grootboekrekening FROM KeuzePerVermogensbeheerder WHERE categorie='Grootboekrekeningen' AND vermogensbeheerder='".$vermogensbeheerder['Vermogensbeheerder']."'";
$DB->SQL($query);
$DB->Query();
if($DB->records()>0)
{
  while ($gb = $DB->NextRecord())
  {
    $object->data['fields']["Grootboekrekening"]["form_options"][] = $gb['Grootboekrekening'];
  }
}
else
{
  $DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening");
  $DB->Query();
  while ($gb = $DB->NextRecord())
  {
    $object->data['fields']['Grootboekrekening']['form_options'][] = $gb['Grootboekrekening'];
  }
}


//$object->data['fields']['Grootboekrekening']['form_options'] = array(
//  'BEH'   => 'BEH',
//  'BEW'   => 'BEW',
//  'DIV'   => 'DIV',
//  'DIVBE' => 'DIVBE',
//  'FONDS' => 'FONDS',
//  'HUUR'  => 'HUUR',
//  'KNBA'  => 'KNBA',
//  'KOBU'  => 'KOBU',
//  'KOST'  => 'KOST',
//  'Kruis' => 'Kruis',
//  'MEM'   => 'MEM',
//  'OG'    => 'OG',
//  'ONTTR' => 'ONTTR',
//  'RENME' => 'RENME',
//  'RENOB' => 'RENOB',
//  'RENTE' => 'RENTE',
//  'ROER'  => 'ROER',
//  'STORT' => 'STORT',
//  'TOB'   => 'TOB',
//  'VERM'  => 'VERM',
//  'VKSTO' => 'VKSTO',
//  'VMAR'  => 'VMAR',
//  'VTRES' => 'VTRES'
//);

//limit transaction type
$object->data['fields']['Transactietype']['form_options'] = array(
  'A'   => 'A',
  'A/O' => 'A/O',
  'A/S' => 'A/S',
  'B'   => 'B',
  'D'   => 'D',
  'L'   => 'L',
  'S'   => 'S',
  'V'   => 'V',
  'V/O' => 'V/O',
  'V/S' => 'V/S',
  'W'   => 'W',
);




$object->setValue('Valutakoers', '1.00');
$object->setValue('Kosten_Valutakoers', '1');
$object->setValue('kostenBuitenland_Valutakoers', '1');
$object->setValue('rente_Valutakoers', '1');


/** set form size **/
$object->setPropertie('value', 'form_size', 17);

$object->setPropertie('Kosten_Input', 'form_size', 13);
$object->setPropertie('kostenBuitenland_Input', 'form_size', 13);
$object->setPropertie('rente_Input', 'form_size', 13);

$object->setPropertie('Boekdatum', 'form_size', 8);
$object->setPropertie('settlementDatum', 'form_size', 8);
$object->setPropertie('Valutakoers', 'form_size', 10);
$object->setPropertie('Fondskoers', 'form_size', 10);
$object->setPropertie('Omschrijving', 'form_size', 35);

/** set input filters for fields **/
$object->addClass('Aantal', 'maskRekeningMutatieAantalEdit');
$object->addClass('Fondskoers', 'maskFondsKoers');
$object->addClass('Valutakoers', 'maskNumeric10Digits');

$object->addClass('Debet', 'maskValuta');
$object->addClass('Credit', 'maskValuta');

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
        $("input[name=Fondskoerseenheid]").val(ui.item.data.Fondseenheid);
        $("#Valuta").val(ui.item.data.Valuta);
        $("#Valuta").trigger("change");
        
        fondsChanged(\'Fonds\');
        
        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');

        checkFondsAantal("Fonds");
        checkShortPositions("Fonds");
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



//$editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-primary" value="opslaan">opslaan</button>';

/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Transactietype');
$object->addRequired('Aantal');
$object->addRequired('Valuta');

$object->addRequired('Grootboekrekening');
$object->addRequired('value');
$object->addRequired('Omschrijving');

//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften_v2.js\" type=text/javascript></script>\n";

$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/overige.js');



$editObject->template = $editcontent;
$editObject->controller($action,$data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'].'/html/classTemplates/rekeningmutaties/overige.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it