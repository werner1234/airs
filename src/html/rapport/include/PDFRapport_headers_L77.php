<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/28 15:45:39 $
 		File Versie					: $Revision: 1.22 $

 		$Log: PDFRapport_headers_L77.php,v $
 		Revision 1.22  2020/03/28 15:45:39  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2020/02/15 18:29:05  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2019/06/08 16:06:01  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/06/02 10:03:42  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2019/04/07 11:06:41  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2019/04/03 15:52:48  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2019/03/23 17:30:48  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/03/06 16:13:44  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2019/02/23 18:32:59  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/11/28 13:18:46  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/11/22 07:25:26  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/11/10 15:41:30  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/10/27 16:49:57  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/10/24 16:00:59  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2018/10/20 18:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/10/13 17:18:13  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/10/06 17:20:57  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/09/29 16:19:30  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2018/09/15 17:45:24  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.2  2018/05/21 10:58:19  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/05/20 10:39:24  rvv
 		*** empty log message ***
 		
 	
*/
function Header_basis_L77($object)
{
  global $__appvar;
  $pdfObject = &$object;

	if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
		$pdfObject->rapportNewPage = $pdfObject->page;

	 if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT"  )
    {
		  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 		  $pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;
    }
    else
    {
    	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	  	$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;
  
      if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
        $pdfObject->rapportNewPage = $pdfObject->page;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY()-5;

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);



		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
		}

		if($pdfObject->lastPOST['anoniem'])
		  $pdfObject->rapport_depotbankOmschrijving='';


	  $pdfObject->SetXY($pdfObject->marge,$pdfObject->marge+2);
	  $pdfObject->SetFont($pdfObject->rapport_kopFont,'b',18);
		$pdfObject->SetTextColor($pdfObject->rapport_lichtblauw[0],$pdfObject->rapport_lichtblauw[1],$pdfObject->rapport_lichtblauw[2]);
	  $pdfObject->MultiCell(297-(2*$pdfObject->pdf->marge),4,strtoupper(vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal)),0,'L');
		$pdfObject->SetTextColor(0);
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->line($pdfObject->marge,20,297-$pdfObject->marge,20,array('color'=>$pdfObject->rapport_lichtblauw));


		}
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];

  if ( ! in_array($pdfObject->rapport_type, array('MUT', 'TRANS')) ) {
    $pdfObject->SetY(11);
    $rapportagePeriode = vertaalTekst('Verslagperiode',$pdfObject->rapport_taal).' '.date("j",$pdfObject->rapport_datumvanaf)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".
      date("Y",$pdfObject->rapport_datumvanaf).
      ' '.vertaalTekst('t/m',$pdfObject->rapport_taal).' '.
      date("j",$pdfObject->rapport_datum)." ".
      vertaalTekst($__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".
      date("Y",$pdfObject->rapport_datum);
    $pdfObject->cell($pdfObject->w-$pdfObject->marge-7, 4, $rapportagePeriode,false,false,'R');
  }

  $pdfObject->SetXY($pdfObject->marge,22);

	$pdfObject->SetFillColor(0,0,0);
	$pdfObject->SetDrawColor(0,0,0);



	//$pdfObject->widths=$widthBackup;
}

function HeaderFISCAAL_L77($object)
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
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 12 , 'F');
  
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
               vertaalTekst("Ongerealiseerd\nResultaat",$pdfObject->rapport_taal),'',''));
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
  $pdfObject->setY($y);
  $pdfObject->row(array("Categorie\n"));
  $pdfObject->ln();
  $pdfObject->ln();
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  
  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}

