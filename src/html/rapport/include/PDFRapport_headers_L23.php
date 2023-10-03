<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.3 $

 		$Log: PDFRapport_headers_L23.php,v $
 		Revision 1.3  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/03/10 19:53:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/01/09 11:41:01  rvv
 		*** empty log message ***



*/
function Header_basis_L23($object)
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

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
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
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		if(is_file($pdfObject->rapport_logo))
		{
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 43, 15);
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
	  $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetX(100);

	  $pdfObject->SetXY(100,$y);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel2,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

    $pdfObject->SetY($y);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y+12);
    }
}


	function HeaderVKM_L23($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	  function HeaderVOLK_L23($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		foreach ($pdfObject->widthB as $id=>$value)
		{
		  if($id < 3)
		    $actueel +=$value;
		  if($id < 7)
		    $eindactueel +=$value;
		  if($id < 8)
		    $resultaat  +=$value;
		  if($id < 11)
		    $eindresultaat  +=$value;
		  if($id < 12)
		    $risico  +=$value;
		  if($id < 13)
		    $eindrisico  +=$value;
		  $eind +=$value;
		}

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell($eindactueel-$actueel,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell($eindresultaat-$resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$risico);
		$pdfObject->Cell($eindrisico-$risico,4, vertaalTekst("Risicoscore",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$risico),$pdfObject->GetY(),$pdfObject->marge + $eindrisico,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();
 		$pdfObject->row(array("",vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal)),
										"",
										vertaalTekst("Risicoscore",$pdfObject->rapport_taal)
										);


		$pdfObject->setY($y);
		$pdfObject->ln(8);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eind,$pdfObject->GetY());
		$pdfObject->ln();
	  }

	function HeaderTRANS_L23($object)
  {
    $pdfObject = &$object;


    $pdfObject->ln(8);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns(array('L','R','L','R','L','L','R','R','C','R'));

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		foreach ($pdfObject->widthA as $id=>$value)
		{
		  if($id < 7)
		    $resultaat  +=$value;
		  if($id < 10)
		    $eindresultaat  +=$value;
		  $eind +=$value;
		}
		$pdfObject->Line($pdfObject->marge+$resultaat,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->SetFont($pdfObject->rapport_font,"B",$pdfObject->rapport_fontsize);
    $pdfObject->row(array("","Aankopen","","Verkopen","","","","","€",""));
    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eind,$pdfObject->GetY());
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

    $pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Soort\nTransactie",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in\nvaluta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop\nOpbrengst",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop bedrag",$pdfObject->rapport_taal),
										 vertaalTekst("Gerealiseerd resultaat",$pdfObject->rapport_taal)));
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eind,$pdfObject->GetY());
   $pdfObject->ln(2);

  }


?>