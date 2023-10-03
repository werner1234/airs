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
$editObject->formVars['mutation_type'] = 'dividendcoupon';

/**
 * Handle fields
 */
/** reset fields **/
$object->data['fields']['Valuta']['form_extra'] = '';
$object->data['fields']['Fonds']['form_extra'] = '';
$object->data['fields']['Valutakoers']['form_extra'] = '';

/** Clone fields **/
$object->data['fields']['dividend_valuta'] = $object->data['fields']['Valuta'];
$object->data['fields']['kosten_valuta'] = $object->data['fields']['Valuta'];

$object->data['fields']['value'] = $object->data['fields']['Bedrag'];
$object->data['fields']['dividend_value'] = $object->data['fields']['Bedrag'];
$object->data['fields']['kosten_value'] = $object->data['fields']['Bedrag'];

$object->data['fields']['kosten_bedrag'] = $object->data['fields']['Bedrag'];
$object->data['fields']['dividend_bedrag'] = $object->data['fields']['Bedrag'];


$object->data['fields']['dividend_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['kosten_Valutakoers'] = $object->data['fields']['Valutakoers'];
$object->data['fields']['kosten_Grootboekrekening'] = $object->data['fields']['Grootboekrekening'];



//$object->data['fields']['Fonds']['form_type'] = 'autocomplete';

/** set input filters for fields **/
$object->addClass('dividend_value', 'maskValuta2digitsPositive');
$object->addClass('kosten_value', 'maskValuta2digitsPositive');
$object->addClass('value', 'maskValuta2digits');
$object->addClass('Bedrag', 'maskValuta2digits');
$object->addClass('kosten_bedrag', 'maskValuta2digits');
$object->addClass('dividend_bedrag', 'maskValuta2digits');

$object->addClass('Valutakoers', 'maskNumeric10Digits');
$object->addClass('dividend_Valutakoers', 'maskNumeric10Digits');
$object->addClass('kosten_Valutakoers', 'maskNumeric10Digits');

$object->setPropertie('Bedrag', 'form_extra', 'READONLY');
$object->setPropertie('kosten_bedrag', 'form_extra', 'READONLY');
$object->setPropertie('dividend_bedrag', 'form_extra', 'READONLY');


/** set values **/
$object->data['fields']['Omschrijving']['value'] = 'Dividend/Coupon';
$object->data['fields']['kosten_Grootboekrekening']['form_extra'] = '';

/** set form size **/
$object->setPropertie('value', 'form_size', 8);
$object->setPropertie('dividend_value', 'form_size', 14);
$object->setPropertie('kosten_value', 'form_size', 14);
$object->setPropertie('Boekdatum', 'form_size', 8);
$object->setPropertie('settlementDatum', 'form_size', 8);
$object->setPropertie('Valutakoers', 'form_size', 11);
$object->setPropertie('dividend_Valutakoers', 'form_size', 11);
$object->setPropertie('kosten_Valutakoers', 'form_size', 11);
$object->setPropertie('Omschrijving', 'form_size', 50);
$object->setPropertie('Bedrag', 'form_size', 8);



//limit Grootboekrekening
$divGrootboeken=array('DIV','RENOB','VKSTO');
$object->data['fields']['Grootboekrekening']['form_options'] = array();
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$divGrootboeken;
foreach($divGrootboeken as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['Grootboekrekening']['form_options'][]=$grootboek;
}

//limit Grootboekrekening
$kostGrootboeken=array('KOST','KOBU','KNBA','ROER');
$object->data['fields']['kosten_Grootboekrekening']['form_options'] = array();
if(!is_array($grootboekrekeningen))
  $grootboekrekeningen=$divGrootboeken;
foreach($kostGrootboeken as $grootboek)
{
  if(in_array($grootboek,$grootboekrekeningen))
    $object->data['fields']['kosten_Grootboekrekening']['form_options'][]=$grootboek;
}



$db=new DB();
$query = "
select 
  distinct Portefeuille, Fonds 

  from Rekeningmutaties 

  inner join Rekeningen on Rekeningmutaties.Rekening = Rekeningen.Rekening 

  where Rekeningmutaties.boekdatum = '" . date('Y', strtotime($afschrift['aDatum'])) . "0101' 
  AND Rekeningmutaties.Fonds <> '' 
  AND Portefeuille IN ('" . implode('\', \'', $portefeuilleData) . "')
";

$db->SQL($query);
$db->Query();
while($gb = $db->NextRecord())
{
  $markList[] = $gb['Fonds'];
}


/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$editObject->formVars['dividend_fonds'] = $autocomplete->addVirtuelField('Fonds', array(
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
    'markRecords' => array(
      'markOn' => 'Fonds',
      'markList'  => $markList
    ),
    'extra_fields' => array(
      'Valuta',
      'Fondseenheid',
    ),
    'value' => 'ISINCode', //value from table of join
    'source_data' => array(
      'name' => array(
        'Boekdatum',
        'fondssoortExclude'
      )
    ),
    'actions' => array(
      'select' => '
      event.preventDefault();
        $("#Fonds").val(ui.item.field_value);
        $("#Fonds_hidden").val(ui.item.value);
        $("#fondseenheid").val(ui.item.data.Fondseenheid);

        $("#fonds_omschrijving").val(ui.item.data.Omschrijving);
        $("#fonds_fonds").val(ui.item.data.Fonds);

        $("#Valuta").val(ui.item.data.Valuta);
        $("#Valuta").trigger("change");
        
        var grootboek = "Dividend";
        if ( $("#Grootboekrekening").val() == "RENOB") {
          var grootboek = "Coupon";
        }

        
        setDescription ();
        checkFondsAantal("Fonds");
      '
    ),
    'conditions' => array(
      'AND' => array(
        ' (Fondsen.EindDatum  >=  "{$get:Boekdatum}" OR Fondsen.EindDatum = "0000-00-00")',
        ' `fondssoort` NOT IN ({$get:fondssoortExclude})'
      )
    ),
  ),
  'form_extra' => '',
  'form_class' => 'fondsLookup requiredField',
  'form_size' => '20',
  ));
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');


/** set required fields **/
$object->addRequired('Boekdatum');
$object->addRequired('Grootboekrekening');
$object->addRequired('Valuta');
$object->addRequired('value');
$object->addRequired('Omschrijving');

$object->addRequired('dividend_valuta');
$object->addRequired('dividend_value');
$object->addRequired('dividend_Valutakoers');

$object->addRequired('kosten_value');
$object->addRequired('kosten_Grootboekrekening');
$object->addRequired('kosten_valuta');
$object->addRequired('kosten_Valutakoers');

/** acties voor voorlopigerekeningmutaties **/
if ( isset($data['type']) && $data['type'] === 'temp' ) {
  //unset settlementDatum
  unset($editObject->object->data['fields']['settlementDatum']);
}

$template = new AE_template();
$template->loadTemplateFromFile('rekeningmutaties/js/dividend_coupons.js', 'js');
$editcontent['script_voet'] .= $template->parseBlock('js');
//$editcontent['javascript'] = '';
$editObject->template = $editcontent;
$editObject->controller($action, $data);
//listarray($editObject);

$editObject->formTemplate = $__appvar['basedir'] . '/html/classTemplates/rekeningmutaties/dividend_coupons.html';
$editObject->usetemplate = true;
//$editObject->includeHeaderInOutput = false;

echo $editObject->getOutput();
$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it