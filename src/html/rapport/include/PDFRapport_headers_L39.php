<?php

function Header_basis_L39($object)
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

		if ($pdfObject->rapport_type <> "HUIS")
		  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
			global $__appvar;
//      if($__appvar['bedrijf']=='TEST')
//      {
//        $xSize=40;
//        $logopos=$pdfObject->w/2-$xSize/2;
//        $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize);
//      }
//      else
//      {
        if ($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" || $pdfObject->rapport_type == "HUIS")
        {
          $logopos = 85;
        }
        else
        {
          $logopos = 137;
        }
        $factor=0.06;
        $xSize=340*$factor;
        $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize);
//      }


		}

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY"  || $pdfObject->rapport_type == "HUIS" )
			$x = 160;
		else
			$x = 250;

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  $pdfObject->SetY($y+19);
	  $pdfObject->SetX(100);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+10;
  	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }

}

  function HeaderSCENARIO_L39($object)
	{
	  $pdfObject = &$object;
  }

function HeaderHUIS_L39($object)
{
  $pdfObject = &$object;
  $y=$pdfObject->getY();
  $voet='Capitael B.V. -  Emmapark 9 - 2595 ES DEN HAAG  - +31 70 31 50 999 - info@capitael.nl - www.capitael.nl';
  $pdfObject->AutoPageBreak=false;
  $pdfObject->SetXY($pdfObject->marge,-14);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-2);
  $pdfObject->MultiCell(210-$pdfObject->marge*2,4,$voet,0,'C');
 // $pdfObject->ln();
  $pdfObject->MultiCell(210-$pdfObject->marge*2,4,$pdfObject->rapport_voettext,0,'C');
  $pdfObject->setXY($pdfObject->marge,$y-10);
  $pdfObject->AutoPageBreak=true;
  
}
  function HeaderVKMD_L39($object)
	{
		$pdfObject = &$object;
	$pdfObject->ln();
	$widthBackup=$pdfObject->widths;
	$dataWidth=array(28,50,20,20,18,20,21,18,18,18,20,15,16);
	$pdfObject->SetWidths($dataWidth);
	$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	$lastColors=$pdfObject->CellFontColor;
	unset($pdfObject->CellFontColor);
	unset($pdfObject->CellBorders);
	if(!isset($pdfObject->vmkHeaderOnderdrukken))
	{
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($dataWidth), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->Row(array(vertaalTekst("Risico\ncategorie", $pdfObject->rapport_taal),
								 "\n" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
								 "\n" . date('d-m-Y', $pdfObject->rapport_datumvanaf),
								 "\n" . date('d-m-Y', $pdfObject->rapport_datum),
								 "\n" . vertaalTekst("Mutaties", $pdfObject->rapport_taal),
								 "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
								 vertaalTekst("Gemiddeld vermogen", $pdfObject->rapport_taal),
								 vertaalTekst("Doorl. kosten %", $pdfObject->rapport_taal),
								 vertaalTekst("Trans Cost %", $pdfObject->rapport_taal),
								 vertaalTekst("Perf Fee %", $pdfObject->rapport_taal),
								 vertaalTekst("Fondskost. absoluut", $pdfObject->rapport_taal),
								 "\n" . vertaalTekst("Weging", $pdfObject->rapport_taal),
								 vertaalTekst("VKM\nBijdrage", $pdfObject->rapport_taal)));
		unset($pdfObject->vmkHeaderOnderdrukken);
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	}
	$pdfObject->widths=$widthBackup;
	$pdfObject->CellFontColor=$lastColors;
	$pdfObject->SetLineWidth(0.1);
}
	
  function HeaderMOD_L39($object)
	{
	  $pdfObject = &$object;
    
    	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= array_sum($pdfObject->widthB);

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());

		$tmpY = $pdfObject->GetY();

		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal),
											($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""),
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->setY($pdfObject->GetY()-8);
		$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
		$pdfObject->ln();

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    
    
	}

	function HeaderVOLK_L39($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		//$pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
		$pdfObject->Cell(297-$pdfObject->marge*2,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);


		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
		$eindhuidige 	= $huidige + $pdfObject->widthB[4]+$pdfObject->widthB[5]+$pdfObject->widthB[6];

		$actueel 			= $eindhuidige ;
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8]  ;

		$resultaat 		= $eindactueel + $pdfObject->widthB[9]+ $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11]+  $pdfObject->widthB[12] + $pdfObject->widthB[13];

		$pdfObject->SetX($pdfObject->marge+$huidige);

    $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(50,4, vertaalTekst("Actuele waarde",$pdfObject->rapport_taal), 0,0, "C");
  	$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(50,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


    for($i=0;$i<count($pdfObject->widthB);$i++)
		  $pdfObject->fillCell[] = 1;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
   	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $y = $pdfObject->getY();
    $pdfObject->setY($y);
    $pdfObject->SetWidths(array(array_sum($pdfObject->widthB)));
    $pdfObject->row(array(" \n "));
    unset($pdfObject->fillCell);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

    
   
 		$pdfObject->setY($y);
    
   	$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in euro",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in euro",$pdfObject->rapport_taal),
										vertaalTekst("Weging",$pdfObject->rapport_taal),
                    vertaalTekst("Yield",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);



		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
	}


	function HeaderVHO_L39($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
		$eindhuidige 	= $huidige + $pdfObject->widthB[4]+$pdfObject->widthB[5]+$pdfObject->widthB[6];

		$actueel 			= $eindhuidige ;
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8]  ;

		$resultaat 		= $eindactueel + $pdfObject->widthB[9]+ $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11]+  $pdfObject->widthB[12] + $pdfObject->widthB[13];

		$pdfObject->SetX($pdfObject->marge+$huidige);

    $pdfObject->Cell(50,4, vertaalTekst("Historischewaarde",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(50,4, vertaalTekst("Actuele waarde",$pdfObject->rapport_taal), 0,0, "C");
  	$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(50,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


    for($i=0;$i<count($pdfObject->widthB);$i++)
		  $pdfObject->fillCell[] = 1;
      
   	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $y = $pdfObject->getY();
    $pdfObject->setY($y);
    $pdfObject->SetWidths(array(array_sum($pdfObject->widthB)));
    $pdfObject->row(array(" \n "));
    unset($pdfObject->fillCell);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

    
   
 		$pdfObject->setY($y);
    
   	$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in euro",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in euro",$pdfObject->rapport_taal),
										vertaalTekst("Weging",$pdfObject->rapport_taal),
                    vertaalTekst("Yield",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);



		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->ln();
	}
  
  function HeaderATT_L39($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(27,25,30,30,23,23,23,24,28,24,25);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
	//	  $pdfObject->fillCell[] = 1;

/*
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);
*/
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

	function HeaderOIB_L39($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');

    $pdfObject->SetWidths(array(290-$pdfObject->marge));
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->fillCell=array(1);
    $pdfObject->row(array(" \n "));
    unset($pdfObject->fillCell);
    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),290,$pdfObject->GetY());
   // $pdfObject->HeaderOIB();

	}

	function HeaderAFM_L39($object)
	{ 
    $pdfObject = &$object;
    $pdfObject->SetY($pdfObject->GetY()-4);
    $pdfObject->HeaderOIB();
	}
  
  function HeaderEND_L39($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetWidths(array(290-$pdfObject->marge));
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->fillCell=array(1);
    $pdfObject->row(array(" \n "));
    unset($pdfObject->fillCell);
    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),290,$pdfObject->GetY());
   // $pdfObject->HeaderOIB();
	}
  
  function HeaderMUT_L39($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

 		$pdfObject->SetX(100);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	  $pdfObject->ln();
		// achtergrond kleur
//listarray($pdfObject->widthB);
		$pdfObject->widthB[1]=26;
		$pdfObject->widthB[8]=24;
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
										 vertaalTekst("Bank Afschrift",$pdfObject->rapport_taal),
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

function HeaderTRANSFEE_L39($object)
{
  $pdfObject = &$object;
  $backup=$pdfObject->rapport_fontsize;
  $pdfObject->rapport_fontsize-=2;
  $pdfObject->HeaderTRANS();
  $pdfObject->rapport_fontsize=$backup;
}

  function HeaderTRANS_L39($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	
      /*
  			// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] ;
		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[6] + $pdfObject->widthB[7] ;
		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
		$pdfObject->SetX($inkoop);
		$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($verkoop);
		$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($resultaat);
		$pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->ln();
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
*/


		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    
   

			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Type",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Koers",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoopwaarde in euro",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoopwaarde in euro",$pdfObject->rapport_taal),
										 vertaalTekst("Gerealiseerd resultaat",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal)));
       $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge+ array_sum($pdfObject->widthA),$pdfObject->GetY());
             
      $pdfObject->ln(1);
  }

