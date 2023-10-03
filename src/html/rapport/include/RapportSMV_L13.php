<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/08 15:17:46 $
 		File Versie					: $Revision: 1.24 $

 		$Log: RapportSMV_L13.php,v $
 		Revision 1.24  2020/07/08 15:17:46  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2020/06/06 15:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2019/10/09 15:13:20  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/11/02 06:52:00  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2017/11/01 16:51:06  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/12/19 09:11:09  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/07/06 12:38:11  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2012/09/05 12:24:25  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2012/04/04 12:20:06  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2011/04/13 14:17:39  rvv
 		*** empty log message ***

 		Revision 1.14  2011/01/23 08:53:55  rvv
 		*** empty log message ***

 		Revision 1.13  2011/01/08 14:27:56  rvv
 		*** empty log message ***

 		Revision 1.12  2011/01/05 18:53:09  rvv
 		*** empty log message ***

 		Revision 1.11  2010/12/08 18:29:07  rvv
 		*** empty log message ***

 		Revision 1.10  2010/11/24 20:23:02  rvv
 		*** empty log message ***

 		Revision 1.9  2010/10/17 09:24:05  rvv
 		Geen gebruik van bedrag maar van debet credit * koers

*/
class RapportSMV_L13
{
  function RapportSMV_L13($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
		if($this->pdf->lastPOST['crmAfdruk']==true)
	  	$this->pdf->lastPOST['GB_STORT_ONTTR']=1;
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
    if(isset($this->pdf->lastPOST['GB_STORT_ONTTR']))
		{
			if(count($this->pdf->portefeuilles)>1)
				$this->pdf->setWidths(array(25, 30, 30, 10, 30, 150));
			else
			  $this->pdf->setWidths(array(25, 30, 30, 10, 150));
		}
    else
      $this->pdf->setWidths(array(25,30,30,10,15,150));
		$this->pdf->setAligns(array('L','R','R','C','L','L')) ;



    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,($this->pdf->rapport_fontsize)-1);

		$DB = new DB();
		if($this->pdf->lastPOST['GB_overige'] == '1')
		{
		  $DB->SQL("SELECT DISTINCT(Grootboekrekening) AS Grootboekrekening  FROM Grootboekrekeningen WHERE Grootboekrekening NOT IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) ORDER BY Grootboekrekening ");
      $DB->Query();
      while($gbData = $DB->nextRecord())
        $grootboeken[] = $gbData['Grootboekrekening'];
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
        $grootboeken[] = $gbData['Grootboekrekening'];
		}


    $DB = new DB();
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
    $sumStartSaldo=0;
    $sumeindSaldo=0;
		while($data = $DB->nextRecord())
		{
		  $startSaldo[$data['rekening']]=$data;
		  $sumStartSaldo+=$data['saldo'];
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
		  $sumeindSaldo+=$data['saldo'];
		}


		$beginJaar = date("Y", $this->pdf->rapport_datumvanaf);
	  $jaar = date("Y", $this->pdf->rapport_datum);
    $januariUitluiten='';
    $jarenString='';
    $boekjarenFilter='';
    $januariFilter='';
  	if ($beginJaar != '1970' && $jaar != $beginJaar)
  	{
  	  for($jaren=$beginJaar;$jaren <= $jaar; $jaren++)
  	  {
  	    if($jarenString<>'')
  	    {
  	      $jarenString .= ",'$jaren'";
          if($januariUitluiten<>'')
            $januariUitluiten .=",'$jaren-01-01 00:00:00'";
          else
            $januariUitluiten .= "'$jaren-01-01 00:00:00'";
	      }
	      else
          $jarenString .= "'$jaren'";
 	    }

 	   	$boekjarenFilter = " ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) AND ";
	    $januariFilter = " Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) AND ";
	  }

