<?php

function Header_basis_L126($object)
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
    }
    else
    {
    if($pdfObject->rapport_portefeuilleLast != $pdfObject->rapport_portefeuille)
      $pdfObject->SeqPageNo=0;
    
    $pdfObject->SeqPageNo++;
		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

	
		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if(is_file($pdfObject->rapport_logo))
		{
	    $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->w/2-$pdfObject->logoXsize/2, 3, $pdfObject->logoXsizeLoriantation);
		}

		$pdfObject->SetXY($pdfObject->w-$pdfObject->marge-90,14);
	  if($pdfObject->rapport_type != "VAR")
    {
      $periode= date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
      $maandDag=date("md", $pdfObject->rapport_datum);
      if(in_array($maandDag,array('0331','0630','0930','1231')))
      {
        $kwartaal=intval(ceil(date("n", $pdfObject->rapport_datum)/3));
        $kwartaalTeksen=array(1=>'1ste',2=>'2de',3=>'3de',4=>'4de');
        $periode.="\nRapportage ".$kwartaalTeksen[$kwartaal]." kwartaal ".date("Y", $pdfObject->rapport_datum);
      }
      $pdfObject->MultiCell(90, 4,"$periode", 0,'R');

      $pdfObject->AutoPageBreak=false;
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $voetTekst="| ".$pdfObject->rapport_depotbankOmschrijving." | ".
        $pdfObject->rapport_portefeuille." | ".
       // str_replace('SEQ ','Sequoia ',$pdfObject->portefeuilledata['SoortOvereenkomst'])." | ".
        $pdfObject->portefeuilledata['Risicoklasse']." | Productiedatum ".date('d-m-Y')." |";
      $pdfObject->setXY($pdfObject->marge,$pdfObject->h-8);
      $pdfObject->MultiCell(180, 4,$voetTekst, 0,'L');
      $pdfObject->AutoPageBreak=true;
    }
		$pdfObject->SetY($y);


	  $pdfObject->SetXY($pdfObject->marge,14);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize+1);
		$pdfObject->MultiCell(110,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		if(isset($pdfObject->rapport_sub_titel))
		{
			$pdfObject->SetXY($pdfObject->marge, 30 - 8);
			$pdfObject->MultiCell(297 - $pdfObject->marge*2, 4, vertaalTekst($pdfObject->rapport_sub_titel, $pdfObject->rapport_taal), 0, 'C');
			unset($pdfObject->rapport_sub_titel);
		}
  
     $pdfObject->setDrawColor($pdfObject->rapportLineColor[0],$pdfObject->rapportLineColor[1],$pdfObject->rapportLineColor[2]);
     $pdfObject->line($pdfObject->marge,23,$pdfObject->w-$pdfObject->marge,23);
      
     
		$pdfObject->SetY(30);
	 	$pdfObject->headerStart = $pdfObject->getY()+17;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  
		$pdfObject->rapport_portefeuilleLast = $pdfObject->rapport_portefeuille;
  }
}

	function HeaderVKM_L126($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderFRONT_L126($object)
{
  $pdfObject = &$object;
}

function HeaderFRONT1_L126($object)
{
  $pdfObject = &$object;
}

function HeaderFISCAAL_L126($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderFISCAAL($object);
}


function HeaderJOURNAAL_L126($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
}

function HeaderVAR_L126($object)
{
  $pdfObject = &$object;
	$pdfObject->ln();
}

function HeaderVOLKD_L126($object)
{
  HeaderVOLK_L126($object);
}
function HeaderVOLK_L126($object)
{
    $pdfObject = &$object;
		$pdfObject->ln();
    $borderBackup=$pdfObject->CellBorders;
    unset($pdfObject->CellBorders);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
			$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4];

			$actueel 			= $eindhuidige + $pdfObject->widthB[5];
			$eindactueel 	= $actueel + $pdfObject->widthB[6] + $pdfObject->widthB[7];

			$resultaat 		= $eindactueel + $pdfObject->widthB[8] ;
			$eindresultaat = $resultaat +  $pdfObject->widthB[9] +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12];



		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);


		// lijntjes onder beginwaarde in het lopende jaar
			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4],4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
			  $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);


			$pdfObject->row(array(vertaalTekst("Portefeuille overzicht",$pdfObject->rapport_taal),
                        vertaalTekst("Aantal",$pdfObject->rapport_taal),
                        vertaalTekst("% Weging",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
                    vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);


		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  
  $pdfObject->CellBorders=$borderBackup;
  
  }



function HeaderHUIS_L126($object)
{
    $pdfObject = &$object;
		
}
  
  
   function HeaderPERF_L126($object)
  {
	  	$pdfObject = &$object;

  }


function HeaderKERNZ_L126($object)
{
  $pdfObject = &$object;
  
}


function HeaderATT_L126($object)
	{
    $pdfObject = &$object;
    $colW=280/11;
    $pdfObject->widthA = array($colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW);//,23
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		//for($i=0;$i<count($pdfObject->widthA);$i++)
		//  $pdfObject->fillCell[] = 1;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);

		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopen\nrente",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
                          "Rendement\n "));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}

   function HeaderTRANS_L126($object)
  {
    $pdfObject=&$object;
    $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');


				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] ;

		$verkoop			= $inkoopEind+8;
		$verkoopEind = $verkoop + $pdfObject->widthB[6] + $pdfObject->widthB[7]-8;

		$resultaat			= $verkoopEind+8;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell(44,4, vertaalTekst("Uitgaven",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell(44,4, vertaalTekst("Ontvangsten",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell(66,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
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
										 vertaalTekst("Soort\ntransactie",$pdfObject->rapport_taal),
                     vertaalTekst("Fondsnaam",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										// vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 //vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische\nkostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopend jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }
  
  function HeaderSCENARIO_L126($object)
  {
    $pdfObject=&$object;
  }
function HeaderEND_L126($object)
{
  $pdfObject=&$object;
}

  function HeaderMUT2_L126($object)
  {
    $pdfObject=&$object;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->setX(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]));
		$pdfObject->Cell(110,4,vertaalTekst("Inkomsten",$pdfObject->rapport_taal),0,1,"C");
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());// + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]
		$pdfObject->ln(1);
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if(isset($pdfObject->customHeader))
      $pdfObject->row($pdfObject->customHeader);
		/*
		$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
										 vertaalTekst("Bruto",$pdfObject->rapport_taal),
										 vertaalTekst("Provisie",$pdfObject->rapport_taal),
										 vertaalTekst("Kosten",$pdfObject->rapport_taal),
										 vertaalTekst("Belasting",$pdfObject->rapport_taal),
										 vertaalTekst("Netto",$pdfObject->rapport_taal)));
    */
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


  function HeaderOIH_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderHSE();
	}

	function HeaderOIB_L126($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	 // $pdfObject->HeaderOIB();
     // $pdfObject->Ln();
	}

	function HeaderOIV_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}

	function HeaderPERFG_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFG();
	}
	function HeaderVHO_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderVHO();
	}
	function HeaderGRAFIEK_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L126($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L126($object)
	{
  	  $pdfObject = &$object;
  	//  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
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