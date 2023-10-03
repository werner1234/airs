<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/09/18 14:52:23 $
 		File Versie					: $Revision: 1.15 $

 		$Log: PDFRapport_headers_L43.php,v $
 		Revision 1.15  2019/09/18 14:52:23  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2019/07/13 17:49:46  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/07/29 17:18:20  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/12/08 07:03:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/29 15:41:46  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/10/09 14:45:08  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/09/11 08:30:02  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/02/06 16:42:56  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/01/14 08:21:34  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/12/02 16:16:29  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/03/20 16:56:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/02/17 11:00:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/02/03 09:04:21  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/12/09 16:28:50  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2011/03/23 17:01:47  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/
function Header_basis_L43($object)
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
			$logopos = 130;
		}


		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);


		if(is_file($pdfObject->rapport_logo))
		{

		 // $factor=0.09;
		 // $xSize=492*$factor;
		//  $ySize=211*$factor;
		  $factor=0.0375;
		  $xSize=1667*$factor;//1667 417
		  $ySize=379*$factor;//379 100
		  //echo "$xSize $ySize <br>\n";exit;
	    $pdfObject->Image($pdfObject->rapport_logo, 292/2-$xSize/2, 5, $xSize, $ySize);

	  //  $pdfObject->Image($pdfObject->rapport_logo, 220, 5, 65, 20);
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



		$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY(100,$y);

		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');

		$pdfObject->SetXY(100,$y+18);
    $pdfObject->headerStart = $pdfObject->getY()+14;
  }
}

	function HeaderVKM_L43($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
function HeaderFRONT_L43($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderFRONT();
}
function HeaderOIH_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIH();
}
function HeaderOIS_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIS();
}
function HeaderOIBS_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIBS();
}
function HeaderOIR_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIR();
}
function HeaderSCENARIO_L43($object)
{
  $pdfObject = &$object;
  
}
function HeaderOIB_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIB();
}
function HeaderAFM_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderAFM();
}
function HeaderOIV_L43($object)
{
	$pdfObject = &$object;
	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
	$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

	// lijntjes onder beginwaarde in het lopende jaar
	$lijn1 =65;
	$lijn1eind = 125;

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);


	if(is_array($pdfObject->portefeuilles))
	{
		if(count($pdfObject->portefeuilles) < 6)
		{
			$pdfObject->SetX($pdfObject->marge);
			$pdfObject->Cell(65, 4, vertaalTekst("Beleggingscategorie", $pdfObject->rapport_taal), 0, 0, "L");
			$pdfObject->Cell(35, 4, 'Totaal', 0, 0, "C");
			foreach ($pdfObject->portefeuilles as $portefeuille)
			{
				$pdfObject->Cell(35, 4, "Waarden", 0, 0, "C");
			}
			$pdfObject->Ln();
			$pdfObject->SetX($pdfObject->marge + 65+35);
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
		$pdfObject->SetX($pdfObject->marge+$lijn1);
		$pdfObject->MultiCell(35,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
		$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));
	}
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	if(count($pdfObject->portefeuilles) > 5)
		$pdfObject->Ln(10);
}
function HeaderPERFG_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERFG();
}

 	  function HeaderPERFD_L43($object)
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
	  
function old_HeaderPERFD_L43($object)
{
	$pdfObject = &$object;
	$pdfObject->ln(-11);
	$object->SetFont($object->rapport_font,$pdfObject->rapport_kop_fontstyle,$object->rapport_kop_fontsize);


	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
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
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	$object->SetWidths($object->widthA);
	$object->SetAligns($object->alignA);
}
function HeaderGRAFIEK_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderGRAFIEK();
}
function HeaderVOLKD_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVOLKD();
}
function HeaderVHO_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVHO();
}
function HeaderMUT_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderMUT();
}

function HeaderATT_L43($object)
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
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
		$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
											vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
											vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
											vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
											vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
											vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("periode",$pdfObject->rapport_taal).")",
											vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$sumWidth = array_sum($pdfObject->widthA);
		$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}

