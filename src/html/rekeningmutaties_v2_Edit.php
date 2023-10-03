<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2020/02/07 15:54:06 $
    File Versie         : $Revision: 1.29 $

    $Log: rekeningmutaties_v2_Edit.php,v $
    Revision 1.29  2020/02/07 15:54:06  rm
    8222


*/
include_once("wwwvars.php");


$mutatieId=$_GET['mutatieId'] ;
if(isset($_GET['mutatieId']) && $_GET['mutatieId'] > 0 && !isset($_GET['id']) && $_GET['action']=='edit' )
{
  $DB=new DB();
  $query="SELECT Afschriftnummer,Rekening FROM Rekeningmutaties WHERE id='".$_GET['mutatieId']."'";
  $DB->SQL($query);
  $mutatie=$DB->lookupRecord();
  $query="SELECT id FROM Rekeningafschriften WHERE Afschriftnummer='".$mutatie['Afschriftnummer']."' AND Rekening='".$mutatie['Rekening']."'";
  $DB->SQL($query);
  $dbAfschrift=$DB->lookupRecord();
  $_GET['afschrift_id']=$dbAfschrift['id'];
  $_GET['action']='edit';//'new';
  $_GET['id']=$_GET['mutatieId'];
  $_GET['type']='';
  //$_SESSION['terugNaarMutatieList']="rekeningmutatiesList.php";
}

if ( isset ($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'voorlopigeRekeningafschriften_v2_List') !== false || strpos($_SERVER['HTTP_REFERER'], 'rekeningafschriften_v2_List') !== false )
{
  if ( strpos($_SERVER['HTTP_REFERER'], 'status=verwerkt') !== false )
  {
    $_SESSION['listReturnAddon'] = 'status=verwerkt';
  }
  else
  {
    $_SESSION['listReturnAddon'] = '';
  }
}

$AETemplate = new AE_template();
$AEMessage = new AE_Message();

//$inst = new AIRS_invul_instructies();

$editcontent['javascript'] = '';
$AERekeningmutaties = new AE_RekeningMutaties ();
$data = array_merge($_GET, $_POST);

$action = $data['action'];
$mutationType = ( ( isset ($data['type']) && $data['type'] === 'temp') ? 'temp' : '');

if ( $mutationType === 'temp')
{
  $object = new VoorlopigeRekeningmutaties_v2();
  $__funcvar['listurl']  = 'rekeningmutaties_v2_List.php';
}
else
{
  $object = new Rekeningmutaties_v2();
  $__funcvar['listurl']  = 'rekeningmutaties_v2_List.php';
}

$__funcvar['location'] = "rekeningmutaties_v2_Edit.php";


/** mutatie verschil opheffen **/
if($_GET['action'] == 'opheffen')
{
  $AERekeningmutaties->balanceAccount ($data, ( isset ($data['type']) && $data['type'] === 'temp' ? 'temp' : '') );
  $action = 'new';
}

/**
 * Verwijder afschrift en mutatieregels
 */
if( $action == 'delete' && isset($data['deleteType']) && $data['deleteType'] == 'deleteAfschrift' )
{
  if ( $mutationType === 'temp')
  {
    $afschrift = new VoorlopigeRekeningafschriften ();
  }
  else
  {
    $afschrift = new Rekeningafschriften_v2();
  }
  
  $query="SELECT id FROM `".$object->data['table']."` WHERE `".$object->data['table']."`.`Afschriftnummer` = '".$data['afschrift_id']."' AND `".$object->data['table']."`.`Rekening` = '".$data['rekening']."'";
  $db = new DB;
  $db->SQL($query);

  $db->Query();
  while($gb = $db->NextRecord())
  {
    $removeDb = new DB;
    $removeQuery = 'DELETE FROM `'.$object->data['table'].'` WHERE `id` = ' . $gb['id'];
    $removeDb->SQL($removeQuery);
    $removeDb->Query();
  }
  
  $removeAfschriftQuery = 'DELETE FROM `'.$afschrift->data['table'].'` WHERE `'.$afschrift->data['table'].'`.`id` = ' . $data['id'] . ' AND `'.$afschrift->data['table'].'`.`Rekening` = "'.$data['rekening'].'"';
  $db->SQL($removeAfschriftQuery);
  $db->Query();

  if ( $mutationType === 'temp')
  {
    header("Location: voorlopigeRekeningafschriften_v2_List.php?type=temp");
  }
  else
  {
    header("Location: rekeningafschriften_v2_List.php");
  }
  exit();
}
elseif( $action == 'delete' && isset($data['deleteType']) && $data['deleteType'] == 'deleteMutation' )
{
  
  if ( $mutationType === 'temp')
  {
    $afschrift = new VoorlopigeRekeningafschriften ();
  }
  else
  {
    $afschrift = new Rekeningafschriften_v2();
  }
  $db = new DB;
  $removeQuery = 'DELETE FROM `'.$object->data['table'].'` WHERE `id` = ' . $data['id'];  
  
  $db->SQL($removeQuery);
  $db->Query();

  if ( $mutationType === 'temp')
  {
    header("Location: rekeningmutaties_v2_Edit.php?action=new&afschrift_id=".$data['afschrift_id']."&type=temp&mutatieId=".$mutatieId);
  }
  else
  {
    header("Location: rekeningmutaties_v2_Edit.php?action=new&afschrift_id=".$data['afschrift_id'].'&mutatieId='.$mutatieId);
  }
  exit();
}


