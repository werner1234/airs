<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/08 15:27:18 $
 		File Versie					: $Revision: 1.8 $

 		$Log: PDFRapport_headers_L89.php,v $
 		Revision 1.8  2020/07/08 15:27:18  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2020/06/27 16:25:30  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2020/06/10 15:35:05  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2020/05/27 16:15:09  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2020/05/13 15:37:13  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2020/05/10 10:59:00  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/05/09 16:57:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/04/08 15:45:20  rvv
 		*** empty log message ***
 		


*/
function Header_basis_L89($object)
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
      $pdfObject->rapportNewPage = $pdfObject->page;
    }
    else
    {
  	  if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	  	$pdfObject->customPageNo = 0;
  
      if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
        $pdfObject->rapportNewPage = $pdfObject->page;

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
  
  
    if($pdfObject->rapport_type == "VKMA")
    {
      $rapport_koptextBackup=$pdfObject->rapport_koptext;
      $pdfObject->rapport_koptext="{Naam1}\nRisicoprofiel van uw portefeuille: {Risicoklasse}";
    }

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

		if(is_file($pdfObject->rapport_logo))
		{
      $logoW=54;
      $logopos = $pdfObject->w/2-$logoW/2;
		  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 6, $logoW);
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



		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}



		$pdfObject->SetY(8);
		$pdfObject->SetX($x);//vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n".
	  $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetX(100);

	  $pdfObject->SetXY($pdfObject->w/2-100/2,$y);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->w/2-100/2);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel2,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

    $pdfObject->SetY(8);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y+4);
      $pdfObject->headerStart = $pdfObject->getY()+15;
  
      $pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }
  
  if($pdfObject->rapport_type == "VKMA" && isset($rapport_koptextBackup))
  {
    $pdfObject->rapport_koptext=$rapport_koptextBackup;
  }
}

	function HeaderVKM_L89($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMA_L89($object)
{
  $pdfObject = &$object;
}

function HeaderOIB_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
  $lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  

    $pdfObject->SetX($pdfObject->marge+$lijn1+5);
    $pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
  $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());
  $pdfObject->SetDrawColor(0,0,0);
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
    
      $pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
                   vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
                   vertaalTekst("in valuta",$pdfObject->rapport_taal),
                   vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                   vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                   vertaalTekst("in %",$pdfObject->rapport_taal)));
    


  
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 // $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}

function HeaderVAR_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  
}

function HeaderMUT2_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->SetX(110);
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
  //$pdfObject->Cell(110,4,vertaalTekst("Inkomsten",$pdfObject->rapport_taal),0,1,"C");
 // $pdfObject->Line(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  $pdfObject->ln(1);
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  
  $pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
               vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
               vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
               vertaalTekst("Bruto",$pdfObject->rapport_taal),
               vertaalTekst("Kosten",$pdfObject->rapport_taal),
               vertaalTekst("Belasting",$pdfObject->rapport_taal),
               vertaalTekst("Netto",$pdfObject->rapport_taal)));
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
 // $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge,$pdfObject->GetY());
  $pdfObject->ln();
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}
function HeaderPERF_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERF();
}

function HeaderPERFG_L89($object)
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
     $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
     $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
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

function HeaderINDEX_L89($object)
{
  $pdfObject = &$object;
  
}


function HeaderFRONT_L89($object)
{
  $pdfObject = &$object;
  
}

function HeaderINHOUD_L89($object)
{
  $pdfObject = &$object;
}

function HeaderATT_L89($object)
{
  $pdfObject = &$object;
  $w=(297-$pdfObject->marge*2-5)/11;
  $tmp=array();
  for($i=0;$i<11;$i++)
    $tmp[]=$w;
  $tmp[0]+=5;
  $pdfObject->widthA = $tmp;//array(29,28,32,32,25,25,25,26,30,25,28);
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
                    vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
                    vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("per maand",$pdfObject->rapport_taal).")",
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge,$pdfObject->GetY());
  $pdfObject->ln(1);
}


function HeaderVOLK_L89_($object)
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
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
  // lijntjes onder beginwaarde in het lopende jaar
  $pdfObject->SetX($pdfObject->marge+$huidige-5);
  $y = $pdfObject->getY();
  $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
  $pdfObject->SetX($pdfObject->marge+$actueel);
  $pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetX($pdfObject->marge+$resultaat);
  $pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->SetDrawColor(255,255,255);
  $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
  $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
  $pdfObject->SetDrawColor(0,0,0);
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->setXY($pdfObject->marge,$y);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
  $teken=$pdfObject->rapportageValuta;
  
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
                    vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("in %",$pdfObject->rapport_taal)));
  
  
  $pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln();
}


