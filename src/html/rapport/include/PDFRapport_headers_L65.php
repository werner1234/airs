<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/11 17:30:27 $
 		File Versie					: $Revision: 1.38 $

 		$Log: PDFRapport_headers_L65.php,v $
 		Revision 1.38  2020/07/11 17:30:27  rvv
 		*** empty log message ***
 		
 	
*/
function Header_basis_L65($object)
{
 $pdfObject = &$object;

	  if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
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
      

     
		$pdfObject->customPageNo++;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		//$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_default_fontcolor['r'],$pdfObject->rapport_default_fontcolor['g'],$pdfObject->rapport_default_fontcolor['b']);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;  
		}
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $pdfObject->rapport_depotbank, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{DepotbankOmschrijving}", $pdfObject->rapport_depotbankOmschrijving, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoklasse}", $pdfObject->rapport_risicoklasse, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Risicoprofiel}", $pdfObject->rapport_risicoprofiel, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Client}", $pdfObject->rapport_client, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $pdfObject->rapport_clientVermogensbeheerder, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Accountmanager}", $pdfObject->rapport_accountmanager, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{SoortOvereenkomst}", $pdfObject->portefeuilledata['SoortOvereenkomst'], $pdfObject->rapport_koptext);
		
		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
	  	$pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->__appvar['consolidatie']['portefeuillenaam1'], $pdfObject->rapport_koptext);
	  	$pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->__appvar['consolidatie']['portefeuillenaam2'], $pdfObject->rapport_koptext);
		}
		else 
		{		
		  $pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		  $pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);
		}
		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 127;
		}

		if(is_file($pdfObject->rapport_logo))
		{
	  		$logopos=$pdfObject->marge;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, 29);
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
      $pdfObject->ln(1);
		$pdfObject->MultiCell(120,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD")
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);
 
		$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetXY($pdfObject->marge,$y);
 
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->ln(12);
    $pdfObject->SetX(0);
		$pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		
		$pdfObject->SetY($y+20);
    $pdfObject->headerStart=$pdfObject->GetY()+15;
    }

}

	function HeaderVKM_L65($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

  function HeaderVKMS_L65($object)
  {
    $pdfObject = &$object;
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 6 , 'F');
    $pdfObject->ln();
    $pdfObject->SetTextColor(0);
  }

  function HeaderVKMD_L65($object)
  {
    $pdfObject = &$object;
    $pdfObject->ln();
    $widthBackup=$pdfObject->widths;
    $dataWidth=array(28,48,20,20,20,20,20,18,18,18,18,18,15);
    $pdfObject->SetWidths($dataWidth);
    $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
 

    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge,8 , 'F');
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
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
                   vertaalTekst("Fondskost absoluut", $pdfObject->rapport_taal),
                   "\n" . vertaalTekst("Weging", $pdfObject->rapport_taal),
                   vertaalTekst("VKM\nBijdrage", $pdfObject->rapport_taal)));
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());

    $pdfObject->widths=$widthBackup;

    $pdfObject->SetLineWidth(0.1);
    $pdfObject->SetTextColor(0);
    
    
  }

function HeaderRISK_L65($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 6 , 'F');
	$celw=(297-2*$pdfObject->marge)/2;
	//Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->Cell($celw,6,vertaalTekst("Verloop standaarddeviatie",$pdfObject->rapport_taal),0,0,'C');
	$pdfObject->Cell($celw,6,vertaalTekst("Risicoparameters",$pdfObject->rapport_taal),0,0,'C');
	$pdfObject->SetTextColor(0);


}

function HeaderKERNV_L65($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-2*$pdfObject->marge, 6 , 'F');
  $celw=(297-2*$pdfObject->marge)/2;
  //Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->Cell($celw,6,vertaalTekst("Verloop standaarddeviatie",$pdfObject->rapport_taal),0,0,'C');
  $pdfObject->Cell($celw,6,vertaalTekst("Risicoparameters",$pdfObject->rapport_taal),0,0,'C');
  $pdfObject->SetTextColor(0);
  
  
}
	
	  function HeaderSCENARIO_L65($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }

