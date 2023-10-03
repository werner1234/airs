<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/06/25 16:57:02 $
 		File Versie					: $Revision: 1.4 $

 		$Log: ATTberekening_L53.php,v $
 		Revision 1.4  2016/06/25 16:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/06/15 15:59:41  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/06/11 14:24:49  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/05/27 11:57:58  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/02/15 10:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/08/30 16:31:49  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/08/27 15:47:58  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/07/27 11:31:03  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/07/19 14:27:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/06/11 15:35:21  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/05/21 09:32:51  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/05/10 13:54:39  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/19 16:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/12 16:28:12  rvv
 		*** empty log message ***
 		
 	
 */
class rapportHulp_L53
{
  function rapportHulp_L53()
  {
    $this->test='';
  }
}



class ATTberekening_L53
{

	function ATTberekening_L53($rapportData)
	{
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datumvanaf);
	  $this->indexPerformance=false;

	}
  
  function getPerf($portefeuille, $datumBegin, $datumEind)
  {
    $this->rapport = new rapportHulp_L53();
    $db=new DB();
    $query="SELECT * FROM Portefeuilles WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $db->lookupRecord();

    $this->rapport->pdf->portefeuilledata=$db->lookupRecord();
    $this->rapport->portefeuille=$portefeuille;
   	$this->rapport_datumvanaf=db2jul($datumBegin);
	  $this->rapport_datum=db2jul($datumEind);
	  $this->rapport_jaar  =date('Y',$this->rapport_datumvanaf);
    $perfData=$this->bereken($datumBegin, $datumEind);


    unset($totalen);
    unset($perfData['totaal']);
    foreach ($perfData as $categorie=>$categorieData)
    {
      foreach ($categorieData['perfWaarden'] as $maand=>$maandWaarden)
      {
        if($maand <> '')
        {
          $totalen[$maand]['portBijdrage']+=$maandWaarden['bijdrage']*100;

        }
      }
    }
    
    foreach ($totalen as $maand=>$maandWaarden)
   	{
   	  foreach ($maandWaarden as $veld=>$waarde)
   	  {
   	    if(!isset($laatste[$veld]))
   	      $laatste[$veld]=0;
   	    $jaarTotalen[$veld]=((1+$maandWaarden[$veld]/100)*(1+$laatste[$veld]/100)-1)*100;
   	    $laatste[$veld]=$jaarTotalen[$veld];
   	  }
   	}
    
    
  //  listarray($jaarTotalen);
    //listarray($perfData['totaal']);
    return $jaarTotalen['portBijdrage'];//$perfData['totaal']['procent'];
  }

	function bereken($van,$tot)
	{
	  global $__appvar;
 		$DB=new DB();
     $this->categorien=array('totaal'=>'Totaal');
    
    $query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
Beleggingscategorien.Omschrijving
FROM
CategorienPerHoofdcategorie
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
	  while($data = $DB->NextRecord())
	  {
  	    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['Omschrijving'];
	      $perHoofdcategorie[$data['Hoofdcategorie']]['categorie']=$data['Hoofdcategorie'];
        $this->categorien[$data['Hoofdcategorie']]=$data['Omschrijving'];
	  }

    $query="SELECT
    Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving
FROM
Beleggingscategorien
INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN ZorgplichtPerPortefeuille ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht
WHERE 
ZorgplichtPerPortefeuille.Portefeuille = '".$this->rapport->portefeuille."' AND ZorgplichtPerPortefeuille.extra=0 AND
ZorgplichtPerPortefeuille.norm > 0
ORDER BY Beleggingscategorien.Afdrukvolgorde";
     
   
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen']=array();
      $perCategorie[$data['Beleggingscategorie']]['fondsValuta']=array();
    } 

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
      if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
        
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perHoofdcategorie[$data['Hoofdcategorie']]['categorie']=$data['Hoofdcategorie'];
		    $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		    $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];
		    $perCategorie[$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];
		    $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
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
		  if($data['Beleggingscategorie']=='')
      {
        $data['Beleggingscategorie']='geenCategorie';
        $data['categorieOmschrijving']=$data['Beleggingscategorie'];
      }
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['categorie']=$data['Hoofdcategorie'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

    $this->totalen['gemiddeldeWaarde']=0;
    $perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true);

    $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
    foreach ($perHoofdcategorie as $categorie=>$categorieData)
    { 
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,$totaal);
		    $this->categorien[$categorie]=$categorieData['omschrijving'];
    }
    $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,true);
    //$this->categorien['totaal']='Totaal';
    //listarray($perfData['totaal']);

    return $perfData;
	}

	function indexPerformance($categorie,$van,$tot)
	{
	  global $__appvar;
    $DB = new DB();



			$query="SELECT IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerPortefeuille.norm / 100 as Percentage
FROM
IndexPerBeleggingscategorie
 Join ZorgplichtPerBeleggingscategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
 Join ZorgplichtPerPortefeuille ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf < '$van' AND IndexPerBeleggingscategorie.vanaf < '$van' AND  
ZorgplichtPerPortefeuille.extra=0 AND IndexPerBeleggingscategorie.Portefeuille = '".$this->rapport->portefeuille."'
order by ZorgplichtPerPortefeuille.vanaf desc,  IndexPerBeleggingscategorie.vanaf desc limit 1 ";
			$DB->SQL($query);
			$fondsData=$DB->lookupRecord();
		


if($fondsData['Fonds']=='')
{


		$query="SELECT IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerPortefeuille.norm / 100 as Percentage
FROM
IndexPerBeleggingscategorie
 Join ZorgplichtPerBeleggingscategorie ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
 Join ZorgplichtPerPortefeuille ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerPortefeuille.Zorgplicht AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."'
WHERE
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND 
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf < '$van' AND IndexPerBeleggingscategorie.vanaf < '$van' AND 
ZorgplichtPerPortefeuille.extra=0 AND IndexPerBeleggingscategorie.Portefeuille = ''
order by ZorgplichtPerPortefeuille.vanaf desc,  IndexPerBeleggingscategorie.vanaf desc limit 1 ";
		$DB->SQL($query); //echo $query."<br>\n";
		$fondsData=$DB->lookupRecord();

}


if($fondsData['Fonds']=='')
{


	$query="SELECT
IndexPerBeleggingscategorie.Fonds,
ZorgplichtPerRisicoklasse.norm / 100 AS Percentage
FROM
IndexPerBeleggingscategorie
LEFT JOIN ZorgplichtPerRisicoklasse ON IndexPerBeleggingscategorie.Beleggingscategorie = ZorgplichtPerRisicoklasse.Zorgplicht AND IndexPerBeleggingscategorie.Vermogensbeheerder = ZorgplichtPerRisicoklasse.Vermogensbeheerder
INNER JOIN Portefeuilles ON ZorgplichtPerRisicoklasse.Risicoklasse = Portefeuilles.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Portefeuilles.Portefeuille='".$this->rapport->portefeuille."'
WHERE 
IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND IndexPerBeleggingscategorie.Beleggingscategorie='$categorie'
";
	$DB->SQL($query);
	$fondsData=$DB->lookupRecord();
  

}



  //  echo "$query <br>\n";

   //  $query="SELECT ModelPortefeuilleFixed.Percentage/100 as Percentage
   //           FROM ModelPortefeuilleFixed
   //           Join Portefeuilles ON Portefeuilles.ModelPortefeuille = ModelPortefeuilleFixed.Portefeuille
   //           WHERE Portefeuilles.Portefeuille='".$this->rapport->portefeuille."' AND Fonds='".$fondsData['Fonds']."' AND Datum <='$tot' ORDER BY Datum DESC LIMIT 1";
	 //  	$DB->SQL($query);
	 //   $percentage=$DB->lookupRecord();
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
	   	$DB->SQL($query);
	    $startKoers=$DB->lookupRecord();
	    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fondsData['Fonds']."' ORDER BY Datum DESC LIMIT 1";
		  $DB->SQL($query);
	    $eindKoers=$DB->lookupRecord();
	    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
      $tmp= array('perf'=>$perf,'bijdrage'=>$perf*$fondsData['Percentage'],'datum'=>$tot,'percentage'=>$fondsData['Percentage'],'categorie'=>$categorie,'koersVan'=>$startKoers['Koers'],'koersEind'=>$eindKoers['Koers']);//,'waarden'=>$waarden)

    return $tmp;
  }



	function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$totaal=false)
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));

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
    //if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
   //   $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
   // else
      $weegDatum=$datumBegin;
    $datumEind=$periode['stop'];

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

    if(db2jul($weegDatum) < db2jul($this->rapport->pdf->PortefeuilleStartdatum))
      $weegDatum=$this->rapport->pdf->PortefeuilleStartdatum;

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
    $totaalGemiddelde2 = $totaalBeginwaarde + $weging['totaal2'];


      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";


      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) as liqWaarde,
      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0)) as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];


       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2 as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     //$ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew'];
	     $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];

	     $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
	    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
	    $DB->Query();
	    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();

      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as RENMETotaal
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
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();


	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
	     $DB->SQL($query);//echo $query."<br>\n";
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];

