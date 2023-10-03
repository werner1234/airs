<?php

function Header_basis_L12($object)
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
  	//if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	//	$pdfObject->customPageNo = 0;

		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if(isset($pdfObject->__appvar['consolidatie']))
		{
  		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		}

		if($pdfObject->checkRappNaam==false)
		{
			$db = new DB();
			$query = "SHOW COLUMNS FROM CRM_naw like 'RappNaam'";
			$db->SQL($query);
			$db->query();
			if ($db->records() > 0)
			{
				$pdfObject->checkRappNaam = true;
			}
			else
			{
				$pdfObject->checkRappNaam = false;
			}
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
		  if(trim($pdfObject->rapport_naam2)<>'')
        $pdfObject->rapport_koptext = str_replace("{Naam2}", "\n".$pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		  else
        $pdfObject->rapport_koptext = str_replace("{Naam2}","",$pdfObject->rapport_koptext);
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
      $pdfObject->SetTextColor($pdfObject->rapport_header_fontcolor['r'],$pdfObject->rapport_header_fontcolor['g'],$pdfObject->rapport_header_fontcolor['b']);
   // if($pdfObject->rapport_type == 'OIH')
      $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
		  $logoWidth=92;
      $logopos=($pdfObject->w/2)-($logoWidth/2);
			  $pdfObject->Image($pdfObject->rapport_logo, $logopos,20, $logoWidth); #2800X200
		}

  		$pdfObject->AutoPageBreak=false;
			if ($pdfObject->rapport_type != "FACTUUR")
			{
    
				//
				$pdfObject->SetY(-10);
				$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
				$pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
        $pdfObject->SetY(-10);
				$pdfObject->Cell(297-$pdfObject->marge*2,4,$pdfObject->customPageNo,0,0,'R');//vertaalTekst("Pagina",$pdfObject->rapport_taal)." "
        $pdfObject->SetXY($pdfObject->marge,-6);
      //  $pdfObject->MultiCell(40,4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');//"\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal).
        
        $pdfObject->MultiCell(60,4,vertaalTekst("Datum opmaak",$pdfObject->rapport_taal).' '.date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y"),0,'L');
        //
        
      }

      $pdfObject->AutoPageBreak=true;
	    $pdfObject->SetXY(100,$y+22);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
     // $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
      //$pdfObject->SetTextColor($pdfObject->rapport_header_fontcolor['r'],$pdfObject->rapport_header_fontcolor['g'],$pdfObject->rapport_header_fontcolor['b']);
/*
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->setXY($pdfObject->w-$pdfObject->marge-40,8);
      $pdfObject->MultiCell(40,4,"\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
  */
      $pdfObject->SetY($y+25);
    $pdfObject->headerStart = $y+16;

		}

}
	function HeaderVKM_L12($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
function HeaderVKMD_L12($object)
{
  $pdfObject = &$object;
  //$pdfObject->HeaderVKM();
}

function HeaderVKMS_L12($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
 // $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
//  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 8, 'F');
 // $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
 // $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 // $pdfObject->ln(2);
 //   $pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->vkmsVanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->vkmsVanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->vkmsVanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
  $pdfObject->ln(2);
}

	  function HeaderOIH_L12($object)
	  {
	    $pdfObject = &$object;
	    if($pdfObject->widthsDefault)
	      $oldWidths=$pdfObject->widths;
	    $pdfObject->CellBorders = array();
      if(isset($pdfObject->CellFontStyle))
      {
        $CellFontStyle=$pdfObject->CellFontStyle;
        unset($pdfObject->CellFontStyle);
      }

			$pdfObject->ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
			$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
			$pdfObject->SetTextColor(255,255,255);

			$yStart=$pdfObject->getY();
			$pdfObject->setWidths($pdfObject->widthA);
			$pdfObject->setAligns($pdfObject->alignA);
			$pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 14, 'F');
			$pdfObject->ln(1.5);


	    if($pdfObject->rapport_deel == 'overzicht')
	    {
	 	  $pdfObject->SetWidths(array(60,22,25,25,25,25,25,25,25,24));
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
      $pdfObject->Row($pdfObject->rapport_header1);
      $pdfObject->fillCell = array();
	    $pdfObject->CellBorders = array();
	    $pdfObject->Row(array(''));
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
	    if(is_array($oldWidths))
	      $pdfObject->widths=$oldWidths;
	    }
    
      if(isset($CellFontStyle))
        $pdfObject->CellFontStyle=$CellFontStyle;
	  }

	 function HeaderATT_L12($object)
   {
    $pdfObject = &$object;
    $pdfObject->ln();
     $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8, 'F');
     $pdfObject->SetTextColor(255,255,255);
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

