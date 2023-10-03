<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/12/02 09:48:57 $
File Versie					: $Revision: 1.26 $

$Log: Factuur_L5.php,v $
Revision 1.26  2015/12/02 09:48:57  rvv
*** empty log message ***

Revision 1.25  2015/04/19 08:36:36  rvv
*** empty log message ***

Revision 1.24  2015/02/18 17:09:30  rvv
*** empty log message ***

Revision 1.23  2014/02/09 11:01:07  rvv
*** empty log message ***

Revision 1.22  2013/07/09 06:15:58  rvv
*** empty log message ***

Revision 1.21  2013/07/08 17:45:46  rvv
*** empty log message ***

Revision 1.20  2013/01/09 10:39:55  rvv
*** empty log message ***

Revision 1.19  2013/01/02 16:51:16  rvv
*** empty log message ***

Revision 1.18  2012/12/30 14:27:53  rvv
*** empty log message ***

Revision 1.17  2012/12/25 12:46:34  rvv
*** empty log message ***

Revision 1.16  2012/12/25 10:10:15  rvv
*** empty log message ***

Revision 1.15  2012/12/23 10:30:45  rvv
*** empty log message ***

Revision 1.14  2012/11/14 16:49:19  rvv
*** empty log message ***

Revision 1.13  2012/09/30 11:17:18  rvv
*** empty log message ***

Revision 1.12  2011/10/05 09:44:18  rvv
*** empty log message ***

Revision 1.11  2011/07/03 06:44:00  rvv
*** empty log message ***

Revision 1.10  2011/06/15 16:25:45  rvv
*** empty log message ***

Revision 1.9  2010/04/12 17:23:11  rvv
*** empty log message ***

Revision 1.8  2010/03/03 20:06:41  rvv
*** empty log message ***

Revision 1.7  2009/12/23 15:00:59  rvv
*** empty log message ***

Revision 1.6  2009/06/26 13:37:51  rvv
*** empty log message ***

Revision 1.5  2009/05/05 12:38:08  cvs
*** empty log message ***

Revision 1.4  2008/03/18 09:42:38  rvv
*** empty log message ***

Revision 1.3  2008/01/10 16:27:31  rvv
*** empty log message ***

Revision 1.2  2007/10/04 09:14:51  rvv
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***



*/


