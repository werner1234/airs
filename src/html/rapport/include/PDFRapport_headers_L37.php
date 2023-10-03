<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.12 $

 		$Log: PDFRapport_headers_L37.php,v $
 		Revision 1.12  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/07/08 07:03:49  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/07/04 16:05:11  rvv
 		*** empty log message ***

 		Revision 1.8  2012/06/06 18:18:25  rvv
 		*** empty log message ***

 		Revision 1.7  2012/05/30 16:02:38  rvv
 		*** empty log message ***


*/
function Header_basis_L37($object)
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
      //if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;

  		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		  	$logopos = 45;
	  	else
		  	$logopos = 90;

	  	if(is_file($pdfObject->rapport_logo))
	  	{
	  	  $factor=0.08;
	  	  $xSize=1462*$factor;
	  	  $ySize=298*$factor;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize, $ySize);
		  }
		  if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			  $x = 60;
		  else
			  $x = 150;

		  $pdfObject->Line($pdfObject->marge,30,$x+140,30);
    }
    else
    {
    	//if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	  //	$pdfObject->customPageNo = 0;

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

		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old;

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
			$logopos = 45;
		else
			$logopos = 90;

		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
		  $factor=0.08;
		  $xSize=1462*$factor;
		  $ySize=298*$factor;
	    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize, $ySize);
		}

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 60;
		else
			$x = 150;

		$pdfObject->Line($pdfObject->marge,30,$x+140,30);
		$pdfObject->SetY($y);

		$pdfObject->SetWidths(array(40,10,200));
		$pdfObject->SetAligns(array('L','C','L'));
		$pdfObject->SetXY($pdfObject->marge,32);
	  $pdfObject->Row(array(vertaalTekst('Portefeuille',$pdfObject->rapport_taal),':',$pdfObject->portefeuilledata['Portefeuille']));// .' '. $pdfObject->rapport_naam2
	  $pdfObject->ln(2);


	  $pdfObject->Row(array(vertaalTekst("Depotbank",$pdfObject->rapport_taal),':',$pdfObject->portefeuilledata['DepotbankOmschrijving']));
		$pdfObject->SetXY($x,32);

		$rapPeriode=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst('t/m',$pdfObject->rapport_taal)." ".
	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
	  $rapDatum=date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y");

	 	$pdfObject->SetWidths(array(160,40,5,75));
		$pdfObject->SetAligns(array('L','L','C','R'));
		$pdfObject->SetXY($pdfObject->marge,32);

		$pdfObject->Row(array('',vertaalTekst("Rapportageperiode",$pdfObject->rapport_taal),':',$rapPeriode));
		$pdfObject->SetY(38);
		$pdfObject->Row(array('',vertaalTekst("Datum rapport",$pdfObject->rapport_taal),':',$rapDatum));

	//  $pdfObject->MultiCell(140,4,vertaalTekst(vertaalTekst("Rapportageperiode:",$pdfObject->rapport_taal)." "),0,'L');
	//	$pdfObject->SetXY($x,38);
	//  $pdfObject->MultiCell(140,4,vertaalTekst(vertaalTekst("Datum rapport:",''),0,'L');
	  $pdfObject->Line($pdfObject->marge,$pdfObject->getY()+3,$x+140,$pdfObject->getY()+3);
	  $pdfObject->SetXY(50,52);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
	  $pdfObject->MultiCell($x+50,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
//echo $pdfObject->rapport_type." ".$pdfObject->customPageNo." <br>\n";
}

	function HeaderVKM_L37($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	  function HeaderHSE_L37($object)
	  {
	    $pdfObject = &$object;

      $pdfObject->ln();
      $dataWidth=array(28,55,18,18,18,18,22,22,22,22,22);
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


  function HeaderOIS_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	    $pdfObject->SetWidths($pdfObject->widthB);
			$pdfObject->SetAligns($pdfObject->alignB);
			$pdfObject->ln();

			if($pdfObject->rapport_titel == "De 10 grootste posities per categorie" || $pdfObject->rapport_titel == "Waarde verdeling portefeuilles")
			{
        $pdfObject->SetWidths($pdfObject->widthA);
			}
			elseif($pdfObject->cat == "Liquiditeiten")
			{
			  $pdfObject->SetWidths($pdfObject->widthB);
			  $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
			  $pdfObject->row(array('',vertaalTekst("Rekening",$pdfObject->rapport_taal),vertaalTekst("Valuta",$pdfObject->rapport_taal),'','',vertaalTekst("Saldo",$pdfObject->rapport_taal)));
			  $pdfObject->CellBorders=array();
			}
			elseif($pdfObject->Hcat == "Risicomijdende beleggingen")
			{
			  if(isset($pdfObject->OISsettings['VARheader']))
        {
			  $pdfObject->SetWidths($pdfObject->widthB);
			  $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
			  $pdfObject->row(array('',
			  vertaalTekst('Titel',$pdfObject->rapport_taal),
			  vertaalTekst('Valuta',$pdfObject->rapport_taal),
			  vertaalTekst('Nominaal/Aantal',$pdfObject->rapport_taal),
			  vertaalTekst('Huidige koers',$pdfObject->rapport_taal),
			  vertaalTekst('Waarde in EUR',$pdfObject->rapport_taal),
			  vertaalTekst('Historische kostprijs',$pdfObject->rapport_taal),
			  vertaalTekst('Ongerealiseerd resultaat',$pdfObject->rapport_taal),
			  vertaalTekst('%',$pdfObject->rapport_taal),
			  vertaalTekst('YTM',$pdfObject->rapport_taal)));
			  $pdfObject->CellBorders=array();
        }
			}

      else
			{
        if(isset($pdfObject->OISsettings['ZAKheader']))
        {
			    $pdfObject->SetWidths($pdfObject->widthA);
			    $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
			    $pdfObject->row(array('',vertaalTekst('Titel',$pdfObject->rapport_taal),
			    vertaalTekst('Valuta',$pdfObject->rapport_taal),
			    vertaalTekst('Aantal',$pdfObject->rapport_taal),
			    vertaalTekst('Huidige koers',$pdfObject->rapport_taal),
			    vertaalTekst('Waarde in EUR',$pdfObject->rapport_taal),
			    vertaalTekst('Historische kostprijs',$pdfObject->rapport_taal),
			    vertaalTekst('Ongerealiseerd resultaat',$pdfObject->rapport_taal),
			    vertaalTekst('%',$pdfObject->rapport_taal)));
			    $pdfObject->CellBorders=array();
        }
			}


	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderPERFG_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->ln(8);
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderCASHY_L37($object)
	{
    $pdfObject = &$object;
	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->ln(2);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    $pdfObject->row(array('',vertaalTekst("Jaar",$pdfObject->rapport_taal),vertaalTekst("Lossing",$pdfObject->rapport_taal),vertaalTekst("Rente",$pdfObject->rapport_taal),vertaalTekst("Totaal",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());

	}

		function HeaderCASHYV_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetWidths($pdfObject->widthA);
	    $pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    	$pdfObject->ln();
    	if($pdfObject->templateVars['CASHYVPaginas'] =='')
    	{
    	  $pdfObject->SetWidths(array(25+50+25+25,50,15+50+25));
    	  $pdfObject->SetAligns(array('C','C','C'));
        $pdfObject->row(array( vertaalTekst("De komende twee jaar",$pdfObject->rapport_taal) ,'', vertaalTekst("Totalen per jaar",$pdfObject->rapport_taal)));
	      $pdfObject->SetWidths($pdfObject->widthA);
	      $pdfObject->SetAligns($pdfObject->alignA);
 		    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
 		    $pdfObject->Line($pdfObject->marge+ (25+50+25+25+50),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
 		    //$pdfObject->row(array('Datum',"Instrument","Coupon/Lossing","Bedrag",'','Jaar',"Lossing","Rente","Totaal"));

 		    $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
 		    vertaalTekst("Instrument",$pdfObject->rapport_taal),
 		    vertaalTekst("Coupon/Lossing",$pdfObject->rapport_taal),
 		    vertaalTekst("Bedrag",$pdfObject->rapport_taal),'',
 		    vertaalTekst("Jaar",$pdfObject->rapport_taal),
 		    vertaalTekst("Lossing",$pdfObject->rapport_taal),
 		    vertaalTekst("Rente",$pdfObject->rapport_taal),
 		    vertaalTekst("Totaal",$pdfObject->rapport_taal)));

 		    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
 		    $pdfObject->Line($pdfObject->marge+ (25+50+25+25+50),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
    	}
    	else
    	{
    	  $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),vertaalTekst("Instrument",$pdfObject->rapport_taal),vertaalTekst("Coupon/Lossing",$pdfObject->rapport_taal),vertaalTekst("Bedrag",$pdfObject->rapport_taal)));
    	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
    	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
    	}
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	}

  function HeaderFRONT_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	  function HeaderINDEX_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderEND_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderOIB_L37($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderATT_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderPERF_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderVHO_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(63,15,18,18,23, 20,  29,28,28,20,20));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 1)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
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
   $pdfObject->setXY($positie['fondsStart'],$y);

   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);

   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');


   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal)."   ",
		'',
		"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Gemiddelde",$pdfObject->rapport_taal)."   \n".vertaalTekst("aankoopwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal)."     ",
    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)."    "));
    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		unset($pdfObject->CellBorders);

	}

	function HeaderVOLK_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(63,15,18,18,23, 20,  29,28,28,20,20));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 1)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
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
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);
   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nNaam","\nValuta","\nAantal","\nKoers","Begin\nKoers",'',"\nMarktwaarde","\nBeginwaarde","Ongerealiseerd\nresultaat","%\nportf.","Opgelopen\nrente"));

		$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 vertaalTekst("Begin",$pdfObject->rapport_taal)."\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 '',
		"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Beginwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)."    "));

     unset($pdfObject->CellBorders);
	}

	function HeaderVOLKV_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(60,16,16,16,21,21,25, 5,  20,20,20,20,20));

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

    $pdfObject->SetAligns(array('L','L','L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));


				$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
		 vertaalTekst("Rating debiteur",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),'',
		 vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
		 vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
		 vertaalTekst("Macaulay",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
		  vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
		   vertaalTekst("%",$pdfObject->rapport_taal)."\n".vertaalTekst("port.",$pdfObject->rapport_taal)));


		unset($pdfObject->CellBorders);//"Modified\nduration",
	}

	function HeaderMUT_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(25,80,25,25,35, 20  ,30,30));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 2)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
       {
         $positie['midden'] = $positie['waardeStart'] ;
         $positie['midden'] -=$waarde/2;
       }
     }
     if($id < 10)
       $positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
    }
    foreach ($positie as $key=>$value)
      $positie[$key]+=$pdfObject->marge;

   $y=$pdfObject->GetY()+5;
    $pdfObject->pageTop=array($positie['midden'],$y+1);
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);

   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');


   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('R','L','R','R','R', 'R'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nDatum","\nOmschrijving","\nValuta","Valuta-\nkoers ","\nBedrag",'',"\nBedrag",''));

		$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		vertaalTekst("Valuta-",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),'',
		"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),''));

     unset($pdfObject->CellBorders);

	}