function HeaderFACTUUR_L65($object)
{
	$pdfObject = &$object;
	//$pdfObject->headerSCENARIO();

}

function HeaderFRONT_L65($object)
{
	$pdfObject = &$object;
	//$pdfObject->headerSCENARIO();

}

    function HeaderKERNZ_L65($object)
    {
	    $pdfObject = &$object;
	  }

function HeaderTRANSFEE_L65($object)
{
  headerHUIS_L65($object);
}

function HeaderSMV_L65($object)
{
  headerOIH_L65($object);
}


function HeaderHUIS_L65($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

  $x=$pdfObject->getX();
  $y=$pdfObject->getY();
  $pdfObject->AutoPageBreak=false;
  $pdfObject->setXY(0,210-$pdfObject->marge);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-2);
  if(isset($pdfObject->huis3) && $pdfObject->huis3==true)
  {
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-3.75);
    $pdfObject->Cell(297, 4, vertaalTekst('*De portefeuilledetails zoals hierboven aangegeven zijn gebaseerd op de laatste ultimo maand posities in het beleggingsfonds. Gedurende de maand kunnen hier wijzigingen in ontstaan.', $pdfObject->rapport_taal) . ' ' .
                             vertaalTekst('De spreiding per regio is gebaseerd op het vestigingsland van de positie en Europa plus staat voor Europa, Midden-Oosten en Afrika', $pdfObject->rapport_taal), 0, 0, 'C');
  }
  else
  {
    $pdfObject->Cell(297, 4, vertaalTekst('*De grootste posities per beleggingsfonds zoals hierboven aangegeven zijn gebaseerd op de laatste ultimo maand posities in het respectievelijke beleggingsfonds.', $pdfObject->rapport_taal) . ' ' .
                             vertaalTekst('Gedurende de maand kunnen hier wijzigingen in ontstaan.', $pdfObject->rapport_taal), 0, 0, 'C');
  }
  $pdfObject->AutoPageBreak=true;
  $pdfObject->setXY($x,$y);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  
}
    
    function HeaderTRANS_L65($object)
	  {
	     $pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$pdfObject->SetX(100);
			$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
			$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);


		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Transactie",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
									 	 vertaalTekst("Aantal",$pdfObject->rapport_taal),
									 	 vertaalTekst("Koers in Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in Euro",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("Transactie\nkosten",$pdfObject->rapport_taal),
										 vertaalTekst("Totaal",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 $procentTotaal));
      //$pdfObject->ln(2); 
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
   	  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

	  }
    
    function HeaderMUT_L65($object)
	  {
	    $pdfObject = &$object;
   		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  	  $pdfObject->SetX(100);
	  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		  $pdfObject->ln();
		  // achtergrond kleur
		  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		  $pdfObject->SetWidths($pdfObject->widthB);
		  $pdfObject->SetAligns($pdfObject->alignB);
		  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
      $pdfObject->Ln(2);
		  $pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 "",
										 "",
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
		  $pdfObject->ln(2);
		  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    }
    
	  function HeaderAFM_L65($object)
	  {
	    $pdfObject = &$object;
	  
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


		  $pdfObject->SetX($pdfObject->marge+$lijn1+5);
		  $pdfObject->MultiCell(90,4, '', 0, "C");
$pdfObject->ln(2);
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
			$pdfObject->row(array(vertaalTekst("AFM categorie",$pdfObject->rapport_taal),'Valuta',
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal),
                      vertaalTekst("in %",$pdfObject->rapport_taal)));
$pdfObject->ln(2);
	

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	  }
	  function HeaderINHOUD_L65($object)
	  {
	    $pdfObject = &$object;
	    //$pdfObject->headerSCENARIO();

	  }

    function HeaderZORG_L65($object)
    {
	    $pdfObject = &$object;
			$pdfObject->ln();
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Weging'));
			$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
			$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 	  }

