<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/14 17:49:52 $
File Versie					: $Revision: 1.3 $

$Log: RapportVKMS_L85.php,v $
Revision 1.3  2019/12/14 17:49:52  rvv
*** empty log message ***

Revision 1.2  2019/08/31 12:20:52  rvv
*** empty log message ***

Revision 1.9  2019/02/06 07:33:55  rvv
*** empty log message ***

Revision 1.8  2019/02/03 17:52:57  rvv
*** empty log message ***

Revision 1.7  2019/02/03 15:50:24  rvv
*** empty log message ***

Revision 1.6  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.5  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.4  2019/01/26 19:33:28  rvv
*** empty log message ***

Wij verstrekken u ten minst

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVKMS_L85
{
	function RapportVKMS_L85($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		if(is_object($pdf))
		{
			$this->pdf = &$pdf;
			$this->pdf->rapport_type = "VKMS";
			$this->pdf->rapport_datum = db2jul($rapportageDatum);
			$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
			$this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
			$this->pdf->underlinePercentage=0.8;
			$this->pdf->rapport_titel = vertaalTekst("Vergelijkende kostenmaatstaf",$this->pdf->rapport_taal);
			$this->pdfVullen=true;
			$this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
		}
		else
			$this->pdfVullen=false;
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
			'Doorl.kosten %','FundTransCost %','FundPerfFee %','dl kosten absoluut','Weging','VKM bijdrage');
		$this->verdelingTotaal=array();
		$this->verdelingFondsen=array();
		$this->skipSummary=false;
		$this->skipDetail=true;
    $this->skipLangeTermijn=false;
	}

	function formatGetal($waarde, $dec,$procent=false,$toonNul=false)
	{
	  if($waarde==0 && $toonNul==false)
	    return;
		$data=number_format($waarde,$dec,",",".");
		if($procent==true)
		  $data.=" %";
		return $data;
	}


	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if($waarde==0)
	    return;
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}



	function printSubTotaal($lastCategorieOmschrijving,$allData,$style='')
	{
		if($this->pdf->getY()+4>$this->pdf->PageBreakTrigger)
			$this->pdf->addPage();
	  if($lastCategorieOmschrijving != 'Totaal')
	  {
	    $prefix='Subtotaal';
	    $this->pdf->CellBorders = array('','','TS','TS','TS','TS','TS','','','','TS','TS','TS','TS','TS');
	  }
	  else
	  {
	    $prefix='';
	    $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),'','','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
	  }

    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

    $this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
    $this->pdf->setX($this->pdf->marge);

    $data=$allData['perf'];


   	$this->pdf->row(array('','',
												$this->formatGetal($data['beginwaarde'],0),
												$this->formatGetal($data['eindwaarde'],0),
												$this->formatGetal($data['stort'],0),
												$this->formatGetal($data['resultaat'],0),
                        $this->formatGetal($data['gemWaarde'],0),
											'','',
											$this->formatGetal($data['dlkostenPercentage'],0),
											$this->formatGetal($data['dlkostenAbsoluut'],0),
											$this->formatGetal($data['weging']*100,2,true),
											$this->formatGetal($data['bijdrageVKM'],2,true)
										));

    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function printKop($title, $type='',$ln=false)
	{
		if($ln)
	    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,vertaalTekst($title,$this->pdf->rapport_taal),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}

	function vulVorigJaar()
	{
				if(substr($this->vanafDatum,5,5)=='01-01')
					$startjaar=true;
				else
					$startjaar=false;
				$fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $this->vanafDatum,$startjaar);
				vulTijdelijkeTabel($fondswaarden ,$this->portefeuille, $this->vanafDatum);
				$this->extraVulling = true;

	}

	function kostenKader($totaalDoorlopendekosten,$perfTotaal,$totaalDoorlopendekostenGesplitst)
	{
		$query="SELECT SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)) AS totaal
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
AND Rekeningen.Memoriaal = 0 AND Rekeningmutaties.Grootboekrekening='FONDS'  AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S')
GROUP BY Rekeningmutaties.Grootboekrekening";
		$DB=new DB();
		$DB->SQL($query);
		$DB->Query();
		$spreadKosten=$DB->nextRecord();
		$spreadKostenEUR=($this->spreadKostenPunten / 10000 * $spreadKosten['totaal']);
    
    $fontsize=$this->pdf->rapport_fontsize-1;
    
    $query="SELECT
Portefeuilles.Portefeuille,
Portefeuilles.Risicoprofiel,
Portefeuilles.Risicoklasse,
Risicoklassen.verwachtRendement,
Portefeuilles.BeheerfeeBTW
FROM
Portefeuilles
INNER JOIN Risicoklassen ON Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder AND Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $risicoklasse=$DB->nextRecord();
  

    $query="SELECT CRM_naw.doeldatum FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->query();
    $data=$DB->nextRecord();
    
    if($data['doeldatum'] > 1900)
      $jaren=$this->pdf->rapport_jaar-$data['doeldatum'];
    else
      $jaren=10;
    
	 $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))*-1  AS totaal,
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
		$gemiddelde=$this->verdelingTotaal['totaal']['gemiddelde'];
		$doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['gemWaarde'];
