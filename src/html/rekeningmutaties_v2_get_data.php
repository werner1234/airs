<?php
//get data
//$data = $_GET;
//$action = 'new';
$inst = new AIRS_invul_instructies();
$editObject->formVars["invulScr"] = $inst->getMessageDiv();



$__funcvar['listurl']  = "rekeningmutaties_v2_List.php";
$__funcvar['location'] = "rekeningmutaties_v2_Edit.php";


$data = array_merge($_GET, $_POST);

$useTable = 'Rekeningmutaties';
if ( isset ($data['type']) && $data['type'] == 'temp') {
  $useTable = 'VoorlopigeRekeningmutaties';
}

$action = ( ! isset ($data['action']) ? 'new' : $data['action'] );
$data['action'] = ( ! isset ($data['action']) ? 'new' : $data['action'] );
if ( ! isset($data['Fonds']) ){$data['Fonds'] = '';}

$AETemplate = new AE_template();
$AEDate = new AE_datum();
/**
 * ophalen gegevens
 */
$AERekeningmutaties = new AE_RekeningMutaties ();

$DB = new DB();


// valuta ophalen
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $gb['Valuta'];
}

if(isset($_GET['mutatieId']) && $_GET['mutatieId'] > 0)
{
  $DB=new DB();
  $query="SELECT Afschriftnummer,Rekening FROM $useTable WHERE id='".$_GET['mutatieId']."'";
  $DB->SQL($query);
  $mutatie=$DB->lookupRecord();
  $query="SELECT id FROM Rekeningafschriften WHERE Afschriftnummer='".$mutatie['Afschriftnummer']."' AND Rekening='".$mutatie['Rekening']."'";
  $DB->SQL($query);
  $afschrift=$DB->lookupRecord();
//echo $query;
  $_GET['afschrift_id']=$afschrift['id'];
  $_GET['action']='edit';//'new';
  $_GET['id']=$_GET['mutatieId'];
  //listarray($_GET);exit;
  //http://192.168.222.9/rvv/AIRS/html/rekeningmutatiesEdit.php?action=edit&mutatieId=3813527 uit mutatie lijst
  //http://192.168.222.9/rvv/AIRS/html/rekeningmutatiesEdit.php?action=new&afschrift_id=1198845
}



$afschrift = $AERekeningmutaties->getCopyData ((isset($_GET['afschrift_id']) && ! empty($_GET['afschrift_id']) ? $_GET['afschrift_id'] : null), ( isset ($data['type']) && $data['type'] === 'temp' ? 'VoorlopigeRekeningafschriften' : 'Rekeningafschriften') );

if ( ! empty ($afschrift) ) {
  $editObject->formVars = array_merge($editObject->formVars, $afschrift);
}else{
  $object->data['fields']["Memoriaalboeking"]["value"] = 1;
}


// fondsen ophalen
$DB->SQL("SELECT Bewaarder FROM Bewaarders ORDER BY Bewaarder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Bewaarder"]["form_options"][] = $gb['Bewaarder'];
}

//VoorlopigeRekeningafschriften
if($object->get('Rekening') <> '')
{
	$query = "SELECT Vermogensbeheerder FROM Portefeuilles JOIN Rekeningen ON Portefeuilles.Portefeuille=Rekeningen.Portefeuille WHERE Rekeningen.Rekening='" . $object->get('Rekening') . "'";
}
else
{
	if($data['type']=='temp')
		$prefix="Voorlopige";
	else
		$prefix='';
	$query = "SELECT Vermogensbeheerder FROM Portefeuilles JOIN Rekeningen ON Portefeuilles.Portefeuille=Rekeningen.Portefeuille JOIN ".$prefix."Rekeningafschriften ON ".$prefix."Rekeningafschriften.Rekening=Rekeningen.Rekening WHERE ".$prefix."Rekeningafschriften.id='" . $data['afschrift_id'] . "'";
}
$DB->SQL($query);
$vermogensbeheerder=$DB->lookupRecord();

