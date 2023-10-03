<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/23 16:39:00 $
 		File Versie					: $Revision: 1.46 $

 		$Log: PDFRapport_headers_L51.php,v $
 		Revision 1.46  2020/05/23 16:39:00  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2020/05/16 15:57:02  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2020/04/22 15:40:47  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2020/04/08 15:42:42  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2020/03/21 16:32:57  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2019/12/08 13:30:47  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2019/12/07 17:48:23  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2019/12/01 07:51:04  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2019/11/27 15:55:39  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2019/11/23 12:59:28  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2019/11/16 17:12:28  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2019/10/30 16:45:39  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2019/09/14 17:09:05  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2019/05/04 18:22:48  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2018/11/04 11:15:32  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.30  2018/04/18 16:17:01  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/03/14 17:17:41  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/12/30 16:38:17  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/10/28 18:03:18  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2017/08/23 15:22:13  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/06/24 16:30:07  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2017/06/21 16:10:36  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/06/18 09:18:24  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/03/25 16:01:09  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/08/27 16:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/08/21 08:52:52  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/06/05 12:37:50  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/02/13 14:02:39  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/01/12 12:14:55  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2015/12/30 19:01:23  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/12/20 16:46:36  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2015/04/22 14:26:44  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2015/03/19 07:00:37  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2015/03/01 14:08:16  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/09/06 15:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/05/03 15:47:40  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/04/26 16:43:08  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/04/17 17:20:41  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/04/02 15:53:15  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/02/02 10:49:59  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/01/08 16:52:37  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/11/13 15:47:34  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/09/18 15:23:07  rvv
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

 function Header_basis_L51($object)
 { 
   $pdfObject = &$object;     
//echo $pdfObject->rapport_type." <br>\n| ".$pdfObject->lastPortefeuille2." != ".$pdfObject->portefeuilledata['Portefeuille'] ."<br>\n";
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
      
  	  if($pdfObject->lastPortefeuille2 != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille2))
  	  	$pdfObject->rapportNewPage = $pdfObject->page;
        
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
    
    //$pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
    //$pdfObject->Rect($pdfObject->marge,$pdfObject->marge,297-$pdfObject->marge*2,15,'DF');
    
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

		if($pdfObject->rapport_type == "MOD")
		{
			$logopos = 50;
		}
		else
		{
			$logopos = 120;
		}


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
 		    $factor=0.04;
		    $xSize=1417*$factor;
		    $ySize=591*$factor;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 0, $xSize, $ySize);
		}

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 160;
		else
			$x = 250;

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  //$pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)."\n\n",0,'R');
	  
    //$pdfObject->SetTextColor(255);
    $pdfObject->SetY(18);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  $pdfObject->SetX($pdfObject->marge);
	  //$pdfObject->MultiCell(297,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
    $pdfObject->MultiCell(250,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;

    }
    $pdfObject->lastPortefeuille2=$pdfObject->portefeuilledata['Portefeuille'];
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

	function HeaderVKM_L51($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderOIS_L51($object)
{
	$pdfObject = &$object;
}

function HeaderPORTAL_L51($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIBS();
}

function HeaderOIV_L51($object)
{
  $pdfObject = &$object;
  
  if($pdfObject->volkRapport==true)
  {
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Cell(297-$pdfObject->marge*2,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
  }
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths(array(80,35,25,30,25,25,25,20,15));
  $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R'));
  $pdfObject->ln(7);

  if($pdfObject->noHeader==true)
    return;

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widths), 8 , 'F');

  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
//Categorie
  if($pdfObject->volkRapport==true)
  {
    $pdfObject->row(array(
                      "\n".vertaalTekst("Categorie",$pdfObject->rapport_taal),
                      '',
                      vertaalTekst("Periode begin",$pdfObject->rapport_taal)."\n".vertaalTekst("waarde in EUR",$pdfObject->rapport_taal),
                      '',
                      vertaalTekst("Actuele",$pdfObject->rapport_taal)."\n".vertaalTekst("waarde in EUR",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("Fondsresultaat",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("Valutaresultaat",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("in %",$pdfObject->rapport_taal)," \n ",
                    ));
  
  }
  else
  {
    $pdfObject->row(array(
                      "\n".vertaalTekst("Categorie",$pdfObject->rapport_taal),
                      '',
                      vertaalTekst("Historische",$pdfObject->rapport_taal)."\n".vertaalTekst("waarde in EUR",$pdfObject->rapport_taal),
                      '',
                      vertaalTekst("Actuele",$pdfObject->rapport_taal)."\n".vertaalTekst("waarde in EUR",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("Fondsresultaat",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("Valutaresultaat",$pdfObject->rapport_taal),
                      "\n".vertaalTekst("in %",$pdfObject->rapport_taal)," \n ",
                    ));
  
  }
  

  
  $pdfObject->ln();
  $pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
  
}

function HeaderDOORKIJK_L51($object)
{
  $pdfObject = &$object;
}
	
  function HeaderOIH_L51($object)
	{
		$pdfObject = &$object;

		$pdfObject->ln();
		$dataWidth=array(65,25,15,30,30,10,25,30,20,20);

		$pdfObject->SetWidths($dataWidth);
		unset($pdfObject->CellBorders);
		$pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);


		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$y=$pdfObject->GetY();
		$sumWidth = array_sum($dataWidth);
		$pdfObject->Rect($pdfObject->marge,$y,$sumWidth,12,'F');
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->ln();
		$pdfObject->Cell($dataWidth[0]+$dataWidth[1]+$dataWidth[2],4, '',0,0);
		$pdfObject->Cell($dataWidth[3]+$dataWidth[4],4, vertaalTekst("Aanschafwaarde",$pdfObject->rapport_taal),0,0,'C');

		$pdfObject->Cell($dataWidth[5],4, '',0,0);
		$pdfObject->Cell($dataWidth[6]+$dataWidth[7]+$dataWidth[8]+$dataWidth[9],4, vertaalTekst("Resultaat",$pdfObject->rapport_taal),0,0,'C');
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge+$dataWidth[0]+$dataWidth[1]+$dataWidth[2],$pdfObject->GetY(),$pdfObject->marge+$dataWidth[0]+$dataWidth[1]+$dataWidth[2]+$dataWidth[3]+$dataWidth[4],$pdfObject->GetY());
		$pdfObject->Line($pdfObject->marge+array_sum($dataWidth)-$dataWidth[6]-$dataWidth[7]-$dataWidth[8]-$dataWidth[9],$pdfObject->GetY(),$pdfObject->marge+array_sum($dataWidth),$pdfObject->GetY());



		$lastColors=$pdfObject->CellFontColor;
		unset($pdfObject->CellFontColor);
		$pdfObject->pageYstart=$pdfObject->GetY();
		$pdfObject->Row(array(vertaalTekst("Naam Fonds/Object",$pdfObject->rapport_taal),
											vertaalTekst("Aantal",$pdfObject->rapport_taal),
											vertaalTekst("Valuta",$pdfObject->rapport_taal),
											vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
											vertaalTekst("Waarde in Euro",$pdfObject->rapport_taal),
											'',
											vertaalTekst("Directe kosten",$pdfObject->rapport_taal),
											vertaalTekst("Directe opgrengsten",$pdfObject->rapport_taal),
											vertaalTekst("Resultaat",$pdfObject->rapport_taal),
											vertaalTekst("Resultaat %",$pdfObject->rapport_taal)));
		$pdfObject->CellFontColor=$lastColors;
		$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
		$pdfObject->SetLineWidth(0.1);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->SetDrawColor(0);

	}

function HeaderEND_L51($object)
{
  $pdfObject = &$object;
  
  $pdfObject->ln();
  $dataWidth=array(65,25,15,30,30,10,25,30,20,20);
  
  $dataWidth=array(50,20,12,20,20,2,20,20,12,2,20,20,15,20,20,14);
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


function HeaderAFM_L51($object)
{
  $pdfObject = &$object;
  if($pdfObject->widthsDefault)
    $oldWidths=$pdfObject->widths;
  $pdfObject->CellBorders = array();
  if(isset($pdfObject->CellFontStyle))
  {
    $CellFontStyle=$pdfObject->CellFontStyle;
    unset($pdfObject->CellFontStyle);
  }
  
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_kleur[0],$pdfObject->rapport_kop_kleur[1],$pdfObject->rapport_kop_kleur[2]);
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

  
  $yStart=$pdfObject->getY();
  $pdfObject->setWidths($pdfObject->widthA);
  $pdfObject->setAligns($pdfObject->alignA);
  $pdfObject->Rect($pdfObject->marge, $yStart+.5, 297-($pdfObject->marge*2), 14, 'F');
  $pdfObject->ln(1.5);
  
  
  if($pdfObject->rapport_deel == 'overzicht')
  {
    $pdfObject->SetWidths(array(60,22,25,25,25,25,25,25,25,24));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $pdfObject->Row($pdfObject->rapport_header1);
    $pdfObject->fillCell = array();
    $pdfObject->CellBorders = array();
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY()+.5,297-($pdfObject->marge),$pdfObject->GetY()+.5);
    $pdfObject->Row(array(''));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    if(is_array($oldWidths))
      $pdfObject->widths=$oldWidths;

  }
  
  if(isset($CellFontStyle))
    $pdfObject->CellFontStyle=$CellFontStyle;
}

 	function HeaderCASHY_L51($object)
	{
    $pdfObject = &$object;

	}
 
  function HeaderINDEX_L51($object)
	{
    $pdfObject = &$object;

	}

  function HeaderZORG_L51($object)
  {
  	$pdfObject = &$object;
		$pdfObject->ln();
		$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		$pdfObject->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Percentage','ZorgWaarde'));
		$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }

function HeaderTRANSFEE_L51($object)
{
	$pdfObject = &$object;
	$pdfObject->SetY(40);
}

  function HeaderPERFG_L51($object)
	{
    $pdfObject = &$object;
    $pdfObject->ln();
    $pdfObject->ln();

	}
  
  function HeaderGRAFIEK_L51($object)
	{
    $pdfObject = &$object;

	}

function HeaderRISK_L51($object)
{
	$pdfObject = &$object;
	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	$pdfObject->SetWidths(array(76,16,16,21,21,25, 5,  20,20,20,20,20));
	$pdfObject->ln(7);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widths), 8 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

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
	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);

	unset($pdfObject->CellBorders);//"Modified\nduration",
	unset($pdfObject->fillCell);
}