function HeaderPERFG_L39($object)
{
	$pdfObject = &$object;
	$pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
	//	  $pdfObject->fillCell[] = 1;

	/*
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln(1);
      $pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
      $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
      $pdfObject->ln(1);
  */
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
	$pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
										vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
										vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
										vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).")",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$sumWidth = array_sum($pdfObject->widthA);
	$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}


	function HeaderPERF_L39($object)
	{
    $pdfObject = &$object;

    	// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB)-$pdfObject->marge, 8, 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(2);
	//  $pdfObject->Cell(90,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//	$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		
    $pdfObject->setX($pdfObject->marge);
    $pdfObject->Cell(297-($pdfObject->marge*2),4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');

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
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB)-$pdfObject->marge,$pdfObject->GetY());
	}

function HeaderINDEX_L39($object)
{
  $pdfObject = &$object;
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

		$query = "SELECT TijdelijkeRapportage.portefeuille, 
                     TijdelijkeRapportage.fonds, 
                     TijdelijkeRapportage.rekening, 
                     TijdelijkeRapportage.".$type."Omschrijving as Omschrijving, 
                     TijdelijkeRapportage.".$type." as type,
                     (TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  ".
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '".$object->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$object->rapportageDatum."' $extraWhere"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.".$type."Volgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query); //echo $query;
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{// listarray($categorien);
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
      if($categorien['fonds'] <> '')
        $valutaData[$categorien['type']]['port']['fondsen'][$categorien['fonds']]=$categorien['fonds'];
      if($categorien['rekening'] <> '')  
        $valutaData[$categorien['type']]['port']['rekeningen'][$categorien['rekening']]=$categorien['rekening'];
      
    }
   

		foreach ($valutaData as $waarde=>$data)
		{
		  if(isset($data['port']['waarde']))
		  {
        $veldnaam=$object->pdf->veldOmschrijvingen[$type][$waarde];
        if($veldnaam=='')
          $veldnaam='Overige';
          
        $typeData['port']['fondsen'][$waarde]=$data['port']['fondsen'];
        $typeData['port']['rekeningen'][$waarde]=$data['port']['rekeningen'];
        

		    $typeData['port']['procent'][$waarde]=$data['port']['waarde']/$portTotaal;
		    $typeData['port']['waarde'][$waarde]=$data['port']['waarde'];
		    $typeData['grafiek'][$veldnaam]=$typeData['port']['procent'][$waarde]*100;
		    $typeData['grafiekKleur'][]=array($kleuren[$waarde]['R']['value'],$kleuren[$waarde]['G']['value'],$kleuren[$waarde]['B']['value']);
		  }
		}

   $object->pdf->grafiekData[$type]=$typeData;

	}
}