function HeaderAFM_L12($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  $dataWidth=array(65,25,15,30,30,10,25,30,20,20);
  
  $dataWidth=array(50,20,12,21,21,2,21,21,12,2,21,21,2,21,21,15);
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
                    '',//vertaalTekst("Directe opbrengst",$pdfObject->rapport_taal),
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


function HeaderOIV_L12($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor(255,255,255);
  
  $yStart=$pdfObject->getY();

  $pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 6, 'F');
  $pdfObject->ln(1.5);
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  
  if($pdfObject->rapportageValuta=='' || $pdfObject->rapportageValuta=='EUR')
    $valuta='euro';
  else
    $valuta=$pdfObject->rapportageValuta;
  
    $pdfObject->row(array(vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
                 vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
                 vertaalTekst("In valuta",$pdfObject->rapport_taal),
                 vertaalTekst("in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("in ".$valuta,$pdfObject->rapport_taal),
                 vertaalTekst("in %",$pdfObject->rapport_taal)));
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $pdfObject->headerStart = 50;
  
}

  function HeaderOIS_L12($object)
  {
    $pdfObject = &$object;
    // achtergrond kleur
    $pdfObject->ln();
    $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor(255,255,255);
  
    $yStart=$pdfObject->getY();
    $pdfObject->setWidths($pdfObject->widthA);
    $pdfObject->setAligns($pdfObject->alignA);
    $pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 6, 'F');
    $pdfObject->ln(1.5);
    if($pdfObject->rapportageValuta=='' || $pdfObject->rapportageValuta=='EUR')
      $valuta='euro';
    else
      $valuta=$pdfObject->rapportageValuta;
    //$pdfObject->CellFontStyle=array('','','','','','','','','',array($pdfObject->pdf->rapport_font,'B',$pdfObject->rapport_fontsize)); hele regel is al bold
    $pdfObject->row(array("",
                      date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),
                      '',
                      '',
                      vertaalTekst("Aantal",$pdfObject->rapport_taal),
                      vertaalTekst("Koers",$pdfObject->rapport_taal),
                      vertaalTekst("Valuta",$pdfObject->rapport_taal),
                      "",
                      vertaalTekst("Waarde in ".$valuta,$pdfObject->rapport_taal),
                      vertaalTekst("%",$pdfObject->rapport_taal)));
    //unset($pdfObject->CellFontStyle);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->ln();
    
  }

