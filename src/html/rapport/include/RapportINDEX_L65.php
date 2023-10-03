<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L65.php");

//ini_set('max_execution_time',60);
class RapportINDEX_L65
{
  function RapportINDEX_L65($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    //
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "INDEX";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Rendement vs doelstelling ";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

    $this->RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->RapStartJaar."-01-01"))
      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
    else
      $this->tweedePerformanceStart = $this->RapStartJaar."-01-01";
    $this->perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);

  }

  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }

  function writeRapport()
  {
    global $__appvar;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->addPage();
    $this->pdf->templateVars['INDEXPaginas']=$this->pdf->page;
    $this->pdf->ln();
    //  $this->printBenchmarkvergelijking();

    $index=new indexHerberekening_L65();
    $indexData = $index->getWaarden($this->tweedePerformanceStart ,$this->rapportageDatum ,$this->portefeuille);
    $kwartaalData=$this->maandenNaarKwartalen($indexData);

    $index=new indexHerberekening();
    $data=$index->getWaarden($this->pdf->PortefeuilleStartdatum, $this->rapportageDatum,$this->portefeuille,'','maanden',$this->pdf->rapportageValuta);

    $this->grafiekData = array('titel'=>vertaalTekst('Portefeuille rendement vanaf' ,$this->pdf->rapport_taal).' '.date('d-m-Y',db2jul($this->pdf->PortefeuilleStartdatum)));
    $maanden=array('','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
    foreach($maanden as $key=>$value)
      $maanden[$key]=vertaalTekst($value,$this->pdf->rapport_taal);

    $doelstellingSum = null;
    foreach($data as $values)
    {
      list($from, $to) = explode('->', $values['periode']);
      if ( date('d', strtotime($to)) !== date('t', strtotime($to))) {
        continue;
      }
      $tmp = getBenchmarkvergelijking($this,$from,$to);

      if ( ! $doelstellingSum ) {
        $doelstellingSum = $tmp['totaal']['periode'];
      } else {
        $doelstellingSum = ( ( (1 + ($doelstellingSum / 100)) * (1 + ($tmp['totaal']['periode'] / 100)) ) - 1 ) * 100;
      }

      $this->grafiekData['specifiekeIndex'][]=$doelstellingSum;
      $this->grafiekData['portefeuille'][]=$values['index']-100;
      $julDatum=db2jul($values['datum']);
      $this->grafiekData['datum'][]=$maanden[date('n',$julDatum)].'-'.date('y',$julDatum);
    }

    $benchmarkData=getBenchmarkvergelijking($this,$this->perioden['begin'],$this->perioden['eind'],$this->perioden['jan']);
    $this->toonBenchmarkvergelijking($benchmarkData,$kwartaalData);
  }

  function toonKwartaalData($kwartaalData)
  {

    $this->pdf->ln();

    $tmp=vertaalTekst("Het beleggingsproces van DoubleDividend is gericht op het behalen van een absoluut rendement dat aansluit bij de beleggingsvraag en niet op het verslaan van een benchmark (de ‘markt’).",$this->pdf->rapport_taal).' '.
      vertaalTekst("Voor aandelen verwachten we op lange termijn gemiddeld een jaarlijks netto rendement van 8%, voor de selectie obligaties 2% (gebaseerd op de huidige rentestanden) en voor de alternatieven 7%. Deze lange termijn rendementen zijn zowel gebaseerd op historische rendementen als ook toekomstige verwachtingen.",$this->pdf->rapport_taal);

    if ( $this->pdf->rapport_taal === 1 ) {
      $tmp = 'DoubleDividend\'s investment process is focused on achieving an absolute return that matches investment demand and not on beating a benchmark (the "market"). In the long term, we expect an average annual net return of 8% for equities, 2% for the selection of bonds (based on current interest rates) and 7% for the alternatives. These long-term returns are based on historical returns as well as future expectations.';
    }
    $this->pdf->multicell(275,3,vertaalTekst($tmp ,$this->pdf->rapport_taal));
    $this->pdf->ln();
    $tmp= "*De verwachte rendementsdoelstelling van het risicoprofiel (gebaseerd op de normweging) zijn na aftrek depotbankkosten en kosten van vermogensbeheer en gebaseerd op een vermogen van EUR 1 miljoen.";

    if ( $this->pdf->rapport_taal === 1 ) {
      $tmp = '*The expected return target of the risk profile (based on the standard weighting) is after deduction of custodian bank costs and asset management costs and based on assets of EUR 1 million.';
    }
    $this->pdf->multicell(275,3,vertaalTekst($tmp ,$this->pdf->rapport_taal));
    $this->pdf->ln();

    $this->pdf->setX($this->pdf->getX() + 10);
    $this->LineDiagram(100, 50, $this->grafiekData, array(51,102,204), $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,true);

    $this->pdf->setY($this->pdf->getY() + 70);

    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->Cell(100,4, vertaalTekst("Rendement portefeuille vs doelstelling",$this->pdf->rapport_taal),0,0);
    $this->pdf->ln(6);



    $this->pdf->SetWidths(array(30,30,30,30,30));
    $this->pdf->SetAligns(array('L','R','R','R','R'));
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Resultaat',$this->pdf->rapport_taal),vertaalTekst("Totaal\nRendement",$this->pdf->rapport_taal),vertaalTekst("Doelstelling",$this->pdf->rapport_taal),vertaalTekst("Relatieve\nPerformance",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($kwartaalData as $kwartaal=>$data)
    {
      $tmp=getBenchmarkvergelijking($this,$data['beginDatum'],$data['eindDatum']);
      $this->pdf->row(array(vertaalTekst("KW",$this->pdf->rapport_taal).' '.$kwartaal,$this->formatGetal($data['resultaatVerslagperiode'],0), $this->formatGetal($data['performance'],2).'%', $this->formatGetal($tmp['totaal']['periode'],2).'%',$this->formatGetal($data['performance']-$tmp['totaal']['periode'],2)."%"));
    }

    //$this->pdf->row(array($tmp));
  }




  function maandenNaarKwartalen($maandDataIn)
  {
//listarray($maandData);
    $tmp=array();
    $somVelden=array('stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','rente','gerealiseerd');
    $stapelItems=array('performance');
    $gemiddeldeVelden=array('gemiddelde');
    // listarray($maandDataIn);
    $eersteDag=array();
    foreach($maandDataIn as $totaalData)
    {
      // $beginJul=db2jul();
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);

      //echo $kwartaal." ".$totaalData['periode']."<br>\n";
      if(!isset($eersteDag[$kwartaal]))
        $eersteDag[$kwartaal]=substr($totaalData['periode'],0,10);

      if($kwartaal<>$laatsteKwartaal)
      {
        $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);
      }

      $laasteJulDatum=$julDatum;
      $laatsteKwartaal=$kwartaal;
    }
    $laatsteDag[$laatsteKwartaal]=date("-m-d",$laasteJulDatum);

    $aantalWaarden=0;
    foreach($maandDataIn as $totaalData)
    {
      $julDatum=db2jul($totaalData['datum']);
      $kwartaal=ceil(date("m",$julDatum) / 3)." ".date("Y",$julDatum);
      $dateEnd=$laatsteDag[$kwartaal];
      $dateBegin=$eersteDag[$kwartaal];
      if($kwartaal <> '')
      {

        if($kwartaal <> $lastKwartaal)
        {
          $lastKwartaal='';
          foreach ($stapelItems as $item)
          {
            //      if(!isset($tmp[$categorie]['perfWaarden'][$kwartaal.$dateEnd][$item]))
            //      $tmp[$categorie]['perfWaarden'][$kwartaal.$dateEnd][$item]=1;
          }
          if($lastKwartaal <> '')
          {
            foreach ($gemiddeldeVelden as $item)
              $tmp['perfWaarden'][$kwartaal][$item]=$tmp['perfWaarden'][$kwartaal][$item] /($aantalWaarden+1);
          }
          $aantalWaarden=0;

        }

        if(!isset($tmp['perfWaarden'][$kwartaal]['waardeBegin']))
          $tmp['perfWaarden'][$kwartaal]['waardeBegin']=$totaalData['waardeBegin'];
        $tmp['perfWaarden'][$kwartaal]['waardeHuidige']=$totaalData['waardeHuidige'];
        $tmp['perfWaarden'][$kwartaal]['index']=$totaalData['index'];
        $tmp['perfWaarden'][$kwartaal]['beginDatum']=$dateBegin;
        $tmp['perfWaarden'][$kwartaal]['eindDatum']=date('Y-m-d',$julDatum);




        foreach($somVelden as $veld)
          $tmp['perfWaarden'][$kwartaal][$veld]+=$totaalData[$veld];

        foreach ($stapelItems as $item)
          $tmp['perfWaarden'][$kwartaal][$item] = ((($tmp['perfWaarden'][$kwartaal][$item]/100+1)  * ($totaalData[$item]/100+1))-1)*100;

        foreach ($gemiddeldeVelden as $item)
          $tmp['perfWaarden'][$kwartaal][$item] += $totaalData[$item];

        $lastKwartaal=$kwartaal;
        $aantalWaarden++;
      }
    }
    foreach ($gemiddeldeVelden as $item)
      $tmp['perfWaarden'][$kwartaal][$item] =$tmp['perfWaarden'][$kwartaal][$item]/($aantalWaarden+1);
    //foreach ($stapelItems as $item)
    //   $tmp['perfWaarden'][$kwartaal.$dateEnd][$item] =$tmp['perfWaarden'][$kwartaal.$dateEnd][$item]-1;


    //listarray($tmp);
    return $tmp;
  }

  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }

  function toonBenchmarkvergelijking($benchmarkData,$kwartaalData)
  {
    $zorgplichtcategorien=$benchmarkData['zorgplichtcategorien'];
    $samengesteldeBenchmark=$benchmarkData['samengesteldeBenchmark'];
    $verdeling=$benchmarkData['verdeling'];
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->Cell(100,4, vertaalTekst("Rendementdoelstelling*",$this->pdf->rapport_taal),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



    $this->toonKwartaalData($kwartaalData['perfWaarden']);

    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->Cell(100,4, vertaalTekst("Asset Allocatie per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);

    $this->pdf->SetWidths(array(40,20,20,20,20,20,20,20,20));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Min',$this->pdf->rapport_taal),vertaalTekst("Norm",$this->pdf->rapport_taal),vertaalTekst("Max",$this->pdf->rapport_taal),vertaalTekst("Huidig",$this->pdf->rapport_taal),vertaalTekst("Verschil",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
    {
      $this->pdf->row(array(vertaalTekst($zorgplichtCategorie ,$this->pdf->rapport_taal),$this->formatGetal($zorgData['Minimum'],1).'%',
        $this->formatGetal($zorgData['norm'],1).'%',
        $this->formatGetal($zorgData['Maximum'],1).'%',
        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%'));
    }
  }

  function printBenchmarkvergelijking()
  {
    global $__appvar;
    $DB = new DB();
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

    $zorgplichtcategorien=array();
    $query="SELECT waarde as Zorgplicht FROM KeuzePerVermogensbeheerder WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Zorgplicht' ORDER BY Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;

    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
              ZorgplichtPerBeleggingscategorie.Zorgplicht,
              beleggingscategorieOmschrijving ".
      "FROM TijdelijkeRapportage
             INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']." 
              GROUP BY Zorgplicht 
              ORDER BY beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;
      $verdeling[$data['Zorgplicht']]['percentage'] = $data['totaal']/$totaalWaarde*100;
    }

    $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $zorgplichtcategorien=array();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }
    $query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."'
 ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }

    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie 
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Zorgplichtcategorien' AND Categorie='$zorgplicht' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
      $DB->SQL($query);
      $DB->Query();
      $data = $DB->nextRecord();
      $zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
      $zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
    }

    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving 
      FROM benchmarkverdeling 
      JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmark='".$zorgplichtData['fonds']."'";
      $DB->SQL($query);
      $DB->Query();
      while($data = $DB->nextRecord())
        $zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']]=$data;
    }

    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->Cell(100,4, vertaalTekst("Portefeuille vs Doelstelling",$this->pdf->rapport_taal),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);


    $this->pdf->SetWidths(array(60,25,25,25,25,25,25,20,25));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Doelstelling',$this->pdf->rapport_taal),vertaalTekst('Gewicht',$this->pdf->rapport_taal),vertaalTekst("Rendement\nperiode",$this->pdf->rapport_taal),vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n".$this->RapStartJaar));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgplichtData)
    {
      // $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      // $this->pdf->row(array($zorgplichtCategorie));
      // $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


      if(!isset($zorgplichtData['fondsSamenselling']))
        $zorgplichtData['fondsSamenselling']=array($zorgplichtData['fonds']=>array('fonds'=>$zorgplichtData['fonds'],
          'percentage'=>100,
          'Omschrijving'=>$zorgplichtData['fondsOmschrijving']));
      foreach($zorgplichtData['fondsSamenselling'] as $fonds=>$fondsData)
      {
        $indexData[$fonds]=$index;
        foreach ($this->perioden as $periode=>$datum)
        {
          $indexData[$fonds]['fondsKoers_'.$periode]=$this->getFondsKoers($fonds,$datum);
          $indexData[$fonds]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
        $indexData[$fonds]['performanceJaar'] = ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_jan'])    / ($indexData[$fonds]['fondsKoers_jan']/100 );
        $indexData[$fonds]['performance'] =     ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin']/100 );
        $indexData[$fonds]['performanceEurJaar'] = ($indexData[$fonds]['fondsKoers_eind']*$indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_jan']  *$indexData[$fonds]['valutaKoers_jan'])/(  $indexData[$fonds]['fondsKoers_jan']*  $indexData[$fonds]['valutaKoers_jan']/100 );
        $indexData[$fonds]['performanceEur'] =     ($indexData[$fonds]['fondsKoers_eind']*$indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_begin']*$indexData[$fonds]['valutaKoers_begin'])/($indexData[$fonds]['fondsKoers_begin']*$indexData[$fonds]['valutaKoers_begin']/100 );


        $indexData[$zorgplichtData['fonds']]['performance']+=($indexData[$fonds]['performance']*($fondsData['percentage']/100));
        $indexData[$zorgplichtData['fonds']]['performanceJaar']+=($indexData[$fonds]['performanceJaar']*($fondsData['percentage']/100));

        $this->pdf->row(array($fondsData['Omschrijving'],
          $this->formatGetal($fondsData['percentage'],1).'%',
          $this->formatGetal($indexData[$fonds]['performance'],2).'%',
          $this->formatGetal($indexData[$fonds]['performanceJaar'],2).'%'));
      }
      $fonds=$zorgplichtData['fonds'];
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst('Doelstelling',$this->pdf->rapport_taal).' '.vertaalTekst($zorgplichtCategorie,$this->pdf->rapport_taal),
        $this->formatGetal(100,1).'%',
        $this->formatGetal($indexData[$fonds]['performance'],2).'%',
        $this->formatGetal($indexData[$fonds]['performanceJaar'],2).'%'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->ln();

      $samengesteldeBenchmark[$zorgplichtCategorie]['norm']=$zorgplichtData['norm'];
      $samengesteldeBenchmark[$zorgplichtCategorie]['periode']=$indexData[$fonds]['performance'];
      $samengesteldeBenchmark[$zorgplichtCategorie]['jaar']=$indexData[$fonds]['performanceJaar'];
    }
    $totalen=array();
    foreach($samengesteldeBenchmark as $zorgplichtCategorie=>$data)
    {
      $this->pdf->row(array($zorgplichtCategorie,
        $this->formatGetal($data['norm'],1).'%',
        $this->formatGetal($data['periode'],2).'%',
        $this->formatGetal($data['jaar'],2).'%'));
      $totalen['norm']+= $data['norm'];
      $totalen['periode']+=$data['norm']*$data['periode']/100;
      $totalen['jaar']+=$data['norm']*$data['jaar']/100;
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Overall doelstelling',$this->pdf->rapport_taal),
      $this->formatGetal($totalen['norm'],1).'%',
      $this->formatGetal($totalen['periode'],2).'%',
      $this->formatGetal($totalen['jaar'],2).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->Cell(100,4, vertaalTekst("Asset Allocatie per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);

    $this->pdf->SetWidths(array(40,20,20,20,20,20,20,20,20));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',vertaalTekst('Min',$this->pdf->rapport_taal),vertaalTekst("Norm",$this->pdf->rapport_taal),vertaalTekst("Max",$this->pdf->rapport_taal),vertaalTekst("Huidig",$this->pdf->rapport_taal),vertaalTekst("Verschil",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
    {
      $this->pdf->row(array(vertaalTekst($zorgplichtCategorie ,$this->pdf->rapport_taal),$this->formatGetal($zorgData['Minimum'],1).'%',
        $this->formatGetal($zorgData['norm'],1).'%',
        $this->formatGetal($zorgData['Maximum'],1).'%',
        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%'));
    }


    // listarray($zorgplichtcategorien);


  }

  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafNul=false)
  {
    global $__appvar;
//debug($data);
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];

    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + 2;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'C');

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);

    if($color1 == null)
      $color1=array(81,84,95);

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

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);
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

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      if($i > $YPage)
      {
        $skipNull = true;
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
      /*
      $yGetal=$offset-($n*$stapgrootte)+$minVal;
      if($yGetal>=$minVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
*/

      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      /*
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
*/
      $yGetal=$offset-(-1*$n*$stapgrootte)+$minVal;
      if($yGetal<=$maxVal)
      {
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
          $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      }


      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)*2/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+9,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

      if ($i>0 || $vanafNul==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      }
//      if ($i==count($data)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);


      $yval = $yval2;
    }

    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

        if ($i>0 || $vanafNul==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
//          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        }
//        if ($i==count($data1)-1)
//          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

        $yval = $yval2;
      }
    }


    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));

    $step=5;
    $xpos = $XPage + 120;
    foreach (array('Rendement', 'Doelstelling ') as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($xpos, $YPage + $step, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($xpos +3,$YPage + $step);
      $this->pdf->Cell(0,3,$item);
      $step+=5;
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);

    $this->pdf->setY($YPage);
  }

}
?>
