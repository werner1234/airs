<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 7 oktober 2015
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/06/21 14:09:31 $
    File Versie         : $Revision: 1.12 $
 		
    $Log: modelportefeuillesperportefeuilleEdit.php,v $
    Revision 1.12  2019/06/21 14:09:31  rm
    7857

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$ajx = new AE_cls_ajaxLookup(array("portefeuille"));
$ajx->changeModuleTriggerID("portefeuille", "Portefeuille");
$ajx->extraParameters='includeConsolidatie=1';
//$ajx->changeModuleTriggerClass("portefeuille", "ajaxPortefeuille");

$subHeader      = "";
$mainHeader     = vt("muteren");

$__funcvar['listurl']  = "modelportefeuillesperportefeuilleList.php";
$__funcvar['location'] = "modelportefeuillesperportefeuilleEdit.php";

$object = new ModelPortefeuillesPerPortefeuille();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_GET, $_POST);

$action = $data['action'];






$aeJson = new AE_Json();

if ( isset ($_GET['type']) && $_GET['type'] == 'copyTo' ) {
  $copyData = $_GET;
  
  $AEArray = new AE_Array();
  
  $modelPortefeuillesPerPortefeuilleObj = new ModelPortefeuillesPerPortefeuille();
  $modelPortefeuillesPerPortefeuilleDatas = $modelPortefeuillesPerPortefeuilleObj->parseBySearch(array(
    'Portefeuille'  => $copyData['Portefeuille'],
    'Vanaf'  => date('Y-m-d', strtotime($copyData['Vanaf']))
  ),"all", null, -1);
  
  foreach ( $modelPortefeuillesPerPortefeuilleDatas as $modelPortefeuillesPerPortefeuilleData ) {
    $modelPortefeuillesPerPortefeuilleData['id'] = null;
    $modelPortefeuillesPerPortefeuilleData['add_date'] = date('Y-m-d H:i:s');
    $modelPortefeuillesPerPortefeuilleData['change_date'] = date('Y-m-d H:i:s');
    $modelPortefeuillesPerPortefeuilleData['add_user'] = $USR;
    $modelPortefeuillesPerPortefeuilleData['change_user'] = $USR;
    $modelPortefeuillesPerPortefeuilleData['Vanaf'] = date('Y-m-d', strtotime($copyData['newDate']));
    $modelPortefeuilleFixedInsertQuery = "INSERT INTO `ModelPortefeuillesPerPortefeuille` SET " . $AEArray->toSqlInsert($modelPortefeuillesPerPortefeuilleData);
    $db = new DB();
    $db->executeQuery($modelPortefeuilleFixedInsertQuery);
  }
  
  header("Location: modelportefeuillesperportefeuilleList.php");
  exit();
}


/** bij ajax post velden extra valideren */
if( requestType('ajax') && $action == 'update') {
  $errorMssg = array();
  $data['ModelPortefeuille'] = trim($data['ModelPortefeuille']);
  if ( empty ($data['ModelPortefeuille']) ) {
    unset($data['ModelPortefeuille']);
  }
  
  if ( empty ($data['Percentage']) ) {
    unset($data['Percentage']);
  } else {
    if ( ! is_numeric ($data['Percentage']) ) {
      $errorMssg['Percentage'] = vt('Foutief percentage ingevoerd.');
    }
  }
  
  if ( isset ($data['saveType']) && $data['saveType'] === 'new' )
  {
    if ( ! isset ($_GET['ModelPortefeuille']) || empty ($_GET['ModelPortefeuille']) ) {
      $_GET['ModelPortefeuille'] = $data['ModelPortefeuille'];
    }
    
    if ( ! isset ($data['ModelPortefeuille']) || empty ($data['ModelPortefeuille']) ) {
      $errorMssg['ModelPortefeuille'] = vt('ModelPortefeuille mag niet leeg zijn.');
    }
    
    if ( ! isset ($data['Percentage']) || empty ($data['Percentage']) ) {
      $errorMssg['Percentage'] = vt('Percentage mag niet leeg zijn.');
    }
  } else {
    $modelPortefeuillesPerPortefeuille = $object->parseById($data['id']);
    $data['Portefeuille'] = $modelPortefeuillesPerPortefeuille['Portefeuille'];

    if ( ! isset ($data['ModelPortefeuille']) || empty ($data['ModelPortefeuille']) ) {
      $data['ModelPortefeuille'] = $modelPortefeuillesPerPortefeuille['ModelPortefeuille'];
      $_GET['ModelPortefeuille'] = $modelPortefeuillesPerPortefeuille['ModelPortefeuille'];
    }
  }
  
  if ( ! empty ($errorMssg) ) {
    echo $aeJson->json_encode(array(
                                'success' => true,
                                'saved'   => false,
                                'errors'   => $errorMssg
                              ));
    exit();
  }
}




$object->data['fields']['ModelPortefeuille']['form_type'] = 'autocomplete';


$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
//$object->setOption('Portefeuille', 'form_type', 'text');
$editObject->formTemplate="modelportefeuillesperportefeuilleEditTemplate.html";

$editObject->controller($action,$data);





