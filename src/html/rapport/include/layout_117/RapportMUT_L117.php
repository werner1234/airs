<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportMUT_L117
{

	function RapportMUT_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille=$portefeuille;
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;
    
    $this->pdf->rapport_titel = "Overzicht stortingen en onttrekkingen";
	}
  
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  
  function testTxtLength($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }
  
  function header($type)
  {
    if($type=='cash')
    {
      $this->pdf->setAligns(array("L", 'R', 'R', 'R', 'R', 'R'));
      $this->pdf->fillCell = array(1, 1, 1, 1, 1, 1);
  
      $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0], $this->pdf->rapport_donkergroen[1], $this->pdf->rapport_donkergroen[2]);
      $this->pdf->rowHeight = $this->pdf->rapport_lowRow;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
      $this->pdf->SetWidths(array(130, 30, 30, 30, 30, 30));
      $this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),297-$this->pdf->marge*2-1,20,'F');
      $this->pdf->Row(array("\n ", vertaalTekst("Valuta\n ", $this->pdf->rapport_taal), vertaalTekst("Waarde\n ", $this->pdf->rapport_taal), vertaalTekst("Wisseloers\n ", $this->pdf->rapport_taal), vertaalTekst("Waarde in EUR\n ", $this->pdf->rapport_taal), vertaalTekst("Valutadatum\n ", $this->pdf->rapport_taal)));
      $this->pdf->Row(array("\n ", "\n ", "\n ", "\n ", "\n ", "\n "));
    }
    elseif($type=='inkomsten')
    {
      $this->pdf->setAligns(array("L", "L", 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
//      $this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
      $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0], $this->pdf->rapport_donkergroen[1], $this->pdf->rapport_donkergroen[2]);
      $this->pdf->rowHeight = $this->pdf->rapport_lowRow;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
      $this->pdf->SetWidths(array(60, 40, 23, 23, 23, 23, 22, 23, 23, 20));
      $this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),297-$this->pdf->marge*2-1,21,'F');
      $this->pdf->Row(array(vertaalTekst("Inkomsten/Kosten\n \n ", $this->pdf->rapport_taal), vertaalTekst("Soort\n \n ", $this->pdf->rapport_taal), vertaalTekst("Aantal\nNominale\n waarde", $this->pdf->rapport_taal), vertaalTekst("Bruto*\n \n ", $this->pdf->rapport_taal), vertaalTekst("Valuta\n \n ", $this->pdf->rapport_taal), vertaalTekst("Bruto\n \n ", $this->pdf->rapport_taal), vertaalTekst("Ingehouden\nkosten\n ", $this->pdf->rapport_taal), vertaalTekst("Ingehouden\nbelasting\n ", $this->pdf->rapport_taal), vertaalTekst("Netto\n \n ", $this->pdf->rapport_taal), vertaalTekst("Boekdatum\n \n ", $this->pdf->rapport_taal)));
      $this->pdf->Row(array("\n ", vertaalTekst("Rekening**\n ", $this->pdf->rapport_taal), vertaalTekst("Dividenden/\nRentevoet", $this->pdf->rapport_taal), "\n ", vertaalTekst("Wissel\nkoers", $this->pdf->rapport_taal), "\n ", "\n ", "\n ", "\n ", vertaalTekst("Valutadatum\n ", $this->pdf->rapport_taal)));
      $this->pdf->setY($this->pdf->getY()+1);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    
  }
  
  function getRecords($type)
  {
    //if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
    //  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    //else
    $koersQuery = "";
    $belCatSelect = '';
    if($type=='cash')
    {
      $belCatSelect='';
      $grootboekFilter = "AND (Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
      $join='';
      $orderBy=' Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, ';
    }
    else
    {
      $grootboekFilter = "AND NOT (Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1' OR Grootboekrekeningen.FondsAanVerkoop = '1')";
      $belCatSelect='Beleggingscategorien.Omschrijving as BelcatOmschrtijving,Fondsen.ISINCode,Fondsen.Omschrijving as FondsOmschrijving,Fondsen.Rentepercentage,
       if(Beleggingscategorien.Afdrukvolgorde is null,255,Beleggingscategorien.Afdrukvolgorde) as belVolgorde,';
      $join="LEFT JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds=BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie=Beleggingscategorien.Beleggingscategorie
      LEFT JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds ";
      $orderBy='belVolgorde,Rekeningmutaties.Boekdatum, Grootboekrekeningen.Afdrukvolgorde, ';
  
    }
    
  //  $grootboekFilter="AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
    $query = "SELECT $belCatSelect".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Omschrijving ,".
      "ABS(Rekeningmutaties.Aantal) AS Aantal, ".
      "Rekeningmutaties.Debet $koersQuery as Debet, ".
      "Rekeningmutaties.Credit $koersQuery as Credit, ".
      "Rekeningmutaties.Valutakoers, ".
      "Rekeningmutaties.Rekening, ".
      "Rekeningmutaties.Valuta, ".
      "Rekeningmutaties.Grootboekrekening, ".
      "Rekeningmutaties.Afschriftnummer, ".
      "Rekeningmutaties.Fonds, ".
      "Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
      "Grootboekrekeningen.Opbrengst, ".
      "Grootboekrekeningen.Kosten, ".
      "Grootboekrekeningen.Afdrukvolgorde ".
      "FROM Rekeningmutaties
       JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
       JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening $join".
      "WHERE ".
      " Rekeningen.Portefeuille = '".$this->portefeuille."' ".
      "AND Rekeningmutaties.Verwerkt = '1' ".
      "AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
      "AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "$grootboekFilter".
      "\n ORDER BY $orderBy Rekeningmutaties.id";
    //"AND Grootboekrekeningen.Grootboekrekening <> 'KNBA' ".
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $belastingGB=array('DIVBE','ROER');
    $regels=array();
    while($mutaties = $DB->nextRecord())
    {
      $omschrijving=vertaalTekst($mutaties['Omschrijving'],$this->pdf->rapport_taal);
      if($omschrijving==$mutaties['Omschrijving'])
      {
        $omschrijvingDelen=explode(" ",$mutaties['Omschrijving']);
        foreach($omschrijvingDelen as $i=>$part)
          $omschrijvingDelen[$i]=vertaalTekst($part,$this->pdf->rapport_taal);
        $omschrijving=implode(" ",$omschrijvingDelen);
      }
      $mutaties['Omschrijving']=$omschrijving;

      $waardeEur=($mutaties['Credit'] - $mutaties['Debet']) * $mutaties['Valutakoers'];
      if($type=='cash')
      {
        $regels['regels'][$mutaties['gbOmschrijving']][] = $mutaties;
        $regels['totaalGB'][$mutaties['gbOmschrijving']] += $waardeEur;
        $regels['totaal']['actuelePortefeuilleWaardeEuro'] += $waardeEur;
      }
      elseif($type=='inkomsten')
      {
        $aantal=fondsAantalOpdatum($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum']);
        $mutaties['fondsAantal']=$aantal['totaalAantal'];
        $mutaties['fondsEenheid']=$aantal['fondsEenheid'];
        {
          $mutaties['BelcatOmschrtijving']='Liquiditeiten';
          $mutaties['FondsOmschrijving']=$mutaties['Omschrijving'];
        }
        if(!isset($regels['regels'][$mutaties['BelcatOmschrtijving']][$mutaties['Boekdatum']][$mutaties['Fonds']]))
        {
          $regels['regels'][$mutaties['BelcatOmschrtijving']][$mutaties['Boekdatum']][$mutaties['Fonds']] = $mutaties;//[$mutaties['Grootboekrekening']][]
        }
        if(in_array($mutaties['Grootboekrekening'],$belastingGB))
        {
          $somVar='BelastingEUR';
        }
        elseif($mutaties['Kosten']==1)
        {
          $somVar='KostenEUR';
        }
        elseif($mutaties['Opbrengst']==1)
        {
          $somVar='OpbrengstEUR';
        }
        else
        {
          $somVar='geen';

        }
        $regels['totaalBelcat'][$mutaties['BelcatOmschrtijving']][$somVar] += $waardeEur;
        $regels['totaalBelcat'][$mutaties['BelcatOmschrtijving']]['TotaalEUR'] += $waardeEur;
        $regels['regels'][$mutaties['BelcatOmschrtijving']][$mutaties['Boekdatum']][$mutaties['Fonds']][$somVar] += $waardeEur;
        $regels['regels'][$mutaties['BelcatOmschrtijving']][$mutaties['Boekdatum']][$mutaties['Fonds']]['TotaalEUR'] += $waardeEur;
        $regels['totaal'][$somVar] += $waardeEur;
        $regels['totaal']['TotaalEUR'] += $waardeEur;
      }
    }
    return $regels;
  }
  
  function printLiqKop($omschrijving,$data,$gray=true)
  {
    if($gray==true)
      $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    else
    {
      $this->pdf->SetFillColor(255, 255, 255);
      $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
      $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    }
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array(vertaalTekst($omschrijving, $this->pdf->rapport_taal),"","","",$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2),""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset( $this->pdf->CellBorders);
  }
  function printFondsenKop($omschrijving,$data,$gray=true)
  {
    if($gray==true)
      $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    else
    {
      $this->pdf->SetFillColor(255, 255, 255);
      $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
      $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    }
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array(vertaalTekst($omschrijving, $this->pdf->rapport_taal),"","","","",$this->formatGetal($data['Bruto'],0),$this->formatGetal($data['Kosten'],0),$this->formatGetal($data['Belasting'],0),$this->formatGetal($data['Netto'],0),''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset( $this->pdf->CellBorders);
  }
  
  function printLiqRegel($data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $omschrijving=$this->testTxtLength($data['Omschrijving'],0);
    $this->pdf->Row(array($omschrijving,
                      $data['Valuta'],
                      $this->formatGetal($data['Credit']-$data['Debet'],2),
                      $this->formatGetal($data['Valutakoers'],4),
                      $this->formatGetal($data['Valutakoers']*($data['Credit']-$data['Debet']),2),
                      date('d/m/Y',db2jul($data['Boekdatum']))));
    if($this->pdf->getY()>180)
      $this->pdf->addPage();
    unset( $this->pdf->CellBorders);
  }
  
  function printFondsRegel($data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    
    $omschrijving=$this->testTxtLength($data['FondsOmschrijving'],0);
    $brutoInValuta=$data['OpbrengstEUR']/$data['Valutakoers'];
    $this->pdf->Row(array($omschrijving,
                      vertaalTekst($data['gbOmschrijving'],$this->pdf->rapport_taal),
                      ($data['fondsAantal']<>0?$this->formatGetal($data['fondsAantal'],0):''),$this->formatGetal($brutoInValuta,0),
                      $data['Valuta'],
                      $this->formatGetal($data['OpbrengstEUR'],0),
                      $this->formatGetal($data['KostenEUR'],0),
                      $this->formatGetal($data['BelastingEUR'],0),
                      $this->formatGetal($data['TotaalEUR'],0),
                      date('d/m/Y',db2jul($data['Boekdatum']))));
  
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->Row(array($data['ISINCode'],
                      $data['Rekening'],
                      trim(($data['Rentepercentage']<>0?$this->formatGetal($data['Rentepercentage'],0):'').' '.($data['fondsAantal']<>0?$this->formatGetal($brutoInValuta/$data['fondsAantal']/$data['fondsEenheid'],2):'')),
                      '',
                      $this->formatGetal($data['Valutakoers'],4),
                      '','','','',
                      date('d/m/Y',db2jul($data['Boekdatum']))));
  
//    Rentepercentage
    unset( $this->pdf->CellBorders);
  }
  
  function printTotaal($omschrijving,$data,$fondsTotaal=false)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0], $this->pdf->rapport_donkergrijs[1], $this->pdf->rapport_donkergrijs[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

    if($fondsTotaal==false)
    {
      $this->pdf->Row(array(vertaalTekst($omschrijving, $this->pdf->rapport_taal), "", "", "", $this->formatGetal($data['actuelePortefeuilleWaardeEuro'], 0), ""));
    }
    else
    {
      $this->pdf->Row(array(vertaalTekst($omschrijving, $this->pdf->rapport_taal), "", "", "", "", $this->formatGetal($data['OpbrengstEUR'], 0), $this->formatGetal($data['KostenEUR'], 0), $this->formatGetal($data['BelastingEUR'], 0), $this->formatGetal($data['TotaalEUR'], 0), ''));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset( $this->pdf->CellBorders);
    unset($this->pdf->fillCell);
    
  }
	function writeRapport()
	{
		global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,40,
      $this->pdf->w-$this->pdf->marge-5,45,
      $this->pdf->marge,45);
    
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(250));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(27);

    if( $this->pdf->rapport_taal == 2 ) {
      $intro = "The table provides an overview of the amounts deposited or withdrawn from your accout. 
These deposits and/of withdrawals are taken into account when caculating investment results. When there are multiple accounts within the contract, a transer is accounted as a withdrawel and booked as a deposit.";
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $intro = "Le tableau vous indique les apports et retraits effectués sur votre portefeuille. 
Ces apports/retraits sont pris en compte dans le calcul de votre performance. Quand plusieurs comptes de liquidités sont repris dans le contrat, un transfert entre ces deux comptes est comptabilisé comme un retrait et affiché comme un dépôt.";
    } else {
      $intro = "Deze tabel toont een overzicht van de bedragen die u heeft gestort op en/of onttrokken van uw rekening.
De berekening van het beleggingsresultaat wordt gecorrigeerd voor alle stortingen en/of onttrekkingen die u gedurende de rapportageperiode heeft verricht. Stortingen dragen niet bij aan het resultaat, terwijl onttrekkingen niet ten koste van het resultaat gaan. Wanneer er sprake is van meerdere rekeningen binnen het contract wordt een overboeking tussen deze rekeningen getoond als een onttrekking en als storting geboekt.";
    }
    $this->pdf->Row(array($intro));
    $this->pdf->ln(8);
    
    $this->header('cash');
    $regels=$this->getRecords('cash');
    $this->printLiqKop('Cash',$regels['totaal']);
    foreach($regels['regels'] as $gb=>$regelData)
    {
      $this->printLiqKop($gb,array('actuelePortefeuilleWaardeEuro'=>$regels['totaalGB'][$gb]),false);
      foreach($regelData as $regel)
        $this->printLiqRegel($regel);
      if($this->pdf->getY()>180)
      {
        $this->pdf->addPage();
        $this->header('cash');
      }
    }
    $this->printTotaal('Totaal',$regels['totaal']);
    
    # tweede deel
    $this->pdf->rapport_titel = "Overzicht inkomsten en kosten";
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'2Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'2Paginas']=$this->pdf->rapport_titel;
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,32,
      $this->pdf->w-$this->pdf->marge-5,37,
      $this->pdf->marge,37);
    
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(250));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(27);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

    if( $this->pdf->rapport_taal == 2 ) {
      $intro = "The table provides an overview of the revenues you have received. The revenues consts of, for example, dividend payments on shares and investment funds, interest payments on individual bonds (coupons) and interest on cash. These revenues are added to your account and are part of the investment result.";
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $intro = "Le tableau vous indique les revenus perçus. Il s'agit, par exemple, des paiements de dividendes sur les actions et les fonds de placement, d'intérêts sur obligations individuelles (coupons) et des intérêts servis sur les liquidités. Ces revenus sont versés sur votre compte et font partie du résultat des placements.";
    } else {
      $intro = "De tabel geeft een overzicht van de inkomsten die u heeft ontvangen. De inkomsten bestaan uit bijvoorbeeld dividendbetalingen van aandelen en beleggingsfondsen, rentevergoedingen van
individuele obligaties (couponbetalingen) en rente op liquiditeiten. Deze inkomsten worden aan uw vermogen toegevoegd en zijn onderdeel van het beleggingsresultaat.";
    }

    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->Row(array($intro));
    $this->pdf->ln(8);
    
    $this->header('inkomsten');
    $regels=$this->getRecords('inkomsten');
    foreach($regels['regels'] as $belcat=>$belcatData)
    {
      $this->printFondsenKop($belcat,array('Netto'=>$regels['totaalBelcat'][$belcat]['TotaalEUR'],
                                           'Bruto'=>$regels['totaalBelcat'][$belcat]['OpbrengstEUR'],
                                           'Belasting'=>$regels['totaalBelcat'][$belcat]['BelastingEUR'],
                                           'Kosten'=>$regels['totaalBelcat'][$belcat]['KostenEUR']),true);
      foreach($belcatData as $boekDatum=>$fondsen)
      {
        foreach ($fondsen as $regel)
        {
          $this->printFondsRegel($regel);
          if($this->pdf->getY()>180)
          {
            $this->pdf->addPage();
            $this->header('inkomsten');
          }
        }
      }
    }
    $this->printTotaal('Totaal',$regels['totaal'],true);
    $this->pdf->rowHeight = $this->pdf->rapport_lowRow;

    if($this->pdf->getY()>165)
      $this->pdf->addPage();

    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->setWidths(array(200));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'I',$this->pdf->rapport_fontsize);
    $this->pdf->ln(3);
    $this->pdf->Row(array(vertaalTekst("* De bedragen in deze kolom worden getoond in de valuta van de belegging.",$this->pdf->rapport_taal)));
    $this->pdf->Row(array(vertaalTekst("** Indien geen rekeningnummer is ingevuld dan zijn deze inkomsten op een rekening geboekt die geen deel uitmaakt van dit rapport.",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    unset($this->pdf->fillCell);
  }
  
 
}
?>