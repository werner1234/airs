<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/03 15:41:08 $
File Versie					: $Revision: 1.1 $

$Log: RapportVKMA_L25.php,v $
Revision 1.1  2020/06/03 15:41:08  rvv
*** empty log message ***

Revision 1.8  2019/02/06 07:34:23  rvv
*** empty log message ***

Revision 1.7  2019/02/03 17:52:13  rvv
*** empty log message ***

Revision 1.6  2019/02/03 15:50:59  rvv
*** empty log message ***

Revision 1.5  2019/02/03 13:42:47  rvv
*** empty log message ***

Revision 1.4  2019/01/31 09:47:24  rvv
*** empty log message ***

Revision 1.4  2019/01/26 19:33:28  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVKMA_L25
{
	function RapportVKMA_L25($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		if(is_object($pdf))
		{
			$this->pdf = &$pdf;
			$this->pdf->rapport_type = "VKMA";
			$this->pdf->rapport_datum = db2jul($rapportageDatum);
			$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
			$this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
			$this->pdf->underlinePercentage=0.8;
			$this->pdf->rapport_titel = vertaalTekst("vergelijkende kostenmaatstaf ex-ante",$this->pdf->rapport_taal);
			$this->pdfVullen=true;
			$this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
		}
		else
			$this->pdfVullen=false;
    
    
    $this->layout='P';
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

	function formatGetalKoers($waarde, $dec , $start = false)
	{
		if ($start == false)
			$waarde = $waarde / $this->pdf->ValutaKoersEind;
		else
			$waarde = $waarde / $this->pdf->ValutaKoersStart;

		return number_format($waarde,$dec,",",".");
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
	       if ($decimaal != '0' && !isset($newDec))
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
	  if($lastCategorieOmschrijving != 'Totaal')
	  {
	    $prefix='Subtotaal';
	    $this->pdf->CellBorders = array('','','TS','','','','TS','TS','TS');
	  }
	  else
	  {
	    $prefix='';
	    $this->pdf->CellBorders = array('','',array('TS','UU'),'','','',array('TS','UU'),array('TS','UU'),array('TS','UU'));
	  }

    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

    $this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
    $this->pdf->setX($this->pdf->marge);

    $data=$allData['perf'];


   	$this->pdf->row(array('','',
												$this->formatGetal($data['eindwaarde']*$this->scaleFactor,0),
											'','',
											$this->formatGetal($data['dlkostenPercentage'],0),
											$this->formatGetal($data['dlkostenAbsoluut']*$this->scaleFactor,0),
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
    
    $fontsize=$this->pdf->rapport_fontsize;//-2;
    
    $query="SELECT
Portefeuilles.Portefeuille,
Portefeuilles.Risicoprofiel,
Portefeuilles.Risicoklasse,
Risicoklassen.verwachtRendement,
Risicoklassen.verwachtBrutoRendement,
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
      $jaren=$data['doeldatum']-$this->pdf->rapport_jaar;
    else
      $jaren=10;
    
    if($this->pdf->lastPOST['vkma_eindjaar']>$this->pdf->rapport_jaar)
      $jaren=$this->pdf->lastPOST['vkma_eindjaar']-$this->pdf->rapport_jaar;
    
    $handmatigeKosten=array('BEH'=>'vkma_kosten_beheer','BEW'=>'vkma_kosten_service','KOST'=>'vkma_kosten_transactie','KNBA'=>'vkma_kosten_bank');
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
    
    $handmatigeOmschrijving=array('BEH'=>'Beheerkosten','BEW'=>'Servicekosten depotbank (administratie, bewaring, etc)','KOST'=>'Transactiekosten (op basis van verwacht aantal en omvang transacties)','KNBA'=>'Overige kosten (spread, overig)');
    
    
    while($data = $DB->nextRecord())
    {
      if($data['Grootboekrekening']=='KOBU')
        $data['Grootboekrekening']='KOST';
      
      if(isset($handmatigeOmschrijving[$data['Grootboekrekening']]))
        $data['Omschrijving']=$handmatigeOmschrijving[$data['Grootboekrekening']];
      
      $grootboekKostenData[$data['Grootboekrekening']]['totaal']+=$data['totaal'];
      $grootboekKostenData[$data['Grootboekrekening']]['Grootboekrekening']=$data['Grootboekrekening'];
      $grootboekKostenData[$data['Grootboekrekening']]['Omschrijving']=$data['Omschrijving'];
    }
   
		$gemiddelde=$this->verdelingTotaal['totaal']['gemiddelde'];
    $eindWaarde=$this->verdelingTotaal['perioden'][$this->rapportageDatum];
    $doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['eindwaarde'];
   // echo $this->verdelingTotaal['totaal']['gemiddelde'];exit;
//listarray($grootboekKostenData);exit;
    

    
    
//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['gemWaarde']."<br>\n";exit;
    
    $grootboekKostenDataNew=array();//$grootboekKostenData;
    $gebruikHandmatigeGegevens=false;
    $kostenRow=array();
    $totaal=0;
    foreach($handmatigeKosten as $grootboek=>$kosten)
    {
      $kostenbedragKey=str_replace('vkma_kosten','vkma_bedrag',$kosten);
      if((isset($this->pdf->lastPOST[$kosten]) && $this->pdf->lastPOST[$kosten] <> '') || isset($this->pdf->lastPOST[$kostenbedragKey]) && $this->pdf->lastPOST[$kostenbedragKey] <> '')
        $gebruikHandmatigeGegevens=true;
      $grootboekKostenDataNew[$grootboek]['totaal']=floatval($this->pdf->lastPOST[$kosten]/100*$eindWaarde)+floatval($this->pdf->lastPOST[$kostenbedragKey]);
      $grootboekKostenDataNew[$grootboek]['vasteKosten']=floatval($this->pdf->lastPOST[$kostenbedragKey]);
      $grootboekKostenDataNew[$grootboek]['Grootboekrekening']=$grootboek;
      $grootboekKostenDataNew[$grootboek]['Omschrijving']=$handmatigeOmschrijving[$grootboek];
    }
    if($gebruikHandmatigeGegevens==true)
      $grootboekKostenData=$grootboekKostenDataNew;

    if($spreadKostenEUR <> 0)
    {
      $kostenProcent = $spreadKostenEUR / $gemiddelde * 100;
      $spreadKostenEUR = $eindWaarde*$kostenProcent/100;
      $grootboekKostenData['KNBA']['Omschrijving']=$handmatigeOmschrijving['KNBA'];
      $grootboekKostenData['KNBA']['totaal']+=($spreadKostenEUR*$this->scaleFactor);
      if($this->pdfVullen==true)
      {
        // $kostenRow[] = array(vertaalTekst('- Overige kosten (spread, overig)', $this->pdf->rapport_taal), '€', $this->formatGetal($spreadKostenEUR * $this->scaleFactor, 0), $this->formatGetal($kostenProcent, 2) . ' %');
      }
      //$totaal += $spreadKostenEUR;
      $barData['Spread-kosten'] = $kostenProcent;
      //	$this->pdf->excelData[]=array('','Spread-kosten','',round($spreadKostenEUR*$this->scaleFactor,0),round($kostenProcent,2) );
      
      
    }
    
  //listarray($grootboekKostenDataNew);
//listarray($this->pdf->lastPOST);
		$percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
		$herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
		$aandeelIndirect=$perfTotaal['eindwaarde']/$eindWaarde;
		//echo "	$aandeelIndirect=".$perfTotaal['eindwaarde']."/$eindWaarde; <br>\n";exit;
		
		$vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
    $barData=array();
    $barData['Doorlopende kosten']=$vkmPercentagePortefeuille;

	
    $grootBoekKostenTotaal=0;
    $grootboekKosten=array();

		foreach($grootboekKostenData as $data)
    {
      if(count($data)==0)
        continue;
//      $kostenProcent=$data['totaal']/$eindWaarde*100;
//      $kostenHerrekend=$eindWaarde*$kostenProcent/100;
  
  
      $kostenProcent=($data['totaal']-$data['vasteKosten'])/$eindWaarde*100;
      $kostenHerrekend=(($eindWaarde*$kostenProcent/100)*$this->scaleFactor)+$data['vasteKosten'];
      $kostenProcent=$kostenHerrekend/($eindWaarde*$this->scaleFactor)*100;
      $data['totaal']=$kostenHerrekend/$this->scaleFactor;
      
      //listarray($data);echo "$kostenProcent $kostenHerrekend <br>\n";
      
		  if($data['Grootboekrekening']=='BEH')
      {
        $data['Omschrijving']='Beheervergoeding inclusief '.$this->formatGetal($btw['BeheerfeeBTW'],0).'% BTW';
        $this->grootboekKleuren[$data['Omschrijving']]=$this->grootboekKleuren['Beheervergoeding'];
        $btwFactor=1+$btw['BeheerfeeBTW']/100;
        $kostenHerrekendZonderBtw=$kostenHerrekend/$btwFactor;
        $btwBedrag=$kostenHerrekend-$kostenHerrekendZonderBtw;
     
        $kostenProcent=$kostenHerrekendZonderBtw/$kostenHerrekend*$data['totaal']/$eindWaarde*100;
        $kostenProcentBtw=$btwBedrag/$kostenHerrekend*$data['totaal']/$eindWaarde*100;
        $kostenHerrekend=$kostenHerrekendZonderBtw;
  
        if ($this->pdfVullen == true)
        {
          $kostenRow[] = array('- ' . 'Beheer- of adviesvergoeding', '€', $this->formatGetal($kostenHerrekend, 0), $this->formatGetal($kostenProcent, 2) . ' %');
          $barData[$data['Omschrijving']] = $kostenProcent;
  
          $kostenRow[] = array('- ' . 'BTW over beheer- of adviesvergoeding', '€', $this->formatGetal($btwBedrag, 0), $this->formatGetal($kostenProcentBtw, 2) . ' %');
          $barData[$data['Omschrijving']] = $kostenProcent;
          
        }
      }
      else
      {
        if ($this->pdfVullen == true)
        {
          $kostenRow[] = array('- ' . $data['Omschrijving'], '€', $this->formatGetal($kostenHerrekend , 0), $this->formatGetal($kostenProcent, 2) . ' %');
          //$this->pdf->excelData[]=array('',$data['Omschrijving'],'',round($kostenHerrekend*$this->scaleFactor,0),round($kostenProcent,2) );
          $barData[$data['Omschrijving']] = $kostenProcent;
        }
      }
			$grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
			$totaal+=$data['totaal'];
		}
    $grootBoekKostenTotaal=$totaal;




		$kostenPercentage=$totaal/$eindWaarde*100;
		$vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
		if($this->pdfVullen==true)
		{
	
		}
    
    
    if($this->pdfVullen==true)
    {
      $this->pdf->setX($this->pdf->marge);
      $this->pdf->setAligns(array('L'));
      $this->pdf->setWidths(array($this->pdf->w-$this->pdf->marge*2));
      $this->pdf->ln();
  
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array('Vergelijkende kostenmaatstaf ex-ante / indicatie te verwachten kosten beleggingsportefeuille'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->ln();
  
      $this->pdf->row(array('Wij willen u als relatie een helder overzicht geven van de totale directe en indirecte kosten waar u mee te maken krijgt. De verschillende kosten die worden onderscheiden zijn:'));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      //function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      $inspringen=$this->pdf->GetStringWidth('- Indirecte kosten:')+2;
      $this->pdf->setWidths(array($this->pdf->w-$this->pdf->marge*2-$inspringen));
      $this->pdf->Cell($inspringen,$this->pdf->rowHeight,'- Directe kosten:');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array('de kosten van onze dienstverlening, kosten van de depotbank voor het aanhouden van uw beleggingsrekening en indien van toepassing een inschatting van de transactiekosten.'));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->Cell($inspringen,$this->pdf->rowHeight,'- Indirecte kosten:');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array('de kosten van financiële instrumenten in uw beleggings portefeuille, met name de (doorlopende) kosten van de beleggingsfondsen in uw portefeuille (die door de fondsaanbieders worden verrekend in de koers van het fondsen).'));
      $this->pdf->setWidths(array($this->pdf->w-$this->pdf->marge*2));
      $this->pdf->row(array('
Het onderstaande overzicht is opgesteld op basis van te beleggen vermogen en het voorgestelde beleggingsprofiel. De opstelling betreft een zo goed mogelijke inschatting en is mede afhankelijk van het daadwerkelijk met u overeengekomen beleggingsprofiel, de omvang van uw beleggingsportefeuille en de wijzigende omstandigheden op de financiëlemarkten. Hierdoor kunnen de kosten naar boven of beneden afwijken.'));
  
      $this->pdf->ln(4);
    $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      $this->pdf->setAligns(array('L', 'R','R', 'R'));
      if($this->layout=='P')
        $this->pdf->setWidths(array(150, 40));
      else
        $this->pdf->setWidths(array(133, 30, 30));
      $startY=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array('Beleggingsprofiel',$risicoklasse['Risicoklasse']));
      $this->pdf->row(array('Te beleggen vermogen', $this->formatGetal($eindWaarde*$this->scaleFactor, 0)));
     // $rendementProcent  	= performanceMeting($this->portefeuille, $this->vanafDatum, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
     // $this->pdf->row(array('Werkelijk bruto jaarrendement over de afgelopen 12 maanden', $this->formatGetal($rendementProcent+$vkmWaarde, 2) . ' %' ));
      $this->pdf->row(array('Verwacht bruto rendement per jaar', $this->formatGetal(($risicoklasse['verwachtBrutoRendement']<>0?$risicoklasse['verwachtBrutoRendement']:$risicoklasse['verwachtRendement']), 2) . ' %' ));
     
      $this->pdf->ln();
      if($this->layout=='P')
        $this->pdf->setWidths(array(145,5,20,20));
      else
        $this->pdf->setWidths(array(130,5,15,15));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array('Te verwachten kosten op jaarbasis'));
      $this->pdf->ln();
      $this->pdf->row(array('Direct: kosten van onze dienstverlening', '', ''));
      $this->pdf->excelData[]=array('','Directe kosten vanaf ' . date('d-m-Y', db2jul($this->vanafDatum)),'', 'EUR', 'Percentage');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
  
      foreach($kostenRow as $row)
        $this->pdf->row($row);
  
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $kostenHerrekend=$eindWaarde*$kostenPercentage/100;
      $this->pdf->fillCell=array(1,1,1,1);
      $this->pdf->setFillColor(230);
      $this->pdf->row(array('Totaal directe kosten','€',  $this->formatGetal($kostenHerrekend*$this->scaleFactor, 0), $this->formatGetal($kostenPercentage, 2). ' %'));
      unset($this->pdf->fillCell);
//      $this->pdf->row(array(vertaalTekst('Totaal directe kosten',$this->pdf->rapport_taal),'€', $this->formatGetal($totaal, 0), $this->formatGetal($kostenPercentage, 2).' %'));
      $this->pdf->ln();
      
      
      
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array('Indirect: kosten van financiële instrumenten in portefeuille (o.a. beleggingsfondsen)'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array('- Eenmalig', '€',$this->formatGetal(0, false,false,true) , $this->formatGetal(0, 2,true,true)));
      $kostenSom=array_sum($totaalDoorlopendekostenGesplitst);
      $aandeel=($totaalDoorlopendekostenGesplitst['TotCostFund'])/$kostenSom;
      $this->pdf->row(array('- Doorlopend (op basis van lopende kosten factor)','€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$eindWaarde/100*$this->scaleFactor, 0), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
      $aandeel=$totaalDoorlopendekostenGesplitst['FundTransCost']/$kostenSom;
      $this->pdf->row(array('- Transactiekosten en overige','€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$eindWaarde/100*$this->scaleFactor, 0), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
      $aandeel=$totaalDoorlopendekostenGesplitst['FundPerfFee']/$kostenSom;
      $this->pdf->row(array('- Incidenteel (bijvoorbeeld performance fee)','€',$this->formatGetal($vkmPercentagePortefeuille*$aandeel*$eindWaarde/100*$this->scaleFactor, 0,false,true), $this->formatGetal($vkmPercentagePortefeuille*$aandeel, 2,true,true)));
  
      
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->fillCell=array(1,1,1,1);
      $this->pdf->row(array(vertaalTekst('Totaal indirecte kosten',$this->pdf->rapport_taal), '€',$this->formatGetal($vkmPercentagePortefeuille*$eindWaarde/100*$this->scaleFactor, 0), $this->formatGetal($vkmPercentagePortefeuille, 2,true ,true)));
      $this->pdf->ln();
      $this->pdf->setFillColor(200);
      $this->pdf->row(array('Totaal te verwachten directe en indirecte kosten op jaarbasis', '€',$this->formatGetal($vkmWaarde*$eindWaarde/100*$this->scaleFactor, 0), $this->formatGetal($vkmWaarde, 2,true ,true)));
      $this->pdf->ln();
      unset($this->pdf->fillCell);
      /*
      if ($this->melding <> '')
      {
        $y=$this->pdf->getY();
        $this->pdf->AutoPageBreak=false;
        $this->pdf->setY(270);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
        $this->pdf->row(array($this->melding));
        $this->pdf->excelData[] = array();
        $this->pdf->excelData[] = array('', $this->melding);
        $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
        $this->pdf->setY($y);
        $this->pdf->AutoPageBreak=true;
        unset($this->melding);
      }
      */
      
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
        $this->pdf->row(array('Totaal te verwachten directe en indirecte kosten op jaarbasis', '','', $this->formatGetal($percentage['AfwijkendeExAnte'], 2, true, true)));
        $this->pdf->ln();
      }
      else
      {
      //  $this->pdf->row(array('Totaal te verwachten directe en indirecte kosten op jaarbasis', '','', $this->formatGetal($vkmWaarde, 2, true, true)));
      }
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      //$this->pdf->row(array('Werkelijk netto jaarrendement over de afgelopen 12 maanden','','', $this->formatGetal($rendementProcent, 2) . ' %' ));
    //  $this->pdf->row(array('Bruto rendementsverwachting per jaar op basis van profiel','', '',$this->formatGetal($risicoklasse['verwachtRendement'], 2) . ' %' ));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $fontsize);
      $this->pdf->row(array('Netto rendementsverwachting','','', $this->formatGetal($risicoklasse['verwachtBrutoRendement']-$vkmWaarde, 2) . ' %' ));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $fontsize);
      $this->pdf->row(array('Beleggingshorizon in jaren','','', $jaren));
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

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

		$DB=new DB();
		$query = "SELECT " .
			"SUM(((TO_DAYS('".$tot."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$tot."') - TO_DAYS('".$van."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS gewogen, " .
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal " .
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

		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
		{
			$koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

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
			$naamBackup=$this->pdf->rapport_naam1;
			if($this->pdf->lastPOST['vkma_naam']<>'')
			$this->pdf->rapport_naam1=$this->pdf->lastPOST['vkma_naam'];
			$this->pdf->AddPage($this->layout);
      $this->pdf->rapport_naam1=$naamBackup;
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
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
      {
				$startjaar=true;
      }
			else
      {
				$startjaar=false;
      }
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

    $perHoofdcategorie=array();
    $perCategorie=array();



		$query="SELECT sum(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.Portefeuille='".$this->portefeuille."'  AND
TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$portefeuileWaarde=$DB->lookupRecord();
    $this->scaleFactor=1;
		if($this->pdf->lastPOST['vkma_clientselectie']<>1 && $this->pdf->lastPOST['vkma_bedrag']<>0)
		  $this->scaleFactor=$this->pdf->lastPOST['vkma_bedrag']/$portefeuileWaarde['actuelePortefeuilleWaardeEuro'];

//		listarray($this->pdf->lastPOST);

		$this->verdelingTotaal['perioden'][$this->rapportageDatum]=$portefeuileWaarde['actuelePortefeuilleWaardeEuro'];
	//	$this->verdelingTotaal['totaal']['gemiddelde']=$this->verdelingTotaal['perioden'][$this->rapportageDatum] ; Vermogen op einddatum vervangen voor gemiddelde vermogen zoals in de VKM


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
    $alleData=array();
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
    $totaalSom=array();
    $sub=array();
    $kostenPercentage=array();
    $laatsteFonds='';
    $totaalKosten=0;
    $totaaldlKosten=0;



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
    $lastCategorie='';
    $lastRegio='';
    $laatste='';
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
      $somVelden=array('beginwaarde','eindwaarde','stort','resultaat','gemWaarde','weging','bijdrage','kosten');
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
																$this->formatGetal($sub['eindwaarde']*$this->scaleFactor, 0),
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
					$dlkostenAbsoluutTxt=$this->formatGetal($dlkostenAbsoluut*$this->scaleFactor, 0,false,true);
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
														$this->formatGetal($data['eindwaarde']*$this->scaleFactor, 0),
														$TotCostFundTxt,
														$FundTransCostTxt,
														$FundPerfFeeTxt,
														$dlkostenAbsoluutTxt,
														$this->formatGetal($sub['weging'] * 100, 2, true),
														$this->formatGetal($bijdrageVKM, 2, true)
													));
					$this->pdf->excelData[]=array($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $fondsData['fondsOmschrijving'][$id],
						round($data['beginwaarde']*$this->scaleFactor, 0),
						round($data['eindwaarde']*$this->scaleFactor, 0),
						round($data['stort']*$this->scaleFactor, 0),
						round($data['resultaat']*$this->scaleFactor, 0),
						round($data['gemWaarde']*$this->scaleFactor, 0),
						round($kostenPercentage['TotCostFund'], 2),
						round($kostenPercentage['FundTransCost'], 2),
						round($kostenPercentage['FundPerfFee'], 2),
						round($dlkostenAbsoluut*$this->scaleFactor, 0),
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
																$this->formatGetal($sub['beginwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
																$this->formatGetal($sub['eindwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
																$this->formatGetal($sub['stort']*$this->scaleFactor, 0),
																$this->formatGetal($sub['resultaat']*$this->scaleFactor, 0),
																$this->formatGetal($sub['gemWaarde']*$this->scaleFactor, 0),
																$this->formatGetal($sub['transkosten']*$this->scaleFactor, 0),
																$this->formatGetal($sub['dlkostenPercentage'], 0),
																$this->formatGetal($sub['dlkostenAbsoluut']*$this->scaleFactor, 0, true),
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


			if($this->pdfVullen==true)
			{
				if ($lastRegio <> '')
				{
					$subregio = $perRegio[$hoofdcategorie][$categorie][$lastRegio]['perf'];
					$this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS');
					$this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
					$this->pdf->row(array('', '  subtotaal ' . $perRegio[$hoofdcategorie][$categorie][$lastRegio]['omschrijving'],
														$this->formatGetal($subregio['beginwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subregio['eindwaarde']*$this->scaleFactor, $this->pdf->rapport_VOLK_decimaal),
														$this->formatGetal($subregio['stort']*$this->scaleFactor, 0),
														$this->formatGetal($subregio['resultaat']*$this->scaleFactor, 0),
														$this->formatGetal($subregio['gemWaarde']*$this->scaleFactor, 0),
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
				  $this->pdf->AddPage($this->layout);
					//$y=$this->pdf->getY()+10;
			}
		}
		if($this->skipDetail==true)
		{
			$this->pdfVullen = true;
			$this->pdf->vmkHeaderOnderdrukken = true;
      
      if($this->pdf->lastPOST['vkma_naam']<>'')
      {
        $naamBackup=$this->pdf->rapport_naam1;
        $rapport_koptextBackup=$this->pdf->rapport_koptext;
        $this->pdf->rapport_naam1 = $this->pdf->lastPOST['vkma_naam'];
        $this->pdf->rapport_koptext='{Naam1}';
        $this->pdf->AddPage($this->layout);
        $this->pdf->rapport_naam1=$naamBackup;
        $this->pdf->rapport_koptext=$rapport_koptextBackup;
      }
      else
      {
        $this->pdf->AddPage($this->layout);
      }
     
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
				/*
				if ($this->melding <> '')
				{
					$this->pdf->ln();
					$this->pdf->row(array($this->melding));
					$this->pdf->excelData[] = array();
					$this->pdf->excelData[] = array('', $this->melding);
				}
        */
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

		$query="SELECT Risicoklassen.verwachtBrutoRendement FROM Portefeuilles
 JOIN Risicoklassen ON Portefeuilles.Risicoklasse=Risicoklassen.Risicoklasse AND Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder
 WHERE Portefeuilles.portefeuille='".$this->portefeuille."'";
		$db->SQL($query);
		$db->query();
		$data=$db->nextRecord();
    if($data['verwachtBrutoRendement'] <> 0 )
    {
      $rendement = $data['verwachtBrutoRendement'];
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
    
    if(isset($this->pdf->lastPOST['vkma_bedrag']) && $this->pdf->lastPOST['vkma_bedrag'] <> 0)
      $beginwaarde=$this->pdf->lastPOST['vkma_bedrag'];
    
    $grafiekWaardenVorigjaar=array();
		if($this->melding<>'')
    {
      $beginwaardeVorigJaar=getStortingen($this->portefeuille,$this->vanafDatum ,$this->rapportageDatum);
      $beginDatum=substr($this->vanafDatum,0,7);
    }
    else
    {
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage
		WHERE TijdelijkeRapportage.portefeuille = '" . $this->portefeuille . "' AND TijdelijkeRapportage.rapportageDatum= '" . $this->vanafDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $db->SQL($query);
      $db->Query();
      $start = $db->NextRecord();
      $beginwaardeVorigJaar = $start['actuelePortefeuilleWaardeEuro'];
      $beginDatum=substr($this->vanafDatum,0,4);
    }
    $kosten=$this->vkmWaarde['totaalDoorlopendekosten']+$this->vkmWaarde['totaalDirecteKosten'];

    $grafiekWaardenVorigjaar['waardeZonderKosten'][]=$beginwaardeVorigJaar;
    $grafiekWaardenVorigjaar['waardeZonderKosten'][]=$beginwaarde+$kosten;
    $grafiekWaardenVorigjaar['waardeNaKosten'][]=$beginwaardeVorigJaar;
    $grafiekWaardenVorigjaar['waardeNaKosten'][]=$beginwaarde;
    $grafiekWaardenVorigjaar['cumulatieveKosten'][]=0;
    $grafiekWaardenVorigjaar['cumulatieveKosten'][]=$kosten;
    
    $grafiekWaardenVorigjaar['datum'][]=substr($this->vanafDatum,0,7);
    $grafiekWaardenVorigjaar['datum'][]=substr($this->rapportageDatum,0,4);
    $grafiekWaardenVorigjaar['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Impact van kosten op ontwikkeling vermogen');
    $grafiekWaardenVorigjaar['titel']="Impact van kosten op het rendement van de afgelopen 12 maanden";
    
		$this->pdf->excelData[]=array();
		$this->pdf->excelData[]=array('Termijngrafiek');
		$this->pdf->excelData[]=array('doelJaar','rendement','beginwaarde','vkm');
		$this->pdf->excelData[]=array($doelJaar,round($rendement,2),round($beginwaarde,2),round($this->vkmWaarde['vkmWaarde'],4));
		$this->pdf->excelData[]=array('jaar','waardeNaKosten','cumulatieveKosten','waardeZonderKosten');

		$kosten=0;
		$grafiekWaarden=array();


    $vkmwaarde=$this->vkmWaarde['vkmWaarde'];

    for($i=$this->pdf->rapport_jaar; $i<=$doelJaar; $i++)
		{
   
			$jaren=$i-$this->pdf->rapport_jaar;
			$nieuweWaarde=$beginwaarde*pow(1+(($rendement-$vkmwaarde)/100),$jaren);
			
			$kosten+=$nieuweWaarde*($vkmwaarde/100);
     // echo "rendement $rendement | kosten : $kosten <br>\n";
      
      //$grafiekWaarden[$i]['waardeZonderKosten']=$nieuweWaarde;
			//$grafiekWaarden[$i]['waardeNaKosten']=$nieuweWaarde-$kosten;
			//$grafiekWaarden[$i]['cumulatieveKosten']=$kosten;
      
      $grafiekWaarden['waardeZonderKosten'][]=$nieuweWaarde+$kosten;
      $grafiekWaarden['waardeNaKosten'][]=$nieuweWaarde;//-$kosten;
      $grafiekWaarden['cumulatieveKosten'][]=$kosten;
      $grafiekWaarden['datum'][]=$i;
  
			
			$this->pdf->excelData[]=array($i,round($nieuweWaarde,2),round($kosten,2),round($nieuweWaarde+$kosten,2));
		}
		
		$grafiekWaarden['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Impact van kosten op ontwikkeling vermogen');

		$grafiekWaarden['titel']="Impact van kosten op de lange termijn waardeontwikkeling van de portefeuille";
		
	
		$waardeZonderKostenKleur=array(151,185,199);//array( 0,112,192 );
		$waardeNaKostenKleur=array(154,178,148);//array(12,173,71);
		$cumulatieveKostenKleur=array(241,89,38);//array(250,6,87);

		//if($this->pdf->getY()+70>$this->pdf->pagebreak)
		//	$this->pdf->AddPage('P');
    
    $this->pdf->AutoPageBreak=false;
    /*
    $this->pdf->SetXY(195,112-50);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize-2);
    $this->pdf->Cell(90, 4,  "Impact van kosten op het rendement van de afgelopen 12 maanden",0,0,'C');
    $this->pdf->setXY(195,107);
    //$this->LineDiagram(90, 40, $grafiekWaardenVorigjaar,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,true);//50
    $this->VBarDiagram2(90, 40, $grafiekWaardenVorigjaar);
    */
    
    if($this->layout=='P')
    {
      //$this->pdf->SetXY($this->pdf->marge + 20, 205);
      //$this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      //$this->pdf->Cell(150, 4, "Impact van kosten op het lange termijn rendement van de portefeuille", 0, 0, 'C');
      $this->pdf->setXY($this->pdf->marge + 20, 200);
      //$this->pdf->setXY($this->pdf->marge + 20, 205 + 55);
      $this->LineDiagram(150, 50, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50
      //$this->VBarDiagram2(150, 50, $grafiekWaarden);
      
      
    }
    else
    {
      /*
      $extray=10;
      $this->pdf->SetXY(195,112-50+$extray);
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize-2);
      $this->pdf->Cell(90, 4,  "Impact van kosten op het rendement van de afgelopen 12 maanden",0,0,'C');
      $this->pdf->setXY(195,107+$extray);
      //$this->LineDiagram(90, 40, $grafiekWaardenVorigjaar,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,true);//50
      $this->VBarDiagram2(90, 40, $grafiekWaardenVorigjaar);
      
      $extray=12;
      $this->pdf->SetXY(195,175-55+$extray);
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize-2);
      $this->pdf->Cell(90, 4, "Impact van kosten op het lange termijn rendement van de portefeuille", 0, 0, 'C');
      $this->pdf->setXY(195,165+$extray);
      //$this->LineDiagram(90, 40, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50
      $this->VBarDiagram2(90, 40, $grafiekWaarden);
  */
  
      $waardeZonderKostenKleur=array(102,36,131);//array( 0,112,192 );
      $waardeNaKostenKleur=array(154,178,148);//array(12,173,71);
      $cumulatieveKostenKleur=array(241,89,38);//array(250,6,87);
  
      $this->pdf->setXY(195,72);
      $this->LineDiagram(90, 40, $grafiekWaardenVorigjaar,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,true);//50
  
  
      $this->pdf->setXY(195,135);
      $this->LineDiagram(90, 40, $grafiekWaarden,array($waardeZonderKostenKleur,$waardeNaKostenKleur,$cumulatieveKostenKleur),0,0,4,4,false);//50
  
  
  
  
  
    }
    $this->pdf->AutoPageBreak=true;
		//listarray($grafiekWaarden);
//		echo "$doelJaar $rendement $beginwaarde";
	//	listarray( $this->vkmWaarde);
	//	exit;
	}
  
  
  function VBarDiagram2($w, $h, $data,$nbDiv=4,$numBars=0)
  {
    global $__appvar;
    $legendDatum = $data['datum'];
    //$data = $data['portefeuille'];
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //$this->pdf->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1);
    
    $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)

    $maxVal=0;
    $minVal=0;
    $maanden=array();
    foreach($data as $maand=>$maandData)
    {
      $maanden[$maand]=$maand;
      foreach($maandData as $type=>$waarde)
      {
        if($waarde > $maxVal)
          $maxVal = $waarde;
        if($waarde < $minVal)
          $minVal = $waarde;
      }
    }
    if($maxVal > 1)
      $maxVal=ceil($maxVal);
    if($minVal < -1)
      $minVal=floor($minVal);
    $minVal = $minVal * 1.1;
    $maxVal = $maxVal * 1.1;
    if ($maxVal <0)
      $maxVal=0;
  
    $maxVal=(ceil($maxVal/100000)*100000);
    if($minVal < 0)
    {
      $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
      $nulYpos =  $unit * (-1 * $minVal);
    }
    else
    {
      $unit = $hGrafiek / $maxVal * -1;
      $nulYpos =0;
    }
    
    
    
    $horDiv = 10;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    
    //$stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
    $stapgrootte = abs($bereik)/$horDiv;
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;
    
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Text($XstartGrafiek-7, $i, round($n*$stapgrootte));
  
      $this->pdf->SetXY($XstartGrafiek - 7, $i-2);
      $this->pdf->Cell(7, 4, number_format($n * $stapgrootte,0,',','.')."",0,0,'R');
      
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        //$this->pdf->Text($XstartGrafiek - 7, $i, round());
  
        $this->pdf->SetXY($XstartGrafiek - 7, $i-2);
        $this->pdf->Cell(7, 4, number_format($n * $stapgrootte,0,',','.')."",0,0,'R');
        
      }
      $n++;
      if($n >20)
        break;
    }
    
    $numBars=count($data);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
   // $colors=array('allocateEffect'=>array(108,31,128),'selectieEffect'=>array(234,105,11));//,'totaalEffect'=>array(0, 52, 121)); //
  
    $waardeZonderKostenKleur=array(151,185,199);//array( 0,112,192 );
    $waardeNaKostenKleur=array(154,178,148);//array(12,173,71);
    $cumulatieveKostenKleur=array(241,89,38);//array(250,6,87);
  
    $colors=array('waardeZonderKosten'=>$waardeZonderKostenKleur,'waardeNaKosten'=>$waardeNaKostenKleur,'cumulatieveKosten'=>$cumulatieveKostenKleur);
    $legenda=array('waardeZonderKosten'=>'Waardeontwikkeling zonder kosten','waardeNaKosten'=>'Waardeontwikkeling na kosten','cumulatieveKosten'=>'Impact van kosten op ontwikkeling vermogen');

    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/4; //4
    $bGrafiek = $vBar * ($this->pdf->NbVal );
    $eBaton = ($vBar * 80 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    foreach($data as $maand=>$maandData)
    {
      
      foreach($maandData as $type=>$val)
      {
        $color=$colors[$type];
        //Bar
        $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiek + $nulYpos;
        $hval = ($val * $unit);
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
        /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3 && $eBaton > 4)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        */
        $i++;
      }
      $i++;
      
      
      $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,$maand);
  
  
     // $this->pdf->SetXY($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2, $YstartGrafiek +3);
     // $this->pdf->Cell($eBaton, 4, $maand."",0,0,'C');
  
  
    }

		$yOffset=5;
    $step=0;
    $i=0;
    if($this->layout=='P')
      $newlineTrigger=2;
    else
      $newlineTrigger=1;
		foreach ($legenda as $categorie=>$item)
    {

        $kleur=$colors[$categorie];
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XstartGrafiek+$step , $YPage+$yOffset+0.5, 1.5, 1.5, 'F','',$kleur);
      $this->pdf->SetXY($XstartGrafiek+3+$step,$YPage+$yOffset);
      $this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));
      $step+=50;
      if($i==$newlineTrigger)
      {
        $step=0;
        $yOffset+=4;
      }
      $i++;
      
      
    }
    $this->pdf->SetDrawColor(0,0,0);
    
    
    // $color=array(155,155,155);
    // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
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

		//	$this->pdf->setY($Ypage-3);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize-1);
		$this->pdf->Cell($w,0,vertaalTekst($titel,$this->pdf->rapport_taal),0,0,'C');
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
    if($vanafBegin==true)
      $offset=-1;
    else
      $offset=0;
      
		$unit = $lDiag / (count($data)+$offset);



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
			$this->pdf->Cell(7 , 4 , "€ ". $this->formatGetal(0-round($n*$stapgrootte/1000)*1000,0) , 0, 1, "R");

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
				$this->pdf->Cell(7 , 4 , "€ " .$this->formatGetal(round($n * $stapgrootte/1000)*1000 + 0,0) , 0, 1, "R");

			}
			$n++;
			if($n >20)
				break;
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
		$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		$jaren=ceil(count($data)/12);
		if($vanafBegin==true)
		  $xoffset=-1;
		else
		  $xoffset=0;
		for ($i=0; $i<count($data); $i++)
		{
			if($i%$jaren==0)
				$this->pdf->TextWithRotation($XDiag+($i+$xoffset)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
			$yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

			if ($i>0 )
			{
				$this->pdf->line($XDiag+($i+$xoffset)*$unit, $yval, $XDiag+($i+1+$xoffset)*$unit, $yval2,$lineStyle );
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

				if ($i>0 )
				{
					$this->pdf->line($XDiag+($i+$xoffset)*$unit, $yval, $XDiag+($i+1+$xoffset)*$unit, $yval2,$lineStyle );
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

				if ($i>0)
				{
					$this->pdf->line($XDiag+($i+$xoffset)*$unit, $yval, $XDiag+($i+1+$xoffset)*$unit, $yval2,$lineStyle );
				}
				$yval = $yval2;
			}
		}

		$this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.1,'cap' => 'butt'));
		$step=0;
		$aantal=count($legendaItems);
		$yOffset=10;
		foreach ($legendaItems as $index=>$item)
		{
			if($index==0)
				$kleur=$color;
			elseif($index==1)
				$kleur=$color1;
			else
				$kleur=$color2;
			$this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
			$this->pdf->Rect($XPage+$step , $YPage+$h+$yOffset+1, 3, 0.7, 'F','',$kleur);
			$this->pdf->SetXY($XPage+3+$step,$YPage+$h+$yOffset);
			$this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));
      $step+=50;
			if($index==2 || ($this->layout=='L' && $index==1) )
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
			$weging=$eindwaarde/$this->totalen['gemiddeldeWaarde'];
    else
      $weging=$eindwaarde/$this->verdelingTotaal['totaal']['gemiddelde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$eindwaarde*$weging;



  return array(
  'beginwaarde'=>$beginwaarde,
  'eindwaarde'=>$eindwaarde,

  'stort'=>$AttributieStortingenOntrekkingen['totaal'],
  'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
  'storting'=>$AttributieStortingenOntrekkingen['storting'],
  'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
  'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
  'resultaat'=>$resultaat,
  'gemWaarde'=>$eindwaarde,

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
