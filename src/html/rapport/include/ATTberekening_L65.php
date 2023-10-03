<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.5 $

 		$Log: ATTberekening_L65.php,v $
 		Revision 1.5  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.4  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/12/19 09:03:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/11/29 13:13:22  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/10/04 11:52:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		

 */


class ATTberekening_L65
{

	function ATTberekening_L65($rapportData,$periode='maanden')
	{
    $this->rapport=&$rapportData;

   	$this->rapportageDatumVanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar=date('Y',$this->rapportageDatumVanaf);
	  $this->indexPerformance=false;
    $this->perioden=$periode;

	}

	function bereken($van,$tot,$verdeling='Hoofdcategorie')
	{
	  global $__appvar;
 		$DB=new DB();
    $this->categorien=array('totaal'=>'Totaal');
    
    if($verdeling=='Hoofdcategorie')
    {
      $categorieFilter='Hoofdcategorie';
      //$categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }
    elseif($verdeling=='totaal')  
    {  
      $categorieFilter='geen';
    }
    elseif($verdeling=='sector')  
    {
      $categorieFilter='Beleggingssectoren';
      $join="LEFT JOIN Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector";
      $selectOmschrijving=',Beleggingssectoren.Omschrijving';
    }
    else
    { 
      $categorieFilter='Beleggingscategorien';
      $join="LEFT JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie";
      $selectOmschrijving=',Beleggingscategorien.Omschrijving';
    }

    $query="SELECT waarde $selectOmschrijving FROM KeuzePerVermogensbeheerder $join
    WHERE categorie='$categorieFilter' AND 
    Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
    ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query); 
    $DB->Query();
    $tmp=array();
    while($data=$DB->nextRecord())
    {
      $tmp[$data['waarde']]=array('categorie'=>$data['waarde'],'omschrijving'=>$data['Omschrijving']);
    }
    $perHoofdcategorie=$tmp;
    $perRegio=$tmp;
    $perSector=$tmp;
    $perCategorie=$tmp;  

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
BeleggingssectorPerFonds.Beleggingssector,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";

			$DB->SQL($query); 
		  $DB->Query();
		  while($data = $DB->NextRecord())
		  {
		    if($data['Hoofdcategorie']=='')
          $data['Hoofdcategorie']='Geen H-cat';

 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }

        
        
        if($data['Beleggingscategorie']=='')
          $data['Beleggingscategorie']='Geen cat';     
                            
		    $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		    $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		    $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
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
Rekeningmutaties.Boekdatum >= '".$this->rapport->pdf->PortefeuilleStartdatum."' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";

		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
		  if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
 		  if($data['Beleggingssector']=='')
      {
        if($data['Beleggingscategorie']!='')
        {
          $data['Beleggingssector']=$data['Beleggingscategorie'];
          $data['sectorOmschrijving']=$data['categorieOmschrijving'];  
        }
        else
        {  
          $data['Beleggingssector']='Geen sector'; 
          $data['sectorOmschrijving']=$data['Geen sector'];   
        }
 		  }
      if($data['Beleggingscategorie']=='')
        $data['Beleggingscategorie']='Geen cat';  
		  $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
		  $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
		  $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
		  $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
		  $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
		  $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
		  $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
		  $alleData['rekeningen'][]=$data['rekening'];
	  }

 
    //$this->totalen['gemiddeldeWaarde']=0;
    //$perfTotaal=$this->fondsPerformance($alleData,$van,$tot,false,true);
    
    if($verdeling=='Hoofdcategorie')
      $categorien=$perHoofdcategorie;
    elseif($verdeling=='totaal')  
      $categorien=array();
    elseif($verdeling=='sector')  
      $categorien=$perSector;
    else
      $categorien=$perCategorie;

