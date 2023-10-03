<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$AETemplate = new AE_template();

$__funcvar['listurl']  = "optieSymbolList.php";
$__funcvar['location'] = "optieSymbolEdit.php";

$object = new fondsOptieSymbolen();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$data = array_merge($_GET, $_POST);
$action = $data['action'];

$DB = new DB();
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']['optieValuta']['form_options'][] = $gb['Valuta'];
}

/** set input filters for fields **/

/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$editObject->formVars['Fonds_inputfield'] = $autocomplete->addVirtuelField('Fonds', array(
  'autocomplete' => array(
    'table' => 'Fondsen',
    'label' => array(
      'Fonds',
      'ISINCode'
    ),
    'searchable' => array(
      'Fonds',
      'ISINCode'
    ),
    'field_value' => array(
      'Fonds'
    ),
    'extra_fields' => array(
      'Valuta',
      'Fondseenheid',
    ),
    'conditions' => array(
      'HeeftOptie' => 1,
      'AND' => ' (Fondsen.EindDatum  >=  "' . date('Y-m-d') . '" OR Fondsen.EindDatum = "0000-00-00")'
    ),
    'value' => 'ISINCode', //value from table of join
    'actions' => array(
      'select' => '
      event.preventDefault();
        $("#Fonds").val(ui.item.field_value);
      '
    )
  ),
  'form_extra' => '',
  'form_size' => '30',
  ));


$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editObject->template['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');


$editObject->controller($action,$data);

//$object->addClass('aantal', 'maskValuta');

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>