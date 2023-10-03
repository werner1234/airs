<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/01/18 16:37:36 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIV_L75.php,v $
Revision 1.8  2020/01/18 16:37:36  rvv
*** empty log message ***

Revision 1.6  2019/10/26 16:07:18  rvv
*** empty log message ***

Revision 1.5  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.2  2018/06/10 11:45:56  rvv
*** empty log message ***

Revision 1.1  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.6  2018/05/26 17:23:51  rvv
*** empty log message ***

Revision 1.5  2018/05/19 16:24:53  rvv
*** empty log message ***

Revision 1.4  2018/04/30 05:37:37  rvv
*** empty log message ***

Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/18 16:17:44  rvv
*** empty log message ***

Revision 1.1  2018/04/15 12:34:29  rvv
*** empty log message ***

Revision 1.4  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.3  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.2  2018/03/18 10:55:47  rvv
*** empty log message ***

Revision 1.1  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.4  2018/03/11 10:53:28  rvv
*** empty log message ***

Revision 1.3  2018/03/10 18:24:22  rvv
*** empty log message ***

Revision 1.13  2017/12/09 17:54:25  rvv
*** empty log message ***

Revision 1.12  2017/10/01 14:29:55  rvv
*** empty log message ***

Revision 1.11  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.10  2016/10/23 11:32:33  rvv
*** empty log message ***

Revision 1.9  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.8  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.7  2016/09/07 15:42:21  rvv
*** empty log message ***

Revision 1.6  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.5  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L75.php");

