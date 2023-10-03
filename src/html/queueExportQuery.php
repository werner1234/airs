<?

function buildQuery($table, $query, $values, $type=false)
{
	if(!$type)
		$query = str_replace( "{".$table."}", $table.".*", $query);
	else
	{
	 if($type=='ids')
		 $query = str_replace( "{".$table."}", "".$table.".id as id", $query);
   else
		 $query = str_replace( "{".$table."}", "count(".$table.".id) as aantal", $query);
	}

  if(isset($values['jaar']))
    $values['add_date_filter']="AND year(".$table.".add_date)='".$values['jaar']."'";
   
  foreach($values as $key=>$val)
  	$query = str_replace( "{".$key."}", $val, $query);

	$query = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $query);
	//echo $query;
	return $query;
}
// export Query
$exportQuery['Accountmanagers'] = "SELECT {Accountmanagers} FROM Accountmanagers, VermogensbeheerdersPerBedrijf WHERE ".
	 " Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " Accountmanagers.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['BeleggingscategoriePerFonds'] = "SELECT {BeleggingscategoriePerFonds} FROM BeleggingscategoriePerFonds, VermogensbeheerdersPerBedrijf WHERE ".
	 " BeleggingscategoriePerFonds.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " BeleggingscategoriePerFonds.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['BeleggingscategorienPerWegingscategorie'] = "SELECT {BeleggingscategorienPerWegingscategorie} FROM BeleggingscategorienPerWegingscategorie, VermogensbeheerdersPerBedrijf WHERE ".
	 " BeleggingscategorienPerWegingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " BeleggingscategorienPerWegingscategorie.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['CategorienPerHoofdcategorie'] = "SELECT {CategorienPerHoofdcategorie} FROM CategorienPerHoofdcategorie, VermogensbeheerdersPerBedrijf WHERE ".
	 " CategorienPerHoofdcategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " CategorienPerHoofdcategorie.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['VermogensbeheerdersPerBedrijf'] = "SELECT {VermogensbeheerdersPerBedrijf} FROM VermogensbeheerdersPerBedrijf WHERE Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['Beleggingscategorien'] = "SELECT {Beleggingscategorien} FROM Beleggingscategorien WHERE 1 {BeleggingscategorienQuery} {add_date_filter}";

$exportQuery['BeleggingssectorPerFonds'] = "SELECT {BeleggingssectorPerFonds} FROM BeleggingssectorPerFonds, VermogensbeheerdersPerBedrijf WHERE ".
	 " BeleggingssectorPerFonds.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " BeleggingssectorPerFonds.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['SectorenPerHoofdsector'] = "SELECT {SectorenPerHoofdsector} FROM SectorenPerHoofdsector, VermogensbeheerdersPerBedrijf WHERE ".
	 " SectorenPerHoofdsector.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " SectorenPerHoofdsector.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['Beleggingssectoren'] = "SELECT {Beleggingssectoren} FROM Beleggingssectoren WHERE ( 1 {BeleggingssectorenQuery} OR standaard=1 ) {add_date_filter}";

$exportQuery['DuurzaamCategorien'] = "SELECT {DuurzaamCategorien} FROM DuurzaamCategorien WHERE  1 {DuurzaamCategorienQuery} {add_date_filter}";

$exportQuery['Clienten'] = "SELECT {Clienten} FROM Clienten WHERE Clienten.Client {clientenQuery} AND Clienten.consolidatie<2 AND Clienten.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Depotbanken'] = "SELECT {Depotbanken} ".
	 " FROM Depotbanken ".
	 " WHERE ".
	 " Depotbanken.Depotbank {depotbankQuery} {add_date_filter}"; //AND Depotbanken.change_date >= '{lastUpdate}' 

$exportQuery['Fondskoersen'] = "SELECT {Fondskoersen}
 FROM Fondskoersen
 WHERE (Fondskoersen.change_date >= '{lastUpdate}' {add_date_filter} AND Fondskoersen.Fonds {fondsenQuery} ) OR Fondskoersen.Fonds {newFondsenQuery} "; //use INDEX ( FondsDatum )

