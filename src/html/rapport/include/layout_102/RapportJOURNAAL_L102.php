<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/27 16:21:20 $
File Versie					: $Revision: 1.12 $

$Log: RapportJOURNAAL.php,v $

*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportJOURNAAL_L102
{
	function RapportJOURNAAL_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "JOURNAAL";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		if($this->pdf->rapport_JOURNAAL_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_JOURNAAL_titel;
		else
			$this->pdf->rapport_titel = "Journaal ".date("j",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst("tot en met",$this->pdf->rapport_taal)." ".
      date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
//$this->pdf->lastPOST['journaal_perRekening']=1;
    if($this->pdf->lastPOST['journaal_perRekening']==1)
		  $this->groupRekening=true;
    else
      $this->groupRekening=false;
	}
  
  function toon($getal,$teken='')
  {
    global $totalen;
    if($getal<0 && $teken=='-')
    {
      $getal=$getal*-1;
      $totalen['debet']+=$getal;
      return $this->formatGetal($getal,2);
    }
    elseif($getal>0 && $teken=='+')
    {
      $totalen['credit']+=$getal;
      return $this->formatGetal($getal,2);
    }
    else
      return $this->formatGetal(0,2);  
  }

	function toonXls($getal,$teken='')
	{
		if($getal<0 && $teken=='-')
		{
			$getal=$getal*-1;
			return round($getal,2);
		}
		elseif($getal>0 && $teken=='+')
		{
			return round($getal,2);
		}
		else
			return round(0,2);
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
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
	
	function toonTotalen($totalen)
  {
  
    $this->pdf->Row(array(vertaalTekst('Ongerealiseerde resultaten',$this->pdf->rapport_taal),$this->toon($totalen['ongerealiseerdeResultaten'],'-'),$this->toon($totalen['ongerealiseerdeResultaten'],'+')));
    $this->pdf->excelData[]=array('Ongerealiseerde resultaten',$this->toonXls($totalen['ongerealiseerdeResultaten'],'-'),$this->toonXls($totalen['ongerealiseerdeResultaten'],'+'));
    $this->pdf->Row(array(vertaalTekst('Gerealiseerde resultaten',$this->pdf->rapport_taal),$this->toon($totalen['gerealiseerdeKoersResultaat']['fonds'],'-'),$this->toon($totalen['gerealiseerdeKoersResultaat']['fonds'],'+')));
    $this->pdf->excelData[]=array('Gerealiseerde resultate',$this->toonXls($totalen['gerealiseerdeKoersResultaat']['fonds'],'-'),$this->toonXls($totalen['gerealiseerdeKoersResultaat']['fonds'],'+'));
    $this->pdf->Row(array(vertaalTekst('Valuta resultaten',$this->pdf->rapport_taal),$this->toon($totalen['gerealiseerdeKoersResultaat']['valuta'],'-'),$this->toon($totalen['gerealiseerdeKoersResultaat']['valuta'],'+')));
    $this->pdf->excelData[]=array('Valuta resultaten',$this->toonXls($totalen['gerealiseerdeKoersResultaat']['valuta'],'-'),$this->toonXls($totalen['gerealiseerdeKoersResultaat']['valuta'],'+'));

    
    if(round($totalen['koersResulaatValutas'],2) <> 0)
  {
    $this->pdf->Row(array(vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->toon($totalen['koersResulaatValutas'],'-'),$this->toon($totalen['koersResulaatValutas'],'+')));
    $this->pdf->excelData[]=array("Koersresultaten valuta's",$this->toonXls($totalen['koersResulaatValutas'],'-'),$this->toonXls($totalen['koersResulaatValutas'],'+'));
  }
  
  $this->pdf->Row(array(vertaalTekst('Mutatie opgelopen rente',$this->pdf->rapport_taal),$this->toon($totalen['mutatieRente'],'-'),$this->toon($totalen['mutatieRente'],'+')));
  $this->pdf->excelData[]=array('Mutatie opgelopen rente',$this->toonXls($totalen['mutatieRente'],'-'),$this->toonXls($totalen['mutatieRente'],'+'));
  

  }


	function writeRapport()
	{
		global $__appvar,$totalen;
		$DB = new DB();

		$this->pdf->widthA = array(80,30,30);
		$this->pdf->alignA = array('L','R','R');
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->AddPage();
    $this->pdf->templateVars['JOURNAALPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['JOURNAALPaginas']=$this->pdf->rapport_titel;

		// print categorie headers



		// haal totaalwaarde op om % te berekenen
    $perioden=array('begin'=>array($this->rapportageDatumVanaf,$this->pdf->ValutaKoersBegin),
                    'eind'=>array($this->rapportageDatum,$this->pdf->ValutaKoersEind));
    $totalen=array();  
    $totalenRekening=array();
    $totaalWaarde=array();
    foreach($perioden as $periode=>$opties)
    {
		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$opties[1]."  AS totaal,
                       SUM(beginPortefeuilleWaardeEuro)/ ".$opties[1]."  AS beginWaarde, 
                       type,rekening ".
				  		 "FROM TijdelijkeRapportage WHERE ".
				  		 " rapportageDatum ='".$opties[0]."' AND ".
					  	 " portefeuille = '".$this->portefeuille."' "
					  	 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY type,rekening";
		  debugSpecial($query,__FILE__,__LINE__);
	  	$DB->SQL($query);
	  	$DB->Query();
		  while($data = $DB->nextRecord())
      {
        $totalen[$periode][$data['type']]['eindWaarde']+=$data['totaal'];
        $totalen[$periode][$data['type']]['beginWaarde']+=$data['beginWaarde'];
  
        $totaalWaarde[$periode] += $data['totaal'];
        if($data['type']=='rekening')
        {
          $totalenRekening[$data['rekening']][$periode]['eindWaarde']+=$data['totaal'];
          $totalenRekening[$data['rekening']][$periode]['beginWaarde']+=$data['beginWaarde'];
        }  
      }
      
		}
  

		// print detail (select from tijdelijkeRapportage)
		$query = "SELECT (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro ".
			" FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
		$DB->SQL($query);
		$DB->Query();
    
		while($data = $DB->NextRecord())
  	{
        $totalen['fiscaleWaardering']+=min($data['historischeWaardeTotaalValuta'],$data['actuelePortefeuilleWaardeEuro']);
        $totalen['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
		}
    $totalen['balanswaardeEffectenFISCAAL']=$totalen['eind']['fondsen']['eindWaarde']-$totalen['fiscaleWaardering'];
    $opgelopenRente=$totalen['eind']['rente']['eindWaarde']-$totalen['begin']['rente']['eindWaarde'];
    $totalen['balanswaardeEffectenVOLK']=$totalen['eind']['fondsen']['eindWaarde']-$totalen['begin']['fondsen']['eindWaarde']+$opgelopenRente;
    $totalen['mutatieBanksaldo']=$totalen['eind']['rekening']['eindWaarde']-$totalen['begin']['rekening']['eindWaarde'];
    $totalen['mutatieRente']=$totalen['eind']['rente']['eindWaarde']-$totalen['begin']['rente']['eindWaarde'];
    $totalen['ongerealiseerdeResultaten']=$totalen['eind']['fondsen']['eindWaarde']-$totalen['eind']['fondsen']['beginWaarde'];
  	$totalen['gerealiseerdeKoersResultaat'] = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta,true,'Totaal',true);
  	$totalen['stortingen'] 		  	 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$totalen['onttrekkingen'] 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->Row(array(vertaalTekst('Grootboekrekening',$this->pdf->rapport_taal),vertaalTekst('Debet',$this->pdf->rapport_taal),vertaalTekst('Credit',$this->pdf->rapport_taal)));
		$this->pdf->excelData[]=array('Grootboekrekening','Debet','Credit');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($this->groupRekening==true)
    {
      $extraGroup = "Rekeningmutaties.Rekening,Rekeningmutaties.Transactietype,";
      $extraInput="OR Grootboekrekeningen.FondsAanVerkoop = 1 OR Grootboekrekeningen.storting=1 OR Grootboekrekeningen.onttrekking=1 OR Grootboekrekeningen.kruispost=1";
    }
		else
    {
      $extraGroup = "";
      $extraInput = "";
    }
		if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";
		$query = "SELECT Rekeningmutaties.Transactietype,
		SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)*-1 AS totaaldebet,
     Rekeningmutaties.Grootboekrekening ,Grootboekrekeningen.Omschrijving,Rekeningmutaties.Rekening  ".
		"FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening 
     JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening".
   	" WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
	 	"Rekeningmutaties.Verwerkt = '1' AND ".
	 	"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1 $extraInput ) AND ".
	 	"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' GROUP BY $extraGroup Rekeningmutaties.Grootboekrekening ORDER BY $extraGroup Grootboekrekeningen.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
    
    $totaalOpbrengst=$totalen['ongerealiseerdeResultaten']+$totalen['gerealiseerdeKoersResultaat']['totaal'];
    //echo $totalen['ongerealiseerdeResultaten']."+".$totalen['gerealiseerdeKoersResultaat']['totaal'];exit;
    $totaalKosten=0;
    $laatsteRekening='';
    if($this->groupRekening==true)
      $this->pdf->ln();
    $rekeningData=array();
		while($data = $DB->NextRecord())
  	{
  		if($this->groupRekening==true)
      {
      	if($data['Grootboekrekening']=='FONDS')
        {
          if (in_array($data['Transactietype'], array('V', 'V/S', 'A/S', 'L')))
          {
            $data['Grootboekrekening'] = 'FONDSV';
            $data['Omschrijving']='Waarde-mutatie effecten';
          }
          elseif (in_array($data['Transactietype'], array('A','V/O','A/O','D')))
          {
            $data['Grootboekrekening'] = 'FONDSA';
            $data['Omschrijving']='Waarde-mutatie effecten';
          }
        }
      }
      else
			{
        $data['Rekening'] = 'leeg';
			}
  		
      $rekeningData[$data['Rekening']][$data['Grootboekrekening']]['totaalcredit']+=$data['totaalcredit'];
      $rekeningData[$data['Rekening']][$data['Grootboekrekening']]['totaaldebet']+=$data['totaaldebet'];
      $rekeningData[$data['Rekening']][$data['Grootboekrekening']]['Omschrijving']=$data['Omschrijving'];
    
      $totaalOpbrengst += $data['totaalcredit'];
      $totaalKosten += $data['totaaldebet'];
		}
    
    $resultaatVerslagperiode = ($totaalWaarde['eind']-$totaalWaarde['begin']) - $totalen['stortingen']  + $totalen['onttrekkingen'];
    $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  +  $totaalKosten)-$opgelopenRente;
    //echo "JOURNAAL koersResulaatValutas = $koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  +  $totaalKosten); <br>\n";exit;
    $totalen['koersResulaatValutas']=$koersResulaatValutas;
    
		//listarray($rekeningData);exit;
    $toonTotalen=true;
		foreach($rekeningData as $rekening=>$grootboekDataPerRekening)
		{
      foreach($grootboekDataPerRekening as $grootboek=>$grootboekdata)
      {
        if ($this->groupRekening == true && $rekening <> $laatsteRekening)
        {
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->Row(array($rekening));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
        }
        if($toonTotalen==true && substr($rekening,-3)=='MEM')
        {
          $this->toonTotalen($totalen);
          $toonTotalen=false;
        }
        $this->pdf->Row(array(vertaalTekst($grootboekdata['Omschrijving'], $this->pdf->rapport_taal), $this->toon($grootboekdata['totaaldebet'], '-'), $this->toon($grootboekdata['totaalcredit'], '+')));
        $this->pdf->excelData[] = array($grootboekdata['Omschrijving'], $this->toonXls($grootboekdata['totaaldebet'], '-'), $this->toonXls($grootboekdata['totaalcredit'], '+'));
        $laatsteRekening = $rekening;
      }
      
      if ($this->groupRekening == true && isset($totalenRekening[$rekening]))
      {
        $saldi = $totalenRekening[$rekening];
        $mutatie = ($saldi['eind']['eindWaarde'] - $saldi['begin']['eindWaarde']) * -1;
        $this->pdf->Row(array(vertaalTekst('Mutatie banksaldo', $this->pdf->rapport_taal) . ' ' . $rekening, $this->toon($mutatie, '-'), $this->toon($mutatie, '+')));
        $this->pdf->excelData[] = array('Mutatie banksaldo ' . $rekening, $this->toonXls($mutatie, '-'), $this->toonXls($mutatie, '+'));
      }
      
		}
		
    if($this->groupRekening==true)
      $this->pdf->ln();
 
    
    if($toonTotalen==true)
    {
      $this->toonTotalen($totalen);
      $this->pdf->ln();
      $this->pdf->excelData[]=array();
    }
    
    $this->pdf->Row(array(vertaalTekst('Onttrekkingen',$this->pdf->rapport_taal),$this->toon($totalen['onttrekkingen']*-1,'-'),$this->toon($totalen['onttrekkingen']*-1,'+')));
    $this->pdf->excelData[]=array('Onttrekkingen',$this->toonXls($totalen['onttrekkingen'],'-'),$this->toonXls($totalen['onttrekkingen'],'+'));
    
    $this->pdf->Row(array(vertaalTekst('Stortingen',$this->pdf->rapport_taal),$this->toon($totalen['stortingen'],'-'),$this->toon($totalen['stortingen'],'+')));
    $this->pdf->excelData[]=array('Stortingen',$this->toonXls($totalen['stortingen'],'-'),$this->toonXls($totalen['stortingen'],'+'));
    
    $this->pdf->Row(array(vertaalTekst('Balanswaarde effecten',$this->pdf->rapport_taal),$this->toon($totalen['balanswaardeEffectenVOLK']*-1,'-'),$this->toon($totalen['balanswaardeEffectenVOLK']*-1,'+')));
    $this->pdf->excelData[]=array('Balanswaarde effecten',$this->toonXls($totalen['balanswaardeEffectenVOLK']*-1,'-'),$this->toonXls($totalen['balanswaardeEffectenVOLK']*-1,'+'));
    
    if ($this->groupRekening == false)
    {
      foreach ($totalenRekening as $rekening => $saldi)
      {
        $mutatie = ($saldi['eind']['eindWaarde'] - $saldi['begin']['eindWaarde']) * -1;
        $this->pdf->Row(array(vertaalTekst('Mutatie banksaldo', $this->pdf->rapport_taal) . ' ' . $rekening, $this->toon($mutatie, '-'), $this->toon($mutatie, '+')));
        $this->pdf->excelData[] = array('Mutatie banksaldo ' . $rekening, $this->toonXls($mutatie, '-'), $this->toonXls($mutatie, '+'));
      }
    }
    $this->pdf->ln();
		$this->pdf->excelData[]=array();
		$this->pdf->excelData[]=array('Totaal',$this->toonXls($totalen['debet']*-1,'-'),$this->toonXls($totalen['credit'],'+'));
    $this->pdf->Row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->toon($totalen['debet']*-1,'-'),$this->toon($totalen['credit'],'+')));


   $this->pdf->ln();
	 $this->pdf->excelData[]=array();
   $totalen['debet']=0;
	 $totalen['credit']=0;
   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   $this->pdf->Row(array(vertaalTekst('Boeking voor de fiscale waardering',$this->pdf->rapport_taal)));
	 $this->pdf->excelData[]=array('Boeking voor de fiscale waardering');
   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   $this->pdf->Row(array(vertaalTekst('Balanswaarde effecten',$this->pdf->rapport_taal),$this->toon(0),$this->toon($totalen['balanswaardeEffectenFISCAAL'],'+')));
	 $this->pdf->excelData[]=array('Balanswaarde effecten',$this->toonXls(0),$this->toonXls($totalen['balanswaardeEffectenFISCAAL'],'+'));

   $this->pdf->Row(array(vertaalTekst('Ongerealiseerde resultaten',$this->pdf->rapport_taal),$this->toon($totalen['balanswaardeEffectenFISCAAL']*-1,'-'),$this->toon(0)));
	 $this->pdf->excelData[]=array('Ongerealiseerde resultaten',$this->toonXls($totalen['balanswaardeEffectenFISCAAL']*-1,'-'),$this->toonXls(0));

	 $this->pdf->ln();
   $this->pdf->excelData[]=array();

	 $this->pdf->excelData[]=array('Totaal',$this->toonXls($totalen['debet']*-1,'-'),$this->toonXls($totalen['credit'],'+'));
   $this->pdf->Row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),$this->toon($totalen['debet']*-1,'-'),$this->toon($totalen['credit'],'+')));

	}
}
?>
