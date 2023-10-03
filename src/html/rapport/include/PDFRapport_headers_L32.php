<?php

function Header_basis_L32($object)
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
	  	$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
	  	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		
		  if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		  $pdfObject->customPageNo = 0;
      $pdfObject->rapportNewPage = $pdfObject->page;
      voetBalk_L32($object);
    }
    else 
    {
  
    voetBalk_L32($object);
      
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
      
  	if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
  	  	$pdfObject->rapportNewPage = $pdfObject->page;
     
		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_default_fontcolor['r'],$pdfObject->rapport_default_fontcolor['g'],$pdfObject->rapport_default_fontcolor['b']);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;  
		}
    if($pdfObject->__appvar['consolidatie'])
    {
      $pdfObject->rapport_koptext = $pdfObject->portefeuilledata['ClientVermogensbeheerder'];
    }
    
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		
		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
	  	$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
	  	$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		}
		else 
		{		
		  $pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		  $pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		}
		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);


		if(is_file($pdfObject->rapport_logo))
		{
 		    $factor=0.025;
		    $xSize=1658*$factor;
		    $ySize=561*$factor;
       // $logopos=(297/2)-($xSize/2);
	      $pdfObject->Image($pdfObject->rapport_logo, 0, 0, $xSize, $ySize);
		}
 		else if(!empty($pdfObject->rapport_logo_tekst))
		{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor['r'],$pdfObject->rapport_logo_fontcolor['g'],$pdfObject->rapport_logo_fontcolor['b']);
			$pdfObject->SetFont($pdfObject->rapport_logo_font,$pdfObject->rapport_logo_fontstyle,$pdfObject->rapport_logo_fontsize);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst,0, "C");
			if ($pdfObject->rapport_logo_tekst2)
			{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor2['r'],$pdfObject->rapport_logo_fontcolor2['g'],$pdfObject->rapport_logo_fontcolor2['b']);
			$pdfObject->SetFont($pdfObject->rapport_logo_font2,$pdfObject->rapport_logo_fontstyle2,$pdfObject->rapport_logo_fontsize2);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst2,0, "C");
			}
			$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
			$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		}
    
	//	$pdfObject->MultiCell(120,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD")
		{
			$x = 210-158;
		}
		else
		{
			$x = 297-158;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);

		$pdfObject->MultiCell(150,4,$pdfObject->rapport_koptext."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".
														vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".
														date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(12);
    $pdfObject->SetX(0);
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
   

			$pdfObject->AutoPageBreak=false;
			$pdfObject->SetY(-9);
			$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
			$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);

			$pdfObject->Cell(255,4,vertaalTekst('Aan deze opgave kunnen geen rechten worden ontleend.',$pdfObject->rapport_taal).' '.$pdfObject->rapport_voettext_rechts,'0','L');
			$pdfObject->SetX(0);
			if($pdfObject->rapport_type == "VHO" || $pdfObject->rapport_type == "VOLK" || $pdfObject->rapport_type == "HSE")
				$pdfObject->Cell(297,4,vertaalTekst("Gebruikte koersen in deze rapportage gemarkeerd met een * zijn ouder dan",$pdfObject->rapport_taal).' '.	$pdfObject->portefeuilledata['VerouderdeKoersDagen'].' '.vertaalTekst("dagen ten opzichte van de rapportagedatum.",$pdfObject->rapport_taal),false,false,'C');

			$pdfObject->AutoPageBreak=true;

		$pdfObject->SetY($y+20);
    $pdfObject->headerStart=$pdfObject->GetY()+15;
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
 }
}

