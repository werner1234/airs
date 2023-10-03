<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.8 $

 		$Log: PDFRapport_headers_L8.php,v $
 		Revision 1.8  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/07/06 12:38:11  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2011/02/26 16:02:23  rvv
 		*** empty log message ***

 		Revision 1.5  2011/02/24 17:46:56  rvv
 		*** empty log message ***

 		Revision 1.4  2009/11/20 09:38:15  rvv
 		*** empty log message ***

 		Revision 1.3  2009/08/05 11:32:38  rvv
 		*** empty log message ***

 		Revision 1.2  2009/07/18 14:12:22  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/18 07:14:41  rvv
 		*** empty log message ***

 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/
function Header_basis_L8($object)
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

		if(is_file($pdfObject->rapport_logo))
		{
			 // $pdfObject->Image($pdfObject->rapport_logo, 18, 3.5, 52, 20.6);
			 $factor=0.12;
			 $x=512*$factor;
			 $y=90*$factor;
			 $pdfObject->Image($pdfObject->rapport_logo, 18, 13, $x, $y);
		}
    /*
		$factor = 1.0388;
		$kop=array(86*$factor,43*$factor,18.5*$factor,18.5*$factor,36.5*$factor,68*$factor);
		$marge =8;

		  $pdfObject->SetFillColor(104,109,156);
		  $pdfObject->Rect($marge, 23, $kop[0], 2, 'F');
		  $pdfObject->SetFillColor(144,127,94);
		  $pdfObject->Rect($marge+$kop[0], 23, $kop[1], 2, 'F');
		  $pdfObject->SetFillColor(226,198,160);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1], 23, $kop[2], 2, 'F');
		  $pdfObject->SetFillColor(166,146,139);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2], 23, $kop[3], 2, 'F');
		  $pdfObject->SetFillColor(131,72,90);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3], 23, $kop[4], 2, 'F');
		  $pdfObject->SetFillColor(200,72,69);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3]+$kop[4], 23, $kop[5], 2, 'F');
		  $pdfObject->SetXY(100,$y+18);
		*/

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


		if(is_file($pdfObject->rapport_logo))
		{
			 // $pdfObject->Image($pdfObject->rapport_logo, 18, 3.5, 52, 20.6);//43 15
			 $factor=0.12;
			 $x=512*$factor;
			 $y=90*$factor;
			 $pdfObject->Image($pdfObject->rapport_logo, 8, 5.5, $x, $y);
		}



		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		}

		$pdfObject->rapport_koptext = str_replace("{Rapportagedatum}",vertaalTekst("\nRapportagedatum:",$pdfObject->rapport_taal)." ".date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y"), $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", vertaalTekst($pdfObject->rapport_risicoklasse,$pdfObject->rapport_taal), $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", vertaalTekst($pdfObject->rapport_risicoprofiel,$pdfObject->rapport_taal), $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
	  	$pdfObject->rapport_naamtext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_naamtext);
	  	$pdfObject->rapport_naamtext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_naamtext);
		}
		else
		{
		  $pdfObject->rapport_naamtext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_naamtext);
		  $pdfObject->rapport_naamtext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_naamtext);
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		if($pdfObject->rapport_type == "MOD")
			$x = 100;
		else
			$x = 190;

		$pdfObject->SetY($y-4);
		$pdfObject->SetX($x);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize + 2);

		$pdfObject->MultiCell(90,8,rtrim($pdfObject->rapport_naamtext),0,'R');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetX($x);
		$pdfObject->MultiCell(100,4,$pdfObject->rapport_koptext,0,'R');


		$pdfObject->SetX($pdfObject->marge);

		$pdfObject->SetY($y);

