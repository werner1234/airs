<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "rekeningmutatiesList.php";
$__funcvar['location'] = "rekeningmutatiesEdit.php";

if(isset($_GET['mutatieId']) && $_GET['mutatieId'] > 0)
{
  $DB=new DB();
  $query="SELECT Afschriftnummer,Rekening FROM Rekeningmutaties WHERE id='".$_GET['mutatieId']."'";
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

$object = new Rekeningmutaties();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$DB = new DB();
if($_GET['action'] == 'opheffen')
{
  $_GET['action']='update';
  $DB->SQL("SELECT SUM(ROUND(Bedrag,2)) AS Totaal FROM Rekeningmutaties WHERE Afschriftnummer = '".$_GET["Afschriftnummer"]."' AND Rekening = '".$_GET["Rekening"]."'");
  $DB->Query();
  $totaal = $DB->NextRecord();
  $mutatie=round($totaal['Totaal'],2);
  $DB->SQL("UPDATE Rekeningafschriften SET NieuwSaldo=ROUND(Saldo+$mutatie,2),change_date=NOW(),change_user='$USR' WHERE Rekeningafschriften.id = '".$_GET['afschrift_id']."'");
  $DB->Query();
}
$data = $_GET;
$action = $data['action'];
if(!isset($data['Fonds']))
  $data['Fonds']='';

// grootboekgegevens ophalen

$DB->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekening");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Grootboekrekening"]["form_options"][] = $gb['Grootboekrekening'];
}

// als Fonds bekend is ,
// transactietypen ophalen
$DB->SQL("SELECT Transactietype FROM Transactietypes ORDER BY Transactietype");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Transactietype"]["form_options"][] = $gb['Transactietype'];
}


// valuta ophalen
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Valuta"]["form_options"][] = $gb['Valuta'];
}

// fondsen ophalen
$DB->SQL("SELECT Bewaarder FROM Bewaarders ORDER BY Bewaarder");
$DB->Query();
while($gb = $DB->NextRecord())
{
	$object->data['fields']["Bewaarder"]["form_options"][] = $gb['Bewaarder'];
}

// afschriftgegevens ophalen
$DB->SQL("SELECT Rekeningen.Valuta, Rekeningen.Memoriaal, Rekeningafschriften.* FROM Rekeningafschriften, Rekeningen WHERE Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningafschriften.id = '".$_GET['afschrift_id']."'");
$DB->Query();
$afschrift = $DB->NextRecord();

$afschrift["Saldo"] = round($afschrift["Saldo"],2);
$afschrift["NieuwSaldo"] = round($afschrift["NieuwSaldo"],2);

$editObject->formVars['aRekening'] = $afschrift["Rekening"];
$editObject->formVars['aValuta'] = $afschrift["Valuta"];
$editObject->formVars['aDatum'] = jul2form(db2jul($afschrift["Datum"]));
$editObject->formVars['aAfschriftnummer'] = $afschrift["Afschriftnummer"];

if(!$afschrift['Memoriaal'])
{
	$editObject->formVars['txtSaldo'] = "Saldo";
	$editObject->formVars['txtNieuwSaldo'] = "Nieuw Saldo";
	$editObject->formVars['aSaldo'] = $afschrift["Saldo"];
	$editObject->formVars['aNieuwSaldo'] = $afschrift["NieuwSaldo"];
}
else
{
  $object->data['fields']["Memoriaalboeking"]["value"] = 1;
}
$editObject->formVars['aMemoriaal'] = $afschrift['Memoriaal'];
$editObject->formVars['aId'] = $afschrift["id"];
$editObject->formVars['aTotaal'] = round(($afschrift["NieuwSaldo"] -$afschrift["Saldo"]),2);
$editObject->formVars['listurl'] = $__funcvar['listurl'];
$editObject->formVars['rekeningafschriftenEdit'] = 'rekeningafschriftenEdit.php';

// Haal totaal mutaties op
$DB->SQL("SELECT SUM(ROUND(Bedrag,2)) AS Totaal FROM Rekeningmutaties WHERE Afschriftnummer = '".$afschrift["Afschriftnummer"]."' AND Rekening = '".$afschrift["Rekening"]."'");
$DB->Query();
$totaal = $DB->NextRecord();
// Reken mutatieverschil uit
$editObject->formVars['mutatieVerschil'] = $editObject->formVars['aTotaal'] - round($totaal['Totaal'],2);
// Zet Fieldset Class voor mutatie veschil
if($editObject->formVars['mutatieVerschil'] <> 0)
	$editObject->formVars['fieldsetClass'] = "rekeningmutatie_verschil";
else
	$editObject->formVars['fieldsetClass'] = "rekeningmutatie_geenverschil";


$query="SELECT
Bedrijfsgegevens.vastzetdatumRapportages,
Rekeningen.Rekening,
Bedrijfsgegevens.Bedrijf,
VermogensbeheerdersPerBedrijf.Vermogensbeheerder
FROM
Bedrijfsgegevens
INNER JOIN VermogensbeheerdersPerBedrijf ON Bedrijfsgegevens.Bedrijf = VermogensbeheerdersPerBedrijf.Bedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
INNER JOIN Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille
WHERE Rekeningen.Rekening='".mysql_real_escape_string($afschrift["Rekening"])."'";
$DB->SQL($query);
$DB->Query();
$vastzetdatumRapportages = $DB->NextRecord();

