<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:49:26 $
 		File Versie					: $Revision: 1.14 $
 		
 		$Log: PDFRapport_headers_L4.php,v $
 		Revision 1.14  2018/12/21 17:49:26  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/09/22 17:12:17  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/09/12 11:41:19  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/01/05 11:57:31  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2018/01/04 13:41:18  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/01/03 14:19:56  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/12/27 18:29:09  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/03 19:22:25  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/11/30 16:48:42  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/11/27 11:09:59  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/11/14 08:12:30  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/11/13 16:28:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/05/06 15:35:03  rvv
 		*** empty log message ***
 		
 
 	
*/
function Header_basis_L4($object)
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
    }
    else
    {
//  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
//  		$pdfObject->customPageNo = 0;

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

		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old;

		if(isset($pdfObject->__appvar['consolidatie']))
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
		}
		else
		{
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		//rapport_risicoklasse


		if(is_file($pdfObject->rapport_logo))
		{

        $factor=0.025;
		    $xSize=2050*$factor;
		    $ySize=391*$factor;
		    $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->w/2-$xSize/2, 7, $xSize, $ySize);

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

		if ($pdfObject->rapport_layout != 17 )
		  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);

		if ($pdfObject->rapport_layout == 14)
	  {

		$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY(100,$y);

		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');

		$pdfObject->SetXY(100,$y+18);

	  }
	  elseif ($pdfObject->rapport_layout == 15)
	  {
	    //lege pagina
		  $pdfObject->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
		  $pdfObject->SetFillColor(255,255,255);
		  $pdfObject->Rect(8.5, 8.5, 280, 193, 'D');
		  $pdfObject->Rect(9.5, 9.5, 278, 191, 'D');
      $pdfObject->SetFillColor(255,255,153);
		 	$pdfObject->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
		  $pdfObject->Rect(14, 14, 268, 182, 'DF');
		  $pdfObject->Rect(15, 15, 266, 180, 'D');
		  $pdfObject->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
		  $pdfObject->Rect(160, 20, 110, 30, 'D');
			$pdfObject->SetLineStyle(array('width' => 0.6 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
			$pdfObject->SetFillColor(255,255,255);
		  $pdfObject->Rect(161, 21, 108, 28, 'DF');
		  if(is_file($pdfObject->rapport_afbeelding))
		  {
			  $pdfObject->Image($pdfObject->rapport_afbeelding, 162, 22, 106, 26);
		  }
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		  $pdfObject->SetFont('arial','B',11);
		  $pdfObject->SetXY(30,30);
		  $pdfObject->SetAligns(array('L','C'));
		  $pdfObject->SetWidths(array(75,40));
		  $pdfObject->ln();
		  $pdfObject->row(array('Clientnummer',$pdfObject->rapport_clientVermogensbeheerder));

	    $i=1;
	  	for($j=0;$j<strlen($pdfObject->rapport_portefeuille);$j++)
	  	{
		   if($i>2 && $j < 7)
	  	 {
	  	  $portefeuilleString.='.';
		    $i=1;
	  	 }
	  	 $portefeuilleString.= $pdfObject->rapport_portefeuille[$j];
		   $i++;
	  	}
		  $pdfObject->row(array('Rekeningnummer '.$pdfObject->rapport_depotbank.' Bank',$portefeuilleString));
		  $pdfObject->ln(12);
		  $pdfObject->SetFont('arial','B',14);
		  $pdfObject->Cell(100,8,$pdfObject->rapport_titel);
		  $pdfObject->SetFont('arial','',14);
		  $pdfObject->Cell(40,8,jul2form($pdfObject->rapport_datum));
		  $pdfObject->SetFont('arial','',14);
		  $pdfObject->Cell(100,8,'Client: '.$pdfObject->rapport_naam1);

		  $pdfObject->ln(12);
	  }
	  elseif ($pdfObject->rapport_layout == 16)
	  {
	    $pdfObject->MultiCell(40,4,"\n\n\n",0,'R');

	    $pdfObject->SetX(100);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
	  }
	  elseif ($pdfObject->rapport_layout == 17)
	  {

	 //   $pdfObject->CellBorders = array();
		//  $pdfObject->fillCell = array();
		  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_fontstyle,$pdfObject->rapport_fontsize);
		  $pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
		  $pdfObject->SetDrawColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);

	    $pdfObject->SetXY($pdfObject->marge,$pdfObject->marge);
	    $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
	    $pdfObject->SetXY($x,$pdfObject->marge);
	    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	    $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->SetX($x);
      $pdfObject->MultiCell(40,4,"\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	    $pdfObject->SetXY(100+$pdfObject->marge,15);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',12);
		  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');

	  }
	  else
	  {
	    $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	    $pdfObject->SetX(100);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
	  }

   $pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
 }
}
  function HeaderATT_L4($object)
	{
    $pdfObject = &$object;
	// achtergrond kleur

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8, 'F');
	
  }
  
  	function HeaderVKM_L4($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function getFondsKoers_L4($fonds,$datum)
{
	$db=new DB();
	$query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	$db->SQL($query);
	$koers=$db->lookupRecord();
	return $koers['Koers'];
}

function getValutaKoers_L4($valuta,$datum)
{
	$db=new DB();
	$query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
	$db->SQL($query);
	$koers=$db->lookupRecord();
	return $koers['Koers'];
}

function formatGetal_L4($waarde, $dec)
{
	return number_format($waarde,$dec,",",".");
}

function HeaderPERFG_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderPERFG();
}

