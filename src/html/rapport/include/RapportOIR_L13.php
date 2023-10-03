<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
class RapportOIR_L13
{
	function RapportOIR_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIR";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Mutatie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' || $this->pdf->rapportageValuta != '')
		 $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec, $nulOnderdrukken=false)
	{
	  if($nulOnderdrukken==true && round($waarde,2)==0.00)
	    return '';
	  else
		return number_format($waarde,$dec,",",".");
	}

	function printCol($row, $data, $type = "tekst")
	{
		$y = $this->pdf->getY();
		$start = $this->pdf->marge;
		for($tel=0;$tel <$row;$tel++)
		{
			$start += $this->pdf->widthB[$tel];
		}
		$writerow = $this->pdf->widthB[($tel)];
		$end = $start + $writerow;

		// print cell , 1
		$this->pdf->Cell($start-$this->pdf->marge,4,"",0,0,"R");
		$this->pdf->Cell($writerow,4,$data, 0,0, "R");

		if($type == "totaal" || $type == "subtotaal" || $type == "grandtotaal")
		{
			$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
			$this->pdf->ln();
			if($type == "totaal")
			{
				$this->pdf->setDash(1,1);
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->setDash();
			}
			else if($type == "grandtotaal")
			{
				$this->pdf->Line($start+2,$this->pdf->GetY(),$end,$this->pdf->GetY());
				$this->pdf->Line($start+2,$this->pdf->GetY()+1,$end,$this->pdf->GetY()+1);
			}
		}
		$this->pdf->setY($y);
	}

	//function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)
  function printTotaal($title, $waarden,$grandtotal=false)
	{
		$hoogte = 16;

		if(($this->pdf->GetY() + $hoogte) > $this->pdf->pagebreak)
		{
			$this->pdf->AddPage();
			$this->pdf->ln();
		}

		if(!$grandtotal)
			$totType = "totaal";
		else
			$totType = "grandtotaal";

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->printCol(1,$title,"tekst");
		//if($totaalA <>0)
		$this->printCol(3,$this->formatGetal( $waarden['Waarde'],$this->pdf->rapport_MUT2_decimaal),$totType);
		//if($totaalB <>0)
		$this->printCol(4,$this->formatGetal( $waarden['WaardeKosten'],$this->pdf->rapport_MUT2_decimaal),$totType);

		//if($totaalC <>0)
	//		$this->printCol(4,$this->formatGetal($totaalC,$this->pdf->rapport_MUT2_decimaal),$totType);
		if( $waarden['WaardeBelasting'] <>0)
			$this->printCol(5,$this->formatGetal( $waarden['WaardeBelasting'],$this->pdf->rapport_MUT2_decimaal),$totType);
		if( $waarden['WaardeNetto'] <>0)
			$this->printCol(6,$this->formatGetal( $waarden['WaardeNetto'],$this->pdf->rapport_MUT2_decimaal),$totType);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$DB = new DB();
		// voor data
		$this->pdf->widthA = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignA = array('R','L','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthB = array(25,100,25,25,25,25,25,25);
		$this->pdf->alignB = array('R','L','R','R','R','R','R','R');

		$this->pdf->AddPage();
    
    $extraquery='';
    if($this->pdf->selectData['GrootboekTm'])
      $extraquery .= " AND (Rekeningmutaties.Grootboekrekening >= '".$this->pdf->selectData['GrootboekVan']."' AND Rekeningmutaties.Grootboekrekening  <= '".$this->pdf->selectData['GrootboekTm']."') ";
    
    foreach ($this->pdf->lastPOST as $key=>$value)
    {
      if(substr($key,0,4)=='MUT_' && $value==1)
      {
        $grootboeken[]=substr($key,4);
        $filter = 1;
      }
    }
    
    if($filter == 1)
    {
      $grootboekSelectie = implode('\',\'',$grootboeken);
      $extraquery .= "AND Rekeningmutaties.Grootboekrekening IN('$grootboekSelectie')  ";
    }

    // loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT  Rekeningmutaties.id,".
			"IF(Grootboekrekeningen.Opbrengst = 1, 1, IF(Grootboekrekeningen.Kosten = 1, 2, 3)) AS displayOrder, ".
			"Rekeningmutaties.Boekdatum, ".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers $koersQuery as Debet, ".
			"Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers $koersQuery as Credit, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Rekeningmutaties.Afschriftnummer, 
			 Rekeningmutaties.bankTransactieId, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
      "Rekeningmutaties.Fonds, ".
			"Grootboekrekeningen.Opbrengst, ".
			"Grootboekrekeningen.Kosten, ".
			"Grootboekrekeningen.Afdrukvolgorde ".
			"FROM Rekeningmutaties, Rekeningen,  Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"AND Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL $extraquery".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Onttrekking='1' OR Grootboekrekeningen.Kruispost = '1') ".
			"ORDER BY displayOrder, Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum, Rekeningmutaties.id";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$data=array();
    $totaal=array();
    $gbTotaal=array();
		while($mutaties = $DB->nextRecord())
		{
		  //if($mutaties['Grootboekrekening']=='DIV')
      //  $mutaties['Omschrijving'] = str_replace("Dividend","",$mutaties['Omschrijving']);
      if($mutaties['gbOmschrijving']=='Bankkosten')
        $mutaties['gbOmschrijving']='Servicekosten Bank';
      
      $mutaties['Waarde']=($mutaties['Credit']-$mutaties['Debet']);
		  if($mutaties['bankTransactieId']<>'' && in_array($mutaties['Grootboekrekening'],array('DIV','DIVBE')))
      {
        $id = $mutaties['bankTransactieId'] . "_" . substr($mutaties['Boekdatum'], 0, 10) . "_" . $mutaties['Fonds'];
        if(!isset($data[$id]['Omschrijving']))
          $data[$id]=$mutaties;
        if ($mutaties['Grootboekrekening'] == 'DIVBE')
        {
          $data[$id]['WaardeBelasting'] += ($mutaties['Debet']-$mutaties['Credit']);
        }
        elseif ($mutaties['Opbrengst'] == 1)
        {
          $data[$id]['WaardeOpbrengst'] += ($mutaties['Debet']-$mutaties['Credit']);
        }
        if ($mutaties['Kosten'] == 1)
        {
          $data[$id]['WaardeKosten'] += ($mutaties['Debet']-$mutaties['Credit']);
          $data[$id]['Waarde']='';
        }
        if ($mutaties['Grootboekrekening'] == 'DIVBE' && $data[$id]['WaardeOpbrengst']==0)
          $data[$id]['WaardeNetto']=+$data[$id]['Waarde'];
        else
          $data[$id]['WaardeNetto']=+$data[$id]['Waarde']-$data[$id]['WaardeBelasting'];
      }
      else
      {
        $id = $mutaties['id'];
        if(!isset($data[$id]['Omschrijving']))
          $data[$id]=$mutaties;
        if ($mutaties['Opbrengst'] == 1)
        {
          $data[$id]['WaardeOpbrengst'] += ($mutaties['Credit'] - $mutaties['Debet']);
        }
        if ($mutaties['Kosten'] == 1)
        {
          $data[$id]['WaardeKosten'] += ($mutaties['Debet']-$mutaties['Credit']);
          $data[$id]['Waarde']='';
        }
      }
		}
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $lastCategorie='';
    $lastGb='';
    foreach($data as $regel)
    {
      if($lastCategorie <> $regel['gbOmschrijving'])
      {
        if($lastCategorie<>'')
        {
          $this->printTotaal('Totaal '.$lastCategorie, $gbTotaal[$lastGb]);
        }
        $this->printKop(vertaalTekst($regel['gbOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $lastCategorie=$regel['gbOmschrijving'];
        $lastGb=$regel['Grootboekrekening'];
      }

      $this->pdf->row(array(date("d-m",db2jul($regel['Boekdatum'])),
                        vertaalTekst($regel['Omschrijving'],$this->pdf->rapport_taal),
                        '',
                        $this->formatGetal( $regel['Waarde'],2,true),
                        $this->formatGetal( $regel['WaardeKosten'],2,true),
                        $this->formatGetal( $regel['WaardeBelasting'],2,true),
                        $this->formatGetal( $regel['WaardeNetto'],2,true)));

      $gbTotaal[$regel['Grootboekrekening']]['Waarde'] += $regel['Waarde'];
      $gbTotaal[$regel['Grootboekrekening']]['WaardeKosten'] += $regel['WaardeKosten'];
      $gbTotaal[$regel['Grootboekrekening']]['WaardeBelasting'] += $regel['WaardeBelasting'];
      $gbTotaal[$regel['Grootboekrekening']]['WaardeNetto'] += $regel['WaardeNetto'];
  
      $totaal['Waarde'] += $regel['Waarde'];
      $totaal['WaardeKosten'] += $regel['WaardeKosten'];
      $totaal['WaardeBelasting'] += $regel['WaardeBelasting'];
      $totaal['WaardeNetto'] += $regel['WaardeNetto'];
    }
    if($lastGb<>'')
    {
      $this->printTotaal('Totaal '.$regel['gbOmschrijving'], $gbTotaal[$lastGb]);
    }
    $this->pdf->ln();
    $this->printTotaal('Totaal Generaal', $totaal,true);

	}
}
?>