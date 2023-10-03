<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIR_L32
{
	function RapportOIR_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht Private Equity Portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$this->pdf->underlinePercentage=0.8;
		$this->extraVoetPages=array();
		$this->extraVoet='';
		$this->extraVoet2='';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



	function writeRapport()
	{
		global $__appvar;
		//$brightness=1.55;


		$this->pdf->AddPage();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['ENDPaginas']=$this->pdf->rapport_titel;
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $db=new DB();
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $db->SQL($q);
    $db->Query();
    $kleuren = $db->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
		
		$FondsExtraInformatieObject=new FondsExtraInformatie();
		if(isset($FondsExtraInformatieObject->data['fields']['Aanvang']))
		{
			$extraVeld=',FondsExtraInformatie.Aanvang,FondsExtraInformatie.Strategie';
			$extraJoin='LEFT JOIN FondsExtraInformatie ON TijdelijkeRapportage.fonds=FondsExtraInformatie.fonds';
		}
		else
		{
      $extraVeld='';
      $extraJoin='';
		}
    
    
    if(isset($this->pdf->portefeuilles)&& count($this->pdf->portefeuilles)>0)
    {
      $portefeuilles = $this->pdf->portefeuilles;
    }
    else
      $portefeuilles=array($this->portefeuille);
    
    $query="SELECT soortReservering,bedrag,Omschrijving,contractueleUitsluitingen.fonds,Fondsen.Valuta
FROM contractueleUitsluitingen
JOIN Portefeuilles ON contractueleUitsluitingen.Portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.Einddatum>'".$this->rapportageDatum."'
LEFT JOIN Fondsen ON contractueleUitsluitingen.fonds=Fondsen.Fonds
WHERE contractueleUitsluitingen.soortReservering='Commitment' AND contractueleUitsluitingen.Portefeuille IN('".implode("','",$portefeuilles)."') ";
    $db->SQL($query);
    $db->Query();
    $restricties=array();
    $fondsen=array();
    while($data=$db->nextRecord())
    {
      $data['valutakoers']=getValutaKoers($data['Valuta'],$this->rapportageDatum);
      $restricties[$data['fonds']]=$data;
      $fondsen[]=mysql_real_escape_string($data['fonds']);
    }
    
    $query="SELECT (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers -Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers) as Bedrag,Rekeningmutaties.Fonds,Rekeningmutaties.transactietype,Rekeningmutaties.Boekdatum,
Rekeningmutaties.Aantal*Rekeningmutaties.Fondskoers*Fondsen.fondseenheid as BedragValuta
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND
     Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND Rekeningmutaties.transactietype IN('A','D','B','V','L') AND
     Rekeningmutaties.Fonds IN('".implode("','",$fondsen)."') ORDER BY Rekeningmutaties.Boekdatum";
    $db->SQL($query);
    $db->Query();
    $tansacties=array();
    $eersteBoeking=array();
    while($data=$db->nextRecord())
    {
    	if($data['transactietype']=='A'||$data['transactietype']=='D'||$data['transactietype']=='B')
    		$type='aankoop';
    	else
    		$type='verkoop';
      if(!isset($tansacties[$data['Fonds']][$type]))
      {
        $tansacties[$data['Fonds']][$type]['EUR'] = 0;
        $tansacties[$data['Fonds']][$type]['valuta'] = 0;
      }
  
      $data['valutakoers']=getValutaKoers($data['Fonds'],$data['Boekdatum']);
      
      if($data['transactietype']=='B')
      {
        if(!isset($eersteBoeking[$data['Fonds']]))
        {
          $tansacties[$data['Fonds']][$type]['EUR'] += $data['Bedrag'];
          $tansacties[$data['Fonds']][$type]['valuta'] += $data['BedragValuta'];
        }
      }
      else
      {
      	//listarray($data);
        $tansacties[$data['Fonds']][$type]['EUR'] += $data['Bedrag'];
        $tansacties[$data['Fonds']][$type]['valuta'] += $data['BedragValuta'];
      }
      if(!isset($eersteBoeking[$data['Fonds']]))
      {
        $eersteBoeking[$data['Fonds']] = true;
      }
    }
    

		$query = "SELECT TijdelijkeRapportage.beleggingscategorie,
        TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.Valuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
       (TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as beginPortefeuilleWaardeEuro,".
			" TijdelijkeRapportage.actueleFonds,
				TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				  TijdelijkeRapportage.beleggingscategorie,
				  TijdelijkeRapportage.valuta,
          TijdelijkeRapportage.type,
				   TijdelijkeRapportage.portefeuille,
				   TijdelijkeRapportage.historischeWaarde,
           round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd $extraVeld".
			" FROM TijdelijkeRapportage $extraJoin
      WHERE  ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type IN('fondsen') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND ".
      " TijdelijkeRapportage.Fonds IN('".implode("','",$fondsen)."') "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.type,
TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB2 = new DB();
		$DB->SQL($query);
		$DB->Query();
  
		$totalen=array();
    $dataregel=array();
    $totaleCommitment=0;
    $grafiek=array();
		while($data = $DB->NextRecord())
    {
      $dataregel[]=$data;
      $totaleCommitment+=$restricties[$data['fonds']]['bedrag']*$restricties[$data['fonds']]['valutakoers'];
    }
    $randomKleur=array();
    foreach($allekleuren as $soort=>$categorieen)
		{
			foreach($categorieen as $categorie=>$kleur)
			{
				if($kleur['R']['value']||$kleur['G']['value']||$kleur['B']['value'])
          $randomKleur[$kleur['R']['value'].$kleur['G']['value'].$kleur['B']['value']]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
			}
		}
    $randomKleur=array_values($randomKleur);
    $i=0;
    foreach($dataregel as $data)
    {
      $query = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )-ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) ,0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           Grootboekrekeningen.Opbrengst=1  AND  Rekeningmutaties.Grootboekrekening<>'FONDS' AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND
           Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND Rekeningmutaties.Fonds = '".$data['fonds']."'";
      $DB2->SQL($query);
      $extra=$DB2->lookupRecord();
      
      $commitmentValuta=$restricties[$data['fonds']]['bedrag'];
      $commitmentEUR=$restricties[$data['fonds']]['bedrag']*$restricties[$data['fonds']]['valutakoers'];
      
      $aankoopEur=$tansacties[$data['fonds']]['aankoop']['EUR'];
      $aankoopValuta=$tansacties[$data['fonds']]['aankoop']['valuta'];
    
			$this->pdf->row(array($data['fondsOmschrijving'],
												$data['Aanvang'],
                       	$data['Valuta'],
                        $this->formatGetal($commitmentValuta,0),
                        $this->formatGetal($commitmentEUR,0),
'',

                        $this->formatGetal($aankoopValuta,0),
                        $this->formatGetal($aankoopEur,0),
                        $this->formatGetal($aankoopValuta/$commitmentValuta*100,1),
												"",
                        $this->formatGetal($tansacties[$data['fonds']]['verkoop']['valuta']*-1,0),
                        $this->formatGetal($tansacties[$data['fonds']]['verkoop']['EUR']*-1,0),
                        $this->formatGetal($extra['opbrengstTotaal'],0),
                        $this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],0),
                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),
                        $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$extra['opbrengstTotaal']-$tansacties[$data['fonds']]['verkoop']['EUR'])/$aankoopEur,2)
											)	);
			