	$query = "SELECT Rekeningen.Rekening,Rekeningen.Valuta FROM Rekeningen JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE Portefeuilles.Portefeuille = '".$this->portefeuille."'";
	$DB->SQL($query);
	$DB->Query();
	while($data = $DB->nextRecord())
	{
		$alleRekeningen[]=$data['Rekening'];
	  $rekeningen[$data['Rekening']]=$data;
	}
	//$rekeningen =	 array_merge(array_keys($startSaldo),array_keys($eindSaldo));
	//$rekeningen = array_unique($rekeningen);
	//$rekeningen = array_unique($alleRekeningen);
	//listarray($rekeningen);exit;
	ksort($rekeningen);
    $saldoOpDatum=array();
    $mutatieData=array();
    $som=0;
  if(isset($this->pdf->lastPOST['GB_STORT_ONTTR']))
	{
	 $query = "SELECT Rekeningmutaties.Boekdatum, Rekeningmutaties.Bedrag ,Rekeningmutaties.Omschrijving ,
   ABS(Rekeningmutaties.Aantal) AS Aantal, Rekeningmutaties.Debet as Debet, Rekeningmutaties.Credit as Credit, Rekeningmutaties.Valutakoers,
   Rekeningmutaties.Rekening, Rekeningmutaties.Afschriftnummer, Grootboekrekeningen.Grootboekrekening, Rekeningen.Memoriaal, Rekeningen.Valuta
   FROM
   Rekeningmutaties
   JOIN Grootboekrekeningen ON  Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
   JOIN Rekeningen  on Rekeningmutaties.Rekening = Rekeningen.Rekening
   JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
   WHERE Portefeuilles.Portefeuille  = '".$this->portefeuille."' AND
   Rekeningmutaties.Verwerkt = '1' AND ".
	 $boekjarenFilter.$januariFilter.
	 " Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
	 " Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
   " (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Kruispost = '1' OR Grootboekrekeningen.FondsAanVerkoop = '1')
   ORDER BY Rekeningmutaties.Boekdatum ,Rekeningmutaties.Omschrijving, Rekeningmutaties.id";
	 $DB->SQL($query);
	 $DB->Query();
	 $newSaldo = $sumStartSaldo;


   while($mutaties = $DB->nextRecord())
   {
	   $mutatieData[]  =  $mutaties;
		 if($mutaties['Memoriaal'] != 1)
	   {
		   $newSaldo = $newSaldo + ($mutaties['Credit']*$mutaties['Valutakoers'] - $mutaties['Debet']*$mutaties['Valutakoers']);
		   $saldoOpDatum[$mutaties['Boekdatum']] = $newSaldo;
	   }
   }
   $addPage=false;
   foreach ($mutatieData as $mutaties)
     if($mutaties['Grootboekrekening'] == 'STORT'||$mutaties['Grootboekrekening'] == 'ONTTR' )
       $addPage=true;
		if($addPage)
		{
		  if($this->pdf->portefeuilledata['Layout'] == 13)
        $this->pdf->AddPage('P');
       else
        $this->pdf->AddPage();
		}

		foreach ($mutatieData as $mutaties)
		{
		  $mutatieWaarde = $mutaties['Bedrag'];

		  if($mutatieWaarde > 0)
		    $type = "C";
		  elseif($mutatieWaarde < 0)
	      $type = "D" ;
	    else
	      $type = "" ;

	    $newSaldo =  $saldoOpDatum[$mutaties['Boekdatum']];
      $newSaldoTxt = $this->formatGetal($saldoOpDatum[$mutaties['Boekdatum']],2);
      if($mutaties['Grootboekrekening'] == 'STORT'||$mutaties['Grootboekrekening'] == 'ONTTR' )
	    {
//rvv 14-04-2012 $mutatieWaarde=$mutatieWaarde*$mutaties['Valutakoers'];	      
        if($mutaties['Valuta'] <> 'EUR')
          $mutatieWaarde=$mutatieWaarde*$mutaties['Valutakoers'];

				$som += $mutatieWaarde;
				if(count($this->pdf->portefeuilles)>1)
				{
					$this->pdf->Row( array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
														 $newSaldoTxt,
														 $this->formatGetal($mutatieWaarde,2),
														 $type,$mutaties['Rekening'],
														 $mutaties['Omschrijving']));
				}
	      else
		      $this->pdf->Row( array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
		                           $newSaldoTxt,
		                           $this->formatGetal($mutatieWaarde,2),
		                           $type,
	                             $mutaties['Omschrijving']));
	     }

