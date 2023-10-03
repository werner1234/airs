<?php
/*
    AE-ICT sourcemodule created 25 sep. 2020
    Author              : Chris van Santen
    Filename            : rekeningafschriften_v2_Edit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$data = array_merge($_POST, $_GET);

$AETemplate = new AE_template();
//debug($data);
$__funcvar['listurl']  = "rekeningafschriften_v2_List.php";
$__funcvar['location'] = "rekeningafschriften_v2_Edit.php";

$subHeader    = vt("Rekeningafschriften");
$mainHeader   = vt("muteren");

$object = new Rekeningafschriften_v2();

$temp = false;
if( ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 || checkAccess() ) && ( isset ($data['type']) && $data['type'] == 'temp') ) {
  $__funcvar['listurl']  = "rekeningafschriften_v2_List.php?type=temp";
  $__funcvar['location'] = "rekeningafschriften_v2_Edit.php?type=temp";
  $object = new VoorlopigeRekeningafschriften_v2 ();
  $temp = true;
}

$editObject = new editObject($object);
$editObject->formVars['voorlopigeRekeningmutaties'] = ($temp === true ? 'true' : 'false');


$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";
$editcontent['jsincludes'] .= $AETemplate->loadJs('jsrsClient');// "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
//$editcontent['body'] = "onLoad='document.editForm.Client.focus();'";
$editObject->template = $editcontent;
$editObject->includeHeaderInOutput = true;
// controlleer of het een memoriaal rekening is. Include dan een andere Template
//if($memoriaal)
//{
//	$selectMemoriaal = 1;
//	$template = "rekeningafschriften_v2_MemoriaalTemplate.html";
//}
//else
//{
	$selectMemoriaal = 0;
//	$template = "rekeningafschriften_v2_Template.html";
//}
$template = "rekeningafschriften_v2_Template.html";

$editObject->formTemplate = $template;
$editObject->usetemplate = true;

//$data = $_GET;
$action = $data['action'];

// Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Client


$DB = new DB();
if($action <> 'edit')
{
  $query = " SELECT DISTINCT(Client) FROM Portefeuilles,Rekeningen WHERE ".
  				 " Portefeuilles.Portefeuille = Rekeningen.Portefeuille ";
//          . "AND ".
//  				 " Rekeningen.Memoriaal = '".$selectMemoriaal."' ORDER BY Client           ";
  $DB->SQL($query);
  $DB->Query();
  $editObject->formVars['Client_options']	= "\n<option value=\"\">----------------</option>";
  while($clientdata = $DB->NextRecord())
  {
	  $editObject->formVars['Client_options']	.= "\n<option value=\"".$clientdata['Client']."\">".$clientdata['Client']."</option>";
  }
}

  $beperktToegankelijk = '';
  $joinPortefeuilles = '';
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') ";
  }
  else
  {
    $joinPortefeuilles="INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
    $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }

/*
 * Create an virtual autocomplete field
 */
//include_once("../classes/AutocompleteClass.php");
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Client');
$editObject->formVars['Client_options'] = $autocomplete->addVirtuelField('Client', array(
  'autocomplete' => array(
    'query' => "
      SELECT Client, Portefeuilles.Portefeuille, Portefeuille AS subPortefeuille
      FROM Portefeuilles
      LEFT JOIN `fixDepotbankenPerVermogensbeheerder` ON `Portefeuilles`.`Vermogensbeheerder` = `fixDepotbankenPerVermogensbeheerder`.`vermogensbeheerder`
      AND `Portefeuilles`.`depotbank` = `fixDepotbankenPerVermogensbeheerder`.`depotbank`
      ".$joinPortefeuilles."
      WHERE Portefeuilles.consolidatie=0 AND (Client LIKE '%{find}%' OR Portefeuilles.Portefeuille LIKE '%{find}%')
      AND (Portefeuilles.EindDatum >= now() OR Portefeuilles.EindDatum = '0000-00-00')
      AND (SELECT COUNT(*) FROM `Rekeningen` WHERE Portefeuille = Portefeuilles.Portefeuille AND inactief = 0) > 0
      ".$beperktToegankelijk."
      ORDER BY Client  ",
    'label' => array(
      'Client',
       'Portefeuille',
    ),
    'searchable' => array(
      'Client',
       'Portefeuille',
    ),
    'field_value' => array(
      'Client',
      'Portefeuille',
    ),
    'value'             => 'Client',
    'actions' => array(
      'select_addon' => '
        $("#Portefeuille").val(ui.item.data.Portefeuille);
        clientChanged();
      '
      
    )
  ),
  
));
$editObject->template['script_voet'] = $autocomplete->getAutoCompleteVirtuelFieldScript('Client');

$editObject->formVars['Rekening_options'] = "\n<option value=\"{Rekening_value}\" selected>{Rekening_value}</option>";

$editObject->controller($action,$data);

if ( $action === 'edit' && (int) $object->get('Verwerkt') === 1) {
  $object->setOption('Rekening', 'form_extra' ,'READONLY');
  $object->setOption('NieuwSaldo', 'form_extra' ,'READONLY');
}

if($action=='edit' || $action=='update')
{
  $query="SELECT Client FROM Portefeuilles Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE Rekeningen.Rekening='".$object->get('Rekening')."'";
  $DB->SQL($query);
  $clientData=$DB->lookupRecord();
  $editObject->formVars['Client_options'] = "\n<option value=\"".$clientData['Client']."\" selected>".$clientData['Client']."</option>";
  $editObject->formVars['Rekening_options'] = "\n<option value=\"".$object->get('Rekening')."\" selected>".$object->get('Rekening')."</option>";
  $object->setOption('Afschriftnummer', 'form_extra' ,'READONLY');
}
  $editObject->template['jsincludes'] .= $AETemplate->loadJs('rekeningmutaties_v2');

$editObject->formVars['type'] = ($temp === true ? 'temp' : '');
$editObject->template['style'] .= $AETemplate->loadCss('rekeningmutaties');
$editObject->template['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editObject->formVars['saveButton'] = '<button type="button" onclick="verzendFormulier();" class="btn-gray">'.drawButton('save').' ' . vt('Opslaan') . '</button>';

$editObject->template['javascript']=str_replace('//check values ?','if(checkDatum()==false){return false;}',$editObject->template['javascript']);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	if($data['action'] === 'update')
		header("Location: rekeningmutaties_v2_Edit.php?action=new" . ($temp === true ? '&type=temp' : '') . "&afschrift_id=".$object->get("id"));
	else
		header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>