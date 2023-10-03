<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/10/17 15:36:45 $
    File Versie         : $Revision: 1.22 $

    $Log: rekeningenEdit.php,v $
    Revision 1.22  2018/10/17 15:36:45  rvv
    *** empty log message ***
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

if($_GET['action']=='kopieerRekeningparameters')
{
  include('rekeningenhistorischeparametersEdit.php');
  exit;
}

// let op $rekeningTypes staat als kopie is classes/rekeningAddStamgegevens.php
// bij mutaties ook die array aanpassen!!
//$rekeningTypes = array(
//  "cash"        => vt("Standaard geldrekening"),
//  "AABBELSP"    => vt("AAB Beleggersspaarrek."),
//  "AABONDDEP"   => vt("AAB Ondernemersdep."),
//  "AABORR"      => vt("AAB Optimale Renterekening"),
//  "AABPBS"      => vt("AAB Priv Banking Spaarrek."),
//  "AABSPAAR"    => vt("AAB Spaarrekening"),
//  "AABVERM"     => vt("AAB Vermogens Spaarrek."),
//  "AABzakenrek" => vt("AAB Zakenrekening"),
//  "AABMPPRC"    => vt("AAB MeesP Part Rek Crt"),
//  "AABMPPBS"    => vt("AAB MeesP Private Banking Spaarrek"),
//  "AABMP653"    => vt("AAB MeesP 24.91.18.653 F BV"),
//  "AABMP928"    => vt("AAB MeesP 24.93.36.928 F BV"),
//  "AABMP956"    => vt("AAB MeesP 56.12.89.956 F BV"),
//  "AABMP483"    => vt("AAB MeesP 56.34.74.483 F BV"),
//  "AABMP965"    => vt("AAB MeesP Beleggingsrek pl 25.15.47.965"),
//  "AABMP379"    => vt("AAB MP Vermogensbeheer 25.38.13.379"),
//  "AABMP082"    => vt("AAB MP PB Spaarrekening 25.38.28.082"),
//  "AAB619"      => vt("AAB RC EUR 40.95.80.619"),
//  "MARGIN"      => vt("Margin rekening"),
//  "VLER"        => vt("Van Lanschot Effectenrekening")
//);
// call 10503
$rekeningTypes = array(
  "cash"        => vt("Standaard geldrekening"),
  "AABBELSP"    => vt("AAB Beleggersspaarrek."),
  "AABONDDEP"   => vt("AAB Ondernemersdep."),
  "AABORR"      => vt("AAB Optimale Renterekening"),
  "AABSPAAR"    => vt("AAB Spaarrekening"),
  "AABVERM"     => vt("AAB Vermogens Spaarrek."),
  "AABzakenrek" => vt("AAB Zakenrekening"),
  "AABMPPRC"    => vt("AAB MeesP Part Rek Crt"),
  "AABPBS"      => vt("AAB Priv Banking Spaarrek."),
  "AABMPPBS"    => vt("AAB MeesP Private Banking Spaarrek"),
  "MARGIN"      => vt("Margin rekening"),
  "VLER"        => vt("Van Lanschot Effectenrekening"),
  "KIR"         => vt("KIR-rekening"),
  "ZICHT"       => vt("Zicht-rekening"),
);

$subHeader  = vt("rekening");
$mainHeader = vt("muteren");
$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$__funcvar['listurl']  = "rekeningenList.php";
$__funcvar['location'] = "rekeningenEdit.php";

$object = new Rekeningen();

$editcontent['jsincludes'].="\n<script type=\"text/javascript\" src=\"javascript/iban.js\"></script>\n";

