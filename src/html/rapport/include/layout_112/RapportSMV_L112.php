<?php

class RapportSMV_L112
{
  function RapportSMV_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
   	$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SMV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Saldomutatieverloop";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
  }

  	function formatGetal($waarde, $dec)
	{
	  if(round($waarde,2)== 0.00)
	    return '';
		return number_format($waarde,$dec,",",".");
	}

  function writeRapport()
  {
    global $__appvar;

    $this->pdf->setWidths(array(25,30,30,10,15,150));
		$this->pdf->setAligns(array('L','R','R','C','L','L')) ;

    if($this->pdf->portefeuilledata['Layout'] == 13)
      $this->pdf->AddPage('P');
    else
      $this->pdf->AddPage();
      
    $this->pdf->templateVars['SMVPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['SMVPaginas']=$this->pdf->rapport_titel;

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		$DB = new DB();
		if($this->pdf->lastPOST['GB_overige'] == '1')
		{
		  $DB->SQL("SELECT DISTINCT(Grootboekrekening) AS Grootboekrekening  FROM Grootboekrekeningen WHERE AND Rekeningmutaties.Grootboekrekening NOT IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) ORDER BY Grootboekrekening ");
      $DB->Query();
      while($gbData = $DB->nextRecord())
        $grootboeken[] = strtoupper($gbData['Grootboekrekening']);
		}
		if($this->pdf->lastPOST['GB_STORT_ONTTR'] == '1')
		{
		  $grootboeken[]='STORT';
		  $grootboeken[]='ONTTR';
		}

		if(!isset($this->pdf->lastPOST['GB_overige']) && !isset($this->pdf->lastPOST['GB_STORT_ONTTR']))
		{
		  $DB->SQL("SELECT DISTINCT(Grootboekrekening) AS Grootboekrekening  FROM Grootboekrekeningen  ORDER BY Grootboekrekening ");
      $DB->Query();
      while($gbData = $DB->nextRecord())
        $grootboeken[] = strtoupper($gbData['Grootboekrekening']);
		}


    $DB = new DB();
  
    if(is_array($this->pdf->portefeuilles) AND count($this->pdf->portefeuilles) > 0 )
    {
      $portefeuilleFilter=" Rekeningen.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
    }
    else
    {
      $portefeuilleFilter=" Rekeningen.Portefeuille = '".$this->portefeuille."'";
    }


$query="SELECT Distinct(Rekeningmutaties.Rekening), Rekeningen.Valuta as valuta, Rekeningen.Portefeuille,Portefeuilles.selectieveld1,Portefeuilles.Depotbank
FROM Rekeningmutaties
JOIN Rekeningen on Rekeningen.Rekening=Rekeningmutaties.Rekening
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
WHERE Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND $portefeuilleFilter AND memoriaal=0 ORDER BY  Rekeningen.Portefeuille, Rekeningen.Rekening";

    $DB->SQL($query);
		$DB->Query();
    $portefeuilles=array();
    $depotbanken=array();
    $startSaldo=array();
		while($data = $DB->nextRecord())
    {
      $portefeuilles[$data['Portefeuille']][$data['Rekening']] = $data;
      $depotbanken[$data['Portefeuille']]=$data;
    }
    $query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta as saldo , TijdelijkeRapportage.rekening, TijdelijkeRapportage.valuta, TijdelijkeRapportage.rekening,Rekeningen.Portefeuille,Portefeuilles.selectieveld1,Portefeuilles.Depotbank
    FROM
    TijdelijkeRapportage
     JOIN Rekeningen ON TijdelijkeRapportage.Rekening=Rekeningen.Rekening
     JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
    WHERE
    TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatumVanaf."' AND
   $portefeuilleFilter AND
    TijdelijkeRapportage.`type` =  'rekening'
    ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $startSaldo[$data['rekening']]=$data;
      $portefeuilles[$data['Portefeuille']][$data['rekening']]=$data;
      $depotbanken[$data['Portefeuille']]=$data;
		}

		$query = "SELECT
    TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as saldo , TijdelijkeRapportage.rekening, TijdelijkeRapportage.valuta, TijdelijkeRapportage.rekening,Rekeningen.Portefeuille,Portefeuilles.selectieveld1,Portefeuilles.Depotbank
    FROM
    TijdelijkeRapportage
    JOIN Rekeningen ON TijdelijkeRapportage.Rekening=Rekeningen.Rekening
    JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
    WHERE
    TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatum."' AND
   $portefeuilleFilter AND
    TijdelijkeRapportage.`type` =  'rekening'
    ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $eindSaldo[$data['rekening']]=$data;
      $portefeuilles[$data['Portefeuille']][$data['rekening']]=$data;
      $depotbanken[$data['Portefeuille']]=$data;
		}


	//$rekeningen =	 array_merge(array_keys($startSaldo),array_keys($eindSaldo));
	//$rekeningen = array_unique($rekeningen);
