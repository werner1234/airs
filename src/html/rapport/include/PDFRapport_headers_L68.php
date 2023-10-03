<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/10/30 16:44:17 $
 		File Versie					: $Revision: 1.34 $

 		$Log: PDFRapport_headers_L68.php,v $
 		Revision 1.34  2019/10/30 16:44:17  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2019/06/23 11:25:08  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2019/06/22 16:32:52  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2019/06/09 14:52:19  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2019/05/30 05:54:30  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2019/05/25 16:22:07  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/05/11 16:48:39  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2019/05/01 15:53:25  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/12/22 16:15:52  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2018/12/08 18:28:30  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2018/11/24 19:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2018/11/21 16:48:32  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2018/10/31 17:23:34  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2018/08/04 11:54:53  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2018/06/27 16:13:50  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2018/06/20 16:40:16  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2018/06/13 15:27:10  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2017/07/01 11:16:18  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2017/06/18 09:18:24  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2017/02/25 18:02:28  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/01/08 10:46:31  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/12/17 16:33:26  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/11/05 17:51:44  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/09/18 08:49:02  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/06/19 15:22:08  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/06/12 10:27:20  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/05/29 13:47:41  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/05/29 13:26:30  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/05/21 19:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/05/08 19:24:24  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/05/04 16:08:25  rvv
 		*** empty log message ***
 		
 	
*/

function Header_basis_L68($object)
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetDrawColor($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);


		if(isset($pdfObject->__appvar['consolidatie']))
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
		   $y=379*$factor;//$y=849*$factor;
       $xStart=(297)-($x)-$pdfObject->marge;
		   $pdfObject->Image($pdfObject->rapport_logo, $xStart, 5, $x, $y);
		}


		$pdfObject->SetXY($pdfObject->marge,5);
	  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		
    $pdfObject->SetXY(297/2-60,4);
    
    $headerTypen[1]=array('VHO','PERFG','VAR','GRAFIEK','VOLK');
    $headerTypen[2]=array('OIB','KERNZ','KERNV','HSE','DOORKIJK','DOORKIJKVR');
    $headerTypen[3]=array('HUIS','PERF','TRANS','MUT','PERFD');
    $headerTypen[4]=array('leeg');
  
   
    if(date("Y",$pdfObject->rapport_datum)==date("Y",$pdfObject->rapport_datumvanaf))
      $headerTypen[4][]='ATT';
    else
      $headerTypen[3][]='ATT';

    $vanTxt=vertaalTekst("van",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf);
    $rapDatumTxt=date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
    $totTxt=vertaalTekst("t/m",$pdfObject->rapport_taal)." ".$rapDatumTxt;
    $perTxt=vertaalTekst("per",$pdfObject->rapport_taal)." ".$rapDatumTxt;
      $tekst='';
    if(in_array($pdfObject->rapport_type,$headerTypen[1]))
      $tekst=vertaalTekst("Rapportage",$pdfObject->rapport_taal)." ".$totTxt;
    elseif(in_array($pdfObject->rapport_type,$headerTypen[2]))
      $tekst=vertaalTekst("Rapportage",$pdfObject->rapport_taal)." ".$perTxt;
    elseif(in_array($pdfObject->rapport_type,$headerTypen[3]))
      $tekst=vertaalTekst("Rapportage",$pdfObject->rapport_taal)." ".$vanTxt." ".$totTxt;
    elseif(in_array($pdfObject->rapport_type,$headerTypen[4]))
    {
      $vanTxt=vertaalTekst("van",$pdfObject->rapport_taal)." 1 ".vertaalTekst($pdfObject->__appvar["Maanden"][1],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf);
      $tekst = vertaalTekst("Rapportage", $pdfObject->rapport_taal) . " " . $vanTxt . " " . $totTxt;
    }
 
    $pdfObject->MultiCell(120,4,$tekst,0,'C');
  
    //  $pdfObject->MultiCell(80,4,vertaalTekst("Rapportage t/m",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
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
    $pdfObject->SetY(20);
	  $pdfObject->SetX($pdfObject->marge);
      $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize+2);
      $pdfObject->MultiCell(150,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
		$pdfObject->SetY(26);
	 	$pdfObject->headerStart = $pdfObject->getY()+13;
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    
  }
  $pdfObject->lastPortefeuille=$pdfObject->rapport_portefeuille;
    
}

	function HeaderVKM_L68($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
function HeaderFRONT_L68($object)
{
$pdfObject = &$object;
}

function HeaderINHOUD_L68($object)
{
  $pdfObject = &$object;
}

function HeaderAFM_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  
  
  // achtergrond kleur
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 12 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle.'u',$pdfObject->rapport_fontsize);
  
  $pdfObject->SetX(90);
  $pdfObject->Cell(100,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,"",$pdfObject->rapport_fontsize);
  
  $pdfObject->row(array(vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Effect",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Portefeuille\nin ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
                    vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
                    vertaalTekst("Standaarddeviatie",$pdfObject->rapport_taal),
                    vertaalTekst("Ongewogen correlatie",$pdfObject->rapport_taal),
                    vertaalTekst("Ongewogen correlatie binnen categorie",$pdfObject->rapport_taal)
                  ));
  
  $pdfObject->ln(-8);
  $pdfObject->SetFont($pdfObject->rapport_font,"i",$pdfObject->rapport_fontsize);
  $pdfObject->row(array("",vertaalTekst("Categorie"."\n ",$pdfObject->rapport_taal)));
  
  
  
}

