<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/09 16:41:48 $
 		File Versie					: $Revision: 1.14 $
 		
 		$Log: PDFRapport_headers_L73.php,v $
 		Revision 1.14  2019/11/09 16:41:48  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/04/07 15:21:44  rvv
 		*** empty log message ***
 		

*/
function Header_basis_L73($object)
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
			$pdfObject->customPageNo = 0;
      $pdfObject->rapportNewPage = $pdfObject->page;
		}
    else 
    {  
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
		//	echo $pdfObject->portefeuilledata['Portefeuille']." ".$pdfObject->rapportNewPage."<br>\n";
  	if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'])
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
/*
		if(is_file($pdfObject->rapport_logo))
		{
 		    $factor=0.035;
		    $xSize=1200*$factor;
		    $ySize=769*$factor;
        $logopos=(297/2)-($xSize/2);
	      $pdfObject->Image($pdfObject->rapport_logo, 0, 0, $xSize, $ySize);
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
    */
	//	$pdfObject->MultiCell(120,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD")
		{
			$x = 210-158;
		}
		else
		{
			$x = 297-158;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($pdfObject->marge);

		$pdfObject->setFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->rect($pdfObject->marge,$y,297-$pdfObject->marge*2,11,'F');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
//$pdfObject->rapport_koptext."\n".
			$pdfObject->setTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$pdfObject->SetY($y+3);
		$pdfObject->Cell(150,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,0,'L');
		$pdfObject->Cell(297-$pdfObject->marge*2-150,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".
														vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".
														date("Y",$pdfObject->rapport_datum),0,0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
			$pdfObject->setTextColor(0);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(12);
    $pdfObject->SetX(0);

		
		$pdfObject->SetY($y+20);
    $pdfObject->headerStart=$pdfObject->GetY()+15;

  }
	$pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
}

	function HeaderVKM_L73($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderFRONT_L73($object)
{
	$pdfObject = &$object;
}

function HeaderTRANS_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


}

function HeaderMODEL_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,"b",16);
	$pdfObject->SetXY($pdfObject->marge,11.5);
	$pdfObject->setTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->Cell(200,8, vertaalTekst("Modelcontrole", $pdfObject->rapport_taal) ,0,1,"L");
	$pdfObject->setTextColor(0);
	$pdfObject->SetY(22);

	$pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);
	$pdfObject->SetX($pdfObject->marge);
	//rij 3
	$pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(70,4, vertaalTekst("Controledatum",$pdfObject->rapport_taal).": ",0,0,"R");
	$pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(50,4, date("j",$pdfObject->selectData['datumTm'])." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->tmdatum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->selectData[datumTm]),0,1,"L");

	$pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(70,4, vertaalTekst("Modelportefeuille",$pdfObject->rapport_taal).": ",0,0,"R");
	$pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_portefeuille'],0,1,"L");
	$pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);

	if($pdfObject->selectData['modelcontrole_rapport'] == "vastbedrag")
	{
		$extraTekst =" Vast bedrag: ".$pdfObject->selectData['modelcontrole_vastbedrag'];
	}
	elseif($pdfObject->selectData["modelcontrole_filter"] != "gekoppeld")
	{
		$extraTekst = " : niet gekoppeld depot";
	}
	else
		$extraTekst = "";

	$pdfObject->Cell(70,4, vertaalTekst("Client",$pdfObject->rapport_taal).": ",0,0,"R");
	$pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(50,4, $pdfObject->clientOmschrijving,0,1,"L");

	$pdfObject->SetFont($pdfObject->rapport_font,"b",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(70,4, vertaalTekst("Naam",$pdfObject->rapport_taal).": ",0,0,"R");
	$pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
	$pdfObject->Cell(50,4, $pdfObject->naamOmschrijving.$extraTekst,0,1,"L");



	$pdfObject->ln();
	$pdfObject->SetWidths(array(60,20,20,20,25,25,25,30,25,25));
	$pdfObject->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
	$pdfObject->Row(array(vertaalTekst("Fonds",$pdfObject->rapport_taal),
							 vertaalTekst("Model Percentage",$pdfObject->rapport_taal),
							 vertaalTekst("Werkelijk Percentage",$pdfObject->rapport_taal),
										vertaalTekst("Afwijkings Percentage",$pdfObject->rapport_taal),
										vertaalTekst("Afwijkings in EUR",$pdfObject->rapport_taal),
										vertaalTekst("Kopen",$pdfObject->rapport_taal),
										vertaalTekst("Verkopen",$pdfObject->rapport_taal),
										vertaalTekst("Waarde volgens model",$pdfObject->rapport_taal),
										vertaalTekst("Koers in locale valuta",$pdfObject->rapport_taal),
							      vertaalTekst("Geschat orderbedrag",$pdfObject->rapport_taal)));

	$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
	$pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);

}

