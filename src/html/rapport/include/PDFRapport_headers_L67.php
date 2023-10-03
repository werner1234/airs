<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/04 15:59:25 $
 		File Versie					: $Revision: 1.25 $

 		$Log: PDFRapport_headers_L67.php,v $
 		Revision 1.25  2020/07/04 15:59:25  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2020/03/25 16:43:07  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2019/05/25 16:22:07  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2019/04/14 15:41:42  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2019/04/13 17:42:49  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2019/04/06 17:11:28  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/01/12 17:08:31  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/10/31 17:23:34  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/10/27 16:49:57  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/09/01 16:53:24  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/02/12 07:32:48  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/02/10 18:09:12  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/11/15 17:03:35  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/09/13 15:45:00  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/07/05 16:06:40  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/04/21 15:10:13  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2017/02/18 17:32:08  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/10/26 16:13:40  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/09/04 14:42:06  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/04/10 15:48:34  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/04/03 10:58:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/03/12 17:41:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 18:17:00  rvv
 		*** empty log message ***
 		
 
 	
*/

function Header_basis_L67($object)
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
			$pdfObject->rapportNewPage = $pdfObject->page;
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 1;
    }
    else
    {

			if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
				$pdfObject->rapportNewPage = $pdfObject->page;


			if($pdfObject->CurOrientation=='P')
     {
       $voetbeginY=284;
       $pageWidth=210;
     }
     else
     {
       $voetbeginY=196;
       $pageWidth=297;
     }
     // $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
     // $pdfObject->Line($pdfObject->marge,25,$pageWidth-$pdfObject->marge,25);
      
 
  	if(!isset($pdfObject->customPageNo) || $pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		$pdfObject->customPageNo = 1;

		

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor(0);
		$pdfObject->SetDrawColor(0);
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
       $factor=0.05;
		   $xSize=961*$factor;//1500 $x=885*$factor;
		   $ySize=331*$factor;//182 $y=849*$factor;

       $logoX=$pageWidth/2-$xSize/2;
			 $pdfObject->Image($pdfObject->rapport_logo, $logoX, 5, $xSize, $ySize);
		}
    

      $lijnY=-2;
      //$pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
     // $pdfObject->Line($pdfObject->marge,$voetbeginY+$lijnY,$pageWidth-$pdfObject->marge,$voetbeginY+$lijnY);
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
    
  
        $pdfObject->AutoPageBreak=false;
        $pdfObject->SetY($voetbeginY);
	      $pdfObject->MultiCell(90,3,$pdfObject->rapport_koptext,0,'L');
   	    $pdfObject->SetY($voetbeginY); 
	      $pdfObject->MultiCell($pageWidth-($pdfObject->marge*2),4,vertaalTekst("Pagina",$pdfObject->rapport_taal).": ".$pdfObject->customPageNo,0,'R');
        $pdfObject->customPageNo++;
        $pdfObject->setXY($pdfObject->marge,$voetbeginY+4);
        $pdfObject->SetFont($pdfObject->rapport_font,'',6);
        $pdfObject->MultiCell($pageWidth-$pdfObject->marge*2,4,$pdfObject->rapport_voettext,0,'C');
        /*
        ."\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n".
          vertaalTekst("Opmaak",$pdfObject->rapport_taal).": ".date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y")
          */
        $pdfObject->AutoPageBreak=true;
      
      


		$pdfObject->SetY($y);



	 // $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->SetY(15);
	  $pdfObject->SetX($pdfObject->marge);
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->SetY(20);
	 	$pdfObject->headerStart = $pdfObject->getY()+4+13;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    $pdfObject->last_rapport_type=$pdfObject->rapport_type;
  }
	$pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];

}


	function HeaderVKM_L67($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

  function HeaderVKMS_L67($object)
  {
    $pdfObject = &$object;
    $pdfObject->ln(8);
  }

function Headerwaardeprognose_L67($object)
{
  $pdfObject = &$object;
  $pdfObject->setY(10);
  
  $pdfObject->SetFont($pdfObject->rapport_font,'B',16);
  
  $pdfObject->SetX($pdfObject->marge);
  $pdfObject->Cell(200,8, vertaalTekst("Waardeprognose", $pdfObject->rapport_taal) ,0,1,"L");
  $pdfObject->SetX(250);
  

  $pdfObject->ln(10);
  $pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
  
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

}


function HeaderVAR_L67($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(76,16,16,21,21,25, 5,  20,20,20,20,20));
    $pdfObject->ln(7);
	  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widths), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $positie=array();
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
  //$pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);
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
   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);

		unset($pdfObject->CellBorders);//"Modified\nduration",
    unset($pdfObject->fillCell);
	}


