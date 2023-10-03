<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/07/01 13:47:10 $
 		File Versie					: $Revision: 1.29 $

 		$Log: PDFRapport_headers_L55.php,v $
 		Revision 1.29  2018/07/01 13:47:10  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2018/06/30 17:43:55  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2018/03/21 17:04:24  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/03/17 18:48:55  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2018/02/24 18:33:46  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2018/02/18 14:58:36  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/05/13 16:27:34  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/12/17 18:57:35  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/10/16 15:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/03/09 17:24:31  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/03/02 16:59:05  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2015/11/07 16:45:15  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2015/10/14 16:12:05  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2015/09/26 15:57:57  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/12/13 19:24:44  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/09/03 15:56:32  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2014/08/06 15:41:01  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/07/06 12:34:34  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/06/14 16:40:37  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/06/11 15:35:21  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/06/08 15:27:58  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/05/17 16:35:44  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/05/07 08:40:26  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/04/30 16:03:17  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/04/19 16:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/04/12 16:28:12  rvv
 		*** empty log message ***
 		


*/
function Header_basis_L55($object)
{
 $pdfObject = &$object;
	if($pdfObject->lastPortefeuille != $pdfObject->rapport_portefeuille && !empty($pdfObject->lastPortefeuille))
	{
		$pdfObject->rapportNewPage = $pdfObject->page;
	}

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
    $pdfObject->rapportNewPage = $pdfObject->page;
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
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", formatPortefeuille($pdfObject->rapport_portefeuille), $pdfObject->rapport_koptext);
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

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{

 		    $factor=0.06;
		    $xSize=371*$factor;
		    $ySize=354*$factor;
        //$logopos=297/2-$xSize/2;
        $logopos=297-$xSize-$pdfObject->marge;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 3, $xSize, $ySize);
		}


    $pdfObject->SetX($pdfObject->marge);
		$pdfObject->SetY($y);



	  //$pdfObject->MultiCell(60,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
	  $pdfObject->SetY($y+10);
	  $pdfObject->SetX($pdfObject->marge);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	  $pdfObject->MultiCell(150,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->headerStart = $pdfObject->getY()+15;
  	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }
$pdfObject->Ln();
$pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
}


  function HeaderINHOUD_L55($object)
  {
    $pdfObject = &$object;
		
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),297-2*$pdfObject->marge, 8, 'F');

  }

function HeaderFRONT_L55($object)
{
	$pdfObject = &$object;

}

