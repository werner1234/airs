<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/01/15 08:01:57 $
 		File Versie					: $Revision: 1.1 $

 		$Log: ATTberekening_L25.php,v $
 		Revision 1.1  2017/01/15 08:01:57  rvv
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


class ATTberekening_L101
{

	function ATTberekening_L101($rapportData)
	{
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
	  $this->indexPerformance=false;
    $this->categorien=array('totaal'=>'Totaal');
	}



	function bereken($van,$tot,$valuta='EUR',$stapeling='categorie',$periode='maanden')
	{
	  if($valuta=='')
      $valuta='EUR';
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
   // $this->categorien['totaal']='Totaal';
    $perfData['totaal']=$perfTotaal;
    if($stapeling=='categorie')
    {
      foreach ($perCategorie as $categorie => $categorieData)
      {
        $perfData[$categorie] = $this->fondsPerformance($categorieData, $van, $tot, $periode, false, $valuta, $categorie);
        if($categorieData['omschrijving']=='')
          $categorieData['omschrijving']=$categorie;
        $this->categorien[$categorie]=$categorieData['omschrijving'];
      }
    }
		elseif($stapeling=='hoofdcategorie')
      foreach ($perHoofdcategorie as $categorie=>$categorieData)
      {
        $perfData[$categorie] = $this->fondsPerformance($categorieData, $van, $tot, $periode, false, $valuta, $categorie);
        if($categorieData['omschrijving']=='')
          $categorieData['omschrijving']=$categorie;
        $this->categorien[$categorie]=$categorieData['omschrijving'];
      }

		
    return $perfData;
	}

	function fondsPerformance($fondsData,$van,$tot,$stapeling='',$totaal=false,$valuta='EUR',$categorie='leeg')
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
      //if($stapeling==false)
      $perioden[]=array('start'=>$van,'stop'=>$tot);
      //else
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
        $totaalGemiddelde = $totaalBeginwaarde + $storting['gewogen'];
        $this->totaalGemiddelde=$totaalGemiddelde;
      
        if($categorie=='totaal')
        {
          $beginwaarde = $this->totalen[$datumBegin]['totaalWaardeEur'];
          $eindwaarde = $this->totalen[$datumEind]['totaalWaardeEur'];
        
          $performance = ((($totaalEindwaarde - $totaalBeginwaarde) - $storting['totaal']) / $this->totaalGemiddelde);
          $performanceBruto=$performance;
          //echo $categorie." $datumEind  ". ($performance*100) ." = ((($eindwaarde - $beginwaarde) - ".$storting['totaal'].") / $this->totaalGemiddelde)<br>\n";ob_flush();
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
	              WHERE (Grootboekrekeningen.Opbrengst=1 ) AND Rekeningmutaties.Fonds = '' AND
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
          $DB->SQL($queryFondsDirecteKostenOpbrengsten." ");
          $DB->Query();
          $FondsDirecteKostenOpbrengsten = $DB->NextRecord();
        
          if(substr($datumEind,0,4) < 2015)
          {
            $queryAttributieStortingenOntrekkingen = "SELECT ".
              " 1 * SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) AS gewogen, ".
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
          }
          else
          {
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
          }
        
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
        
          if($categorie=='Liquiditeiten')
          {
            $RekeningDirecteKostenOpbrengsten['kostenTotaal']+= $nietToegerekendeKosten['kostenTotaal'];
          }
        
        
        
          $stort=$AttributieStortingenOntrekkingen['onttrekking']+$AttributieStortingenOntrekkingen['storting'];
          $opbr=$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'];
          $kost=($FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal']);
        

            $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
        
        
          if($gemiddelde==0)
          {
            $gemiddelde=$AttributieStortingenOntrekkingen['totaal'];
          }
        
          $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde;
          $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde;
        
        
          //echo $categorie." $datumEind  $performance = (($eindwaarde - $beginwaarde) +  $stort + $opbr ) / $gemiddelde)<br>\n";
          //listarray($AttributieStortingenOntrekkingenBruto);
// listarray($AttributieStortingenOntrekkingen);
        
        }
      
       // $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind, $fondsData['fondsen']);
        $indexData=$this->indexPerformance($categorie,$datumBegin,$datumEind);
      
      
        // $renteResultaat=$eind['renteWaarde']-$start['renteWaarde'];
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
          'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
          'resultaat'=>$resultaat,
          'gemWaarde'=>$gemiddelde,
          'indexPerf'=> $indexData['perf'],
     //     'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
     //     'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
          'weging'=>$weging,
          'aandeelOpTotaal'=>$aandeelOpTotaal,
          'bijdrage'=>$bijdrage);
      }
    
    
    
      $stapelItems=array('procent','bijdrage','procentBruto');
      $avgItems=array('weging','gemWaarde');
      $somItems=array('resultaat','storting','onttrekking','kosten','opbrengst','resultaatBruto','stort','ongerealiseerd','gerealiseerd');
      $sum=array();
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
      
        $mutaties=$this->genereerMutatieLijst($van,$tot, $fondsData['fondsen']);
        $perfData['totaal']['gerealiseerdFondsResultaat']=$mutaties['totalen']['fonds'];
        $perfData['totaal']['gerealiseerdValutaResultaat']=$mutaties['totalen']['valuta'];
        $perfData['totaal']['gerealiseerdResultaat']=$mutaties['totalen']['valuta']+$mutaties['totalen']['fonds'];
      
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
        $perfData['totaal']['ongerealiseerdResultaat']=$ongerealiseerdValutaResultaat+$ongerealiseerdFondsResultaat;
      
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
        elseif($categorie=='Liquiditeiten')
          $filter="AND Rekeningmutaties.Fonds=''";
        else
          $filter=$fondsenWhere;
        $query = "SELECT  Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten, Grootboekrekeningen.Grootboekrekening,".
          "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, ".
          "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet ".
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
        $totaalOpbrengst=0;
        $totaalKosten=0;
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
    

    $DB = new DB();
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='".$this->rapport->portefeuille."''  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND
    Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."' GROUP BY Rekeningmutaties.Boekdatum  ORDER BY Boekdatum";
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
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND
       ZorgplichtPerRisicoklasse.Risicoklasse='".$this->rapport->pdf->portefeuilledata['Risicoklasse']."' 
       ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
		   $DB->SQL($q);
		   $DB->Query();
		   while($data=$DB->nextRecord())
		     $this->normData[$data['Hoofdcategorie']]=$data['norm'];
		//	 listarray($this->rapport->pdf->portefeuilledata);

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
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);

    return $tmp;
  }


}

?>