function voetBalk_L32($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_voet_bgcolor[0], $pdfObject->rapport_voet_bgcolor[1], $pdfObject->rapport_voet_bgcolor[2]);
  $hoogte=12;
  $pdfObject->Rect(0 ,$pdfObject->h-$hoogte, $pdfObject->w, $pdfObject->h, 'F');
  
  $pdfObject->SetFillColor(113,28,59);
  $pdfObject->Rect(0 ,$pdfObject->h-$hoogte*.25, $pdfObject->w*0.55, $pdfObject->h, 'F');
  $pdfObject->SetFillColor(238,238,18);
  $pdfObject->Rect($pdfObject->w*0.55 ,$pdfObject->h-$hoogte*.25, $pdfObject->w*0.72, $pdfObject->h, 'F');
  $pdfObject->SetFillColor(26,53,91);
  $pdfObject->Rect($pdfObject->w*0.72 ,$pdfObject->h-$hoogte*.25, $pdfObject->w, $pdfObject->h, 'F');
}
function HeaderFRONT_L32($object)
{

}

	function HeaderVKM_L32($object)
	{
		$pdfObject = &$object;
		$pdfObject->ln();
		$widthBackup=$pdfObject->widths;
		$dataWidth=array(28,50,20,20,20,20,20,20,20,20,20,20);
		$pdfObject->SetWidths($dataWidth);
		$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R'));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		$lastColors=$pdfObject->CellFontColor;
		unset($pdfObject->CellFontColor);
		unset($pdfObject->CellBorders);
		if(!isset($pdfObject->vmkHeaderOnderdrukken))
		{
			$pdfObject->Row(array(vertaalTekst("Risico\ncategorie", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
									 "\n" . date('d-m-Y', $pdfObject->rapport_datumvanaf),
									 "\n" . date('d-m-Y', $pdfObject->rapport_datum),
									 "\n" . vertaalTekst("Mutaties", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
									 vertaalTekst("Gemiddeld vermogen", $pdfObject->rapport_taal),
									 vertaalTekst("transactie\nkosten", $pdfObject->rapport_taal),
									 vertaalTekst("dl kosten\n%", $pdfObject->rapport_taal),
									 vertaalTekst("dl kosten\nabsoluut", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Weging", $pdfObject->rapport_taal),
									 "" . vertaalTekst("VKM\nBijdrage", $pdfObject->rapport_taal)));
			unset($pdfObject->vmkHeaderOnderdrukken);
			$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
		}
		else
		{
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'], $pdfObject->rapport_kop_bgcolor['g'], $pdfObject->rapport_kop_bgcolor['b']);
			$pdfObject->Rect($pdfObject->marge, 32, 297 - $pdfObject->marge * 2, 8, 'F');
			$pdfObject->ln(2);
		}
		$pdfObject->widths=$widthBackup;
		$pdfObject->CellFontColor=$lastColors;
		$pdfObject->SetLineWidth(0.1);
	}

function HeaderINHOUD_L32()
{
	
}

function HeaderKERNV_L32($object)
{

}
function HeaderKERNZ_L32($object)
{

}

function HeaderOIB_L32($object)
{
	$pdfObject = &$object;

	if (is_array($pdfObject->portefeuilles))
	{
	$pdfObject = &$object;
	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'], $pdfObject->rapport_kop_bgcolor['g'], $pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297 - $pdfObject->marge * 2, 8, 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'], $pdfObject->rapport_kop_fontcolor['g'], $pdfObject->rapport_kop_fontcolor['b']);

	$pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
	$lijn1 = $pdfObject->widthB[0] + $pdfObject->widthB[1];
	$lijn1eind = $lijn1 + $pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

	// lijntjes onder beginwaarde in het lopende jaar
	$lijn1 = 65;
	$lijn1eind = 125;

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);


	if (is_array($pdfObject->portefeuilles))
	{
		if (count($pdfObject->portefeuilles) < 6)
		{
			$pdfObject->SetX($pdfObject->marge);
			$pdfObject->Cell(65, 4, vertaalTekst("Beleggingscategorie", $pdfObject->rapport_taal), 0, 0, "L");
			$pdfObject->Cell(35, 4, 'Totaal', 0, 0, "C");
			foreach ($pdfObject->portefeuilles as $portefeuille)
			{
				$pdfObject->Cell(35, 4, "Waarden", 0, 0, "C");
			}
			$pdfObject->Ln();
			$pdfObject->SetX($pdfObject->marge + 65 + 35);
			foreach ($pdfObject->portefeuilles as $portefeuille)
			{
				if ($pdfObject->clientVermogensbeheerder[$portefeuille])
				{
					$naam = $pdfObject->clientVermogensbeheerder[$portefeuille];
				}
				else
				{
					$naam = $portefeuille;
				}
				$pdfObject->Cell(35, 4, $naam, 0, 0, "C");
			}

			$pdfObject->Ln();
		}
	}
	else
	{
		$pdfObject->SetX($pdfObject->marge + $lijn1);
		$pdfObject->MultiCell(35, 4, vertaalTekst("Waarden", $pdfObject->rapport_taal), 0, "C");
		$pdfObject->row(array(vertaalTekst("Beleggingscategorie", $pdfObject->rapport_taal),
											vertaalTekst("in " . $pdfObject->rapportageValuta, $pdfObject->rapport_taal),
											vertaalTekst("in %", $pdfObject->rapport_taal)));
	}
	$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
	//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	if (count($pdfObject->portefeuilles) > 5)
	{
		$pdfObject->Ln(10);
	}
  }
	else
	{
		$pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		if($pdfObject->rapport_OIB_specificatie == 1)
		{
			$pdfObject->SetX($pdfObject->marge+$lijn1+5);
			$pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
			$pdfObject->setdrawcolor(255,255,255);
			$pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
												vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
												vertaalTekst("in valuta",$pdfObject->rapport_taal),
												vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal)));

		}

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	}
	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'], $pdfObject->rapport_fontcolor['g'], $pdfObject->rapport_fontcolor['b']);


}

