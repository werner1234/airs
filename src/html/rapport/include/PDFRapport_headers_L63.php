<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/03/25 10:16:55 $
 		File Versie					: $Revision: 1.8 $

 		$Log: PDFRapport_headers_L63.php,v $
 		Revision 1.8  2018/03/25 10:16:55  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2018/02/07 17:22:29  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/02/21 07:32:49  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/02/13 14:02:39  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/01/23 17:53:31  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/01/09 18:58:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/09/20 17:32:28  rvv
 		*** empty log message ***
 		
 
*/

function Header_basis_L63($object)
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	//if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	//	$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);


		$pdfObject->SetY(25);

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

    $oldLogo=substr($pdfObject->rapport_logo,-12,12);
   // echo $oldLogo;exit;
		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 55;
		}
		else
		{
		  if($oldLogo=='logo_wey.png')
      	$logopos = 180;
      else
        $logopos = 220;
		}

   // if($pdfObject->rapport_type == 'OIH')
    //$pdfObject->SetY(0);
		//$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
		  if($oldLogo=='logo_wey.png')
			  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 108);
      else  
        $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 70);

      $pdfObject->SetDrawColor($pdfObject->rapport_kop_lijn['r'],$pdfObject->rapport_kop_lijn['g'],$pdfObject->rapport_kop_lijn['b']);
      $pdfObject->SetLineWidth(0.5);
      $pdfObject->Line($pdfObject->marge,23,290,23);
      $pdfObject->SetLineWidth($pdfObject->lineWidth);
      $pdfObject->SetDrawColor(0);
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

//			$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
//	    $pdfObject->SetX(100);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',16);
      $pdfObject->SetXY(0,30);
		  $pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
      $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
    
    $pdfObject->headerStart = $pdfObject->getY()+16;
  }
  $pdfObject->SetXY($pdfObject->marge,38);

}

	function HeaderVKM_L63($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
  function HeaderATT_L63($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
	}
  function HeaderPERF_L63($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderVHO_L63($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetWidths(array(73,18,18,25,30,30,30,23,20,10));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','L'));
    $pdfObject->ln();
    $y=$pdfObject->GetY();
    $backup=$pdfObject->PageBreakTrigger;
    $pdfObject->PageBreakTrigger=210;
    $pdfObject->SetXY(0,200);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->MultiCell(297,$pdfObject->rowHeight,vertaalTekst("Koersen met een * zijn meer dan",$pdfObject->rapport_taal)." ".$pdfObject->koersenMaxDagen." ".vertaalTekst("dagen oud.",$pdfObject->rapport_taal),0,'C');
    $pdfObject->PageBreakTrigger=$backup;
    $pdfObject->SetXY($pdfObject->marge,$y);

	}

	function HeaderTRANS_L63($object)
	{
    $pdfObject = &$object;
    if(isset($pdfObject->fillCell))
    {
      $fillBackup=$pdfObject->fillCell;
      unset($pdfObject->fillCell);
		}
   	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'FD');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    
     $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal/\nBedrag",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Prijs",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta-\nkoers",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag",$pdfObject->rapport_taal),
										 vertaalTekst("Rente",$pdfObject->rapport_taal),
										 vertaalTekst("Historische Kostprijs (".$pdfObject->rapportageValuta.")",$pdfObject->rapport_taal),
                     vertaalTekst("Rendement voorafgaande verslagperiode",$pdfObject->rapport_taal),
                     vertaalTekst("Rendement in verslagperiode",$pdfObject->rapport_taal),
                     vertaalTekst("%",$pdfObject->rapport_taal)
										 ));
         $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);        
    if(isset($fillBackup))
      $pdfObject->fillCell=$fillBackup;
	}
  
	function HeaderMUT_L63($object)
	{
    $pdfObject = &$object;
    if(isset($pdfObject->fillCell))
    {
      $fillBackup=$pdfObject->fillCell;
      unset($pdfObject->fillCell);
		}
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
   	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'FD');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    $pdfObject->CellBorders=array();
 		$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal/\nBedrag",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Prijs",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta-\nkoers",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag debet",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag credit",$pdfObject->rapport_taal)
										 ));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                   
    $pdfObject->Ln();
    if(isset($fillBackup))
      $pdfObject->fillCell=$fillBackup;
	}

  
  function HeaderVOLK_L63($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetWidths(array(73,18,18,25,30,30,30,23,20,10));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','L'));
    $pdfObject->ln();
    $y=$pdfObject->GetY();
    $backup=$pdfObject->PageBreakTrigger;
    $pdfObject->PageBreakTrigger=210;
    $pdfObject->SetXY(0,200);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->MultiCell(297,$pdfObject->rowHeight,vertaalTekst("Koersen met een * zijn meer dan",$pdfObject->rapport_taal)." ".$pdfObject->koersenMaxDagen." ".vertaalTekst("dagen oud.",$pdfObject->rapport_taal),0,'C');
		$pdfObject->PageBreakTrigger=$backup;
    $pdfObject->SetXY($pdfObject->marge,$y);

	}
  
  function HeaderAFM_L63($object)
	{
    $pdfObject = &$object;
 //   $pdfObject->HeaderOIB();
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
    $pdfObject->SetX($pdfObject->marge+$lijn1+5);
	  $pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");

	  $pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

	  $pdfObject->SetWidths($pdfObject->widthA);
	  $pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("AFM categorie",$pdfObject->rapport_taal),
											vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
											vertaalTekst("in valuta",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));
 		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());                     
       
      
	}

  function HeaderOIB_L63($object)
	{
    $pdfObject = &$object;
    if($pdfObject->rapport_titel == "Onderverdeling in AFM categorien")
    {
      $pdfObject->HeaderOIB();
    }
    else
    {
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->widthA = array(30,30,30,25,30,25,25,25,25,30);//,23,23
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
		$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Beginvermogen",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Stortingen en\nonttrekkingen",$pdfObject->rapport_taal)."",
											vertaalTekst("Gerealiseerd\nresultaat",$pdfObject->rapport_taal)."",
											vertaalTekst("Ongerealiseerd\nresultaat",$pdfObject->rapport_taal)."",
											vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Opgelopenrente",$pdfObject->rapport_taal)."\n ",
											vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
											vertaalTekst("Eindvermogen",$pdfObject->rapport_taal)."\n "));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
    unset($pdfObject->fillCell);
    }

	}




?>