if(!function_exists('PieChart'))
{
  function PieChart($object,$w, $h, $data, $format, $colors=null)
  {



      $object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
      $object->SetLegends($data,$format);

      $XPage = $object->GetX();
      $YPage = $object->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $object->NbVal; $i++) {
              $gray = $i * intval(255 / $object->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $object->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;

      $object->sum=0;
      foreach ($data as $key=>$value)
      {
        $data[$key]=abs($value);
        $object->sum+=abs($value);
      }


      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($object->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $object->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $object->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $object->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $object->SetFont($object->rapport_font, '', $object->rapport_fontsize);

      $x1 = $XPage + $w ;
      $x2 = $x1  + $margin ;
      $y1 = $YDiag - $radius + ($margin*2)  ;



      for($i=0; $i<$object->NbVal; $i++) {
          $object->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $object->Rect($x1-2, $y1, $hLegend, $hLegend, 'DF');
          $object->SetXY($x2,$y1);
          $object->Cell(0,$hLegend,$object->legends[$i]);
          $y1+=$hLegend + $margin;
      }
      $object->setY($YPage+$h);

  }
}


if(!function_exists('printRendement'))
{
 function printRendement($object, $portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
 {
  		global $__appvar;
		// vergelijk met begin Periode rapport.
  //$startX=$object->marge;
  $startX=$object->getX();
  
  
		$DB= new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];

		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$vergelijkWaarde = $DB->nextRecord();
		$vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($object->rapportageValuta,$rapportageDatumVanaf);

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
    	debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille = $DB->nextRecord();
		$actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $object->ValutaKoersEind;

		$resultaat = ($actueleWaardePortefeuille -
									$vergelijkWaarde -
									getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta) +
									getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta)
									);

		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $object->portefeuilledata['PerformanceBerekening'],$object->rapportageValuta);

		$object->ln(2);
	 $extraX=10;

		if($kort)
			$min = 8;

		if(($object->GetY() + 22 - $min) >= $object->pagebreak) {
			$object->AddPage();
			$object->ln();
		}

		$object->SetFillColor($object->rapport_kop_bgcolor['r'],$object->rapport_kop_bgcolor['g'],$object->rapport_kop_bgcolor['b']);
		$object->Rect($startX,$object->getY(),119+$extraX,(16-$min),'F');
		$object->SetFillColor(0);
		$object->Rect($startX,$object->getY(),119+$extraX,(16-$min));
		$object->ln(2);
		//$object->SetX($startX);
		$object->SetX($startX);

    if(substr($rapportageDatumVanaf,5,5)=='01-01')
    {
      $resultaatTekst="Resultaat lopend kalenderjaar";
      $performanceTekst="Rendement lopend kalenderjaar";
    }
    else
    {
      $resultaatTekst="Resultaat over verslagperiode"; 
      $performanceTekst="Rendement over verslagperiode";
    }
		// kopfontcolor
		if(!$kort)
		{
			$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
			$object->Cell(80+$extraX,4, vertaalTekst($resultaatTekst,$object->rapport_taal), 0,0, "L");
			$object->Cell(39,4, $object->formatGetal($resultaat,2), 0,1, "R");
			$object->ln();
		}
		$object->SetX($startX);

		$object->Cell(80+$extraX,4, vertaalTekst($performanceTekst,$object->rapport_taal), 0,0, "L");
		$object->Cell(39,4, $object->formatGetal($performance,2)."%", 0,1, "R");
		$object->ln(2);
  }
}

