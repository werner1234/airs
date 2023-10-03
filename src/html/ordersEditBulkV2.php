<?php

include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_dateFrom.php");
include_once("../classes/mysqlList.php");
include_once("orderControlleRekenClassV2.php");

session_start();
$__funcvar['listurl']  = "bulkordersListV2.php";
$__funcvar['location'] = "bulkordersEditV2.php";

//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("Verwerken","tijdelijkebulkordersList.php");
//$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem('Importeren uit bestand','ordersEditBulkV2_import.php?action=select');


$_GET = array_merge($_GET,$_POST);
$currentBatch=$_GET['batchId'];

$object = new TijdelijkeBulkOrdersV2();
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
//$_SESSION['NAV']='';
//listarray($_GET);
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","editForm");
$koppelObject[0]->addFields("ISINCode","ISINCode",true,true);
$koppelObject[0]->addFields("Fonds","fonds",false,true);
$koppelObject[0]->addFields("Omschrijving","",true,true);
//$koppelObject[0]->action = "fondsSelected();";
$koppelObject[0]->name = "fonds";
$koppelObject[0]->focus = "koersLimiet";
$koppelObject[0]->extraQuery = " AND (EindDatum > NOW() OR EindDatum = '0000-00-00') ORDER BY Fondsen.Omschrijving";

//$editcontent['javascript']=str_replace('document.editForm.submit();','if(checkPage())document.editForm.submit();',$editcontent['javascript']);
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
//$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/ordersEdit.js\" type=text/javascript></script>\n";
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();
$editcontent['body'] = "onLoad='document.editForm.portefeuille.focus();'";

$editcontent['javascript'] .= "

function submitForm()
{
  document.editForm.submit();
}

function addRecord()
{
	//parent.frames['content'].location = '".$editScript."?action=new';
}

function deleteRecord(id)
{
 AEConfirm('" . vt('Weet u zeker dat u deze regel wilt verwijderen?') . "','Verwijderen regel',function (){ parent.frames['content'].location = '".$editScript."?action=delete&id='+id;} ,function (){ var tmp=false;}  );

}
";