		     $lastDatum = $mutaties['Boekdatum'];
		}
		   	$this->pdf->ln(2);
    if(round($som,2)<>0.00)
	   	$this->pdf->Row( array('',vertaalTekst('totaal' ,$this->pdf->rapport_taal),$this->formatGetal($som,2),'',''));

	}
	else
	{
    if(count($rekeningen) > 0)
    {
		  if($this->pdf->portefeuilledata['Layout'] == 13)
        $this->pdf->AddPage('P');
       else
        $this->pdf->AddPage();
    }

		foreach ($rekeningen as $rekening=>$rekeningData)
		{

		if($startSaldo[$rekening]['saldo'] > 0)
		  $type = "C";
		elseif($startSaldo[$rekening]['saldo'] < 0)
	    $type = "D" ;
	  else
	   $type = "" ;

	  $this->pdf->Row( array(date("d-m-Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($startSaldo[$rekening]['saldo'],2),$this->formatGetal($startSaldo[$rekening]['saldo'],2),$type,'',"Beginwaarde $rekening"));


		$query = "SELECT  Rekeningmutaties.Valuta ,".
			"Rekeningmutaties.Boekdatum, Rekeningmutaties.Bedrag ,".
			"Rekeningmutaties.Omschrijving ,".
			"ABS(Rekeningmutaties.Aantal) AS Aantal, ".
			"Rekeningmutaties.Debet as Debet, ".
			"Rekeningmutaties.Credit as Credit, ".
			"Rekeningmutaties.Valutakoers, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Rekeningmutaties.Afschriftnummer, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving, ".
			"Grootboekrekeningen.Opbrengst, ".
			"Grootboekrekeningen.Kosten, ".
			"Grootboekrekeningen.Afdrukvolgorde ".
			"FROM Rekeningmutaties,   Grootboekrekeningen ".
			"WHERE Rekeningmutaties.Rekening = '$rekening' AND".
      $boekjarenFilter.$januariFilter.
			" Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".$extraquery.
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Kruispost=1 OR Grootboekrekeningen.FondsAanVerkoop=1) ".
			"ORDER BY Rekeningmutaties.Boekdatum ,Rekeningmutaties.Omschrijving, Rekeningmutaties.id";

		$DB->SQL($query);
		$DB->Query();

		$DB2 = new DB();
		$n=0;
		$mutatieData = array();
		$newSaldo = $startSaldo[$rekening]['saldo'];
		while($mutaties = $DB->nextRecord())
		{
		  $mutatieData[]  =  $mutaties;
		  $newSaldo = $newSaldo + $mutaties['Bedrag'];
		  $saldoOpDatum[$mutaties['Boekdatum']] = $newSaldo;
		}

		//listarray($mutatieData);
		unset($lastDatum);
		$n=0;
		$aantal = count($mutatieData);
      $som=array();
		foreach ($mutatieData as $mutaties)
		{
		  //$mutatieWaarde = $mutaties['Credit'] * $mutaties['Valutakoers'] - $mutaties['Debet'] * $mutaties['Valutakoers'];
		  $mutatieWaarde = $mutaties['Bedrag'];

		  if($mutatieWaarde > 0)
		    $type = "C";
		  elseif($mutatieWaarde < 0)
	      $type = "D" ;
	    else
	      $type = "" ;

	    $newSaldo = $mutaties['afschriftTotaal'];
	    $newSaldo =  $saldoOpDatum[$mutaties['Boekdatum']];
		//  $newSaldo = $newSaldo + $mutatieWaarde;
	    if(in_array($mutaties['Grootboekrekening'],$grootboeken))
	    {

	        $newSaldoTxt = $this->formatGetal($saldoOpDatum[$mutaties['Boekdatum']],2);

	      $som[$rekening] += $mutatieWaarde;

	      if(isset($this->pdf->lastPOST['GB_STORT_ONTTR']))
	        $mutaties['Grootboekrekening'] = '';

		    $this->pdf->Row( array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
		                           $newSaldoTxt,
		                           $this->formatGetal($mutatieWaarde,2),
		                           $type,$mutaties['Grootboekrekening'],
		                           $mutaties['Omschrijving']));

		     $lastDatum = $mutaties['Boekdatum'];
	    }
	    $n++;
		}
      
      
      if(round($newSaldo-$eindSaldo[$rekening]['saldo'],1) != 0.0 && $rekeningData['Valuta'] == 'EUR')
      {
        $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,"B",($this->pdf->rapport_fontsize)-1);
        $this->pdf->MultiCell(200,4, vertaalTekst("Begin en eindwaarden voor portefeuille",$this->pdf->rapport_taal).' '.$rekening." ".
																 vertaalTekst("komen niet overeen met de verwachte waarde." ,$this->pdf->rapport_taal)." (".$this->formatGetal($eindSaldo[$rekening]['saldo'],2)." != ".$this->formatGetal($newSaldo,2).
																 " ".vertaalTekst("verschil",$this->pdf->rapport_taal).": ".$this->formatGetal($newSaldo-$eindSaldo[$rekening]['saldo'],2).")", 0, "L");
        $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
        //  echo "<script> alert('Waarden SMV rapport voor portefeuille ".$this->portefeuille." komen niet overeen (".$this->formatGetal($eindSaldo['saldo'],2)." - ".$this->formatGetal($newSaldo,2).")'); </script>";flush();
        //  exit;
      }

		$this->pdf->ln();
  }
	}


  }


}
?>
