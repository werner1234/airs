<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/02/11 17:30:10 $
 		File Versie					: $Revision: 1.24 $

 		$Log: PDFRapport_headers_L45.php,v $
 		Revision 1.24  2017/02/11 17:30:10  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/02/01 16:44:57  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/11/25 13:52:50  rm
 		participanten
 		
 		Revision 1.19  2015/11/18 10:10:33  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/11/14 13:24:32  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2015/11/01 17:25:34  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/09/13 14:38:35  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/09/06 15:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2014/08/02 15:25:09  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2014/07/23 15:44:04  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/07/16 16:01:16  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/10/12 15:54:06  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2013/10/09 06:39:45  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/10/05 15:58:48  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/09/28 14:43:25  rvv
 		*** empty log message ***
 		
 	
*/

function Header_basis_L45($object)
{
  global $__appvar;
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 0;
    }
    else
    {
      
      if($pdfObject->CurOrientation=='P')
      {
        $logoX=140;
        $pageWidth=210;
      }
      else
      {
  	    $logoX=229;
        $pageWidth=297;
      }
      
      $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
      $pdfObject->Line($pdfObject->marge,25,$pageWidth-$pdfObject->marge,25);
      
      
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

   	if(is_file($pdfObject->rapport_logo))
		{
       $factor=0.06;
		   $xSize=983*$factor;//$x=885*$factor;
		   $ySize=217*$factor;//$y=849*$factor;
			 $pdfObject->Image($pdfObject->rapport_logo, $logoX, 5, $xSize, $ySize);
		}
    
    
          if($pdfObject->CurOrientation=='P')
      {
        $voetbeginY=285;
        $pageWidth=210;
      }
      else
      {
  	    $voetbeginY=193;
        $pageWidth=297;
      }
      $lijnY=-2;
      $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
      $pdfObject->Line($pdfObject->marge,$voetbeginY+$lijnY,$pageWidth-$pdfObject->marge,$voetbeginY+$lijnY);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

      if ($pdfObject->frontPage == true)
      {

      }
      elseif($pdfObject->rapport_type == "Course" || $pdfObject->rapport_type == "Participatie")
      {
        $pdfObject->rapport_koptext=$pdfObject->rapport_koptextParticipants;
		    $pdfObject->rapport_koptext = str_replace("{Naam1}", $pdfObject->rapport_naam1, $pdfObject->rapport_koptext);
		    $pdfObject->rapport_koptext = str_replace("{Naam2}", $pdfObject->rapport_naam2, $pdfObject->rapport_koptext);

        if ( isset ($_POST['DateEnd'])) {
          $pdfObject->rapport_datum=form2jul($_POST['DateEnd']);
        } elseif ( isset ($_POST['date'])) {
          $pdfObject->rapport_datum=form2jul($_POST['date']);
        }
        
        $pdfObject->AutoPageBreak=false;
        $pdfObject->SetY($voetbeginY);
	      $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
   	    $pdfObject->SetY($voetbeginY);
	      $pdfObject->MultiCell(297-($pdfObject->marge*2),4,"Pagina: ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n".
          vertaalTekst("Opmaak",$pdfObject->rapport_taal).": ".date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y"),0,'R');
        $pdfObject->AutoPageBreak=true;
        
      }
      else
      {    
        $pdfObject->AutoPageBreak=false;
        $pdfObject->SetY($voetbeginY);
	      $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
   	    $pdfObject->SetY($voetbeginY);
	      $pdfObject->MultiCell(297-($pdfObject->marge*2),4,"Pagina: ".$pdfObject->customPageNo."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n".
          vertaalTekst("Opmaak",$pdfObject->rapport_taal).": ".date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y"),0,'R');
        $pdfObject->AutoPageBreak=true;
      }
      


		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 160;
		else
			$x = 250;

	 // $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->SetY(15);
	  $pdfObject->SetX(100);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->SetY(20);
	 	$pdfObject->headerStart = $pdfObject->getY()+4+13;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    $pdfObject->last_rapport_type=$pdfObject->rapport_type;
  }
}


  function HeaderCourse_L45($object)
  {
    $pdfObject = &$object;
    $pdfObject->Ln(5.5);
    
  }


  function HeaderParticipatie_L45($object)
  {
    $pdfObject = &$object;
  }

 	function HeaderVKM_L45($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderFISCAAL_L45($object)
{
	$pdfObject = &$object;
	$pdfObject->ln(7);

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
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 12 , 'F');

	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	$pdfObject->SetX($pdfObject->marge+$huidige+5);
	$pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische kostprijs",$pdfObject->rapport_taal), 0,0,"C");
	$pdfObject->SetX($pdfObject->marge+$actueel);
	$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
	$pdfObject->SetX($pdfObject->marge+$resultaat);
	//$pdfObject->Cell(70,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");
	$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
	$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
	//$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$y = $pdfObject->getY();
	$pdfObject->Ln();
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
										vertaalTekst('',$pdfObject->rapport_taal),
										vertaalTekst("Fiscale\nWaardering",$pdfObject->rapport_taal),
										"",'',''));

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
	$pdfObject->setY($y);
	$pdfObject->row(array("Categorie\n"));
	$pdfObject->ln(8);
	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);

}

	function HeaderVAR_L45($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(76,16,16,21,21,25, 5,  20,20,20,20,20));
    $pdfObject->ln(7);
	  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widths), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

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

    $pdfObject->SetAligns(array('L','L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
	//	$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		
		//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
  unset($pdfObject->fillCell);
  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;
    
				$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal)," \n ",
		 vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
		 vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
		 vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
		  vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
		   vertaalTekst("%",$pdfObject->rapport_taal)."\n".vertaalTekst("port.",$pdfObject->rapport_taal)));

		$pdfObject->ln();
   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);

		unset($pdfObject->CellBorders);//"Modified\nduration",
    unset($pdfObject->fillCell);
	}

