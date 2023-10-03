<?php

function Header_basis_L123($object)
{
  global $__appvar;
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
  	  	$pdfObject->customPageNo = 1;
      $pdfObject->rapportNewPage = $pdfObject->page;
    }
    else 
    {  
  	//  if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	//	  $pdfObject->customPageNo = 0;
      
  	  if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
  	    	$pdfObject->rapportNewPage = $pdfObject->page;
     
		  $pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->rapport_kop_lineWidth);
		$pdfObject->SetLineStyle(array('cap'=>'round'));


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



		if(is_file($pdfObject->rapport_logo))
		{
      $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->marge, $pdfObject->h-20,60);
		}
      $pdfObject->SetXY($pdfObject->marge,8);
    $pdfObject->SetFont($pdfObject->rapport_font,'',24);
    $pdfObject->SetTextColor($pdfObject->rapport_groen[0],$pdfObject->rapport_groen[1],$pdfObject->rapport_groen[2]);
      
		$pdfObject->MultiCell(200,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
    $pdfObject->SetDrawColor($pdfObject->rapport_groen[0],$pdfObject->rapport_groen[1],$pdfObject->rapport_groen[2]);
    $pdfObject->ln();
    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge,$pdfObject->GetY());
   // $pdfObject->Line($pdfObject->w-$pdfObject->marge-8,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge-2,$pdfObject->GetY()-6);
  

     $rapportagePeriode = date("d/m/Y",$pdfObject->rapport_datumvanaf).' - '.date("d/m/Y",$pdfObject->rapport_datum);
  
      $pdfObject->Rect(75, $pdfObject->h-20, $pdfObject->w-$pdfObject->marge-75, 5, 'F',null,$pdfObject->rapport_donker);

      $pdfObject->setXY(90,$pdfObject->h-20);
      $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);
      $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
      if($pdfObject->portefeuilledata['SoortOvereenkomst']<>'')
        $pdfObject->MultiCell(150,5,$rapportagePeriode.' - '.$pdfObject->portefeuilledata['SoortOvereenkomst'],0,'L');
      else
        $pdfObject->MultiCell(150,5,$rapportagePeriode,0,'L');
      $pdfObject->setXY(180,$pdfObject->h-20);
      $pdfObject->MultiCell(50,5,$pdfObject->rapport_portefeuille,0,'L');
      $pdfObject->setXY($pdfObject->w-$pdfObject->marge-25,$pdfObject->h-20);
      $pdfObject->MultiCell(20,5,$pdfObject->customPageNo,0,'R');

  
      $pdfObject->SetTextColor($pdfObject->rapport_groen[0],$pdfObject->rapport_groen[1],$pdfObject->rapport_groen[2]);
      $pdfObject->headerStart=$pdfObject->GetY()+15;

    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
      $pdfObject->setY(25);
    }
}

	function HeaderVKM_L123($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	  function HeaderFRONT_L123($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }
	  function HeaderSCENARIO_L123($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }
function HeaderVKMS_L123($object)
{
  $pdfObject = &$object;
  //$pdfObject->headerSCENARIO();
  
}

    function HeaderRISK_L123($object)
    {
	    $pdfObject = &$object;
			$pdfObject->Ln();
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);
			$pdfObject->Ln(10);
	  }

    function HeaderGRAFIEK_L123($object)
    {
	    $pdfObject = &$object;
    }

    function HeaderEND_L123($object)
    {
     	$pdfObject = &$object;
		}

    function HeaderPERFD_L123($object)
    {
      $pdfObject = &$object;
      HeaderPERF_L123($object);
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
    }
  
    
    function HeaderTRANS_L123($object)
	  {
	    $pdfObject = &$object;
    	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  		$pdfObject->SetX(100);
			$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
			$pdfObject->ln();
	    
  		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);

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
			$pdfObject->ln();
    $pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
	//	$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());
    $pdfObject->SetDrawColor(0,0,0);
		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

      $pdfObject->SetXY($pdfObject->marge,$y);
			$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/\nVerKoop",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)));
      $pdfObject->ln(1);
	  }
    
    function HeaderMUT_L123($object)
	  {
	    $pdfObject = &$object;

    }
    
	  function HeaderAFM_L123($object)
	  {
	    $pdfObject = &$object;
	  
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);


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
	  function HeaderINHOUD_L123($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }	  
 	  function HeaderPERF_L123($object)
	  {
	    $pdfObject = &$object;

	  }
    
    function HeaderINDEX_L123($object)
	  {
	    $pdfObject = &$object;
	  }
    function HeaderOIB_L123($object)
	  {
	    $pdfObject = &$object;
	  }

