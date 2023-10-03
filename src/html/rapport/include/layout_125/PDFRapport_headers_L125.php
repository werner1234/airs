<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/16 17:12:28 $
 		File Versie					: $Revision: 1.21 $

 		$Log: PDFRapport_headers_L107.php,v $
 		Revision 1.21  2019/11/16 17:12:28  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2019/10/12 17:11:44  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2019/10/11 17:39:17  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/09/05 15:53:27  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2018/06/10 11:45:56  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2018/06/10 06:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2018/06/09 15:58:54  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/03/11 10:53:28  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2018/03/04 10:14:13  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2018/02/24 18:33:46  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/08/21 08:52:52  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/11/24 16:03:42  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2013/11/23 17:23:24  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2013/04/27 16:29:28  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/03/24 09:41:15  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/03/23 16:19:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/03/20 16:56:53  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/03/17 10:58:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/03/13 17:01:08  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/12/15 14:52:51  rvv
 		*** empty log message ***

*/

function Header_basis_L125($object)
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
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast  && $pdfObject->rapport_layout != 16)
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
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
		$pdfObject->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $pdfObject->portefeuilledata['VermogensbeheerderNaam'], $pdfObject->rapport_koptext);
		$pdfObject->rapport_koptext = str_replace("{crm.naam}", $pdfObject->portefeuilledata['crm.naam'], $pdfObject->rapport_koptext);
    $pdfObject->rapport_koptext = str_replace("{ModelPortefeuille}", $pdfObject->portefeuilledata['ModelPortefeuille'], $pdfObject->rapport_koptext);
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
  
    $pdfObject->memImage($pdfObject->logoBeeld, $pdfObject->w-$pdfObject->marge-$pdfObject->logoXsize,$pdfObject->h-20 ,$pdfObject->logoXsize);
 

    $break=$pdfObject->AutoPageBreak;
    $pdfObject->AutoPageBreak=0;
    $pdfObject->SetXY($pdfObject->w-38,$pdfObject->h-15);
    $pdfObject->SetTextColor($pdfObject->textGrijs[0],$pdfObject->textGrijs[1],$pdfObject->textGrijs[2]);
    $pdfObject->MultiCell(15,4,$pdfObject->customPageNo,0,'R');//."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
    $pdfObject->AutoPageBreak=$break;
    $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);

	 // $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->Rect(0,15,$pdfObject->w,10,'F',null, array($pdfObject->textGroen[0],$pdfObject->textGroen[1],$pdfObject->textGroen[2]));
    $pdfObject->SetXY(20,15);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize+5);
    $pdfObject->SetTextColor(255,255,255);
		$pdfObject->MultiCell($pdfObject->w-20,10,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->SetY(30);
	 	$pdfObject->headerStart = $pdfObject->getY()+17;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
  }
}

function subHeader_L125($pdfObject,$y,$widths,$data,$achtergrondKleur='',$alings=array(),$fontStyle=array())
{
  if(!is_array($achtergrondKleur))
    $achtergrondKleur=$pdfObject->kopGrijs;
  $pdfObject->rect(0,$y,$pdfObject->w,8,'F', null, array($achtergrondKleur[0],$achtergrondKleur[1],$achtergrondKleur[2]));
  $pdfObject->setWidths($widths);
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize+2);
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->setXY(20,$y);
  foreach($data as $i=>$txt)
  {
    if(isset($alings[$i]))
      $align=$alings[$i];
    else
      $align='L';
    if(isset($fontStyle[$i]))
    {
      $pdfObject->SetFont($fontStyle[$i][0],$fontStyle[$i][1],$fontStyle[$i][2]);
    }
    $pdfObject->Cell($widths[$i], 8, $txt, 0, 0, $align);
  }
}


	function HeaderVKMS_L125($object)
	{
	  return '';
		$pdfObject = &$object;
		$pdfObject->ln();
		$widthBackup=$pdfObject->widths;
		$dataWidth=array(28,50,20,20,20,20,20,18,18,18,18,18,15);
		$pdfObject->SetWidths($dataWidth);
		$pdfObject->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);

		$lastColors=$pdfObject->CellFontColor;
		unset($pdfObject->CellFontColor);
		unset($pdfObject->CellBorders);
		if(!isset($pdfObject->vmkHeaderOnderdrukken))
		{
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
			unset($pdfObject->vmkHeaderOnderdrukken);
			$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
		}
		$pdfObject->widths=$widthBackup;
		$pdfObject->CellFontColor=$lastColors;
		$pdfObject->SetLineWidth(0.1);
	}

function HeaderVKM_L125($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderVKM();
}

