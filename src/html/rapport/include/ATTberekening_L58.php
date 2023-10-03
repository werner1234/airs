<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/01/21 13:47:11 $
 		File Versie					: $Revision: 1.17 $

 		$Log: ATTberekening_L58.php,v $
 		Revision 1.17  2016/01/21 13:47:11  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/01/20 17:16:02  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2015/12/19 08:29:17  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2015/05/06 15:35:53  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/05/02 14:57:32  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/04/23 14:06:10  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/04/22 10:32:05  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/04/19 08:35:37  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/04/01 16:00:45  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/03/29 07:43:24  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/02/22 09:55:14  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/02/02 08:20:06  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/31 20:02:23  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/01/22 14:52:32  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/01/20 13:00:06  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/12 16:50:48  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/11/08 18:37:31  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/01/15 14:21:32  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/10/02 15:48:20  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/07/17 15:53:14  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/13 15:19:44  rvv
 		*** empty log message ***
 		
 	
 */

class rapportHulp_L58
{
  function rapportHulp_L58()
  {
    $this->test='';
  }
}


class ATTberekening_L58
{

	function ATTberekening_L58($rapportData)
	{
    $this->rapport=&$rapportData;
   	$this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
	  $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
	  $this->rapport_jaar  =date('Y',$this->rapport_datum);
	  $this->indexPerformance=false;
	}
	
	function getPerfArray($portefeuille, $rapportStart,$rapportDatum,$valuta)
  {
    $this->rapport = new rapportHulp_L58();
    $db=new DB();
    $query="SELECT * FROM Portefeuilles WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $db->lookupRecord();
  
    $this->rapport->pdf->portefeuilledata=$db->lookupRecord();
    $this->rapport->portefeuille=$portefeuille;
    $this->rapport_datumvanaf=db2jul($rapportStart);
    $this->rapport_datum=db2jul($rapportDatum);
    $this->rapport_jaar  =date('Y',$this->rapport_datumvanaf);
    
    $datum=$this->getMaandenYtd($this->rapport_datumvanaf,$this->rapport_datum,true);
    $portefeuilleStarJul=db2jul( $this->rapport->pdf->portefeuilledata['Startdatum']);
    $perfData=array();
    $jarenCumu=0;
    $lastCumu=0;
    $start='';
 
	foreach ($datum as $index=>$periode)
  {
    if($portefeuilleStarJul > db2jul($periode['start']) && $portefeuilleStarJul < db2jul($periode['stop']))
    {
      $periode['start']=substr($this->rapport->pdf->portefeuilledata['Startdatum'],0,10);
    }
    $data = $this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille);
    $data['datum'] = jul2sql(form2jul(substr($data['periodeForm'],-10,10)));
    $data['index'] = ($data['index']  * (100+$data['performance'])/100);

      if($data['datum'] != '0000-00-00')
      {
      
        $periode=explode('->',$data['periode']);
        if($start=='')
          $start=$periode[0];
        $stop=$periode[1];
      
        $perfCumu=$data['performance'];
        $perfCumuTotaal=((1+$data['performance']/100)*(1+$jarenCumu/100)-1)*100;
        if(substr($stop,5,5) =='12-31')
        {
          $jarenCumu=$perfCumuTotaal;
        }
        $maandPerf=((1+$perfCumu/100)/(1+$lastCumu/100)-1)*100;
      
        if(substr($stop,5,5) =='12-31')
        {
          $lastCumu=0;
        }
        else
        {
          $lastCumu = $perfCumu;
        }
        $data['performance']=$maandPerf;
        $data['index']=$perfCumuTotaal;
  
        $perfData[$stop]['index'] = $data['index'];
        $perfData[$stop]['performance'] = $data['performance'];
       }
    }
    return $perfData;
  }

