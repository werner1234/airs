<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.8 $
 		
 		$Log: PDFRapport_headers_L19.php,v $
 		Revision 1.8  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/11/13 16:27:53  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/06/22 16:15:05  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/03/27 17:32:25  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/12/17 11:14:00  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2008/09/15 08:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2008/07/02 10:25:04  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/07/01 07:12:34  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***
 		
 	
*/
function Header_basis_L19($object)
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
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
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
      $pageWidth=210;
		}
		else
		{
			$logopos = 127;
      $pageWidth=297;
		}


		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{ 
       if(substr($pdfObject->rapport_logo,-4)=='.png')
			   $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 43, 15);	
       else
       {
         $factor=0.15;
		     $xSize=380*$factor;//$x=885*$factor;
		     $ySize=109*$factor;//$y=849*$factor;
         $logoX=$pageWidth/2-$xSize/2;
	  		 $pdfObject->Image($pdfObject->rapport_logo, $logoX, 2, $xSize, $ySize);
       }
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
 
		$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(10);
    $pdfObject->SetX(0);
		$pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+20);
}
}

	function HeaderVKM_L19($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	  function HeaderAFM_L19($object)
	  {
	   $pdfObject = &$object;

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);



			$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal)));
			  $pdfObject->SetWidths($pdfObject->widthB);
		  $pdfObject->SetAligns($pdfObject->alignB);	
      if($pdfObject->afmPage2==true)
      	$pdfObject->row(array(vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal),
                      vertaalTekst("in Tot%",$pdfObject->rapport_taal)));
      else                
				$pdfObject->row(array(vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
    }

   function HeaderVOLKD_L19($object)
   {
	   $pdfObject = &$object;
	  $pdfObject->SetWidths(array(63,22,18,25,30,30,30,23,20,20));		
	  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','L'));
	  $pdfObject->ln();
  }

	  function HeaderVOLK_L19($object)
	  {
	    $pdfObject = &$object;
	    $pdfObject->ln(1);
	    $y=$pdfObject->getY();
	    $widths=array(10,60,17,17,17,17,17,17,5,17,17,17,17,17,17);
	    
	    		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($widths), 16 , 'F');

	    
	    $pdfObject->SetXY($pdfObject->marge,$y+4);
	    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	    $pdfObject->cell(100,4,'Categorie');
	    $pdfObject->SetXY($pdfObject->marge,$y);

	 	 
	 	 $pdfObject->SetAligns(array('L','C','C','C'));
	 	 $pdfObject->SetWidths(array($widths[0]+$widths[1],
	 	                             $widths[2]+$widths[3]+$widths[4]+$widths[5]+$widths[6]+$widths[7],
	 	                             $widths[8],
	 	                             $widths[9]+$widths[10]+$widths[11]+$widths[12]+$widths[13]+$widths[14]));
	 	 $pdfObject->CellBorders = array('',"U",'',"U");
	 	 
	 	 
	 	  $pdfObject->Row(array("","Portefeuille",'',"Resultaat"));
	 	  $pdfObject->CellBorders = array();
	 	  $pdfObject->SetWidths($widths);
		  $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Row(array('',"\nFondsomschrijving","Aantal","Valuta","Koers\nin valuta","Waarde\nin valuta","Waarde\nin EUR","Weging",'',"Koers\nResult.\n%","Koers\nContrib.","Valuta\nResult.\n%","Valuta\nContrib.","Totaal\nResult.\n%","Totaal\nContrib.\n%"));
	    $pdfObject->line(8,$pdfObject->getY(),array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY());
	    $pdfObject->SetAligns(array('L','C','C','R','R','R','R','R','R','R'));

	  }
	
   function HeaderPERFD_L19($object)
   {
 	   $pdfObject = &$object;
   }

   function HeaderPERFG_L19($object)
   {
	   $pdfObject = &$object;
   }

if(!function_exists('BarDiagram'))
{
	function PieChart($object, $w, $h, $data, $format, $colors = null)
	{
		$pdfObject = &$object;


		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->legends = array();
		$pdfObject->wLegend = 0;
		$pdfObject->sum = array_sum($data);
		$pdfObject->NbVal = count($data);
		foreach ($data as $l => $val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';

			$p = number_format($val, 2, ",", ".") . "%";
			$legend = str_replace(array('%l', '%v', '%p'), array($l, $val, $p), $format);
			$pdfObject->legends[] = $legend;
			$pdfObject->wLegend = max($pdfObject->GetStringWidth($legend), $pdfObject->wLegend);
		}


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

?>