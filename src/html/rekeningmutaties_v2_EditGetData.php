<?php
include_once 'rekeningmutaties_v2_get_data.php';
$editObject->formVars['mutation_type'] = 'editForm';
$AETemplate = new AE_template();
/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';
$object->data['fields']['Fonds']['form_extra'] = '';
$object->data['fields']['Valutakoers']['form_extra'] = '';
$object->data['fields']['Aantal']['form_extra'] = '';


//$object->data['fields']['Fonds']['form_type'] = 'autocomplete';

/** set values **/

//limit Grootboekrekening
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

// grootboekgegevens ophalen

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
    $object->data['fields']["Grootboekrekening"]["form_options"][] = $gb['Grootboekrekening'];
  }
}
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




$object->setvalue('Valutakoers', '1.00');


/** set form size **/
$object->setPropertie('Boekdatum', 'form_size', 8);

if ( ! $data['type'] ) {
  $object->setPropertie('settlementDatum', 'form_size', 8);
}

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
        $("#Fonds").val(ui.item.field_value);
        $("#Fonds_hidden").val(ui.item.value);
        $("#fondseenheid").val(ui.item.data.Fondseenheid);
        $("#Valuta").val(ui.item.data.Valuta);
        $("#Valuta").trigger("change");
        
        fondsChanged(\'Fonds\');
        
        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');


        checkFondsAantal("Fonds");
      '
    )
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
$object->addRequired('Omschrijving');

//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften_v2.js\" type=text/javascript></script>\n";
$editcontent['script_voet'] .= $AETemplate->parseFile('rekeningmutaties/js/edit.js');

if ( ($object->get('Verwerkt') == 0 && $mutationType === 'temp') || ($mutationType !== 'temp' && $object->checkAccess()) ) {
  $editObject->formVars['deleteButton'] = '<a onClick="return confirm(\'Record wordt verwijderd. Weet u het zeker?\');" href="{location}?action=delete&id='.$object->get('id').'&rekening='.$object->get('Rekening').'&afschrift_id='.$afschrift['id'].'&type='.$mutationType.'&deleteType=deleteMutation" id="deleteLine" class="btn btn-gray"><img src="icon/16/delete.png" class="simbisIcon"> ' . vt('Verwijderen') . '</a>';
}
