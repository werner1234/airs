<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/05/04 16:32:49 $
 		File Versie					: $Revision: 1.11 $

 		$Log: Factuur_L8.php,v $
 		Revision 1.11  2011/05/04 16:32:49  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2011/01/16 11:18:19  rvv
 		*** empty log message ***

 		Revision 1.9  2010/04/26 13:33:35  cvs
 		Af: Gemiddeld belegd

 		Revision 1.8  2010/04/24 19:14:21  rvv
 		*** empty log message ***

 		Revision 1.7  2009/11/20 09:38:32  rvv
 		*** empty log message ***

 		Revision 1.6  2009/07/24 10:28:38  rvv
 		*** empty log message ***

 		Revision 1.5  2009/07/22 09:37:05  rvv
 		*** empty log message ***

 		Revision 1.4  2009/06/24 14:38:05  rvv
 		*** empty log message ***

 		Revision 1.3  2009/04/18 15:03:07  rvv
 		*** empty log message ***

 		Revision 1.2  2009/01/24 15:09:12  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/17 11:15:28  rvv
 		*** empty log message ***


*/



 global $__appvar;

		$this->pdf->marge = 8;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);
		$this->pdf->rapport_type = "FACTUUR";

$type=0;

//listarray($this->waarden);
		$this->pdf->AddPage('L');


	if(is_file($this->pdf->rapport_logo))
		{
			//  $this->pdf->Image($this->pdf->rapport_logo, 18, 3.5, 52, 20.6);//43 15
			 $factor=0.12;
			 $x=512*$factor;
			 $y=90*$factor;
			 $this->pdf->Image($this->pdf->rapport_logo, 10, 10, $x, $y);
		}

//
  	$this->pdf->SetLineWidth($this->pdf->lineWidth);
	if(empty($this->pdf->top_marge))
			$this->pdf->top_marge = $this->pdf->marge;
		$this->pdf->SetY($this->pdf->top_marge);
		$this->pdf->SetTextColor($this->pdf->rapport_kop2_fontcolor[r],$this->pdf->rapport_kop2_fontcolor[g],$this->pdf->rapport_kop2_fontcolor[b]);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$y = $this->pdf->GetY();

		$this->pdf->rapport_koptext = str_replace("{Rapportagedatum}",vertaalTekst("\nRapportagedatum:",$this->pdf->rapport_taal)." ".date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y"), $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{PortefeuilleFormat}", $this->pdf->rapport_portefeuilleFormat, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Portefeuille}", $this->pdf->rapport_portefeuille, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $this->pdf->rapport_portefeuilleVoorzet, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Depotbank}", $this->pdf->rapport_depotbank, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{DepotbankOmschrijving}", $this->pdf->rapport_depotbankOmschrijving, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Risicoklasse}", vertaalTekst($this->pdf->rapport_risicoklasse,$this->pdf->rapport_taal), $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Risicoprofiel}", vertaalTekst($this->pdf->rapport_risicoprofiel,$this->pdf->rapport_taal), $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Client}", $this->pdf->rapport_client, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $this->pdf->rapport_clientVermogensbeheerder, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{Accountmanager}", $this->pdf->rapport_accountmanager, $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{ModelPortefeuille}", $this->pdf->portefeuilledata['ModelPortefeuille'], $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $this->pdf->portefeuilledata['VermogensbeheerderNaam'], $this->pdf->rapport_koptext);
		$this->pdf->rapport_koptext = str_replace("{SoortOvereenkomst}", $this->pdf->portefeuilledata['SoortOvereenkomst'], $this->pdf->rapport_koptext);
	  $this->pdf->rapport_naamtext = str_replace("{Naam1}", $this->pdf->rapport_naam1, $this->pdf->rapport_naamtext);
	  $this->pdf->rapport_naamtext = str_replace("{Naam2}", $this->pdf->rapport_naam2, $this->pdf->rapport_naamtext);

		$this->pdf->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $this->pdf->rapport_portefeuilleVoorzet, $this->pdf->rapport_liquiditeiten_omschr);

  	$logopos = 130;
		$x = 190;
		$this->pdf->SetY($y-4);
		$this->pdf->SetX($x);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize + 2);
		$this->pdf->MultiCell(90,8,rtrim($this->pdf->rapport_naamtext),0,'R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX($x);
		$this->pdf->MultiCell(100,4,$this->pdf->rapport_koptext,0,'R');
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->SetY($y);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize + 2);
		$this->pdf->SetX(70);//" in ".vertaalTekst("EUR",$this->pdf->rapport_taal)." ".
    $this->pdf->MultiCell(100,4,"\n".vertaalTekst("Feenota",$this->pdf->rapport_taal)." ".
    vertaalTekst("nummer",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot']))."/".$this->factuurnummer,0,'L');
		$this->pdf->SetX(70);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->MultiCell(100,4,vertaalTekst(vertaalTekst("Verslagperiode: ",$this->pdf->rapport_taal))." "
		.date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).
		' '.vertaalTekst('t/m',$this->pdf->rapport_taal).' '.
		date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])),0,'L');

    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    /*
		$factor = 1.0388;
		$kop=array(86*$factor,43*$factor,18.5*$factor,18.5*$factor,36.5*$factor,68*$factor);
		$marge =8;

		  $this->pdf->SetFillColor(104,109,156);
		  $this->pdf->Rect($marge, 23, $kop[0], 2, 'F');
		  $this->pdf->SetFillColor(144,127,94);
		  $this->pdf->Rect($marge+$kop[0], 23, $kop[1], 2, 'F');
		  $this->pdf->SetFillColor(226,198,160);
		  $this->pdf->Rect($marge+$kop[0]+$kop[1], 23, $kop[2], 2, 'F');
		  $this->pdf->SetFillColor(166,146,139);
		  $this->pdf->Rect($marge+$kop[0]+$kop[1]+$kop[2], 23, $kop[3], 2, 'F');
		  $this->pdf->SetFillColor(131,72,90);
		  $this->pdf->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3], 23, $kop[4], 2, 'F');
		  $this->pdf->SetFillColor(200,72,69);
		  $this->pdf->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3]+$kop[4], 23, $kop[5], 2, 'F');
		  $this->pdf->SetXY(100,$y+18);
*/
      $this->pdf->rowHeight=6;
		  $this->pdf->SetY($this->pdf->getY() +30);


		  $y=$this->pdf->GetY();

    $this->pdf->marge = 13;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);