if( requestType('ajax') && $action == 'update' ) {
  $db = new DB();
//  $returnDataQuery = "
//    SELECT
//
//    ModelPortefeuilleFixed.id,
//    ModelPortefeuilleFixed.Portefeuille,
//    ModelPortefeuilleFixed.Fonds,
//    ModelPortefeuilleFixed.Percentage,
//    Fondsen.valuta,
//    BeleggingscategoriePerFonds.Beleggingscategorie,
//    BeleggingssectorPerFonds.Beleggingssector,
//    BeleggingssectorPerFonds.Regio,
//    BeleggingscategoriePerFonds.afmCategorie
//
//    FROM  ModelPortefeuilleFixed
//
//    LEFT JOIN Fondsen on ModelPortefeuilleFixed.Fonds=Fondsen.Fonds
//    Inner Join Portefeuilles ON ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille
//    Left Join BeleggingssectorPerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingssectorPerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingssectorPerFonds.Vermogensbeheerder
//    Left Join BeleggingscategoriePerFonds ON ModelPortefeuilleFixed.Fonds = BeleggingscategoriePerFonds.Fonds AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder
//
//     WHERE ModelPortefeuilleFixed.id = " . $object->get('id') . "
//  ";
//
//  $db->sql($returnDataQuery);
//  $linedata = $db->lookupRecord();
//
  $rowdata = '';
  if ( $data['saveType'] === 'new' ) {
  
    $deleteBtn = '';
    if ( (int) $data['frame'] !== 1 && $_SESSION['usersession']['gebruiker']['Beheerder'] == 1 )
    {
      $deleteBtn = "<span data-toggle='tooltip' title='".vt("Regel verwijderen")."' class='btn-new btn-default btn-xs deleteInline' data-rowid='" . $object->get('id') . "'><i class='fa fa-times' aria-hidden='true'></i></span>";
    }
    
    $rowdata = "
  <tr data-lineid='" . $object->get('id') . "' class='list_dataregel' onmouseover='this.className='list_dataregel_hover' 
      onmouseout='this.className='list_dataregel' title='".vt("Klik op de knop links om de details te zien/muteren")."'>
" . $deleteBtn . "
		  
  <td class='listTableData'  align='left'> &nbsp;</td>
  <td data-field='Portefeuille' class='listTableData' align='left'>" . $object->get('Portefeuille') . " &nbsp;</td>
  <td data-field='ModelPortefeuille' class='listTableData' align='left'>" . $object->get('ModelPortefeuille') . " &nbsp;</td>
  <td data-field='Percentage' class='listTableData' align='right'><span class='PercentageVal'>" . number_format($object->get('Percentage'), 2) . "</span> &nbsp;</td>
  <td data-field='Vanaf' class='listTableData' >" . date('j-n-Y', strtotime($object->get('Vanaf'))) . " &nbsp;</td>
  <td data-field='' class='listTableData'>
        <span data-toggle='tooltip' title='".vt("Regel Wijzigen")."' class='btn-new btn-default btn-xs editInline' data-rowid='" . $object->get('id') . "'><i class='fa fa-pencil-square-o' aria-hidden='true'></i> </span>
        <span class='btn-new btn-default btn-xs saveInline' style='display:none;' data-rowid='" . $object->get('id') . "'><i class='fa fa-floppy-o' aria-hidden='true'></i> </span>
        <span data-toggle='tooltip' title='Wijzigen ongedaan maken' class='btn-new btn-default btn-xs cancelInline' style='display:none;' data-rowid='" . $object->get('id') . "'><i class='fa fa-refresh' aria-hidden='true'></i> </span>
         &nbsp;</td>

  </tr>
    
    
    ";
  }

  echo $aeJson->json_encode(array(
                              'success' => true,
                              'saved'   => $editObject->result,
                              'lineData'  => array(
                                'id'  => $object->get('id'),
                                'ModelPortefeuille'  => $object->get('ModelPortefeuille'),
                                'Percentage'  => $object->get('Percentage'),
                              ),
                              'trHtml' => $rowdata
                            ));
  exit();
}

if( requestType('ajax') && $action == 'delete' ) {
  echo $aeJson->json_encode(array(
                              'success' => true,
                              'saved'   => $editObject->result,
                            ));
  exit();
}




$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('ModelPortefeuille');
$query = str_replace('WHERE', 'WHERE (ModelPortefeuilles.Portefeuille LIKE "%{find}%" OR ModelPortefeuilles.omschrijving  LIKE "%{find}%") AND ', $object->data['fields']['ModelPortefeuille']['select_query']);
$query = str_replace('SELECT ', 'SELECT ModelPortefeuilles.omschrijving, ', $query);

$editObject->formVars['ModelPortefeuille'] = $autocomplete->addVirtuelField('ModelPortefeuille', array(
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
  'form_value' => $editObject->object->data['fields']['ModelPortefeuille']['value'],
  'form_size' => '30',
));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('ModelPortefeuille');



if($data['frame']==1)
{
	if($_GET['Portefeuille'])
	{
		$object->set('Portefeuille', $_GET['Portefeuille']);
	}
	$object->setOption('Portefeuille', 'form_type', 'text');
	$object->setOption('Portefeuille', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);
	if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0 )
	{
		$editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="'.vt("Verzenden").'" align="absmiddle">&nbsp;'.vt("Naar AIRS verzenden").'</a>
    <input type="hidden" name="frame" value="1">';
	}
	else
  {
    $editObject->formVars["verzendKnop"] = vt('Geen rechten om te verzenden.');
  }

	echo $editObject->getOutput();
}
else
{
  $editObject->JSinsert = $ajx->getJsInTags();
  echo $editObject->getOutput();
}




if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{

	echo $_error = $editObject->_error;
}
