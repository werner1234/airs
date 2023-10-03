<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportTRANS_L117
{

	function RapportTRANS_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->portefeuille=$portefeuille;
    $this->rapportageDatumVanaf=$rapportageDatumVanaf;
    $this->rapportageDatum=$rapportageDatum;

    $this->pdf->rapport_titel = "Overzicht transacties";
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
      $this->pdf->setAligns(array("L", "L", 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
//      $this->pdf->fillCell = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
      $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0], $this->pdf->rapport_donkergroen[1], $this->pdf->rapport_donkergroen[2]);
      $this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),297-$this->pdf->marge*2-1,20,'F');
      $this->pdf->rowHeight = $this->pdf->rapport_lowRow;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
      $this->pdf->SetWidths(array(60, 40, 23, 23, 23, 23, 22, 23, 23, 20));
      $vertaling=	array("A"=>"Aankoop",
                                 'A/O'=>'Aankoop / openen',
                                 'A/S'=>'Aankoop / sluiten',
                                 'D'=>'Deponering',
                                 'L'=>'Lichting',
                                 'V'=>'Verkoop',
                                 'V/O'=>'Verkoop / openen',
                                 'V/S'=>'Verkoop / sluiten');


      if(substr($type,0,1)=='A')
      {
        $resultaat = "\n \n ";
      }
      else
      {
        $resultaat = vertaalTekst("Gerealiseerd\nresultaat\n ", $this->pdf->rapport_taal);
      }
      $omschrijving=vertaalTekst($vertaling[$type], $this->pdf->rapport_taal);
      $this->pdf->Row(array(
        "$omschrijving\n \n ",
        vertaalTekst("Transactie\ndatum \n ", $this->pdf->rapport_taal),
        vertaalTekst("Aantal/\nNominale\n waarde", $this->pdf->rapport_taal),
        vertaalTekst("Transactie*\nbedrag** \n ", $this->pdf->rapport_taal),
        vertaalTekst("Valuta\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Koers**\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("\nWaarde\n ", $this->pdf->rapport_taal),
        $resultaat,vertaalTekst("Kosten\n \n ",
        $this->pdf->rapport_taal),
        vertaalTekst("Belastingen\n*** \n ", $this->pdf->rapport_taal)
      ));
      $this->pdf->Row(array("\n ", "\n ", "\n ", "\n ", vertaalTekst("Wissel\nkoers", $this->pdf->rapport_taal), "\n ", "\n ", "\n ", vertaalTekst("Kosten %\n ", $this->pdf->rapport_taal), vertaalTekst("Belastingen\n%***", $this->pdf->rapport_taal)));

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

  }

  function getRecords()
  {
    //if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
    //  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    //else
    $koersQuery = "";

  //  $grootboekFilter="AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1')";
    $query = "SELECT if(Rekeningmutaties.Transactietype='','Z',Rekeningmutaties.Transactietype) as Transactietype, Fondsen.ISINCode, Fondsen.Fondseenheid, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Omschrijving ,".
      "ABS(Rekeningmutaties.Aantal) AS Aantal, ".
      "Rekeningmutaties.Debet $koersQuery as Debet, ".
      "Rekeningmutaties.Credit $koersQuery as Credit,
      1 $koersQuery as Rapportagekoers, ".
      "Rekeningmutaties.Valutakoers, ".
      "Rekeningmutaties.Rekening, ".
      "Rekeningmutaties.Valuta, ".
      "Rekeningmutaties.Grootboekrekening, ".
      "Rekeningmutaties.Afschriftnummer, Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Fonds,
      Fondsen.Omschrijving as FondsOmschrijving,".
      "Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
      "Grootboekrekeningen.Opbrengst, ".
      "Grootboekrekeningen.Kosten, ".
      "Grootboekrekeningen.Afdrukvolgorde ".
      "FROM Rekeningmutaties
       JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
       JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
       LEFT JOIN Fondsen ON Rekeningmutaties.Fonds=Fondsen.Fonds ".
      "WHERE ".
      " Rekeningen.Portefeuille = '".$this->portefeuille."' ".
      "AND Rekeningmutaties.Verwerkt = '1' ".
      "AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
      "AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
      "AND Rekeningmutaties.Fonds <> ''".
      "\n ORDER BY Transactietype, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";
    //"AND Grootboekrekeningen.Grootboekrekening <> 'KNBA' ".
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $belastingGB=array('DIVBE');
    $regels=array();
    $somVars=array('waardeEur','resultaat');
    while($mutaties = $DB->nextRecord())
    {
      if($mutaties['Fonds']<>'' && $mutaties['Transactietype']<>'Z')
      {
        $regels['volgorde'][$mutaties['Transactietype']][$mutaties['Boekdatum']][$mutaties['Fonds']] = $mutaties;
      }

      if(	$mutaties['Transactietype'] == "L" ||
        $mutaties['Transactietype'] == "V" ||
        $mutaties['Transactietype'] == "V/S" ||
        $mutaties['Transactietype'] == "A/S")
      {
        $t_verkoop_waarde = abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
        $t_aankoop_waarde = 0;
        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'], $this->pdf->rapportageValuta, $this->rapportageDatumVanaf, $mutaties['id']);
        if ($mutaties['Transactietype'] == "A/S")
        {
          $historischekostprijs = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
          $beginditjaar = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
        }
        else
        {
          $historischekostprijs = $mutaties['Aantal'] * $historie['historischeWaarde'] * $historie['historischeValutakoers'] * $mutaties['Fondseenheid'];
          $beginditjaar = $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
        }
        if ($this->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->pdf->rapportageValuta)
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
          $beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
        }
        elseif ($this->pdf->rapportageValuta != 'EUR')
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
          $beginditjaar = $beginditjaar / getValutaKoers($this->pdf->rapportageValuta, date("Y", db2jul($this->rapportageDatum) . '-01-01'));
        }

        if ($historie['voorgaandejarenActief'] == 0)
        {
          $resultaatvoorgaande = 0;
          $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
          if ($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = 0;
            $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
          }
        }
        else
        {
          $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
          $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
          if ($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
            $resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
          }
        }


        $mutaties['resultaat'] = $resultaatlopende + $resultaatvoorgaande;
      }
        $mutaties['waardeEur']=($mutaties['Credit'] - $mutaties['Debet']) * $mutaties['Valutakoers'];


        if(isset( $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$mutaties['Grootboekrekening']]))
        {
          $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$mutaties['Grootboekrekening']]['aantal'] += $mutaties['aantal'];
          $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$mutaties['Grootboekrekening']]['waardeEur'] += $mutaties['waardeEur'];
        }
        else
        {
          $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$mutaties['Grootboekrekening']] = $mutaties;//[$mutaties['Grootboekrekening']][]
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
      $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$somVar] += $mutaties['waardeEur'];

        foreach($somVars as $somVar)
        {
          $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']][$somVar] += $mutaties[$somVar];
          $regels['totaal'][$mutaties['Transactietype']][$somVar] += $mutaties[$somVar];
        }
        $regels['regels'][$mutaties['Boekdatum']][$mutaties['Fonds']]['TotaalEUR'] += $mutaties['waardeEur'];
        $regels['totaal'][$mutaties['Transactietype']]['TotaalEUR'] += $mutaties['waardeEur'];

    }
    foreach($regels['regels'] as $datum=>$fondsen)
    {
      foreach($fondsen as $fonds=>$grootboekData)
      {
        $transactietype=$grootboekData['FONDS']['Transactietype'];
        if($transactietype<>'')
        {
          $regels['totaal'][$transactietype]['KostenEUR'] += $grootboekData['KostenEUR'];
          $regels['totaal'][$transactietype]['BelastingEUR'] += $grootboekData['BelastingEUR'];
        }
      }
    }


    return $regels;
  }




  function printRegel($data)
  {
    if(!is_array($data['FONDS']))
      return '';
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);

  //  listarray($data);
    $omschrijving=$this->testTxtLength($data['FONDS']['FondsOmschrijving'],0);
    $this->pdf->Row(array($omschrijving,
                      date('d/m/Y',db2jul($data['FONDS']['Boekdatum'])),
                      $this->formatGetal($data['FONDS']['Aantal'],0),
                      $this->formatGetal($data['FONDS']['waardeEur'],2),
                      $data['FONDS']['Valuta'],
                      $this->formatGetal($data['FONDS']['Fondskoers'],2),
                      $this->formatGetal($data['FONDS']['waardeEur'],2),
                      ($data['FONDS']['resultaat']<>0?$this->formatGetal($data['FONDS']['resultaat'],2):''),
                      $this->formatGetal($data['KostenEUR'],0),
                      $this->formatGetal($data['BEL']['waardeEur']+$data['DIVBE']['waardeEur'],2)
                      ));

    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->Row(array($data['FONDS']['ISINCode'],
                      '','','','','','','',
                      $this->formatGetal($data['KostenEUR']/$data['FONDS']['waardeEur'],4).'%',
                      $this->formatGetal($data['BelastingEUR']/$data['FONDS']['waardeEur'],4).'%'
                      ));

