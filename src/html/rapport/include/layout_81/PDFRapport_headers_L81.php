<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/21 16:32:39 $
 		File Versie					: $Revision: 1.8 $
 		
 		$Log: PDFRapport_headers_L81.php,v $
 		Revision 1.8  2019/09/21 16:32:39  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2019/09/18 14:54:35  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/09/15 09:00:00  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/07/06 15:41:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/03/06 19:21:33  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/02/09 19:02:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/01/06 12:44:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/12/27 15:11:17  rvv
 		*** empty log message ***
 		


*/
function Header_basis_L81($object)
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
			$factor=0.050;
			$xSize=494*$factor;
			$ySize=273*$factor;
      $logopos=(297/2)-($xSize/2);
	    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, $xSize, $ySize);
//	    $voetLogo=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAOcAAACxCAMAAAAI/Vr4AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAFxUExURVliiWtzlcXI1u3u8r3B0LW5yrq+zsrM2ePl6+Xn7eDi6S05aWdvk/b3+XF5mpyhuIWMqKmtwfz8/aSpvqKnvc7R3LK2yGBpjjI9bFBZgtze5tTW4CcyZU1XgFVehujp7nV8nNvd5Sk1ZkFMeNHU3t7g6JOZsjpFc9DS3XqBoH2EosHE0unr8CQwY+/w80ROesPG1NbZ4is2aLC1x0ZQe6eswEhSfLi8zZedtV5mjI+Vr252mGRskFxli42TrkpUfouRrDdCcJietoeOqVJcgzxHdDhDcT5IdZ6kuqyxxDA7a0xVf5GXsNbY4fHy9cjL2IOKpvr6+/39/ltkivX1+PLz9vP09vf4+efo7ldgh/Dx9YqQq3yDofT095abtNja4/v7/K6yxTM+btnb5Hd+noCHpGJrj7y/z3h/nquww6+zxq2yxWVukZqgt0BLd5+ku+vs8V9njJCWsD9KdnZ9nYKJppGXsX+GpMvO2iMvYv///wPBiJkAAAB7dFJOU///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////AJPDqOoAAAgdSURBVHja7N37Q9JqGAfw4V0umtlR8ExCOYWpSBKRl9PF4wUYKOClLCuLk93LPJ08Pvvrz4AB7/uyjW26ket5fpH1hW2f2OXd9m5wop0Vg0aN2jplztapQTxbq2jKyc5s/aWATnSiE53otKymSKfH7VTnlADp+kABIm5nOiXmAjE4bSuUaxfTZijXNqa9UM5+5tx+0X4oZzNzakCQjlUC6Sc2Qzlbmb4QgGftJAywfmYvlLOTWdyByMfyQju2DPxtW6Fazt2ti6mO2kK7CoFv8rivgf9bDTp/QdPZKppyrsIFVVRmunn/bH3sR/BfbWM0eVET8nQYd24/ByF77vJUpi9vaXPwsjH+LY9QrEHLVTr/xLLAvzPqLA5AZEwx+b1QqW5dq0UW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');
//	    $pdfObject->MemImage($voetLogo,(297/2)-9,190,18);
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

	function HeaderVKM_L81($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L81($object)
{
  $pdfObject = &$object;
}

function HeaderCASHY_L81($object)
{
  $pdfObject = &$object;
}

function HeaderCASH_L81($object)
{
  $pdfObject = &$object;
}

function HeaderOIS_L81($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
}

	  function HeaderFRONT_L81($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }


function HeaderOIH_L81($object)
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
  
  $resultaat			= $verkoopEind ;
  $resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
  
  $y=$pdfObject->GetY();
  $pdfObject->SetX($inkoop);
  $pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetX($verkoop);
  $pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
 // $pdfObject->SetX($resultaat);
 // $pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->ln();
  $pdfObject->SetDrawColor(255,255,255);
  $pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
  $pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
  $pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
  $pdfObject->SetDrawColor(0,0,0);
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
                    '',//	 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    '',//			 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "".vertaalTekst("Kostprijs lopend jaar",$pdfObject->rapport_taal),
                    "",
                    vertaalTekst("Resultaat",$pdfObject->rapport_taal).' '.vertaalTekst("lopend jaar",$pdfObject->rapport_taal),
                    "\n".$procentTotaal));
  $pdfObject->ln(1);
}

