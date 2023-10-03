<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/12/19 08:29:17 $
 		File Versie					: $Revision: 1.5 $

 		$Log: ATTberekening_L37.php,v $
 		Revision 1.5  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/07/08 07:03:49  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/06/13 14:35:27  rvv
 		*** empty log message ***

 		Revision 1.2  2012/05/12 15:11:00  rvv
 		*** empty log message ***

 		Revision 1.1  2012/05/02 15:53:13  rvv
 		*** empty log message ***

 		Revision 1.4  2012/03/18 16:08:24  rvv
 		*** empty log message ***

 */


class ATTberekening_L37
{

	function ATTberekening_L37($rapportData)
	{
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
	  $this->indexPerformance=false;
	}



	function bereken($van,$tot,$valuta='EUR',$stapeling='categorie')
	{
	  global $__appvar;
 		$DB=new DB();
		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
			$DB->SQL($query);
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    if($data['Hoofdcategorie']=='')
		      $data['Hoofdcategorie']='geen-Hcat';

		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['Hoofdcategorie']=$data['Hoofdcategorie'];
		    $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $alleData['fondsen'][]=$data['Fonds'];
		  }


		$query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		    if($data['Hoofdcategorie']=='')
		        $data['Hoofdcategorie']='geen-Hcat';
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

	  $alleData['Hoofdcategorie']='totaal';
    $perfTotaal=$this->fondsPerformance($alleData,$van,$tot,'maanden',true,$valuta,'totaal');

    if($stapeling=='categorie')
      foreach ($perCategorie as $categorie=>$categorieData)
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,'maanden',false,$valuta,$categorie);
		elseif($stapeling=='hoofdcategorie')
      foreach ($perHoofdcategorie as $categorie=>$categorieData)
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,'maanden',false,$valuta,$categorie);

		$perfData['totaal']=$perfTotaal;
    return $perfData;
	}

	function fondsPerformance($fondsData,$van,$tot,$stapeling='',$totaal=false,$valuta='EUR',$catnaam='leeg')
  {
    global $__appvar;
    if($stapeling=='maanden')
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    else
      $perioden[]=array('start'=>$van,'stop'=>$tot);


    global $__appvar;
	  $DB=new DB();

    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
         $query ="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$rapDatum' ".$__appvar['TijdelijkeRapportageMaakUniek'];
         if(!$DB->QRecords($query))
         {
           if(substr($rapDatum,0,5)=='01-01')
             $startJaar=1;
           else
             $startJaar=0;
	         $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
	         vulTijdelijkeTabel($fondswaarden ,$this->rapport->portefeuille,$rapDatum);
         }
      }
    }


    foreach ($perioden as $periode)
    {
      $datumBegin=$periode['start'];
      if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
        $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
      else
        $weegDatum=$datumBegin;
      $datumEind=$periode['stop'];

      if(!$fondsData['fondsen'])
        $fondsData['fondsen']=array('geen');
      if(!$fondsData['rekeningen'])
        $fondsData['rekeningen']=array('geen');

      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";

      if ($valuta <> 'EUR')
      {
	      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	      $startValutaKoers= getValutaKoers($valuta,$datumBegin);
	      $eindValutaKoers= getValutaKoers($valuta,$datumEind);
      }
	    else
	    {
	      $koersQuery = "";
	      $startValutaKoers= 1;
	      $eindValutaKoers= 1;
	    }

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
                 ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	    $DB->SQL($query);
	    $DB->Query();
	    $start = $DB->NextRecord();
	    $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];

	  $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro  FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin'".$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
	  $DB->Query();
	  $start = $DB->NextRecord();
	  $totaalBeginwaarde = $start['actuelePortefeuilleWaardeEuro'];

   	$query = "SELECT ".
	    "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	    "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	    "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	    "WHERE ".
      "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	    "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	    "Rekeningmutaties.Verwerkt = '1' AND ".
	    "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	    "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	    "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal1'];


	    if(!isset($this->totalen[$datumEind]['gemiddeldeWaarde']))
	    {
	      $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))$koersQuery AS totaal
	      FROM Rekeningen
	      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	      JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	      WHERE
        Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	      Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
        $DB->SQL($query);
        $DB->Query();
        $weging = $DB->NextRecord();
        $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal'];
        $this->totalen[$datumEind]['gemiddeldeWaarde']=$totaalGemiddelde;
	    }

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro))/$startValutaKoers as beginWaardeNew
             FROM TijdelijkeRapportage
             WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	    $DB->SQL($query);
	    $DB->Query();
	    $eind = $DB->NextRecord();
	    $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	    $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];

	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQuery AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	    $DB->SQL($queryAttributieStortingenOntrekkingenRekening); //echo $queryAttributieStortingenOntrekkingenRekening."";exit
	    $DB->Query();
	    $AttributieStortingenOntrekkingenRekening = $DB->NextRecord(); // listarray($AttributieStortingenOntrekkingenRekening);

	    $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQuery AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)$koersQuery  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query();
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();


      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0))$koersQuery as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0))$koersQuery as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0))$koersQuery as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
      $DB->SQL($queryFondsDirecteKostenOpbrengsten);
      $DB->Query();
      $FondsDirecteKostenOpbrengsten = $DB->NextRecord(); //echo "$queryFondsDirecteKostenOpbrengsten <br><br>\n";

	    $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningmutaties.Grootboekrekening = 'FONDS' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere ";//
      $DB->SQL($queryAttributieStortingenOntrekkingen);
      $DB->Query();
      $AttributieStortingenOntrekkingen = $DB->NextRecord();
      //listarray($AttributieStortingenOntrekkingen);

	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
	    $DB->SQL($query);//echo "$query <br><br>\n";
	    $DB->Query();
	    $data = $DB->nextRecord();
	    $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	    $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	    $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];

      $queryKostenOpbrengsten = "SELECT SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)$koersQuery) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)$koersQuery) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten=1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
	    $DB->SQL($queryKostenOpbrengsten);
	    $DB->Query();
	    $nietToegerekendeKosten = $DB->NextRecord();

	    $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
