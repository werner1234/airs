<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/14 16:43:21 $
 		File Versie					: $Revision: 1.15 $

 		$Log: PDFRapport_headers_L30.php,v $
 		Revision 1.15  2018/12/14 16:43:21  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/12/12 16:19:08  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/06/08 15:42:01  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/05/30 06:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/05/28 14:21:20  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/02/01 11:08:33  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/01/31 20:02:46  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/01/02 16:50:38  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/04/01 07:40:26  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2011/12/21 19:19:33  rvv
 		*** empty log message ***

 		Revision 1.3  2011/08/11 15:38:50  rvv
 		*** empty log message ***

 		Revision 1.2  2011/08/07 09:02:51  rvv
 		*** empty log message ***

 		Revision 1.1  2010/09/15 16:29:10  rvv
 		*** empty log message ***

 		Revision 1.1  2010/09/11 15:17:37  rvv
 		*** empty log message ***

 		Revision 1.2  2010/03/10 19:53:17  rvv
 		*** empty log message ***

 		Revision 1.1  2010/01/09 11:41:01  rvv
 		*** empty log message ***



*/
function Header_basis_L30($object)
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
			$logopos = 115;
		}

		if(is_file($pdfObject->rapport_logo))
		{

		  $factor=0.04;
		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, 1691*$factor, 586*$factor);
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
	  $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetX(100);

	  $pdfObject->SetXY($pdfObject->w/2-100/2,$y);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->w/2-100/2);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel2,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

    $pdfObject->SetY($y);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y+12);
    }
}

	function HeaderVKM_L30($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
/*
function HeaderVOLK_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVOLK();
}

function HeaderPERF_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERF();
}
function HeaderVHO_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVHO();
}

function HeaderTRANS_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderTRANS();
}

function HeaderMUT_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderMUT();
}

function HeaderOIB_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIB();
}
function HeaderOIV_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIV();
}
function HeaderOIR_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIR();
}
function HeaderOIS_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIS();
}

function HeaderCASH_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASH();
}

function HeaderGRAFIEK_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderGRAFIEK();
}
function HeaderPERFG_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERFG();
}

function HeaderCASHY_L30($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->HeaderCASHY();
  $pdfObject->ln();
}
*/


function HeaderDUURZAAM_L30($object)
{
    $pdfObject = &$object;
    $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->row(array(" ",
										 "Duurzaamheidsscores"));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);                     
		$pdfObject->row(array(" Fonds",
										 "Economisch",
										 "Sociaal",
										 "Milieu",
										 "Totaal"));
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + 285,$pdfObject->GetY());
}

function HeaderKERNV_L30($object)
{
  
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_kop_fontstyle, $pdfObject->rapport_fontsize);
  
}
function HeaderRISK_L30($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);


		$pdfObject->ln(2);
	  $pdfObject->Cell(100,4, "",0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		$pdfObject->ln(2);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array("",
										 "",
										 "",
										 "",
										 "",
										 ""));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + 285,$pdfObject->GetY());
  }

