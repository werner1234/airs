<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKMA.php");

class RapportVKMA_L54
{

	function RapportVKMA_L54($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->vkma = new RapportVKMA($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    //$pdf->lastPOST['vkma_clientselectie']=1;
    if($pdf->lastPOST['vkma_clientselectie']!=1)
    {
      $this->pdf = &$pdf;
      $this->pdf->rapport_type = "VKMA1";
      $this->pdf->rapport_naam1=$this->pdf->lastPOST['vkma_naam'];
      $this->pdf->rapport_datum = db2jul($rapportageDatum);
      $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
      $this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->rapport_titel = vertaalTekst("vergelijkende kostenmaatstaf ex-ante",$this->pdf->rapport_taal);
      $this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
      if(!isset($this->pdf->PortefeuilleStartdatum))
      {
        $db=new DB();
        $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder, Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
        $db->SQL($query);
        $pdata = $db->lookupRecord();
        $this->pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
      }
  
      $this->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
      $this->rapport_datum = db2jul($rapportageDatum);
      $this->rapport_jaar = date('Y', $this->rapport_datum);
      $this->ValutaKoersEind=1;
      $this->vanafDatum=($this->rapport_jaar-1).date('-m-d',$this->rapport_datum);
      $this->vanafJul=db2jul($this->vanafDatum);
      $this->pdf->rapport_datumvanaf=$this->vanafJul;
      $portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
      $this->melding="";
      $this->perioden=array();
      $this->queryVanaf=$this->vanafDatum;
      if($portefeuilleStartJul>$this->vanafJul)
      {
        $oldstart=$this->vanafDatum;
        $this->queryVanaf=date('Y-m-d',$portefeuilleStartJul);
        $this->pdf->rapport_datumvanaf =$portefeuilleStartJul;//+86400
        $this->vanafDatum=date('Y-m-d',$portefeuilleStartJul);//+86400
        $dagen=($this->pdf->rapport_datum-$portefeuilleStartJul)/86400;//+86400
        $this->vanafJul=$portefeuilleStartJul;//+86400;
    
        $this->melding= vertaalTekst("Door onvoldoende historie bedraagt de rapportage periode",$this->pdf->rapport_taal)." ".round($dagen)." ".vertaalTekst("dagen",$this->pdf->rapport_taal).".";
      }
      $this->portefeuille = $portefeuille;
      $this->rapportageDatumVanaf = $this->vanafDatum;
      $this->rapportageDatum = $rapportageDatum;
      $this->pdf->excelData[]=array('Categorie','Fonds',date('d-m-Y',$this->pdf->rapport_datumvanaf),
        date('d-m-Y',$this->pdf->rapport_datum),'Mutaties','Resultaat','Gemiddeld vermogen',
        'transactie kosten','dl kosten %','dl kosten absoluut','Weging','VKM bijdrage');
      $this->verdelingTotaal=array();
      $this->verdelingFondsen=array();
      $this->skipSummary=false;
      $this->skipDetail=true;
      $this->skipLangeTermijn=false;
      $this->pdfVullen=true;
    }
	}
  
  function kostenKader($totaalDoorlopendekosten,$perfTotaal,$totaalDoorlopendekostenGesplitst)
  {
    
    if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    $DB=new DB();
    
    $query="SELECT BeheerfeeBTW FROM Portefeuilles where Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $btw=$DB->nextRecord();

    if ( ! empty ($this->pdf->lastPOST['vkma_btw_beheer']) ) {
      $btw['BeheerfeeBTW'] = $this->pdf->lastPOST['vkma_btw_beheer'];
    }
    
    $query="SELECT SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)) $koersQuery AS totaal
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
AND Rekeningen.Memoriaal = 0 AND Rekeningmutaties.Grootboekrekening='FONDS'  AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S')
GROUP BY Rekeningmutaties.Grootboekrekening";
    
    $DB->SQL($query);
    $DB->Query();
    $spreadKosten=$DB->nextRecord();
    $spreadKostenEUR=($this->spreadKostenPunten / 10000 * $spreadKosten['totaal']);
    
    
    $grootboekKostenData=array();
    $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) $koersQuery )*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening AND Grootboekrekeningen.Kosten=1
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $grootboekKostenData[$data['Grootboekrekening']]=$data;
    }
    
    $gemiddelde=$this->vkma->verdelingTotaal['totaal']['gemiddelde'];
    $eindWaarde=$this->vkma->verdelingTotaal['perioden'][$this->rapportageDatum];
    $doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['eindwaarde'];
    // echo $this->verdelingTotaal['totaal']['gemiddelde'];exit;
//listarray($grootboekKostenData);exit;




