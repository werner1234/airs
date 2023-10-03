<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:12:28 $
 		File Versie					: $Revision: 1.21 $

 		$Log: PDFRapport_headers_L44.php,v $
 		Revision 1.21  2019/11/16 17:12:28  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2019/10/12 17:11:44  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/10/11 17:39:17  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/09/05 15:53:27  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/06/10 11:45:56  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/06/10 06:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/06/09 15:58:54  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/03/11 10:53:28  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/03/04 10:14:13  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/02/24 18:33:46  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/08/21 08:52:52  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/11/24 16:03:42  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/11/23 17:23:24  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/04/27 16:29:28  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/03/24 09:41:15  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/03/23 16:19:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/03/20 16:56:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/03/17 10:58:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/03/13 17:01:08  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/12/15 14:52:51  rvv
 		*** empty log message ***

*/

function Header_basis_L44($object)
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
		$pdfObject->SetDrawColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
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
           if($pdfObject->portefeuilledata['Vermogensbeheerder']=='IBE')
           {
		     $factor=0.05;
		     $x=1292*$factor;//$x=885*$factor;
		     $y=400*$factor;//$y=849*$factor;
 	         $pdfObject->Image($pdfObject->rapport_logo, 223, 5, $x, $y);
           }
           else
           {
             $factor = 0.045;
             $x = 1392 * $factor;//$x=885*$factor;
             $y = 420 * $factor;//$y=849*$factor;
             $pdfObject->Image($pdfObject->rapport_logo, 225, 5, $x, $y);
             $y=23;
           }
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

		$pdfObject->SetY(5);
	  $pdfObject->MultiCell(150,4,$pdfObject->rapport_koptext.vertaalTekst("\n \nRapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
		{
			$x = 160;
			$pwidth=210;
		}
		else
		{
			$x = 250;
			$pwidth=297;
		}
    
    $break=$pdfObject->AutoPageBreak;

    $pdfObject->AutoPageBreak=0;
    $pdfObject->SetXY(255,200);
    $pdfObject->SetTextColor(0,0,0);
    $pdfObject->MultiCell(35,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');;//."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->AutoPageBreak=$break;
    $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);

			if($pdfObject->rapport_type <> "DOORKIJK")
			{
				$txt = vertaalTekst("Verslagperiode", $pdfObject->rapport_taal) . " " .
					date("j", $pdfObject->rapport_datumvanaf) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datumvanaf)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datumvanaf) . " " .
					vertaalTekst("tot en met", $pdfObject->rapport_taal) . " " .
					date("j", $pdfObject->rapport_datum) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datum)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datum) . " ";
				$width = $pdfObject->GetStringWidth($txt);

				$pdfObject->setY($y + 2);
				$pdfObject->SetX($pwidth / 2 - $width / 2);

				$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_kop_fontstyle, $pdfObject->rapport_fontsize);
				$pdfObject->Write(4, vertaalTekst("Verslagperiode", $pdfObject->rapport_taal) . " ");
				$pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
				$pdfObject->Write(4, date("j", $pdfObject->rapport_datumvanaf) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datumvanaf)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datumvanaf) . " ");
				$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_kop_fontstyle, $pdfObject->rapport_fontsize);
				$pdfObject->Write(4, vertaalTekst("tot en met", $pdfObject->rapport_taal) . " ");
				$pdfObject->SetFont($pdfObject->rapport_font, 'b', $pdfObject->rapport_fontsize);
				$pdfObject->Write(4, date("j", $pdfObject->rapport_datum) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datum)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datum) . " ");
				$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_kop_fontstyle, $pdfObject->rapport_fontsize);
			}

			$pdfObject->SetX($x);

	 // $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->SetY(30-13);
	  $pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetY(30);
	 	$pdfObject->headerStart = $pdfObject->getY()+4+13;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
  }
}



	function HeaderVKMS_L44($object)
	{
		$pdfObject = &$object;
		$pdfObject->ln();
		$widthBackup=$pdfObject->widths;
		$dataWidth=array(28,50,20,20,20,20,20,18,18,18,18,18,15);
		$pdfObject->SetWidths($dataWidth);
		$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);

		$lastColors=$pdfObject->CellFontColor;
		unset($pdfObject->CellFontColor);
		unset($pdfObject->CellBorders);
		if(!isset($pdfObject->vmkHeaderOnderdrukken))
		{
			$pdfObject->Row(array(vertaalTekst("Risico\ncategorie", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
									 "\n" . date('d-m-Y', $pdfObject->rapport_datumvanaf),
									 "\n" . date('d-m-Y', $pdfObject->rapport_datum),
									 "\n" . vertaalTekst("Mutaties", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
									 vertaalTekst("Gemiddeld vermogen", $pdfObject->rapport_taal),
									 vertaalTekst("Doorl. kosten %", $pdfObject->rapport_taal),
									 vertaalTekst("Trans Cost %", $pdfObject->rapport_taal),
									 vertaalTekst("Perf Fee %", $pdfObject->rapport_taal),
									 vertaalTekst("Fondskost. absoluut", $pdfObject->rapport_taal),
									 "\n" . vertaalTekst("Weging", $pdfObject->rapport_taal),
									 vertaalTekst("VKM\nBijdrage", $pdfObject->rapport_taal)));
			unset($pdfObject->vmkHeaderOnderdrukken);
			$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
		}
		$pdfObject->widths=$widthBackup;
		$pdfObject->CellFontColor=$lastColors;
		$pdfObject->SetLineWidth(0.1);
	}

function HeaderVKM_L44($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderVKM();
}

function HeaderVOLK_L44($object)
{
    $pdfObject = &$object;

	if($pdfObject->skipRapportHeader==true)
	{
		$pdfObject->ln();
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
		$pdfObject->ln();
		unset($pdfObject->skipRapportHeader);
		return ;
	}
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6] ;
			$eindactueel 	= $actueel  + $pdfObject->widthB[7]+ $pdfObject->widthB[8];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[9] ;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13];
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);


			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell(80,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();


			$pdfObject->row(array("\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	


		$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
			$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  }



function HeaderHUIS_L44($object)
{
    $pdfObject = &$object;
		
}

function HeaderDOORKIJK_L44($object)
{
	$pdfObject = &$object;

}

   function HeaderPERF_L44($object)
  {
	  	$pdfObject = &$object;
	  	$pdfObject->SetY($pdfObject->GetY()+4);
  	  $pdfObject->HeaderPERF();
  }

   function HeaderTRANS_L44($object)
  {
    $pdfObject=&$object;
    $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
			$pdfObject->ln(2);
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
			$pdfObject->ln();

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
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
										 vertaalTekst("Soort\ntrans-\nactie",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Koers",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopend jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }


  function HeaderMUT2_L44($object)
  {
    $pdfObject=&$object;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		$pdfObject->ln();

		$pdfObject->setX(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]));
		$pdfObject->Cell(110,4,vertaalTekst("Inkomsten in",$pdfObject->rapport_taal).' '.$pdfObject->rapportageValuta,0,1,"C");
		$pdfObject->Line(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->ln(1);
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');




		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Uitgaven",$pdfObject->rapport_taal).' '.$pdfObject->rapportageValuta,
										 vertaalTekst("Bruto",$pdfObject->rapport_taal),
										 vertaalTekst("Provisie",$pdfObject->rapport_taal),
										 vertaalTekst("Kosten",$pdfObject->rapport_taal),
										 vertaalTekst("Belasting",$pdfObject->rapport_taal),
										 vertaalTekst("Netto",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


  function HeaderOIH_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L44($object)
	{
			$pdfObject = &$object;

			if($pdfObject->skipRapportHeader==true)
			{
				$pdfObject->ln();
				$pdfObject->ln();
				$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
				$pdfObject->ln();
				unset($pdfObject->skipRapportHeader);
				return ;
			}
			$pdfObject->ln();
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6] ;
			$eindactueel 	= $actueel  + $pdfObject->widthB[7]+ $pdfObject->widthB[8];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[9] ;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13];


			// achtergrond kleur
			$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
			$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
			$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);


			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell(80,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
				$pdfObject->Cell(55,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
				$pdfObject->Cell(55,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");


			$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
			$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
			$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


			$pdfObject->SetWidths($pdfObject->widthB);
			$pdfObject->SetAligns($pdfObject->alignB);

			$y = $pdfObject->getY();


			$pdfObject->row(array("\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("Valuta",$pdfObject->rapport_taal),
												vertaalTekst("Koers",$pdfObject->rapport_taal),
												vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
												"",
												vertaalTekst("Koers",$pdfObject->rapport_taal),
												vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst("",$pdfObject->rapport_taal),
												vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
												vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal))
			);



			$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
			$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

			$pdfObject->SetWidths($pdfObject->widthB);
			$pdfObject->SetAligns($pdfObject->alignB);
			$pdfObject->ln();

			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
			$pdfObject->ln();
		}


	function HeaderOIB_L44($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	  $pdfObject->HeaderOIB();
      $pdfObject->Ln();
	}

	function HeaderOIV_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}

	function HeaderPERFG_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFG();
	}
	function HeaderPERFD_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFD();
	}
function HeaderVOLKD_L44($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $dataWidth=array(28,55,15,25,25,20,22,25,22,18,20);
  $pdfObject->SetWidths($dataWidth);
  $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
  
  
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
                    "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  if(is_array($pdfObject->widthsBackup))
    $pdfObject->widths=$pdfObject->widthsBackup;
  // listarray($pdfObject->widths);echo "new page <br>\n";
}
	function HeaderVHO_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderVHO();
	}
	function HeaderGRAFIEK_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L44($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
	}



  function HeaderATT_L44($object)
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
		$pdfObject->ln();
		$pdfObject->row(array("Periode\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopen\nrente",
		                      "Beleggings-\nresultaat",
		                     	"Eindvermogen",
                          "Rendement"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

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