function HeaderTRANS_L37($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(15,15,20,80 ,25,25,25,  25,25,25,25));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 4)
       $positie['fondsStart'] +=$waarde;
     if($id < 7)
       $positie['fondsEind'] +=$waarde;
     if($id < 7)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==7)
       {
         $positie['midden'] = $positie['waardeStart'] ;
         $positie['midden'] -=$waarde/2;
       }
     }
     if($id < 10)
       $positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
    }
    foreach ($positie as $key=>$value)
      $positie[$key]+=$pdfObject->marge;

    if ($pdfObject->rapportageValuta == "EUR" )
	    $valuta = 'EURO';
	  else
	    $valuta = $pdfObject->rapportageValuta ;

   $y=$pdfObject->GetY()+5;
   $pdfObject->pageTop=array($positie['midden'],$y+1);
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);
   $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal),0,'C');

   /*
   			$object->SetX($inkoop);
//			$object->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$object->rapport_taal), 0,0, "C"); //60 ipv 65
			$object->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$object->rapport_taal), 0,0, "C");
			$object->SetX($verkoop);
//			$object->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$object->rapport_taal), 0,0, "C"); //60 ipv 65
			$object->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$object->rapport_taal), 0,0, "C");
			$object->SetX($resultaat);
//			$this->Cell(65,4, vertaalTekst("Resultaat bepaling",$this->rapport_taal), 0,0, "C"); //81 ipv 65
			$this->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$this->rapport_taal), 0,0, "C");
			$this->ln();
			*/

   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('R','L','R','L','R', 'R'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nDatum","\nOmschrijving","\nValuta","\nAantal","\nKoers","Valuta-\nkoers ","\nBedrag",'',"\nBedrag","Gerealiseerd\nresultaat   ","\nProvisie"));
		$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)
										));

    unset($pdfObject->CellBorders);
	}

	function HeaderMODEL_L37($object)
	{
    $pdfObject = &$object;

		$pdfObject->SetFont($pdfObject->rapport_font,"b",10);
		$pdfObject->Cell(70,4, "Modelportefeuille: ",0,0,"R");
		$pdfObject->SetFont($pdfObject->rapport_font,"",10);
		$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_portefeuille'],0,1,"L");
		$pdfObject->SetFont($pdfObject->rapport_font,"b",10);

		if($pdfObject->selectData[modelcontrole_rapport] == "vastbedrag")
		{
			$pdfObject->Cell(70,4, "Vast bedrag: ",0,0,"R");
			$pdfObject->SetFont($pdfObject->rapport_font,"",10);
			$pdfObject->Cell(50,4, $pdfObject->selectData[modelcontrole_vastbedrag],0,1,"L");
		}
		else
		{
			if($pdfObject->selectData["modelcontrole_filter"] != "gekoppeld")
				$extraTekst = " : niet gekoppeld depot";
			else
				$extraTekst = "";

			$pdfObject->Cell(70,4, "Client: ",0,0,"R");
			$pdfObject->SetFont($pdfObject->rapport_font,"",10);
			$pdfObject->Cell(50,4, $pdfObject->clientOmschrijving,0,1,"L");
		}

		$pdfObject->ln();
		//$pdfObject->SetWidths(array(60,25,25,25,25,25,25,10,27,25));
		//$pdfObject->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
		//$pdfObject->Row(array("Fonds","Model Percentage","Werkelijk Percentage","Grootste afwijking","Kopen","Verkopen","Overschrijding waarde EUR","","Waarde volgens percentage model","Koers in locale valuta"));
		$pdfObject->SetWidths(array(28,60,25,15,21,25,25,25,25,25,25));
		$pdfObject->SetAligns(array("L","L","R","R","R","R","R","R","R","R","R","R","R"));
		$pdfObject->Row(array("ISIN Code","Fonds","Werkelijke waarde", "in %","Model Percentage","Afwijking","Aantal kopen","Waarde kopen","Aantal verkopen","Waarde verkopen"));

		$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
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


if(!function_exists('getTypeGrafiekData'))
{
	function getTypeGrafiekData($object, $type, $extraWhere = '', $items = array())
	{
		global $__appvar;
		$DB = new DB();
		if (!is_array($object->pdf->grafiekKleuren))
		{
			$q = "SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $object->pdf->portefeuilledata['Vermogensbeheerder'] . "'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$object->pdf->grafiekKleuren = $kleuren;
		}
		$kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'Regio' => 'OIR', 'Beleggingssector' => 'OIS');
		$kleuren = $object->pdf->grafiekKleuren[$kleurVertaling[$type]];

		if (!isset($object->pdf->rapportageDatumWaarde) || $extraWhere != '')
		{
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum = '" . $object->rapportageDatum . "' AND " .
				" portefeuille = '" . $object->portefeuille . "' $extraWhere"
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			$DB->SQL($query);
			$DB->Query();
			$portefwaarde = $DB->nextRecord();
			$portTotaal = $portefwaarde['totaal'];
			if ($extraWhere == '')
			{
				$object->pdf->rapportageDatumWaarde = $portTotaal;
			}
		}
		else
		{
			$portTotaal = $object->pdf->rapportageDatumWaarde;
		}

		$query = "SELECT TijdelijkeRapportage.portefeuille, TijdelijkeRapportage." . $type . "Omschrijving as Omschrijving, TijdelijkeRapportage." . $type . " as type,SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  " .
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '" . $object->portefeuille . "') AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $object->rapportageDatum . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY " . $type . "  ORDER BY TijdelijkeRapportage." . $type . "Volgorde";
		debugSpecial($query, __FILE__, __LINE__);

		$DB->SQL($query);
		$DB->Query();

		while ($categorien = $DB->NextRecord())
		{
			$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = vertaalTekst($categorien['Omschrijving'], $object->pdf->rapport_taal);
			if ($categorien['type'] == '')
			{
				$categorien['type'] = 'geenWaarden';
			}

			if (count($items) > 0 && !in_array($categorien['type'], $items))
			{
				$categorien['type'] = 'Overige';
				$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = vertaalTekst('Overige', $object->pdf->rapport_taal);
				//$kleuren[$categorien['type']]=array('R'=>array('value'=>100),'G'=>array('value'=>100),'B'=>array('value'=>100));
			}


			$valutaData[$categorien['type']]['port']['waarde'] += $categorien['subtotaalactueel'];
		}

		foreach ($valutaData as $waarde => $data)
		{
			if (isset($data['port']['waarde']))
			{


				$typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
				$typeData['grafiek'][$object->pdf->veldOmschrijvingen[$type][$waarde]] = $typeData['port']['procent'][$waarde] * 100;
				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}
}


if(!function_exists('LineDiagram'))
{
	function LineDiagram($object, $w, $h, $data, $color = null, $maxVal = 0, $minVal = 0, $horDiv = 4, $verDiv = 4, $jaar = 0)
	{
		global $__appvar;

		$legendDatum = $data['datum'];
		$data1 = $data['specifiekeIndex'];
		$data = $data['portefeuille'];
		$legendaItems = $data['legenda'];

		if (count($data1) > 0)
		{
			$bereikdata = array_merge($data, $data1);
		}
		else
		{
			$bereikdata = $data;
		}

		$XPage = $object->GetX();
		$YPage = $object->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 1);
		$XDiag = $XPage + $margin * 1;
		$lDiag = floor($w - $margin * 1);

		$object->Rect($XDiag, $YDiag, $w - $margin, $h, 'FD', '', array(245, 245, 245));

		if (is_array($color[0]))
		{
			$color1 = $color[1];
			$color = $color[0];
		}

		if ($color == null)
		{
			$color = array(155, 155, 155);
		}
		$object->SetLineWidth(0.2);

		$object->SetFont($object->rapport_font, '' . $kopStyle, $object->rapport_fontsize);
		$object->SetFillColor($color[0], $color[1], $color[2]);

		if ($maxVal == 0)
		{
			$maxVal = ceil(max($bereikdata));
			if ($maxVal < 0)
			{
				$maxVal = 1;
			}
		}
		if ($minVal == 0)
		{
			$minVal = floor(min($bereikdata));
			if ($minVal > 0)
			{
				$minVal = -1;
			}
		}

		$minVal = floor(($minVal - 1) * 1.1);
		$maxVal = ceil(($maxVal + 1) * 1.1);
		$legendYstep = ($maxVal - $minVal) / $horDiv;
		$verInterval = ($lDiag / $verDiv);
		$horInterval = ($hDiag / $horDiv);
		$waardeCorrectie = $hDiag / ($maxVal - $minVal);
		$unit = $lDiag / count($data);

		if ($jaar)
		{
			$unit = $lDiag / 12;
		}

		for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
		{
			$xpos = $XDiag + $verInterval * $i;
		}

		$object->SetFont($object->rapport_font, '', 8);
		$object->SetTextColor(0, 0, 0);
		$object->SetDrawColor(0, 0, 0);

		$stapgrootte = ceil(abs($maxVal - $minVal) / $horDiv);
		$unith = $hDiag / (-1 * $minVal + $maxVal);

		$top = $YPage;
		$bodem = $YDiag + $hDiag;
		$absUnit = abs($unith);

		$nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
		$n = 0;
		for ($i = $nulpunt; $i <= $bodem; $i += $absUnit * $stapgrootte)
		{
			$skipNull = true;
			$object->Line($XDiag, $i, $XPage + $w, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
			$object->Text($XDiag - 7, $i, 0 - ($n * $stapgrootte) . " %");
			$n++;
			if ($n > 20)
			{
				break;
			}
		}

		$n = 0;
		for ($i = $nulpunt; $i >= $top; $i -= $absUnit * $stapgrootte)
		{
			$object->Line($XDiag, $i, $XPage + $w, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
			if ($skipNull == true)
			{
				$skipNull = false;
			}
			else
			{
				$object->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0 . " %");
			}

			$n++;
			if ($n > 20)
			{
				break;
			}
		}
		$yval = $YDiag + (($maxVal) * $waardeCorrectie);
		$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
		for ($i = 0; $i < count($data); $i++)
		{
			$object->TextWithRotation($XDiag + ($i) * $unit - 5 + $unit, $YDiag + $hDiag + 8, $legendDatum[$i], 25);
			$yval2 = $YDiag + (($maxVal - $data[$i]) * $waardeCorrectie);
			$object->line($XDiag + $i * $unit, $yval, $XDiag + ($i + 1) * $unit, $yval2, $lineStyle);
			if ($i > 0)
			{
				$object->Rect($XDiag + $i * $unit - 0.5, $yval - 0.5, 1, 1, 'F', '', $color);
			}
			if ($i == count($data) - 1)
			{
				$object->Rect($XDiag + ($i + 1) * $unit - 0.5, $yval2 - 0.5, 1, 1, 'F', '', $color);
			}
			$yval = $yval2;
		}

		if (is_array($data1) && count($data1) > 0)
		{
			$yval = $YDiag + (($maxVal) * $waardeCorrectie);
			$lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

			for ($i = 0; $i < count($data1); $i++)
			{
				$yval2 = $YDiag + (($maxVal - $data1[$i]) * $waardeCorrectie);
				$object->line($XDiag + $i * $unit, $yval, $XDiag + ($i + 1) * $unit, $yval2, $lineStyle);
				if ($i > 0)
				{
					$object->Rect($XDiag + $i * $unit - 0.5, $yval - 0.5, 1, 1, 'F', '', $color1);
				}
				if ($i == count($data1) - 1)
				{
					$object->Rect($XDiag + ($i + 1) * $unit - 0.5, $yval2 - 0.5, 1, 1, 'F', '', $color1);
				}

				$yval = $yval2;
			}
		}
		$object->SetLineStyle(array('color' => array(0, 0, 0)));


		//   $XPage
		// $YPage
		if (count($data1) > 0)
		{
			$legendaItems = array('portefeuille', 'benchmark');
		}
		else
		{
			$legendaItems = array();
		}
		$step = 5;
		foreach ($legendaItems as $index => $item)
		{
			if ($index == 0)
			{
				$kleur = $color;
			}
			else
			{
				$kleur = $color1;
			}
			$object->SetDrawColor($kleur[0], $kleur[1], $kleur[2]);
			$object->Rect($XPage + $step, $YPage + $h + 10, 3, 3, 'DF', '', $kleur);
			$object->SetXY($XPage + 3 + $step, $YPage + $h + 10);
			$object->Cell(0, 3, $item);
			$step += ($w / 2);
		}
		$object->SetDrawColor(0, 0, 0);
		$object->SetFillColor(0, 0, 0);
	}
}


if(!function_exists('VBarDiagram'))
{
	function VBarDiagram($object, $w, $h, $data, $titel)
	{
		global $__appvar;

		$grafiekPunt = array();
		$verwijder = array();
		$xPositie = $object->getX();
		$yPositie = $object->getY();
		$object->SetFont($object->rapport_font, 'B', $object->rapport_fontsize + 2);
		$object->setXY($xPositie, $yPositie - $h - 8);
		$object->Multicell($w, 5, $titel, '', 'C');
		$object->setXY($xPositie + $w, $yPositie);
		$object->SetFont($object->rapport_font, 'B', 6);
		$object->Multicell(20, 5, 'X 1.000', '', 'L');
		$object->setXY($xPositie, $yPositie);

		foreach ($data as $datum => $waarden)
		{
			$legenda[$datum] = $datum;
			$n = 0;
			foreach ($waarden as $categorie => $waarde)
			{
				$datumTotalen[$datum] += $waarde;
				$grafiek[$datum][$categorie] = $waarde;
				$grafiekCategorie[$categorie][$datum] = $waarde;
				$categorien[$categorie] = $n;
				$categorieId[$n] = $categorie;
				if ($waarde < 0)
				{
					$verwijder[$datum] = $datum;
					$grafiek[$datum][$categorie] = 0;
					$grafiekCategorie[$categorie][$datum] = 0;
				}


				if (!isset($colors[$categorie]))
				{
					$colors[$categorie] = array($object->categorieKleuren[$categorie]['R']['value'], $object->categorieKleuren[$categorie]['G']['value'], $object->categorieKleuren[$categorie]['B']['value']);
				}
				$n++;


			}
		}

		$colors = array('lossing' => array(31, 73, 125), 'rente' => array(0, 115, 42));

		foreach ($verwijder as $datum)
		{
			foreach ($data[$datum] as $categorie => $waarde)
			{
				$grafiek[$datum][$categorie] = 0;
				$grafiekCategorie[$categorie][$datum] = 0;
			}
		}

		$numBars = count($legenda);


		$maxVal = max($datumTotalen);
		$minVal = 0;
		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
		$XPage = $object->GetX();
		$YPage = $object->GetY();
		$margin = 2;
		$YstartGrafiek = $YPage - floor($margin * 1);
		$hGrafiek = ($h - $margin * 1);
		$XstartGrafiek = $XPage + $margin * 1;
		$bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

		$n = 0;
		foreach (array_reverse($object->categorieVolgorde) as $categorie)
		{
			if (is_array($grafiekCategorie[$categorie]))
			{
				$object->Rect($XstartGrafiek + $bGrafiek + 3, $YstartGrafiek - $hGrafiek + $n * 10 + 2, 2, 2, 'DF',null, $colors[$categorie]);
				$object->SetXY($XstartGrafiek + $bGrafiek + 6, $YstartGrafiek - $hGrafiek + $n * 10 + 1.5);
				$object->Cell(20, 3, $this->categorieOmschrijving[$categorie], 0, 0, 'L');
				$n++;
			}
		}
		$maxmaxVal = ceil($maxVal / (pow(10, strlen(round($maxVal))))) * pow(10, strlen(round($maxVal)));

		if ($maxmaxVal / 8 > $maxVal)
		{
			$maxVal = $maxmaxVal / 8;
		}
		elseif ($maxmaxVal / 4 > $maxVal)
		{
			$maxVal = $maxmaxVal / 4;
		}
		elseif ($maxmaxVal / 2 > $maxVal)
		{
			$maxVal = $maxmaxVal / 2;
		}
		else
		{
			$maxVal = $maxmaxVal;
		}

		$unit = $hGrafiek / $maxVal * -1;

		$nulYpos = 0;

		$horDiv = 5;
		$horInterval = $hGrafiek / $horDiv;
		$bereik = $hGrafiek / $unit;

		$object->SetFont($object->rapport_font, '', 6);
		$object->SetTextColor(0, 0, 0);

		$stapgrootte = (abs($bereik) / $horDiv);
		$top = $YstartGrafiek - $h;
		$bodem = $YstartGrafiek;
		$absUnit = abs($unit);

		$nulpunt = $YstartGrafiek + $nulYpos;

		$object->Rect($XstartGrafiek, $YstartGrafiek - $hGrafiek, $bGrafiek, $hGrafiek, 'FD', '', array(245, 245, 245));

		$n = 0;

		for ($i = $nulpunt; $i > $top; $i -= $absUnit * $stapgrootte)
		{
			$object->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek, $i, array('dash' => 1, 'color' => array(0, 0, 0)));
			$object->SetXY($XstartGrafiek + $bGrafiek + 1, $i - 1.5);
			$object->SetFont($object->rapport_font, 'B', 6);
			$object->Cell(10, 3, $object->formatGetal($n * $stapgrootte / 1000) . "", 0, 0, 'L');
			$n++;
			if ($n > 100)
			{
				break;
			}
		}

		if ($numBars > 0)
		{
			$object->NbVal = $numBars;
		}

		$vBar = ($bGrafiek / ($object->NbVal + 1));
		$bGrafiek = $vBar * ($object->NbVal + 1);
		$eBaton = ($vBar * 50 / 100);


		$object->SetLineStyle(array('dash' => 0, 'color' => array(0, 0, 0)));
		$object->SetLineWidth(0.2);

		$object->SetFillColor($color[0], $color[1], $color[2]);
		$i = 0;

		foreach ($grafiek as $datum => $data)
		{
			foreach ($data as $categorie => $val)
			{
				if (!isset($YstartGrafiekLast[$datum]))
				{
					$YstartGrafiekLast[$datum] = $YstartGrafiek;
				}
				//Bar
				$xval = $XstartGrafiek + (1 + $i) * $vBar - $eBaton / 2;
				$lval = $eBaton;
				$yval = $YstartGrafiekLast[$datum] + $nulYpos;
				$hval = ($val * $unit);

				$object->Rect($xval, $yval, $lval, $hval, 'DF',null, $colors[$categorie]);
				$YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum] + $hval;
				$object->SetTextColor(255, 255, 255);
				$object->SetTextColor(0, 0, 0);
				if ($legendaPrinted[$datum] != 1)
				{
					$object->TextWithRotation($xval - 0.75, $YstartGrafiek + 5.25, $legenda[$datum], 45);
				}

				if ($grafiekPunt[$categorie][$datum])
				{
					$object->Rect($xval + .5 * $eBaton - .5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek - .5, 1, 1, 'DF',null, array(128, 128, 128));
					if ($lastX)
					{
						$object->line($lastX, $lastY, $xval + .5 * $eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
					}
					$lastX = $xval + .5 * $eBaton;
					$lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
				}
				$legendaPrinted[$datum] = 1;
			}
			$i++;
		}
		$x1 = $xval + 25;
		$y1 = $YstartGrafiek - $h + 10;
		$hLegend = 3;
		$legendaMarge = 2;
		$vertaling['rente'] = vertaalTekst('Rente', $object->rapport_taal);
		$vertaling['lossing'] = vertaalTekst('Lossingen', $object->rapport_taal);

		foreach ($colors as $categorie => $color)
		{
			$object->SetFont($object->rapport_font, '', 6);
			$object->SetTextColor($object->rapport_fonds_fontcolor['R'], $object->rapport_fonds_fontcolor['G'], $object->rapport_fonds_fontcolor['B']);
			$object->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

			$object->SetFillColor($color[0], $color[1], $color[2]);
			$object->Rect($x1 - 5, $y1, $hLegend, $hLegend, 'DF');
			$object->SetXY($x1, $y1);
			$object->Cell(0, 4, $vertaling[$categorie]);
			// $y1+= $hLegend + $legendaMarge;
			$y1 += 6;
			$i++;
		}
		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
	}
}