function HeaderOIB_L12($object)
{
  $pdfObject = &$object;
  // achtergrond kleur
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor(255,255,255);
  
  $yStart=$pdfObject->getY();
  $pdfObject->setWidths($pdfObject->widthA);
  $pdfObject->setAligns($pdfObject->alignA);
  $pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 6, 'F');
  $pdfObject->ln(1.5);
  if($pdfObject->rapportageValuta=='' || $pdfObject->rapportageValuta=='EUR')
    $valuta='euro';
  else
    $valuta=$pdfObject->rapportageValuta;
  $pdfObject->row(array("",date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),'',
                    '',
                    vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "",
                    vertaalTekst("Waarde in ".$valuta,$pdfObject->rapport_taal),
                    vertaalTekst("%",$pdfObject->rapport_taal),
                    vertaalTekst("Bewaarder",$pdfObject->rapport_taal)));
  
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  
}

  function HeaderGRAFIEK_L12($object)
  {
    $pdfObject = &$object;
    $pdfObject->ln();
  }

  function HeaderSCENARIO_L12($object)
  {
  	$pdfObject = &$object;
  }



  function HeaderVHO_L12($object)
  {
  	$pdfObject = &$object;



		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor(255,255,255);
		$pdfObject->setDrawColor(255,255,255);

		$yStart=$pdfObject->getY();
		$pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 11, 'F');
		$pdfObject->ln(1.5);



		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] +  $pdfObject->widthB[14];
		$eindresultaat2 = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] ;

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
/*
		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige+5);


		if($pdfObject->rapport_VHO_volgorde_beginwaarde == 0)
				$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		else
				$pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		if($pdfObject->rapport_VHO_volgorde_beginwaarde == 0)
				$pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
		else
				$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");

		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(70,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
*/
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);


		$y = $pdfObject->getY();

		$pdfObject->row(array("",
                      date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),//"\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
									 vertaalTekst("Aantal",$pdfObject->rapport_taal),
									 vertaalTekst("Historische koers",$pdfObject->rapport_taal),
									 vertaalTekst("Kostprijs in valuta ",$pdfObject->rapport_taal),
									 vertaalTekst("Kostprijs in ".($pdfObject->rapportageValuta=='EUR'?'euro':$pdfObject->rapportageValuta),$pdfObject->rapport_taal),
									 "",
									 vertaalTekst("Actuele koers",$pdfObject->rapport_taal),
									 vertaalTekst("Actuele waarde in valuta",$pdfObject->rapport_taal),
									 vertaalTekst("Actuele waarde in ".($pdfObject->rapportageValuta=='EUR'?'euro':$pdfObject->rapportageValuta),$pdfObject->rapport_taal),
									 '',
									 vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
									 vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
									 vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal).'*',
									 vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->AutoPageBreak=false;
		$lastY=$pdfObject->getY();
		$pdfObject->setXY($pdfObject->w-108,-10);
		$pdfObject->SetTextColor($pdfObject->rapport_header_fontcolor['r'],$pdfObject->rapport_header_fontcolor['g'],$pdfObject->rapport_header_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
		$pdfObject->MultiCell(90,4, vertaalTekst("* = O.a. dividend, coupon, rente en dergelijke",$pdfObject->rapport_taal), 0, "R");
		$pdfObject->AutoPageBreak=true;
		$pdfObject->setXY($pdfObject->marge,$lastY);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	  $pdfObject->ln(2);


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

  }
  
  function HeaderPERFG_L12($object)
  {
    $pdfObject = &$object;
		$pdfObject->ln();
  }

function HeaderPERFD_L12($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 280, 8 , 'F');
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+8,$pdfObject->marge + 280,$pdfObject->GetY()+8);
}


function HeaderVOLK_L12($object)
{
	$pdfObject = &$object;



	$pdfObject->ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetTextColor(255,255,255);
	$pdfObject->setDrawColor(255,255,255);

	$yStart=$pdfObject->getY();
	$pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 11, 'F');
	$pdfObject->ln(1.5);

	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
		
		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
		
		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13];

	// lijntjes onder beginwaarde in het lopende jaar
  /*
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
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
	*/
	
	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);

	$pdfObject->setX($pdfObject->marge+$pdfObject->widthB[0]+$pdfObject->widthB[1]-10);
	$pdfObject->Cell($pdfObject->widthB[1],4,vertaalTekst("Bewaarder",$pdfObject->rapport_taal),null,null,null,null,null);
	$pdfObject->setX($pdfObject->marge);
  
  if($pdfObject->rapportageValuta=='' || $pdfObject->rapportageValuta=='EUR')
    $valuta='euro';
  else
    $valuta=$pdfObject->rapportageValuta;
	
	$pdfObject->row(array("",
                    date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),//"\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
								 vertaalTekst("Aantal",$pdfObject->rapport_taal),
								 vertaalTekst("Begin koers",$pdfObject->rapport_taal),
								 vertaalTekst("Beginwaarde in valuta",$pdfObject->rapport_taal),
								 vertaalTekst("Beginwaarde in ".$valuta,$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Actuele koers",$pdfObject->rapport_taal),
								 vertaalTekst("Actuele waarde in valuta",$pdfObject->rapport_taal),
								 vertaalTekst("Actuele waarde in ".$valuta,$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
								 vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                 vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
								 vertaalTekst("in %",$pdfObject->rapport_taal))
		);

	

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->ln();
	
//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
//	$pdfObject->ln();
}


