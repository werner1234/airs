<?php

class RapportTRANSFEE_L102
{
  function RapportTRANSFEE_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
   	$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANSFEE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Overzicht verrichtingen";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
  
    $this->pdf->excelData[]=array("Soort transfer","Effecten","ISIN code","Datum","Aantal","Munt","Koers","Bruto munt","Kosten","TAKS / RV","Netto munt","Netto EUR","Wissel");
  }

  	function formatGetal($waarde, $dec)
	{
	  if(round($waarde,2)== 0.00)
	    return '';
		return number_format($waarde,$dec,",",".");
	}
	
	function row($data)
  {
    $this->lineCounter++;
    if($this->lineCounter%2==0)
      $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
    else
      unset($this->pdf->fillCell);
  
    $this->pdf->Row($data);
  }

  function writeRapport()
  {
    global $__appvar;
    $w=($this->pdf->w-$this->pdf->marge*2)/14;
    $this->pdf->setWidths(array($w-3,$w*2,$w+4,$w-1,$w,$w,$w,$w,$w,$w,$w,$w,$w));
		$this->pdf->setAligns(array('L','L','L','L','R','R','R','R','R','R','R','R','R')) ;

    $this->pdf->AddPage();
      
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		$DB = new DB();
  
    $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);
    $this->lineCounter=0;

		$query = "SELECT Rekeningmutaties.Fonds, Rekeningmutaties.Valuta,Rekeningmutaties.Fondskoers,
		Rekeningmutaties.Transactietype,
Fondsen.ISINCode, ".
			"Rekeningmutaties.Boekdatum, Rekeningmutaties.Bedrag ,".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet as Debet, ".
			"Rekeningmutaties.Credit as Credit, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening,".
			"Rekeningmutaties.Afschriftnummer ".
			"FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
			LEFT JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds ".
			"WHERE Rekeningen.Portefeuille = '".$this->portefeuille."' ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds desc, Rekeningmutaties.bankTransactieId, Rekeningmutaties.Omschrijving, Rekeningmutaties.id";
		$DB->SQL($query);
		$DB->Query();

		$n=0;
		$mutatieData = array();
		while($mutaties = $DB->nextRecord())
		{
		  if($mutaties['Fonds'])
		    $group=$mutaties['Fonds'];
		  else
		    $group='overig';
      $mutatieData[$mutaties['Boekdatum']][$group][$mutaties['Grootboekrekening']]['details'][] = $mutaties;
		  $mutatieData[$mutaties['Boekdatum']][$group][$mutaties['Grootboekrekening']]['Bedrag'] += $mutaties['Bedrag'];
      $mutatieData[$mutaties['Boekdatum']][$group][$mutaties['Grootboekrekening']]['Debet'] += $mutaties['Debet'];
      $mutatieData[$mutaties['Boekdatum']][$group][$mutaties['Grootboekrekening']]['Credit'] += $mutaties['Credit'];
		}
