<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/19 17:00:47 $
 		File Versie					: $Revision: 1.7 $

 		$Log: PDFRapport_headers_L69.php,v $
 		Revision 1.7  2018/12/19 17:00:47  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/12/03 19:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/19 15:37:34  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/06/25 16:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/05/11 16:03:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/04/23 15:33:07  rvv
 		*** empty log message ***
 		
 

*/
function Header_basis_L69($object)
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	//if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	//	$pdfObject->customPageNo = 0;

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
  
    if(isset($pdfObject->__appvar['consolidatie']))
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
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
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
			$logopos = 130;
		}

   // if($pdfObject->rapport_type == 'OIH')
		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
			//  $pdfObject->Image($pdfObject->rapport_logo, $logopos -33, 5, 108, 15);
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
	    $pdfObject->SetX(100);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
      
    $pdfObject->headerStart = $pdfObject->getY()+16;
  }

}

	function HeaderVKM_L69($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
    function HeaderOIS_L69($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }

function HeaderVOLK_L69($object)
	{
    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize-1);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige+5);

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
										'',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
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
		$pdfObject->ln();

	}



function HeaderVOLKD_L69($object)
{
	$pdfObject = &$object;
	if($pdfObject->widthsDefault)
		$oldWidths=$pdfObject->widths;
	$pdfObject->ln(1);
	$pdfObject->SetXY($pdfObject->marge,$pdfObject->getY());
	$pdfObject->CellBorders = array();
	$tint=30;
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r']+$tint,$pdfObject->rapport_kop_bgcolor['g']+$tint,$pdfObject->rapport_kop_bgcolor['b']+$tint);

	if($pdfObject->rapport_deel == 'overzicht')
	{
		$pdfObject->fillCell = array(0,0,0,0,0,0,0,0,0,0,0,0);
		$pdfObject->SetWidths(array(60,22,25,25,28,25,25,24,24,24));
		$pdfObject->preFillColumn($pdfObject->getY()+3,195);
		$pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->setY($pdfObject->getY()+3);
		//$pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
		$pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
		$pdfObject->Row($pdfObject->rapport_header1);
		$pdfObject->fillCell = array();
		$pdfObject->CellBorders = array();
		$pdfObject->Row(array(''));
		$pdfObject->line(8,$pdfObject->getY()-4,array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY()-4);
		$pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
		if(is_array($oldWidths))
			$pdfObject->widths=$oldWidths;
	}
}

function HeaderVHO_L69($object)
	{
    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize-1);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige+5);

	    $pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
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
										'',//vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
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
		$pdfObject->ln();

	}
	
	  function HeaderOIH_L69($object)
	  {
	    $pdfObject = &$object;
	    if($pdfObject->widthsDefault)
	      $oldWidths=$pdfObject->widths;
	    $pdfObject->ln(1);
	    $pdfObject->SetXY($pdfObject->marge,$pdfObject->getY());
	    $pdfObject->CellBorders = array();
	    $tint=30;
	    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r']+$tint,$pdfObject->rapport_kop_bgcolor['g']+$tint,$pdfObject->rapport_kop_bgcolor['b']+$tint);

	    if($pdfObject->rapport_deel == 'overzicht')
	    {
	    $pdfObject->fillCell = array(0,0,0,0,0,0,0,0,0,0,0,0);
	 	  $pdfObject->SetWidths(array(60,22,25,25,25,25,25,25,25,25));
	 	  $pdfObject->preFillColumn($pdfObject->getY()+3,195);
	 	  $pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
	 	  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->setY($pdfObject->getY()+3);
      //$pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
      $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
      $pdfObject->Row($pdfObject->rapport_header1);
      $pdfObject->fillCell = array();
	    $pdfObject->CellBorders = array();
	    $pdfObject->Row(array(''));
	    $pdfObject->line(8,$pdfObject->getY()-4,array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY()-4);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
	    if(is_array($oldWidths))
	      $pdfObject->widths=$oldWidths;
	    }
	  }