function HeaderVAR_L81($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths(array(65,13,15,17,16,21,21,20,25, 5,  15,15,18,12));
  
  $positie=array();
  foreach ($pdfObject->widths as $id=>$waarde)
  {
    if($id < 1)
      $positie['fondsStart'] +=$waarde;
    if($id < 5)
      $positie['fondsEind'] +=$waarde;
    if($id < 8)
    {
      $positie['waardeStart'] +=$waarde;
      if($id==7)
      {
        $positie['midden'] = $positie['waardeStart'] ;
        $positie['midden'] -=$waarde/2;
      }
    }
    if($id < 11)
      $positie['waardeEind'] +=$waarde;
    
  }
  foreach ($positie as $key=>$value)
    $positie[$key]+=$pdfObject->marge;
  
  $y=$pdfObject->GetY()+5;
  $pdfObject->pageTop=array($positie['midden'],$y+1);
  
  $pdfObject->SetAligns(array('L','R','L','L','R','R','R','R','R'  ,'R','R','R','R','R'));
  //$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
  //$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
  
  $pdfObject->row(array(
                    "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
                    vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("%",$pdfObject->rapport_taal),
                    "".vertaalTekst("Coupon-\ndatum",$pdfObject->rapport_taal),
                    "".vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "".vertaalTekst("Opgelopen\nrente",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),'',
                    vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
                    vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
                    vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
                    vertaalTekst("%",$pdfObject->rapport_taal)."  \n".vertaalTekst("port.",$pdfObject->rapport_taal)));
  
  
  unset($pdfObject->CellBorders);//"Modified\nduration",
  
}

function HeaderTRANSFEE_L81($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$widthBackup=$pdfObject->widths;
	$dataWidth=array(28,50,20,40,40,20,30,20,20,20,20,20);
	$pdfObject->SetWidths($dataWidth);
	$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R'));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	$lastColors=$pdfObject->CellFontColor;
	unset($pdfObject->CellFontColor);
	unset($pdfObject->CellBorders);
	if(!isset($pdfObject->vmkHeaderOnderdrukken))
	{
		$pdfObject->Row(array(vertaalTekst("Risico/categorie", $pdfObject->rapport_taal),
											"" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
											"" . date('d-m-Y', $pdfObject->rapport_datum),
											vertaalTekst("Prognose dl kosten %", $pdfObject->rapport_taal),
											vertaalTekst("Prognose dl kosten absoluut", $pdfObject->rapport_taal),
											"" . vertaalTekst("Weging", $pdfObject->rapport_taal),
											"" . vertaalTekst("VKM Bijdrage", $pdfObject->rapport_taal)));
		unset($pdfObject->vmkHeaderOnderdrukken);
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	}
	$pdfObject->widths=$widthBackup;
	$pdfObject->CellFontColor=$lastColors;
	$pdfObject->SetLineWidth(0.1);
}
	  function HeaderSCENARIO_L81($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }

    function HeaderRISK_L81($object)
    {
	    $pdfObject = &$object;
			$pdfObject->Ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$pdfObject->Ln(10);
	  }

    function HeaderGRAFIEK_L81($object)
    {
	    $pdfObject = &$object;
    }

    function HeaderEND_L81($object)
    {
     	$pdfObject = &$object;
			$pdfObject->Ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$pdfObject->Ln(10);

		}

    function HeaderPERFD_L81($object)
    {
      $pdfObject = &$object;
      HeaderPERF_L81($object);
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
    }
  
    
    function HeaderTRANS_L81($object)
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
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();
    $pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
    $pdfObject->SetDrawColor(0,0,0);
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
									'',//	 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
							    '',//			 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Kostprijs ",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Historisch",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Huidig",$pdfObject->rapport_taal),
										 "\n".$procentTotaal));
      $pdfObject->ln(1);
	  }
    
    function HeaderMUT_L81($object)
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
    
	  function HeaderAFM_L81($object)
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
	  function HeaderINHOUD_L81($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }	  
 	  function HeaderPERF_L81($object)
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
    
    function HeaderINDEX_L81($object)
	  {
	    $pdfObject = &$object;
	  }
    function HeaderOIB_L81($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }
  function HeaderATT_L81($object)
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

   function HeaderPERFG_L81($object)
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

  function HeaderVOLK_L81($object)
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
  
function HeaderVHO_L81($object)
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
?>