//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['eindwaarde']."<br>\n";exit;
    
    $handmatigeKosten=array('BEH'=>'vkma_kosten_beheer','BEW'=>'vkma_kosten_service','KOST'=>'vkma_kosten_transactie','KNBA'=>'vkma_kosten_bank');
    $handmatigeOmschrijving=array('BEH'=>'Beheerkosten','BEW'=>'Servicekosten bank','KOST'=>'Transactiekosten ','KNBA'=>'Overige bankkosten');
    $grootboekKostenDataNew=array();//$grootboekKostenData;
    $gebruikHandmatigeGegevens=false;
    foreach($handmatigeKosten as $grootboek=>$kosten)
    {
      if(isset($this->pdf->lastPOST[$kosten]) && $this->pdf->lastPOST[$kosten] <> '')
        $gebruikHandmatigeGegevens=true;
      $grootboekKostenDataNew[$grootboek]['totaal']=floatval($this->pdf->lastPOST[$kosten]/100*$eindWaarde);
      $grootboekKostenDataNew[$grootboek]['Grootboekrekening']=$grootboek;
      $grootboekKostenDataNew[$grootboek]['Omschrijving']=$handmatigeOmschrijving[$grootboek];
    }
    if($gebruikHandmatigeGegevens==true)
      $grootboekKostenData=$grootboekKostenDataNew;
    
    if($this->pdfVullen==true)
    {
      
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      $this->pdf->setAligns(array('L', 'R', 'R'));
      $this->pdf->setWidths(array(108, 30, 30));
      $startY=$this->pdf->getY();
    }
    
		$percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($eindWaarde+$totaalDoorlopendekosten);
    $herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
		$aandeelIndirect=$perfTotaal['eindwaarde']/$eindWaarde;
		//echo "	$aandeelIndirect=".$perfTotaal['eindwaarde']."/$eindWaarde; <br>\n";exit;
    
    $vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
    if($this->pdfVullen==true)
    {

      $this->pdf->row(array('Vermogen',$this->vkma->formatGetal($eindWaarde*$this->scaleFactor, 0) . ' '.$this->pdf->rapportageValuta ));
      $this->pdf->excelData[]=array('','Vermogen','','',round($eindWaarde*$this->scaleFactor,0),$this->pdf->rapportageValuta );
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('Doorlopende kosten factor van de portefeuille',$this->vkma->formatGetal($vkmPercentagePortefeuille, 2) . ' %'));
      $barData=array();
      $barData['Indirecte (fonds)kosten']=$vkmPercentagePortefeuille;
      $this->pdf->excelData[]=array('','Doorlopende kosten factor van de portefeuille','','',round($vkmPercentagePortefeuille, 2),'%');
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
      //$this->pdf->setWidths(array(60, 20, 20, 40));
      $this->pdf->setWidths(array(28+50+20,20,20,20,20,20,20,20,20,20));
      $this->pdf->row(array('Directe kosten vanaf ' . date('d-m-Y', db2jul($this->vanafDatum)), $this->pdf->rapportageValuta , 'Percentage'));
      $this->pdf->excelData[]=array('','Directe kosten vanaf ' . date('d-m-Y', db2jul($this->vanafDatum)),'', $this->pdf->rapportageValuta , 'Percentage');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    }
    $totaal=0;
    $grootBoekKostenTotaal=0;
    $grootboekKosten=array();
    
    foreach($grootboekKostenData as $data)
    {
      
      if($data['Grootboekrekening']=='BEH')
      {
        $data['Omschrijving']='Beheervergoeding inclusief '.$this->vkma->formatGetal($btw['BeheerfeeBTW'],0).'% BTW';
        $this->grootboekKleuren[$data['Omschrijving']]=$this->grootboekKleuren['Beheervergoeding'];
      }
      $kostenProcent=$data['totaal']/$eindWaarde*100;
      $kostenHerrekend=$eindWaarde*$kostenProcent/100;
      if($this->pdfVullen==true)
      {
        $this->pdf->row(array($data['Omschrijving'],$this->vkma->formatGetal($kostenHerrekend*$this->scaleFactor,0),$this->vkma->formatGetal($kostenProcent,2). ' %' ));
        $this->pdf->excelData[]=array('',$data['Omschrijving'],'',round($kostenHerrekend*$this->scaleFactor,0),round($kostenProcent,2) );
        $barData[$data['Omschrijving']]=$kostenProcent;
      }
      $grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
      $totaal+=$data['totaal'];
    }
    $grootBoekKostenTotaal=$totaal;
    
    if($spreadKostenEUR <> 0)
    {
			$kostenProcent = $spreadKostenEUR / $eindWaarde * 100;
      $spreadKostenEUR = $eindWaarde*$kostenProcent/100;
      if($this->pdfVullen==true)
      {
        $this->pdf->row(array(vertaalTekst('Spread-kosten',$this->pdf->rapport_taal),$this->vkma->formatGetal($spreadKostenEUR*$this->scaleFactor, 0),$this->vkma->formatGetal($kostenProcent, 2) . ' %'));
        $this->pdf->excelData[]=array('','Spread-kosten','',round($spreadKostenEUR*$this->scaleFactor,0),round($kostenProcent,2) );
      }
      $totaal += $spreadKostenEUR;
      $barData['Spread-kosten'] = $kostenProcent;
    }
    
    
		$kostenPercentage=$totaal/$eindWaarde*100;
    $vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
    if($this->pdfVullen==true)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $kostenHerrekend=$eindWaarde*$kostenPercentage/100;
      $this->pdf->row(array('Totaal directe kosten',$this->vkma->formatGetal($kostenHerrekend*$this->scaleFactor, 0),$this->vkma->formatGetal($kostenPercentage, 2). ' %'));
      $this->pdf->excelData[]=array('','Totaal directe kosten','', round($kostenHerrekend/$this->pdf->ValutaKoersEind, 0), round($kostenPercentage, 2));
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
      //$this->pdf->setWidths(array(40 + 20, 20));
      //echo "$vkmPercentagePortefeuille*1+$kostenPercentage <br>\n";exit;
      $this->pdf->row(array('Vergelijkende kostenmaatstaf','',$this->vkma->formatGetal($vkmWaarde, 2). ' %'));
      $this->pdf->excelData[]=array('','Vergelijkende kostenmaatstaf','', '',round($vkmWaarde, 2));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $startYGrafiek=$this->pdf->getY();
      $this->pdf->setXY(30,165);
      arsort($barData);
      //echo $startYGrafiek-$startY-10;exit;
      $huidigeY=$this->pdf->getY();
      $this->vkma->grootboekKleuren=$this->grootboekKleuren;
      $this->vkma->VBarVerdeling(50,70,$barData);
      $this->pdf->setY($huidigeY);
    }
    $this->vkmWaarde=array('vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde,'grootboekKosten'=>$grootboekKosten,
      'gemiddeldeWaarde'=>$eindWaarde,'grootBoekKostenTotaal'=>$grootBoekKostenTotaal,'totaalDoorlopendekosten'=>$totaalDoorlopendekosten,'totaalDirecteKosten'=>$totaal,
                           'totaalDoorlopendekostenGesplitst'=>$totaalDoorlopendekostenGesplitst);
    
  }

	function writeRapport()
	{
    if($this->vkma->pdf->lastPOST['vkma_clientselectie']==1)
    {
      $this->vkma->writeRapport();
    }
    else
    {
      global $__appvar,$USR;
  
      $this->vkma->vulVorigJaar();
  
      $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,Portefeuilles.spreadKosten, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
      $DB = new DB();
      $DB->SQL($query);
      $DB->Query();
      $this->portefeuilledata = $DB->nextRecord();
  
  
      $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
      $q = "SELECT grafiek_kleur ,grafiek_sortering,spreadKosten FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
      $DB = new DB();
      $DB->SQL($q);
      $DB->Query();
      $kleuren = $DB->LookupRecord();
      $this->spreadKostenPunten=$kleuren['spreadKosten'];
      if($this->portefeuilledata['spreadKosten'] <> 0)
        $this->spreadKostenPunten=$this->portefeuilledata['spreadKosten'];
  
      $allekleuren = unserialize($kleuren['grafiek_kleur']);
      $gewensteKleuren = $allekleuren['Grootboek'];
      $mogelijkeKleuren=array();
  
      $kleurGebruikt=array();
      foreach($allekleuren as $type=>$typeKleuren)
      {
        foreach ($typeKleuren as $kleurcategorie => $kleurdata)
        {
          $kleur = array($kleurdata['R']['value'], $kleurdata['G']['value'], $kleurdata['B']['value']);
      
          if ($kleur[0] <> 0 || $kleur[1] <> 0 || $kleur[2] <> 0)
          {
            $kleurString = $kleur[0] . $kleur[1] . $kleur[2];
            if (!in_array($kleurString, $kleurGebruikt))
            {
              $kleurGebruikt[] = $kleurString;
              $mogelijkeKleuren[] = $kleur;
            }
          }
        }
      }
  
      if($this->skipDetail==true)
        $this->pdfVullen=false;
  
      if($this->pdfVullen==true)
      {
        $dataWidth=array(28,50,28,28,28,28,28,28,28);
        $this->pdf->SetWidths($dataWidth);
    
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'], $this->pdf->rapport_default_fontcolor['g'], $this->pdf->rapport_default_fontcolor['b']);
        $naamBackup=$this->pdf->rapport_naam1;
        $this->pdf->rapport_naam1=$this->pdf->lastPOST['vkma_naam'];
        $this->pdf->AddPage("P");
        $this->pdf->templateVars['VKMAPaginas'] = $this->pdf->page;
        $this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'], $this->pdf->rapport_lijn_rood['g'], $this->pdf->rapport_lijn_rood['b']);
        $this->pdf->SetLineWidth(0.1);
      }
  
      $indexberekening = new indexHerberekening();
      $julvanaf = db2jul($this->rapportageDatumVanaf);
      $jultot = db2jul($this->rapportageDatum);
      $dagenTotaal = round(($jultot - $julvanaf) / 86400);
      $this->perioden = $indexberekening->getMaanden($julvanaf, $jultot);
  
      foreach ($this->perioden as $periode)
      {
        $portefeuileWaarde = array();
        $dagenPeriode = round((db2jul($periode['stop']) - db2jul($periode['start'])) / 86400);
    
        if (substr($this->vanafDatum, 5, 5) == '01-01')
        {
          $startjaar = true;
        }
        else
        {
          $startjaar = false;
        }
        $fondswaardenStart = berekenPortefeuilleWaarde($this->portefeuille, $periode['start'], $startjaar, $this->pdf->rapportageValuta, $periode['start']);
        $storingen = $this->vkma->getGewogenStortingenOnttrekkingen($periode['start'], $periode['stop']);
        foreach ($fondswaardenStart as $waarden)
        {
      
          $portefeuileWaarde['start'] += $waarden['actuelePortefeuilleWaardeEuro'];
          $this->vkma->verdelingFondsen[$periode['start']][$waarden['fonds']]['start'] += $waarden['actuelePortefeuilleWaardeEuro'];
        }
        $portefeuileWaarde['gemiddelde'] = $portefeuileWaarde['start'] + $storingen['gewogen'];
        $portefeuileWaarde['aandeel'] = $dagenPeriode / $dagenTotaal;
        $this->vkma->verdelingTotaal['perioden'][$periode['stop']] = $portefeuileWaarde;
        $this->vkma->verdelingTotaal['totaal']['gemiddelde'] += $portefeuileWaarde['aandeel'] * $portefeuileWaarde['gemiddelde'];
      }
      $perHoofdcategorie=array();
      $perCategorie=array();
  
      $query="SELECT sum(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille='".$this->portefeuille."'  AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $portefeuileWaarde=$DB->lookupRecord();
      $this->scaleFactor=1;
      if($this->pdf->lastPOST['vkma_clientselectie']<>1 && $this->pdf->lastPOST['vkma_bedrag']<>0)
      {
        $this->scaleFactor = $this->pdf->lastPOST['vkma_bedrag'] / $portefeuileWaarde['actuelePortefeuilleWaardeEuro'];
      }
      $this->vkma->scaleFactor=$this->scaleFactor;
      //		listarray($this->pdf->lastPOST);
  
      $this->vkma->verdelingTotaal['perioden'][$this->rapportageDatum]=$portefeuileWaarde['actuelePortefeuilleWaardeEuro'];
      //	$this->vkma->verdelingTotaal['totaal']['gemiddelde']=$this->vkma->verdelingTotaal['perioden'][$this->rapportageDatum] ; Vermogen op einddatum vervangen voor gemiddelde vermogen zoals in de VKM
  
      $query="SELECT
TijdelijkeRapportage.Portefeuille,
TijdelijkeRapportage.Fonds,
if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,TijdelijkeRapportage.Fonds) as fondsVolgorde,
Fondsen.OptieBovenliggendFonds,
TijdelijkeRapportage.Regio,
TijdelijkeRapportage.Beleggingscategorie,
TijdelijkeRapportage.BeleggingscategorieOmschrijving AS categorieOmschrijving,
TijdelijkeRapportage.BeleggingscategorieVolgorde,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.HoofdcategorieOmschrijving as hoofdCategorieOmschrijving,
TijdelijkeRapportage.FondsOmschrijving as FondsOmschrijving,
TijdelijkeRapportage.Valuta,
Fondsen.VKM
FROM
TijdelijkeRapportage
Inner Join Fondsen ON TijdelijkeRapportage.Fonds = Fondsen.Fonds
WHERE
TijdelijkeRapportage.Portefeuille='".$this->portefeuille."'  AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
AND TijdelijkeRapportage.Fonds <> '' AND Fondsen.VKM=1 ".$__appvar['TijdelijkeRapportageMaakUniek']."
GROUP BY TijdelijkeRapportage.Fonds
ORDER BY HoofdcategorieVolgorde, BeleggingscategorieVolgorde,fondsVolgorde,OptieBovenliggendFonds,FondsOmschrijving ";
  
  
      $heeftOptie=array();
      $DB->SQL($query);
      $DB->Query();
      while($data = $DB->NextRecord())
      {
        $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
        $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];//[$data['Regio']]
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];//[$data['Regio']]
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];//[$data['Regio']]
        $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];//[$data['Regio']]
        $alleData['fondsen'][]=$data['Fonds'];
        $fondsGegevens[$data['Fonds']]=$data;
    
        if($data['OptieBovenliggendFonds'] <> '' && !in_array($data['OptieBovenliggendFonds'],$heeftOptie))
          $heeftOptie[]=$data['OptieBovenliggendFonds'];
      }
  
      $this->vkma->totalen['gemiddeldeWaarde']=0;
      $totaalBijdrageVKM=0;
      $totaalDoorlopendekosten=0;
      $totaalDoorlopendekostenGesplitst=array();
      $perfTotaal=$this->vkma->fondsPerformance($alleData,true);
  
      $this->vkma->totalen['gemiddeldeWaarde']=$perfTotaal['eindwaarde'];
      $totaalSom=array();
      $sub=array();
      $kostenPercentage=array();
      $laatsteFonds='';
      $totaalKosten=0;
      $totaaldlKosten=0;

      foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
        $perHoofdcategorie[$hoofdCategorie]['perf'] = $this->vkma->fondsPerformance($hoofdcategorieData);

      foreach ($perCategorie as $hoofdCategorie=>$regioData)
        foreach ($regioData as $categorie=>$categorieData)
          $perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->vkma->fondsPerformance($categorieData); //[$regio]
  
      if($this->pdfVullen==true)
      {
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        $oldWidths = $this->pdf->widths;
        $this->pdf->widths[0] += 35;
        $this->pdf->widths[1] -= 35;
      }
      foreach ($perHoofdcategorie as $hoofdcategorie=>$hoofdcategorieData)
      {
        $data=$hoofdcategorieData['perf'];
        if($this->pdfVullen==true)
        {
          if ($data['bijdrage'] < 0)
          {
            $this->pdf->CellFontColor = array('', '', '', '', '', '', '', '', '', '', '', '', $this->pdf->rapport_font_rood);
          }
          else
          {
            $this->pdf->CellFontColor = array('', '', '', '', '', '', '', '', '', '', '', '', $this->pdf->rapport_font_groen);
          }
        }
        $totaalSom['beginwaarde'] += $data['beginwaarde'];
        $totaalSom['eindwaarde'] += $data['eindwaarde'];
        $totaalSom['stort'] += $data['stort'];
        $totaalSom['gerealiseerd'] += $data['gerealiseerd'];
        $totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
        $totaalSom['kosten'] += $data['kosten'];
        $totaalSom['resultaat'] += $data['resultaat'];
        $totaalSom['weging'] += $data['weging'];
        $totaalSom['bijdrage'] += $data['bijdrage'];
      }
      $perfTotaal = $totaalSom;
      $percentageIndirectVermogenMetKostenfactor=0;
      if($this->pdfVullen==true)
      {
        $this->pdf->widths = $oldWidths;
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array('T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T');
        unset($this->pdf->CellBorders);
      }
      $lastCategorie='';
      $lastRegio='';
      $laatste='';
      foreach ($perCategorie as $hoofdcategorie=>$categorieData)
      {
        if($this->pdfVullen==true)
          $this->vkma->printKop($perHoofdcategorie[$hoofdcategorie]['omschrijving'],'BI',true);
    
        foreach ($categorieData as $categorie=>$fondsData)
        {
      
          if($this->pdfVullen==true)
          {
            if ($categorie != $lastCategorie)
            {
              $this->vkma->printKop($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], '');
            }
            $lastCategorie = $categorie;
        
            $widthsBackup = $this->pdf->widths;
            $alignsBackup = $this->pdf->aligns;
            $newIndex = 0;
            $newWidths = array();
        
            foreach ($this->pdf->widths as $index => $waarde)
            {
              if ($index < 2)
              {
                $newIndex += $waarde;
              }
              else
              {
                $newIndex = $waarde;
              }
              if ($index == 0)
              {
                $newWidths[] = 0;
              }
              else
              {
                $newWidths[] = $newIndex;
              }
            }
        
            $this->pdf->widths = $newWidths;
            $this->pdf->widthsBackup = $newWidths;
          }
          $somVelden=array('beginwaarde','eindwaarde','stort','resultaat','gemWaarde','weging','bijdrage','kosten');
          foreach ($fondsData['fondsen'] as $id=>$fonds)
          {
        
            $lastLn=false;
            $tmp=array();
            $tmp['fondsen']=array($fonds);
            $tmp['categorie']=$categorie;
            $data=$this->vkma->fondsPerformance($tmp);
        
            if($fondsGegevens[$fonds]['Fonds']!=$fondsGegevens[$fonds]['fondsVolgorde'] && $fondsGegevens[$fonds]['OptieBovenliggendFonds']==$laatste)
            {
              foreach($somVelden as $veld)
                $sub[$veld]+=$data[$veld];
              $sub['aantal']++;
            }
        
            if($fondsGegevens[$fonds]['OptieBovenliggendFonds'] == '')
              $laatste=$fonds;
        
            if($fondsGegevens[$fonds]['Fonds']==$fondsGegevens[$fonds]['fondsVolgorde'] || (isset($lastfondsVolgorde) && $fondsGegevens[$fonds]['fondsVolgorde'] <> $lastfondsVolgorde))
            { //echo " $laatsteFonds ".$sub['aantal']."<br>\n";ob_flush();
              if($sub['aantal']>1 )
              {
                $bijdrageVKM=$sub['weging']*100*$kostenPercentage['percentage'];
                $perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
                $perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
                if($this->pdfVullen==true)
                {
                  $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS');
                  $this->pdf->row(array('', '        subtotaal ' . $laatsteFonds,
                                    $this->vkma->formatGetal($sub['eindwaarde']*$this->scaleFactor, 0),
                                    $this->vkma->formatGetal($kostenPercentage['percentage'], 2),
                                    $this->vkma->formatGetal($sub['eindwaarde'] * $kostenPercentage['percentage'] / 100, 0),
                                    $this->vkma->formatGetal($sub['weging'] * 100, 2, true),
                                    $this->vkma->formatGetal($bijdrageVKM, 2, true)));
              
              
                  unset($this->pdf->CellBorders);
                  $this->pdf->Ln();
                  $lastLn = true;
                }
              }
              $sub=array('aantal'=>1);
              foreach($somVelden as $veld)
                $sub[$veld]+=$data[$veld];
          
              $laatsteFonds=substr($fondsData['fondsOmschrijving'][$id],0,30);
          
            }
            $lastfondsVolgorde=$fondsGegevens[$fonds]['fondsVolgorde'];
        
        
            if($data['beginwaarde'] < 0 || $data['eindwaarde'] < 0)
              $spiegeling=-1;
            else
              $spiegeling=1;
            $this->pdf->widths=$newWidths;
            $this->pdf->aligns=$alignsBackup;
            if(in_array($fonds,$heeftOptie) && $lastLn==false)
              $this->pdf->Ln();
        
            $query="SELECT fondskosten.percentage as TotCostFund, fondskosten.transCostFund as FundTransCost, fondskosten.perfFeeFund as FundPerfFee FROM fondskosten
                       JOIN Fondsen ON fondskosten.fonds=Fondsen.Fonds
                       WHERE fondskosten.fonds='$fonds' AND Fondsen.VKM=1 AND datum <= '".$this->rapportageDatum."'
                       ORDER BY datum desc";
            $DB->SQL($query);
            $DB->Query();
            $kostenPercentage = $DB->NextRecord();
            $totaalKostenPercentage=($kostenPercentage['TotCostFund']+$kostenPercentage['FundTransCost']+$kostenPercentage['FundPerfFee']);
            $bijdrageVKM=$sub['weging']*$totaalKostenPercentage;
            $dlkostenAbsoluut=$sub['gemWaarde']*$totaalKostenPercentage/100;
            if($DB->records()>0)
            {//$kostenPercentage['percentage']<>0
              $percentageIndirectVermogenMetKostenfactor += $sub['weging'];
              $TotCostFundTxt = $this->vkma->formatGetal($kostenPercentage['TotCostFund'], 2, false, true);
              $FundTransCostTxt = $this->vkma->formatGetal($kostenPercentage['FundTransCost'], 2, false, true);
              $FundPerfFeeTxt = $this->vkma->formatGetal($kostenPercentage['FundPerfFee'], 2, false, true);
              $dlkostenAbsoluutTxt=$this->vkma->formatGetal($dlkostenAbsoluut*$this->scaleFactor, 0,false,true);
            }
            else
            {
              $TotCostFundTxt = '';
              $FundTransCostTxt = '';
              $FundPerfFeeTxt = '';
              $dlkostenAbsoluutTxt = '';
            }
        
            if($this->pdfVullen==true)
            {
              $omschrijvingWidth = $this->pdf->GetStringWidth('    ' . $fondsData['fondsOmschrijving'][$id]);
              $cellWidth = $this->pdf->widths[1] - 2;
              if ($omschrijvingWidth > $cellWidth)
              {
                $dotWidth = $this->pdf->GetStringWidth('...');
                $chars = strlen('    ' . $fondsData['fondsOmschrijving'][$id]);
                $newOmschrijving = '    ' . $fondsData['fondsOmschrijving'][$id];
                for ($i = 3; $i < $chars; $i++)
                {
                  $omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
                  if ($cellWidth > ($omschrijvingWidth + $dotWidth))
                  {
                    $omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
                    break;
                  }
                }
              }
              else
              {
                $omschrijving = '    ' . $fondsData['fondsOmschrijving'][$id];
              }
              //   echo $this->pdf->widths[0]." ".$this->pdf->widths[1]." ".$omschrijving."<br>\n";
              $this->pdf->row(array('', $omschrijving,
                                $this->vkma->formatGetal($data['eindwaarde']*$this->scaleFactor, 0),
                                $TotCostFundTxt,
                                $FundTransCostTxt,
                                $FundPerfFeeTxt,
                                $dlkostenAbsoluutTxt,
                                $this->vkma->formatGetal($sub['weging'] * 100, 2, true),
                                $this->vkma->formatGetal($bijdrageVKM, 2, true)
                              ));
              $this->pdf->excelData[]=array($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $fondsData['fondsOmschrijving'][$id],
                round($data['beginwaarde']*$this->scaleFactor, 0),
                round($data['eindwaarde']*$this->scaleFactor, 0),
                round($data['stort']*$this->scaleFactor, 0),
                round($data['resultaat']*$this->scaleFactor, 0),
                round($data['eindwaarde']*$this->scaleFactor, 0),
                round($kostenPercentage['TotCostFund'], 2),
                round($kostenPercentage['FundTransCost'], 2),
                round($kostenPercentage['FundPerfFee'], 2),
                round($dlkostenAbsoluut*$this->scaleFactor, 0),
                round($sub['weging'] * 100, 2),
                round($bijdrageVKM, 2));
            }
            $totaalBijdrageVKM+=$bijdrageVKM;
            $totaalDoorlopendekosten+=$sub['eindwaarde']*$totaalKostenPercentage/100;
            $totaalDoorlopendekostenGesplitst['TotCostFund']+=$sub['eindwaarde']*$kostenPercentage['TotCostFund']/100;
            $totaalDoorlopendekostenGesplitst['FundTransCost']+=$sub['eindwaarde']*$kostenPercentage['FundTransCost']/100;
            $totaalDoorlopendekostenGesplitst['FundPerfFee']+=$sub['eindwaarde']*$kostenPercentage['FundPerfFee']/100;
        
            $perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
            $perHoofdcategorie[$hoofdcategorie]['perf']['transkosten'] +=$data['kosten'];
            $perHoofdcategorie[$hoofdcategorie]['perf']['dlkostenAbsoluut'] +=$dlkostenAbsoluut;
            $perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
            $perCategorie[$hoofdcategorie][$categorie]['perf']['transkosten'] +=$data['kosten'];
            $perCategorie[$hoofdcategorie][$categorie]['perf']['dlkostenAbsoluut'] +=$dlkostenAbsoluut;
        
            $totaalKosten+=$data['kosten'];
            $totaaldlKosten+=$dlkostenAbsoluut;
            // listarray($data);
        
            if($this->pdfVullen==true)
            {
              if (count($fondsData['fondsen']) - 1 == $id)
              {
                if ($sub['aantal'] > 1)
                {
                  $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS');
                  $this->pdf->row(array('', '        subtotaal ' . $laatsteFonds,
                                    $this->vkma->formatGetal($sub['beginwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
                                    $this->vkma->formatGetal($sub['eindwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
                                    $this->vkma->formatGetal($sub['stort']*$this->scaleFactor, 0),
                                    $this->vkma->formatGetal($sub['resultaat']*$this->scaleFactor, 0),
                                    $this->vkma->formatGetal($sub['eindwaarde']*$this->scaleFactor, 0),
                                    $this->vkma->formatGetal($sub['transkosten']*$this->scaleFactor, 0),
                                    $this->vkma->formatGetal($sub['dlkostenPercentage'], 0),
                                    $this->vkma->formatGetal($sub['dlkostenAbsoluut']*$this->scaleFactor, 0, true),
                                    $this->vkma->formatGetal($sub['weging'] * 100, 2, true)
                                  ));
                  unset($this->pdf->CellBorders);
                  $this->pdf->Ln();
                }
                $sub = array('aantal' => 1);
                foreach ($somVelden as $veld)
                {
                  $sub[$veld] += $data[$veld];
                }
            
                $laatsteFonds = substr($fondsData['fondsOmschrijving'][$id], 0, 30);
            
              }
          
            }
        
          }
          $rekeningData=array();
          $totaalRekeningen=0;
          foreach ($fondsData['rekeningen'] as $id=>$rekening)
          {
            $tmp=array();
            $tmp['rekeningen']=array($rekening);
            $data=$this->vkma->fondsPerformance($tmp);
            $rekeningData[$id]=array('perf'=>$data,'rekening'=>$rekening);
            $rekeningWaarde[$id]=$data['eindwaarde'];
            $totaalRekeningen+=$data['eindwaarde'];
          }
          arsort($rekeningWaarde);
      
      
          if($this->pdfVullen==true)
          {
            if ($lastRegio <> '')
            {
              $subregio = $perRegio[$hoofdcategorie][$categorie][$lastRegio]['perf'];
              $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS');
              $this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
              $this->pdf->row(array('', '  subtotaal ' . $perRegio[$hoofdcategorie][$categorie][$lastRegio]['omschrijving'],
                                $this->vkma->formatGetal($subregio['beginwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
                                $this->vkma->formatGetal($subregio['eindwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
                                $this->vkma->formatGetal($subregio['stort']*$this->scaleFactor, 0),
                                $this->vkma->formatGetal($subregio['resultaat']*$this->scaleFactor, 0),
                                $this->vkma->formatGetal($subregio['eindwaarde']*$this->scaleFactor, 0),
                                $this->vkma->formatGetal($subregio['resultaat'] / $subregio['eindwaarde'] * 100, 2),
                                $this->vkma->formatGetal($subregio['weging'] * 100, 2, true),
                                $this->vkma->formatGetal($subregio['bijdrage'] * 100, 2, true)));
              $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
              $this->pdf->Ln();
              unset($this->pdf->CellBorders);
              $lastRegio = '';
            }
        
            $this->pdf->widths = $widthsBackup;
            $this->vkma->printSubTotaal($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $perCategorie[$hoofdcategorie][$categorie]);
          }
        }
        if($this->pdfVullen==true)
          $this->vkma->printSubTotaal($perHoofdcategorie[$hoofdcategorie]['omschrijving'],$perHoofdcategorie[$hoofdcategorie],'BI');
        $lastHoofdcategorie=$hoofdcategorie;
      }
  
      $perfTotaal['bijdrageVKM']=$totaalBijdrageVKM;
      $perfTotaal['transkosten']=$totaalKosten;
      $perfTotaal['dlkostenAbsoluut']=$totaaldlKosten;
      $perfTotaal['percentageIndirectVermogenMetKostenfactor']=$percentageIndirectVermogenMetKostenfactor;
  
      $this->pdf->excelData[]=array('Totaal', '',
        round($perfTotaal['beginwaarde'], 0),
        round($perfTotaal['eindwaarde'], 0),
        round($perfTotaal['stort'], 0),
        round($perfTotaal['resultaat'], 0),
        round($perfTotaal['eindwaarde'], 0),
        round($perfTotaal['kosten'], 0),
        round($perfTotaal['percentage'], 2),
        round($perfTotaal['dlkostenAbsoluut'], 0),
        round($perfTotaal['weging'] * 100, 2),
        round($perfTotaal['bijdrageVKM'], 2));
  
      if ($this->pdfVullen == true)
      {
        $this->vkma->printSubTotaal('Totaal', array('perf' => $perfTotaal), 'BI');
        $y = $this->pdf->getY() + 10 + 18 * $this->pdf->rowHeight;
        if ($y > $this->pdf->PageBreakTrigger)
        {
          $this->pdf->vmkHeaderOnderdrukken = true;
          if($this->skipSummary==false)
            $this->pdf->AddPage("P");
          //$y=$this->pdf->getY()+10;
        }
      }
  
      if($this->skipDetail==true)
      {
        $this->pdfVullen = true;
        $this->pdf->vmkHeaderOnderdrukken = true;
        $this->pdf->AddPage("P");
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      }
  
  
      if($this->skipSummary==false)
      {
    
        $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten=1";
        $DB->SQL($query);
        $DB->Query();
        $n=0;
        $grootboekKleuren=array();
        while($data=$DB->nextRecord())
        {
          $mogelijkeKleuren[$n];
          if($gewensteKleuren[$data['Grootboekrekening']]['R']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['G']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['B']['value'])
            $grootboekKleuren[$data['Omschrijving']]=array($gewensteKleuren[$data['Grootboekrekening']]['R']['value'],$gewensteKleuren[$data['Grootboekrekening']]['G']['value'],$gewensteKleuren[$data['Grootboekrekening']]['B']['value']);
          else
            $grootboekKleuren[$data['Omschrijving']]=$mogelijkeKleuren[$n];
          $n++;
        }
    
        $key='Indirecte (fonds)kosten';
        if($gewensteKleuren[$key]['R']['value'] || $gewensteKleuren[$key]['G']['value'] || $gewensteKleuren[$key]['B']['value'])
          $grootboekKleuren[$key]=array($gewensteKleuren[$key]['R']['value'],$gewensteKleuren[$key]['G']['value'],$gewensteKleuren[$key]['B']['value']);
        else
          $grootboekKleuren['Indirecte (fonds)kosten']=$mogelijkeKleuren[$n];
    
    
        $this->grootboekKleuren=$grootboekKleuren;
  
  
        $this->kostenKader($totaalDoorlopendekosten, $perfTotaal,$totaalDoorlopendekostenGesplitst);
    
        if ($this->pdfVullen == true)
        {
          $this->pdf->setAligns(array('L', 'R', 'R'));
          $this->pdf->setWidths(array(110, 30, 30));
          if ($this->melding <> '')
          {
            $this->pdf->ln();
            $this->pdf->row(array($this->melding));
            $this->pdf->excelData[] = array();
            $this->pdf->excelData[] = array('', $this->melding);
          }
          unset($this->pdf->CellFontColor);
      
          if($this->skipLangeTermijn==false)
            $this->langeTermijngrafiek();
      
          //$txt='De Indirecte (fonds)kosten van de financiële instrumenten zijn zo goed mogelijk ingeschat. Voor de betrokken derde partijen is de regelgeving nog in ontwikkeling. Er is dan ook sprake van voortschrijdend inzicht. Wij sluiten niet uit dat de Indirecte (fonds)kosten te hoog zijn ingeschat.';
          //$this->pdf->setXY($this->pdf->marge,180);
          //$this->pdf->MultiCell(280,4,$txt,0,'L',0);
        }
      }
      if (isset($this->pdf->vmkHeaderOnderdrukken))
      {
        unset($this->pdf->vmkHeaderOnderdrukken);
      }

        $this->pdf->setXY(100, -15);
        $this->pdf->AutoPageBreak = false;
        $this->pdf->MultiCell(100, 5, $this->pdf->portefeuilledata['Portefeuille'], 0, 'R');
        $this->pdf->AutoPageBreak = true;
    }

	}
  
  
  function langeTermijngrafiek()
  {
    global $__appvar;
    $db=new DB();
    $query="SELECT CRM_naw.doeldatum FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $db->query();
    $data=$db->nextRecord();
    if($data['doeldatum'] > 1900)
      $doelJaar=$data['doeldatum'];
    else
      $doelJaar=$this->pdf->rapport_jaar+10;
    
    
    if($this->pdf->lastPOST['vkma_eindjaar']>$this->pdf->rapport_jaar)
      $doelJaar=$this->pdf->lastPOST['vkma_eindjaar'];
    
    $query="SELECT Risicoklassen.verwachtRendement FROM Portefeuilles
 JOIN Risicoklassen ON Portefeuilles.Risicoklasse=Risicoklassen.Risicoklasse AND Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder
 WHERE Portefeuilles.portefeuille='".$this->portefeuille."'";
    $db->SQL($query);
    $db->query();
    $data=$db->nextRecord();
    if($data['verwachtRendement'] <> 0 )
      $rendement=$data['verwachtRendement'];
    else
    {
      $jaren=(db2jul($this->rapportageDatum)-db2jul($this->pdf->PortefeuilleStartdatum))/(365.25*3600*24);
      $rendementProcentJaar = performanceMeting($this->portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
      $rendement = $rendementProcentJaar/$jaren;
    }
    
    $query ="SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
		WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $start = $db->NextRecord();
    $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];
    if($this->pdf->lastPOST['vkma_bedrag'] <> 0)
      $beginwaarde=$this->pdf->lastPOST['vkma_bedrag'];

    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('Termijngrafiek');
    $this->pdf->excelData[]=array('doelJaar','rendement','beginwaarde','vkm');
    $this->pdf->excelData[]=array($doelJaar,round($rendement,2),round($beginwaarde,2),round($this->vkmWaarde['vkmWaarde'],4));
    $this->pdf->excelData[]=array('jaar','waardeNaKosten','cumulatieveKosten','waardeZonderKosten');
    
    $kosten=0;
    $grafiekWaarden=array();
    for($i=$this->pdf->rapport_jaar; $i<=$doelJaar; $i++)
    {
      $jaren=$i-$this->pdf->rapport_jaar;
      $nieuweWaarde=$beginwaarde*pow(1+($rendement/100),$jaren);
      $kosten+=$nieuweWaarde*($this->vkmWaarde['vkmWaarde']/100);
      
      $grafiekWaarden['waardeNaKosten'][]=$nieuweWaarde;
      $grafiekWaarden['cumulatieveKosten'][]=$kosten;
      $grafiekWaarden['waardeZonderKosten'][]=$nieuweWaarde+$kosten;
      $grafiekWaarden['datum'][]=$i;
      $this->pdf->excelData[]=array($i,round($nieuweWaarde,2),round($kosten,2),round($nieuweWaarde+$kosten,2));
    }
    $grafiekWaarden['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Cumulatieve kosten');
    
    $grafiekWaarden['titel']="Impact van kosten op het lange termijn rendement van de portefeuille";
    $waardeZonderKostenKleur=array(100,100,200);
    $waardeNaKostenKleur=array(100,200,100);
    $cumulatieveKostenKleur=array(200,100,100);

    $this->pdf->setXY(30,195);
    $this->LineDiagram(120, 55, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50


  }
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafBegin=false)
  {
    global $__appvar;
    
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['waardeNaKosten'];
    $data2 = $data['cumulatieveKosten'];
    $data = $data['waardeZonderKosten'];
    
    
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    if(count($data2)>0)
      $bereikdata = array_merge($bereikdata,$data2);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY()+2;
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->setXY($XPage,$YPage-4);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,vertaalTekst($titel,$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
    
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color2= $color[2];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
    
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }
    
    
    //	echo $maxVal;exit;
    
    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
    
    //	$maxVal=round($maxVal,floor(log10($maxVal))*-1+1);
    
    $significance=floor(log10($maxVal));
    $significance=pow(10,$significance);
    $maxVal=	ceil($maxVal/$significance)*$significance;
    
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
    
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->setXY($XDiag-7, $i);
      $this->pdf->Cell(7 , 4 , "€ ". 0-($n*$stapgrootte) , 0, 1, "R");
      
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->setXY($XDiag-7, $i);
        $this->pdf->Cell(7 , 4 , "€ " .(($n * $stapgrootte) + 0) , 0, 1, "R");
        
      }
      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      
      if ($i>0 || $vanafBegin==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }
      
      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }
    
    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);
      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;
        
        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    $aantal=count($legendaItems);
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$w+5 , $YPage+$step+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+$w+3+5,$YPage+$step+10);
      $this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));
      
      $step+=6;
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
}