function HeaderDOORKIJK_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}
function HeaderDOORKIJKVR_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}
function HeaderZAK_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}
function HeaderVAR_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
}

function headerKERNZ_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

}

function headerKERNV_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->Ln();  
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

}

function HeaderVOLKD_L68($object)
{
	HeaderHSE_L68($object);
}

function HeaderVOLK_L68($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();

  $headerData = array("".vertaalTekst("Categorie",$pdfObject->rapport_taal).
    "\n   ".vertaalTekst("Fonds",$pdfObject->rapport_taal),'',
    "\n".vertaalTekst("ISIN-code",$pdfObject->rapport_taal),
    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
    "\n".vertaalTekst("Koersdatum",$pdfObject->rapport_taal),
    "".vertaalTekst("Waarde in EUR",$pdfObject->rapport_taal),
    "".vertaalTekst("Rend in %",$pdfObject->rapport_taal),
    "".vertaalTekst("Datum laatste transactie",$pdfObject->rapport_taal)
  );

  if(isset($pdfObject->portefeuilles) && count($pdfObject->portefeuilles)>0)
  {
    $dataWidth = array(27, 50, 29, 12, 21, 21, 22, 22, 22, 22, 32);
    $headerData[] = "\n".vertaalTekst("Portefeuille",$pdfObject->rapport_taal);
  }
  else
  {
    $dataWidth=array(28,60,30,21,21,21,22,22,32,22);
  }
  $pdfObject->SetWidths($dataWidth);
  $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 10, 'F');
  $pdfObject->ln(1);
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  $pdfObject->Row($headerData);
  //$pdfObject->CellFontColor=$lastColors;
  //$pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
 // $pdfObject->SetLineWidth(0.1);
  $pdfObject->ln(1);
}

function HeaderHSE_L68($object)
{
	$pdfObject = &$object;
	$fillColorBackup=$pdfObject->FillColor;
	$fillCellBackup=$pdfObject->fillCell;
	$pdfObject->Ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  

  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  $pdfObject->SetDrawColor($pdfObject->rapport_paars[0],$pdfObject->rapport_paars[1],$pdfObject->rapport_paars[2]);

  //for($i=0;$i<count($pdfObject->widthA);$i++)
  //  $pdfObject->fillCell[] = 1;
      
  $y = $pdfObject->getY();
  $pdfObject->setY($y);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);
  