/*
      $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
	     $DB->SQL($queryKostenOpbrengsten); //if($fondsData['categorie']=='Liquiditeiten' && $datumEind=='2012-11-30') echo $queryKostenOpbrengsten."<br>\n";
	     $DB->Query(); echo $queryKostenOpbrengsten;
	     $nietToegerekendeKosten = $DB->NextRecord();
*/
// verwijderd    $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];


      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      if($beginwaarde==0)
      {
         $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $AttributieStortingenOntrekkingen['totaal']);
         $gemiddelde=$AttributieStortingenOntrekkingen['totaal'];
         $totaalGemiddelde=$totaalGemiddelde2;
      }
      else
      {
        $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
//echo "rvv $performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde);<br>\n";exit;
      }
 //     echo $fondsData['categorie']." $datumBegin $datumEind ".$AttributieStortingenOntrekkingen['totaal']." -> $performance <br>\n";
//if($fondsData['categorie']=='Liquiditeiten' && $datumEind=='2012-11-30') 
//echo "$performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde);<br>\n";

      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
      $indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);
      //echo "$weegDatum ->  $datumBegin,$datumEind <br>\n";

      if($totaal==true)
      {
        $this->totalen['gemiddeldeWaarde']=$gemiddelde;
      }

      //echo "$totaalGemiddelde ".$this->totalen['gemiddeldeWaarde']." <br>\n";
      $weging=$gemiddelde/$totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
    //echo "$bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";
      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=(($performance*$weging)-($indexData['perf']*$indexData['percentage']));//$overPerfPeriode*$weging;

      //echo $indexData['categorie']." ($performance*$weging) - (".$indexData['perf']."*".$indexData['percentage'].") <br>\n";

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
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage);
/*
  if($indexData['categorie']=='Liquiditeiten' && $datumEind=='2012-11-30') 
  {
    echo "$datumEind <br>\n";
    listarray($waarden[$datumEind]);
    exit;
  }
*/  
}
//listarray($fondsData);
//listarray($waarden);

