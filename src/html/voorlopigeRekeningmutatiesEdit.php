<?php
/*
    AE-ICT sourcemodule created 25 sep. 2020
    Author              : Chris van Santen
    Filename            : voorlopigeRekeningmutatiesEdit.php


*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader    = vt("voorlopige Rekeningmutaties");
$mainHeader   = vt("muteren");

$__funcvar["listurl"]  = "voorlopigeRekeningmutatiesList.php";
$__funcvar["location"] = "voorlopigeRekeningmutatiesEdit.php";

$object = new VoorlopigeRekeningmutaties();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editObject->formVars['opslaanButton']='<input type="submit" value="'.vt("opslaan").'"> (F10)';

$DB = new DB();
if($_GET['action'] == 'opheffen')
{
  $_GET['action']='update';
  $DB->SQL("SELECT SUM(ROUND(Bedrag,2)) AS Totaal FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '".$_GET["Afschriftnummer"]."' AND Rekening = '".$_GET["Rekening"]."'");
  $DB->Query();
  $totaal = $DB->NextRecord();
  $mutatie=round($totaal['Totaal'],2);
  $DB->SQL("UPDATE VoorlopigeRekeningafschriften SET NieuwSaldo=ROUND(Saldo+$mutatie,2),change_date=NOW(),change_user='$USR' WHERE VoorlopigeRekeningafschriften.id = '".$_GET['afschrift_id']."'");
  $DB->Query();
}

$data = $_GET;
$action = $data["action"];

if(!isset($data['Fonds']))
  $data['Fonds']='';

if($action == 'delete')
{
  $query="SELECT id FROM VoorlopigeRekeningmutaties WHERE VoorlopigeRekeningmutaties.Afschriftnummer = '".$_GET['Afschriftnummer']."'";
  $db = new DB;
  $db->SQL($query);
  $aantal = $db->QRecords($query);
  if($aantal == 0)
  {
    $query = "DELETE FROM VoorlopigeRekeningafschriften WHERE id = '".$_GET['afschrift_id']."'";
    $db->SQL($query);
    if($db->Query())
      header("Location: voorlopigeRekeningafschriftenList.php");
    exit;
  }
}

// grootboekgegevens ophalen
$DB = new DB();
$DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Grootboekrekening"]["form_options"][] = $gb["Grootboekrekening"];
}

// als Fonds bekend is ,
// transactietypen ophalen
$DB->SQL("SELECT Transactietype FROM Transactietypes ORDER BY Transactietype");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Transactietype"]["form_options"][] = $gb["Transactietype"];
}

// valuta ophalen
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $gb["Valuta"];
}

// fondsen ophalen
$DB->SQL("SELECT Bewaarder FROM Bewaarders ORDER BY Bewaarder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Bewaarder"]["form_options"][] = $gb['Bewaarder'];
}
if(!$afschrift_id)
  $afschrift_id=$_GET['afschrift_id'];
// afschriftgegevens ophalen
$DB->SQL("SELECT Rekeningen.Valuta, Rekeningen.Memoriaal, VoorlopigeRekeningafschriften.* FROM VoorlopigeRekeningafschriften, Rekeningen WHERE VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening AND VoorlopigeRekeningafschriften.id = '".$afschrift_id."'");
$DB->Query();
$afschrift = $DB->NextRecord();

$afschrift["Saldo"] = round($afschrift["Saldo"],2);
$afschrift["NieuwSaldo"] = round($afschrift["NieuwSaldo"],2);

$editObject->formVars['aRekening'] = $afschrift["Rekening"];
$editObject->formVars['aValuta'] = $afschrift["Valuta"];
$editObject->formVars['aDatum'] = jul2form(db2jul($afschrift["Datum"]));
$editObject->formVars['aAfschriftnummer'] = $afschrift["Afschriftnummer"];
$editObject->formVars['aAfschriftnummer'] = $afschrift["Afschriftnummer"];
$editObject->formVars['listurl'] = $__funcvar['listurl'];
$editObject->formVars['rekeningafschriftenEdit'] = 'voorlopigeRekeningafschriftenEdit.php';


if(!$afschrift['Memoriaal'])
{
	$editObject->formVars['txtSaldo']       = vt("Saldo");
	$editObject->formVars['txtNieuwSaldo']  = vt("Nieuw Saldo");
	$editObject->formVars['txtNieuwSaldo']  = vt("Nieuw Saldo");

	$editObject->formVars['aSaldo']         = $afschrift["Saldo"];
	$editObject->formVars['aNieuwSaldo']    = $afschrift["NieuwSaldo"];
}
else
{
  $object->data['fields']["Memoriaalboeking"]["value"] = 1;
}
$editObject->formVars['aMemoriaal'] = $afschrift['Memoriaal'];
$editObject->formVars['aId']        = $afschrift["id"];
$editObject->formVars['aTotaal']    = round(($afschrift["NieuwSaldo"] -$afschrift["Saldo"]),2);

// Haal totaal mutaties op
$DB->SQL("SELECT SUM(ROUND(Bedrag,2)) AS Totaal FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '".$afschrift["Afschriftnummer"]."' AND Rekening = '".$afschrift["Rekening"]."'");
$DB->Query();
$totaal = $DB->NextRecord();
// Reken mutatieverschil uit
$editObject->formVars['mutatieVerschil'] = $editObject->formVars['aTotaal'] - round($totaal["Totaal"],2);
// Zet Fieldset Class voor mutatie veschil
if($editObject->formVars['mutatieVerschil'] <> 0)
	$editObject->formVars['fieldsetClass'] = "rekeningmutatie_verschil";
else
	$editObject->formVars['fieldsetClass'] = "rekeningmutatie_geenverschil";


// laatstvolgnr
if($action == "new")
{
	$DB->SQL("SELECT Valuta, Boekdatum, Omschrijving, (Volgnummer+1) AS Volgende FROM VoorlopigeRekeningmutaties WHERE Afschriftnummer = '".$afschrift["Afschriftnummer"]."' AND Rekening = '".$afschrift["Rekening"]."' ORDER BY Volgnummer DESC LIMIT 1");
	$DB->Query();
	if($DB->Records() > 0)
	{
		$volgnr = $DB->NextRecord();
		$object->data['fields']['Volgnummer']['value'] 		= $volgnr["Volgende"];
		$object->data['fields']['Boekdatum']['value'] 		= $volgnr["Boekdatum"];
		$object->data['fields']['Omschrijving']['value'] 	= $volgnr["Omschrijving"];
		$object->data['fields']['Valuta']['value'] 	      = $volgnr["Valuta"];

	}
	else
	{
		$object->data['fields']['Volgnummer']['value'] 	= 1;
		$object->data['fields']['Boekdatum']['value'] 	= $afschrift["Datum"];
		$object->data['fields']['Valuta']['value']      = $afschrift["Valuta"];
	}

	$object->data['fields']['Afschriftnummer']['value'] = $afschrift["Afschriftnummer"];
	$object->data['fields']['Rekening']['value'] 				= $afschrift["Rekening"];
}
else
{
	// haal koerseenheid op!
	$DB->SQL("SELECT Fondsen.Fondseenheid FROM Fondsen LEFT JOIN VoorlopigeRekeningmutaties ON VoorlopigeRekeningmutaties.Fonds = Fondsen.Fonds WHERE VoorlopigeRekeningmutaties.id = '".$id."'");
	$DB->Query();
	$eenheid = $DB->NextRecord();
	$editObject->formVars['koerseenheid'] = $eenheid['Fondseenheid'];

	// controlleer of veld Credit is gevuld, maak dan aantal negatief.
	if($data["Credit"] <> 0)
	{
		if($data["Aantal"] > 0)
		{
			$data["Aantal"] = $data["Aantal"] * -1;
		}
	}
}

$editObject->controller($action,$data);


if($object->get('Boekdatum') <> '')
  $einddatum="'".$object->get('Boekdatum')."'";
else
  $einddatum="now()";
$orFondsen='';
if($object->get('Fonds') <> '')
  $orFondsen=" OR Fondsen.Fonds='".$object->get('Fonds')."'";

// fondsen ophalen
$DB->SQL("SELECT Fonds,ISINCode FROM Fondsen WHERE EindDatum > $einddatum $orFondsen OR  EindDatum = '0000-00-00' ORDER BY Fonds");//einddatum > now
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Fonds"]["form_options"][$gb['Fonds']] = $gb['Fonds']." - ".$gb['ISINCode'] ;
}

$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","editForm");
$koppelObject[0]->addFields("Fonds","Fonds",false,true);
$koppelObject[0]->addFields("Valuta","Valuta",false,true);
$koppelObject[0]->addFields("ISINCode","",true,true);
$koppelObject[0]->addFields("Omschrijving","",true,true);
$koppelObject[0]->name = "fonds";
$koppelObject[0]->focus = 'Fonds';
$koppelObject[0]->extraQuery = " AND (EindDatum > $einddatum $orFondsen OR EindDatum = '0000-00-00') ";


$editcontent['jsincludes'] = "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}\n";
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();

$editcontent["body"] = "onLoad='doOnload();'";
$editObject->template = $editcontent;

$editObject->formTemplate = "rekeningmutatiesTemplate.html";
$editObject->usetemplate = true;

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: voorlopigeRekeningmutatiesEdit.php?action=new&afschrift_id=".$afschrift["id"]);
	//header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
