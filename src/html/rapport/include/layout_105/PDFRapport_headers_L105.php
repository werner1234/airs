<?php

function Header_basis_L105($object)
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
		
		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast  && $pdfObject->rapport_layout != 16)
  		$pdfObject->customPageNo = 0;
      $pdfObject->rapportNewPage = $pdfObject->page;
    }
    else 
    {  
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
      
  	if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
  	  	$pdfObject->rapportNewPage = $pdfObject->page;
     
		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		//$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_default_fontcolor['r'],$pdfObject->rapport_default_fontcolor['g'],$pdfObject->rapport_default_fontcolor['b']);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;  
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

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 127;
		}
  
  if(is_file($pdfObject->rapport_logo))
  {
    $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->w / 2 - $pdfObject->logoXsizeHeader / 2, 3, $pdfObject->logoXsizeHeader);
  }
		
    if(strlen($pdfObject->rapport_logo_beeld)>0)
    {
        //
      $w=15;
       $pdfObject->memImage($pdfObject->rapport_logo_beeld,$pdfObject->w/2-$w/2,$pdfObject->h-10,$w);
       
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
    
		$pdfObject->MultiCell(120,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD")
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);
 
		$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(4);
    $pdfObject->SetX(0);
		$pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+13);
    $pdfObject->headerStart=$pdfObject->GetY()+15;

    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    }
}



function HeaderHSE_L105($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

    $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
    $eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5]+$pdfObject->widthB[6];
    $actueel 			= $eindhuidige + $pdfObject->widthB[7];
    $eindactueel 	= array_sum($pdfObject->widthB);

  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  // lijntjes onder beginwaarde in het lopende jaar
  
  $tmpY = $pdfObject->GetY();
  $pdfObject->SetX($pdfObject->marge+$huidige+5);
  $pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
  $pdfObject->SetY($tmpY);
  $pdfObject->SetX($pdfObject->marge+$actueel);
  
  $pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
  
  
  $pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
  $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
  
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
    $pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                 vertaalTekst("Aantal",$pdfObject->rapport_taal),
                 vertaalTekst("Valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 "",
                 vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 ($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""));
  
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
	function HeaderVKM_L105($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	  function HeaderFRONT_L105($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }
	  function HeaderSCENARIO_L105($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }

function HeaderKERNZ_L105($object)
{
  $pdfObject = &$object;
  //$pdfObject->headerSCENARIO();
  
}

    function HeaderRISK_L105($object)
    {
	    $pdfObject = &$object;
			$pdfObject->Ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$pdfObject->Ln(10);
	  }

   function HeaderVAR_L105($object)
   {
    $pdfObject = &$object;
    $pdfObject->Ln();
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->Ln(10);
   }

    function HeaderGRAFIEK_L105($object)
    {
	    $pdfObject = &$object;
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 6 , 'F');
      $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    }

    function HeaderEND_L105($object)
    {
     	$pdfObject = &$object;
      $pdfObject->Ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$pdfObject->Ln(10);

		}

function HeaderDOORKIJK_L105($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->Ln(10);
  
}

function HeaderDOORKIJKVR_L105($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->Ln(10);
  
}

    function HeaderPERFD_L105($object)
    {
      $pdfObject = &$object;
      $pdfObject->Ln();
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 8 , 'F');
      $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
      $pdfObject->Ln(8);
    }
  
    
    function HeaderTRANS_L105($object)
	  {
	    $pdfObject = &$object;
    	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  		$pdfObject->SetX(100);
			$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
			$pdfObject->ln();
	    
  		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

			// afdrukken header groups
	  	$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		  $inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];

		  $verkoop			= $inkoopEind;
		  $verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		  $resultaat			= $verkoopEind;
		  $resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

	    $y=$pdfObject->GetY();
			$pdfObject->SetX($inkoop);
			$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();
   // $pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
	//	$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
   // $pdfObject->SetDrawColor(0,0,0);
		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

      $pdfObject->SetXY($pdfObject->marge,$y);
			$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/\nVerKoop",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)));
      $pdfObject->ln(1);
	  }
    
    function HeaderMUT_L105($object)
	  {
	    $pdfObject = &$object;
   		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  	  $pdfObject->SetX(100);
	  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		  $pdfObject->ln();
		  // achtergrond kleur
		  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		  $pdfObject->SetWidths($pdfObject->widthB);
		  $pdfObject->SetAligns($pdfObject->alignB);
		  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
      $pdfObject->Ln(2);
		  $pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 "",
										 "",
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
		  $pdfObject->ln(2);
		  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    }
    
	  function HeaderAFM_L105($object)
	  {
	    $pdfObject = &$object;
	  
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		  $pdfObject->SetX($pdfObject->marge+$lijn1+5);
		  $pdfObject->MultiCell(90,4, '', 0, "C");
$pdfObject->ln(2);
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("AFM categorie",$pdfObject->rapport_taal),'Valuta',
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal),
                      vertaalTekst("in %",$pdfObject->rapport_taal)));