function HeaderZORG_L30($object)
{

	$pdfObject = &$object;
	$pdfObject->ln(6);
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + 285,$pdfObject->GetY());
}

	  function HeaderOIH_L30($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		foreach ($pdfObject->widthB as $id=>$value)
		{
		  if($id < 3)
		    $actueel +=$value;
		  if($id < 7)
		    $eindactueel +=$value;
		  if($id < 8)
		    $resultaat  +=$value;
		  if($id < 11)
		    $eindresultaat  +=$value;
		  if($id < 12)
		    $risico  +=$value;
		  if($id < 13)
		    $eindrisico  +=$value;
		  $eind +=$value;
		}

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell($eindactueel-$actueel,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell($eindresultaat-$resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
		//$pdfObject->SetX($pdfObject->marge+$risico);
		//$pdfObject->Cell($eindrisico-$risico,4, vertaalTekst("Risicoscore",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		//$pdfObject->Line(($pdfObject->marge+$risico),$pdfObject->GetY(),$pdfObject->marge + $eindrisico,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();
 		$pdfObject->row(array("",vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										vertaalTekst("Effectief\nRendement",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal)),
										"",
										''
										);


		$pdfObject->setY($y);
		$pdfObject->ln(8);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eind,$pdfObject->GetY());
		$pdfObject->ln();
	  }


if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object, $vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
		$pdfObject = &$object;
		$query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '" . $pdfObject->portefeuilledata['Vermogensbeheerder'] . "' ORDER BY Afdrukvolgorde";
		$border = 0;
		$DB = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if (($pdfObject->GetY() + $hoogte) > $pdfObject->pagebreak)
		{
			$pdfObject->AddPage();
			$pdfObject->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$julRapDatumVanaf = db2jul($rapportageDatumVanaf);
			$rapJaar = date('Y', $julRapDatumVanaf);
			$dagMaand = date('d-m', $julRapDatumVanaf);
			$januariDatum = $rapJaar . '-01-01';
			if ($dagMaand == '01-01')
			{
				$pdfObject->rapport_perfIndexJanuari = false;
			}
		}
		if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
		{
			$extraX = 26;
			$perfEur = 1;
			$perfVal = 0;
			$perfJan = 0;
		}
		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$perfEur = 0;
			$perfVal = 0;
			$perfJan = 1;
		}

		if ($pdfObject->printAEXVergelijkingProcentTeken)
		{
			$teken = '%';
		}
		else
		{
			$teken = '';
		}


		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$extraX += 51;
		}

		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r], $pdfObject->rapport_kop_bgcolor[g], $pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 110 + 19 + $extraX, $hoogte, 'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 110 + 19 + $extraX, $hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor[r],$pdfObject->rapport_kop4_fontcolor[g],$pdfObject->rapport_kop4_fontcolor[b]);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r], $pdfObject->rapport_kop_fontcolor[g], $pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font, $pdfObject->rapport_kop4_fontstyle, $pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(50, 4, vertaalTekst("Index-vergelijking", $pdfObject->rapport_taal), 0, 0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_fontstyle, $pdfObject->rapport_fontsize);
		//$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r], $pdfObject->rapport_kop_fontcolor[g], $pdfObject->rapport_kop_fontcolor[b]);
		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$pdfObject->Cell(26, 4, date("d-m-Y", db2jul($januariDatum)), $border, 0, "R");
		}
		$pdfObject->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatumVanaf)), $border, 0, "R");
		$pdfObject->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatum)), $border, 0, "R");

		$pdfObject->Cell(26, 4, vertaalTekst("Perf in %", $pdfObject->rapport_taal), $border, $perfVal, "R");
		if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
		{
			$pdfObject->Cell(26, 4, vertaalTekst("Perf in % in EUR", $pdfObject->rapport_taal), $border, $perfEur, "R");
		}
		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$pdfObject->Cell(26, 4, vertaalTekst("Jaar Perf.", $pdfObject->rapport_taal), $border, $perfJan, "R");
		}

		while ($perf = $DB->nextRecord())
		{
			if ($perf['Valuta'] != 'EUR')
			{
				if ($pdfObject->rapport_perfIndexJanuari == true)
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

			if ($pdfObject->rapport_perfIndexJanuari == true)
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
			$pdfObject->Cell(50, 4, $perf[Omschrijving], $border, 0, "L");
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '" . $pdfObject->rapport_portefeuille . "' ";
		$DB->SQL($query2);
		$DB->Query();

		while ($perf = $DB->nextRecord())
		{

			if ($perf['Valuta'] != 'EUR')
			{

				if ($pdfObject->rapport_perfIndexJanuari == true)
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

			if ($pdfObject->rapport_perfIndexJanuari == true)
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


			$pdfObject->Cell(50, 4, $perf['Omschrijving'], 0, 0, "L");
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers0['Koers'], 2), $border, 0, "R");
			}
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers1['Koers'], 2), $border, 0, "R");
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($koers2['Koers'], 2), $border, 0, "R");
			$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(26, 4, $pdfObject->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}
	}
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