function HeaderPERF_L32($object)
{
	$pdfObject = &$object;
	if (is_array($pdfObject->portefeuilles))
	{
		$pdfObject = &$object;

		$object->SetFont($object->rapport_font,$pdfObject->rapport_kop_fontstyle,$object->rapport_kop_fontsize);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		if($pdfObject->doubleHeader==true)
		{
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 12, 'F');
			unset($pdfObject->doubleHeader);
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+16,297-$pdfObject->marge,$pdfObject->GetY()+16);

		}
		else
		{
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 8, 'F');
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+12,297-$pdfObject->marge,$pdfObject->GetY()+12);
		}
		$pdfObject->SetDrawColor(0);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$object->SetWidths($object->widthA);
		$object->SetAligns($object->alignA);



	}
	else
	  $pdfObject->HeaderPERF();
}

function HeaderRISK_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge,32,297-$pdfObject->marge*2, 8, 'F');
	//$pdfObject->HeaderRISK();
}

function HeaderMUT_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	
	$pdfObject->SetX(100);
	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	$pdfObject->ln();
	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
							 vertaalTekst("Bankafschrift",$pdfObject->rapport_taal),
							 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
							 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
							 vertaalTekst("Rekening",$pdfObject->rapport_taal),
							 "",
							 vertaalTekst("Debet",$pdfObject->rapport_taal),
							 vertaalTekst("Credit",$pdfObject->rapport_taal),
							 ""));
	
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->ln();
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	
	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

}

function HeaderSMV_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->Row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),vertaalTekst("Saldo",$pdfObject->rapport_taal),vertaalTekst("Bedrag",$pdfObject->rapport_taal),vertaalTekst("C/D",$pdfObject->rapport_taal),vertaalTekst("GB",$pdfObject->rapport_taal),vertaalTekst("Omschrijving",$pdfObject->rapport_taal)));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln(6);
	
}

function HeaderTRANS_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->SetX(100);
	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	$pdfObject->ln();

	$pdfObject->widthA[1]=12;
	$pdfObject->widthA[11]=25;
	$pdfObject->widthA[12]=25;
	$pdfObject->widthA[13]=4;
	$pdfObject->widthB=$pdfObject->widthA;


	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		
		// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];
		
		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
		
	$resultaat			= $verkoopEind;
	$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
	
	$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
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
	$pdfObject->setDrawColor(255,255,255);
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