$type='portefeuille';
if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 else
	 {
    	$join=" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}

//}
$editObject->__appvar = $__appvar;
$data = $_GET;

if ( $data['action'] === 'update'
  && ( isset($data['portefeuille']) && ! empty($data['portefeuille']) )
  && ( isset($data['fonds']) && ! empty($data['fonds']) )
) {
  $portefeuilleObject = new Portefeuilles ();
  $portefeuilleData = $portefeuilleObject->parseBySearch(
    array(
      'Portefeuille' => $data['portefeuille']
    ),
    array(
      'Vermogensbeheerder'
    )
  );

  $beleggingscategoriePerFondsObject = new BeleggingscategoriePerFonds ();
  $beleggingscategoriePerFondsData = $beleggingscategoriePerFondsObject->parseBySearch(
    array(
      'Vermogensbeheerder' => $portefeuilleData['Vermogensbeheerder'],
      'Fonds' => $data['fonds']
    ),
    array(
      'Beleggingscategorie'
    )
  );
  $data['Beleggingscategorie'] = $beleggingscategoriePerFondsData['Beleggingscategorie'];
}

if($data['aantal']<>0)
{
  $db=new DB();
  $q="SELECT SUM(Rekeningmutaties.aantal) as aantal FROM Rekeningmutaties
        INNER JOIN Rekeningen ON Rekeningen.Rekening = Rekeningmutaties.Rekening AND Rekeningen.inactief=0
        WHERE year(Rekeningmutaties.Boekdatum)='".date('Y')."' AND Rekeningmutaties.Fonds='".mysql_real_escape_string($data['fonds'])."' AND Grootboekrekening='FONDS'
        AND Rekeningen.Portefeuille = '".trim($data['portefeuille'])."'";
  $db->SQL($q);
  $db->Query();
  $aantalAanwezig	= $db->nextRecord();
  $aantalInPositie=$aantalAanwezig['aantal'];
  $data['aantalInPositie']=$aantalInPositie;
  if(substr($data['transactieSoort'],0,1)=='V')
    $data['nieuwAantal']=$aantalInPositie-$data['aantal'];
  else
    $data['nieuwAantal']=$aantalInPositie+$data['aantal'];

}

if($data['action']==''||$data['action']=='checkOrders')
  $data['action']='new';

$action = $data['action'];
$redirectUrl = "orderregelsList.php";
$editObject->usetemplate = true;
$data['controleStatus']=1;
if($action=='update' && isset($data['setDepotbank']) && $data['setDepotbank'] <> '')
{
  $data['depotbank']='';
  $data['rekening']='';
  $data['accountmanager']='';
}
$editObject->controller($action,$data);

if($action=='update' && $object->get('id') > 0)
{
  $orderLogs = new orderLogs();
  $orderLogs->getByField('bulkorderRecordId',$object->get('id'));
  if($orderLogs->get('id')<1)
  {
    $orderLogs->addToBulkLog($object->get('id'), 'Bulkorderregel aangemaakt door '.$USR);
  }
}
//echo $action;listarray( $editObject->object);exit;

$DB = new DB();
/*
$DB->SQL("SELECT pagina FROM TijdelijkeBulkOrdersV2 WHERE add_user='$USR' AND pagina <> 0 GROUP BY pagina ORDER BY change_date ASC");
$DB->Query();
$maxPagina=0;
$selectedPagina=$object->get('pagina');
if($_GET['pagina']&&$_GET['pagina']<>'')
  $selectedPagina=$_GET['pagina'];
if($_POST['pagina']&&$_POST['pagina']<>'')
  $selectedPagina=$_POST['pagina'];


while($pagina = $DB->NextRecord())
{
  if($pagina['pagina'] > $maxPagina)
    $maxPagina=$pagina['pagina'];
	$paginaOptions .= "<option value=\"".$pagina['pagina']."\"  ".($selectedPagina==$pagina['pagina']?"selected":"")." >".$pagina['pagina']."</option>\n";
}
*/
$editObject->formVars['koersLimietHidden_value']=0;
if($data['action']=='new')
{
  $object->set('portefeuille',$data['portefeuille']);
  $object->set('client',$data['client']);
}
elseif($data['action']=='edit')
{
  $valuta=$object->get('fondsValuta');
  $query="SELECT Koers FROM Valutakoersen WHERE valuta='$valuta' order by datum desc limit 1";
  $DB->SQL($query);//echo $query;exit;
  $koers=$DB->lookupRecord();
  $editObject->formVars['valutaKoers_value'] = $koers['Koers'];

  $fonds=$object->get('fonds');
  $query="SELECT Koers FROM Fondskoersen WHERE fonds='".mysql_real_escape_string($fonds)."' order by datum desc limit 1";
  $DB->SQL($query);//echo $query;exit;
  $koers=$DB->lookupRecord();
  $editObject->formVars['koersLimietHidden_value'] = $koers['Koers'];
}

$query="SELECT MAX(regelNr) as regelNr, max(depotbank) as depotbank FROM TijdelijkeBulkOrdersV2 WHERE bron='bulkInvoer' AND change_user='$USR' ";//AND pagina='$selectedPagina'
$DB->SQL($query);//echo $query;exit;
$regelNr=$DB->lookupRecord();
$laatsteDepotbank=$regelNr['depotbank'];
if($action=='edit' || $action=='new')
{
  if($object->get('regelNr')==1)
  {
    $object->set('regelNr',$regelNr['regelNr']+1);
  }
}


//$editObject->formVars['paginaOptions']=$paginaOptions;
//$editObject->formVars['maxPagina']=$maxPagina+1;

/** set autocomplete velden **/
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('ISINCode');
$editObject->formVars['Fonds'] = $autocomplete->addVirtuelField('ISINCode', array(
  'autocomplete' => array(
    'table' => 'Fondsen',
//    'order' => 'Fondskoersen.Datum DESC',
    'label' => array(
      'Fondsen.Fonds',
      'Fondsen.ISINCode',
      'combine' => '({Valuta})'
    ),
    'searchable' => array('Fondsen.Fonds','Fondsen.ISINCode','Fondsen.Omschrijving'),
    'field_value' => array('Fondsen.ISINCode'),
    'extra_fields' => array('*'),
//    'extra_fields' => array('Fondsen.Valuta','Fondsen.Fondseenheid','Fondsen.fondssoort',
//      'Fondsen.id', 'Fondsen.beurs', 'Fondsen.Valuta', 'Fondsen.OptieType', 'Fondsen.OptieUitoefenPrijs', 'Fondsen.OptieExpDatum'),
    'value' => 'ISINCode',
    'actions' => array(
      'select' => '
        event.preventDefault();
        $("#ISINCode").val(ui.item.field_value);
        $("input[name=fonds_id]").val(ui.item.data.id);
        $("input[name=fonds]").val(ui.item.data.Fonds);
        $("#fondsOmschrijving").val(ui.item.data.Omschrijving);
        $("#fondsOmschrijvingHidden").val(ui.item.data.Omschrijving);


        $("#fondseenheid").val(ui.item.data.Fondseenheid);
        $("#fondsBeurscode").val(ui.item.data.beurs);
        $("#fondsValuta").val(ui.item.data.Valuta);

        $("#optieType").val(ui.item.data.OptieType);
        $("#optieUitoefenprijs").val(ui.item.data.OptieUitoefenPrijs);
        $("#optieExpDatum").val(ui.item.data.OptieExpDatum);
        $("#fondssoort").val(ui.item.data.fondssoort);


        fondsChanged("fonds");
        valutaChanged(ui.item.data.Valuta);
        fillTransactionType(ui.item.data.fondssoort);
        isinChanged(ui.item.data);
        
      '),
    'conditions' => array('AND' => '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00")')
  ),
  'form_size' => '15',
  'validate'  => $object->data['fields']['fonds']['validate'],
  'form_value'  => $object->get('ISINCode'),
  'form_class'    => ( isset($ISINCodeClass) ? $ISINCodeClass : '')
));
$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('ISINCode');

$query = "
  SELECT
  Portefeuilles.id as id
  FROM (Portefeuilles)
  INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '" . $USR . "'
  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
  WHERE 1 AND (Portefeuilles.beperktToegankelijk = '0' OR Gebruikers.beperkingOpheffen = '1' )
  ORDER BY Portefeuilles.Portefeuille ASC";

$db=new DB();
$db->executeQuery($query);
while ( $queryData = $db->nextRecord() ) {
  $Portefeuilles[] = $queryData['id'];
}


$autocomplete->resetVirtualField('PortefeuilleSelectie');
$editObject->formVars['PortefeuilleSelectie'] = $autocomplete->addVirtuelField('PortefeuilleSelectie', array(
  'autocomplete' => array(
    'table' => 'Portefeuilles',
    'prefix'  => true,
    'returnType'  => 'expanded',
    'join' => array(
      'fixDepotbankenPerVermogensbeheerder' => array (
        'type'  => 'left',
        'on' => array(
          'Portefeuilles.Vermogensbeheerder' => 'vermogensbeheerder',
          'Portefeuilles.depotbank' => 'depotbank'
        )
      )
    ),
    'extra_fields' => array(
      'Portefeuille',
      'Client',
      'Vermogensbeheerder',
      'id',

      'Risicoklasse',
      'Depotbank',
      'fixDepotbankenPerVermogensbeheerder.depotbank',
      'fixDepotbankenPerVermogensbeheerder.fixDefaultAan',
      //'`Portefeuille` AS subPortefeuille'
    ),
    'label' => array('Portefeuilles.Portefeuille','Portefeuilles.Client'),
    'searchable' => array('Portefeuilles.Portefeuille','Portefeuilles.Client'),
//    'extra_fields'  => array(),
    'field_value' => array('Portefeuilles.Client'),
    'value' => 'Portefeuilles.Client',
    'actions' => array(
      'select' => 'event.preventDefault();
        $("#PortefeuilleSelectie").val(ui.item.field_value);
        $("#client").val(ui.item.field_value);
        $("#portefeuille").val(ui.item.data.Portefeuilles.Portefeuille);
        $("input[name=portefeuille_id]").val(ui.item.data.Portefeuilles.id);

        var fixDefaultAan=false;
        if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.fixDefaultAan != null )
        {
          if(ui.item.data.fixDepotbankenPerVermogensbeheerder.fixDefaultAan==1)
            fixDefaultAan=true;
        }
        
        if ( ui.item.data.fixDepotbankenPerVermogensbeheerder.depotbank != null  && ui.item.data.fixDepotbankenPerVermogensbeheerder.depotbank != "") {
          fixOrder(true,fixDefaultAan);
        } else {
          fixOrder(false,fixDefaultAan);
        }

        $("#fixOrder").change();
        $(".rekeningField").hide();
        clientChanged();

        $("#depobankValue").html(ui.item.data.Portefeuilles.Depotbank);
        $("#Depotbank").val(ui.item.data.Portefeuilles.Depotbank);
        $("#profileValue").html(ui.item.data.Portefeuilles.Risicoklasse);

    //    $("#Depotbank").val(ui.item.data.Portefeuilles.Depotbank);
        $("#Risicoklasse").val(ui.item.data.Portefeuilles.Risicoklasse);
        portefeuilleChanged (ui.item);

        portefeuilleChanged();


      '),
    'conditions' => array(
      'AND' => array(
        '(Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = "0000-00-00")',
        'Portefeuilles.id' => $Portefeuilles,
        '(SELECT COUNT(*) FROM `Rekeningen` WHERE Portefeuille = Portefeuilles.Portefeuille AND inactief = 0) > 0'
      )
    )
  ),
  'form_size' => '15',
  'form_value'  => $object->get('client'),
  'form_class'  => ( isset($PortefeuilleSelectieClass) ? $PortefeuilleSelectieClass : '')
));

$editcontent['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('PortefeuilleSelectie');


/**
 * Optie expiratie datum opmaken
 */
$OptieExpDatum = $object->data['fields']['optieExpDatum']['value'];
$expJaarDb = substr($OptieExpDatum, 0, 4);
$expMaandDb = substr($OptieExpDatum, 4, 2);

$huidigeJaar = date('Y') - 1; //get current year minus one for history

$i = 0;
$OptieExpJaar = '';
for ( $i; $i < 10; $i++ ) {
  $expJaar = $huidigeJaar + $i;
  if ($expJaar == $expJaarDb) {
    $OptieExpJaar .= "<option value=\"" . $expJaar . "\" SELECTED>" . $expJaar . "</option>";
  } else {
    $OptieExpJaar .= "<option value=\"" . $expJaar . "\" >" . $expJaar . "</option>";
  }
}
$editObject->formVars["OptieExpJaar"] = $OptieExpJaar;

$OptieExpMaand = '';
$huidigeMaand = date('n');
for ( $i = 1; $i < 13; $i++ ) {
  if ($i < 10) {
    $maandString = '0' . $i;
  } else {
    $maandString = $i;
  }

  if ($i == $expMaandDb) {
    $OptieExpMaand .= "<option value=\"$maandString\" SELECTED>" . $__appvar["Maanden"][$i] . " </option>";
  } else {
    $OptieExpMaand .= "<option value=\"$maandString\" >" . $__appvar["Maanden"][$i] . " </option>";
  }
}
$editObject->formVars["OptieExpMaand"] = $OptieExpMaand;

/** kopieren form voor nieuw fonds **/
$object->data['fields']['fondsISINCode'] = $object->data['fields']['ISINCode'];
$object->data['fields']['fondsFonds'] = $object->data['fields']['fonds'];
$object->data['fields']['fondsFonds']['form_visible'] = true;
$object->data['fields']['fondsFonds']['form_class'] = '';
$object->data['fields']['fondsFonds']['form_extra'] = '';
$object->data['fields']['fondsFonds']['error'] = '';
$object->set('fondsFonds', $object->get('fondsOmschrijving'));

$object->data['fields']['fondsFondsOmschrijving'] = $object->data['fields']['fondsOmschrijving'];
$object->data['fields']['fondsFondseenheid'] = $object->data['fields']['fondseenheid'];
$object->data['fields']['fondsFondsValuta'] = $object->data['fields']['fondsValuta'];

/** kopieren form voor nieuw optie **/
$object->data['fields']['optieISINCode'] = $object->data['fields']['ISINCode'];
$object->data['fields']['optieFonds'] = $object->data['fields']['fonds'];
$object->data['fields']['optieFonds']['form_visible'] = true;
$object->data['fields']['optieFonds']['form_class'] = '';
$object->data['fields']['optieFonds']['form_extra'] = '';
$object->data['fields']['optieFonds']['error'] = '';

$object->set('optieFonds', $object->get('fondsOmschrijving'));
$object->data['fields']['optieFondsOmschrijving'] = $object->data['fields']['fondsOmschrijving'];

$object->data['fields']['optieFondseenheid'] = $object->data['fields']['fondseenheid'];
$object->data['fields']['optieBeurs'] = $object->data['fields']['beurs'];
$object->data['fields']['optieFondsValuta'] = $object->data['fields']['fondsValuta'];
$object->data['fields']['optieOptieSymbool'] = $object->data['fields']['optieSymbool'];
$object->data['fields']['optieOptieType'] = $object->data['fields']['optieType'];
$object->data['fields']['optieOptieUitoefenprijs'] = $object->data['fields']['optieUitoefenprijs'];
$object->data['fields']['optieOptieExpDatum'] = $object->data['fields']['optieExpDatum'];

$object->data['fields']['fondsFondsOmschrijving']['form_extra'] = '';
$object->data['fields']['optieFondsOmschrijving']['form_extra'] = '';

$object->addClass('aantal', 'maskAmountPN');
$object->addClass('koersLimiet', 'maskValutaKoers');

//javascript bankdepotcodes
$AEJson = new AE_Json();
$editObject->formVars["BankDepotCodes"] = $AEJson->json_encode($__fixVars['BankDepotCodes']);

$AETemplate = new AE_template();
$editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/orderEditBulkTemplate.js', $editObject->formVars);

$editcontent['script_voet'] .= $AETemplate->parseFile('orders/js/ordersEdit.js');
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask.js');
//$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask-masks.js');
$editcontent['script_voet'] .= $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');


$editObject->formVars['addFonds'] = $AETemplate->parseBlockFromFileWithForm(
  'classTemplates/orders/newOrderSelectie.html',
  $editObject->formVars,
  $object
);

$editObject->formTemplate = "classTemplates/orders/orderEditBulkTemplate.html";
$editObject->template = $editcontent;

if ( checkOrderAcces ('handmatigBulk_opslaan') === true )
{
  $editObject->formVars['opslaanKnop'] = '<input class="btn-new btn-save" type="button" value="opslaan" id="formulierOpslaan"  />';
}


//echo template($__appvar["templateContentHeader"], $editcontent);
//
//$editObject->includeHeaderInOutput = false;
$_SESSION['NAV']='';
echo $editObject->getOutput();


if ($result = $editObject->result)
{
  global $__appvar;
  if(isset($__appvar['extraOrderLogging']))
    $extraLog=$__appvar['extraOrderLogging'];
  else
    $extraLog=false;
  if($extraLog)
    logIt("Portefeuille ".$_POST['portefeuille']." fonds:".$_POST['fonds']." aantal:".$_POST['aantal']." aangemaakt/bijgewerkt via handmatige bulk");

  header("Location: ordersEditBulkV2.php?action=new&portefeuille=".$_POST['portefeuille']."&client=".$_POST['client']."&pagina=".$selectedPagina);
}
else
{
	echo $_error = $editObject->_error;
}



$__appvar['rowsPerPage']=1000;

$subHeader     = "";
$mainHeader    = vt('Verwerk geselecteerde fondsregels tot orders.');

$editScript = "ordersEditBulkV2.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$delete=checkOrderAcces('handmatigBulk_opslaan');

$list->addColumn("TijdelijkeBulkOrdersV2","id",array("list_width"=>"100","search"=>false));
if($delete)
  $list->addColumn("","delete",array("description"=>' ',"list_width"=>"20","search"=>false,'list_align'=>'right'));
$list->addColumn("TijdelijkeBulkOrdersV2","regelNr",array("description"=>'#',"list_width"=>"30","search"=>false,'list_align'=>'right'));
$list->addColumn("TijdelijkeBulkOrdersV2","client",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","portefeuille",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","transactieSoort",array("search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","aantal",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","bedrag",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","ISINCode",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","koersLimiet",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","controleRegels",array("list_invisible"=>true,"list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrdersV2","orderbedrag",array("description"=>'Bedrag',"list_width"=>"100","search"=>false,'list_align'=>'right'));

$list->setWhere("add_user='$USR' AND bron='bulkInvoer' $extraWhere ");//AND pagina='".$selectedPagina."'
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
if($_GET['sort']=='')
{
  $_GET['sort'][]='add_date';
  $_GET['direction'][] = "ASC";
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);
?>

<div class="main_content">

 <div class="row">
    <div class="formHolder box box12 {fieldsetClass}">
      <div class="formTitle textB"><?= vt('Bulkorders'); ?></div>
      <div class="formContent">
        <div class="form-actions clearB " id="saveForm">
          <div class="padded-5">
          <?
          if ( checkOrderAcces ('handmatigBulk_verwerken') === true )
          {
            echo "<div class=\"btn-new btn-default\" style=\"width:150px;margin-right:10px;\" ><a href='tijdelijkebulkordersv2Verwerken.php?checkOrders=1&setBulkFilter=1'>&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Verwerken') . "</a></div>";
            echo "<div class=\"btn-new btn-default\" style=\"width:150px;\" ><a href='ordersEditBulkV2.php?action=checkOrders&pagina=".$selectedPagina."'>&nbsp;&nbsp;<img src='icon/16/refresh.png' class='simbisIcon' /> " . vt('Pre validatie') . "</a></div>";
          }
          ?>
          </div>
        </div>



<form method="POST" name="selectForm">
<input type="hidden" name="verwerk" value="1">
<table class="list_tabel" cellspacing="0" style="margin-left:0px; width:100%;margin-bottom: 0;">

<?=$list->printHeader();?>
<?php


$n=1;
while($data = $list->getRow())
{
  /*
  $fonds=$data['fonds']['value'];

  if(!isset($fondsen[$fonds]))
  {
    $query="SELECT Fondseenheid,Valuta FROM Fondsen WHERE Fonds='".$fonds."'";
    $DB->SQL($query);
    $fondsData=$DB->lookupRecord();
    $fondsen[$fonds]['Fondseenheid']=$fondsData['Fondseenheid'];
    $fondsen[$fonds]['Valuta']=$fondsData['Valuta'];
  }

  if(!isset($fondskoersen[$fonds]))
  {
    $queryf = "SELECT koers FROM Fondskoersen WHERE Fonds = '".$fonds."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($queryf);
    $fondsKoers = $DB->lookupRecord();
    $fondskoersen[$fonds]=$fondsKoers['koers'];
  }

  if(!isset($valutakoersen[$fondsen[$fonds]['Valuta']]))
  {
    $queryf = "SELECT koers FROM Valutakoersen WHERE Valuta = '".$fondsen[$fonds]['Valuta']."' ORDER BY Datum DESC LIMIT 1";
    $DB->SQL($queryf);
    $valutaKoers = $DB->lookupRecord();
    $valutakoersen[$fondsen[$fonds]['Valuta']]=$valutaKoers['koers'];
  }

  $brutoBedragValuta=abs($data['aantal']['value'])*$fondsen[$fonds]['Fondseenheid']*$fondskoersen[$fonds];
  $brutoBedrag=$brutoBedragValuta*$valutakoersen[$fondsen[$fonds]['Valuta']];

  $data['bedrag']['value']=number_format($brutoBedrag,2,",",'.');
*/

//rvv

  if($_GET['action']=='checkOrders')
  {

    if($data['id']['value'] != "" && $data['portefeuille']['value'] !="")
    {
      if($extraLog)
        logIt("action:".$_GET['action']."| Portefeuille:".$data['portefeuille']['value']."| id: ".$data['id']['value']);

      $ordercheck = new orderControlleBerekeningV2(true);
      $check=$ordercheck->updateChecksByBulkorderregelId($data['id']['value'],$validateIdKeys[$data['id']['value']],1);
      $newCheck=$check['controleRegels'];

      foreach($newCheck as $checkNaam=>$checkData)
      {
        if($export['controleRegels'][$checkNaam]['checked']==1)
          $newCheck[$checkNaam]['checked']=1;
      }

      $export['controleRegels']=$newCheck;
    }
  }
  else
    $export['controleRegels']=unserialize($data['controleRegels']['value']);

  $title='';
  $data['tr_class']='';
  if(is_array($export['controleRegels']))
    $data['tr_class']="list_dataregel_groen";
  foreach($export['controleRegels'] as $check=>$checkData)
  {


    //$title.=$checkData['resultaat'];
    if($checkData['short'] > 0 || $checkData['checked']==1)
    {
      if($checkData['checked']==1)
        $status=" status: Checked";
      elseif($checkData['short'] > 0 )
        $status=' status: Foutmelding';
      else
        $status='';
      $title.=$checkData['naam']." $status "."\n".str_replace('<br>',"\n",$checkData['resultaat']."\n");
       $data['tr_class']="list_dataregel_geel";
     // $data[$checkVeld]['value'].="<input $checkboxChecked type=\"checkbox\" name=\"order_controle_checkbox_".$check."_".$data['id']['value']."\" value=\"".$data['id']['value']."\">";
    }


  }

  // $data['t']['value']=$n;
  if($delete)
    $data['delete']['value'] = "<a href=\"javascript:deleteRecord('".$data['id']['value']."');\">".maakKnop('delete.png',array('size'=>16,'tooltip'=>'Verwijder regel'))."</a>";

  $data['tr_title']=$title;
	echo $list->buildRow($data);
  $n++;
}
?>
</table>
</form>



<div class="form-actions clearB " id="saveForm">
  <div class="padded-5">
  <?
  if ( checkOrderAcces ('handmatigBulk_verwerken') === true )
  {//checkOrders=1&setBulkFilter=1
    echo "<div class=\"btn-new btn-default\" style=\"width:150px;margin-right:10px;\" ><a href='tijdelijkebulkordersv2List.php?resetFilter=1'>&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Verwerken') . "</a></div>";
    echo "<div class=\"btn-new btn-default\" style=\"width:150px;\" ><a href='ordersEditBulkV2.php?action=checkOrders&pagina=".$selectedPagina."'>&nbsp;&nbsp;<img src='icon/16/refresh.png' class='simbisIcon' /> " . vt('Pre validatie') . "</a></div>";
  }
  ?>
  </div>
</div>



</div>      </div></div></div></div></div>

<?php
//echo "<a href='tijdelijkebulkordersList.php?maakOrders=1&pagina=$selectedPagina&user=$selectedUser'>Verwerken</a>";
logAccess();
if($__debug)
{
	echo getdebuginfo();
}



echo template($__appvar["templateRefreshFooter"],$content);
?>