function HeaderHUIS_L51($object)
{
  $pdfObject = &$object;
  $pdfObject->widthA = array(26,25,24,24,24,20,20,25,24,24,23,22);
  $pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  unset($pdfObject->fillCell);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->ln(1);
  $pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
  $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  $pdfObject->ln(1);
  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  
  $pdfObject->ln();
  $y=$pdfObject->GetY();
  $sumWidth = array_sum($pdfObject->widthA);
  $pdfObject->Rect($pdfObject->marge,$y,$sumWidth,12,'F');
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

  $pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
                    vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Rendement",$pdfObject->rapport_taal),
                    vertaalTekst("Rendement (Cumulatief)",$pdfObject->rapport_taal),
                    vertaalTekst("Benchmark",$pdfObject->rapport_taal),
                    vertaalTekst("Benchmark (Cumulatief)",$pdfObject->rapport_taal)));
  $pdfObject->SetY($y+8);
  $pdfObject->row(array('','€','€','%','%','%','%'));
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
  $pdfObject->SetDrawColor(0);
}

 	function HeaderATT_L51($object)
  {
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,24,24,24,20,20,25,24,24,23,22);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    unset($pdfObject->fillCell);  
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, '',0,0); //vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal)
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    
    $pdfObject->ln();
    $y=$pdfObject->GetY();
    $sumWidth = array_sum($pdfObject->widthA);
    $pdfObject->Rect($pdfObject->marge,$y,$sumWidth,12,'F');
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
 		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
 		if($pdfObject->attKwartalen==true)
    {
      $periode='kwartaal';
    }
    else
    {
      $periode='maand';
    }
		$pdfObject->row(array(vertaalTekst("Maand",$pdfObject->rapport_taal),
		                      vertaalTekst("Begin-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Stortingen en ont-\ntrekkingen",$pdfObject->rapport_taal),
		                      vertaalTekst("Gerealiseerd\nresultaat",$pdfObject->rapport_taal),
		                      vertaalTekst("Onge-\nrealiseerd resultaat",$pdfObject->rapport_taal),
		                      vertaalTekst("Inkomsten",$pdfObject->rapport_taal),
		                      vertaalTekst("Kosten",$pdfObject->rapport_taal),
		                      vertaalTekst("Opgelopen-\nrente",$pdfObject->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$pdfObject->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement ($periode)",$pdfObject->rapport_taal),
		                      vertaalTekst("Rendement (Cumulatief)",$pdfObject->rapport_taal)));
    $pdfObject->SetY($y+12);                      
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);                      
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());
    $pdfObject->SetDrawColor(0);
	}


 function HeaderVAR_L51($object)
 {
	 $pdfObject = &$object;
	 $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
	 $pdfObject->SetWidths(array(30+60,20,20,20,20,20,20));
	 $pdfObject->ln(7);
	 $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	 $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widths), 8 , 'F');
	 $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

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

	 $pdfObject->SetAligns(array('L','R','R'  ,'R','R','R','R','R','R'));
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

	 $pdfObject->row(array("\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
										 "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
										 vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
										 vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
										 vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
										 vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
										 vertaalTekst("%\nvan de Port.",$pdfObject->rapport_taal)));

	 $pdfObject->ln();
	 $pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);

	 unset($pdfObject->CellBorders);//"Modified\nduration",
	 unset($pdfObject->fillCell);
 }

 function HeaderVOLK_L51($object)
	{
	    $pdfObject = &$object;
      $dataWidth=array(65,23,11,23,23,3,23,22,12,3,16,14,16,16,10);//4+5+5+2
      $splits=array(2,4,5,8,9,14);
      $oldFill=$pdfObject->fillCell;
      $oldrowHeight=$pdfObject->rowHeight;
      $n=0;
		  $kopWidth=array();
      foreach ($dataWidth as $index=>$value)
      {
        if($index<=$splits[$n])
         $kopWidth[$n] += $value;
        if($index>=$splits[$n])
         $n++;
      }

    $pdfObject->rowHeight=4;
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
      $pdfObject->SetWidths($kopWidth);
      unset($pdfObject->fillCell);
	    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
 		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

    $y=$pdfObject->GetY();
    $sumWidth = array_sum($dataWidth);
    $pdfObject->Rect($pdfObject->marge,$y,$sumWidth,12,'F');

      $pdfObject->Row(array('',
      vertaalTekst('Beginwaarde rapportage periode',$pdfObject->rapport_taal),'',
      vertaalTekst('Actuele koers',$pdfObject->rapport_taal),'',
      vertaalTekst('Resultaat',$pdfObject->rapport_taal)));
      $pdfObject->CellBorders = array();

 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
      $pdfObject->Row(array(vertaalTekst("Categorie/Effect",$pdfObject->rapport_taal),vertaalTekst("Aantal",$pdfObject->rapport_taal),vertaalTekst("Valuta",$pdfObject->rapport_taal),
	    vertaalTekst("Per stuk\n in valuta",$pdfObject->rapport_taal),vertaalTekst("Waarde\n in EUR",$pdfObject->rapport_taal),
	    "",vertaalTekst("Per stuk\n in valuta",$pdfObject->rapport_taal),vertaalTekst("Waarde\n in EUR",$pdfObject->rapport_taal),vertaalTekst("in %",$pdfObject->rapport_taal),
	    "",vertaalTekst("Fonds\nresultaat",$pdfObject->rapport_taal),vertaalTekst("Valuta\nresultaat",$pdfObject->rapport_taal),
				vertaalTekst("Ongereali-\nresultaat",$pdfObject->rapport_taal),
					vertaalTekst("Directe\nopbrengst",$pdfObject->rapport_taal),vertaalTekst("in %",$pdfObject->rapport_taal)));//,"Historische\nkostprijs"
      
      $pdfObject->SetY($y+12); 
      $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->fillCell = $oldFill;
	    $pdfObject->rowHeight=$oldrowHeight;
      $pdfObject->SetDrawColor(0);
	    //$pdfObject->HeaderVOLK();
  }