if ( isset($data['Debet']) )
{
  $data['Debet'] = abs($data['Debet']);
}


if( ! isset($data['Fonds']) ) {$data['Fonds'] = '';}

$editObject = new editObject($object);

$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
//$editObject->formVars["invulScr"] = $inst->getMessageDiv();

$editObject->formVars['rekeningafschriftenEdit'] = 'rekeningafschriften_v2_Edit.php';
$editObject->formTemplate = "rekeningmutaties_v2_Template.html";

//$_SESSION['NAV']->returnUrl = "rekeningmutaties_v2_Edit.php?action=new&afschrift_id=" . $data['afschrift_id'];

$editObject->formVars['type'] = $mutationType;
$editObject->formVars['mutatieId'] = $mutatieId;
/** get afschrift **/
$afschrift = $AERekeningmutaties->getCopyData ((isset($_GET['afschrift_id']) && ! empty($_GET['afschrift_id']) ? $_GET['afschrift_id'] : null), ( isset ($data['type']) && $data['type'] === 'temp' ? 'VoorlopigeRekeningafschriften' : 'Rekeningafschriften'));
//$inst->getBeheerderViaRekening($afschrift['Rekening']);
//$editObject->formVars["VB"] = $inst->vermogensBeheerder;

if ( $action === 'edit'  &&  isset($data['id']) )
{
//  $__funcvar['listurl']  = ;
//  $_SESSION['NAV']->returnUrl = "rekeningafschriften_v2_List.php";
  $editcontent['style'] .= $editcontentNieuw['style'];

  include_once 'rekeningmutaties_v2_EditGetData.php';
  $editObject->formTemplate = "rekeningmutaties_v2_EditTemplate.html";

  $editObject->controller($action, $data);
  /**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$editObject->formVars['consoleLog'] = checkAccess();
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
        
        fondsChanged(\'Fonds\');
        
        $(\'#fonds-info\').html(\'Eenheid: \'+ ui.item.data.Fondseenheid).addClass(\'label label-info\');
        $(\'input[name="Fondskoerseenheid"]\').val(ui.item.data.Fondseenheid);
        
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
  'form_value'  => $editObject->object->data['fields']['Fonds']['value']
  ));
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');
  
}


if ( $action === 'edit' &&  isset($data['id']) )
{
  $_SESSION['NAV']->returnUrl = "rekeningmutaties_v2_Edit.php?action=new&afschrift_id=" . $data['afschrift_id'] . '&type=' . $mutationType.'&mutatieId='.$mutatieId;
}
elseif ( $action == 'new' && ! empty ($data['afschrift_id']) )
{
  $_SESSION['NAV']->returnUrl = "rekeningafschriften_v2_List.php" . ( ! empty ($_SESSION['listReturnAddon']) ? '?' . $_SESSION['listReturnAddon'] : '');
  if ( $mutationType === 'temp')
  {
    $_SESSION['NAV']->returnUrl = "voorlopigeRekeningafschriften_v2_List.php" . ( ! empty ($_SESSION['listReturnAddon']) ? '?' . $_SESSION['listReturnAddon'] : '');
  }
  if($mutatieId>0)
  {
    $_SESSION['NAV']->returnUrl = 'rekeningmutatiesList.php';
  }
}

$editObject->usetemplate = true;
$editObject->template = $editcontent;

$editObject->formVars['listurl'] = $__funcvar['listurl'];


/** create counter rule **/
if( isset ($data['submitCounterRule']) && is_numeric ($data['submitCounterRule']) )
{
  $AERekeningmutaties->balenceMemoriaalAccount($data['editRekening'], $data['editAfschriftNummer'], ( isset ($data['type']) ? $data['type'] : ''));
  
  header("Location: rekeningmutaties_v2_Edit.php?action=new&afschrift_id=" . $data['submitCounterRule'] . '&type=' . $mutationType);
  exit();
}

$editObject->controller($action,$data);


if ( ! empty ($afschrift) )
{
  //get mutation total
  $totals = $AERekeningmutaties->getMutationSum ($afschrift, ( isset ($data['type']) ? $data['type'] : ''));
 
  // Zet Fieldset Class voor mutatie veschil
  if($totals['mutatieVerschil'] <> 0)
  {
    $totals['fieldsetClass'] = 'rekeningmutatie_verschil fieldsetWarningLeft';
    $totals['mutationDifferenceMessage'] = '<span id="differenceMessage" class="message"><strong>Let op! er is een mutatieverschil.</strong></span>';
  }
  else
  {
    $totals['fieldsetClass'] = "rekeningmutatie_geenverschil fieldsetsuccessLeft";
  }
  $editObject->formVars['aId'] = $afschrift["id"];
  
  $editObject->formVars = array_merge($editObject->formVars, $afschrift, $totals);
}
else
{
  $object->data['fields']["Memoriaalboeking"]["value"] = 1;
}

$_SESSION['NAV']->items['navedit']->buttonSave = false; //deny save here we dont need it
$_SESSION['NAV']->items['navedit']->buttonDelete = false; //deny delete here we dont need it


if ( $mutationType != 'temp')
{
  if ( checkAccess() )
  {
    $editObject->formVars['changeAfschrift'] = '<a href="' . $editObject->formVars['rekeningafschriftenEdit'] . '?action=edit&id=' . $editObject->formVars['aId'] . '&memoriaal=' . $editObject->formVars['aMemoriaal'] . '&afschrift_id=' . $editObject->formVars['aId'] . '&type=' . $mutationType . '&mutatieId='.$mutatieId.'" class="btn btn-gray">'.vt("Wijzigen").'</a>';
  }
  
  if ( $object->checkAccess('delete') )
  {
    $editObject->formVars['deleteAfschrift'] = '<a href="rekeningmutaties_v2_Edit.php?action=delete&id=' . $editObject->formVars['aId'] . '&rekening=' . $editObject->formVars['aRekening'] . '&memoriaal=' . $editObject->formVars['aMemoriaal'] . '&afschrift_id=' . $editObject->formVars['Afschriftnummer'] . '&type=' . $mutationType . '&mutatieId='.$mutatieId.'&deleteType=deleteAfschrift" id="deleteAll" class="btn btn-gray">'.vt("Verwijderen").'</a>';
  }
  $editObject->formVars['balanceAccount'] = '<span id="balanceAccount" class="btn btn-gray" type="button" name="verschilOpheffen" value="" size="12" align="right" ><img src="icon/16/replace2.png" width="16" height="16" border="0" alt="" align="absmiddle"> '.vt("Mutatie verschil opheffen").'</span>';
}
elseif ( $mutationType == 'temp' && ( isset ($afschrift['Verwerkt']) && $afschrift['Verwerkt'] == 0 ) )
{
  $editObject->formVars['changeAfschrift'] = '<a href="' . $editObject->formVars['rekeningafschriftenEdit'] . '?action=edit&id=' . $editObject->formVars['aId'] . '&memoriaal=' . $editObject->formVars['aMemoriaal'] . '&afschrift_id=' . $editObject->formVars['aId'] . '&type=' . $mutationType . '&mutatieId='.$mutatieId.'" class="btn btn-gray">'.vt("Wijzigen").'</a>';
  $editObject->formVars['deleteAfschrift'] = '<a href="rekeningmutaties_v2_Edit.php?action=delete&id=' . $editObject->formVars['aId'] . '&rekening=' . $editObject->formVars['aRekening'] . '&memoriaal=' . $editObject->formVars['aMemoriaal'] . '&afschrift_id=' . $editObject->formVars['Afschriftnummer'] . '&type=' . $mutationType . '&deleteType=deleteAfschrift&mutatieId='.$mutatieId.'" id="deleteAll" class="btn btn-gray">'.vt("Verwijderen").'</a>';
  $editObject->formVars['balanceAccount'] = '<span id="balanceAccount" class="btn btn-gray" type="button" name="verschilOpheffen" value="" size="12" align="right" ><img src="icon/16/replace2.png" width="16" height="16" border="0" alt="" align="absmiddle"> '.vt("Mutatie verschil opheffen").'</span>';
}

if ( $mutationType != 'temp' && ! checkAccess() )
{
  $editObject->formVars['radio_select'] = '<div class="alert alert-info"> '.vt("Rekeningafschrift kan niet gemuteerd worden").'</div>';
} elseif ($mutationType == 'temp' && ( isset ($afschrift['Verwerkt']) && $afschrift['Verwerkt'] > 0 )  ) {
  $editObject->formVars['radio_select'] = '<div class="alert alert-info"> '.vt("Rekeningafschrift is al verzonden").'</div>';
}
else
{
  $editObject->formVars['radio_select'] = $AETemplate->parseFile(
    'rekeningmutaties/radio_select.html', 
    array_merge($data, $afschrift)
  );
}

$editObject->formVars['Memoriaal'] = $afschrift['Memoriaal'];
if ( $afschrift['Memoriaal'] == 1 )
{
  $editObject->formVars['counterRules'] = '<br /><br /> <button type="submit" class="btn btn-gray" name="submitCounterRule" value="' . $afschrift['id'] . '">' . maakKnop('replace2.png') . ' '.vt("Tegenregel aanmaken").'</button>';
  if( ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 ) && ( isset ($data['type']) && $data['type'] != 'temp') )
  {
    $editObject->formVars['counterRules'] = '';
  }
}

