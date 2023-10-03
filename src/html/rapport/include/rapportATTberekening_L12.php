<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/02/21 17:15:09 $
 		File Versie					: $Revision: 1.10 $

 		$Log: ATTberekening_L4.php,v $
 		Revision 1.10  2018/02/21 17:15:09  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/01/08 18:10:13  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/05/26 16:45:07  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/12/19 09:03:50  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/09/02 15:53:18  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/05/10 13:37:43  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/05/10 08:02:25  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/05/03 14:14:38  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/04/16 10:55:36  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/04/15 18:25:11  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/01/17 18:32:01  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/10/04 15:22:54  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/09/28 14:43:25  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/07/04 15:40:04  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/06/26 15:55:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/06/19 15:54:30  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/06/12 18:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/05/26 13:54:49  rvv
 		*** empty log message ***
 		
 	
 */

class rapportHulp_L12
{
  function rapportHulp_L12()
  {
    $this->test='';
  }
}


class rapportATTberekening_L12
{
  
  function rapportATTberekening_L12($rapportData)
  {
    $this->rapport=&$rapportData;
    $this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
    $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
    $this->rapport_jaar=date('Y',$this->rapport_datumvanaf);
    $this->indexPerformance=false;
    
  }
  
  function getPerf($portefeuille, $datumBegin, $datumEind)
  {
    $this->rapport = new rapportHulp_L12();
    $db=new DB();
    $query="SELECT * FROM Portefeuilles WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $db->lookupRecord();
    
    $this->rapport->pdf->portefeuilledata=$db->lookupRecord();
    $this->rapport->portefeuille=$portefeuille;
    $this->rapport_datumvanaf=db2jul($datumBegin);
    $this->rapport_datum=db2jul($datumEind);
    $this->rapport_startjaar  =date('Y',$this->rapport_datumvanaf);
    $this->rapport_jaar  =date('Y',$this->rapport_datum);
    $perfData=$this->bereken($datumBegin, $datumEind,'totaal');
    
    return $perfData['totaal']['procent'];
  }
  