function HeaderPERF_L65($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->ln();
      $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
      $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  		$pdfObject->ln(2);
	 	  $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
    	$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  		$pdfObject->ln(2);

$pdfObject->ln();
		  $pdfObject->SetWidths($pdfObject->widthA);
		  $pdfObject->SetAligns($pdfObject->alignA);
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	  }
    
    function HeaderINDEX_L65($object)
	  {
	    $pdfObject = &$object;

	  }
    
    function HeaderOIB_L65($object)
	  {
	    $pdfObject = &$object;
     	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }
  function HeaderATT_L65($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
    $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);

    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
    $pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
      vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
      vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
      vertaalTekst("Koersresultaat (totaal)*",$pdfObject->rapport_taal)."\n ",
      vertaalTekst("Inkomsten",$pdfObject->rapport_taal)."\n ",
      vertaalTekst("Kosten",$pdfObject->rapport_taal)."\n ",
      vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
      vertaalTekst("Resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n ",
      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $sumWidth = array_sum($pdfObject->widthA);
    $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}
   function HeaderPERFG_L65($object)
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
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n ",
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

function HeaderOIH_L65($object)
{
  $pdfObject = &$object;
  HeaderVOLK_L65($object);
  
  $x=$pdfObject->getX();
  $y=$pdfObject->getY();
  $pdfObject->AutoPageBreak=false;
  $pdfObject->setXY(0,210-$pdfObject->marge);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-2);
  $pdfObject->SetTextColor(0);
  $pdfObject->Cell(297, 4,vertaalTekst('*De posities zoals hierboven aangegeven zijn gebaseerd op de laatste ultimo maand posities in het',$pdfObject->rapport_taal).' '.$pdfObject->huisfondsOmschrijving.'. '.vertaalTekst('Gedurende de maand kunnen hier wijzigingen in ontstaan.',$pdfObject->rapport_taal), 0, 0,'C');
  $pdfObject->AutoPageBreak=true;
  $pdfObject->setXY($x,$y);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

  function HeaderVOLK_L65($object)
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
    $pdfObject->SetDrawColor(255,255,255);
    if(isset($pdfObject->huisAandeel) && $pdfObject->huisAandeel<>1)
    {
      $pdfObject->Cell(65, 4, '', 0, 0, "C");
    }
    else
    {
      $pdfObject->Cell(65, 4, date('d-m-Y', $pdfObject->rapport_datumvanaf), 0, 0, "C");
      $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
    }
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, date('d-m-Y',$pdfObject->rapport_datum), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");

		
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
    $pdfObject->SetDrawColor(0,0,0);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->setXY($pdfObject->marge,$y);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
      $teken=$pdfObject->rapportageValuta;

		if(date('d-m',$pdfObject->rapport_datumvanaf)!='01-01')
		{
			$verslagperiode=vertaalTekst("Rend. in % verslagp.",$pdfObject->rapport_taal);
			$lopendeJaar=vertaalTekst("Rend. in % lopend jaar",$pdfObject->rapport_taal);
		}
		else
		{
			$verslagperiode='';
			$lopendeJaar=vertaalTekst("Rend. in % lopend jaar",$pdfObject->rapport_taal);
		}
		
    if(isset($pdfObject->huisAandeel) && $pdfObject->huisAandeel<>1)
    {
      $pdfObject->row(array("",
                        "\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                        "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                        "","","","",
                        "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                        "\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
                        "\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
                        "\n".vertaalTekst("in %",$pdfObject->rapport_taal),
                        "","","",""
                      ));
    }
		$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat*",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
										$verslagperiode,
										$lopendeJaar
                    ));
	
    //$pdfObject->Line(141.5,$pdfObject->GetY(),141.5,190);
    //$pdfObject->Line(215,$pdfObject->GetY(),215,190);
		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
	}
  
function HeaderVHO_L65($object)
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

//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
      $teken=$pdfObject->rapportageValuta;
		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige-5);
    $y = $pdfObject->getY();
    $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,1, "C");
$pdfObject->SetDrawColor(255,255,255);
		$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
	//	$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
