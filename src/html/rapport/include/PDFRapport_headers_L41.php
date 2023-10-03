<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.16 $

 		$Log: PDFRapport_headers_L41.php,v $
 		Revision 1.16  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/04/06 16:16:30  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/03/02 17:14:06  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/01/06 10:09:57  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/12/30 14:27:11  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/12/02 11:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/11/17 16:02:20  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/11/14 16:48:28  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/11/07 17:08:05  rvv
 		*** empty log message ***
 		

 		
*/
function Header_basis_L41($object)
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


	 // $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
	//	$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD"  )
			$x = 160;
		else
			$x = 297-($pdfObject->marge)-40;

		$pdfObject->SetXY($x,$y);
//	  $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  $pdfObject->SetXY((297/2)-50,$y);
    $pdfObject->SetXY(15,15);
    $pdfObject->SetTextColor($pdfObject->blue[0],$pdfObject->blue[1],$pdfObject->blue[2]);
  	$pdfObject->SetFont('garmond','',30);
   	$pdfObject->MultiCell(280,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
    $pdfObject->SetXY(15,31.5);

		$pdfObject->headerStart = $pdfObject->getY()+4;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }

}

	function HeaderVKM_L41($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	function HeaderCASHY_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}
  
  function HeaderINDEX_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}
  
	function HeaderRISK_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}

	function HeaderOIB_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}
  
  function HeaderEND_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}
  
  	function HeaderPERFG_L41($object)
	{
    $pdfObject = &$object;
   // $pdfObject->HeaderOIB();
	}

	function HeaderTRANS_L41($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderTRANS();
	}
  
  function HeaderVHO_L41($object)
	{
    $pdfObject = &$object;

    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $lastColors=$pdfObject->CellFontColor;
    unset($pdfObject->CellFontColor);
    $pdfObject->fillCell=array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
    $pdfObject->SetFillColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
    
   

      $pdfObject->SetTextColor(255,255,255);    
    
		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
		$actueel 			= $pdfObject->widthB[6];
		$eindactueel 	= $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
		$resultaat 		= 0;
		$eindresultaat = $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] +  $pdfObject->widthB[10];
		// achtergrond kleur
	
		$pdfObject->SetWidths(array($huidige,$eindhuidige,$actueel,$eindactueel,$resultaat,$eindresultaat));
		$pdfObject->SetAligns(array('C','C','C','C','C','C'));
    $pdfObject->row(array('',vertaalTekst("Gemiddelde historische kostprijs",$pdfObject->rapport_taal),
                          '',vertaalTekst("Huidige waarde",$pdfObject->rapport_taal),
                          '',vertaalTekst("Rendement",$pdfObject->rapport_taal)));
       
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();

			$pdfObject->row(array(
											"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                      vertaalTekst("Valuta\n ",$pdfObject->rapport_taal),
											vertaalTekst("Aantal\n ",$pdfObject->rapport_taal),
												vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille\nin valuta",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												" ",
												vertaalTekst("Per stuk\nin valuta",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille\nin valuta",$pdfObject->rapport_taal),
												vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal)."\n "));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->setY($y);
	  $pdfObject->row(array("Categorie\n"));
		$pdfObject->ln();
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
    
          $pdfObject->CellBorders = array();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $totaal=0;
      $totaalProcent=0;
      unset($pdfObject->fillCell);
      $pdfObject->SetFillColor(0,0,0);
      $pdfObject->SetTextColor(0,0,0);  
  
      $pdfObject->CellFontColor=$lastColors;
   
	}

  function HeaderHSE_L41($object)
	{
	    $pdfObject = &$object;
      $dataWidth=array(28,51,20,22,22,20,22,22,20,20,20);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
      $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->fillCell=array(1,1,1,1,1,1,1,1,1,1,1,1);
      $pdfObject->SetFillColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
      //$pdfObject->SetDrawColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
      $pdfObject->SetTextColor(255,255,255);
      
      $pdfObject->CellBorders = array();

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
      "".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
     
      $pdfObject->CellBorders = array();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $totaal=0;
      $totaalProcent=0;
      unset($pdfObject->fillCell);
      $pdfObject->SetFillColor(0,0,0);
      $pdfObject->SetTextColor(0,0,0);  
  
      $pdfObject->CellFontColor=$lastColors;
	    $pdfObject->SetLineWidth(0.1);
      if(is_array($pdfObject->widthsBackup))
       $pdfObject->widths=$pdfObject->widthsBackup;
     // listarray($pdfObject->widths);echo "new page <br>\n";
  }
    
	function HeaderPERF_L41($object)
	{
    $pdfObject = &$object;
		// achtergrond kleur
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->ln(2);
	  //$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		//$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		//$pdfObject->ln(2);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	}

	function HeaderATT_L41($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(23,27,23,22,24,19,19,20,22,27,19,20);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->ln(2);
		//$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		//$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    //$pdfObject->ln(2);
    
    $pdfObject->fillCell=array(1,1,1,1,1,1,1,1,1,1,1,1);
    $pdfObject->SetFillColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
    $pdfObject->SetDrawColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
    $pdfObject->SetTextColor(255,255,255);

		//$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    //$pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Opgelopen-\nrente ",
                          "Kosten\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
		                      "Rendement\n(maand)",
		                      "Rendement\n(Cumulatief)"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor(0,0,0);
    $pdfObject->fillCell=array();
    $sumWidth = array_sum($pdfObject->widthA);
	  //$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

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
				if (isset($kleuren[$waarde]))
				{
					$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
				}
				else
				{
					$typeData['grafiekKleur'][] = array(rand(0, 255), rand(0, 255), rand(0, 255));
				}
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

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

		$object->sum = 0;
		foreach ($data as $key => $value)
		{
			$data[$key] = abs($value);
			$object->sum += abs($value);
		}


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


if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object, $vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
		$query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '" . $object->portefeuilledata['Vermogensbeheerder'] . "' ORDER BY Afdrukvolgorde";
		$border = 0;
		$DB = new DB();
		$DB2 = new DB();
		$lmarge = 140;

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if (($object->GetY() + $hoogte) > $object->pagebreak)
		{
			$object->AddPage();
			$object->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if ($object->rapport_perfIndexJanuari == true)
		{
			$julRapDatumVanaf = db2jul($rapportageDatumVanaf);
			$rapJaar = date('Y', $julRapDatumVanaf);
			$dagMaand = date('d-m', $julRapDatumVanaf);
			$januariDatum = $rapJaar . '-01-01';
			if ($dagMaand == '01-01')
			{
				$object->rapport_perfIndexJanuari = false;
			}
		}
		if ($object->rapport_printAEXVergelijkingEur == 1)
		{
			$extraX = 26;
			$perfEur = 1;
			$perfVal = 0;
			$perfJan = 0;
		}
		if ($object->rapport_perfIndexJanuari == true)
		{
			$perfEur = 0;
			$perfVal = 0;
			$perfJan = 1;
		}

		if ($object->printAEXVergelijkingProcentTeken)
		{
			$teken = '%';
		}
		else
		{
			$teken = '';
		}


		if ($object->rapport_perfIndexJanuari == true)
		{
			$extraX += 51;
		}

		$object->ln();
		$object->SetFillColor($object->rapport_kop_bgcolor[r], $object->rapport_kop_bgcolor[g], $object->rapport_kop_bgcolor[b]);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte, 'F');
		$object->SetFillColor(0);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte);
		$object->SetX($object->marge + $lmarge);

		// kopfontcolor
		//$object->SetTextColor($object->rapport_kop4_fontcolor[r],$object->rapport_kop4_fontcolor[g],$object->rapport_kop4_fontcolor[b]);
		$object->SetTextColor($object->rapport_kop_fontcolor[r], $object->rapport_kop_fontcolor[g], $object->rapport_kop_fontcolor[b]);
		$object->SetFont($object->rapport_kop4_font, $object->rapport_kop4_fontstyle, $object->rapport_kop4_fontsize);
		$object->Cell(40, 4, vertaalTekst("Index-vergelijking", $object->rapport_taal), 0, 0, "L");

		$object->SetFont($object->rapport_font, $object->rapport_fontstyle, $object->rapport_fontsize);
		//$object->SetTextColor($object->rapport_fonds_fontcolor[r],$object->rapport_fonds_fontcolor[g],$object->rapport_fonds_fontcolor[b]);
		$object->SetTextColor($object->rapport_kop_fontcolor[r], $object->rapport_kop_fontcolor[g], $object->rapport_kop_fontcolor[b]);
		if ($object->rapport_perfIndexJanuari == true)
		{
			$object->Cell(26, 4, date("d-m-Y", db2jul($januariDatum)), $border, 0, "R");
		}
		$object->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatumVanaf)), $border, 0, "R");
		$object->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatum)), $border, 0, "R");

		if ($object->portefeuilledata['Layout'] == 30 || $object->portefeuilledata['Layout'] == 14 || $object->portefeuilledata['Layout'] == 25)
		{
			$object->Cell(26, 4, vertaalTekst("Perf in %", $object->rapport_taal), $border, $perfVal, "R");
		}
		else
		{
			$object->Cell(26, 4, vertaalTekst("Performance in %", $object->rapport_taal), $border, $perfVal, "R");
		}
		if ($object->rapport_printAEXVergelijkingEur == 1)
		{
			$object->Cell(26, 4, vertaalTekst("Perf in % in EUR", $object->rapport_taal), $border, $perfEur, "R");
		}
		if ($object->rapport_perfIndexJanuari == true)
		{
			$object->Cell(26, 4, vertaalTekst("Jaar Perf.", $object->rapport_taal), $border, $perfJan, "R");
		}

		while ($perf = $DB->nextRecord())
		{
			if ($perf['Valuta'] != 'EUR')
			{
				if ($object->rapport_perfIndexJanuari == true)
				{
					$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $januariDatum . "' ORDER BY Datum DESC LIMIT 1 ";
					$DB2->SQL($q);
					$DB2->Query();
					$valutaKoersJan = $DB2->LookupRecord();
				}

				$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $rapportageDatumVanaf . "' ORDER BY Datum DESC LIMIT 1 ";
				$DB2->SQL($q);
				$DB2->Query();
				$valutaKoersStart = $DB2->LookupRecord();

				$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $rapportageDatum . "' ORDER BY Datum DESC LIMIT 1 ";
				$DB2->SQL($q);
				$DB2->Query();
				$valutaKoersStop = $DB2->LookupRecord();

			}
			else
			{
				$valutaKoersJan['Koers'] = 1;
				$valutaKoersStart['Koers'] = 1;
				$valutaKoersStop['Koers'] = 1;
			}

			if ($object->rapport_perfIndexJanuari == true)
			{
				$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $januariDatum . "' AND Fonds = '" . $perf[Beursindex] . "'  ORDER BY Datum DESC LIMIT 1";
				$DB2->SQL($q);
				$DB2->Query();
				$koers0 = $DB2->LookupRecord();
			}

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $rapportageDatumVanaf . "' AND Fonds = '" . $perf[Beursindex] . "'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $rapportageDatum . "' AND Fonds = '" . $perf[Beursindex] . "'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers'] / 100);
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers'] / 100);
			$performanceEur = ($koers2['Koers'] * $valutaKoersStop['Koers'] - $koers1['Koers'] * $valutaKoersStart['Koers']) / ($koers1['Koers'] * $valutaKoersStart['Koers'] / 100);
			//echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
			$object->Cell($lmarge, 4, '', $border, 0, "L");
			$object->Cell(40, 4, $perf[Omschrijving], $border, 0, "L");
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$object->Cell(26, 4, $object->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($object->rapport_printAEXVergelijkingEur == 1)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '" . $object->rapport_portefeuille . "' ";
		$DB->SQL($query2);
		$DB->Query();

		while ($perf = $DB->nextRecord())
		{

			if ($perf['Valuta'] != 'EUR')
			{

				if ($object->rapport_perfIndexJanuari == true)
				{
					$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $januariDatum . "' ORDER BY Datum DESC LIMIT 1 ";
					$DB2->SQL($q);
					$DB2->Query();
					$valutaKoersJan = $DB2->LookupRecord();
				}

				$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $rapportageDatumVanaf . "' ORDER BY Datum DESC LIMIT 1 ";
				$DB2->SQL($q);
				$DB2->Query();
				$valutaKoersStart = $DB2->LookupRecord();

				$q = "SELECT Koers FROM Valutakoersen WHERE Valuta='" . $perf['Valuta'] . "' AND Datum <= '" . $rapportageDatum . "' ORDER BY Datum DESC LIMIT 1 ";
				$DB2->SQL($q);
				$DB2->Query();
				$valutaKoersStop = $DB2->LookupRecord();

			}
			else
			{
				$valutaKoersJan['Koers'] = 1;
				$valutaKoersStart['Koers'] = 1;
				$valutaKoersStop['Koers'] = 1;
			}

			if ($object->rapport_perfIndexJanuari == true)
			{
				$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $januariDatum . "' AND Fonds = '" . $perf[SpecifiekeIndex] . "'  ORDER BY Datum DESC LIMIT 1";
				$DB2->SQL($q);
				$DB2->Query();
				$koers0 = $DB2->LookupRecord();
			}

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $rapportageDatumVanaf . "' AND Fonds = '" . $perf[SpecifiekeIndex] . "'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '" . $rapportageDatum . "' AND Fonds = '" . $perf[SpecifiekeIndex] . "'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers'] / 100);
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers'] / 100);
			$performanceEur = ($koers2['Koers'] * $valutaKoersStop['Koers'] - $koers1['Koers'] * $valutaKoersStart['Koers']) / ($koers1['Koers'] * $valutaKoersStart['Koers'] / 100);
			//echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";

			$object->Cell($lmarge, 4, '', $border, 0, "L");
			$object->Cell(40, 4, $perf[Omschrijving], 0, 0, "L");
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$object->Cell(26, 4, $object->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($object->rapport_printAEXVergelijkingEur == 1)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}
	}
}
  
  
  function PieChart_L41($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
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
          if ($angle != 0) 
          {
              $angleEnd = $angleStart + $angle;
              $avgAngle=($angleStart+$angleEnd)/360*M_PI;

              //$lineAngle=($angleEnd)/180*M_PI;
              //$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));
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
         $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
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

      for($i=0; $i<$pdfObject->NbVal; $i++) {
          $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
          $pdfObject->SetXY($x2,$y1);
          $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
          $y1+=$hLegend*2;
      }

  }



?>