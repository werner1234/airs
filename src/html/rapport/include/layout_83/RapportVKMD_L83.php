<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");

class RapportVKMD_L83
{
	function RapportVKMD_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{

			$this->pdf = &$pdf;
			$this->pdf->rapport_type = "VKMD";
			$this->pdf->rapport_datum = db2jul($rapportageDatum);
			$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
			$this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
			$this->pdf->underlinePercentage=0.8;
			$this->pdf->rapport_titel = vertaalTekst("Vergelijkende kostenmaatstaf in",$this->pdf->rapport_taal).' '. vertaalTekst($this->pdf->valutaOmschrijvingen[$this->pdf->portefeuilledata['RapportageValuta']],$this->pdf->rapport_taal);
			$this->pdfVullen=true;
			$this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
			$this->portefeuille=$portefeuille;
    
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
    
    
    if(is_array($this->pdf->portefeuilles))
    {
      $this->portefeuilles=array($portefeuille);
      foreach($this->pdf->portefeuilles as $consolidatie)
        $this->portefeuilles[] = $consolidatie;
    }
    else
      $this->portefeuilles=array($portefeuille);
    
    $this->grootboekOmschrijvingen=array();
    
  }

	function formatGetal($waarde, $dec,$procent=false,$toonNul=true)
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