  function bereken($van,$tot,$verdeling='attributie',$valuta='EUR')
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
		elseif($verdeling=='attributie')
    {
      $categorieFilter='AttributieCategorien';
      $join="LEFT JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde = AttributieCategorien.AttributieCategorie";
      $selectOmschrijving=',AttributieCategorien.Omschrijving';
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
    $perAttributie=$tmp;
  
    $koppelOmschrijvingen=getAfdrukVolgordeOmschrijving($this->rapport->pdf->portefeuilledata['Vermogensbeheerder']);
    
    
    $query="SELECT waarde as Beleggingscategorie,Omschrijving,KeuzePerVermogensbeheerder.Afdrukvolgorde FROM KeuzePerVermogensbeheerder
    JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie
    WHERE categorie='Beleggingscategorien' AND Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde desc";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $perCategorie[$data['Beleggingscategorie']]['categorie']=$data['Beleggingscategorie'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['Omschrijving'];
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
BeleggingssectorPerFonds.AttributieCategorie,
Beleggingssectoren.Omschrijving as sectorOmschrijving,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
AttributieCategorien.Omschrijving as AttributieOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT JOIN AttributieCategorien ON BeleggingssectorPerFonds.AttributieCategorie = AttributieCategorien.AttributieCategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_startjaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Beleggingssectoren.Afdrukvolgorde,Fondsen.Omschrijving ";
    
    $DB->SQL($query);
    $DB->Query();
    $koppelVelden=array('beleggingssector'=>'Beleggingssector','AttributieCategorie'=>'AttributieCategorie','beleggingscategorie'=>'Beleggingscategorie');
    
    while($data = $DB->NextRecord())
    {
      $koppelingdata=getFondsKoppelingen($this->rapport->pdf->portefeuilledata['Vermogensbeheerder'],$tot,$data['Fonds'],1);
      foreach($koppelVelden as $bronVeld=>$doelveld)
      {
        if($koppelingdata[$bronVeld] != $data[$doelveld])
        {
          $data[$doelveld]=$koppelingdata[$bronVeld];
          if($doelveld=='Beleggingssector')
          {
            $data['sectorOmschrijving']=$koppelOmschrijvingen['omschrijving']['Beleggingssectoren'][$data[$doelveld]];
          }
          if($doelveld=='Beleggingscategorie')
          {
            $data['categorieOmschrijving'] = $koppelOmschrijvingen['omschrijving']['Beleggingscategorien'][$data[$doelveld]];
            $data['Hoofdcategorie']=$koppelOmschrijvingen['hoofdcategoriePerCategorie'][$data[$doelveld]];
            $data['hoofdCategorieOmschrijving']=$koppelOmschrijvingen['omschrijving']['Beleggingscategorien'][$data['Hoofdcategorie']];
          }
          if($doelveld=='AttributieCategorie')
          {
            $data['AttributieOmschrijving'] = $koppelOmschrijvingen['omschrijving']['AttributieCategorien'][$data[$doelveld]];
          }
        }
      }
      
      if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='Geen H-cat';
      if($data['AttributieCategorie']=='')
        $data['AttributieCategorie']='Geen Att-Cat';
      
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
      $perAttributie[$data['AttributieCategorie']]['omschrijving']=$data['AttributieOmschrijving'];
      $perAttributie[$data['AttributieCategorie']]['fondsen'][]=$data['Fonds'];
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
Rekeningen.AttributieCategorie,
AttributieCategorien.Omschrijving AS AttributieOmschrijving,
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
LEFT JOIN AttributieCategorien ON Rekeningen.AttributieCategorie = AttributieCategorien.AttributieCategorie
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_startjaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '$tot'
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
      if($data['AttributieCategorie']=='')
        $data['AttributieCategorie']='Liquiditeiten';
      
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perSector[$data['Beleggingssector']]['omschrijving']=$data['sectorOmschrijving'];
      $perSector[$data['Beleggingssector']]['fondsen'][]=$data['Fonds'];
      $perSector[$data['Beleggingssector']]['rekeningen'][]=$data['rekening'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
      $perAttributie[$data['AttributieCategorie']]['omschrijving']=$data['AttributieOmschrijving'];
      $perAttributie[$data['AttributieCategorie']]['rekeningen'][]=$data['rekening'];
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
		elseif($verdeling=='attributie')
      $categorien=$perAttributie;
    else
      $categorien=$perCategorie;
    
    
    
    $this->huidigeCategorie='totaal';
    $perfData['totaal'] = $this->fondsPerformance($alleData,$van,$tot,true,'totaal',$valuta);
    
    foreach ($categorien as $categorie=>$categorieData)
    {
      $this->huidigeCategorie=$categorie;
      $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,true,$categorie,$valuta);
      if($categorieData['omschrijving']=='')
        $categorieData['omschrijving']=$categorie;
      $this->categorien[$categorie]=$categorieData['omschrijving'];
    }
    
    //$this->categorien['totaal']='Totaal';
    //listarray($perfData);
    
    return $perfData;
  }
  

  
  
  
  function fondsPerformance($fondsData,$van,$tot,$stapeling=false,$categorie='',$valuta='EUR')
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
    elseif(count($fondsData['rekeningen'])>0)
    {
      $liqCat=true;
    }

    //
    //    $grootboekKostenFilter='OR Grootboekrekeningen.Kosten =1';
    // else
    $grootboekKostenFilter='';
    
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

      
      $totaalBeginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur']/$startValutaKoers;
      $totaalEindwaarde = $this->totalen[$datumEind]['totaalWaardeEur']/$eindValutaKoers;
      
      
      $query="SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1";
      $DB->SQL($query);
      $DB->Query();
      $grootboekrekeningen=array();
      while($grootboekrekening=$DB->nextRecord())
        $grootboekrekeningen[]=$grootboekrekening['Grootboekrekening'];
      

      $query = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
        "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))  AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal
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
//      }
      
      $DB->SQL($query);
      $DB->Query();
      $storting = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
      $this->totaalGemiddelde=$totaalGemiddelde;
      
      if($categorie=='totaal')
      {
        $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur']/$startValutaKoers;
        $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur']/$eindValutaKoers;
        
        $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
        $procentBerekening= " $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - ".$storting['totaal'].") / ".$this->totaalGemiddelde.")\n";
        //echo "$weegDatum $totaalGemiddelde = $totaalBeginwaarde + ".$storting['gewogen']."<br>\n";
        //echo $categorie." $datumEind  $performance = ((($eindwaarde - $beginwaarde) - ".$storting['totaal'].") / $this->totaalGemiddelde)<br>\n";ob_flush();
        $stortingen 			 	= getStortingen($this->rapport->portefeuille,$datumBegin,$datumEind,$valuta);
        $onttrekkingen 		 	= getOnttrekkingen($this->rapport->portefeuille,$datumBegin,$datumEind,$valuta);
        $AttributieStortingenOntrekkingen['storting']=$stortingen;
        $AttributieStortingenOntrekkingen['onttrekking']=$onttrekkingen;
        $AttributieStortingenOntrekkingen['totaal']=$storting['totaal'];
      }
      else
      {
        $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
        $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
        $beginwaarde = $this->totalen[$datumBegin]['WaardeEur'][$categorie]/$startValutaKoers;
        $eindwaarde = $this->totalen[$datumEind]['WaardeEur'][$categorie]/$eindValutaKoers;//$eind['actuelePortefeuilleWaardeEuro'];
        

          $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))*-1  AS gewogen, ".
            "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking ".
            "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ". //OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1
            "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
            "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
      
