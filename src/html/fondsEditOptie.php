<?php
  /** clone fields **/
$object->data['fields']['optieOptieType'] = array_merge(array(
  'description'   => $object->data['fields']['OptieType']['description'],
  'form_type'     => 'select',
  'form_options'  => $object->data['fields']['OptieType']['form_options'],
  'form_visible'  => true,
  'value'         => $_GET['optieOptieType']
), (array)$object->data['fields']['optieOptieType']);

$object->data['fields']['optieOptieUitoefenPrijs'] = array_merge(array(
  'description'   =>'Uitoefenprijs',
  'db_type'       =>'varchar',
  'form_size'     =>'16',
  'form_type'     =>'text',
  'form_visible'  =>true,
  'value'         => $_GET['optieOptieUitoefenPrijs']
), (array)$object->data['fields']['optieOptieUitoefenPrijs']);
$object->addClass('optieOptieUitoefenPrijs', 'maskValuta');

$object->data['fields']['optieexpiratieMaand'] = array_merge(array(
  'description'   => $object->data['fields']['OptieExpDatum']['description'],
  'form_type'     => 'selectKeyed',
  'form_options'  => $OptieExpMaand,
  'form_visible'  => true,
  'value'         => $_GET['optieexpiratieMaand']
), (array)$object->data['fields']['optieexpiratieMaand']);

$object->data['fields']['optieexpiratieJaar'] = array_merge(array(
  'description'   => '', //leeg zodat 2 velden naast elkaar kunnen staan
  'form_type'     => 'selectKeyed',
  'form_options'  => $OptieExpJaar,
  'form_visible'  => true,
  'value'         => $_GET['optieexpiratieJaar']
), (array)$object->data['fields']['optieexpiratieJaar']);



//  $object->data['fields']['optieOptieType'] = $object->data['fields']['OptieType'];
//  $object->data['fields']['optieOptieUitoefenPrijs'] = $object->data['fields']['OptieUitoefenPrijs'];
  
  /** setup fonds selection **/
  $autocomplete = new Autocomplete();
  $autocomplete->resetVirtualField('fondsOptieSymbolen');
  $editObject->formVars['fondsOptieSymbolen'] = $autocomplete->addVirtuelField('fondsOptieSymbolen', array(
    'autocomplete' => array(
      'table' => 'fondsOptieSymbolen, Fondsen',
      'label' => array(
        'key',
        'Fonds',
        'combine' => '({optieBeurs} / {aantal})'
      ),
      'searchable' => array(
        'key',
        'fondsOptieSymbolen`.`Fonds'
      ),
      'field_value' => array(
        'key'
      ),
      'extra_fields' => array(
        '*',
      ),
      'conditions' => array(
        'fondsOptieSymbolen`.`Fonds' => '`Fondsen`.`Fonds`',
        'AND' => ' (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = "0000-00-00")',
        'AND' => ' Fondsen.Fonds <> "" '
      ),
      'value' => 'key', //value from table of join
      'actions' => array(
        'select' => '
          event.preventDefault();

          $("#fondsOptieSymbolen").val(ui.item.field_value);
          $("#OptieBovenliggendFonds").val(ui.item.data.Fonds);
          $("#Beurs").val(ui.item.data.optieBeurs);
          
          $("#Fondseenheid").val(ui.item.data.aantal);
          $("#standaardSector").val(ui.item.data.standaardSector);
          
          $("#Fonds").val(ui.item.data.Fonds);
          $("#Valuta").val(ui.item.data.optieValuta);
          $("#fondssoort").val("OPT");
          
          $("#optieIdentifierVWD").val(ui.item.data.optieVWD);
          $("#optieVWDSuffix").val(ui.item.data.optieVWDSuffix);
          $("#optieVWDFactor").val(ui.item.data.optieVWDFactor);
          
          makeFonds ();
          makeDescription ();
          makeImportCode ();
          makeVWD ();
        '
      )
    ),
    'form_size' => '24',
    'form_value' => ( isset($data['fondsOptieSymbolen']) ? $data['fondsOptieSymbolen'] : ''),
    'form_class' => (isset ($editObject->object->data['fields']['fondsOptieSymbolen']['form_class']) ? $editObject->object->data['fields']['fondsOptieSymbolen']['form_class'] : '')
  ));
  $editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('fondsOptieSymbolen');
  
  if ( isset ($editObject->object->data['fields']['fondsOptieSymbolen']['error']) ) {
    $editObject->formVars['fondsOptieSymbolen'] .= "\n<div style=\"color:red;\">".$object->getError('fondsOptieSymbolen')."</div>\n";
  }
  
  $jsData['fondsOptieSymbolenDisplay'] = ( isset($data['fondsInputType']) && $data['fondsInputType'] === 'fondsOption' ? 'true' : 'false' );