$query="SELECT waarde as Grootboekrekening FROM KeuzePerVermogensbeheerder WHERE categorie='Grootboekrekeningen' AND vermogensbeheerder='".$vermogensbeheerder['Vermogensbeheerder']."'";
$DB->SQL($query);
$DB->Query();
$grootboekrekeningen=array();
if($DB->records()>0)
{
	while ($gb = $DB->NextRecord())
	{
		$grootboekrekeningen[] = $gb['Grootboekrekening'];
	}
}
else
{
	$DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening");
	$DB->Query();
	while ($gb = $DB->NextRecord())
	{
		$grootboekrekeningen[] = $gb['Grootboekrekening'];
	}
}

// laatstvolgnr
if($action == "new")
{
	$DB->SQL("SELECT $useTable.Valuta, $useTable.Boekdatum, $useTable.Omschrijving, (Volgnummer+1) AS Volgende,
  $useTable.Fonds,Rekeningen.Valuta as ValutaRekening 
  FROM $useTable     
  INNER JOIN Rekeningen ON $useTable.Rekening = Rekeningen.Rekening
  WHERE Afschriftnummer = '".$afschrift["Afschriftnummer"]."' AND $useTable.Rekening = '".$afschrift["Rekening"]."' 
  ORDER BY Volgnummer DESC LIMIT 1");
	$DB->Query();
	if($DB->Records() > 0)
	{
    
		$volgnr = $DB->NextRecord();
		$object->data['fields']['Volgnummer']['value'] 		= $volgnr['Volgende'];
		$object->data['fields']['Boekdatum']['value'] 		= $volgnr['Boekdatum'];
//		$object->data['fields']['Omschrijving']['value'] 	= $volgnr['Omschrijving'];
    $object->data['fields']['Omschrijving']['value'] = '';
		$object->data['fields']['Valuta']['value']      	= $volgnr['ValutaRekening'];
    $object->data['fields']['Fonds']['value'] 	    	= $volgnr['Fonds'];

	}
	else
	{
		$object->data['fields']['Volgnummer']['value'] 	= 1;
		$object->data['fields']['Boekdatum']['value'] 	= $afschrift["Datum"];
		$object->data['fields']['Valuta']['value']  = $afschrift["Valuta"];
	}
  
  $editObject->formVars['Volgnummer'] = $object->data['fields']['Volgnummer']['value'];

	$object->data['fields']['Afschriftnummer']['value'] = $afschrift["Afschriftnummer"];
	$object->data['fields']['Rekening']['value'] 				= $afschrift["Rekening"];
}
else
{
	// haal koerseenheid op!
	$DB->SQL("SELECT Fondsen.Fondseenheid FROM Fondsen LEFT JOIN $useTable ON $useTable.Fonds = Fondsen.Fonds WHERE $useTable.id = '".$_GET['id']."'");
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


$editObject->formVars['opslaanButton']='<input type="submit" value="opslaan"> (F10)';
$editObject->formVars['opslaanButton']=vt('Opslaan');

  if( $mutationType != 'temp' && ! checkAccess() )
  {
    $editObject->formVars['opslaanButton']='';
    $query="SELECT Gebruikers.mutatiesAanleveren FROM Gebruikers WHERE Gebruikers.Gebruiker='$USR' ";
    $DB->SQL($query);
   	$DB->Query();
  	$aanleveren = $DB->NextRecord();
  	if($aanleveren['mutatiesAanleveren']==2) //Alleen rechten om internedepot mutaties aan te leveren.
  	{
  	  $query="SELECT Portefeuilles.InternDepot, VermogensbeheerdersPerGebruiker.Gebruiker, Portefeuilles.Portefeuille, Rekeningen.Rekening FROM
              Portefeuilles
              Inner Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
              Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
              WHERE Rekeningen.Rekening='".$data['Rekening']."' AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  	  $DB->SQL($query);
     	$DB->Query();
  	  $intern = $DB->NextRecord();

  	  if($intern['InternDepot']==1) {
  	    $editObject->formVars['opslaanButton']='<input type="submit" value="Mutaties aan Airs verzenden"> (F10)';
        $editObject->formVars['opslaanButton']=vt('Mutaties aan Airs verzenden');
      }
  	  if($action=='update' && $intern['InternDepot'] != 1)
        $action='edit';
  	}
  	elseif( (int) $aanleveren['mutatiesAanleveren'] === 1 )  // Rechten om alle mutaties aan te leveren
  	{
  	  if($action != 'update') {
  	    $editObject->formVars['opslaanButton']='<input type="submit" value="Mutaties aan Airs verzenden"> (F10)';
        $editObject->formVars['opslaanButton']=vt('Mutaties aan Airs verzenden');
      }
  	}
  	else
  	{
  	  $editObject->formVars['opslaanButton']='';
  	  if($action=='update')
        $action='edit';
   	}

   	if($action=='update')
   	{
  	  $query="SELECT Portefeuilles.Vermogensbeheerder FROM Portefeuilles
              Inner Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
              Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
              WHERE Rekeningen.Rekening='".$data['Rekening']."' AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
  	  $DB->SQL($query);
     	$DB->Query();
  	  $vermogensbeheerder = $DB->NextRecord();
   	  $editObject->verzendVermogensbeheerder=$vermogensbeheerder['Vermogensbeheerder'];
   	}
  }

  $editObject->formVars['message']= ( isset($_GET['message']) ? urldecode($_GET['message']) : '');

$editObject->controller($action,$data);

$einddatum = "now()";
if($object->get('Boekdatum') !== '') {
  $einddatum = "'".$object->get('Boekdatum')."'";
}

$orFondsen = '';
if($object->get('Fonds') !== '') {
  $orFondsen = " OR Fondsen.Fonds='".$object->get('Fonds')."'";
}

/**
 * end ophalen gegevens
 */

/** Portefeuille ophalen die bij de rekening hoort */
$query="SELECT Portefeuilles.Portefeuille FROM Portefeuilles    
              Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
              WHERE Rekeningen.Rekening='" . mysql_real_escape_string($afschrift["Rekening"]) . "' ";

$DB->SQL($query);
$DB->Query();
while($gb = $DB->NextRecord())
{
	$portefeuilleData[] = $gb['Portefeuille'];
}


/** Portefeuille ophalen die bij de rekening hoort ivm startdatum */
$query="SELECT Portefeuilles.startdatum FROM Portefeuilles
              Inner Join Rekeningen ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
              WHERE Rekeningen.Rekening='" . mysql_real_escape_string($afschrift["Rekening"]) . "'
               AND Portefeuilles.consolidatie = 0";

$DB->SQL($query);
$DB->Query();
$gb = $DB->NextRecord();
$boekDatumCheckDate = '';
if ( ! empty ($gb['startdatum']) ) {
  $boekDatumCheckDate = date('d-m-Y', strtotime($gb['startdatum']));
}
$editObject->formVars['boekDatumCheckDate'] = $boekDatumCheckDate;

/** set button **/
if ( ! empty ($editObject->formVars['opslaanButton']) ) {
  $editObject->formVars['btn_submit'] = ' <button id="submit-form" type="submit" class="btn btn-gray" value="opslaan">' . maakKnop('disk_blue.png') . ' ' . $editObject->formVars['opslaanButton'] . '</button>';
}

if ( $mutationType == 'temp' ) {
  if ( isset ($afschrift['Verwerkt']) && $afschrift['Verwerkt'] > 0 ) {
    $editObject->formVars['btn_submit'] = '<span><strong>' . vt('Mutatie is al verwerkt') . '</strong></span>';
  }
}


$editObject->formVars['type'] = $data['type'];

$inst->getBeheerderViaRekening($afschrift['Rekening']);
$editObject->formVars["VB"] = $inst->vermogensBeheerder;
$editObject->formVars['consoleLog'] = checkAccess();

/** Load script files **/
$editcontent['jsincludes'] .= $AETemplate->loadJs('jsrsClient');
$editcontent['jsincludes'] .= $AETemplate->loadJs('bootstrapTooltip');
$editcontent['jsincludes'] .= $AETemplate->loadJs('jquery-input-mask');
$editcontent['jsincludes'] .= $AETemplate->loadJs('rekeningmutaties_v2');
$editcontent['script_voet'] = $AETemplate->parseFile('/javascript/jquery-input-mask-masks.js');

$editcontent['style'] .= $AETemplate->loadCss('jquery.webui-popover');
$editcontent['jsincludes'] .= $AETemplate->loadJs('bootstrapTooltip');
$editcontent['jsincludes'] .= $AETemplate->loadJs('AE-jqueryPluginInvulinstructie');


$object->set('Bedrag', '');

