<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/02/10 18:09:11 $
 		File Versie					: $Revision: 1.31 $

 		$Log: PDFRapport_headers_L42.php,v $
 		Revision 1.31  2018/02/10 18:09:11  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2017/07/01 17:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2016/10/16 15:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2016/04/30 15:33:27  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2015/04/04 15:15:15  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2015/01/11 12:48:50  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2014/12/17 16:14:40  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2014/12/10 16:58:25  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2014/12/06 18:13:44  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2014/08/06 15:41:01  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2014/08/02 15:25:09  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/06/04 16:13:28  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/05/25 14:38:33  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/05/21 12:39:28  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/10/16 15:35:04  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/08/24 15:48:47  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/08/18 12:23:35  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/08/10 15:48:01  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/06/09 18:01:53  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/06/05 15:56:07  rvv
 		*** empty log message ***
 		
*/

 function Header_basis_L42($object)
 { 
   $pdfObject = &$object;
   if(!isset($pdfObject->beeldMerk))
   {
     $beeld = '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';
     $pdfObject->beeldMerk = base64_decode($beeld);
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
      
      
    if(empty($pdfObject->lastPortefeuille) || $pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'])
    {
  	  	$pdfObject->rapportNewPage = $pdfObject->page;
        unset($pdfObject->grafiekData);
        unset($pdfObject->rapportageDatumWaarde);
        unset($pdfObject->grafiekKleuren);
    }

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
    
    $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
    //$pdfObject->Rect(18,9,297-18*2,18,'F');
  
  
      if($pdfObject->CurOrientation == "P")
      {
        $pageWidth = 210;
        $pageHeight=297;
        $logopos = 185;
        $x = 160;
      }
      else
      {
        $pageWidth = 297;
        $pageHeight=210;
        $logopos = 280;
        $x = 250;
      }
      
    $pdfObject->Rect($pdfObject->marge,9,$pageWidth-$pdfObject->marge*2,18,'F');
    
    
    $pdfObject->MemImage($pdfObject->beeldMerk,260,13,10);
    
    
    $pdfObject->last_rapport_type = $pdfObject->rapport_type;

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
    $namen=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
    if($pdfObject->__appvar['consolidatie']['portefeuillenaam1'] <> '')
      $namen.="\n".$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
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
    $namen=$pdfObject->rapport_naam1;
    if($pdfObject->rapport_naam2 <> '')
      $namen.="\n".$pdfObject->rapport_naam2;
		}

if($namen=='')
  $namen=$pdfObject->rapport_portefeuille;
$pdfObject->rapport_koptext = str_replace("{Namen}", $namen, $pdfObject->rapport_koptext);
$rapport_datum=date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
$pdfObject->rapport_koptext = str_replace("{RapportageDatum}", $rapport_datum, $pdfObject->rapport_koptext);


		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);




    if(!empty($pdfObject->rapport_logo_tekst))
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

	 
    //$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{
 		    $factor=0.035;
		    $xSize=382*$factor;
		    $ySize=591*$factor;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, $xSize, $ySize);
		}


		

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  //$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  
    $pdfObject->SetTextColor(255);
    $pdfObject->SetY(16);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize+2);
	  $pdfObject->SetX(18);
	  $pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    $pdfObject->SetTextColor(0);
    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    $pdfObject->ln(13);
    $pdfObject->headerStart = $pdfObject->getY()+10;
  //  $pdfObject->ln();
 }

  	function HeaderVKM_L42($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
 	function HeaderATT_L42($object)
	{
    $pdfObject = &$object;
	}

function HeaderVKMA_L42($object)
{
  $pdfObject = &$object;
}


function HeaderFACTUUR_L42($object)
{
	$pdfObject = &$object;
}

 	function HeaderFRONT_L42($object)
	{
    $pdfObject = &$object;
	}
  
 	function HeaderEND_L42($object)
	{
    $pdfObject = &$object;
	}

function HeaderVAR_L42($object)
{
	$pdfObject = &$object;
}
  
 	function HeaderZORG_L42($object)
	{
	    $pdfObject = &$object;
      $pdfObject->ln(-4);
      $pdfObject->HeaderZORG();
	}
  
  function HeaderINDEX_L42($object)
	{
    $pdfObject = &$object;
	}
  
    function HeaderSMV_L42($object)
	{
    $pdfObject = &$object;
    $pdfObject->Ln(-4);
    $pdfObject->HeaderSMV();
	}
  
  function HeaderFISCAAL_L42($object)
	{
    $pdfObject = &$object;

    $pdfObject->HeaderFISCAAL();
    
	}

function HeaderSCENARIO_L42($object)
{
	$pdfObject = &$object;
	

	
}
    
  function HeaderMODEL_L42($object)
	{
    $pdfObject = &$object;
		//$pdfObject->SetFont("Times","",10);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor(0);
    $pdfObject->SetWidths(array(60,20,20,20,20,25,25,25,5,27,25));
		$pdfObject->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R","R"));
    
		$pdfObject->Row(array("Fonds",'Bewaarder',
												 "Model Percentage",
												 "Werkelijk Percentage",
												 "Grootste afwijking",
												 "Kopen",
												 "Verkopen",
												 "Overschrijding waarde EUR",
												 "",
												 "Waarde volgens percentage model",
												 "Koers in locale valuta"));

		$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
		$pdfObject->SetFont("Times","",10);

	}
  
  	function HeaderCASHY_L42($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  function HeaderAFM_L42($object)
	{
	    $pdfObject = &$object;
      $pdfObject->ln(-4);
      $pdfObject->HeaderOIB();
	}

function HeaderVOLK_L42($object)
	{
	    $pdfObject = &$object;


      $dataWidth=array(63,25,11,22,4,22,22,2,23,23,23,23,17);
      $n=0;
      $headerWidths=array();
      $splits=array(3,7,8,12);
      foreach($dataWidth as $index=>$value)
      {
        if(in_array($index,$splits))
        {
          $n++;
        }  
        $headerWidths[$n]+=$value;
      }
     
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetAligns(array('C','C','C','C'));
      $pdfObject->SetWidths($headerWidths);
      $pdfObject->CellBorders = array('',array('LU','U','RU'),'',array('LU','U','RU'));
      $pdfObject->Row(array('','Originele valuta','','Basis valuta (euro)'));
 	 	  $pdfObject->SetWidths($dataWidth);
      unset($pdfObject->CellBorders);
	    $pdfObject->SetAligns(array('L','R','R','R','L','R','R','C','R','R','R','R','R','R'));

      
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->pageYstart=$pdfObject->GetY();
      $pdfObject->Row(array(vertaalTekst("\n Fondsnaam",$pdfObject->rapport_taal),
      vertaalTekst("\n Aantal",$pdfObject->rapport_taal),
      vertaalTekst(" Valuta",$pdfObject->rapport_taal),
      vertaalTekst("\n Koers*",$pdfObject->rapport_taal),
      '',
      vertaalTekst("\n Kostprijs",$pdfObject->rapport_taal),
      vertaalTekst("Bruto rendement",$pdfObject->rapport_taal),
      '',
      vertaalTekst("\n Marktwaarde",$pdfObject->rapport_taal),
      vertaalTekst("\n Kostprijs",$pdfObject->rapport_taal),
      vertaalTekst("Netto resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Netto rendement",$pdfObject->rapport_taal),
      vertaalTekst("\n Weging",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
      $pdfObject->ln();

	}
  function HeaderHSE_L42($object)
	{
	    $pdfObject = &$object;

      $dataWidth=array(63,25,18,11,21,4,20,20,2,21,21,19,19,16);
      $n=0;
      $headerWidths=array();
      $splits=array(4,8,9,13);
      foreach($dataWidth as $index=>$value)
      {
        if(in_array($index,$splits))
        {
          $n++;
        }  
        $headerWidths[$n]+=$value;
      }
     
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetAligns(array('C','C','C','C'));
      $pdfObject->SetWidths($headerWidths);
      $pdfObject->CellBorders = array('',array('LU','U','RU'),'',array('LU','U','RU'));
      $pdfObject->Row(array('','Originele valuta','','Basis valuta (euro)'));
 	 	  $pdfObject->SetWidths($dataWidth);
      unset($pdfObject->CellBorders);
	    $pdfObject->SetAligns(array('L','R','C','R','R','L','R','R','C','R','R','R','R','R','R'));

      
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->pageYstart=$pdfObject->GetY();
      $pdfObject->Row(array(vertaalTekst("\n Fondsnaam",$pdfObject->rapport_taal),
      vertaalTekst("\n Aantal",$pdfObject->rapport_taal),
      vertaalTekst("\nBewaarder",$pdfObject->rapport_taal),
      vertaalTekst(" Valuta",$pdfObject->rapport_taal),
      vertaalTekst("\n Koers*",$pdfObject->rapport_taal),
      '',
      vertaalTekst("\n Kostprijs",$pdfObject->rapport_taal),
      vertaalTekst("Bruto rendement",$pdfObject->rapport_taal),
      '',
      vertaalTekst("\nMarktwaarde",$pdfObject->rapport_taal),
      vertaalTekst("\n Kostprijs",$pdfObject->rapport_taal),
      vertaalTekst("Netto resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Netto rendement",$pdfObject->rapport_taal),
      vertaalTekst("\n Weging",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
      $pdfObject->ln();

	}

  function HeaderVHO_L42($object)
	{
	    $pdfObject = &$object;
      $dataWidth=array(63,25,11,20,4,20,20,21,20,20,20,19,17);
 	 	  $pdfObject->SetWidths($dataWidth);
      unset($pdfObject->CellBorders);
	    $pdfObject->SetAligns(array('L','R','R','R','L','R','R','R','R','R','R','R','R'));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->pageYstart=$pdfObject->GetY();
      $pdfObject->Row(array(vertaalTekst("Instrument",$pdfObject->rapport_taal),
      vertaalTekst("Aantal",$pdfObject->rapport_taal),
      vertaalTekst("Valuta",$pdfObject->rapport_taal),
      vertaalTekst("Koers*",$pdfObject->rapport_taal),
      '',
      vertaalTekst("Kostprijs\nin valuta",$pdfObject->rapport_taal),
      vertaalTekst("Rendement\nin valuta",$pdfObject->rapport_taal),
      vertaalTekst("Marktwaarde\nin Euro",$pdfObject->rapport_taal),
      vertaalTekst("Kostprijs\nin Euro",$pdfObject->rapport_taal),
      vertaalTekst("Rendement\nin Euro",$pdfObject->rapport_taal),
      vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
      vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
      vertaalTekst("Weging",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);

	}
  

	function HeaderOIB_L42($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderMUT_L42($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
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
;
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Ln();

  }

	function HeaderPERF_L42($object)
	{
	  $pdfObject = &$object;
		$object->SetFont($object->rapport_font,'',$object->rapport_fontsize);
		$object->SetWidths($object->widthA);
		$object->SetAligns($object->alignA);
	}


  function HeaderTRANS_L42($object)
	{
    $pdfObject = &$object;
	  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5]  ;

		$verkoop			= $inkoopEind+ $pdfObject->widthB[6];
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8]  ;

		$resultaat			= $verkoopEind+ $pdfObject->widthB[9];
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
//			$pdfObject->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C"); //60 ipv 65
			$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
//			$pdfObject->Cell(65,4, vertaalTekst("Resultaat bepaling",$pdfObject->rapport_taal), 0,0, "C"); //81 ipv 65
			$pdfObject->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaatbepaling",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->ln();
		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());

		// bij layout 1 zit het % totaal
		if($pdfObject->rapport_TRANS_procent == 1)
			$procentTotaal = "Rendement";

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->row(array("\n \n ".vertaalTekst("Datum",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/ Ver- Koop",$pdfObject->rapport_taal),
										 "\n \n ".vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 "\n \n ".vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                     '',
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                     '',
										 "\n ".vertaalTekst("Kostprijs\n in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 "\n ".vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										 "\n ".vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										 "\n ".$procentTotaal));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  
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
		$kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'regio' => 'OIR', 'beleggingssector' => 'OIS');
		$geenWaardeKoppeling = array('Beleggingscategorie' => 'geenWaarden', 'Valuta' => 'geenWaarden', 'regio' => 'Geen regio', 'beleggingssector' => 'Geen sector');

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
				if ($waarde == 'geenWaarden')
				{
					$waarde = $geenWaardeKoppeling[$type];
				}

				$typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
				$typeData['port']['waarde'][$waarde] = $data['port']['waarde'];
				$typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;
				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}
}


if(!function_exists('PieChart'))
{
	function PieChart($object, $w, $h, $data, $format, $colors = null, $legendaLocatie)
	{


		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize - 2);
		$object->SetLegends($data, $format);

		$XPage = $object->GetX();
		$YPage = $object->GetY();
		$margin = 2;
		$hLegend = 2;
		$radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if ($colors == null)
		{
			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$gray = $i * intval(255 / $object->NbVal);
				$colors[$i] = array($gray, $gray, $gray);
			}
		}

		//Sectors
		$object->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;

		$object->sum = 0;
		foreach ($data as $key => $value)
		{
			$data[$key] = abs($value);
			$object->sum += abs($value);
		}


		foreach ($data as $val)
		{
			$angle = floor(($val * 360) / doubleval($object->sum));
			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$object->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$object->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize - 2);


		if ($legendaLocatie == 'z')
		{
			$max = 0;
			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$lw = $object->GetStringWidth($object->legends[$i]);
				if ($lw > $max)
				{
					$max = $lw;
				}
			}

			$x1 = ($XPage + $radius + $margin) - $max / 2;
			$x2 = $x1 + $margin;
			$y1 = $YDiag + $radius + ($margin * 2);

			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$object->Rect($x1 - 2, $y1, $hLegend, $hLegend, 'DF');
				$object->SetXY($x2, $y1);
				$object->Cell(0, $hLegend, $object->legends[$i]);
				$y1 += $hLegend + $margin;
			}
		}
		else
		{
			$x1 = $XPage + $w;
			$x2 = $x1 + $margin;
			$y1 = $YDiag - $radius + ($margin * 2);
			for ($i = 0; $i < $object->NbVal; $i++)
			{
				$object->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$object->Rect($x1 - 2, $y1, $hLegend, $hLegend, 'DF');
				$object->SetXY($x2, $y1);
				$object->Cell(0, $hLegend, $object->legends[$i]);
				$y1 += $hLegend + $margin;
			}
		}
		$object->setY($YPage + $h);

	}
}


if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object, $vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
		$query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '" . $object->portefeuilledata['Vermogensbeheerder'] . "' ORDER BY Afdrukvolgorde";
		$border = 0;
		$DB = new DB();
		$DB2 = new DB();
		$lmarge = 140;

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if (($object->GetY() + $hoogte) > $object->pagebreak)
		{
			$object->AddPage();
			$object->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if ($object->rapport_perfIndexJanuari == true)
		{
			$julRapDatumVanaf = db2jul($rapportageDatumVanaf);
			$rapJaar = date('Y', $julRapDatumVanaf);
			$dagMaand = date('d-m', $julRapDatumVanaf);
			$januariDatum = $rapJaar . '-01-01';
			if ($dagMaand == '01-01')
			{
				$object->rapport_perfIndexJanuari = false;
			}
		}
		if ($object->rapport_printAEXVergelijkingEur == 1)
		{
			$extraX = 26;
			$perfEur = 1;
			$perfVal = 0;
			$perfJan = 0;
		}
		if ($object->rapport_perfIndexJanuari == true)
		{
			$perfEur = 0;
			$perfVal = 0;
			$perfJan = 1;
		}

		if ($object->printAEXVergelijkingProcentTeken)
		{
			$teken = '%';
		}
		else
		{
			$teken = '';
		}


		if ($object->rapport_perfIndexJanuari == true)
		{
			$extraX += 51;
		}

		$object->ln();
		$object->SetFillColor($object->rapport_kop_bgcolor['r'], $object->rapport_kop_bgcolor['g'], $object->rapport_kop_bgcolor['b']);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte, 'F');
		$object->SetFillColor(0);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte);
		$object->SetX($object->marge + $lmarge);

		// kopfontcolor
		//$object->SetTextColor($object->rapport_kop4_fontcolor['r'],$object->rapport_kop4_fontcolor['g'],$object->rapport_kop4_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'], $object->rapport_kop_fontcolor['g'], $object->rapport_kop_fontcolor['b']);
		$object->SetFont($object->rapport_kop4_font, $object->rapport_kop4_fontstyle, $object->rapport_kop4_fontsize);
		$object->Cell(40, 4, vertaalTekst("Index-vergelijking", $object->rapport_taal), 0, 0, "L");

		$object->SetFont($object->rapport_font, $object->rapport_fontstyle, $object->rapport_fontsize);
		//$object->SetTextColor($object->rapport_fonds_fontcolor['r'],$object->rapport_fonds_fontcolor['g'],$object->rapport_fonds_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'], $object->rapport_kop_fontcolor['g'], $object->rapport_kop_fontcolor['b']);
		if ($object->rapport_perfIndexJanuari == true)
		{
			$object->Cell(26, 4, date("d-m-Y", db2jul($januariDatum)), $border, 0, "R");
		}
		$object->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatumVanaf)), $border, 0, "R");
		$object->Cell(26, 4, date("d-m-Y", db2jul($rapportageDatum)), $border, 0, "R");

		if ($object->portefeuilledata['Layout'] == 30 || $object->portefeuilledata['Layout'] == 14 || $object->portefeuilledata['Layout'] == 25)
		{
			$object->Cell(26, 4, vertaalTekst("Perf in %", $object->rapport_taal), $border, $perfVal, "R");
		}
		else
		{
			$object->Cell(26, 4, vertaalTekst("Performance in %", $object->rapport_taal), $border, $perfVal, "R");
		}
		if ($object->rapport_printAEXVergelijkingEur == 1)
		{
			$object->Cell(26, 4, vertaalTekst("Perf in % in euro", $object->rapport_taal), $border, $perfEur, "R");
		}
		if ($object->rapport_perfIndexJanuari == true)
		{
			$object->Cell(26, 4, vertaalTekst("Jaar Perf.", $object->rapport_taal), $border, $perfJan, "R");
		}

		while ($perf = $DB->nextRecord())
		{
			if ($perf['Valuta'] != 'EUR')
			{
				if ($object->rapport_perfIndexJanuari == true)
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

			if ($object->rapport_perfIndexJanuari == true)
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
			$object->Cell($lmarge, 4, '', $border, 0, "L");
			$object->Cell(40, 4, $perf[Omschrijving], $border, 0, "L");
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$object->Cell(26, 4, $object->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($object->rapport_printAEXVergelijkingEur == 1)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
			}
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '" . $object->rapport_portefeuille . "' ";
		$DB->SQL($query2);
		$DB->Query();

		while ($perf = $DB->nextRecord())
		{

			if ($perf['Valuta'] != 'EUR')
			{

				if ($object->rapport_perfIndexJanuari == true)
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

			if ($object->rapport_perfIndexJanuari == true)
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

			$object->Cell($lmarge, 4, '', $border, 0, "L");
			$object->Cell(40, 4, $perf[Omschrijving], 0, 0, "L");
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($koers0[Koers], 2), $border, 0, "R");
			}
			$object->Cell(26, 4, $object->formatGetal($koers1[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($koers2[Koers], 2), $border, 0, "R");
			$object->Cell(26, 4, $object->formatGetal($performance, 2) . $teken, $border, $perfVal, "R");
			if ($object->rapport_printAEXVergelijkingEur == 1)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceEur, 2) . $teken, $border, $perfEur, "R");
			}
			if ($object->rapport_perfIndexJanuari == true)
			{
				$object->Cell(26, 4, $object->formatGetal($performanceJaar, 2) . $teken, $border, $perfJan, "R");
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


?>