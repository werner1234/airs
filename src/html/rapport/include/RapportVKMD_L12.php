<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/26 06:50:14 $
File Versie					: $Revision: 1.11 $

$Log: RapportVKMD_L12.php,v $
Revision 1.11  2020/07/26 06:50:14  rvv
*** empty log message ***

Revision 1.10  2020/07/25 15:36:13  rvv
*** empty log message ***

Revision 1.9  2020/02/12 10:49:30  rvv
*** empty log message ***

Revision 1.8  2020/02/05 17:12:14  rvv
*** empty log message ***

Revision 1.7  2020/02/01 18:11:55  rvv
*** empty log message ***

Revision 1.6  2020/01/29 17:36:42  rvv
*** empty log message ***

Revision 1.5  2020/01/25 16:37:01  rvv
*** empty log message ***

Revision 1.4  2019/12/21 14:08:32  rvv
*** empty log message ***

Revision 1.3  2019/12/07 17:48:23  rvv
*** empty log message ***

Revision 1.2  2019/12/04 15:56:35  rvv
*** empty log message ***

Revision 1.1  2019/11/16 17:12:28  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVKMD_L12
{
	function RapportVKMD_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		if(is_object($pdf))
		{
			$this->pdf = &$pdf;
			$this->pdf->rapport_type = "VKMD";
			$this->pdf->rapport_datum = db2jul($rapportageDatum);
			$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
			$this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
			$this->pdf->underlinePercentage=0.8;
			$this->pdf->rapport_titel = '';//vertaalTekst("Overzicht van de kosten in de portefeuille",$this->pdf->rapport_taal);
			$this->pdfVullen=true;
			$this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
			$this->voetnootGrijs=array(100,100,100);
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
		$this->pdf->vkmsVanaf= db2jul($this->vanafDatum);
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
		  $data.="%";
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
    
    $query="SELECT
Portefeuilles.Portefeuille,
Portefeuilles.Risicoprofiel,
Portefeuilles.Risicoklasse,
Portefeuilles.BeheerfeeBTW
FROM
Portefeuilles
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $risicoklasse=$DB->nextRecord();

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
    if($this->pdfVullen==true)
		{
      $this->pdf->setY(70);
			$this->pdf->ln();
			$this->pdf->setAligns(array('L', 'R', 'R'));
			$this->pdf->setWidths(array(108, 30, 30));
		//	$this->pdf->row(array(vertaalTekst('Doorlopende kosten',$this->pdf->rapport_taal), $this->formatGetal($totaalDoorlopendekosten, 0) . ' EUR'));
	//		$this->pdf->row(array(vertaalTekst('Doorlopende kosten ten opzichte van onderliggend vermogen',$this->pdf->rapport_taal), $this->formatGetal($doorlopendeKostenPercentage * 100, 2) . ' %'));
			$this->pdf->ln();
	
	  }

		$percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
		$herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
		$aandeelIndirect=$perfTotaal['gemWaarde']/$gemiddelde;
		//echo $aandeelIndirect;exit;
		$vkmPercentagePortefeuille=$herrekendeKosten*100*$aandeelIndirect;

		$totaal=0;

    $grootboekKosten=array();


    $directeKostenRegels=array();
		while($data = $DB->nextRecord())
		{
			$voetNoot='';
			if($this->pdfVullen==true)
			{
        if($data['Grootboekrekening']=='KOST')
        {
          //$data['totaal']+=$spreadKostenEUR;
					$voetNoot='3';
        }
        if($data['Grootboekrekening']=='BEW')
				{
					$data['Omschrijving'] = 'Servicekosten depotbank';
					$voetNoot='4';
				}
				if($data['Grootboekrekening']=='KOBU')
				{
					$voetNoot='3';
				}
        $kostenProcent=$data['totaal']/$gemiddelde*100;
        //if($data['Grootboekrekening']=='BEW')
        //  $data['Omschrijving']=$data['Omschrijving'].' 1)';
        if($data['Grootboekrekening']=='KOST')
          $data['Omschrijving']=$data['Omschrijving'].' 1)';
        
        if($data['Grootboekrekening']=='BEH')
        {
          $zonderBTWBedrag=$data['totaal']/(1+($risicoklasse['BeheerfeeBTW']/100));
          $directeKostenRegels[]=array('Vergoeding Waterland',$zonderBTWBedrag/$gemiddelde*100,$zonderBTWBedrag,'1');
          if($risicoklasse['BeheerfeeBTW']<>0)
             $directeKostenRegels[]=array('BTW over vergoeding Waterland',($data['totaal']-$zonderBTWBedrag)/$gemiddelde*100, $data['totaal']-$zonderBTWBedrag,'4');
        }
        else
        {
          $directeKostenRegels[]=array($data['Omschrijving'],$kostenProcent,$data['totaal'],$voetNoot);
        }
				$barData[$data['Omschrijving']]=$kostenProcent;
			}
			$grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
			$totaal+=$data['totaal'];
		}
    
    if($spreadKostenEUR <> 0)
    {
      $grootboekKosten['KOST'] += $spreadKostenEUR;
      $totaal+=$spreadKostenEUR;
    }
    
	//	listarray($grootboekKosten);exit;
		//

    unset($this->pdf->CellBorders);
    $grootBoekKostenTotaal=$totaal;
    $restKosten=$grootBoekKostenTotaal-($grootboekKosten['BEH']+$grootboekKosten['KOST']+$grootboekKosten['KOBU']+$grootboekKosten['BEW']);


		$kostenPercentage=$totaal/$gemiddelde*100;
		$vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
		if($this->pdfVullen==true)
		{

		  /*
1. Beleggingsdienstverlening / doorlopend = BEH
2. Beleggingsdienstverlening / transacties = KOST + KOBU
3. Beleggingsdienstverlening / verwant = BEW
4. Kosten instrumenten / doorlopend = totCostFund
5. Kosten instrumenten / transacties = transCostFund
6. Kosten instrumenten / incidenteel = perfFeeFund
*/
//listarray($totaalDoorlopendekostenGesplitst);
      $totaalKosten=array_sum($totaalDoorlopendekostenGesplitst);
      foreach($totaalDoorlopendekostenGesplitst as $kostenSoort=>$waarde)
      {
        $aandeel = $totaalDoorlopendekostenGesplitst[$kostenSoort] / $totaalKosten;
        //echo ($vkmPercentagePortefeuille * $gemiddelde * $aandeel / 100) . "= $vkmPercentagePortefeuille*$gemiddelde*$aandeel/100 <br>\n";
        $totaalDoorlopendekostenGesplitst[$kostenSoort]=$vkmPercentagePortefeuille*$gemiddelde*$aandeel/100;
        
      }
     // listarray($totaalDoorlopendekostenGesplitst);
		  $bedragen=array('Kosten van de beleggingsdienstverlening'=>array(1=>$grootboekKosten['BEH'],2=>$grootboekKosten['KOST']+$grootboekKosten['KOBU'],3=>$grootboekKosten['BEW']+$restKosten),
                      'Kosten van de financiële instrumenten in de beleggingsportefeuille 2)'=>array(4=>$totaalDoorlopendekostenGesplitst['TotCostFund'],5=>$totaalDoorlopendekostenGesplitst['FundTransCost'],6=>$totaalDoorlopendekostenGesplitst['FundPerfFee']));
      //$groepen=array('Eenmalig','Doorlopend','Transacties','Verwante diensten','Incidentieel');
      $groepen=array('Kosten van de beleggingsdienstverlening'=>array(0=>'Eenmalig',1=>'Doorlopend',2=>'Transacties 1)',3=>'Verwante diensten',4=>'Incidentieel'),
                     'Kosten van de financiële instrumenten in de beleggingsportefeuille 2)'=>array(0=>'Eenmalig',4=>'Doorlopend',5=>'Transacties 1)',3=>'Verwante diensten',6=>'Incidentieel'));
      // MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
      $this->pdf->SetFillColor($this->pdf->rapport_kop_kleur[0],$this->pdf->rapport_kop_kleur[1],$this->pdf->rapport_kop_kleur[2]);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor(255);
			$this->pdf->setY(40);

      $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
      
      $this->pdf->multicell(160,6,vertaalTekst('Specificatie van uw kosten',$this->pdf->rapport_taal).' '.
                               date('d-',$this->pdf->vkmsVanaf).vertaalTekst($maanden[date('n',$this->pdf->vkmsVanaf)],$this->pdf->rapport_taal).date('-Y',$this->pdf->vkmsVanaf).' '.
                               vertaalTekst('tot en met',$this->pdf->rapport_taal).' '.
                               date('d-',$this->pdf->rapport_datum).vertaalTekst($maanden[date('n',$this->pdf->rapport_datum)],$this->pdf->rapport_taal).date('-Y',$this->pdf->rapport_datum),0,'L',1);
      
      $this->pdf->SetTextColor(0);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //$this->pdf->Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      $ybegin=50;
      foreach($groepen as $hoofdgroep=>$subgroepen)
      {
        $this->pdf->setY($ybegin);
        $this->pdf->ln(2);
        
        $this->pdf->MultiCell(60,4,vertaalTekst($hoofdgroep,$this->pdf->rapport_taal),0,'L',0);
        
        if($hoofdgroep=='Kosten van de beleggingsdienstverlening')
          $this->pdf->SetFillColor($this->pdf->rapport_donker_kleur[0],$this->pdf->rapport_donker_kleur[1],$this->pdf->rapport_donker_kleur[2]);
        else
          $this->pdf->SetFillColor($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2]);
        $this->pdf->setY($ybegin);
        foreach($subgroepen as $index=>$groepOmschrijving)
        {
          $this->addKostenRegel($groepOmschrijving,'',$bedragen[$hoofdgroep][$index],$bedragen[$hoofdgroep][$index]/$gemiddelde*100);//$index
        }
        $ybegin+=30;
      }
      /*
      $this->pdf->ln(12);
      $this->pdf->Cell(60,5,vertaalTekst('Kosten van de beleggingsdienstverlening',$this->pdf->rapport_taal),0,0,'L',0);
      $this->pdf->SetFillColor($this->pdf->rapport_donker_kleur[0],$this->pdf->rapport_donker_kleur[1],$this->pdf->rapport_donker_kleur[2]);

			$this->pdf->setY(50);
      foreach($groepen as $i=>$groep)
			  $this->addKostenRegel($groep,$i+1,0,0);

      //$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      //$this->pdf->Cell(25,5,'€ '.$this->formatGetal($totaal, 0),0,0,'R',1);
      //$this->pdf->Cell(15,5, $this->formatGetal($kostenPercentage, 2).' %',0,0,'R',1);

			$this->pdf->setY(85);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->MultiCell(60,5,vertaalTekst("Kosten van de financiële instrumenten in de beleggingsportefeuille",$this->pdf->rapport_taal),0,'L',0);
      $this->pdf->SetFillColor($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2]);
			$this->pdf->setY(80);
			foreach($groepen as $i=>$groep)
				$this->addKostenRegel($groep,$i+1,0,0);

     // $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
     // $this->pdf->setXY($this->pdf->marge+60,$this->pdf->getY()-7.5);
     // $this->pdf->Cell(25,5,'€ '.$this->formatGetal($vkmPercentagePortefeuille*$gemiddelde/100, 0),0,0,'R',1);
      ////$this->pdf->Cell(15,5, $this->formatGetal($vkmPercentagePortefeuille, 2).' %',0,0,'R',1);
      //$this->pdf->ln(8);
      */

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			/*
      $this->pdf->line($this->pdf->getX(),$this->pdf->getY(),$this->pdf->getX()+100,$this->pdf->getY(),
											 array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
      $this->pdf->ln(3);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(60,5,vertaalTekst('Totaal',$this->pdf->rapport_taal),0,0,'L',0);
      $this->pdf->SetFillColor($this->pdf->rapport_donker_kleur[0],$this->pdf->rapport_donker_kleur[1],$this->pdf->rapport_donker_kleur[2]);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(25,5,'€ '.$this->formatGetal($vkmWaarde*$gemiddelde/100, 0),0,0,'R',0);
      $this->pdf->Cell(15,5, $this->formatGetal($vkmWaarde, 2).' %',0,0,'R',0);
      
      
      $this->pdf->setY(90);
      $this->pdf->Cell(60,5,'',0,0,'L',0);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(40,5,$this->formatGetal($totaal/$gemiddelde*100,2).' %',0,0,'C',0);
      $this->pdf->ln(8);
			$this->pdf->SetFillColor($this->pdf->rapport_donker_kleur[0],$this->pdf->rapport_donker_kleur[1],$this->pdf->rapport_donker_kleur[2]);
      foreach($directeKostenRegels as $regel)
      {
				$this->addKostenRegel($regel[0],$regel[3],$regel[2],$regel[1]);
      }

			$this->pdf->ln(3);
			$this->pdf->line($this->pdf->marge+103,98,$this->pdf->marge+103,$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
			$this->pdf->line($this->pdf->marge+60,$this->pdf->getY(),$this->pdf->marge+103,$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
			$this->pdf->line($this->pdf->marge+103,$this->pdf->getY(),$this->pdf->marge+103,$this->pdf->getY()+3*8+2,array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->TextWithRotation($this->pdf->marge+105,$this->pdf->getY()+1,0,0);
			$this->pdf->TextWithRotation($this->pdf->marge+107,$this->pdf->getY()-8,"Directe kosten",90);
			$this->pdf->TextWithRotation($this->pdf->marge+107,$this->pdf->getY()+25,"Indirecte kosten",90);
      $this->pdf->ln(5);


      $totaalKosten=$totaalDoorlopendekostenGesplitst['FundTransCost']+$totaalDoorlopendekostenGesplitst['FundPerfFee']+$totaalDoorlopendekostenGesplitst['TotCostFund'];
      $this->pdf->SetFillColor($this->pdf->rapport_licht_kleur[0],$this->pdf->rapport_licht_kleur[1],$this->pdf->rapport_licht_kleur[2]);
			$aandeel=$totaalDoorlopendekostenGesplitst['TotCostFund']/$totaalKosten;
			$this->addKostenRegel('Kosten beleggingsfondsen','5',$vkmPercentagePortefeuille*$gemiddelde*$aandeel/100,$vkmPercentagePortefeuille*$aandeel);
			$aandeel=$totaalDoorlopendekostenGesplitst['FundTransCost']/$totaalKosten;
			$this->addKostenRegel('Transactiekosten beleggingsfondsen','6',$vkmPercentagePortefeuille*$gemiddelde*$aandeel/100,$vkmPercentagePortefeuille*$aandeel);
			$aandeel=$totaalDoorlopendekostenGesplitst['FundPerfFee']/$totaalKosten;
			$this->addKostenRegel('Performance fee beleggingsfondsen','2',$vkmPercentagePortefeuille*$gemiddelde*$aandeel/100,$vkmPercentagePortefeuille*$aandeel);

			$this->pdf->setX($this->pdf->marge);
      $this->pdf->Cell(60,5,'',0,0,'L',0);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->Cell(40,5,$this->formatGetal($vkmPercentagePortefeuille,2).' %',0,0,'C',0);
      
   //   rapport_kop_kleur
     // $pdf->rapport_donker_kleur = array(128,150,171);
    //  $pdf->rapport_licht_kleur = array(194,207,218);
			*/
			$this->voetNoot();
      

      unset($this->pdf->CellBorders);

		}
    $this->vkmWaarde=array('vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde,'grootboekKosten'=>$grootboekKosten,
      'gemiddeldeWaarde'=>$gemiddelde,'grootBoekKostenTotaal'=>$grootBoekKostenTotaal,'totaalDoorlopendekosten'=>$totaalDoorlopendekosten,'totaalDirecteKosten'=>$totaal,
      'totaalDoorlopendekostenGesplitst'=>$totaalDoorlopendekostenGesplitst);

	}

	function addKostenRegel($omschrijving,$getal,$euroBedrag,$percentage)
	{
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->setX(80);
		$this->pdf->Cell(40,5,vertaalTekst($omschrijving,$this->pdf->rapport_taal),0,0,'L',1);
		$this->pdf->Cell(3,5,'',0,0,'R',0);
    $this->pdf->SetFont($this->pdf->rapport_fontEur,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell(4,5,($euroBedrag<>''?' €':''),0,0,'L',1);
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Cell(18,5,$this->formatGetal($euroBedrag, 0),0,0,'R',1);
		$this->pdf->Cell(3,5,'',0,0,'R',0);
		$this->pdf->Cell(15,5,$this->formatGetal($percentage, 2) . ($percentage<>''?' %':''),0,0,'R',1);
		$this->pdf->setTextColor($this->voetnootGrijs[0],$this->voetnootGrijs[1],$this->voetnootGrijs[2]);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->Cell(5,5,$getal,0,0,'R',0);
		$this->pdf->setTextColor(0);
		$this->pdf->ln(4);
	}



	function voetNoot()
	{
		$this->pdf->setWidths(array(5,230));
		$this->pdf->setAligns(array('L','L','L'));
		$this->pdf->setXY($this->pdf->marge,140);
		$this->pdf->line($this->pdf->getX(),$this->pdf->getY()-4,$this->pdf->getX()+105,$this->pdf->getY()-4,
										 array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
    $this->pdf->setTextColor($this->voetnootGrijs[0],$this->voetnootGrijs[1],$this->voetnootGrijs[2]);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('1','Inclusief bied/laat kosten voor transacties en transactiekosten valuta\'s.'));
    $this->pdf->row(array('2','Volgens opgave van het fonds of schatting van Waterland als er geen opgave is.'));
    /*
		$this->pdf->row(array('1','Betreft de verwachte beheervergoeding Waterland inclusief BTW.'));
		$this->pdf->row(array('2','Betreft verwachte transactiekosten en valutakosten, inclusief invulling en/of omzetting portefeuille op basis van een proxy van de afgelopen twee jaar.'));
		$this->pdf->row(array('3','In de transactiekosten wordt een schatting van de gemiddelde spreadkosten meegenomen (ook van valutatransacties). Let wel, alleen valutatransacties uitgevoerd bij InsingerGilisser.'));
		$this->pdf->row(array('4','Betreft de verwachte administratiekosten en bewaarloon inclusief BTW van de depotbank InsignerGilissen en indien van toepassing ABN AMBRO.'));
		$this->pdf->row(array('5','Betreft de doorlopende kosten volgens opgave van het fonds ( PRIIRP, jaarverslag of KIID ) over het voorgaande kalenderjaar.'));
		$this->pdf->row(array('6','Betreft de transactiekosten van voorgaande jaar volgens opgrave van het fonds of schatting van WIS (incusief spread en valutakosten).'));
    */
    //Betreft de verwachte performance fee kosten van fondsen in de voorgestelde portefeuille op basis van onze verwachte perfomence voor deze fondsen in het aankomend jaar.

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
			$this->pdf->templateVars['VKMDPaginas'] = $this->pdf->page;
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
       // listarray($kostenPercentage);


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

		if($this->skipDetail==true)
		{
			$this->pdfVullen = true;
			$this->pdf->vmkHeaderOnderdrukken = true;
			$this->pdf->addPage();
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		}


		if($this->skipSummary==false)
		{
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

			}
		}
    
    if (isset($this->pdf->vmkHeaderOnderdrukken))
    {
      unset($this->pdf->vmkHeaderOnderdrukken);
    }


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

  
}