if(!function_exists('getBenchmarkvergelijking'))
{
	function getBenchmarkvergelijking($object)
	{
		$rapportObject = &$object;
		$DB = new DB();
		$query = "SELECT Portefeuilles.SpecifiekeIndex,Fondsen.Omschrijving,Fondsen.Valuta
              FROM Portefeuilles Join Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
              WHERE Portefeuilles.Portefeuille='" . $rapportObject->portefeuille . "'";
		$DB->SQL($query);
		$DB->Query();
		$index = $DB->lookupRecord();

		$totalen = array();
		$totalen['SpecifiekeIndex'] = $index['SpecifiekeIndex'];
		$totalen['Omschrijving'] = $index['Omschrijving'];

		$zorgplichtPerFonds = array();
		$query = "SELECT
benchmarkverdeling.fonds,
benchmarkverdeling.percentage,
Fondsen.Omschrijving,
BeleggingscategoriePerFonds.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
BeleggingscategoriePerFonds.Vermogensbeheerder,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM
benchmarkverdeling
JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
JOIN BeleggingscategoriePerFonds ON benchmarkverdeling.fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "'
INNER JOIN ZorgplichtPerBeleggingscategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "'
WHERE benchmarkverdeling.benchmark='" . $index['SpecifiekeIndex'] . "'";
		$DB->SQL($query);
		$DB->Query();
		$zorgplicht = array();
		$samenstelling = array();
		while ($index = $DB->nextRecord())
		{
			$samenstelling[$index['fonds']] = $index['percentage'] / 100;
			$fondsOmschrijving[$index['fonds']] = $index['Omschrijving'];
			$zorgplicht['benchmark'][$index['Zorgplicht']] += $index['percentage'] / 100;
			$zorgplichtPerFonds[$index['fonds']] = $index['Zorgplicht'];
		}

		$query = "SELECT norm, Zorgplicht FROM ZorgplichtPerRisicoklasse WHERE Risicoklasse='" . $rapportObject->pdf->portefeuilledata['Risicoklasse'] . "'";
		$DB->SQL($query);
		$DB->Query();
		while ($index = $DB->nextRecord())
		{
			if ($index['Zorgplicht'] == '')
			{
				$index['Zorgplicht'] = 'geen';
			}
			$zorgplicht['portefeuille'][$index['Zorgplicht']] = $index['norm'] / 100;
		}

		$query = "SELECT Zorgplicht,norm FROM ZorgplichtPerPortefeuille WHERE Portefeuille='" . $rapportObject->portefeuille . "'";
		$DB->SQL($query);
		$DB->Query();
		while ($index = $DB->nextRecord())
		{
			if ($index['Zorgplicht'] == '')
			{
				$index['Zorgplicht'] = 'geen';
			}
			$zorgplicht['portefeuille'][$index['Zorgplicht']] = $index['norm'] / 100;
		}

		foreach ($zorgplicht['benchmark'] as $zp => $percentage)
		{
			if (isset($zorgplicht['portefeuille']))
			{
				$zorgplicht['zorgplichtFactor'][$zp] = $zorgplicht['portefeuille'][$zp] / $percentage;
			}
			else
			{
				$zorgplicht['zorgplichtFactor'][$zp] = 1;
			}
		}

		$indexData = array();
		foreach ($samenstelling as $fonds => $percentage)
		{
			$indexData[$fonds] = $index;
			foreach ($rapportObject->perioden as $periode => $datum)
			{
				$indexData[$fonds]['fondsKoers_' . $periode] = getFondsKoers($fonds, $datum);
				$indexData[$fonds]['valutaKoers_' . $periode] = getValutaKoers($index['Valuta'], $datum);
			}
			$indexData[$fonds]['performanceJaar'] = ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_jan']) / ($indexData[$fonds]['fondsKoers_jan'] / 100);
			$indexData[$fonds]['performance'] = ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin'] / 100);
			$indexData[$fonds]['performanceEurJaar'] = ($indexData[$fonds]['fondsKoers_eind'] * $indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_jan'] * $indexData[$fonds]['valutaKoers_jan']) / ($indexData[$fonds]['fondsKoers_jan'] * $indexData[$fonds]['valutaKoers_jan'] / 100);
			$indexData[$fonds]['performanceEur'] = ($indexData[$fonds]['fondsKoers_eind'] * $indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_begin'] * $indexData[$fonds]['valutaKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin'] * $indexData[$fonds]['valutaKoers_begin'] / 100);
		}

		//	$rapportObject->pdf->Rect($rapportObject->pdf->marge,$rapportObject->pdf->getY(),130,((count($indexData)+1)*4));
		// $rapportObject->pdf->ln(2);


		$regelData = array();
		foreach ($indexData as $fonds => $index)
		{
			$zp = $zorgplichtPerFonds[$fonds];
			$nieuweWeging = $samenstelling[$fonds] * $zorgplicht['zorgplichtFactor'][$zp];
			$rendementAandeel = $index['performance'] * $nieuweWeging;

			$regelData[] = array('fonds'                  => $fonds,
													 'zorgplicht'             => $zp,
													 'fondsKoers_begin'       => $index['fondsKoers_begin'],
													 'fondsKoers_eind'        => $index['fondsKoers_eind'],
													 'performance'            => $index['performance'],
													 'samenstellingBenchmark' => $samenstelling[$fonds] * 100,
													 'wegingPortefeuille'     => $zorgplicht['portefeuille'][$zp] * 100,
													 'factor'                 => $zorgplicht['zorgplichtFactor'][$zp],
													 'wegingNew'              => $nieuweWeging * 100,
													 'aandeelPerf'            => $rendementAandeel);

			$totalen['samenstellingBm'] += $samenstelling[$fonds];
			$totalen['weging'] += $nieuweWeging;
			$totalen['rendement'] += $rendementAandeel;

		}


		return array('opbouw' => $regelData, 'totaal' => $totalen);
	}
}

?>