function HeaderOIH_L67($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  
  $dataWidth=array(45,20,12,20,20,2,20,20,12,2,20,20,17,20,20,14);
  $splits=array(2,4,5,8,9,11,12,14);
  $n=0;
  $kopWidth=array();
  foreach ($dataWidth as $index=>$value)
  {
    if($index<=$splits[$n])
      $kopWidth[$n] += $value;
    if($index>=$splits[$n])
      $n++;
  }
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($kopWidth);
  $pdfObject->SetAligns(array('L','C','L','C','L','C','L','C'));
  $pdfObject->CellBorders = array('','U','','U','','U','','U');
  $pdfObject->Row(array('',"Totaal commitment",'','Totaal opgevraagd','','Totaal terugbetaald','','Restant investering'));
  $pdfObject->CellBorders = array();
  
  $pdfObject->SetWidths($dataWidth);
  
  
  
  unset($pdfObject->CellBorders);
  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetWidths($dataWidth);
  
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->pageYstart=$pdfObject->GetY();
  $pdfObject->Row(array(vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
                    vertaalTekst("Aanvang",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    vertaalTekst("fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("in%",$pdfObject->rapport_taal),
                    '',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Directe opbrengst",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Fondsvaluta",$pdfObject->rapport_taal),
                    vertaalTekst("Rapportage\nvaluta",$pdfObject->rapport_taal),
                    //'',
                    vertaalTekst("Multiple",$pdfObject->rapport_taal)));
  $pdfObject->CellFontColor=$lastColors;
  $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
  $pdfObject->SetLineWidth(0.1);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor(0);
  
}

function HeaderVOLK_L67($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVOLK();
}


function HeaderFISCAAL_L67($object)
{
  $pdfObject = &$object;
  $pdfObject->widthB[3]=20;
  $pdfObject->widthB[6]=5;
  $pdfObject->widthB[10]=5;
  unset($pdfObject->widthB[13]);
  unset($pdfObject->widthB[14]);
  //listarray($pdfObject->widthB);
  $pdfObject->HeaderFISCAAL();
}

function HeaderVHO_L67($object)
{
	$pdfObject = &$object;

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  unset($pdfObject->fillCell);
  for($i=0;$i<count($pdfObject->widthA);$i++)
    $pdfObject->fillCell[] = 1;
    
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
  if($pdfObject->rapport_titel == "Vastrentende waarden")
  {
    
    $pdfObject->SetWidths($pdfObject->widthB);
	  $pdfObject->SetAligns($pdfObject->alignA);
	  $pdfObject->row(array("\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										"\n ",
                    vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Kost\nprijs",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
                   	vertaalTekst("Koers-\ndatum",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Rating",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Loop-\ntijd",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Yield",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Dura-\ntion",$pdfObject->rapport_taal)."\n ",
										vertaalTekst("Waarde",$pdfObject->rapport_taal)."\n ",
								    vertaalTekst("Opg.\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Ongereali-\nseerd",$pdfObject->rapport_taal),
                    vertaalTekst("W/V",$pdfObject->rapport_taal),
				         		vertaalTekst("Weging",$pdfObject->rapport_taal)."\n "
                		));
	
  }
  elseif($pdfObject->rapport_titel == "Liquiditeiten")
  {
    $pdfObject->SetWidths($pdfObject->widthA);
	  $pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->widths[0]=$pdfObject->widthA[0]-14;
		$pdfObject->widths[9]=$pdfObject->widthA[9]+14;


    $pdfObject->setXY($pdfObject->marge,155);
    $pdfObject->Ln();
  }  
  else
  {
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
                   '',// vertaalTekst("Opgelopen\nRente",$pdfObject->rapport_taal),
										vertaalTekst("Fonds\nResultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta\nResultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal)."\n "));
  }
 	$pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  if($pdfObject->rapport_titel != "Liquiditeiten")
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
		//$pdfObject->SetWidths($pdfObject->widthA);
	//	$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
	//	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
unset($pdfObject->fillCell);
	}

function HeaderHSE_L67($object)
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
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge ,$pdfObject->GetY());
		$pdfObject->ln();
    	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
unset($pdfObject->fillCell);
	}


 function HeaderPERF_L67($object)
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
  
  function HeaderINDEX_L67($object)
  {
    $pdfObject = &$object;
  }


  function HeaderSCENARIO_L67($object)
{
    $pdfObject = &$object;
    $pdfObject->Ln();
		
}

  function HeaderMOD_L67($object)
{
    $pdfObject = &$object;
    $pdfObject->Ln();
    $pdfObject->HeaderMOD();
		
}

  function HeaderMODEL_L67($object)
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
		$pdfObject->SetWidths(array(70,25,25,25,25,25,25,6,30,25));
		$pdfObject->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
    
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

    for($i=0;$i<count($pdfObject->widths);$i++)
      $pdfObject->fillCell[] = 1;
    $pdfObject->SetY(27);
		$pdfObject->Row(array("Fonds\n ",
												 "Model Percentage",
												 "Werkelijk Percentage",
												 "Grootste afwijking",
												 "Kopen\n ",
												 "Verkopen\n ",
												 "Overschrijding waarde EUR",
												 " \n ",
												 "Waarde volgens percentage model",
												 "Koers in locale valuta"));
   if($setFill==false)                      
     unset($pdfObject->fillCell); 
}