$editcontent['javascript'] .= '
function showRente()
{
  if (document.getElementById(\'rente\').src == "blank.html" || document.getElementById(\'RenteBerekenen\').checked == true)
  {
  document.getElementById(\'rente\').src = "depositorentepercentagesList.php?rekening="+document.getElementById(\'Rekening\').value;
  document.getElementById(\'RenteField\').style.visibility="visible";
  }
  else
  {
  document.getElementById(\'rente\').src = "blank.html";
  document.getElementById(\'RenteField\').style.visibility="hidden";
  }
}

function initRente()
{
  if (document.getElementById(\'RenteBerekenen\').checked == false)
  {
  document.getElementById(\'RenteField\').style.visibility="hidden";

  }
  else
  {
    document.getElementById(\'RenteField\').style.visibility="visible";
  }

}

function checkIban()
{
  if(IBAN.isValid($("#IBANnr").val())==false)
  {
   $("#IBANnr").css( "background-color","#FFCCCC" );
  }
  else
  {
   $("#IBANnr").css( "background-color","#CCFFCC" );
  }
}
';

$editcontent['body'] 				= " onLoad=\"javascript:initRente();\" ";

//
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;




$editObject->formTemplate = "RekeningenTemplate.html";
$editObject->usetemplate = true;

$data = $_GET;
$action = $data["action"];

// Vermogensbeheerder ophalen
$DB = new DB();
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $gb['Valuta'];
}


$object->data['fields']["typeRekening"]["form_options"] = $rekeningTypes;
$object->data['fields']["Portefeuille"]["form_type"] = "TEXT";

//call 3856
$ajx = new AE_cls_ajaxLookup("portefeuille");
$ajx->changeModuleTriggerID("portefeuille","Portefeuille");

if ($data['RenteBerekenen'] == '0')
{
  $data['rentemethodiek'] = 0;
}

$editObject->controller($action,$data);

if ($object->get('rentemethodiek') == 1 && $object->get('RenteBerekenen') == 0)
{
  $object->set('rentemethodiek', 0);
}

if($object->get("Portefeuille") <> '')
{
  $object->setOption('Beleggingscategorie', 'select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
AND Portefeuilles.Portefeuille='" . $object->get("Portefeuille") . "'");
  
  $object->setOption('AttributieCategorie', 'select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.categorie='AttributieCategorien'
AND Portefeuilles.Portefeuille='" . $object->get("Portefeuille") . "'");
  
}

if ($object->get('RenteBerekenen') == 1)
{
  $editObject->formVars["iframe"] = "depositorentepercentagesList.php?rekening=" . $object->get('Rekening');
}
else
{
  $editObject->formVars["iframe"] = "blank.html";
}


$editObject->formVars['rekeningparameterHistorie'] =
  '<div class="btn btn-gray" onclick="document.editForm.action.value=\'kopieerRekeningparameters\';submitForm();" >'.vt("Kopieer naar rekeningparameter historie").'.</div>';
$query="SELECT max(GebruikTot) as laatsteDatum, count(id) as aantal FROM RekeningenHistorischeParameters WHERE Rekening='".mysql_real_escape_string($object->get('Rekening'))."'";
$DB->SQL($query);
$stats=$DB->lookupRecord();
$editObject->formVars['rekeningparameterHistorie'] .= " ".vt("Laatste record").": ".$stats['laatsteDatum'].", ".vt("Aantal records").":(".$stats['aantal'].")";

if($data['frame']==1)
{
  //foreach($object->data['fields'] as $fieldname=>$dat)
  //  echo "'$fieldname'".',';
  $disableFields=array('Memoriaal','Termijnrekening','RenteBerekenen','Rente30_360','Deposito');
  $readonlyFields=array('Rekening','Portefeuille','Valuta','RekeningDepotbank','Inleg','Depotbank','typeRekening');
  foreach($readonlyFields as $field)
  {
    $object->setOption($field, 'form_type', 'text');
    $object->setOption($field, 'form_extra', 'READONLY');
  }
  foreach($disableFields as $field)
  {
    $object->setOption($field, 'beperkt', true);
  }
  $object->data['fields']['Rekening']['key_field']=false;

  if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0  )
  {
    $editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
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
  $con=new AIRS_consolidatie();
  //$VPs=$con->ophalenVPsViaRekening($object->get('Rekening'));
  //if(count($VPs)>0)
   $con->bijwerkenConsolidaties();
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