        $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
        $DB->Query();
        $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
        
        $queryRekeningDirecteKostenOpbrengsten = "SELECT
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 $grootboekKostenFilter) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
          "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
          "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
        $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
        $DB->Query();
        $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
        
        $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0)) as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
        $DB->SQL($queryFondsDirecteKostenOpbrengsten." ");
        $DB->Query();
        $FondsDirecteKostenOpbrengsten = $DB->NextRecord();
        

          $queryAttributieStortingenOntrekkingen = "SELECT ".
            "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
            "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
            "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1 $koersQuery,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery,0))  AS onttrekking ".
            "FROM  (Rekeningen, Portefeuilles)
                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
            "WHERE ".
            "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND  Rekeningmutaties.transactieType <> 'B' AND ".
            "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
            "Rekeningmutaties.Verwerkt = '1' AND ".
            "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
            "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
            "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere ";//
            
        $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        //$DB->SQL($queryAttributieStortingenOntrekkingen." AND Rekeningmutaties.Grootboekrekening='FONDS' "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        
        $DB->Query();
        $AttributieStortingenOntrekkingen = $DB->NextRecord();//echo "$categorie | $datumEind | $queryAttributieStortingenOntrekkingen <br>\n";
        
        $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
        $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
        $DB->Query();
        $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
        
        
        $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
        
        $query = "SELECT
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles)
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                
	              WHERE
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.transactieType <> 'B' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Kruispost=1 OR Rekeningmutaties.Fonds <> ''  )";
        $DB->SQL($query);
        
        $DB->Query();
        $data = $DB->nextRecord();
        $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
        $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
        $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
        
        if($categorie=='Liquiditeiten')
          $DB->SQL($query);
        else
          $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
        $DB->Query();
        $data = $DB->nextRecord();
        
        $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
        $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
        $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];
        
        //   if($categorie=='Liquiditeiten')
        //      listarray($AttributieStortingenOntrekkingenBruto);
        /*
        if($categorie<>'totaal')
        {
          
          $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.transactieType <> 'B' AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = ''  "; // AND $rekeningRekeningenWhere
          $DB->SQL($queryKostenOpbrengsten);
          $DB->Query();
          $nietToegerekendeKosten = $DB->NextRecord();
          
          $eindAandeel=$eindwaarde/$totaalEindwaarde;
          //echo "$categorie | $eindAandeel | ".$nietToegerekendeKosten['kostenTotaal']."<br>\n";
          $nietToegerekendeKostenExtra=$nietToegerekendeKosten['kostenTotaal']*$eindAandeel;
          
          
          if($categorie=='Liquiditeiten')
          {
            $AttributieStortingenOntrekkingen['totaal']+= $nietToegerekendeKosten['kostenTotaal']*(1-$eindAandeel);
          }
          else
          {
            $AttributieStortingenOntrekkingen['totaal']-= $nietToegerekendeKostenExtra;
            $FondsDirecteKostenOpbrengsten['kostenTotaal']+= $nietToegerekendeKostenExtra;
          }
          
        }
        */
        
        
        $stort=$AttributieStortingenOntrekkingen['onttrekking']+$AttributieStortingenOntrekkingen['storting'];
        $opbr=$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal']+$FondsDirecteKostenOpbrengsten['RENMETotaal'];
        $kost=($FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal']);
        

        
        if($beginwaarde==0)
        {
          $AttributieStortingenOntrekkingen['gewogen'] = $AttributieStortingenOntrekkingen['totaal']*-1;
        }
        if($eindwaarde==0)
        {
          $AttributieStortingenOntrekkingen['gewogen'] = 0;
        }
        $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
        
        $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde;
        $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde;
        $procentBerekening= "$performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde \n";
        
     //   echo $categorie." $datumEind  $performance = (($eindwaarde - $beginwaarde) +  $stort + $opbr ) / $gemiddelde)<br>\n";
        //listarray($AttributieStortingenOntrekkingenBruto);
// listarray($AttributieStortingenOntrekkingen);
      
      }
      
      //$mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
      //$indexData=$this->indexPerformance($fondsData['categorie'],$datumBegin,$datumEind);
      
      
      $renteResultaat=$eind['renteWaarde']-$start['renteWaarde'];
      $weging=$gemiddelde/$this->totaalGemiddelde;//$this->totalen['gemiddeldeWaarde'];
      $aandeelOpTotaal=$eindwaarde/$totaalEindwaarde;
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $resultaaBruto=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal'] - $RekeningDirecteKostenOpbrengsten['kostenTotaal']; //
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
        'begindatum'=>$datumBegin,
        'beginwaarde'=>$beginwaarde,
        'eindwaarde'=>$eindwaarde,
        'procent'=>$performance,
        'procentBruto'=>$performanceBruto,
        'resultaatBruto'=>$resultaaBruto,
        'stort'=>$AttributieStortingenOntrekkingen['totaal'],
        'storting'=>$AttributieStortingenOntrekkingen['storting'],
        'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
        'extraKosten'=>$nietToegerekendeKostenExtra,
        'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal']+$FondsDirecteKostenOpbrengsten['RENMETotaal'],
        'resultaat'=>$resultaat,
        'gemWaarde'=>$gemiddelde,
        'weging'=>$weging,
        'aandeelOpTotaal'=>$aandeelOpTotaal,
        'bijdrage'=>$bijdrage);
      
      if($this->rapport->pdf->lastPOST['debug']==1)
      {
        $waarden[$datumEind]['procentBerekening']=$procentBerekening;
      }
    }
    
    
    
    $stapelItems=array('procent','bijdrage','procentBruto');
    $avgItems=array('weging','gemWaarde');
    $somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','resultaatBruto','stort','extraKosten');
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=1;
    
    foreach ($waarden as $datum=>$waarde)
    {
      // echo $waarde['begindatum']."- $van ".$waarde['beginwaarde']."<br>\n";
      //if(!isset($perfData['totaal']['beginwaarde']))
      if($waarde['begindatum']==$van)
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
  
      if ($valuta <> 'EUR')
      {
        $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
        $startValutaKoers= getValutaKoers($valuta,$van);
        $eindValutaKoers= getValutaKoers($valuta,$tot);
      }
      else
      {
        $koersQuery = "";
        $startValutaKoers= 1;
        $eindValutaKoers= 1;
      }
      
      $mutaties=$this->genereerMutatieLijst($van,$tot, $fondsData['fondsen'],$valuta);
      $perfData['totaal']['gerealiseerdFondsResultaat']=$mutaties['totalen']['fonds'];
      $perfData['totaal']['gerealiseerdValutaResultaat']=$mutaties['totalen']['valuta'];
      
      //historischeWaarde
      $fondsenWhere="AND Fonds IN('".implode('\',\'',$fondsData['fondsen'])."')";
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro/$eindValutaKoers - beginPortefeuilleWaardeEuro/$startValutaKoers ) AS resultaatEUR,
  SUM(totaalAantal*fondsEenheid*(actueleFonds-beginwaardeLopendeJaar)*actueleValuta)/$eindValutaKoers as fondsresultaatEUR".
        " FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum ='$tot' $fondsenWhere AND".
        " portefeuille = '".$this->rapport->portefeuille."' AND "
        ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaal = $DB->nextRecord();
      $ongerealiseerdFondsResultaat = $totaal['fondsresultaatEUR'] ;
      $ongerealiseerdValutaResultaat = $totaal['resultaatEUR']-$totaal['fondsresultaatEUR'] ;
      $perfData['totaal']['ongerealiseerdFondsResultaat']=$ongerealiseerdFondsResultaat;
      $perfData['totaal']['ongerealiseerdValutaResultaat']=$ongerealiseerdValutaResultaat;
      
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro)/$eindValutaKoers AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$tot' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalA = $DB->nextRecord();
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro)/$startValutaKoers AS totaal FROM TijdelijkeRapportage WHERE rapportageDatum ='$van' AND portefeuille = '".$this->rapport->portefeuille."' AND  type = 'rente' $fondsenWhere ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $totaalB = $DB->nextRecord();
      $perfData['totaal']['opgelopenrente'] = ($totaalA['totaal'] - $totaalB['totaal']) ;
      
      if($categorie=='totaal')
        $filter='';
			elseif($categorie=='Liquiditeiten')
        $filter="AND Rekeningmutaties.Fonds=''";
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
        "Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.transactieType <> 'B' AND ".
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
      
      $perfData['totaal']['opbrengst']=$totaalOpbrengst;
      $perfData['totaal']['grootboekOpbrengsten']=$opbrengstenPerGrootboek;
      if($categorie=='Liquiditeiten')
        $perfData['totaal']['kosten']=$nietToegerekendeKostenExtra;
      else
        $perfData['totaal']['kosten']=$totaalKosten+$nietToegerekendeKostenExtra;
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
  
  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='',$valuta)
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
		elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='1';
  
    if ($valuta <> 'EUR')
    {
      $koersQuery =	" (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "1";
    }
    
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
      "Rekeningmutaties.Valutakoers,
       ($koersQuery ) as Rapportagekoers ".
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
    //listarray($buffer);
    $data=array();
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
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
      //$mutaties['Rapportagekoers']=1;
      
      switch($mutaties['Transactietype'])
      {
        case "A" :
        case "A/O" :
        case "A/S" :
        case "D" :
        case "S" :
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] / $mutaties['Rapportagekoers'];
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
        $historie = berekenHistorischKostprijs($this->rapport->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$valuta,$rapportageDatumVanaf,'');
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
      {// listarray($historie);
        //$historischekostprijsValuta = $mutaties['Aantal']*$historie['historischeWaarde']* $mutaties['Fondseenheid'];//$historischekostprijs = $mutaties[Aantal]        * $historie[historischeWaarde]       * $historie[historischeValutakoers]        * $mutaties[Fondseenheid];
        $historischekostprijsValuta = $mutaties['Aantal']*$historie['beginwaardeLopendeJaar']* $mutaties['Fondseenheid'];
        $fondsResultaat = ($t_verkoop_waardeinValuta-$historischekostprijsValuta) * getValutaKoers($mutaties['Valuta'],$mutaties['Boekdatum'])/$mutaties['Rapportagekoers'];
        $valutaResultaat=	($result_lopendejaar)-$fondsResultaat;  //$resultaatvoorgaande
      }
 
      $data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
      $data['totalen']['fonds']+=$fondsResultaat;
      $data['totalen']['valuta']+=$valutaResultaat;
    }
    //listarray($data);exit;
    return $data;
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
    //$waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
    $tmp= array('perf'=>$perf,'bijdrage'=>$perf*$fondsData['Percentage'],'datum'=>$tot,'percentage'=>$fondsData['Percentage'],'categorie'=>$categorie,'koersVan'=>$startKoers['Koers'],'koersEind'=>$eindKoers['Koers']);//,'waarden'=>$waarden)
    
    return $tmp;
  }
}
?>