$pdfObject->ln(2);
	

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	  }
	  function HeaderINHOUD_L105($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }	  
 	  function HeaderPERF_L105($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  		$pdfObject->ln(2);
	 	  $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
    	$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  		$pdfObject->ln(2);

$pdfObject->ln();
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	  }
    
    function HeaderINDEX_L105($object)
	  {
	    $pdfObject = &$object;
	  }
    function HeaderOIB_L105($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }
  function HeaderATT_L105($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,25);
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
   function HeaderPERFG_L105($object)
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

  function HeaderVOLK_L105($object)
	{
    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

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


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige-5);
    $y = $pdfObject->getY();
    $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
    $pdfObject->SetDrawColor(0,0,0);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->setXY($pdfObject->marge,$y);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
      $teken=$pdfObject->rapportageValuta;
    
		$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                    ));
	

		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
	}
  
function HeaderVHO_L105($object)
	{
    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

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

//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
      $teken=$pdfObject->rapportageValuta;
		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige-5);
    $y = $pdfObject->getY();
    $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,1, "C");
$pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
	//	$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
$pdfObject->SetDrawColor(0,0,0);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->SetXY($pdfObject->marge,$y);

			$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                    ));
	

		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
    
   // $pdfObject->ln(20);
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


function printRendement_L105($object,$portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
{
  $pdfObject = &$object;
  global $__appvar;
  // vergelijk met begin Periode rapport.
  
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
  $vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($pdfObject->rapportageValuta,$rapportageDatumVanaf);
  
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
    "FROM TijdelijkeRapportage WHERE ".
    " rapportageDatum ='".$rapportageDatum."' AND ".
    " portefeuille = '".$portefeuille."' ".
    $__appvar['TijdelijkeRapportageMaakUniek'];
  debugSpecial($query,__FILE__,__LINE__);
  $DB->SQL($query);
  $DB->Query();
  $actueleWaardePortefeuille = $DB->nextRecord();
  $actueleWaardePortefeuille = $actueleWaardePortefeuille['totaal']  / $pdfObject->ValutaKoersEind;
  
  $resultaat = ($actueleWaardePortefeuille -
    $vergelijkWaarde -
    getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$pdfObject->rapportageValuta) +
    getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$pdfObject->rapportageValuta)
  );
  
  $performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $pdfObject->portefeuilledata['PerformanceBerekening'],$pdfObject->rapportageValuta);
  
  $pdfObject->ln(2);
  
  if($kort)
    $min = 8;
  
  if(($pdfObject->GetY() + 22 - $min) >= $pdfObject->pagebreak) {
    $pdfObject->AddPage();
    $pdfObject->ln();
  }
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  //$pdfObject->SetX($pdfObject->marge + $pdfObject->widthB[0]);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110,(16-$min),'F');
  $pdfObject->SetFillColor(0);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110,(16-$min));
  $pdfObject->ln(2);
  //$pdfObject->SetX($pdfObject->marge);
  $pdfObject->SetX($pdfObject->marge);
  
  // kopfontcolor
  if(!$kort)
  {
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    if ($pdfObject->rapport_resultaatText)
      $pdfObject->Cell(80,4, vertaalTekst($pdfObject->rapport_resultaatText,$pdfObject->rapport_taal), 0,0, "L");
    else
      $pdfObject->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$pdfObject->rapport_taal), 0,0, "L");
    $pdfObject->Cell(30,4, $pdfObject->formatGetal($resultaat,2), 0,1, "R");
    $pdfObject->ln();
  }
  $pdfObject->SetX($pdfObject->marge);
  if ($pdfObject->rapport_rendementText)
    $pdfObject->Cell(80,4, vertaalTekst($pdfObject->rapport_rendementText,$pdfObject->rapport_taal), 0,0, "L");
  else
    $pdfObject->Cell(80,4, vertaalTekst("Rendement over verslagperiode",$pdfObject->rapport_taal), 0,0, "L");
  $pdfObject->Cell(30,4, $pdfObject->formatGetal($performance,2)."%", 0,1, "R");
  $pdfObject->ln(2);
}
?>