if(!function_exists('PieChartOnder'))
{
	function PieChartOnder($object, $w, $h, $data, $format, $colors = null, $titel = '')
	{

		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
		$object->SetLegends($data, $format);
		$stringWidth = 0;
		$centerMarge = 15;

		for ($i = 0; $i < $object->NbVal; $i++)
		{
			$stringWidth = max(array($object->GetStringWidth($object->legends[$i]), $stringWidth));
		}
		$stringWidth = $stringWidth;

		$XPage = $object->GetX();
		$YPage = $object->GetY();


		$margin = 2;
		$hLegend = 2;

		$radius = min($w, $h); //
		$radius = ($radius / 2);
		$XDiag = $XPage + $radius;
		$YDiag = $YPage + $radius;
		if ($colors == null)
		{
			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$gray = $i * intval(255 / $object->NbVal);
				$colors[$i] = array($gray, $gray, $gray);
			}
		}

		$maxWidth = max(array($stringWidth, $radius * 2));
		$maxWidth += $centerMarge;
		if ($maxWidth > $w)
		{
			$diff = ($maxWidth - $w);
		}

		$object->SetFillColor(245, 245, 245);
		$object->Rect($XPage - ($diff / 2), $YPage - 8, $maxWidth, $h + (count($data) * 4) + 10, 'DF');

		$object->setXY($XPage, $YPage - 6);
		$object->Cell($w, 4, $titel, 0, 0, 'C');


		//Sectors
		$object->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach ($data as $val)
		{
			$angle = floor(($val * 360) / doubleval($object->sum));
			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$object->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$object->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends

		$x1 = $XPage - ($diff / 2) + 4;
		$x2 = $x1 + 5;
		$y1 = $YDiag + ($radius) + $margin;

		for ($i = 0; $i < $object->NbVal; $i++)
		{
			$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
			$object->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
			$object->SetXY($x2, $y1);
			$object->Cell(0, $hLegend, $object->legends[$i]);
			$y1 += $hLegend + $margin;
		}

	}
}


if(!function_exists('PieChart'))
{
	function PieChart($object, $w, $h, $data, $format, $colors = null)
	{

		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
		$object->SetLegends($data, $format);

		$XPage = $object->GetX();
		$YPage = $object->GetY();
		$margin = 2;
		$hLegend = 2;
		$radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if ($colors == null)
		{
			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$gray = $i * intval(255 / $object->NbVal);
				$colors[$i] = array($gray, $gray, $gray);
			}
		}

		//Sectors
		$object->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach ($data as $val)
		{
			$angle = floor(($val * 360) / doubleval($object->sum));
			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$object->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$object->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);

		$x1 = $XPage + $w;
		$x2 = $x1 + $margin;
		$y1 = $YDiag - $radius + ($margin * 2);


		for ($i = 0; $i < $object->NbVal; $i++)
		{
			$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
			$object->Rect($x1 - 2, $y1, $hLegend, $hLegend, 'DF');
			$object->SetXY($x2, $y1);
			$object->Cell(0, $hLegend, $object->legends[$i]);
			$y1 += $hLegend + $margin;
		}
		$object->setY($YPage + $h);

	}
}

?>