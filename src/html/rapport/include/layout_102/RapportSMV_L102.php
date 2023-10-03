<?php

class RapportSMV_L102
{
  function RapportSMV_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
    $grootboeken=array();
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


$query="SELECT Distinct(Rekeningmutaties.Rekening), Rekeningen.Valuta as valuta FROM Rekeningmutaties JOIN Rekeningen on Rekeningen.Rekening=Rekeningmutaties.Rekening WHERE Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND Rekeningen.Portefeuille='".$this->portefeuille."' AND memoriaal=0";

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		  $rekeningen[$data['Rekening']]=$data;

    $query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta as saldo , TijdelijkeRapportage.rekening, TijdelijkeRapportage.valuta, TijdelijkeRapportage.rekening
    FROM
    TijdelijkeRapportage
    WHERE
    TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatumVanaf."' AND
    TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' AND
    TijdelijkeRapportage.`type` =  'rekening'
    ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $startSaldo[$data['rekening']]=$data;
		  $rekeningen[$data['rekening']]=$data;
		}

		$query = "SELECT
    TijdelijkeRapportage.actuelePortefeuilleWaardeEuro as saldo , TijdelijkeRapportage.rekening, TijdelijkeRapportage.valuta, TijdelijkeRapportage.rekening
    FROM
    TijdelijkeRapportage
    WHERE
    TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatum."' AND
    TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' AND
    TijdelijkeRapportage.`type` =  'rekening'
    ".$__appvar['TijdelijkeRapportageMaakUniek'];

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $eindSaldo[$data['rekening']]=$data;
		  $rekeningen[$data['rekening']]=$data;
		}


	//$rekeningen =	 array_merge(array_keys($startSaldo),array_keys($eindSaldo));
	//$rekeningen = array_unique($rekeningen);
	ksort($rekeningen);

		$beginJaar = date("Y", $this->pdf->rapport_datumvanaf);
	  $jaar = date("Y", $this->pdf->rapport_datum);
    $boekjarenFilter='';
    $januariFilter='';

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
            $januariUitluiten = "'$jaren-01-01 00:00:00'";
	      }
	      else
        {
          $jarenString = "'$jaren'";
        }
 	    }

 	   	$boekjarenFilter = "AND ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
	    $januariFilter = "AND Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
	  }

		foreach ($rekeningen as $rekening=>$rekeningData)
		{

		if($startSaldo[$rekening]['saldo'] > 0)
		  $type = "C";
		elseif($startSaldo[$rekening]['saldo'] < 0)
	    $type = "D" ;
	  else
	   $type = "" ;


		$query = "SELECT Rekeningmutaties.Boekdatum, Rekeningmutaties.Bedrag ,".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet as Debet, ".
			"Rekeningmutaties.Credit as Credit, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
			"Rekeningmutaties.bankTransactieId ".
			"FROM Rekeningmutaties,   Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = '$rekening' ".
      $boekjarenFilter.$januariFilter.
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Kruispost=1 OR Grootboekrekeningen.FondsAanVerkoop=1) ".
			"ORDER BY Rekeningmutaties.Boekdatum ,Rekeningmutaties.Omschrijving, Rekeningmutaties.id";

		$DB->SQL($query);
		$DB->Query();

		$DB2 = new DB();
		$n=0;
		$mutatieData = array();
    $mutatieDataPerTransactie=array();
		$newSaldo = $startSaldo[$rekening]['saldo'];
		$somVelden=array('Bedrag','Aantal','Debet','Credit');
		while($mutaties = $DB->nextRecord())
		{
		  //listarray($mutaties);
      if($mutaties['bankTransactieId']=='')
        $mutaties['bankTransactieId']=$mutaties['Omschrijving'];
		  if(!isset($mutatieDataPerTransactie[$mutaties['bankTransactieId']]['Omschrijving']))
      {
        $mutatieDataPerTransactie[$mutaties['bankTransactieId']]['Omschrijving'] = $mutaties['Omschrijving'];
      }
      $mutatieDataPerTransactie[$mutaties['bankTransactieId']]['Boekdatum']=$mutaties['Boekdatum'];
		  foreach($somVelden as $veld)
      {
        $mutatieDataPerTransactie[$mutaties['bankTransactieId']][$veld] += $mutaties[$veld];
      }
      $mutatieDataPerTransactie[$mutaties['bankTransactieId']]['Omschrijving'].="; ".$mutaties['Grootboekrekening']."=".$this->formatGetal($mutaties['Bedrag'],2)."";
		  $mutatieData[]  =  $mutaties;
		  $newSaldo = $newSaldo + $mutaties['Bedrag'];
		  $saldoOpDatum[$mutaties['Boekdatum']] = $newSaldo;
		}

		//listarray($mutatieData);
		unset($lastDatum);
		$n=0;
		//foreach ($mutatieData as $mutaties)
		//{
      
      $mutatieDataPerTransactie= array_reverse($mutatieDataPerTransactie,true);
      $newSaldo='';
    foreach($mutatieDataPerTransactie as $bankTransactieId=>$mutaties)
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
  
       if($newSaldo=='')
       {
         $newSaldo = $saldoOpDatum[$mutaties['Boekdatum']];
         $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
         $this->pdf->Row(array($rekening));//'','','','','',
         $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
       }
         $newSaldoTxt = $this->formatGetal($saldoOpDatum[$mutaties['Boekdatum']], 2);
    
    
         $this->pdf->Row(array(date("d-m-Y", db2jul($mutaties['Boekdatum'])),
                           $newSaldoTxt,
                           $this->formatGetal($mutatieWaarde, 2),
                           $type, $mutaties['Grootboekrekening'],
                           $mutaties['Omschrijving']));
    
         $lastDatum = $mutaties['Boekdatum'];
     //  }
       $n++;
    
		}
      $this->pdf->Row( array(date("d-m-Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($startSaldo[$rekening]['saldo'],2),$this->formatGetal($startSaldo[$rekening]['saldo'],2),$type,'',"Beginwaarde $rekening"));
      
      
      
      if(round($eindSaldo[$rekening]['saldo'],2) != round($newSaldo,2) && $rekeningData['valuta'] == 'EUR')
		{
		  $this->pdf->ln();
		  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
		  $this->pdf->MultiCell(200,4, vertaalTekst("Begin en eindwaarden voor portefeuille",$this->pdf->rapport_taal) . " $rekening " . vertaalTekst("komen niet overeen met de verwachte waarde.",$this->pdf->rapport_taal) . " (".$this->formatGetal($eindSaldo[$rekening]['saldo'],2)." != ".$this->formatGetal($newSaldo,2)." " . vertaalTekst("verschil",$this->pdf->rapport_taal) . ": ".$this->formatGetal($newSaldo-$eindSaldo[$rekening]['saldo'],2).")", 0, "L");
      $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
		  //  echo "<script> alert('Waarden SMV rapport voor portefeuille ".$this->portefeuille." komen niet overeen (".$this->formatGetal($eindSaldo['saldo'],2)." - ".$this->formatGetal($newSaldo,2).")'); </script>";flush();
    //  exit;
		}

		$this->pdf->ln();
  }


  }


}
?>