<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/20 17:17:58 $
 		File Versie					: $Revision: 1.4 $

 		$Log: PDFRapport_headers_L88.php,v $
 		Revision 1.4  2020/04/20 17:17:58  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2020/03/28 15:46:18  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/03/25 16:44:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2020/03/21 12:35:10  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.8  2017/12/16 18:44:16  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2017/12/09 17:54:25  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2016/10/19 10:58:45  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2016/10/16 15:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/08/27 16:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/06/15 15:58:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2016/04/03 10:58:02  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2016/03/19 16:51:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2016/03/06 14:37:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/01/14 12:34:42  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/11/01 22:05:56  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/10/29 16:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/09/17 15:16:31  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/06/29 15:38:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/08/25 08:50:52  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/08/18 12:24:51  rvv
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

 function Header_basis_L88($object)
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
    
     if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
     {
       $pdfObject->rapportNewPage = $pdfObject->page;
    
     }
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
    

     if(is_file($pdfObject->rapport_logo))
     {
       //  $factor=0.045;
       //  $xSize=1240*$factor;
       //  $ySize=206*$factor;
       $factor=0.02;
       $xSize=1931*$factor;
       $ySize=701*$factor;
      
       $logopos=$pdfObject->w-$pdfObject->marge-$xSize;
       $pdfObject->Image($pdfObject->rapport_logo, $logopos, 4, $xSize, $ySize);
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
    
     $pdfObject->MultiCell(120,4,$pdfObject->rapport_koptext,0,'L');
     $pdfObject->SetY($y);

     $pdfObject->AutoPageBreak=false;
     $pdfObject->SetXY($pdfObject->marge,$pdfObject->h-12);
    
     $pdfObject->MultiCell(40,4,vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".
                             vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
     $pdfObject->SetXY($pdfObject->marge,$y);
    
     $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
     $pdfObject->ln(4);
     $pdfObject->SetX(0);
     $pdfObject->MultiCell(297,4,vertaalTekst("\n".$pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
    
     $pdfObject->SetY($y+13);
     $pdfObject->headerStart=$pdfObject->GetY()+15;
  
     $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
   }
  
   $pdfObject->AutoPageBreak=true;
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

  function HeaderNOTA_L88($object)
	{

	}

	function HeaderINHOUD_L88($object)
  {
  
  
  }

function HeaderFRONT_L88($object)
{


}
  
  function HeaderEND_L88($object)
  {
	  $pdfObject = &$object;
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 8 , 'F');
    $pdfObject->ln(12);
  }

function HeaderGRAFIEK_L88($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 8 , 'F');
}


function HeaderRISK_L88($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 8 , 'F');
}
	function HeaderVKM_L88($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L88($object)
{
	$pdfObject = &$object;
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-$pdfObject->marge*2, 8 , 'F');
  $pdfObject->ln(8);
}
function HeaderVKMD_L88($object)
{
	$pdfObject = &$object;
	$pdfObject->HeaderVKM();
}

function HeaderAFM_L88($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIB();
  //HeaderOIB_L88(&$object);
}

function HeaderMUT_L88($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
 		$pdfObject->SetX(100);
  	$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
	  $pdfObject->ln();
		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->row(array('','',vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Rekening",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Debet",$pdfObject->rapport_taal),
										 vertaalTekst("Credit",$pdfObject->rapport_taal),
										 ""));
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->ln();
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  function HeaderTRANS_L88($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

      $pdfObject->SetX(100);
      $pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
      $pdfObject->ln();
 
    

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
      $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

	}

function HeaderFACTUUR_L88($object)
{
	$pdfObject = &$object;

}

function HeaderVHO_L88($object)
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
                    "\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                  ));
  
  
  $pdfObject->setY($y);
  $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths($pdfObject->widthA);
  $pdfObject->SetAligns($pdfObject->alignA);
  $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->ln();
}

  function HeaderPERF_L88($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), $pdfObject->w-($pdfObject->marge*2), 8, 'F');

    $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  
  
    $pdfObject->ln(2);
    $pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
  
    $pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
  
    $pdfObject->ln(2);
  
    $pdfObject->SetWidths($pdfObject->widthB);
    $pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->row(array("",
                 "",
                 "",
                 "",
                 "",
                 ""));
  
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
  
 
	}
      function HeaderOIB_L88($object)
	  {
	    $pdfObject = &$object;
      $pdfObject->Ln();
      		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	  	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 6, 'F');
		  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	  //  $pdfObject->headerPERF();

	  }
 	function HeaderVOLKD_L88($object)
	{
    $pdfObject = &$object;
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
		$pdfObject->MultiCell($eindhuidige - $huidige - 5 ,4, '', 0, "C");
		$pdfObject->SetY($tmpY);
		$pdfObject->SetX($pdfObject->marge+$actueel);

		$pdfObject->MultiCell(90,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0, "C");
  	//$pdfObject->Line(($pdfObject->marge+$huidige+5),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
//		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
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
//		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
   function HeaderATT_L88($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,25);
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
    $sumWidth = array_sum($pdfObject->widthA);
    $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
	}

  
  function HeaderVOLK_L88($object)
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
    $pdfObject->Cell(65,4, vertaalTekst("Kostprijs",$pdfObject->rapport_taal), 0,0,"C");
    $pdfObject->SetX($pdfObject->marge+$actueel);
    $pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$resultaat);
    $pdfObject->Cell(60,4, vertaalTekst("",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetDrawColor(255,255,255);
    $pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY()+4,$pdfObject->marge + $eindhuidige,$pdfObject->GetY()+4);
    $pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY()+4,$pdfObject->marge + $eindactueel,$pdfObject->GetY()+4);
    $pdfObject->SetDrawColor(0,0,0);
  
    $pdfObject->SetWidths($pdfObject->widthB);
    $pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->setXY($pdfObject->marge,$y);
