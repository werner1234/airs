<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/06/12 15:23:21 $
 		File Versie					: $Revision: 1.25 $

 		$Log: PDFRapport_headers_L70.php,v $
 		Revision 1.25  2019/06/12 15:23:21  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/06/09 14:52:19  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2019/05/25 16:22:07  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2018/12/15 17:49:14  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2018/12/12 16:19:08  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/11/10 18:20:33  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/09/08 17:43:29  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/08/25 17:11:27  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/07/07 17:35:19  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/04/14 17:23:49  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/03/10 18:24:22  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/12/27 18:29:09  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/10/02 12:38:58  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/09/18 08:49:02  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/08/13 16:55:26  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/07/02 09:36:54  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/06/29 16:04:07  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/06/25 16:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/06/19 15:22:08  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/06/12 10:20:31  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/06/08 15:40:53  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/05/29 10:19:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/05/22 18:49:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		
 		
*/

 function Header_basis_L70($object)
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
			if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
				$pdfObject->rapportNewPage = $pdfObject->page;
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

			$depotbank=$pdfObject->rapport_depotbank;
			if($depotbank=='TGB')
				$depotbank='IG';


		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$pdfObject->rapport_koptext = $pdfObject->rapport_consolidatieKoptext;
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleFormat}", $pdfObject->rapport_portefeuilleFormat, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Portefeuille}", $pdfObject->rapport_portefeuille, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $depotbank, $pdfObject->rapport_koptext);
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
		$pdfObject->rapport_koptext = str_replace("{Depotbank}", $depotbank, $pdfObject->rapport_koptext);
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


		$pdfObject->SetY($y);

		$headersExtraX=0;
		if($pdfObject->rapport_type == "MOD" )
		{
			$pw=210;
			$ph=297;
			$toonRechten=false;
		}
    elseif($pdfObject->rapport_type == "SCENARIO2" || $pdfObject->rapport_type == "VKMS" )
    {
      $pw=210;
      $ph=297;
      $toonRechten=true;
      $headersExtraX=20;
    }
		else
		{
			$pw=297;
			$ph=210;
			$toonRechten=true;
		}

      placeIcon($pdfObject,15,10);
      $pdfObject->SetXY(26,13.5);
		  $pdfObject->SetFont($pdfObject->rapport_font,'b',12);
		  $pdfObject->MultiCell(200,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->Line($pdfObject->marge,21,$pw-$pdfObject->marge,21);
      $pdfObject->AutoPageBreak=false;

			$pdfObject->SetXY($pw-70-$pdfObject->marge-90-1+$headersExtraX,16);
			$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'R');

			$pdfObject->SetXY($pw-70-$pdfObject->marge,16);

			if($pdfObject->rapport_type=='VOLK' || $pdfObject->rapport_type=='VAR')
			{
				$datum = vertaalTekst("Periode", $pdfObject->rapport_taal) . " " .
					date("j", $pdfObject->rapport_datumvanaf) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datumvanaf)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datumvanaf).' t/m '.
					date("j", $pdfObject->rapport_datum) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datum)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datum);
			}
			else
			{
				$datum = vertaalTekst("Rapportagedatum", $pdfObject->rapport_taal) . " " . date("j", $pdfObject->rapport_datum) . " " . vertaalTekst($pdfObject->__appvar["Maanden"][date("n", $pdfObject->rapport_datum)], $pdfObject->rapport_taal) . " " . date("Y", $pdfObject->rapport_datum);
			}
			$pdfObject->MultiCell(70,4,$datum,0,'R');
			$pdfObject->Line($pw-70-$pdfObject->marge+$headersExtraX,16,$pw-70-$pdfObject->marge+$headersExtraX,21);
		//	$pdfObject->SetXY($pw-55,16);
		//	$pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');


			$pdfObject->Line($pdfObject->marge,$ph-15,$pw-$pdfObject->marge,$ph-15);
     // $pdfObject->Line($pdfObject->marge+60,$ph-15,$pdfObject->marge+60,$ph-10);
      $pdfObject->SetXY(15,$ph-14);
      $pdfObject->MultiCell(175,4, $pdfObject->rapport_naam1.' '. $pdfObject->rapport_naam2,0,'L');
     // $pdfObject->SetXY($pdfObject->marge+63,$ph-14);
    //  $pdfObject->MultiCell(90,4,'a',0,'L');
			$pdfObject->SetXY($pw-90-32,$ph-14);
			if($toonRechten==true)
			  $pdfObject->MultiCell(90,4,vertaalTekst("Aan deze opgave kunnen geen rechten worden ontleend",$pdfObject->rapport_taal),0,'R');
			$pdfObject->Line($pw-32,$ph-15,$pw-32,$ph-10);
      $pdfObject->SetXY($pw-55,$ph-14);
      $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo,0,'R');
	  
      $pdfObject->SetY(20);
      
      $pdfObject->AutoPageBreak=true;
      
	    $pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
   }
 }
 
 function placeIcon($object,$x=-1,$y=-1)
 {
   $pdfObject = &$object;
   $icon=base64_decode('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');
   if($x<0 && $y<0)
   {   
     $x=$pdfObject->getX();
     $y=$pdfObject->getY();
   }
   $pdfObject->MemImage($icon,$x,$y,11);
 }

  
 	function HeaderFRONT_L70($object)
	{
    $pdfObject = &$object;
	}