function HeaderMUT_L73($object)
{
	$pdfObject = &$object;

}

function HeaderATT_L73($object)
{
//	$pdfObject = &$object;
	//$pdfObject->HeaderATT();
}

function HeaderOIB_L73($object)
{
	//$pdfObject = &$object;
	//$pdfObject->HeaderATT();
}

function HeaderRISK_L73($object)
{
	//$pdfObject = &$object;
	//$pdfObject->HeaderATT();

}

function HeaderINDEX_L73($object)
{
	//$pdfObject = &$object;
	//$pdfObject->HeaderATT();

}

function HeaderDOORKIJKVR_L73($object)
{
	//$pdfObject = &$object;
	//$pdfObject->HeaderATT();

}

function HeaderEND_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->ln(-6);
	//$pdfObject->HeaderATT();

}

function HeaderVAR_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetWidths(array(65,18,17,1,16,21,21,25, 5,  18,18,18,19,18));

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

	$pdfObject->SetAligns(array('L','L','L','R','R','R','R', 'R'  ,'R','R','R','R','R','R'));
	$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));


	$pdfObject->row(array(
										"\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
										"".vertaalTekst("Coupon-\ndatum",$pdfObject->rapport_taal),
										"".vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
										'',
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

function HeaderPERF_L73($object)
{
	$pdfObject = &$object;
	$w=(297-$pdfObject->marge*2)/11;

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

	$pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
//	$pdfObject->ln(-6);

//	$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
//	$pdfObject->ln(2);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	// $pdfObject->ln(1);
	$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Beginvermogen",$pdfObject->rapport_taal)."\n " ,
 vertaalTekst("Stortingen",$pdfObject->rapport_taal)."\n ",
  vertaalTekst("Onttrekkingen",$pdfObject->rapport_taal)."\n",
vertaalTekst("Koersresultaten",$pdfObject->rapport_taal)."\n ",
 vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
 vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
  vertaalTekst("Beleggings",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
vertaalTekst("Eindvermogen",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n".vertaalTekst("(maand)",$pdfObject->rapport_taal),
 vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n".vertaalTekst("(Cumulatief)",$pdfObject->rapport_taal)));
	$sumWidth = array_sum($pdfObject->widthA);
//	$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

}

function HeaderPERFG_L73($object)
{
	$pdfObject = &$object;
	$w=(297-$pdfObject->marge*2)/11;

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

	$pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
//	$pdfObject->ln(-6);

//	$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
//	$pdfObject->ln(2);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	// $pdfObject->ln(1);
	$pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beginvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Stortingen",$pdfObject->rapport_taal)."\n",
										vertaalTekst("Onttrekkingen",$pdfObject->rapport_taal)."\n",
										vertaalTekst("Koersresultaten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Beleggings",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
										vertaalTekst("Eindvermogen",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n".vertaalTekst("(Jaar)",$pdfObject->rapport_taal),
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n". vertaalTekst("(Cumulatief)",$pdfObject->pdf->rapport_taal)));
	$sumWidth = array_sum($pdfObject->widthA);
//	$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

}


function HeaderPERFD_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
	
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	
  $pdfObject->setTextColor(255,255,255);
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
	$pdfObject->setTextColor(0,0,0);
	//$sumWidth = array_sum($pdfObject->widthA);
	//$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}

function HeaderVHO_L73($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsizeSmall);

	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	$y = $pdfObject->getY();

	if($pdfObject->lastHoofdcategorie=='VAR'||$pdfObject->lastHoofdcategorie=='G-LIQ')
	{
		$renteKop = vertaalTekst("Opgelopen rente", $pdfObject->rapport_taal);
		$pdfObject->widthB[1]=54;
		$pdfObject->widthB[7]=16;
	}
	else
	{
		$renteKop = '';
		$pdfObject->widthB[1]=64;
		$pdfObject->widthB[7]=6;
	}

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);

	$pdfObject->row(array("Aantal\nnominaal",
										vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal)." ".date("j-n",$pdfObject->rapport_datumvanaf),
										vertaalTekst("Kostprijs",$pdfObject->rapport_taal),
										vertaalTekst("Markt-\nwaarde",$pdfObject->rapport_taal),
										$renteKop,
										vertaalTekst("Historische waarde",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Resultaat STD\nAbsoluut",$pdfObject->rapport_taal),
										vertaalTekst("Resultaat STD\nin %",$pdfObject->rapport_taal),
										vertaalTekst("Weging\nin %",$pdfObject->rapport_taal)
									));
	$pdfObject->ln();

}

  function HeaderOIS_L73($object)
  {
	  $pdfObject = &$object;
	  HeaderVOLK_L73($pdfObject);
  }

  function HeaderVOLK_L73($object)
	{
		$pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsizeSmall);

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$y = $pdfObject->getY();

    if($pdfObject->lastHoofdcategorie=='VAR'||$pdfObject->lastHoofdcategorie=='G-LIQ')
		{
			$renteKop = vertaalTekst("Opgelopen rente", $pdfObject->rapport_taal);
			$pdfObject->widthB[1]=54;
			$pdfObject->widthB[7]=16;
		}
		else
		{
			$renteKop = '';
			$pdfObject->widthB[1]=64;
			$pdfObject->widthB[7]=6;
		}

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$pdfObject->row(array("Aantal\nnominaal",
											vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
											vertaalTekst("Valuta",$pdfObject->rapport_taal),
											vertaalTekst("Koers",$pdfObject->rapport_taal),
											vertaalTekst("Koers",$pdfObject->rapport_taal)." ".date("j-n",$pdfObject->rapport_datumvanaf),
											vertaalTekst("Kostprijs",$pdfObject->rapport_taal),
											vertaalTekst("Markt-\nwaarde",$pdfObject->rapport_taal),
											$renteKop,
											vertaalTekst("Waarde",$pdfObject->rapport_taal)." ".date("j-n",$pdfObject->rapport_datumvanaf),
											vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
											vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
											vertaalTekst("Directe opbrengsten",$pdfObject->rapport_taal),
											vertaalTekst("Resultaat YTD\nAbsoluut",$pdfObject->rapport_taal),
											vertaalTekst("Resultaat YTD\nin %",$pdfObject->rapport_taal),
											vertaalTekst("Weging\nin %",$pdfObject->rapport_taal)
										));
		$pdfObject->ln();

/*
		$pdfObject->setY($y);
		$pdfObject->ln();
		$pdfObject->ln();

*/
	}
	

    
 	  function HeaderHSE_L73($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
 	    $pdfObject->ln();
		  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
			$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
			$actueel 			= $eindhuidige + $pdfObject->widthB[6];
			$eindactueel 	= array_sum($pdfObject->widthB);
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		// lijntjes onder beginwaarde in het lopende jaar

		$tmpY = $pdfObject->GetY();
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
		else if($pdfObject->rapport_layout == 4)
			$pdfObject->MultiCell(90,4, vertaalTekst("Fonds",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");

		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
		else if($pdfObject->rapport_layout == 4)
			$pdfObject->MultiCell(90,4, vertaalTekst("Waarde",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");

			$pdfObject->setDrawColor(255,255,255);
		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	
			$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Per stuk\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											"",
											vertaalTekst("Per stuk\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Vermogen\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""));
	

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->setY($pdfObject->GetY()-8);
		$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
		$pdfObject->ln();

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	  }
    

?>