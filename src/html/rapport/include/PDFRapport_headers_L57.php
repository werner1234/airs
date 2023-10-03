<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/08 15:42:42 $
 		File Versie					: $Revision: 1.16 $
 		
 		$Log: PDFRapport_headers_L57.php,v $
 		Revision 1.16  2020/04/08 15:42:42  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2020/03/18 17:44:11  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2020/03/04 16:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2020/02/29 16:24:09  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2020/02/26 16:12:54  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2019/10/30 16:47:58  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/06/18 09:18:24  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/10/21 07:26:16  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/01/07 17:25:26  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/12/31 18:09:06  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/28 14:29:08  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/12/21 13:23:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/10/08 15:42:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/09/03 15:56:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/07/06 12:34:34  rvv
 		*** empty log message ***
 		

*/
function Header_basis_L57($object)
{
 $pdfObject = &$object;
  
  
    if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "VRAGEN")
    {
      if(is_file($pdfObject->rapport_logo))
      {
        $factor=0.05;
        $xSize=879*$factor;
        $ySize=340*$factor;
        $logopos=$pdfObject->marge;//(297/2)-($xSize/2);
        $pdfObject->Image($pdfObject->rapport_logo, $logopos, $logopos, $xSize, $ySize);
        
      }
      $pdfObject->setY(8);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->MultiCell($pdfObject->w-2*$pdfObject->marge,4,$pdfObject->rapport_portefeuille."\n".$pdfObject->rapport_naam1,0,'R');
      $pdfObject->setY(30);
     }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
      $pdfObject->rapportNewPage = $pdfObject->page;
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
    
    
    if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille) || $pdfObject->rapport_type == 'INHOUD')
      $pdfObject->rapportNewPage = $pdfObject->page;


		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		//$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$y = $pdfObject->GetY();

		// default header stuff


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

		if(is_file($pdfObject->rapport_logo))
		{
 		    $factor=0.05;
		    $xSize=879*$factor;
		    $ySize=340*$factor;
        $logopos=$pdfObject->marge;//(297/2)-($xSize/2);
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, $logopos, $xSize, $ySize);
		}


		$pdfObject->SetTextColor($pdfObject->rapport_subtotaal_fontcolor['r'],$pdfObject->rapport_subtotaal_fontcolor['g'],$pdfObject->rapport_subtotaal_fontcolor['b']);
		$pdfObject->SetX($pdfObject->marge);
		$pdfObject->MultiCell($pdfObject->w-2*$pdfObject->marge,4,$pdfObject->rapport_koptext,0,'R');
		$pdfObject->SetY($y);

		$pdfObject->SetXY($pdfObject->marge,$y+12);
 
		$pdfObject->MultiCell($pdfObject->w-2*$pdfObject->marge,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(12);
    $pdfObject->SetX(0);
		$pdfObject->MultiCell($pdfObject->w,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+20);
    $pdfObject->headerStart = $pdfObject->getY()+14;
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
}
}

  function HeaderINHOUD_L57($object)
  {
    $pdfObject = &$object;
  }