if($pdfObject->hoofdcategorie=='VAR')
{
	$pdfObject->widthA = array(0,65,23,15,40,38,20,22,26,22,10);
	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->row(array("", vertaalTekst("Instrument", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Aantal", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Valuta", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Rating", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Duration", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Effectief rendement", $pdfObject->rapport_taal),
										vertaalTekst("Koers", $pdfObject->rapport_taal) . "\n ",
										vertaalTekst("Waarde in EUR", $pdfObject->rapport_taal),
										vertaalTekst("Weging", $pdfObject->rapport_taal) . "\n ",
										" \n "));
}
else
{
	$pdfObject->widthA = array(0,65,23,15,40,55,3,22,26,22,10);
	$pdfObject->SetWidths($pdfObject->widthA);
$pdfObject->row(array("",vertaalTekst("Instrument",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Aantal",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Regio",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Sector",$pdfObject->rapport_taal)."\n ",
" "."\n ",
vertaalTekst("Koers",$pdfObject->rapport_taal)."\n ",
vertaalTekst("Waarde in EUR",$pdfObject->rapport_taal),
vertaalTekst("Weging",$pdfObject->rapport_taal)."\n ",
" \n "));
}

		$pdfObject->ln();
   	$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'],$pdfObject->rapport_fonds_fontcolor['g'],$pdfObject->rapport_fonds_fontcolor['b']);
    $pdfObject->fillCell=$fillCellBackup;

	}




function HeaderHUIS_L68($object)
{
    $pdfObject = &$object;
		
}
 
 
 function HeaderSCENARIO_L68($object)
{
    $pdfObject = &$object;
		
}

  function HeaderTRANS_L68($object)
  {
    $pdfObject=&$object;
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    //	$y = $pdfObject->GetY();
		//	$pdfObject->setY($y-8);
			$pdfObject->SetX(100);
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		/*
			$pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
*/
	//	$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$filLCellBackup=$pdfObject->fillCell;
		$pdfObject->fillCell=array();
		// afdrukken header groups
		$inkoop			= $pdfObject->marge + $pdfObject->widthB[0] + $pdfObject->widthB[1] + $pdfObject->widthB[2] + $pdfObject->widthB[3]+ $pdfObject->widthB[4];
		$inkoopEind = $inkoop + $pdfObject->widthB[5] + $pdfObject->widthB[6] ;

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $pdfObject->widthB[7] + $pdfObject->widthB[8] ;

		$resultaat			= $verkoopEind;
		$resultaatEind = $pdfObject->marge + array_sum($pdfObject->widthB);

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell($pdfObject->widthB[5] + $pdfObject->widthB[6],4, vertaalTekst("Uitgaven",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell($pdfObject->widthB[7] + $pdfObject->widthB[8],4, vertaalTekst("Ontvangsten",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge);


			$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
												vertaalTekst("Soort\ntransactie",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Rekening",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Instrument",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
												"\n".vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
												"\n".vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal)));
	   	$pdfObject->SetWidths($pdfObject->widthA);
	   	$pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
	  	$pdfObject->fillCell=$filLCellBackup;
  }


  function HeaderMUT_L68($object)
  {
    $pdfObject=&$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
/*
			$pdfObject->SetX(100);
			$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');

*/
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
	//	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array('',
											"\n".vertaalTekst("Bankafschrift",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
											"\n". vertaalTekst("Rekening",$pdfObject->rapport_taal),
								 "",
											"\n".vertaalTekst("Uitgaven",$pdfObject->rapport_taal),
											"\n".vertaalTekst("Ontvangsten",$pdfObject->rapport_taal),
								 ""));

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  }


  function HeaderOIH_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIH();
	}

	function HeaderOIBS_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIBS();
	}

	function HeaderOIR_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIR();
	}