    $this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
    foreach ($categorien as $categorie=>$categorieData)
    { 
        $this->huidigeCategorie=$categorie;
		    $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,$categorie);
        if($categorieData['omschrijving']=='')
          $categorieData['omschrijving']=$categorie;
		    $this->categorien[$categorie]=$categorieData['omschrijving'];
    }
    $this->huidigeCategorie='totaal';
    $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,'totaal');
    //$this->categorien['totaal']='Totaal';
    //listarray($perfData);

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
IndexPerBeleggingscategorie.Beleggingscategorie='$categorie' AND ZorgplichtPerPortefeuille.Vanaf < '$van' AND 
ZorgplichtPerPortefeuille.extra=0
order by ZorgplichtPerPortefeuille.vanaf desc limit 1
";
	  $DB->SQL($query);
	  $fondsData=$DB->lookupRecord();

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



	function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$categorie='')
  {
    global $__appvar;
    if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    else
    { 
      if($htis->perioden=='kwartalen')
       $perioden=$this->getKwartalen(db2jul($van),db2jul($tot));
      else
        $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    }
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');  
      
   //   
  //    $grootboekKostenFilter='OR Grootboekrekeningen.Kosten =1';
   // else
   //   $grootboekKostenFilter='';  

    global $__appvar;
	  $DB=new DB();

    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      { 
        if(substr($rapDatum,5,5)=='01-01')
          $startJaar=1;
        else
          $startJaar=0;
        if(!isset($this->totalen[$rapDatum]))
        {  
	        $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
          foreach($fondswaarden as $id=>$fondsWaarde)
          {
            if($fondsWaarde['type']=='fondsen')
              $instrument=$fondsWaarde['fonds'];
            elseif($fondsWaarde['type']=='rente')
              $instrument=$fondsWaarde['fonds'];              
            elseif($fondsWaarde['type']=='rekening')
              $instrument=$fondsWaarde['rekening'];  
            else
              $instrument='geen';  
            $this->totalen[$rapDatum]['totaalWaardeEur']+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
            $this->totalen[$rapDatum]['WaardeEur'][$instrument]+=$fondsWaarde['actuelePortefeuilleWaardeEuro'];
          }
        }
        if(!isset($this->totalen[$rapDatum]['WaardeEur'][$categorie]))
        {
          foreach($this->totalen[$rapDatum]['WaardeEur'] as $instrument=>$waarde)
          {
            if(in_array($instrument,$fondsData['fondsen']) || in_array($instrument,$fondsData['rekeningen']))
            {
              $this->totalen[$rapDatum]['WaardeEur'][$categorie]+=$waarde;
            }
          }
        }
     }
   }
  

foreach ($perioden as $periode)
{
    $grootboekKosten=array();
    $grootboekOpbrengsten=array();
    $FondsDirecteKostenOpbrengsten=array();
    $RekeningDirecteKostenOpbrengsten=array();
    $datumBegin=$periode['start'];
    $datumEind=$periode['stop'];
    
    if($portefeuilleStartJul > db2jul($datumBegin) && $portefeuilleStartJul < db2jul($datumEind))
    {
      $datumBegin=substr($this->rapport->pdf->PortefeuilleStartdatum,0,10);
      $weegDatum=$datumBegin;
    }
    
    if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
      $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
    else
      $weegDatum=$datumBegin;
    
    
    $portefeuilleStartJul=db2jul($this->rapport->pdf->PortefeuilleStartdatum);
    
	  $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	  $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur']; 
    
    
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
      $this->totaalGemiddelde=$totaalGemiddelde;
//echo "$categorie $totaalGemiddelde = $totaalBeginwaarde - ".$storting['gewogen']."<br>\n"; 
    if($categorie=='totaal')
    {
      $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
	    $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];  
      $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
      $stortingen 			 	= getStortingen($this->rapport->portefeuille,$datumBegin,$datumEind);
	  	$onttrekkingen 		 	= getOnttrekkingen($this->rapport->portefeuille,$datumBegin,$datumEind);
      $AttributieStortingenOntrekkingen['storting']=$stortingen;
      $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
      $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
      $gemiddelde = $totaalGemiddelde;
    }
    else
    {
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie];
	    $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie];//$eind['actuelePortefeuilleWaardeEuro'];
      //echo "$categorie $datumEind $eindwaarde <br>\n";

	    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
	              "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
	     $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
	     $DB->Query();
	     $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();

	     $queryRekeningDirecteKostenOpbrengsten = "SELECT 
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
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
/* 
	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Grootboekrekening = 'FONDS' AND $rekeningFondsenWhere ";//
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
 	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles) 
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                 
	              WHERE 	 
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND                
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds <> '' "; //Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  
	     $DB->SQL($query);
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
*/
//begin
	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere ";//
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     //$DB->SQL($queryAttributieStortingenOntrekkingen." AND Rekeningmutaties.Grootboekrekening='FONDS' "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";

	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();
       
       $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
       $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
     
       
 	    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

   	  $query = "SELECT 
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles) 
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening 
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                 
	              WHERE 	 
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND                
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
	     $DB->SQL($query);
       
	     $DB->Query(); 
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
      
       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
         $DB->SQL($query);
       else
         $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
	     $DB->Query(); 
	     $data = $DB->nextRecord();

	     $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];
