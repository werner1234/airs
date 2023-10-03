<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "vermogensbeheerderList.php";
$__funcvar[location] = "vermogensbeheerderEdit.php";

$object = new Vermogensbeheerder();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->skipStripAll=true;
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>";
$editcontent['body'] = " onLoad=\"javascript:tabOpen('0');checkFieldStatus();\" ";
$editcontent['javascript'].=" function checkFieldStatus()
{
			if($(\"#koersExport\").val()==0)
			{
				$(\"#fondskostenDoorkijkExport\").prop(\"disabled\", true);
				$(\"#fondskostenDoorkijkExport\").prop(\"checked\", false);
			}
			else
			{
				$(\"#fondskostenDoorkijkExport\").prop(\"disabled\", false);
			}
}

function checkToon(rapport)
{
  var nietTonen=\"#frontOffice_toonNiet_\"+rapport;
  var tonen=\"#frontOffice_toon_\"+rapport;
   if($(tonen).prop(\"checked\")==true)
   {
     $(nietTonen).prop(\"disabled\", true);
     $(nietTonen).prop(\"checked\", false);
   }
   else
   {
    $(nietTonen).prop(\"disabled\", false);
   }
}

";

$editObject->template = $editcontent;

$editObject->formTemplate = "vermogensbeheerderTemplate.html";
$editObject->usetemplate = true;

$data = $_GET;
if ($_POST)
 $data = $_POST;
$action = $data['action'];

$DB = new DB();

$object->data['fields']["PerformanceBerekening"]["form_options"] = $__appvar["PerformanceBerekeningOptions"];
$object->data['fields']["FactuurBeheerfeeBerekening"]["form_options"] = $__appvar["FactuurBeheerfeeBerekeningOptions"];

// loop over rapport typen.
$typen[] = "frontOffice";
$typen[] = "dag";
//$typen[] = "maand";
//$typen[] = "kwartaal";

$kleuren[] = "R";
$kleuren[] = "G";
$kleuren[] = "B";

$rapporten['BRIEF']='Kwartaal Brief';
$rapporten['FRONT']='Voorpagina';
$rapporten['FACTUUR']='Factuur';
$rapporten=array_merge($rapporten,$__appvar["Rapporten"]);

$editObject->controller($action,$data);
	$vermogensbeheerder=$object->get('Vermogensbeheerder');
  $geenStandaardSector=$object->get('geenStandaardSector');
// voordat er opgeslagen wordt, eerst even de arrays serializen.


$beleggingscategorienQuery = "SELECT id, Beleggingscategorie, omschrijving FROM Beleggingscategorien";
$depotbankenQuery = "SELECT id, Depotbank, omschrijving FROM Depotbanken";
$afmCategorienQuery = "SELECT id, afmCategorie, omschrijving FROM afmCategorien ORDER BY afmCategorie";
$regioQuery = "SELECT id, Regio, omschrijving FROM Regios";
$valutaQuery = "SELECT id, Valuta, omschrijving FROM Valutas";
$sectorQuery = "SELECT id, Beleggingssector, Omschrijving FROM Beleggingssectoren";
$attributieQuery = "SELECT id, AttributieCategorie , Omschrijving FROM AttributieCategorien ";
$ratingQuery = "SELECT Rating.id, Rating.rating, Rating.omschrijving FROM Rating";
$grootboekQuery = "SELECT Grootboekrekeningen.id, Grootboekrekeningen.omschrijving, Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen WHERE kosten=1";
$duurzaamQuery = "SELECT id, DuurzaamCategorie , Omschrijving FROM DuurzaamCategorien ";

if($vermogensbeheerder)
{
  $beleggingscategorienQuery = "SELECT Beleggingscategorien.id, Beleggingscategorien.Beleggingscategorie,
    Beleggingscategorien.omschrijving, Beleggingscategorien.afdrukvolgorde
    FROM
    Beleggingscategorien
    LEFT Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie AND BeleggingscategoriePerFonds.Vermogensbeheerder = '$vermogensbeheerder'
    LEFT Join CategorienPerVermogensbeheerder ON  Beleggingscategorien.Beleggingscategorie = CategorienPerVermogensbeheerder.Beleggingscategorie  AND CategorienPerVermogensbeheerder.Vermogensbeheerder = '$vermogensbeheerder'
    WHERE BeleggingscategoriePerFonds.Beleggingscategorie is  NOT NULL OR CategorienPerVermogensbeheerder.Beleggingscategorie IS NOT NULL
    GROUP BY Beleggingscategorien.Beleggingscategorie
  UNION
    SELECT Beleggingscategorien.id,
    CategorienPerHoofdcategorie.Hoofdcategorie,
    Beleggingscategorien.Omschrijving, Beleggingscategorien.afdrukvolgorde
    FROM
    CategorienPerHoofdcategorie
    Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
    WHERE CategorienPerHoofdcategorie.Vermogensbeheerder ='$vermogensbeheerder'
    GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie ORDER BY afdrukvolgorde";
  
  
  $beleggingscategorienQuery = "SELECT id,Beleggingscategorie,max(omschrijving) as omschrijving,afdrukvolgorde FROM (SELECT Beleggingscategorien.id, Beleggingscategorien.Beleggingscategorie,
    Beleggingscategorien.omschrijving, Beleggingscategorien.afdrukvolgorde
    FROM
    Beleggingscategorien
    LEFT Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie AND BeleggingscategoriePerFonds.Vermogensbeheerder = '$vermogensbeheerder'
    LEFT Join CategorienPerVermogensbeheerder ON  Beleggingscategorien.Beleggingscategorie = CategorienPerVermogensbeheerder.Beleggingscategorie  AND CategorienPerVermogensbeheerder.Vermogensbeheerder = '$vermogensbeheerder'
    WHERE BeleggingscategoriePerFonds.Beleggingscategorie is  NOT NULL OR CategorienPerVermogensbeheerder.Beleggingscategorie IS NOT NULL
  UNION
    SELECT Beleggingscategorien.id,
    CategorienPerHoofdcategorie.Hoofdcategorie,
    concat(Beleggingscategorien.Omschrijving,' (HC)') as Omschrijving, Beleggingscategorien.afdrukvolgorde
    FROM
    CategorienPerHoofdcategorie
    Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
    WHERE CategorienPerHoofdcategorie.Vermogensbeheerder ='$vermogensbeheerder'
UNION
SELECT
Beleggingscategorien.id, Beleggingscategorien.Beleggingscategorie, Beleggingscategorien.omschrijving, Beleggingscategorien.afdrukvolgorde
FROM KeuzePerVermogensbeheerder
JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie
WHERE categorie='Beleggingscategorien' AND Vermogensbeheerder='$vermogensbeheerder'
) tmp
	GROUP BY
		Beleggingscategorie
	ORDER BY
		afdrukvolgorde";
  
  if($DB->QRecords("SELECT id FROM KeuzePerVermogensbeheerder WHERE categorie='Grootboekrekeningen' AND Vermogensbeheerder='$vermogensbeheerder'") > 0)
	{
    $grootboekQuery = "SELECT Grootboekrekeningen.id, Grootboekrekeningen.omschrijving, Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen
   JOIN KeuzePerVermogensbeheerder ON  Grootboekrekeningen.Grootboekrekening=KeuzePerVermogensbeheerder.waarde AND  categorie='Grootboekrekeningen' AND Vermogensbeheerder='$vermogensbeheerder'
   WHERE Grootboekrekeningen.kosten=1";
  }


  $depotbankenQuery = "SELECT id, Depotbank, omschrijving FROM Depotbanken WHERE Depotbank IN(SELECT Depotbank FROM Portefeuilles WHERE Vermogensbeheerder='$vermogensbeheerder')";
  
  $afmCategorienQuery = "SELECT id, afmCategorie, omschrijving FROM afmCategorien ORDER BY afmCategorie";
  
  $regioQuery="SELECT id,Regio,omschrijving FROM (
SELECT Regios.id, Regios.Regio, Regios.omschrijving
FROM Regios
Join BeleggingssectorPerFonds ON Regios.Regio = BeleggingssectorPerFonds.Regio AND
BeleggingssectorPerFonds.Vermogensbeheerder='$vermogensbeheerder'
UNION
SELECT
Regios.id, Regios.Regio, Regios.omschrijving
FROM KeuzePerVermogensbeheerder
JOIN Regios ON KeuzePerVermogensbeheerder.waarde=Regios.Regio
WHERE categorie='Regios' AND Vermogensbeheerder='$vermogensbeheerder'
) as regios
GROUP BY Regio";
  

  if($geenStandaardSector==1)
     $filter="10";
  else
     $filter="1";

  $sectorQuery="SELECT * FROM (SELECT Beleggingssectoren.id, Beleggingssectoren.Beleggingssector, Beleggingssectoren.Omschrijving, Beleggingssectoren.afdrukvolgorde FROM Beleggingssectoren Inner Join BeleggingssectorPerFonds ON Beleggingssectoren.Beleggingssector = BeleggingssectorPerFonds.Beleggingssector AND BeleggingssectorPerFonds.Vermogensbeheerder='$vermogensbeheerder' GROUP BY Beleggingssector
    UNION
    SELECT id, Beleggingssector, Omschrijving,Beleggingssectoren.afdrukvolgorde FROM Beleggingssectoren WHERE standaard='$filter'
    UNION
SELECT
Beleggingssectoren.id, Beleggingssectoren.Beleggingssector, Beleggingssectoren.omschrijving,Beleggingssectoren.afdrukvolgorde
FROM KeuzePerVermogensbeheerder
JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde=Beleggingssectoren.Beleggingssector
WHERE categorie='Beleggingssectoren' AND Vermogensbeheerder='$vermogensbeheerder') as tmp GROUP BY Beleggingssector ORDER BY afdrukvolgorde";

    $attributieQuery="SELECT id,AttributieCategorie,Omschrijving FROM (
SELECT AttributieCategorien.id, AttributieCategorien.AttributieCategorie, AttributieCategorien.Omschrijving
FROM AttributieCategorien
Join BeleggingssectorPerFonds ON AttributieCategorien.AttributieCategorie = BeleggingssectorPerFonds.AttributieCategorie AND
BeleggingssectorPerFonds.Vermogensbeheerder='$vermogensbeheerder'
UNION
SELECT
AttributieCategorien.id, AttributieCategorien.AttributieCategorie, AttributieCategorien.Omschrijving
FROM KeuzePerVermogensbeheerder
JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde=AttributieCategorien.AttributieCategorie
WHERE categorie='AttributieCategorien' AND Vermogensbeheerder='$vermogensbeheerder'
) as Attributie
GROUP BY AttributieCategorie";
  
  
    $duurzaamQuery="SELECT id,DuurzaamCategorie,Omschrijving FROM (
SELECT DuurzaamCategorien.id, DuurzaamCategorien.DuurzaamCategorie, DuurzaamCategorien.Omschrijving
FROM DuurzaamCategorien
Join BeleggingssectorPerFonds ON DuurzaamCategorien.DuurzaamCategorie = BeleggingssectorPerFonds.DuurzaamCategorie AND
BeleggingssectorPerFonds.Vermogensbeheerder='$vermogensbeheerder'
UNION
SELECT
DuurzaamCategorien.id, DuurzaamCategorien.DuurzaamCategorie, DuurzaamCategorien.Omschrijving
FROM KeuzePerVermogensbeheerder
JOIN DuurzaamCategorien ON KeuzePerVermogensbeheerder.waarde=DuurzaamCategorien.DuurzaamCategorie
WHERE categorie='DuurzaamCategorien' AND Vermogensbeheerder='$vermogensbeheerder'
) as DuurzaamCategorie
GROUP BY DuurzaamCategorie";

  $lookupQueries['OIB']=$beleggingscategorienQuery;
  $lookupQueries['DEP']=$depotbankenQuery;
  $lookupQueries['AFM']=$afmCategorienQuery;
  $lookupQueries['OIR']=$regioQuery;
  $lookupQueries['OIV']=$valutaQuery;
  $lookupQueries['OIS']=$sectorQuery;
  $lookupQueries['ATT']=$attributieQuery;
  $lookupQueries['Rating']=$ratingQuery;
  $lookupQueries['Grootboek']=$grootboekQuery;
  $lookupQueries['DUU']=$duurzaamQuery;
  
}

//listarray($lookupQueries);

if($action == "update")
{
	foreach($rapporten as $key=>$value)
	{
		for($a=0; $a < count($typen); $a++)
		{
		  if($typen[$a] == 'frontOffice')
      {
		    $export[$typen[$a]][$key]['toon'] = $data[$typen[$a]."_toon_".$key];
        $export[$typen[$a]][$key]['toonNiet'] = $data[$typen[$a]."_toonNiet_".$key];
        $export[$typen[$a]][$key]['xls'] = $data[$typen[$a]."_xls_".$key];
				$export[$typen[$a]][$key]['shortName'] = $data[$typen[$a]."_shortName_".$key];
				$export[$typen[$a]][$key]['longName'] = $data[$typen[$a]."_longName_".$key];
			}
      $export[$typen[$a]][$key]['checked'] = $data[$typen[$a]."_checkbox_".$key];
			$export[$typen[$a]][$key]['volgorde'] = $data[$typen[$a]."_volgorde_".$key];
			$export[$typen[$a]][$key]['dag'] = $data[$typen[$a]."_dag_".$key];
			$export[$typen[$a]][$key]['maand'] = $data[$typen[$a]."_maand_".$key];
			$export[$typen[$a]][$key]['jaar'] = $data[$typen[$a]."_jaar_".$key];
			if($key=='CASHFLOW-Y')
        $export[$typen[$a]]['CASHY']=$export[$typen[$a]][$key];
      if($key=='CASHFLOW')
        $export[$typen[$a]]['CASH']=$export[$typen[$a]][$key];
		}
	}
 
 
	
	while (list($key, $value) = each($__ORDERvar["orderControles"]))
	{
			$export['order_controle'][$key]['checked'] = $data["order_controle_checkbox_".$key];
	}
  foreach ($__ORDERvar["orderStatus"] as $key=>$value)
	{
	  if($data["order_status_checkbox_".$key]==1)
	    $value=0;
	  else
	    $value=1;
	  $export['OrderStatusKeuze'][$key]['checked'] = $value;
    $export['OrderStatusKeuze'][$key]['checkedEmail'] = $data["order_status_checkboxEmail_".$key];
	}

	//Grafiekkleuren update
	for($a=0; $a < count($kleuren); $a++)
	{
	$export['grafiek_kleur']['OIB']['Opgelopen Rente'][$kleuren[$a]]['value'] = $data['OIB_grafiek_OpgelopenRente_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIR']['Geen regio'][$kleuren[$a]]['value'] = $data['OIR_grafiek_geenRegio_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIS']['Geen hoofdsector'][$kleuren[$a]]['value'] = $data['OIS_grafiek_geenHoofdsector_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIS']['Geen sector'][$kleuren[$a]]['value'] = $data['OIS_grafiek_geenSector_'.$kleuren[$a]];
	$export['grafiek_kleur']['ATT']['Liquiditeiten'][$kleuren[$a]]['value'] = $data['ATT_grafiek_Liquiditeiten_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIR']['Liquiditeiten'][$kleuren[$a]]['value'] = $data['OIR_grafiek_Liquiditeiten_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIS']['Liquiditeiten'][$kleuren[$a]]['value'] = $data['OIS_grafiek_Liquiditeiten_'.$kleuren[$a]];
	$export['grafiek_kleur']['Rating']['Geen rating'][$kleuren[$a]]['value'] = $data['Rating_grafiek_geenRating_'.$kleuren[$a]];
	$export['grafiek_kleur']['OIV']['Overige'][$kleuren[$a]]['value'] = $data['OIV_grafiek_Overige_'.$kleuren[$a]];
	$export['grafiek_kleur']['Grootboek']['Doorlopende kosten'][$kleuren[$a]]['value'] = $data['Grootboek_grafiek_doorlopendeKosten_'.$kleuren[$a]];
  $export['grafiek_kleur']['Grootboek']['Indirecte (fonds)kosten'][$kleuren[$a]]['value'] = $data['Grootboek_grafiek_doorlopendeKosten_'.$kleuren[$a]];
  $export['grafiek_kleur']['Grootboek']['Spread-kosten'][$kleuren[$a]]['value'] = $data['Grootboek_grafiek_Spread-kosten_'.$kleuren[$a]];
  $export['grafiek_kleur']['Grootboek']['BTW over Beheervergoeding'][$kleuren[$a]]['value'] = $data['Grootboek_grafiek_btw_beheerkosten_'.$kleuren[$a]];
	}
//listarray($data);listarray($export['grafiek_kleur']);
	$DB->SQL($beleggingscategorienQuery);
	$DB->Query();
	while($categorie = $DB->NextRecord())
	{
		$key = $categorie['Beleggingscategorie'];
		$key1 = $categorie['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['OIB'][$key][$kleuren[$a]]['value'] = $data['OIB_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}
		//Grafiekkleuren update
	for($a=0; $a < count($kleuren); $a++)
	{
	  $export['grafiek_kleur']['OIB']['Liquiditeiten'][$kleuren[$a]]['value'] = $data['OIB_grafiek_Liquiditeiten_'.$kleuren[$a]];
	}

	
	$DB->SQL($valutaQuery);
	$DB->Query();
	while($valuta = $DB->NextRecord())
	{
		$key = $valuta['Valuta'];
		$key1 = $valuta['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['OIV'][$key][$kleuren[$a]]['value'] = $data['OIV_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}


	$DB->SQL($depotbankenQuery);
	$DB->Query();
	while($valuta = $DB->NextRecord())
	{
		$key = $valuta['Depotbank'];
		$key1 = $valuta['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['DEP'][$key][$kleuren[$a]]['value'] = $data['DEP_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}


	$DB->SQL($afmCategorienQuery);
	$DB->Query();
	while($afm = $DB->NextRecord())
	{
		$key = $afm['afmCategorie'];
		$key1 = $afm['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['AFM'][$key][$kleuren[$a]]['value'] = $data['AFM_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}


	$DB->SQL($regioQuery);
	$DB->Query();
	while($valuta = $DB->NextRecord())
	{
		$key = $valuta['Regio'];
		$key1 = $valuta['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['OIR'][$key][$kleuren[$a]]['value'] = $data['OIR_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}

	$DB->SQL($sectorQuery);
	$DB->Query();
	while($sector = $DB->NextRecord())
	{
		$key = $sector['Beleggingssector'];
		$key1 = $sector['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['OIS'][$key][$kleuren[$a]]['value'] = $data['OIS_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}


	$DB->SQL($attributieQuery);
	$DB->Query();
	while($sector = $DB->NextRecord())
	{
		$key = $sector['AttributieCategorie'];
		$key1 = $sector['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['ATT'][$key][$kleuren[$a]]['value'] = $data['ATT_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}
 
	$DB->SQL($ratingQuery);
	$DB->Query();
	while($rating = $DB->NextRecord())
	{
		$key = $rating['rating'];
		$key1 = $rating['id'];
		for($a=0; $a < count($kleuren); $a++)
			$export['grafiek_kleur']['Rating'][$key][$kleuren[$a]]['value'] = $data['Rating_grafiek_'.$key1."_".$kleuren[$a]];
	}
 

	$DB->SQL($grootboekQuery);
	$DB->Query();
	while($rating = $DB->NextRecord())
	{
		$key = $rating['Grootboekrekening'];
		$key1 = $rating['id'];
		for($a=0; $a < count($kleuren); $a++)
			$export['grafiek_kleur']['Grootboek'][$key][$kleuren[$a]]['value'] = $data['Grootboek_grafiek_'.$key1."_".$kleuren[$a]];
	}

	$DB->SQL($duurzaamQuery);
	$DB->Query();
	while($cat = $DB->NextRecord())
	{
		$key = $cat['DuurzaamCategorie'];
		$key1 = $cat['id'];
		for($a=0; $a < count($kleuren); $a++)
		{
			$export['grafiek_kleur']['DUU'][$key][$kleuren[$a]]['value'] = $data['DUU_grafiek_'.$key1."_".$kleuren[$a]];
		}
	}

	// eind grafiek kleuren

	// set serialized data
	$object->set("Export_data_dag",serialize($export["dag"]));
	$object->set("Export_data_maand",serialize($export["maand"]));
	$object->set("Export_data_kwartaal",serialize($export["kwartaal"]));
	$object->set("order_controle",serialize($export["order_controle"]));
	$object->set("grafiek_kleur",serialize($export["grafiek_kleur"]));
	$object->set("Export_data_frontOffice",serialize($export["frontOffice"]));
	$object->set("OrderStatusKeuze",serialize($export["OrderStatusKeuze"]));
  if($editObject->object->error==false)
  	$object->save();
}

	// get serialized export data
$export['dag'] 			= unserialize($object->get("Export_data_dag"));
$export['maand'] 		= unserialize($object->get("Export_data_maand"));
$export['kwartaal'] = unserialize($object->get("Export_data_kwartaal"));
$export['order_controle'] = unserialize($object->get("order_controle"));
$export['grafiek_kleur'] = unserialize($object->get("grafiek_kleur"));
$export['frontOffice'] = unserialize($object->get("Export_data_frontOffice"));
$export['OrderStatusKeuze'] = unserialize($object->get("OrderStatusKeuze"));

if(!is_array($export['frontOffice']))
{
  foreach ($rapporten as $key=>$value)
  {
    $export['frontOffice'][$key]['checked'] = $object->get($key);
    $export['frontOffice'][$key]['volgorde'] = $object->get("Afdrukvolgorde".$key);
  }

}

// vul extra formvar voor export checkboxen .
reset($rapporten);


for($a=0; $a < count($typen); $a++)
{
	if($typen[$a] == 'frontOffice')
	  $editObject->formVars["export_".$typen[$a]."_checkbox"]='<table cellspacing="10"><tr><td colspan="3"><b>' . vt('Rapport (Toon checkbox,xls-uitvoer,Backoffice aan,Rapportage,Niet Tonen)') . '</b></td><td><b>' . vt('Afdrukvolgorde') . '</b></td><td><b>' . vt('Afdruk begindatum') . '</b></td></tr>';
  elseif($typen[$a] == 'dag')
  {
    $editObject->formVars["export_".$typen[$a]."_checkbox"]='<table cellspacing="10"><tr><td><b>' . vt('Rapport') . '</b></td><td><b>' . vt('Code') . '</b></td><td><b>' . vt('Afdrukvolgorde') . '</b></td><td><b>' . vt('Afdruk begindatum') . '</b></td></tr>';
  }
  else
		$editObject->formVars["export_".$typen[$a]."_checkbox"]='<table cellspacing="10"><tr><td><b>' . vt('Rapport') . '</b></td><td><b>' . vt('Afdrukvolgorde') . '</b></td><td><b>' . vt('Afdruk begindatum') . '</b></td></tr>';
	foreach($rapporten as $key=>$value)
	{
	  if($typen[$a] == 'frontOffice')
		{
		  $toon= "<input onclick=\"checkToon('$key');\" type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_toon_".$key."\" name=\"".$typen[$a]."_toon_".$key."\" ".(($export[$typen[$a]][$key]['toon']==1)?"checked":"").">\n";
		  $toon.= "<input type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_xls_".$key."\" name=\"".$typen[$a]."_xls_".$key."\" ".(($export[$typen[$a]][$key]['xls']==1)?"checked":"").">\n";
      $toonNiet= "<input ".(($export[$typen[$a]][$key]['toon']==1)?"disabled":"")." onclick=\"checkToon('$key');\" type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_toonNiet_".$key."\" name=\"".$typen[$a]."_toonNiet_".$key."\" ".(($export[$typen[$a]][$key]['toonNiet']==1)?"checked":"").">\n";
    }
    else
    {
      $toon = '';
      $toonNiet = '';
    }
		if($typen[$a] == 'frontOffice')
		{
			if($export[$typen[$a]][$key]['shortName']=='')
				$export[$typen[$a]][$key]['shortName']=$key;
			if($export[$typen[$a]][$key]['longName']=='')
				$export[$typen[$a]][$key]['longName']=$value;

			$shortname='<input type="text" size="8" value="'.$export[$typen[$a]][$key]['shortName'].'" name="'.$typen[$a]."_shortName_".$key.'">';
			$longname='<input type="text" size="35" value="'.$export[$typen[$a]][$key]['longName'].'" name="'.$typen[$a]."_longName_".$key.'">';

			$editObject->formVars["export_".$typen[$a]."_checkbox"] .= "<tr><td><div>$toon<input type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_checkbox_".$key."\" name=\"".$typen[$a]."_checkbox_".$key."\" ".(($export[$typen[$a]][$key]['checked']==1)?"checked":"")."><td>".$key.'</td><td> '.$toonNiet.$shortname.$longname."</td> </td>";
		}
    elseif($typen[$a] == 'dag')
    {
      $shortName = $key;
      $longName = $value;
      if( isset ($export['frontOffice'][$key]['shortName']) && ! empty ($export['frontOffice'][$key]['shortName']) ) {
        $shortName = $export['frontOffice'][$key]['shortName'];
      }
      if( isset ($export['frontOffice'][$key]['longName']) && ! empty ($export['frontOffice'][$key]['longName']) ) {
        $longName = $export['frontOffice'][$key]['longName'];
      }

      $editObject->formVars["export_".$typen[$a]."_checkbox"] .= "<tr><td>$toon<input type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_checkbox_".$key."\" name=\"".$typen[$a]."_checkbox_".$key."\" ".(($export[$typen[$a]][$key]['checked']==1)?"checked":"")."> <label for=\"".$typen[$a]."_checkbox_".$key."\" title=\"".$value."\">".$longName." (" . $key . ")</label></td>";
      $editObject->formVars["export_".$typen[$a]."_checkbox"] .= '<td>' . $shortName . '</td>';
    }
		else {
	  	$editObject->formVars["export_".$typen[$a]."_checkbox"] .= "<tr><td>$toon<input type=\"checkbox\" value=\"1\" id=\"".$typen[$a]."_checkbox_".$key."\" name=\"".$typen[$a]."_checkbox_".$key."\" ".(($export[$typen[$a]][$key]['checked']==1)?"checked":"")."> <label for=\"".$typen[$a]."_checkbox_".$key."\" title=\"".$value."\">".$value." </label></td>";
    }
		$editObject->formVars["export_".$typen[$a]."_checkbox"] .= "<td><input type=\"text\" size=\"5\" style=\"text-align:right;\" name=\"".$typen[$a]."_volgorde_".$key."\" value=\"".$export[$typen[$a]][$key]['volgorde']."\"></td>";
		$editObject->formVars["export_".$typen[$a]."_checkbox"] .= "<td><input type=\"text\" size=\"2\" name=\"".$typen[$a]."_dag_".$key."\" value=\"".$export[$typen[$a]][$key]['dag']."\"><input type=\"text\" size=\"2\" name=\"".$typen[$a]."_maand_".$key."\" value=\"".$export[$typen[$a]][$key]['maand']."\"><input type=\"text\" size=\"5\" name=\"".$typen[$a]."_jaar_".$key."\" value=\"".$export[$typen[$a]][$key]['jaar']."\"></td>";
		$editObject->formVars["export_".$typen[$a]."_checkbox"] .='</td></tr>';
	//_volgorde _start
	}
	$editObject->formVars["export_".$typen[$a]."_checkbox"].='</table>';
}


while (list($key, $value) = each($__ORDERvar["orderControles"]))
{
  if($key=='groot')
    $extra=', Max. weging order:'.$editObject->form->makeInput('orderMaxPercentage').' Max. weging positie:'.$editObject->form->makeInput('orderMaxPercentagePositie').' Max. bedrag:'.$editObject->form->makeInput('orderMaxBedrag');
  elseif($key=='akkam')
    $extra=', '.$editObject->form->makeInput('orderAkkoord');
	elseif($key=='liqu')
    $extra=', '.$editObject->form->makeInput('orderLiqVerkopen').' Incl. verkopen';
  else
    $extra='';
  $editObject->formVars["export_order_controle_checkbox"] .= "<div><input type=\"checkbox\" value=\"1\" id=\"order_controle_checkbox_".$key."\" name=\"order_controle_checkbox_".$key."\" ".(($export['order_controle'][$key][checked]==1)?"checked":"")."> <label for=\"order_controle_checkbox_".$key."\" title=\"".$value."\">".$value." </label> $extra </div><br>\n";
}

foreach ($__ORDERvar["orderStatus"] as $key=>$value)
{
  	$editObject->formVars["OrderStatusKeuzeChecks"] .= "<div>
    <input type=\"checkbox\" value=\"1\" id=\"order_status_checkbox_".$key."\" name=\"order_status_checkbox_".$key."\" ".(($export['OrderStatusKeuze'][$key]['checked']==0)?"checked":"")."> 
    <label for=\"order_status_checkbox_".$key."\" title=\"".$value."\">".$value." </label>
    <input type=\"checkbox\" value=\"1\" id=\"order_status_checkbox_".$key."\" name=\"order_status_checkboxEmail_".$key."\" ".(($export['OrderStatusKeuze'][$key]['checkedEmail']==1)?"checked":"")."> email bij deze status
    </div><br>\n";
}
//$editObject->formVars["OrderStatusKeuzeChecks"] .= 'test';

// Grafiek kleuren in tabel zetten.

$DB->SQL($beleggingscategorienQuery);
$DB->Query();


/** Opbouwen van Beleggingscategorien kleuren */
$editObject->formVars["OIB_grafiek_kleur"] = '
	<table>
		<tr>
			<td>Omschrijving</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">Kleur</td>
		</tr>
		
		<tr>
			<td>Opgelopen rente</td>
			<td colspan="4">
';
// Handmatig toegevoegde opgelopen rente.
for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["OIB_grafiek_kleur"] .= ' <input size="3" maxlength="3" type="text" value="'.$export['grafiek_kleur']['OIB']['Opgelopen Rente'][$kleuren[$a]]['value'].'" class="colorp" id="OIB_grafiek_OpgelopenRente_'.$kleuren[$a].'" data-group="OIB_grafiek_OpgelopenRente" name="OIB_grafiek_OpgelopenRente_'.$kleuren[$a].'" >';

$editObject->formVars["OIB_grafiek_kleur"] .= '<div id="OIB_grafiek_OpgelopenRente-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';



$editObject->formVars["OIB_grafiek_kleur"] .= "<tr><td>Liquiditeiten</td><td colspan=\"4\">\n";
for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["OIB_grafiek_kleur"] .= "<input size=\"3\" class=\"colorp\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIB']['Liquiditeiten'][$kleuren[$a]]['value']."\" id=\"OIB_grafiek_Liquiditeiten_".$kleuren[$a]."\" data-group=\"OIB_grafiek_Liquiditeiten\" name=\"OIB_grafiek_Liquiditeiten_".$kleuren[$a]."\" > \n";

$editObject->formVars["OIB_grafiek_kleur"] .= '<div id="OIB_grafiek_Liquiditeiten-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';


while($categorie = $DB->NextRecord())
{
	$editObject->formVars["OIB_grafiek_kleur"] .= "<tr><td>".$categorie['omschrijving']."</td><td colspan=\"4\">\n";
	$key = $categorie['Beleggingscategorie'];
	$key1 = $categorie['id'];
	for($a=0; $a < count($kleuren); $a++)
		$editObject->formVars["OIB_grafiek_kleur"] .= "<input class=\"colorp\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIB'][$key][$kleuren[$a]]['value']."\" id=\"OIB_grafiek_".$key1."_".$kleuren[$a]."\" data-group=\"OIB_grafiek_".$key1."\" name=\"OIB_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
  $editObject->formVars["OIB_grafiek_kleur"] .= '<div id="OIB_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/> <span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["OIB_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["OIB_grafiek_kleur"] .= "</table>";
/** Einde opbouwen van Beleggingscategorien kleuren */


/** Opbouwen van Valuta kleuren */


$editObject->formVars["OIV_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">Kleur</td>
		</tr>
		
		<tr>
			<td>' . vt('Overige') . '</td>
			<td colspan="4">
';


	$DB->SQL($valutaQuery);
$DB->Query();
for($a=0; $a < count($kleuren); $a++)
{
$editObject->formVars["OIV_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIV_grafiek_Overige\"  size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIV']['Overige'][$kleuren[$a]]['value']."\" id=\"OIV_grafiek_Overige_".$kleuren[$a]."\" name=\"OIV_grafiek_Overige_".$kleuren[$a]."\" > \n";
}
$editObject->formVars["OIV_grafiek_kleur"] .= '<div id="OIV_grafiek_Overige-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

while($valuta = $DB->NextRecord())
{
	$editObject->formVars["OIV_grafiek_kleur"] .= "<tr><td>".$valuta['omschrijving']."</td><td colspan=\"4\">\n";
	$key = $valuta['Valuta'];
	$key1 = $valuta['id'];
	for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["OIV_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIV_grafiek_".$key1."\"  size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIV'][$key][$kleuren[$a]]['value']."\" id=\"OIV_grafiek_".$key1."_".$kleuren[$a]."\" name=\"OIV_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  
  $editObject->formVars["OIV_grafiek_kleur"] .= '<div id="OIV_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["OIV_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["OIV_grafiek_kleur"] .= "</table>";
/** einde opbouwen van Valuta kleuren */

/** Opbouwen van Depotbank kleuren */

$DB->SQL($depotbankenQuery);
$DB->Query();


$editObject->formVars["DEP_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
		
';

while($depot = $DB->NextRecord())
{
	$editObject->formVars["DEP_grafiek_kleur"] .= "<tr><td>".$depot['omschrijving']."</td><td colspan=\"4\">\n";
	$key = $depot['Depotbank'];
	$key1 = $depot['id'];
	for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["DEP_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"DEP_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['DEP'][$key][$kleuren[$a]]['value']."\" id=\"DEP_grafiek_".$key1."_".$kleuren[$a]."\" name=\"DEP_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  
  $editObject->formVars["DEP_grafiek_kleur"] .= '<div id="DEP_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["DEP_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["DEP_grafiek_kleur"] .= "</table>";
/** einde opbouwen van Depotbank kleuren */

/** Opbouwen van afm kleuren */

$DB->SQL($afmCategorienQuery);
$DB->Query();
$editObject->formVars["AFM_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
';

while($afm = $DB->NextRecord())
{
  $editObject->formVars["AFM_grafiek_kleur"] .= "<tr><td>".$afm['omschrijving']."</td><td colspan=\"4\">\n";
	$key = $afm['afmCategorie'];
	$key1 = $afm['id'];
for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["AFM_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"AFM_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['AFM'][$key][$kleuren[$a]]['value']."\" id=\"AFM_grafiek_".$key1."_".$kleuren[$a]."\" name=\"AFM_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  $editObject->formVars["AFM_grafiek_kleur"] .= '<div id="AFM_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["AFM_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["AFM_grafiek_kleur"] .= "</table>";
/** einde opbouwen van afm kleuren */


$DB->SQL($regioQuery);
$DB->Query();
$editObject->formVars["OIR_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
		
		<tr>
			<td>' . vt('Geen regio') . '</td>
			<td colspan="4">
';// Handmatig toegevoegde geen regio.
for($a=0; $a < count($kleuren); $a++)
{
$editObject->formVars["OIR_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIR_grafiek_geenRegio\"  size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIR']['Geen regio'][$kleuren[$a]]['value']."\" id=\"OIR_grafiek_geenRegio_".$kleuren[$a]."\" name=\"OIR_grafiek_geenRegio_".$kleuren[$a]."\" > \n";
}
$editObject->formVars["OIR_grafiek_kleur"] .= '<div id="OIR_grafiek_geenRegio-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

//
while($regio = $DB->NextRecord())
{
	$editObject->formVars["OIR_grafiek_kleur"] .= "<tr><td>".$regio['omschrijving']."</td><td colspan=\"4\">\n";
	$key = $regio['Regio'];
	$key1 = $regio['id'];
	for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["OIR_grafiek_kleur"] .= "<input class=\"colorp\"
data-group=\"OIR_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIR'][$key][$kleuren[$a]]['value']."\" id=\"OIR_grafiek_".$key1."_".$kleuren[$a]."\" name=\"OIR_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  $editObject->formVars["OIR_grafiek_kleur"] .= '<div id="OIR_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["OIR_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["OIR_grafiek_kleur"] .= "<tr><td>" . vt('Liquiditeiten') . "</td><td colspan=\"4\">\n";
for($a=0; $a < count($kleuren); $a++)
	$editObject->formVars["OIR_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIR_grafiek_Liquiditeiten\"  size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIR']['Liquiditeiten'][$kleuren[$a]]['value']."\" id=\"OIR_grafiek_Liquiditeiten_".$kleuren[$a]."\" name=\"OIR_grafiek_Liquiditeiten_".$kleuren[$a]."\" > \n";
$editObject->formVars["OIR_grafiek_kleur"] .= '<div id="OIR_grafiek_Liquiditeiten-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

$editObject->formVars["OIR_grafiek_kleur"] .= "</td></tr>\n";
$editObject->formVars["OIR_grafiek_kleur"] .= "</table>";

/** einde Opbouwen van Regio kleuren */

/** Opbouwen van sectoren kleuren */


$DB->SQL($sectorQuery);
$DB->Query();
$editObject->formVars["OIS_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
		
		<tr>
			<td>' . vt('Geen hoofdsector') . '</td>
			<td colspan="4">
';

for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["OIS_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIS_grafiek_geenHoofdsector\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIS']['Geen hoofdsector'][$kleuren[$a]]['value']."\" id=\"OIS_grafiek_geenHoofdsector_".$kleuren[$a]."\" name=\"OIS_grafiek_geenHoofdsector_".$kleuren[$a]."\" > \n";
$editObject->formVars["OIS_grafiek_kleur"] .= '<div id="OIS_grafiek_geenHoofdsector-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';



// Hnadmatig toegevoegde geen Sector.
$editObject->formVars["OIS_grafiek_kleur"] .= "<tr><td>" . vt('Geen sector') . "</td><td colspan=\"4\">\n";
for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["OIS_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIS_grafiek_geenSector\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIS']['Geen sector'][$kleuren[$a]]['value']."\" id=\"OIS_grafiek_geenSector_".$kleuren[$a]."\" name=\"OIS_grafiek_geenSector_".$kleuren[$a]."\" > \n";
$editObject->formVars["OIS_grafiek_kleur"] .= '<div id="OIS_grafiek_geenSector-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

while($sector = $DB->NextRecord())
{
	$editObject->formVars["OIS_grafiek_kleur"] .= "<tr><td>".$sector['Omschrijving']."</td><td colspan=\"4\">\n";
	$key1 = $sector['id'];
	$key = $sector['Beleggingssector'];
	for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["OIS_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIS_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIS'][$key][$kleuren[$a]]['value']."\" id=\"OIS_grafiek_".$key1."_".$kleuren[$a]."\" name=\"OIS_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  $editObject->formVars["OIS_grafiek_kleur"] .= '<div id="OIS_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["OIS_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["OIS_grafiek_kleur"] .= "<tr><td>" . vt('Liquiditeiten') . "</td><td colspan=\"4\">\n";
for($a=0; $a < count($kleuren); $a++)
	$editObject->formVars["OIS_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"OIS_grafiek_Liquiditeiten\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['OIS']['Liquiditeiten'][$kleuren[$a]]['value']."\" id=\"OIS_grafiek_Liquiditeiten_".$kleuren[$a]."\" name=\"OIS_grafiek_Liquiditeiten_".$kleuren[$a]."\" > \n";
$editObject->formVars["OIS_grafiek_kleur"] .= '<div id="OIS_grafiek_Liquiditeiten-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

$editObject->formVars["OIS_grafiek_kleur"] .= "</td></tr>\n";
$editObject->formVars["OIS_grafiek_kleur"] .= "</table>";
/** einde Opbouwen van sectoren kleuren */

/** Opbouwen van Attributie categorie kleuren */

//attributie

$DB->SQL($attributieQuery);
$DB->Query();


$editObject->formVars["ATT_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
';

while($sector = $DB->NextRecord())
{
	$editObject->formVars["ATT_grafiek_kleur"] .= "<tr><td>".$sector['Omschrijving']."</td><td colspan=\"4\">\n";
	$key1 = $sector['id'];
	$key = $sector['AttributieCategorie'];
	for($a=0; $a < count($kleuren); $a++)
		{
		$editObject->formVars["ATT_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"ATT_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['ATT'][$key][$kleuren[$a]]['value']."\" id=\"ATT_grafiek_".$key1."_".$kleuren[$a]."\" name=\"ATT_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
		}
  $editObject->formVars["ATT_grafiek_kleur"] .= '<div id="ATT_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["ATT_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["ATT_grafiek_kleur"] .= "<tr><td>" . vt('Liquiditeiten') . "</td><td colspan=\"4\">\n";
for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["ATT_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"ATT_grafiek_Liquiditeiten\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['ATT']['Liquiditeiten'][$kleuren[$a]]['value']."\" id=\"ATT_grafiek_Liquiditeiten_".$kleuren[$a]."\" name=\"ATT_grafiek_Liquiditeiten_".$kleuren[$a]."\" > \n";
$editObject->formVars["ATT_grafiek_kleur"] .= '<div id="ATT_grafiek_Liquiditeiten-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';

$editObject->formVars["ATT_grafiek_kleur"] .= "</td></tr>\n";
$editObject->formVars["ATT_grafiek_kleur"] .= "</table>";
//eindattributie

/** einde Opbouwen van Attributie categorie kleuren */

/** Opbouwen van Duurzaam categorie  kleuren */


$DB->SQL($duurzaamQuery);
$DB->Query();
$editObject->formVars["DUU_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
';


while($sector = $DB->NextRecord())
{
	$editObject->formVars["DUU_grafiek_kleur"] .= "<tr><td>".$sector['Omschrijving']."</td><td colspan=\"4\">\n";
	$key1 = $sector['id'];
	$key = $sector['DuurzaamCategorie'];
	for($a=0; $a < count($kleuren); $a++)
	{
		$editObject->formVars["DUU_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"DUU_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['DUU'][$key][$kleuren[$a]]['value']."\" id=\"DUU_grafiek_".$key1."_".$kleuren[$a]."\" name=\"DUU_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
	}
  $editObject->formVars["DUU_grafiek_kleur"] .= '<div id="DUU_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  $editObject->formVars["DUU_grafiek_kleur"] .= "</td></tr>\n";
}
$editObject->formVars["DUU_grafiek_kleur"] .= "</table>";
//einde duu
/** einde Opbouwen van Duurzaam categorie  kleuren */

/** Opbouwen van Rating categorien kleuren */

$editObject->formVars["Rating_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="">' . vt('Kleur') . '</td>
		</tr>
		
		<tr>
			<td>' . vt('Geen rating') . '</td>
			<td colspan="4">
';

for($a=0; $a < count($kleuren); $a++)
  $editObject->formVars["Rating_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"Rating_grafiek_geenRating\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['Rating']['Geen rating'][$kleuren[$a]]['value']."\" id=\"Rating_grafiek_geenRating_".$kleuren[$a]."\" name=\"Rating_grafiek_geenRating_".$kleuren[$a]."\" > \n";
$editObject->formVars["Rating_grafiek_kleur"] .= '<div id="Rating_grafiek_geenRating-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';


	$DB->SQL($ratingQuery);
	$DB->Query();
	while($rating = $DB->NextRecord())
	{
		$key = $rating['rating'];
		$key1 = $rating['id'];
		$editObject->formVars["Rating_grafiek_kleur"] .= "<tr><td>".$rating['omschrijving']."</td><td colspan=\"4\">\n";
		for($a=0; $a < count($kleuren); $a++)
					$editObject->formVars["Rating_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"Rating_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['Rating'][$key][$kleuren[$a]]['value']."\" id=\"Rating_grafiek_".$key1."_".$kleuren[$a]."\" name=\"Rating_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
    $editObject->formVars["Rating_grafiek_kleur"] .= '<div id="Rating_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
    
  }
	$editObject->formVars["Rating_grafiek_kleur"] .= "</table>";
/** einde Opbouwen van Rating categorien kleuren */




/** Opbouwen van Grootboekrekeningen kleuren */

$editObject->formVars["Grootboek_grafiek_kleur"] = '
	<table>
		<tr>
			<td>' . vt('Omschrijving') . '</td>
			<td style="width:60px;">R</td>
			<td style="width:60px;">G</td>
			<td style="width:43px;">B</td>
			<td style="40px">' . vt('Kleur') . '</td>
		</tr>
		';

$extraVelden=array('doorlopendeKosten'=>array('Indirecte (fonds)kosten','Doorlopende kosten'),
									 'Spread-kosten'=>    array('Spread-kosten','Spread-kosten'),
									 'btw_beheerkosten'=> array('BTW over Beheervergoeding','BTW over Beheervergoeding'));
foreach($extraVelden as $key=>$omschrijving)
{
  $editObject->formVars["Grootboek_grafiek_kleur"] .= '		<tr>
			<td>'.$omschrijving[0].'</td>
			<td colspan="4">';
  for ($a = 0; $a < count($kleuren); $a++)
  {
    $editObject->formVars["Grootboek_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"Grootboek_grafiek_$key\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"" . $export['grafiek_kleur']['Grootboek'][$omschrijving[1]][$kleuren[$a]]['value'] . "\" id=\"Grootboek_grafiek_".$key."_" . $kleuren[$a] . "\" name=\"Grootboek_grafiek_".$key."_" . $kleuren[$a] . "\" > \n";
  }
  $editObject->formVars["Grootboek_grafiek_kleur"] .= '<div id="Grootboek_grafiek_'.$key.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option">
<input type="hidden" class="form-control input-lg" value=""/>
<span class="input-group-addon" ><i></i></span></div>';
}

//


$DB->SQL($grootboekQuery);
$DB->Query();
while($rating = $DB->NextRecord())
{
	$key = $rating['Grootboekrekening'];
	$key1 = $rating['id'];
	$editObject->formVars["Grootboek_grafiek_kleur"] .= "<tr><td>".$rating['omschrijving']."</td><td colspan=\"4\">\n";
	for($a=0; $a < count($kleuren); $a++)
		$editObject->formVars["Grootboek_grafiek_kleur"] .= "<input class=\"colorp\" data-group=\"Grootboek_grafiek_".$key1."\" size=\"3\" maxlength=\"3\" type=\"text\" value=\"".$export['grafiek_kleur']['Grootboek'][$key][$kleuren[$a]]['value']."\" id=\"Grootboek_grafiek_".$key1."_".$kleuren[$a]."\" name=\"Grootboek_grafiek_".$key1."_".$kleuren[$a]."\" > \n";
  $editObject->formVars["Grootboek_grafiek_kleur"] .= '<div id="Grootboek_grafiek_'.$key1.'-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option"><input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
}
$editObject->formVars["Grootboek_grafiek_kleur"] .= "</table>";

/** einde Opbouwen van Grootboekrekeningen kleuren */


// Einde grafiek kleuren tabel.


if (GetModuleAccess("ORDER"))
  $editObject->formVars['ordercontrole_active']= "<input type=\"button\" class=\"tabbuttonInActive\" onclick=\"javascript:tabOpen('6')\" id=\"tabbutton6\" value=\"".vt('Order controle')."\">";
else
  $editObject->formVars['ordercontrole_active']='';

echo $editObject->getOutput();
//listarray($editObject);
if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>