//    Rentepercentage
    unset( $this->pdf->CellBorders);
  }

  function printTotaal($omschrijving,$data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0], $this->pdf->rapport_donkergrijs[1], $this->pdf->rapport_donkergrijs[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0], $this->pdf->rapport_kop_fontcolor[1], $this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->Row(array($omschrijving, "", "", "", "", "", $this->formatGetal($data['waardeEur'], 0), $this->formatGetal($data['resultaat'], 0), $this->formatGetal($data['KostenEUR'], 0), $this->formatGetal($data['belastingEUR'], 0), ''));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    unset( $this->pdf->CellBorders);
    $this->pdf->ln(2);
  }
	function writeRapport()
	{
		global $__appvar;
    $this->pdf->AddPage();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

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
      $intro = "The transaction overview captures executed investment transactions during the reporting period*. The date shown is the date on which the transaction has been executed. For all transactions, the transaction amount is provided. For sell transactions we mention the realised result on the sale, which is part of the investment result calculation. For buy of sell transactions in a foreign currency, the daily exchange rate is indicated.";
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $intro = "Le récapitulatif des transactions rend compte des transactions de placement exécutées pendant la période du rapportage. La date indiquée est celle de l'exécution de la transaction. Le montant de la transaction est toujours indiqué. Pour les achats et les ventes dans une monnaie étrangère, nous indiquons le taux de change le jour de la transaction. Pour les transactions liées aux obligations, les montants des coupons à percevoir sont indiqués séparément. ";
    } else {
      $intro = "Het transactieoverzicht toont de beleggingstransacties die gedurende de rapportageperiode zijn uitgevoerd*. De vermelde datum is de dag waarop de transactie is uitgevoerd. Voor alle transacties is het transactiebedrag weergegeven. Voor verkooptransacties vermelden wij het gerealiseerd resultaat, dit is het bedrag dat u terugvindt in de berekening van het beleggingsresultaat. Bij aan- of verkooptransacties in vreemde valuta’s wordt de wisselkoers per de transactiedatum vermeld. Voor obligatietransacties wordt de te betalen couponrente (aankoop) dan wel de te ontvangen couponrente (verkoop) afzonderlijk vermeld.";
    }

    $this->pdf->Row(array($intro));
    $this->pdf->ln(8);

    $regels=$this->getRecords();
 //  listarray($regels['volgorde']);

    $lastHeader='';
    $transactieType='';
    foreach($regels['volgorde'] as $transactieType=>$datumVelden)//foreach($regels['regels'] as $datum=>$fondsen)
    {
      foreach($datumVelden as $datum=>$fondsen)//      foreach($fondsen as $fondsData)
      {
        foreach($fondsen as $fonds=>$details)
        {
          $fondsData = $regels['regels'][$datum][$fonds];

          $transactieType = $fondsData['FONDS']['Transactietype'];
          if ($transactieType <> '' && $transactieType <> $lastHeader)
          {
            if ($lastHeader <> '')
            {
              $this->printTotaal(vertaalTekst('Totaal', $this->pdf->rapport_taal), $regels['totaal'][$lastHeader]);
            }
            $this->header($transactieType);
            $lastHeader = $fondsData['FONDS']['Transactietype'];
          }
          $this->printRegel($fondsData);
          if ($this->pdf->getY() > 150)
          {
            $this->pdf->addPage();
            $this->header($transactieType);
          }
        }
      }
    }
    $this->printTotaal(vertaalTekst('Totaal', $this->pdf->rapport_taal),$regels['totaal'][$lastHeader]);
    $this->pdf->rowHeight = $this->pdf->rapport_lowRow;


    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->setWidths(array(200));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'I',$this->pdf->rapport_fontsize);
    $this->pdf->ln(3);
    $this->pdf->Row(array(vertaalTekst("** De bedragen in deze kolom worden getoond in de valuta van de belegging.",$this->pdf->rapport_taal)));
    $this->pdf->Row(array(vertaalTekst("*** Alleen belastingen die gerelateerd zijn aan de transactie worden getoond. ",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    unset($this->pdf->fillCell);

  }


}