function HeaderZORG_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->ln(8);
	$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	$pdfObject->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Percentage','ZorgWaarde','% Totale waarde'));
	$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), 297-$pdfObject->marge,$pdfObject->GetY());
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderVKMS_L70($object)
{
	$pdfObject = &$object;
}

function HeaderAFM_L70($object)
{
  $pdfObject = &$object;
}

function HeaderINHOUD_L70($object)
{
	$pdfObject = &$object;
}
  function HeaderEND_L70($object)
  {
  	$pdfObject = &$object;
		$pdfObject->ln();
  }

function HeaderMOD_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();

	$pdfObject->widthB = array(10,50,20,25,25,30,20);
	$pdfObject->alignB = array('L','L','R','R','R','R','R');

	// voor kopjes
	$pdfObject->widthA = array(55,20,25,25,30,20);
	$pdfObject->alignA = array('L','R','R','R','R','R');

	$pdfObject->headerMOD();
}

function HeaderCASH_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->ln(6);
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	if($pdfObject->debug)
		$pdfObject->row(array("Datum","Instrument", "Coupon/lossing", "Bedrag",'jaar','PV','PV*T'));
	else
		$pdfObject->row(array("Datum","Instrument", "Coupon/lossing", "Bedrag"));
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge ,$pdfObject->GetY());
}

function HeaderOIBS_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	//$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	//$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
	$pdfObject->ln();

	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->row(array("",
								 vertaalTekst("Sectoren",$pdfObject->rapport_taal),
								 vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
								 vertaalTekst("Aantal",$pdfObject->rapport_taal),
								 vertaalTekst("Koers",$pdfObject->rapport_taal),
								 vertaalTekst("Valuta",$pdfObject->rapport_taal),
								 "",
								 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)));

	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge ,$pdfObject->GetY());
}

function HeaderMUT_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->ln(2);
	$pdfObject->SetX(100);
	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
  $pdfObject->ln(2);
	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$fillBackup=$pdfObject->fillCell;
	unset($pdfObject->fillCell);
	$pdfObject->row(array('',
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

	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->fillCell=$fillBackup;
	$pdfObject->ln(0.5);

}
  
 	function HeaderOIV_L70($object)
	{
	    $pdfObject = &$object;
	}

function HeaderHUIS_L70($object)
{
	$pdfObject = &$object;
}
 	function HeaderVOLKD_L70($object)
	{
    $pdfObject = &$object;
  	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
		$pdfObject->fillCell=$fillBackup;
	  $huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= array_sum($pdfObject->widthB);
	
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
  	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		// lijntjes onder beginwaarde in het lopende jaar
  	$tmpY = $pdfObject->GetY();
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, '', 0, "C");
		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
  	//$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
  	$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("ISIN-code",$pdfObject->rapport_taal),
										  '','',"",
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""));
	
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->setY($pdfObject->GetY()-8);
		$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
		$pdfObject->ln();
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->fillCell=$fillBackup;
	}
  
   function HeaderATT_L70($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26.907,25.8721,31.0466,31.0466,23.8023,23.8023,23.8023,24.8372,28.9767,26.907);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R');

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
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
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
		                      vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
		$pdfObject->fillCell=$fillBackup;
	}
  
  function HeaderVOLK_L70($object)
	{
    $pdfObject = &$object;
   	$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$huidige+5);

		if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
	    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
	  else
	    $pdfObject->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();

	
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
										vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal)
                    ));
	

		$pdfObject->setY($y);
		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
		$pdfObject->fillCell=$fillBackup;
	}
  
  function HeaderSCENARIO_L70($object)
  {
    $pdfObject = &$object;
  }