/*
	function HeaderHSE_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderHSE();
	}
*/
	function HeaderOIB_L68($object)
	{
  	$pdfObject = &$object;
	  // achtergrond kleur
		$pdfObject->ln();
    $regels=2;
    if(count($pdfObject->gebruiktePortefeuilles) < 6)
    {
      foreach ($pdfObject->gebruiktePortefeuilles as $portefeuille)
      {
        if ($pdfObject->clientVermogensbeheerder[$portefeuille])
        {
          $naam = $pdfObject->clientVermogensbeheerder[$portefeuille];
        }
        else
        {
          $naam = $portefeuille;
        }
        if ($pdfObject->GetStringWidth($naam) > 35)
        {
          $regels = 3;
        }
      }
    }
		
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	//	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2+1, $regels*4 , 'F');
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
  	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $lijn1 			= $pdfObject->widthB[0]+$pdfObject->widthB[1];
		$lijn1eind 	= $lijn1+$pdfObject->widthB[2] + $pdfObject->widthB[3] + $pdfObject->widthB[4] + $pdfObject->widthB[5];
  
    // lijntjes onder beginwaarde in het lopende jaar
    $lijn1 =65;
	  $lijn1eind = 125;
	  
    $pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
    

    if(is_array($pdfObject->portefeuilles))
    {
			if(count($pdfObject->gebruiktePortefeuilles) < 6)
			{
				$pdfObject->SetX($pdfObject->marge);
				$pdfObject->Cell(65, 4, vertaalTekst("Beleggingscategorie", $pdfObject->rapport_taal), 0, 0, "L");
				$pdfObject->Cell(35, 4, 'Totaal', 0, 0, "C");

				foreach ($pdfObject->gebruiktePortefeuilles as $portefeuille)
				{
					if ($pdfObject->clientVermogensbeheerder[$portefeuille])
					{
						$naam = $pdfObject->clientVermogensbeheerder[$portefeuille];
					}
					else
					{
						$naam = $portefeuille;
					}
					//$pdfObject->Cell(35, 4, $naam, 0, 0, "C");
          $x=$pdfObject->getX();
					$y=$pdfObject->getY();
          $pdfObject->MultiCell(35,4, $naam, 0, "C");
          $pdfObject->setXY($x+35,$y);
				}
				$pdfObject->Ln();
        if($regels==3)
          $pdfObject->Ln();
				$pdfObject->SetX($pdfObject->marge + 65);
				$pdfObject->Cell(20, 4, "Waarde", 0, 0, "C");
				$pdfObject->Cell(15, 4, "%", 0, 0, "C");
				foreach ($pdfObject->gebruiktePortefeuilles as $portefeuille)
				{
					$pdfObject->Cell(23, 4, "Waarde", 0, 0, "C");
					$pdfObject->Cell(14, 4, "%", 0, 0, "C");
				}


				$pdfObject->Ln();
			}
			else
			{
				$pdfObject->Ln();
				$pdfObject->SetX($pdfObject->marge + 65);
				for($i=0;$i<6;$i++)
				{
					$pdfObject->Cell(23, 4, "Waarde", 0, 0, "C");
					$pdfObject->Cell(14, 4, "%", 0, 0, "C");
				}
			}
    }
    else
    {
      $pdfObject->SetX($pdfObject->marge);
      $pdfObject->MultiCell(35,4, vertaalTekst("Waarden",$pdfObject->rapport_taal), 0, "C");
      $pdfObject->row(array(vertaalTekst("Beleggingscategorie",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));
  	}
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		if(count($pdfObject->portefeuilles) > 5)
	  	$pdfObject->Ln(10);
	}

	function HeaderOIV_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderOIV();
	}


	
	function HeaderPERFD_L68($object)
	{
    $pdfObject = &$object;
   	$object->SetFont($object->rapport_font,$pdfObject->rapport_kop_fontstyle,$object->rapport_kop_fontsize);
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->ln();
    if($pdfObject->doubleHeader==true)
    {
      $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 12, 'F');
      unset($pdfObject->doubleHeader);
      //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+16,297-$pdfObject->marge*2,$pdfObject->GetY()+16);
    
    }
    else
    {
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
     // $pdfObject->Line($pdfObject->marge,$pdfObject->GetY()+12,297-$pdfObject->marge*2,$pdfObject->GetY()+12);
    } 
    $pdfObject->SetDrawColor(0);  
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 
		$object->SetWidths($object->widthA);
		$object->SetAligns($object->alignA);

	}
	
	function HeaderVHO_L68($object)
	{
	  $pdfObject = &$object;
    HeaderPERFG_L68($pdfObject);
	}
	
	function HeaderGRAFIEK_L68($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->SetDrawColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
    $pdfObject->SetDrawColor(0);
    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln(8);
  	//  $pdfObject->HeaderGRAFIEK();
	}
	function HeaderCASH_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderCASH();
	}
	
	function HeaderCASHY_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->ln();
  	  $pdfObject->HeaderCASHY();
	}

	function HeaderMODEL_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderMODEL();
	}
	
	function HeaderSMV_L68($object)
	{
  	  $pdfObject = &$object;
  	  $pdfObject->HeaderSMV();
	}

	function HeaderRISK_L68($object)
	{
		$pdfObject = &$object;
		$pdfObject->Ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	}

	function HeaderPERF_L68($object)
	{
		$pdfObject = &$object;
		$pdfObject->Ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8, 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetDrawColor(0);  
	  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	  $object->SetWidths($object->widthA);
	  $object->SetAligns($object->alignA);
    
	}
	
  function HeaderATT_L68($object)
	{
    $pdfObject = &$object;
		$pdfObject->Ln();
    $w=282/11;
   
  	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

    $pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
   	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
//		$pdfObject->ln(-6);
//		$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
  //  $pdfObject->ln(2);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
   // $pdfObject->ln(1);
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Koersresultaten\n ",
		                      "Directe opbrengsten",
		                      "Kosten\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
		                      "Benchmark\n(maand)",
										      "Rendement\n(maand)",
		                      "Rendement\n(Cumulatief)"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}


