<?php

$rbuilder = array("Fondsoverzicht","Geaggregeerd-portefeuille-overzicht","Managementoverzicht","Zorgplichtcontrole");
//$rbuilder = array("Fondsoverzicht","Geaggregeerd-portefeuille-overzicht");

$operatorOptions = "		<option value=\"=\" > = </option>
		<option value=\">\" > > </option>
		<option value=\"<\" > < </option>
		<option value=\"<=\"> <= </option>
		<option value=\">=\"> >= </option>
		<option value=\"<>\"> <> </option>
		<option value=\"LIKE\"> bevat </option>";

$rselection["Fondsoverzicht"] = array("Portefeuille","Einddatum",
																			"Vermogensbeheerder",
																			"Client",
																			"Naam",
																			"Naam1",
																			"profielOverigeBeperkingen",
																			"Depotbank",
																			"Accountmanager",
																			"Risicoprofiel",
																			"SoortOvereenkomst",
																			"Risicoklasse",
																			"Remisier",
																			"AFMprofiel",
                                      "Consolidatie",
																			"InternDepot",
																			"Fonds","standaardSector",
																			"Kostprijs",
                                      "KoersDatum",
                                      "hoofdcategorie","hoofdsector","beleggingssector","beleggingscategorie","regio","attributieCategorie","afmCategorie",
                                      "LaatsteKoers",                                      
																			"Beginwaardelopendjaar",
																			"AandeelBeleggingscategorie",
                                      "ISINCode","fondsValuta","renteWaarde","fondsWaarde","VKM","passiefFonds","fondssoort",
																			"AandeelTotaalvermogen",
																			"AandeelTotaalBelegdvermogen",
																			"AantalInPortefeuille","Bewaarder");


                    
$rselection["FondsoverzichtSelect"] = array(
                                      "Portefeuille"=>2,
																			"Vermogensbeheerder"=>2,
																			"Client"=>2,
																			"Depotbank"=>2,"Consolidatie"=>2,
																			"Accountmanager"=>2,
																			"Risicoprofiel"=>2,
																			"SoortOvereenkomst"=>2,
																			"Risicoklasse"=>2,
																			"Remisier"=>2,
																			"AFMprofiel"=>2,
																			"InternDepot"=>2,
//																			"Fonds"=>1,
																			"Kostprijs"=>3,
																			"Beginwaardelopendjaar"=>3,
																			"AandeelBeleggingscategorie"=>3,
																			"AandeelTotaalvermogen"=>3,
																			"AandeelTotaalBelegdvermogen"=>3,
																			"AantalInPortefeuille"=>3,
                                      "KoersDatum"=>3,
                                      "ISINCode"=>3,"fondsValuta"=>3,"renteWaarde"=>3,"fondsWaarde"=>3,"VKM"=>3,"passiefFonds"=>3,
                                      "hoofdcategorie"=>3,"hoofdsector"=>3,"beleggingssector"=>3,"beleggingscategorie"=>3,"regio"=>3,"attributieCategorie"=>3,"afmCategorie"=>3,  
                                      "LaatsteKoers"=>3,"Bewaarder"=>2);


$rselection["Geaggregeerd-portefeuille-overzicht"] = array(
                                      "Fonds", "Portefeuille","Consolidatie",
																			"Omschrijving",
																			"FondsImportCode",
																			"Valuta",
																			"Fondseenheid",
																			"Rentedatum",
																			"Renteperiode",
																			"ISINCode",
																			"InternDepot",
																			"rating",
																			"TGBCode",
																			"stroeveCode",
																			"AABCode",
                                      "VKM",
	                                    "passiefFonds",
																			"Beleggingscategorie",
																			"RisicoPercentageFonds",
																			"Beleggingssector",
																			"standaardSector",
																			"Zorgplicht",
																			"Aantal",
																			"Fondskoers",
																			"Fondstotaal",
																			"FondstotaalEUR",
																			"PercentageTotaal",
																			"AantalWaarnemingen",
                                      "opgelopenrente",
                                      "opgelopenrenteFondsvaluta",
                                      "Regio",
	                                    "afmCategorie",
	                                    "AttributieCategorie",
                                      "Duurzaamheid",
                                      "FondsYtd",
                                      "KoersDatum");