function HeaderVOLKD_L51($object)
{
	$pdfObject = &$object;
	$pdfObject->headerVOLK();
}

function HeaderHSE_L51($object)
{
	$pdfObject = &$object;
}

function HeaderVHO_L51($object)
{
	$pdfObject = &$object;

	$pdfObject->headerVHO();
}

function HeaderOIR_L51($object)
{
  $pdfObject = &$object;

}

	function HeaderOIB_L51($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderKERNZ_L51($object)
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
                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("jaar",$pdfObject->rapport_taal).")",
                      vertaalTekst("Rendement",$pdfObject->rapport_taal)."\n(".vertaalTekst("Cumulatief",$pdfObject->rapport_taal).")"));
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $sumWidth = array_sum($pdfObject->widthA);
    $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}
 
 	function HeaderKERNV_L51($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Ln(10);
	} 
	function HeaderMUT_L51($object)
  {
    $pdfObject = &$object;

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);

		 if ($pdfObject->rapport_layout != 8)
		 {
  		$pdfObject->SetX(100);
	  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		 }
		  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->widthA = array(20,25,90,25,40,20,25,25,15);
    $pdfObject->widthB = $pdfObject->widthA;
		$pdfObject->SetWidths($pdfObject->widthB);
    
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
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
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }
  
  function HeaderPERFD_L51($object)
  {
    HeaderPERF_L51($object);
  }

	function HeaderPERF_L51($object)
	{
	  $pdfObject = &$object;
    
    $pdfObject->ln();
		$object->SetFont($object->rapport_font,$pdfObject->rapport_kop_fontstyle,$object->rapport_kop_fontsize);

         
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    if($pdfObject->doubleHeader==true)
    {
      $pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 12, 'F');
      unset($pdfObject->doubleHeader);
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+16,297-$pdfObject->marge,$pdfObject->GetY()+16);
    
    }
    else
    {
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY()+4, array_sum($pdfObject->widthB), 8, 'F');
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+12,297-$pdfObject->marge,$pdfObject->GetY()+12);
    } 
    $pdfObject->SetDrawColor(0);  
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 
		$object->SetWidths($object->widthA);
		$object->SetAligns($object->alignA);


  
	}


  function HeaderTRANS_L51($object)
  {
    $pdfObject = &$object;
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_kop_fontsize);
		$pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
    $startY=$pdfObject->GetY()+16;
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3];
		$inkoopEind = $inkoop + $pdfObject->widthB[4] + $pdfObject->widthB[5] + $pdfObject->widthB[6];
		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];
		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);
		$pdfObject->SetX($inkoop);
		$pdfObject->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($verkoop);
		$pdfObject->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($resultaat);
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
										 vertaalTekst("Aan-/\nver\nkoop",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Fonds",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Aankoop waarde in ",$pdfObject->rapport_taal)." ".$pdfObject->rapportageValuta,
										 vertaalTekst("Verkoop koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Verkoop waarde in ",$pdfObject->rapport_taal)." ".$pdfObject->rapportageValuta,
										 vertaalTekst("Beginwaarde lopend jaar ",$pdfObject->rapport_taal).$pdfObject->rapportageValuta,
                     vertaalTekst("Resultaat lopende jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),297-$pdfObject->marge,$pdfObject->GetY());               
     $pdfObject->SetY($startY);                
     $pdfObject->ln(1);
     $pdfObject->SetDrawColor(0);
  }
  
  
  function ophalenVerdelingsData_L51($rapport,$portefeuille,$index,$filter)
  {
		global $__appvar;
		$DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal 
    FROM TijdelijkeRapportage WHERE rapportageDatum ='".$rapport->rapportageDatum."' AND 
    portefeuille = '".$rapport->portefeuille."' GROUP BY TijdelijkeRapportage.portefeuille"
		.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
    while($tmp = $DB->nextRecord())
  		$totaalWaardePortefeuille[$tmp['portefeuille']] = $tmp['totaal'];
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ,portefeuille
    FROM TijdelijkeRapportage WHERE rapportageDatum ='".$rapport->rapportageDatum."' AND 
    portefeuille IN('".$portefeuille."','".$index."') $filter "
		.$__appvar['TijdelijkeRapportageMaakUniek']."GROUP BY portefeuille";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();

    while($tmp = $DB->nextRecord())
	  	$totaalWaarde[$tmp['portefeuille']] = $tmp['totaal'];
      
  $query = "SELECT TijdelijkeRapportage.valuta,
  TijdelijkeRapportage.valutaOmschrijving,
  TijdelijkeRapportage.valutaVolgorde
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille IN('".$portefeuille."','".$index."') AND 
TijdelijkeRapportage.rapportageDatum = '".$rapport->rapportageDatum."' $filter"
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.valuta
ORDER BY TijdelijkeRapportage.valutaVolgorde asc";  
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
      $valutas[$data['valuta']]=$data['valutaOmschrijving'];

  $query = "SELECT TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingssectorOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.portefeuille,
  if( TijdelijkeRapportage.beleggingssectorVolgorde=0,128, TijdelijkeRapportage.beleggingssectorVolgorde) as volgorde
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille IN('".$portefeuille."','".$index."') AND 
TijdelijkeRapportage.rapportageDatum = '".$rapport->rapportageDatum."' $filter "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.beleggingscategorie,TijdelijkeRapportage.beleggingssector
ORDER BY volgorde";  
		$DB->SQL($query);  
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      if($data['beleggingssector']=='')
      {
        $data['beleggingssector']=$data['beleggingscategorie'];
        $data['beleggingssectorOmschrijving']=$data['beleggingscategorieOmschrijving'];
      }
      $sectoren[$data['beleggingssector']]=$data['beleggingssectorOmschrijving'];
    }

    
    $query = "SELECT 
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.portefeuille,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.beleggingssector,
TijdelijkeRapportage.beleggingscategorie
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille IN('".$portefeuille."','".$index."')  AND 
TijdelijkeRapportage.rapportageDatum = '".$rapport->rapportageDatum."' $filter "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.valuta,TijdelijkeRapportage.beleggingssector
ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.valutaVolgorde";
		$DB->SQL($query); 
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      if($data['beleggingssector']=='')
        $data['beleggingssector']=$data['beleggingscategorie'];
      $table[$data['portefeuille']]['valuta'][$data['valuta']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['valuta'][$data['valuta']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
      $table[$data['portefeuille']]['valuta']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['valuta']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
      $table[$data['portefeuille']]['Totaal'][$data['beleggingssector']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['Totaal'][$data['beleggingssector']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
      $table[$data['portefeuille']]['Totaal']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['Totaal']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
    } 
    
    $query = "SELECT 
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.portefeuille,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille IN('".$portefeuille."','".$index."')  AND 
TijdelijkeRapportage.rapportageDatum = '".$rapport->rapportageDatum."' $filter "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.beleggingscategorie
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
		$DB->SQL($query); 
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      $categorien[$data['beleggingscategorie']]=$data['beleggingscategorieOmschrijving'];
      $table[$data['portefeuille']]['beleggingscategorie'][$data['beleggingscategorie']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['beleggingscategorie'][$data['beleggingscategorie']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
      $table[$data['portefeuille']]['beleggingscategorie']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['beleggingscategorie']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
    } 
   
    $query = "SELECT 
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta,
Sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.portefeuille,
TijdelijkeRapportage.regio,
TijdelijkeRapportage.regioOmschrijving
FROM TijdelijkeRapportage WHERE 
TijdelijkeRapportage.portefeuille IN('".$portefeuille."','".$index."')  AND 
TijdelijkeRapportage.rapportageDatum = '".$rapport->rapportageDatum."' $filter "
.$__appvar['TijdelijkeRapportageMaakUniek'].
" GROUP BY TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.regio
ORDER BY TijdelijkeRapportage.regioVolgorde";
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
    {
      $regios[$data['regio']]=$data['regioOmschrijving'];
      $table[$data['portefeuille']]['regio'][$data['regio']]['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['regio'][$data['regio']]['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
      $table[$data['portefeuille']]['regio']['Totaal']['waardeEUR']+=$data['actuelePortefeuilleWaardeEuro'];
      $table[$data['portefeuille']]['regio']['Totaal']['aandeel']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde[$data['portefeuille']];
    }

    return array('table'=>$table,'valutas'=>$valutas,'sectoren'=>$sectoren,'beleggingscategorien'=>$categorien,'regios'=>$regios);
  }



if(!function_exists('getTypeGrafiekData'))
{
	function getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
	{
	  global $__appvar;
	  $DB = new DB();
	  if(!is_array($object->pdf->grafiekKleuren))
	  {
	    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$object->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  	$DB->SQL($q);
  		$DB->Query();
  		$kleuren = $DB->LookupRecord();
  		$kleuren = unserialize($kleuren['grafiek_kleur']);
  		$object->pdf->grafiekKleuren=$kleuren;
	  }
    $kleurVertaling=array('Beleggingscategorie'=>'OIB','Valuta'=>'OIV','regio'=>'OIR','beleggingssector'=>'OIS');
    $geenWaardeKoppeling=array('Beleggingscategorie'=>'geenWaarden','Valuta'=>'geenWaarden','regio'=>'Geen regio','beleggingssector'=>'Geen sector');
    
	  $kleuren=$object->pdf->grafiekKleuren[$kleurVertaling[$type]];

	  if(!isset($object->pdf->rapportageDatumWaarde) || $extraWhere !='')
	  {
	   $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$object->rapportageDatum."' AND ".
								 " portefeuille = '".$object->portefeuille."' $extraWhere"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
  		$DB->SQL($query);
  		$DB->Query();
  		$portefwaarde = $DB->nextRecord();
  		$portTotaal = $portefwaarde['totaal'];
  		if($extraWhere=='')
  	  	$object->pdf->rapportageDatumWaarde=$portTotaal;
	  }
	  else
	    $portTotaal=$object->pdf->rapportageDatumWaarde;

		$query = "SELECT TijdelijkeRapportage.portefeuille, TijdelijkeRapportage.".$type."Omschrijving as Omschrijving, TijdelijkeRapportage.".$type." as type,SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  ".
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '".$object->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$object->rapportageDatum."' $extraWhere"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY ".$type."  ORDER BY TijdelijkeRapportage.".$type."Volgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $object->pdf->veldOmschrijvingen[$type][$categorien['type']]=vertaalTekst($categorien['Omschrijving'],$object->pdf->rapport_taal);
		  if ($categorien['type']=='')
		    $categorien['type']='geenWaarden';

		  if(count($items) > 0 && !in_array($categorien['type'],$items))
		  {
		    $categorien['type']='Overige';
		    $object->pdf->veldOmschrijvingen[$type][$categorien['type']]='Overige';
		    $kleuren[$categorien['type']]=array('R'=>array('value'=>100),'G'=>array('value'=>100),'B'=>array('value'=>100));
		  }


      $valutaData[$categorien['type']]['port']['waarde']+=$categorien['subtotaalactueel'];
    }

		foreach ($valutaData as $waarde=>$data)
		{
		  if(isset($data['port']['waarde']))
		  {
        $veldnaam=$object->pdf->veldOmschrijvingen[$type][$waarde];
        if($veldnaam=='')
          $veldnaam='Overige';
        if($waarde=='geenWaarden')
          $waarde=$geenWaardeKoppeling[$type];

		    $typeData['port']['procent'][$waarde]=$data['port']['waarde']/$portTotaal;
		    $typeData['port']['waarde'][$waarde]=$data['port']['waarde'];
		    $typeData['grafiek'][$veldnaam]=$typeData['port']['procent'][$waarde]*100;
		    $typeData['grafiekKleur'][]=array($kleuren[$waarde]['R']['value'],$kleuren[$waarde]['G']['value'],$kleuren[$waarde]['B']['value']);
		  }
		}

   $object->pdf->grafiekData[$type]=$typeData;

	}
}


if(!function_exists('PieChart'))
{
  function PieChart($object,$w, $h, $data, $format, $colors=null,$legendaLocatie)
  {



      $object->SetFont($object->rapport_font, '', $object->rapport_fontsize-2);
      $object->SetLegends($data,$format);

      $XPage = $object->GetX();
      $YPage = $object->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $object->NbVal; $i++) {
              $gray = $i * intval(255 / $object->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $object->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;

      $object->sum=0;
      foreach ($data as $key=>$value)
      {
        $data[$key]=abs($value);
        $object->sum+=abs($value);
      }


      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($object->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $object->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $object->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $object->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $object->SetFont($object->rapport_font, '', $object->rapport_fontsize-2);




if($legendaLocatie=='z')
{
  $max=0;
  for($i=0; $i<$object->NbVal; $i++) {
    $lw=$object->GetStringWidth($object->legends[$i]);
    if($lw>$max)
      $max=$lw;
  }
  
  $x1=($XPage+$radius+$margin)-$max/2;
  $x2 = $x1  + $margin ;
  $y1 = $YDiag + $radius + ($margin*2)  ;

        for($i=0; $i<$object->NbVal; $i++) {
          $object->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $object->Rect($x1-2, $y1, $hLegend, $hLegend, 'DF');
          $object->SetXY($x2,$y1);
          $object->Cell(0,$hLegend,$object->legends[$i]);
          $y1+=$hLegend + $margin;
      }
}
else
{
      $x1 = $XPage + $w ;
      $x2 = $x1  + $margin ;
      $y1 = $YDiag - $radius + ($margin*2)  ;
      for($i=0; $i<$object->NbVal; $i++) {
          $object->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $object->Rect($x1-2, $y1, $hLegend, $hLegend, 'DF');
          $object->SetXY($x2,$y1);
          $object->Cell(0,$hLegend,$object->legends[$i]);
          $y1+=$hLegend + $margin;
      }
}      
      $object->setY($YPage+$h);

  }
}


if(!function_exists('printAEXVergelijking'))
{
	function printAEXVergelijking($object,$vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	 
	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$object->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();
		$lmarge=140;

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($object->GetY() + $hoogte) > $object->pagebreak)
		{
			$object->AddPage();
			$object->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($object->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $object->rapport_perfIndexJanuari = false;
	  }
		if($object->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($object->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($object->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($object->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$object->ln();
		$object->SetFillColor($object->rapport_kop_bgcolor['r'],$object->rapport_kop_bgcolor['g'],$object->rapport_kop_bgcolor['b']);
		$object->Rect($object->marge+$lmarge,$object->getY(),110+9+$extraX,$hoogte,'F');
		$object->SetFillColor(0);
		$object->Rect($object->marge+$lmarge,$object->getY(),110+9+$extraX,$hoogte);
		$object->SetX($object->marge+$lmarge);

		// kopfontcolor
		//$object->SetTextColor($object->rapport_kop4_fontcolor['r'],$object->rapport_kop4_fontcolor['g'],$object->rapport_kop4_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		$object->SetFont($object->rapport_kop4_font,$object->rapport_kop4_fontstyle,$object->rapport_kop4_fontsize);
		$object->Cell(40,4, vertaalTekst("Index-vergelijking",$object->rapport_taal), 0,0, "L");

		$object->SetFont($object->rapport_font,$object->rapport_fontstyle,$object->rapport_fontsize);
		//$object->SetTextColor($object->rapport_fonds_fontcolor['r'],$object->rapport_fonds_fontcolor['g'],$object->rapport_fonds_fontcolor['b']);
		$object->SetTextColor($object->rapport_kop_fontcolor['r'],$object->rapport_kop_fontcolor['g'],$object->rapport_kop_fontcolor['b']);
		if($object->rapport_perfIndexJanuari == true)
			$object->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$object->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$object->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");

		if($object->portefeuilledata['Layout']==30 || $object->portefeuilledata['Layout']==14 || $object->portefeuilledata['Layout']==25)
		  $object->Cell(26,4, vertaalTekst("Perf in %",$object->rapport_taal), $border,$perfVal, "R");
		else
	  	$object->Cell(26,4, vertaalTekst("Performance in %",$object->rapport_taal), $border,$perfVal, "R");
		if($object->rapport_printAEXVergelijkingEur == 1)
		  $object->Cell(26,4, vertaalTekst("Perf in % in euro",$object->rapport_taal), $border,$perfEur, "R");
		if($object->rapport_perfIndexJanuari == true)
			$object->Cell(26,4, vertaalTekst("Jaar Perf.",$object->rapport_taal), $border,$perfJan, "R");

		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($object->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
			  }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
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

		  if($object->rapport_perfIndexJanuari == true)
		  {
		    $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
		  	$DB2->SQL($q);
		  	$DB2->Query();
		  	$koers0 = $DB2->LookupRecord();
		  }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[Beursindex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";
			$object->Cell($lmarge,4, '', $border,0, "L");
      $object->Cell(40,4, $perf[Omschrijving], $border,0, "L");
		  if($object->rapport_perfIndexJanuari == true)
		     $object->Cell(26,4, $object->formatGetal($koers0[Koers],2), $border,0, "R");
			$object->Cell(26,4, $object->formatGetal($koers1[Koers],2), $border,0, "R");
			$object->Cell(26,4, $object->formatGetal($koers2[Koers],2), $border,0, "R");
		  $object->Cell(26,4, $object->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($object->rapport_printAEXVergelijkingEur == 1)
		    $object->Cell(26,4, $object->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($object->rapport_perfIndexJanuari == true)
		    $object->Cell(26,4, $object->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $object->rapport_portefeuille."' ";
		$DB->SQL($query2);
		$DB->Query();

		while($perf = $DB->nextRecord())
		{

		  if($perf['Valuta'] != 'EUR')
		  {

		    if($object->rapport_perfIndexJanuari == true)
		    {
		      $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$januariDatum."' ORDER BY Datum DESC LIMIT 1 ";
		      $DB2->SQL($q);
			    $DB2->Query();
			    $valutaKoersJan = $DB2->LookupRecord();
		    }

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatumVanaf."' ORDER BY Datum DESC LIMIT 1 ";
		    $DB2->SQL($q);
			  $DB2->Query();
			  $valutaKoersStart = $DB2->LookupRecord();

		    $q = "SELECT Koers FROM Valutakoersen WHERE Valuta='".$perf['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1 ";
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

		  	if($object->rapport_perfIndexJanuari == true)
		    {
		  	  $q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$januariDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			    $DB2->SQL($q);
			    $DB2->Query();
			    $koers0 = $DB2->LookupRecord();
		    }

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatumVanaf."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers1 = $DB2->LookupRecord();

			$q = "SELECT Datum, Koers FROM Fondskoersen WHERE Datum <= '".$rapportageDatum."' AND Fonds = '".$perf[SpecifiekeIndex]."'  ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$koers2 = $DB2->LookupRecord();

			$performanceJaar = ($koers2['Koers'] - $koers0['Koers']) / ($koers0['Koers']/100 );
			$performance = ($koers2['Koers'] - $koers1['Koers']) / ($koers1['Koers']/100 );
			$performanceEur = ($koers2['Koers']*$valutaKoersStop['Koers'] - $koers1['Koers']*$valutaKoersStart['Koers']) / ($koers1['Koers']*$valutaKoersStart['Koers']/100 );
      //echo $perf[Omschrijving]." $performanceEur = (.".$koers2['Koers']."*".$valutaKoersStop['Koers']." - ".$koers1['Koers']."*".$valutaKoersStart['Koers'].") / (".$koers1['Koers']."*".$valutaKoersStart['Koers']."/100 );<br>";

      $object->Cell($lmarge,4, '', $border,0, "L");
			$object->Cell(40,4, $perf[Omschrijving], 0,0, "L");
			if($object->rapport_perfIndexJanuari == true)
		     $object->Cell(26,4, $object->formatGetal($koers0[Koers],2), $border,0, "R");
			$object->Cell(26,4, $object->formatGetal($koers1[Koers],2), $border,0, "R");
			$object->Cell(26,4, $object->formatGetal($koers2[Koers],2), $border,0, "R");
		  $object->Cell(26,4, $object->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($object->rapport_printAEXVergelijkingEur == 1)
		    $object->Cell(26,4, $object->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($object->rapport_perfIndexJanuari == true)
		    $object->Cell(26,4, $object->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
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

if(!function_exists('PieChart_L51'))
{
  function PieChart_L51($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
  {

      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $pdfObject->SetLegends($data,$format);
     

      $XPage = $pdfObject->GetX();
      $YPage = $pdfObject->GetY();
      
      if($pdfObject->debug==true)
      {
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
        $pdfObject->line($XPage+2,$YPage+$pdfObject->rowHeight-1,$XPage+2,$YPage+$pdfObject->rowHeight+4);
        $pdfObject->Rect($XPage,$YPage,$w,$h);
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
      } 
      $pdfObject->setXY($XPage,$YPage);
      $pdfObject->SetFont($pdfObject->rapport_font, 'B', 8.5);
      $pdfObject->Cell($w,4,$titel,0,1,'L');
      //$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

      $YPage=$YPage+$pdfObject->rowHeight+4;
      $pdfObject->setXY($XPage,$YPage);
      $margin = 4;
      $hLegend = 2;
      $radius = min($w, $h); //
      $radius = ($radius / 2)-4;
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $pdfObject->NbVal; $i++) {
              $gray = $i * intval(255 / $pdfObject->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $pdfObject->SetDrawColor(255,255,255);
      $pdfObject->SetLineWidth(0.1);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $factor =$radius+4;
      $pdfObject->SetFont($pdfObject->rapport_font, '', 7);
      foreach($data as $val) 
      {
          $angle = (($val * 360) / doubleval($pdfObject->sum));
          //$pdfObject->SetDrawColor(255,255,0);          
          $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          if ($angle != 0 && $angle>1)
          {
              $angleEnd = $angleStart + $angle;
              $avgAngle=($angleStart+$angleEnd)/360*M_PI;

              //$lineAngle=($angleEnd)/180*M_PI;
              //$pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$factor), $YDiag-(cos($lineAngle)*$factor));
              //echo ($angleEnd-$angleStart)."= ( $angleEnd-$angleStart ) $val  <br>\n";ob_flush();
             
              if(round($angleEnd,1)==360)
                $angleEnd=360;
            //    echo "$val : $XDiag, $YDiag, $radius, $angleStart, $angleEnd <br>\n";
              if(abs($angleEnd-$angleStart) > 1)
                $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd,'F');
              
              if($val > 2)
              {
                //$pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2); 
                if($pdfObject->debug==true)
                {
                  $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255)));
                  $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor));
                }
                $pdfObject->SetXY($XDiag+(sin($avgAngle)*$factor)-5, $YDiag-(cos($avgAngle)*$factor)-2); 
                $pdfObject->Cell(10,4,number_format($val,0,',','.').'%',0,0,'C');
              }                
              $angleStart += $angle;
          }
    
          $i++;
      }
      if ($angleEnd != 360) 
      {
        $pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360,'F');
      }
      
      
      $i = 0;
      foreach($data as $val) 
      {
          $angle = (($val * 360) / doubleval($pdfObject->sum));
          $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.3527,'color'=>array(255,255,255)));  
          if ($angle != 0 && $angle != 360) 
          {
              $angleEnd = $angleStart + $angle;
              $lineAngle=($angleEnd)/180*M_PI;
              $pdfObject->line($XDiag,$YDiag,$XDiag+(sin($lineAngle)*$radius), $YDiag-(cos($lineAngle)*$radius));
              $angleStart += $angle;
          }
          $i++;
      }
      
      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $pdfObject->SetDrawColor(0,0,0);

      //Legends
      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

      $x1 = $XPage + $margin;
      $x2 = $x1 + $hLegend + 2 ;
      $y1 = $YDiag + ($radius) + $margin +5;
      
      if($pdfObject->debug==true)
      {
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>'1,1'));
        $pdfObject->line($XPage+2,$YDiag + ($radius) + $margin,$XPage+2,$YDiag + ($radius) + $margin +5);
        $pdfObject->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,255),'dash'=>0));
      } 
      
      if(is_array($legendaStart))
      {
        $x1=$legendaStart[0];
        $y1=$legendaStart[1];
        $x2 = $x1 + $hLegend + 2 ;
        
      }
      elseif($legendaStart=='geen')
      {
        return '';
      }

      for($i=0; $i<$pdfObject->NbVal; $i++)
			{
          $pdfObject->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $pdfObject->Rect($x1, $y1, $hLegend, $hLegend, 'F');
          $pdfObject->SetXY($x2,$y1);
          $pdfObject->Cell(0,$hLegend,$pdfObject->legends[$i]);
          $y1+=$hLegend*2;
      }

  }
}


if(!function_exists('dualVBarDiagram_L51'))
{
  function dualVBarDiagram_L51($pdfObject,$w,$h,$data,$omschrijving,$kleuren,$volgorde)
  {
      global $__appvar;
      $legendaWidth = 60;
      $grafiekPunt = array();
      $verwijder=array();
      if(!is_array($volgorde))
        $volgorde=array_keys($omschrijving);
      
      $volgorde=array_reverse($volgorde);
      
      foreach ($data as $type=>$waarden)
      {
        $legenda[$type] = $type;
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$type][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$type]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$type][$categorie]);
             $grafiekNegatief[$type][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$type][$categorie]=0;


          if(!isset($colors[$categorie]))
          {
            if(isset($kleuren[$categorie]))
              $colors[$categorie]=array($kleuren[$categorie]['R']['value'],$kleuren[$categorie]['G']['value'],$kleuren[$categorie]['B']['value']);
            else
              $colors[$categorie]=array(rand(0,255),rand(0,255),rand(0,255));
          }
          $n++;
        }
      }



      $numBars = count($legenda);
      //$numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }

      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;


      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;



      $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
      $XPage = $pdfObject->GetX();
      $YPage = $pdfObject->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      $extraY=0;
      
    //  listarray($grafiekCategorie);
      $rowNr=ceil(count($grafiekCategorie)/2);
      $items=0;
      foreach (array_reverse($volgorde) as $categorie)
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $pdfObject->Rect($XstartGrafiek+$legendaWidth-9 , $YstartGrafiek-$h+7+$extraY, 2, 2, 'DF',null,$colors[$categorie]);
          $pdfObject->SetXY($XstartGrafiek+$legendaWidth-6 ,$YstartGrafiek-$h+6.5+$extraY );
          $pdfObject->Cell($legendaWidth, 3,$omschrijving[$categorie],0,0,'L');
          $extraY+=4;
        }
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $pdfObject->SetFont($pdfObject->rapport_font, '', 6);
      $pdfObject->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $pdfObject->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $pdfObject->SetXY($XstartGrafiek-12, $i-1.5);
        $pdfObject->Cell(10, 3, formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $pdfObject->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $pdfObject->SetXY($XstartGrafiek-12, $i-1.5);
          $pdfObject->Cell(10, 3, formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $pdfObject->NbVal=$numBars;

        $vBar = ($bGrafiek / ($pdfObject->NbVal + 1));
        $bGrafiek = $vBar * ($pdfObject->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $pdfObject->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $pdfObject->SetLineWidth($pdfObject->lineWidth);

      $pdfObject->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
   {
     foreach($volgorde as $categorie)
     {
        $val=$data[$categorie];
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $pdfObject->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $pdfObject->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $pdfObject->SetXY($xval, $yval+($hval/2)-2);
            $pdfObject->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
          }
         $pdfObject->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $pdfObject->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

         if($grafiekPunt[$categorie][$datum])
         {
            $pdfObject->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $pdfObject->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {

     foreach($volgorde as $categorie)
     {
     $val=$data[$categorie];
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $pdfObject->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $pdfObject->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $pdfObject->SetXY($xval, $yval+($hval/2)-2);
            $pdfObject->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $pdfObject->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $pdfObject->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $pdfObject->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
  }
}


if(!function_exists('BarDiagram'))
{
	function BarDiagram($pdfObject, $w, $h, $data, $format, $colorArray, $titel)
	{
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		SetLegends2($pdfObject, $data, $format);
		$XPage = $pdfObject->GetX();
		$YPage = $pdfObject->GetY();
		$margin = 0;
		$nbDiv = 5;
		$legendWidth = 10;
		$YDiag = $YPage;
		$hDiag = floor($h);
		$XDiag = $XPage + $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if ($color == null)
		{
			$color = array(155, 155, 155);
		}
		if ($maxVal == 0)
		{
			$maxVal = max($data) * 1.1;
		}
		if ($minVal == 0)
		{
			$minVal = min($data) * 1.1;
		}
		if ($minVal > 0)
		{
			$minVal = 0;
		}
		$maxVal = ceil($maxVal / 10) * 10;

		$offset = $minVal;
		$valIndRepere = ceil(round(($maxVal - $minVal) / $nbDiv, 2) * 100) / 100;
		$bandBreedte = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$unit = $lDiag / $bandBreedte;
		$hBar = 5;//floor($hDiag / ($pdfObject->NbVal + 1));
		$hDiag = $hBar * ($pdfObject->NbVal + 1);

		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);
		$legendaStep = $unit;

		$legendaStep = $unit / $nbDiv * $bandBreedte;
		$valIndRepere = round($valIndRepere / $unit / 5) * 5;


		$pdfObject->SetLineWidth(0.2);
		$pdfObject->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->SetFillColor($color[0], $color[1], $color[2]);
		$nullijn = $XDiag - ($offset * $unit);

		$i = 0;
		$nbDiv = 10;

		$pdfObject->SetFont($pdfObject->rapport_font, '', 5);
		if (round($legendaStep, 5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for ($x = $nullijn; $x > $XDiag; $x = $x - $legendaStep)
			{
				$pdfObject->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$pdfObject->setXY($x, $YDiag + $hDiag);
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit, 2), 0, 0, 'C');
				$i++;
				if ($i > 100)
				{
					break;
				}
			}

			$i = 0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for ($x = $nullijn; $x < ($XDiag + $lDiag); $x = $x + $legendaStep)
			{
				$pdfObject->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$pdfObject->setXY($x, $YDiag + $hDiag);
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit, 2), 0, 0, 'C');

				$i++;
				if ($i > 100)
				{
					break;
				}
			}
		}
		$pdfObject->SetFont($pdfObject->rapport_font, 'B', $pdfObject->rapport_fontsize);
		$i = 0;

		$pdfObject->SetXY($XDiag, $YDiag);
		$pdfObject->Cell($lDiag, $hval - 4, $titel, 0, 0, 'C');
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize - 2);


		foreach ($data as $key => $val)
		{
			$pdfObject->SetFillColor($colorArray[$key][0], $colorArray[$key][1], $colorArray[$key][2]);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$pdfObject->Rect($xval, $yval, $lval, $hval, 'DF');
			$pdfObject->SetXY($XPage, $yval);
			$pdfObject->Cell($legendWidth, $hval, $pdfObject->legends[$i], 0, 0, 'R');
			$i++;
		}

		//Scales
		$minPos = ($minVal * $unit);
		$maxPos = ($maxVal * $unit);

		$unit = ($maxPos - $minPos) / $nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
	}
}
  if(!function_exists('formatgetal'))
  {
    function formatGetal($waarde, $dec)
	  {
	  	return number_format($waarde,$dec,",",".");
	  }
  }
  
  if(!function_exists('SetLegends2'))
  {
    function SetLegends2($pdfObject,$data, $format)
    {
      $pdfObject->legends=array();
      $pdfObject->wLegend=0;
      $pdfObject->sum=array_sum($data);
      $pdfObject->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
          $pdfObject->legends[]=$legend;
          $pdfObject->wLegend=max($pdfObject->GetStringWidth($legend),$pdfObject->wLegend);
      }
    }
  }
  
  
  function bepaaldFondsWaardenVerdiept_L51($portefeuille,$einddatum,$pdf,$datumVanaf='')
  {
    $verdiept = new portefeuilleVerdiept($pdf,$portefeuille,$einddatum);
    $verdiepteFondsen = $verdiept->getFondsen();
    foreach ($verdiepteFondsen as $fonds)
      $verdiept->bepaalVerdeling($fonds,$verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum,'',$datumVanaf);
    
    $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,(substr($einddatum, 5, 5) == '01-01')?true:false,'EUR',$datumVanaf);
    //listarray($verdiept->FondsPortefeuilleData);exit;
    // listarray($fondswaarden);
    $correctieVelden=array('totaalAantal','actuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
    foreach($fondswaarden as $i=>$fondsData)
    {
      //
      if(isset($pdf->fondsPortefeuille[$fondsData['fonds']]))
      {
        //echo $fondsData['fonds'];ob_flush();exit;
        $fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
        $fondsWaardeHuis=$pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
        $aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
       // 		echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis <br>\n";exit;
        unset($fondswaarden[$i]);
        foreach($pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
        {
          foreach ($details as $element => $emementDetail)
          {
            
            if(isset($emementDetail['overige']))
            {
              foreach($correctieVelden as $veld)
              {
                if($veld=='actuelePortefeuilleWaardeEuro' && !isset($emementDetail['overige'][$veld]))
                  $veld='ActuelePortefeuilleWaardeEuro';
                
                $emementDetail['overige'][$veld] = $emementDetail['overige'][$veld] * $aandeel;
                //  echo "$element $veld ".$emementDetail['overige'][$veld]."<br>\n";
              }
              
              //	$emementDetail['overige']['beginPortefeuilleWaardeInValuta']=$emementDetail['overige']['totaalAantal']*$emementDetail['overige']['beginwaardeLopendeJaar'];
              //	$emementDetail['overige']['beginPortefeuilleWaardeEuro']=$emementDetail['overige']['beginPortefeuilleWaardeInValuta']*$emementDetail['overige']['beginwaardeValutaLopendeJaar'];
              
              // unset($emementDetail['overige']['actuelePortefeuilleWaardeEuro']);
              // $emementDetail['overige']['actuelePortefeuilleWaardeInValuta']=$emementDetail['overige']['totaalAantal']*$emementDetail['overige']['actueleFonds']*$emementDetail['overige']['fondsEenheid'];
              //if(isset($emementDetail['overige']['ActuelePortefeuilleWaardeEuro ']))
              // $emementDetail['overige']['ActuelePortefeuilleWaardeEuro']=$emementDetail['overige']['ActuelePortefeuilleWaardeEuro']*$emementDetail['overige']['actueleValuta'];
              //listarray($emementDetail);
              //'historischeWaarde',
              
              unset($emementDetail['overige']['WaardeEuro']);
              unset($emementDetail['overige']['koersLeeftijd']);
              unset($emementDetail['overige']['FondsOmschrijving']);
              unset($emementDetail['overige']['Fonds']);
              if(!isset($emementDetail['overige']['historischeRapportageValutakoers']))
                $emementDetail['overige']['historischeRapportageValutakoers']=1;
              
              $fondswaarden[] = $emementDetail['overige'];
            }
          }
        }
      }
    }
    $fondswaarden  = array_values($fondswaarden);// listarray($fondswaarden);exit;
    $tmp=array();
    $conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
    foreach($fondswaarden as $mixedInstrument)
    {
      $instrument=array();
      foreach($mixedInstrument as $index=>$value)
      {
        if(isset($conversies[$index]))
          $instrument[$conversies[$index]] = $value;
        else
          $instrument[$index] = $value;
      }
      unset($instrument['voorgaandejarenactief']);
      
      $key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
      if(isset($tmp[$key]))
      {
        foreach($correctieVelden as $veld)
        {
          //$veld=($veld);
          $tmp[$key][$veld] += $instrument[$veld];
        }
      }
      else
        $tmp[$key]=$instrument;
      //	listarray($instrument);
    }
    $fondswaarden  = array_values($tmp);
    //echo $portefeuille,$einddatum;listarray($fondswaarden);
    
    return $fondswaarden;
  }



?>