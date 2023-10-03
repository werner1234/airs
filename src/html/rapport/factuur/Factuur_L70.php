<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/28 15:44:38 $
File Versie					: $Revision: 1.14 $

$Log: Factuur_L70.php,v $
Revision 1.14  2020/03/28 15:44:38  rvv
*** empty log message ***

Revision 1.13  2020/01/29 17:38:27  rvv
*** empty log message ***

Revision 1.12  2018/04/14 17:22:16  rvv
*** empty log message ***

Revision 1.11  2018/02/24 18:32:52  rvv
*** empty log message ***

Revision 1.10  2016/12/10 19:23:33  rvv
*** empty log message ***

Revision 1.9  2016/10/19 18:41:21  rvv
*** empty log message ***

Revision 1.8  2016/09/18 08:50:23  rvv
*** empty log message ***

Revision 1.7  2016/09/04 14:43:02  rvv
*** empty log message ***

Revision 1.6  2016/08/13 16:54:32  rvv
*** empty log message ***

Revision 1.5  2016/07/07 15:38:16  rvv
*** empty log message ***

Revision 1.4  2016/07/06 16:09:56  rvv
*** empty log message ***

Revision 1.3  2016/07/02 09:37:56  rvv
*** empty log message ***

Revision 1.2  2016/06/29 16:04:41  rvv
*** empty log message ***

Revision 1.1  2016/06/25 16:57:59  rvv
*** empty log message ***



*/

    $margeMackup=$this->pdf->marge;
    $this->pdf->marge = 30;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight=5;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->SetFont($this->pdf->rapport_font,"",11);
		$this->pdf->rapport_type = "FACTUUR";
    
    $this->pdf->nextFactuur=true;
    $this->pdf->AddPage('L');
    $this->pdf->customPageNo++;
$this->pdf->templateVars['FACTUURPaginas']=$this->pdf->page;
$this->pdf->templateVarsOmschrijving['FACTUURPaginas']='Factuur';