function HeaderVRAGEN_L57($object)
{
  $pdfObject = &$object;
}
  
  	function HeaderVKM_L57($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L57($object)
{
  $pdfObject = &$object;
}

function HeaderVKMA_L57($object)
{
  $pdfObject = &$object;
}
	  function HeaderHSE_L57($object)
	  {
	    $pdfObject = &$object;
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	    $pdfObject->headerHSE();

	  }	
	  function HeaderOIS_L57($object)
	  {
	    $pdfObject = &$object;
	    $pdfObject->ln();
		  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
      $borderBackup=$pdfObject->CellBorders;
  		unset($pdfObject->CellBorders);
	  	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
      $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
      
      $pdfObject->SetFont($pdfObject->rapport_font,'BI',$pdfObject->rapport_fontsize);
      $pdfObject->row(array(vertaalTekst($pdfObject->hoofdSortering,$pdfObject->rapport_taal)));
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
			$pdfObject->row(array(vertaalTekst($pdfObject->tweedeSortering,$pdfObject->rapport_taal),//'      '.
											vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Koers",$pdfObject->rapport_taal),
											vertaalTekst("Valuta",$pdfObject->rapport_taal),
											vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                      vertaalTekst("In % Totaal",$pdfObject->rapport_taal)));

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
    $pdfObject->CellBorders=$borderBackup;
	  }
    
	  function HeaderOIB_L57($object)
	  {
	    $pdfObject = &$object;
	  $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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


				$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
											vertaalTekst("Valuta",$pdfObject->rapport_taal),
											vertaalTekst("in valuta",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
    
	  }    
	  function HeaderAFM_L57($object)
	  {
	    $pdfObject = &$object;
    $pdfObject = &$object;
	  $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2] + $pdfObject->widthB[3];
		$lijn1eind 	= $lijn1 + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		// lijntjes onder beginwaarde in het lopende jaar

		  $pdfObject->SetX($pdfObject->marge+$lijn1);
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		  $pdfObject->MultiCell($pdfObject->widthB[4] + $pdfObject->widthB[5]+8,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");

		  $pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);


				$pdfObject->row(array(vertaalTekst("AFM-categorie",$pdfObject->rapport_taal),
											"",
											"",
											"",
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	  }	  
	  function HeaderOIV_L57($object)
	  {
	    $pdfObject = &$object;
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	    $pdfObject->headerOIV();

	  }
	  function HeaderPERF_L57($object)
	  {
	    $pdfObject = &$object;
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	    $pdfObject->headerPERF();

	  }

function HeaderHUIS_L57($object)
{
  $pdfObject = &$object;
  
}

function HeaderRISK_L57($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}


function HeaderVAR_L57($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths(array(65,27,15,13,1,12,21,19,25, 5,  14,16,16,19,12));
  
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
  
  $pdfObject->SetAligns(array('L','L','L','L','R','R','R','R', 'R'  ,'R','R','R','R','R','R'));
  //$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
  //$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  if($pdfObject->rapport_taal==1)
    $valutaTxt='CCY';
  else
    $valutaTxt=vertaalTekst("Valuta",$pdfObject->rapport_taal);
  
  $pdfObject->row(array(
                    "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("ISIN",$pdfObject->rapport_taal),
                    "".vertaalTekst("Coupon-\ndatum",$pdfObject->rapport_taal),
                    "".vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
                    '',
                    "\n".$valutaTxt,
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

function HeaderPERFD_L57($object)
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
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).")",
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
  
}

	  function HeaderVOLK_L57($object)
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
      $pdfObject->Cell(65,4, vertaalTekst("Kostprijs lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
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
    if($pdfObject->rapportageValuta=='EUR')
      $teken='€';
    else
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
    
      $pdfObject->setXY($pdfObject->marge+$pdfObject->widthB[0]+$pdfObject->widthB[1],$y+4);
    
      $pdfObject->Cell($pdfObject->widthB[1],4,vertaalTekst("Valuta",$pdfObject->rapport_taal),null,null,null,null,null);
     
    
      $pdfObject->setY($y);
      $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($pdfObject->widthA);
      $pdfObject->SetAligns($pdfObject->alignA);
      $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
      $pdfObject->SetWidths($pdfObject->widthB);
      $pdfObject->SetAligns($pdfObject->alignB);
      $pdfObject->ln();


	  }
 	  function HeaderTRANS_L57($object)
	  {
	    $pdfObject = &$object;
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	    $pdfObject->headerTRANS();

	  } 
    
 	  function HeaderMUT_L57($object)
	  {
	    $pdfObject = &$object;
			$pdfObject->setDrawcolor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	    $pdfObject->headerMUT();
	  }   
   	
    function HeaderCASHY_L57($object)
	  {
	    $pdfObject = &$object;
	  }    
    
    function HeaderATT_L57($object)
	  {
	    $pdfObject = &$object;
  		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln(2);
		  $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
			$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  		$pdfObject->ln(2);
	  	$pdfObject->SetWidths($pdfObject->widthB);
	  	$pdfObject->SetAligns($pdfObject->alignB);
	  	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->fillCell = array();
		  $pdfObject->row(array("", "", "", "", "", "", "", "", "", ""));
  		$pdfObject->SetWidths($pdfObject->widthA);
	  	$pdfObject->SetAligns($pdfObject->alignA);
  		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	  }
    
  function HeaderPERFG_L57($object)
	{
    $pdfObject = &$object;
    $w=(297-$pdfObject->marge*2)/11;
    $tmp=array();
    for($i=0;$i<11;$i++)
      $tmp[]=$w;
    $pdfObject->widthA = $tmp;//array(29,28,32,32,25,25,25,26,30,25,28);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
		                      vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("per maand",$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    //$sumWidth = array_sum($pdfObject->widthA);
	  //$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}


if(!function_exists('printPie'))
{
	function printPie($pdfObject, $pieData, $kleurdata)
	{
		// default colors
		// custom maken zet de kleuren in config/rapportage.php , en laad deze hier als ze bestaand, anders deze als default .
		if (is_array($pdfObject->customPieColors))
		{
			$col1 = $pdfObject->customPieColors["col1"];
			$col2 = $pdfObject->customPieColors["col2"];
			$col3 = $pdfObject->customPieColors["col3"];
			$col4 = $pdfObject->customPieColors["col4"];
			$col5 = $pdfObject->customPieColors["col5"];
			$col6 = $pdfObject->customPieColors["col6"];
			$col7 = $pdfObject->customPieColors["col7"];
			$col8 = $pdfObject->customPieColors["col8"];
			$col9 = $pdfObject->customPieColors["col9"];
			$col0 = $pdfObject->customPieColors["col0"];
			$standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);
		}
		else
		{
			$col1 = array(255, 0, 0); // rood
			$col2 = array(0, 255, 0); // groen
			$col3 = array(255, 128, 0); // oranje
			$col4 = array(0, 0, 255); // blauw
			$col5 = array(255, 255, 0); // geel
			$col6 = array(255, 0, 255); // paars
			$col7 = array(128, 128, 128); // grijs
			$col8 = array(128, 64, 64); // bruin
			$col9 = array(255, 255, 255); // wit
			$col0 = array(0, 0, 0); //zwart
			$standaardKleuren = array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col0);
		}

// standaardkleuren vervangen voor eigen kleuren.

		if ($kleurdata)
		{
			if (!$pdfObject->rapport_dontsortpie)
			{
				$sorted = array();
				$percentages = array();
				$kleur = array();
				$valuta = array();

				while (list($key, $data) = each($kleurdata))
				{
					$percentages[] = $data[percentage];
					$kleur[] = $data[kleur];
					$valuta[] = $key;
				}
				arsort($percentages);

				while (list($key, $percentage) = each($percentages))
				{
					$sorted[$valuta[$key]]['kleur'] = $kleur[$key];
					$sorted[$valuta[$key]]['percentage'] = $percentage;
				}
				$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
			}

			$pieData = array();
			$grafiekKleuren = array();

			$a = 0;
			while (list($key, $value) = each($kleurdata))
			{
				if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
				{
					$grafiekKleuren[] = $standaardKleuren[$a];
				}
				else
				{
					$grafiekKleuren[] = array($value['kleur']['R']['value'], $value['kleur']['G']['value'], $value['kleur']['B']['value']);
				}
				$pieData[$key] = $value[percentage];
				$a++;
			}
		}
		else
		{
			$grafiekKleuren = $standaardKleuren;
		}

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r], $pdfObject->rapport_fontcolor[g], $pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapport_printpie = true;

		while (list($key, $value) = each($pieData))
		{
			if ($value < 0)
			{
				if ($pdfObject->rapport_layout == 8 || $pdfObject->rapport_layout == 10)
				{
					$pieData[$key] = -1 * $value;
				}
				else
				{
					$pdfObject->rapport_printpie = false;
				}
			}
		}

		if ($pdfObject->rapport_printpie)
		{

			$pdfObject->SetXY(210, $pdfObject->headerStart);
			$y = $pdfObject->getY();
			$pdfObject->SetFont($pdfObject->pdf->rapport_font, 'b', 10);
			$pdfObject->Cell(50, 4, vertaalTekst($pdfObject->rapport_titel, $pdfObject->rapport_taal), 0, 1, "C");
			$pdfObject->SetFont($pdfObject->pdf->rapport_font, '', $pdfObject->pdf->rapport_fontsize);
			$pdfObject->SetX(210);
			$pdfObject->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($pdfObject->getY() - $y) + 8;
			$pdfObject->setY($y);

			$pdfObject->SetLineWidth($pdfObject->lineWidth);


		}
	}
}


if(!function_exists('PieChart_L57'))
{
  function PieChart_L57($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);
    
    
    $XPage = $pdfObject->GetX();
    $YPage = $pdfObject->GetY();
    //$pdfObject->debug=true;
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
      if ($angle != 0)
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
          $pdfObject->Cell(10,4,number_format($val,1,',','.').'%',0,0,'C');
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
    /* witte lijnen tussen taartpunten.
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
*/
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor(0,0,0);
    
    //Legends
    //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    
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
    
    for($i=0; $i<$pdfObject->NbVal; $i++) {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
    $pdfObject->SetDrawColor(0,0,0);
    $pdfObject->SetFillColor(0,0,0);
  }
}
?>