<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/12/11 11:17:44 $
 		File Versie					: $Revision: 1.11 $

 		$Log: PDFRapport_headers_L48.php,v $
 		Revision 1.11  2019/12/11 11:17:44  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/03/27 17:56:36  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/11/11 17:28:10  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/03/01 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/07/13 15:19:44  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/07/04 15:40:04  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/06/15 15:55:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/06/12 18:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/05/26 13:54:49  rvv
 		*** empty log message ***
 		

*/

function Header_basis_L48($object)
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
      
      $pdfObject->rapportNewPage = $pdfObject->page;
    }
    else
    {
  	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
      
      
    if(empty($pdfObject->lastPortefeuille) || $pdfObject->lastPortefeuille != $pdfObject->rapport_portefeuille)
    {
     	$pdfObject->rapportNewPage = $pdfObject->page;
    }

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

		if(is_file($pdfObject->rapport_logo))
		{
		   $factor=0.045;
		   $x=1000*$factor;//$x=885*$factor;
		   $y=620*$factor;//$y=849*$factor;
       $xStart=(297)/2-($x/2);
		   $pdfObject->Image($pdfObject->rapport_logo, $xStart, 0, $x, $y);
		}


		$pdfObject->SetY(5);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		
    $pdfObject->SetXY(297-48,4);
    $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->SetY($y);
    //.vertaalTekst("\n \nRapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)

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
    $pdfObject->SetY(30-2);
	  $pdfObject->SetX($pdfObject->marge);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->SetY(30);
	 	$pdfObject->headerStart = $pdfObject->getY()+4+13;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    
  }
  $pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
}

function HeaderFRONT_L48($object)
{
$pdfObject = &$object;
}

	function HeaderVKM_L48($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}


function HeaderVOLK_L48($object)
{
	$pdfObject = &$object;
  $pdfObject->Ln();

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

//  for($i=0;$i<count($pdfObject->widthA);$i++)
 //   $pdfObject->fillCell[] = 1;
      
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->rect($pdfObject->marge,$pdfObject->getY(),297-($pdfObject->marge*2),8,'F');
 	$pdfObject->row(array("",vertaalTekst("Categorie",$pdfObject->rapport_taal)."\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Vergelijken-\nde koers",$pdfObject->rapport_taal),
										vertaalTekst("Vergelijken-\nde waarde",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Marktwaarde",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Weging",$pdfObject->rapport_taal)."\n ",
                    "\n",//  vertaalTekst("Opgelopen\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Ongerealiseerd\nResultaat",$pdfObject->rapport_taal),
                    "\n ",
										vertaalTekst("in %",$pdfObject->rapport_taal)."\n ")
										);


		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
	//	$pdfObject->ln();
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
    unset($pdfObject->fillCell);
	}