//echo $data['fondsOmschrijving']." | ".$data['actuelePortefeuilleWaardeEuro']."+".$extra['opbrengstTotaal'].")/$aankoopEur <br>\n";
			$totalen['commitment']+=$commitmentEUR;
			$totalen['aankopen']+=$aankoopEur;
			$totalen['verkopen']+=$tansacties[$data['fonds']]['verkoop']['EUR'];
      $totalen['opbrengstTotaal']+=$extra['opbrengstTotaal'];
			$totalen['huidigewaarde']+=$data['actuelePortefeuilleWaardeEuro'];
			if($data['Aanvang']=='')
        $data['Aanvang']='leeg';
      if($data['Strategie']=='')
        $data['Strategie']='leeg';
			$grafiek['Aanvang']['Percentage'][$data['Aanvang']]+=$commitmentEUR/$totaleCommitment*100;
      $grafiek['Strategie']['Percentage'][$data['Strategie']]+=$commitmentEUR/$totaleCommitment*100;
      
      $grafiek['Aanvang']['Kleur'][]=$randomKleur[$i];
      $grafiek['Strategie']['Kleur'][]=$randomKleur[$i];
      $i++;
		}

		$this->pdf->CellBorders = array('','','','','SUB','','','SUB','','','','SUB','SUB','','SUB');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','','','',
                      $this->formatGetal($totalen['commitment'],0),
                       '','',
                      $this->formatGetal($totalen['aankopen'],0),
                      '','','',
                      $this->formatGetal($totalen['verkopen']*-1,0),
                      $this->formatGetal($totalen['opbrengstTotaal'],0),
                      '',
                      $this->formatGetal($totalen['huidigewaarde'],0)
                      ));
		$this->pdf->CellBorders = array();
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
    /*
    $ystart=100;
    $headerHeight=30;
    $vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;
    $chartsize=55;

    $i=0;
    $this->pdf->setXY($this->pdf->marge+5+90*$i , $ystart);
    $legendaStart=array($this->pdf->marge+5+90*$i,$ystart+$chartsize+10);
    PieChart_L51($this->pdf, $chartsize, $vwh, $grafiek['Aanvang']['Percentage'], '%l', $grafiek['Aanvang']['Kleur'], vertaalTekst('Aanvang', $this->pdf->rapport_taal), $legendaStart);

    $i=1;
    $this->pdf->setXY($this->pdf->marge+5+90*$i , $ystart);
    $legendaStart=array($this->pdf->marge+5+90*$i,$ystart+$chartsize+10);
    PieChart_L51($this->pdf, $chartsize, $vwh, $grafiek['Strategie']['Percentage'], '%l', $grafiek['Strategie']['Kleur'], vertaalTekst('Strategie', $this->pdf->rapport_taal), $legendaStart);
    */
    
    
  }

}
?>