function HeaderPERFD_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->widthA = array(24,25,30,30,23,23,23,24,28,24,28);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
	
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	
  $pdfObject->setTextColor(255,255,255);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
	$pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Begin-\nvermogen in €",$pdfObject->rapport_taal),
										vertaalTekst("Stortingen en \nonttrekkingen in €",$pdfObject->rapport_taal),
										vertaalTekst("Koersresultaat in €",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Inkomsten\nin €",$pdfObject->rapport_taal)."",
										vertaalTekst("Kosten in €",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Opgelopen-\nrente in €",$pdfObject->rapport_taal),
										vertaalTekst("Beleggings\nresultaat in €",$pdfObject->rapport_taal),
										vertaalTekst("Eind-\nvermogen in €",$pdfObject->rapport_taal),
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).") in %",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).") in %"));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->setTextColor(0,0,0);
	//$sumWidth = array_sum($pdfObject->widthA);
	//$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}


function HeaderVOLKD_L32($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  $dataWidth=array(48,20,12,20,20,2,20,20,12,2,20,20,17,20,20,15);
  $splits=array(2,4,5,8,9,11,12,14);
  $n=0;
  $kopWidth=array();
  foreach ($dataWidth as $index=>$value)
  {
    if($index<=$splits[$n])
      $kopWidth[$n] += $value;
    if($index>=$splits[$n])
      $n++;
  }
  //$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($kopWidth);
  $pdfObject->SetAligns(array('L','C','L','C','L','C','L','C'));
  $pdfObject->CellBorders = array('','U','','U','','U','','U');
  $pdfObject->Row(array('',"Totaal commitment",'','Totaal opgevraagd','','Totaal terugbetaald','','Restant investering'));
  $pdfObject->CellBorders = array();
  
  $pdfObject->SetWidths($dataWidth);
  
  
  
  unset($pdfObject->CellBorders);
  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetWidths($dataWidth);
  
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->pageYstart=$pdfObject->GetY();
  $pdfObject->Row(array(vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    vertaalTekst("Aanvang",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    vertaalTekst("fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("in%",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Directe opbrengst",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Multiple",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor(0);
  
}

function HeaderVAR_L32($object)
{
  $pdfObject = &$object;
  $pdfObject->widthA = array(40,25,31,30,31,30,31,30,23,23,24,28);
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->rapport_fontsize=$pdfObject->rapport_fontsize;//+2;
  $pdfObject->setTextColor(255,255,255);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge*2, 8, 'F');
  $pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),'',
                    vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),'',
                    vertaalTekst("Resultaat",$pdfObject->rapport_taal),'',
                    vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal)));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->setTextColor(0,0,0);
  //$sumWidth = array_sum($pdfObject->widthA);
  //$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}