$exportQuery['Rentepercentages'] = "SELECT {Rentepercentages}
	 FROM (Rentepercentages, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds=FondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = Rentepercentages.Fonds AND
	 {jaarInQuery}
	 (FondsenPerBedrijf.Bedrijf IS NULL OR Rentepercentages.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['Fondsen'] = "SELECT {Fondsen} ".
	" FROM (Fondsen, tmpFondsenPerBedrijf) ".
	" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
	" WHERE  ".
	" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" tmpFondsenPerBedrijf.Fonds = Fondsen.Fonds AND ".
	" (FondsenPerBedrijf.Bedrijf IS NULL OR Fondsen.change_date >= '{lastUpdate}') {add_date_filter}";//	"GROUP BY Fondsen.Fonds ";

//$exportQuery['Gebruikers'] = "SELECT {Gebruikers} FROM Gebruikers WHERE  Gebruiker {gebruikerQuery} AND Gebruikers.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Grootboekrekeningen'] = "SELECT {Grootboekrekeningen} ".
	"FROM Grootboekrekeningen ".
	"WHERE Grootboekrekeningen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Indices'] = "SELECT {Indices} ".
	"FROM Indices, VermogensbeheerdersPerBedrijf ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND  ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Indices.Vermogensbeheerder AND ".
	"Indices.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['KortingenPerDepotbank'] = "SELECT {KortingenPerDepotbank} ".
	"FROM KortingenPerDepotbank, VermogensbeheerdersPerBedrijf ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND  ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = KortingenPerDepotbank.Vermogensbeheerder AND ".
	"KortingenPerDepotbank.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Portefeuilles'] = "SELECT {Portefeuilles} ".
	"FROM Portefeuilles, VermogensbeheerdersPerBedrijf ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND  ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Portefeuilles.consolidatie<2 AND ".
	"Portefeuilles.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Rekeningafschriften'] = "SELECT {Rekeningafschriften} ".
	"FROM Rekeningafschriften, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf ".
	"WHERE ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningafschriften.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0 AND ".
	"Rekeningafschriften.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Rekeningen'] = "SELECT {Rekeningen} ".
	"FROM Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf ".
	"WHERE ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningen.consolidatie<2 AND ".
	"Rekeningen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Rekeningmutaties'] = "SELECT {Rekeningmutaties} ".
	" FROM Rekeningmutaties, Rekeningen, Portefeuilles, VermogensbeheerdersPerBedrijf ".
	" WHERE  ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	" Portefeuilles.Portefeuille = Rekeningen.Portefeuille AND ".
	" Rekeningen.Rekening = Rekeningmutaties.Rekening AND Rekeningen.consolidatie=0 AND ".
	" Rekeningmutaties.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Risicoklassen'] = "SELECT {Risicoklassen} ".
	" FROM Risicoklassen, VermogensbeheerdersPerBedrijf ".
	" WHERE ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder AND ".
	" Risicoklassen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Transactietypes'] = "SELECT {Transactietypes} ".
	" FROM Transactietypes WHERE ".
	" Transactietypes.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Valutakoersen'] = "SELECT {Valutakoersen} ".
	" FROM Valutakoersen ".
	" LEFT JOIN ValutasPerBedrijf ON ValutasPerBedrijf.Valuta = Valutakoersen.Valuta AND ValutasPerBedrijf.Bedrijf = '{Bedrijf}' ".
	" WHERE  ".
	" ( Valutakoersen.Valuta {valutaQuery} ) AND ".
	" (ValutasPerBedrijf.Bedrijf IS NULL OR Valutakoersen.change_date >= '{lastUpdate}' ) {add_date_filter}";//.	" GROUP BY Valutakoersen.Datum, Valutakoersen.Valuta ";

$exportQuery['Valutas'] = "SELECT {Valutas} ".
	" FROM Valutas ".
	" WHERE Valutas.change_date >= '{lastUpdate}' {add_date_filter}";
//	" LEFT JOIN ValutasPerBedrijf ON ValutasPerBedrijf.Valuta = Valutas.Valuta AND ValutasPerBedrijf.Bedrijf = '{Bedrijf}' ".
//	" WHERE  ".
//	"  Valutas.Valuta {valutaQuery} AND ".
//	" (ValutasPerBedrijf.Bedrijf IS NULL OR Valutas.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['Vermogensbeheerders'] = "SELECT {Vermogensbeheerders} ".
	" FROM Vermogensbeheerders, VermogensbeheerdersPerBedrijf ".
	" WHERE ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND ".
	" Vermogensbeheerders.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['VermogensbeheerdersPerBedrijf'] = "SELECT {VermogensbeheerdersPerBedrijf} ".
	" FROM VermogensbeheerdersPerBedrijf ".
	" WHERE ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.change_date >= '{lastUpdate}' {add_date_filter}";
/*
  $exportQuery['VermogensbeheerdersPerGebruiker'] = "SELECT {VermogensbeheerdersPerGebruiker} ".
	"FROM VermogensbeheerdersPerGebruiker, VermogensbeheerdersPerBedrijf ".
	"WHERE ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ".
	"VermogensbeheerdersPerGebruiker.change_date >= '{lastUpdate}' {add_date_filter}";
*/
//$exportQuery['Vertalingen'] = "SELECT {Vertalingen} ".
//	"FROM Vertalingen WHERE ".
//	"Vertalingen.change_date >= '{lastUpdate}' ";

$exportQuery['ZorgplichtPerFonds'] = "SELECT {ZorgplichtPerFonds} ".
	" FROM ZorgplichtPerFonds, VermogensbeheerdersPerBedrijf ".
	" WHERE  ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = ZorgplichtPerFonds.Vermogensbeheerder AND ".
	" ZorgplichtPerFonds.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['ZorgplichtPerPortefeuille'] = "SELECT {ZorgplichtPerPortefeuille} ".
	" FROM ZorgplichtPerPortefeuille, VermogensbeheerdersPerBedrijf ".
	" WHERE  ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = ZorgplichtPerPortefeuille.Vermogensbeheerder AND ".
	" ZorgplichtPerPortefeuille.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Zorgplichtcategorien'] = "SELECT {Zorgplichtcategorien} ".
	"FROM Zorgplichtcategorien, VermogensbeheerdersPerBedrijf ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Zorgplichtcategorien.Vermogensbeheerder AND ".
	"Zorgplichtcategorien.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Regios'] = "SELECT {Regios} FROM Regios WHERE 1 {RegiosQuery} {add_date_filter}";

$exportQuery['ModelPortefeuilles'] = "SELECT {ModelPortefeuilles}
	FROM ModelPortefeuilles, Portefeuilles, VermogensbeheerdersPerBedrijf
	WHERE
	ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille AND
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
	ModelPortefeuilles.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['ValutaPerRegio'] = "SELECT {ValutaPerRegio} ".
	" FROM ValutaPerRegio, VermogensbeheerdersPerBedrijf ".
	" WHERE  ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = ValutaPerRegio.Vermogensbeheerder AND ".
	" ValutaPerRegio.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['AttributieCategorien'] = "SELECT {AttributieCategorien} FROM AttributieCategorien WHERE 1 {AttributieCategorienQuery} {add_date_filter}";

$exportQuery['AttributiePerGrootboekrekening'] = "SELECT {AttributiePerGrootboekrekening} ".
	" FROM AttributiePerGrootboekrekening, VermogensbeheerdersPerBedrijf ".
	" WHERE  ".
	" VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" VermogensbeheerdersPerBedrijf.Vermogensbeheerder = AttributiePerGrootboekrekening.Vermogensbeheerder AND ".
	" AttributiePerGrootboekrekening.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['DepositoRentepercentages'] = "SELECT {DepositoRentepercentages} ".
	"FROM DepositoRentepercentages, Rekeningen,  Portefeuilles, VermogensbeheerdersPerBedrijf  ".
	"WHERE ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0 AND ".
	"Rekeningen.Rekening = DepositoRentepercentages.Rekening AND Rekeningen.consolidatie=0 AND ".
	"DepositoRentepercentages.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['RapportBuilderQueryAirs'] = "SELECT {RapportBuilderQueryAirs} ".
	"FROM RapportBuilderQueryAirs WHERE ".
	"RapportBuilderQueryAirs.change_date >= '{lastUpdate}' ";

$exportQuery['HistorischePortefeuilleIndex'] = "SELECT {HistorischePortefeuilleIndex}
	FROM HistorischePortefeuilleIndex,   Portefeuilles, VermogensbeheerdersPerBedrijf
	WHERE
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
	HistorischePortefeuilleIndex.Portefeuille = Portefeuilles.Portefeuille AND
	HistorischePortefeuilleIndex.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['BbLandcodes'] = "SELECT {BbLandcodes} ".
	"FROM BbLandcodes WHERE ".
	"BbLandcodes.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['AutoRun'] = "SELECT {AutoRun}
	FROM AutoRun, VermogensbeheerdersPerBedrijf
	WHERE
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = AutoRun.Vermogensbeheerder AND
	AutoRun.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Remisiers'] = "SELECT {Remisiers}
	FROM Remisiers, VermogensbeheerdersPerBedrijf
	WHERE
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Remisiers.Vermogensbeheerder AND
	Remisiers.change_date >= '{lastUpdate}'	{add_date_filter}";

/*
$exportQuery[GeconsolideerdePortefeuilles] = "SELECT {GeconsolideerdePortefeuilles}
	FROM GeconsolideerdePortefeuilles, VermogensbeheerdersPerBedrijf
	WHERE
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = GeconsolideerdePortefeuilles.Vermogensbeheerder AND
	GeconsolideerdePortefeuilles.change_date >= '{lastUpdate}' ";
*/

$exportQuery['Beleggingsplan'] = "SELECT {Beleggingsplan}
	FROM  Beleggingsplan, Portefeuilles, VermogensbeheerdersPerBedrijf
	WHERE
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
	Beleggingsplan.Portefeuille = Portefeuilles.Portefeuille AND
	Beleggingsplan.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Bewaarders'] =
"SELECT {Bewaarders} FROM Bewaarders
	 WHERE Bewaarder {bewaarderQuery} {add_date_filter}"; //AND Bewaarders.change_date >= '{lastUpdate}'

$exportQuery['Schaduwkoersen'] = "SELECT {Schaduwkoersen} ".
	" FROM (Schaduwkoersen, tmpFondsenPerBedrijf) ".
	" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
	" WHERE ".
	" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" tmpFondsenPerBedrijf.Fonds = Schaduwkoersen.Fonds AND ".
	" {jaarInQuery} ".
	" (FondsenPerBedrijf.Bedrijf IS NULL OR Schaduwkoersen.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['FondsenBuitenBeheerfee'] = "SELECT {FondsenBuitenBeheerfee} ".
	"FROM FondsenBuitenBeheerfee, VermogensbeheerdersPerBedrijf ".
	"WHERE  ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = FondsenBuitenBeheerfee.Vermogensbeheerder AND ".
	"FondsenBuitenBeheerfee.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['FondsOmschrijvingVanaf'] = "SELECT {FondsOmschrijvingVanaf} ".
	" FROM (FondsOmschrijvingVanaf, tmpFondsenPerBedrijf) ".
	" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
	" WHERE ".
	" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" tmpFondsenPerBedrijf.Fonds = FondsOmschrijvingVanaf.Fonds AND ".
	" (FondsenPerBedrijf.Bedrijf IS NULL OR FondsOmschrijvingVanaf.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['ZorgplichtPerRisicoklasse'] = "SELECT {ZorgplichtPerRisicoklasse} FROM ZorgplichtPerRisicoklasse, VermogensbeheerdersPerBedrijf WHERE ".
	 " ZorgplichtPerRisicoklasse.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " ZorgplichtPerRisicoklasse.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['ZorgplichtPerBeleggingscategorie'] = "SELECT {ZorgplichtPerBeleggingscategorie} FROM ZorgplichtPerBeleggingscategorie, VermogensbeheerdersPerBedrijf WHERE ".
	 " ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " ZorgplichtPerBeleggingscategorie.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['KeuzePerVermogensbeheerder'] = "SELECT {KeuzePerVermogensbeheerder} FROM KeuzePerVermogensbeheerder, VermogensbeheerdersPerBedrijf WHERE
	KeuzePerVermogensbeheerder.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND
	 KeuzePerVermogensbeheerder.change_date >= '{lastUpdate}' AND
	 VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['IndexPerBeleggingscategorie'] = "SELECT {IndexPerBeleggingscategorie} FROM IndexPerBeleggingscategorie
  JOIN VermogensbeheerdersPerBedrijf ON  IndexPerBeleggingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
  WHERE IndexPerBeleggingscategorie.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['ReferentieportefeuillePerBeleggingscategorie'] = "SELECT {ReferentieportefeuillePerBeleggingscategorie} FROM ReferentieportefeuillePerBeleggingscategorie
  JOIN VermogensbeheerdersPerBedrijf ON  ReferentieportefeuillePerBeleggingscategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
  WHERE ReferentieportefeuillePerBeleggingscategorie.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['PositieLijst'] = "SELECT {PositieLijst} FROM PositieLijst
  JOIN VermogensbeheerdersPerBedrijf ON  PositieLijst.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
  WHERE PositieLijst.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

/*
$exportQuery['ModelPortefeuilleFixed'] = "SELECT {ModelPortefeuilleFixed}
	FROM ModelPortefeuilleFixed, Portefeuilles, VermogensbeheerdersPerBedrijf
	WHERE
	ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille AND
	VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND
	VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
	ModelPortefeuilleFixed.change_date >= '{lastUpdate}' {add_date_filter}";
*/

$exportQuery['emittenten'] = "SELECT {emittenten} FROM emittenten WHERE  emittenten.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['emittentPerFonds'] = "SELECT {emittentPerFonds} FROM emittentPerFonds, VermogensbeheerdersPerBedrijf WHERE ".
	 " emittentPerFonds.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder AND ".
	 " emittentPerFonds.change_date >= '{lastUpdate}' AND ".
	 " VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['benchmarkverdeling'] = "SELECT {benchmarkverdeling} ".
	" FROM (benchmarkverdeling, tmpFondsenPerBedrijf) ".
	" LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
	" WHERE  ".
	" tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	" tmpFondsenPerBedrijf.Fonds = benchmarkverdeling.`benchmark` AND ".
	" (FondsenPerBedrijf.Bedrijf IS NULL OR benchmarkverdeling.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['benchmarkverdelingVanaf'] = "SELECT {benchmarkverdelingVanaf} ".
  " FROM (benchmarkverdelingVanaf, tmpFondsenPerBedrijf) ".
  " LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' ".
  " WHERE  ".
  " tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
  " tmpFondsenPerBedrijf.Fonds = benchmarkverdelingVanaf.`benchmark` AND ".
  " (FondsenPerBedrijf.Bedrijf IS NULL OR benchmarkverdelingVanaf.change_date >= '{lastUpdate}') {add_date_filter}";

/*
$exportQuery['historischeTenaamstelling'] = "SELECT {historischeTenaamstelling} FROM  historischeTenaamstelling
JOIN Clienten ON Clienten.id=historischeTenaamstelling.clientId
JOIN Portefeuilles ON  Portefeuilles.Client= Clienten.Client
JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE
VermogensbeheerdersPerBedrijf.Bedrijf='{Bedrijf}' AND
historischeTenaamstelling.change_date >='{lastUpdate}' {add_date_filter}";
*/

$exportQuery['updateInformatie'] = "SELECT {updateInformatie} FROM updateInformatie WHERE  updateInformatie.publiceer = 1 AND updateInformatie.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Rating'] = "SELECT {Rating} FROM Rating WHERE  Rating.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['EigendomPerPortefeuille'] = "SELECT {EigendomPerPortefeuille} FROM EigendomPerPortefeuille
Inner Join Portefeuilles ON EigendomPerPortefeuille.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  EigendomPerPortefeuille.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['Eigenaars'] = "SELECT {Eigenaars} FROM Eigenaars
WHERE  Eigenaars.change_date >= '{lastUpdate}' {add_date_filter}  AND Eigenaars.Eigenaar {eigenaarsQuery}";

$exportQuery['NormwegingPerBeleggingscategorie'] = "SELECT {NormwegingPerBeleggingscategorie} ".
	"FROM NormwegingPerBeleggingscategorie, Portefeuilles, VermogensbeheerdersPerBedrijf ".
	"WHERE ".
	"VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ".
	"VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
	"NormwegingPerBeleggingscategorie.Portefeuille = Portefeuilles.Portefeuille AND ".
	"NormwegingPerBeleggingscategorie.change_date >= '{lastUpdate}' {add_date_filter}";
  
$exportQuery['GeconsolideerdePortefeuilles'] = "SELECT {GeconsolideerdePortefeuilles} FROM GeconsolideerdePortefeuilles
Inner Join VermogensbeheerdersPerBedrijf ON GeconsolideerdePortefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  GeconsolideerdePortefeuilles.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";  

$exportQuery['orderkosten']="SELECT {orderkosten} FROM orderkosten
JOIN VermogensbeheerdersPerBedrijf ON orderkosten.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE orderkosten.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['grootboeknummers']="SELECT {grootboeknummers} FROM grootboeknummers
JOIN VermogensbeheerdersPerBedrijf ON grootboeknummers.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE grootboeknummers.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['StandaarddeviatiePerRisicoklasse'] = "SELECT {StandaarddeviatiePerRisicoklasse} FROM StandaarddeviatiePerRisicoklasse
Inner Join VermogensbeheerdersPerBedrijf ON StandaarddeviatiePerRisicoklasse.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  StandaarddeviatiePerRisicoklasse.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['NormPerRisicoprofiel'] = "SELECT {NormPerRisicoprofiel} FROM NormPerRisicoprofiel
Inner Join VermogensbeheerdersPerBedrijf ON NormPerRisicoprofiel.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  NormPerRisicoprofiel.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['StandaarddeviatiePerPortefeuille'] = "SELECT {StandaarddeviatiePerPortefeuille} FROM StandaarddeviatiePerPortefeuille
Inner Join VermogensbeheerdersPerBedrijf ON StandaarddeviatiePerPortefeuille.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  StandaarddeviatiePerPortefeuille.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['scenariosPerVermogensbeheerder'] = "SELECT {scenariosPerVermogensbeheerder} FROM scenariosPerVermogensbeheerder
Inner Join VermogensbeheerdersPerBedrijf ON scenariosPerVermogensbeheerder.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE scenariosPerVermogensbeheerder.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['Beurzen'] = "SELECT {Beurzen} FROM Beurzen WHERE Beurzen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['IndexPerAttributieCategorie'] = "SELECT {IndexPerAttributieCategorie} FROM IndexPerAttributieCategorie
Inner Join VermogensbeheerdersPerBedrijf ON IndexPerAttributieCategorie.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  IndexPerAttributieCategorie.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['BICcodes'] = "SELECT {BICcodes} FROM BICcodes
Inner Join VermogensbeheerdersPerBedrijf ON BICcodes.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  BICcodes.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['PSAFperFonds'] = "SELECT {PSAFperFonds} FROM PSAFperFonds
Inner Join VermogensbeheerdersPerBedrijf ON PSAFperFonds.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  PSAFperFonds.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['fixDepotbankenPerVermogensbeheerder'] = "SELECT {fixDepotbankenPerVermogensbeheerder} FROM fixDepotbankenPerVermogensbeheerder
Inner Join VermogensbeheerdersPerBedrijf ON fixDepotbankenPerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  fixDepotbankenPerVermogensbeheerder.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}"; 

$exportQuery['SoortOvereenkomsten'] = "SELECT {SoortOvereenkomsten}	FROM SoortOvereenkomsten 
WHERE (1 {SoortOvereenkomstenQuery} AND SoortOvereenkomsten.change_date >= '{lastUpdate}') OR
 SoortOvereenkomsten.SoortOvereenkomst IN(SELECT waarde FROM KeuzePerVermogensbeheerder WHERE
 KeuzePerVermogensbeheerder.categorie = 'SoortOvereenkomsten' 
AND KeuzePerVermogensbeheerder.vermogensbeheerder {vermogensbeheerderQuery} AND KeuzePerVermogensbeheerder.change_date > '{lastUpdate}' GROUP BY waarde)";

$exportQuery['ModelPortefeuillesPerPortefeuille']="SELECT {ModelPortefeuillesPerPortefeuille}
FROM
ModelPortefeuillesPerPortefeuille WHERE 1 {PortefeuillesQuery} AND ModelPortefeuillesPerPortefeuille.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Bedrijfsgegevens'] = "SELECT {Bedrijfsgegevens}	FROM Bedrijfsgegevens WHERE Bedrijfsgegevens.Bedrijf = '{Bedrijf}' AND 
Bedrijfsgegevens.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['fixDepotbankenPerVermogensbeheerder'] = "SELECT {fixDepotbankenPerVermogensbeheerder}	FROM fixDepotbankenPerVermogensbeheerder
 Inner Join VermogensbeheerdersPerBedrijf ON fixDepotbankenPerVermogensbeheerder.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
 WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND 
 fixDepotbankenPerVermogensbeheerder.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['fondskosten'] = "SELECT {fondskosten}  FROM (fondskosten, tmpFondsenPerBedrijf) 
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' 
	 WHERE 
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND 
	 tmpFondsenPerBedrijf.Fonds = fondskosten.Fonds AND 
	 (FondsenPerBedrijf.Bedrijf IS NULL OR fondskosten.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['Orderredenen'] = "SELECT {Orderredenen} FROM Orderredenen WHERE Orderredenen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['Brokerinstructies'] = "SELECT {Brokerinstructies} FROM Brokerinstructies
Inner Join VermogensbeheerdersPerBedrijf ON Brokerinstructies.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  Brokerinstructies.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['Rendementsheffing'] = "SELECT {Rendementsheffing} FROM Rendementsheffing WHERE Rendementsheffing.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['handleidingenAIRS'] = "SELECT {handleidingenAIRS} FROM handleidingenAIRS WHERE  handleidingenAIRS.publiceer = 1 AND handleidingenAIRS.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['fondsOptieSymbolen'] = "SELECT {fondsOptieSymbolen} FROM fondsOptieSymbolen WHERE  fondsOptieSymbolen.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['PortefeuilleHistorischeParameters'] = "SELECT {PortefeuilleHistorischeParameters} FROM PortefeuilleHistorischeParameters
INNER JOIN Portefeuilles ON PortefeuilleHistorischeParameters.portefeuille = Portefeuilles.Portefeuille
INNER JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf='{Bedrijf}' AND PortefeuilleHistorischeParameters.change_date >= '{lastUpdate}' ";

$exportQuery['contractueleUitsluitingen'] = "SELECT {contractueleUitsluitingen} FROM contractueleUitsluitingen
Inner Join VermogensbeheerdersPerBedrijf ON contractueleUitsluitingen.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  contractueleUitsluitingen.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['portefeuilleClusters'] = "SELECT {portefeuilleClusters} FROM portefeuilleClusters
Inner Join VermogensbeheerdersPerBedrijf ON portefeuilleClusters.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  portefeuilleClusters.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";


$exportQuery['doorkijk_categoriePerVermogensbeheerder'] = "SELECT {doorkijk_categoriePerVermogensbeheerder} FROM doorkijk_categoriePerVermogensbeheerder
Inner Join VermogensbeheerdersPerBedrijf ON doorkijk_categoriePerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  doorkijk_categoriePerVermogensbeheerder.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['doorkijk_koppelingPerVermogensbeheerder'] = "SELECT {doorkijk_koppelingPerVermogensbeheerder} FROM doorkijk_koppelingPerVermogensbeheerder
Inner Join VermogensbeheerdersPerBedrijf ON doorkijk_koppelingPerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  doorkijk_koppelingPerVermogensbeheerder.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['doorkijk_categorieWegingenPerFonds'] = "SELECT {doorkijk_categorieWegingenPerFonds} FROM doorkijk_categorieWegingenPerFonds
JOIN tmpFondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds = doorkijk_categorieWegingenPerFonds.Fonds 
LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' 
WHERE  tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND  
(FondsenPerBedrijf.Bedrijf IS NULL OR doorkijk_categorieWegingenPerFonds.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['doorkijk_msCategoriesoort'] = "SELECT {doorkijk_msCategoriesoort} FROM doorkijk_msCategoriesoort WHERE doorkijk_msCategoriesoort.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['inflatiepercentages'] = "SELECT {inflatiepercentages} FROM inflatiepercentages
Inner Join VermogensbeheerdersPerBedrijf ON inflatiepercentages.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  inflatiepercentages.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['klantMutaties'] = "SELECT {klantMutaties} FROM klantMutaties
Inner Join VermogensbeheerdersPerBedrijf ON klantMutaties.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  klantMutaties.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['FondsenEMTdata'] = "SELECT {FondsenEMTdata}  FROM (FondsenEMTdata, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = FondsenEMTdata.Fonds AND
	 (FondsenPerBedrijf.Bedrijf IS NULL OR FondsenEMTdata.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['fondsenOptiestatistieken'] = "SELECT {fondsenOptiestatistieken}
	 FROM (fondsenOptiestatistieken, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON tmpFondsenPerBedrijf.Fonds=FondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = fondsenOptiestatistieken.Fonds AND
	 (FondsenPerBedrijf.Bedrijf IS NULL OR fondsenOptiestatistieken.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['ISOLanden'] = "SELECT {ISOLanden} FROM ISOLanden WHERE ISOLanden.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['begrippenCategorie'] = "SELECT {begrippenCategorie} FROM begrippenCategorie
Inner Join VermogensbeheerdersPerBedrijf ON begrippenCategorie.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  begrippenCategorie.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['begrippenRapport'] = "SELECT {begrippenRapport} FROM begrippenRapport
Inner Join VermogensbeheerdersPerBedrijf ON begrippenRapport.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE  begrippenRapport.change_date >= '{lastUpdate}' AND VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' {add_date_filter}";

$exportQuery['FondsParameterHistorie'] = "SELECT {FondsParameterHistorie}  FROM (FondsParameterHistorie, tmpFondsenPerBedrijf) 
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}' 
	 WHERE 
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND 
	 tmpFondsenPerBedrijf.Fonds = FondsParameterHistorie.Fonds AND 
	 (FondsenPerBedrijf.Bedrijf IS NULL OR FondsParameterHistorie.change_date >= '{lastUpdate}') {add_date_filter}";


$exportQuery['modelPortefeuillesPerModelPortefeuille'] = "SELECT {modelPortefeuillesPerModelPortefeuille}
FROM
modelPortefeuillesPerModelPortefeuille
JOIN Portefeuilles ON modelPortefeuillesPerModelPortefeuille.Modelportefeuille=Portefeuilles.Portefeuille
JOIN VermogensbeheerdersPerBedrijf ON Portefeuilles.vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND modelPortefeuillesPerModelPortefeuille.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['toelichtingStortOnttr'] = "SELECT {toelichtingStortOnttr}	FROM toelichtingStortOnttr
WHERE toelichtingStortOnttr.toelichting IN(SELECT waarde FROM KeuzePerVermogensbeheerder WHERE
 KeuzePerVermogensbeheerder.categorie = 'toelichtingStortOnttr'
AND KeuzePerVermogensbeheerder.vermogensbeheerder {vermogensbeheerderQuery} AND KeuzePerVermogensbeheerder.change_date > '{lastUpdate}' GROUP BY waarde)";

$exportQuery['uitsluitingenModelcontrole']="SELECT {uitsluitingenModelcontrole}
FROM
uitsluitingenModelcontrole WHERE 1 {PortefeuillesQuery} AND uitsluitingenModelcontrole.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['FondsenFundInformatie'] = "SELECT {FondsenFundInformatie}  FROM (FondsenFundInformatie, tmpFondsenPerBedrijf)
	 LEFT JOIN FondsenPerBedrijf ON FondsenPerBedrijf.Fonds = tmpFondsenPerBedrijf.Fonds AND FondsenPerBedrijf.Bedrijf = '{Bedrijf}'
	 WHERE
	 tmpFondsenPerBedrijf.Bedrijf = '{Bedrijf}' AND
	 tmpFondsenPerBedrijf.Fonds = FondsenFundInformatie.fonds AND
	 (FondsenPerBedrijf.Bedrijf IS NULL OR FondsenFundInformatie.change_date >= '{lastUpdate}') {add_date_filter}";

$exportQuery['PortefeuillesGeconsolideerd']="SELECT {PortefeuillesGeconsolideerd}
FROM
PortefeuillesGeconsolideerd WHERE 1 {PortefeuillesQuery} AND PortefeuillesGeconsolideerd.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['appVertaling']="SELECT {appVertaling}
FROM
appVertaling WHERE  appVertaling.change_date >= '{lastUpdate}' {add_date_filter}";

/*
$exportQuery['afmCategorien'] = "SELECT afmCategorien.id,afmCategorien.afmCategorie, afmCategorien.afmCategorie, afmCategorien.omschrijving, afmCategorien.correlatie, 
afmCategorien.Afdrukvolgorde, afmCategorien.standaarddeviatieMin, afmCategorien.standaarddeviatieMax, afmCategorien.add_date, 
afmCategorien.add_user, afmCategorien.change_date, afmCategorien.change_user 
FROM afmCategorien WHERE afmCategorien.change_date >= '{lastUpdate}'  {add_date_filter}";
*/
//$exportQuery['externeQueries'] = "SELECT {externeQueries} FROM externeQueries WHERE externeQueries.homeOnly=0 AND externeQueries.change_date >= '{lastUpdate}' {add_date_filter}";

$exportQuery['ParametersPerVermogensbeheerder'] = "SELECT {ParametersPerVermogensbeheerder}
FROM
ParametersPerVermogensbeheerder
JOIN VermogensbeheerdersPerBedrijf ON ParametersPerVermogensbeheerder.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '{Bedrijf}' AND ParametersPerVermogensbeheerder.change_date >= '{lastUpdate}' {add_date_filter}";


?>