$stapelItems=array('procent','bijdrage','indexPerf','indexBijdrage','overPerf','relContrib');
$gemiddeldeItems=array('weging','indexBijdrageWaarde');
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

foreach ($waarden as $datum=>$waarde)
{
  if(!isset($perfData['totaal']['beginwaarde']))
    $perfData['totaal']['beginwaarde'] =$waarde['beginwaarde']+0;
  $perfData['totaal']['eindwaarde'] =$waarde['eindwaarde'];
  $perfData['totaal']['stortEnOnttrekking'] +=$waarde['stortEnOnttrekking'];
  $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  foreach($gemiddeldeItems as $item)
   $perfData['totaal'][$item]+=$waarde[$item];
}
foreach($gemiddeldeItems as $item)
  $perfData['totaal'][$item]=$perfData['totaal'][$item]/count($waarden)*100;

foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
$perfData['totaal']['categorie']=$fondsData['categorie'];
//stapelen verwijderen
$stapelenVerwijderen=false;
if($stapelenVerwijderen==true)
{
 $perfData['totaal']['procent']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'];
 $perfData['totaal']['bijdrage']=$perfData['totaal']['resultaat']  / $perfData['totaal']['gemWaarde'] * $perfData['totaal']['weging'] *100;
 
 if($categorie=='totaal')
 {
  $perfData['totaal']['procent']=$perfData['totaal']['procent']*100;
 $perfData['totaal']['bijdrage']=$perfData['totaal']['bijdrage']*100;
 }
}