function HeaderVHO_L32($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	 $pdfObject->SetFont( $pdfObject->rapport_font, "B", $pdfObject->rapport_fontsize);
	
	$huidige 			=  $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2];
	$eindhuidige 	=  $huidige + $pdfObject->widthB[3]+ $pdfObject->widthB[4]+ $pdfObject->widthB[5];
	
	$actueel 			= $eindhuidige +  $pdfObject->widthB[6];
	$eindactueel 	= $actueel +  $pdfObject->widthB[7] +  $pdfObject->widthB[8] +  $pdfObject->widthB[9];
	
	$resultaat 		= $eindactueel +  $pdfObject->widthB[10];
	$eindresultaat = $resultaat +   $pdfObject->widthB[11] +   $pdfObject->widthB[12] +   $pdfObject->widthB[13] +   $pdfObject->widthB[14];

	// achtergrond kleur
	 $pdfObject->SetFillColor( $pdfObject->rapport_kop_bgcolor['r'], $pdfObject->rapport_kop_bgcolor['g'], $pdfObject->rapport_kop_bgcolor['b']);
	 $pdfObject->Rect( $pdfObject->marge,  $pdfObject->getY(), array_sum( $pdfObject->widthB), 16 , 'F');
	
	 $pdfObject->SetTextColor( $pdfObject->rapport_kop_fontcolor['r'], $pdfObject->rapport_kop_fontcolor['g'], $pdfObject->rapport_kop_fontcolor['b']);
	
	 $pdfObject->SetX( $pdfObject->marge+$huidige);


		if( $pdfObject->rapport_VHO_volgorde_beginwaarde == 0)
			 $pdfObject->Cell($pdfObject->widthB[3]+ $pdfObject->widthB[4]+ $pdfObject->widthB[5],4, vertaalTekst("Actuele koers", $pdfObject->rapport_taal), 0,0, "C");
		else
			 $pdfObject->Cell($pdfObject->widthB[3]+ $pdfObject->widthB[4]+ $pdfObject->widthB[5],4, vertaalTekst("Gemiddelde historische inkoopprijs", $pdfObject->rapport_taal), 0,0,"C");
		 $pdfObject->SetX( $pdfObject->marge+$actueel);
		if( $pdfObject->rapport_VHO_volgorde_beginwaarde == 0)
			 $pdfObject->Cell( $pdfObject->widthB[7] +  $pdfObject->widthB[8] +  $pdfObject->widthB[9],4, vertaalTekst("Gemiddelde historische inkoopprijs", $pdfObject->rapport_taal), 0,0,"C");
		else
			 $pdfObject->Cell( $pdfObject->widthB[7] +  $pdfObject->widthB[8] +  $pdfObject->widthB[9],4, vertaalTekst("Actuele koers", $pdfObject->rapport_taal), 0,0, "C");
		
		 $pdfObject->SetX( $pdfObject->marge+$resultaat);
		 $pdfObject->Cell($pdfObject->widthB[11] +   $pdfObject->widthB[12] +   $pdfObject->widthB[13],4, vertaalTekst("Rendement", $pdfObject->rapport_taal), 0,1, "C");
	$pdfObject->setDrawColor(255,255,255);
		 $pdfObject->Line(( $pdfObject->marge+$huidige), $pdfObject->GetY(), $pdfObject->marge + $eindhuidige, $pdfObject->GetY());
		 $pdfObject->Line(( $pdfObject->marge+$actueel), $pdfObject->GetY(), $pdfObject->marge + $eindactueel, $pdfObject->GetY());
		 $pdfObject->Line(( $pdfObject->marge+$resultaat), $pdfObject->GetY(), $pdfObject->marge + $eindresultaat, $pdfObject->GetY());


	
	
	if( $pdfObject->rapport_VHO_percentageTotaal == 1)
	{

			$aandeel = "Aandeel op totale waarde";
	}
	
	 $pdfObject->SetWidths( $pdfObject->widthB);
	 $pdfObject->SetAligns( $pdfObject->alignB);
	
	
	$y =  $pdfObject->getY();

		 $pdfObject->row(array("",
								 "\n".vertaalTekst("Fondsomschrijving", $pdfObject->rapport_taal),
								 vertaalTekst("Aantal", $pdfObject->rapport_taal),
								 vertaalTekst("Per stuk in valuta", $pdfObject->rapport_taal),
								 '',//vertaalTekst("Portefeuille in valuta", $pdfObject->rapport_taal),
								 vertaalTekst("Portefeuille in ". $pdfObject->rapportageValuta, $pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Per stuk in valuta", $pdfObject->rapport_taal),
								 '',//vertaalTekst("Portefeuille in valuta", $pdfObject->rapport_taal),
								 vertaalTekst("Portefeuille in ". $pdfObject->rapportageValuta, $pdfObject->rapport_taal),
								 vertaalTekst($aandeel, $pdfObject->rapport_taal),
								 vertaalTekst("Fonds-\nresultaat", $pdfObject->rapport_taal),
								 vertaalTekst("Valuta-\nresultaat", $pdfObject->rapport_taal),
								 vertaalTekst("Directe\nopbrengst", $pdfObject->rapport_taal),
								 vertaalTekst("in %", $pdfObject->rapport_taal)));

	 $pdfObject->SetWidths( $pdfObject->widthA);
	 $pdfObject->SetAligns( $pdfObject->alignA);
	if( $pdfObject->rapport_layout == 14)
		 $pdfObject->SetFont( $pdfObject->rapport_font,'b', $pdfObject->rapport_fontsize);
	else
		 $pdfObject->SetFont( $pdfObject->rapport_font,'bi', $pdfObject->rapport_fontsize);
	 $pdfObject->setY($y);
	if( $pdfObject->rapport_layout != 16)
		 $pdfObject->row(array(vertaalTekst("Categorie\n", $pdfObject->rapport_taal)));
	 $pdfObject->ln();

	
	 $pdfObject->SetWidths( $pdfObject->widthB);
	 $pdfObject->SetAligns( $pdfObject->alignB);
	
	// $pdfObject->Line( $pdfObject->marge, $pdfObject->GetY(), $pdfObject->marge + array_sum( $pdfObject->widthB), $pdfObject->GetY());
	$pdfObject->ln();
}


