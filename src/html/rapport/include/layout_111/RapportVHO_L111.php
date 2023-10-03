<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVHO_L111
{
	function RapportVHO_L111($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "VHO";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

			$this->pdf->rapport_titel = "Totaal historisch overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->underlinePercentage=0.8;
    $this->pdf->excelData[]=array('Category',"Current shares","Current share price (local)","Avg costs price (local)","Proceeds adj cost price (local)","Share price return","Total costs price (EUR)","Current market value (EUR)","Cumulative dividends (EUR)","Total sale proceeds (EUR)","Total result (EUR)","Total return (EUR) in %","Annualised return (EUR) in %");
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  function getVerkopen()
  {
    $DB = new DB();
    $query="SELECT
Rekeningmutaties.Fonds,
Sum(Rekeningmutaties.Aantal) AS aantal,
Sum((Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)-(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)) AS waarde,
Rekeningmutaties.Boekdatum
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningen.Portefeuille='".$this->portefeuille."' AND SUBSTRING(Rekeningmutaties.Transactietype,1,1)='V' AND Rekeningmutaties.Boekdatum <='".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Fonds";
    $DB->SQL($query);
    $DB->Query();
    $verkopen=array();
    while($data=$DB->nextRecord())
    {
      $verkopen[$data['Fonds']]=$data;
    }
    return $verkopen;
  }
  
  function getAanVerkopen()
  {
    $DB = new DB();
//Sum(Rekeningmutaties.Aantal*Rekeningmutaties.Fondskoers*Rekeningmutaties.Valutakoers) AS waarde,
    $query="SELECT
Rekeningmutaties.Fonds,
Rekeningen.Portefeuille,
(Rekeningmutaties.Aantal) AS aantal,
Rekeningmutaties.Transactietype,
(Rekeningmutaties.Credit-Rekeningmutaties.Debet) AS waardeValuta,
((Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)-(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)) AS waarde,
(Rekeningmutaties.Bedrag) as bedrag,
CASE
    WHEN Rekeningmutaties.Transactietype ='V' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='V/S' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='A/S' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='L' THEN \"A\"
    ELSE \"A\"
END as Transactietype2
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype IN('A','A/O','A/V','D', 'V','V/S','A/S','L','B','V/O') AND Rekeningmutaties.Boekdatum <='".$this->rapportageDatum."'
ORDER BY Transactietype2,	Rekeningmutaties.Fonds, Rekeningmutaties.Boekdatum";
    $DB->SQL($query);
    $DB->Query();
    $transacties=array();
  
    //$verkopen=$aanverkopen['V'];//+$aanverkopen['V/S']+$aanverkopen['A/S']+$aanverkopen['L'];
    //$aankopen=$aanverkopen['A'];//+$aanverkopen['A/O']+$aanverkopen['V/O']+$aanverkopen['D'];
    while($data=$DB->nextRecord())
    {
      if(!isset($transacties[$data['Transactietype2']][$data['Fonds']]))
      {
        $transacties[$data['Transactietype2']][$data['Fonds']] = $data;
      }
      else
      {
        if($data['Transactietype'] <> 'B')
        {
          $transacties[$data['Transactietype2']][$data['Fonds']]['aantal'] += $data['aantal'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['waardeValuta'] += $data['waardeValuta'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['waarde'] += $data['waarde'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['bedrag'] += $data['bedrag'];
        }
      }
    }
    //listarray($transacties);
    return $transacties;

  }
  
  function getBoekingen()
  {
    $DB = new DB();
    $query="SELECT
Rekeningmutaties.Fonds,
Min(Rekeningmutaties.Boekdatum) AS BoekdatumEerste,
Max(Rekeningmutaties.Boekdatum) AS BoekdatumLaatste,
Fondsen.Omschrijving AS fondsOmschrijving,
Fondsen.Valuta,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as BeleggingscategorieOmschrijving
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
INNER JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN KeuzePerVermogensbeheerder ON BeleggingscategoriePerFonds.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' AND KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
INNER JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum <='".$this->rapportageDatum."'
GROUP BY
	Rekeningmutaties.Fonds
ORDER BY
	KeuzePerVermogensbeheerder.Afdrukvolgorde,Fondsen.Omschrijving";
    $DB->SQL($query);
    $DB->Query();
    $boekdatum=array();
    while($data=$DB->nextRecord())
    {
      $boekdatum[$data['Fonds']]=$data;
    }
    return $boekdatum;
  }
  
  function getDividend($fonds,$huidigAantal=0,$fondsValuta='EUR')
  {
    global $__appvar;
    
    if($fonds=='')
      return 0;
      
     $query="SELECT rapportageDatum,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as actuelePortefeuilleWaardeEuro,
         TijdelijkeRapportage.type,
         TijdelijkeRapportage.totaalAantal
     FROM TijdelijkeRapportage
     WHERE 
       TijdelijkeRapportage.fonds='$fonds' AND
       portefeuille = '".$this->portefeuille."'".$__appvar['TijdelijkeRapportageMaakUniek']. "
     GROUP BY rapportageDatum,TijdelijkeRapportage.type";
  
     $DB = new DB();
  	 $DB->SQL($query); 
		 $DB->Query();
     $totaal=0;
     
     while($data = $DB->nextRecord())
     { 
       if($data['type']=='rente')
         $rente[$data['rapportageDatum']]=$data['actuelePortefeuilleWaardeEuro'];
     //  elseif($data['type']=='fondsen')
     //    $aantal[$data['rapportageDatum']]=$data['totaalAantal'];
     }
     
     $totaal+=($rente[$this->rapportageDatum]-$rente[$this->rapportageDatumVanaf]);
     $totaalValuta=$totaal;
     $totaalCorrected=$totaal;
     
     

     $query="SELECT Boekdatum,(Debet*Valutakoers) as Debet,(Credit*valutakoers) as Credit,Bedrag,Rekeningmutaties.Omschrijving ,Grootboekrekeningen.Opbrengst,Rekeningmutaties.Valuta
     FROM Rekeningmutaties 
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
     WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND ".
//     " Rekeningmutaties.Boekdatum >= '".	$this->rapportageDatumVanaf."' AND ".
     " Rekeningmutaties.Boekdatum <= '".	$this->rapportageDatum."' AND
     Rekeningmutaties.Fonds='$fonds' ";
		$DB->SQL($query); 
		$DB->Query();
    //echo "$query <br>\n";
    $helftDatum='';
    while($data = $DB->nextRecord())
    {
  
      
      $boekdatum=substr($data['Boekdatum'],0,10);
      if(!isset($aantal[$data['Boekdatum']]))
      {
        if(!isset($aantal[$boekdatum]))
          $fondsAantal=fondsAantalOpdatum($this->portefeuille,$fonds,$data['Boekdatum']);
        //echo "if(".$fondsAantal['totaalAantal'].">=$huidigAantal/2 && $helftDatum=='') <br>\n";
        if($huidigAantal>=0 && $fondsAantal['totaalAantal']>=$huidigAantal/2 && $helftDatum=='')
          $helftDatum=$data['Boekdatum'];
        $aantal[$boekdatum]=$fondsAantal['totaalAantal'];
      }
      $aandeel=1;
      if($data['Opbrengst']==1)
      {
        if ($aantal[$boekdatum] > $aantal[$this->rapportageDatum])
        {
          $aandeel = $aantal[$this->rapportageDatum] / $aantal[$boekdatum];
        }
        // echo "$fonds $aandeel  $boekdatum ".$this->rapportageDatum." ".($data['Credit']-$data['Debet'])."<br>\n";
  
   //  ($data['Credit'] - $data['Debet'])  delen door  valutakoers fondsen.valuta op datum rekeningmutatie
        $totaal += ($data['Credit'] - $data['Debet']);
        
        if ($data['Debet'] != $fondsValuta)
        {
          $totaalValuta += ($data['Credit'] - $data['Debet']) / getValutaKoers($fondsValuta,$boekdatum);
        }
        else
        {
          $totaalValuta += ($data['Credit'] - $data['Debet']);
        }
        
        
        $totaalCorrected += (($data['Credit'] - $data['Debet']) * $aandeel);
      }
    }
    
    return array('totaal'=>$totaal/$this->pdf->ValutaKoersEind,'totaalValuta'=>$totaalValuta/$this->pdf->ValutaKoersEind,'corrected'=>$totaalCorrected/$this->pdf->ValutaKoersEind,'helftDatum'=>$helftDatum);
  }


	function writeRapport()
	{
		global $__appvar;

		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$w=($this->pdf->w-45-$this->pdf->marge*2)/13;
		
		$this->pdf->widthA = array(45,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $aanverkopen=$this->getAanVerkopen();
    
    $verkopen=$aanverkopen['V'];//+$aanverkopen['V/S']+$aanverkopen['A/S']+$aanverkopen['L'];
    $aankopen=$aanverkopen['A'];//+$aanverkopen['A/O']+$aanverkopen['V/O']+$aanverkopen['D'];
    $boekdatum=$this->getBoekingen();
    $fondsData=$boekdatum;
    //foreach($boekdatum as $fonds=>$boekingen)
    //  $fondsData[$fonds]=array();
    
    		$subquery = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving, TijdelijkeRapportage.fondsOmschrijving, ".
				" TijdelijkeRapportage.fonds, ".
				" TijdelijkeRapportage.actueleValuta, ".
				" TijdelijkeRapportage.totaalAantal, ".
          " TijdelijkeRapportage.fondseenheid, ".
				" TijdelijkeRapportage.beginwaardeLopendeJaar , ".
        " TijdelijkeRapportage.historischeWaarde, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
				" TijdelijkeRapportage.Valuta, ".
				" TijdelijkeRapportage.beginPortefeuilleWaardeEuro  as beginPortefeuilleWaardeEuro,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal,
(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalEUR,
TijdelijkeRapportage.actueleFonds,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
				 TijdelijkeRapportage.actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersEind. " as actuelePortefeuilleWaardeEuro ,
				 TijdelijkeRapportage.beleggingscategorie,
				 TijdelijkeRapportage.beleggingscategorieOmschrijving,
				 TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
				" FROM TijdelijkeRapportage WHERE ".
				" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type =  'fondsen' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,TijdelijkeRapportage.beleggingscategorie asc,TijdelijkeRapportage.fondsOmschrijving asc";//exit;
			
			// print detail (select from tijdelijkeRapportage)
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();
//echo $subquery."<br><br>";exit;
      $regels=$DB2->records();
      $n=0;
      $kopPrinted=false;
			while($subdata = $DB2->NextRecord())
      {
        $fondsData[$subdata['fonds']]=$subdata;
      }
      $lastCategorie='';
    $totaalcat=array();
    $totaal=array();
      foreach($fondsData as $fondsKey=>$subdata)
      {
  
        if($subdata['totaalAantal'] == 0)
        {
          //  listarray($subdata);
          $historie = berekenHistorischKostprijs($this->portefeuille, $subdata['Fonds'],$subdata['BoekdatumLaatste'],$this->pdf->rapportageValuta,$subdata['BoekdatumEerste'],'');
          $subdata['fonds']=$subdata['Fonds'];
          $subdata['historischeWaarde']=$historie['historischeWaarde'];
        //  $subdata['actueleFonds']=$historie['actueleFonds'];
          $subdata['fondsEenheid']=$historie['fondsEenheid'];
        //  $subdata['actuelePortefeuilleWaardeEuro']=10000;
          // listarray($historie);
        }
        
        if($subdata['BeleggingscategorieOmschrijving']<>$lastCategorie)
        {
          if($n>0)
          {
            $this->pdf->CellBorders = array('','','','','','','','SUB','SUB','SUB','SUB','SUB');
            $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
            $this->pdf->row(array(vertaalTekst("Totaal", $this->pdf->rapport_taal) . ' ' . vertaalTekst($lastCategorie, $this->pdf->rapport_taal),'','','','', '','',
                              $this->formatGetal($totaalcat['historischeWaardeTotaalEUR'], $this->pdf->rapport_VOLK_decimaal),
                              $this->formatGetal($totaalcat['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),//F
                              $this->formatGetal($totaalcat['dividend'], $this->pdf->rapport_VOLK_decimaal),//G
                              $this->formatGetal($totaalcat['verkoopWaarde'], $this->pdf->rapport_VOLK_decimaal), //H
                              $this->formatGetal($totaalcat['result'], $this->pdf->rapport_VOLK_decimaal)));
            $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
            $totaalcat=array();
            $this->pdf->ln();
            unset($this->pdf->CellBorders);
          }
          $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
          $this->pdf->row(array(($subdata['BeleggingscategorieOmschrijving']<>''?vertaalTekst($subdata['BeleggingscategorieOmschrijving'], $this->pdf->rapport_taal):vertaalTekst($subdata['Beleggingscategorie'], $this->pdf->rapport_taal))));
          $lastCategorie=$subdata['BeleggingscategorieOmschrijving'];
          $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
        }
        $subdata['historischeWaardeTotaalEUR']=$aankopen[$subdata['fonds']]['waarde']*-1;
        if ($n > $regels - 2 && $this->pdf->GetY() > 160)//
        {
          $this->pdf->AddPage();
        }
     
        $dividend = $this->getDividend($subdata['fonds'],$subdata['totaalAantal'],$subdata['Valuta']);
        $dividendtxt = '';
        if ($dividend['totaal'] <> 0)
        {
          $dividendtxt = $this->formatGetal($dividend['totaal'], $this->pdf->rapport_VOLK_decimaal);
        }
        
        $this->pdf->SetWidths($this->pdf->widthA);
        $this->pdf->SetAligns($this->pdf->alignA);
        
        // print fondsomschrijving appart ivm met apparte fontkleur
        $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'], $this->pdf->rapport_fonds_fontcolor['g'], $this->pdf->rapport_fonds_fontcolor['b']);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->Cell($this->pdf->widthA[0], 4, $subdata['fondsOmschrijving'], null, null, null, null, null);
        $this->pdf->setX($this->pdf->marge);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
        $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        
        //$this->formatGetal($subdata['totaalAantal'], $this->pdf->rapport_VOLK_aantal_decimaal),
				$verkoopWaarde=$verkopen[$subdata['fonds']]['waarde'];
				if($verkoopWaarde<>0)
					$verkoopTxt= $this->formatGetal($verkoopWaarde, $this->pdf->rapport_VOLK_decimaal);
				else
					$verkoopTxt='';
				
				$result = $subdata['actuelePortefeuilleWaardeEuro'] + $dividend['totaal'] + $verkoopWaarde - $subdata['historischeWaardeTotaalEUR'];
        $resultProcent = (($subdata['actuelePortefeuilleWaardeEuro'] + $dividend['totaal'] + $verkoopWaarde) / $subdata['historischeWaardeTotaalEUR'])-1;
        
        //echo "$resultProcent = (".$subdata['actuelePortefeuilleWaardeEuro']." + ".$dividend['totaal']." + $verkoopWaarde) / ".$subdata['historischeWaardeTotaalEUR']."<br>\n";exit;
        if(isset($boekdatum[$subdata['fonds']]['BoekdatumEerste']))
        {
          if(isset($dividend['helftDatum']))
            $eersteBoeking = $dividend['helftDatum'];
          else
            $eersteBoeking = $boekdatum[$subdata['fonds']]['BoekdatumEerste'];
          if($subdata['totaalAantal']==0)
            $laatsteDag= $boekdatum[$subdata['fonds']]['BoekdatumLaatste'];
          else
            $laatsteDag=$this->rapportageDatum;
          $dagen= round((db2jul($laatsteDag)-db2jul($eersteBoeking))/86400,0);
        }
        else
          $dagen=0;
        
        if($dagen>0)
				{
          //$resultProcentJaar=$resultProcent/(pow(1+$resultProcent,$dagen/365));
          
          $resultProcentJaar=(pow(1+$resultProcent, (365.25 / $dagen))-1);
          
          //echo "$resultProcentJaar=(pow(1+$resultProcent, (365 / $dagen))-1)*100;";exit;
          $resultProcentJaarTxt=$this->formatGetal($resultProcentJaar*100, 2).'%';
				}
				else
				{
          $resultProcentJaarTxt='';
				}
  

        $procAdjCostPrice=($aankopen[$subdata['fonds']]['waardeValuta']*-1-$dividend['totaalValuta']-$verkopen[$subdata['fonds']]['waardeValuta'])/$subdata['totaalAantal']/$subdata['fondseenheid'];
				/*
        if($dividend['totaal'] <> 0)
        {
          echo $subdata['fondsOmschrijving'] . " | $procAdjCostPrice=(" . $aankopen[$subdata['fonds']]['waardeValuta'] . "*-1-" . $dividend['totaal'] . "-" . $verkopen[$subdata['fonds']]['waardeValuta'] . ")/" . $subdata['totaalAantal'] . "/" . $subdata['fondseenheid'] . "<br>\n";
          listarray($dividend);
          echo "-------- <br>\n";
        }
				*/
				$sharePriceReturn=($subdata['actueleFonds']/($subdata['historischeWaarde']- ($dividend['totaal']/$subdata['totaalAantal'])))-1;
				if($sharePriceReturn==-1)
          $sharePriceReturn=0;
				
      //  echo $aankopen[$subdata['fonds']]['waardeValuta']."-".$dividend['totaal']."/".$aankopen[$subdata['fonds']]['aantal']."<br>\n";exit;
      //  echo $subdata['fondsOmschrijving']."=>".($subdata['historischeWaarde']-($dividend['totaal']/$subdata['totaalAantal']))."=".$subdata['historischeWaarde']."-(".$dividend['totaal']."/".$subdata['totaalAantal'].").<br>\n";
        $this->pdf->row(array("",$subdata['Valuta'],
                          $this->formatGetal($subdata['totaalAantal'],0),//current shares
                          $this->formatGetal($subdata['actueleFonds'],2),//A current share price local
                          $this->formatGetal($subdata['historischeWaarde'], 2),//B avg costs price local //- ($dividend['totaal']/$subdata['totaalAantal'])
                          $this->formatGetal($procAdjCostPrice, 2),//C proceeds adj cost price local
                          $this->formatGetal($sharePriceReturn*100, 2)."%",//D share price return
                          $this->formatGetal($subdata['historischeWaardeTotaalEUR'], $this->pdf->rapport_VOLK_decimaal),//E Total costs price (EUR)
                          $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),//F
                          $dividendtxt,//G
					                $verkoopTxt, //H
                          $this->formatGetal($result, $this->pdf->rapport_VOLK_decimaal),//I
                          $this->formatGetal($resultProcent*100, 2).'%',//J
                          $resultProcentJaarTxt));
				
				$totaalcat['historischeWaardeTotaalEUR']+=$subdata['historischeWaardeTotaalEUR'];
        $totaalcat['actuelePortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
        $totaalcat['dividend']+=$dividend['totaal'];
        $totaalcat['verkoopWaarde']+=$verkoopWaarde;
        $totaalcat['result']+=$result;
  
        $totaal['historischeWaardeTotaalEUR']+=$subdata['historischeWaardeTotaalEUR'];
        $totaal['actuelePortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
        $totaal['dividend']+=$dividend['totaal'];
        $totaal['verkoopWaarde']+=$verkoopWaarde;
        $totaal['result']+=$result;
        
        $this->pdf->excelData[]=array($subdata['BeleggingscategorieOmschrijving'],$subdata['fondsOmschrijving'],round($subdata['totaalAantal'],0),
          round($subdata['actueleFonds'],2),//A
          round($subdata['historischeWaarde']- ($dividend['totaal']/$subdata['totaalAantal']), 2),//B
          round(($aankopen[$subdata['fonds']]['waardeValuta']*-1-
                               $dividend['totaal']-
                               $verkopen[$subdata['fonds']]['waardeValuta'])/$aankopen[$subdata['fonds']]['aantal'], 2),//C
          round(($subdata['actueleFonds']/($subdata['historischeWaarde']- ($dividend['totaal']/$subdata['totaalAantal'])))-1, 2),//D
          round($subdata['historischeWaardeTotaalEUR'], $this->pdf->rapport_VOLK_decimaal),//E
          round($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),//F
          round( $dividend['totaal'],2),//G
          round($verkoopWaarde,2), //H
          round($result, $this->pdf->rapport_VOLK_decimaal),//I
          round($resultProcent*100, 2).'%',//J
          round($resultProcentJaar*100,2));
        $n++;
      }
    
    $this->pdf->CellBorders = array('','','','','','','','SUB','SUB','SUB','SUB','SUB');
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Totaal", $this->pdf->rapport_taal). ' ' . vertaalTekst($lastCategorie, $this->pdf->rapport_taal),'','','','', '','',
                      $this->formatGetal($totaalcat['historischeWaardeTotaalEUR'], $this->pdf->rapport_VOLK_decimaal),
                      $this->formatGetal($totaalcat['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),//F
                      $this->formatGetal($totaalcat['dividend'], $this->pdf->rapport_VOLK_decimaal),//G
                      $this->formatGetal($totaalcat['verkoopWaarde'], $this->pdf->rapport_VOLK_decimaal), //H
                      $this->formatGetal($totaalcat['result'], $this->pdf->rapport_VOLK_decimaal)));
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    
    $this->pdf->ln();
    $this->pdf->CellBorders = array('','','','','','','','UU','UU','UU','UU','UU');
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Totaal", $this->pdf->rapport_taal),'','','', '','','',
                      $this->formatGetal($totaal['historischeWaardeTotaalEUR'], $this->pdf->rapport_VOLK_decimaal),
                      $this->formatGetal($totaal['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_VOLK_decimaal),//F
                      $this->formatGetal($totaal['dividend'], $this->pdf->rapport_VOLK_decimaal),//G
                      $this->formatGetal($totaal['verkoopWaarde'], $this->pdf->rapport_VOLK_decimaal), //H
                      $this->formatGetal($totaal['result'], $this->pdf->rapport_VOLK_decimaal)));
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);

	}
}
?>
