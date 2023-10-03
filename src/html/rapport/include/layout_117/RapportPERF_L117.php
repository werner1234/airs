<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_117/RapportATT_L117.php");

class RapportPERF_L117
{

	function RapportPERF_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->att= new RapportATT_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		$this->pdf->rapport_titel = "Beleggingsresultaat";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar;

		$DB = new DB();


		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $poly=array($this->pdf->marge,25,
      $this->pdf->marge+85,25,
      $this->pdf->marge+85,100,
      $this->pdf->marge+80,105,
      $this->pdf->marge,105);
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(85));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(26);

    if( $this->pdf->rapport_taal == 2 ) {
      $this->pdf->Row(array("The gross investment result (before costs) represents the value of your assets during the reporting period. This result includes the gains and/of losses on you investments, the result by currency exchange rates, all coupon and dividend income an, if you have individual bonds in you portfolio, the accrued interes.

The result makes allowance for all deposits and/of withdrawals you have made during the reportingn periode. Deposits do not contribute tot the result, while withdrawels are not at the expense of the result.

Net results are the gross results minus the charged fees.

In the second table, we split te result in realised (booked) and unrealised results. The breakdown of the result is based on the above-mentiond categories."));
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $this->pdf->Row(array("La performance brute (avant frais) représente l'évolution de la valeur de vos actifs pendant la période de référence. Elle est composée des gains et/ou pertes enregistrés sur vos placements, du résultat des opérations de change, de tous les revenus, coupons et dividendes, et, si vous avez des obligations individuelles en portefeuille, des intérêts courus.

La performance est corrigée de tous les dépôts et/ou retraits que vous auriez pu faire pendant cette période. La performance ne tient compte ni des apports ni des retraits.

La performance nette correspond à la performance brute diminuée des frais et taxes.

Dans le deuxième tableau, nous distinguons les plus ou moins-values réalisées et les plus ou moins-values latentes. La répartition suit les catégories mentionnées ci-dessus."));
    } else {

    $this->pdf->Row(array("Het bruto beleggingsresultaat (voor aftrek van kosten en belastingen) geeft de waardeontwikkeling weer van uw beleggingen gedurende de rapportageperiode. Dit resultaat bestaat uit de koerswinsten en/of -verliezen op uw beleggingen, het effect van wisselkoersbewegingen, alle coupon- en dividendinkomsten en wijzigingen in de opgelopen rente, voor zover individuele obligaties in uw portefeuille zijn opgenomen.

Het resultaat wordt gecorrigeerd voor alle stortingen en/of onttrekkingen die u gedurende de rapportageperiode heeft verricht. Stortingen dragen niet bij aan het resultaat, terwijl onttrekkingen niet ten koste van het resultaat gaan.

Het nettoresultaat is het brutoresultaat minus de in rekening gebrachte kosten.

In de tweede tabel splitsen we het resultaat op in gerealiseerd (geboekt) en ongerealiseerd resultaat, op basis van de hierboven genoemde categorieën."));
    }
    
    $index=new indexHerberekening();
    $maanden = $index->getMaanden(db2jul($this->rapportageDatumVanaf) ,db2jul($this->rapportageDatum));
    $jaarWaarden=array();
    $somVelden=array('waardeMutatie','stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','ongerealiseerdFondsValuta','ongerealiseerdFonds','ongerealiseerdValuta',
      'rente','belasting','gerealiseerdFonds','gerealiseerdValuta');
    $grafiekData=array();
    foreach($maanden as $periode)
    {
      $maandData=$this->att->BerekenMutaties($periode['start'],$periode['stop'],$this->portefeuille);
      foreach($somVelden as $veld)
      {
        $jaarWaarden[$veld]+=$maandData[$veld];
      }
      if(!isset($jaarWaarden['waardeBegin']))
        $jaarWaarden['waardeBegin']=$maandData['waardeBegin'];
      $jaarWaarden['waardeHuidige']=$maandData['waardeHuidige'];
      $jaarWaarden['performance']=((1+$jaarWaarden['performance']/100)*(1+$maandData['performance']/100)-1)*100;
  
      $grafiekData['Index'][]=$jaarWaarden['performance'];
      $grafiekData['Datum'][]=$periode['stop'];
    }
  

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);


    $gemiddeldeVermogen=$jaarWaarden['resultaatVerslagperiode']/($jaarWaarden['performance']/100);
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->setY(25);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->SetWidths(array(95,115,40,30));
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=6;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->Row(array('','',vertaalTekst('Waarde in EUR', $this->pdf->rapport_taal),'%'));
    unset($this->pdf->fillCell);
    $this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    $this->pdf->CellBorders=array('','U','U','U','U');
    $this->pdf->Row(array('',vertaalTekst('Vermogen', $this->pdf->rapport_taal).' '.date("j",$this->pdf->rapport_datumvanaf)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($jaarWaarden['waardeBegin'],2),''));
    $this->pdf->Row(array('',vertaalTekst('Vermogen', $this->pdf->rapport_taal).' '.date("j",$this->pdf->rapport_datum)." ".
      vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".
      date("Y",$this->pdf->rapport_datum),$this->formatGetal($jaarWaarden['waardeHuidige'],2),''));
    $this->pdf->Row(array('',vertaalTekst('Vermogensmutatie', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['waardeMutatie'],2),''));
    $this->pdf->Row(array('',vertaalTekst('Overzicht stortingen en onttrekkingen', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['stortingen']-$jaarWaarden['onttrekkingen'],2),''));
    unset($this->pdf->CellBorders);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->Row(array('',vertaalTekst('Bruto resultaat beleggingen', $this->pdf->rapport_taal),$this->formatGetal(($jaarWaarden['resultaatVerslagperiode']-$jaarWaarden['kosten']-$jaarWaarden['belasting']),2),$this->formatGetal(($jaarWaarden['resultaatVerslagperiode']-$jaarWaarden['kosten']-$jaarWaarden['belasting'])/$gemiddeldeVermogen*100,2).'%'));
    unset($this->pdf->fillCell);
    $this->pdf->Row(array('',vertaalTekst('Kosten & belastingen', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['kosten']+$jaarWaarden['belasting'],2),$this->formatGetal(($jaarWaarden['kosten']+$jaarWaarden['belasting'])*-1/$gemiddeldeVermogen*-100,2).'%'));
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0],$this->pdf->rapport_donkergrijs[1],$this->pdf->rapport_donkergrijs[2]);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->Row(array('',vertaalTekst('Netto resultaat beleggingen', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['resultaatVerslagperiode'],2),$this->formatGetal($jaarWaarden['performance'],2).'%'));
    
    
    
    $this->pdf->ln(8);
    $this->pdf->fillCell=array(0,1,1,1,1,1,1,1);
    $this->pdf->setAligns(array('L','L','R','R','R'));
    $this->pdf->SetWidths(array(95,50,45,45,45));
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=6;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->Row(array('',vertaalTekst('Herkomst resultaat beleggingen', $this->pdf->rapport_taal),vertaalTekst('Gerealiseerd', $this->pdf->rapport_taal),vertaalTekst('Ongerealiseerd', $this->pdf->rapport_taal),vertaalTekst('Totaal', $this->pdf->rapport_taal)));
    $this->pdf->setAligns(array('L','L','R','R','R','R','R','R'));
    $this->pdf->SetWidths(array(95,50,30,15,30,15,30,15));
    $this->pdf->Row(array('','',vertaalTekst('In EUR', $this->pdf->rapport_taal),'%',vertaalTekst('In EUR', $this->pdf->rapport_taal),'%',vertaalTekst('In EUR', $this->pdf->rapport_taal),'%'));
    unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    //$gerealiseerdResultaat=gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $valuta='EUR',$vanafStartdatum = true,$attributieCategorie = 'Totaal',$gesplitst=true,$debug=false);
    $this->pdf->CellBorders=array('','U','U','U','U','U','U','U');
    
    

    
    $this->pdf->Row(array('',vertaalTekst('Koersresultaat', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['gerealiseerdFonds'],2),
                      $this->formatGetal($jaarWaarden['gerealiseerdFonds']/$gemiddeldeVermogen*100,2).'%',
                      $this->formatGetal($jaarWaarden['ongerealiseerdFonds'],2),
                      $this->formatGetal($jaarWaarden['ongerealiseerdFonds']/$gemiddeldeVermogen*100,2).'%',
                      $this->formatGetal($jaarWaarden['gerealiseerdFonds']+$jaarWaarden['ongerealiseerdFonds'],2),
                      $this->formatGetal(($jaarWaarden['gerealiseerdFonds']+$jaarWaarden['ongerealiseerdFonds'])/$gemiddeldeVermogen*100,2).'%'));
    $this->pdf->Row(array('',vertaalTekst('Valutaresultaat', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['gerealiseerdValuta'],2),
                      $this->formatGetal($jaarWaarden['gerealiseerdValuta']/$gemiddeldeVermogen*100,2).'%',
                      $this->formatGetal($jaarWaarden['ongerealiseerdValuta'],2),
                      $this->formatGetal($jaarWaarden['ongerealiseerdValuta']/$gemiddeldeVermogen*100,2).'%',
                      $this->formatGetal($jaarWaarden['gerealiseerdValuta']+$jaarWaarden['ongerealiseerdValuta'],2),
                      $this->formatGetal(($jaarWaarden['gerealiseerdValuta']+$jaarWaarden['ongerealiseerdValuta'])/$gemiddeldeVermogen*100,2).'%'));
    $this->pdf->Row(array('',vertaalTekst('Inkomstenresultaat', $this->pdf->rapport_taal),$this->formatGetal($jaarWaarden['opbrengsten'],2),
                      $this->formatGetal($jaarWaarden['opbrengsten']/$gemiddeldeVermogen*100,2).'%',
                      '','',
                      $this->formatGetal($jaarWaarden['opbrengsten'],2),
                      $this->formatGetal($jaarWaarden['opbrengsten']/$gemiddeldeVermogen*100,2).'%'));
    $this->pdf->Row(array('',vertaalTekst('Mutatie opgelopen rente', $this->pdf->rapport_taal),'','',
                      $this->formatGetal($jaarWaarden['rente'],2),
                      $this->formatGetal($jaarWaarden['rente']/$gemiddeldeVermogen*100,2).'%',
                      $this->formatGetal($jaarWaarden['rente'],2),
                      $this->formatGetal($jaarWaarden['rente']/$gemiddeldeVermogen*100,2).'%'));
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    
    $this->pdf->rowHeight=$rowHeightBackup;
    unset($this->pdf->fillCell);
    

    $this->pdf->ln(6);
    $this->pdf->SetWidths(array(95,100));
    unset($this->pdf->CellBorders);
    $this->pdf->Row(array('',vertaalTekst('Tijd gewogen netto resultaat portefeuille (cumulatief)', $this->pdf->rapport_taal)));
    $this->pdf->setXY(115,130);
    $this->LineDiagram(100,45,$grafiekData);
    //listarray($indexData);exit;

	}
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=6, $verDiv=6,$jaar=0)
  {
    global $__appvar;
    $legendDatum= $data['Datum'];
    $data = $data['Index'];
    $bereikdata =   $data;
    

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $color=array($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }
    
    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
    if($jaar)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = round(abs($maxVal - $minVal)/$horDiv);
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
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
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
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      
      $n++;
      if($n >20)
        break;
    }
    
    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    for ($i=0; $i<count($data); $i++)
    {
      $this->pdf->setXY($XDiag+($i+0.5)*$unit,$YDiag+$hDiag+1);

      $maand = $__appvar['Maanden'][date('n', strtotime($legendDatum[$i]))];
      $maand = substr($maand,0,3);
      if($i == 2) {
        $maand='mrt';
      }
      $maand = vertaalTekst($maand, $this->pdf->rapport_taal);
      $this->pdf->Cell($unit,4, date("d",db2jul($legendDatum[$i])) . '-' . $maand,0,0,'C'); //Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }
  
    $this->pdf->line($XDiag+$w/2-12, $YDiag+$hDiag+8,$XDiag+$w/2-6, $YDiag+$hDiag+8,$lineStyle );
    $this->pdf->setXY($XDiag,$YDiag+$hDiag+6);
    $this->pdf->Cell($w,4, vertaalTekst('Portefeuille', $this->pdf->rapport_taal),0,0,'C');
  
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
}
?>