	function kostenKader()
	{
    
    $gebruikteCrmVelden = array(
      'Portefeuillesoort',
      'PortefeuilleNaam');
    
    $db = new DB();
    

    
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden = array();
    while ($data = $db->nextRecord())
    {
      $crmVelden[] = strtolower($data['Field']);
    }
    
    $nawSelect = '';
    $nietgevonden = array();
    foreach ($gebruikteCrmVelden as $veld)
    {
      if (in_array(strtolower($veld), $crmVelden))
      {
        $nawSelect .= ",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[] = $veld;
      }
    }
    
    $indirecteKosten['totaal']=array('TotCostFund'=>'','FundTransCost'=>'','FundPerfFee'=>'','totaal'=>'');
    $gebruiktePortefeuilles=array();
    $vkmPerPortefeuille=array();
    $totaleGemiddeldeWaarde=0;
    $totaleFondsGemiddeldeWaarde=0;
    $pdataPerPortefeuille=array();
    foreach ($this->portefeuilles as $portefeuille)
    {
  
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder, Portefeuilles.Startdatum $nawSelect FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $pdataPerPortefeuille[$portefeuille]=$pdata;
      if ($pdata['Portefeuillesoort'] <> 'Effecten' || $portefeuille==$this->portefeuille)//&&
      {
        continue;
      }
      $gebruiktePortefeuilles[]=$portefeuille;
  
    //  listarray($pdata);
      if(db2jul($pdata['Startdatum']) > db2jul($this->rapportageDatumVanaf))
        $pstart=substr($pdata['Startdatum'],0,10);
      else
        $pstart=$this->rapportageDatumVanaf;

      $vkm = new RapportVKM(null, $portefeuille, $pstart, $this->rapportageDatum);
      $vkm->writeRapport();
      
    
      foreach($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst'] as $kostenSoort=>$kosten)
      {
        $indirecteKosten['totaal']['totaal']+=$kosten;
        $indirecteKosten['totaal'][$kostenSoort]+=$kosten;
        $indirecteKosten[$portefeuille][$kostenSoort]+=$kosten;
        $indirecteKosten[$portefeuille]['totaal']+=$kosten;
      }
      $vkmPerPortefeuille[$portefeuille]=$vkm->vkmWaarde;
      $totaleGemiddeldeWaarde+=$vkm->vkmWaarde['gemiddeldeWaarde'];
      $totaleFondsGemiddeldeWaarde+=$vkm->vkmWaarde['fondsGemiddeldeWaarde'];
      

    }
    
    $aandeelPrameters=array('doorlopendeKostenPercentage','percentageIndirectVermogenMetKostenfactor','vkmPercentagePortefeuille','vkmWaarde');
    foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarde)
    {
      $aandeel=$vkmWaarde['gemiddeldeWaarde']/$totaleGemiddeldeWaarde;
      foreach ($vkmWaarde as $key => $value)
      {
        if (!is_array($value))
        {
          if(in_array($key,$aandeelPrameters))
          {
            $vkmPerPortefeuille['totaal'][$key] += $value*$aandeel;
            /*
            if($key=='vkmPercentagePortefeuille')
            {
              echo "som:".$vkmPerPortefeuille['totaal'][$key]." | ".($value*$aandeel)." = $value*$aandeel; portefeuille $portefeuille <br>\n";
            }
            */
          }
          else
          {
            $vkmPerPortefeuille['totaal'][$key] += $value;
          }
        }
        else
        {
          foreach ($value as $key2 => $value2)
          {
            if(!is_array($value2))
            {
              $vkmPerPortefeuille['totaal'][$key][$key2] += $value2;
            }
          }
        }
      }
    }
    
    
  //  listarray($vkmPerPortefeuille);
    
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->setAligns(array('L', 'R','R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
    $this->pdf->setWidths(array(70,20, 25, 25, 25, 25, 25, 25, 25, 25));
    $header=array('Kosten','','Totaal');
    $portefeuillesInLoop=array('totaal');
    foreach($gebruiktePortefeuilles as $portefeuille)
    {
      if($pdataPerPortefeuille[$portefeuille]['PortefeuilleNaam']<>'')
        $header[] = $pdataPerPortefeuille[$portefeuille]['PortefeuilleNaam'];
      else
        $header[] = $portefeuille;
      $portefeuillesInLoop[]=$portefeuille;
    }
    //$this->pdf->ln(2);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->row($header);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
   // $this->pdf->ln(2);
   // listarray($indirecteKosten);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $row=array('Indirecte fondskosten');
    $this->pdf->row($row);
    
    $parameters=array('TotCostFund'=>'Doorlopende kosten','FundTransCost'=>'Transactie kosten','FundPerfFee'=>'Performance fee','totaalDoorlopendekosten'=>'Totaal Indirecte fondskosten');
    
    foreach($parameters as $parameter=>$omschrijving)
    {
      $row = array($omschrijving);
      foreach ($portefeuillesInLoop as $portefeuille)
      {
        if($parameter=='totaalDoorlopendekosten')
          $indirecteKosten[$portefeuille][$parameter]=$vkmPerPortefeuille[$portefeuille]['totaalDoorlopendekosten'];
        if($portefeuille=='totaal')
        {
          $row[] = $this->formatGetal($indirecteKosten[$portefeuille][$parameter], 0);
          $row[] = $this->formatGetal($indirecteKosten[$portefeuille][$parameter]/$vkmPerPortefeuille[$portefeuille]['fondsGemiddeldeWaarde']*100, 2).'%';
        }
        else
        {
          $row[] = $this->formatGetal($indirecteKosten[$portefeuille][$parameter], 0);
        }
      }
      $this->pdf->row($row);
    }


    
    $this->pdf->setWidths(array(90, 25, 25, 25, 25, 25, 25, 25, 25));
    /*
    $row=array('Indirecte fondskosten tov onderliggend vermogen.');
    foreach($indirecteKosten as $portefeuille=>$kostenDetails)
      $row[]=$this->formatGetal($kostenDetails['totaal']/$vkmPerPortefeuille[$portefeuille]['fondsGemiddeldeWaarde']*100,3);
    $this->pdf->row($row);
    */
    /*
    $row=array('Indirecte fondskosten tov onderliggend vermogen.');
    foreach($portefeuillesInLoop as $portefeuille)
      $row[]=$this->formatGetal($vkmPerPortefeuille[$portefeuille]['doorlopendeKostenPercentage']*100,2).' %';
    $this->pdf->row($row);
    */
    $this->pdf->ln();
    $row=array('Gemiddeld vermogen in fondsen met beschikbare kosten');
    foreach($portefeuillesInLoop as $portefeuille)
      $row[]=$this->formatGetal($vkmPerPortefeuille[$portefeuille]['fondsGemiddeldeWaarde'],0);
    $this->pdf->row($row);
    /*
    $row=array('Herrekende indirecte (fonds) vermogen');
    foreach($portefeuillesInLoop as $portefeuille)
      $row[]=$this->formatGetal($vkmPerPortefeuille[$portefeuille]['doorlopendeKostenPercentage']/$vkmPerPortefeuille[$portefeuille]['percentageIndirectVermogenMetKostenfactor']*100,2).' %';
    $this->pdf->row($row);
    
    $row=array('Aandeel indirecte beleggingen');
    foreach($portefeuillesInLoop as $portefeuille)
      $row[]=$this->formatGetal($vkmPerPortefeuille[$portefeuille]['fondsGemiddeldeWaarde']/$vkmPerPortefeuille[$portefeuille]['gemiddeldeWaarde']*100,2).' %';
    $this->pdf->row($row);
    */
    $row=array('Totaal gemiddeld vermogen');
    foreach($portefeuillesInLoop as $portefeuille)
      $row[]=$this->formatGetal($vkmPerPortefeuille[$portefeuille]['gemiddeldeWaarde'],0) ;
    $this->pdf->row($row);
    
    $row=array('Indirecte fondskosten factor van de portefeuille');
    foreach($portefeuillesInLoop as $portefeuille)
    {
      $row[] = $this->formatGetal($vkmPerPortefeuille[$portefeuille]['vkmPercentagePortefeuille'], 2) . ' %';
      //$row[] = $this->formatGetal($vkmPerPortefeuille[$portefeuille]['vkmPercentagePortefeuille'], 2) . ' %';
    }
    $this->pdf->row($row);
    
    $this->pdf->ln();
    $this->pdf->row(array('Directe kosten'));
    $this->pdf->setWidths(array(90-25, 25, 25, 25, 25, 25, 25, 25, 25, 25));
    $KostenTotaalPerPortefeuille=array();
    foreach($vkmPerPortefeuille['totaal']['grootboekKosten'] as $grootboek=>$kostenTotaal)
    {
      $row=array($this->grootboekOmschrijvingen[$grootboek],$this->formatGetal($vkmPerPortefeuille['totaal']['grootboekKosten'][$grootboek],0) );
      foreach($portefeuillesInLoop as $portefeuille)
      {
        $row[] = $this->formatGetal($vkmPerPortefeuille[$portefeuille]['grootboekKosten'][$grootboek] / $vkmPerPortefeuille[$portefeuille]['gemiddeldeWaarde'] * 100, 2) . ' %';
        $KostenTotaalPerPortefeuille[$portefeuille]+=$vkmPerPortefeuille[$portefeuille]['grootboekKosten'][$grootboek];
      }
      $this->pdf->row($row);

    }
    $row=array('Totaal directe kosten',$this->formatGetal($KostenTotaalPerPortefeuille['totaal'],0) );
    foreach($KostenTotaalPerPortefeuille as $portefeuille=>$kostenTotaal)
    {
      $row[]=$this->formatGetal($KostenTotaalPerPortefeuille[$portefeuille] / $vkmPerPortefeuille[$portefeuille]['gemiddeldeWaarde'] * 100, 2) . ' %';
    }
    $this->pdf->row($row);
    
    $this->pdf->ln();
    $row=array('Vergelijkende kostenmaatstaf',$this->formatGetal($vkmPerPortefeuille['totaal']['totaalDoorlopendekosten']+$KostenTotaalPerPortefeuille['totaal'],0) );
    foreach($KostenTotaalPerPortefeuille as $portefeuille=>$kostenTotaal)
    {
      $row[]=$this->formatGetal( $vkmPerPortefeuille[$portefeuille]['vkmWaarde'], 2) . ' %';
    }
    $this->pdf->row($row);

     //listarray($indirecteKosten);
      return '';
      

	}


	function writeRapport()
  {
    global $__appvar, $USR;
    

    
    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,Portefeuilles.spreadKosten, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

    
    $query="SELECT Grootboekrekeningen.Grootboekrekening,Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen";
    $DB->SQL($query);
    $DB->Query();
    while($gb = $DB->nextRecord())
    {
      $this->grootboekOmschrijvingen[$gb['Grootboekrekening']]=$gb['Omschrijving'];
    }
    
      $this->kostenKader();
      
  }


}