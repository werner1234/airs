<?php

function Header_basis_L54($object)
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
    elseif ($pdfObject->rapport_type == "VRAGEN")
    {
     if(is_file($pdfObject->rapport_logo))
     {
       $factor=0.04;
       $xSize=1605*$factor;//$x=885*$factor;
       $ySize=203*$factor;//$y=849*$factor;
       $logopos=$pdfObject->marge;//(297/2)-($xSize/2);
       $pdfObject->Image($pdfObject->rapport_logo, $logopos, $logopos, $xSize, $ySize);
      
     }
     $pdfObject->setY(8);
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
     $pdfObject->MultiCell($pdfObject->w-2*$pdfObject->marge,4,$pdfObject->rapport_portefeuille."\n".$pdfObject->rapport_naam1,0,'R');
     $pdfObject->setY(30);
   }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);
  
      if($pdfObject->rapport_type == "VKMA1")
      {
        $rapport_koptextBackup=$pdfObject->rapport_koptext;
        $pdfObject->rapport_koptext="{Naam1}";//\nRisicoprofiel van uw portefeuille: {Risicoklasse}
      }

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
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", formatPortefeuille($pdfObject->rapport_portefeuille), $pdfObject->rapport_koptext);
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


		//rapport_risicoklasse


    if(!empty($pdfObject->rapport_logo_tekst))
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

		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
      global $__appvar;

			$toekomstLogo=$__appvar["basedir"]."/html/rapport/logo/toekomstbeleggen.png";
			if($pdfObject->portefeuilledata['SoortOvereenkomst']=='Toekomstbeleggen' && file_exists($toekomstLogo))
			{
				$factor=0.04;
				$xSize=1605*$factor;//$x=885*$factor;
				$ySize=203*$factor;//$y=849*$factor;
				$logo = $toekomstLogo;
			}
      else
			{
				$factor=0.03;
				$xSize=1605*$factor;//$x=885*$factor;
				$ySize=611*$factor;//$y=849*$factor;
				if ($pdfObject->portefeuilledata['Vermogensbeheerder'] == 'HAV')
				{
			//		$xSize = 2509 * $factor;//$x=885*$factor;

				}
				$logo=$pdfObject->rapport_logo;
			}
			$logopos = ($pdfObject->w / 2) - ($xSize / 2);
      
      if($pdfObject->rapport_type <> "SCENARIO")
      {
        $pdfObject->Image($logo, $logopos, 5, $xSize, $ySize);
      }
		}

		if($pdfObject->rapport_type == "MOD" || round($pdfObject->w) == 210)
			$x = 160;
		else
			$x = 250;

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  $pdfObject->SetY($y+19);
	  $pdfObject->SetX($pdfObject->w/2-50);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+15;
  	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
  
      if($pdfObject->rapport_type == "VKMA1" && isset($rapport_koptextBackup))
      {
        $pdfObject->rapport_koptext=$rapport_koptextBackup;
      }
    }

}

function HeaderVKMA1_L54($object)
{
  $pdfObject = &$object;
}

	function HeaderVKM_L54($object)
	{
		$pdfObject = &$object;
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->ln();
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
		$pdfObject->HeaderVKM();
	}

function HeaderSCENARIO_L54($object)
{
	$pdfObject = &$object;

}

function HeaderEND_L54($object)
{
	$pdfObject = &$object;

}

function HeaderVRAGEN_L54($object)
{
  $pdfObject = &$object;
}

function HeaderTRANS_L54($object)
{
	$pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
    $pdfObject->SetX(100);
    $pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
    $pdfObject->ln();
  //listarray($pdfObject->widthA);
  $pdfObject->widthA[11]=24;
  $pdfObject->widthA[12]=$pdfObject->widthA[11];
  
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

	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle.'u',$pdfObject->rapport_fontsize);

	//		echo "$inkoopEind - $inkoop en $verkoopEind - $verkoop en $resultaatEind - $resultaat ";
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
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

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
                 vertaalTekst("Aankoop waarde\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop waarde\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Resultaat voorafgaand verslagperiode",$pdfObject->rapport_taal),
                 vertaalTekst("Resultaat gedurende verslagperiode",$pdfObject->rapport_taal),
                 $procentTotaal));
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);


}
function HeaderMUT_L54($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderMUT();
}
	
