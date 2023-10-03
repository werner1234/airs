<?php

function Header_basis_L35($object)
{
   $pdfObject = &$object;

	if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
		$pdfObject->rapportNewPage = $pdfObject->page;

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
 		  $pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;
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
		$y = $pdfObject->GetY()-5;

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old;

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
		}

		if($pdfObject->lastPOST['anoniem'])
		  $pdfObject->rapport_depotbankOmschrijving='';

		if(trim($pdfObject->rapport_clientVermogensbeheerderReal) != '')
		{
			$depotbank=trim($pdfObject->rapport_clientVermogensbeheerderReal);
			$db=new DB();
			$query="SELECT Omschrijving FROM Depotbanken WHERE Depotbank='".mysql_real_escape_string($depotbank)."'";
			$db->SQL($query);
			$depot=$db->lookupRecord();
			if($depot['Omschrijving']<>'')
			  $depotbankOmschrijving=$depot['Omschrijving'];
			else
				$depotbankOmschrijving=$depotbank;
		}
		else
		{
			$depotbankOmschrijving= $pdfObject->rapport_depotbankOmschrijving;
			$depotbank=$pdfObject->rapport_depotbank;
		}

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}",$depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{RapportageValuta}", $pdfObject->portefeuilledata['RapportageValuta'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
		}
		else
		{
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}",$depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $depotbankOmschrijving, $pdfObject->rapport_koptext);
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
		$pdfObject->rapport_koptext = str_replace("{RapportageValuta}", $pdfObject->portefeuilledata['RapportageValuta'], $pdfObject->rapport_koptext);
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		$logopos = 10;
		//	$pdfObject->rapport_logo='/develop/php/robert/AIRS/html/rapport/logo/logo_ave.png';
		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
			if(substr($pdfObject->rapport_logo,-4)=='.png')
			{
				$factor = 0.025;
				$xSize = 1425 * $factor;
				$ySize = 699 * $factor;
			}
			else
			{
				$factor = 0.12;
				$xSize = 420 * $factor;
				$ySize = 168 * $factor;
			}
			$pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize, $ySize);
		}



		if($pdfObject->rapport_type == "MOD" )
			$x = 60;
		else
			$x = 150;

		//$pdfObject->Line($pdfObject->marge,30,$x+140,30);
		$pdfObject->SetY($y);

		$pdfObject->MultiCell(140,4,$pdfObject->rapport_koptext,0,'L');
			$widthBackup=$pdfObject->widths;
		$pdfObject->SetWidths(array(35,10,200));
		$pdfObject->SetAligns(array('L','C','L'));
		$pdfObject->SetXY($pdfObject->marge,32);
	  //$pdfObject->Row(array('Cliënt',':',$pdfObject->portefeuilledata['Naam']));// .' '. $pdfObject->rapport_naam2
	  $pdfObject->ln(2);
   // $pdfObject->Row(array('Portefeuille',':',$pdfObject->portefeuilledata['Portefeuille']));
		$pdfObject->SetXY($x,32);
		$pdfObject->SetXY(50,32);