$rselection["Geaggregeerd-portefeuille-overzichtSelect"] = array(
                                      "Fonds"=>1, "Portefeuille"=>1,"Consolidatie"=>1,
																			"Omschrijving"=>1,
																			"FondsImportCode"=>1,
																			"Valuta"=>1,
																			"Fondseenheid"=>1,
																			"Rentedatum"=>1,
																			"Renteperiode"=>1,
																			"ISINCode"=>1,
																			"TGBCode"=>1,
																			"stroeveCode"=>1,
																			"AABCode"=>1,
                                      "VKM"=>1,
																		  "passiefFonds"=>1,
																			"InternDepot"=>1,
																			"Beleggingscategorie"=>1,
																			"RisicoPercentageFonds"=>1,
																			"Beleggingssector"=>1,
																			"standaardSector"=>1,
																			"Zorgplicht"=>1,
																			"Aantal"=>2,
																			"Fondskoers"=>2,
																			"Fondstotaal"=>2,
																			"FondstotaalEUR"=>2,
																			"PercentageTotaal"=>2,
																			"AantalWaarnemingen"=>2,
                                      "opgelopenrente"=>2,
                                      "opgelopenrenteFondsvaluta"=>2,
                                      "FondsYtd"=>2,
                                      "Regio"=>2,
	                                    "afmCategorie"=>2,
	                                    "AttributieCategorie"=>2,
                                      "Duurzaamheid"=>2,
                                      "KoersDatum"=>2);

$rselection["Managementoverzicht"] = array("Portefeuille",
                                      "Naam",
																			"Vermogensbeheerder",
																			"Client",
                                      "profielOverigeBeperkingen",
																			"Consolidatie",
																			"Depotbank",
                                      "InternDepot",
																			"Startdatum",
																			"Einddatum",
																			"ClientVermogensbeheerder",
																			"Accountmanager",
																			"Risicoprofiel",
																			"SoortOvereenkomst",
																			"Risicoklasse",
																			"Remisier",
																			"AFMprofiel",
																			"ModelPortefeuille",
	                                    "totaalbeginvermogen",
																			"totaalvermogen",
																			"inprocenttotaal",
																			"performance",
																			"resultaat",
																			"rendement",
																			"OnttrLicht",
	                                    "Onttrekkingen",
	                                    "Lichtingen",
																			"StortDepon",
	                                    "Stortingen",
	                                    "Deponeringen",
																			"dividend",
																			"dividendbelasting",
																			"rente",
																			"koersongerealiseerd",
																			"koersgerealiseerd",
																			"stockdividend",
																			"creditrente",
																			"transactiekosten",
																			"kostenbuitenland",
																			"beheerfee",
																			"bewaarloon",
																			"bankkosten",
																			"liquiditeiten",
                                      "gemVermogen",
                                      "omzet",
                                      "omzetsnelheid",
	                                    "benchmarkRendement",
                                      "TOB",
                                      "BTLBR",
                                      "BANK",
                                      "VALK");

$rselection["ManagementoverzichtSelect"] = array(
                                      "Portefeuille"=>1,
																			"Vermogensbeheerder"=>1,
																			"Client"=>1,
                                      "Consolidatie"=>1,
																			"Depotbank"=>1,
																			"Startdatum"=>1,
																			"Einddatum"=>1,
																			"ClientVermogensbeheerder"=>1,
																			"Accountmanager"=>1,
																			"Risicoprofiel"=>1,
																			"SoortOvereenkomst"=>1,
																			"Risicoklasse"=>1,
																			"Remisier"=>1,
																			"AFMprofiel"=>1,
																			"ModelPortefeuille"=>1,
																			"totaalbeginvermogen"=>2,
																			"totaalvermogen"=>2,
																			"inprocenttotaal"=>2,
																			"performance"=>2,
																			"resultaat"=>2,
																			"rendement"=>2,
																			"onttrekkingen"=>2,
																			"stortingen"=>2,
																			"dividend"=>2,
																			"dividendbelasting"=>2,
																			"rente"=>2,
																			"koersongerealiseerd"=>2,
																			"koersgerealiseerd"=>2,
																			"stockdividend"=>2,
																			"creditrente"=>2,
																			"transactiekosten"=>2,
																			"beheerfee"=>2,
																			"bewaarloon"=>2,
																			"bankkosten"=>2,
																			"liquiditeiten"=>2,
                                      "gemVermogen"=>2,
                                      "omzet"=>2,
                                      "omzetsnelheid"=>2,
                                      "TOB"=>3,
                                      "BTLBR"=>3,
                                      "BANK"=>3,
                                      "VALK"=>3);