function HeaderVOLK_L45($object)
{
	$pdfObject = &$object;
  $pdfObject->Ln(7);

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  unset($pdfObject->fillCell);
  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;
    
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
 	$pdfObject->row(array("\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Kostprijs",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("",$pdfObject->rapport_taal)."\n ",
										"\n ",
										vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Marktwaarde",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Weging",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Opgelopen\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Ongerealiseerd\nResultaat",$pdfObject->rapport_taal),
                    "\n ",
										vertaalTekst("in %",$pdfObject->rapport_taal)."\n "),'  '
										);

		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
unset($pdfObject->fillCell);
	}

function HeaderHSE_L45($object)
{
	$pdfObject = &$object;
  $pdfObject->Ln(7);

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
										vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Begin\nKoers",$pdfObject->rapport_taal),
                    vertaalTekst("Begin\nwaarde",$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal)."\n ",
										"\n ",
										vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Marktwaarde",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Weging",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Opgelopen\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Ongerealiseerd\nResultaat",$pdfObject->rapport_taal),
                    "\n ",
										vertaalTekst("in %",$pdfObject->rapport_taal)."\n "),' '
										);

		$pdfObject->setY($y);
  	$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
unset($pdfObject->fillCell);
	}


  
   function HeaderPERF_L45($object)
  {
	  	$pdfObject = &$object;
	  	$pdfObject->SetY($pdfObject->GetY()+4);
  	  $pdfObject->HeaderPERF();
  }
  
  function HeaderINDEX_L45($object)
  {
    $pdfObject = &$object;
  }

  function HeaderSCENARIO_L45($object)
{
    $pdfObject = &$object;
    $pdfObject->Ln();
		
}

  function HeaderMOD_L45($object)
{
    $pdfObject = &$object;
    $pdfObject->Ln();
    $pdfObject->HeaderMOD();
		
}

  function HeaderMODEL_L45($object)
{
    $pdfObject = &$object;
    $pdfObject->SetY(5);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX($pdfObject->marge);
		$pdfObject->Cell(30,4, "Modelportefeuille: ",0,0,"L");
		$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_portefeuille'],0,1,"L");
    $setFill=false;
    if(count($pdfObject->fillCell) > 0)
      $setFill=true;
	
		if($pdfObject->selectData['modelcontrole_rapport'] == "vastbedrag")
		{
			$pdfObject->Cell(30,4, "Vast bedrag: ",0,0,"L");
			$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_vastbedrag'],0,1,"L");
		}
		else
		{
      if($pdfObject->selectData["modelcontrole_filter"] != "gekoppeld")
				$extraTekst = " : niet gekoppeld depot";
			else
				$extraTekst = "";

			$pdfObject->Cell(30,4, "Client: ",0,0,"L");
      $pdfObject->Cell(50,4, $pdfObject->clientOmschrijving,0,1,"L");
      $pdfObject->Cell(30,4, "Naam: ",0,0,"L");
      $pdfObject->Cell(50,4, $pdfObject->naamOmschrijving.$extraTekst,0,1,"L");
		}

		$pdfObject->ln();
		$pdfObject->SetWidths(array(70,27,12,19,20,20,20,20,25,1,22,25));
		$pdfObject->SetAligns(array("L","L","L","R","R","R","R","R","R","R","R","R","R","R"));
    
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

    for($i=0;$i<count($pdfObject->widths);$i++)
      $pdfObject->fillCell[] = 1;
    $pdfObject->SetY(27);
		$pdfObject->Row(array("Fonds\n ","ISIN\n ","Valuta\n ",
												 "Model Percentage",
												 "Werkelijk Percentage",
												 "Grootste afwijking",
												 "Kopen\n ",
												 "Verkopen\n ",
												 "Overschrijding waarde EUR",
												 " ",
												 "Waarde naar\n% model",
												 "Koers in locale valuta"));
   if($setFill==false)                      
     unset($pdfObject->fillCell); 
}