function HeaderTRANSFEE_L55($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$widthBackup=$pdfObject->widths;
	$dataWidth=array(28,50,20,40,40,20,30,20,20,20,20,20);
	$pdfObject->SetWidths($dataWidth);
	$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R'));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	$lastColors=$pdfObject->CellFontColor;
	unset($pdfObject->CellFontColor);
	unset($pdfObject->CellBorders);
	if(!isset($pdfObject->vmkHeaderOnderdrukken))
	{
		$pdfObject->Row(array(vertaalTekst("Risico/categorie", $pdfObject->rapport_taal),
								 "" . vertaalTekst("Fonds", $pdfObject->rapport_taal),
								 "" . date('d-m-Y', $pdfObject->rapport_datum),
								 vertaalTekst("Prognose dl kosten %", $pdfObject->rapport_taal),
								 vertaalTekst("Prognose dl kosten absoluut", $pdfObject->rapport_taal),
								 "" . vertaalTekst("Weging", $pdfObject->rapport_taal),
								 "" . vertaalTekst("VKM Bijdrage", $pdfObject->rapport_taal)));
		unset($pdfObject->vmkHeaderOnderdrukken);
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	}
	$pdfObject->widths=$widthBackup;
	$pdfObject->CellFontColor=$lastColors;
	$pdfObject->SetLineWidth(0.1);
}

  function HeaderMUT_L55($object)
  {
	  $pdfObject = &$object;

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 		$pdfObject->SetX(100);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		$pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
										 vertaalTekst("Bankafschrift",$pdfObject->rapport_taal),
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
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }

  	function HeaderVKM_L55($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
  function HeaderAFM_L55($object)
  {
  	$pdfObject = &$object;
  	//$pdfObject->SetY($pdfObject->GetY()+4);
		$pdfObject->ln();

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	  $pdfObject->SetX($pdfObject->marge);
		  //$pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");

	  $pdfObject->SetWidths($pdfObject->widthA);
	  $pdfObject->SetAligns($pdfObject->alignA);


		$pdfObject->row(array(vertaalTekst("AFM-Categorie",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
    
  }
  


	function HeaderOIB_L55($object)
	{
  	  $pdfObject = &$object;
      $pdfObject->ln();
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

  	  //$pdfObject->HeaderOIB();
      $pdfObject->SetWidths($pdfObject->widthB);
      $pdfObject->SetAligns($pdfObject->alignB);
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
      $pdfObject->row(array('Hoofdcategorie','Sub-categorie',"Waarde EUR","In %"));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->Ln();
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	}

	
function HeaderTRANS_L55($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	
	
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

    //$pdfObject->Ln();

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);





			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Transactie-\nsoort",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
                     vertaalTekst("Valutakoers",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Meegekochte/ -verkochte rente",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
                     vertaalTekst("Kosten in EUR",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in EUR",$pdfObject->rapport_taal),
                     vertaalTekst("Resultaat in EUR",$pdfObject->rapport_taal)));
                     
                     
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

  }

  function HeaderATT_L55($object)
	{
    $pdfObject = &$object;
    /*
    if($pdfObject->page2att==true)
    {
    
    }
    else
    {
      $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
	  	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  		$pdfObject->SetWidths($pdfObject->widthA);
	  	$pdfObject->SetAligns($pdfObject->alignA);

  		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
      $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
	  	$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
		                      vertaalTekst("Koersresultaat",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Opgelopen\nrente",$pdfObject->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("maand",$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)." %\n(".vertaalTekst("cumulatief",$pdfObject->rapport_taal).")"));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
      $sumWidth = array_sum($pdfObject->widthA);
	    $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
    }
    */

	}

	function HeaderCASHY_L55($object)
	{
	    $pdfObject = &$object;
      
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->ln();
      $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),297-2*$pdfObject->marge, 8, 'F');
      
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

function HeaderHSE_L55($object)
{
	$pdfObject = &$object;
  $pdfObject->Ln();

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  $pdfObject->fillCell=array();
  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;
    

      
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
 	$pdfObject->row(array("\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    vertaalTekst("Regio",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Begin\nKoers",$pdfObject->rapport_taal),
                    vertaalTekst("Beginwaarde\nin Euro",$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal)."\n ",
										"\n ",
										vertaalTekst("Huidige\nKoers",$pdfObject->rapport_taal),
										vertaalTekst("Huidige waarde in Euro",$pdfObject->rapport_taal)."",
										vertaalTekst("Weging in categorie",$pdfObject->rapport_taal),
                    vertaalTekst("Weging in portefeuille",$pdfObject->rapport_taal),
										vertaalTekst("Koersresultaat\nperiode in %",$pdfObject->rapport_taal),//'',
                    "\n ",
										'',''));

		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
unset($pdfObject->fillCell);
	}


function HeaderOIS_L55($object)
{
	$pdfObject = &$object;
	$pdfObject->Ln();


}

function HeaderPERF_L55($object)
	  {

    $pdfObject = &$object;
    
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),297-2*$pdfObject->marge, 8, 'F');
		
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->ln();

  }

function HeaderVHO_L55($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] +  $pdfObject->widthB[14];
		$eindresultaat2 = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] ;

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	  $pdfObject->SetX($pdfObject->marge+$huidige);

		$pdfObject->Cell($pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5],4, vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Huidige koers",$pdfObject->rapport_taal), 0,0, "C");

		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(70,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());



		if($pdfObject->rapport_VHO_percentageTotaal == 1)
		{
				$aandeel = "Aandeel op totale waarde";
		}

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);


		$y = $pdfObject->getY();

   $pdfObject->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
											vertaalTekst("Aantal",$pdfObject->rapport_taal),
												vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
												vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
												vertaalTekst("Waarde in\n".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												"",
												vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
												vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
												vertaalTekst("Waarde in\n".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												vertaalTekst($aandeel,$pdfObject->rapport_taal),
												vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										    vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
												vertaalTekst("Totaalrende-\nment in %",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->setY($y);
	  $pdfObject->row(array("Categorie\n"));
		$pdfObject->ln();


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

  }  
  
function HeaderRISK_L55($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

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
		$kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'Regio' => 'OIR', 'Beleggingssector' => 'OIS', 'Hoofdcategorie' => 'OIB');
		$kleuren = $object->pdf->grafiekKleuren[$kleurVertaling[$type]];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
			" rapportageDatum = '" . $object->rapportageDatum . "' AND " .
			" portefeuille = '" . $object->portefeuille . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$portefwaarde = $DB->nextRecord();
		$portTotaal = $portefwaarde['totaal'];

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

if(!function_exists('fillLine'))
{
	function fillLine($object, $n, $fillArray = array())
	{
		$pdfObject = &$object;
		$rapportRegelSwich = array('HSE', 'VHO', 'OIB', 'MUT');
		if (in_array($pdfObject->rapport_type, $rapportRegelSwich))
		{
			$check = 1;
		}
		else
		{
			$check = 0;
		}
		$pdfObject->SetFillColor($pdfObject->regelFillKleur[0], $pdfObject->regelFillKleur[1], $pdfObject->regelFillKleur[2]);
		if (count($fillArray) == 0)
		{
			$fillArray = array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
		}
		if ($n % 2 != $check)
		{
			$pdfObject->fillCell = $fillArray;
		}
		else
		{
			unset($pdfObject->fillCell);
		}
		$n++;

		return $n;
	}
}

?>