//	  $pdfObject->MultiCell($x+50,4,vertaalTekst(vertaalTekst("Verslagperiode:",$pdfObject->rapport_taal)." ".
//	  date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." - ".
//	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)),0,'C');
		$pdfObject->SetXY($x,$y);
	  $pdfObject->MultiCell(140,4,"\n\n".vertaalTekst(vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".
	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)),0,'R');

	  $pdfObject->SetXY(50,$y-5);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
	  $pdfObject->MultiCell($x+50,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
		 $pdfObject->Line($pdfObject->marge,$y+18,$x+140,$y+18);
    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    	    $pdfObject->SetXY($pdfObject->marge,38);
	$pdfObject->widths=$widthBackup;
}

	function HeaderVKM_L35($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderKERNZ_L35($object)
{
	$pdfObject = &$object;

}

function HeaderAFM_L35($object)
{
  $pdfObject = &$object;
}

function HeaderVAR_L35($object)
{
  $pdfObject = &$object;
}

function HeaderKERNV_L35($object)
{
	$pdfObject = &$object;

}

function HeaderOIH_L35($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  $dataWidth=array(65,25,15,30,30,10,25,30,20,20);
  
  $dataWidth=array(65,1,1,23,23,2,23,23,12,2,23,23,2,22,22,15);
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
  $pdfObject->Row(array('',"Totaal commitment",'','Totaal opgevraagd','','Totaal terugbetaald','','Marktwaarde'));
  $pdfObject->CellBorders = array();
  
  $pdfObject->SetWidths($dataWidth);
  
  
  
  unset($pdfObject->CellBorders);
  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetWidths($dataWidth);
  
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->pageYstart=$pdfObject->GetY();
  $pdfObject->Row(array(vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    '',//vertaalTekst("Aanvang",$pdfObject->rapport_taal),
                    '',//vertaalTekst("Valuta",$pdfObject->rapport_taal),
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

  function HeaderPERF_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->SetXY(110,32);
	    $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
      $pdfObject->ln(6);
	}

  function HeaderOIV_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderFRONT_L35($object)
  {
  	$pdfObject = &$object;
  }
  
  function HeaderGRAFIEK_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderPERFG_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}


  function HeaderOIS_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderVOLK_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	    $pdfObject->ln();
//	    $pdfObject->HeaderVOLK();

//	   	$pdfObject->ln();
//		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige +$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4];

			$actueel 			= $eindhuidige + $pdfObject->widthB[5];
			$eindactueel 	= $actueel + $pdfObject->widthB[6] + $pdfObject->widthB[7];

			$resultaat 		= $eindactueel + $pdfObject->widthB[8] ;
			$eindresultaat = $resultaat +  $pdfObject->widthB[9] +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12];
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
//		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
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
										"",
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);


		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->row(array("",vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		}

  function HeaderINDEX_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderTRANS_L35($object)
	{
	  $pdfObject = &$object;
    
	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  $pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	}

function HeaderTRANSFEE_L35($object)
{
	$pdfObject = &$object;

  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
}
  
  	function HeaderMUT_L35($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 		
    $pdfObject->SetXY(110,32);
	 	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	
		  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
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
	
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	}

	function HeaderCASHY_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderRISK_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderATT_L35($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  	  function HeaderHSE_L35($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
      $dataWidth=array(28,35,15,20,20,20,22,22,22,18,20,20,20);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R'));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln();
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->Row(array(vertaalTekst("Risico\nCategorie",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
      "\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
      "\n".date('d-m-Y',$pdfObject->rapport_datum),
      "\n".vertaalTekst("Mutaties",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
      vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal),
      vertaalTekst("Regionale Benchmark",$pdfObject->rapport_taal),"\n".vertaalTekst("Benchmark",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
      if(is_array($pdfObject->widthsBackup))
       $pdfObject->widths=$pdfObject->widthsBackup;
     // listarray($pdfObject->widths);echo "new page <br>\n";
    }

	function HeaderVHO_L35($object)
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
			elseif($pdfObject->Hcat == "Vastrentende waarden")
			{
			  $pdfObject->SetWidths($pdfObject->widthB);
				 $pdfObject->row(array('',vertaalTekst('Naam',$pdfObject->rapport_taal),
												vertaalTekst('rating',$pdfObject->rapport_taal),
												vertaalTekst('nominaal/aantal',$pdfObject->rapport_taal),
												vertaalTekst("huidige koers",$pdfObject->rapport_taal),
												vertaalTekst("aanschafwaarde in",$pdfObject->rapport_taal)." ".$pdfObject->rapportageValuta,
												vertaalTekst("ongerealiseerd resultaat",$pdfObject->rapport_taal),
												vertaalTekst("opgelopen rente",$pdfObject->rapport_taal),
												vertaalTekst("coupon datum",$pdfObject->rapport_taal),
												vertaalTekst("effectief rendement",$pdfObject->rapport_taal),
												vertaalTekst("duration",$pdfObject->rapport_taal),
												vertaalTekst("markt-\nwaarde",$pdfObject->rapport_taal),
												vertaalTekst("% op totaal",$pdfObject->rapport_taal))
												);
			}
      else
			{
			  $pdfObject->SetWidths($pdfObject->widthA);
	      $pdfObject->row(array('',vertaalTekst('Naam',$pdfObject->rapport_taal),
												vertaalTekst('aantal',$pdfObject->rapport_taal),
												vertaalTekst('valuta',$pdfObject->rapport_taal),
											  vertaalTekst("huidige koers",$pdfObject->rapport_taal),
												vertaalTekst("aanschafwaarde in",$pdfObject->rapport_taal)." ".$pdfObject->rapportageValuta,
												vertaalTekst("ongerealiseerd valutaresultaat",$pdfObject->rapport_taal),
												vertaalTekst("ongerealiseerd fondsresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Marktwaarde in",$pdfObject->rapport_taal)." ".$pdfObject->rapportageValuta,
												vertaalTekst("% op totaal",$pdfObject->rapport_taal))
												);
			}


	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

if(!function_exists('PieChart_L35'))
{
  function PieChart_L35($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {
    
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
    $pdfObject->SetLegends($data,$format);
    
    
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
      if ($angle != 0 && $angle>1)
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
          $pdfObject->Cell(10,4,number_format($val,0,',','.').'%',0,0,'C');
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
    elseif($legendaStart=='geen')
    {
      return '';
    }
    
    for($i=0; $i<$pdfObject->NbVal; $i++)
    {
      $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
      $pdfObject->SetXY($x2,$y1);
      $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
      $y1+=$hLegend*2;
    }
    
  }
}



?>