class RapportOIV_L75
{
	function RapportOIV_L75($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Resultaten-analyse lopend jaar, gedetailleerd";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->filterCategorie='Liquide';
		$this->totaalWaarde=0;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function getCRMnaam($portefeuille)
	{
		$db = new DB();
		$query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
		$db->SQL($query);
		$crmData=$db->lookupRecord();
		$naamParts=explode('-',$crmData['naam'],2);
		$naam=trim($naamParts[1]);
		if($naam<>'')
			return $naam;
		else
			return $portefeuille;
	}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		if($_POST['debug']==1)
		{
			$this->pdf->line(0, 30, 297, 30);
			$this->pdf->line(0, 23, 297, 23);
			$this->pdf->line(297 / 2, 23, 297 / 2, 210);
			$this->pdf->line(0, (210 - 30) / 2 + 30, 297, (210 - 30) / 2 + 30);
			$this->pdf->line(0, (210 - 30) / 2 + 23, 297, (210 - 30) / 2 + 23);
		}

		$DB=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$this->rapportageDatum."' AND ".
			" portefeuille = '".$this->portefeuille."' ". $__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;

		if (!is_array($this->pdf->grafiekKleuren))
		{
			$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$this->pdf->grafiekKleuren = $kleuren;
		}


		$this->ATTblok_L75(25);


		$this->pdf->fillCell=array();
		$this->pdf->CellBorders = array();
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
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
    $db->SQL($query);
    $db->Query();
    $this->categorieVolgorde['LIQ']=0;
    while($data=$db->nextRecord())
      $this->categorieVolgorde[$data['Beleggingscategorie']]=0;
    
    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
      if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
        return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));
      
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
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
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
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
    $weging = $DB->NextRecord(); //listarray($query);
    
    if($totaalWaarde['begin']==0)
      $gemiddelde = $totaalWaarde['begin'] + $weging['totaal2'];
    else
      $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
    
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
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
  
  function kostenCorrectie($data)
  {
    
    //   listarray($data);
    $DB=new DB();
    $maanden=array();
    $huidigeCategorieBijdrageSom=array();
    foreach($data as $categorie=>$perfWaarden)
    {
      foreach ($perfWaarden['perfWaarden'] as $eindMaand => $perfData)
      {
        if($categorie<>'totaal')
        {
          $huidigeCategorieBijdrageSom[$eindMaand]['categorieen']+=$perfData['bijdrage'];
        }
        else
        {
          $huidigeCategorieBijdrageSom[$eindMaand]['totaal']+=$perfData['bijdrage'];
        }
      }
    }
    foreach($huidigeCategorieBijdrageSom as $eindMaand=>$bijdrageData)
    {
      //listarray($bijdrageData);
      $huidigeCategorieBijdrageSom[$eindMaand]['verschil']=round(($bijdrageData['totaal']-$bijdrageData['categorieen'])*100,8);
    }
  
    
    foreach($data['totaal']['perfWaarden'] as $eindMaand=>$perfWaarden)
    {
      
      $query = "SELECT
	SUM((Rekeningmutaties.Credit - Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) AS totaal
FROM
	Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening
WHERE
	Rekeningen.Portefeuille = '" . $this->portefeuille . "'
AND Rekeningmutaties.Verwerkt = '1'
AND Rekeningmutaties.Boekdatum > '" . $perfWaarden['begin'] . "'
AND Rekeningmutaties.Boekdatum <= '" . $perfWaarden['eind'] . "'
AND Grootboekrekeningen.Kosten=1";
      $DB->SQL($query);
      $DB->Query();
      $kosten = $DB->nextRecord();
      
      // listarray($perfWaarden);
      //    listarray($kosten);
      //$perf=performanceMeting($this->portefeuille , $perfWaarden['begin'] ,  $perfWaarden['eind'] , 1 );
      $perfDetail=$this->BerekenMutaties2($perfWaarden['begin'] ,  $perfWaarden['eind'],$this->portefeuille, $this->pdf->rapportageValuta);//
      //   listarray($perfDetail);
      $kostenPercentage=$kosten['totaal']/$perfDetail['gemiddelde'];
      $perfWaarden['procent']+=$kostenPercentage;
      $perf=$perfDetail['performance'];//listarray($perf);
      
      $maanden[$eindMaand]=array('kostenTotaal'=>$kosten['totaal'],'kostenPercentage'=>$kostenPercentage,'perfvoorKosten'=>$perfWaarden['procent']*100,'perfNaKosten'=>$perf,
                                 'kostenCorrectie'=>($perf-($perfWaarden['procent']*100)));
      
    }
//listarray($maanden);
    $aandeelTotaal=0;
   // $aandeelSom=array();
    $aandeelPerCategorie=array();
    
    foreach($data as $categorie=>$perfWaarden)
    {
      
      $bijdrageGestapeld=0;
      $procentGestapeld=0;
      
      $inBijdrage=$data[$categorie]['bijdrage'];
    
      foreach($perfWaarden['perfWaarden'] as $einddatum=>$perfDetails)
      {
        $aandeel=$perfDetails['bijdrage']/$perfDetails['procent']  ;
        //if($aandeel>1 || $aandeel<-1)
        //  $aandeel=0;
     //   echo "$categorie $einddatum $aandeel <br>\n";
        
        if(in_array($categorie,array('LIQ','Spaar','VAL-TERM','CALL-DEP')))
          $aandeel=0;
  
        
        if($categorie<>'totaal')
        {
          //echo $maanden[$einddatum]['kostenCorrectie']." ".$huidigeCategorieBijdrageSom[$einddatum]['verschil']."<br>\n";
           $extraKostenPercentage = $aandeel * ($maanden[$einddatum]['kostenCorrectie']+ $huidigeCategorieBijdrageSom[$einddatum]['verschil']);
           $aandeelPerCategorie[$categorie]+=$aandeel;
        }
        else
        {
        
        $extraKostenPercentage=$aandeel *  $maanden[$einddatum]['kostenCorrectie'] ;
        }
        
        $data[$categorie]['perfWaarden'][$einddatum]['bijdrage']+=$extraKostenPercentage/100;
        $bijdrageGestapeld=((1+$bijdrageGestapeld)*(1+$data[$categorie]['perfWaarden'][$einddatum]['bijdrage']))-1;
        
        //   $tmp=  $data[$categorie]['perfWaarden'][$einddatum]['bijdrage']/$aandeel;
        //   echo "$categorie ".$perfDetails['procent'] ." | $aandeel | $tmp <br>\n";
        
        $data[$categorie]['perfWaarden'][$einddatum]['procent']=  $data[$categorie]['perfWaarden'][$einddatum]['bijdrage']/$aandeel;
        $procentGestapeld=((1+$procentGestapeld)*(1+$data[$categorie]['perfWaarden'][$einddatum]['procent']))-1;
        
        //    echo "$categorie $bijdrageGestapeld=((1+$bijdrageGestapeld)*(1+".$data[$categorie]['perfWaarden'][$einddatum]['bijdrage']."))-1; <br>\n";
        
        $aandeelTotaal+=$aandeel;
        //    echo "$categorie $aandeel=".$perfDetails['bijdrage']." /".$perfDetails['procent']." |  $aandeelTotaal | $extraKostenPercentage |$bijdrageGestapeld<br>\n";
        
        
      }
     // listarray($aandeelSom);

      $data[$categorie]['bijdrage']=$bijdrageGestapeld*100;
     // $data[$categorie]['procent']=$procentGestapeld*100;
      
      
    }

    $meetpunten=array_sum($aandeelPerCategorie);
    $eindCorrectieVerdeling=array();
    $bijdrageSom=0;
    foreach($aandeelPerCategorie as $categorie=>$aandeel)
    {
      if($aandeel<>0)
      {
        $eindCorrectieVerdeling[$categorie] = $aandeel / $meetpunten;
        $bijdrageSom += $data[$categorie]['bijdrage'];
      }
    }
    $correctie=$data['totaal']['bijdrage']-$bijdrageSom;
    foreach($eindCorrectieVerdeling as $categorie=>$aandeel)
    {
      $data[$categorie]['bijdrage']+=($correctie*$aandeel);
    }
    
  //  echo $correctie;
   // listarray($eindCorrectieVerdeling);
    
   // echo "verschilsom: $verschilSom <br>\n";
    return $data;
    //exit;
    
  }

	function ATTblok_L75($ystart)
	{

 
		global $__appvar;
		$jaarTotalen=array();
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$portefeuilleStartJul=db2jul($portefeuilledata['startDatum']) ;
		if($portefeuilleStartJul < db2jul(date("Y-01-01",$this->pdf->rapport_datumvanaf)))
			$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datumvanaf);
		else
			$rapportageStartJaar=date('Y-m-d',($portefeuilleStartJul-0*86400));
		$this->tweedePerformanceStart=$rapportageStartJaar;

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersStart."  AS totaal ".
			"FROM TijdelijkeRapportage WHERE ".
			" rapportageDatum ='".$rapportageStartJaar."' AND ".
			" portefeuille = '".$this->portefeuille."' ". $__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaardeBegin = $totaalWaarde['totaal'];


		$query="SELECT SUM(actuelePortefeuilleWaardeEuro)  / ".$this->pdf->ValutaKoersEind."  AS totaal,
TijdelijkeRapportage.afmCategorie,
TijdelijkeRapportage.afmCategorieOmschrijving
FROM
TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='".$this->rapportageDatum."'".
			$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY afmCategorie";
		//echo $query."<br>\n"; echo $this->totaalWaarde;exit;
		$DB->SQL($query);
		$DB->Query();
		$categorieVerdeling=array();

		while($cat = $DB->nextRecord())
		{
			$categorieVerdeling[$cat['afmCategorie']] = $cat['totaal']/$this->totaalWaarde*100;
		}


		$att=new ATTberekening_L75($this);
		$att->specifiekeIndex=$this->pdf->portefeuilledata['SpecifiekeIndex'];
		$att->indexPerformance=true;
		//echo $this->laatsteMaandBegin." ".$this->tweedePerformanceStart."<br>\n";exit;
		//$this->waarden['Periode']=$att->bereken($this->laatsteMaandBegin,  $this->rapportageDatum,$this->pdf->rapportageValuta);//$this->rapportageDatumVanaf
		$this->waarden['Jaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum,'afm');
 $this->waarden['Jaar']=$this->kostenCorrectie($this->waarden['Jaar']);
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->tweedePerformanceStart, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
		$this->ytdRendement=$rendementProcent;

		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$waardeMutatie=$this->totaalWaarde-$totaalWaardeBegin;
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;


		$this->pdf->setY($ystart);
		$witruimte=4;
		$this->pdf->SetWidths(array(75,17,25,17,17));
		$this->pdf->SetWidths(array(85,25,32,20,25));
		$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
		unset($this->pdf->CellBorders);
		$this->pdf->row(array(vertaalTekst("Resultaten analyse",$this->pdf->rapport_taal),vertaalTekst("Allocatie in %",$this->pdf->rapport_taal),
											vertaalTekst("Resultaat in euro",$this->pdf->rapport_taal),vertaalTekst("YTD in %",$this->pdf->rapport_taal),
											vertaalTekst("Bijdrage in %",$this->pdf->rapport_taal)));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$bovencat=$att->categorien;
		$max=1000;
		$barGraph=false;
		$this->pdf->ln($witruimte);

		unset($bovencat['totaal']);
		foreach ($bovencat as $categorie=>$categorieOmschrijving)
		{
			if($this->waarden['Jaar'][$categorie]['weging'] <> 0)
			{
				$this->pdf->row(array(vertaalTekst($categorieOmschrijving,$this->pdf->rapport_taal),
													$this->formatGetal($categorieVerdeling[$categorie], 2,false,$max),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['resultaat'], 2,false,$max),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['procent'], 2,false,$max),
													$this->formatGetal($this->waarden['Jaar'][$categorie]['bijdrage'], 2,false,$max)));
				$this->pdf->ln($witruimte);
				if($this->waarden['Jaar'][$categorie]['bijdrage']<0)
					$barGraph=true;
				$jaarTotalen['resultaat']+=$this->waarden['Jaar'][$categorie]['resultaat'];
				$jaarTotalen['bijdrage']+=$this->waarden['Jaar'][$categorie]['bijdrage'];

				$jaarTotalen['weging']+=$categorieVerdeling[$categorie];

				$categorieVerdeling['percentage'][$categorieOmschrijving]=$this->waarden['Jaar'][$categorie]['bijdrage'];
				$categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['AFM'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['AFM'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['AFM'][$categorie]['B']['value']);
				$categorieVerdeling['kleurBar'][$categorieOmschrijving]=array($this->pdf->grafiekKleuren['AFM'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['AFM'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['AFM'][$categorie]['B']['value']);

			}
		}



		$this->pdf->CellBorders=array('','TS','TS','','TS');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),
											$this->formatGetal($jaarTotalen['weging'],2,false,$max),
											$this->formatGetal($jaarTotalen['resultaat'],2,false,$max),
											'',
											$this->formatGetal($this->waarden['Jaar']['totaal']['bijdrage'],2,false,$max)));//$jaarTotalen['bijdrage']
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		unset($this->pdf->CellBorders);
		$this->pdf->ln($witruimte);
		$this->pdf->row(array(vertaalTekst("Kosten",$this->pdf->rapport_taal),'',$this->formatGetal($resultaatVerslagperiode-$jaarTotalen['resultaat'],2,false,$max),'',
											$this->formatGetal($rendementProcent-$this->waarden['Jaar']['totaal']['bijdrage'],2,false,$max)));//$this->jaarTotalen['portBijdrage'] //$jaarTotalen['bijdrage']

		$this->pdf->ln($witruimte);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders=array('','TS','TS','','TS');
		$this->pdf->row(array(vertaalTekst("Totaal na kosten",$this->pdf->rapport_taal),$this->formatGetal($jaarTotalen['weging'],2,false,$max),$this->formatGetal($resultaatVerslagperiode,2,false,$max),'',
											$this->formatGetal($rendementProcent,2,false,$max)));
		unset($this->pdf->CellBorders);

		$xOffset=150;
		$grafiekY=27;
			$this->pdf->setXY(20+$xOffset,$grafiekY-5);
			$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
			$this->pdf->Multicell(297/2,4,vertaalTekst('Bijdrage in het resultaat',$this->pdf->rapport_taal),'','C');
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

			$this->pdf->setXY(50+$xOffset,$grafiekY);
			$this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2) //%l (%p)
		$this->pdf->SetDrawColor(0,0,0);
	}





	function SetLegends2($data, $format)
	{
		$this->pdf->legends=array();
		$this->pdf->wLegend=0;

		$this->pdf->sum=array_sum($data);

		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			$p=sprintf('%.1f',$val).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}

	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{
		$pdfObject = &$object;
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->SetLegends2($data,$format);


		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$nbDiv=5;
		$legendWidth=10;
		$YDiag = $YPage;
		$hDiag = floor($h);
		$XDiag = $XPage +  $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if($color == null)
			$color=array(155,155,155);
		$maxVal=0;
		$minVal=0;
		if ($maxVal == 0) {
			$maxVal = max($data)*1.1;
		}
		if ($minVal == 0) {
			$minVal = min($data)*1.1;
		}
		if($minVal > 0)
			$minVal=0;
		
		$minVal=floor($minVal*10)/10;
		$maxVal=ceil($maxVal*10)/10;

		$offset=$minVal;
		$maxMin=ceil(($maxVal-$minVal)*10)/10;
		//$maxMin=$maxMin*1.3;
	  $valIndRepere = round($maxMin / $nbDiv*10,1)/10;



		//echo "$minVal $maxVal  $maxMin <br>\n";exit;
		//echo ($maxMin/$nbDiv)." $valIndRepere <br>\n";
		if(abs($maxMin/$nbDiv)>abs($minVal))
		{
			$minVal = floor(abs($maxMin / $nbDiv) * -1*10)/10;
			$maxMin=ceil(($maxVal-$minVal)*10)/10;
			$valIndRepere = round($maxMin / $nbDiv*10,0)/10;
			$offset=$minVal;
		}
		//echo "$minVal $maxVal  $maxMin <br>\n";
		//echo ($maxMin/$nbDiv)." $valIndRepere<br>\n";
		//exit;

		$bandBreedte = $valIndRepere * $nbDiv;
		//echo $bandBreedte;exit;
		$lRepere = floor($lDiag / $nbDiv);
		$unit = $lDiag / $bandBreedte;
		$hBar = 8;//floor($hDiag / ($this->pdf->NbVal + 1));
		$hDiag = $hBar * ($this->pdf->NbVal + 1);

		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);


		$legendaStep=$unit/$nbDiv*$bandBreedte;
		//echo "	$legendaStep=$unit/$nbDiv*$bandBreedte;";exit;

		//$valIndRepere=round($valIndRepere/$unit/5)*5;


		$this->pdf->SetLineWidth(0.2);
		$this->pdf->SetDrawColor(0,0,0);
		$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);

		$nullijn=$XDiag - ($offset * $unit);

		$this->pdf->Line($nullijn, $YDiag, $nullijn, $YDiag + $hDiag,array('dash' => 0));
		$this->pdf->setXY($nullijn,$YDiag + $hDiag);
		$this->pdf->Cell(0.1, 5,"0",0,0,'C');

		$i=0;
		$nbDiv=10;

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

		if(round($legendaStep,5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for($x=$nullijn;$x>=$XDiag; $x=$x-$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag,array('dash' => 1));
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
				$i++;
				if($i>100)
					break;
			}

			$i=0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag,array('dash' => 1));
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');

				$i++;
				if($i>100)
					break;
			}
		}

		$i=0;

		$this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
		$this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
//listarray($colorArray);listarray($data);
		$this->pdf->setDash();
		foreach($data as $key=>$val)
		{
			$this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;

			//$this->pdf->Line($XDiag, $yval+$eBaton/2, $XPage+$w, $yval+$eBaton/2,array('dash' => 3));


			$hval = $eBaton;
			$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
			$this->pdf->SetXY($XPage, $yval);
			$this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
			$i++;
		}

		//Scales
		$minPos=($minVal * $unit);
		$maxPos=($maxVal * $unit);

		$unit=($maxPos-$minPos)/$nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


	}


}
?>