if($type== 1)
{
		$this->pdf->setWidths(array(5,90,30,5));
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetDrawColor(144,127,94);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),48);
		$this->pdf->ln(6);

		$this->pdf->setAligns(array('L','L','R','R'));
    $this->pdf->Row(array('',vertaalTekst("Aanvangsvermogen per ",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).":",
                   $this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));

    $this->pdf->Row(array('',vertaalTekst("Eindvermogen",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                   $this->formatGetal($this->waarden['totaalWaarde'],2)));

		$this->pdf->Line($this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] ,$this->pdf->GetY(),$this->pdf->marge +$this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] ,$this->pdf->GetY());
		$this->pdf->Row(array('',vertaalTekst("Gemiddeld belegd vermogen:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['basisekenvermogen'],2)));
		$this->pdf->ln(6);
		$this->pdf->Row(array('',vertaalTekst("Beheerfee op jaarbasis volgens contract:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['beheerfeeOpJaarbasis'],2)));

		if($this->waarden['performancefee'])
      $this->pdf->Row(array('',vertaalTekst("Performancefee:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['performancefee'],2)));
    else
      $this->pdf->ln(6);


		// start vierde block
		$this->pdf->ln(12);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),36);
		$this->pdf->ln(6);

		$this->pdf->Row(array('',vertaalTekst("Totaal aan stortingen / onttrekkingen:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['stortingenOntrekkingen'],2)));
    $this->pdf->Row(array('',vertaalTekst("Netto vermogenstoename / afname:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['resultaat'],2)));
    $this->pdf->Row(array('',vertaalTekst("Performance periode",$this->pdf->rapport_taal)." ".date("j-n-Y",db2jul($this->waarden['datumVan']))." t/m ".date("j-n-Y",db2jul($this->waarden['datumTot'])).":",
                          $this->formatGetal($this->waarden['performancePeriode'],2)." %"));
    $this->pdf->Row(array('',vertaalTekst("Performance jaar",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                         $this->formatGetal($this->waarden['performanceJaar'],2)." %"));



	  $this->pdf->setY($y);
		// start derde block

		$this->pdf->setWidths(array(140,5,90,30,5));
		$this->pdf->setAligns(array('L','L','L','R','R'));
		$this->pdf->Rect($this->pdf->marge+$this->pdf->widths[0] ,$this->pdf->getY(),array_sum($this->pdf->widths)-$this->pdf->widths[0],90);
		$this->pdf->ln(6);
		$this->pdf->Row(array('','',vertaalTekst("Beheerfee per periode volgens contract")."\n".vertaalTekst("(inclusief admin vergoeding)").":","\n".$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
		$this->pdf->Row(array('','',vertaalTekst("Totaal betaalde effectenprovisie:")." ".$this->formatGetal($this->waarden['totaalTransactie'],2),''));
    $this->pdf->setWidths(array(140,5,$this->pdf->widths[2]*.8,$this->pdf->widths[2]*.2,30,5));

		if($this->waarden['BeheerfeeRemisiervergoedingsPercentage'])
		  $this->pdf->Row(array('','',vertaalTekst("In mindering op de beheerfee:",$this->pdf->rapport_taal),$this->waarden['BeheerfeeRemisiervergoedingsPercentage']."%", $this->formatGetal($this->waarden['remisierBedrag'],2)));
		else
			$this->pdf->ln(6);

		if($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'])
  		 $this->pdf->Row(array('','',vertaalTekst("Korting i.v.m. beleggingen in huisfondsen:"),$this->formatGetal($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'], 2)."%",$this->formatGetal($this->waarden['huisfondsKorting'], 2)));
		else
			$this->pdf->ln(6);


		$this->pdf->ln(6);

  	$this->pdf->Row(array('','',vertaalTekst("Totaal te betalen beheerfee:",$this->pdf->rapport_taal),'',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->Row(array('','',vertaalTekst("BTW:",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['btw'],2)));
    $this->pdf->Row(array('','', vertaalTekst("Beheerfee inclusief BTW",$this->pdf->rapport_taal),'',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
		$this->pdf->setXY($this->pdf->marge+$this->pdf->widths[0]+5,134);
		$this->pdf->Cell(0,6, vertaalTekst("Verschuldigde beheerfee wordt automatisch van uw rekening afgeschreven.",$this->pdf->rapport_taal), 0,1, "L");
}
elseif($type == 2)
{

		$this->pdf->setWidths(array(5,90,5,25,5));
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetDrawColor(144,127,94);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),54);
		$this->pdf->ln(6);

		$this->pdf->setAligns(array('L','L','C','R','R'));
    $this->pdf->Row(array('',vertaalTekst("Aanvangsvermogen per ",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).":",
                   "€",$this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));

    $this->pdf->Row(array('',vertaalTekst("Eindvermogen",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                   "€",$this->formatGetal($this->waarden['totaalWaarde'],2)));

		$this->pdf->Line($this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] ,$this->pdf->GetY(),$this->pdf->marge +$this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] + $this->pdf->widths[3] ,$this->pdf->GetY());
		$this->pdf->Row(array('',vertaalTekst("Gemiddeld belegd vermogen:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
		$this->pdf->ln(6);
		if($this->waarden['fondsWaardeBuitenFee'] > 0.0)
	  	$this->pdf->Row(array('',vertaalTekst("Af: Gemiddeld belegd vermogen in beleggingspools:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['fondsWaardeBuitenFee'],2)));
		$this->pdf->Row(array('',vertaalTekst("Gemiddeld vermogen voor berekening beheerfee:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['rekenvermogen'],2)));
		$this->pdf->Row(array('',vertaalTekst("Beheerfee op jaarbasis volgens contract:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['beheerfeeOpJaarbasis'],2)));

		if($this->waarden['performancefee'])
      $this->pdf->Row(array('',vertaalTekst("Performancefee:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['performancefee'],2)));
    else
      $this->pdf->ln(6);


	  $this->pdf->setY(116);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),36);
		$this->pdf->ln(6);

		$this->pdf->Row(array('',vertaalTekst("Totaal aan stortingen / onttrekkingen:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['stortingenOntrekkingen'],2)));
    $this->pdf->Row(array('',vertaalTekst("Netto vermogenstoename / afname:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['resultaat'],2)));
    $this->pdf->Row(array('',vertaalTekst("Performance periode",$this->pdf->rapport_taal)." ".date("j-n-Y",db2jul($this->waarden['datumVan']))." t/m ".date("j-n-Y",db2jul($this->waarden['datumTot'])).":",
                          '',$this->formatGetal($this->waarden['performancePeriode'],2)." %"));
    $this->pdf->Row(array('',vertaalTekst("Performance jaar",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                          '',$this->formatGetal($this->waarden['performanceJaar'],2)." %"));



	  $this->pdf->setY($y);
		// start derde block

		$this->pdf->setWidths(array(140,5,90,5,25,5));
		$this->pdf->setAligns(array('L','L','L','R','R','R'));
		$this->pdf->Rect($this->pdf->marge+$this->pdf->widths[0] ,$this->pdf->getY(),array_sum($this->pdf->widths)-$this->pdf->widths[0],96);
		$this->pdf->ln(6);
		$this->pdf->Row(array('','',vertaalTekst("Beheerfee per periode volgens contract",$this->pdf->rapport_taal)."\n".vertaalTekst("(inclusief administratie vergoeding):",$this->pdf->rapport_taal),"\n€","\n".$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
		$this->pdf->Row(array('','',vertaalTekst("Totaal betaalde effectenprovisie:",$this->pdf->rapport_taal)." € ".$this->formatGetal($this->waarden['totaalTransactie'],2),'',''));
    $this->pdf->setWidths(array(140,5,$this->pdf->widths[2]*.8,$this->pdf->widths[2]*.2,5,25,5));

		if($this->waarden['BeheerfeeRemisiervergoedingsPercentage'])
		  $this->pdf->Row(array('','',vertaalTekst("In mindering op de beheerfee:",$this->pdf->rapport_taal),$this->waarden['BeheerfeeRemisiervergoedingsPercentage']." %","€", $this->formatGetal($this->waarden['remisierBedrag'],2)));
		else
			$this->pdf->ln(6);

		if($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'])
  		 $this->pdf->Row(array('','',vertaalTekst("Korting i.v.m. beleggingen in huisfondsen:"),$this->formatGetal($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'], 2)." %","€",$this->formatGetal($this->waarden['huisfondsKorting'], 2)));
		else
			$this->pdf->ln(6);


		$this->pdf->ln(6);

  	$this->pdf->Row(array('','',vertaalTekst("Totaal te betalen beheerfee:",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->Row(array('','',vertaalTekst("BTW:",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['btw'],2)));
    $this->pdf->Row(array('','', vertaalTekst("Beheerfee inclusief BTW",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
		$this->pdf->setXY($this->pdf->marge+$this->pdf->widths[0]+5,140);
		$this->pdf->Cell(0,6, vertaalTekst("Verschuldigde beheerfee wordt automatisch van uw rekening afgeschreven.",$this->pdf->rapport_taal), 0,1, "L");
}
else
{

		$this->pdf->setWidths(array(5,90,5,25,5));
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetDrawColor(144,127,94);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),54+6);
		$this->pdf->ln(6);

		$this->pdf->setAligns(array('L','L','C','R','R'));
    $this->pdf->Row(array('',vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])).":",
                   "€",$this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));

    $this->pdf->Row(array('',vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                   "€",$this->formatGetal($this->waarden['totaalWaarde'],2)));

    $this->pdf->Row(array('',vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['totaalWaarde']-$this->waarden['totaalWaardeVanaf'],2)));
    $this->pdf->Row(array('',vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['stortingen'],2)));
    $this->pdf->Row(array('',vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['stortingenOntrekkingen']-$this->waarden['stortingen'],2)));
	$this->pdf->Line($this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] ,$this->pdf->GetY(),$this->pdf->marge +$this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] + $this->pdf->widths[3] ,$this->pdf->GetY());

    $this->pdf->Row(array('',vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['totaalWaarde']-$this->waarden['totaalWaardeVanaf']-$this->waarden['stortingenOntrekkingen'],2)));

	$this->pdf->Line($this->pdf->marge + $this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] ,$this->pdf->GetY(),$this->pdf->marge +$this->pdf->widths[0] + $this->pdf->widths[1] + $this->pdf->widths[2] + $this->pdf->widths[3] ,$this->pdf->GetY());

    $this->pdf->Row(array('',vertaalTekst("Performance periode",$this->pdf->rapport_taal)." ".date("j-n-Y",db2jul($this->waarden['datumVan']))." t/m ".date("j-n-Y",db2jul($this->waarden['datumTot'])).":",
                          '',$this->formatGetal($this->waarden['performancePeriode'],2)." %"));
    $this->pdf->Row(array('',vertaalTekst("Performance jaar",$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])).":",
                          '',$this->formatGetal($this->waarden['performanceJaar'],2)." %"));

    $this->pdf->setY(116+6);
    $this->pdf->ln(6);
   $this->pdf->Row(array('',vertaalTekst("Gemiddeld belegd vermogen:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['basisRekenvermogen'],2)));
		if($this->waarden['fondsWaardeBuitenFee'] > 0.0)
	  	$this->pdf->Row(array('',vertaalTekst("Af: Gemiddeld belegd vermogen in beleggingspools:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['fondsWaardeBuitenFee'],2)));

		$this->pdf->Row(array('',vertaalTekst("Gemiddeld vermogen voor berekening beheerfee:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['rekenvermogen'],2)));
		$this->pdf->Row(array('',vertaalTekst("Beheerfee op jaarbasis volgens contract:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['beheerfeeOpJaarbasis'],2)));

		if($this->waarden['performancefee'])
      $this->pdf->Row(array('',vertaalTekst("Performancefee:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['performancefee'],2)));
    else
      $this->pdf->ln(6);


	  $this->pdf->setY(116+6);
		$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),array_sum($this->pdf->widths),36);
		$this->pdf->ln(6);

	//	$this->pdf->Row(array('',vertaalTekst("Totaal aan stortingen / onttrekkingen:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['stortingenOntrekkingen'],2)));
  //  $this->pdf->Row(array('',vertaalTekst("Netto vermogenstoename / afname:",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['resultaat'],2)));




	  $this->pdf->setY($y);
		// start derde block

		$this->pdf->setWidths(array(140,5,90,5,25,5));
		$this->pdf->setAligns(array('L','L','L','R','R','R'));
		$this->pdf->Rect($this->pdf->marge+$this->pdf->widths[0] ,$this->pdf->getY(),array_sum($this->pdf->widths)-$this->pdf->widths[0],96+6);
		$this->pdf->ln(6);
		$this->pdf->Row(array('','',vertaalTekst("Beheerfee per periode volgens contract",$this->pdf->rapport_taal)."\n".vertaalTekst("(inclusief administratie vergoeding):",$this->pdf->rapport_taal),"\n€","\n".$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
		$this->pdf->Row(array('','',vertaalTekst("Totaal betaalde effectenprovisie:",$this->pdf->rapport_taal)." € ".$this->formatGetal($this->waarden['totaalTransactie'],2),'',''));
    $this->pdf->setWidths(array(140,5,$this->pdf->widths[2]*.8,$this->pdf->widths[2]*.2,5,25,5));

		if($this->waarden['BeheerfeeRemisiervergoedingsPercentage'])
		  $this->pdf->Row(array('','',vertaalTekst("In mindering op de beheerfee:",$this->pdf->rapport_taal),$this->waarden['BeheerfeeRemisiervergoedingsPercentage']." %","€", $this->formatGetal($this->waarden['remisierBedrag'],2)));
		else
			$this->pdf->ln(6);

		if($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'])
  		 $this->pdf->Row(array('','',vertaalTekst("Korting i.v.m. beleggingen in huisfondsen:"),$this->formatGetal($this->waarden['BeheerfeeTeruggaveHuisfondsenPercentage'], 2)." %","€",$this->formatGetal($this->waarden['huisfondsKorting'], 2)));
		else
			$this->pdf->ln(6);


		$this->pdf->ln(6);

  	$this->pdf->Row(array('','',vertaalTekst("Totaal te betalen beheerfee:",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->Row(array('','',vertaalTekst("BTW:",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['btw'],2)));
    $this->pdf->Row(array('','', vertaalTekst("Beheerfee inclusief BTW",$this->pdf->rapport_taal),'',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
		$this->pdf->setXY($this->pdf->marge+$this->pdf->widths[0]+5,140+6);
		$this->pdf->Cell(0,6, vertaalTekst("Verschuldigde beheerfee wordt automatisch van uw rekening afgeschreven.",$this->pdf->rapport_taal), 0,1, "L");
}


?>