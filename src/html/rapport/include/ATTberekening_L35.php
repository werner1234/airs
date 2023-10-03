<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
 		File Versie					: $Revision: 1.28 $

 		$Log: ATTberekening_L35.php,v $
 		Revision 1.28  2019/11/20 16:19:15  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2019/11/17 17:58:42  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/11/13 14:49:22  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/10/19 16:45:25  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/10/16 15:23:48  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/10/14 17:27:54  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/04/12 08:33:15  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/01/27 17:08:53  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/05/31 10:15:24  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/05/27 11:57:58  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/04/05 15:33:48  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/01/08 16:52:37  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/12/23 16:43:00  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/10/26 15:42:47  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/07/20 16:26:07  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/07/17 15:53:14  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/04/03 14:58:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/12/05 16:45:29  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/07/08 19:29:46  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/06/09 13:43:40  rvv
 		*** empty log message ***

 		Revision 1.6  2012/05/09 18:47:45  rvv
 		*** empty log message ***

 		Revision 1.5  2012/05/02 15:53:13  rvv
 		*** empty log message ***

 		Revision 1.4  2012/03/18 16:08:24  rvv
 		*** empty log message ***

 		Revision 1.3  2012/03/17 11:58:16  rvv
 		*** empty log message ***

 		Revision 1.2  2012/03/14 17:30:11  rvv
 		*** empty log message ***

 		Revision 1.1  2012/03/04 11:39:58  rvv
 		*** empty log message ***

 		Revision 1.5  2011/10/02 08:37:20  rvv
 		*** empty log message ***

 		Revision 1.4  2011/09/25 16:23:28  rvv
 		*** empty log message ***

 		Revision 1.3  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/05/22 11:47:35  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/08 09:36:52  rvv
 		*** empty log message ***

 		Revision 1.3  2011/04/19 16:42:01  rvv
 		*** empty log message ***

 		Revision 1.2  2011/03/30 20:17:54  rvv
 		*** empty log message ***

 		Revision 1.1  2011/03/26 16:49:09  rvv
 		*** empty log message ***

 		Revision 1.15  2011/02/24 08:53:12  rvv

 */


class ATTberekening_L35
{

	function ATTberekening_L35($rapportData)
	{
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
	  $this->indexPerformance=false;
	}



	function bereken($van,$tot,$valuta='EUR',$stapeling='categorie',$periode='maanden')
	{
	  $this->rapport_jaar =substr($van,0,4);
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
      $perHoofdcategorie[$data['Hoofdcategorie']]['Hoofdcategorie']=$data['Hoofdcategorie'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

	  $alleData['Hoofdcategorie']='totaal';
    $perfTotaal=$this->fondsPerformance($alleData,$van,$tot,$periode,true,$valuta,'totaal');

    if($stapeling=='categorie')
      foreach ($perCategorie as $categorie=>$categorieData)
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,$periode,false,$valuta,$categorie);
		elseif($stapeling=='hoofdcategorie')
      foreach ($perHoofdcategorie as $categorie=>$categorieData)
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,$periode,false,$valuta,$categorie);