function HeaderCASH_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASH();
}
function HeaderCASHY_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASHY();
}
function HeaderMODEL_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderMODEL();
}
function HeaderSMV_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderSMV();
}
function HeaderRISK_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderRISK();
}                             
function HeaderEND_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderEND();
}                             
function HeaderINDEX_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
}   
function HeaderZORG_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderZORG();
}                             
function HeaderHUIS_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderHUIS();
}
function HeaderFISCAAL_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderFISCAAL();
}
function HeaderDUURZAAM_L43($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderDUURZAAM();
}
	  function HeaderTRANS_L43($object)
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


		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Transactie",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
									 	 vertaalTekst("Aantal",$pdfObject->rapport_taal),
									 	 vertaalTekst("Koers in Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in Euro",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("Provisie ",$pdfObject->rapport_taal),
										 vertaalTekst("Totaal",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 $procentTotaal));

	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
    
    }

	  function HeaderPERF_L43($object)
	  {
	  $pdfObject = &$object;
    $pdfObject->HeaderPERF();
    
    }

	  function HeaderVOLK_L43($object)
	  {
	  $pdfObject = &$object;


	  	  // voor data
		$pdfObject->widthB = array(10,60,18,22,5,22,1,5,22,22,19,23,19,19,15);
		$pdfObject->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$pdfObject->widthA = array(60,18,15,22,22,1,15,25,22,12,22,15,22,15);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');



    $pdfObject->setX($pdfObject->marge);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->marge+75);
		$pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
  	$pdfObject->SetX($pdfObject->marge+135);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  	$pdfObject->SetX($pdfObject->marge+195);
	  $pdfObject->Cell(90,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
 		$pdfObject->SetWidths($pdfObject->widthA);
 		$pdfObject->SetAligns($pdfObject->alignA);
    $y = $pdfObject->getY();
	  $pdfObject->row(array("Categorie"));
	  $pdfObject->SetWidths($pdfObject->widthB);
	  $pdfObject->SetAligns($pdfObject->alignB);
 		$pdfObject->SetY($y);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  	$pdfObject->row(array("",
												"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("koers in EUR",$pdfObject->rapport_taal),
								        "",
												vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												"",
												vertaalTekst(" ",$pdfObject->rapport_taal),
												vertaalTekst("koers in EUR",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("Aandeel in portefeuille",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthB)+$pdfObject->marge,$pdfObject->GetY());
    $pdfObject->ln();
		

	  }

function HeaderVOLKV_L43($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetWidths(array(60,16,16,16,21,21,25, 5,  20,20,20,20,20));

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
//      echo "$id => $waarde \n<br>";
	}
	foreach ($positie as $key=>$value)
		$positie[$key]+=$pdfObject->marge;

	$y=$pdfObject->GetY()+5;
	$pdfObject->pageTop=array($positie['midden'],$y+1);
	//  $pdfObject->setXY($positie['fondsStart'],$y);
	// $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,"FONDS VALUTA",0,'C');
	//  $pdfObject->setXY($positie['waardeStart'],$y);
//   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
//     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
//   else
//     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

//   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
//   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

	$pdfObject->SetAligns(array('L','L','L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
	$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));


	$pdfObject->row(array(
										"\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
										vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
										vertaalTekst("Rating debiteur",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),'',
										vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
										vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
										vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
										vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
										vertaalTekst("%",$pdfObject->rapport_taal)."  \n".vertaalTekst("port.",$pdfObject->rapport_taal)));


	unset($pdfObject->CellBorders);//"Modified\nduration",
}


	  function HeaderHSE_L43($object)
	  {
	  $pdfObject = &$object;


	  	  // voor data
		$pdfObject->widthB = array(10,60,18,22,5,23,1,5,20,23,1,13,20,22,25,10);
		$pdfObject->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$pdfObject->widthA = array(60,18,15,22,22,1,15,25,22,15,22,15,22,10);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');



    $pdfObject->setX($pdfObject->marge);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->marge+75);
		$pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
  	$pdfObject->SetX($pdfObject->marge+135);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  	$pdfObject->SetX($pdfObject->marge+195);
	  $pdfObject->Cell(70,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
 		$pdfObject->SetWidths($pdfObject->widthA);
 		$pdfObject->SetAligns($pdfObject->alignA);
    $y = $pdfObject->getY();
	  $pdfObject->row(array("Categorie"));
	  $pdfObject->SetWidths($pdfObject->widthB);
	  $pdfObject->SetAligns($pdfObject->alignB);
 		$pdfObject->SetY($y);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  	$pdfObject->row(array("",
												"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("koers in EUR",$pdfObject->rapport_taal),
								        "",
												vertaalTekst("Portefeuille\n in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												"",
												vertaalTekst(" ",$pdfObject->rapport_taal),
												vertaalTekst("koers in EUR",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille\n in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												'',
												'',
												vertaalTekst("Aandeel in portefeuille",$pdfObject->rapport_taal),
												vertaalTekst("Absoluut",$pdfObject->rapport_taal),
                        vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal)));

		 $pdfObject->ln();
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge+array_sum($pdfObject->widthB),$pdfObject->GetY());


	  }


function PieChart($object,$w, $h, $data, $format, $colors=null)
{
	$pdfObject = &$object;


	$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
	$pdfObject->SetLegends($data,$format);

	$XPage = $pdfObject->GetX();
	$YPage = $pdfObject->GetY();
	$margin = 2;
	$hLegend = 2;
	$radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
	$radius = floor($radius / 2);
	$XDiag = $XPage + $margin + $radius;
	$YDiag = $YPage + $margin + $radius;
	if($colors == null) {
		for($i = 0;$i < $pdfObject->NbVal; $i++) {
			$gray = $i * intval(255 / $pdfObject->NbVal);
			$colors[$i] = array($gray,$gray,$gray);
		}
	}

	//Sectors
	$pdfObject->SetLineWidth(0.2);
	$angleStart = 0;
	$angleEnd = 0;
	$i = 0;
	foreach($data as $val) {
		$angle = floor(($val * 360) / doubleval($pdfObject->sum));
		if ($angle != 0) {
			$angleEnd = $angleStart + $angle;
			$pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
			$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
			$angleStart += $angle;
		}
		$i++;
	}
	if ($angleEnd != 360) {
		$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
	}

	//Legends
	$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

	$x1 = $XPage + $w + $radius*.5 ;
	$x2 = $x1 + $hLegend + $margin - 12;
	$y1 = $YDiag -($radius) + $margin;

	for($i=0; $i<$pdfObject->NbVal; $i++)
	{
		$pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
		$pdfObject->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
		$pdfObject->SetXY($x2,$y1);
		$pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
		$y1+=$hLegend + $margin;
	}

}




?>