//$pdfObject->SetLineStyle(array('color' => array(144,127,94)));
   $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize + 2);

			$pdfObject->SetX(80+$pdfObject->marge);

    if($pdfObject->rapport_type == "PERFG")
      $pdfObject->MultiCell(100,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
    else
		   $pdfObject->MultiCell(100,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal)." in ".$pdfObject->rapportageValuta,0,'L');
		$pdfObject->SetX(80+$pdfObject->marge);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst(vertaalTekst("Verslagperiode: ",$pdfObject->rapport_taal))." "
		.date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf).
		' '.vertaalTekst('t/m',$pdfObject->rapport_taal).' '.
		date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');

    /*
		$factor = 1.0388;
		$kop=array(86*$factor,43*$factor,18.5*$factor,18.5*$factor,36.5*$factor,68*$factor);
		$marge =8;

		  $pdfObject->SetFillColor(104,109,156);
		  $pdfObject->Rect($marge, 23, $kop[0], 2, 'F');
		  $pdfObject->SetFillColor(144,127,94);
		  $pdfObject->Rect($marge+$kop[0], 23, $kop[1], 2, 'F');
		  $pdfObject->SetFillColor(226,198,160);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1], 23, $kop[2], 2, 'F');
		  $pdfObject->SetFillColor(166,146,139);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2], 23, $kop[3], 2, 'F');
		  $pdfObject->SetFillColor(131,72,90);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3], 23, $kop[4], 2, 'F');
		  $pdfObject->SetFillColor(200,72,69);
		  $pdfObject->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3]+$kop[4], 23, $kop[5], 2, 'F');
		  */

		$pdfObject->SetXY(100,$y+18);

		$pdfObject->headerStart = $pdfObject->getY()+14;
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

	//	$pdfObject->SetLineStyle(array('color' => array(144,127,94)));
	//	$pdfObject->SetDrawColor(144,127,94);
		$pdfObject->SetDrawColor(119,119,119);

}
}

	function HeaderVKM_L8($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

 	  function HeaderFRONT_L8($object)
	  {
  	  $pdfObject = &$object;
	  }

 	  function HeaderOIH_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	  }

	  function HeaderOIBS_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	  }

	  function HeaderOIR_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	  }

		function HeaderHSE_L8($object)
	  {
  	  $pdfObject = &$object;
	    $pdfObject->HeaderHSE();
	  }

	  function HeaderOIB_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIB();
	  }

	  function HeaderOIV_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	  }

	  function HeaderPERF_L8($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERF();
	  }

	  function HeaderPERFD_L8($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderPERFD();
	  }

	  function HeaderVOLK_L8($object)
	  {
  	  $pdfObject = &$object;
  	  //$pdfObject->HeaderVOLK();

  	  if($pdfObject->skip_VOLK_header_L8 == false)
  	  {
    	$pdfObject->ln();
		  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
		$eindhuidige 	= $huidige +$pdfObject->widthB[4]+$pdfObject->widthB[5]+$pdfObject->widthB[6];
		$actueel 			= $eindhuidige + $pdfObject->widthB[7] + $pdfObject->widthB[8] ;
		$eindactueel 	= $actueel + $pdfObject->widthB[9] + $pdfObject->widthB[10] + $pdfObject->widthB[11];
		$resultaat 		= $eindactueel +  $pdfObject->widthB[12] - 15;
		$eindresultaat = $resultaat  +  $pdfObject->widthB[13] + $pdfObject->widthB[14]+ $pdfObject->widthB[15]+ $pdfObject->widthB[16] + $pdfObject->widthB[17] +15;

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
	    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
	  else
	    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
  			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$y = $pdfObject->getY();
		$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
										'',
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										"",
										vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),

										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										'',//"%",
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										'',//"%",
										'',//vertaalTekst("Totaal Resultaat %",$pdfObject->rapport_taal),
										vertaalTekst("Totaal Bijdrage %",$pdfObject->rapport_taal))
										);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths(array());
		$pdfObject->SetAligns(array());
		$pdfObject->setY($y);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  	  }

	  }

	  function HeaderVOLKD_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVOLKD();
	  }

	  function HeaderVHO_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVHO();
	  }
		  function HeaderTRANS_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderTRANS();
    }
	  	  function HeaderMUT_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMUT();
	  }

	  function HeaderGRAFIEK_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
  	  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
	  }

	  function HeaderATT_L8($object)
	  {
  	  $pdfObject = &$object;
  	//  $pdfObject->HeaderATT();
	  }

	  function HeaderCASH_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	  }

	  function HeaderCASHY_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASHY();
	  }

	  function HeaderPERFG_L8($object)
	  {
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFG();
	  }
?>