function HeaderSCENARIO2_L70($object)
{
  $pdfObject = &$object;
}

  function HeaderRISK_L70($object)
  {
    $pdfObject = &$object;
  }

function HeaderDOORKIJK_L70($object)
{
  $pdfObject = &$object;
}
function HeaderDOORKIJKVR_L70($object)
{
  $pdfObject = &$object;
}

function HeaderVKM_L70($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$fillBackup=$pdfObject->fillCell;
	unset($pdfObject->fillCell);
	//$dataWidth=array(28,50,20,20,20,20,20,20,20,17,17,17);
	$dataWidth=array(28,50,18,18,18,18,18,18,18,18,18,18,15);
	$pdfObject->SetWidths($dataWidth);
	$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	$lastColors=$pdfObject->CellFontColor;
	unset($pdfObject->CellFontColor);
	unset($pdfObject->CellBorders);
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
	$pdfObject->CellFontColor=$lastColors;
	$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	//$pdfObject->SetLineWidth(0.1);
	$pdfObject->fillCell=$fillBackup;




}

  function HeaderOIH_L70($object)
	{
	  $pdfObject = &$object;
    $pdfObject->ln();
    //$dataWidth=array(28,55,20,20,20,20,22,22,22,22,22);
    $dataWidth=array(27.384615384615,53.791208791209,19.56043956044,19.56043956044,19.56043956044,19.56043956044,21.516483516484,21.516483516484,21.516483516484,21.516483516484,21.516483516484 );
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
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
      "\n".vertaalTekst("Stortingen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
    $pdfObject->CellFontColor=$lastColors;
    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
    $pdfObject->SetLineWidth(0.1);
		$pdfObject->fillCell=$fillBackup;
  }
  
  function HeaderPERFG_L70($object)
	{
    $pdfObject = &$object;
    $colWidth=(297-(2*$pdfObject->marge+30))/10;
    $tmp=array();
    for($i=0;$i<=9;$i++)
      $tmp[]=$colWidth;
    $tmp[]=15;
    $tmp[]=15;  
    $pdfObject->widthA = $tmp;//array(26,25,24,24,24,20,20,25,24,24,23,23);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
    $pdfObject->ln(1);
		//$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat",$pdfObject->rapport_taal),0,0);
		//$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
		$pdfObject->row(array("Kwartaal\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopenrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
		                      vertaalTekst("Rend.",$pdfObject->rapport_taal)."\n(".vertaalTekst($pdfObject->rapport_header_periode,$pdfObject->rapport_taal).")",
		                      vertaalTekst("Rend.",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumu.",$pdfObject->rapport_taal).")"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
    unset($pdfObject->fillCell);
	}

function HeaderHSE_L70($object)
	{
    $pdfObject = &$object;
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
			$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];
			$actueel 			= $eindhuidige + $pdfObject->widthB[6];
			$eindactueel 	= array_sum($pdfObject->widthB);

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

		// lijntjes onder beginwaarde in het lopende jaar

		$tmpY = $pdfObject->GetY();
		$pdfObject->SetX($pdfObject->marge+$huidige+5);
		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
		else if($pdfObject->rapport_layout == 4)
			$pdfObject->MultiCell(90,4, vertaalTekst("Fonds",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
//			$pdfObject->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
// Bovenstaande cel was standaard 90 Breed. Nu dynamische ivm tekstcentreren wanneer kolombreedte smaller.
		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		if($pdfObject->rapport_HSE_volgorde_beginwaarde == 0)
			$pdfObject->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0, "C");
		else
			$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");


		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$pdfObject->row(array("","\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											"",
											vertaalTekst("Per stuk \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											($pdfObject->rapport_inprocent)?vertaalTekst("In % Totaal",$pdfObject->rapport_taal):""));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		$pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
		$pdfObject->setY($pdfObject->GetY()-8);
		$pdfObject->row(array(vertaalTekst("Categorie",$pdfObject->rapport_taal)));
		$pdfObject->ln();

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->fillCell=$fillBackup;
  }


  function HeaderVHO_L70($object)
	{
	  $pdfObject = &$object;
	  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] +  $pdfObject->widthB[14];
		$eindresultaat2 = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] ;

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	//	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
    $pdfObject->Ln(4);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

	  $pdfObject->SetX($pdfObject->marge+$huidige);

		$pdfObject->Cell(55,4, vertaalTekst("Gemiddelde historische inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(60,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");

		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(70,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
	

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);


		$y = $pdfObject->getY();

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
												vertaalTekst("Aandeel op totale waarde",$pdfObject->rapport_taal),
												vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
												vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),
												vertaalTekst("in %",$pdfObject->rapport_taal)
											));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
	  $pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
		$pdfObject->setY($y);
		  $pdfObject->row(array("Categorie\n"));
		$pdfObject->ln();
		$pdfObject->ln();
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->fillCell=$fillBackup;

	}
  
  function HeaderGRAFIEK_L70($object)
  {
    $pdfObject = &$object;
  }

function HeaderOIB_L70($object)
{
  $pdfObject = &$object;
}
function HeaderDUURZAAM_L70($object)
{
  $pdfObject = &$object;
}
function HeaderOIR_L70($object)
{
  $pdfObject = &$object;
}
	function HeaderCASHY_L70($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}    
  function HeaderPERF_L70($object)
  {
    $pdfObject = &$object;
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(2);
	  $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
		$pdfObject->ln(2);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
   
    //$pdfObject->Ln(10);
		//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
  }


  function HeaderTRANS_L70($object)
  {
    $pdfObject = &$object;
     $pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$fillBackup=$pdfObject->fillCell;
		unset($pdfObject->fillCell);
			$pdfObject->SetX(0);
     
			$pdfObject->MultiCell(297,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
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

	// Formaat van de kopcellen dynamisch gemaakt aan de hand van de kolombreedte.
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
		$pdfObject->ln(1);
		$pdfObject->fillCell=$fillBackup;
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

		//if(!isset($object->pdf->rapportageDatumWaarde) || $extraWhere !='')
		//{
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
		//}
		//else
		//  $portTotaal=$object->pdf->rapportageDatumWaarde;

		$query = "SELECT TijdelijkeRapportage.portefeuille, 
                     TijdelijkeRapportage.fonds, 
                     TijdelijkeRapportage.rekening, 
                     TijdelijkeRapportage." . $type . "Omschrijving as Omschrijving, 
                     TijdelijkeRapportage." . $type . " as type,
                     (TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  " .
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '" . $object->portefeuille . "') AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $object->rapportageDatum . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" ORDER BY TijdelijkeRapportage." . $type . "Volgorde";
		debugSpecial($query, __FILE__, __LINE__);

		$DB->SQL($query); //echo $query;
		$DB->Query();

		while ($categorien = $DB->NextRecord())
		{// listarray($categorien);
			if ($categorien['type'] == '')
			{
				$categorien['type'] = 'geenWaarden';
				$categorien['Omschrijving'] = 'Geen regio';
				$kleuren[$categorien['type']] = array('R' => array('value' => 100), 'G' => array('value' => 100), 'B' => array('value' => 100));
			}
			$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = vertaalTekst($categorien['Omschrijving'], $object->pdf->rapport_taal);


			if (count($items) > 0 && !in_array($categorien['type'], $items))
			{
				$categorien['type'] = 'Overige';
				$object->pdf->veldOmschrijvingen[$type][$categorien['type']] = 'Overige';
				$kleuren[$categorien['type']] = array('R' => array('value' => 100), 'G' => array('value' => 100), 'B' => array('value' => 100));
			}

			$valutaData[$categorien['type']]['port']['waarde'] += $categorien['subtotaalactueel'];
			if ($categorien['fonds'] <> '')
			{
				$valutaData[$categorien['type']]['port']['fondsen'][$categorien['fonds']] = $categorien['fonds'];
			}
			if ($categorien['rekening'] <> '')
			{
				$valutaData[$categorien['type']]['port']['rekeningen'][$categorien['rekening']] = $categorien['rekening'];
			}

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

				$typeData['port']['fondsen'][$waarde] = $data['port']['fondsen'];
				$typeData['port']['rekeningen'][$waarde] = $data['port']['rekeningen'];


				$typeData['port']['procent'][$waarde] = $data['port']['waarde'] / $portTotaal;
				$typeData['port']['waarde'][$waarde] = $data['port']['waarde'];
				$typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;
				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}
}
?>