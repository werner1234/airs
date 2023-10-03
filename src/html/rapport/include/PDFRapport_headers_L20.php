<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/03 16:05:45 $
 		File Versie					: $Revision: 1.16 $

 		$Log: PDFRapport_headers_L20.php,v $
 		Revision 1.16  2019/07/03 16:05:45  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/07/03 15:37:22  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/05/31 18:55:19  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/12/22 15:34:10  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/12/19 17:01:17  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/12/15 14:52:51  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2009/11/04 16:14:36  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2009/02/07 16:34:38  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2009/02/05 15:54:07  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2009/01/31 16:42:38  rvv
 		*** empty log message ***

 		Revision 1.5  2009/01/25 15:15:53  rvv
 		*** empty log message ***

 		Revision 1.4  2009/01/17 17:02:31  rvv
 		*** empty log message ***

 		Revision 1.3  2008/12/23 09:16:27  rvv
 		*** empty log message ***

 		Revision 1.2  2008/11/24 15:41:07  rvv
 		*** empty log message ***

 		Revision 1.1  2008/11/24 09:57:53  rvv
 		*** empty log message ***

 		Revision 1.6  2008/09/15 08:04:05  rvv
 		*** empty log message ***

 		Revision 1.5  2008/07/02 10:25:04  rvv
 		*** empty log message ***

 		Revision 1.4  2008/07/01 07:12:34  rvv
 		*** empty log message ***

 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/