		$perfData['totaal']=$perfTotaal;
    return $perfData;
	}

	function fondsPerformance($fondsData,$van,$tot,$stapeling='',$totaal=false,$valuta='EUR',$catnaam='leeg')
  { 
    global $__appvar;
    if($stapeling=='maanden')
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    elseif($stapeling=='weken')
      $perioden=$this->getWeken(db2jul($van),db2jul($tot));
    elseif($stapeling=='wekenVrijdag')  
      $perioden=$this->getWeken(db2jul($van),db2jul($tot),true);  
    elseif($stapeling=='dagen')
      $perioden=$this->getDagen(db2jul($van),db2jul($tot));  
    else
      $perioden[]=array('start'=>$van,'stop'=>$tot);


    global $__appvar;
	  $DB=new DB();

    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
         $query ="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$rapDatum' ".$__appvar['TijdelijkeRapportageMaakUniek'];
         if($DB->QRecords($query) < 1)
         {
           if(substr($rapDatum,5,5)=='01-01')
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
  
      if($catnaam=='totaal')
      {
  
        $query="SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1";
        $DB->SQL($query);
        $DB->Query();
        $grootboekrekeningen=array();
        while($grootboekrekening=$DB->nextRecord())
          $grootboekrekeningen[]=$grootboekrekening['Grootboekrekening'];
        
        $query = "SELECT ".
          "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
          "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
          "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
          "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal
      ".
          "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
          "WHERE ".
          "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
          "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
          "Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
          "Rekeningmutaties.Grootboekrekening IN ('".implode("','",$grootboekrekeningen)."')";
        $DB->SQL($query);
        $DB->Query();
        $storting = $DB->NextRecord();
        $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];

        $totaalEindwaarde = $eindwaarde;//$this->totalen[$datumEind]['totaalWaardeEur'];

        $stortingen 			 	= getStortingen($this->rapport->portefeuille,$datumBegin,$datumEind);
        $onttrekkingen 		 	= getOnttrekkingen($this->rapport->portefeuille,$datumBegin,$datumEind);
        $AttributieStortingenOntrekkingen['storting']=$stortingen;
        $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
        $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
        $gemiddelde = $totaalGemiddelde;

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

				$performanceBruto = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal'] - $nietToegerekendeKosten['kostenTotaal']) / $totaalGemiddelde);


				$performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
  
 

      }
      else
      {

	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQuery AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	    $DB->SQL($queryAttributieStortingenOntrekkingenRekening); //echo $queryAttributieStortingenOntrekkingenRekening."";
	    $DB->Query();
	    $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

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
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
      $DB->SQL($queryAttributieStortingenOntrekkingen);// echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
      $DB->Query();
      $AttributieStortingenOntrekkingen = $DB->NextRecord();
     // listarray($AttributieStortingenOntrekkingen);

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
	   if($catnaam=='VAR')
		 {
			 $nietToegerekendeKosten = $DB->NextRecord();
			 //$AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
		 }

      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
      $performanceBruto = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']-$nietToegerekendeKosten['kostenTotaal']) / $gemiddelde);
//echo  round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
//echo $fondsData['Hoofdcategorie']." ".round($performance*100,2)."= ((($eindwaarde - $beginwaarde) - ". $AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde) <br>\n";
      }
      
      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);

      $indexData=$this->indexPerformance($fondsData['Hoofdcategorie'],$datumBegin,$datumEind);

     // if($totaal==true)
      //  $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;

      $weging=$gemiddelde/$totaalGemiddelde;//$this->totalen[$datumEind]['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $resultaatBruto=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']-$nietToegerekendeKosten['kostenTotaal'];

        $bijdrage=$resultaat/$gemiddelde*$weging;
  
  
  
      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=$overPerfPeriode*$weging;
//echo " $datumEind $weging <br>\n";
 //  echo "$resultaat = ($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
//echo "$performance <br>\n $weging=$gemiddelde/".$this->totalen[$datumEind]['gemiddeldeWaarde']."; <br>\n $bijdrage=$resultaat/$gemiddelde*$weging <br>\n<br>\n";
      $waarden[$datumEind]=array('periode'=>"$datumBegin $datumEind $weegDatum",
  'indexPerf'=>$indexData['perf'],
  'indexBijdrage'=>$indexData['bijdrage'],
  'indexBijdrageWaarde'=>$indexData['percentage'],
  'overPerf'=>$overPerfPeriode,
  'relContrib'=>$relContrib,
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance,
  'procentBruto'=>$performanceBruto,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal'],
  'kostenNietGekoppeld'=>$nietToegerekendeKosten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'resultaatBruto'=>$resultaatBruto,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage);
}


$stapelItems=array('procent','bijdrage','indexBijdrage','overPerf','relContrib','procentBruto');
$somItems=array('indexPerf','stort','stortEnOnttrekking','storting','onttrekking','kosten','kostenNietGekoppeld','resultaat','ongerealiseerd','gerealiseerd','opbrengst','resultaatBruto');
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