function HeaderVOLK_L54($object)
{
    $pdfObject = &$object;
		$pdfObject->ln();

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6] ;
			$eindactueel 	= $actueel  + $pdfObject->widthB[7]+ $pdfObject->widthB[8];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[9] ;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13];
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle.'u',$pdfObject->rapport_fontsize);

			$pdfObject->SetX($pdfObject->marge+$huidige+$pdfObject->widthB[2]);
			$pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5],4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell($pdfObject->widthB[7]+ $pdfObject->widthB[8],4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"R");
			else
			  $pdfObject->Cell($pdfObject->widthB[7]+ $pdfObject->widthB[8],4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"R");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell($pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13],4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->aligns[10]='C';
	$pdfObject->aligns[11]='C';
	$pdfObject->aligns[12]='C';
	$pdfObject->aligns[13]='C';
	//listarray($pdfObject->aligns);exit;
		$y = $pdfObject->getY();


			$pdfObject->row(array(vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	$pdfObject->SetAligns($pdfObject->alignB);
	


		$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
			$pdfObject->row(array("",vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  }

function HeaderKERNZ_L54($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();

  
   // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 12 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle.'u',$pdfObject->rapport_fontsize);
  
  $pdfObject->SetX(90);
  $pdfObject->Cell(100,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
  
  $pdfObject->row(array(vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Standaarddeviatie",$pdfObject->rapport_taal),
                    vertaalTekst("Ongewogen correlatie",$pdfObject->rapport_taal),
                    vertaalTekst("Ongewogen correlatie binnen categorie",$pdfObject->rapport_taal)
                    ));
  
  $pdfObject->ln(-8);
  $pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
  $pdfObject->row(array("",vertaalTekst("Categorie"."\n ",$pdfObject->rapport_taal)));



}

  function HeaderHUIS_L54($object)
  {
    $pdfObject = &$object;
  }
  
  function HeaderINDEX_L54($object)
  {
	  	$pdfObject = &$object;
  }

function HeaderDOORKIJK_L54($object)
{
	$pdfObject = &$object;
}
    
  function HeaderPERF_L54($object)
  {
	  	$pdfObject = &$object;
	  	//$pdfObject->SetY($pdfObject->GetY()+4);
  	  $pdfObject->HeaderPERF();
  }

  function HeaderAFM_L54($object)
  {
	  	$pdfObject = &$object;
	
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
	
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	
		// lijntjes onder beginwaarde in het lopende jaar
	
		if($pdfObject->rapport_OIB_specificatie == 1)
		{
			$pdfObject->SetX($pdfObject->marge+$lijn1+5);
			$pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
		

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

  }
  
  function HeaderOIH_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderHSE();
	}

function HeaderPERFG_L54($object)
{
  $pdfObject = &$object;
  $w=282/7;
  $pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w);
  
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
  $pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
                    vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).")",
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
  
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

}

	function HeaderOIB_L54($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	 // $pdfObject->HeaderOIB();
     // $pdfObject->Ln();
	}

	function HeaderOIV_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}


	function HeaderPERFD_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFD();
	}
	function HeaderVOLKD_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVOLKD();
	}
	function HeaderVHO_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderVHO();
	}
	function HeaderGRAFIEK_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L54($object)
	{
  	  $pdfObject = &$object;
  	
	}

function HeaderFISCAAL_L54($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
  $eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
  
  $actueel 			= $eindhuidige + $pdfObject->widthB[6];
  $eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
  
  $resultaat 		= $eindactueel + $pdfObject->widthB[10];
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 12 , 'F');
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetX($pdfObject->marge+$huidige+5);
  $pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische kostprijs",$pdfObject->rapport_taal), 0,0,"C");
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetX($pdfObject->marge+$resultaat);
  //$pdfObject->Cell(70,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");
  //$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
  //$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
  //$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $y = $pdfObject->getY();
  $pdfObject->Ln();
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
               vertaalTekst('',$pdfObject->rapport_taal),
               vertaalTekst("Fiscale\nWaardering",$pdfObject->rapport_taal),
               "",'',''));
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
  $pdfObject->setY($y);
  $pdfObject->row(array("Categorie\n"));
  $pdfObject->ln();
  $pdfObject->ln();
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  
  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge*2,$pdfObject->GetY());
  
}

	function HeaderMODEL_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L54($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L54($object)
	{
  	  $pdfObject = &$object;
  	//  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
	}



  function HeaderATT_L54($object)
	{
    $pdfObject = &$object;
    $colW=280/11;
    $pdfObject->widthA = array($colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW);//,23
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    


		//for($i=0;$i<count($pdfObject->widthA);$i++)
		//  $pdfObject->fillCell[] = 1;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		$pdfObject->Cell(94,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(94,4, date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
    $pdfObject->ln(1);


    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
    $pdfObject->ln();
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Resultaat\n ",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopenrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
                          "Rendement\n ",
                          "Rendement\ncumulatief"));
    $sumWidth = array_sum($pdfObject->widthA);
	 // $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}