function HeaderTRANS_L45($object)
{
  $pdfObject = &$object;
   $pdfObject->Ln(7);
    
  $pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);   
  
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;

  $pdfObject->row(array(vertaalTekst("Datum\n ",$pdfObject->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$pdfObject->rapport_taal),
                     vertaalTekst("Fonds\n ",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal\n ",$pdfObject->rapport_taal),
                     vertaalTekst("Valuta\n ",$pdfObject->rapport_taal),
										 vertaalTekst("Koers\nin valuta",$pdfObject->rapport_taal),
                     vertaalTekst("Valuta\nkoers",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop\n waarde",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop\n waarde",$pdfObject->rapport_taal),
										 vertaalTekst("Gerealiseerd\nresultaat",$pdfObject->rapport_taal)));  
     
   unset($pdfObject->fillCell);                    
}

  function HeaderMUT_L45($object)
  {
       	$pdfObject = &$object;
    $pdfObject->Ln(7);
    
    		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
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
										 vertaalTekst("Credit",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    
    
  }
  function HeaderMUT2_L45($object)
  {
   	$pdfObject = &$object;
    $pdfObject->Ln(7);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->row(array(vertaalTekst("\nBoekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("\nOmschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
										 vertaalTekst("\nBruto",$pdfObject->rapport_taal),
										 vertaalTekst("\nProvisie",$pdfObject->rapport_taal),
										 vertaalTekst("Inkomsten\nKosten",$pdfObject->rapport_taal),
										 vertaalTekst("\nBelasting",$pdfObject->rapport_taal),
										 vertaalTekst("\nNetto",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


	function HeaderOIB_L45($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	//  $pdfObject->HeaderOIB();
	}

	function HeaderCASHY_L45($object)
	{
  	  $pdfObject = &$object;
  	//  $pdfObject->ln();
  	//  $pdfObject->HeaderCASHY();
	}


	function HeaderSMV_L45($object)
	{
  	  $pdfObject = &$object;
      $pdfObject->ln(7);
     $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
     $pdfObject->Row(array("Boek-\ndatum","Aan/\nVerkoop",'Fonds','Saldo r/c',"Fonds-\nmutaties","Gekochte/\nverkochte rente","gererealiseerd resultaat (ytd)",
     "Transactie kosten","Dividend","Rente Obligaties","Bron-\nheffing","Bewaar\nloon","Beheer\nvergoeding","Rente","stortingen/\nonttrekkingen"));
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}


	function HeaderRISK_L45($object)
	{
	  $pdfObject = &$object;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);


   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
    unset($pdfObject->fillCell);
  	 
	}



  function HeaderATT_L45($object)
  {
    $pdfObject = &$object;


	}
  
  function HeaderEND_L45($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();

		// achtergrond kleur

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

				// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

//			echo "$inkoopEind - $inkoop en $verkoopEind - $verkoop en $resultaatEind - $resultaat ";
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

		$pdfObject->Line(($inkoop+2),$pdfObject->GetY(),$inkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($verkoop+2),$pdfObject->GetY(),$verkoopEind,$pdfObject->GetY());
		$pdfObject->Line(($resultaat+2),$pdfObject->GetY(),$resultaatEind,$pdfObject->GetY());

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
    //	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
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