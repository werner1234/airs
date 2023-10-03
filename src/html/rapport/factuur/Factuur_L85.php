<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/18 14:52:38 $
File Versie					: $Revision: 1.8 $

$Log: Factuur_L85.php,v $
Revision 1.8  2020/07/18 14:52:38  rvv
*** empty log message ***

Revision 1.7  2020/07/15 16:36:16  rvv
*** empty log message ***

Revision 1.6  2020/04/11 16:34:15  rvv
*** empty log message ***

Revision 1.5  2020/01/22 07:18:28  rvv
*** empty log message ***

Revision 1.4  2019/11/02 15:19:01  rvv
*** empty log message ***

Revision 1.3  2019/10/09 15:14:57  rvv
*** empty log message ***

Revision 1.2  2019/07/24 15:47:07  rvv
*** empty log message ***

Revision 1.1  2019/07/20 16:31:34  rvv
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


$factor=0.04;
$xSize=1750*$factor;
$ySize=525*$factor;

$logopos=10;//(297/2)-($xSize/2);
if(file_exists($this->pdf->rapport_logo))
  $this->pdf->Image($this->pdf->rapport_logo, $logopos, 8, $xSize, $ySize);
$this->pdf->setXY(85,16);
$this->pdf->SetFont($this->pdf->rapport_font,"BI",16);
$this->pdf->Cell(100,4,'A Global Perspective Focused on You', 0,0, "L");
$this->pdf->SetFont($this->pdf->rapport_font,"",11);
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
		
		if ($this->waarden['SoortOvereenkomst'] == 'Vermogensbeheer')
		   $this->pdf->row(array('',vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Beheervergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
		elseif ($this->waarden['SoortOvereenkomst'] == 'Beleggingsadvies')
		   $this->pdf->row(array('',vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Adviesvergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
    elseif ($this->waarden['SoortOvereenkomst'] == 'Beleggingsbemiddeling')
      $this->pdf->row(array('',vertaalTekst("Betreft",$this->pdf->rapport_taal).':', vertaalTekst('Bemiddelingsvergoeding inzake portefeuille',$this->pdf->rapport_taal).' '.$this->portefeuille));
    
		if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		 $this->pdf->row(array('',vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst($this->waarden['kwartaal'].'e kwartaal',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));
		if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		 $this->pdf->row(array('',vertaalTekst("Periode",$this->pdf->rapport_taal).':', vertaalTekst('Jaar',$this->pdf->rapport_taal).' '.$this->waarden['rapportJaar']));

		$this->pdf->row(array('',vertaalTekst("Factuur",$this->pdf->rapport_taal).":", $factuurnummer));
		$this->pdf->row(array('',vertaalTekst("Datum",$this->pdf->rapport_taal).":", date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));

		$this->pdf->ln();
		$this->pdf->SetY($this->pdf->getY() +15);
		$this->pdf->SetFont($this->pdf->rapport_font,"",11);


	if ($this->waarden['SoortOvereenkomst'] == 'Vermogensbeheer')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensbeheer zullen wij opdracht geven uw rekening één dezer dagen te ".
						"belasten voor de beheervergoeding over het",$this->pdf->rapport_taal)." ".
            vertaalTekst($this->waarden['kwartaal']."e kwartaal van",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensbeheer zullen wij opdracht geven uw rekening één dezer dagen te ".
						"belasten voor de beheervergoeding over het jaar",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	}
	elseif ($this->waarden['SoortOvereenkomst'] == 'Beleggingsadvies')
	{
	   if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensadvies zullen wij uw rekening één dezer dagen ".
						"belasten voor de adviesvergoeding over het",$this->pdf->rapport_taal)." ".
             vertaalTekst($this->waarden['kwartaal']."e kwartaal van",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	   if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$introTekst = 	vertaalTekst("Conform de overeenkomst tot vermogensadvies zullen wij uw rekening één dezer dagen ".
						"belasten voor de adviesvergoeding over het jaar",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
	}
  elseif ($this->waarden['SoortOvereenkomst'] == 'Beleggingsbemiddeling')
  {
    if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
      $introTekst = 	vertaalTekst("Conform de overeenkomst voor beleggingsbemiddeling/orderexecutie zullen wij uw rekening één dezer dagen belasten voor de bemiddelingsvergoeding over het",$this->pdf->rapport_taal)." ".
        vertaalTekst($this->waarden['kwartaal']."e kwartaal van",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
    if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
      $introTekst = 	vertaalTekst("Conform de overeenkomst voor beleggingsbemiddeling/orderexecutie zullen wij uw rekening één dezer dagen ".
                                  "belasten voor de bemiddelingsvergoeding over het jaar",$this->pdf->rapport_taal)." ". $this->waarden['rapportJaar'] ."." ;
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
  {
    $parts=explode('.',$this->waarden['BeheerfeePercentageVermogenDeelVanJaar']);
    if(strlen($parts[1])==1)
      $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,2);
    else
      $beheerfeePercentagePeriode = str_replace('.', ',', $this->waarden['BeheerfeePercentageVermogenDeelVanJaar']);
  }


	
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
  
  if($this->waarden['BeheerfeeBedragVast']<>0)
  {
    $this->pdf->row(array('',vertaalTekst("Beheerfee per kwartaal",$this->pdf->rapport_taal)." ","EUR","".$this->formatGetal($feePeriode,2) ));
  
  }
	elseif ($this->waarden['SoortOvereenkomst'] == 'Vermogensbeheer')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het beheerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	}
	elseif ($this->waarden['SoortOvereenkomst'] == 'Beleggingsadvies')
	{
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 4)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per kwartaal, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	  if ($this->waarden['BeheerfeeAantalFacturen'] == 1)
		$this->pdf->row(array('',vertaalTekst("De berekende fee bedraagt",$this->pdf->rapport_taal)." ".$beheerfeePercentagePeriode."% ".vertaalTekst("over het geadviseerde vermogen per jaar, derhalve",$this->pdf->rapport_taal)." ","\nEUR","\n".$this->formatGetal($feePeriode,2) ));
	}
  elseif($this->waarden['SoortOvereenkomst'] == 'Beleggingsbemiddeling')
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


	$this->pdf->row(array('',vertaalTekst("Totaal te verrekenen",$this->pdf->rapport_taal),"EUR",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));

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
$this->pdf->Line($this->pdf->marge,$this->pdf->GetY()-2,297-$this->pdf->marge ,$this->pdf->GetY()-2,array('color'=>array(0,129,129)));

	$this->pdf->row(array('Noesis B.V., Stroombaan 10.C.2.11, 1181 VX, Amstelveen, 088-8000100, info@noesis-capital.nl, www.noesis-capital.nl
Besloten vennootschap ingeschreven bij de kamer van koophandel onder nummer 72561041, BTW nummer NL859153332B01, AFM geregistreerd'));

$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->AutoPageBreak=true;

?>