//end       
       
//       echo $query;
//listarray($AttributieStortingenOntrekkingen);


      
      $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
	     $DB->SQL($queryKostenOpbrengsten);
	     $DB->Query();
	     $nietToegerekendeKosten = $DB->NextRecord();
       
       
       //  echo $categorie. " ".count($fondsData['rekeningen'])."<br>\n";

       
       if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
       {

         // $RekeningDirecteKostenOpbrengsten['kostenTotaal']+= $nietToegerekendeKosten['kostenTotaal'];
          $AttributieStortingenOntrekkingen['totaal']+= $nietToegerekendeKosten['kostenTotaal'];
          $AttributieStortingenOntrekkingen['onttrekking']+= $nietToegerekendeKosten['kostenTotaal'];
     
       }

    
//	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
       $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
       $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] + $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
       
       $gemiddeldeBruto  = $beginwaarde - $AttributieStortingenOntrekkingenBruto['gewogen'];
       $performanceBruto = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal']- $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
  
// echo $categorie." $datumEind  $performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde)<br>\n";
//listarray($RekeningDirecteKostenOpbrengsten); ob_flush();
     // listarray($AttributieStortingenOntrekkingen);
     // listarray($AttributieStortingenOntrekkingenBruto);
      }

      //$mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
      //$indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);


      $renteResultaat=$eind['renteWaarde']-$start['renteWaarde'];
      //listarray($FondsDirecteKostenOpbrengsten['RENMETotaal']);
      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      //echo "$categorie $weging=$gemiddelde/".$this->totaalGemiddelde.";<br>\n";
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      //  echo $categorie.' '.$datumEind.' '.$aandeelOpTotaal.' '.$eindwaarde.'/'.$totaalEindwaarde."<br>\n";
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']+ $RekeningDirecteKostenOpbrengsten['kostenTotaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
    //echo "$categorie $bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";
      //$overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      //$relContrib=(($performance*$weging)-($indexData['perf']*$indexData['percentage']));//$overPerfPeriode*$weging;
      //$verschilWeging=($weging-$indexData['percentage']);
      //$gerealiseerd=$mutatieData['totalen']['gerealiseerdResultaat'] ;
      //$ongerealiseerd=$ongerealiseerdResultaat - $renteResultaat;//$FondsDirecteKostenOpbrengsten['RENMETotaal'];
      //$resultaatValuta=$resultaat-$gerealiseerd-$ongerealiseerd-
      //           $FondsDirecteKostenOpbrengsten['kostenTotaal']-
      //           $RekeningDirecteKostenOpbrengsten['kostenTotaal']-
      //           $FondsDirecteKostenOpbrengsten['opbrengstTotaal']-
      //           $RekeningDirecteKostenOpbrengsten['opbrengstTotaal']-
      //           $renteResultaat;
      //echo $indexData['categorie']." ".($performance*$weging)." - ".($indexData['perf']*$indexData['percentage'])." <br>\n";

      $waarden[$datumEind]=array(
   'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,
  'procent'=>$performance,
  'procentBruto'=>$performanceBruto,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'weging'=>$weging,
  'aandeelOpTotaal'=>$aandeelOpTotaal,
  'bijdrage'=>$bijdrage);
}


$stapelItems=array('procent','bijdrage');
$avgItems=array('weging','gemWaarde');
$somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','stort');
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;