function HeaderHSE_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->SetX(100);
	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	$pdfObject->ln();

	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
	$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5] +$pdfObject->widthB[6];
	$actueel 			= $eindhuidige + $pdfObject->widthB[7];
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


	$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
	$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());


	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


		$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
								 vertaalTekst("Aantal / Nominaal",$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Beginwaarde verslagperiode",$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Koers (valuta)",$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Valuta",$pdfObject->rapport_taal),
								 vertaalTekst($pdfObject->rapportageValuta,$pdfObject->rapport_taal),
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

	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->headerStart = $pdfObject->getY();//+4;

}

function HeaderPERF_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerPerf();
}

function HeaderVOLK_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVOLK();
}


function HeaderTRANS_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerTRANS();
}

function HeaderMUT_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerMUT();
}

function HeaderOIB_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerOIB();
}

function HeaderOIV_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerOIV();
}

function HeaderCASHY_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->headerCASHY();
}

function HeaderPERFD_L4($object)
{
	$pdfObject = &$object;
	$pdfObject->widthA = array(26,27,28,30,23,23,23,24,28,24,26);
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
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
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
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("maand",$pdfObject->rapport_taal).")",
										vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->SetTextColor(0,0,0);


	//$sumWidth = array_sum($pdfObject->widthA);
	// $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}


function indexKader_L4($object)
{
	$pdfObject = &$object;
	$db=new DB();
	$query="SELECT IndexPerBeleggingscategorie.Fonds,IndexPerBeleggingscategorie.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving, Fondsen.Omschrijving as fondsOmschrijving
 FROM IndexPerBeleggingscategorie 
JOIN Beleggingscategorien ON IndexPerBeleggingscategorie.Beleggingscategorie =Beleggingscategorien.Beleggingscategorie 
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds 
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$object->portefeuilledata['Vermogensbeheerder']."' AND
 (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$object->portefeuilledata['Portefeuille']."') ORDER BY Beleggingscategorien.Afdrukvolgorde,IndexPerBeleggingscategorie.id"; //ALP
	$db->SQL($query);
	$db->Query();
	$categorien=array();
	//$kwartaal=ceil(date('m',$object->rapport_datum)/3);
	//$begindagen=array(1=>'01-01',2=>'03-31',3=>'06-30',4=>'09-31');
	//$beginDatum=date('Y',$object->rapport_datum).'-'.$begindagen[$kwartaal];
	$beginDatum=date('Y-m-d',$object->rapport_datumvanaf);
	$perioden=array('jan'=>date('Y-01-01',$object->rapport_datum),'begin'=>$beginDatum,'eind'=>date('Y-m-d',$object->rapport_datum));

	while($data=$db->nextRecord())
	{
		foreach($perioden as $periode=>$datum)
 		  $data[$periode]=getFondsKoers_L4($data['Fonds'],$datum);

		$data['ytd']= ($data['eind'] - $data['jan']) / ($data['jan']/100 );
		$data['kwartaal']= ($data['eind'] - $data['begin']) / ($data['begin']/100 );

		$categorien[$data['categorieOmschrijving']][$data['Fonds']]=$data;
	}
	//listarray($categorien);

	$eindDatum=date('d/m/Y',$object->rapport_datum);

	$pdfObject->ln(6);
	foreach($categorien as $categorie=>$fondsRegels)
	{

		if($pdfObject->getY()>170)
			$pdfObject->addPage();
		$pdfObject->setAligns(array('L','R','R','R','R'));
		$pdfObject->setWidths(array(70,40,40,40,40));
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->row(array($categorie));
		$pdfObject->CellBorders=array('U','U','U','U','U');
		$pdfObject->row(array('Index',"Rendement\nverslagperiode","Cumulatief\nrendement YTD",'Indexstand',"Indexstand\nper"));
		unset($pdfObject->CellBorders);
		$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		foreach($fondsRegels as $fonds=>$fondsData)
		{
			$pdfObject->setAligns(array('L','R','R','R','R'));
			$pdfObject->setWidths(array(70,40,40,40,40));
			$pdfObject->row(array($fondsData['fondsOmschrijving'],
												    formatGetal_L4($fondsData['kwartaal'],2).'%',
											      formatGetal_L4($fondsData['ytd'],2).'%',
		                        formatGetal_L4($fondsData['eind'],2),
			                      $eindDatum));
		}
		$pdfObject->ln(3);

	}


}
  

?>