//echo  round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
//echo round($performance*100,2)."= ((($eindwaarde - $beginwaarde) - ". $AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde) <br>\n";

      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);

      $indexData=$this->indexPerformance($fondsData['Hoofdcategorie'],$datumBegin,$datumEind);

      if($totaal==true)
        $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;

      $weging=$gemiddelde/$totaalGemiddelde;//$this->totalen[$datumEind]['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;


      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=$overPerfPeriode*$weging;

 //  echo "$resultaat = ($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
//echo "$performance <br>\n $weging=$gemiddelde/".$this->totalen[$datumEind]['gemiddeldeWaarde']."; <br>\n $bijdrage=$resultaat/$gemiddelde*$weging <br>\n<br>\n";
      $waarden[$datumEind]=array(
  'indexPerf'=>$indexData['perf'],
  'indexBijdrage'=>$indexData['bijdrage'],
  'indexBijdrageWaarde'=>$indexData['percentage'],
  'overPerf'=>$overPerfPeriode,
  'relContrib'=>$relContrib,
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal'],
  'kostenNietGekoppeld'=>$nietToegerekendeKosten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage);
}


$stapelItems=array('procent','bijdrage','indexPerf','indexBijdrage','overPerf','relContrib');
$somItems=array('stort','stortEnOnttrekking','storting','onttrekking','kosten','kostenNietGekoppeld','resultaat','ongerealiseerd','gerealiseerd','opbrengst');
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

foreach ($waarden as $datum=>$waarde)
{
  $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  $waarden[$datum]['index']=$perfData['totaal']['procent']*100;
}
$this->waarden[$catnaam]=$waarden;
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
$perfData['totaal']['categorie']=$fondsData['categorie'];

foreach ($waarden as $datum=>$waarde)
{
  if($waarde['beginwaarde']=='')
    $waarde['beginwaarde']=0;

  if(!isset($perfData['totaal']['beginwaarde']))
    $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];

  $perfData['totaal']['eindwaarde']=$waarde['eindwaarde'];

  foreach ($somItems as $item)
    $perfData['totaal'][$item]+=$waarde[$item];
}
//listarray($FondsDirecteKostenOpbrengsten);

