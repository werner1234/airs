<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_117/RapportOIS_L117.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");
//ini_set('max_execution_time',60);
class RapportVKMS_L117
{

	function RapportVKMS_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VKMS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht kosten";

    $this->vkm=new RapportVKM(null,$portefeuille,$rapportageDatumVanaf,$rapportageDatum, false);
    $this->vkm->writeRapport();
    
    $this->vkmdata=$this->kostenKader($portefeuille, $this->vkm->vkmWaarde['totaalDoorlopendekosten'],  $this->vkm->vkmWaarde['perfTotaal'],  $this->vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']);
   // listarray($vkmTotaal);
   // $this->vkmdata=$this->vkm->vkmWaarde;
    
	}
  
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  
  
  function writeRapport()
	{
		global $__appvar;
    
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->marge+85,25,
      $this->pdf->marge+85,180,
      $this->pdf->marge+80,185,
      $this->pdf->marge,185);
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(85));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(26);
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if( $this->pdf->rapport_taal == 2 ) {
      $tekst = "Investment service costs:
These are the costs you have paid to the bank: portdolio mangement fee, custody fee as well as the transaction costs.

Taxes:
All taxes that the bank has withheld on your behalf, such as withholding tax, stock exchange tax, Reynder's tax. 

Refunds:
Investment funds charge a management fee for managing the assets in the fund. If ABN AMRO receives a refund of (part of) this management fee, this part will be transferred to you. The calculation is made on the basis of the average assets invested in the investment fund at the end of each quarter.

Product costs:
Also called ongoing costs. These are the costs made by the investment fund in manging the fund's assets. The main cost is the management fee (of which you may get a refund), but also the transaction costs, custody fee, accountant costs, administration,..... These costs are periodically included in the price of the fund and are therefore not charged to you separately. We provide this information to give you a full cost overview of your portfolio.

Indication yearly costs:
This gives an indication of the total costs (including external product costs) in function of the average invested capital. This percentage will differ from the percentage you can find under \"fee(s) and taxes\" on the investment result page, where only fees actually charged by the bank are taken into account and where TWR calculation applies.";
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $tekst = "Frais de services d'investissement:
Frais payés à la banque pour les services (frais de transaction, droits de garde, frais de gestion de portefeuille).

Taxes:
Tous impôts (Belges et étrangers) ainsi que toutes retenues à la source.

Remboursement des commissions de commercialisation:
Les fonds d'investissement facturent des frais de gestion pour la gestion des actifs du fonds. Si ABN AMRO reçoit un remboursement de (une partie de) ces frais de gestion, cette partie vous seront remboursées. Le calcul est effectué sur la base de la moyenne des actifs investis dans le fonds d'investissement à la fin de chaque trimestre.

Frais liés aux produits:
Aussi appelé frais récurrents. Il s'agit des frais engagés par le fonds d'investissement pour la gestion de ses actifs. Le coût principal est celui de la gestion (dont une partie est remboursée par la rétrocession des commissions de commercialisation), mais aussi les frais de transactions, les droits de garde, les honoraires des auditeurs, l'administration, etc. Ces frais sont déjà inclus dans le prix du fonds, mais sont précisés pour vous permettre d'avoir une vue globale de votre portefeuille.

Total indicatif des frais sur la période:
Cela donne simplement une indication des frais totaux (y compris les frais liés aux produits) en fonction du capital moyen investi. Ce pourcentage s'écarte du pourcentage que vous trouverez sous \"frais et taxes\" sur la page Performances, ou seuls les frais de services d'investissement sont pris en compte et ou le calcul du taux de rendement pondéré par le temps s'applique.";
    } else {
      $tekst = 'Kosten beleggingsdienstverlening:
Dit zijn de kosten die u aan de bank betaald heeft voor het beheren en bewaren van de portefeuille alsook de transactiekosten.

Taksen:
Alle belastingen en taksen die de bank voor u heeft ingehouden zoals roerende voorheffing, beurstaks, Reynderstaks.

Rebates:
Beleggingsfondsen rekenen een beheervergoeding aan voor het beheren van het vermogen in het fonds.
 Indien ABN AMRO een terugstorting ontvangt van (een deel van) deze beheersvergoeding, dan wordt dit gedeelte aan u doorgestort. De berekening gebeurt op basis van het gemiddeld belegd vermogen in het beleggingsfonds op het einde van ieder kwartaal.

Product kosten:
Ook wel lopende kosten factor genoemd. Dit zijn dekosten die het beleggingsfonds maakt inzake het beheer van het vermogen van het fonds. De voornaamste kost is de fee voor het beheer (waarvan u een deel terugkrijgt via de rebates), maar ook de kosten voor het uitvoeren van transacties, bewaarloon,kosten accountant, administratie,...
Deze kosten worden op periodieke wijze verrekend in de koers van het fonds en dus niet afzonderlijk aan u doorgerekend. Wij houden eraan u dit mede te delen om een correct beeld te geven van het volledige kostenoverzicht van uw portefeuille.

Indicatieve kosten rapportageperiode:
Dit geeft louter een indicatie van de totale kosten (incl. de externe productkosten) in functie van het gemiddeld geïnvesteerd vermogen.
Dit percentage zal afwijken van het percentage wat u kan terugvinden onder ""kosten en belastingen""op de pagina van het beleggingsresultaat, waar enkel rekening gehouden wordt met effectief door de bank aangerekende kosten en waar TWR- berekening van toepassing is.';
    }
    $this->pdf->MultiCell(85,4,$tekst,0,'L');
    
    $this->pdf->setY(25);
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->setAligns(array('L','L','L','R','R'));
    $this->pdf->SetWidths(array(95,30,85,40,30));
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->Row(array('','','','',''));
    $this->pdf->Row(array('','','','',''));
    $this->pdf->Row(array('','','',vertaalTekst('in EUR', $this->pdf->rapport_taal),vertaalTekst('Percentage', $this->pdf->rapport_taal)));
    $this->pdf->Row(array('','','','',''));
    unset($this->pdf->fillCell);
    $this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->CellBorders=array('','U','U','U','U','U');
    $this->pdf->rowHeight=6;
    unset($this->pdf->CellBorders);
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->ln(0.5);
    $ystart=$this->pdf->getY();
    $this->pdf->ln(0.5);
    $this->pdf->SetWidths(array(95.5,30,85,40,29));
    $this->pdf->Row(array('',vertaalTekst('Directe kosten', $this->pdf->rapport_taal),'','',''));
    $this->pdf->ln(1);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$rowHeightBackup;
    $this->pdf->Row(array('',vertaalTekst('Aan de bank', $this->pdf->rapport_taal)));
    $vertalingen=array('BEW'=>'Servicekosten bank');
    foreach($this->vkmdata['grootboekKosten'] as $gb=>$waarde)
    {
      $waarde=$waarde*-1;
      $this->pdf->Row(array('', '', vertaalTekst('Kosten ' . (isset($vertalingen[$gb])?$vertalingen[$gb]:$gb), $this->pdf->rapport_taal), $this->formatGetal($waarde, 0), $this->formatGetal($waarde / $this->vkmdata['gemiddeldeWaarde'] * 100, 2) . '%'));
    }
    $this->pdf->Row(array('',vertaalTekst('Externe kosten', $this->pdf->rapport_taal)));
    //listarray($this->vkmdata);

    $this->pdf->Row(array('','',vertaalTekst('Belastingen', $this->pdf->rapport_taal),$this->formatGetal($this->vkmdata['belasting']*-1,0),$this->formatGetal($this->vkmdata['belasting']*-1 / $this->vkmdata['gemiddeldeWaarde'] * 100,2).'%'));
    $this->pdf->Row(array('',vertaalTekst('Inducements', $this->pdf->rapport_taal)));
    $this->pdf->Row(array('','',vertaalTekst('De door de bank ontvangen distributievergoedingen die we aan u doorstorten', $this->pdf->rapport_taal),$this->formatGetal(0,0),$this->formatGetal(0,2).'%'));
    $this->pdf->Row(array('','',vertaalTekst('De door de bank ontvangen distributievergoedingen die we niet doorstorten', $this->pdf->rapport_taal),$this->formatGetal(0,0),$this->formatGetal(0,2).'%'));
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->rowHeight=6;
    $this->pdf->Row(array('',vertaalTekst('Toaal Directe kosten', $this->pdf->rapport_taal),'',$this->formatGetal($this->vkmdata['totaalDirecteKosten']*-1,0),$this->formatGetal($this->vkmdata['totaalDirecteKosten']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->rect($this->pdf->marge+95,$ystart,185,$this->pdf->getY()-$ystart+0.5);
    
    
    $this->pdf->ln();
    
    $this->pdf->ln(0.5);
    $ystart=$this->pdf->getY();
    $this->pdf->ln(0.5);
    $this->pdf->Row(array('',vertaalTekst('Indirecte kosten', $this->pdf->rapport_taal),'','',''));
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$rowHeightBackup;
    $this->pdf->Row(array('',vertaalTekst('Externe kosten', $this->pdf->rapport_taal)));
    $this->pdf->Row(array('','',vertaalTekst('Product kosten', $this->pdf->rapport_taal),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['TotCostFund']*-1,0),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['TotCostFund']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->Row(array('','',vertaalTekst('Fonds transactie kosten', $this->pdf->rapport_taal),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['FundTransCost']*-1,0),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['FundTransCost']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->Row(array('','',vertaalTekst('Fonds performance fees', $this->pdf->rapport_taal),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['FundPerfFee']*-1,0),$this->formatGetal($this->vkmdata['totaalDoorlopendekostenGesplitst']['FundPerfFee']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->rowHeight=6;
    $this->pdf->SetWidths(array(95.5,115,40,29));
    $this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->Row(array('',vertaalTekst('Totaal Indirecte kosten', $this->pdf->rapport_taal),$this->formatGetal($this->vkmdata['totaalDoorlopendekosten']*-1,0),$this->formatGetal($this->vkmdata['totaalDoorlopendekosten']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->ln(0.5);
    $this->pdf->rect($this->pdf->marge+95,$ystart,185,$this->pdf->getY()-$ystart);
    
    $this->pdf->ln();
    $this->pdf->line($this->pdf->marge+95,$this->pdf->getY(),$this->pdf->w-$this->pdf->marge,$this->pdf->getY());
    $this->pdf->ln();
    unset($this->pdf->fillCell);
    $this->pdf->Row(array('',vertaalTekst('Totaal kosten aan de bank', $this->pdf->rapport_taal),
                      $this->formatGetal(($this->vkmdata['totaalDirecteKosten']-$this->vkmdata['belasting'])*-1,0),
                      $this->formatGetal(($this->vkmdata['totaalDirecteKosten']-$this->vkmdata['belasting'])/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->ln(2);
    $this->pdf->Row(array('',vertaalTekst('Totaal Externe kosten', $this->pdf->rapport_taal),$this->formatGetal(($this->vkmdata['belasting']+$this->vkmdata['totaalDoorlopendekosten'])*-1,0),$this->formatGetal($this->vkmdata['belasting']/$this->vkmdata['gemiddeldeWaarde']*-100,2).'%'));
    $this->pdf->ln(2);
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0],$this->pdf->rapport_donkergrijs[1],$this->pdf->rapport_donkergrijs[2]);
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',vertaalTekst('Indicatieve kosten rapportageperiode', $this->pdf->rapport_taal),$this->formatGetal(($this->vkmdata['totaalDoorlopendekosten']+$this->vkmdata['totaalDirecteKosten'])*-1,0),$this->formatGetal( $this->vkmdata['vkmWaarde']*-1,2).'%'));
    $this->pdf->ln();
    $this->pdf->line($this->pdf->marge+95,$this->pdf->getY(),$this->pdf->w-$this->pdf->marge,$this->pdf->getY());
  
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$rowHeightBackup;
  }
  
  
  function kostenKader($portefeuille,$totaalDoorlopendekosten,$perfTotaal,$totaalDoorlopendekostenGesplitst)
  {
    
    if ($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta<>'')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    
    $DB=new DB();
    $query="SELECT CRM_naw.naam FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $crm=$DB->nextRecord();
    
    
    $query="SELECT SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet $koersQuery)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit $koersQuery)) AS totaal
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vkm->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->vkm->rapportageDatum."'
AND Rekeningen.Memoriaal = 0 AND Rekeningmutaties.Grootboekrekening='FONDS'  AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S')
GROUP BY Rekeningmutaties.Grootboekrekening";
    $DB->SQL($query);
    $DB->Query();
    $spreadKosten=$DB->nextRecord();
    $spreadKostenEUR=($this->vkm->spreadKostenPunten / 10000 * $spreadKosten['totaal']);
    
    $alleGrootboekWaarden=array();
    $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ))*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vkm->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->vkm->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      if($data['Grootboekrekening']=='KOBU')
        $data['Omschrijving']='Overige kosten';
      $alleGrootboekWaarden[$data['Grootboekrekening']]=$data['totaal'];
    }

//echo "$spreadKostenEUR=(".$this->vkm->spreadKostenPunten." / 10000 * ".$spreadKosten['totaal']."); <br>\n";exit;
    $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ))*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening AND (Grootboekrekeningen.Kosten=1 OR Rekeningmutaties.Grootboekrekening IN('BEH','BEW','KOST') ) AND Rekeningmutaties.Grootboekrekening <> 'TOB'
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vkm->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->vkm->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $gemiddelde=$this->vkm->verdelingTotaal['totaal']['gemiddelde'];
    $doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['gemWaarde'];
//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['gemWaarde']."<br>\n";exit;

    
    $barData=array();

    $percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
    $herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
    $aandeelIndirect=$perfTotaal['gemWaarde']/$gemiddelde;
    $vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
    $barData['Lopende kosten']=$vkmPercentagePortefeuille;
    if($this->vkm->pdfVullen==true)
    {
    
    }
    $totaal=0;
    $grootBoekKostenTotaal=0;
    $kostenPerGrootboek=array('BEW','KOBU');
    $grootboekKosten=array('BEW'=>0);//,'overige'=>0
    $grootboekOmschrijving=array('BEH'=>'Beheerfee','BEW'=>'Bewaarloon','KOST'=>'Transactiekosten','overige'=>'Overige kosten');
    while($data = $DB->nextRecord())
    {
      if(!in_array($data['Grootboekrekening'],$kostenPerGrootboek))
      {
        $data['Grootboekrekening'] = 'overige';
        $data['Omschrijving']='Overige kosten';
      }
      if($data['Grootboekrekening']=='KOBU')
      {
        $data['Grootboekrekening'] = 'KOST';
        $data['Omschrijving'] = 'Transactiekosten';
      }
      
      $grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
      if(!isset($grootboekOmschrijving[$data['Grootboekrekening']]))
        $grootboekOmschrijving[$data['Grootboekrekening']]=$data['Omschrijving'];
      $totaal+=$data['totaal'];
      $kostenProcent=$data['totaal']/$gemiddelde*100;
      $barData[$data['Omschrijving']]+=$kostenProcent;
      
    }
    $belasting=$alleGrootboekWaarden['TOB']+$alleGrootboekWaarden['DIVBE']+$alleGrootboekWaarden['ROER']+$alleGrootboekWaarden['BTLBR']+$alleGrootboekWaarden['TOBO'];
    $totaal+=  $belasting;
    
    
    if($spreadKostenEUR <> 0)
    {
      $kostenProcent = $spreadKostenEUR / $gemiddelde * 100;
      $totaal += $spreadKostenEUR;
      $barData['Spread-kosten'] = $kostenProcent;
      
      $grootboekKosten[$data['Spread-kosten']]+=$spreadKostenEUR;
      $grootboekOmschrijving['Spread-kosten']='Spread-kosten';
    }
    
    $grootBoekKostenTotaal=$totaal;
    //echo " $grootBoekKostenTotaal=$totaal;";exit;
    
    $kostenPercentage=$totaal/$gemiddelde*100;
    $vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
    
    $vkmArray=array('belasting'=>$belasting,'vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde,'grootboekKosten'=>$grootboekKosten,'grootboekOmschrijving'=>$grootboekOmschrijving,
                    'gemiddeldeWaarde'=>$gemiddelde,'grootBoekKostenTotaal'=>$grootBoekKostenTotaal,'totaalDoorlopendekosten'=>$totaalDoorlopendekosten,'totaalDirecteKosten'=>$totaal,
                    'totaalDoorlopendekostenGesplitst'=>$totaalDoorlopendekostenGesplitst,'doorlopendeKostenPercentage'=>$doorlopendeKostenPercentage,'percentageIndirectVermogenMetKostenfactor'=>$perfTotaal['percentageIndirectVermogenMetKostenfactor'],
                    'fondsGemiddeldeWaarde'=>$perfTotaal['gemWaarde'],'alleGrootboekWaarden'=>$alleGrootboekWaarden,'naam'=>$crm['naam']);
    if($this->portefueille=$portefeuille)
    {
      $this->vkmWaarde[$portefeuille] = $vkmArray;
      $this->barData[$portefeuille]=$barData;
    }
    return $vkmArray;
    
  }
  
 
}
?>