function HeaderVOLKD_L12($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	//$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-($pdfObject->marge*2), 6 , 'F');
  

  $pdfObject->SetTextColor(255,255,255);
  $pdfObject->setDrawColor(255,255,255);
  
	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
	$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

	$actueel 			= $eindhuidige + $pdfObject->widthB[6];
	$eindactueel 	= array_sum($pdfObject->widthB);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->ln(1);
	$pdfObject->row(array("",date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),
										'',
										vertaalTekst("Bewaarder",$pdfObject->rapport_taal),
										vertaalTekst("ISIN",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("In %",$pdfObject->rapport_taal)));

	$pdfObject->ln(2);
	//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}

  function HeaderHSE_L12($object)
  {
    $pdfObject = &$object;
		$pdfObject->ln();
  }

  function HeaderTRANS_L12($object)
  {
    $pdfObject = &$object;
    if(isset($pdfObject->CellFontColor))
    {
      $CellFontColor=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
    }
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->marge);
  //  $pdfObject->ln();

    if($pdfObject->rapportageValuta=='' || $pdfObject->rapportageValuta=='EUR')
      $valuta='euro';
    else
      $valuta=$pdfObject->rapportageValuta;
    
		$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
    $pdfObject->SetTextColor(255,255,255);
    
    $pdfObject->MultiCell(150,4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
    $pdfObject->ln(-4);
    $pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array('',//vertaalTekst("Datum",$pdfObject->rapport_taal),
                      '',//vertaalTekst("Aan /\nver-\nkopen",$pdfObject->rapport_taal),
                      '',//vertaalTekst("Aantal",$pdfObject->rapport_taal),
                      '',//vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in\n".$valuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in\n".$valuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$valuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat",$pdfObject->rapport_taal)."*"));
     $pdfObject->ln(1);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    if(isset($CellFontColor))
    {
      $pdfObject->CellFontColor=$CellFontColor;
    }
  }

  function HeaderMUT_L12($object)
  {
    $pdfObject = &$object;
    if(isset($pdfObject->CellFontColor))
    {
      $CellFontColor=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
    }
    /*
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
		$pdfObject->SetX($pdfObject->marge);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
	  */
    //
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 7 , 'F');


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1.5);
    $pdfObject->SetTextColor(255,255,255);
		$pdfObject->row(array('',
										 '',
										 '',//,vertaalTekst("Omschrijving",$pdfObject->rapport_taal)
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("Af",$pdfObject->rapport_taal),
										 vertaalTekst("Bij",$pdfObject->rapport_taal),
										 ""));
    $pdfObject->ln($pdfObject->rowHeight*-1);
    $pdfObject->MultiCell(100,4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
    
    $pdfObject->ln(-2);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    if(isset($CellFontColor))
    {
      $pdfObject->CellFontColor=$CellFontColor;
    }

  }

function HeaderOIR_L12($object)
{
  $pdfObject = &$object;
  // achtergrond kleur
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor(255,255,255);

  $yStart=$pdfObject->getY();
  
  $pdfObject->Rect($pdfObject->marge, $yStart, $pdfObject->widthA[0], 8, 'F');
  $pdfObject->Rect($pdfObject->marge+$pdfObject->widthA[0]+$pdfObject->widthA[1], $yStart, 297-($pdfObject->marge*2+$pdfObject->widthA[0]+$pdfObject->widthA[1]), 8, 'F');
  $pdfObject->ln(2);
  $header=array('% Sectoren','','Fondsomschrijving','Aantal','Koers in Valuta','Waarde in Euro','In %');
  $pdfObject->setWidths($pdfObject->widthA);
  $pdfObject->setAligns($pdfObject->alignA);
  $pdfObject->row($header);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
}

function HeaderRISK_L12($object)
{
  $pdfObject = &$object;
}

function HeaderKERNV_L12($object)
{
  $pdfObject = &$object;
  // achtergrond kleur
  $pdfObject->ln(22);
	/*
	$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  */
 // $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
}

function HeaderHUIS_L12($object)
{
	$pdfObject = &$object;
	// achtergrond kleur
	$pdfObject->ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	$pdfObject->SetTextColor(255,255,255);

	$blokSymetrisch=(297-$pdfObject->marge*2)/3-(10/3);
	//$yStart=$pdfObject->getY();
	$pdfObject->widthsA=array();
	//echo $yStart;exit;
  $derdeKleiner=40;
  
  $pdfObject->blokken=array();
	$yStart=53;
  $xStart=$pdfObject->marge;
	for($i=0;$i<3;$i++)
	{
    if($i==2)
    {
      $blok = $blokSymetrisch - $derdeKleiner;
    }
    else
    {
      $blok=$blokSymetrisch+($derdeKleiner/2);
    }
    $pdfObject->blokken[]=$blok;
		$pdfObject->Rect($xStart, $yStart, $blok, 6, 'F');
		$pdfObject->SetXY($xStart,$yStart+1);
    $xStart+=($blok+5);
		if($i==0)
		{
			//$pdfObject->widthsA[]=$blok;
			//$pdfObject->widthsA[]=5;
			$pdfObject->Cell($blok, 4, "Portefeuille", 0,0, "C");
		}
		elseif($i==1)
		{
			$pdfObject->Cell($blok, 4, "Bandbreedtes", 0, 0,"C");
		}
		elseif($i==2)
		{
			$pdfObject->Cell($blok, 4, "Conform portefeuilleprofiel", 0, 0,"C");
		}

		//if($i>0)
		//{
			$pdfObject->widthsA[]=$blok/3;
			$pdfObject->widthsA[]=$blok/3;
			$pdfObject->widthsA[]=$blok/3+5;
		//}
	}
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}


function HeaderVAR_L12($object)
{
  $pdfObject = &$object;
  // achtergrond kleur
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor(255,255,255);
  
  $blok=(297-$pdfObject->marge*2)/3-(10/3);
  //$yStart=$pdfObject->getY();
  $pdfObject->widthsA=array();
  //echo $yStart;exit;
  $yStart=53;
  for($i=0;$i<3;$i++)
  {
    $xStart=$pdfObject->marge+$i*($blok+5);
    $pdfObject->Rect($xStart, $yStart, $blok, 6, 'F');
    $pdfObject->SetXY($xStart,$yStart+1);
    if($i==0)
    {
      $pdfObject->widthsA[]=$blok;
      $pdfObject->widthsA[]=5;
    }
    elseif($i==1)
    {
      $pdfObject->Cell($blok, 4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)
                            . " tot en met " .
                            date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)
        , 0, "L");
    }
    elseif($i==2)
    {
      $pdfObject->Cell($blok, 4, date("j",$pdfObject->tweedePerformanceStart)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->tweedePerformanceStart)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->tweedePerformanceStart)
                            . " tot en met " .
                            date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)
        , 0, "L");
    }
    
    if($i>0)
    {
      $pdfObject->widthsA[]=$blok/3;
      $pdfObject->widthsA[]=$blok/3;
      $pdfObject->widthsA[]=$blok/3+5;
    }
  }
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}