$AEDate = new AE_datum();
$DB = new DB();
$query="SELECT
Bedrijfsgegevens.vastzetdatumRapportages,
Rekeningen.Rekening,
Bedrijfsgegevens.Bedrijf,
VermogensbeheerdersPerBedrijf.Vermogensbeheerder,
Portefeuilles.startdatum
FROM
Bedrijfsgegevens
INNER JOIN VermogensbeheerdersPerBedrijf ON Bedrijfsgegevens.Bedrijf = VermogensbeheerdersPerBedrijf.Bedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE Rekeningen.Rekening='".mysql_real_escape_string($afschrift["Rekening"])."'";

$DB->SQL($query);
$DB->Query();
$vastzetdatumRapportages = $DB->NextRecord();

$boekDatumCheckDate = '';
if ( ! empty ($vastzetdatumRapportages['startdatum']) ) {
  $boekDatumCheckDate = date('d-m-Y', strtotime($vastzetdatumRapportages['startdatum']));
}
$editObject->formVars['boekDatumCheckDate'] = $boekDatumCheckDate;

if($vastzetdatumRapportages['vastzetdatumRapportages'] != '0000-00-00')
{
  $vastzetdatum = $vastzetdatumRapportages['vastzetdatumRapportages'];//$AEDate->dbToForm($vastzetdatumRapportages['vastzetdatumRapportages']);
  $editObject->template['javascript'] .= '
    function checkVastzetDatum () {
      var ret = true;
      var bookDate = $.datepicker.parseDate("dd-mm-yy",  $("input[name=Boekdatum]").val());
      
      var blockDate = $.datepicker.parseDate("yy-mm-dd",  "' . $vastzetdatum . '");
      if (blockDate >= bookDate) {
        ret=confirm("'.vt("De boekdatum ligt voor de vastzet datum").' (' . $AEDate->dbToForm($vastzetdatum) . ') '.vt("wilt u doorgaan").'?");
      }
      return ret;
    };
  ';
}
else
{
  $editObject->template['javascript'] .= '
    function checkVastzetDatum () {
      return true;
    };
  ';
}