function HeaderTRANS_L67($object)
	{
	  $pdfObject = &$object;
    
	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  $pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	}

  function HeaderMUT_L67($object)
  {
    $pdfObject = &$object;
    if($pdfObject->rapport_titel == "Mutatie overzicht")
    {
      $pdfObject->ln();
 	  	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
		  $pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag",$pdfObject->rapport_taal),
										 vertaalTekst("Valuta koers",$pdfObject->rapport_taal),
										 vertaalTekst("DIVB",$pdfObject->rapport_taal)));     
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    }
/*
    		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

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
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    */
    
  }
  function HeaderMUT2_L67($object)
  {
   	$pdfObject = &$object;
    $pdfObject->Ln(7);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);


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

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


	function HeaderOIB_L67($object)
	{
  	  $pdfObject = &$object;
  	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
  	//  $pdfObject->HeaderOIB();
	}

	function HeaderCASHY_L67($object)
	{
  	  $pdfObject = &$object;
  	//  $pdfObject->ln();
  	//  $pdfObject->HeaderCASHY();
	}



function HeaderKERNZ_L67($object)
{
  $pdfObject = &$object;
  //  $pdfObject->ln();
  //  $pdfObject->HeaderCASHY();
}

	function HeaderSMV_L67($object)
	{
  	  $pdfObject = &$object;
      $pdfObject->ln(7);
     $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
     $pdfObject->Row(array("Boek-\ndatum","Aan/\nVerkoop",'Fonds','Saldo r/c',"Fonds-\nmutaties","Gekochte/\nverkochte rente","gererealiseerd resultaat (ytd)",
     "Transactie kosten","Dividend","Rente Obligaties","Bron-\nheffing","Bewaar\nloon","Beheer\nvergoeding","Rente","stortingen/\nonttrekkingen"));
     $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}


	function HeaderRISK_L67($object)
	{
	  $pdfObject = &$object;
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);


   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
    unset($pdfObject->fillCell);
  	 
	}



 function HeaderATT_L67($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,27,28,30,23,23,23,24,28,24,26);
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
    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
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
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("maand",$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->SetTextColor(0,0,0);

             
    //$sumWidth = array_sum($pdfObject->widthA);
	 // $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}
  
  function HeaderEND_L67($object)
  {
    $pdfObject = &$object;

    //	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }

function HeaderGRAFIEK_L67($object)
{
  $pdfObject = &$object;
  $pdfObject->ln(8);
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
    $valutaData=array();
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