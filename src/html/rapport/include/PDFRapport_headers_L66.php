<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/11/01 16:51:06 $
 		File Versie					: $Revision: 1.8 $

 		$Log: PDFRapport_headers_L66.php,v $
 		Revision 1.8  2017/11/01 16:51:06  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/10/23 11:32:33  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/04/03 10:58:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/03/19 16:51:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/01/14 12:34:42  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/11/01 22:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/10/29 16:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/09/17 15:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/08/25 08:50:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/18 12:24:51  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/08/10 15:48:01  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/06/09 18:01:53  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/06/05 15:56:07  rvv
 		*** empty log message ***
 		
*/

 function Header_basis_L66($object)
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
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

     if($pdfObject->CurOrientation=='P')
       $pageWidth=210;
     else
       $pageWidth=297;
  	//rapport_risicoklasse


   	if(is_file($pdfObject->rapport_logo))
		{
       $factor=0.03;
		   $xSize=1500*$factor;//$x=885*$factor;
		   $ySize=665*$factor;//$y=849*$factor;

       $logoX=$pageWidth/2-$xSize/2;
			 $pdfObject->Image($pdfObject->rapport_logo, $logoX, 2, $xSize, $ySize);
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

	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" )
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);

	    $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n \n ",0,'R');
	    $pdfObject->SetX(0);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		  $pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
	    $pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
      //$pdfObject->SetY(30);
   }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
 }

  	function HeaderVKM_L66($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
 	function HeaderFRONT_L66($object)
	{

	    $pdfObject = &$object;

	}

function HeaderRISK_L66($object)
{

	$pdfObject = &$object;

}

 	function HeaderMUT_L66($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 		$pdfObject->SetX(100);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array('','',vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  function HeaderTRANS_L66($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderTRANS();
	}

function HeaderMOD_L66($object)
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
							 vertaalTekst("Aantal",$pdfObject->rapport_taal),
							vertaalTekst("Valuta",$pdfObject->rapport_taal),
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

  function HeaderPERF_L66($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderPERF();
	}
      function HeaderOIB_L66($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }

  function HeaderCASHY_L66($object)
  {
 	  $pdfObject = &$object;
   	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }

 	function HeaderVOLKD_L66($object)
	{
    $pdfObject = &$object;
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
		$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, '', 0, "C");
		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
  	//$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
  	$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("ISIN-code",$pdfObject->rapport_taal),
										  '','',"",
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
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
	}
  
   function HeaderATT_L66($object)
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

  
  function HeaderVOLK_L66($object)
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
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige-5);

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
										vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
									  vertaalTekst("Valuta",$pdfObject->rapport_taal),
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
  
  function HeaderOIH_L66($object)
	{
	  $pdfObject = &$object;
    $pdfObject->ln();
    $dataWidth=array(28,55,20,20,20,20,22,22,22,22,22);
 	  $pdfObject->SetWidths($dataWidth);
    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
    $lastColors=$pdfObject->CellFontColor;
    unset($pdfObject->CellFontColor);
    $pdfObject->Row(array(vertaalTekst("Risico\nCategorie",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
      "\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
      "\n".date('d-m-Y',$pdfObject->rapport_datum),
      "\n".vertaalTekst("Stortingen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
    $pdfObject->CellFontColor=$lastColors;
    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
    $pdfObject->SetLineWidth(0.1);
  }
  
  function HeaderPERFG_L66($object)
	{
    $pdfObject = &$object;
    $colWidth=(297-(2*$pdfObject->marge+30))/10;
    $tmp=array();
    for($i=0;$i<=9;$i++)
      $tmp[]=$colWidth;
    $tmp[]=15;
    $tmp[]=15;  
    $pdfObject->widthA = $tmp;//array(26,25,24,24,24,20,20,25,24,24,23,23);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');
    

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
    $pdfObject->ln(1);
		//$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat",$pdfObject->rapport_taal),0,0);
		//$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
		$pdfObject->row(array("Kwartaal\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopenrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
		                      vertaalTekst("Rend.",$pdfObject->rapport_taal)."\n(".vertaalTekst("maand",$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rend.",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumu.",$pdfObject->rapport_taal).")"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
    unset($pdfObject->fillCell);
	}

if(!function_exists('BarDiagram'))
{
	function BarDiagram($pdfObject, $w, $h, $data, $colorArray, $titel)
	{
		$pdfObject->sum = array_sum($data);
		$pdfObject->NbVal = count($data);
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$XPage = $pdfObject->GetX();
		$YPage = $pdfObject->GetY();
		$margin = 0;
		$nbDiv = 5;
		$legendWidth = 10;
		$YDiag = $YPage;
		$hDiag = floor($h);
		$XDiag = $XPage + $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if ($color == null)
		{
			$color = array(155, 155, 155);
		}
		if ($maxVal == 0)
		{
			$maxVal = max($data) * 1.1;
		}
		if ($minVal == 0)
		{
			$minVal = min($data) * 1.1;
		}
		if ($minVal > 0)
		{
			$minVal = 0;
		}
		$maxVal = ceil($maxVal * 10) / 10;

		$offset = $minVal;
		$valIndRepere = ceil(round(($maxVal - $minVal) / $nbDiv, 2) * 100) / 100;
		$bandBreedte = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$unit = $lDiag / $bandBreedte;
		$hBar = 5;//floor($hDiag / ($pdfObject->NbVal + 1));
		$hDiag = $hBar * ($pdfObject->NbVal + 1);

		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);
		$legendaStep = $unit;

		$legendaStep = $unit / $nbDiv * $bandBreedte;
		$valIndRepere = round($valIndRepere / $unit / 5) * 5;


		$pdfObject->SetLineWidth(0.2);
		$pdfObject->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->SetFillColor($color[0], $color[1], $color[2]);
		$nullijn = $XDiag - ($offset * $unit);

		$i = 0;
		$nbDiv = 10;

		$pdfObject->SetFont($pdfObject->rapport_font, '', 5);
		if (round($legendaStep, 5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for ($x = $nullijn; $x > $XDiag; $x = $x - $legendaStep)
			{
				$pdfObject->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$pdfObject->setXY($x, $YDiag + $hDiag);
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit * 100, 2) . '%', 0, 0, 'C');
				$i++;
				if ($i > 100)
				{
					break;
				}
			}

			$i = 0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for ($x = $nullijn; $x < ($XDiag + $lDiag); $x = $x + $legendaStep)
			{
				$pdfObject->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$pdfObject->setXY($x, $YDiag + $hDiag);
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit * 100, 2) . '%', 0, 0, 'C');

				$i++;
				if ($i > 100)
				{
					break;
				}
			}
		}
		$pdfObject->SetFont($pdfObject->rapport_font, 'B', $pdfObject->rapport_fontsize);
		$i = 0;

		$pdfObject->SetXY($XDiag, $YDiag);
		$pdfObject->Cell($lDiag, $hval - 4, $titel, 0, 0, 'C');
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize - 2);


		foreach ($data as $key => $val)
		{
			$pdfObject->SetFillColor($colorArray[$key]['R']['value'], $colorArray[$key]['G']['value'], $colorArray[$key]['B']['value']);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$pdfObject->Rect($xval, $yval, $lval, $hval, 'DF');
			$pdfObject->SetXY($XPage, $yval);
			$pdfObject->Cell($legendWidth, $hval, $key, 0, 0, 'R');
			$i++;
		}

		//Scales
		$minPos = ($minVal * $unit);
		$maxPos = ($maxVal * $unit);

		$unit = ($maxPos - $minPos) / $nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


	}
}


?>