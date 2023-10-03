<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L116
{
	function RapportOIB_L116($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }

    $query = "SELECT 
       beleggingscategorie, beleggingscategorieOmschrijving,
       hoofdcategorie, hoofdcategorieOmschrijving, 
       
        SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
		    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro
       
       FROM TijdelijkeRapportage ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      "GROUP BY TijdelijkeRapportage.beleggingscategorie".
      " ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde asc, TijdelijkeRapportage.beleggingscategorieVolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

    $pieDataSet = array();
    $tabelDataSet = array();
    $totalenDataSet = array();

    while($categorien = $DB->NextRecord()) {
      if ( $categorien['hoofdcategorie'] === 'H-Liq' ) continue;

      $kleur = $this->kleuren['alle'][$categorien['beleggingscategorie']];

      $pieDataSet[$categorien['hoofdcategorieOmschrijving']][$categorien['beleggingscategorie']] = array(
        'kleur'               => array(
          $kleur['R']['value'],
          $kleur['G']['value'],
          $kleur['B']['value']
        ),
        'waardeEUR'           => $categorien['actuelePortefeuilleWaardeEuro'],
        'percentage'          => 0
      );

      if ( ! isset ($pieDataSet[$categorien['hoofdcategorieOmschrijving']]['totaalWaardeHoofdCategorie']) ) {
        $pieDataSet[$categorien['hoofdcategorieOmschrijving']]['totaalWaardeHoofdCategorie'] = $categorien['actuelePortefeuilleWaardeEuro'];
      } else {
        $pieDataSet[$categorien['hoofdcategorieOmschrijving']]['totaalWaardeHoofdCategorie'] +=$categorien['actuelePortefeuilleWaardeEuro'];
      }


      $this->hoofdCategorieOmschrijving[$categorien['hoofdcategorieOmschrijving']] = $categorien['hoofdcategorie'];
      $this->beleggingscategorieOmschrijving[$categorien['beleggingscategorie']] = $categorien['beleggingscategorieOmschrijving'];
    }

    $this->pdf->ln(10);

    foreach ( $pieDataSet as $hoofdCategorie => $beleggingscategorieen ) {

      foreach ( $beleggingscategorieen as $beleggingscategorie => $beleggingscategorieData ){
        $beleggingscategorieen[$beleggingscategorie]['percentage'] = $beleggingscategorieData['waardeEUR'] / ($beleggingscategorieen['totaalWaardeHoofdCategorie'] / 100);
      }
      unset($beleggingscategorieen['totaalWaardeHoofdCategorie']);
      $y = $this->pdf->getY();

      $this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize+2);
      $this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
      $this->pdf->SetX($this->pdf->marge);
      $this->pdf->MultiCell(160,4, vertaalTekst($hoofdCategorie,$this->pdf->rapport_taal), 0, "L");
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
      $this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),297-$this->pdf->marge ,$this->pdf->GetY());

      $this->printPie($beleggingscategorieen, $this->pdf->marge+15, $y+5, '');
      $this->toonTabel($beleggingscategorieen,$this->pdf->marge+70, $y+10,'beleggingscategorie', $this->kleuren['alle']);
      $this->pdf->ln(8);
    }

	}


  function printPie($kleurdata, $xstart, $ystart, $titel)
  {
    $col1 = array(255, 0, 0); // rood
    $col2 = array(0, 255, 0); // groen
    $col3 = array(255, 128, 0); // oranje
    $col4 = array(0, 0, 255); // blauw
    $col5 = array(255, 255, 0); // geel
    $col6 = array(255, 0, 255); // paars
    $col7 = array(128, 128, 128); // grijs
    $col8 = array(128, 64, 64); // bruin
    $col9 = array(255, 255, 255); // wit
    $col0 = array(0, 0, 0); //zwart
    $standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);
    $pieData = array();
    if ($kleurdata)
    {
      $sorted = array();
      $percentages = array();
      $kleur = array();
      $valuta = array();

      foreach ($kleurdata as $key => $data)
      {
        $percentages[] = $data['percentage'];
        $kleur[] = $data['kleur'];
        $valuta[] = $key;
      }
      //arsort($percentages);

      foreach ($percentages as $key => $percentage)
      {
        $sorted[$valuta[$key]]['kleur'] = $kleur[$key];
        $sorted[$valuta[$key]]['percentage'] = $percentage;
      }
      $kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
      $grafiekKleuren = array();

      $a = 0;
      foreach ($kleurdata as $key => $value)
      {
        if ($value['kleur'][0] == 0 && $value['kleur'][1] == 0 && $value['kleur'][2] == 0)
        {
          $grafiekKleuren[] = $standaardKleuren[$a];
        }
        else
        {
          $grafiekKleuren[] = array($value['kleur'][0], $value['kleur'][1], $value['kleur'][2]);
        }
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
    {
      $grafiekKleuren = $standaardKleuren;
    }

    // $this->pdf->SetTextColor(255, 255, 255);

    $trapport_printpie = true;
    foreach ($pieData as $key => $value)
    {
      if (round($value,2) < 0)
      {
        $trapport_printpie = false;
      }
    }

    if ($trapport_printpie)
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();

      $this->pdf->SetFont($this->pdf->rapport_font,'B',10);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);


      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $this->pdf->SetXY($xstart, $ystart + 4);
      $this->PieChart(30, 55, $pieData, '%l (%p)', $grafiekKleuren);
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
    else
    {
      $this->pdf->SetXY($xstart, $ystart - 4);
      $y = $this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', 10);
      if($this->pdf->portefeuilledata['Layout']==95)
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'], $this->pdf->rapport_kop_fontcolor['g'], $this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50, 4, vertaalTekst($titel, $this->pdf->rapport_taal), 0, 1, "C");
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

      $this->pdf->SetXY($xstart-10, $ystart);
      $this->BarDiagram(70, $pieData, '%l (%p)', $grafiekKleuren, '');
      $this->pdf->setY($y);
      $this->pdf->SetLineWidth($this->pdf->lineWidth);
    }
  }

  function toonTabel($regels, $xOffset, $ypos = 80, $titel, $kleuren)
  {
    $this->pdf->setWidths(array($xOffset + 5, 90, 30, 40));
    $this->pdf->setAligns(array('L', 'L', 'R', 'R'));
    $this->pdf->setXY($this->pdf->marge, $ypos);

    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'], $this->pdf->rapport_fontcolor['g'], $this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array('', '', vertaalTekst('EUR', $this->pdf->rapport_taal), '%'));
    $this->pdf->ln(1);
    $this->pdf->Line($xOffset + 8 ,$this->pdf->GetY(),$xOffset +8+90+30+40+5 ,$this->pdf->GetY());
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    $this->pdf->ln(1);
    $totalen = array();

    foreach ($regels as $categorie => $data)
    {
      if (!isset($kleuren[$categorie]))
      {
        $kleuren[$categorie] = array(128, 128, 128);
      }
    }

    foreach($regels as $categorie=>$data)
    {
      $data = $regels[$categorie];
      $kleur = array($kleuren[$categorie]['R']['value'], $kleuren[$categorie]['G']['value'], $kleuren[$categorie]['B']['value']);
      $this->pdf->rect($this->pdf->getX() + $xOffset, $this->pdf->getY() + 1, 4, 4, 'DF', '',$kleur);
      $this->pdf->ln(1);
      $this->pdf->row(array('', vertaalTekst($this->beleggingscategorieOmschrijving[$categorie], $this->pdf->rapport_taal), $this->formatGetal($data['waardeEUR'], 2), $this->formatGetal($data['percentage'], 2) . '%'));
      $totalen['waardeEUR'] += $data['waardeEUR'];
      $totalen['percentage'] += $data['percentage'];
    }
    $this->pdf->setWidths(array($xOffset , 90+5, 30, 40));
    $this->pdf->ln(2);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);

    $this->pdf->Line($xOffset + 8 ,$this->pdf->GetY(),$xOffset +8+90+30+40+5 ,$this->pdf->GetY());
    $this->pdf->ln(1);
    $this->pdf->row(array('', vertaalTekst('Totaal', $this->pdf->rapport_taal), $this->formatGetal($totalen['waardeEUR'], 2), $this->formatGetal($totalen['percentage'], 2) . '%'));
    $this->pdf->SetFont($this->pdf->rapport_font, $this->pdf->rapport_fontstyle, $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
  }

  function PieChart($w, $h, $data, $format, $colors = null)
  {

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    //	$this->pdf->SetLegends($data,$format);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 2, $h - $margin * 2); //

    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < count($data); $i++)
      {
        $gray = $i * intval(255 / count($data));
        $colors[$i] = array($gray, $gray, $gray);
      }
    }

    //Sectors
    $sum = array_sum($data);
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $angle = 0;
    //echo "<br>\n";
    foreach ($data as $val)
    {
      //$angle = round(($val * 360) / doubleval($sum),1);
      $angle = round(floor(($val * 360) / doubleval($sum) * 5) / 5, 1);
      //echo "$angle <br>\n"; ob_flush();
      if ($angle > 1)
      {
        $angleEnd = $angleStart + $angle;
        // echo "$angleEnd = $angleStart + $angle <br>\n"; ob_flush();
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      else
      {
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }

  }


}
?>