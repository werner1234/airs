<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNZ_L114
{
	function RapportKERNZ_L114($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Resultaat en verloop van vermogen";//Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->clientVermogensbeheerder=array();
    $this->selectieveldPortefeuille=array();
    $this->soortOvereenkomst=array();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function portefeuilleWaarden($portefeuille,$details=false)
  {
    $portefeuilleWaarden['belCatWaarde']=array();
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum,(substr($this->rapportageDatum,5,5)=='01-01')?true:false,'EUR',$this->rapportageDatumVanaf);
    $detailData=array();
    foreach($gegevens as $waarde)
    {
      if($details==true)
      {
        if($waarde['fondsOmschrijving']<>'')
          $omschrijving=$waarde['fondsOmschrijving'];
        else
          $omschrijving=$waarde['rekening'];
        $detailData[$omschrijving]['fonds']=$waarde['fonds'];
        $detailData[$omschrijving]['rekening']=$waarde['rekening'];
        $detailData[$omschrijving]['actuelePortefeuilleWaardeEuro']=$waarde['actuelePortefeuilleWaardeEuro'];
      }
      else
      {
        $portefeuilleWaarden['totaleWaarde'] += $waarde['actuelePortefeuilleWaardeEuro'];
      }
      
    }
  
    
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatumVanaf,(substr($this->rapportageDatumVanaf,5,5)=='01-01')?true:false,'EUR',$this->rapportageDatumVanaf);
    foreach($gegevens as $waarde)
    {
      $portefeuilleWaarden['totaleWaardeBegin']+=$waarde['actuelePortefeuilleWaardeEuro'];
  
      if($waarde['fondsOmschrijving']<>'')
        $omschrijving=$waarde['fondsOmschrijving'];
      else
        $omschrijving=$waarde['rekening'];
      
      $detailData[$omschrijving]['fonds']=$waarde['fonds'];
      $detailData[$omschrijving]['rekening']=$waarde['rekening'];
      $detailData[$omschrijving]['beginPortefeuilleWaardeEuro']=$waarde['actuelePortefeuilleWaardeEuro'];
    }
  
    if($details==true)
    {
      foreach ($detailData as $omschrijving => $fondsData)
      {
        $detailData[$omschrijving] = $this->fondsPerformance($portefeuille, $fondsData, $this->rapportageDatumVanaf, $this->rapportageDatum);
      }
      return $detailData;
    }
    
    $waardeEind				= $portefeuilleWaarden['totaleWaarde'];
    $waardeBegin 			 	= $portefeuilleWaarden['totaleWaardeBegin'];
    $waardeMutatie 	   	= $waardeEind - $waardeBegin;
    $stortingen 			 	= getStortingen($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);//,$kruispost);
    $onttrekkingen 		 	= getOnttrekkingen($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);//$kruispost);
    $geldTransacties     = $stortingen-$onttrekkingen;
    $portefeuilleWaarden['stortingen']         = $stortingen;
    $portefeuilleWaarden['onttrekkingen']      = $onttrekkingen;
  
    $portefeuilleWaarden['resultaatVerslagperiode'] = $waardeMutatie - $geldTransacties;
    //echo "perf: $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen <br>\n";
    if(substr($this->rapportageDatum,0,4) < 2016)
      $perfBerekening=2;
    else
      $perfBerekening=$this->pdf->portefeuilledata['PerformanceBerekening'];
  
    $portefeuilleWaarden['rendementProcent']  	= performanceMeting($portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $perfBerekening,$this->pdf->rapportageValuta);
    return $portefeuilleWaarden;
  }
  
  function fondsPerformance($portefeuille,$fondsData,$datumBegin,$datumEind)
  {
    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');
    $bFilter=" AND Rekeningmutaties.Transactietype <> 'B' ";
    
    $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fonds'])."') ";
    $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekening'])."')  ";
    $beginwaarde =$fondsData['beginPortefeuilleWaardeEuro'];
    $eindwaarde = $fondsData['actuelePortefeuilleWaardeEuro'];
    $DB=new DB();
    if($beginwaarde==0)
    {
      $query="SELECT Rekeningmutaties.Boekdatum FROM (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                  WHERE
                 Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningFondsenWhere ORDER BY Rekeningmutaties.Boekdatum asc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $datum=$DB->nextRecord();
      $weegDatum=$datum['Boekdatum'];
    }
  
    $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 AS gewogen, ".
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
      "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' ) AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere $bFilter";
    $DB->SQL($queryAttributieStortingenOntrekkingenRekening);
    $DB->Query();
    $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
  
    $queryRekeningDirecteKostenOpbrengsten = "SELECT
                SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	             SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers),0))  AS opbrengstTotaal,
               SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1) AND Rekeningmutaties.Fonds = '' AND
	              Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere $bFilter";
    $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
    $DB->Query();
    $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
  
    $queryAttributieStortingenOntrekkingen = "SELECT ".
      "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ) )) AS gewogen, ".
      "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ))  AS totaal,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1,0))  AS storting,
	               SUM(if(Rekeningmutaties.Grootboekrekening='FONDS',ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers,0))  AS onttrekking ".
      "FROM  (Rekeningen, Portefeuilles)
	                JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
      "Rekeningmutaties.Fonds <> '' AND $rekeningFondsenWhere $bFilter";//
    $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
    $DB->Query();
    $AttributieStortingenOntrekkingen = $DB->NextRecord();
  
    $queryAttributieStortingenOntrekkingen=str_replace('Rekeningmutaties.Rekening = Rekeningen.Rekening','Rekeningmutaties.Rekening = Rekeningen.Rekening JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening',$queryAttributieStortingenOntrekkingen);
    $DB->SQL($queryAttributieStortingenOntrekkingen." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1) "); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
    $DB->Query();
    $AttributieStortingenOntrekkingenBruto = $DB->NextRecord();
   
    $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
  
    $query = "SELECT
                SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)  as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking
 	              FROM (Rekeningen, Portefeuilles)
                JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
                
	              WHERE
                Rekeningen.Portefeuille = '".$portefeuille."' AND
	              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	              Rekeningmutaties.Verwerkt = '1' AND $rekeningRekeningenWhere AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  ) $bFilter";
    $DB->SQL($query);
  
    $DB->Query();
    $data = $DB->nextRecord();
    $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
    $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
    $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
  
    if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
      $DB->SQL($query);
    else
      $DB->SQL($query." AND (Rekeningmutaties.Grootboekrekening='FONDS' OR Grootboekrekeningen.Opbrengst=1)   ");
    $DB->Query();
    $data = $DB->nextRecord();
  
    $AttributieStortingenOntrekkingenBruto['totaal'] +=$data['totaal'];
    $AttributieStortingenOntrekkingenBruto['storting'] +=$data['storting'];
    $AttributieStortingenOntrekkingenBruto['onttrekking'] +=$data['onttrekking'];
  
    $queryKostenOpbrengsten = "SELECT
          SUM(if(Grootboekrekeningen.Kosten=1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere $bFilter";
    $DB->SQL($queryKostenOpbrengsten);
    $DB->Query();
    $nietToegerekendeKosten = $DB->NextRecord();
  
  
    if(count($fondsData['rekeningen']) > 0 && $fondsData['rekeningen'][0] <> 'geen')
    {
      $AttributieStortingenOntrekkingen['totaal']+= $nietToegerekendeKosten['kostenTotaal'];
      $AttributieStortingenOntrekkingen['onttrekking']+= $nietToegerekendeKosten['kostenTotaal'];
    }

    $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
    $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'] + $RekeningDirecteKostenOpbrengsten['kostenTotaal']) / $gemiddelde);
    $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']+ $RekeningDirecteKostenOpbrengsten['kostenTotaal'];

    $portefeuilleWaarden['belCatWaarde']       = array();
    $portefeuilleWaarden['totaleWaarde']       = $eindwaarde;
    $portefeuilleWaarden['totaleWaardeBegin']  = $beginwaarde;
    $portefeuilleWaarden['stortingen']         = $AttributieStortingenOntrekkingen['storting'];
    $portefeuilleWaarden['onttrekkingen']      = $AttributieStortingenOntrekkingen['onttrekking'];
    $portefeuilleWaarden['resultaatVerslagperiode'] = $resultaat;
    $portefeuilleWaarden['rendementProcent']   = $performance*100;
    return $portefeuilleWaarden;
  }
	
	function header($categorieVolgorde,$extraW)
  {
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->SetX($this->pdf->marge);
    $this->pdf->Cell(37+$extraW, 6, vertaalTekst("Investering", $this->pdf->rapport_taal), 0, 0, "L",1);
    foreach($categorieVolgorde as $categorie=>$veld)
    {
      $this->pdf->Cell(2, 6, '', 0, 0, "C", 0);
      $this->pdf->Cell(35 + $extraW, 6,$categorie, 0, 0, "C", 1);
    }
    $this->pdf->Ln();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
  
  }
	
	
  
	function writeRapport()
	{
		global $__appvar;
		
		$DB = new DB();
   
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    

	  $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  $DB->SQL($q);
  	$DB->Query();
   	$kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    $this->pdf->grafiekKleuren=$kleuren;
    $this->categorieKleuren=$kleuren['OIB'];

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
    
    $portefeuilleWaarden=array();
    $soortOvereenkomstDetail='Intern_uit';
    
    
    if(is_array($this->pdf->portefeuilles))
    {
      $query="SELECT Portefeuille,ClientVermogensbeheerder,Selectieveld1,soortOvereenkomst  FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."') ORDER BY soortOvereenkomst desc, Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($portefeuille = $DB->NextRecord())
      {
        $this->clientVermogensbeheerder[$portefeuille['Portefeuille']]=$portefeuille['ClientVermogensbeheerder'];//$this->getCRMnaam($portefeuille['Portefeuille']);
        $this->selectieveldPortefeuille[$portefeuille['Portefeuille']]=$portefeuille['Selectieveld1'];
        $this->soortOvereenkomst[$portefeuille['Portefeuille']]=$portefeuille['soortOvereenkomst'];
      }
    }
    else
    {
      $query="SELECT Portefeuille,ClientVermogensbeheerder,Selectieveld1,soortOvereenkomst  FROM Portefeuilles WHERE Portefeuille = '".$this->portefeuille."'";
      $DB->SQL($query);
      $DB->Query();
      $portefeuille = $DB->NextRecord();
      $this->clientVermogensbeheerder[$this->portefeuille]=$portefeuille['ClientVermogensbeheerder'];
      $this->selectieveldPortefeuille[$this->portefeuille]=$portefeuille['Selectieveld1'];
      $this->soortOvereenkomst[$portefeuille['Portefeuille']]=$portefeuille['soortOvereenkomst'];
    }
    
    foreach($this->soortOvereenkomst as $portefeuille=>$so)
    {
      if($so==$soortOvereenkomstDetail)
        $portefeuilleWaarden[$portefeuille]=array();
    }
    
    asort($this->clientVermogensbeheerder);


      $aantalPortefeuilles=count($this->clientVermogensbeheerder);
      foreach($this->clientVermogensbeheerder as $portefeuille=>$client)
      {
        if($this->soortOvereenkomst[$portefeuille]==$soortOvereenkomstDetail)
          $portefeuilleWaardenDetails[$portefeuille]=$this->portefeuilleWaarden($portefeuille,true);
        
        $portefeuilleWaarden[$portefeuille]=$this->portefeuilleWaarden($portefeuille);
      }
      if($consolidatie==true)
        $portefeuilleWaardenTotaal[$this->portefeuille]=$this->portefeuilleWaarden($this->portefeuille);
   
   // listarray($this->clientVermogensbeheerder);
   // listarray($portefeuilleWaarden);
   // listarray($portefeuilleWaardenDetails);
 
  //2+35+extraw
    $categorieVolgorde=array('Beginvermogen'=>'totaleWaardeBegin','Stortingen'=>'stortingen','Onttrekkingen'=>'onttrekkingen','Resultaat'=>'resultaatVerslagperiode','Eindvermogen'=>'totaleWaarde','Rendement'=>'rendementProcent');
    $aantalCategorieen=count($categorieVolgorde);
    $paginaWidth=(35+2)+(35+2)*($aantalCategorieen);
    $maxWidth=297-$this->pdf->marge*2;
    $extraRuimte=$maxWidth-$paginaWidth;
    //echo $paginaWidth." ";
    
    $maxPortefeuilles=400;
    $extraW=$extraRuimte/($aantalCategorieen+2);
    //echo $extraW;exit;
		// voor kopjes
		$pw=14;
    $eurw=5;
		$portw=23;
		

		$this->pdf->widthA = array(30+3,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw,$eurw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data

		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
 		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);



		$regelDataTotaal=array();
		$totaalPercentage=0;

    $portefeuilleAantal=count($portefeuilleWaarden);
   // echo $portefeuilleAantal."<br>\n";listarray($portefeuilleWaarden);exit;
    
    $portrefeuilleDataPerBlok=array();
    $i=0;
		$blokken=ceil($portefeuilleAantal/$maxPortefeuilles);
		$n=1;
    foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
    {
      $portrefeuilleDataPerBlok[$i][$portefeuille]=$belCatData;
      if($n%$maxPortefeuilles==0)
        $i++;
      $n++;
    }
		
  
		for($i=0;$i<$blokken;$i++)
		{
      $portefeuilleWaarden= $portrefeuilleDataPerBlok[$i];
   
		  if($i>0)
		    $this->pdf->addPage();
			//Kop regel
			$regel = array();
  
			array_push($regel, 'Portefeuille');
	  

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
      
      $barGraph=false;

      if(1)//count($this->pdf->portefeuilles) < 60)
      {
  
        $this->header($categorieVolgorde,$extraW);
        $categorieTotalen=array();
        $clientTotalen=array();
        $lastClient='';
        foreach ($portefeuilleWaarden as $portefeuille=>$pdata)
        {
          if($this->pdf->getY()>170)
          {
            $this->pdf->addPage();
            $this->header($categorieVolgorde,$extraW);
          }
          $client=$this->clientVermogensbeheerder[$portefeuille];
          if($client<>$lastClient && isset($clientTotalen[$lastClient]))
          {
            $this->clientTotaal($clientTotalen[$lastClient],$lastClient,$extraW);
            if($this->pdf->getY()>160)
            {
              $this->pdf->addPage();
              $this->header($categorieVolgorde,$extraW);
            }
          }
          
          $this->pdf->SetX($this->pdf->marge);
          if(isset($this->selectieveldPortefeuille[$portefeuille]) && $this->selectieveldPortefeuille[$portefeuille] <> '')
            $portefeuilleTxt=$this->selectieveldPortefeuille[$portefeuille];
          else
            $portefeuilleTxt=$portefeuille;
          
          
          if(isset($portefeuilleWaardenDetails[$portefeuille]))
          {
            foreach($portefeuilleWaardenDetails[$portefeuille] as $omschrijving=>$detailData)
            {
              $this->pdf->Cell(37 + $extraW, 4, $omschrijving, 0, 0, "L", 0);
              foreach ($categorieVolgorde as $categorie => $veld)
              {
                if ($veld == 'rendementProcent')
                {
                  $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($detailData[$veld], 1) . ' %', 0, 0, "R");
                }
                else
                {
                  $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($detailData[$veld], 0), 0, 0, "R");
                }
                $clientTotalen[$client][$veld] += $detailData[$veld];
              }
              $this->pdf->ln(4);
            }
            $clientTotalen[$client]['gemiddelde']+=$portefeuilleWaarden[$portefeuille]['resultaatVerslagperiode']/$portefeuilleWaarden[$portefeuille]['rendementProcent']*100;
            
          }
          else
          {
            $this->pdf->Cell(37 + $extraW, 4, $portefeuilleTxt, 0, 0, "L", 0);
            foreach ($categorieVolgorde as $categorie => $veld)
            {
              if ($veld == 'rendementProcent')
              {
                $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($portefeuilleWaarden[$portefeuille][$veld], 1) . ' %', 0, 0, "R");
              }
              else
              {
                $this->pdf->Cell(37 + $extraW, 4, $this->formatGetal($portefeuilleWaarden[$portefeuille][$veld], 0), 0, 0, "R");
              }
              $clientTotalen[$client][$veld] += $portefeuilleWaarden[$portefeuille][$veld];
            }
          }
          $clientTotalen[$client]['gemiddelde']+=$portefeuilleWaarden[$portefeuille]['resultaatVerslagperiode']/$portefeuilleWaarden[$portefeuille]['rendementProcent']*100;
          $this->pdf->ln(4);
          $lastClient=$client;
        }
        if(isset($clientTotalen[$lastClient]))
          $this->clientTotaal($clientTotalen[$lastClient],$lastClient,$extraW);
        $this->pdf->Ln(3);
  
        $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
        $this->pdf->Cell(37+$extraW, 4,vertaalTekst('Totaal' ,$this->pdf->rapport_taal), 0, 0, "L",0);
        foreach($categorieVolgorde as $categorie=>$volgorde)
        {
          foreach($categorieVolgorde as $categorie=>$veld)
          {
            if($veld=='rendementProcent')
              $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaardenTotaal[$this->portefeuille][$veld],1).' %', 0, 0, "R");
            else
              $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($portefeuilleWaardenTotaal[$this->portefeuille][$veld],0), 0, 0, "R");
          }
        }
  
        $randomKleuren=array();
        foreach($this->pdf->grafiekKleuren['OIB'] as $categorie=>$kleur)
          $randomKleuren[]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
        $i=0;
        foreach($portefeuilleWaarden as $portefeuille=>$pData)
        {
  
          $query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
          $DB->SQL($query);
          $DB->Query();
          while($tmp = $DB->nextRecord())
            $portefeuilledata[$tmp['Portefeuille']]=$tmp;
          
          
          
          //$percentage=$portefeuilleWaarden[$portefeuille]['resultaatVerslagperiode']/$portefeuilleWaardenTotaal[$this->portefeuille]['resultaatVerslagperiode']*100;
          $percentage=$portefeuilleWaarden[$portefeuille]['rendementProcent'];
          $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($percentage,1).' %', 0, 0, "R");
  
          if($percentage<0)
            $barGraph=true;
  

          
  
          $kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
          //$kleur=array();
          if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
            $kleur = $randomKleuren[$i];
  
          if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
            $kleur = array(rand(0, 255), rand(0, 255), rand(0, 255));
          
          $categorieVerdeling['percentage'][$portefeuille]=$percentage;
          $categorieVerdeling['kleur'][]=$kleur;
          $categorieVerdeling['kleurBar'][$portefeuille]=$kleur;
  
          $i++;
        }
       // $this->pdf->Cell(37+$extraW, 4, 'aaabbb'.$this->formatGetal($totaalWaarde/$totaalWaarde*100,1).' %', 0, 1, "R");
        
     
      }
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
     // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
			
			
		}
    unset($this->pdf->CellFontStyle);
		//echo $this->pdf->getY();exit;
    if($this->pdf->getY()>105)
    {
      $this->pdf->addPage();
      $grafiekY=40;
      $grafiekH=$aantalPortefeuilles*6;
    }
    else
    {
      $grafiekY = 120;
      $grafiekH=65;
    }
    $this->pdf->setY($grafiekY-10);
    $this->pdf->SetAligns(array('C','C'));
    $this->pdf->SetWidths(array(140,140));
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
    $this->pdf->row(array(vertaalTekst("Rendementsverdeling",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    

    
    if($barGraph==false)
    {
      $this->pdf->setXY(20,$grafiekY);
      $this->PieChart(65, $grafiekH, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);
    }
    else
    {
      $this->pdf->setXY(50,$grafiekY);
      $this->BarDiagram(80, $grafiekH, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
    }
  }

  function clientTotaal($data,$client,$extraW)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
    $this->pdf->Cell(37+$extraW, 4,vertaalTekst('Totaal',$this->pdf->rapport_taal).' '.$client, 0, 0, "L",0);
    foreach($data as $categorie=>$waarde)
    {
      if($categorie=='gemiddelde' )
        continue;
      elseif($categorie=='rendementProcent')
        $this->pdf->Cell(37+$extraW, 4,''  , 0, 1, "R");
        //$this->pdf->Cell(37+$extraW, 4,$this->formatGetal($data['resultaatVerslagperiode']/$data['gemiddelde']*100,1).'%'  , 0, 1, "R");
      else
        $this->pdf->Cell(37+$extraW, 4, $this->formatGetal($waarde,0), 0, 0, "R");
    }
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
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
    
    $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
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
    
    $maxVal=ceil($maxVal/10)*10;
    if($maxVal<0)
      $maxVal=0;
  //  echo "$minVal $maxVal <br>\n ";exit;
    $offset=$minVal;
    $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
    $bandBreedte = $valIndRepere * $nbDiv;
    $lRepere = floor($lDiag / $nbDiv);
    $unit = $lDiag / $bandBreedte;
    $hBar = floor($hDiag / ($this->pdf->NbVal + 1));

    if($hBar>5)
      $hBar=5;
  
    $aantal=count($data);
    if($aantal>25)
      $hBar=4;
    
    $hDiag = $hBar * ($this->pdf->NbVal + 1);
    
    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 80 / 100);
    $legendaStep=$unit;
    
    $legendaStep=$unit/$nbDiv*$bandBreedte;
    //if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*5;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //  $legendaStep=$legendaStep*2;
    // if($bandBreedte/$legendaStep > $nbDiv)
    //   $legendaStep=$legendaStep/2*5;
    $valIndRepere=round($valIndRepere/$unit/5)*5;
  
    $color=array();
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    $nbDiv=10;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($legendaStep,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>=$XDiag; $x=$x-$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5,  $this->formatGetal(($x-$nullijn)/$unit,0),0,0,'C');
        $i++;
        if($i>100)
          break;
      }
      
      $i=0;
      //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
      for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$legendaStep)
      {
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, $this->formatGetal(($x-$nullijn)/$unit,0),0,0,'C');
        
        $i++;
        if($i>100)
          break;
      }
    }
    
    $i=0;
    
    $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->Cell($lDiag, -5, $titel,0,0,'C');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
    foreach($data as $key=>$val)
    {
      $this->pdf->SetDrawColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $hval = $eBaton;
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
      $this->pdf->SetXY($XPage, $yval);
      
      if(isset($this->selectieveldPortefeuille[$key]) && $this->selectieveldPortefeuille[$key] <> '')
        $portefeuilleTxt=$this->selectieveldPortefeuille[$key];
      else
        $portefeuilleTxt=$key;
  
      $this->pdf->Cell($legendWidth , $hval, $portefeuilleTxt.' ('.$this->formatGetal($val,0)."%)",0,0,'R');
      $i++;
    }
    
    //Scales
    $minPos=($minVal * $unit);
    $maxPos=($maxVal * $unit);
    
    $unit=($maxPos-$minPos)/$nbDiv;
    // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
    
    
  }
  
  function PieChart( $w, $h, $data, $format, $colors = null)
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $this->pdf->setDrawColor(255,255,255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0,0,0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;

   // for ($i = 0; $i < $this->pdf->NbVal; $i++)
    
    $i=0;
    foreach ($data as $key=>$val)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
      {
       // $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
        if(isset($this->selectieveldPortefeuille[$key]) && $this->selectieveldPortefeuille[$key] <> '')
          $portefeuilleTxt=$this->selectieveldPortefeuille[$key];
        else
          $portefeuilleTxt=$key;
  
        $this->pdf->Cell(0 , $hLegend, $portefeuilleTxt.' ('.$this->formatGetal($val,1)."%)",0,0,'L');
      }
      $y1 += $hLegend + $margin;
      $i++;
    }
  }



}
?>