//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['gemWaarde']."<br>\n";exit;
  

		$percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
		$herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
		$aandeelIndirect=$perfTotaal['gemWaarde']/$gemiddelde;
		$vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
    $barData=array();
    $barData['Doorlopende kosten']=$vkmPercentagePortefeuille;

		$totaal=0;
    $grootBoekKostenTotaal=0;
    $grootboekKosten=array();
    $kostenRow=array();
    $kostenData=array('BEH'=>array('Grootboekrekening'=>'BEH','Omschrijving'=>'Beheer- of adviesvergoeding (incl. administratie- en toezichtskosten)'),
                      'KOST'=>array('Grootboekrekening'=>'KOST','Omschrijving'=>'Transactiekosten depotbank'),
                      'BEW'=>array('Grootboekrekening'=>'BEW','Omschrijving'=>'Kosten depotbank (administratie, bewaring, servicekosten)'));
    
    while($data = $DB->nextRecord())
    {
      if($data['Grootboekrekening']=='KOBU')
      {
        $data['Grootboekrekening'] = 'KOST';
      }
      elseif($data['Grootboekrekening']=='KNBA')
      {
        $data['Grootboekrekening'] = 'BEW';

      }
  
      if($data['Grootboekrekening']=='BEW' AND !isset($kostenData['VALK']))
        $kostenData['VALK']=array('Grootboekrekening'=>'VALK','Omschrijving'=>'Valutakosten depotbank');
      
      if(!isset($kostenData[$data['Grootboekrekening']]['Grootboekrekening']))
      {
        $kostenData[$data['Grootboekrekening']]['Grootboekrekening'] = $data['Grootboekrekening'];
      }
      if(!isset($kostenData[$data['Grootboekrekening']]['Omschrijving']))
      {
        $kostenData[$data['Grootboekrekening']]['Omschrijving'] = $data['Omschrijving'];
      }
      $kostenData[$data['Grootboekrekening']]['totaal']+=$data['totaal'];
    }
    
		foreach($kostenData as $data)
		{
			if($this->pdfVullen==true)
			{
        if($data['Grootboekrekening']=='VALK')
          $nulTonen=true;
        else
          $nulTonen=false;
          
				$kostenProcent=$data['totaal']/$gemiddelde*100;
        
        if($data['Grootboekrekening']=='BEH')
        {
          $bedrag=$data['totaal']/(1+($risicoklasse['BeheerfeeBTW']/100));
          $btw=$data['totaal']-$bedrag;
          $kostenRow[] = array(vertaalTekst($data['Omschrijving'], $this->pdf->rapport_taal).'','€', $this->formatGetal($bedrag, 0), $this->formatGetal($bedrag/$gemiddelde*100, 2) . ' %');
          if($risicoklasse['BeheerfeeBTW'] <> 0)
          {
            $kostenRow[] = array("BTW over ".vertaalTekst($data['Omschrijving'], $this->pdf->rapport_taal), '€',$this->formatGetal($btw, 0), $this->formatGetal($btw/$gemiddelde*100, 2) . ' %');
          }
        }
        else
        {
          $kostenRow[] = array(vertaalTekst($data['Omschrijving'], $this->pdf->rapport_taal),'€', $this->formatGetal($data['totaal'], 0,false,$nulTonen), $this->formatGetal($kostenProcent, 2,false,$nulTonen) . ' %');
        }
//				$this->pdf->excelData[]=array('',$data['Omschrijving'],'',round($data['totaal'],0),round($kostenProcent,2) );
				$barData[$data['Omschrijving']]=$kostenProcent;
			}
			$grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
			$totaal+=$data['totaal'];
		}
    $grootBoekKostenTotaal=$totaal;

    if($spreadKostenEUR <> 0)
		{
			$kostenProcent = $spreadKostenEUR / $gemiddelde * 100;
			if($this->pdfVullen==true)
			{
				
        $kostenRow[]=array(vertaalTekst('Spread-kosten',$this->pdf->rapport_taal),'€', $this->formatGetal($spreadKostenEUR, 0), $this->formatGetal($kostenProcent, 2) . ' %');
				//$this->pdf->excelData[]=array('','Spread-kosten','',round($spreadKostenEUR,0),round($kostenProcent,2) );
			}
			$totaal += $spreadKostenEUR;
			$barData['Spread-kosten'] = $kostenProcent;
		}


		$kostenPercentage=$totaal/$gemiddelde*100;
		$vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
		if($this->pdfVullen==true)
		{
	
		}
    
    
    if($this->pdfVullen==true)
    {
      
      $this->pdf->setAligns(array('L'));
      $this->pdf->setWidths(array(297-$this->pdf->marge*2));
  
      $this->pdf->row(array(vertaalTekst('Wij verstrekken u ten minste jaarlijks een overzicht van de totale directe en indirecte kosten waar u mee te maken heeft. De verschillende kosten die worden onderscheiden zijn:',$this->pdf->rapport_taal).'
'.vertaalTekst('- Directe kosten: de kosten van onze dienstverlening, inclusief de kosten van de depotbank voor het aanhouden van uw beleggingsrekening.' ,$this->pdf->rapport_taal).'
'.vertaalTekst('- Indirecte kosten: de kosten van financiële instrumenten in uw beleggingsportefeuille, met name de (doorlopende) kosten van de beleggingsfondsen in uw portefeuille (die door de fondsaanbieders worden verrekend in de koers van het fondsen).',$this->pdf->rapport_taal).'

'.vertaalTekst('In het onderstaande overzicht zijn de werkelijke directe en indirecte kosten met betrekking tot uw beleggingsportefeuille opgenomen over de laatste 12 maanden.' ,$this->pdf->rapport_taal).' '.vertaalTekst('Door middel van twee illustraties is weergegeven wat de impact is van de kosten op het rendement van de afgelopen 12 maanden en wat de impact is van de verwachte kosten op het verwachte rendement van de portefeuille op lange termijn.' ,$this->pdf->rapport_taal) . ' '. vertaalTekst('Indien u meer informatie wenst over de specificatie van de te verwachten kosten, kunt u uiteraard contact met ons opnemen.',$this->pdf->rapport_taal) ));
  
      $this->pdf->ln();
    $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      $this->pdf->setAligns(array('L', 'R','R', 'R'));
      $this->pdf->setWidths(array(133, 30, 30));
      $startY=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array(vertaalTekst('Beleggingsprofiel' ,$this->pdf->rapport_taal),vertaalTekst($risicoklasse['Risicoklasse'],$this->pdf->rapport_taal)));
      $rendementProcent  	= performanceMeting($this->portefeuille, $this->vanafDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
      $this->pdf->row(array(vertaalTekst('Werkelijk bruto jaarrendement over de afgelopen 12 maanden' ,$this->pdf->rapport_taal), $this->formatGetal($rendementProcent+$vkmWaarde, 2) . ' %' ));
      $this->pdf->row(array(vertaalTekst('Verwacht bruto rendement per jaar' ,$this->pdf->rapport_taal), $this->formatGetal($risicoklasse['verwachtRendement'], 2) . ' %' ));
      $this->pdf->row(array(vertaalTekst('Gemiddeld vermogen' ,$this->pdf->rapport_taal), $this->formatGetal($gemiddelde, 0)));
      $this->pdf->ln();
  
      $this->pdf->setWidths(array(130,5,15,15));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array(vertaalTekst('Direct: werkelijke kosten van onze dienstverlening',$this->pdf->rapport_taal), '', ''));
      $this->pdf->excelData[]=array('',vertaalTekst('Directe kosten vanaf' ,$this->pdf->rapport_taal). ' ' . date('d-m-Y', db2jul($this->vanafDatum)),'', vertaalTekst('EUR' ,$this->pdf->rapport_taal), vertaalTekst('Percentage' ,$this->pdf->rapport_taal));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
  
      foreach($kostenRow as $row)
        $this->pdf->row($row);
  
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array(vertaalTekst('Totaal directe kosten',$this->pdf->rapport_taal),'€', $this->formatGetal($totaal, 0), $this->formatGetal($kostenPercentage, 2).' %'));
      $this->pdf->ln();
      
      
      
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array(vertaalTekst('Indirect: werkelijke kosten van financiële instrumenten in portefeuille (o.a. beleggingsfondsen)' ,$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array(vertaalTekst('Eenmalig kosten (in- en uitstapkosten)' ,$this->pdf->rapport_taal), '€',$this->formatGetal(0, false,false,true) , $this->formatGetal(0, 2,true,true)));
      $kostenSom=array_sum($totaalDoorlopendekostenGesplitst);
      $aandeel=($totaalDoorlopendekostenGesplitst['TotCostFund'])/$kostenSom;
      $this->pdf->row(array(vertaalTekst('Doorlopend (lopende kosten factor)',$this->pdf->rapport_taal),'€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$gemiddelde/100, 0), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
      $aandeel=$totaalDoorlopendekostenGesplitst['FundTransCost']/$kostenSom;
      $this->pdf->row(array(vertaalTekst('Doorlopend (transactiekosten)' ,$this->pdf->rapport_taal),'€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$gemiddelde/100, 0), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
      $aandeel=$totaalDoorlopendekostenGesplitst['FundPerfFee']/$kostenSom;
      $this->pdf->row(array(vertaalTekst('Incidentele kosten (bijvoorbeeld performance fee)' ,$this->pdf->rapport_taal),'€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$gemiddelde/100, 0,false,true), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
  
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array(vertaalTekst('Totaal indirecte kosten',$this->pdf->rapport_taal), '€',$this->formatGetal($vkmPercentagePortefeuille*$gemiddelde/100, 0), $this->formatGetal($vkmPercentagePortefeuille, 2,true ,true)));
      $this->pdf->ln();
  
      $this->pdf->row(array(vertaalTekst('Totale directe en indirecte kosten over de afgelopen 12 maanden' ,$this->pdf->rapport_taal), '€',$this->formatGetal($vkmWaarde*$gemiddelde/100, 0), $this->formatGetal($vkmWaarde, 2,true ,true)));
      $this->pdf->ln();

      if ($this->melding <> '')
      {
        $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
        $this->pdf->row(array($this->melding));
        $this->pdf->excelData[] = array();
        $this->pdf->excelData[] = array('', $this->melding);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      }
      
      
      $this->pdf->ln();
  

      $query="SHOW COLUMNS FROM CRM_naw like 'AfwijkendeExAnte'";
      $percentage=array();
      if($DB->Qrecords($query)>0)
      {
        $query="SELECT CRM_naw.AfwijkendeExAnte FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
        $DB->SQL($query);
        $DB->Query();
        $percentage=$DB->nextRecord();
      }
      
      if($percentage['AfwijkendeExAnte']<>0)
      {
        $this->pdf->row(array(vertaalTekst('Totaal te verwachten directe en indirecte kosten op jaarbasis',$this->pdf->rapport_taal), '','', $this->formatGetal($percentage['AfwijkendeExAnte'], 2, true, true)));
      }
      else
      {
        $this->pdf->row(array(vertaalTekst('Totaal te verwachten directe en indirecte kosten op jaarbasis',$this->pdf->rapport_taal), '','', $this->formatGetal($vkmWaarde, 2, true, true)));
      }
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array(vertaalTekst('Werkelijk netto jaarrendement over de afgelopen 12 maanden' ,$this->pdf->rapport_taal),'','', $this->formatGetal($rendementProcent, 2) . ' %' ));
      $this->pdf->row(array(vertaalTekst('Verwacht netto rendement per jaar' ,$this->pdf->rapport_taal),'', '',$this->formatGetal($risicoklasse['verwachtRendement']-$vkmWaarde, 2) . ' %' ));
      $this->pdf->row(array(vertaalTekst('Beleggingshorizon in jaren' ,$this->pdf->rapport_taal),'','', $jaren));
     // $this->pdf->ln();
     // $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
     // $this->pdf->row(array('Indien u meer informatie wenst over de specificatie van de te verwachten kosten kunt u contact met ons opnemen.', '', ''));
  
      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $startYGrafiek=$this->pdf->getY();
      $this->pdf->setXY(170,$startYGrafiek-5);
      arsort($barData);
      //echo $startYGrafiek-$startY-10;exit;
      $huidigeY=$this->pdf->getY();
     // $this->VBarVerdeling(50,$startYGrafiek-$startY-10,$barData);
      $this->pdf->setY(50);
    }
    
    
    $this->vkmWaarde=array('vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde,'grootboekKosten'=>$grootboekKosten,
      'gemiddeldeWaarde'=>$gemiddelde,'grootBoekKostenTotaal'=>$grootBoekKostenTotaal,'totaalDoorlopendekosten'=>$totaalDoorlopendekosten,'totaalDirecteKosten'=>$totaal,
      'totaalDoorlopendekostenGesplitst'=>$totaalDoorlopendekostenGesplitst);

	}

	function getGewogenStortingenOnttrekkingen($van,$tot)
	{
		$DB=new DB();
		$query = "SELECT " .
			"SUM(((TO_DAYS('".$tot."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$tot."') - TO_DAYS('".$van."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, " .
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal " .
			"FROM  (Rekeningen, Portefeuilles)
	       Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening " .
			"WHERE " .
			"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
			"Rekeningmutaties.Verwerkt = '1' AND " .
			"Rekeningmutaties.Boekdatum > '".$van."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();
    return $weging;
	}

	function getGewogenStortingenOnttrekkingenFondsen($datumBegin,$datumEind,$rekeningFondsenWhere,$koersQuery)
	{
		$DB=new DB();
$queryAttributieStortingenOntrekkingen = "SELECT ".
"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
"  * ((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
"FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
"WHERE ".
"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
"Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Grootboekrekening='FONDS' AND ".
"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
" $rekeningFondsenWhere ";
    $DB->SQL($queryAttributieStortingenOntrekkingen);//echo $queryAttributieStortingenOntrekkingen;
		$DB->Query();
		$weging = $DB->NextRecord();
		return $weging;
	}

	function writeRapport()
	{
		global $__appvar,$USR;

		$this->vulVorigJaar();

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
			$dataWidth=array(28,50,20,20,20,20,20,18,18,18,18,18,15);
			$this->pdf->SetWidths($dataWidth);

			$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'], $this->pdf->rapport_default_fontcolor['g'], $this->pdf->rapport_default_fontcolor['b']);
			$this->pdf->AddPage();
			$this->pdf->templateVars['VKMPaginas'] = $this->pdf->page;
			$this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'], $this->pdf->rapport_lijn_rood['g'], $this->pdf->rapport_lijn_rood['b']);
			$this->pdf->SetLineWidth(0.1);
		}



		$indexberekening=new indexHerberekening();
		$julvanaf=db2jul($this->rapportageDatumVanaf);
		$jultot=db2jul($this->rapportageDatum);
		$dagenTotaal=round(($jultot-$julvanaf)/86400);
		$this->perioden=$indexberekening->getMaanden($julvanaf,$jultot);
		foreach($this->perioden as $periode)
		{
			$portefeuileWaarde=array();
			$dagenPeriode=round((db2jul($periode['stop'])-db2jul($periode['start']))/86400);

			if(substr($this->vanafDatum,5,5)=='01-01')
				$startjaar=true;
			else
				$startjaar=false;
			$fondswaardenStart=berekenPortefeuilleWaarde($this->portefeuille, $periode['start'],$startjaar,$this->pdf->rapportageValuta,$periode['start']);
//			$fondswaardenStop=berekenPortefeuilleWaarde($this->portefeuille, $periode['stop'],$startjaar,$this->pdf->rapportageValuta,$periode['start']);
			$storingen=$this->getGewogenStortingenOnttrekkingen($periode['start'], $periode['stop']);
			foreach($fondswaardenStart as $waarden)
			{

				$portefeuileWaarde['start']+=$waarden['actuelePortefeuilleWaardeEuro'];
				$this->verdelingFondsen[$periode['start']][$waarden['fonds']]['start']+=$waarden['actuelePortefeuilleWaardeEuro'];
			}
/*
			foreach($fondswaardenStop as $waarden)
			{
				$portefeuileWaarde['stop']+=$waarden['actuelePortefeuilleWaardeEuro'];
				$this->verdelingFondsen[$periode['stop']][$waarden['fonds']]['stop']=$waarden['actuelePortefeuilleWaardeEuro'];
			}

			$portefeuileWaarde['gemiddelde2']=($portefeuileWaarde['start']+$portefeuileWaarde['stop'])/2;
*/
			$portefeuileWaarde['gemiddelde']=$portefeuileWaarde['start']+$storingen['gewogen'];
	//		echo $periode['start']."->".$periode['stop']." | ".$portefeuileWaarde['gemiddelde']."=(".$portefeuileWaarde['start']."+".$storingen['gewogen'].") aandeel:(".($dagenPeriode/$dagenTotaal).")<br>\n";
			$portefeuileWaarde['aandeel']=$dagenPeriode/$dagenTotaal;
			$this->verdelingTotaal['perioden'][$periode['stop']]=$portefeuileWaarde;
			$this->verdelingTotaal['totaal']['gemiddelde']+=$portefeuileWaarde['aandeel']*$portefeuileWaarde['gemiddelde'];
		}
//echo $this->verdelingTotaal['totaal']['gemiddelde'];exit;


		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,Rekeningmutaties.Fonds) as fondsVolgorde,
Fondsen.OptieBovenliggendFonds,
BeleggingssectorPerFonds.Regio,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta,
Fondsen.VKM
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
LEFT JOIN KeuzePerVermogensbeheerder as BeleggingscategorienVolgorde ON BeleggingscategoriePerFonds.Beleggingscategorie = BeleggingscategorienVolgorde.waarde AND BeleggingscategorienVolgorde.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategorienVolgorde.categorie='Beleggingscategorien'
LEFT JOIN KeuzePerVermogensbeheerder as HoofdcategorienVolgorde ON HoofdBeleggingscategorien.Beleggingscategorie = HoofdcategorienVolgorde.waarde AND HoofdcategorienVolgorde.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND HoofdcategorienVolgorde.categorie='Beleggingscategorien'
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->queryVanaf."' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> '' AND Fondsen.VKM=1
GROUP BY Rekeningmutaties.Fonds 
ORDER BY HoofdcategorienVolgorde.Afdrukvolgorde, HoofdBeleggingscategorien.Afdrukvolgorde,BeleggingscategorienVolgorde.Afdrukvolgorde, BeleggingscategorienVolgorde.Afdrukvolgorde, Beleggingscategorien.Afdrukvolgorde,fondsVolgorde,OptieBovenliggendFonds,FondsOmschrijving ";

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
	
$this->totalen['gemiddeldeWaarde']=0;
		$totaalBijdrageVKM=0;
		$totaalDoorlopendekosten=0;
    $totaalDoorlopendekostenGesplitst=array();
$perfTotaal=$this->fondsPerformance($alleData,true);

$this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];



	foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
	  $perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($hoofdcategorieData);


	foreach ($perCategorie as $hoofdCategorie=>$regioData)
	    foreach ($regioData as $categorie=>$categorieData)
	       $perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->fondsPerformance($categorieData); //[$regio]

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
$totaalSom['gemWaarde'] += $data['gemWaarde'];
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
foreach ($perCategorie as $hoofdcategorie=>$categorieData)
{
	if($this->pdfVullen==true)
	   $this->printKop($perHoofdcategorie[$hoofdcategorie]['omschrijving'],'BI',true);

    foreach ($categorieData as $categorie=>$fondsData)
    {

			if($this->pdfVullen==true)
			{
				if ($categorie != $lastCategorie)
				{
					$this->printKop($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], '');
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
      $somVelden=array('beginwaarde','eindwaarde','stort','resultaat','gemWaarde','weging','bijdrage');
      foreach ($fondsData['fondsen'] as $id=>$fonds)
      {

        $lastLn=false;
        $tmp=array();
        $tmp['fondsen']=array($fonds);
        $tmp['categorie']=$categorie;
        $data=$this->fondsPerformance($tmp);

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
																$this->formatGetal($sub['beginwaarde'], 0),
																$this->formatGetal($sub['eindwaarde'], 0),
																$this->formatGetal($sub['stort'], 0),
																$this->formatGetal($sub['resultaat'], 0),
																$this->formatGetal($sub['gemWaarde'], 0),
																$this->formatGetal($sub['kosten'], 0),
																$this->formatGetal($kostenPercentage['percentage'], 2),
																$this->formatGetal($sub['gemWaarde'] * $kostenPercentage['percentage'] / 100, 0),
																$this->formatGetal($sub['weging'] * 100, 2, true),
																$this->formatGetal($bijdrageVKM, 2, true)));


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
					$TotCostFundTxt=$this->formatGetal($kostenPercentage['TotCostFund'], 2,false,true);
					$FundTransCostTxt=$this->formatGetal($kostenPercentage['FundTransCost'], 2,false,true);
					$FundPerfFeeTxt=$this->formatGetal($kostenPercentage['FundPerfFee'], 2,false,true);
					$dlkostenAbsoluutTxt=$this->formatGetal($dlkostenAbsoluut, 0,false,true);
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
														$this->formatGetal($data['beginwaarde'], 0),
														$this->formatGetal($data['eindwaarde'], 0),
														$this->formatGetal($data['stort'], 0),
														$this->formatGetal($data['resultaat'], 0),
														$this->formatGetal($data['gemWaarde'], 0),
														$TotCostFundTxt,
														$FundTransCostTxt,
														$FundPerfFeeTxt,
														$dlkostenAbsoluutTxt,
														$this->formatGetal($sub['weging'] * 100, 2, true),
														$this->formatGetal($bijdrageVKM, 2, true)
													));
					$this->pdf->excelData[]=array($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $fondsData['fondsOmschrijving'][$id],
						round($data['beginwaarde'], 0),
						round($data['eindwaarde'], 0),
						round($data['stort'], 0),
						round($data['resultaat'], 0),
						round($data['gemWaarde'], 0),
						round($kostenPercentage['TotCostFund'], 2),
						round($kostenPercentage['FundTransCost'], 2),
						round($kostenPercentage['FundPerfFee'], 2),
						round($dlkostenAbsoluut, 0),
						round($sub['weging'] * 100, 2),
						round($bijdrageVKM, 2));
				}
				$totaalBijdrageVKM+=$bijdrageVKM;
				$totaalDoorlopendekosten+=$sub['gemWaarde']*$totaalKostenPercentage/100;
        $totaalDoorlopendekostenGesplitst['TotCostFund']+=$sub['gemWaarde']*$kostenPercentage['TotCostFund']/100;
        $totaalDoorlopendekostenGesplitst['FundTransCost']+=$sub['gemWaarde']*$kostenPercentage['FundTransCost']/100;
        $totaalDoorlopendekostenGesplitst['FundPerfFee']+=$sub['gemWaarde']*$kostenPercentage['FundPerfFee']/100;

				$perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
				$perHoofdcategorie[$hoofdcategorie]['perf']['transkosten'] +=$data['kosten'];
				$perHoofdcategorie[$hoofdcategorie]['perf']['dlkostenAbsoluut'] +=$dlkostenAbsoluut;
	      $perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
				//$perCategorie[$hoofdcategorie][$categorie]['perf']['transkosten'] +=$data['kosten'];
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
																$this->formatGetal($sub['beginwaarde'], $this->pdf->rapport_VOLK_decimaal),
																$this->formatGetal($sub['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
																$this->formatGetal($sub['stort'], 0),
																$this->formatGetal($sub['resultaat'], 0),
																$this->formatGetal($sub['gemWaarde'], 0),
																$this->formatGetal($sub['transkosten'], 0),
																$this->formatGetal($sub['dlkostenPercentage'], 0),
																$this->formatGetal($sub['dlkostenAbsoluut'], 0, true),
																$this->formatGetal($sub['weging'] * 100, 2, true)
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
        $data=$this->fondsPerformance($tmp);
        $rekeningData[$id]=array('perf'=>$data,'rekening'=>$rekening);
        $rekeningWaarde[$id]=$data['eindwaarde'];
        $totaalRekeningen+=$data['eindwaarde'];
      }
      arsort($rekeningWaarde);


				$query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten=1";
				$DB->SQL($query);
				$DB->Query();
				$n=0;
				$grootboekKleuren=array();
				while($data=$DB->nextRecord())
				{
					if($data['Omschrijving']=='Beheervergoeding')
						$data['Omschrijving']='Adviesvergoeding';
					
					$mogelijkeKleuren[$n];
					if($gewensteKleuren[$data['Grootboekrekening']]['R']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['G']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['B']['value'])
					 	$grootboekKleuren[$data['Omschrijving']]=array($gewensteKleuren[$data['Grootboekrekening']]['R']['value'],$gewensteKleuren[$data['Grootboekrekening']]['G']['value'],$gewensteKleuren[$data['Grootboekrekening']]['B']['value']);
					else
						$grootboekKleuren[$data['Omschrijving']]=$mogelijkeKleuren[$n];
					$n++;
				}

			 $key='Doorlopende kosten';
		  	if($gewensteKleuren[$key]['R']['value'] || $gewensteKleuren[$key]['G']['value'] || $gewensteKleuren[$key]['B']['value'])
					$grootboekKleuren[$key]=array($gewensteKleuren[$key]['R']['value'],$gewensteKleuren[$key]['G']['value'],$gewensteKleuren[$key]['B']['value']);
	      else
			  	$grootboekKleuren['Doorlopende kosten']=$mogelijkeKleuren[$n];


				$this->grootboekKleuren=$grootboekKleuren;
			if($this->pdfVullen==true)
			{
				if ($lastRegio <> '')
				{
					$subregio = $perRegio[$hoofdcategorie][$categorie][$lastRegio]['perf'];
					$this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS');
					$this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
					$this->pdf->row(array('', '  subtotaal ' . $perRegio[$hoofdcategorie][$categorie][$lastRegio]['omschrijving'],
														$this->formatGetal($subregio['beginwaarde'], $this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subregio['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subregio['stort'], 0),
														$this->formatGetal($subregio['resultaat'], 0),
														$this->formatGetal($subregio['gemWaarde'], 0),
														$this->formatGetal($subregio['resultaat'] / $subregio['gemWaarde'] * 100, 2),
														$this->formatGetal($subregio['weging'] * 100, 2, true),
														$this->formatGetal($subregio['bijdrage'] * 100, 2, true)));
					$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
					$this->pdf->Ln();
					unset($this->pdf->CellBorders);
					$lastRegio = '';
				}

				$this->pdf->widths = $widthsBackup;
				$this->printSubTotaal($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $perCategorie[$hoofdcategorie][$categorie]);
			}
    }
	if($this->pdfVullen==true)
    $this->printSubTotaal($perHoofdcategorie[$hoofdcategorie]['omschrijving'],$perHoofdcategorie[$hoofdcategorie],'BI');
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
			round($perfTotaal['gemWaarde'], 0),
			round($perfTotaal['kosten'], 0),
			round($perfTotaal['percentage'], 2),
			round($perfTotaal['dlkostenAbsoluut'], 0),
			round($perfTotaal['weging'] * 100, 2),
			round($perfTotaal['bijdrageVKM'], 2));

		if ($this->pdfVullen == true)
		{
			$this->printSubTotaal('Totaal', array('perf' => $perfTotaal), 'BI');
			$y = $this->pdf->getY() + 10 + 18 * $this->pdf->rowHeight;
			if ($y > $this->pdf->PageBreakTrigger)
			{
				$this->pdf->vmkHeaderOnderdrukken = true;
				if($this->skipSummary==false)
				  $this->pdf->addPage();
					//$y=$this->pdf->getY()+10;
			}
		}
    //echo $this->pdf->page; ob_flush();
		if($this->skipDetail==true)
		{
			$this->pdfVullen = true;
			$this->pdf->vmkHeaderOnderdrukken = true;
			$this->pdf->addPage();
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
			//echo '|'.$this->pdf->page.'|'; ob_flush();
		}


		if($this->skipSummary==false)
		{
			$this->kostenKader($totaalDoorlopendekosten, $perfTotaal,$totaalDoorlopendekostenGesplitst);

			if ($this->pdfVullen == true)
			{

				unset($this->pdf->CellFontColor);

				if($this->skipLangeTermijn==false)
					$this->langeTermijngrafiek();
			}
		}
    if (isset($this->pdf->vmkHeaderOnderdrukken))
    {
      unset($this->pdf->vmkHeaderOnderdrukken);
    }
	}


	function langeTermijngrafiek()
	{
		global $__appvar;
		$db=new DB();
    $query="SHOW COLUMNS FROM CRM_naw like 'VKMAlternatief'";
    if($db->Qrecords($query)>0)
    {
      $query="SELECT CRM_naw.doeldatum, CRM_naw.VKMAlternatief FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
    }
    else
    {
      $query="SELECT CRM_naw.doeldatum FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
    }

		$db->SQL($query);
		$db->query();
		$data=$db->nextRecord();
		$VKMAlternatief=$data['VKMAlternatief'];
		if($data['doeldatum'] > 1900)
			$doelJaar=$data['doeldatum'];
		else
			$doelJaar=$this->pdf->rapport_jaar+10;

		$query="SELECT Risicoklassen.verwachtRendement FROM Portefeuilles 
 JOIN Risicoklassen ON Portefeuilles.Risicoklasse=Risicoklassen.Risicoklasse AND Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder
 WHERE Portefeuilles.portefeuille='".$this->portefeuille."'";
		$db->SQL($query);
		$db->query();
		$data=$db->nextRecord();
    if($data['verwachtRendement'] <> 0 )
    {
      $rendement = $data['verwachtRendement'];
    }
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
    
    $grafiekWaardenVorigjaar=array();
		if($this->melding<>'')
    {
      $beginwaardeVorigJaar=getStortingen($this->portefeuille,$this->vanafDatum ,$this->rapportageDatum);
      $grafiekWaardenVorigjaar['datum'][]=substr($this->vanafDatum,0,7);
    }
    else
    {
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
		WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND TijdelijkeRapportage.rapportageDatum= '" . $this->vanafDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $db->SQL($query);
      $db->Query();
      $start = $db->NextRecord();
      $beginwaardeVorigJaar = $start['actuelePortefeuilleWaardeEuro'];
      $grafiekWaardenVorigjaar['datum'][]=substr($this->vanafDatum,0,4);
    }

   // $grafiekWaardenVorigjaar['waardeNaKosten'][]=$beginwaardeVorigJaar;
   // $grafiekWaardenVorigjaar['waardeNaKosten'][]=$beginwaarde;
    $kosten=$this->vkmWaarde['totaalDoorlopendekosten']+$this->vkmWaarde['totaalDirecteKosten'];
   // $grafiekWaardenVorigjaar['cumulatieveKosten'][]=0;
   // $grafiekWaardenVorigjaar['cumulatieveKosten'][]=$kosten;
   // $grafiekWaardenVorigjaar['waardeZonderKosten'][]=$beginwaardeVorigJaar;
   // $grafiekWaardenVorigjaar['waardeZonderKosten'][]=$beginwaarde+$kosten;
    
    //$grafiekWaardenVorigjaar['data'][0]['waardeZonderKosten']=$beginwaardeVorigJaar;
   // $grafiekWaardenVorigjaar['data'][0]['waardeNaKosten']=$beginwaardeVorigJaar;
   // $grafiekWaardenVorigjaar['data'][0]['cumulatieveKosten']=0;
   // $grafiekWaardenVorigjaar['datum'][0]=substr($this->vanafDatum,0,4);
    $grafiekWaardenVorigjaar['data'][0]=array();
    $grafiekWaardenVorigjaar['datum'][0]='';
    $grafiekWaardenVorigjaar['data'][1]['waardeZonderKosten']=$beginwaarde+$kosten;
    $grafiekWaardenVorigjaar['data'][1]['waardeNaKosten']=$beginwaarde;
    $grafiekWaardenVorigjaar['data'][1]['cumulatieveKosten']=$kosten;
    $grafiekWaardenVorigjaar['datum'][1]=vertaalTekst('12 Maanden',$this->pdf->rapport_taal);//substr($this->rapportageDatum,0,4);
    $grafiekWaardenVorigjaar['data'][2]=array();
    $grafiekWaardenVorigjaar['datum'][2]='';
    //$grafiekWaardenVorigjaar['datum'][]=substr($this->rapportageDatum,0,4);
    $grafiekWaardenVorigjaar['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Cumulatieve kosten');
    $grafiekWaardenVorigjaar['titel']=vertaalTekst("Impact van kosten op het rendement van de afgelopen 12 maanden",$this->pdf->rapport_taal);
    
		$this->pdf->excelData[]=array();
		$this->pdf->excelData[]=array('Termijngrafiek');
		$this->pdf->excelData[]=array('doelJaar','rendement','beginwaarde','vkm');
		$this->pdf->excelData[]=array($doelJaar,round($rendement,2),round($beginwaarde,2),round($this->vkmWaarde['vkmWaarde'],4));
		$this->pdf->excelData[]=array('jaar','waardeNaKosten','cumulatieveKosten','waardeZonderKosten');

		$kosten=0;
		$grafiekWaarden=array();
		if($VKMAlternatief <> 0)
    {
      $vkmwaarde = $VKMAlternatief;
    }
    else
    {
      $vkmwaarde=$this->vkmWaarde['vkmWaarde'];
    }
    $n=0;
    for($i=$this->pdf->rapport_jaar; $i<=$doelJaar; $i++)
		{
			$jaren=$i-$this->pdf->rapport_jaar;
			$nieuweWaarde=$beginwaarde*pow(1+(($rendement-$vkmwaarde)/100),$jaren);
			$kosten+=$nieuweWaarde*($vkmwaarde/100);
//			$grafiekWaarden['waardeNaKosten'][]=$nieuweWaarde-$kosten;
//			$grafiekWaarden['cumulatieveKosten'][]=$kosten;
//			$grafiekWaarden['waardeZonderKosten'][]=$nieuweWaarde;
//			$grafiekWaarden['datum'][]=$i;
      
      $grafiekWaarden['data'][$n]['waardeZonderKosten']=$nieuweWaarde+$kosten;
      $grafiekWaarden['data'][$n]['waardeNaKosten']=$nieuweWaarde;
      $grafiekWaarden['data'][$n]['cumulatieveKosten']=$kosten;
      $grafiekWaarden['datum'][$n]=$i;
      
			$this->pdf->excelData[]=array($i,round($nieuweWaarde,2),round($kosten,2),round($nieuweWaarde+$kosten,2));
			$n++;
		}
		$grafiekWaarden['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Cumulatieve kosten');

		$grafiekWaarden['titel']=vertaalTekst("Impact van kosten op het lange termijn rendement van de portefeuille",$this->pdf->rapport_taal);
		
	
		$waardeZonderKostenKleur=array(151,185,199);//array( 0,112,192 );
		$waardeNaKostenKleur=array(154,178,148);//array(12,173,71);
		$cumulatieveKostenKleur=array(241,89,38);//array(250,6,87);

		//if($this->pdf->getY()+70>$this->pdf->pagebreak)
		//	$this->pdf->addPage();
    
    $this->pdf->setXY(195,72);
  //  $this->LineDiagram(90, 40, $grafiekWaardenVorigjaar,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,true);//50
    $this->BarDiagram(90, 40, $grafiekWaardenVorigjaar,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50

    
    $this->pdf->setXY(195,135);
		//$this->LineDiagram(90, 40, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50
    $this->BarDiagram(90, 40, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50


		//listarray($grafiekWaarden);
//		echo "$doelJaar $rendement $beginwaarde";
	//	listarray( $this->vkmWaarde);
	//	exit;
	}
  
  function BarDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafBegin=false)
  {
    global $__appvar;
    
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    //	$data1 = $data['waardeNaKosten'];
    //$data2 = $data['cumulatieveKosten'];
    $data = $data['data'];
    
    
    
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY()+2;
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 )-5;
    
    //	$this->pdf->setY($Ypage-3);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize-1);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
    
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));
    /*
        if(is_array($color[0]))
        {
          $color1= $color[1];
          $color2= $color[2];
          $color = $color[0];
        }
    */
    //	if($color == null)
    //		$color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
    
    
    //	$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      foreach($data as $dataArray)
        foreach($dataArray as $value)
          $maxVal = ceil(max($value,$maxVal));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($data));
    }
    
    
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
    
    $stapgrootte = (abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; round($i)<=round($bodem); $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->setXY($XDiag-7, $i);
      $this->pdf->Cell(7 , 4 , "€ ". 0-round($n*$stapgrootte/100)*100 , 0, 1, "R");
      
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; round($i) >= round($top); $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->setXY($XDiag-7, $i);
        $this->pdf->Cell(7 , 4 , "€ " .(round($n * $stapgrootte/100) *100) , 0, 1, "R");
        
      }
      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    //	$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    
    foreach($color as $i=>$col)
      $colorShade[$i]=array(intval($col[0]*0.7),intval($col[1]*0.7),intval($col[2]*0.7));
    foreach($data as $i=>$barData)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit+$unit/2,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $n=0;
      foreach($barData as $bar=>$value)
      {
        $lineStyle = array('width' => 0.01, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color[$n]);
        
        $yval2 =  ($value) * $waardeCorrectie *-1;
        
        if ($i>-1 || $vanafBegin==true)
        {
          $this->pdf->SetLineStyle($lineStyle);
          //$this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
          $xstart=$XDiag+($i)*$unit+($n+1)/4*$unit;
          $barWidth=$unit/4;
          $this->pdf->rect($xstart, $yval, $barWidth, $yval2,'F','', $color[$n] );
          $pol=array($xstart,$yval+$yval2,  $xstart+2,$yval+$yval2-1, $xstart+$barWidth+2,$yval+$yval2-1,  $xstart+$barWidth+2,$yval-1,  $xstart+$barWidth,$yval,  $xstart+$barWidth,$yval+$yval2);
          $this->pdf->Polygon($pol, 'F', null, $colorShade[$n] ) ;
        }
        
        //	$yval = $yval2;
        
        
        $n++;
      }
      
    }
  
  
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1,'cap' => 'butt'));
    $step=0;
    $aantal=count($legendaItems);
    $yOffset=10;
    foreach ($legendaItems as $index=>$item)
    {
      $kleur=$color[$index];
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step , $YPage+$h+$yOffset+1, 3, 0.7, 'F','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+$yOffset);
      $this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));
      $step+=50;
      if($index==1)
      {
        $step=0;
        $yOffset+=4;
      }
    
    
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }





		function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
		{
		  $DB=new DB();
		  $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
      $DB->SQL($query); //echo "$fonds $query  <br>\n";
      $DB->Query();
      $totaalWaarde = $DB->NextRecord();

		  return $totaalWaarde['totaalWaarde'];
		}


	function fondsPerformance($fondsData,$totaal=false)
  {
    $datumBegin=$this->vanafDatum;
    $weegDatum=$datumBegin;
    $datumEind=$this->rapportageDatum;

    global $__appvar;
	  $DB=new DB();
    $totaalPerf = 100;

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

      if ($this->pdfVullen==true && $this->pdf->rapportageValuta <> 'EUR')
      {
	      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	      $startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
	      $eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
      }
	    else
	    {
	      $koersQuery = "";
	      $startValutaKoers= 1;
	      $eindValutaKoers= 1;
	    }



      $fondsenWhere = " Fondsen.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";

    
      

      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
               SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as liqWaarde,
               SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
	     $DB->SQL($query);
	     $DB->Query();
	     $start = $DB->NextRecord();
	     $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];

	     $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2/$eindValutaKoers  as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
	     $DB->SQL($query);
	     $DB->Query();
	     $eind = $DB->NextRecord();
	     $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
	     $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];


      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM((if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
       SUM((if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0))) as opbrengstTotaal ,
       SUM((if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0))) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
       $DB->SQL($queryFondsDirecteKostenOpbrengsten);
       $DB->Query();
       $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


	     $queryAttributieStortingenOntrekkingen = "SELECT ".
	              "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
	              "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
	              "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
	              "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	              "WHERE ".
	              "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	              "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
	              "Rekeningmutaties.Verwerkt = '1' AND ".
	              "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	              "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	              " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
	     $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
	     $DB->Query();
	     $AttributieStortingenOntrekkingen = $DB->NextRecord();

	 //   $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];


      $queryKostenOpbrengsten = "SELECT
          SUM((if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
          SUM((if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
	     $DB->SQL($queryKostenOpbrengsten);
	     $DB->Query();
	     $nietToegerekendeKosten = $DB->NextRecord();
	     $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];



     // $indexData=$this->indexPerformance($fondsData['categorie'],$weegDatum,$datumEind);
		$gemiddelde=0;
		foreach($this->perioden as $periode)
		{
			$aandeelPeriode=$this->verdelingTotaal['perioden'][$periode['stop']]['aandeel'];

			$stortingen=$this->getGewogenStortingenOnttrekkingenFondsen($periode['start'],$periode['stop'],$rekeningFondsenWhere,$koersQuery);
			$startwaarde=0;
			foreach($fondsData['fondsen'] as $fonds)
			{
				$startwaarde += $this->verdelingFondsen[$periode['start']][$fonds]['start'];
			}
			$gemiddeldeMaand=$startwaarde+$stortingen['gewogen'];

			//if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
			//  echo $fondsData['fondsen'][0]." ".$periode['stop']." $aandeelPeriode*($startwaarde+".$stortingen['gewogen'].")=".($aandeelPeriode*$gemiddeldeMaand)."<br>\n";

			$gemiddelde+=$aandeelPeriode*$gemiddeldeMaand;
		}
		//if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
		// echo "<br>\n$gemiddelde";
		if($totaal==false)
			$weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'];
    else
      $weging=$gemiddelde/$this->verdelingTotaal['totaal']['gemiddelde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;



  return array(
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,

  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$gemiddelde,

  'weging'=>$weging,
  'bijdrage'=>$bijdrage);
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
  
  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
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
      
      if($this->pdf->debug==true)
      {
        echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
        echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
        echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
        echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
        echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
        echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
      }
      $totalPerf+=($perf*$percentage/100);
    }  
    //echo "t $fonds $totalPerf $van,$tot<br>\n";

    return $totalPerf;
  }

	function VBarVerdeling($w, $h, $data)
	{
		global $__appvar;
		$grafiekPunt = array();

			$minVal=0;


		  $n=0;
		$grafiek=array();
		$colors=array();

		$aantal=count($data);
		$kleurStap=floor((255-75)/$aantal);
			foreach ($data as $categorie=>$waarde)
			{
				$grafiek[$categorie]=$waarde;
				$categorien[$categorie] = $n;
				$categorieId[$n]=$categorie ;


				if(!isset($colors[$categorie]))
				{
					if(is_array($this->grootboekKleuren[$categorie]))
						$colors[$categorie] = $this->grootboekKleuren[$categorie];
					else
					{
						$random = 75 + $kleurStap * $n;
						$colors[$categorie] = array($random, $random, $random);//,rand(0,255),rand(0,255)
					}
				}
				$n++;
			}

		$numBars=1;
		if($color == null)
		{
			$color=array(155,155,155);
		}


		$maxVal=ceil(array_sum($data)*2)/2;
		if($maxVal <= 0)
			$maxVal=0;

		if($minVal >= 0)
			$minVal = 0;


		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();

		$YstartGrafiek = $YPage;
		$hGrafiek = $h;
		$XstartGrafiek = $XPage;
		$bGrafiek = $w; // - legenda

		$unit = $hGrafiek / $maxVal * -1;
		$nulYpos =0;


		$horDiv = 5;
		$bereik = $hGrafiek/$unit;
		$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
		$this->pdf->SetTextColor(0,0,0);
		$stapgrootte = round(abs($bereik)/$horDiv*10)/10;
		$top = $YstartGrafiek-$h;
		$absUnit =abs($unit);

		$nulpunt = $YstartGrafiek + $nulYpos;


		$n=0;
		for($i=$nulpunt; $i >= $top-0.1; $i-= $absUnit*$stapgrootte)
		{
			//echo $n*$stapgrootte." => $i >= $top  ->$maxVal ".$absUnit*$stapgrootte."<br>\n";
			$this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
			if($skipNull == true)
				$skipNull = false;
			else
			{
				$this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
				$this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,1,false,true)." %",0,0,'R');
			}
			$n++;
			if($n >20)
				break;
		}


		if($numBars > 0)
			$this->pdf->NbVal=$numBars;

		$vBar = ($bGrafiek / 2);
		$eBaton = ($vBar / 2);


		$this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
		$this->pdf->SetLineWidth(0.2);

		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);


		foreach($grafiek as $categorie=>$val)
		{
				if(!isset($YstartGrafiekLast))
					$YstartGrafiekLast = $YstartGrafiek;
				//Bar
				$xval = $XstartGrafiek +  $vBar - $eBaton/2 ;
				$lval = $eBaton;
				$yval = $YstartGrafiekLast+ $nulYpos ;
				$hval = ($val * $unit);

				$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
				$YstartGrafiekLast = $YstartGrafiekLast+$hval;
				$this->pdf->SetTextColor(255,255,255);
				if(abs($hval) > 3)
				{
					$this->pdf->SetXY($xval, $yval+($hval/2)-2);
					$this->pdf->Cell($eBaton, 4, number_format($val,2,',','.')."%",0,0,'C');
				}
				$this->pdf->SetTextColor(0,0,0);

	}


		$xval = $XstartGrafiek +  $bGrafiek +5;
		$yval = $YstartGrafiek  ;
		foreach($grafiek as $categorie=>$val)
		{
			$yval-=10;
			$this->pdf->Rect($xval, $yval, 2, 2, 'DF',null,$colors[$categorie]);
			$this->pdf->SetXY($xval+4, $yval-1);
			$this->pdf->Cell(50, 4, $categorie." ".number_format($val,2,',','.')."%",0,0,'L');

		}

		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
	}
  
}