//    if($pdfObject->rapportageValuta=='EUR')
//      $teken='€';
//    else
    $teken=$pdfObject->rapportageValuta;
  
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
                      "\n".vertaalTekst("in %",$pdfObject->rapport_taal)
                    ));
  
  
    $pdfObject->setY($y);
    $pdfObject->SetFont($pdfObject->rapport_font,"bi",$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths($pdfObject->widthA);
    $pdfObject->SetAligns($pdfObject->alignA);
    $pdfObject->row(array(vertaalTekst("Categorie\n",$pdfObject->rapport_taal)));
    $pdfObject->SetWidths($pdfObject->widthB);
    $pdfObject->SetAligns($pdfObject->alignB);
    $pdfObject->ln();
  }


function HeaderHSE_L88($object)
{
	$pdfObject = &$object;
	$pdfObject->ln();
	$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

	$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2]+$pdfObject->widthB[3];
	$eindhuidige 	= $huidige +$pdfObject->widthB[4]+$pdfObject->widthB[5];

	$actueel 			= $eindhuidige + $pdfObject->widthB[6];
	$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

	$resultaat 		= $eindactueel + $pdfObject->widthB[10];
	$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13]+  $pdfObject->widthB[14] +  $pdfObject->widthB[15];

	// achtergrond kleur
	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


	// lijntjes onder beginwaarde in het lopende jaar
	$pdfObject->SetX($pdfObject->marge+$huidige);

	if(substr(jul2form($pdfObject->rapport_datumvanaf),0,5) == '01-01')
		$pdfObject->Cell($eindhuidige-$huidige,4, vertaalTekst("Beginwaarde in het lopende jaar",$pdfObject->rapport_taal), 0,0,"C");
	else
		$pdfObject->Cell($eindhuidige-$huidige,4, vertaalTekst("Beginwaarde rapportage periode",$pdfObject->rapport_taal), 0,0,"C");
	$pdfObject->SetX($pdfObject->marge+$actueel);
	$pdfObject->Cell($eindactueel-$actueel,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");
	$pdfObject->SetX($pdfObject->marge+$resultaat);
	$pdfObject->Cell($eindresultaat-$resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,1, "C");

	$pdfObject->Line(($pdfObject->marge+$huidige),$pdfObject->GetY(),$pdfObject->marge + $eindhuidige,$pdfObject->GetY());
	$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
	$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());


	$pdfObject->SetWidths($pdfObject->widthB);
	$pdfObject->SetAligns($pdfObject->alignB);

	$y = $pdfObject->getY();


	$pdfObject->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$pdfObject->rapport_taal),
										vertaalTekst("ISIN",$pdfObject->rapport_taal),
										vertaalTekst("Aantal",$pdfObject->rapport_taal),
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Per stuk in valuta",$pdfObject->rapport_taal),
										'',
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

}
  
  function HeaderOIH_L88($object)
	{
	  $pdfObject = &$object;
    $pdfObject->ln();
    $dataWidth=array(28,55,20,20,20,20,22,22,22,22,22);
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

  }
  
  function HeaderPERFG_L88($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,30,30,23,23,23,24,28,24,25);
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



if(!function_exists('PieChart_L88'))
{
  function PieChart_L88($pdfObject,$w,$h,$data, $format, $colors=null,$titel='',$legendaStart='')
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
      if ($angle != 0)
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


if(!function_exists('BarDiagram'))
{
	function BarDiagram($pdfObject, $w, $h, $data, $colorArray, $titel)
	{
		$pdfObject->sum = array_sum($data);
		$pdfObject->NbVal = count($data);
		$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize);
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
		$maxVal = ceil($maxVal * 10) / 10;

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
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit * 100, 2) . '%', 0, 0, 'C');
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
				$pdfObject->Cell(0.1, 5, round(($x - $nullijn) / $unit * 100, 2) . '%', 0, 0, 'C');

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
			$pdfObject->SetFillColor($colorArray[$key]['R']['value'], $colorArray[$key]['G']['value'], $colorArray[$key]['B']['value']);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$pdfObject->Rect($xval, $yval, $lval, $hval, 'DF');
			$pdfObject->SetXY($XPage, $yval);
			$pdfObject->Cell($legendWidth, $hval, $key, 0, 0, 'R');
			$i++;
		}

		//Scales
		$minPos = ($minVal * $unit);
		$maxPos = ($maxVal * $unit);

		$unit = ($maxPos - $minPos) / $nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


	}
}


?>