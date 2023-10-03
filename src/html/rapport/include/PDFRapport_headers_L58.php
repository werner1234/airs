<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/25 17:15:30 $
 		File Versie					: $Revision: 1.13 $
 		
 		$Log: PDFRapport_headers_L58.php,v $
 		Revision 1.13  2020/04/25 17:15:30  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/03/01 17:17:08  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/04/23 06:16:11  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/04/22 10:32:05  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/01/22 10:20:55  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/01/20 12:28:53  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/20 12:22:52  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/20 16:32:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/23 14:13:22  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/11/08 18:37:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/10/04 15:23:36  rvv
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
function Header_basis_L58($object)
{
    $pdfObject = &$object;
//echo "RapType:".$pdfObject->rapport_type."<br>\n";
    $pdfObject->last_rapport_type=$pdfObject->rapport_type;
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
      
   	$pdfObject->rapportNewPage = $pdfObject->page; 

    }
    else 
    {  
    	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	  	$pdfObject->customPageNo = 0;
        
       
  	  if($pdfObject->lastPortefeuille != $pdfObject->rapport_portefeuille && !empty($pdfObject->lastPortefeuille))
      {
  	  	$pdfObject->rapportNewPage = $pdfObject->page;
        //echo $pdfObject->lastPortefeuille." != ".$pdfObject->rapport_portefeuille." ".$pdfObject->page."<br>\n";
      }
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

    $logoWidth=17;
		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 210/2-$logoWidth/2;
		}
		else
		{
			$logopos = 297/2-$logoWidth/2;
		}


		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{

			  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $logoWidth);	
        $pdfObject->Line($pdfObject->marge,27,297-$pdfObject->marge,27);
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
 //vertaalTekst("".\n\n Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."
		$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(17);
    $pdfObject->SetX(0);
		$pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+22);
   }
   

   $pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
}
	  
	  function HeaderFRONT_L58($object)
	  {
	   $pdfObject = &$object;
	  }

function HeaderINDEX_L58($object)
{
  $pdfObject = &$object;
}