function Header_basis_L20($object)
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


		//$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
		  
      $factor=0.06;
		  $xSize=1200*$factor;
		  $ySize=224*$factor;
		  //echo "$xSize $ySize <br>\n";exit;
	    $pdfObject->Image($pdfObject->rapport_logo, 0, 0, $xSize, $ySize);
      
      
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
			$x = 120;
		}
		else
		{
			$x = 210;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);

		// vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n".
		$pdfObject->MultiCell(80,4,$pdfObject->portefeuilledata['crm.naam']."\n".
		                           "Nummer depotbank: ".$pdfObject->rapport_portefeuille."\n".
		                            vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);

		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize+4);
		$pdfObject->ln();
		$pdfObject->MultiCell(280,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->SetXY(100,$y+30);
}
}

	  function HeaderHSE_L20($object)
	  {
	    $pdfObject = &$object;
      $borderBackup=$pdfObject->CellBorders;
        $pdfObject->ln(1);
	    $pdfObject->SetXY($pdfObject->marge,$pdfObject->getY());
	    $pdfObject->CellBorders = array();
       if($pdfObject->rapport_deel == 'overzicht')
	    {
	    $pdfObject->SetFillColor(234,230,223);
      $pdfObject->fillCell = array(0,0,0,0,0,0,1,0,0,1,0,0);
	 	  $pdfObject->SetWidths(array(70,25,30,25,30,30,30,25,25,25));
	 	  $pdfObject->fillCell = array();
      $pdfObject->setY($pdfObject->getY()+3);
      $pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
      $pdfObject->Row($pdfObject->rapport_header1);
	    $pdfObject->CellBorders = array();
	    $pdfObject->line(8,$pdfObject->getY(),array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY());
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
      }
       if($pdfObject->rapport_deel == 'index')
	    {
	      $pdfObject->fillCell = array();
	    $pdfObject->SetWidths(array(75,20,20,20,10,75,20,20,20));
		  $pdfObject->SetAligns(array('L','C','C','C','R','L','C','C','C','C'));
		  $pdfObject->CellBorders = array('U','U','U','U','','U','U','U','U','U');
      $pdfObject->setY($pdfObject->getY()+3);
      
      $pdfObject->fillCell = array(1,1,1,1,0,1,1,1,1);
       $pdfObject->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U','R'),'',array('T','L','U'),array('T','U'),array('T','U'),array('T','R','U'));
	    $pdfObject->Row($pdfObject->rapport_header3);
	    $pdfObject->CellBorders = array();
	    $pdfObject->SetAligns(array('L','R','R','R','R','L','R','R','R'));
	    $pdfObject->fillCell = array();
	    }
      $pdfObject->CellBorders=$borderBackup;
      
    }

		function HeaderVKM_L20($object)
	{
		$pdfObject = &$object;
    $pdfObject = &$object;
    $y=$pdfObject->getY();
    $pdfObject->SetXY($pdfObject->marge,22);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Cell(280,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,"C");
    $pdfObject->SetXY($pdfObject->marge, $y);
    
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	}

	  function HeaderVOLK_L20($object)
	  {
	    $pdfObject = &$object;
	    $pdfObject->ln(1);
	    $pdfObject->SetXY($pdfObject->marge,$pdfObject->getY());
      $borderBackup=$pdfObject->CellBorders;
	    $pdfObject->CellBorders = array();
	    if($pdfObject->rapport_deel == 'overzicht')
	    {

	    $pdfObject->SetFillColor(234,230,223);
      $pdfObject->fillCell = array(0,0,0,0,0,0,1,0,0,1,0,0);
	 	  $pdfObject->SetWidths(array(60,22,25,25,25,25,25,25,25,25));
	 	  $pdfObject->preFillColumn($pdfObject->getY(),195);
	 	  $pdfObject->fillCell = array();
      $pdfObject->setY($pdfObject->getY()+3);
      $pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
      $pdfObject->Row($pdfObject->rapport_header1);
	    $pdfObject->CellBorders = array();
	    $pdfObject->Row(array('','','','','','','B','','','A'));
	    $pdfObject->line(8,$pdfObject->getY()-4,array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY()-4);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));

	    }

	    if($pdfObject->rapport_deel == 'mutaties')
	    {

      $pdfObject->fillCell = array(0,0,0,1,0,0,0,0,1,0,0,0);
	    $pdfObject->SetWidths(array(80,25,25,25,25,1,25,25,25,25));
	    $pdfObject->preFillColumn($pdfObject->getY(),195);
	 	  $pdfObject->fillCell = array();
		  $pdfObject->SetAligns(array('L','C','C','C','C','C','C','C','C','C'));
		 // $pdfObject->CellBorders = array('U','U','U','U','U','U','','U','U','U','U');
      $pdfObject->setY($pdfObject->getY()+3);
	    $pdfObject->Row($pdfObject->rapport_header2);
	    $pdfObject->CellBorders = array();
	    $pdfObject->Row(array('','B-A','C',"(B-A)-C\n ","","",'',''," \n ",''));//(B-A)-C\nTotaal
	    $pdfObject->line(8,$pdfObject->getY()-8,array_sum($pdfObject->widths)+$pdfObject->marge,$pdfObject->getY()-8);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
	    }

	    if($pdfObject->rapport_deel == 'index')
	    {
	      $pdfObject->fillCell = array();
	    $pdfObject->SetWidths(array(75,20,20,20,10,75,20,20,20));
		  $pdfObject->SetAligns(array('L','C','C','C','R','L','C','C','C','C'));
		  $pdfObject->CellBorders = array('U','U','U','U','','U','U','U','U','U');
      $pdfObject->setY($pdfObject->getY()+3);
      
      $pdfObject->fillCell = array(1,1,1,1,0,1,1,1,1);
       $pdfObject->CellBorders=array(array('T','L','U'),array('T','U'),array('T','U'),array('T','U','R'),'',array('T','L','U'),array('T','U'),array('T','U'),array('T','R','U'));
	    $pdfObject->Row($pdfObject->rapport_header3);
	    $pdfObject->CellBorders = array();
	    $pdfObject->SetAligns(array('L','R','R','R','R','L','R','R','R'));
	    $pdfObject->fillCell = array();
	    }
	    
	    $y=$pdfObject->getY();
	    $pdfObject->SetXY($pdfObject->marge,22);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Cell(280,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,"C");
	    $pdfObject->SetXY($pdfObject->marge, $y);
      $pdfObject->CellBorders=$borderBackup;

	  }

	  function HeaderATT_L20($object)
	  {
	    $pdfObject = &$object;
	    $y=$pdfObject->getY();
	    $pdfObject->SetXY($pdfObject->marge,22);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Cell(280,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,"C");
	    $pdfObject->SetXY($pdfObject->marge, $y);
	  }

function HeaderPORTAL_L20($object)
{
   HeaderTRANS_L20($object);
}
	  
	  function HeaderTRANS_L20($object)
	  {
	    $pdfObject = &$object;
	    $y=$pdfObject->getY();
	    $pdfObject->SetXY($pdfObject->marge,22);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Cell(280,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,"C");
	    $pdfObject->SetXY($pdfObject->marge, $y);

	  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);



		// achtergrond kleur
		

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
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
//			$pdfObject->Cell(65,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C"); //81 ipv 65
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
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopende jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
	

	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	  }
	  
	  function HeaderMUT_L20($object)
	  {
	    $pdfObject = &$object;
	    $y=$pdfObject->getY();
	    $pdfObject->SetXY($pdfObject->marge,22);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->Cell(280,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,"C");
	    $pdfObject->SetXY($pdfObject->marge, $y);

				$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
										 vertaalTekst("Bank Afschrift",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 "",
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


?>