if(!function_exists('getTypeGrafiekData'))
{
	function getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
	{
	  global $__appvar;
	  $DB = new DB();
	  if(!is_array($object->pdf->grafiekKleuren))
	  {
	    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$object->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  	$DB->SQL($q);
  		$DB->Query();
  		$kleuren = $DB->LookupRecord();
  		$kleuren = unserialize($kleuren['grafiek_kleur']);
  		$object->pdf->grafiekKleuren=$kleuren;
	  }
    $kleurVertaling=array('Beleggingscategorie'=>'OIB','Valuta'=>'OIV','Regio'=>'OIR','Beleggingssector'=>'OIS');
	  $kleuren=$object->pdf->grafiekKleuren[$kleurVertaling[$type]];

	  //if(!isset($object->pdf->rapportageDatumWaarde) || $extraWhere !='')
	  //{
	   $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$object->rapportageDatum."' AND ".
								 " portefeuille = '".$object->portefeuille."' $extraWhere"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
  		$DB->SQL($query);
  		$DB->Query();
  		$portefwaarde = $DB->nextRecord();
  		$portTotaal = $portefwaarde['totaal'];
  		if($extraWhere=='')
  	  	$object->pdf->rapportageDatumWaarde=$portTotaal;
	  //}
	  //else
	  //  $portTotaal=$object->pdf->rapportageDatumWaarde;

		$query = "SELECT TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.".$type."Omschrijving as Omschrijving, TijdelijkeRapportage.".$type." as type,SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  ".
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '".$object->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$object->rapportageDatum."' $extraWhere"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY ".$type."  ORDER BY TijdelijkeRapportage.".$type."Volgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $object->pdf->veldOmschrijvingen[$type][$categorien['type']]=vertaalTekst($categorien['Omschrijving'],$object->pdf->rapport_taal);
		  if ($categorien['type']=='')
		    $categorien['type']='geenWaarden';

		  if(count($items) > 0 && !in_array($categorien['type'],$items))
		  {
		    $categorien['type']='Overige';
		    $object->pdf->veldOmschrijvingen[$type][$categorien['type']]='Overige';
		    $kleuren[$categorien['type']]=array('R'=>array('value'=>100),'G'=>array('value'=>100),'B'=>array('value'=>100));
		  }


      $valutaData[$categorien['type']]['port']['waarde']+=$categorien['subtotaalactueel'];
    }

		foreach ($valutaData as $waarde=>$data)
		{
		  if(isset($data['port']['waarde']))
		  {
        $veldnaam=$object->pdf->veldOmschrijvingen[$type][$waarde];
        if($veldnaam=='')
          $veldnaam='Overige';

		    $typeData['port']['procent'][$waarde]=$data['port']['waarde']/$portTotaal;
		    $typeData['port']['waarde'][$waarde]=$data['port']['waarde'];
		    $typeData['grafiek'][$veldnaam]=$typeData['port']['procent'][$waarde]*100;

		    //if($veldnaam=='Overige' && isset($kleuren['Liquiditeiten']))
		    //  $waarde='Liquiditeiten';

		    $typeData['grafiekKleur'][]=array($kleuren[$waarde]['R']['value'],$kleuren[$waarde]['G']['value'],$kleuren[$waarde]['B']['value']);
		  }
		}

   $object->pdf->grafiekData[$type]=$typeData;

	}
}

if(!function_exists('formatPortefeuille'))
{
	function formatPortefeuille($portefeuille)
	{
		$oldPortefeuilleString = strval($portefeuille);
		$i = 1;
		$puntenAantal = 0;
		if (strlen($oldPortefeuilleString) == 9)
		{
			$maxPuntenAantal = 3;
			$maxTekensPerPunt = 2;
		}
		elseif (strlen($oldPortefeuilleString) == 6)
		{
			$maxPuntenAantal = 1;
			$maxTekensPerPunt = 3;
		}
		else
		{
			return $oldPortefeuilleString;
		}

		for ($j = 0; $j < strlen($oldPortefeuilleString); $j++)
		{
			if ($i > $maxTekensPerPunt && $puntenAantal < $maxPuntenAantal)
			{
				$portefeuilleString .= '.';
				$i = 1;
				$puntenAantal++;
			}
			$portefeuilleString .= $oldPortefeuilleString[$j];
			$i++;
		}

		return $portefeuilleString;
	}
}


if(!function_exists('PieChart_L54'))
{
  function PieChart_L54($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);

    
    
    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();

		$extraWidth=0;
		$extraY=0;
		if(is_array($legendaStart))
		{
			$x1 = $legendaStart[0];
			if ($x1 > $XPage + $w) //legenda rechts
			{
				$extraWidth = $pdfObject->wLegend + $w / 2;
				$extraY = -15;
			}
			else
			{
				$extraWidth=6;
				$extraY=15+count($pdfObject->legends)*4;
			}
		}
		
    
    if($pdfObject->debug==true)
    {
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
      $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
      $pdfObject->Rect($XPage,$YPage,$w,$h);
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
    }
		$pdfObject->Rect($XPage-3,$YPage-3,$w+$extraWidth,$h+$extraY);
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
      if (round($angle,1) != 0.0)
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
    
    for($i=0; $i<$pdfObject->NbVal; $i++) {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
  }
}
?>