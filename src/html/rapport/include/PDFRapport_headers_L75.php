<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/22 16:35:31 $
 		File Versie					: $Revision: 1.34 $

 		$Log: PDFRapport_headers_L75.php,v $
 		Revision 1.34  2020/07/22 16:35:31  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2020/06/17 15:38:53  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2020/06/03 15:41:21  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2020/05/20 17:13:48  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2020/03/11 15:18:12  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2020/01/18 13:30:29  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/06/02 10:03:42  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2019/05/25 16:22:07  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/05/22 16:06:45  rvv
 		*** empty log message ***
 		
 	

*/
function Header_basis_L75($object)
{
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
		  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 		  $pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;
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

		//	$pdfObject->rapport_logo='/develop/php/robert/AIRS/html/rapport/logo/logo_ave.png';
		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
			$factor = 0.025;
			$xSize = 1637 * $factor;
			$ySize = 315 * $factor;
			$pdfObject->rect(0,0,297,$ySize+10,'F','',array($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']));
			$pdfObject->Image($pdfObject->rapport_logo, 297-$xSize-$pdfObject->marge, 5, $xSize, $ySize);
			$pdfObject->Line(0,$ySize+10,297,$ySize+10);
		}


	  $pdfObject->SetXY(0,8);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	  $pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;


    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    	    $pdfObject->SetXY($pdfObject->marge,22);
	//$pdfObject->widths=$widthBackup;
}


function HeaderOIH_L75($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  $dataWidth=array(65,25,15,30,30,10,25,30,20,20);
  
  $dataWidth=array(50,20,12,20,20,2,20,20,12,2,20,20,15,20,22,14);
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
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($kopWidth);
  $pdfObject->SetAligns(array('L','C','L','C','L','C','L','C'));
  $pdfObject->CellBorders = array('','U','','U','','U','','U');
  $pdfObject->Row(array('',"Totaal commitment",'','Totaal opgevraagd','','Totaal terugbetaald','','Huidige waarde investering'));
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
                    vertaalTekst("Directe \n op- \n brengst",$pdfObject->rapport_taal),
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

function HeaderINHOUD_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderEND_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderOIS_L75($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderOIR_L75($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderHUIS_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderFRONT_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderCASHY_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderPERF_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderPERFG_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderRISK_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderOIB_L75($object)
{
	$pdfObject = &$object;
}

function HeaderDOORKIJK_L75($object)
{
	$pdfObject = &$object;

}

function HeaderGRAFIEK_L75($object)
{
  $pdfObject = &$object;
  
}

function HeaderDOORKIJKVR_L75($object)
{
  $pdfObject = &$object;
  
}
function HeaderOIV_L75($object)
{
	$pdfObject = &$object;

}

function HeaderSMV_L75($object)
{
  $pdfObject = &$object;
  $pdfObject->headerSMV();
}

function HeaderINDEX_L75($object)
{
  $pdfObject = &$object;
}

function HeaderVKM_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVKM();
}
function HeaderVKMS_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVKM();
}
function HeaderVKMD_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVKM();
}

function HeaderKERNV_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderKERNZ_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}
function HeaderKERNZ2_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}


function HeaderHSE_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	$y = $pdfObject->getY();


	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->setXY($pdfObject->marge,$y);
	if($pdfObject->rapportageValuta=='EUR')
		$teken='€';
	else
		$teken=$pdfObject->rapportageValuta;

	$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										'',//"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										'',//"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										'',	//			"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										vertaalTekst("Kostprijs",$pdfObject->rapport_taal)."\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										'',//"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										'',		//		"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										vertaalTekst("Actuele ",$pdfObject->rapport_taal)."\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										'',//vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										'',//vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										'',//"\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),


										''//"\n".vertaalTekst("in %",$pdfObject->rapport_taal)
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