function HeaderOIS_L123($object)
{
  $pdfObject = &$object;
}
function HeaderCASHY_L123($object)
{
  $pdfObject = &$object;
}
function HeaderOIR_L123($object)
{
  $pdfObject = &$object;
}
function HeaderOIH_L123($object)
{
  $pdfObject = &$object;
}
function HeaderHSE_L123($object)
{
  $pdfObject = &$object;
}
  function HeaderATT_L123($object)
	{
    $pdfObject = &$object;
	}
   function HeaderPERFG_L123($object)
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
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
 		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);

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

  function HeaderVOLK_L123($object)
	{
    $pdfObject = &$object;
  }
  
function HeaderVHO_L123($object)
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
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[0],$pdfObject->rapport_kop_bgcolor[1],$pdfObject->rapport_kop_bgcolor[2]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[0],$pdfObject->rapport_kop_fontcolor[1],$pdfObject->rapport_kop_fontcolor[2]);

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


function PieChart_L123($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='',$donut=false)
{
  
  $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  
  
  $pdfObject->legends=array();
  $pdfObject->wLegend=0;
  
  $pdfObject->sum=array_sum($data);
  
  $pdfObject->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      $p=sprintf('%.2f',$val).'%';
      $p = str_replace('.', ',', $p);
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $pdfObject->legends[]=$legend;
      $pdfObject->wLegend=max($pdfObject->GetStringWidth($legend),$pdfObject->wLegend);
    }
  
  
  $XPage = $pdfObject->GetX();
  $YPage = $pdfObject->GetY();
  
  if($pdfObject->debug==true)
  {
    $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
    $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
    $pdfObject->Rect($XPage,$YPage,$w,$h);
    $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
  }
  $pdfObject->setXY($XPage,$YPage);

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

  $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
  //Sectors
  $pdfObject->SetLineWidth(0.2);
  $angleStart = 0;
  $i = 0;
  $aantal=count($data);
  if($donut==true)
    $factor=1;
  else
    $factor=1.5;
  foreach($data as $val)
  {
    $angle = floor(($val * 360) / doubleval($pdfObject->sum));
    
    if ($angle != 0)
    {
      $angleEnd = $angleStart + $angle;
      
      $avgAngle=($angleStart+$angleEnd)/360*M_PI;
      
      if($i==($aantal-1))
        $angleEnd=360;
      
      //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      if($donut==true)
        $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
      else
        $pdfObject->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
      $angleStart += $angle;
    }
    $i++;
  }
  
  if($donut==true)
  {
    /*
    foreach($data as $val)
    {
      $angle = (($val * 360) / doubleval($pdfObject->sum));
      $pdfObject->SetLineStyle(array('cap'=>'round','width'=>2,'color'=>array(255,255,255)));
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
      $pdfObject->Circle($XDiag, $YDiag, $radius * 0.75, 0, 360, 'F', null, array(255, 255, 255));
  }

  $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor(0,0,0);
  
  //Legends
  $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  
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
  
  for($i=0; $i<$pdfObject->NbVal; $i++) {
    $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
    $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
    $pdfObject->SetXY($x2,$y1);
    $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
    $y1+=$hLegend*2;
  }
  
}



?>