function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
{
  $totaalWaarde =array();
  //$db = new DB();
  $koersQuery='';
  
  if(substr($this->rapport->pdf->portefeuilledata['Startdatum'],0,10) == $beginDatum)
    $wegingsDatum=date('Y-m-d',db2jul($beginDatum)+86400);
  else
    $wegingsDatum=$beginDatum;
  //  echo "$wegingsDatum=$beginDatum; <br>\n";
  //$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);
  $totaalWaardeExtLiq=array();
  $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,(substr($beginDatum,5,5)=='01-01'?true:false),'EUR',$beginDatum);
  foreach ($fondswaarden['beginmaand'] as $regel)
  {
    if($regel['beleggingscategorie'] == 'Liq-Extern')
      $totaalWaardeExtLiq['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
    
    $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
    if($regel['type']=='rente' && $regel['fonds'] != '')
      $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
  }
  
  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,(substr($eindDatum,5,5)=='01-01'?true:false),'EUR',$beginDatum);
  
  
  foreach ($fondswaarden['eindmaand'] as $regel)
  {
    if($regel['beleggingscategorie'] == 'Liq-Extern')
      $totaalWaardeExtLiq['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
    
    $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
    
    if($regel['type']=='fondsen')
    {
      $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
      $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
      //$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    elseif($regel['type']=='rente' && $regel['fonds'] != '')
    {
      $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
      //$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    elseif($regel['type']=='rekening')
    {
      //$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      // $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
  }
  
  //$ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
  $DB=new DB();
  
  $query = "SELECT ".
    "SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
    "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1,
  SUM(IF(Beleggingscategorie = 'Liq-Extern',((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )),0 )) AS extLiqGewogen, ".
    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2, ".
    "SUM(IF(Beleggingscategorie = 'Liq-Extern',(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as extLiq
  FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
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
  
  // listarray($totaalWaardeExtLiq); ob_flush();
  
  
  
  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  $gemiddeldeExtLiq = $totaalWaardeExtLiq['begin'] + $weging['extLiqGewogen'];
  
  $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / ($gemiddelde -$gemiddeldeExtLiq) ) * 100;
  $performanceNormaal = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / ($gemiddelde) ) * 100;
// echo "$beginDatum -> $eindDatum | $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / ($gemiddelde -$gemiddeldeExtLiq) ) * 100 <br>\n";
//if(db2jul($beginDatum)>db2jul('2014-12-21'))
  //echo "ATT | ".$totaalWaardeExtLiq['begin']." + ".$weging['extLiqGewogen']." | $beginDatum -> $eindDatum | $wegingsDatum | $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / ( $gemiddelde -$gemiddeldeExtLiq)) * 100; <br>\n"; //
  
  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
  $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
  $onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
  $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
  /*
  $query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
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
  
  
  $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
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
  */
  //$db->SQL($query);
  //$opbrengsten = $db->lookupRecord();
  
  //$opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
  //$valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
  //$ongerealiseerd+=$valutaResultaat;
  
  $data['periode']= $beginDatum."->".$eindDatum;
  $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
  $data['waardeBegin']=round($totaalWaarde['begin'],2);
  $data['waardeHuidige']=round($totaalWaarde['eind'],2);
  $data['waardeMutatie']=round($waardeMutatie,2);
  $data['stortingen']=round($stortingen,2);
  $data['onttrekkingen']=round($onttrekkingen,2);
  $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
  //$data['kosten'] = round($kosten['totaalkosten'],2);
 // $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
  $data['performance'] =$performance;
  $data['performanceNormaal'] = $performanceNormaal;
  //$data['ongerealiseerd'] =$ongerealiseerd;
  //$data['rente'] = $opgelopenRente;
  //$data['gerealiseerd'] =$koersResultaat;
 // $data['extra']=array('cat'=>$categorieVerdeling);
  return $data;
  
}
  
  function getPerf($portefeuille, $datumBegin, $datumEind,$valuta)
  {
    $tmp=$this->getPerfArray($portefeuille, $datumBegin, $datumEind, $valuta);
    return $tmp[$datumEind]['index'];
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
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
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
Rekeningen.AttributieCategorie,
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
    elseif($stapeling=='maandenYTD')
      $perioden=$this->getMaandenYtd(db2jul($van),db2jul($tot),true);
    elseif($stapeling=='weken')
      $perioden=$this->getWeken(db2jul($van),db2jul($tot));  
    elseif($stapeling=='dagen')
      $perioden=$this->getDagen(db2jul($van),db2jul($tot));
    elseif($stapeling=='jaar')
      $perioden=$this->getJaren(db2jul($van),db2jul($tot));
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

    $portefeuilleStarJul=db2jul($this->rapport->pdf->PortefeuilleStartdatum);
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
      $bfilter='AND Rekeningmutaties.transactieType <> \'B\' ';

      if ($valuta <> 'EUR' && $valuta <> '')
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
	    $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];// echo "$query ";exit;
      //echo "$datumBegin $weegDatum <br>\n";

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
	    "FROM  (Rekeningen)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	    "WHERE ".
      "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
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
	         JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	      WHERE
        Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' $bfilter AND
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
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) $bfilter AND ".
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
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' $bfilter AND $rekeningRekeningenWhere ";
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
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin'  $bfilter AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
      $DB->SQL($queryFondsDirecteKostenOpbrengsten);
      $DB->Query();
      $FondsDirecteKostenOpbrengsten = $DB->NextRecord(); //echo "$queryFondsDirecteKostenOpbrengsten <br><br>\n";


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              " SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) AS gewogen, ".
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
	              "Rekeningmutaties.Fonds <> '' $bfilter AND $rekeningFondsenWhere ";//
                
      $DB->SQL($queryAttributieStortingenOntrekkingen);// echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
      $DB->Query();
      $AttributieStortingenOntrekkingen = $DB->NextRecord();

	  //  $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];

      $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
      $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	    $DB->Query();
	    $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();

   	  $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' $bfilter AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
	    $DB->SQL($query);//echo "$query <br><br>\n";
	    $DB->Query();
	    $data = $DB->nextRecord();
	    $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
	    $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
	    $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];

       if($catnaam=='Liquiditeiten' || $catnaam=='Liq-Extern')
         $DB->SQL($query);
       else
         $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
	     $DB->Query(); 
	     $data = $DB->nextRecord();

	     $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
	     $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
	     $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];
       
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
       
	    $DB->SQL($queryKostenOpbrengsten);
	    $DB->Query();
	    $nietToegerekendeKosten = $DB->NextRecord();