function HeaderCASHY_L32($object)
{
  $pdfObject = &$object;
  //$pdfObject->ln();
}

function HeaderATT_L32($object)
{
	$pdfObject = &$object;
	//$pdfObject->ln();
}
function HeaderOIV_L32($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
}
  function HeaderOIH_L32($object)
	{
		$pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige);

		if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			$pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5],4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"R");
		else
			$pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5],4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"R");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell($pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9],4, vertaalTekst("Actuele waarde",$pdfObject->rapport_taal), 0,0, "R");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell($pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15],4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->setDrawColor(255,255,255);
		//$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		//$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();


		$pdfObject->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											'',
											'',//vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
											'',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
											'',//vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											"",
											'',//vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
											'',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
											'',//vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
											vertaalTekst("Resultaat",$pdfObject->rapport_taal),
											vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
											vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)
										));


		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());



	}

function HeaderVOLK_L32($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
  
  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
  $eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
  
  $actueel 			= $eindhuidige + $pdfObject->widthB[6];
  $eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
  
  $resultaat 		= $eindactueel + $pdfObject->widthB[10];
  $eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  unset($pdfObject->fillCell);
  // lijntjes onder beginwaarde in het lopende jaar
  $pdfObject->SetX($pdfObject->marge+$huidige);
  $pdfObject->setDrawColor(255,255,255);
  $y = $pdfObject->getY();
  if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
    $pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5],4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
  else
    $pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5],4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell($pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9],4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,1, "C");
 // $pdfObject->SetX($pdfObject->marge+$resultaat);
 // $pdfObject->Cell($pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15],4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
  

  $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
  $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