$eersteDatum=true;
foreach ($waarden as $datum=>$waarde)
{
  if($eersteDatum==true)
  {
    $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];
    $eersteDatum=false;
  }
  $perfData['totaal']['eindwaarde']=$waarde['eindwaarde'];
  $perfData['totaal']['aandeelOpTotaal']=$waarde['aandeelOpTotaal'];
  

    
  foreach ($somItems as $item)    
    $perfData['totaal'][$item] +=$waarde[$item];
  foreach ($stapelItems as $item)
    $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
  foreach ($avgItems as $item)  
    $sum[$item] += $waarde[$item];
}
foreach ($avgItems as $item)
  $perfData['totaal'][$item]=$sum[$item]/count($waarden);

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
      $fondsenWhere='1';


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
    
      $fondsResultaat=0;
      $valutaResultaat=0;
      if($verkoop_koers <> '' )
      { //listarray($historie);
        $historischekostprijsValuta = $mutaties['Aantal']*$historie['historischeWaarde']* $mutaties['Fondseenheid'];//$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
        $historischekostprijsValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];  
        $fondsResultaat = ($t_verkoop_waardeinValuta-$historischekostprijsValuta)*getValutaKoers($mutaties['Valuta'] ,$mutaties['Boekdatum']);
	      $valutaResultaat=	($resultaatlopende)-$fondsResultaat;  //$resultaatvoorgaande
      }
      
				$data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
        $data['totalen']['fonds']+=$fondsResultaat;
        $data['totalen']['valuta']+=$valutaResultaat;
		}
		return $data;
	}
}


class indexHerberekening_L65
{
	function indexHerberekening_L65( $selectData )
	{
		$this->selectData = $selectData;
	}

	function formatGetal($waarde, $dec=2)
	{
		return number_format($waarde,$dec,",",".");
	}

	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;


		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
	  $DB=new DB();

  	$query = "SELECT SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
 	  "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$beginDatum."')) ".
	  "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	  "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	  "FROM  (Rekeningen, Portefeuilles ) Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
  	"WHERE ".
  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
  	"Rekeningmutaties.Verwerkt = '1' AND ".
  	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
  	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
  	$DB->SQL($query);
  	$DB->Query();
  	$weging = $DB->NextRecord();
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
	  $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
  	$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
  	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	  $query = "SELECT SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) - SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($resultaatVerslagperiode+$kosten['totaalkosten'],2);
    $data['performance'] =$performance;
    return $data;

	}


	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();

		$query="SELECT
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille,
CategorienPerHoofdcategorie.Beleggingscategorie,
CategorienPerHoofdcategorie.Hoofdcategorie,
Beleggingscategorien.Omschrijving
FROM
BeleggingscategoriePerFonds
INNER JOIN Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
INNER JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND 
BeleggingscategoriePerFonds.Vermogensbeheerder = CategorienPerHoofdcategorie.Vermogensbeheerder
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
     $this->categorieVolgorde['Liquiditeiten']=0;
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;


    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        //$categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        //$categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['Liquiditeiten'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