function HeaderVOLK_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  

    $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
    $eindhuidige 	= $huidige +$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4];
    
    $actueel 			= $eindhuidige + $pdfObject->widthB[5];
    $eindactueel 	= $actueel + $pdfObject->widthB[6] + $pdfObject->widthB[7];
    
    $resultaat 		= $eindactueel + $pdfObject->widthB[8] ;
    $eindresultaat = $resultaat +  $pdfObject->widthB[9] +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12];
 
  
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 16 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  

    $pdfObject->SetX($pdfObject->marge+$huidige+5);
    $pdfObject->Cell(65,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$actueel);
    if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
      $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"L");
    else
      $pdfObject->Cell(50,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"L");
    $pdfObject->SetX($pdfObject->marge+$resultaat);
    $pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
  
  $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
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
                        vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                        vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
                   vertaalTekst("in %",$pdfObject->rapport_taal))
      );


  
  $pdfObject->setY($y);

    $pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
    $pdfObject->row(array("",vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln();
  $pdfObject->ln();
  
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
  $pdfObject->ln();
  $pdfObject->SetDrawColor(0,0,0);
  
}
function HeaderTRANS_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

    $pdfObject->SetX(100);
    $pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
    $pdfObject->ln();
  
  // achtergrond kleur
  
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    
    // afdrukken header groups
    $inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
    $inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];
    
    $verkoop			= $inkoopEind;
    $verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
    
  $resultaat			= $verkoopEind;
  $resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
  
    $pdfObject->SetX($inkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
    $pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($verkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
    $pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($resultaat);
//			$pdfObject->Cell(65,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C"); //81 ipv 65
    $pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->ln();
  
  //$pdfObject->SetDrawColor(255,255,255);
    $pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
    $pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
    $pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge,$pdfObject->GetY());
  //$pdfObject->SetDrawColor(0,0,0);
  
  // bij layout 1 zit het % totaal
  if($pdfObject->rapport_TRANS_procent == 1)
    $procentTotaal = "%";
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
    $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
                 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
                 vertaalTekst("Aantal",$pdfObject->rapport_taal),
                 vertaalTekst("Fonds",$pdfObject->rapport_taal),
                 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Aankoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 vertaalTekst("Resultaat voorafgaand verslagperiode",$pdfObject->rapport_taal),
                 vertaalTekst("Resultaat gedurende verslagperiode",$pdfObject->rapport_taal),
                 $procentTotaal));
  
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);

  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->w-$pdfObject->marge,$pdfObject->GetY());
  $pdfObject->ln(1);

}

function HeaderVHO_L89($object)
{
  $pdfObject = &$object;
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
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
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
  
  
  $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
    $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
    $pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
  $pdfObject->SetDrawColor(0,0,0);

  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  
  
  $y = $pdfObject->getY();
  
    $pdfObject->row(array("",
                 "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                 vertaalTekst("Aantal",$pdfObject->rapport_taal),
                 vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 "",
                 vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
                 vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                 '',
                 vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
                 "",
                 vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                 vertaalTekst("in %",$pdfObject->rapport_taal)));
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
  $pdfObject->setY($y);
  $pdfObject->row(array("Categorie\n"));
  $pdfObject->ln();
  $pdfObject->ln();
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  
 // $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
}

/*
function HeaderPERF_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERF();
}



function HeaderMUT_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderMUT();
}

function HeaderOIB_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIB();
}
function HeaderOIV_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIV();
}
function HeaderOIR_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIR();
}
function HeaderOIS_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIS();
}

function HeaderCASH_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderCASH();
}

function HeaderGRAFIEK_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderGRAFIEK();
}
function HeaderPERFG_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderPERFG();
}

function HeaderCASHY_L89($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->HeaderCASHY();
  $pdfObject->ln();
}
*/


function HeaderDUURZAAM_L89($object)
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

function HeaderKERNV_L89($object)
{
  
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_kop_fontstyle, $pdfObject->rapport_fontsize);
  
}

function HeaderKERNZ_L89($object)
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
function HeaderRISK_L89($object)
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

function HeaderZORG_L89($object)
{

	$pdfObject = &$object;
	$pdfObject->ln(6);
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + 285,$pdfObject->GetY());
}

	  function HeaderOIH_L89($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $actueel=0;
    $eindactueel=0;
    $eind=0;
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
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


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
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'], $pdfObject->rapport_kop_bgcolor['g'], $pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 110 + 19 + $extraX, $hoogte, 'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 110 + 19 + $extraX, $hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor['r'],$pdfObject->rapport_kop4_fontcolor['g'],$pdfObject->rapport_kop4_fontcolor['b']);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'], $pdfObject->rapport_kop_fontcolor['g'], $pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font, $pdfObject->rapport_kop4_fontstyle, $pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(50, 4, vertaalTekst("Index-vergelijking", $pdfObject->rapport_taal), 0, 0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_fontstyle, $pdfObject->rapport_fontsize);
		//$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'], $pdfObject->rapport_kop_fontcolor['g'], $pdfObject->rapport_kop_fontcolor['b']);
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