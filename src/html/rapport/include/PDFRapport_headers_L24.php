<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/05 16:42:29 $
 		File Versie					: $Revision: 1.3 $

 		$Log: PDFRapport_headers_L24.php,v $
 		Revision 1.3  2019/07/05 16:42:29  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/02/06 16:07:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/03/31 17:26:12  rvv
 		*** empty log message ***
 		
*/

function Header_basis_L24($object)
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
		
		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old; 
		
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

		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 43);
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

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
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
	  

	 		$pdfObject->headerStart = $pdfObject->getY()+4;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
  }

}

function HeaderVOLKD_L24($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  

    $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
    $eindhuidige 	= $huidige +$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4];
    
    $actueel 			= $eindhuidige + $pdfObject->widthB[5];
    $eindactueel 	= $actueel + $pdfObject->widthB[6] + $pdfObject->widthB[7];
    
    $resultaat 		= $eindactueel + $pdfObject->widthB[8] ;
    $eindresultaat = $resultaat +  $pdfObject->widthB[9] +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12];


  
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
  

    $pdfObject->SetX($pdfObject->marge+$huidige+5);
    $pdfObject->Cell(65,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$actueel);
    if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
      $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"L");
    else
      $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"L");
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
                   vertaalTekst("Koers",$pdfObject->rapport_taal),
                   vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                   vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
                   "",
                   vertaalTekst("Koers",$pdfObject->rapport_taal),
                   vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                   vertaalTekst("",$pdfObject->rapport_taal),
                   vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
                   vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                   vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
                   vertaalTekst("in %",$pdfObject->rapport_taal))
      );


  
  
  $pdfObject->setY($y);
  if($pdfObject->rapport_VOLK_volgorde_beginwaarde == 2)
  {
    $pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
    $pdfObject->row(array("",vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  }
  else
  {
    $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
    $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  }
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln();
  $pdfObject->ln();
  
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
  $pdfObject->ln();
}

?>