//listarray($nietToegerekendeKosten);listarray($queryKostenOpbrengsten);
	//    $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];


     $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
     if($catnaam=='totaal' )
     {
       $kosten=$this->getKosten($this->rapport->portefeuille,$datumBegin,$datumEind);
       
       $query = "SELECT ".
	"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1
  FROM  (Rekeningen)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' $bfilter AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);//echo "<br>\n".$query."<br>\n";
	$DB->Query();
	$weging = $DB->NextRecord();
     $gemiddelde = $beginwaarde + $weging['totaal1'];
//listarray($kosten);
       $performanceBruto = ((($eindwaarde - $beginwaarde) - round($AttributieStortingenOntrekkingen['totaal'],2) -$kosten['kostenTotaal'] )/ $gemiddelde);
       $performance = ((($eindwaarde - $beginwaarde) - round($AttributieStortingenOntrekkingen['totaal'],2)) / $gemiddelde);
       $resultaat    =($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
       // echo " $performance = ((($eindwaarde - $beginwaarde) - round(".$AttributieStortingenOntrekkingen['totaal'].",2)) / $gemiddelde)<br>\n";
       $resultaaBruto =($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] -$kosten['kostenTotaal'];
       $FondsDirecteKostenOpbrengsten=array();
       $RekeningDirecteKostenOpbrengsten=array();
       $FondsDirecteKostenOpbrengsten=array();
       $RekeningDirecteKostenOpbrengsten['kostenTotaal']=$kosten['kostenTotaal'];
  
      $performanceTotaalZonderExtLiq='';
  

       
       ///----------------------------
       ///
       /// 	   	if($regel['beleggingscategorie'] == '')
       //       $totaalWaardeExtLiq['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
       $query = "SELECT ".
         "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
         "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
         "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1,
  SUM(IF(Beleggingscategorie = 'Liq-Extern',((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )),0 )) AS extLiqGewogen, ".
         "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2, ".
         "SUM(IF(Beleggingscategorie = 'Liq-Extern',(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0)) as extLiq
  FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
         "WHERE ".
         "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
         "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
         "Rekeningmutaties.Verwerkt = '1' AND ".
         "Rekeningmutaties.Boekdatum > '".$datumBegin."' $bfilter AND ".
         "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
         "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
       $DB->SQL($query);
       $DB->Query();
       $weging = $DB->NextRecord();
  
       // listarray($totaalWaardeExtLiq); ob_flush();
  
       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
                Beleggingscategorie='Liq-Extern' ".$__appvar['TijdelijkeRapportageMaakUniek'];
       $DB->SQL($query);
       $DB->Query();
       $totaalWaardeExtLiq = $DB->NextRecord();
       $liqWaarde['start']=$totaalWaardeExtLiq['actuelePortefeuilleWaardeEuro'];
       $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro
                FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind' AND
                Beleggingscategorie='Liq-Extern' ".$__appvar['TijdelijkeRapportageMaakUniek'];
       $DB->SQL($query);
       $DB->Query();
       $totaalWaardeExtLiq = $DB->NextRecord();
       $liqWaarde['eind']=$totaalWaardeExtLiq['actuelePortefeuilleWaardeEuro'];
       
  
       $gemiddelde = $beginwaarde + $weging['totaal1'];
       $gemiddeldeExtLiq = $liqWaarde['start'] + $weging['extLiqGewogen'];
  
       $performanceTotaalZonderExtLiq = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / ($gemiddelde -$gemiddeldeExtLiq) ) ;
       //echo "PERF $datumBegin $datumEind | $performanceTotaalZonderExtLiq = ((($eindwaarde-".$liqWaarde['start']." - $beginwaarde-".$liqWaarde['eind'].") - ".$weging['totaal2'].") / ($gemiddelde -$gemiddeldeExtLiq) ) ;  <br>\n";ob_flush();
       //$performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / ($gemiddelde) ) ;
      
     }
     else
     {//listarray($FondsDirecteKostenOpbrengsten);
     
       $stort=$AttributieStortingenOntrekkingen['onttrekking']+$AttributieStortingenOntrekkingen['storting'];
       $opbr=$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal']+$FondsDirecteKostenOpbrengsten['RENMETotaal'];
       $kost=($FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal']);
       if($catnaam=='Liquiditeiten')
       {
         $stort+=$nietToegerekendeKosten['kostenTotaal'];
         $kost+=$nietToegerekendeKosten['kostenTotaal'];
       
         //$FondsDirecteKostenOpbrengsten['kostenTotaal']+=$nietToegerekendeKosten['kostenTotaal'];
         $RekeningDirecteKostenOpbrengsten['kostenTotaal']+=$nietToegerekendeKosten['kostenTotaal'];
         $AttributieStortingenOntrekkingen['onttrekking']+=$nietToegerekendeKosten['kostenTotaal'];
         $nietToegerekendeKosten['kostenTotaal']=0;
       }

       
       $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde;
       $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde;
       //echo "$catnaam | $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde;<br>\n";
       $resultaaBruto=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingenBruto['totaal'] - $RekeningDirecteKostenOpbrengsten['kostenTotaal']; //
       if($catnaam=='Liq-Extern')
       {
          $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro  FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumEind'".$__appvar['TijdelijkeRapportageMaakUniek'];
          $DB->SQL($query);
	        $DB->Query();
	        $start = $DB->NextRecord();
	        $totaalEindwaarde = $start['actuelePortefeuilleWaardeEuro'];

          $query = "SELECT ".
	        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	        "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	        " SUM(IF(Beleggingscategorie = 'Liq-Extern',((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )),0 )) AS extLiqGewogen, 
          SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2, 
          SUM(IF(Beleggingscategorie = 'Liq-Extern',((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)),0))  AS extLiq ".
	        "FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	        Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	        "WHERE ".
	        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' $bfilter AND ".
	        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	        "Rekeningmutaties.Verwerkt = '1' AND ".
	        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	        Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	        $DB->SQL($query); //echo "$query <br>\n";
	        $DB->Query();
	        $weging = $DB->NextRecord();

          $totaalGemiddelde=$totaalBeginwaarde+$weging['totaal1'];
          $gemiddeldeExtLiq = $beginwaarde + $weging['extLiqGewogen'];
          $stort=$weging['extLiq'];
          $AttributieStortingenOntrekkingen['totaal']=$stort;
          $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddeldeExtLiq;
          // echo "$performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddeldeExtLiq; <br>\n";
          //echo " $gemiddelde $gemiddeldeExtLiq | $beginwaarde + ".$weging['extLiqGewogen']." <br>\n";
          
          $performanceTotaalZonderExtLiq = ((($totaalEindwaarde - $totaalBeginwaarde) - round($weging['totaal2'],2)) / ($totaalGemiddelde-$gemiddeldeExtLiq));
          //echo "Extern $performanceTotaalZonderExtLiq = ((($totaalEindwaarde - $totaalBeginwaarde) - ".round($weging['totaal2'],2).") / ($totaalGemiddelde-$gemiddeldeExtLiq)); <br>\n";ob_flush();
          //echo "PERF | $totaalGemiddelde-$gemiddelde | $datumBegin -> $datumEind | $weegDatum | $performanceTotaalZonderExtLiq = ((($totaalEindwaarde - $totaalBeginwaarde) - ".round($weging['totaal2'],2).") / ($totaalGemiddelde-$gemiddeldeExtLiq));<br>\n";
         
          //echo $performanceTotaal; ob_flush();
       }

     }
     
       if($catnaam=='Liquiditeiten11')
       {
        
        echo "n $performance      = (($eindwaarde - $beginwaarde) - $stort + $opbr + $kost) / $gemiddelde; <br>\n";
        echo "b $performanceBruto = (($eindwaarde - $beginwaarde) - $stort + $opbr ) / $gemiddelde; ";
        echo "---RekeningDirecteKostenOpbrengste<br>\n";
        listarray($RekeningDirecteKostenOpbrengsten);
         echo "---FondsDirecteKostenOpbrengsten<br>\n";
        listarray($FondsDirecteKostenOpbrengsten);
         echo "---nietToegerekendeKosten<br>\n";
        listarray($nietToegerekendeKosten);
        echo "- -<br>\n";
        ob_flush();
         
       }


//echo  round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
//echo round($performance*100,2)."= ((($eindwaarde - $beginwaarde) - ". $AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde) <br>\n";

      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);

      

      if($totaal==true)
      {
        $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;
        $this->totalen[$datumEind]['eindwaarde']=$eindwaarde;
      }
