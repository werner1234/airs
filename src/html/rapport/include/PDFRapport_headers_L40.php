<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/04 16:40:47 $
 		File Versie					: $Revision: 1.37 $

 		$Log: PDFRapport_headers_L40.php,v $
 		Revision 1.37  2020/03/04 16:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2019/02/13 16:42:08  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2019/02/03 13:43:54  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2019/01/05 18:38:35  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2018/01/06 18:10:41  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2017/07/15 16:13:43  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2017/01/21 17:48:03  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2017/01/19 08:05:11  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/01/19 07:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/01/18 17:02:28  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2016/09/07 15:42:21  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/02/03 13:07:09  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2015/09/20 17:32:28  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2015/09/05 16:48:04  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/02/15 10:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2014/08/23 15:45:01  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2013/12/21 18:31:53  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2013/11/02 17:04:05  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2013/07/13 15:19:44  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/07/10 16:01:24  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/03/09 16:22:24  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/02/17 11:00:30  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/02/10 10:06:07  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/01/20 13:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/12/05 16:45:29  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/11/14 16:48:28  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/10/07 14:57:17  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/10/02 16:17:32  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/09/30 11:18:17  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/09/23 08:51:44  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/09/16 12:45:46  rvv
 		*** empty log message ***
 		
*/

 function Header_basis_L40($object)
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);
    

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

    $pdfObject->rapport_koptext = str_replace("{Namen}", $namen, $pdfObject->rapport_koptext);

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		//rapport_risicoklasse


    if(!empty($pdfObject->rapport_logo_tekst))
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

		if ($pdfObject->rapport_layout != 17 )
		  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if(is_file($pdfObject->rapport_logo))
		{

 		    $factor=0.035;
		    $xSize=1246*$factor;
		    $ySize=540*$factor;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, $xSize, $ySize);
		}
	
			$pdfObject->AutoPageBreak=false;
			if ($pdfObject->rapport_type != "FACTUUR")
			{

				$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
				$pdfObject->SetY(-14);
				$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_voetfontsize);
				if($pdfObject->last_rapport_type == 'TRANS')
				{

					$transactietypenomschrijving= array('A'=>'Aankoop','V'=>'Verkoop',
																							'D'=>'Deponering','L'=>'Lichting',
																							'A/O'=>'Aankoop / openen','A/S'=>'Aankoop / sluiten',
																							'V/O'=>'Verkoop / openen','V/S'=>'Verkoop / sluiten',);
					$transVoet='';
					foreach ($transactietypenomschrijving as $key=>$value)
					{
						if($transVoet != '')
							$transVoet .="     -     ";
						$transVoet .= "$key = $value";
					}
					$pdfObject->MultiCell(200,4,$transVoet,'1','L');
				}

				$pdfObject->MultiCell(240,4,$pdfObject->rapport_voettext,'0','L');
				$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

			}
			$pdfObject->AutoPageBreak=true;

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 160;
		else
			$x = 250;

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  $pdfObject->SetY($y+15);
	  $pdfObject->SetX(100);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);


	  $pdfObject->MultiCell(190,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'R');


		$pdfObject->headerStart = $pdfObject->getY()+14;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
 }
 
 function Headerwaardeprognose_L40($object)
 {
   $pdfObject = &$object;

 }