function HeaderVOLK_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$fillBackup=$pdfObject->fillCell;
	unset($pdfObject->fillCell);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
	$eindhuidige 	= $huidige +$pdfObject->widthB[4]+$pdfObject->widthB[5];

	$actueel 			= $eindhuidige + $pdfObject->widthB[6];
	$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9]+ $pdfObject->widthB[10];

	$resultaat 		= $eindactueel + $pdfObject->widthB[11];
	$eindresultaat = $resultaat +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

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
    if($pdfObject->rapportageValuta=='EUR')
      $teken='€';
    else
	$teken=$pdfObject->rapportageValuta;

	$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),

						'',	//			"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
						'',		//		"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
		'',
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
	$pdfObject->ln();
	$pdfObject->fillCell=$fillBackup;
}

function HeaderVHO_L75($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$fillBackup=$pdfObject->fillCell;
	unset($pdfObject->fillCell);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
	$eindhuidige 	= $huidige +$pdfObject->widthB[4]+$pdfObject->widthB[5];

	$actueel 			= $eindhuidige + $pdfObject->widthB[6];
	$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9]+ $pdfObject->widthB[10];

	$resultaat 		= $eindactueel + $pdfObject->widthB[11];
	$eindresultaat = $resultaat +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

    if($pdfObject->rapportageValuta=='EUR')
      $teken='€';
    else
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

										'',	//			"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										'',		//		"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										'',
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
	$pdfObject->fillCell=$fillBackup;

	// $pdfObject->ln(20);
}


function HeaderTRANS_L75($object)
{
	$pdfObject = &$object;
  $fillBackup=$pdfObject->fillCell;
  $pdfObject->fillCell=array();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
//	$pdfObject->SetX(100);
	$pdfObject->MultiCell(275,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
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
                    "\n".vertaalTekst("Soort",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"\n".vertaalTekst("Kostprijs ",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Historisch",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Huidig",$pdfObject->rapport_taal),
										"\n".$procentTotaal));
	$pdfObject->ln(1);
  $pdfObject->fillCell=$fillBackup;
}

function HeaderMUT_L75($object)
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

function HeaderATT_L75($object)
{
	$pdfObject = &$object;
	$w=282/10;

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+6, 297-2*$pdfObject->marge, 8 , 'F');

	$pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
	$pdfObject->ln(2);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	// $pdfObject->ln(1);
	$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beginvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Stortingen en",$pdfObject->rapport_taal)."\n".vertaalTekst("onttrekkingen",$pdfObject->rapport_taal),
										vertaalTekst("Koersresultaten",$pdfObject->rapport_taal) ."\n ",
										vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beleggings",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
										vertaalTekst("Eindvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n". vertaalTekst("(maand)",$pdfObject->rapport_taal),
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n". vertaalTekst("(Cumulatief)",$pdfObject->rapport_taal)));
	//$sumWidth = array_sum($pdfObject->widthA);
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());

}

function HeaderPERFD_L75($object)
{
	$pdfObject = &$object;
	$w=282/10;

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+6, 297-2*$pdfObject->marge, 8 , 'F');

	$pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
	$pdfObject->ln(2);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	// $pdfObject->ln(1);
	$pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beginvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Stortingen en",$pdfObject->rapport_taal)."\n".vertaalTekst("onttrekkingen",$pdfObject->rapport_taal),
										vertaalTekst("Koersresultaten",$pdfObject->rapport_taal) ."\n ",
										vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beleggings",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
										vertaalTekst("Eindvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n". vertaalTekst("(jaar)",$pdfObject->rapport_taal),
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n". vertaalTekst("(Cumulatief)",$pdfObject->rapport_taal)));
	//$sumWidth = array_sum($pdfObject->widthA);
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());

}


