<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/05/26 16:45:07 $
 		File Versie					: $Revision: 1.4 $

 		$Log: ATTberekening_L47.php,v $
 		Revision 1.4  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/12/19 09:03:50  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/05/04 15:59:49  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/04/17 16:00:15  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/03/23 16:19:36  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/03/17 10:58:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/03/06 16:59:28  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/02/20 15:12:14  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/02/10 10:06:07  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/02/06 19:06:11  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/02/03 09:04:21  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/01/27 14:14:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/20 13:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/01/16 16:54:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/01/13 13:35:39  rvv
 		*** empty log message ***
 		

 */


class ATTberekening_L47
{

	function ATTberekening_L47($rapportData)
	{
    $this->rapport=&$rapportData;

   	$this->rapportageDatumVanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar=date('Y',$this->rapportageDatumVanaf);
	  $this->indexPerformance=false;

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
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
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
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '$tot'
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
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
  
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
              $this->totalen[$rapDatum]['WaardeEur'][$categorie]+=$waarde;
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
    if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
      $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
    else
      $weegDatum=$datumBegin;
    $datumEind=$periode['stop'];

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
      $totaalGemiddelde = $totaalBeginwaarde - $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;
     
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
    }
    else
    {
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie];
	    $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie];//$eind['actuelePortefeuilleWaardeEuro'];

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
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds <> '' AND Grootboekrekeningen.Grootboekrekening='FONDS' "; //Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  
	     $DB->SQL($query);
	     $DB->Query();
	     $data = $DB->nextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
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
    
//	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
       $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
       $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
       
 //echo $categorie." $datumEind  $performance = ((($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde)<br>\n";
      }

      //$mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
      //$indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);


      $renteResultaat=$eind['renteWaarde']-$start['renteWaarde'];
      //listarray($FondsDirecteKostenOpbrengsten['RENMETotaal']);
      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      if($eindwaarde > 0)
      {
     //   echo $this->huidigeCategorie." $datumEind ".round($aandeelOpTotaal*100,2)."=$eindwaarde/$totaalEindwaarde; <br>\n";
      }
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
    //echo "$bijdrage=$resultaat/$gemiddelde*$weging; <br>\n";
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
$somItems=array('resultaat','storting','onttrekking','kosten','opbrengst');
foreach ($stapelItems as $item)
  $perfData['totaal'][$item]=1;
  
foreach ($waarden as $datum=>$waarde)
{
  if(!isset($perfData['totaal']['beginwaarde']))
    $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];
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

?>