function HeaderKERNZ_L12($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  /*
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  $pdfObject->ln(-4);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  */
}

function HeaderINDEX_L12($object)
{
  $pdfObject = &$object;

  // achtergrond kleur
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor(255,255,255);
  
  $blok=(297-$pdfObject->marge*2)/3-(10/3);
  $blokBackup=$blok;
  $yStart=$pdfObject->getY();
  $pdfObject->widthsA=array();
  $xStart=$pdfObject->marge;
  for($i=0;$i<3;$i++)
  {
    if($i==0)
    {
      $blok = $blokBackup - 16;
    }
    else
    {
      $blok = $blokBackup + 8;
      if($i==1)
        $xStart+=$blokBackup - 16+5;
      else
        $xStart+=$blokBackup + 8+5;
    }
   
    $pdfObject->Rect($xStart, $yStart, $blok, 7, 'F');
    $pdfObject->SetXY($xStart,$yStart+1.5);
    if($i==0)
    {
      $pdfObject->widthsA[]=$blok;
      $pdfObject->widthsA[]=5;
      $pdfObject->Cell($blok, 4,'', 0, "L");//vertaalTekst('Portefeuille rendement en indices',$pdfObject->rapport_taal)
    }
    elseif($i==1)
    {
      $pdfObject->Cell($blok, 4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)
                       . " tot en met " .
                       date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)
                       , 0, "L");
    }
    elseif($i==2)
    {
      $pdfObject->Cell($blok, 4, date("j",$pdfObject->tweedePerformanceStart)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->tweedePerformanceStart)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->tweedePerformanceStart)
                       . " tot en met " .
                       date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)
                       , 0, "L");
    }
    
    if($i>0)
    {
      $pdfObject->widthsA[]=$blok/4;
      $pdfObject->widthsA[]=$blok/4;
      $pdfObject->widthsA[]=$blok/4;
      $pdfObject->widthsA[]=$blok/4;
      $pdfObject->widthsA[]=5;
    }
  }
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln(8);
  

}
    
  function printIndex($object)
  {
    global $__appvar;
    $rapportObject = &$object;
    $RapStartJaar = date("Y", db2jul($rapportObject->rapportageDatumVanaf));
	  if(db2jul($rapportObject->pdf->PortefeuilleStartdatum) > db2jul($rapportObject->rapportageDatumVanaf))
	    $rapportObject->tweedePerformanceStart = $rapportObject->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($rapportObject->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $rapportObject->tweedePerformanceStart = $rapportObject->pdf->PortefeuilleStartdatum;
	  else
	   $rapportObject->tweedePerformanceStart = "$RapStartJaar-01-01";

    $performance = performanceMeting($rapportObject->portefeuille, $rapportObject->rapportageDatumVanaf, $rapportObject->rapportageDatum, $rapportObject->pdf->portefeuilledata['PerformanceBerekening'],$rapportObject->pdf->rapportageValuta);


	    $extraBreedte=70-90;
	    $performanceJaar = performanceMeting($rapportObject->portefeuille, $rapportObject->tweedePerformanceStart, $rapportObject->rapportageDatum, $rapportObject->pdf->portefeuilledata['PerformanceBerekening'],$rapportObject->pdf->rapportageValuta);
	  
    $klein=false;
	  $DB=new DB();
	  $perioden=array('jan'=>$rapportObject->tweedePerformanceStart,'begin'=>$rapportObject->rapportageDatumVanaf,'eind'=>$rapportObject->rapportageDatum);

    $categorien[] = 'Totaal';
   // $index=new indexHerberekening();
    $DB = new DB();
	  foreach ($perioden as $periode=>$datum)
	  {


		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$datum' AND ".
						 " portefeuille = '".$rapportObject->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$dbwaarde = $DB->nextRecord();
		$totaalWaarde[$periode] = $dbwaarde['totaal'];

	    if(($klein == false) || ($klein == true && $periode !='jan'))
	    {
	   //   $rendamentWaarden = $index->getWaardenATT('2005-01-01' ,$datum ,$rapportObject->portefeuille,$categorien);
	    //  $rendamentWaarden = $index->getWaardenATT($rapportObject->tweedePerformanceStart ,$datum ,$rapportObject->portefeuille,$categorien);
     //   $portefeuilleIndex[$periode]=$rendamentWaarden[count($rendamentWaarden)-1]['index'];
	    }
	  }

	  $query = "SELECT
Indices.Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta,
BeleggingscategoriePerFonds.Beleggingscategorie,
CategorienPerHoofdcategorie.Hoofdcategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
Indices
JOIN Fondsen ON Indices.Beursindex = Fondsen.Fonds
INNER JOIN BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$rapportObject->pdf->portefeuilledata['Vermogensbeheerder']."' 
INNER JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$rapportObject->pdf->portefeuilledata['Vermogensbeheerder']."' 
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE Indices.Vermogensbeheerder = '".$rapportObject->pdf->portefeuilledata['Vermogensbeheerder']."' 
ORDER BY Beleggingscategorien.Afdrukvolgorde,Indices.Afdrukvolgorde
";
		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
		$categorieAantal=0;
		$lastCategorie='';
	  while($index = $DB->nextRecord())
		{
		  if($index['benchmark'] == '')
		    $index['benchmark']='rest';
			if($index['categorieOmschrijving'] <> $lastCategorie)
			  $categorieAantal++;
			$lastCategorie=$index['categorieOmschrijving'];

		  $benchmarkCategorie[$index['benchmark']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$rapportObject->getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}

		$query = "SELECT TijdelijkeRapportage.valuta,Valutas.Omschrijving, Valutas.Afdrukvolgorde FROM TijdelijkeRapportage Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$rapportObject->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$rapportObject->pdf->rapportageValuta."' GROUP BY Valuta ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum)/getValutaKoers($rapportObject->pdf->rapportageValuta,$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_jan'])    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_begin']) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/100 );
		}
    if(count($indexValuta) > 0)
      $aantalValuta=count($indexValuta);
    else
      $aantalValuta=0;




		$regels = count($indexData)+$aantalValuta;
		$hoogte = ($regels+$categorieAantal) * 4;
		if(($rapportObject->pdf->GetY() + $hoogte +16) > $rapportObject->pdf->pagebreak)
		{
			$rapportObject->pdf->AddPage();
			$rapportObject->pdf->ln();
		}

		$blokken=array(3,count($indexData)+$categorieAantal,$aantalValuta+1);
		$y=$rapportObject->pdf->getY();
    foreach ($blokken as $regels)
    {
      if($regels > 0)
      {
        $hoogte=($regels)*4;//+4;
		    $rapportObject->pdf->SetFillColor($rapportObject->pdf->rapport_kop_bgcolor['r'],$rapportObject->pdf->rapport_kop_bgcolor['g'],$rapportObject->pdf->rapport_kop_bgcolor['b']);
		    $rapportObject->pdf->Rect($rapportObject->pdf->marge,$y,160+$extraBreedte,$hoogte,'F');
		    $rapportObject->pdf->SetFillColor(0);
		    $rapportObject->pdf->Rect($rapportObject->pdf->marge,$y,160+$extraBreedte,$hoogte);
		    $y+=($hoogte);
      }
    }
    $rapportObject->pdf->SetX($rapportObject->pdf->marge);
  	$rapportObject->pdf->SetWidths(array(60,40,40));
  	$rapportObject->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
  	$rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);

  	if($klein==true)
  	{
  	  $rapportObject->pdf->SetWidths(array(60,40));
  	  $headerRow=array("","Rendement\nverslagperiode");
      $rapportObject->pdf->row($headerRow);
  	}
    else
    {
      $rapportObject->pdf->SetWidths(array(60,40,40));
      $headerRow=array("","Rendement\nverslagperiode","Rendement\nlopende jaar");
 	    $rapportObject->pdf->row($headerRow);
    }
    


  	if($klein==true)
    {
      $rapportObject->pdf->row(array("Portefeuille"));
      $rapportObject->pdf->ln(-4);
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      $rapportObject->pdf->row(array("",$rapportObject->formatGetal($performance,2)." %"));
    }
    else
    {
      $rapportObject->pdf->row(array("Portefeuille"));
      $rapportObject->pdf->ln(-4);
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      $rapportObject->pdf->row(array("",$rapportObject->formatGetal($performance,2)." %",$rapportObject->formatGetal($performanceJaar,2)." %"));
    }

  //	 $rapportObject->pdf->ln();


    //$headerRow[0]="\nIndexvergelijking";
    //$rapportObject->pdf->row(array("Indexvergelijking"));


		$lastCategorie='';
    foreach ($indexData as $fonds=>$fondsData)
    {
			if($fondsData['categorieOmschrijving'] <> $lastCategorie)
			{
				$rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);
				$rapportObject->pdf->row(array($fondsData['categorieOmschrijving'].' indices in euro'));
				$rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
			}