function HeaderVKM_L58($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
 
function HeaderVHO_L58($object)
{
    $pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6] ;
			$eindactueel 	= $actueel  + $pdfObject->widthB[7]+ $pdfObject->widthB[8];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[9] ;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13];
	

		// achtergrond kleur
	//	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	//	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
	//	$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);

			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell(80,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();


			$pdfObject->row(array(vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	


		$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
			$pdfObject->row(array("",vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
    
		$pdfObject->ln();
  }
  
  
  function HeaderATT_L58($object)
	{
    $pdfObject = &$object;
    $colW=280/12;
    $pdfObject->widthA = array($colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW);//,23
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

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
    $pdfObject->ln();
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopen\nrente",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
                          "Rendement\n ",
                          "Rendement\n(Cumulatief)"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}
  
  function HeaderTRANS_L58($object)
	{
    $pdfObject = &$object;
    $pdfObject->ln();
    $pdfObject->HeaderTRANS();
  }
  function HeaderMUT_L58($object)
	{
    $pdfObject = &$object;
    $pdfObject->ln();
    $pdfObject->HeaderMUT();
  }
  
 	function HeaderOIB_L58($object)
	{
  	  $pdfObject = &$object;

		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		// lijntjes onder beginwaarde in het lopende jaar

		  $pdfObject->SetX($pdfObject->marge);

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
  
  function HeaderPERF_L58($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(2);
	  $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		$pdfObject->ln(2);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
   
    //$pdfObject->Ln(10);
		//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }
  
  function HeaderRISK_L58($object)
  {
    $pdfObject = &$object;
  }


if(!function_exists('DonutChart'))
{
	function DonutChart($object, $w, $h, $data, $colors = null)
	{

		$object->pdf->SetFont($object->pdf->rapport_font, '', $object->pdf->rapport_fontsize);


		$XPage = $object->pdf->GetX();
		$YPage = $object->pdf->GetY();
		$margin = 2;
		$hLegend = 2;
		$radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;


		//Sectors
		$object->pdf->SetLineWidth(0.1);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		$totaal = array_sum($data);
		$angleMarge = 0.1;
		foreach ($data as $val)
		{
			$angle = (($val * 360) / $totaal);


			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$angleEndMarge = $angleEnd - $angleMarge;
				$angleStartMarge = $angleStart + $angleMarge;

				if (round($angleEndMarge) == 360)
				{
					$angleEndMarge = 360;
				}

				$object->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);

				//echo ($angleEndMarge)-($angleStartMarge)." $val -> $angleStartMarge $angleEndMarge <br>\n";ob_flush();

				if (($angleEndMarge) - ($angleStartMarge) > 1.0)
				{
					$object->pdf->Sector($XDiag, $YDiag, $radius, $angleStartMarge, $angleEndMarge, 'F');
				}
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEndMarge != 360)
		{
			//   $object->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360,'F');
		}
		$angleStart = 0;
		$angleEnd = 0;

		foreach ($data as $val)
		{
			$angle = (($val * 360) / $totaal);
			$object->pdf->SetLineStyle(array('cap' => 'round', 'width' => 0.75, 'color' => array(255, 255, 255)));
			if ($angle != 0 && $angle != 360)
			{
				$angleEnd = $angleStart + $angle;
				$lineAngle = ($angleEnd) / 180 * M_PI;
				$object->pdf->line($XDiag, $YDiag, $XDiag + (sin($lineAngle) * $radius), $YDiag - (cos($lineAngle) * $radius));
				$angleStart += $angle;
			}
			$i++;
		}
		$object->pdf->Circle($XDiag, $YDiag, $radius * 0.75, 0, 360, 'F', null, array(255, 255, 255));


		$object->pdf->SetLineWidth(0.2);
		//Legends
		$object->pdf->SetFont($object->pdf->rapport_font, '', $object->pdf->rapport_fontsize);

		$x1 = $XPage;
		$x2 = $x1 + $hLegend + $margin - 12;
		$y1 = $YDiag + ($radius) + $margin + 5;

		$object->pdf->SetXY($x2, $y1);
		$object->pdf->SetFont($object->pdf->rapport_font, 'B', $object->pdf->rapport_fontsize);
		$object->pdf->Cell(50, $hLegend, 'beleggingscategorie');
		$object->pdf->Cell(30, $hLegend, 'in %', 0, 0, 'R');
		$object->pdf->SetFont($object->pdf->rapport_font, '', $object->pdf->rapport_fontsize);
		$y1 += $hLegend + $margin;
		$n = 0;
		$totaal = 0;
		foreach ($data as $categorie => $percentage)
		{
			$object->pdf->SetFillColor($colors[$n][0], $colors[$n][1], $colors[$n][2]);
			$object->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
			$object->pdf->SetXY($x2, $y1);
			$object->pdf->Cell(50, $hLegend, $categorie);
			$object->pdf->Cell(30, $hLegend, number_format($percentage, 1, ',', '.') . '', 0, 0, 'R');
			$y1 += $hLegend + $margin;
			$n++;
			$totaal += $percentage;
		}
		$object->pdf->SetXY($x2, $y1);
		$object->pdf->SetFont($object->pdf->rapport_font, 'B', $object->pdf->rapport_fontsize);
		$object->pdf->Cell(50, $hLegend, 'Totaal');
		$object->pdf->Cell(30, $hLegend, $totaal, 0, 0, 'R');
		$object->pdf->SetFont($object->pdf->rapport_font, '', $object->pdf->rapport_fontsize);

		$object->pdf->SetLineStyle(array('color' => array(0, 0, 0)));
		/*
          for($i=0; $i<$object->pdf->NbVal; $i++) {

          }
          */

	}
}


if(!function_exists('getTypeGrafiekData'))
{
	function getTypeGrafiekData($object, $type, $extraWhere = '', $items = array())
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

		//if(!isset($object->pdf->rapportageDatumWaarde) || $extraWhere !='')
		//{
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
		// }
		//else
		//  $portTotaal=$object->pdf->rapportageDatumWaarde;

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
}
?>