//listarray($this->waarden);



    $this->pdf->marge = 30;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont("Arial","",11);
		$this->pdf->rapport_type = "FACTUUR";
    
    $this->pdf->nextFactuur=true;
    $this->pdf->AddPage('P');


		$this->pdf->SetY($this->pdf->getY() +30);
		// start eerste block

				$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));

		$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);
	//	$kwartaal = ceil(date("n",db2jul('2006-03-20'))/3);

		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';
    
    $db=new DB();
    $velden=array();    
    $query = "desc CRM_naw";
    $db->SQL($query);
    $db->query();
    while($data=$db->nextRecord('num'))
      $velden[]=$data[0];
    if(in_array('verzendAdres2',$velden))
      $extraVeld=',verzendAdres2';

	  $query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
	  $db->SQL($query);
	  $crmData = $db->lookupRecord();
    

		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array($this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array($this->waarden['clientNaam1']));
		$this->pdf->row(array($this->waarden['clientAdres']));
    if ($crmData['verzendAdres2'] !='')
      $this->pdf->row(array($crmData['verzendAdres2']));
		$plaats=$this->waarden['clientWoonplaats'];
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " . $plaats;
		$this->pdf->row(array($plaats));
		$this->pdf->row(array($this->waarden['clientLand']));

		$this->pdf->SetY(92);
		$this->pdf->ln();
		if ($this->factuurnummer < 10)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-00'.$this->factuurnummer;
		elseif  ($this->factuurnummer < 100)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-0'.$this->factuurnummer;
		else //toevoeging voor nummers >100
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-'.$this->factuurnummer;

		$this->pdf->SetFont("Arial","I",11);  vertaalTekst("Betreft: Rapportage",$this->pdf->rapport_taal);
		$this->pdf->SetWidths(array(30,100));
		$this->pdf->SetAligns(array("L","L"));
		if ($this->waarden['SoortOvereenkomst'] == 'Beheer')
		   $this->pdf->row(array(vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Beheervergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
		elseif ($this->waarden['SoortOvereenkomst'] == 'Advies')
		   $this->pdf->row(array(vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Adviesvergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
		if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		 $this->pdf->row(array(vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst($this->waarden['kwartaal'].'e kwartaal',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));
		if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		 $this->pdf->row(array(vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst('Jaar',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));

		$this->pdf->row(array(vertaalTekst("Factuur",$this->pdf->rapport_taal).":", $factuurnummer));
		$this->pdf->row(array(vertaalTekst("Datum",$this->pdf->rapport_taal).":", date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']))));

		$this->pdf->ln();
		$this->pdf->SetY($this->pdf->getY() +5);
		$this->pdf->SetFont("Arial","",11);


	if ($this->waarden['SoortOvereenkomst'] == 'Beheer')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensbeheer zullen wij opdracht geven uw rekening een dezer dagen te ".
						"belasten voor de beheervergoeding over het",$this->pdf->rapport_taal)." ".
            vertaalTekst($this->waarden['kwartaal']."e kwartaal van",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensbeheer zullen wij opdracht geven uw rekening een dezer dagen te ".
						"belasten voor de beheervergoeding over het jaar",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	}
	elseif ($this->waarden['SoortOvereenkomst'] == 'Advies')
	{
	   if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensadvies zullen wij uw rekening een dezer dagen ".
						"belasten voor de adviesvergoeding over het",$this->pdf->rapport_taal)." ".
             vertaalTekst($this->waarden['kwartaal']."e kwartaal van",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	   if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensadvies zullen wij uw rekening een dezer dagen ".
						"belasten voor de adviesvergoeding over het jaar",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	}
	else
	{
		$introTekst=vertaalTekst("Geen beheerovereenkomst.",$this->pdf->rapport_taal);
	}

	$this->pdf->SetWidths(array(160));
	$this->pdf->row(array($introTekst));

	$this->pdf->ln();

	//BeheerfeeAantalFacturen


	if (strlen($this->waarden['BeheerfeePercentageVermogenDeelVanJaar']) > 9)
	  $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,8);
	else
	 $beheerfeePercentagePeriode = $this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ;


	$this->pdf->SetWidths(array(90,25,30));
	$this->pdf->SetAligns(array("L","R","R"));

	if ($this->waarden["BeheerfeeBasisberekening"] == 2 )
  {
		$this->pdf->row(array(vertaalTekst("Totaal vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])), "€", $this->formatGetal($this->waarden['totaalWaarde'],2) ));
  }
  
  if(count($this->waarden['huisfondsKortingFondsen']) > 0)
  {
    $newTotaal=$this->waarden['totaalWaarde'];
    foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$waarde)
    {
      $this->pdf->row(array("$fonds", "€", $this->formatGetal($waarde,2) ));
      $newTotaal -= $waarde;
    }
    $this->pdf->ln(2);
    $this->pdf->Line($this->pdf->marge + 110 ,$this->pdf->GetY(),$this->pdf->marge +115 + 30 ,$this->pdf->GetY());
    $this->pdf->ln(2);
    $this->pdf->row(array("", "€", $this->formatGetal($newTotaal,2) ));
  }
	$this->pdf->ln();
  //
  $feePeriode=$this->waarden['beheerfeePerPeriodeNor'];
  
	if ($this->waarden['SoortOvereenkomst'] == 'Beheer')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array(vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\n€","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array(vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\n€","\n".$this->formatGetal($feePeriode,2) ));
	}
	if ($this->waarden['SoortOvereenkomst'] == 'Advies')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array(vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\n€","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array(vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\n€","\n".$this->formatGetal($feePeriode,2) ));
	}
	$this->pdf->row(array(vertaalTekst("BTW",$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0) ."%","€",$this->formatGetal($feePeriode*$this->waarden['btwTarief']/100,2)));

	$this->pdf->ln(2);
	$this->pdf->Line($this->pdf->marge + 110 ,$this->pdf->GetY(),$this->pdf->marge +115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln(2);

	if ($this->waarden['MinJaarbedragGebruikt'])
	{
	$this->pdf->row(array(vertaalTekst("Berekende fee",$this->pdf->rapport_taal),"€",$this->formatGetal($feePeriode*(1+($this->waarden['btwTarief']/100)),2)));
	//$this->waarden['beheerfeePerPeriode'] = $this->waarden['beheerfeePerPeriodeNew'];
	//$this->waarden['btw'] = $this->waarden['btwNew'];
	$this->pdf->SetY($this->pdf->getY() +5);
	$this->pdf->SetWidths(array(100,15,30));
	if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array(vertaalTekst("Minimum kwartaal fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt",$this->pdf->rapport_taal),"\n€","\n". $this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
	if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array(vertaalTekst("Minimum jaar fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt",$this->pdf->rapport_taal),"\n€","\n". $this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
	$this->pdf->row(array(vertaalTekst("BTW",$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0) ."%","€",$this->formatGetal($this->waarden['btw'],2)));
	$this->pdf->ln(2);
	$this->pdf->Line($this->pdf->marge + 110 ,$this->pdf->GetY(),$this->pdf->marge +115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln(2);
	//$this->waarden['beheerfeeBetalenIncl'] = $this->waarden['beheerfeeBetalenInclNew'];
	}

/*
	if($this->waarden['BestandsvergoedingUitkeren'] <> 0) 
	{
	  	$this->pdf->row(array("Te verrekenen fee","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl']+$this->waarden['bestandsvergoeding'],2)));
	    $this->pdf->ln(2);
	  	$this->pdf->row(array("Retournering ontvangen bestandsvergoedingen t/m het ".$this->waarden['kwartaal'].'e kwartaal',"€",$this->formatGetal($this->waarden['bestandsvergoeding']*-1,2)));
	  	$this->pdf->ln(2);
	}
*/
	$this->pdf->row(array(vertaalTekst("Totaal te verrekenen",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
$this->pdf->rowHeight=$rowHeightBackup;




?>