//listarray($fondsData);

			$fondsData['Omschrijving']=str_replace('- EUR','',$fondsData['Omschrijving']);

			$lastCategorie=$fondsData['categorieOmschrijving'];
      if($klein==true)
        $rapportObject->pdf->row(array($fondsData['Omschrijving'],$rapportObject->formatGetal($fondsData['performance'],2)." %"));
      else
        $rapportObject->pdf->row(array($fondsData['Omschrijving'],$rapportObject->formatGetal($fondsData['performance'],2)." %",$rapportObject->formatGetal($fondsData['performanceJaar'],2)." %"));
    }

              //$rapportObject->pdf->ValutaKoersStart
//$rapportObject->pdf->ValutaKoersBegin
//$rapportObject->pdf->ValutaKoersEind

    $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'B',$rapportObject->pdf->rapport_fontsize);

   // $rapportObject->pdf->ln();



    if(count($indexValuta) > 0)
    {
      //$headerRow[0]="\nValutavergelijking";
      $rapportObject->pdf->row(array("Valutavergelijking"));
      $rapportObject->pdf->SetFont($rapportObject->pdf->rapport_font,'',$rapportObject->pdf->rapport_fontsize);
      foreach ($indexValuta as $fonds=>$valutaData)
      {
        /*
              if($klein==true)
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['valutaKoers_begin']/$rapportObject->pdf->ValutaKoersBegin,4),$rapportObject->formatGetal($valutaData['valutaKoers_eind']/$rapportObject->pdf->ValutaKoersEind,4),$rapportObject->formatGetal($valutaData['performance'],2)));
        else
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['valutaKoers_jan']/$rapportObject->pdf->ValutaKoersStart,4),$rapportObject->formatGetal($valutaData['valutaKoers_begin']/$rapportObject->pdf->ValutaKoersBegin,4),$rapportObject->formatGetal($valutaData['valutaKoers_eind']/$rapportObject->pdf->ValutaKoersEind,4),$rapportObject->formatGetal($valutaData['performance'],2),$rapportObject->formatGetal($valutaData['performanceJaar'],2)));

          */
        if($klein==true)
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['performance'],2)." %"));
        else
          $rapportObject->pdf->row(array($valutaData['Omschrijving'],$rapportObject->formatGetal($valutaData['performance'],2)." %",$rapportObject->formatGetal($valutaData['performanceJaar'],2)." %"));
      }
    }
		//listarray($indexValuta);
  }