//echo "perf $eindDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";

    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    return $data;

	}


	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output='')
	{
	  if(is_array($portefeuille))
	  {
	    $portefeuilles=$portefeuille[1];
	    $portefeuille=$portefeuille[0];
	  }
		$db=new DB();
    $julBegin = db2jul($datumBegin);
    $beginDatum=date("Y-m-d",$julBegin);
    $julEind = db2jul($datumEind);

   	$eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $datum == array();

  if($methode=='maanden')
  {
     $datum=$this->getMaanden($julBegin,$julEind);
     $type='m';
  }
  elseif($methode=='dagKwartaal')
  {
    $datum=$this->getDagen($julBegin,$julEind);
    $type='dk';
  }
  elseif($methode=='kwartaal')
  {
    $datum=$this->getKwartalen($julBegin,$julEind);
    $type='k';
  }
  elseif($methode=='jaar')
  {
    $datum=$this->getJaren($julBegin,$julEind);
    $type='j';
  }  
  elseif($methode=='TWR')
  {
    $datum=$this->getTWRstortingsdagen($portefeuille,$julBegin,$julEind);
    $type='t';
  }  
  elseif($methode=='dagYTD')
  {
     //$datum=$this->getDagen($julBegin,$julEind,'jaar');
     $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
     $type='dy';
  }
  elseif ($methode=='halveMaanden')
  {
    $datum=$this->getHalveMaanden($julBegin,$julEind);
    $type='2w';
  }
  elseif($methode=='weken')
  {
    $datum=$this->getWeken($julBegin,$julEind);
    $type='w';
  }
  elseif($methode=='dagen')
  {
    $datum=$this->getDagen2($julBegin,$julEind);
    $type='d';
  }
/*
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;
*/

	$i=1;
	$indexData['index']=100;
	$indexData['specifiekeIndex']=100;
	$kwartaalBegin=100;

	$huidigeIndex=$specifiekeIndex;
  $jsonOutput=array('label'=>$portefeuille,'data'=>array());
	foreach ($datum as $periode)
	{
	    if($specifiekeIndex != '')
	    {
	      //if($specifiekeIndex )
        /*
//	      $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
	      $db->SQL($query);
        $oldIndex=$db->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
        {
          $specifiekeIndex=$oldIndex['specifiekeIndex'];
          unset($startSpecifiekeIndexKoers);
        }
        else
        {
          if($huidigeIndex <> $specifiekeIndex)
            unset($startSpecifiekeIndexKoers);
          $specifiekeIndex=$huidigeIndex;
        }
       */
	      if(empty($startSpecifiekeIndexKoers))
	      {
	        $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
	        $db->SQL($query);
	        $specifiekeIndexData = $db->lookupRecord();
	        $startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
	      }
	      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
	      $db->SQL($query);
	      $specifiekeIndexData = $db->lookupRecord();
	      $specifiekeIndexKoers = $specifiekeIndexData['Koers'];
	    }
      $specifiekeIndexWaarden[$i] =($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;

	  	$query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";

	  	if(db2jul($periode['start']) == db2jul($periode['stop']))
	  	{

	  	}
	  	elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == ''))
	  	{
	  	  $dbData = $db->nextRecord();
	  	  $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	  $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	  $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	  $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	  $indexData['stortingen'] = $dbData['Stortingen'];
	  	  $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	      $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	  $indexData['kosten'] = $dbData['Kosten'];
	  	  $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	  $indexData['performance'] = $dbData['indexWaarde'];
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	    $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	    $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	    $indexData['rente'] = $dbData['rente'];
  	    $indexData['extra'] = unserialize($dbData['extra']);
	  	}
	  	else
	  	{
	  	  if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''))
	  	  {
	  	    $query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";

	  	    if($db->QRecords($query) > 0)
	  	    {
	  	    $dbData = $db->nextRecord();
	  	    $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	    $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	    $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
          $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	    $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	    $indexData['stortingen'] = $dbData['Stortingen'];
	  	    $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	        $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	    $indexData['kosten'] = $dbData['Kosten'];
	  	    $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	    $indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	      $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	      $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	      $indexData['rente'] = $dbData['rente'];
  	      $indexData['extra'] = unserialize($dbData['extra']);
  	      //listarray($indexData);
	    	  }
	    	  else
	  	      $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille));
	  	  }
        else
	  	    $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,$valuta));
	  	}

	  	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";
	  	if($methode=='dagKwartaal')
	  	{
	  	  if($periode['blok'] <> $lastBlok)
	  	    $kwartaalBegin=$indexData['index'];
	  	  $indexData['index'] = ($kwartaalBegin  * (100+$indexData['performance'])/100);
	  	  $lastBlok=$periode['blok'];
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'performance'=>$indexData['performance'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	if($methode=='dagYTD')
	  	{
	  	  $indexData['index']=$indexData['performance']+100;
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	else
	  	{

        if(empty($specifiekeIndexWaarden[$i-1]))
	    	  $indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
	    	else
	    	  $indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
	      $indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;
	      if(empty($indexData['index']))
	        $indexData['index']=100;
	  	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	      $data[$i] = $indexData;
	  	}
      /*)
      if($output=='html')
      {
        
        $jsonOutput['data'][]=array(adodb_db2jul($data[$i]['datum'])*1000,$data[$i]['index']);
        //
        $dbData=mysql_real_escape_string(serialize($data[$i]));
        $query="INSERT INTO CRM_htmlData SET 
        portefeuille='$portefeuille',
        datum='".$data[$i]['datum']."',
        dataType='perf',
        data='".$dbData."',
        add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()";
        $db->SQL($query);
        $db->Query();
        //
        file_put_contents('../tmp/perf.json',json_encode($jsonOutput));
      }
      */

  $i++;
	}

	return $data;
	}

	function getWaardenATT($datumBegin,$datumEind,$portefeuille,$categorie='Totaal',$periodeBlok='maand',$valuta='EUR')
	{
	  $this->berekening = new rapportATTberekening($portefeuille);
	  if(is_array($categorie))
	    $this->berekening->categorien = $categorie;
	  else
      $this->berekening->categorien[] = $categorie;
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($portefeuille,$datumBegin,$datumEind,'rapportagePeriode',$valuta,$periodeBlok);

    foreach ($this->berekening->categorien as $categorie)
    {
      $indexData['index'] = 100;
      foreach ($this->berekening->performance as $periode=>$data)
      {
        if($periode != 'rapportagePeriode')
        {
    	  $indexData['periodeForm']    = jul2form(db2jul(substr($periode,0,10)))." - ".jul2form(db2jul(substr($periode,11)));
  	    $indexData['waardeMutatie']  = $data['totaalWaarde'][$categorie]['eind']-$data['totaalWaarde'][$categorie]['begin'];
        $indexData['waardeBegin']    = $data['totaalWaarde'][$categorie]['begin'];
	  	  $indexData['waardeHuidige']  = $data['totaalWaarde'][$categorie]['eind'];
	  	  $indexData['stortingen']     = $data['AttributieStortingenOntrekkingen'][$categorie]['stortingen'];
	  	  $indexData['onttrekkingen']  = $data['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'];
	  	  $indexData['resultaatVerslagperiode'] = $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	   	  $indexData['kosten']         = $data['totaal']['kosten'][$categorie];
	   	  $indexData['opbrengsten']    = $data['totaal']['opbrengsten'][$categorie];
	   	  $indexData['performance']    = $data['totaal']['performance'][$categorie];
	   	  $indexData['index']          = ($indexData['index']  * (100+$indexData['performance'])/100);
	   	  $indexData['datum']          = substr($periode,11);
	   	  if(count($this->berekening->categorien)>1)
	   	  $tmp[$categorie][] = $indexData;
	   	  else
	  	  $tmp[] = $indexData;
        }
      }
    }
	  return $tmp;
	}




	function Bereken()
	{
	  $einddatum = jul2sql($this->selectData[datumTm]);

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		$extraquery  .= " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		// selectie scherm.
		if($this->selectData[portefeuilleTm])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$this->selectData[portefeuilleTm]."') AND";
		if($this->selectData[vermogensbeheerderTm])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData[vermogensbeheerderTm]."') AND ";
		if($this->selectData[accountmanagerTm])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$this->selectData[accountmanagerTm]."') AND ";
		if($this->selectData[depotbankTm])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$this->selectData[depotbankTm]."') AND ";
		if($this->selectData[AFMprofielTm])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$this->selectData[AFMprofielTm]."') AND ";
		if($this->selectData[RisicoklasseTm])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$this->selectData[RisicoklasseTm]."') AND ";
		if($this->selectData[SoortOvereenkomstTm])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectData[SoortOvereenkomstTm]."') AND ";
		if($this->selectData[RemisierTm])
			$extraquery .= " (Portefeuilles.Remisier >= '".$this->selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$this->selectData[RemisierTm]."') AND ";
		if($this->selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$this->selectData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectData['clientTm']."') AND ";
		if (count($this->selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$this->selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";

		$DBs = new DB();
		$DBs->SQL($query);
		$DBs->Query();

		$DB2 = new DB();
		$records = $DBs->records();
		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


  	while($pdata = $DBs->nextRecord())
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }

	 	if($pdata['Vermogensbeheerder'] == 'WAT' || $pdata['Vermogensbeheerder'] == 'WAT1' || $pdata['Vermogensbeheerder'] == 'WWO')
    {
      $pdata['rapportageDatum']=jul2sql($this->selectData['datumTm']);
      $pdata['rapportageDatumVanaf']=jul2sql($this->selectData['datumVan']);
      $pdata['aanvullen']=$this->selectData['aanvullen'];
      $pdata['debug']=$this->selectData['debug'];
      $berekening = new rapportATTberekening($pdata);
      $berekening->pdata['pdf']=false;
      $berekening->indexSuperUser=$this->indexSuperUser;
      $berekening->Bereken();
      // listarray($berekening->performance);
      //  exit;
    }
    else
    {

      $pstartJul = db2jul($pdata['Startdatum']);
	    if($pstartJul > $this->selectData['datumVan'])
	      $julBegin= $pstartJul;
      else
        $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
      if($pdata['Vermogensbeheerder'] == 'SEQ')
      {
    	  $datum = $this->getKwartalen($julBegin,$julEind);
        $type='k';
    	}
      else
    	{
    	  $datum = $this->getMaanden($julBegin,$julEind);
        $type='m';
      }
      $portefeuille = $pdata['Portefeuille'];

		$indexAanwezig = array();
	  if ($this->selectData['aanvullen'] == 1)
	  {
	    $query = "SELECT Datum FROM HistorischePortefeuilleIndex WHERE Portefeuille = '$portefeuille' AND periode='$type' AND Categorie = 'Totaal' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['Datum'];
	    }
    }

    //rvv debug
    if($pdata['Vermogensbeheerder'] == "HEN" || $pdata['PerformanceBerekening'] == 7)
    {
      $datum=array();
      $newJul=$julBegin;
      $type='dy';
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y',$julBegin)."-01-01",'stop'=>date('Y-m-d',$newJul));
      }
    }
    //echo $portefeuille."<br>\n";
//listarray($datum);
			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     if($pdata['Vermogensbeheerder'] == "HEN")
			     {
             include_once("../classes/AE_cls_fpdf.php");
             include_once("rapport/PDFRapport.php");
             include_once("rapport/include/RapportPERF_L26.php");

			       $pdf = new PDFRapport('L','mm');
             $pdf->rapportageValuta = "EUR";
	           $pdf->ValutaKoersEind  = 1;
             $pdf->ValutaKoersStart = 1;
             $pdf->ValutaKoersBegin = 1;
             loadLayoutSettings($pdf, $portefeuille);
             if(substr($datum[$i]['start'],5,5)=='01-01')
               $startjaar=true;
             else
               $startjaar=false;
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['start'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['start']);
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum[$i]['stop']);

             $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
             $pdf->HENIndex=true;
             $rapport = new RapportPERF_L26($pdf, $portefeuille, $datum[$i]['start'], $datum[$i]['stop']);
	           $rapport->writeRapport();

             foreach ($datum as $periode)
             {
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['start']);
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
             }
             $PerformanceMeting=$rapport->pdf->excelData;

             $performance= number_format($PerformanceMeting[0][37],4) ;
             $data['waardeHuidige']=$PerformanceMeting[0][29];
			       $data['waardeBegin']=$PerformanceMeting[0][28];
 		         $data['stortingen']=$PerformanceMeting[0][30];
			       $data['onttrekkingen']=0;
			       $data['opbrengsten']=$PerformanceMeting[0][32];
			       $data['kosten']=$PerformanceMeting[1][13];
			     }
			     else
			     {
             $data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
		         $performance = number_format($data['performance'],4) ;
			     }
		    $query = "SELECT id FROM HistorischePortefeuilleIndex WHERE periode='$type' AND Portefeuille = '$portefeuille' AND Datum = '".substr($datum[$i]['stop'],0,10)."' ";
		    $DB2->SQL($query);
		    $DB2->Query();
		    $records = $DB2->records();
		    if($records > 1)
		    {
		      echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		    }
		    $qBody=	    " Portefeuille = '$portefeuille' ,
			                Categorie = 'Totaal',
			                PortefeuilleWaarde = '".round($data['waardeHuidige'],2)."' ,
			                PortefeuilleBeginWaarde = '".round($data['waardeBegin'],2)."' ,
 		                  Stortingen = '".round($data['stortingen'],2)."' ,
			                Onttrekkingen = '".round($data['onttrekkingen'],2)."' ,
			                Opbrengsten = '".round($data['opbrengsten'],2)."' ,
			                Kosten = '".round($data['kosten'],2)."' ,
			                Datum = '".$datum[$i]['stop']."',
			                IndexWaarde = '$performance' ,
                      periode='$type',
			                gerealiseerd = '".round($data['gerealiseerd'],2)."',
			                ongerealiseerd = '".round($data['ongerealiseerd'],2)."',
			                rente = '".round($data['rente'],2)."',
			                extra = '".addslashes(serialize($data['extra']))."',
			                gemiddelde = '".round($data['gemiddelde'],2)."',
			                ";

		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
		        $query = "UPDATE
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR'
			               WHERE id = $id ";
		    }
		    else
		    {
			    $query = "INSERT INTO
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR',
			                add_date = NOW(),
			                add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	}
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
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
       $i++;
	  }
	  return $datum;
}