$pdfObject->SetDrawColor(0,0,0);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->SetXY($pdfObject->marge,$y);

			$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"",
										"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde",$pdfObject->rapport_taal),
										"\n".vertaalTekst("Waarde ".$teken,$pdfObject->rapport_taal),
										"\n".vertaalTekst("in %",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Direct\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Rendement\nin %",$pdfObject->rapport_taal)
                    ));
	

		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
    
   // $pdfObject->ln(20);
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


if(!function_exists('getBenchmarkvergelijking'))
{
	function getBenchmarkvergelijking($object, $begin, $eind, $jaarStart = '')
	{
		$rapportObject = &$object;
		global $__appvar;

		$perioden = array();
		$perioden['begin'] = $begin;
		$perioden['eind'] = $eind;
		if ($jaarStart <> '')
		{
			$perioden['jan'] = $jaarStart;
		}

		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro)  AS totaal " .
			"FROM TijdelijkeRapportage WHERE " .
			" rapportageDatum ='" . $eind . "' AND " .
			" portefeuille = '" . $rapportObject->portefeuille . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query, __FILE__, __LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$zorgplichtcategorien = array();
		$query = "SELECT waarde as Zorgplicht FROM KeuzePerVermogensbeheerder WHERE Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND categorie='Zorgplichtcategorien' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
		while ($data = $DB->nextRecord())
		{
			$zorgplichtcategorien[$data['Zorgplicht']] = $data;
		}

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal,
              ZorgplichtPerBeleggingscategorie.Zorgplicht,
              beleggingscategorieOmschrijving " .
			"FROM TijdelijkeRapportage
             INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "'
             WHERE " .
			" rapportageDatum ='" . $eind . "' AND " .
			" portefeuille = '" . $rapportObject->portefeuille . "' "
			. $__appvar['TijdelijkeRapportageMaakUniek'] . " 
              GROUP BY Zorgplicht 
              ORDER BY beleggingscategorieVolgorde";
		debugSpecial($query, __FILE__, __LINE__);
		$DB->SQL($query);
		$DB->Query();
		while ($data = $DB->nextRecord())
		{
			$zorgplichtcategorien[$data['Zorgplicht']] = $data;
			$verdeling[$data['Zorgplicht']]['percentage'] = $data['totaal'] / $totaalWaarde * 100;
		}

		$query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "'
WHERE Portefeuilles.Portefeuille='" . $rapportObject->portefeuille . "' ORDER BY Zorgplicht";
		$DB->SQL($query);
		$DB->Query();

		while ($zorgplicht = $DB->nextRecord())
		{
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']] = $zorgplicht;
		}
		$query = "SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "' AND ZorgplichtPerPortefeuille.Portefeuille='" . $rapportObject->portefeuille . "'
 ORDER BY Zorgplicht";
		$DB->SQL($query);
		$DB->Query();
		while ($zorgplicht = $DB->nextRecord())
		{
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']] = $zorgplicht;
		}

		foreach ($zorgplichtcategorien as $zorgplicht => $zorgplichtData)
		{
			$query = "SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie 
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Zorgplichtcategorien' AND Categorie='$zorgplicht' AND Vermogensbeheerder='" . $rapportObject->pdf->portefeuilledata['Vermogensbeheerder'] . "'
      AND (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='" . $rapportObject->portefeuille . "') AND vanaf < '" . $eind . "'
      ORDER BY IndexPerBeleggingscategorie.Portefeuille desc, vanaf desc limit 1";
			$DB->SQL($query);
			$DB->Query();
			$data = $DB->nextRecord();
			$zorgplichtcategorien[$zorgplicht]['fonds'] = $data['Fonds'];
			$zorgplichtcategorien[$zorgplicht]['fondsOmschrijving'] = $data['Omschrijving'];
		}

		foreach ($zorgplichtcategorien as $zorgplicht => $zorgplichtData)
		{
			$query = "SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving 
      FROM benchmarkverdeling 
      JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
      WHERE benchmark='" . $zorgplichtData['fonds'] . "'";
			$DB->SQL($query);
			$DB->Query();
			while ($data = $DB->nextRecord())
			{
				$zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']] = $data;
			}
		}
//listarray($zorgplichtcategorien);
		foreach ($zorgplichtcategorien as $zorgplichtCategorie => $zorgplichtData)
		{
			if (!isset($zorgplichtData['fondsSamenselling']))
			{
				$zorgplichtData['fondsSamenselling'] = array($zorgplichtData['fonds'] => array('fonds'        => $zorgplichtData['fonds'],
																																											 'percentage'   => 100,
																																											 'Omschrijving' => $zorgplichtData['fondsOmschrijving']));
				$skipZorgFonds = true;
			}
			else
			{
				$skipZorgFonds = false;
			}
			foreach ($zorgplichtData['fondsSamenselling'] as $fonds => $fondsData)
			{
				$indexData[$fonds] = $index;

				foreach ($perioden as $periode => $datum)
				{

					$indexData[$fonds]['fondsKoers_' . $periode] = $rapportObject->getFondsKoers($fonds, $datum);
					$indexData[$fonds]['valutaKoers_' . $periode] = getValutaKoers($index['Valuta'], $datum);
					//echo "$fonds $datum ".$indexData[$fonds]['fondsKoers_'.$periode]."<br>\n";
				}

				$indexData[$fonds]['performance'] = ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin'] / 100);
				$indexData[$fonds]['performanceEur'] = ($indexData[$fonds]['fondsKoers_eind'] * $indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_begin'] * $indexData[$fonds]['valutaKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin'] * $indexData[$fonds]['valutaKoers_begin'] / 100);
				if ($skipZorgFonds == false)
				{
					$indexData[$zorgplichtData['fonds']]['performance'] += ($indexData[$fonds]['performance'] * ($fondsData['percentage'] / 100));
				}
//echo "$fonds ". ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_begin']) / ($indexData[$fonds]['fondsKoers_begin']/100 )." = ".$indexData[$fonds]['performance']."<br>\n";
				if ($perioden['jan'] <> '')
				{
					$indexData[$fonds]['performanceJaar'] = ($indexData[$fonds]['fondsKoers_eind'] - $indexData[$fonds]['fondsKoers_jan']) / ($indexData[$fonds]['fondsKoers_jan'] / 100);
					$indexData[$fonds]['performanceEurJaar'] = ($indexData[$fonds]['fondsKoers_eind'] * $indexData[$fonds]['valutaKoers_eind'] - $indexData[$fonds]['fondsKoers_jan'] * $indexData[$fonds]['valutaKoers_jan']) / ($indexData[$fonds]['fondsKoers_jan'] * $indexData[$fonds]['valutaKoers_jan'] / 100);
					if ($skipZorgFonds == false)
					{
						$indexData[$zorgplichtData['fonds']]['performanceJaar'] += ($indexData[$fonds]['performanceJaar'] * ($fondsData['percentage'] / 100));
					}
				}
			}

			$fonds = $zorgplichtData['fonds'];
			$samengesteldeBenchmark[$zorgplichtCategorie]['norm'] = $zorgplichtData['norm'];
			$samengesteldeBenchmark[$zorgplichtCategorie]['periode'] = $indexData[$fonds]['performance'];
			if ($perioden['jan'] <> '')
			{
				$samengesteldeBenchmark[$zorgplichtCategorie]['jaar'] = $indexData[$fonds]['performanceJaar'];
			}
		}
		$totalen = array();
		foreach ($samengesteldeBenchmark as $zorgplichtCategorie => $data)
		{

			$totalen['norm'] += $data['norm'];
			$totalen['periode'] += $data['norm'] * $data['periode'] / 100;
			if ($perioden['jan'] <> '')
			{
				$totalen['jaar'] += $data['norm'] * $data['jaar'] / 100;
			}
		}

		$benchmarkData = array();
		$benchmarkData['zorgplichtcategorien'] = $zorgplichtcategorien;
		$benchmarkData['samengesteldeBenchmark'] = $samengesteldeBenchmark;
		$benchmarkData['verdeling'] = $verdeling;
		$benchmarkData['totaal'] = $totalen;
//  listarray($perioden);
//listarray($benchmarkData);
		return $benchmarkData;
	}
}

?>