function HeaderINHOUD_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderFRONT_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderCASHY_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderVKMS_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderHUIS_L77($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderPERF_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderPERFG_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderOIV_L77($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderRISK_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderAFM_L77($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderOIB_L77($object)
{
	$pdfObject = &$object;

}

function HeaderOIS_L77($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  
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
 // $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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

function HeaderINDEX_L77($object)
{
	$pdfObject = &$object;
}

function HeaderGRAFIEK_L77($object)
{
  $pdfObject = &$object;
}

function HeaderOIR_L77($object)
{
  $pdfObject = &$object;
}

function HeaderVAR_L77($object)
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
function HeaderEND_L77($object)
{
	$pdfObject = &$object;

}

function HeaderMOD_L77($object)
{
  $pdfObject = &$object;
  
}
function HeaderVKM_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVKM();
}

function HeaderKERNV_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderKERNZ_L77($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}


function HeaderHSE_L77($object)
{
	$pdfObject = &$object;
  
  $pdfObject->fillCell=array();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetX(100);
  $pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
  
  $pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(),297-$pdfObject->marge*2, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
}


function HeaderVOLKD_L77($object)
{
  HeaderVOLK_L77($object);
}

  function HeaderVOLK_L77($object)
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
    $pdfObject->fillCell=array();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    
    $pdfObject->SetDrawColor(255,255,255);
		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige-5);
		$y = $pdfObject->getY();
    if($pdfObject->modelRapport==true)
      $pdfObject->Cell($eindhuidige-$huidige,4,'', 0,0,"C");
    else
		  $pdfObject->Cell($eindhuidige-$huidige,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell($eindactueel-$actueel,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
    if(!$pdfObject->modelRapport)
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
		
		if($pdfObject->rapport_taal==1)
		  $valutaTxt='CCY';
		else
      $valutaTxt=vertaalTekst("Valuta",$pdfObject->rapport_taal);
  
    if($pdfObject->modelRapport==true)
    {
      $pdfObject->row(array("\n" . $valutaTxt,
                        "\n" . vertaalTekst("Fondsomschrijving", $pdfObject->rapport_taal),
                        "\n" .'ISIN-code',
                        "\n" . vertaalTekst("Aantal", $pdfObject->rapport_taal),
                        '',
                        '',
                        "",
                        "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                        '',
                        "\n" . vertaalTekst("Waarde " . $teken, $pdfObject->rapport_taal),
                        vertaalTekst("Gewicht", $pdfObject->rapport_taal) . "\n  %",
                        '','','',''
                      ));

    }
    else
    {
      $pdfObject->row(array("\n" . $valutaTxt,
                        "\n" . vertaalTekst("Fondsomschrijving", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Aantal", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                        '',
                        "\n" . vertaalTekst("Waarde " . $teken, $pdfObject->rapport_taal),
                        "",
                        "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                        '',
                        "\n" . vertaalTekst("Waarde " . $teken, $pdfObject->rapport_taal),
                        vertaalTekst("Gewicht", $pdfObject->rapport_taal) . "\n  %",
                        vertaalTekst("Fonds-\nresultaat", $pdfObject->rapport_taal),
                        vertaalTekst("Valuta-\nresultaat", $pdfObject->rapport_taal),
                        vertaalTekst("Dividend & coupons", $pdfObject->rapport_taal),
                        vertaalTekst("Rende-\nment", $pdfObject->rapport_taal) . " %"
                      ));
    }

		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
	}

function HeaderVHO_L77($object)
{
  $pdfObject = &$object;
  $borderBackup=$pdfObject->CellBorders;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
  $eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
  
  $actueel 			= $eindhuidige + $pdfObject->widthB[6];
  $eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
  
  $resultaat 		= $eindactueel + $pdfObject->widthB[10];
  $eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];
  $pdfObject->fillCell=array();
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  $pdfObject->SetDrawColor(255,255,255);
  // lijntjes onder beginwaarde in het lopende jaar
  $pdfObject->SetX($pdfObject->marge+$huidige-5);
  $y = $pdfObject->getY();
  
  //$vanaf=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf);
  $tot=date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
  
  $pdfObject->Cell($eindhuidige-$huidige,4, $tot, 0,0,"C");//$vanaf
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell($eindactueel-$actueel,4, '', 0,0, "C");
  $pdfObject->SetX($pdfObject->marge+$resultaat);
  $pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
  //$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
  $pdfObject->SetDrawColor(0,0,0);
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->setXY($pdfObject->marge,$y);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
  $teken=$pdfObject->rapportageValuta;
  
  if($pdfObject->rapport_taal==1)
    $valutaTxt='CCY';
  else
    $valutaTxt=vertaalTekst("Valuta",$pdfObject->rapport_taal);
  

    $pdfObject->row(array("\n" . $valutaTxt,
                      "\n" . vertaalTekst("Fondsomschrijving", $pdfObject->rapport_taal),
                      "",
                      "\n" . vertaalTekst("Aantal", $pdfObject->rapport_taal),
                      "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                      "\n" . vertaalTekst("Waarde " . $teken, $pdfObject->rapport_taal),
                      "",
                      vertaalTekst("Directe opbrengst", $pdfObject->rapport_taal),
                      vertaalTekst("Bronheffing", $pdfObject->rapport_taal),
                      '',
                      vertaalTekst("Directe \nopbrengst in EUR", $pdfObject->rapport_taal),
                      vertaalTekst("Bronheffing in EUR", $pdfObject->rapport_taal),
                    ));

  
  $pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln();
  $pdfObject->CellBorders=$borderBackup;
}


function HeaderTRANS_L77($object)
{
	$pdfObject = &$object;
  $backup=$pdfObject->fillCell;
  $pdfObject->fillCell=array();
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
										vertaalTekst("Aan/\nVerk",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n",
										"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n",
										"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"\n".vertaalTekst("Kostprijs ",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Historisch",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Huidig",$pdfObject->rapport_taal),
										"\n".$procentTotaal));
	$pdfObject->ln(1);
  $pdfObject->fillCell=$backup;
}

function HeaderTRANSFEE_L77($object)
{
  $pdfObject = &$object;
  $backup=$pdfObject->fillCell;
  $pdfObject->fillCell=array();
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
                    vertaalTekst("Aan/\nVerk",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n",
                    "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    "\n",
                    "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Kostprijs ",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Historisch",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Huidig",$pdfObject->rapport_taal),
                    "\n".$procentTotaal));
  $pdfObject->ln(1);
  $pdfObject->fillCell=$backup;
}