if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object,$vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$object->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();
		$lmarge=0.001;
    //$startX=$object->marge;
    $startX=$object->getX();

 
		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($object->GetY() + $hoogte) > $object->pagebreak)
		{
			$object->AddPage();
			$object->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;
		$extraX=0;


		if($object->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $object->rapport_perfIndexJanuari = false;
	  }
		if($object->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($object->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($object->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($object->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$extraruimteOmschrijving=19;
		$extraX+=$extraruimteOmschrijving;

		$object->ln();
		$object->SetFillColor($object->rapport_kop_bgcolor['r'],$object->rapport_kop_bgcolor['g'],$object->rapport_kop_bgcolor['b']);
		$object->Rect($startX,$object->getY(),110+$extraX,$hoogte,'F');
		$object->SetFillColor(0);
		$object->Rect($startX,$object->getY(),110+$extraX,$hoogte);
		$object->SetX($startX);

		// kopfontcolor
		//$object->SetTextColor($object->rapport_kop4_fontcolor['r'],$object->rapport_kop4_fontcolor['g'],$object->rapport_kop4_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		$object->SetFont($object->rapport_kop4_font,$object->rapport_kop4_fontstyle,$object->rapport_kop4_fontsize);
		$object->Cell(40+$extraruimteOmschrijving,4, vertaalTekst("Index-vergelijking",$object->rapport_taal), 0,0, "L");

		
		//$object->SetTextColor($object->rapport_fonds_fontcolor['r'],$object->rapport_fonds_fontcolor['g'],$object->rapport_fonds_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		if($object->rapport_perfIndexJanuari == true)
			$object->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$object->Cell(23,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$object->Cell(23,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");
    $object->Cell(23,4, vertaalTekst("Perf in %",$object->rapport_taal), $border,$perfVal, "R");
	    
$object->SetFont($object->rapport_font,$object->rapport_fontstyle,$object->rapport_fontsize);
		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($object->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
			  }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  if($object->rapport_perfIndexJanuari == true)
		  {
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['Beursindex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf['Omschrijving']." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
				$object->SetX($startX);
      $object->Cell($lmarge,4, '', $border,0, "L");
      $object->Cell(40+$extraruimteOmschrijving,4, $perf['Omschrijving'], $border,0, "L");
		  if($object->rapport_perfIndexJanuari == true)
		     $object->Cell(23,4, $object->formatGetal($koers0['Koers'],2), $border,0, "R");
			$object->Cell(23,4, $object->formatGetal($koers1['Koers'],2), $border,0, "R");
			$object->Cell(23,4, $object->formatGetal($koers2['Koers'],2), $border,0, "R");
		  $object->Cell(23,4, $object->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($object->rapport_printAEXVergelijkingEur == 1)
		    $object->Cell(26,4, $object->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($object->rapport_perfIndexJanuari == true)
		    $object->Cell(26,4, $object->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $object->rapport_portefeuille."' ";
		$DB->SQL($query2);
		$DB->Query();

		while($perf = $DB->nextRecord())
		{

		  if($perf['Valuta'] != 'EUR')
		  {

		    if($object->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
		    }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStop = $DB2->LookupRecord();

		  }
		  else
		  {
		    $valutaKoersJan['Koers'] = 1;
		    $valutaKoersStart['Koers'] = 1;
		    $valutaKoersStop['Koers'] = 1;
		  }

		  	if($object->rapport_perfIndexJanuari == true)
		    {
		  	  $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			    $DB2->SQL($q);
			    $DB2->Query();
			    $koers0 = $DB2->LookupRecord();
		    }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf['SpecifiekeIndex']."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf['Omschrijving']." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";

      $object->Cell($lmarge,4, '', $border,0, "L");
			$object->Cell(40+$extraruimteOmschrijving,4, $perf['Omschrijving'], 0,0, "L");
			if($object->rapport_perfIndexJanuari == true)
		     $object->Cell(23,4, $object->formatGetal($koers0['Koers'],2), $border,0, "R");
			$object->Cell(23,4, $object->formatGetal($koers1['Koers'],2), $border,0, "R");
			$object->Cell(23,4, $object->formatGetal($koers2['Koers'],2), $border,0, "R");
		  $object->Cell(23,4, $object->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($object->rapport_printAEXVergelijkingEur == 1)
		    $object->Cell(23,4, $object->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($object->rapport_perfIndexJanuari == true)
		    $object->Cell(23,4, $object->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
	}
}

if(!function_exists('printValutaPerformanceOverzicht'))
{
  function printValutaPerformanceOverzicht($object,$portefeuille, $rapportageDatum, $rapportageDatumVanaf,$omkeren=false)
  {
  	global $__appvar;
		$object->ln();

	 $metJanuari = $object->rapport_valutaPerformanceJanuari;

	 if($metJanuari == true)
	 {
	   $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	   $rapJaar = date('Y',$julRapDatumVanaf);
	   $dagMaand = date('d-m',$julRapDatumVanaf);
	   $januariDatum = $rapJaar.'-01-01';
	   if($dagMaand =='01-01')
       $metJanuari = false;
	 }

	 if($object->printValutaPerformanceOverzichtProcentTeken)
	   $teken = '%';
   else
     $teken = '';

		$extraruimteOmschrijving=19;

		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta,  TijdelijkeRapportage.rapportageDatum".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ". //OR TijdelijkeRapportage.rapportageDatum = '".$rapportageDatumVanaf."' )
		" TijdelijkeRapportage.valuta <> '".$object->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc, TijdelijkeRapportage.rapportageDatum";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$valuta['val']][$valuta['rapportageDatum']]['omschrijving'] = $valuta['ValutaOmschrijving'];
				$valutas[$valuta['val']][$valuta['rapportageDatum']]['koers'] = $valuta['actueleValuta'] / $object->ValutaKoersEind;
			}

			$valutaKeys = array_keys($valutas);
      foreach ($valutaKeys as $valuta)
      {
       $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
               FROM Valutas ,Valutakoersen
               WHERE Valutas.valuta = Valutakoersen.valuta AND
               Valutakoersen.datum <= date '".$rapportageDatumVanaf."' AND
               Valutas.valuta = '".$valuta."'
               ORDER BY Valutakoersen.datum desc LIMIT 1";
       $DB->SQL($query);
       $DB->Query();
       $valutawaarden = $DB->NextRecord();

       $valutas[$valuta][$rapportageDatumVanaf]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
			 $valutas[$valuta][$rapportageDatumVanaf]['koers'] = $valutawaarden['Koers'] / $object->ValutaKoersBegin;

			 if($metJanuari == true)
			 {
			   $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
                 FROM Valutas ,Valutakoersen
                 WHERE Valutas.valuta = Valutakoersen.valuta AND
                 Valutakoersen.datum <= date '$januariDatum' AND
                 Valutas.valuta = '".$valuta."'
                 ORDER BY Valutakoersen.datum desc LIMIT 1";
         $DB->SQL($query);
         $DB->Query();
         $valutawaarden = $DB->NextRecord();

         $valutas[$valuta][$januariDatum]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
			   $valutas[$valuta][$januariDatum]['koers'] = $valutawaarden['Koers'] / $object->ValutaKoersStart;
			   $extraBreedte = 50;
			 }
      }
	//listarray($valutas);
		$kop = "Valuta";

		$regels = count($valutas);
		$hoogte = ($regels * 4) + 8;
		if(($object->GetY() + $hoogte) > $object->pagebreak)
		{
			$object->AddPage();
			$object->ln();
		}

		$extraBreedte+=$extraruimteOmschrijving;

		$object->ln();
		$object->SetFillColor($object->rapport_kop_bgcolor['r'],$object->rapport_kop_bgcolor['g'],$object->rapport_kop_bgcolor['b']);
		$object->Rect($object->marge,$object->getY(),110+$extraBreedte,$hoogte,'F');
		$object->SetFillColor(0);
		$object->Rect($object->marge,$object->getY(),110+$extraBreedte,$hoogte);
		$object->SetX($object->marge);

		// kopfontcolor
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		$object->SetFont($object->rapport_kop4_font,$object->rapport_kop4_fontstyle,$object->rapport_kop4_fontsize);
		$object->Cell(40+$extraruimteOmschrijving,4, vertaalTekst($kop,$object->rapport_taal), 0,0, "L");

		if($metJanuari == true)
			$object->Cell(23,4, date("d-m-Y",db2jul($januariDatum)), 0,0, "R");
		$object->Cell(23,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
		$object->Cell(23,4, date("d-m-Y",db2jul($rapportageDatum)), 0,0, "R");
		if($metJanuari == true)
		{
		  $object->Cell(23,4, vertaalTekst("Performance",$object->rapport_taal), 0,0, "R");
			$object->Cell(23,4, vertaalTekst("Jaar Perf.",$object->rapport_taal), 0,1, "R");
		}
		else
			$object->Cell(23,4, vertaalTekst("Perf in %",$object->rapport_taal), 0,1, "R");

		$object->SetFont($object->rapport_font,$object->rapport_fontstyle,$object->rapport_fontsize);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);


		while (list($key, $data) = each($valutas))
		{
			$performance = ($data[$rapportageDatum]['koers'] - $data[$rapportageDatumVanaf]['koers']) / ($data[$rapportageDatumVanaf]['koers']/100 );
//echo 		"	$performance = (".$data[$rapportageDatum]['koers']." - ".$data[$rapportageDatumVanaf]['koers'].") / (".$data[$rapportageDatumVanaf]['koers']."/100 );";
			$object->Cell(40+$extraruimteOmschrijving,4, vertaalTekst($data[$rapportageDatumVanaf]['omschrijving'],$object->rapport_taal), 0,0, "L");
			if($metJanuari == true)
			{
			  if($omkeren==true)
			    $object->Cell(23,4, $object->formatGetal(1/$data[$januariDatum]['koers'],4), 0,0, "R");
			  else
			  	$object->Cell(23,4, $object->formatGetal($data[$januariDatum]['koers'],4), 0,0, "R");
			}
			if($omkeren==true)
			  $object->Cell(23,4, $object->formatGetal(1/$data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			else
			  $object->Cell(23,4, $object->formatGetal($data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			if($omkeren==true)
			  $object->Cell(23,4, $object->formatGetal(1/$data[$rapportageDatum]['koers'],4), 0,0, "R");
			else
			  $object->Cell(23,4, $object->formatGetal($data[$rapportageDatum]['koers'],4), 0,0, "R");
			if($metJanuari == true)
			{
			  $object->Cell(23,4, $object->formatGetal($performance,2).$teken, 0,0, "R");
			  $performanceJaar = ($data[$rapportageDatum]['koers'] - $data[$januariDatum]['koers']) / ($data[$januariDatum]['koers']/100 );
			  $object->Cell(23,4, $object->formatGetal($performanceJaar,2).$teken, 0,1, "R");
			}
			else
			  $object->Cell(23,4, $object->formatGetal($performance,2).$teken, 0,1, "R");
		}
		$object->ln();
		$object->ln();
		}
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


function printRendement_L39($object,$portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
{
	global $__appvar;
	// vergelijk met begin Periode rapport.
	$extraruimteOmschrijving=19;

	$DB= new DB();
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
		"FROM TijdelijkeRapportage WHERE ".
		" rapportageDatum ='".$rapportageDatumVanaf."' AND ".
		" portefeuille = '".$portefeuille."' ".
		$__appvar['TijdelijkeRapportageMaakUniek'];

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$vergelijkWaarde = $DB->nextRecord();
	$vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($object->rapportageValuta,$rapportageDatumVanaf);

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
		"FROM TijdelijkeRapportage WHERE ".
		" rapportageDatum ='".$rapportageDatum."' AND ".
		" portefeuille = '".$portefeuille."' ".
		$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$actueleWaardePortefeuille = $DB->nextRecord();
	$actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $object->ValutaKoersEind;

	$resultaat = ($actueleWaardePortefeuille -
		$vergelijkWaarde -
		getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta) +
		getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta)
	);

	$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $object->portefeuilledata['PerformanceBerekening'],$object->rapportageValuta);

	$object->ln(2);

	if($kort)
		$min = 8;

	if(($object->GetY() + 22 - $min) >= $object->pagebreak) {
		$object->AddPage();
		$object->ln();
	}

	$object->SetFillColor($object->rapport_kop_bgcolor['r'],$object->rapport_kop_bgcolor['g'],$object->rapport_kop_bgcolor['b']);
	//$object->SetX($object->marge + $object->widthB[0]);
	$object->Rect($object->marge,$object->getY(),110+$extraruimteOmschrijving,(16-$min),'F');
	$object->SetFillColor(0);
	$object->Rect($object->marge,$object->getY(),110+$extraruimteOmschrijving,(16-$min));
	$object->ln(2);
	//$object->SetX($object->marge);
	$object->SetX($object->marge);

	// kopfontcolor
	if(!$kort)
	{
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		if ($object->rapport_resultaatText)
			$object->Cell(80+$extraruimteOmschrijving,4, vertaalTekst($object->rapport_resultaatText,$object->rapport_taal), 0,0, "L");
		else
			$object->Cell(80+$extraruimteOmschrijving,4, vertaalTekst("Resultaat over verslagperiode",$object->rapport_taal), 0,0, "L");
		$object->Cell(30,4, $object->formatGetal($resultaat,2), 0,1, "R");
		$object->ln();
	}
	$object->SetX($object->marge);
	if ($object->rapport_rendementText)
		$object->Cell(80+$extraruimteOmschrijving,4, vertaalTekst($object->rapport_rendementText,$object->rapport_taal), 0,0, "L");
	else
		$object->Cell(80+$extraruimteOmschrijving,4, vertaalTekst("Rendement lopende kalenderjaar",$object->rapport_taal), 0,0, "L");
	$object->Cell(30,4, $object->formatGetal($performance,2)."%", 0,1, "R");
	$object->ln(2);
}

function VOLK_VHO_voet($object)
{
	return '';
  $object->SetTextColor($object->rapport_logo_fontcolor['r'],$object->rapport_logo_fontcolor['g'],$object->rapport_logo_fontcolor['b']);
  $object->SetFont($object->rapport_font,$object->rapport_fontstyle,$object->rapport_fontsize);
  $object->ln();
  $object->MultiCell(297-$object->marge*2,4,"Het door u gekozen portefeuilleprofiel is ".$object->portefeuilledata['Risicoklasse'].". Er van uitgaande dat uw voorkeuren, doelstellingen en persoonlijke situatie in de afgelopen periode niet zijn veranderd, voldoen uw beleggingen nog steeds aan uw profiel.",0, "L");
  $object->ln();
  $object->MultiCell(297-$object->marge*2,4,"Het beleggingsbeleid van Capitael hanteert stringente normen bij de selectie van haar beleggingen op het gebeid van sociaal maatschappelijke onderwerpen zoals o.a. corruptie, milieu, gezondheid, wapenhandel en kinderarbeid. Capitael houdt zoveel mogelijk rekening met de ondersteuning van positieve trends ten aanzien van een betere wereld. Wij hanteren hiertoe de regelementen van de ‘’Council of Ethics of the Norwegian Pension Fund’’. https://etikkradet.no/en/",0, "L");

}

?>