function HeaderVOLK_L125($object)
{
    $pdfObject = &$object;

	if($pdfObject->skipRapportHeader==true)
	{
		$pdfObject->ln();
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
		$pdfObject->ln();
		unset($pdfObject->skipRapportHeader);
		return ;
	}
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
			$eindhuidige 	= $huidige+$pdfObject->widthB[2]+$pdfObject->widthB[3]+$pdfObject->widthB[4]+ $pdfObject->widthB[5];

			$actueel 			= $eindhuidige + $pdfObject->widthB[6] ;
			$eindactueel 	= $actueel  + $pdfObject->widthB[7]+ $pdfObject->widthB[8];

			$resultaat 		= $eindactueel +  $pdfObject->widthB[9] ;
			$eindresultaat = $resultaat  +  $pdfObject->widthB[10] +  $pdfObject->widthB[11]	+  $pdfObject->widthB[12]+  $pdfObject->widthB[13];
	

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);


			$pdfObject->SetX($pdfObject->marge+$huidige);
			$pdfObject->Cell(80,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($pdfObject->marge+$actueel-3);
			if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde van lopend jaar",$pdfObject->rapport_taal), 0,0,"C");
			else
			  $pdfObject->Cell(55,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
			$pdfObject->SetX($pdfObject->marge+$resultaat);
			$pdfObject->Cell(60,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");
	

		$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();


			$pdfObject->row(array("\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
												vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										"",
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);
	


		$pdfObject->setY($y);

			$pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
			$pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->ln();

		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->ln();
  }



function HeaderHUIS_L125($object)
{
    $pdfObject = &$object;
		
}

function HeaderDOORKIJK_L125($object)
{
	$pdfObject = &$object;

}

function HeaderDOORKIJKVR_L125($object)
{
  $pdfObject = &$object;
  
}

   function HeaderPERF_L125($object)
  {
	  	$pdfObject = &$object;
	  	$pdfObject->SetY($pdfObject->GetY()+4);

  }

  function HeaderTRANS_L125($object)
  {
    $pdfObject=&$object;
   
  }


  function HeaderMUT_L125($object)
  {
    $pdfObject=&$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


  function HeaderOIH_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

function HeaderINDEX_L125($object)
{
  $pdfObject = &$object;
}

	function HeaderOIBS_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}

	function HeaderHSE_L125($object)
	{
			$pdfObject = &$object;

	}

	function HeaderOIB_L125($object)
	{
  	  $pdfObject = &$object;

	}

	function HeaderOIV_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}

	function HeaderPERFG_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderPERFG();
	}
	function HeaderPERFD_L125($object)
	{
  	  $pdfObject = &$object;

	}
function HeaderVOLKD_L125($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $dataWidth=array(28,55,15,25,25,20,22,25,22,18,20);
  $pdfObject->SetWidths($dataWidth);
  $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln();
  $lastColors=$pdfObject->CellFontColor;
  unset($pdfObject->CellFontColor);
  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
  
  
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
	function HeaderVHO_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderVHO();
	}
	function HeaderGRAFIEK_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderGRAFIEK();
	}


	function HeaderCASH_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	function HeaderCASHY_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	function HeaderSMV_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}


	function HeaderRISK_L125($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+7,$pdfObject->marge + 283,$pdfObject->GetY()+7);
	}



  function HeaderATT_L125($object)
	{
    $pdfObject = &$object;


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

function printPie_L125($pdfObject, $w, $h, $data, $format, $colors = null)
{
  
  
  $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  
  $pdfObject->legends=array();
  $pdfObject->wLegend=0;
  $pdfObject->sum=array_sum($data);
  $pdfObject->NbVal=count($data);
  foreach($data as $l=>$val)
  {
    $p=number_format($val,1,",",".").'%';
    $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
    $pdfObject->legends[]=$legend;
    $pdfObject->wLegend=max($pdfObject->GetStringWidth($legend),$pdfObject->wLegend);
  }
  
  $XPage = $pdfObject->GetX();
  $YPage = $pdfObject->GetY();
  $margin = 2;
  $hLegend = 3;
  $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
  $radius = floor($radius / 2);
  $XDiag = $XPage + $margin + $radius;
  $YDiag = $YPage + $margin + $radius;
  if ($colors == null)
  {
    for ($i = 0; $i < $pdfObject->NbVal; $i++)
    {
      $gray = $i * intval(255 / $pdfObject->NbVal);
      $colors[$i] = array($gray, $gray, $gray);
    }
  }
  
  //Sectors
  //$pdfObject->SetLineWidth(0.2);
  $angleStart = 0;
  $angleEnd = 0;
  $i = 0;
  
  foreach ($data as $key=>$val)
  {
    $angle = floor(($val * 360) / doubleval($pdfObject->sum));
    if ($angle != 0)
    {
      $angleEnd = $angleStart + $angle;
      $pdfObject->SetFillColor($colors[$key][0], $colors[$key][1], $colors[$key][2]);
      $pdfObject->setDrawColor($colors[$key][0], $colors[$key][1], $colors[$key][2]);
      $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
      $angleStart += $angle;
    }
    $i++;
  }
  if ($angleEnd != 360)
  {
    $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
  }
  $pdfObject->setDrawColor(0,0,0);
  //Legends
  $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  
  $x1 = $XPage + $w + $radius * .5;
  $x2 = $x1 + $hLegend + $margin - 12;
  $y1 = $YDiag - ($radius) + $margin;
  
  $i=0;
  foreach ($data as $key=>$val)
  {
    $pdfObject->SetFillColor($colors[$key][0], $colors[$key][1], $colors[$key][2]);
    //$pdfObject->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
    $pdfObject->Circle($x1 - 10, $y1+1, $hLegend/2,0,360,'F');//, $line_style = null, $fill_color = null, $nSeg = 8)
    $pdfObject->SetXY($x2, $y1);
    if(strpos($pdfObject->legends[$i],'||')>0)
    {
      $parts=explode("||",$pdfObject->legends[$i]);
      $pdfObject->Cell(0, $hLegend, $parts[1]);
    }
    else
    {
      $pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
    }
    $y1 += $hLegend + $margin+2;
    $i++;
  }
}

function formatGetal_L125($waarde, $dec, $teken='')
{
  
  $getalTxt=number_format(abs($waarde),$dec,",",".");
  
  if($teken=='€')
  {
    if($waarde < 0)
    {
      return '- € '.$getalTxt;
    }
    return '€ '.$getalTxt;
  }
  elseif($teken=='%')
  {
    if($waarde < 0)
    {
      return '- '.$getalTxt.'%';
    }
    return $getalTxt.'%';
  }
  elseif($teken!='')
  {
     return $teken.' '.$getalTxt;
  }
  else
  {
    return number_format($waarde,$dec,",",".");
  }
  
}

?>