<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/04/22 16:44:09 $
 		File Versie					: $Revision: 1.6 $

 		$Log: PDFRapport_headers_L50.php,v $
 		Revision 1.6  2017/04/22 16:44:09  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/12/24 16:34:19  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/09/07 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/08/24 15:48:47  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/06/30 15:07:33  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/09/19 16:53:18  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/09/16 12:45:46  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/05/02 15:53:13  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/04/25 15:20:45  rvv
 		*** empty log message ***

 		Revision 1.8  2012/04/14 16:51:17  rvv
 		*** empty log message ***

 		Revision 1.7  2012/03/28 15:55:19  rvv
 		*** empty log message ***

 		Revision 1.6  2012/03/17 11:58:16  rvv
 		*** empty log message ***

 		Revision 1.5  2012/03/14 17:30:11  rvv
 		*** empty log message ***

 		Revision 1.4  2012/03/11 17:19:57  rvv
 		*** empty log message ***

 		Revision 1.3  2012/03/08 07:58:38  rvv
 		*** empty log message ***

 		Revision 1.2  2012/02/29 16:52:49  rvv
 		*** empty log message ***

 		Revision 1.1  2012/02/26 15:17:43  rvv
 		*** empty log message ***

 		Revision 1.23  2012/02/19 16:13:11  rvv
 		*** empty log message ***


*/
function Header_basis_L50($object)
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

		$logopos = 10;

		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
		  $factor=0.075;
		  $xSize=469*$factor;
		  $ySize=277*$factor;
	    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize, $ySize);
		}



		if($pdfObject->rapport_type == "MOD" )
			$x = 60;
		else
			$x = 150;

		//$pdfObject->Line($pdfObject->marge,30,$x+140,30);
		$pdfObject->SetY($y);

		$pdfObject->MultiCell(140,4,$pdfObject->rapport_koptext,0,'L');

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

}

function HeaderVAR_L50($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetWidths(array(60,16,16,16,21,21,25, 5,  20,20,20,20,20));

	foreach ($pdfObject->widths as $id=>$waarde)
	{
		if($id < 1)
			$positie['fondsStart'] +=$waarde;
		if($id < 5)
			$positie['fondsEind'] +=$waarde;
		if($id < 8)
		{
			$positie['waardeStart'] +=$waarde;
			if($id==7)
			{
				$positie['midden'] = $positie['waardeStart'] ;
				$positie['midden'] -=$waarde/2;
			}
		}
		if($id < 11)
			$positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
	}
	foreach ($positie as $key=>$value)
		$positie[$key]+=$pdfObject->marge;

	$y=$pdfObject->GetY()+5;
	$pdfObject->pageTop=array($positie['midden'],$y+1);

	$pdfObject->SetAligns(array('L','L','L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
	$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));


	$pdfObject->row(array(
										"\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
										vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
										vertaalTekst("Rating debiteur",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),'',
										vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
										vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
										vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
										vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
										vertaalTekst("%",$pdfObject->rapport_taal)."  \n".vertaalTekst("port.",$pdfObject->rapport_taal)));


	unset($pdfObject->CellBorders);//"Modified\nduration",
}

	function HeaderVKM_L50($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
  function HeaderPERF_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	    $pdfObject->SetXY(110,32);
	    $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
      $pdfObject->ln(6);
	}

  function HeaderOIV_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderPERFG_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}


  function HeaderOIS_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderVOLK_L50($object)
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

  function HeaderINDEX_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderTRANS_L50_OLD($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
 
			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Type",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Koers",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoopwaarde in euro",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoopwaarde in euro",$pdfObject->rapport_taal),
										 vertaalTekst("Gerealiseerd resultaat",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal)));
       $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge+ array_sum($pdfObject->widthA),$pdfObject->GetY());
             
      $pdfObject->ln(1);
	}
  
  	function HeaderMUT_L50($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 		
    $pdfObject->SetXY(110,32);
	 	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	
		  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

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

	function HeaderCASHY_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderRISK_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

function HeaderHUIS_L50($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}


function HeaderATT_L50($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  	  function HeaderHSE_L50($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
      $dataWidth=array(28,55,20,20,20,20,22,22,22,22,22);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
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
      "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
      if(is_array($pdfObject->widthsBackup))
       $pdfObject->widths=$pdfObject->widthsBackup;
     // listarray($pdfObject->widths);echo "new page <br>\n";
    }

	function HeaderVHO_L50($object)
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
			elseif(strtolower($pdfObject->Hcat) == "vastrentende waarden")
			{
			  $pdfObject->SetWidths($pdfObject->widthB);
				 $pdfObject->row(array('','Naam',
												"sust.\nrating",
												'cr. rating',
												'nominaal/aantal',
												"huidige koers",'Kostprijs',
												"aanschaf in EUR",
												"onger. resul-\ntaat",
												"opgelopen rente",
												"coupon datum",
												"eff. ren-\ndement",
												"dura-\ntion",
												"markt-\nwaarde",
												"% op totaal")
												);
			}
      else
			{
			  $pdfObject->SetWidths($pdfObject->widthA);
	      $pdfObject->row(array('','Naam',
                        "sust.\nrating",
												'aantal',
												'valuta',
												"huidige koers",'Kostprijs',
												"aanschafwaarde in EUR",
												"ongerealiseerd valutaresultaat",
												"ongerealiseerd fondsresultaat",
												"Marktwaarde in EUR",
												"% op totaal")
												);
			}


	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}



?>