if($stapeling == true)
{
  $perfData['totaal']['perfWaarden']=$waarden;
  return $perfData['totaal'];
}
else
  return array(
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance*100,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage*100);
	}

	function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);

	  //  if(substr($datum[$i]['start'],5,5)=='12-31')
	  //  {
	  //    $datum[$i]['start']=(substr($datum[$i]['start'],0,4)+1).'-01-01';
	  //  }

       $i++;
	  }
	  return $datum;
  }

  function getTWRdagen($julBegin, $julEind)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='".$this->rapport->portefeuille."''  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."'
    GROUP BY Rekeningmutaties.Boekdatum
    ORDER BY Boekdatum";

    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$i=0;
	  while($mutaties = $DB->nextRecord())
		{
		  if($i>0)
		  {
		    $datum[$i]['start'] = $lastdatum;
		    $datum[$i]['stop']=$mutaties['datum'];
		  }
		  $lastdatum=$mutaties['datum'];
		  $i++;
		}
		return $datum;
  }

  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='',$valuta='EUR')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';

      if($valuta=='EUR')
        $koersQuery=",(SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";
      else $koersQuery=', 1 as Rapportagekoers ';


		$query = "SELECT Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers ".
		" $koersQuery ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND $fondsenWhere AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
		"Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

	  while($mutaties = $DB->nextRecord())
		{
			$mutaties['Aantal'] = abs($mutaties['Aantal']);
			$aankoop_koers = "";
			$aankoop_waardeinValuta = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waardeinValuta = "";
			$verkoop_waarde = "";
			$historisch_kostprijs = "";
			$resultaat_voorgaande = "";
			$resultaat_lopendeProcent = "";
			$resultaatlopende = 0 ;
      //$mutaties['Rapportagekoers']=1;

			switch($mutaties['Transactietype'])
			{
					case "A" :
					case "A/O" :
					case "A/S" :
 					case "D" :
					case "S" :
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];
						$totaal_aankoop_waarde += $t_aankoop_waarde;
						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $t_aankoop_koers;
						if($t_aankoop_waardeinValuta > 0)
							$aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $t_aankoop_waarde;
					break;
					case "B" :
						// Beginstorting
					break;
					case "L" :
					case "V" :
					case "V/O" :
					case "V/S" :
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];
						$totaal_verkoop_waarde += $t_verkoop_waarde;
						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $t_verkoop_koers;
						if($t_verkoop_waardeinValuta > 0)
							$verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $t_verkoop_waarde;
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}

			/*
				Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
			*/
			if($mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V" || $mutaties['Transactietype'] == "V/S" || $mutaties['Transactietype'] == "A/S")
			{
				$historie = berekenHistorischKostprijs($this->rapport->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->rapport->pdf->rapportageValuta);
				if($mutaties['Transactietype'] == "A/S")
				  $rekenAantal=($mutaties['Aantal'] * -1) ;
				else
				  $rekenAantal=$mutaties['Aantal'];

				$historischekostprijs = $rekenAantal       * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
				$beginditjaar         = $rekenAantal       * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];


        if($this->rapport->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->rapport->pdf->rapportageValuta)
        {
  		    $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
        }
        elseif ($this->rapport->pdf->rapportageValuta != 'EUR')
		    {
		      $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		      $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
		    }

				if($historie['voorgaandejarenActief'] == 0)
				{
					$resultaatvoorgaande = 0;
					$resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = 0;
						$resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
					}
				}
				else
				{
					$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
					$resultaatlopende = $t_verkoop_waarde - $beginditjaar;
					if($mutaties['Transactietype'] == "A/S")
					{
						$resultaatvoorgaande = $beginditjaar - $historischekostprijs;
						$resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
					}
				}
				$result_historischkostprijs = $historischekostprijs;
				$result_voorgaandejaren = $resultaatvoorgaande;
				$result_lopendejaar = $resultaatlopende;
				$totaal_resultaat_waarde += $resultaatlopende;
			}
			else
			{
				$result_historischkostprijs = 0;
				$result_voorgaandejaren = 0;
				$result_lopendejaar = 0;
			}
        //	listarray($mutaties);
				$data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
				$data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
		  	$data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
				$data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
				$data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
				$data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
				$data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
				$data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
				$data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];
		}
		return $data;
	}

	function indexPerformance($categorie,$van,$tot)
	{
	  global $__appvar;
    $DB = new DB();
	  if(!is_array($this->indexLookup) || count($this->indexLookup) < 1)
	  {
	    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $DB->SQL($query);
      $DB->Query();
      while($index=$DB->nextRecord())
        $this->indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];
      $this->indexLookup['totaal']=$this->rapport->pdf->portefeuilledata['SpecifiekeIndex'];
}

     if(!is_array($this->normData) || count($this->normData) < 1)
     {
       $this->normData['totaal']=100;
       $q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht,CategorienPerHoofdcategorie.Hoofdcategorie
       FROM
       ZorgplichtPerRisicoklasse
       Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
		   $DB->SQL($q);
		   $DB->Query();
		   while($data=$DB->nextRecord())
		     $this->normData[$data['Hoofdcategorie']]=$data['norm'];

      $q="SELECT
      ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
      CategorienPerHoofdcategorie.Hoofdcategorie,
      ZorgplichtPerPortefeuille.Zorgplicht,
      ZorgplichtPerPortefeuille.norm
      FROM ZorgplichtPerPortefeuille
      JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      ORDER by CategorienPerHoofdcategorie.Hoofdcategorie
      ";

		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		  $this->normData[$data['Hoofdcategorie']]=$data['norm'];
     }


	  $fonds=$this->indexLookup[$categorie];
	  /*
    $query="SELECT
    IndexPerBeleggingscategorie.Fonds,
    ModelPortefeuilleFixed.Percentage / 100 as Percentage
    FROM IndexPerBeleggingscategorie LEFT Join ModelPortefeuilleFixed ON IndexPerBeleggingscategorie.Beleggingscategorie = ModelPortefeuilleFixed.Fonds
    WHERE Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND Beleggingscategorie='$categorie' ";
	  $DB->SQL($query); //echo " $query <br><br>\n";
	  $fondsData=$DB->lookupRecord();
	  $fonds=$fondsData['Fonds'];
	  */
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
  	$DB->SQL($query);
    $startKoers=$DB->lookupRecord();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
    $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$fondsData['Percentage'],
                'datum'=>$tot,
                'percentage'=>($this->normData[$categorie]/100),//$fondsData['Percentage']
                'categorie'=>$categorie,
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);

    return $tmp;
  }


}

?>