function HeaderATT_L69($object)
{
	$pdfObject = &$object;
	$pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);


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
  
  function HeaderAFM_L69($object)
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


		  $pdfObject->SetX($pdfObject->marge+$lijn1+5);
		  $pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");

		  $pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);


	
				$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal),
											vertaalTekst("Categorie",$pdfObject->rapport_taal),
											vertaalTekst("in valuta",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));

	
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }

  function HeaderOIBS_L69($object)
  {
    $pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= array_sum($pdfObject->widthB);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln(-2);
		$pdfObject->row(array("%",vertaalTekst("Sectoren",$pdfObject->rapport_taal),
												vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("Koers",$pdfObject->rapport_taal),
												vertaalTekst("Valuta",$pdfObject->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("In %",$pdfObject->rapport_taal)));
		$pdfObject->ln(2);
	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }

  function HeaderGRAFIEK_L69($object)
  {
    $pdfObject = &$object;
  }
  
  function HeaderPERFG_L69($object)
  {
    $pdfObject = &$object;
    $pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 280, 8 , 'F');
	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+8,$pdfObject->marge + 280,$pdfObject->GetY()+8);
 
  }

  function HeaderHSE_L69($object)
  {
    $pdfObject = &$object;
		$pdfObject->ln();
		$dataWidth=array(28,60,21,21,21,21,22,22,22,22,22);
		$pdfObject->SetWidths($dataWidth);
		$pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		$lastColors=$pdfObject->CellFontColor;
		unset($pdfObject->CellFontColor);
		$pdfObject->Row(array(vertaalTekst("Risico\ncategorie",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
											"\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
											"\n".date('d-m-Y',$pdfObject->rapport_datum),
											"\n".vertaalTekst("Stortingen",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
											vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
											"".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
		$pdfObject->CellFontColor=$lastColors;
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
		$pdfObject->SetLineWidth(0.1);
  }

  function HeaderTRANS_L69($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();
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

		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan-/\nver\nkoop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in\n".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in\n".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal), 	                                      vertaalTekst("Resultaat lopende jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
     $pdfObject->ln(1);
  }

  function HeaderMUT_L69($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	  //$pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(2);
		$pdfObject->row(array('',
										 '',
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));
    $pdfObject->ln(-2);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

  }
  
    
  function printIndex($object)
  {
    global $__appvar;
    $rapportObject = &$object;
    $RapStartJaar = date("Y", db2jul($rapportObject->rapportageDatumVanaf));
	  if(db2jul($rapportObject->pdf->PortefeuilleStartdatum) > db2jul($rapportObject->rapportageDatumVanaf))
	    $rapportObject->tweedePerformanceStart = $rapportObject->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($rapportObject->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $rapportObject->tweedePerformanceStart = $rapportObject->pdf->PortefeuilleStartdatum;
	  else
	   $rapportObject->tweedePerformanceStart = "$RapStartJaar-01-01";

    $performance = performanceMeting($rapportObject->portefeuille, $rapportObject->rapportageDatumVanaf, $rapportObject->rapportageDatum, $rapportObject->pdf->portefeuilledata['PerformanceBerekening'],$rapportObject->pdf->rapportageValuta);


	    $extraBreedte=70-90;
	    $performanceJaar = performanceMeting($rapportObject->portefeuille, $rapportObject->tweedePerformanceStart, $rapportObject->rapportageDatum, $rapportObject->pdf->portefeuilledata['PerformanceBerekening'],$rapportObject->pdf->rapportageValuta);
	  
    $klein=false;
	  $DB=new DB();
	  $perioden=array('jan'=>$rapportObject->tweedePerformanceStart,'begin'=>$rapportObject->rapportageDatumVanaf,'eind'=>$rapportObject->rapportageDatum);

    $categorien[] = 'Totaal';
   // $index=new indexHerberekening();
    $DB = new DB();
	  foreach ($perioden as $periode=>$datum)
	  {


		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$datum' AND ".
						 " portefeuille = '".$rapportObject->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$dbwaarde = $DB->nextRecord();
		$totaalWaarde[$periode] = $dbwaarde['totaal'];

	    if(($klein == false) || ($klein == true && $periode !='jan'))
	    {
	   //   $rendamentWaarden = $index->getWaardenATT('2005-01-01' ,$datum ,$rapportObject->portefeuille,$categorien);
	    //  $rendamentWaarden = $index->getWaardenATT($rapportObject->tweedePerformanceStart ,$datum ,$rapportObject->portefeuille,$categorien);
     //   $portefeuilleIndex[$periode]=$rendamentWaarden[count($rendamentWaarden)-1]['index'];
	    }
	  }

	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds WHERE Vermogensbeheerder = '".$rapportObject->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
		  if($index['benchmark'] == '')
		    $index['benchmark']='rest';

		  $benchmarkCategorie[$index['benchmark']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$rapportObject->getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}

		$query = "SELECT TijdelijkeRapportage.valuta,Valutas.Omschrijving, Valutas.Afdrukvolgorde FROM TijdelijkeRapportage Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$rapportObject->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$rapportObject->pdf->rapportageValuta."' GROUP BY Valuta ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum)/getValutaKoers($rapportObject->pdf->rapportageValuta,$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_jan'])    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_begin']) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/100 );
		}
    if(count($indexValuta) > 0)
      $aantalValuta=count($indexValuta);
    else
      $aantalValuta=0;




		$regels = count($indexData)+$aantalValuta;
		$hoogte = ($regels * 4);
		if(($rapportObject->pdf->GetY() + $hoogte +16) > $rapportObject->pdf->pagebreak)
		{
			$rapportObject->pdf->AddPage();
			$rapportObject->pdf->ln();
		}

		$blokken=array(2,count($indexData),$aantalValuta);
		$y=$rapportObject->pdf->getY();
    foreach ($blokken as $regels)
    {
      if($regels > 0)
      {
        $hoogte=$regels*4+4;
		    $rapportObject->pdf->SetFillColor($rapportObject->pdf->rapport_kop_bgcolor[r],$rapportObject->pdf->rapport_kop_bgcolor[g],$rapportObject->pdf->rapport_kop_bgcolor[b]);
		    $rapportObject->pdf->Rect($rapportObject->pdf->marge,$y,160+$extraBreedte,$hoogte,'F');
		    $rapportObject->pdf->SetFillColor(0);
		    $rapportObject->pdf->Rect($rapportObject->pdf->marge,$y,160+$extraBreedte,$hoogte);
		    $y+=($hoogte);
      }
    }
    $rapportObject->pdf->SetX($rapportObject->pdf->marge);
  	$rapportObject->pdf->SetWidths(array(60,40,40));
  	$rapportObject->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
  	$rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);

  	if($klein==true)
  	{
  	  $rapportObject->pdf->SetWidths(array(60,40));
  	  $headerRow=array("","Rendement\nverslagperiode");
      $rapportObject->pdf->row($headerRow);
  	}
    else
    {
      $rapportObject->pdf->SetWidths(array(60,40,40));
      $headerRow=array("","Rendement\nverslagperiode","Rendement\nlopende jaar");
 	    $rapportObject->pdf->row($headerRow);
    }
    


  	if($klein==true)
    {
      $rapportObject->pdf->row(array("Portefeuille"));
      $rapportObject->pdf->ln(-4);
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      $rapportObject->pdf->row(array("",$rapportObject->formatGetal($performance,2)." %"));
    }
    else
    {
      $rapportObject->pdf->row(array("Portefeuille"));
      $rapportObject->pdf->ln(-4);
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      $rapportObject->pdf->row(array("",$rapportObject->formatGetal($performance,2)." %",$rapportObject->formatGetal($performanceJaar,2)." %"));
    }

  //	 $rapportObject->pdf->ln();
    $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);

    //$headerRow[0]="\nIndexvergelijking";
    $rapportObject->pdf->row(array("Indexvergelijking"));

   	$rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
    foreach ($indexData as $fonds=>$fondsData)
    {

      if($klein==true)
        $rapportObject->pdf->row(array($fondsData['Omschrijving'],$rapportObject->formatGetal($fondsData['performance'],2)." %"));
      else
        $rapportObject->pdf->row(array($fondsData['Omschrijving'],$rapportObject->formatGetal($fondsData['performance'],2)." %",$rapportObject->formatGetal($fondsData['performanceJaar'],2)." %"));
    }

              //$rapportObject->pdf->ValutaKoersStart
//$rapportObject->pdf->ValutaKoersBegin
//$rapportObject->pdf->ValutaKoersEind

    $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);

   // $rapportObject->pdf->ln();



    if(count($indexValuta) > 0)
    {
      //$headerRow[0]="\nValutavergelijking";
      $rapportObject->pdf->row(array("Valutavergelijking"));
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      foreach ($indexValuta as $fonds=>$valutaData)
      {
        /*
              if($klein==true)
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['valutaKoers_begin']/$rapportObject->pdf->ValutaKoersBegin,4),$rapportObject->formatGetal($valutaData['valutaKoers_eind']/$rapportObject->pdf->ValutaKoersEind,4),$rapportObject->formatGetal($valutaData['performance'],2)));
        else
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['valutaKoers_jan']/$rapportObject->pdf->ValutaKoersStart,4),$rapportObject->formatGetal($valutaData['valutaKoers_begin']/$rapportObject->pdf->ValutaKoersBegin,4),$rapportObject->formatGetal($valutaData['valutaKoers_eind']/$rapportObject->pdf->ValutaKoersEind,4),$rapportObject->formatGetal($valutaData['performance'],2),$rapportObject->formatGetal($valutaData['performanceJaar'],2)));

          */
        if($klein==true)
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['performance'],2)." %"));
        else
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['performance'],2)." %",$rapportObject->formatGetal($valutaData['performanceJaar'],2)." %"));
      }
    }
		//listarray($indexValuta);
  }




?>