<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/16 15:23:48 $
 		File Versie					: $Revision: 1.16 $
 		
 		$Log: PDFRapport_headers_L64.php,v $
 		Revision 1.16  2019/10/16 15:23:48  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/06/19 15:59:09  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2019/01/05 10:56:46  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/07/23 17:29:56  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/07/22 07:08:42  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2018/07/21 15:54:40  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/09/02 17:15:13  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/08/17 16:01:13  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/02/17 19:33:22  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/01/28 08:31:52  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/01/28 08:12:43  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/01/27 17:08:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/01/20 17:17:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/11/29 13:13:22  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2015/10/04 11:52:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2015/09/13 11:32:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		


*/
function Header_basis_L64($object)
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
 		    $factor=0.06;
		    $xSize=1200*$factor;
		    $ySize=377*$factor;
        $logopos=4;//(297/2)-($xSize/2);
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 3, $xSize, $ySize);
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
		$pdfObject->MultiCell(297-$pdfObject->marge*2+1,4,$pdfObject->rapport_koptext,0,'R');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD")
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$pdfObject->SetY($y+8);
		$pdfObject->SetX($x);
 
		$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	
    $pdfObject->SetXY(0,$y+6);

		$pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+16);
    $pdfObject->headerStart=$pdfObject->GetY()+15;
    
   }
   $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
}

	  function HeaderSCENARIO_L64($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();
      $pdfObject->ln();
		  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

	  }
  
  	function HeaderFRONT_L64($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();
	  }

	function HeaderVKM_L64($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L64($object)
{
  $pdfObject = &$object;
}

function HeaderTRANSFEE_L64($object)
{
	$pdfObject = &$object;
}
function HeaderFRONTBRIEF_L64($object)
{
	$pdfObject = &$object;

}
function HeaderMOD_L64($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
	$eindhuidige 	= array_sum($pdfObject->widthB);

	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

	// lijntjes onder beginwaarde in het lopende jaar

	$pdfObject->SetX($pdfObject->marge+$huidige+5);
	$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");

	$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());

	$tmpY = $pdfObject->GetY();

	$pdfObject->SetY($tmpY);
	$pdfObject->SetX($pdfObject->marge);

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("ISIN",$pdfObject->rapport_taal),
							 vertaalTekst("Aantal",$pdfObject->rapport_taal),
							 vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
							 vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
							 vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal),
							 ($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""),
						 vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
						 vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
						 vertaalTekst("Portefeuille \nin EUR",$pdfObject->rapport_taal));

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
}
	
    function HeaderTRANS_L64($object)
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
										 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat",$pdfObject->rapport_taal),
										 $procentTotaal));
      $pdfObject->ln(1);
	  }
    
    function HeaderMUT_L64($object)
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
    
	  function HeaderAFM_L64($object)
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
	  function HeaderINHOUD_L64($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }	  
 	  function HeaderPERF_L64($object)
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
    
    function HeaderINDEX_L64($object)
	  {
	    $pdfObject = &$object;
	  }
    function HeaderOIB_L64($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }
  function HeaderATT_L64($object)
	{
   $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

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
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}
   function HeaderPERFG_L64($object)
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

  function HeaderVOLK_L64($object)
	{
     $pdfObject = &$object;
     $dataWidth=array(66,23,12,18,22,17,3,22,23,3,20,20,15,20);
     $splits=array(2,5,6,8,9,12);
     $n=0;
     foreach ($dataWidth as $index=>$value)
     {
       if($index<=$splits[$n])
        $kopWidth[$n] += $value;
       if($index>=$splits[$n])
        $n++;
     }
      
     $pdfObject->ln();

		 $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
     $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
     $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($dataWidth), 12, 'F');
     
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($kopWidth);
      $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
      $pdfObject->SetDrawColor(255,255,255);
      $pdfObject->Row(array('',"Actuele waardes",'','Beginwaarde verslagperiode','','Resultaat'));
      $pdfObject->SetDrawColor(0,0,0);
      $pdfObject->CellBorders = array();

 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R'));
	    $pdfObject->Row(array('Categorie/Effect','Aantal','Valuta','Koers',"Portefeuille\nin EUR","in % van\nVermogen",'','Koers',"Portefeuille\nin EUR",'','Koers Resultaat','Rente / Dividend',"Totaal\nin %","Historische\nkostprijs"));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    //$pdfObject->HeaderVOLK();
	}
  
