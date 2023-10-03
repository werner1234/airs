<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.12 $

 		$Log: PDFRapport_headers_L18.php,v $
 		Revision 1.12  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/06/25 16:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/05/14 15:28:41  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/10/13 06:47:50  cvs
 		update 13-10-2012
 		
 		Revision 1.8  2011/07/03 06:42:47  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2011/06/29 16:52:23  rvv
 		*** empty log message ***

 		Revision 1.6  2008/09/15 08:04:05  rvv
 		*** empty log message ***

 		Revision 1.5  2008/07/02 10:25:04  rvv
 		*** empty log message ***

 		Revision 1.4  2008/07/01 07:12:34  rvv
 		*** empty log message ***

 		Revision 1.3  2008/05/16 08:13:26  rvv
 		*** empty log message ***

 		Revision 1.2  2008/03/18 12:39:08  rvv
 		*** empty log message ***

 		Revision 1.1  2008/03/18 09:56:48  rvv
 		*** empty log message ***


*/
function Header_basis_L18($object)
{
  $pdfObject = &$object;

  if(empty($pdfObject->last_rapport_type))
		  $pdfObject->last_rapport_type =$pdfObject->rapport_type;

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
	  	  $breedte = 240;
	  	  $hoogte = $breedte / (900/$breedte);
	  	  $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->rMargin,  $pdfObject->tMargin, 120, 30);
	  	}

	   if(empty($pdfObject->customPageNo))
      $pdfObject->customPageNo = 1;
		elseif($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast && $pdfObject->rapport_type <> $pdfObject->last_rapport_type )
  		$pdfObject->customPageNo = 1;
  	else
  		$pdfObject->customPageNo++;
    }
    else
    {
    if(empty($pdfObject->customPageNo))
      $pdfObject->customPageNo = 1;
  	elseif($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 1;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

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
			$x = 110;
		else
			$x = 207-$pdfObject->rMargin;

	    if(is_file($pdfObject->rapport_logo))
	  	{
	  	  $breedte = 120;
	  	  $hoogte = 120 / (450/120);
	  	  $pdfObject->Image($pdfObject->rapport_logo, $pdfObject->rMargin,  $pdfObject->tMargin, 120, 30);
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

		$pdfObject->SetY($pdfObject->tMargin);
		$pdfObject->SetX($x);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);

    $pdfObject->MultiCell(90,6,$pdfObject->rapport_koptext,0,'R',0,1);
		$pdfObject->SetX($x);

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

    $pdfObject->MultiCell(90,6,vertaalTekst(vertaalTekst("Afschrift rekening op:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum).
    "\nGeproduceerd op:",$pdfObject->rapport_taal)." ".date("j",time())." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",time())],$pdfObject->rapport_taal)." ".date("Y",time()),0,'R');

    $pdfObject->SetXY(20,50);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',16);
	  $pdfObject->MultiCell(150,6,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');

	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  }

	  $pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
}


	  function HeaderFRONT_L18($object)
	  {
	  $pdfObject = &$object;
	  $pdfObject->CellBorders = array();
	  $pdfObject->rowHeight = 4;
	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	  }

	  function HeaderTemplate_L18($object)
	  {
	  $pdfObject = &$object;
	  }

	 function HeaderVKM_L18($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

	  function HeaderPERF_L18($object)
	  {
	  $pdfObject = &$object;

	  }

	  function HeaderGRAFIEK_L18($object)
	  {
	    $pdfObject = &$object;
	  }

function HeaderRISK_L18($object)
{
	$pdfObject = &$object;
}
	  function HeaderOIB_L18($object)
	  {
	  $pdfObject = &$object;
		$pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
    $pdfObject->SetWidths(array(15,27,30,27,30,27,30,27,30,27));
		$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->Row(array(''));
    $volgorde = $pdfObject->OIBHeaderData['volgorde'];
    $omschrijvingen = $pdfObject->OIBHeaderData['omschrijvingen'];
    $pdfObject->CellBorders = array();
    $pdfObject->rowHeight = 4;
		$pdfObject->Row(array('','Munt','Aandelen','',$omschrijvingen[$volgorde[1]],'',$omschrijvingen[$volgorde[2]],'','Totaal','%'));
		$pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->rowHeight = 3;
		$pdfObject->Row(array('','','','','','','','','',''));
		$pdfObject->switchFont('fonds');

	  }

	  function HeaderCASH_L18($object)
	  {
	  $pdfObject = &$object;
	  $pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
    $pdfObject->SetWidths(array(15,63,64,64,65));//265
		$pdfObject->SetAligns(array('L','L','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->switchFont('fonds');
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  	$pdfObject->Row(array('','Waardering in EUR','','','','','','','',''));
		$pdfObject->switchFont('fonds');
		}


	  function HeaderOIV_L18($object)
	  {
 	  $pdfObject = &$object;
    $pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
    $pdfObject->SetWidths(array(15,63,63,63,64));
		$pdfObject->SetAligns(array('L','L','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->switchFont('fonds');
		$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  	$pdfObject->Row(array('','Rekeningsaldo\'s','','','','','','','',''));
		$pdfObject->switchFont('fonds');
	  }

	  function HeaderTRANS_L18($object)
	  {
	     $pdfObject = &$object;
    $pdfObject->switchFont('fonds');
    $pdfObject->ln(2);
    $pdfObject->setX(15+$pdfObject->marge);
  //  $pdfObject->MultiCell(90,4,'test',0,'L');
    $pdfObject->Cell(100,4,'van '.date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1);

    $pdfObject->switchFont('rodelijn');
    $pdfObject->SetWidths(array(15,27,27,85,26,30,30,30));
		$pdfObject->SetAligns(array('L','L','L','L','L','R','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->SetAligns(array('L','L','R','L','R','R','R','R','R','R'));
    $pdfObject->rowHeight = $pdfObject->rapport_style['fonds']['rowHeight'];
	  }

	  	  function HeaderATT_L18($object)
	  {
	     $pdfObject = &$object;
    $pdfObject->switchFont('fonds');
    $pdfObject->ln(2);
    $pdfObject->setX(15+$pdfObject->marge);
  //  $pdfObject->MultiCell(90,4,'test',0,'L');
    $pdfObject->Cell(100,4,$pdfObject->subTitle,0,1);

    $pdfObject->switchFont('rodelijn');
    if($pdfObject->subTitle=='')
    {
      $pdfObject->SetWidths($pdfObject->widthB);
      $pdfObject->SetAligns($pdfObject->alignB);
      $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U','U');
    }
    else
    {
      $pdfObject->SetWidths(array(15,55,32,34,34,34,34,32));
		//$pdfObject->SetAligns(array('L','L','L','L','L','R','R','R','R'));
		 $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
     $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
    }
		
		$pdfObject->rowHeight = 3;
    $emptyHeader=array();
    foreach($pdfObject->rapport_header as $col)
      $emptyHeader[]='';
    $pdfObject->Row($emptyHeader);
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U','U');
    $pdfObject->Row($emptyHeader);
    $pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R'));
	  }



	  function HeaderVHO_L18($object)
	  {
	  $pdfObject = &$object;
	  if($pdfObject->rapport_deel == "AAND")
	  {
	  $pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
	 // $pdfObject->SetWidths(array(20,17,17,70,22,22,22,20,35,20));
	  $pdfObject->SetWidths(array(15,17,17,71,20,26,20,18,18,28,20));
	  $pdfObject->SetAligns(array('L','L','R','L','R','R','R','R','R','R','R'));
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U','U');
	  $pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
    //$pdfObject->SetAligns(array('L','L','R','L','R','R','R','R','R','R'));
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','','',''));

	  }
	  else
	  {
    $pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
    $pdfObject->SetWidths(array(15,15,22,72,21,26,21,28,24,26));
		$pdfObject->SetAligns(array('L','L','R','L','R','R','R','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','',''));
   // $pdfObject->SetAligns(array('L','L','R','L','R','R','R','R','R'));
	  }
    }

    function HeaderMUT_L18($object)
	  {
	  $pdfObject = &$object;

	  		// voor kopjes
	  		   $pdfObject->switchFont('fonds');
    $pdfObject->switchFont('rodelijn');
		$pdfObject->SetWidths(array(15,15,20,100,25,30,15,25,25));
		$pdfObject->SetAligns(array('R','R','R','L','R','R','R','R','R'));
		$pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
		$pdfObject->rowHeight = 3;
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->rowHeight = 5;
    $pdfObject->CellBorders = array();
    $pdfObject->setY($pdfObject->getY()+3);
	  $pdfObject->Row($pdfObject->rapport_header);
	  $pdfObject->rowHeight = 3;
	  $pdfObject->CellBorders = array('','U','U','U','U','U','U','U','U','U');
    $pdfObject->Row(array('','','','','','','','','',''));
    $pdfObject->switchFont('fonds');
 	  $pdfObject->SetFont($pdfObject->rapport_font,'',10);
 	     $pdfObject->rowHeight = 5;

    $pdfObject->ln();


	  }
?>