$rselection["Zorgplichtcontrole"] = array("Portefeuille",
                                      "Naam",
																			"Vermogensbeheerder",
																			"Client",
																			"Depotbank",
                                      "Consolidatie",
																			"Startdatum",
																			"Einddatum",
																			"ClientVermogensbeheerder",
																			"Accountmanager",
																			"Risicoprofiel",
																			"SoortOvereenkomst",
																			"Risicoklasse",
																			"Remisier",
																			"AFMprofiel",
																			"ModelPortefeuille",
																			"totaalvermogen",
																			"inprocenttotaal",
																			"conclusie",
																			"reden",
                                      "norm");


	$rselection["ZorgplichtcontroleSelect"] = array(
                                      "Portefeuille"=>1,
																			"Vermogensbeheerder"=>1,
																			"Client"=>1,
                                      "Consolidatie"=>1,
																			"Depotbank"=>1,
																			"Startdatum"=>1,
																			"Einddatum"=>1,
																			"ClientVermogensbeheerder"=>1,
																			"Accountmanager"=>1,
																			"Risicoprofiel"=>1,
																			"SoortOvereenkomst"=>1,
																			"Risicoklasse"=>1,
																			"Remisier"=>1,
																			"AFMprofiel"=>1,
																			"ModelPortefeuille"=>1,
																			"totaalvermogen"=>2,
																			"inprocenttotaal"=>2,
																			"conclusie"=>2,
																			"reden"=>2,
                                      "norm"=>2);


$orderByArray = array("ASC"    => array("value" => "ASC"  , "description" => " Oplopend "    , "list_visible" => true),
                      "DESC"   => array("value" => "DESC" , "description" => " Aflopend "    , "list_visible" => true));

$andOrArray = array("AND"    => array("value" => "AND"  , "description" => " EN "    , "list_visible" => true),
                    "OR"     => array("value" => "OR"   , "description" => " OF "    , "list_visible" => true));

$operatorArray = array("="    => array("value" => "="   , "description" => " = "    , "list_visible" => true),
                       ">"    => array("value" => ">"   , "description" => " > "    , "list_visible" => true),
                       "<"    => array("value" => "<"   , "description" => " < "    , "list_visible" => true),
                       "<="   => array("value" => "<="  , "description" => " <= "   , "list_visible" => true),
                       ">="   => array("value" => ">="  , "description" => " >= "   , "list_visible" => true),
                       "<>"   => array("value" => "<>"  , "description" => " <> "   , "list_visible" => true),
                       "LIKE" => array("value" => "LIKE", "description" => " bevat ", "list_visible" => true));

$groupActionArray = array("SUM"    => array("value" => "SUM"  , "description" => " Totaal "            , "list_visible" => true),
                          "AVG"    => array("value" => "AVG"  , "description" => " Gemiddelde waarde " , "list_visible" => true),
                          "MAX"    => array("value" => "MAX"  , "description" => " Maximale waarde "   , "list_visible" => true),
                          "MIN"    => array("value" => "MIN"  , "description" => " Minimale waarde "   , "list_visible" => true),
                          "COUNT"  => array("value" => "COUNT", "description" => " Aantal waarden "    , "list_visible" => true));



function getOptions($theArray=array(),$selection="",$emptyChoice=true,$arrayIsKeyed=true)
{

  reset($theArray);
  if ($emptyChoice)
	  $options = "<option value=\"\">--</option>\n";
	while (list($key, $value) = each($theArray))
	{
	  if ($arrayIsKeyed == false)
	  {
//	    listarray($theArray);
	    $key = $value;
	    $theArray[$key]['description'] = $value;
	    $theArray[$key]['list_visible'] = true;
	  }


	  if($theArray[$key]['list_visible'] == true)
		{
		  $sstring = "";
	    if($selection == $key) $sstring = "selected";
			$options .= "<option value=\"".$key."\" $sstring>".$theArray[$key]['description']."</option>\n";
		}
	}
	return $options;
}
function getOptions1($theArray=array(),$selection="")
{
	reset($theArray);
  foreach ($theArray as $key=>$val)
  {
  	$sstring = "";
  	if($selection == $key) $sstring = "selected";
  	$options .= "<option value=\"".$key."\" $sstring>".$val."</option>\n";
  }

  return $options;
}





?>