function HeaderVHO_L64($object)
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
										vertaalTekst("Rendement\nin %",$pdfObject->rapport_taal)
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

if(!function_exists('getFondsKoers'))
{
	function getFondsKoers($fonds, $datum)
	{
		$db = new DB();
		$query = "SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
		$db->SQL($query);
		$koers = $db->lookupRecord();

		return $koers['Koers'];
	}
}

if(!function_exists('printValutaPerformanceOverzicht'))
{
function printValutaPerformanceOverzicht($object, $portefeuille, $rapportageDatum, $rapportageDatumVanaf,$omkeren=false)
{
	global $__appvar;
	$pdfObject = &$object;
	$pdfObject->ln();

	$metJanuari = $pdfObject->rapport_valutaPerformanceJanuari;

	if($metJanuari == true)
	{
		$julRapDatumVanaf = db2jul($rapportageDatumVanaf);
		$rapJaar = date('Y',$julRapDatumVanaf);
		$dagMaand = date('d-m',$julRapDatumVanaf);
		$januariDatum = $rapJaar.'-01-01';
		if($dagMaand =='01-01')
			$metJanuari = false;
	}

	if($pdfObject->printValutaPerformanceOverzichtProcentTeken)
		$teken = '%';
	else
		$teken = '';

	// selecteer distinct valuta.
	$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta,  TijdelijkeRapportage.rapportageDatum".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ". //OR TijdelijkeRapportage.rapportageDatum = '".$rapportageDatumVanaf."' )
		" TijdelijkeRapportage.valuta <> '".$pdfObject->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc, TijdelijkeRapportage.rapportageDatum";
	debugSpecial($q,__FILE__,__LINE__);
	$DB = new DB();
	$DB->SQL($q);
	$DB->Query();

	if($DB->records() > 0)
	{
		while ($valuta = $DB->NextRecord())
		{
			$valutas[$valuta['val']][$valuta['rapportageDatum']]['omschrijving'] = $valuta['ValutaOmschrijving'];
			$valutas[$valuta['val']][$valuta['rapportageDatum']]['koers'] = $valuta['actueleValuta'] / $pdfObject->ValutaKoersEind;
		}

		$valutaKeys = array_keys($valutas);
		foreach ($valutaKeys as $valuta)
		{
			$query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
               FROM Valutas ,Valutakoersen
               WHERE Valutas.valuta = Valutakoersen.valuta AND
               Valutakoersen.datum <= date '".$rapportageDatumVanaf."' AND
               Valutas.valuta = '".$valuta."'
               ORDER BY Valutakoersen.datum desc LIMIT 1";
			$DB->SQL($query);
			$DB->Query();
			$valutawaarden = $DB->NextRecord();

			$valutas[$valuta][$rapportageDatumVanaf]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
			$valutas[$valuta][$rapportageDatumVanaf]['koers'] = $valutawaarden['Koers'] / $pdfObject->ValutaKoersBegin;

			if($metJanuari == true)
			{
				$query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
                 FROM Valutas ,Valutakoersen
                 WHERE Valutas.valuta = Valutakoersen.valuta AND
                 Valutakoersen.datum <= date '$januariDatum' AND
                 Valutas.valuta = '".$valuta."'
                 ORDER BY Valutakoersen.datum desc LIMIT 1";
				$DB->SQL($query);
				$DB->Query();
				$valutawaarden = $DB->NextRecord();

				$valutas[$valuta][$januariDatum]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
				$valutas[$valuta][$januariDatum]['koers'] = $valutawaarden['Koers'] / $pdfObject->ValutaKoersStart;
				$extraBreedte = 50;

			}
		}
		//listarray($valutas);
		$kop = "Valuta";

		$regels = count($valutas);
		$hoogte = ($regels * 4) + 8;
		if(($pdfObject->GetY() + $hoogte) > $pdfObject->pagebreak)
		{
			$pdfObject->AddPage();
			$pdfObject->ln();
		}

		$pdfObject->ln();
		//$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110+$extraBreedte-23,$hoogte,'D');
	//	$pdfObject->SetFillColor(0);
	//	$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110+$extraBreedte,$hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font,$pdfObject->rapport_kop4_fontstyle,$pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(40,4, vertaalTekst($kop,$pdfObject->rapport_taal), 0,0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_fontstyle,$pdfObject->rapport_fontsize);
	//	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		if($metJanuari == true)
			$pdfObject->Cell(23,4, date("d-m-Y",db2jul($januariDatum)), 0,0, "R");
		$pdfObject->Cell(23,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
		$pdfObject->Cell(23,4, date("d-m-Y",db2jul($rapportageDatum)), 0,1, "R");
	/*
			if($metJanuari == true)
		{
			$pdfObject->Cell(23,4, vertaalTekst("Performance",$pdfObject->rapport_taal), 0,0, "R");
			$pdfObject->Cell(23,4, vertaalTekst("Jaar Perf.",$pdfObject->rapport_taal), 0,1, "R");
		}
		else
			$pdfObject->Cell(23,4, vertaalTekst("Performance",$pdfObject->rapport_taal), 0,1, "R");
*/


		while (list($key, $data) = each($valutas))
		{
			$performance = ($data[$rapportageDatum]['koers'] - $data[$rapportageDatumVanaf]['koers']) / ($data[$rapportageDatumVanaf]['koers']/100 );
//echo 		"	$performance = (".$data[$rapportageDatum]['koers']." - ".$data[$rapportageDatumVanaf]['koers'].") / (".$data[$rapportageDatumVanaf]['koers']."/100 );";
			$pdfObject->Cell(40,4, vertaalTekst($data[$rapportageDatumVanaf]['omschrijving'],$pdfObject->rapport_taal), 0,0, "L");
			if($metJanuari == true)
			{
				if($omkeren==true)
					$pdfObject->Cell(23,4, $pdfObject->formatGetal(1/$data[$januariDatum]['koers'],4), 0,0, "R");
				else
					$pdfObject->Cell(23,4, $pdfObject->formatGetal($data[$januariDatum]['koers'],4), 0,0, "R");
			}
			if($omkeren==true)
				$pdfObject->Cell(23,4, $pdfObject->formatGetal(1/$data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			else
				$pdfObject->Cell(23,4, $pdfObject->formatGetal($data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			if($omkeren==true)
				$pdfObject->Cell(23,4, $pdfObject->formatGetal(1/$data[$rapportageDatum]['koers'],4), 0,1, "R");
			else
				$pdfObject->Cell(23,4, $pdfObject->formatGetal($data[$rapportageDatum]['koers'],4), 0,1, "R");
			/*
			if($metJanuari == true)
			{
				$pdfObject->Cell(23,4, $pdfObject->formatGetal($performance,2).$teken, 0,0, "R");
				$performanceJaar = ($data[$rapportageDatum]['koers'] - $data[$januariDatum]['koers']) / ($data[$januariDatum]['koers']/100 );
				$pdfObject->Cell(23,4, $pdfObject->formatGetal($performanceJaar,2).$teken, 0,1, "R");
			}
			else
				$pdfObject->Cell(23,4, $pdfObject->formatGetal($performance,2).$teken, 0,1, "R");
			*/
		}
		$pdfObject->ln();
		$pdfObject->ln();
	}
}
}
?>