$this->logo='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';
/*
$factor=0.05;
$xSize=1000*$factor;//$x=885*$factor;
$ySize=379*$factor;//$y=849*$factor;
$logopos=(210/2)-($xSize/2);
$this->pdf->memImage(base64_decode($this->logo), $logopos, 10, $xSize, $ySize);
*/
$this->pdf->memImage(base64_decode($this->logo), 220, 18, 55);

		$this->pdf->SetY($this->pdf->getY() +20);
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
    
    $y=$this->pdf->getY();
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

		$this->pdf->SetY($y-4);
	//	$this->pdf->ln();
		if ($this->factuurnummer < 10)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-00'.$this->factuurnummer;
		elseif  ($this->factuurnummer < 100)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-0'.$this->factuurnummer;
		else //toevoeging voor nummers >100
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-'.$this->factuurnummer;

		$this->pdf->SetFont($this->pdf->rapport_font,"I",11);
		$this->pdf->SetWidths(array(140,30,100));
		$this->pdf->SetAligns(array("L","L"));
		if ($this->waarden['SoortOvereenkomst'] == 'Beheer')
		   $this->pdf->row(array('',vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Beheervergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
		elseif ($this->waarden['SoortOvereenkomst'] == 'Advies')
		   $this->pdf->row(array('',vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Adviesvergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
		if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		 $this->pdf->row(array('',vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst($this->waarden['kwartaal'].'e kwartaal',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));
		if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		 $this->pdf->row(array('',vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst('Jaar',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));

		$this->pdf->row(array('',vertaalTekst("Factuur",$this->pdf->rapport_taal).":", $factuurnummer));
		$this->pdf->row(array('',vertaalTekst("Datum",$this->pdf->rapport_taal).":", date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']))));

		$this->pdf->ln();
		$this->pdf->SetY($this->pdf->getY() +15);
		$this->pdf->SetFont($this->pdf->rapport_font,"",11);


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

$extraX=40;
	$this->pdf->SetWidths(array(230));
	$this->pdf->row(array($introTekst));

	$this->pdf->ln();

	//BeheerfeeAantalFacturen


	if (strlen($this->waarden['BeheerfeePercentageVermogenDeelVanJaar']) > 9)
	  $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,8);
	else
	 $beheerfeePercentagePeriode = $this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ;

	$this->pdf->SetWidths(array($extraX,90,25,30));
	$this->pdf->SetAligns(array("L","L","R","R"));

	if ($this->waarden["BeheerfeeBasisberekening"] == 2 )
  {
		$this->pdf->row(array('',vertaalTekst("Totaal vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])), "EUR", $this->formatGetal($this->waarden['totaalWaarde'],2) ));
  }

$newTotaal=$this->waarden['totaalWaarde'];
  if(count($this->waarden['huisfondsKortingFondsen']) > 0)
  {

    foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$waarde)
    {
      $this->pdf->row(array('',"$fonds", "EUR", $this->formatGetal($waarde,2) ));
			$newTotaal-=$waarde;
    }



  }

if(round($this->waarden['rekenvermogen']) <> round($this->waarden['totaalWaarde']))
{
  $liquiditeiten=$newTotaal-$this->waarden['rekenvermogen'];
	if(round($liquiditeiten) <> 0)
		$this->pdf->row(array('',"Uitgesloten liquiditeiten", "EUR", $this->formatGetal($liquiditeiten,2) ));
	$this->pdf->ln(2);
	$this->pdf->Line($this->pdf->marge + $extraX + 110 ,$this->pdf->GetY(),$this->pdf->marge + $extraX + 115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln(2);
	$this->pdf->row(array('', "", "EUR", $this->formatGetal($this->waarden['rekenvermogen'], 2)));
}
	$this->pdf->ln();
  //
  $feePeriode=$this->waarden['beheerfeePerPeriodeNor'];
  
	if ($this->waarden['SoortOvereenkomst'] == 'Beheer')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	}
	if ($this->waarden['SoortOvereenkomst'] == 'Advies')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	}
	$this->pdf->row(array('',vertaalTekst("BTW",$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0) ."%","EUR",$this->formatGetal($feePeriode*$this->waarden['btwTarief']/100,2)));

	$this->pdf->ln(2);
	$this->pdf->Line($this->pdf->marge + $extraX + 110 ,$this->pdf->GetY(),$this->pdf->marge + $extraX + 115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln(2);

	if ($this->waarden['MinJaarbedragGebruikt'])
	{
	$this->pdf->row(array('',vertaalTekst("Berekende fee",$this->pdf->rapport_taal),"EUR",$this->formatGetal($feePeriode*(1+($this->waarden['btwTarief']/100)),2)));
	//$this->waarden['beheerfeePerPeriode'] = $this->waarden['beheerfeePerPeriodeNew'];
	//$this->waarden['btw'] = $this->waarden['btwNew'];
	$this->pdf->SetY($this->pdf->getY() +5);
	$this->pdf->SetWidths(array($extraX,100,15,30));
	if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array('',vertaalTekst("Minimum kwartaal fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt",$this->pdf->rapport_taal),"\nEUR","\n". $this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
	if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array('',vertaalTekst("Minimum jaar fee zoals in vermogensbeheer- overeenkomst beschreven bedraagt",$this->pdf->rapport_taal),"\nEUR","\n". $this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
	$this->pdf->row(array('',vertaalTekst("BTW",$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0) ."%","EUR",$this->formatGetal($this->waarden['btw'],2)));
	$this->pdf->ln(2);
	$this->pdf->Line($this->pdf->marge + $extraX + 110 ,$this->pdf->GetY(),$this->pdf->marge + $extraX + 115 + 30 ,$this->pdf->GetY());
	$this->pdf->ln(2);
	//$this->waarden['beheerfeeBetalenIncl'] = $this->waarden['beheerfeeBetalenInclNew'];
	}

/*
	if($this->waarden['BestandsvergoedingUitkeren'] <> 0) 
	{
	  	$this->pdf->row(array("Te verrekenen fee","EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl']+$this->waarden['bestandsvergoeding'],2)));
	    $this->pdf->ln(2);
	  	$this->pdf->row(array("Retournering ontvangen bestandsvergoedingen t/m het ".$this->waarden['kwartaal'].'e kwartaal',"EUR",$this->formatGetal($this->waarden['bestandsvergoeding']*-1,2)));
	  	$this->pdf->ln(2);
	}
*/
	$this->pdf->row(array('',vertaalTekst("Totaal te verrekenen",$this->pdf->rapport_taal),"EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
/*
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->AutoPageBreak=false;
$this->pdf->marge=$margeMackup;
$this->pdf->SetLeftMargin($this->pdf->marge);
$this->pdf->SetRightMargin($this->pdf->marge);
$this->pdf->SetTopMargin($this->pdf->marge);
$this->pdf->SetWidths(array(10,50,50,70));
$this->pdf->SetAligns(array("L","L","L"));
$this->pdf->setY(270);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Stroeve & Lemberger','T +31 (0)294 - 492 592','Naamloze vennootschap ingeschreven bij de'));
$this->pdf->row(array('','Vermogensbeheerder NV','F +31 (0)294 - 492 593','Kamer van Koophandel onder nummer 20078018'));
$this->pdf->row(array('','Nesland 1-v | 1382 MZ Weesp','E info@stroevelemberger.nl','BTW nummer 80 33 52 116 B01'));
$this->pdf->row(array('','Postbus 234 | 1380 AE Weesp','I www.stroevelemberger.nl','AFM geregistreerd'));
$this->pdf->AutoPageBreak=true;

Nesland 1-V, 1382 MZ Weesp, Postbus 234, 1380 AE Weesp, T +31 (0) 294 492 592, F +31 (0) 294 492 593, info@stroevelemberger.nl, www.stroevelemberger.nl
Naamloze vennootschap ingeschreven bij de kamer van koophandel onder nummer 20078018, BTW nummer 80 33 52 116 B01, AFM geregistreerd
*/

$this->pdf->AutoPageBreak=false;
$this->pdf->marge=$margeMackup;
$this->pdf->SetLeftMargin($this->pdf->marge);
$this->pdf->SetRightMargin($this->pdf->marge);
$this->pdf->SetTopMargin($this->pdf->marge);
$this->pdf->AutoPageBreak=false;
$this->pdf->setY(192);
$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
$this->pdf->SetWidths(array(297-$margeMackup*2));
$this->pdf->SetAligns(array("C"));
$this->pdf->rowHeight=4;

if($this->waarden['Vermogensbeheerder'] == 'SVA')// && ($this->waarden["Accountmanager"] == 'HLA' || $this->waarden["Accountmanager"] == 'HPA'))
{
	$this->pdf->row(array('Posthoornstraat 69, 6219 NV Maastricht, Postbus 2932, 6201 NA Maastricht, T +31 (0) 433 541 222, info@stroevelembergerva2.nl, www.stroevelemberger.nl
Besloten vennootschap ingeschreven bij de kamer van koophandel onder nummer 73762288, BTW nummer 85 96 55 374 B01, AFM geregistreerd'));
}
else
{
	$this->pdf->row(array('Nesland 1-V, 1382 MZ Weesp, Postbus 234, 1380 AE Weesp, T +31 (0) 294 492 592, info@stroevelemberger.nl, www.stroevelemberger.nl
Naamloze vennootschap ingeschreven bij de kamer van koophandel onder nummer 20078018, BTW nummer 80 33 52 116 B01, AFM geregistreerd'));
}
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->AutoPageBreak=true;

?>