function HeaderPERFG_L68($object)
{
	$pdfObject = &$object;
	$w=282/11;
	$pdfObject->ln();
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');

	$pdfObject->widthA = array($w,$w,$w,$w,$w,$w,$w,$w,$w,$w,$w);
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//	for($i=0;$i<count($pdfObject->widthA);$i++)
//		  $pdfObject->fillCell[] = 1;

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	//$pdfObject->ln(-6);
	//$pdfObject->Cell(297-2*$pdfObject->marge,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,1,'C');
	//$pdfObject->ln(2);
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	// $pdfObject->ln(1);
	$pdfObject->row(array("Periode\n ",
										"Beginvermogen\n ",
										"Stortingen en\nonttrekkingen",
										"Koersresultaten\n ",
										"Directe opbrengsten",
										"Kosten\n ",
										"Beleggings\nresultaat",
										"Eindvermogen\n ",
                    "Benchmark\n(Periode)",
										"Rendement\n(Periode)",
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


if(!function_exists('PieChart'))
{
	function PieChart($object, $w, $h, $data, $format, $colors = null)
	{
		$pdfObject = &$object;


		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
		$pdfObject->SetLegends($data, $format);

		$XPage = $pdfObject->GetX();
		$YPage = $pdfObject->GetY();
		$margin = 2;
		$hLegend = 2;
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
		$pdfObject->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach ($data as $val)
		{
			$angle = floor(($val * 360) / doubleval($pdfObject->sum));
			if ($angle != 0)
			{
				$angleEnd = $angleStart + $angle;
				$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
				$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360)
		{
			$pdfObject->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);

		$x1 = $XPage + $w + $radius * .5;
		$x2 = $x1 + $hLegend + $margin - 12;
		$y1 = $YDiag - ($radius) + $margin;

		for ($i = 0; $i < $pdfObject->NbVal; $i++)
		{
			$pdfObject->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
			$pdfObject->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
			$pdfObject->SetXY($x2, $y1);
			if(strpos($pdfObject->legends[$i],'||')>0)
      {
        $parts=explode("||",$pdfObject->legends[$i]);
        $pdfObject->Cell(0, $hLegend, $parts[1]);
      }
      else
			  $pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
			$y1 += $hLegend + $margin;
		}
	}
}