function HeaderHUIS_L48($object)
{
    $pdfObject = &$object;
		
}
 
 
 function HeaderSCENARIO_L48($object)
{
    $pdfObject = &$object;
		
}
   
  
   function HeaderPERF_L48($object)
  {
	  	$pdfObject = &$object;
	  	$pdfObject->SetY($pdfObject->GetY()+4);
  	  $pdfObject->HeaderPERF();
  }

   function HeaderTRANS_L48($object)
  {
    $pdfObject=&$object;
    $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		//	$y = $pdfObject->GetY();
		//	$pdfObject->setY($y-8);
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


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');


				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] ;

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[6] + $pdfObject->widthB[7] ;

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell($pdfObject->widthB[4] + $pdfObject->widthB[5],4, vertaalTekst("Uitgaven",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell($pdfObject->widthB[6] + $pdfObject->widthB[7],4, vertaalTekst("Ontvangsten",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell(65,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();

		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);


			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Soort\ntrans-actie",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat",$pdfObject->rapport_taal),
										 "%"));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }


  function HeaderMUT_L48($object)
  {
    $pdfObject=&$object;
    $pdfObject->HeaderMUT();
  }


  function HeaderOIH_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderHSE();
	}

	function HeaderOIB_L48($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	  $pdfObject->HeaderOIB();
      $pdfObject->Ln();
	}

	function HeaderOIV_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}

	function HeaderPERFG_L48($object)
	{
  	  $pdfObject = &$object;
    $pdfObject = &$object;
    $pdfObject->widthA = array(28,29,28,28,28,28,28,28,28,29);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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
		                     	"Eindvermogen\n "));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}
	function HeaderPERFD_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFD();
	}
	function HeaderVOLKD_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderVOLKD();
	}
	function HeaderVHO_L48($object)
	{
	$pdfObject = &$object;
  $pdfObject->Ln();

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  //for($i=0;$i<count($pdfObject->widthA);$i++)
 //   $pdfObject->fillCell[] = 1;
      
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
    $pdfObject->rect($pdfObject->marge,$pdfObject->getY(),297-($pdfObject->marge*2),8,'F');
 	$pdfObject->row(array("",vertaalTekst("Categorie",$pdfObject->rapport_taal)."\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Historische-\n koers",$pdfObject->rapport_taal),
										vertaalTekst("Historische-\n waarde",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Marktwaarde",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Weging",$pdfObject->rapport_taal)."\n ",
                    '',//vertaalTekst("Opgelopen\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Ongerealiseerd\nResultaat",$pdfObject->rapport_taal),
                    "\n ",
										vertaalTekst("in %",$pdfObject->rapport_taal)."\n ")
										);


		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
	//	$pdfObject->ln();
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
unset($pdfObject->fillCell);
	}
	function HeaderGRAFIEK_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L48($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
	}


function HeaderATT_L48($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,24,24,24,20,20,25,24,24,23,23);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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
		                      "Rendement\n(maand)",
		                      "Rendement\n(Cumulatief)"));
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
			$extraX += 55;
		}

		$pdfObject->ln();
		$pdfObject->SetFillColor(230);

		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 130 + 15 + $extraX, $hoogte, 'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 130 + 15 + $extraX, $hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor[r],$pdfObject->rapport_kop4_fontcolor[g],$pdfObject->rapport_kop4_fontcolor[b]);
		$pdfObject->SetTextColor(0);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font, $pdfObject->rapport_kop4_fontstyle, $pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(60, 4, vertaalTekst("Index-vergelijking", $pdfObject->rapport_taal), 0, 0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_fontstyle, $pdfObject->rapport_fontsize);
		//$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
		$pdfObject->SetTextColor(0);
		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$pdfObject->Cell(28, 4, date("d-m-Y", db2jul($januariDatum)), $border, 0, "R");
		}
		$pdfObject->Cell(28, 4, date("d-m-Y", db2jul($rapportageDatumVanaf)), $border, 0, "R");
		$pdfObject->Cell(28, 4, date("d-m-Y", db2jul($rapportageDatum)), $border, 0, "R");

		if ($pdfObject->portefeuilledata['Layout'] == 30 || $pdfObject->portefeuilledata['Layout'] == 14 || $pdfObject->portefeuilledata['Layout'] == 25)
		{
			$pdfObject->Cell(28, 4, vertaalTekst("Perf in %", $pdfObject->rapport_taal), $border, $perfVal, "R");
		}
		else
		{
			$pdfObject->Cell(28, 4, vertaalTekst("Performance in %", $pdfObject->rapport_taal), $border, $perfVal, "R");
		}
		if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
		{
			$pdfObject->Cell(28, 4, vertaalTekst("Perf in % in EUR", $pdfObject->rapport_taal), $border, $perfEur, "R");
		}
		if ($pdfObject->rapport_perfIndexJanuari == true)
		{
			$pdfObject->Cell(28, 4, vertaalTekst("Jaar Perf.", $pdfObject->rapport_taal), $border, $perfJan, "R");
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
			$pdfObject->Cell(60, 4, $perf[Omschrijving], $border, 0, "L");
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
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


			$pdfObject->Cell(60, 4, $perf[Omschrijving], 0, 0, "L");
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($pdfObject->rapport_printAEXVergelijkingEur == 1)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($pdfObject->rapport_perfIndexJanuari == true)
			{
				$pdfObject->Cell(28, 4, $pdfObject->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}
	}
}
?>