if ( $mutationType != 'temp' )
{
  $rekeningDuplicaatObj = new RekeningenDuplicaat();
  $heeftDuplicaatRekening = $rekeningDuplicaatObj->parseBySearch(
    array('Rekening' => $afschrift['Rekening'], 'actief' => 1)
  );

  if ( ! empty ($heeftDuplicaatRekening) && is_array($heeftDuplicaatRekening) )
  {
    $AEMessage->setMessage('<strong>LET OP:</strong> '.vt("Er bestaat een duplicaatrekening voor deze rekening").'', 'info');
  }

  $isDuplicaatRekening = $rekeningDuplicaatObj->parseBySearch(
    array('RekeningDuplicaat' => $afschrift['Rekening'], 'actief' => 1)
  );

  if ( ! empty ($isDuplicaatRekening) && is_array($isDuplicaatRekening) ) {
    $AEMessage->setMessage('<strong>LET OP:</strong> '.vt("Het betreft hier een duplicaatrekening").'', 'info');
  }
}

$editObject->formVars['AEMessages'] = $AEMessage->getMessage() . $AEMessage->getFlash();

//     <script type="text/javascript" src="javascript/AE-jqueryPluginInvulinstructie.js"></script>
/** set js / css **/
$editObject->template['style'] .= $AETemplate->loadCss('rekeningmutaties');//'<link href="style/rekeningmutaties.css" rel="stylesheet" type="text/css" media="screen">';
$editObject->template['style'] .= $AETemplate->loadCss('AE-jqueryPluginInvulinstructie');
$editObject->template['style'] .= $AETemplate->loadCss('/fontAwesome/font-awesome.min.css');

$editObject->template['jsincludes'] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>\n";
$editObject->template['jsincludes'] .= '<script language=JavaScript src="javascript/rekeningAfschriften_v2.js" type=text/javascript></script>';

$editObject->template['style'] .= $AETemplate->loadCss('jquery.webui-popover');
$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery.webui-popover');


echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: rekeningmutaties_v2_Edit.php?action=new&afschrift_id=".$afschrift["id"]."&message=".urlencode($editObject->message) . '&type=' . $mutationType.'&mutatieId='.$mutatieId);
	//header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