if(!function_exists('PieChart_L75'))
{
	function PieChart_L75($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
	{

		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $vtdata=array();
		foreach($data as $key=>$val)
		  $vtdata[vertaalTekst($key ,$pdfObject->rapport_taal)]=$val;

		$pdfObject->SetLegends($vtdata,$format);


		$XPage = $pdfObject->GetX();
		$YPage = $pdfObject->GetY();
/*
		if($pdfObject->debug==true)
		{
			$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
			$pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
			$pdfObject->Rect($XPage,$YPage,$w,$h);
			$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
		}
*/
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
			if (round($angle,1) != 0)
			{
				$angleEnd = $angleStart + $angle;
				$avgAngle=($angleStart+$angleEnd)/360*M_PI;

				//$lineAngle=($angleEnd)/180*M_PI;
				//$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));

				if($angleEnd-$angleStart>1)
				  $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd,'F');

				if($val > 2)
				{
					//$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
					/*
					if($pdfObject->debug==true)
					{
						$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255)));
						$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor));
					}
					*/
					$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2);
					$pdfObject->Cell(10,4,number_format($val,0,',','.').'%',0,0,'C');
				}
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
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
/*
		if($pdfObject->debug==true)
		{
			$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
			$pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
			$pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
		}
*/
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

	}
}

function bepaaldFondsWaardenVerdiept_L75($portefeuille,$einddatum,$pdf,$datumVanaf='')
{
	$verdiept = new portefeuilleVerdiept($pdf,$portefeuille,$einddatum);
	$verdiepteFondsen = $verdiept->getFondsen();
	foreach ($verdiepteFondsen as $fonds)
		$verdiept->bepaalVerdeling($fonds,$verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum,'',$datumVanaf);

	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,(substr($einddatum, 5, 5) == '01-01')?true:false,'EUR',substr($einddatum,0,4).'-01-01');
	//listarray($verdiept->FondsPortefeuilleData);exit;
 // listarray($fondswaarden);
	$correctieVelden=array('totaalAantal','actuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
	foreach($fondswaarden as $i=>$fondsData)
	{
		//
		if(isset($pdf->fondsPortefeuille[$fondsData['fonds']]))
		{
			//echo $fondsData['fonds'];ob_flush();exit;
			$fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
			$fondsWaardeHuis=$pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
			$aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
	//		echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis <br>\n";exit;
			unset($fondswaarden[$i]);
			foreach($pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
			{
				foreach ($details as $element => $emementDetail)
				{

					if(isset($emementDetail['overige']))
					{
						foreach($correctieVelden as $veld)
            {
              if($veld=='actuelePortefeuilleWaardeEuro' && !isset($emementDetail['overige'][$veld]))
                $veld='ActuelePortefeuilleWaardeEuro';

              $emementDetail['overige'][$veld] = $emementDetail['overige'][$veld] * $aandeel;
            //  echo "$element $veld ".$emementDetail['overige'][$veld]."<br>\n";
            }

					//	$emementDetail['overige']['beginPortefeuilleWaardeInValuta']=$emementDetail['overige']['totaalAantal']*$emementDetail['overige']['beginwaardeLopendeJaar'];
					//	$emementDetail['overige']['beginPortefeuilleWaardeEuro']=$emementDetail['overige']['beginPortefeuilleWaardeInValuta']*$emementDetail['overige']['beginwaardeValutaLopendeJaar'];
            
           // unset($emementDetail['overige']['actuelePortefeuilleWaardeEuro']);
           // $emementDetail['overige']['actuelePortefeuilleWaardeInValuta']=$emementDetail['overige']['totaalAantal']*$emementDetail['overige']['actueleFonds']*$emementDetail['overige']['fondsEenheid'];
            //if(isset($emementDetail['overige']['ActuelePortefeuilleWaardeEuro ']))
           // $emementDetail['overige']['ActuelePortefeuilleWaardeEuro']=$emementDetail['overige']['ActuelePortefeuilleWaardeEuro']*$emementDetail['overige']['actueleValuta'];
						//listarray($emementDetail);
						//'historischeWaarde',
            
          	unset($emementDetail['overige']['WaardeEuro']);
						unset($emementDetail['overige']['koersLeeftijd']);
						unset($emementDetail['overige']['FondsOmschrijving']);
						unset($emementDetail['overige']['Fonds']);
						if(!isset($emementDetail['overige']['historischeRapportageValutakoers']))
							$emementDetail['overige']['historischeRapportageValutakoers']=1;

						$fondswaarden[] = $emementDetail['overige'];
					}
				}
			}
		}
	}
	$fondswaarden  = array_values($fondswaarden);// listarray($fondswaarden);exit;
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
				//$veld=($veld);
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


?>