//listarray($this->totalen);exit;
      //$weging=$gemiddelde/$totaalGemiddelde;//$this->totalen[$datumEind]['gemiddeldeWaarde'];
      $weging=$eindwaarde/$this->totalen[$datumEind]['eindwaarde'];
      $indexData=$this->indexPerformance($catnaam,$datumBegin,$datumEind,$weging);
   
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
  'procentZonderExtLiq'=>$performanceTotaalZonderExtLiq,
  'procentBruto'=>$performanceBruto,
  'resultaatBruto'=>$resultaaBruto,
  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal']+$RekeningDirecteKostenOpbrengsten['kostenTotaal'],
  'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal']+$RekeningDirecteKostenOpbrengsten['opbrengstTotaal'],
  'RENMETotaal'=>$FondsDirecteKostenOpbrengsten['RENMETotaal'],
  'kostenNietGekoppeld'=>$nietToegerekendeKosten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,
  'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
  'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
  'weging'=>$weging,
  'bijdrage'=>$bijdrage);
}



    if($stapeling=='maandenYTD')
    {
      $perfData['totaal']=array();//$waarden[$datumEind];
  
      $stapelItems=array('indexPerf','procent','procentBruto','bijdrage','indexBijdrage','overPerf','relContrib','procentZonderExtLiq');
      $lastCumu=array();
      $jaarCumu=array();
      foreach($stapelItems as $item)
      {
        $lastCumu[$item] = 0;
        $jaarCumu[$item] = 0;
      }
      
      foreach($waarden as $datum=>$cumuData)
      {
        foreach($stapelItems as $item)
        {
          $cumuData[$item]=(1+$cumuData[$item])*(1+$jaarCumu[$item])-1;
          $waarden[$datum][$item.'_c']=$cumuData[$item];
          $maandPerf = ((1 + $cumuData[$item] ) / (1 + $lastCumu[$item] ) - 1) * 1;
          $lastCumu[$item] = $cumuData[$item];
          $waarden[$datum][$item]=$maandPerf;
          
          
  
         //  if($item=='procentBruto')
         //    echo "$catnaam|$datumEind|$maandPerf <br>\n";//.$cumuData[$item]."=(1+".$cumuData[$item].")*(1+".$jaarCumu[$item].")-1 <br>\n";
          
        }
        if(substr($datum,5,5) =='12-31' || $datum==$datumEind)
        {
          foreach($stapelItems as $item)
          {
            $jaarCumu[$item] = $lastCumu[$item];
            $perfData['totaal'][$item]=$lastCumu[$item];
          }
         // listarray($jaarCumu);
        }
        
      }

     // listarray($waarden);
    }
    else
    {
      $stapelItems=array('indexPerf','procent','procentBruto','bijdrage','indexBijdrage','overPerf','relContrib','procentZonderExtLiq');
      $somItems=array('stort','stortEnOnttrekking','storting','onttrekking','kosten','kostenNietGekoppeld','resultaat','resultaatBruto','ongerealiseerd','gerealiseerd','opbrengst','RENMETotaal');
      foreach ($stapelItems as $item)
        $perfData['totaal'][$item]=1;
  
      foreach ($waarden as $datum => $waarde)
      {
        //$perfData['totaal']['resultaat'] +=$waarde['resultaat'];
        foreach ($stapelItems as $item)
        {
          $perfData['totaal'][$item] = ($perfData['totaal'][$item] * (1 + $waarde[$item]));
        }
        $waarden[$datum]['index'] = $perfData['totaal']['procent'] * 100;
      }
  
      $this->waarden[$catnaam] = $waarden;
      foreach ($stapelItems as $item)
      {
        $perfData['totaal'][$item] = ($perfData['totaal'][$item] - 1) * 100;
      }
    
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
    }
    