//  $pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
 
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->setY($y);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);
  $pdfObject->setY($y);
  $pdfObject->row(array("",
                    "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n". vertaalTekst("Koers",$pdfObject->rapport_taal),
                    '',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "",
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    '',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),
                    vertaalTekst("in %",$pdfObject->rapport_taal)
                  ));
  
  
 

  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  //$pdfObject->ln();
  
  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
  
  
  
}
	
 	  function HeaderPERFG_L32($object)
	  {
			$pdfObject = &$object;
			$pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
			$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->setTextColor(255,255,255);
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
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
			$pdfObject->setTextColor(0,0,0);
			//$sumWidth = array_sum($pdfObject->widthA);
			//$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
    }
    
 	  function HeaderHSE_L32($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
 	    $pdfObject->ln();
		  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
			$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
			$actueel 			= $eindhuidige + $pdfObject->widthB[6];
			$eindactueel 	= array_sum($pdfObject->widthB);
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		$tmpY = $pdfObject->GetY();
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
		else if($pdfObject->rapport_layout == 4)
			$pdfObject->MultiCell(90,4, vertaalTekst("Fonds",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");

		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
		else if($pdfObject->rapport_layout == 4)
			$pdfObject->MultiCell(90,4, vertaalTekst("Waarde",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");

			$pdfObject->setDrawColor(255,255,255);
		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	
			$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Per stuk\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											"",
											vertaalTekst("Per stuk\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""));
	

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->setY($pdfObject->GetY()-8);
		$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);


	  }


function HeaderOIR_L32($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  //$dataWidth=array(65,25,15,30,30,10,25,30,20,20);
  
  $dataWidth=array(50,20,12,20,20,2,20,20,12,2,20,20,15,20,20,14);
  $splits=array(2,4,5,8,9,11,12,14);
  $n=0;
  $kopWidth=array();
  foreach ($dataWidth as $index=>$value)
  {
    if($index<=$splits[$n])
      $kopWidth[$n] += $value;
    if($index>=$splits[$n])
      $n++;
  }
  //$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($kopWidth);
  $pdfObject->SetAligns(array('L','C','L','C','L','C','L','C'));
  $pdfObject->CellBorders = array('','U','','U','','U','','U');
  $pdfObject->Row(array('',"Totaal commitment",'','Totaal opgevraagd','','Totaal terugbetaald','','Restant investering'));
  $pdfObject->CellBorders = array();
  
  $pdfObject->SetWidths($dataWidth);
  
  
  
  unset($pdfObject->CellBorders);
  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetWidths($dataWidth);
  
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->pageYstart=$pdfObject->GetY();
  $pdfObject->Row(array(vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    vertaalTekst("Aanvang",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    vertaalTekst("fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("in%",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Directe opbrengst",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Multiple",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor(0);
  
}

if(!function_exists('getTypeGrafiekData_L32'))
{
	function getTypeGrafiekData_L32($object, $type, $extraWhere = '', $items = array())
	{
		global $__appvar;
		$DB = new DB();
		if (!is_array($object->pdf->grafiekKleuren))
		{
			$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $object->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$object->pdf->grafiekKleuren = $kleuren;
		}
		$kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'Regio' => 'OIR', 'Beleggingssector' => 'OIS');
		$kleuren = $object->pdf->grafiekKleuren[$kleurVertaling[$type]];


			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum = '" . $object->rapportageDatum . "' AND " .
				" portefeuille = '" . $object->portefeuille . "' $extraWhere"
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			$DB->SQL($query);
			$DB->Query();
			$portefwaarde = $DB->nextRecord();
			$portTotaal = $portefwaarde['totaal'];
			if ($extraWhere == '')
			{
				$object->pdf->rapportageDatumWaarde = $portTotaal;
			}


		$query = "SELECT TijdelijkeRapportage.portefeuille, TijdelijkeRapportage." . $type . "Omschrijving as Omschrijving, TijdelijkeRapportage." . $type . " as type,SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  " .
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '" . $object->portefeuille . "') AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $object->rapportageDatum . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY " . $type . "  ORDER BY TijdelijkeRapportage." . $type . "Volgorde";
		debugSpecial($query, __FILE__, __LINE__);

		$DB->SQL($query);
		$DB->Query();

		while ($categorien = $DB->NextRecord())
		{
			$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = vertaalTekst($categorien['Omschrijving'], $object->pdf->rapport_taal);
			if ($categorien['type'] == '')
			{
				$categorien['type'] = 'geenWaarden';
			}

			if (count($items) > 0 && !in_array($categorien['type'], $items))
			{
				$categorien['type'] = 'Overige';
				$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = 'Overige';
				$kleuren[$categorien['type']] = array('R' => array('value' => 100), 'G' => array('value' => 100), 'B' => array('value' => 100));
			}


			$valutaData[$categorien['type']]['port']['waarde'] += $categorien['subtotaalactueel'];
		}

		foreach ($valutaData as $waarde => $data)
		{
			if (isset($data['port']['waarde']))
			{
				$veldnaam = $object->pdf->veldOmschrijvingen[$type][$waarde];
				if ($veldnaam == '')
				{
					$veldnaam = 'Overige';
				}

				$typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
				$typeData['port']['waarde'][$waarde] = $data['port']['waarde'];
				$typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;

				//if($veldnaam=='Overige' && isset($kleuren['Liquiditeiten']))
				//  $waarde='Liquiditeiten';

				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}

/*
	if(!function_exists('PieChart_L32'))
	{
		function PieChart_L32($object, $w, $h, $data, $format, $colors = null)
		{
			$pdfObject = &$object;


			$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
			$pdfObject->SetLegends($data, $format);

			$XPage = $pdfObject->GetX();
			$YPage = $pdfObject->GetY();
			$margin = 2;
			$hLegend = 2;
			$radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
			$radius = floor($radius / 2);
			$XDiag = $XPage + $margin + $radius;
			$YDiag = $YPage + $margin + $radius;
			if ($colors == null)
			{
				for ($i = 0; $i < $pdfObject->NbVal; $i++)
				{
					$gray = $i * intval(255 / $pdfObject->NbVal);
					$colors[$i] = array($gray, $gray, $gray);
				}
			}

			//Sectors
			$pdfObject->SetLineWidth(0.2);
			$angleStart = 0;
			$angleEnd = 0;
			$i = 0;
			foreach ($data as $val)
			{
				$angle = floor(($val * 360) / doubleval($pdfObject->sum));
				if ($angle != 0)
				{
					$angleEnd = $angleStart + $angle;
					$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
					$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
					$angleStart += $angle;
				}
				$i++;
			}
			if ($angleEnd != 360)
			{
				$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
			}

			//Legends
			$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

			$x1 = $XPage + $w + $radius * .5;
			$x2 = $x1 + $hLegend + $margin - 12;
			$y1 = $YDiag - ($radius) + $margin;

			for ($i = 0; $i < $pdfObject->NbVal; $i++)
			{
				$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$pdfObject->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
				$pdfObject->SetXY($x2, $y1);
				$pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
				$y1 += $hLegend + $margin;
			}
		}
	}
*/
}


if(!function_exists('PieChart_L32'))
{
  function PieChart_L32($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);
    
    
    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
      $pdfObject->Rect($XPage,$YPage,$w,$h);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    $pdfObject->setXY($XPage,$YPage);
    $pdfObject->SetFont($pdfObject->rapport_font, 'B', 8.5);
    $pdfObject->Cell($w,4,$titel,0,1,'L');
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $YPage=$YPage+$pdfObject->rowHeight+4;
    $pdfObject->setXY($XPage,$YPage);
    $margin = 4;
    $hLegend = 2;
    $radius = min($w, $h); //
    $radius = ($radius / 2)-4;
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $pdfObject->NbVal; $i++) {
        $gray = $i * intval(255 / $pdfObject->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->SetLineWidth(0.1);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $factor =$radius+4;
    $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      //$pdfObject->SetDrawColor(255,255,0);
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      if ($angle != 0 && $angle>1)
      {
        $angleEnd = $angleStart + $angle;
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        
        //$lineAngle=($angleEnd)/180*M_PI;
        //$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));
        //echo ($angleEnd-$angleStart)."= ( $angleEnd-$angleStart ) $val  <br>\n";ob_flush();
        
        if(round($angleEnd,1)==360)
          $angleEnd=360;
        //    echo "$val : $XDiag, $YDiag, $radius, $angleStart, $angleEnd <br>\n";
        if(abs($angleEnd-$angleStart) > 1)
          $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd,'F');
        
        if($val > 2)
        {
          //$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          if($pdfObject->debug==true)
          {
            $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255)));
            $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor));
          }
          $pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
          $pdfObject->Cell(10,4,number_format($val,0,',','.').'%',0,0,'C');
        }
        $angleStart += $angle;
      }
      
      $i++;
    }
    if ($angleEnd != 360)
    {
      $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360,'F');
    }
    
    
    $i = 0;
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.3527,'color'=>array(255,255,255)));
      if ($angle != 0 && $angle != 360)
      {
        $angleEnd = $angleStart + $angle;
        $lineAngle=($angleEnd)/180*M_PI;
        $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$radius), $YDiag-(cos($lineAngle)*$radius));
        $angleStart += $angle;
      }
      $i++;
    }
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor(0,0,0);
    
    //Legends
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
    $x1 = $XPage + $margin;
    $x2 = $x1 + $hLegend + 2 ;
    $y1 = $YDiag + ($radius) + $margin +5;
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
    
    if(is_array($legendaStart))
    {
      $x1=$legendaStart[0];
      $y1=$legendaStart[1];
      $x2 = $x1 + $hLegend + 2 ;
      
    }
    elseif($legendaStart=='geen')
    {
      return '';
    }
    
    for($i=0; $i<$pdfObject->NbVal; $i++)
    {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
  }
}

?>