function HeaderMUT_L77($object)
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
	$pdfObject->ln(2);
}

function HeaderATT_L77($object)
{
	$pdfObject = &$object;


}

function HeaderDOORKIJK_L77($object)
{
  $pdfObject = &$object;
  
  
}

function HeaderDOORKIJKVR_L77($object)
{
  $pdfObject = &$object;
  
  
}

function HeaderSMV_L77($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->Row(array('Boekdatum','Saldo','Bedrag','C/D','GB','Omschrijving'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  
}

function RestrictiesKader_l77($object)
{
  $pdfObject = &$object;
  $pdfObject->SetAligns(array('R','L','R','R','R','R','R','R','R','R'));
  $pdfObject->SetWidths(array(0,70,8,26,25,25,1,5,25,25));
 ;
  if(isset($pdfObject->portefeuilles)&& count($pdfObject->portefeuilles)>0)
  {
    $portefeuilles = $pdfObject->portefeuilles;
  }
  else
    $portefeuilles=array($pdfObject->portefeuilledata['Portefeuille']);
  $db=new DB();
  $query="SELECT soortReservering,bedrag,Omschrijving,contractueleUitsluitingen.fonds,Fondsen.Valuta
FROM contractueleUitsluitingen
JOIN Portefeuilles ON contractueleUitsluitingen.Portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.Einddatum>'".$pdfObject->rapportageDatum."'
LEFT JOIN Fondsen ON contractueleUitsluitingen.fonds=Fondsen.Fonds
WHERE contractueleUitsluitingen.Portefeuille IN('".implode("','",$portefeuilles)."') AND contractueleUitsluitingen.soortReservering='Commitment'";
  $db->SQL($query);
  $db->Query();
  $restricties=array();
  $fondsen=array();
  if($db->records()>0)
    $pdfObject->ln();
  while($data=$db->nextRecord())
  {
    $data['valutakoers']=getValutaKoers($data['fonds'],$pdfObject->rapportageDatum);
    $restricties[]=$data;
    $fondsen[]=mysql_real_escape_string($data['fonds']);
  }
  //listarray($restricties);exit;
  $query="SELECT (Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers) as Bedrag,Rekeningmutaties.Fonds,Rekeningmutaties.transactietype
     FROM Rekeningmutaties
     JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
     WHERE Rekeningen.Portefeuille='".$pdfObject->portefeuilledata['Portefeuille']."' AND
     Rekeningmutaties.Boekdatum <= '".	$pdfObject->rapportageDatum."' AND Rekeningmutaties.transactietype IN('A','D','B') AND
     Rekeningmutaties.Fonds IN('".implode("','",$fondsen)."') ORDER BY Rekeningmutaties.Boekdatum";
  $db->SQL($query);
  $db->Query();
  $opgevraagden=array();
  $eersteBoeking=array();
  while($data=$db->nextRecord())
  {
    if(!isset($opgevraagden[$data['Fonds']]))
      $opgevraagden[$data['Fonds']]=0;
    
    if($data['transactietype']=='B')
    {
      if(!isset($eersteBoeking[$data['Fonds']]))
        $opgevraagden[$data['Fonds']] += $data['Bedrag'];
    }
    else
    {
      $opgevraagden[$data['Fonds']] += $data['Bedrag'];
    }
    if(!isset($eersteBoeking[$data['Fonds']]))
    {
      $eersteBoeking[$data['Fonds']] = true;
    }
  }
//listarray($opgevraagd);listarray($restricties);exit;
  if(count($restricties)>0)
  {
    //$pdfObject->SetWidths(array(75 + 1, 65, 25, 25));
    $pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
    $pdfObject->Row(array('','Commitments','','','','Commitment','','','Opgevraagd','Verplichting'));
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $totalen[]=array();
    foreach($restricties as $data)
    {
      $opgevraagd=$opgevraagden[$data['fonds']];
      $verplichting=($data['bedrag']-$opgevraagd)*$data['valutakoers'];
      $pdfObject->Row(array('',$data['Omschrijving'],'','','',$pdfObject->formatGetal($data['bedrag'],2),'','',$pdfObject->formatGetal($opgevraagd,2),$pdfObject->formatGetal($verplichting,2)));
      $totalen['bedrag']+=$data['bedrag'];
      $totalen['opgevraagd']+=$opgevraagd;
      $totalen['verplichting']+=$verplichting;
    }
    $pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
    $pdfObject->CellBorders = array('','','','','',array('TS','UU'),'','',array('TS','UU'),array('TS','UU'));
    $pdfObject->Row(array('','Totaal','','','',$pdfObject->formatGetal($totalen['bedrag'],2),'','',$pdfObject->formatGetal($totalen['opgevraagd'],2),$pdfObject->formatGetal($totalen['verplichting'],2)));
    unset($pdfObject->CellBorders);
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  }
}


function bepaaldFondsWaardenVerdiept_L77($portefeuille,$einddatum,$pdf)
{
	$startjaar=true;
	$verdiept = new portefeuilleVerdiept($pdf,$portefeuille,$einddatum);
	$verdiepteFondsen = $verdiept->getFondsen();
	foreach ($verdiepteFondsen as $fonds)
		$verdiept->bepaalVerdeling($fonds,$verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum);


	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
	$correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
	foreach($fondswaarden as $i=>$fondsData)
	{
		//
		if(isset($pdf->fondsPortefeuille[$fondsData['fonds']]))
		{
			//echo $fondsData['fonds'];ob_flush();exit;
			$fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
			$fondsWaardeHuis=$pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
			$aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
			//echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
			unset($fondswaarden[$i]);
			foreach($pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
			{
				foreach ($details as $element => $emementDetail)
				{

					if(isset($emementDetail['overige']))
					{
						foreach($correctieVelden as $veld)
							$emementDetail['overige'][$veld]=$emementDetail['overige'][$veld]*$aandeel;
						unset($emementDetail['overige']['WaardeEuro']);
						unset($emementDetail['overige']['koersLeeftijd']);
						unset($emementDetail['overige']['FondsOmschrijving']);
						unset($emementDetail['overige']['Fonds']);
						$fondswaarden[] = $emementDetail['overige'];
					}
				}
			}
		}
	}
	$fondswaarden  = array_values($fondswaarden);
	$tmp=array();
	$conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
	foreach($fondswaarden as $mixedInstrument)
	{
		$instrument=array();
		foreach($mixedInstrument as $index=>$value)
		{
			if(isset($conversies[$index]))
				$instrument[$conversies[$index]] = $value;
			else
				$instrument[$index] = $value;
		}
		unset($instrument['voorgaandejarenactief']);

		$key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
		if(isset($tmp[$key]))
		{
			foreach($correctieVelden as $veld)
			{
				$veld=($veld);
				$tmp[$key][$veld] += $instrument[$veld];
			}
		}
		else
			$tmp[$key]=$instrument;
		//	listarray($instrument);
	}
	$fondswaarden  = array_values($tmp);
	//echo $portefeuille,$einddatum;listarray($fondswaarden);
	return $fondswaarden;
}


if(!function_exists('PieChart_L77'))
{
	function PieChart_L77($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
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