if($vastzetdatumRapportages['vastzetdatumRapportages'] <> '0000-00-00' && $vastzetdatumRapportages['vastzetdatumRapportages'] <> '')
{
$vastzetParts=explode("-",$vastzetdatumRapportages['vastzetdatumRapportages']);
$editObject->formVars['blockdate']='
  try
  {
    var boekdatum=document.editForm.Boekdatum.value;
    var datumParts=boekdatum.split(\'-\');
    var boekdate=new Date(datumParts[2],(datumParts[1]-1),datumParts[0]);
    var blockdate=new Date('.$vastzetParts[0].',('.$vastzetParts[1].'-1),'.$vastzetParts[2].');
    if (blockdate >= boekdate)
    {
      ret=confirm("De boekdatum ligt voor de vastzet datum ('.$vastzetdatumRapportages['vastzetdatumRapportages'].') wilt u doorgaan?");
    }
  }
  catch(err) { }  
  ';
}

// laatstvolgnr
if($action == "new")
{
	$DB->SQL("SELECT Rekeningmutaties.Valuta, Rekeningmutaties.Boekdatum, Rekeningmutaties.Omschrijving, (Volgnummer+1) AS Volgende,
  Rekeningmutaties.Fonds,Rekeningen.Valuta as ValutaRekening 
  FROM Rekeningmutaties     
  INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
  WHERE Afschriftnummer = '".$afschrift["Afschriftnummer"]."' AND Rekeningmutaties.Rekening = '".$afschrift["Rekening"]."' 
  ORDER BY Volgnummer DESC LIMIT 1");
	$DB->Query();
	if($DB->Records() > 0)
	{
		$volgnr = $DB->NextRecord();
		$object->data['fields']['Volgnummer']['value'] 		= $volgnr['Volgende'];
		$object->data['fields']['Boekdatum']['value'] 		= $volgnr['Boekdatum'];
		$object->data['fields']['Omschrijving']['value'] 	= $volgnr['Omschrijving'];
		$object->data['fields']['Valuta']['value']      	= $volgnr['ValutaRekening'];
    $object->data['fields']['Fonds']['value'] 	    	= $volgnr['Fonds'];

	}
	else
	{
		$object->data['fields']['Volgnummer']['value'] 	= 1;
		$object->data['fields']['Boekdatum']['value'] 	= $afschrift["Datum"];
		$object->data['fields']['Valuta']['value']  = $afschrift["Valuta"];
	}

	$object->data['fields']['Afschriftnummer']['value'] = $afschrift["Afschriftnummer"];
	$object->data['fields']['Rekening']['value'] 				= $afschrift["Rekening"];
}
else
{
	// haal koerseenheid op!
	$DB->SQL("SELECT Fondsen.Fondseenheid FROM Fondsen LEFT JOIN Rekeningmutaties ON Rekeningmutaties.Fonds = Fondsen.Fonds WHERE Rekeningmutaties.id = '".$_GET['id']."'");
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


  if(!checkAccess())
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

  	  if($intern['InternDepot']==1)
  	    $editObject->formVars['opslaanButton']='<input type="submit" value="Mutaties aan Airs verzenden"> (F10)';
  	  if($action=='update' && $intern['InternDepot'] != 1)
        $action='edit';
  	}
  	elseif($aanleveren['mutatiesAanleveren']==1)  // Rechten om alle mutaties aan te leveren
  	{
  	  if($action != 'update')
  	    $editObject->formVars['opslaanButton']='<input type="submit" value="Mutaties aan Airs verzenden"> (F10)';
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

  $editObject->formVars['message']=urldecode($_GET['message']);
  

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
$koppelObject[0]->focus='Fonds';
$koppelObject[0]->extraQuery = " AND (EindDatum > $einddatum $orFondsen OR EindDatum = '0000-00-00') ";

//$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>";
$editcontent['jsincludes'] = "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/rekeningAfschriften.js\" type=text/javascript></script>\n";
//$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/popup.js\" type=text/javascript></script>\n";
$editcontent['eigenFocus']="if(document.getElementById('Volgnummer')){try{document.getElementById('Volgnummer').focus(); break;} catch(err) { }}";
$editcontent['javascript'] = "
function submitForm()
{
        if(checkValues())
         {
           document.editForm.submit();
         }

//  document.editForm.submit();
}\n";
$editcontent['javascript'] .= "\n".$koppelObject[0]->getJavascript();

$editcontent['body'] = "onLoad='doOnload();'";
$editObject->template = $editcontent;

$editObject->formTemplate = "rekeningmutatiesTemplate.html";
$editObject->usetemplate = true;

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($object->get('Transactietype')=='T')
  {
    $object->herrekenFactor();
  }
	header("Location: rekeningmutatiesEdit.php?action=new&afschrift_id=".$afschrift["id"]."&message=".urlencode($editObject->message));
	//header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>