function HeaderVKM_L40($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
	function HeaderFRONT_L40($object)
	{ 
	  $pdfObject = &$object;
	}

  function HeaderRISK_L40($object)
  {
  	$pdfObject = &$object;
  }

	function HeaderVOLK_L40($object)
	{ 
	  $pdfObject = &$object;
		$pdfObject->HeaderVOLK();
	}

function HeaderKERNV_L40($object)
{
  $pdfObject = &$object;
  $w=282/7;
  $pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w);
  
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
  $pdfObject->row(array(vertaalTekst("Jaar",$pdfObject->rapport_taal)."\n ",
                    vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Stortingen en \nonttrekkingen",$pdfObject->rapport_taal),
                    vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).")",
                    vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
  
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
}

function HeaderKERNZ_L40($object)
{
  $pdfObject = &$object;
  
}
function HeaderTRANSFEE_L40($object)
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

function HeaderPERFG_L40($object)
{
	$pdfObject = &$object;
	unset($pdfObject->CellBorders);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 281, 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

	$pdfObject->SetWidths(array(297-2*$pdfObject->marge));
	$pdfObject->SetAligns(array('C'));
	$pdfObject->ln(2);
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	if($pdfObject->portefeuilledata['model'] == '')
		$prefix="Portefeuille ".$pdfObject->portefeuilledata['Portefeuille'];
	else
		$prefix=$pdfObject->portefeuilledata['modelOmschrijving'];
	$pdfObject->row(array($prefix." vs. benchmark "));
	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
//	$pdfObject->HeaderPERFG();
}

  function HeaderPERFD_L40($object)
	{
  $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->Rect($pdfObject->marge,$pdfObject->GetY(),array_sum($pdfObject->widthA), 8, 'F');
    
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
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
    $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

	function HeaderZORG_L40($object)
	{ 
	  $pdfObject = &$object;
		$pdfObject->HeaderZORG();
	}  
  
	function HeaderAFM_L40($object)
	{ 
    $pdfObject = &$object;
   // $pdfObject->SetY($pdfObject->GetY()-4);
    $pdfObject->HeaderOIB();
	}
  
  function HeaderMODEL_L40($object)
	{ 
    $pdfObject = &$object;
   // $pdfObject->SetY($pdfObject->GetY()-4);
   // $pdfObject->HeaderModel();

	}
  
  function HeaderVAR_L40($object)
	{ 
	  $pdfObject = &$object;
	//	$pdfObject->HeaderVOLK();
  /*
  1. Instrument
2. Coupondatum
3. Rating instrument
4. Nominaal bedrag (T)
5. Huidige koers
6. Huidige waarde (T)
7. Effectief rendement (YTM) (T)
8. Modified Duration (T)
9. Resterende looptijd (T)

*/
     unset($pdfObject->CellBorders);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 281, 12 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
    
		$pdfObject->SetWidths(array(70,20,20,20,25,20,20,22,22,22,20));
		$pdfObject->SetAligns(array("L",'L','L','L','L','L','L','L','L'));


 $pdfObject->SetAligns(array("L",'R','R','R','R','R','R','R','R','R','R','R','R'));

    //$pdfObject->ln();
	
   
   
     $pdfObject->row(array(
		 vertaalTekst("\n \nNaam obligatie",$pdfObject->rapport_taal),
     vertaalTekst("\n \nNominaal",$pdfObject->rapport_taal),
		 vertaalTekst("\nAankoop\nkoers",$pdfObject->rapport_taal),
     vertaalTekst("\nHuidige koers",$pdfObject->rapport_taal),
		 vertaalTekst("\nMarktwaarde\n(incl. rente)",$pdfObject->rapport_taal),
     vertaalTekst("\nCoupon datum",$pdfObject->rapport_taal),
		 vertaalTekst("\nRente\nper jaar",$pdfObject->rapport_taal),
     vertaalTekst("Effectief rendement bij aankoop",$pdfObject->rapport_taal),
 		 vertaalTekst("Huidig effectief rendement",$pdfObject->rapport_taal),
     vertaalTekst("% rendement bij verkoop pj.",$pdfObject->rapport_taal),
		 vertaalTekst("\n \nDuration",$pdfObject->rapport_taal)));		   
     /*
		 $pdfObject->row(array("","",
		 vertaalTekst("Actuele\nWaarde",$pdfObject->rapport_taal),
		 vertaalTekst("Rating instrument",$pdfObject->rapport_taal),
		 vertaalTekst("Rating debiteur",$pdfObject->rapport_taal),
		 vertaalTekst("Coupon Yield",$pdfObject->rapport_taal),
		 vertaalTekst("Yield to Maturity",$pdfObject->rapport_taal),
		 vertaalTekst("Modified duration",$pdfObject->rapport_taal),
		 vertaalTekst("Resterende looptijd",$pdfObject->rapport_taal)));
  */
 
       
	}
  
  
   	function HeaderMOD_L40($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
    

		    $pdfObject->SetWidths($pdfObject->widthB);
		    $pdfObject->SetAligns($pdfObject->alignB);
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	    	$pdfObject->row(array("","\n \n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"\n \n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Actuele\nKoers\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Actuele\nWaarde\nin euro",$pdfObject->rapport_taal)));
      
 
    		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize); 
	}


function HeaderOIH_L40($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
  
        $pdfObject->SetWidths($pdfObject->widthB);
      $pdfObject->SetAligns($pdfObject->alignB);
      $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
      $pdfObject->row(array("","\n \n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                        vertaalTekst("Actuele\nKoers\nin valuta",$pdfObject->rapport_taal),
                        vertaalTekst("Actuele\nWaarde\nin euro",$pdfObject->rapport_taal),
                        "\n ".vertaalTekst("Werkelijke Percentage",$pdfObject->rapport_taal),
                        "\n ".vertaalTekst("Model Percentage",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Afwijking in EUR",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Kopen",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Verkopen",$pdfObject->rapport_taal),
                         ));
    
 
  $pdfObject->ln();
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}
  
 	function HeaderHSE_L40($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 12 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
    
                          
    if($pdfObject->tweedeDeel == true)
    {
      $pdfObject->setWidths(array(5,120,25,120)); 
      $pdfObject->SetAligns(array('L','L','L','R'));
      $pdfObject->ln();
      $pdfObject->row(array('','Verdeling over beleggingscategorieën','','Verdeling aandelen'));
      $pdfObject->ln();      
    }
    else
    {
      
      if($pdfObject->modelLayout==true)
      {
 		    $pdfObject->SetWidths($pdfObject->widthB);
		    $pdfObject->SetAligns($pdfObject->alignB);
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	    	$pdfObject->row(array("","\n \n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
        vertaalTekst("Actuele\nWaarde\nin euro",$pdfObject->rapport_taal),
                				"Model Percentage",
												 "Werkelijk Percentage\nexcl. norm",
												 "Afwijking\nexcl. norm",
                         "Mutatie\nexcl. norm",
												 "Werkelijk Percentage\nincl. norm",
												 "Afwijking\nincl. norm",
                         "Mutatie\nincl. norm",
												 ""));       
/*
werkelijk excl. norm / 
afwijking tov excl. norm / 
mutatie tov. excl norm / 

werkelijk incl. norm /  
afwijking tov incl. norm / 
mutatie tov incl. norm
*/                     
      }
      else
      {
		    $pdfObject->SetWidths($pdfObject->widthB);
		    $pdfObject->SetAligns($pdfObject->alignB);
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
	    	$pdfObject->row(array("","\n \n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
                        "\n \n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
										"\n \n".vertaalTekst("Aantal",$pdfObject->rapport_taal),

											vertaalTekst("Koers per\n".date("d-m-Y",$pdfObject->rapport_datumvanaf)."\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Waarde per\n".date("d-m-Y",$pdfObject->rapport_datumvanaf)."\nin euro",$pdfObject->rapport_taal),
											"",
											vertaalTekst("Actuele\nKoers\nin valuta",$pdfObject->rapport_taal),
											vertaalTekst("Actuele\nWaarde\nin euro",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("Resultaat\nin euro",$pdfObject->rapport_taal)." *",
											"\n".vertaalTekst("Resultaat\nin %",$pdfObject->rapport_taal)));
      }
    }
    		$pdfObject->ln();
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize); 
	}


  function HeaderATT_L40($object)
	{
    $pdfObject = &$object;
    $pdfObject->HeaderATT();
	}
  
  function HeaderSCENARIO_L40($object)
	{
    $pdfObject = &$object;
 	}
  
  function HeaderMUT_L40($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array('',
										 '',
										 vertaalTekst("\nOmschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("\nBoekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("\nRekening",$pdfObject->rapport_taal),
										 "",
										 vertaalTekst("\nDebet",$pdfObject->rapport_taal),
										 vertaalTekst("\nCredit",$pdfObject->rapport_taal),
										 ""));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderOIB_L40($object)
	{
    $pdfObject = &$object;

		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		$lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
	  $pdfObject->SetX($pdfObject->marge+$lijn1+5);
	  $pdfObject->MultiCell(90,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
	  $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
											vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
											vertaalTekst("in valuta",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}



	function HeaderPERF_L40($object)
	{
	  $pdfObject = &$object;
	  $object->SetFillColor($object->rapport_kop_bgcolor[r],$object->rapport_kop_bgcolor[g],$object->rapport_kop_bgcolor[b]);
		$object->Rect($object->marge, $object->getY(), array_sum($object->widthB), 8, 'F');
		$object->SetTextColor($object->rapport_kop_fontcolor[r],$object->rapport_kop_fontcolor[g],$object->rapport_kop_fontcolor[b]);
		$object->SetFont($object->rapport_font,'',$object->rapport_fontsize);

    $object->ln(2);
	  $object->Cell(100,4, '',0,0);
		$object->Cell(100,4, vertaalTekst("Verslagperiode",$object->rapport_taal)." ".date("j",$object->rapport_datumvanaf)." ".vertaalTekst($object->__appvar["Maanden"][date("n",$object->rapport_datumvanaf)],$object->rapport_taal)." ".date("Y",$object->rapport_datumvanaf)." ".vertaalTekst("tot en met",$object->rapport_taal)." ".date("j",$object->rapport_datum)." ".vertaalTekst($object->__appvar["Maanden"][date("n",$object->rapport_datum)],$object->rapport_taal)." ".date("Y",$object->rapport_datum),0,0);
		$object->ln(2);
		$object->SetWidths($object->widthB);
		$object->SetAligns($object->alignB);
		$object->SetFont($object->rapport_font,'',$object->rapport_fontsize);
		$object->row(array("",
										 "",
										 "",
										 "",
										 "",
										 ""));

		$object->SetWidths($object->widthA);
		$object->SetAligns($object->alignA);

		$object->Line($object->marge,$object->GetY(),$object->marge + array_sum($object->widthB),$object->GetY());
  
	}


  function HeaderTRANS_L40($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
   	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

			$pdfObject->row(array(vertaalTekst("\nDatum",$pdfObject->rapport_taal),
										 vertaalTekst("Type\ntransactie",$pdfObject->rapport_taal),
										 vertaalTekst("\nAantal",$pdfObject->rapport_taal),
										 vertaalTekst("\nOmschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoopkoers\nin valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoopwaarde\nin euro",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoopkoers\nin valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoopwaarde\nin euro",$pdfObject->rapport_taal),
										 vertaalTekst("",$pdfObject->rapport_taal),
										 vertaalTekst("Historisch resultaat",$pdfObject->rapport_taal),
                     vertaalTekst("Resultaat lopend jaar",$pdfObject->rapport_taal)
										 ));//
                   
      $pdfObject->ln(1);
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

		//if (!isset($object->pdf->rapportageDatumWaarde) || $extraWhere != '')
		//{
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
				" rapportageDatum = '" . $object->rapportageDatum . "' AND " .
				" portefeuille = '" . $object->portefeuille . "' $extraWhere"
				. $__appvar['TijdelijkeRapportageMaakUniek'];
			$DB->SQL($query);
			$DB->Query();
			$portefwaarde = $DB->nextRecord();
			$portTotaal = $portefwaarde['totaal'];
		/*
			if ($extraWhere == '')
			{
				$object->pdf->rapportageDatumWaarde = $portTotaal;
			}
		}
		else
		{
			$portTotaal = $object->pdf->rapportageDatumWaarde;
		}
*/
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
	function PieChart($object, $w, $h, $data, $format, $colors = null)
	{


		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);
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
		$object->SetFont($object->rapport_font, '', $object->rapport_fontsize);

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
		$object->SetFillColor($object->rapport_kop_bgcolor[r], $object->rapport_kop_bgcolor[g], $object->rapport_kop_bgcolor[b]);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte, 'F');
		$object->SetFillColor(0);
		$object->Rect($object->marge + $lmarge, $object->getY(), 110 + 9 + $extraX, $hoogte);
		$object->SetX($object->marge + $lmarge);

		// kopfontcolor
		//$object->SetTextColor($object->rapport_kop4_fontcolor[r],$object->rapport_kop4_fontcolor[g],$object->rapport_kop4_fontcolor[b]);
		$object->SetTextColor($object->rapport_kop_fontcolor[r], $object->rapport_kop_fontcolor[g], $object->rapport_kop_fontcolor[b]);
		$object->SetFont($object->rapport_kop4_font, $object->rapport_kop4_fontstyle, $object->rapport_kop4_fontsize);
		$object->Cell(40, 4, vertaalTekst("Index-vergelijking", $object->rapport_taal), 0, 0, "L");

		$object->SetFont($object->rapport_font, $object->rapport_fontstyle, $object->rapport_fontsize);
		//$object->SetTextColor($object->rapport_fonds_fontcolor[r],$object->rapport_fonds_fontcolor[g],$object->rapport_fonds_fontcolor[b]);
		$object->SetTextColor($object->rapport_kop_fontcolor[r], $object->rapport_kop_fontcolor[g], $object->rapport_kop_fontcolor[b]);
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