//	ksort($rekeningen);

		$beginJaar = date("Y", $this->pdf->rapport_datumvanaf);
	  $jaar = date("Y", $this->pdf->rapport_datum);

  	if ($beginJaar != '1970' && $jaar != $beginJaar)
  	{
  	  for($jaren=$beginJaar;$jaren <= $jaar; $jaren++)
  	  {
  	    if(isset($jarenString))
  	    {
  	      $jarenString .= ",'$jaren'";
          if(isset($januariUitluiten))
            $januariUitluiten .=",'$jaren-01-01 00:00:00'";
          else
            $januariUitluiten .= "'$jaren-01-01 00:00:00'";
	      }
	      else
          $jarenString .= "'$jaren'";
 	    }

 	   	$boekjarenFilter = "AND ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
	    $januariFilter = "AND Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
	  }
  
    $lastPortefeuille='';
  	//listarray($portefeuilles);
		foreach ($portefeuilles as $portefeuille=>$rekeningen)
    {
  
      if($portefeuille<>$lastPortefeuille)
      {
        if($lastPortefeuille<>'')
          $this->pdf->ln();
        $selectieveld=$depotbanken[$portefeuille]['selectieveld1'];
        $depotbankveld=$depotbanken[$portefeuille]['Depotbank'];
        $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
        $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 4, 'F');
        $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
        $this->pdf->Cell(45,4,($selectieveld<>''?$selectieveld:$portefeuille), 0,0, "L");
        $this->pdf->Cell(45,4,$depotbankveld, 0, 1,"L");
        $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
        $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      }
      $lastPortefeuille=$portefeuille;
      
      foreach ($rekeningen as $rekening => $rekeningData)
      {
        if ($startSaldo[$rekening]['saldo'] > 0)
        {
          $type = "C";
        }
        elseif ($startSaldo[$rekening]['saldo'] < 0)
        {
          $type = "D";
        }
        else
        {
          $type = "";
        }
        
        $this->pdf->Row(array(date("d-m-Y", $this->pdf->rapport_datumvanaf), $this->formatGetal($startSaldo[$rekening]['saldo'], 2), $this->formatGetal($startSaldo[$rekening]['saldo'], 2), $type, '', "Beginwaarde $rekening"));
        
        
        $query = "SELECT  Rekeningmutaties.Valuta ," .
          "Rekeningmutaties.Boekdatum, Rekeningmutaties.Bedrag ," .
          "Rekeningmutaties.Omschrijving ," .
          "ABS(Rekeningmutaties.Aantal) AS Aantal, " .
          "Rekeningmutaties.Debet as Debet, " .
          "Rekeningmutaties.Credit as Credit, " .
          "Rekeningmutaties.Valutakoers, " .
          "Rekeningmutaties.Rekening, " .
          "Rekeningmutaties.Grootboekrekening, " .
          "Rekeningmutaties.Afschriftnummer, " .
          "Grootboekrekeningen.Omschrijving AS gbOmschrijving, " .
          "Grootboekrekeningen.Opbrengst, " .
          "Grootboekrekeningen.Kosten, " .
          "Grootboekrekeningen.Afdrukvolgorde " .
          "FROM Rekeningmutaties,   Grootboekrekeningen " .
          "WHERE Rekeningmutaties.Rekening = '$rekening' " .
          $boekjarenFilter . $januariFilter .
          "AND Rekeningmutaties.Verwerkt = '1' " .
          "AND Rekeningmutaties.Boekdatum > '" . $this->rapportageDatumVanaf . "' " .
          "AND Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "' " .
          "AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening " .
          "AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Kruispost=1 OR Grootboekrekeningen.FondsAanVerkoop=1) " .
          "ORDER BY Rekeningmutaties.Boekdatum ,Rekeningmutaties.Omschrijving, Rekeningmutaties.id";
        
        $DB->SQL($query);
        $DB->Query();
        
        $DB2 = new DB();
        $n = 0;
        $mutatieData = array();
        $newSaldo = $startSaldo[$rekening]['saldo'];
        while ($mutaties = $DB->nextRecord())
        {
          $mutatieData[] = $mutaties;
          $newSaldo = $newSaldo + $mutaties['Bedrag'];
          $saldoOpDatum[$mutaties['Boekdatum']] = $newSaldo;
        }
        
        //listarray($mutatieData);
        unset($lastDatum);
        $n = 0;
        $aantal = count($mutatieData);
        foreach ($mutatieData as $mutaties)
        {
          //$mutatieWaarde = $mutaties['Credit'] * $mutaties['Valutakoers'] - $mutaties['Debet'] * $mutaties['Valutakoers'];
          $mutatieWaarde = $mutaties['Bedrag'];
          
          if ($mutatieWaarde > 0)
          {
            $type = "C";
          }
          elseif ($mutatieWaarde < 0)
          {
            $type = "D";
          }
          else
          {
            $type = "";
          }
          
          $newSaldo = $mutaties['afschriftTotaal'];
          $newSaldo = $saldoOpDatum[$mutaties['Boekdatum']];
          //  $newSaldo = $newSaldo + $mutatieWaarde;
          if (in_array(strtoupper($mutaties['Grootboekrekening']), $grootboeken))
          {
            
            $newSaldoTxt = $this->formatGetal($saldoOpDatum[$mutaties['Boekdatum']], 2);
            
            
            $this->pdf->Row(array(date("d-m-Y", db2jul($mutaties['Boekdatum'])),
                              $newSaldoTxt,
                              $this->formatGetal($mutatieWaarde, 2),
                              $type, $mutaties['Grootboekrekening'],
                              $mutaties['Omschrijving']));
            
            $lastDatum = $mutaties['Boekdatum'];
          }
          $n++;
        }
        
        
        if (round($eindSaldo[$rekening]['saldo'], 2) != round($newSaldo, 2) && $rekeningData['valuta'] == 'EUR')
        {
          $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font, "B", $this->pdf->rapport_fontsize);
          $this->pdf->MultiCell(200, 4, "Begin en eindwaarden voor portefeuille $rekening komen niet overeen met de verwachte waarde. (" . $this->formatGetal($eindSaldo[$rekening]['saldo'], 2) . " != " . $this->formatGetal($newSaldo, 2) . " verschil: " . $this->formatGetal($newSaldo - $eindSaldo[$rekening]['saldo'], 2) . ")", 0, "L");
          $this->pdf->SetFont($this->pdf->rapport_font, "", $this->pdf->rapport_fontsize);
          //  echo "<script> alert('Waarden SMV rapport voor portefeuille ".$this->portefeuille." komen niet overeen (".$this->formatGetal($eindSaldo['saldo'],2)." - ".$this->formatGetal($newSaldo,2).")'); </script>";flush();
          //  exit;
        }
        
        $this->pdf->ln();
      }
    }

  }


}
?>