//listarray($FondsDirecteKostenOpbrengsten);
   
if($stapeling == true)
{ 
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
  
  
	function getMaanden($julBegin, $julEind,$ytd=false)
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
	      if($ytd==true)
          $datum[$i]['start'] = date('Y-m-d',$julBegin);
	      else
	        $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(substr($datum[$i]['start'],0,4)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
  }
  
  function getMaandenYtd($julBegin, $julEind,$ytd=true)
  {
    if($ytd==true)
      $jaren=$this->getJaren($julBegin, $julEind);
    else
      $jaren=array(array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind)));
    // echo "ytd= $ytd <br>\n";  listarray($jaren);
    $i = 0;
    $datum=array();
    foreach($jaren as $periode)
    {
      $julEind=db2jul($periode['stop']);
      $julBegin=db2jul($periode['start']);
      
      $eindjaar = date("Y", $julEind);
      $eindmaand = date("m", $julEind);
      $beginjaar = date("Y", $julBegin);
      $startjaar = date("Y", $julBegin);
      $beginmaand = date("m", $julBegin);
      
      //listarray($periode);
      $counterStart=0;
      $j=0;
      $stop = mktime(0, 0, 0, $eindmaand, 0, $eindjaar);
      while ($counterStart < $stop)
      {
        $counterStart = mktime(0, 0, 0, $beginmaand + $j, 0, $beginjaar);
        $counterEnd = mktime(0, 0, 0, $beginmaand + $j + 1, 0, $beginjaar);
        if ($counterEnd >= $julEind)
        {
          $counterEnd = $julEind;
        }
        
        if ($j == 0)
        {
          $datum[$i]['start'] = date('Y-m-d', $julBegin);
        }
        else
        {
          if ($ytd == true)
          {
            $datum[$i]['start'] = date('Y-m-d', $julBegin);
            if (substr($datum[$i]['start'], 5, 5) == '12-31')
            {
              $datum[$i]['start'] = (date('Y', $julBegin) + 1) . "-01-01";
            }
          }
          else
          {
            $datum[$i]['start'] = date('Y-m-d', $counterStart);
            if (substr($datum[$i]['start'], 5, 5) == '12-31')
            {
              $datum[$i]['start'] = (date('Y', $counterStart) + 1) . "-01-01";
            }
          }
          
        }
        
        $datum[$i]['stop'] = date('Y-m-d', $counterEnd);
        
        if ($datum[$i]['start'] == $datum[$i]['stop'])
        {
          unset($datum[$i]);
        }
        $i++;
        $j++;
      }
    }
    // listarray($datum);
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

	function indexPerformance($categorie,$van,$tot,$werkelijkAandeel)
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

     if(1 || !is_array($this->normData) || count($this->normData) < 1)
     {
       $extraFilter="AND ZorgplichtPerRisicoklasse.Risicoklasse= '".$this->rapport->pdf->portefeuilledata['Risicoklasse']."'";

       $q="SELECT
ZorgplichtPerBeleggingscategorie.Beleggingscategorie as Hoofdcategorie,
ZorgplichtPerRisicoklasse.norm,
ZorgplichtPerRisicoklasse.Zorgplicht,
CategorienPerHoofdcategorie.Beleggingscategorie
FROM
ZorgplichtPerRisicoklasse
INNER JOIN ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND
 ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
 INNER JOIN CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND 
 CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'

WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' $extraFilter
AND ZorgplichtPerBeleggingscategorie.Beleggingscategorie <> 'Liquiditeiten' AND ZorgplichtPerRisicoklasse.norm <> 0 ";
		   $DB->SQL($q);
		   $DB->Query(); 
       $this->normData['totaal']=0;
		   while($data=$DB->nextRecord())
       {
         if($this->indexNormaleWeging==true)
         {
           if($data['Beleggingscategorie']=='Liq-Extern' || $data['Beleggingscategorie']=='Liquiditeiten')
             $data['norm']=0;
         }
         $this->normData['totaal']+=$data['norm'];
		     $this->normData[$data['Beleggingscategorie']]=$data['norm'];
       }
       
       if($this->indexNormaleWeging==false)
       {

         $query="SELECT SUM(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE beleggingscategorie='Liquiditeiten' AND portefeuille='".$this->rapport->portefeuille."' AND rapportageDatum='$tot' ".$__appvar['TijdelijkeRapportageMaakUniek'];
         $DB->SQL($query);
         $DB->Query();
         $waarde=$DB->nextRecord();
         $liqAandeel=$waarde['waarde']/$this->totalen[$tot]['eindwaarde']*100;
         $this->normData['Liquiditeiten']=$liqAandeel;// echo "$tot Liquiditeiten ".$waarde['waarde']."/".$this->totalen[$tot]['eindwaarde']."= $liqAandeel <br>\n";ob_flush();
         $this->normData['OBL']=$this->normData['OBL']-$liqAandeel;
         
         $query="SELECT SUM(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE beleggingscategorie='Liq-Extern' AND portefeuille='".$this->rapport->portefeuille."' AND rapportageDatum='$tot' ".$__appvar['TijdelijkeRapportageMaakUniek'];
         $DB->SQL($query);
         $DB->Query();
         $waarde=$DB->nextRecord();
         $liqAandeel=$waarde['waarde']/$this->totalen[$tot]['eindwaarde']*100;
         $this->normData['Liq-Extern']=$liqAandeel; //echo "$tot Liq-Extern ".$waarde['waarde']."/".$this->totalen[$tot]['eindwaarde']."= $liqAandeel <br>\n";ob_flush();
         $this->normData['OBL']=$this->normData['OBL']-$liqAandeel;
       }
     }


	  $fonds=$this->indexLookup[$categorie];
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    
    if(count($verdeling)==0)
      $verdeling[$fonds]=100;
      
    if($categorie=='totaal')
    {  
      $verdeling=array();
      foreach($this->normData as $cat=>$percentage)
      {
        if($cat <> 'totaal')
          $verdeling[$this->indexLookup[$cat]]=$percentage;
      }
    }
    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      
      $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $totalPerf+=($perf*$percentage/100);
    }

    $perf= $totalPerf;
 
    $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);

    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$fondsData['Percentage'],
                'datum'=>$tot,
                'percentage'=>($this->normData[$categorie])/100,//$fondsData['Percentage']
                'categorie'=>$categorie,
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);

    return $tmp;
  }

function getKosten($portefeuille,$van,$tot)
{
  $db=new DB();
  $query="SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$van' AND
           Rekeningmutaties.Boekdatum <= '$tot'";
   $db->SQL($query);
   $kosten=$db->lookupRecord();
   return $kosten;
}

}

?>