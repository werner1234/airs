<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/03/25 16:01:09 $
 		File Versie					: $Revision: 1.7 $

 		$Log: PDFRapport_headers_L46.php,v $
 		Revision 1.7  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/01/14 08:21:34  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/01/13 17:11:59  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/10/07 19:38:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/04/17 16:00:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/09/11 15:17:37  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/03/10 19:53:17  rvv
 		*** empty log message ***

 		Revision 1.1  2010/01/09 11:41:01  rvv
 		*** empty log message ***



*/
function Header_basis_L46($object)
{
 $pdfObject = &$object;


       if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast  && $pdfObject->rapport_layout != 16)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old;
      
    $rapportageDatum= date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
    $huidigeDatum= date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y");


		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
    	$pdfObject->rapport_koptext = str_replace("{rapportageDatum}", $rapportageDatum, $pdfObject->rapport_koptext);
      $pdfObject->rapport_koptext = str_replace("{huidigeDatum}", $huidigeDatum, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
		}
		else
		{
		  $pdfObject->rapport_koptext = str_replace("{rapportageDatum}", $rapportageDatum, $pdfObject->rapport_koptext);
      $pdfObject->rapport_koptext = str_replace("{huidigeDatum}", $huidigeDatum, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$logopos = 90;
      
		}
		else
		{
			$logopos = 140;
      $logopos=(297/2)-(500*0.2/2);
		}

		if(is_file($pdfObject->rapport_logo))
		{
      $factor=0.2;
		  $w=500*$factor;
      $h=101*$factor;
		  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $w, $h);
		}
		else if(!empty($pdfObject->rapport_logo_tekst))
		{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor[r],$pdfObject->rapport_logo_fontcolor[g],$pdfObject->rapport_logo_fontcolor[b]);
			$pdfObject->SetFont($pdfObject->rapport_logo_font,$pdfObject->rapport_logo_fontstyle,$pdfObject->rapport_logo_fontsize);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst,0, "C");

			if ($pdfObject->rapport_logo_tekst2)
			{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor2[r],$pdfObject->rapport_logo_fontcolor2[g],$pdfObject->rapport_logo_fontcolor2[b]);
			$pdfObject->SetFont($pdfObject->rapport_logo_font2,$pdfObject->rapport_logo_fontstyle2,$pdfObject->rapport_logo_fontsize2);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst2,0, "C");
			}

			$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
			$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		}



		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}



		$pdfObject->SetY($y);
		$pdfObject->SetX($x);
	  $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');//"\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)
	  $pdfObject->SetX(100);


    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetY($y);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');

			$pdfObject->SetXY($pdfObject->marge,30);
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->SetTextColor(0,0,0);
			$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
			
		$pdfObject->SetY(35);
    $pdfObject->headerStart = $pdfObject->getY()+14;
    
    }
}

	function HeaderVKM_L46($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	function HeaderTRANS_L46($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();
		$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		// achtergrond kleur
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
			
			// afdrukken header groups
			$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
			$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];
			
			$verkoop			= $inkoopEind;
			$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
			

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
		
	// Formaat van de kopcellen dynamisch gemaakt aan de hand van de kolombreedte.
//			echo "$inkoopEind - $inkoop en $verkoopEind - $verkoop en $resultaatEind - $resultaat ";
			$pdfObject->SetX($inkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
//			$pdfObject->Cell(65,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C"); //81 ipv 65
			$pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();

			$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
			$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
			$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());

		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";
		
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		
			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
									 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
									 vertaalTekst("Aantal",$pdfObject->rapport_taal),
									 vertaalTekst("Fonds",$pdfObject->rapport_taal),
									 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									 vertaalTekst("Resultaat voorafgaand verslagperiode",$pdfObject->rapport_taal),
									 vertaalTekst("Resultaat gedurende verslagperiode",$pdfObject->rapport_taal),
									 $procentTotaal));
			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }
  
	function HeaderPERF_L46($object)
	{
    $pdfObject = &$object;
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->ln(2);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		$pdfObject->ln(2);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array("",
								 "",
								 "",
								 "",
								 "",
								 ""));
		
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		
//		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }
  
	function HeaderOIB_L46($object)
	{
    $pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar
			$pdfObject->SetX($pdfObject->marge+$lijn1+5);
			$pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");

			$pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);


				$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
										 vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
										 vertaalTekst("in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("in %",$pdfObject->rapport_taal)));



		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }       
  
	function HeaderOIV_L46($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderOIV();
  }  

	function HeaderMUT_L46($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderMUT();
  }   
 
 	function HeaderVOLK_L46($object)
	{
    $pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
			
		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
			
		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13];

		// achtergrond kleur
		$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar
			$pdfObject->SetX($pdfObject->marge+$huidige+5);
				//$pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
				
				if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
					$pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
				else
					$pdfObject->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$actueel);
			$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		
		
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$y = $pdfObject->getY();
			$pdfObject->row(array("",
									 "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
									 vertaalTekst("Aantal",$pdfObject->rapport_taal),
									 vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									 "",
									 vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									 vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
									 vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
									 "",
									 vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
									 vertaalTekst("in %",$pdfObject->rapport_taal)));

		
		
		$pdfObject->setY($y);
			$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->ln();
		
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  }
   
	function HeaderVHO_L46($object)
	{
    $pdfObject = &$object;
    $widths=array(13,47,     22,    10,      13,     20,           13,     20,         15,         10,   20,           20,         15,          10, 15,      10,   10);
    $pdfObject->SetWidths($widths);
    
    $i=0;
    $tmpWidth=array();
    foreach($widths as $index=>$waarde)
    {
      if($index<5)
        $tmpWidth[$i]+=$waarde;
      elseif($index<10)
      {
        $i=1;
        $tmpWidth[$i]+=$waarde;
      }
      elseif($index<16)
      {
        $i=2;
        $tmpWidth[$i]+=$waarde;
      }
      else
      {
        $i=3;
        $tmpWidth[$i]+=$waarde;    
      }
    }

    //$pdfObject->SetTextColor(0,0,0);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $y=$pdfObject->GetY();
    $pdfObject->Ln();
    $pdfObject->SetAligns(array('R','L','L','L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $fillbackup=$pdfObject->fillCell;
    $alignBackup=$pdfObject->aligns;
    $pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->row(array("Aantal","Naam",'ISIN',"Valuta","Koers","Marktwaarde","GemKp","Kostprijs","Resultaat","(%)","Marktwaarde","Kostprijs","Resultaat","(%)","Rente","%Mw",'Ltijd'));

    $pdfObject->SetY($y);
    $pdfObject->SetWidths($tmpWidth);
    $pdfObject->SetAligns(array('C','C','C','C'));
    $pdfObject->CellBorders = array('',array('U','LU'),array('U','LU','RU'));
    $pdfObject->row(array('',"In valuta van belegging","In portefeuille valuta",''));
    unset($pdfObject->CellBorders);
    $pdfObject->SetWidths($widths);
    $pdfObject->Ln();



    $pdfObject->fillCell=$fillbackup;
    $pdfObject->aligns=$alignBackup;
	}

function HeaderATT_L46($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,25);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
 		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
		$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
		                      vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("maand",$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

function printPie_L46($object,$pieData,$kleurdata)
{

	$pdfObject = &$object;

	// default colors
	// custom maken zet de kleuren in config/rapportage.php , en laad deze hier als ze bestaand, anders deze als default .
	if (is_array($pdfObject->customPieColors))
	{
		$col1=$pdfObject->customPieColors["col1"];
		$col2=$pdfObject->customPieColors["col2"];
		$col3=$pdfObject->customPieColors["col3"];
		$col4=$pdfObject->customPieColors["col4"];
		$col5=$pdfObject->customPieColors["col5"];
		$col6=$pdfObject->customPieColors["col6"];
		$col7=$pdfObject->customPieColors["col7"];
		$col8=$pdfObject->customPieColors["col8"];
		$col9=$pdfObject->customPieColors["col9"];
		$col0=$pdfObject->customPieColors["col0"];
		$standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
	}
	else
	{
		$col1=array(255,0,0); // rood
		$col2=array(0,255,0); // groen
		$col3=array(255,128,0); // oranje
		$col4=array(0,0,255); // blauw
		$col5=array(255,255,0); // geel
		$col6=array(255,0,255); // paars
		$col7=array(128,128,128); // grijs
		$col8=array(128,64,64); // bruin
		$col9=array(255,255,255); // wit
		$col0=array(0,0,0); //zwart
		$standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
	}

// standaardkleuren vervangen voor eigen kleuren.

	if($kleurdata)
	{
		if(!$pdfObject->rapport_dontsortpie)
		{
			$sorted 		= array();
			$percentages 	= array();
			$kleur			= array();
			$valuta 		= array();

			while (list($key, $data) = each($kleurdata))
			{
				$percentages[] 	= $data[percentage];
				$kleur[] 			= $data[kleur];
				$valuta[] 		= $key;
			}
			arsort($percentages);

			while (list($key, $percentage) = each($percentages))
			{
				$sorted[$valuta[$key]]['kleur']=$kleur[$key];
				$sorted[$valuta[$key]]['percentage']=$percentage;
			}
			$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
		}

		$pieData=array();
		$grafiekKleuren = array();

		$a=0;
		while (list($key, $value) = each($kleurdata))
		{
			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
			{
				$grafiekKleuren[]=$standaardKleuren[$a];
			}
			else
			{
				$grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			}
			$pieData[$key] = $value[percentage];
			$a++;
		}
	}
	else
		$grafiekKleuren = $standaardKleuren;

	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

	$pdfObject->rapport_printpie = true;

	while (list($key, $value) = each($pieData))
	{
		if ($value < 0)
		{
			if($pdfObject->rapport_layout == 8 || $pdfObject->rapport_layout == 10 )
				$pieData[$key] = -1 * $value;
			else
				$pdfObject->rapport_printpie = false;
		}
	}

	if($pdfObject->rapport_printpie)
	{
		//		if(!$pdfObject->rapport_dontsortpie)
		//		{
		//			asort($pieData, SORT_NUMERIC);
		//			$pieData = array_reverse($pieData,true);
		//		}
		$pdfObject->SetXY(210, $pdfObject->headerStart);
		$y = $pdfObject->getY();
		$pdfObject->SetFont($pdfObject->pdf->rapport_font,'b',10);
		$pdfObject->Cell(50,4,vertaalTekst($pdfObject->rapport_titel, $pdfObject->rapport_taal),0,1,"C");
		$pdfObject->SetFont($pdfObject->pdf->rapport_font,'',$pdfObject->pdf->rapport_fontsize);
		$pdfObject->SetX(210);
		$pdfObject->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
//		$hoogte = ($pdfObject->getY() - $y) + 8;
		$pdfObject->setY($y);

		$pdfObject->SetLineWidth($pdfObject->lineWidth);
/*
		if($pdfObject->rapport_type == "OIB")
		{
			$pdfObject->Rect(175,$pdfObject->getY(),113,$hoogte);
		}
		else
		{
			$pdfObject->Rect(190,$pdfObject->getY(),90,$hoogte);
		}
*/
	}
}


?>