if(!function_exists('PieChart'))
{
	function PieChart($object, $w, $h, $data, $format, $colors = null)
	{
		$pdfObject = &$object;


		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->SetLegends($data, $format);

		$XPage = $pdfObject->GetX();
		$YPage = $pdfObject->GetY();
		$margin = 2;
		$hLegend = 2;
		$radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if ($colors == null)
		{
			for ($i = 0; $i < $pdfObject->NbVal; $i++)
			{
				$gray = $i * intval(255 / $pdfObject->NbVal);
				$colors[$i] = array($gray, $gray, $gray);
			}
		}

		//Sectors
		$pdfObject->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach ($data as $val)
		{
			$angle = floor(($val * 360) / doubleval($pdfObject->sum));
			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$pdfObject->setDrawColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

		$x1 = $XPage + $w + $radius * .5;
		$x2 = $x1 + $hLegend + $margin - 12;
		$y1 = $YDiag - ($radius) + $margin;

		for ($i = 0; $i < $pdfObject->NbVal; $i++)
		{
			$pdfObject->setDrawColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
			$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
			$pdfObject->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
			$pdfObject->SetXY($x2, $y1);
			$pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
			$y1 += $hLegend + $margin;
		}
	}
  
  
  
  if(!function_exists('PieChart_L12'))
  {
    function PieChart_L12($pdfObject,$w,$h,$data, $format='', $colors=null,$titel='',$legendaStart='')
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
      
      if(substr($format,0,2)=='%-')
      {
        $i=0;
        foreach($data as $key=>$val)
        {
          $pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
          $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
          $pdfObject->SetXY($x2, $y1);
          $pdfObject->Cell(substr($format,2), $hLegend, $key);
          $pdfObject->Cell(6, $hLegend, number_format($val,1,',','.').'%',0,0,'R');
          $y1 += $hLegend * 2;
          $i++;
        }
      }
      else
      {
        for ($i = 0; $i < $pdfObject->NbVal; $i++)
        {
          $pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
          $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
          $pdfObject->SetXY($x2, $y1);
          $pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
          $y1 += $hLegend * 2;
        }
      }
      $pdfObject->SetDrawColor(0,0,0);
      $pdfObject->SetFillColor(0,0,0);
    }
  }
}

?>