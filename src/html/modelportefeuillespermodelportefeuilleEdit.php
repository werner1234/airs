<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 11 maart 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/03/11 16:51:21 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: modelportefeuillespermodelportefeuilleEdit.php,v $
    Revision 1.2  2020/03/11 16:51:21  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = vt("modelportefeuilles per modelportefeuille");
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "modelportefeuillespermodelportefeuilleList.php";
$__funcvar['location'] = "modelportefeuillespermodelportefeuilleEdit.php";

$object = new ModelPortefeuillesPerModelPortefeuille();

$object->data['fields']['modelPortefeuille']['form_type'] = 'autocomplete';
$object->data['fields']['modelPortefeuilleComponent']['form_type'] = 'autocomplete';


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
$editObject->controller($action,$data);


$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('modelPortefeuille');
$query = str_replace('WHERE', 'WHERE (ModelPortefeuilles.Portefeuille LIKE "%{find}%" OR ModelPortefeuilles.omschrijving  LIKE "%{find}%") AND ', $object->data['fields']['modelPortefeuille']['select_query']);
$query = str_replace('SELECT ', 'SELECT ModelPortefeuilles.omschrijving, ', $query);

$editObject->formVars['modelPortefeuille'] = $autocomplete->addVirtuelField('modelPortefeuille', array(
  'autocomplete' => array(
    'query' => $query,

    'label' => array(
      'Portefeuille',
      'omschrijving',
    ),
    'searchable' => array(
      'Portefeuille',
      'omschrijving',
    ),
    'field_value' => array(
      'Portefeuille'
    ),
  ),
  'form_size' => '30',
));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('modelPortefeuille');


$autocomplete->resetVirtualField('modelPortefeuilleComponent');
$query = str_replace('WHERE', 'WHERE (ModelPortefeuilles.Portefeuille LIKE "%{find}%" OR ModelPortefeuilles.omschrijving  LIKE "%{find}%") AND ', $object->data['fields']['modelPortefeuilleComponent']['select_query']);
$query = str_replace('SELECT ', 'SELECT ModelPortefeuilles.omschrijving, ', $query);

$editObject->formVars['modelPortefeuilleComponent'] = $autocomplete->addVirtuelField('modelPortefeuilleComponent', array(
  'autocomplete' => array(
    'query' => $query,
    'label' => array(
      'omschrijving',
      'displayline'
    ),
    'searchable' => array(
      'Portefeuille',
      'omschrijving',
    ),
    'field_value' => array(
      'Portefeuille'
    ),
  ),
  'form_value' => $editObject->object->data['fields']['modelPortefeuilleComponent']['value'],
  'form_size' => '30',
));

$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('modelPortefeuilleComponent');




// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
