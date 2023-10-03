<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/22 16:15:52 $
 		File Versie					: $Revision: 1.22 $

 		$Log: PDFRapport_headers_L22.php,v $
 		Revision 1.22  2018/12/22 16:15:52  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/09/11 08:30:02  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/08/31 16:18:01  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/07/16 15:16:49  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/03/27 17:34:16  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/07/12 15:30:53  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2014/05/29 12:09:33  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/05/25 14:38:33  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/04/12 16:28:12  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/12/15 14:52:51  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/12/12 16:54:24  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/11/10 15:42:19  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/06/23 15:20:24  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/06/20 18:11:09  rvv
 		*** empty log message ***

 		Revision 1.6  2011/12/11 10:58:53  rvv
 		*** empty log message ***

 		Revision 1.5  2010/07/11 16:00:05  rvv
 		*** empty log message ***

 		Revision 1.4  2010/06/16 19:15:03  rvv
 		*** empty log message ***

 		Revision 1.3  2009/09/30 15:52:07  rvv
 		*** empty log message ***

 		Revision 1.2  2009/09/28 09:23:50  cvs
 		*** empty log message ***

 		Revision 1.1  2009/09/27 12:54:02  rvv
 		*** empty log message ***

*/

function Header_basis_L22($object)
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
		   $factor=0.06;
		   $x=885*$factor;//$x=885*$factor;
		   $y=386*$factor;//$y=849*$factor;
		    $pdfObject->Image($pdfObject->rapport_logo, 230, 3, $x, $y);
		}
		else if(!empty($pdfObject->rapport_logo_tekst))
		{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor['r'],$pdfObject->rapport_logo_fontcolor['g'],$pdfObject->rapport_logo_fontcolor['b']);
			$pdfObject->SetFont($pdfObject->rapport_logo_font,$pdfObject->rapport_logo_fontstyle,$pdfObject->rapport_logo_fontsize);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst,0, "C");

			if ($pdfObject->rapport_logo_tekst2)
			{
			$pdfObject->SetX(110);
			$pdfObject->SetTextColor($pdfObject->rapport_logo_fontcolor2['r'],$pdfObject->rapport_logo_fontcolor2['g'],$pdfObject->rapport_logo_fontcolor2['b']);
			$pdfObject->SetFont($pdfObject->rapport_logo_font2,$pdfObject->rapport_logo_fontstyle2,$pdfObject->rapport_logo_fontsize2);
			$pdfObject->MultiCell(85	,4,$pdfObject->rapport_logo_tekst2,0, "C");
			}

			$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
			$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		}

		$pdfObject->SetY(5);
	  if($pdfObject->rapport_type != "VAR")
	    $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext.vertaalTekst("\n \nRapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}


		$pdfObject->SetX($x);

	 // $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->SetY(30-13);
	  $pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(97,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		if(isset($pdfObject->rapport_sub_titel))
		{
			$pdfObject->SetXY($pdfObject->marge, 30 - 8);
			$pdfObject->MultiCell(297 - $pdfObject->marge*2, 4, vertaalTekst($pdfObject->rapport_sub_titel, $pdfObject->rapport_taal), 0, 'C');
			unset($pdfObject->rapport_sub_titel);
		}
		$pdfObject->SetY(30);
	 	$pdfObject->headerStart = $pdfObject->getY()+4+13;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

		$pdfObject->rapport_portefeuilleLast = $pdfObject->rapport_portefeuille;
  }
}

	function HeaderVKM_L22($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderFRONT_L22($object)
{
  $pdfObject = &$object;
}

function HeaderVAR_L22($object)
{
  $pdfObject = &$object;
	$pdfObject->ln();
}

function HeaderVOLKD_L22($object)
{
  HeaderVOLK_L22($object);
}

function HeaderFISCAAL_L22($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->HeaderFISCAAL($object);
}


function HeaderINDEX_L22($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
}
function HeaderVOLK_L22($object)
{
    $pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		if($pdfObject->rapport_VOLK_volgorde_beginwaarde == 2 )
		{
			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige +$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4];

			$actueel 			= $eindhuidige + $pdfObject->widthB[5];
			$eindactueel 	= $actueel + $pdfObject->widthB[6] + $pdfObject->widthB[7];

			$resultaat 		= $eindactueel + $pdfObject->widthB[8] ;
			$eindresultaat = $resultaat +  $pdfObject->widthB[9] +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12];
		}
		else if ($pdfObject->rapport_layout == 8)
		{
			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
			$eindhuidige 	= $huidige +$pdfObject->widthB[4]+$pdfObject->widthB[5]+$pdfObject->widthB[6];

			$actueel 			= $eindhuidige + $pdfObject->widthB[7] + $pdfObject->widthB[8] ;
			$eindactueel 	= $actueel + $pdfObject->widthB[9] + $pdfObject->widthB[10] + $pdfObject->widthB[11];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[12] - 10;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[13] + $pdfObject->widthB[14]+ $pdfObject->widthB[15]+ $pdfObject->widthB[16] + $pdfObject->widthB[17] +10;
		}
		else
		{
			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
			$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6];
			$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

			$resultaat 		= $eindactueel + $pdfObject->widthB[10];
			$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13];
		}


		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);


					$y = $pdfObject->GetY();
			$pdfObject->setY($y-8);
			$pdfObject->SetX(100);
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$pdfObject->setY($y);

		// lijntjes onder beginwaarde in het lopende jaar
		if($pdfObject->rapport_VOLK_volgorde_beginwaarde == 2 )
		{
			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell(65,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
			  $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
		}
		else
		{
			$pdfObject->SetX($pdfObject->marge+$huidige+5);
			if($pdfObject->rapport_VOLK_volgorde_beginwaarde == 0 ||$pdfObject->rapport_VOLK_volgorde_beginwaarde == 2 )
				$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
			else
			{
				if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
			  else
			    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			}
			$pdfObject->SetX($pdfObject->marge+$actueel);
			if($pdfObject->rapport_VOLK_volgorde_beginwaarde == 0 ||$pdfObject->rapport_VOLK_volgorde_beginwaarde == 2 )
			{
				if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
			  else
			    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			}
			else
				$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
		}

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();

			$pdfObject->row(array("\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
                    vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	



		$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
			$pdfObject->SetWidths($pdfObject->widthA);
			$pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  }



function HeaderHUIS_L22($object)
{
    $pdfObject = &$object;
		
}
  
  
   function HeaderPERF_L22($object)
  {
	  	$pdfObject = &$object;
	  	$pdfObject->SetY($pdfObject->GetY()+4);
  	  $pdfObject->HeaderPERF();
  }
  
  
  function HeaderATT_L22($object)
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
		$pdfObject->ln();
		$pdfObject->Cell(94,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(94,4, date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
    $pdfObject->ln(1);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopenrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
                          "Rendement\n "));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}

   function HeaderTRANS_L22($object)
  {
    $pdfObject=&$object;
    $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
			$y = $pdfObject->GetY();
			$pdfObject->setY($y-8);
			$pdfObject->SetX(100);
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$pdfObject->setY($y);
			$pdfObject->ln(2);
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
			$pdfObject->ln();

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');


				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell(65,4, vertaalTekst("Uitgaven",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell(65,4, vertaalTekst("Ontvangsten",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell(65,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
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
										 vertaalTekst("Soort\ntrans-actie",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopend jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }
  
  function HeaderSCENARIO_L22($object)
  {
    $pdfObject=&$object;
  }


  function HeaderMUT2_L22($object)
  {
    $pdfObject=&$object;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$pdfObject->ln();

		$pdfObject->setX(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]));
		$pdfObject->Cell(110,4,vertaalTekst("Inkomsten",$pdfObject->rapport_taal),0,1,"C");
		$pdfObject->Line(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->ln(1);
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');




		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
										 vertaalTekst("Bruto",$pdfObject->rapport_taal),
										 vertaalTekst("Provisie",$pdfObject->rapport_taal),
										 vertaalTekst("Kosten",$pdfObject->rapport_taal),
										 vertaalTekst("Belasting",$pdfObject->rapport_taal),
										 vertaalTekst("Netto",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


  function HeaderOIH_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderHSE();
	}

	function HeaderOIB_L22($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	  $pdfObject->HeaderOIB();
      $pdfObject->Ln();
	}

	function HeaderOIV_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}

	function HeaderPERFG_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFG();
	}
	function HeaderVHO_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderVHO();
	}
	function HeaderGRAFIEK_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L22($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
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