foreach ($waarden as $datum=>$waarde)
{
 // $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  $waarden[$datum]['index']=$perfData['totaal']['procent']*100;
  $waarden[$datum]['indexBruto']=$perfData['totaal']['procentBruto']*100;
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
{
  

 
	return array(
		'beginwaarde'        => $beginwaarde,
		'eindwaarde'         => $eindwaarde,
		'procent'            => $performance * 100,
		'stort'              => $AttributieStortingenOntrekkingen['totaal'],
		'stortEnOnttrekking' => $AttributieStortingenOntrekkingen['totaal'],
		'storting'           => $AttributieStortingenOntrekkingen['storting'],
		'onttrekking'        => $AttributieStortingenOntrekkingen['onttrekking'],
		'kosten'             => $FondsDirecteKostenOpbrengsten['kostenTotaal'],
		'resultaat'          => $resultaat,
		'gemWaarde'          => $gemiddelde,
		'ongerealiseerd'     => $ongerealiseerdResultaat + $FondsDirecteKostenOpbrengsten['RENMETotaal'],
		'gerealiseerd'       => $mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
		'weging'             => $weging,
		'bijdrage'           => $bijdrage * 100);
}
	}

	function getWeken($julBegin, $julEind, $beginVrijdag=false)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
    
    
    $beginVrijdag=true;
    if($beginVrijdag==true)
    {
      $extraDagen=0;
      $dagVanWeek= date('w',$julBegin);
      if($dagVanWeek < 5)
        $extraDagen=5-$dagVanWeek;
      elseif($dagVanWeek > 5)
        $extraDagen=12-$dagVanWeek; 
      $begindag+=$extraDagen; 
    }

  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;
   
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i=$i+7;
	  }
  
	  return $datum;
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
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
  }
  
    
function getKwartalen($julBegin, $julEind)
{
   if($julBegin > $julEind )
     return array();
   $beginjaar = date("Y",$julBegin);
   $eindjaar = date("Y",$julEind);
   $maandenStap=3;
   $stap=1;
   $n=0;
   $teller=$julBegin;
   $kwartaalGrenzen=array();
   $datum=array();
   while ($teller < $julEind)
   {
     $teller = mktime (0,0,0,$stap,0,$beginjaar);
     $stap +=$maandenStap;
     if($teller > $julBegin && $teller < $julEind)
     {
     $grensDatum=date("d-m-Y",$teller);
     $kwartaalGrenzen[] = $teller;
     }
   }
   if(count($kwartaalGrenzen) > 0)
   {
     $datum[$n]['start']=date('Y-m-d',$julBegin);
     foreach ($kwartaalGrenzen as $grens)
     {
       $datum[$n]['stop']=date('Y-m-d',$grens);
       $n++;
       $start=date('Y-m-d',$grens);
       if(substr($start,-5)=='12-31')
        $start=(substr($start,0,4)+1).'-01-01';

       $datum[$n]['start']=$start;
     }
     $datum[$n]['stop']=date('Y-m-d',$julEind);
   }
   else
   {
     $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
   }
 	 return $datum;
}
  
  function getDagen($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $einddag= date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $counterStart=$julBegin;
	  $i=0;
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
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
	    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie 
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$this->rapport->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
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

    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;
    
    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;
      //$perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $totalPerf+=($perf*$percentage/100);
    }  

    $perf= $totalPerf;
    
    if($_POST['debug']==1)
      echo "$categorie | $fonds | $van | $tot | $perf<br>\n";
    
    
    /*
    
    echo "$fonds <br>\n";
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
  	$DB->SQL($query);
    $startKoers=$DB->lookupRecord();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
    
    */
    $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
    
    
    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$fondsData['Percentage'],
                'datum'=>$tot,
                'percentage'=>($this->normData[$categorie]/100),//$fondsData['Percentage']
                'categorie'=>$categorie,
			          'hoofdfonds'=>$fonds,
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);

    return $tmp;
  }


}

?>