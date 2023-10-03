<?php

//create some fake fields

//<div class="formblock">
//          <div class="formlinks"></div>
//          <div class="formrechts">
//            <input type='text' name='turbo_isinCode' id='turboIsinCode' value=''>
//            {turbo_isinCode_error}
//          </div>
//        </div>

//debug($_GET);

$editObject->formVars['turboLong'] = (isset($_GET['turboLong'])? $_GET['turboLong']:'');
$editObject->formVars['turboShort'] = $_GET['turboShort'];

$object->data['fields']['turbo_isinCode'] = array_merge(array(
  'description'=>'Turbo ISIN-Code',
  'db_type'=>'varchar',
  'form_size'=>'16',
  'form_type'=>'text',
  'form_visible'=>true,
  'value' => $_GET['turbo_isinCode']
), (array)$object->data['fields']['turbo_isinCode']);

$object->data['fields']['turbo_issuer'] = array_merge(array(
  'description'=>'Uitgevende instelling',
  'form_type'=>'selectKeyed',
  'form_options'=> array(
    'AAB'     => 'ABN AMRO',
    'BIN'     => 'Binck Banck',
    'BNP'     => 'BNP Paribas',
    'CITI'    => 'Citigroup',
//    'CB'      => 'Commerzbank', // 9402
    'GS'      => 'Goldman Sachs',
    'ING'     => 'ING Bank',
    'RBS'     => 'Royal Bank of Scotland',
    'SG'      => 'Soc. Generale'
  ),
  'form_visible'=>true,
  'value' => $_GET['turbo_issuer']
), (array)$object->data['fields']['turbo_issuer']);


$object->data['fields']['turbo_kind'] = array_merge(array(
  'description'=>'Soort Turbo',
  'form_type'=>'selectKeyed',
  'form_options'=> array(
    'BSp'     => 'Best speeder',
    'BSpr'    => 'Best sprinter',
    'BTb'     => 'Best turbo',
    'B'       => 'Booster',
    'mFut'    => 'Mini future',
    'S'       => 'Speeder',
    'Spr'     => 'Sprinter',
    'Tr'      => 'Trader',
    'T'       => 'Turbo',
  ),
  'form_visible'=>true,
  'value' => $_GET['turbo_kind']
), (array)$object->data['fields']['turbo_kind']);

$object->data['fields']['turbo_longShort'] = array_merge(array(
  'description'=>'Long / Short',
  'form_type'=>'selectKeyed',
  'form_options'=> array(
    'L'   => 'Long',
    'S'   => 'Short'
  ),
  'form_visible'=>true,
  'value' => $_GET['turbo_longShort']
), (array)$object->data['fields']['turbo_longShort']);


$object->data['fields']['turbo_stopLoss'] = array_merge(array(
  'description'=>'StopLoss',
  'db_type'=>'varchar',
  'form_size'=>'16',
  'form_type'=>'text',
  'form_visible'=>true,
  'value' => $_GET['turbo_stopLoss']
), (array)$object->data['fields']['turbo_stopLoss']);
$object->addClass('turbo_stopLoss', 'maskValuta4digitsPositive');


/** setup fonds selection **/
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('fondsTurboSymbolen');
$editObject->formVars['fondsTurboSymbolen'] = $autocomplete->addVirtuelField('fondsTurboSymbolen', array(
  'autocomplete' => array(
    'table' => 'fondsTurboSymbolen, Fondsen',
    'label' => array(
      'Fonds',
      'combine' => '({short} / {long})'
    ),
    'searchable' => array(
      'fondsTurboSymbolen`.`Fonds',
      'fondsTurboSymbolen`.`short',
      'fondsTurboSymbolen`.`long',
      'Fondsen`.`ISINCode'
    ),
    'field_value' => array(
      'Fonds'
    ),
    'extra_fields' => array(
      '*',

    ),
    'conditions' => array(
      'fondsTurboSymbolen`.`Fonds' => '`Fondsen`.`Fonds`',
      'AND' => ' (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = "0000-00-00")'
    ),
    'value' => 'key', //value from table of join
    'actions' => array(
      'select' => '
          event.preventDefault();

          $("#fondsTurboSymbolen").val(ui.item.field_value);
          $("#TurboBovenliggendFonds").val(ui.item.data.Fonds);
          
          $("#turboLong").val(ui.item.data.long);
          $("#turboShort").val(ui.item.data.short);
          
          $("#Beurs").val("NL");
          
          $("#standaardSector").val(ui.item.data.standaardSector);
          
          $("#Fondseenheid").val(1);
          $("#Valuta").val("EUR");
          $("#fondssoort").val("TURBO");
          
          makeTurboFonds ();
          makeTurboDescription();
        '
    )
  ),
  'form_size' => '24',
  'form_value' => ( isset($data['fondsTurboSymbolen']) ? $data['fondsTurboSymbolen'] : ''),
  'form_class' => (isset ($editObject->object->data['fields']['fondsTurboSymbolen']['form_class']) ? $editObject->object->data['fields']['fondsTurboSymbolen']['form_class'] : '')
));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('fondsTurboSymbolen');

if ( isset ($editObject->object->data['fields']['fondsTurboSymbolen']['error']) ) {
  $editObject->formVars['fondsTurboSymbolen'] .= "\n<div style=\"color:red;\">".$object->getError('fondsTurboSymbolen')."</div>\n";
}

$jsData['fondsTurboSymbolenDisplay'] = ( isset($data['fondsInputType']) && $data['fondsInputType'] === 'fondsTurbo' ? 'true' : 'false' );