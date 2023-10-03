<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_headers_L31.php,v $
 		Revision 1.5  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/03/02 10:26:23  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/01/29 16:54:18  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/01/26 15:08:13  rvv
 		*** empty log message ***
 		
*/

function Header_basis_L31($object)
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
		  if($pdfObject->rapport_layout == 12 || $pdfObject->rapport_layout == 5 || $pdfObject->rapport_layout == 25)
		  {
			  $pdfObject->Image($pdfObject->rapport_logo, $logopos -33, 5, 108, 15);
		  }
		  elseif($pdfObject->rapport_layout == 7)
		  {
		    $factor=0.04;
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 1029*$factor, 632*$factor);
		  }
		  elseif($pdfObject->rapport_layout == 30)
		  {
		    if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		    	$logopos = 80;
		    else
		      $logopos = 115;
		    $factor=0.04;
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, 1691*$factor, 586*$factor);
		  }
		  elseif($pdfObject->rapport_layout == 31)
		  {
		    $factor=0.08;
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos -25 , 5, 1074*$factor, 192*$factor);
		 	}
		  elseif($pdfObject->rapport_layout == 14 )
		  {
			  //$pdfObject->Image($pdfObject->rapport_logo, 220, 5, 65, 20);
			  //$factor=0.09;
		    //$xSize=492*$factor;
		    //$ySize=211*$factor;
        $factor=0.05;
		    $xSize=983*$factor;
		    $ySize=288*$factor;
		    $pdfObject->Image($pdfObject->rapport_logo, 235, 5, $xSize, $ySize);
		  }
		  elseif ($pdfObject->rapport_layout == 16 )
		  {
		    if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		      $logopos = 100;
				else
		    	$logopos = 185;
			//  $pdfObject->Image($pdfObject->rapport_logo, 260, 5, 27, 20); //kei
			$pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 101, 12);//duis 1050,125
		  }
		  elseif ($pdfObject->rapport_layout == 17 )
		  {
			  $pdfObject->Image($pdfObject->rapport_logo, 242, 191, 45, 10);
		  }
		  elseif($pdfObject->rapport_layout == 1)
		  {
			  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 7, 43, 15);
		  }
		  else
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 43, 15);
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
   }
   $pdfObject->headerStart=$pdfObject->GetY()+14;
}

  function HeaderOIB_L31($object)
  {
    $pdfObject = &$object;
    //$pdfObject->HeaderOIB();
  }
   function HeaderAFM_L31($object)
  {
    $pdfObject = &$object;
    $pdfObject->HeaderOIB();
  }
	function HeaderVKM_L31($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
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
				$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = 'Overige';
				$kleuren[$categorien['type']] = array('R' => array('value' => 100), 'G' => array('value' => 100), 'B' => array('value' => 100));
			}


			$valutaData[$categorien['type']]['port']['waarde'] += $categorien['subtotaalactueel'];
		}

		foreach ($valutaData as $waarde => $data)
		{
			if (isset($data['port']['waarde']))
			{
				$veldnaam = $object->pdf->veldOmschrijvingen[$type][$waarde];
				if ($veldnaam == '')
				{
					$veldnaam = 'Overige';
				}

				$typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
				$typeData['port']['waarde'][$waarde] = $data['port']['waarde'];
				$typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;

				//if($veldnaam=='Overige' && isset($kleuren['Liquiditeiten']))
				//  $waarde='Liquiditeiten';

				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}
}

?>