if($stapeling == true)
{
  $mutaties=$this->genereerMutatieLijst($van,$tot, $fondsData['fondsen']);
  $perfData['totaal']['gerealiseerdFondsResultaat']=$mutaties['totalen']['fonds'];
  $perfData['totaal']['gerealiseerdValutaResultaat']=$mutaties['totalen']['valuta']; 
  
  //historischeWaarde
  $fondsenWhere="AND Fonds IN('".implode('\',\'',$fondsData['fondsen'])."')"; 
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro - beginPortefeuilleWaardeEuro) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta) as fondsresultaatEUR".
						 " FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$tot' $fondsenWhere AND".
						 " portefeuille = '".$this->rapport->portefeuille."' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
  $DB->SQL($query); //echo $query;exit;
	$DB->Query();
	$totaal = $DB->nextRecord();
  $ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'] ;
	$ongerealiseerdValutaResultaat = $totaal['resultaatEUR']-$totaal['fondsresultaatEUR'] ;
  $perfData['totaal']['ongerealiseerdFondsResultaat']=$ongerealiseerdFondsResultaat; 
  $perfData['totaal']['ongerealiseerdValutaResultaat']=$ongerealiseerdValutaResultaat; 
  
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$tot' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
	$DB->SQL($query);
	$DB->Query();
	$totaalA = $DB->nextRecord();
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$van' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
	$DB->SQL($query);
	$DB->Query();
	$totaalB = $DB->nextRecord();
	$perfData['totaal']['opgelopenrente'] = ($totaalA['totaal'] - $totaalB['totaal']) ;

  if($categorie=='totaal')
    $filter='';
  else
    $filter=$fondsenWhere;
 $query = "SELECT  Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten, Grootboekrekeningen.Grootboekrekening,".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties 
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
         JOIN Grootboekrekeningen ON Grootboekrekeningen.Grootboekrekening=Rekeningmutaties.Grootboekrekening ".
		  	"WHERE Rekeningen.Portefeuille = '".$this->rapport->portefeuille."'  AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$tot."' $filter AND 
        (Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Kosten = '1') GROUP BY  Grootboekrekeningen.Grootboekrekening
        ORDER BY Grootboekrekeningen.Afdrukvolgorde";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($grootboek = $DB2->nextRecord())
			{
        if($grootboek['Opbrengst']==1)
        {
			 	  $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
				  $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
        if($grootboek['Kosten']==1)
        {
			 	  $kostenPerGrootboek[$grootboek['Grootboekrekening']] =  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
				  $totaalKosten += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']);
        }
			}
      
    if($categorie <> 'totaal')
    {
      $filter=$rekeningRekeningenWhere;    
      $query = "SELECT Rekeningmutaties.Grootboekrekening,".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties 
         JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
         JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille ".
		  	"WHERE Rekeningen.Portefeuille = '".$this->rapport->portefeuille."'  AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$tot."' AND $filter AND 
        (Rekeningmutaties.Grootboekrekening='RENTE') GROUP BY  Rekeningmutaties.Grootboekrekening
        ";    
      $DB2->SQL($query); 
			$DB2->Query();
      while($grootboek = $DB2->nextRecord())
			{
        $opbrengstenPerGrootboek[$grootboek['Grootboekrekening']] +=  ($grootboek['totaalcredit']-$grootboek['totaaldebet']);
		    $totaalOpbrengst += ($grootboek['totaalcredit'] - $grootboek['totaaldebet']); 
      }
    } 
      
      $perfData['totaal']['opbrengst']=$totaalOpbrengst;
      $perfData['totaal']['grootboekOpbrengsten']=$opbrengstenPerGrootboek;
      $perfData['totaal']['kosten']=$totaalKosten;
      $perfData['totaal']['grootboekKosten']=$kostenPerGrootboek;
  $perfData['totaal']['perfWaarden']=$waarden;
  //listarray($perfData['totaal']);
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
         $datum[$i]['start'] = date('Y-m-d',$julBegin);
      else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);

	    if(substr($datum[$i]['start'],5,5)=='12-31')
	     $datum[$i]['start']=(substr($datum[$i]['start'],0,4)+1)."-01-01";

       $i++;
	  }
	  return $datum;
  }

  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='')
	{
	  	// loopje over Grootboekrekeningen Opbrengsten = 1
	  if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';


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

		// haal koersresultaat op om % te berekenen
		$buffer = array();
		$sortBuffer = array();
		while($mutaties = $DB->nextRecord())
		{
			$buffer[] = $mutaties;
		}

	  foreach ($buffer as $mutaties)
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
      $mutaties['Rapportagekoers']=1;

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















}

?>