function getJaren($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,1,0,$beginjaar+$i);
	    $counterEnd   = mktime (0,0,0,1,0,$beginjaar+1+$i);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if(db2jul($datum[$i]['stop']) < db2jul($datum[$i]['start']))
         unset($datum[$i]);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getHalveMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $i=0;
	  $j=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$j,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$j+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $tusenCounter= mktime (0,0,0,$beginmaand+$j,15,$beginjaar);
	    if($tusenCounter > $counterEnd)
	    {
	      $datum[$i]['stop']=date('Y-m-d',$julEind);
        break;
	    }
      if($tusenCounter > $julBegin)
      {
	      $datum[$i]['stop']=date('Y-m-d',$tusenCounter);
	      $i++;
	      $datum[$i]['start']=date('Y-m-d',$tusenCounter);
      }
	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
        
        
      $i++;
      $j++;
	  }
	  return $datum;
}

  function getDagen2($julBegin, $julEind)
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
       $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
       $i++;
	  }
    return $datum;
  }
  
function getDagen($julBegin, $julEind,$periode='kwartaal')
{

  if($periode=='kwartaal')
    $blokken=$this->getKwartalen($julBegin, $julEind);
  elseif($periode=='maanden')
    $blokken=$this->getMaanden($julBegin, $julEind);
  elseif($periode=='jaar')
    $blokken=$this->getJaren($julBegin, $julEind);
  else
    $blokken=array('start'=>date("Y-m-d",$julBegin),'stop'=>date("Y-m-d",$julEind));

  foreach ($blokken as $blok=>$periode)
  {
    $julBegin=db2jul($periode['start']);
    $julEind=db2jul($periode['stop']);
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
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd),'blok'=>$blok);
       $i++;
	  }
  }
  return $datum;
}

  function getTWRstortingsdagen($portefeuille,$julBegin, $julEind)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='$portefeuille'  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."' AND  Rekeningmutaties.Grootboekrekening IN('STORT','ONTTR')
    GROUP BY Rekeningmutaties.Boekdatum
    ORDER BY Boekdatum";

    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$i=0;
		$start =date('Y-m-d',$julBegin);
		$eind =date('Y-m-d',$julEind);
		$lastdatum=$start;
	  while($mutaties = $DB->nextRecord())
		{
		  if($lastdatum <> $mutaties['datum'])
		  {
		    $datum[$i]['start'] = $lastdatum;
		    $datum[$i]['stop']  =$mutaties['datum'];
		  }
		  $lastdatum=$mutaties['datum'];
		  $i++;
		}

		if($lastdatum <> $eind)
		{
		  $datum[$i]['start'] = $lastdatum;
		  $datum[$i]['stop']  =$eind;
		}
		return $datum;
  }

	function getWeken($julBegin, $julEind)
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


}

?>