//listarray($mutatieData);
    $overslaan=array();
		foreach ($mutatieData as $boekDatum=>$groupdata)
		{
        foreach($groupdata as $instrument=>$gbwaarden)
        {
          if(isset($gbwaarden['FONDS']))
          {
            foreach($gbwaarden['FONDS']['details'] as $i=>$details)
            {
              $brutoMunt=($gbwaarden['FONDS']['details'][$i]['Credit']-$gbwaarden['FONDS']['details'][$i]['Debet']);
              $kosten=($gbwaarden['KOST']['details'][$i]['Credit']-$gbwaarden['KOST']['details'][$i]['Debet'])+
                      ($gbwaarden['KOBU']['details'][$i]['Credit']-$gbwaarden['KOBU']['details'][$i]['Debet']);
              $taksRv=($gbwaarden['ROER']['details'][$i]['Credit'] -$gbwaarden['ROER']['details'][$i]['Debet'])+
                      ($gbwaarden['RENME']['details'][$i]['Credit']-$gbwaarden['RENME']['details'][$i]['Debet'])+
                      ($gbwaarden['RENOB']['details'][$i]['Credit']-$gbwaarden['RENOB']['details'][$i]['Debet'])+
                      ($gbwaarden['DIVBE']['details'][$i]['Credit']-$gbwaarden['DIVBE']['details'][$i]['Debet']);
              $nettoMunt=$brutoMunt+$kosten+$taksRv;
              $nettoEur=round($nettoMunt*$gbwaarden['FONDS']['details'][$i]['Valutakoers'],2);
              
              $row=array($gbwaarden['FONDS']['details'][$i]['Transactietype'],
                $gbwaarden['FONDS']['details'][$i]['Fonds'],
                $gbwaarden['FONDS']['details'][$i]['ISINCode'],
                date("d-m-Y", db2jul($gbwaarden['FONDS']['details'][$i]['Boekdatum'])),
                $this->formatGetal($gbwaarden['FONDS']['details'][$i]['Aantal'], 0),
                $gbwaarden['FONDS']['details'][$i]['Valuta'],
                $this->formatGetal($gbwaarden['FONDS']['details'][$i]['Fondskoers'], 2),
                $this->formatGetal($brutoMunt,2),
                $this->formatGetal($kosten, 2),
                $this->formatGetal($taksRv, 2),
                $this->formatGetal($nettoMunt, 2),
                $this->formatGetal($nettoEur, 2),
                $this->formatGetal($gbwaarden['FONDS']['details'][$i]['Valutakoers'], 6));
  
              $rowXls=array($gbwaarden['FONDS']['details'][$i]['Transactietype'],
                $gbwaarden['FONDS']['details'][$i]['Fonds'],
                $gbwaarden['FONDS']['details'][$i]['ISINCode'],
                date("d-m-Y", db2jul($gbwaarden['FONDS']['details'][$i]['Boekdatum'])),
                round($gbwaarden['FONDS']['details'][$i]['Aantal'], 0),
                $gbwaarden['FONDS']['details'][$i]['Valuta'],
                round($gbwaarden['FONDS']['details'][$i]['Fondskoers'], 2),
                round($brutoMunt,2),
                round($kosten, 2),
                round($taksRv, 2),
                round($nettoMunt, 2),
                round($nettoEur, 2),
                round($gbwaarden['FONDS']['details'][$i]['Valutakoers'], 6));
  
              $this->pdf->excelData[]=$rowXls;
              
              $this->Row($row);
              
              foreach($groupdata['overig']['KRUIS']['details'] as $index=>$kruispost)
              {
                
                $kruisNettoEur=round(($kruispost['Credit'] - $kruispost['Debet'])*$kruispost['Valutakoers'],2);
             //   echo  $gbwaarden['FONDS']['details'][$i]['Fonds']."| $index | if($kruisNettoEur==$nettoEur) ".$kruispost['Valutakoers']." <br>\n";
                if(abs($kruisNettoEur)==abs($nettoEur))
                {
                  $this->Row(array('KRUIS',
                                    $kruispost['Omschrijving'],
                                    $kruispost['ISINCode'],
                                    date("d-m-Y", db2jul($kruispost['Boekdatum'])),
                                    $this->formatGetal($kruispost['Aantal'], 0),
                                    $kruispost['Valuta'],
                                    $this->formatGetal($kruispost['Fondskoers'], 2),
                                    $this->formatGetal(($kruispost['Credit'] - $kruispost['Debet']), 2),
                                    '',
                                    '',
                                    $this->formatGetal(($kruispost['Credit'] - $kruispost['Debet']), 2),
                                    $this->formatGetal(($kruispost['Credit'] - $kruispost['Debet'])*$kruispost['Valutakoers'], 2),
                                    $this->formatGetal($kruispost['Valutakoers'], 6),
                                  ));
                  $rowXls=array('KRUIS',
                    $kruispost['Omschrijving'],
                    $kruispost['ISINCode'],
                    date("d-m-Y", db2jul($kruispost['Boekdatum'])),
                    round($kruispost['Aantal'], 0),
                    $kruispost['Valuta'],
                    round($kruispost['Fondskoers'], 2),
                    round(($kruispost['Credit'] - $kruispost['Debet']), 2),
                    '',
                    '',
                    round(($kruispost['Credit'] - $kruispost['Debet']), 2),
                    round(($kruispost['Credit'] - $kruispost['Debet'])*$kruispost['Valutakoers'], 2),
                    round($kruispost['Valutakoers'], 6),
                  );
                  $this->pdf->excelData[]=$rowXls;
                //  unset($groupdata['overig']['KRUIS']['details'][$i]);
                  $overslaan[$boekDatum]['overig']['KRUIS']['details'][$index]=1;
                }
              }
           //   exit;
            }
          }
          else
          {
            $keys=array_keys($gbwaarden);
            
            if(in_array('DIV',$keys))
            {
              $key='DIV';
            }
            elseif(in_array('RENOB',$keys))
            {
              $key='RENOB';
            }
            else
            {
              $key='';
            }
        
            
            if($instrument<>'overig' && ($key=='RENOB' || $key='DIV'))
            {
          //    listarray($gbwaarden);
              foreach($gbwaarden[$key]['details'] as $i=>$details)
              {
  
                $brutoMunt=($gbwaarden[$key]['details'][$i]['Credit']-$gbwaarden[$key]['details'][$i]['Debet']);
                $kosten=($gbwaarden['KOST']['details'][$i]['Credit']-$gbwaarden['KOST']['details'][$i]['Debet'])+
                  ($gbwaarden['KOBU']['details'][$i]['Credit']-$gbwaarden['KOBU']['details'][$i]['Debet']);
                $taksRv=($gbwaarden['ROER']['details'][$i]['Credit'] -$gbwaarden['ROER']['details'][$i]['Debet'])+
                  ($gbwaarden['BTLBR']['details'][$i]['Credit']-$gbwaarden['BTLBR']['details'][$i]['Debet'])+
                  ($gbwaarden['DIVBE']['details'][$i]['Credit']-$gbwaarden['DIVBE']['details'][$i]['Debet']);
                $nettoMunt=$brutoMunt+$kosten+$taksRv;
                $nettoEur=round($nettoMunt*$gbwaarden[$key]['details'][$i]['Valutakoers'],2);
                
                $this->Row(array($key,
                                  $gbwaarden[$key]['details'][$i]['Fonds'],
                                  $gbwaarden[$key]['details'][$i]['ISINCode'],
                                  date("d-m-Y", db2jul($gbwaarden[$key]['details'][$i]['Boekdatum'])),
                                  $this->formatGetal($gbwaarden[$key]['details'][$i]['Aantal'], 0),
                                                     $gbwaarden[$key]['details'][$i]['Valuta'],
                                  $this->formatGetal($gbwaarden[$key]['details'][$i]['Fondskoers'], 2),
                                  $this->formatGetal($brutoMunt,2),
                                  $this->formatGetal($kosten, 2),
                                  $this->formatGetal($taksRv, 2),
                                  $this->formatGetal($nettoMunt, 2),
                                  $this->formatGetal($nettoEur, 2),
                                  $this->formatGetal($gbwaarden[$key]['details'][$i]['Valutakoers'], 6),
                                ));
                $rowXls=array($key,
                  $gbwaarden[$key]['details'][$i]['Fonds'],
                  $gbwaarden[$key]['details'][$i]['ISINCode'],
                  date("d-m-Y", db2jul($gbwaarden[$key]['details'][$i]['Boekdatum'])),
                  round($gbwaarden[$key]['details'][$i]['Aantal'], 0),
                  $gbwaarden[$key]['details'][$i]['Valuta'],
                  round($gbwaarden[$key]['details'][$i]['Fondskoers'], 2),
                  round($brutoMunt,2),
                  round($kosten, 2),
                  round($taksRv, 2),
                  round($nettoMunt, 2),
                  round($nettoEur, 2),
                  round($gbwaarden[$key]['details'][$i]['Valutakoers'], 6),
                );
                $this->pdf->excelData[]=$rowXls;
              }
            }
            else
            {
              foreach ($gbwaarden as $gb => $details)
              {
                foreach ($details['details'] as $i => $detail)
                {
                  if(isset($overslaan[$boekDatum][$instrument][$gb]['details'][$i]))
                    continue;
                  $this->Row(array($gb,
                                    $detail['Omschrijving'],
                                    '',
                                    date("d-m-Y", db2jul($detail['Boekdatum'])),
                                    '',//$this->formatGetal($detail['Aantal'], 0),
                                    $detail['Valuta'],
                                    '',//$this->formatGetal($detail['Fondskoers'], 2),
                                    $this->formatGetal(($detail['Credit'] - $detail['Debet']), 2),
                                    '',//$this->formatGetal($detail['Bedrag'], 2),
                                    '',
                                    $this->formatGetal(($detail['Credit'] - $detail['Debet']), 2),
                                    $this->formatGetal(($detail['Credit'] - $detail['Debet'])*$detail['Valutakoers'], 2),
                                    $this->formatGetal($detail['Valutakoers'], 6),
                                    ''));
                  $rowXls=array($gb,
                    $detail['Omschrijving'],
                    '',
                    date("d-m-Y", db2jul($detail['Boekdatum'])),
                    '',//round($detail['Aantal'], 0),
                    $detail['Valuta'],
                    '',//round($detail['Fondskoers'], 2),
                    round(($detail['Credit'] - $detail['Debet']), 2),
                    '',//round($detail['Bedrag'], 2),
                    '',
                    round(($detail['Credit'] - $detail['Debet']), 2),
                    round(($detail['Credit'] - $detail['Debet'])*$detail['Valutakoers'], 2),
                    round($detail['Valutakoers'], 6),
                    '');
                  $this->pdf->excelData[]=$rowXls;
      
      
                }
              }
            }
          }
        }
        
		}
 
		$this->pdf->ln();
  
  
    unset($this->pdf->fillCell);
  }


}
?>