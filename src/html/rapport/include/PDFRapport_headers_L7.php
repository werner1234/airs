<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/20 12:14:24 $
 		File Versie					: $Revision: 1.30 $

 		$Log: PDFRapport_headers_L7.php,v $
 		Revision 1.30  2020/06/20 12:14:24  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2020/02/22 18:46:19  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2019/01/23 16:27:16  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2018/09/26 15:53:28  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/09/22 17:12:17  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.24  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/10/16 15:17:38  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/10/12 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/09/18 08:49:02  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/07/27 15:50:38  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/07/16 15:16:49  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/05/15 17:15:00  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2016/05/04 16:01:30  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2016/05/01 18:44:12  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2016/04/30 15:33:27  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2016/04/20 15:46:31  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2016/04/13 16:30:05  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/04/10 15:48:34  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/03/30 10:35:05  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/03/27 17:35:07  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/03/16 14:24:20  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/01/06 16:28:55  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2015/12/30 19:01:23  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2015/12/23 16:21:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2015/12/21 08:22:32  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2015/12/20 16:47:30  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/10/29 16:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/10/23 15:45:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/09/11 15:17:37  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/03/10 19:53:17  rvv
 		*** empty log message ***

 		Revision 1.1  2010/01/09 11:41:01  rvv
 		*** empty log message ***



*/
function Header_basis_L7($object)
{
 $pdfObject = &$object;


    if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      //$pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
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

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);
;
		if($pdfObject->__appvar['consolidatie'])
		{
		  if(!isset($_POST['anoniem']))
      {
        $db=new DB();
        $query="SELECT naam, naam1 FROM CRM_naw WHERE portefeuille='".$pdfObject->rapport_portefeuille."'";
        $db->SQL($query);
        $crmNaam=$db->lookupRecord();
        if($db->Records()>0)
        {
          $pdfObject->__appvar['consolidatie']['portefeuillenaam1']=$crmNaam['naam'];
          $pdfObject->__appvar['consolidatie']['portefeuillenaam2']=$crmNaam['naam1'];
        }
      }
  
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
		  //$pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 43, 15);
      $factor=0.02;
      $xLogo=840*$factor;
			$yLogo=837*$factor;
      
      if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		  {
		  	$logopos = 85;
	  	}
  		else
  		{
  			$logopos = 297-$xLogo-$pdfObject->marge;
  		}
    
		  $pdfObject->Image($pdfObject->rapport_logo, $logopos, 6, $xLogo, $yLogo);
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



		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

     wiltonLogo($pdfObject,0,202,true);


    if ($pdfObject->rapport_type <> "FRONT2")
    {
	    $pdfObject->SetXY($pdfObject->marge,$y+15);
	  	$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize+1);
  		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'L');
  		$pdfObject->SetFont($pdfObject->rapport_font,'bi',$pdfObject->rapport_fontsize);
  		$pdfObject->SetX($pdfObject->marge);
  		$pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel2,$pdfObject->rapport_taal),0,'L');
  		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

      $pdfObject->SetY($y);
      
      
      if ($pdfObject->rapport_type == "MUT2")
      {
        $pdfObject->MultiCell(250,4,$pdfObject->rapport_koptext,0,'L');
        $pdfObject->SetX($pdfObject->marge);
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		    $pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
		    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		    $pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
		    $pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
		    $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
		    $pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
		    $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
      }
      else
	      $pdfObject->MultiCell(250,4,$pdfObject->rapport_koptext.vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'L');
		  
    }
    else
    {
      /*
      $pdfObject->SetY($y);
      if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
	      $koptekst=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'].$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
      else 
			  $koptekst=$pdfObject->rapport_naam1. $pdfObject->rapport_naam2;

	    $pdfObject->MultiCell(250,4,$koptekst,0,'L');
      */
    }
    $pdfObject->SetY($y+17);
    $pdfObject->headerStart = $pdfObject->getY()+14;

			$pdfObject->SetDrawColor(61,82,101);
			$pdfObject->Line(6, 194, 297 - 6, 194);
			$pdfObject->SetDrawColor(0,0,0);
    }
}

	function HeaderVKM_L7($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
    function HeaderFRONT2_L7($object)
    {
      
    }

  function HeaderRISK_L7($object)
  {

  }

function HeaderKERNV_L7($object)
{
  
}
function HeaderKERNZ_L7($object)
{

}
	  function HeaderMUT_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->HeaderMUT();
    }


function HeaderATT_L7($object)
{
	$pdfObject = &$object;
	//$colW=280/11;
	$colW=25;
	$pdfObject->widthA = array($colW+2,$colW+1,$colW,$colW,$colW,$colW,$colW+2,$colW,$colW,$colW,$colW);//,23
	$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

	$pdfObject->SetWidths($pdfObject->widthA);
	$pdfObject->SetAligns($pdfObject->alignA);

	//for($i=0;$i<count($pdfObject->widthA);$i++)
	//  $pdfObject->fillCell[] = 1;
	$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);

	$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	$pdfObject->ln();
	$pdfObject->Cell(94,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
	$pdfObject->Cell(94,4, date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0,'C');
	$pdfObject->ln(1);

	$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
	$pdfObject->ln();
	$pdfObject->row(array("Maand\n ",
										"Beginvermogen\n ",
										"Stortingen en\nonttrekkingen",
										"Resultaat\n ",
										"Inkomsten\n ",
										"Kosten\n ",
										"Opgelopenrente\n ",
										"Beleggings\nresultaat",
										"Eindvermogen\n ",
										"Rendement\n ",
										"Rendement\ncumulatief"));
	$sumWidth = array_sum($pdfObject->widthA);
	$pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

}

function HeaderFISCAAL_L7($object)
{
	$pdfObject = &$object;
	$pdfObject->Ln(6);
	$pdfObject->HeaderFISCAAL();
}

    function HeaderMUT2_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->Ln(6);

		$pdfObject->setX(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]+ $pdfObject->widthB[2]));
		//$pdfObject->Line(($pdfObject->marge + $pdfObject->widthB[0]+ $pdfObject->widthB[1]),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
		$pdfObject->ln(1);
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 8 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

if($pdfObject->rapport_titel =='Mutatie-overzicht: Opbrengsten')
{
    $pdfObject->CellBorders=array(array('T'),array('T'),array('T'),array('T'),array('T'),array('T'));
    $pdfObject->row(array('','','','','',''));
    $pdfObject->ln(-3);
    $pdfObject->CellBorders=array(array('U'),array('U'),array('U'),array('U'),array('U'),array('U'));
		$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Bruto",$pdfObject->rapport_taal),
										 vertaalTekst("Kosten",$pdfObject->rapport_taal),
										 vertaalTekst("Belastingen",$pdfObject->rapport_taal),
										 vertaalTekst("Netto",$pdfObject->rapport_taal)));
}
else
{
  $pdfObject->CellBorders=array(array('T'),array('T'),array('T'));
  $pdfObject->row(array('','','','','',''));
  $pdfObject->ln(-3);
  $pdfObject->CellBorders=array(array('U'),array('U'),array('U'));
  if($pdfObject->rapport_titel =='Mutatie-overzicht: Stortingen en onttrekkingen')
  	$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Bedrag",$pdfObject->rapport_taal)));  
  else
 	$pdfObject->row(array(vertaalTekst("Boekdatum",$pdfObject->rapport_taal),
										 vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
										 vertaalTekst("Bruto",$pdfObject->rapport_taal))); 
}
   unset($pdfObject->CellBorders);
		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		//$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor[r],$pdfObject->rapport_fontcolor[g],$pdfObject->rapport_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->Ln();
    }  
    

	  function HeaderOIB_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->HeaderOIB();
    }

	  function HeaderHSE_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->HeaderHSE();
    }

function HeaderPERFD_L7($object)
{

}
    
	  function HeaderPERF_L7($object)
	  {
      $pdfObject = &$object;
  	  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
			$y = $pdfObject->GetY();
			$pdfObject->setY($y-8);
      //$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
      $pdfObject->Cell(100,4, '',0,0);
    	$pdfObject->Cell(100,4, date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
     	$pdfObject->SetWidths($pdfObject->widthA);
	  	$pdfObject->SetAligns($pdfObject->alignA);
      $pdfObject->setY($y);
      $pdfObject->Ln(2);
		  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
      $pdfObject->ln();
    }
    
	  function HeaderTRANS_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$y = $pdfObject->GetY();
			$pdfObject->setY($y-8);
			$pdfObject->SetX(110);
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,vertaalTekst("tot en met",$pdfObject->rapport_taal)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
			$pdfObject->Write(4,date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)." ");
			$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

			$pdfObject->setY($y);
			$pdfObject->ln(2);
			$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());
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

			$pdfObject->SetX($inkoop);
			$pdfObject->Cell(65,4, vertaalTekst("Uitgaven",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($verkoop);
			$pdfObject->Cell(65,4, vertaalTekst("Ontvangsten",$pdfObject->rapport_taal), 0,0, "C");
			$pdfObject->SetX($resultaat);
			$pdfObject->Cell(65,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
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
										 vertaalTekst("Soort\ntrans-actie",$pdfObject->rapport_taal),
										 vertaalTekst("Aantal",$pdfObject->rapport_taal),
										 vertaalTekst("Effect",$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Koers in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in valuta",$pdfObject->rapport_taal),
										 vertaalTekst("Waarde in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$pdfObject->rapport_taal),
										 vertaalTekst("Resultaat lopend jaar",$pdfObject->rapport_taal),
										 $procentTotaal));
		
  
      $pdfObject->ln(1);
    }
    
	  function HeaderVOLK_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->HeaderVOLK();
    }

	  function HeaderVHO_L7($object)
	  {
      $pdfObject = &$object;
      $pdfObject->ln(6);

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
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');

		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
	  $pdfObject->SetX($pdfObject->marge+$huidige);
		$pdfObject->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$pdfObject->rapport_taal), 0,0,"C");
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell(65,4, vertaalTekst("Actuele koers",$pdfObject->rapport_taal), 0,0, "C");

		$pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell(45,4, vertaalTekst("Rendement",$pdfObject->rapport_taal), 0,1, "C");

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
												vertaalTekst($aandeel,$pdfObject->rapport_taal),
												vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
												"",
												vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),''));
	
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
    }
    
	  function HeaderOIH_L7($object)
	  {

    $pdfObject = &$object;
   	$pdfObject->ln(10);
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		foreach ($pdfObject->widthB as $id=>$value)
		{
		  if($id < 3)
		    $actueel +=$value;
		  if($id < 7)
		    $eindactueel +=$value;
		  if($id < 8)
		    $resultaat  +=$value;
		  if($id < 11)
		    $eindresultaat  +=$value;
		  if($id < 12)
		    $risico  +=$value;
		  if($id < 13)
		    $eindrisico  +=$value;
		  $eind +=$value;
		}

		// achtergrond kleur
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);


		// lijntjes onder beginwaarde in het lopende jaar
		$pdfObject->SetX($pdfObject->marge+$actueel);
		$pdfObject->Cell($eindactueel-$actueel,4, vertaalTekst("Actuele waardes",$pdfObject->rapport_taal), 0,0, "C");
    $pdfObject->SetX($pdfObject->marge+$resultaat);
		$pdfObject->Cell($eindresultaat-$resultaat,4, vertaalTekst("Resultaat",$pdfObject->rapport_taal), 0,0, "C");
		$pdfObject->SetX($pdfObject->marge+$risico);
		$pdfObject->Cell($eindrisico-$risico,4, vertaalTekst("Risicoscore",$pdfObject->rapport_taal), 0,1, "C");

		$pdfObject->Line(($pdfObject->marge+$actueel),$pdfObject->GetY(),$pdfObject->marge + $eindactueel,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$resultaat),$pdfObject->GetY(),$pdfObject->marge + $eindresultaat,$pdfObject->GetY());
		$pdfObject->Line(($pdfObject->marge+$risico),$pdfObject->GetY(),$pdfObject->marge + $eindrisico,$pdfObject->GetY());


		$pdfObject->SetWidths($pdfObject->widthB);
		$pdfObject->SetAligns($pdfObject->alignB);

		$y = $pdfObject->getY();
 		$pdfObject->row(array("",vertaalTekst("Aantal",$pdfObject->rapport_taal),
										"\n".vertaalTekst("",$pdfObject->rapport_taal),
										vertaalTekst("Valuta",$pdfObject->rapport_taal),
										vertaalTekst("Koers",$pdfObject->rapport_taal),
										vertaalTekst("Portefeuille in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
										vertaalTekst("in % van vermogen",$pdfObject->rapport_taal),
										vertaalTekst("Effectief\nRendement",$pdfObject->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal)),
										"",
										vertaalTekst("Risicoscore",$pdfObject->rapport_taal)
										);


		$pdfObject->setY($y);
		$pdfObject->ln(8);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + $eind,$pdfObject->GetY());
		$pdfObject->ln();
	  }

	  function factuurKop($object,$vermData)
    {
      $pdfObject = &$object;
      $pdfObject->AutoPageBreak = false;
      $ystart=15;
      $factor=0.025*(922/840);
      $x=840*$factor;
      $y=837*$factor;
      if(file_exists($pdfObject->rapport_logo))
        $pdfObject->Image($pdfObject->rapport_logo, 15, $ystart, $x, $y);
  
  //function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
      $pdfObject->SetXY($pdfObject->w-35,280);
      $pdfObject->SetFont($pdfObject->rapport_font,'B',8);
      $pdfObject->SetTextColor(61,82,101);
      $pdfObject->Cell(18,4,'www.wilton',0,0,'R',0,'www.wilton.nl');
      $pdfObject->SetTextColor(201,134,89);
      $pdfObject->setX($pdfObject->getX()-2);
      $pdfObject->Cell(20,4,'.nl',0,0,'L',0,'www.wilton.nl');
      
      $pdfObject->SetXY(45,$ystart+8);
      $pdfObject->SetFont($pdfObject->rapport_font,'B',8);
      $pdfObject->SetTextColor(61,82,101);
      $pdfObject->Cell(18,4,'Wilton',0,0,'R');
      $pdfObject->SetTextColor(201,134,89);
      $pdfObject->SetFont($pdfObject->rapport_font,'',8);
      $pdfObject->Cell(20,4,'Family Office',0,0,'L');
      $pdfObject->SetTextColor(61,82,101);
  
      $pdfObject->setX($pdfObject->marge);
      $pdfObject->setWidths(array(55,25,25,4,22,45));
  
      $pdfObject->SetFont($pdfObject->rapport_font,'B',7);
      $pdfObject->row(array('','','','T','','Bank nr'));
      $pdfObject->row(array('','','','E','','KvK'));
      $pdfObject->row(array('','','','','','BTW nr'));
      $pdfObject->SetFont($pdfObject->rapport_font,'',7);
      $pdfObject->SetXY($pdfObject->marge,$ystart+8);
      $pdfObject->row(array('',$vermData['Adres'],'Postbus 4667','',$vermData['Telefoon'],'                  NL93ABNA0478307535'));
      $pdfObject->row(array('',$vermData['Woonplaats'],'4803 ER Breda','',$vermData['Email'],'          Breda 20125148'));
      $pdfObject->row(array('','The Netherlands','The Netherlands','','','                 NL 815936102B01'));
      $pdfObject->AutoPageBreak = true;
      /*
      $woorden=array(array('tekst'=>'ADRES','style'=>'b'),array('tekst'=>$vermData['Adres'].', '.$vermData['Woonplaats'],'style'=>''),
        array('tekst'=>'TEL.','style'=>'b'),array('tekst'=>$vermData['Telefoon'],'style'=>''),
        array('tekst'=>'KVK','style'=>'b'),array('tekst'=>'5567.5107','style'=>''),
        array('tekst'=>'IBAN','style'=>'b'),array('tekst'=>'NL13.ABNA.040.26.37.917','style'=>''),
        array('tekst'=>'BTW NR','style'=>'b'),array('tekst'=>'851813264.B01','style'=>''));
      $this->pdf->SetTextColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
      foreach($woorden as $woordData)
      {
        if($woordData['style']=='b')
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', 9);
        else
          $this->pdf->SetFont($this->pdf->rapport_font, '', 9);
        $w=$this->pdf->GetStringWidth($woordData['tekst'].' ');
        $this->pdf->cell($w,5,$woordData['tekst'].' ',0,0,'L');
      }
      */
      
    }
	  
function wiltonLogo($object,$x,$y,$addVoet=false)
{
    $pdfObject = &$object;
    $pdfObject->AutoPageBreak=false;
    $pdfObject->SetXY($x,$y);
    $pdfObject->SetFont($pdfObject->rapport_font,'B',12);
    $pdfObject->SetTextColor(61,82,101);
    $pdfObject->Cell(20,4,'Wilton',0,0,'R');
    $pdfObject->SetTextColor(201,134,89);
    $pdfObject->SetFont($pdfObject->rapport_font,'',12);
    $pdfObject->Cell(20,4,'Family Office',0,0,'L');
    $pdfObject->SetTextColor(61,82,101);
    
    if($addVoet==true)
    {
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize-1);
      $pdfObject->SetXY(5,-14);
      $pdfObject->MultiCell(240,4,vertaalTekst('Aan deze opgave kunnen geen rechten worden ontleend.',$pdfObject->rapport_taal),'0','L');
      $pdfObject->SetXY(8,-14);
      $pdfObject->MultiCell(297-13,4,vertaalTekst("Productiedatum ",$pdfObject->rapport_taal).date("d-m-Y"),'0','R');

    }
    $pdfObject->AutoPageBreak=true;
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
		$kleurVertaling = array('Beleggingscategorie' => 'OIB', 'Valuta' => 'OIV', 'Regio' => 'OIR', 'Beleggingssector' => 'OIS', 'Hoofdcategorie' => 'OIB');
		$kleuren = $object->pdf->grafiekKleuren[$kleurVertaling[$type]];

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE " .
			" rapportageDatum = '" . $object->rapportageDatum . "' AND " .
			" portefeuille = '" . $object->portefeuille . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$portefwaarde = $DB->nextRecord();
		$portTotaal = $portefwaarde['totaal'];

		$query = "SELECT TijdelijkeRapportage.portefeuille, 
    TijdelijkeRapportage." . $type . "Omschrijving as Omschrijving, 
    TijdelijkeRapportage." . $type . " as type,
    SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel  " .
			" FROM TijdelijkeRapportage
  			WHERE (TijdelijkeRapportage.portefeuille = '" . $object->portefeuille . "') AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $object->rapportageDatum . "' $extraWhere"
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" GROUP BY " . $type . "  ORDER BY subtotaalactueel desc, TijdelijkeRapportage." . $type . "Volgorde";
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
				$typeData['port']['omschrijving'][$waarde] = $veldnaam;
				$typeData['grafiek'][$veldnaam] = $typeData['port']['procent'][$waarde] * 100;

				//if($veldnaam=='Overige' && isset($kleuren['Liquiditeiten']))
				//  $waarde='Liquiditeiten';

				$typeData['grafiekKleur'][] = array($kleuren[$waarde]['R']['value'], $kleuren[$waarde]['G']['value'], $kleuren[$waarde]['B']['value']);
			}
		}

		$object->pdf->grafiekData[$type] = $typeData;

	}
}

function printRendement_L7($pdfObject,$portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
{
	$object = &$pdfObject;

	global $__appvar;
	// vergelijk met begin Periode rapport.

	$DB= new DB();
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
		"FROM TijdelijkeRapportage WHERE ".
		" rapportageDatum ='".$rapportageDatumVanaf."' AND ".
		" portefeuille = '".$portefeuille."' ".
		$__appvar['TijdelijkeRapportageMaakUniek'];

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$vergelijkWaarde = $DB->nextRecord();
	$vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($object->rapportageValuta,$rapportageDatumVanaf);

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
		"FROM TijdelijkeRapportage WHERE ".
		" rapportageDatum ='".$rapportageDatum."' AND ".
		" portefeuille = '".$portefeuille."' ".
		$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$actueleWaardePortefeuille = $DB->nextRecord();
	$actueleWaardePortefeuille = $actueleWaardePortefeuille[totaal]  / $object->ValutaKoersEind;
  $storting=getStortingenKruis($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta,true);
	$onttrekking=getOnttrekkingenKruis($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$object->rapportageValuta,true);
	$resultaat = ($actueleWaardePortefeuille -
		$vergelijkWaarde -
		($storting['storting'] + $storting['kruispost']) +
		($onttrekking['onttrekking'] + $onttrekking['kruispost']) );

	//echo "kader: $resultaat = ".($actueleWaardePortefeuille-$vergelijkWaarde)." - ".($storting['storting'] + $storting['kruispost'])." + ".($onttrekking['onttrekking'] + $onttrekking['kruispost'])."  voor $beginDatum $eindDatum<br>\n";

	$performance = performanceMeting_L7($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $object->portefeuilledata['PerformanceBerekening'],$object->rapportageValuta,true);

	$object->ln(2);

	if($kort)
		$min = 8;

	if(($object->GetY() + 22 - $min) >= $object->pagebreak) {
		$object->AddPage();
		$object->ln();
	}

	$object->SetFillColor($object->rapport_kop_bgcolor[r],$object->rapport_kop_bgcolor[g],$object->rapport_kop_bgcolor[b]);
	//$object->SetX($object->marge + $object->widthB[0]);
	$object->Rect($object->marge,$object->getY(),110,(16-$min),'F');
	$object->SetFillColor(0);
	$object->Rect($object->marge,$object->getY(),110,(16-$min));
	$object->ln(2);
	//$object->SetX($object->marge);
	$object->SetX($object->marge);

	// kopfontcolor
	if(!$kort)
	{
		$object->SetTextColor($object->rapport_kop_fontcolor[r],$object->rapport_kop_fontcolor[g],$object->rapport_kop_fontcolor[b]);
		if ($object->rapport_resultaatText)
			$object->Cell(80,4, vertaalTekst($object->rapport_resultaatText,$object->rapport_taal), 0,0, "L");
		else
			$object->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$object->rapport_taal), 0,0, "L");
		$object->Cell(30,4, $object->formatGetal($resultaat,2), 0,1, "R");
		$object->ln();
	}
	$object->SetX($object->marge);
	if ($object->rapport_rendementText)
		$object->Cell(80,4, vertaalTekst($object->rapport_rendementText,$object->rapport_taal), 0,0, "L");
	else
		$object->Cell(80,4, vertaalTekst("Rendement lopende kalenderjaar",$object->rapport_taal), 0,0, "L");
	$object->Cell(30,4, $object->formatGetal($performance,2)."%", 0,1, "R");
	$object->ln(2);
}

if(!function_exists('printValutaoverzicht'))
{
	function printValutaoverzicht($object, $portefeuille, $rapportageDatum, $omkeren = false)
	{
		$pdfObject = &$object;
		global $__appvar;
		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta" .
			" FROM TijdelijkeRapportage, Valutas " .
			" WHERE TijdelijkeRapportage.portefeuille = '" . $portefeuille . "' AND " .
			" TijdelijkeRapportage.rapportageDatum = '" . $rapportageDatum . "' AND " .
			" TijdelijkeRapportage.valuta <> '" . $pdfObject->rapportageValuta . "' AND " .
			" TijdelijkeRapportage.valuta = Valutas.Valuta "
			. $__appvar['TijdelijkeRapportageMaakUniek'] .
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q, __FILE__, __LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if ($DB->records() > 0)
		{
			$pdfObject->ln();
			$pdfObject->ln();
			$t = 0;
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$t] = $valuta;
				$t++;
			}

			$regels = ceil((count($valutas)));
			if (count($valutas) > 4)
			{
				$regels = ceil((count($valutas) / 2));
			}
			$hoogte = ($regels * 4) + 4;
			if (($pdfObject->GetY() + $hoogte) > $pdfObject->pagebreak)
			{
				$pdfObject->AddPage();
				$pdfObject->ln();
			}

			$kop = "Actuele koersen";

			if ($pdfObject->rapport_layout == 1 || $pdfObject->rapport_layout == 17)
			{
				$kop = "Valuta koersen";
			}

			$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor[r], $pdfObject->rapport_kop4_fontcolor[g], $pdfObject->rapport_kop4_fontcolor[b]);
			$pdfObject->SetFont($pdfObject->rapport_kop4_font, $pdfObject->rapport_kop4_fontstyle, $pdfObject->rapport_fontsize - 1);
			$pdfObject->Cell(5, 4, '');
			$pdfObject->Cell($pdfObject->widthB[1], 4, vertaalTekst($kop, $pdfObject->rapport_taal), 0, 1, "L");

			$plusmarge = 5;

			$y = $pdfObject->getY();
			$start = false;
			//while ($valuta = $DB->NextRecord())
			for ($a = 0; $a < count($valutas); $a++)
			{
				if ($pdfObject->rapport_valutaoverzicht_rev)
				{
					if ($valutas[$a]['actueleValuta'] <> 0)
					{
						$valutas[$a]['actueleValuta'] = 1 / $valutas[$a]['actueleValuta'];
					}
				}

				if (count($valutas) > 4)
				{
					if ($a >= $regels && $start == false)
					{
						$y2 = $pdfObject->getY();
						$pdfObject->setY($y);
						$plusmarge = 65;
						$start = true;
					}
				}

				$pdfObject->SetX($pdfObject->marge + $plusmarge);
				$pdfObject->SetFont($pdfObject->rapport_font, $pdfObject->rapport_fontstyle, $pdfObject->rapport_fontsize - 2);
				$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor['r'], $pdfObject->rapport_fonds_fontcolor['g'], $pdfObject->rapport_fonds_fontcolor['b']);
				$pdfObject->Cell(35, 4, vertaalTekst($valutas[$a]['ValutaOmschrijving'], $pdfObject->rapport_taal), 0, 0, "L");
				$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'], $pdfObject->rapport_fontcolor['g'], $pdfObject->rapport_fontcolor['b']);
				$pdfObject->SetFont($pdfObject->rapport_font, '', $pdfObject->rapport_fontsize - 2);


				if ($pdfObject->ValutaKoersEind > 0)
				{
					$valutas[$a]['actueleValuta'] = $valutas[$a]['actueleValuta'] / $pdfObject->ValutaKoersEind;
				}

				if ($omkeren == true)
				{
					$pdfObject->Cell(20, 4, $pdfObject->formatGetal(1 / $valutas[$a]['actueleValuta'], 4), 0, 1, "R");
				}
				else
				{
					$pdfObject->Cell(20, 4, $pdfObject->formatGetal($valutas[$a]['actueleValuta'], 4), 0, 1, "R");
				}

			}

			if ($start == true)
			{
				$pdfObject->setY($y2);
			}
		}

	}
}


if(!function_exists('getOnttrekkingenKruis'))
{
	function getOnttrekkingenKruis($portefeuille, $van, $tot, $valuta = 'EUR', $kruispostOphalen = false)
	{
		if ($valuta != "EUR")
		{
			$koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $valuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

		$query = "SELECT " .
			"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet , " .
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit " .
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
			"WHERE " .
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
			"Rekeningen.Portefeuille = '" . $portefeuille . "' AND " .
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
			"Rekeningmutaties.Verwerkt = '1' AND " .
			"Rekeningmutaties.Boekdatum > '" . $van . "' AND " .
			"Rekeningmutaties.Boekdatum <= '" . $tot . "' AND " .
			"Rekeningmutaties.Grootboekrekening IN ";
		$DB = new DB();
		$DB->SQL($query . "(SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Onttrekking=1)");
		$DB->Query();
		$data = $DB->nextRecord();
		$onttrekking = 0;
		$kruispost = 0;
		$onttrekking = $data['subdebet'] - $data['subcredit'];
		if ($kruispostOphalen == true)
		{
			$DB->SQL($query . "('KRUIS')");
			$DB->Query();
			$data = $DB->nextRecord();
			$kruispost += $data['subdebet'];
		}

		return array('onttrekking' => $onttrekking, 'kruispost' => $kruispost);
	}
}


if(!function_exists('getStortingenKruis'))
{
	function getStortingenKruis($portefeuille, $van, $tot, $valuta = 'EUR', $kruispostOphalen = false)
	{
		if ($valuta != "EUR")
		{
			$koersQuery = " / (SELECT Koers FROM Valutakoersen WHERE Valuta='" . $valuta . "' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
		}
		else
		{
			$koersQuery = "";
		}

		$query = "SELECT " .
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit , " .
			"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet " .
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles " .
			"WHERE " .
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND " .
			"Rekeningen.Portefeuille = '" . $portefeuille . "' AND " .
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
			"Rekeningmutaties.Verwerkt = '1' AND " .
			"Rekeningmutaties.Boekdatum > '" . $van . "' AND " .
			"Rekeningmutaties.Boekdatum <= '" . $tot . "' AND " .
			"Rekeningmutaties.Grootboekrekening IN";
		$DB = new DB();
		$DB->SQL($query . " (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Storting=1)");
		$DB->Query();
		$data = $DB->nextRecord();
		$storting = 0;
		$kruispost = 0;
		$storting = $data['subcredit'] - $data['subdebet'];
		if ($kruispostOphalen == true)
		{
			$DB->SQL($query . "('KRUIS')");
			$DB->Query();
			$data = $DB->nextRecord();
			$kruispost += $data['subcredit'];
		}

		return array('storting' => $storting, 'kruispost' => $kruispost);
	}
}
 function performanceMeting_L7($portefeuille, $datumBegin, $datumEind, $type = "1", $valuta = 'EUR',$kruispost=false)
 {
	global $__appvar;
  $DB = new DB();
  $query="SELECT layout FROM Vermogensbeheerders JOIN Portefeuilles on Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $DB->SQL($query);
  $layout=$DB->lookupRecord();
  if(file_exists($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php"))
  {  
    include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php");
    $attObject="ATTberekening_L".$layout['layout'];
    $att=new $attObject();
    if(method_exists("ATTberekening_L".$layout['layout'],'getPerf'))
    {
      return $att->getPerf($portefeuille, $datumBegin, $datumEind);
    }
  }

	if($type == 6)//Attributie kwartaalwaardering
	{
	  $index=new rapportATTberekening($portefeuille);
	  $index->categorien[] = 'Totaal';
	  $performance = $index->attributiePerformance($portefeuille, $datumBegin, $datumEind,'all',$valuta,'kwartaal');
	  return $performance['Totaal'] -100;
	}
	elseif($type == 5)//Maandelijkse waardering realtime?
	{
	  $index=new indexHerberekening_L7();
    $indexData = $index->getWaardenATT($datumBegin, $datumEind,$portefeuille,'Totaal','maand',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}
	elseif($type == 3)//TWR
	{
	  $index=new indexHerberekening_L7();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','TWR',$kruispost);
		foreach ($indexData as $data)
		  $performance =  $data['index'] -100;
	  return $performance;
	}
	elseif($type == 4)//Maandelijkse waardering
	{
	  $index=new indexHerberekening_L7();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','maanden',$valuta,$kruispost); //listarray($indexData);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;

	}
	elseif($type == 7)//Dagelijkse YtD waardering
	{
	  $index=new indexHerberekening_L7();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','dagYTD',$kruispost);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;

	}
	elseif($type == 8)//Kwartaal waardering
	{
	  $index=new indexHerberekening_L7();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','kwartaal',$valuta,$kruispost);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}  

	if ($valuta != "EUR" )
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

  if(substr($datumBegin,0,4)==substr($datumEind,0,4) || ((substr($datumBegin,5,5)=='31-12') && substr($datumEind,5,5)=='01-01') )
  {
	// haal beginwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum = '".$datumBegin."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$beginwaarde = $DB->NextRecord();
	//echo $beginwaarde." = ".$beginwaarde[totaal]." / ".getValutaKoers($valuta,$datumBegin)."<br>";
	$beginwaarde = $beginwaarde['totaal'] / getValutaKoers($valuta,$datumBegin);

	// haal eindwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum ='".$datumEind."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query); 
	$DB->Query();
	$eindwaarde = $DB->NextRecord();
	$eindwaarde = $eindwaarde['totaal']  / getValutaKoers($valuta,$datumEind);

  if($kruispost==true)
    $kruispostGb=" OR Grootboekrekeningen.Kruispost=1";
  else
    $kruispostGb='';
      
	$query = "SELECT ".
	"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 $kruispostGb)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $beginwaarde + $weging['totaal1'];
  if($gemiddelde <> 0)
    $performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;
  }
  else
  {
    $index=new indexHerberekening_L7();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','jaar',$valuta,$kruispost);
    foreach($indexData as $index)
      $performance=$index['index']-100;
  }
//echo "gemiddelde $gemiddelde = $beginwaarde + ".$weging[totaal1]."\n<br>\n";
//echo "$datumBegin - $datumEind -> performance = $performance = ((($eindwaarde - $beginwaarde) - ".$weging[totaal2].") / $gemiddelde) * 100";flush();
//echo "<br>$performance<br>";
 return $performance;
 }


class indexHerberekening_L7
{
	function indexHerberekening_L7( $selectData )
	{
		$this->selectData = $selectData;
    $this->voorStartdatumNegeren=false;
	}

	function formatGetal($waarde, $dec=2)
	{
		return number_format($waarde,$dec,",",".");
	}

	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;


		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
	  $DB=new DB();

  	$query = "SELECT SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
 	  "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$beginDatum."')) ".
	  "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	  "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	  "FROM  (Rekeningen, Portefeuilles ) Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
  	"WHERE ".
  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
  	"Rekeningmutaties.Verwerkt = '1' AND ".
  	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
  	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
  	$DB->SQL($query);
  	$DB->Query();
  	$weging = $DB->NextRecord();
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
	  $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
  	$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
  	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	  $query = "SELECT SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) - SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($resultaatVerslagperiode+$kosten['totaalkosten'],2);
    $data['performance'] =$performance;
    return $data;

	}

	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR',$kruispost=false)
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();

		$query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
     $this->categorieVolgorde['LIQ']=0;
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;

    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
       if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
       return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));

      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }


	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();
  if($kruispost==true)
    $kruispostGb=" OR Grootboekrekeningen.Kruispost=1";
  else
    $kruispostGb='';
      
	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 $kruispostGb)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingenKruis($portefeuille,$beginDatum, $eindDatum,$valuta,$kruispost);
    $interneboeking     = $stortingen['kruispost'];
		$stortingen=$stortingen['storting']+$stortingen['kruispost'];
		$onttrekkingen = getOnttrekkingenKruis($portefeuille,$beginDatum, $eindDatum,$valuta,$kruispost);
		$interneboeking -= $onttrekkingen['kruispost'];
		$onttrekkingen=$onttrekkingen['onttrekking']+$onttrekkingen['kruispost'];
    
  

		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";

    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['interneboeking']=round($interneboeking,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    return $data;

	}


	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$kruispost=false)
	{
	  if(is_array($portefeuille))
	  {
	    $portefeuilles=$portefeuille[1];
	    $portefeuille=$portefeuille[0];
	  }
		$db=new DB();
    $julBegin = db2jul($datumBegin);
    $beginDatum=date("Y-m-d",$julBegin);
    $julEind = db2jul($datumEind);

   	$eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $datum == array();

  if($methode=='maanden')
  {
     $datum=$this->getMaanden($julBegin,$julEind);
     $type='m';
  }
  elseif($methode=='dagKwartaal')
  {
    $datum=$this->getDagen($julBegin,$julEind);
    $type='dk';
  }
  elseif($methode=='kwartaal')
  {
    $datum=$this->getKwartalen($julBegin,$julEind);
    $type='k';
  }
  elseif($methode=='jaar')
  {
    $datum=$this->getJaren($julBegin,$julEind);
    $type='j';
  }  
  elseif($methode=='TWR')
  {
    $datum=$this->getTWRstortingsdagen($portefeuille,$julBegin,$julEind);
    $type='t';
  }  
  elseif($methode=='dagYTD')
  {
     //$datum=$this->getDagen($julBegin,$julEind,'jaar');
     $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
     $type='dy';
  }
  elseif ($methode=='halveMaanden')
  {
    $datum=$this->getHalveMaanden($julBegin,$julEind);
    $type='2w';
  }
  elseif($methode=='weken')
  {
    $datum=$this->getWeken($julBegin,$julEind);
    $type='w';
  }
  elseif($methode=='dagen')
  {
    $datum=$this->getDagen2($julBegin,$julEind);
    $type='d';
  }
/*
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;
*/

	$i=1;
	$indexData['index']=100;
	$indexData['specifiekeIndex']=100;
	$kwartaalBegin=100;

	$huidigeIndex=$specifiekeIndex;
  $jsonOutput=array('label'=>$portefeuille,'data'=>array());
	foreach ($datum as $periode)
	{
	    if($specifiekeIndex != '')
	    {
	      //if($specifiekeIndex )
        /*
//	      $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
	      $db->SQL($query);
        $oldIndex=$db->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
        {
          $specifiekeIndex=$oldIndex['specifiekeIndex'];
          unset($startSpecifiekeIndexKoers);
        }
        else
        {
          if($huidigeIndex <> $specifiekeIndex)
            unset($startSpecifiekeIndexKoers);
          $specifiekeIndex=$huidigeIndex;
        }
        */
	      if(empty($startSpecifiekeIndexKoers))
	      {
	        $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
	        $db->SQL($query);
	        $specifiekeIndexData = $db->lookupRecord();
	        $startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
	      }
	      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
	      $db->SQL($query);
	      $specifiekeIndexData = $db->lookupRecord();
	      $specifiekeIndexKoers = $specifiekeIndexData['Koers'];
	    }
      $specifiekeIndexWaarden[$i] =($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;

	  	$query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";

	  	if(db2jul($periode['start']) == db2jul($periode['stop']))
	  	{

	  	}
	  	elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == ''))
	  	{
	  	  $dbData = $db->nextRecord();
	  	  $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	  $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	  $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	  $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	  $indexData['stortingen'] = $dbData['Stortingen'];
	  	  $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	      $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	  $indexData['kosten'] = $dbData['Kosten'];
	  	  $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	  $indexData['performance'] = $dbData['indexWaarde'];
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	    $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	    $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	    $indexData['rente'] = $dbData['rente'];
  	    $indexData['extra'] = unserialize($dbData['extra']);
	  	}
	  	else
	  	{
	  	  if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''))
	  	  {
	  	    $query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";

	  	    if($db->QRecords($query) > 0)
	  	    {
	  	    $dbData = $db->nextRecord();
	  	    $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	    $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	    $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
          $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	    $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	    $indexData['stortingen'] = $dbData['Stortingen'];
	  	    $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	        $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	    $indexData['kosten'] = $dbData['Kosten'];
	  	    $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	    $indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	      $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	      $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	      $indexData['rente'] = $dbData['rente'];
  	      $indexData['extra'] = unserialize($dbData['extra']);
  	      //listarray($indexData);
	    	  }
	    	  else
	  	      $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,'EUR',$kruispost));
	  	  }
        else
	  	    $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,$valuta,$kruispost));
	  	}

	  	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";
	  	if($methode=='dagKwartaal')
	  	{
	  	  if($periode['blok'] <> $lastBlok)
	  	    $kwartaalBegin=$indexData['index'];
	  	  $indexData['index'] = ($kwartaalBegin  * (100+$indexData['performance'])/100);
	  	  $lastBlok=$periode['blok'];
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	if($methode=='dagYTD')
	  	{
	  	  $indexData['index']=$indexData['performance']+100;
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	else
	  	{

        if(empty($specifiekeIndexWaarden[$i-1]))
	    	  $indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
	    	else
	    	  $indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
	      $indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;
	      if(empty($indexData['index']))
	        $indexData['index']=100;
	  	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	      $data[$i] = $indexData;
	  	}
      /*)
      if($output=='html')
      {
        
        $jsonOutput['data'][]=array(adodb_db2jul($data[$i]['datum'])*1000,$data[$i]['index']);
        //
        $dbData=mysql_real_escape_string(serialize($data[$i]));
        $query="INSERT INTO CRM_htmlData SET 
        portefeuille='$portefeuille',
        datum='".$data[$i]['datum']."',
        dataType='perf',
        data='".$dbData."',
        add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()";
        $db->SQL($query);
        $db->Query();
        //
        file_put_contents('../tmp/perf.json',json_encode($jsonOutput));
      }
      */

  $i++;
	}

	return $data;
	}

	function getWaardenATT($datumBegin,$datumEind,$portefeuille,$categorie='Totaal',$periodeBlok='maand',$valuta='EUR')
	{
	  $this->berekening = new rapportATTberekening($portefeuille);
	  if(is_array($categorie))
	    $this->berekening->categorien = $categorie;
	  else
      $this->berekening->categorien[] = $categorie;
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($portefeuille,$datumBegin,$datumEind,'rapportagePeriode',$valuta,$periodeBlok);

    foreach ($this->berekening->categorien as $categorie)
    {
      $indexData['index'] = 100;
      foreach ($this->berekening->performance as $periode=>$data)
      {
        if($periode != 'rapportagePeriode')
        {
    	  $indexData['periodeForm']    = jul2form(db2jul(substr($periode,0,10)))." - ".jul2form(db2jul(substr($periode,11)));
  	    $indexData['waardeMutatie']  = $data['totaalWaarde'][$categorie]['eind']-$data['totaalWaarde'][$categorie]['begin'];
        $indexData['waardeBegin']    = $data['totaalWaarde'][$categorie]['begin'];
	  	  $indexData['waardeHuidige']  = $data['totaalWaarde'][$categorie]['eind'];
	  	  $indexData['stortingen']     = $data['AttributieStortingenOntrekkingen'][$categorie]['stortingen'];
	  	  $indexData['onttrekkingen']  = $data['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'];
	  	  $indexData['resultaatVerslagperiode'] = $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	   	  $indexData['kosten']         = $data['totaal']['kosten'][$categorie];
	   	  $indexData['opbrengsten']    = $data['totaal']['opbrengsten'][$categorie];
	   	  $indexData['performance']    = $data['totaal']['performance'][$categorie];
	   	  $indexData['index']          = ($indexData['index']  * (100+$indexData['performance'])/100);
	   	  $indexData['datum']          = substr($periode,11);
	   	  if(count($this->berekening->categorien)>1)
	   	  $tmp[$categorie][] = $indexData;
	   	  else
	  	  $tmp[] = $indexData;
        }
      }
    }
	  return $tmp;
	}




	function Bereken()
	{
	  $einddatum = jul2sql($this->selectData[datumTm]);

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		$extraquery  .= " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		// selectie scherm.
		if($this->selectData[portefeuilleTm])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$this->selectData[portefeuilleTm]."') AND";
		if($this->selectData[vermogensbeheerderTm])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData[vermogensbeheerderTm]."') AND ";
		if($this->selectData[accountmanagerTm])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$this->selectData[accountmanagerTm]."') AND ";
		if($this->selectData[depotbankTm])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$this->selectData[depotbankTm]."') AND ";
		if($this->selectData[AFMprofielTm])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$this->selectData[AFMprofielTm]."') AND ";
		if($this->selectData[RisicoklasseTm])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$this->selectData[RisicoklasseTm]."') AND ";
		if($this->selectData[SoortOvereenkomstTm])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectData[SoortOvereenkomstTm]."') AND ";
		if($this->selectData[RemisierTm])
			$extraquery .= " (Portefeuilles.Remisier >= '".$this->selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$this->selectData[RemisierTm]."') AND ";
		if($this->selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$this->selectData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectData['clientTm']."') AND ";
		if (count($this->selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$this->selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";

		$DBs = new DB();
		$DBs->SQL($query);
		$DBs->Query();

		$DB2 = new DB();
		$records = $DBs->records();
		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


  	while($pdata = $DBs->nextRecord())
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }

	 	if($pdata['Vermogensbeheerder'] == 'WAT' || $pdata['Vermogensbeheerder'] == 'WAT1' || $pdata['Vermogensbeheerder'] == 'WWO')
    {
      $pdata['rapportageDatum']=jul2sql($this->selectData['datumTm']);
      $pdata['rapportageDatumVanaf']=jul2sql($this->selectData['datumVan']);
      $pdata['aanvullen']=$this->selectData['aanvullen'];
      $pdata['debug']=$this->selectData['debug'];
      $berekening = new rapportATTberekening($pdata);
      $berekening->pdata['pdf']=false;
      $berekening->indexSuperUser=$this->indexSuperUser;
      $berekening->Bereken();
      // listarray($berekening->performance);
      //  exit;
    }
    else
    {

      $pstartJul = db2jul($pdata['Startdatum']);
	    if($pstartJul > $this->selectData['datumVan'])
	      $julBegin= $pstartJul;
      else
        $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
      if($pdata['Vermogensbeheerder'] == 'SEQ')
      {
    	  $datum = $this->getKwartalen($julBegin,$julEind);
        $type='k';
    	}
      else
    	{
    	  $datum = $this->getMaanden($julBegin,$julEind);
        $type='m';
      }
      $portefeuille = $pdata['Portefeuille'];

		$indexAanwezig = array();
	  if ($this->selectData['aanvullen'] == 1)
	  {
	    $query = "SELECT Datum FROM HistorischePortefeuilleIndex WHERE Portefeuille = '$portefeuille' AND periode='$type' AND Categorie = 'Totaal' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['Datum'];
	    }
    }

    //rvv debug
    if($pdata['Vermogensbeheerder'] == "HEN" || $pdata['PerformanceBerekening'] == 7)
    {
      $datum=array();
      $newJul=$julBegin;
      $type='dy';
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y',$julBegin)."-01-01",'stop'=>date('Y-m-d',$newJul));
      }
    }
    //echo $portefeuille."<br>\n";
//listarray($datum);
			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     if($pdata['Vermogensbeheerder'] == "HEN")
			     {
             include_once("../classes/AE_cls_fpdf.php");
             include_once("rapport/PDFRapport.php");
             include_once("rapport/include/RapportPERF_L26.php");

			       $pdf = new PDFRapport('L','mm');
             $pdf->rapportageValuta = "EUR";
	           $pdf->ValutaKoersEind  = 1;
             $pdf->ValutaKoersStart = 1;
             $pdf->ValutaKoersBegin = 1;
             loadLayoutSettings($pdf, $portefeuille);
             if(substr($datum[$i]['start'],5,5)=='01-01')
               $startjaar=true;
             else
               $startjaar=false;
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['start'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$periode['start']);
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],$startjaar,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum[$i]['stop']);

             $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
             $pdf->HENIndex=true;
             $rapport = new RapportPERF_L26($pdf, $portefeuille, $datum[$i]['start'], $datum[$i]['stop']);
	           $rapport->writeRapport();

             foreach ($datum as $periode)
             {
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['start']);
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
             }
             $PerformanceMeting=$rapport->pdf->excelData;

             $performance= number_format($PerformanceMeting[0][37],4) ;
             $data['waardeHuidige']=$PerformanceMeting[0][29];
			       $data['waardeBegin']=$PerformanceMeting[0][28];
 		         $data['stortingen']=$PerformanceMeting[0][30];
			       $data['onttrekkingen']=0;
			       $data['opbrengsten']=$PerformanceMeting[0][32];
			       $data['kosten']=$PerformanceMeting[1][13];
			     }
			     else
			     {
             $data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
		         $performance = number_format($data['performance'],4) ;
			     }
           $senarioWaarden=$this->getScenario($portefeuille,$datum[$i]['stop'],$data['waardeHuidige']);
		    $query = "SELECT id FROM HistorischePortefeuilleIndex WHERE periode='$type' AND Portefeuille = '$portefeuille' AND Datum = '".substr($datum[$i]['stop'],0,10)."' ";
		    $DB2->SQL($query);
		    $DB2->Query();
		    $records = $DB2->records();
		    if($records > 1)
		    {
		      echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		    }
		    $qBody=	    " Portefeuille = '$portefeuille' ,
			                Categorie = 'Totaal',
			                PortefeuilleWaarde = '".round($data['waardeHuidige'],2)."' ,
			                PortefeuilleBeginWaarde = '".round($data['waardeBegin'],2)."' ,
 		                  Stortingen = '".round($data['stortingen'],2)."' ,
			                Onttrekkingen = '".round($data['onttrekkingen'],2)."' ,
			                Opbrengsten = '".round($data['opbrengsten'],2)."' ,
			                Kosten = '".round($data['kosten'],2)."' ,
			                Datum = '".$datum[$i]['stop']."',
			                IndexWaarde = '$performance' ,
                      periode='$type',
			                gerealiseerd = '".round($data['gerealiseerd'],2)."',
			                ongerealiseerd = '".round($data['ongerealiseerd'],2)."',
			                rente = '".round($data['rente'],2)."',
			                extra = '".addslashes(serialize($data['extra']))."',
			                gemiddelde = '".round($data['gemiddelde'],2)."',
			                ";
        if(count($senarioWaarden)>0)
        {
          $qBody.="scenarioKansOpDoel='".round($senarioWaarden['scenarioKansOpDoel'],2)."', 
                   scenarioVerwachtVermogen='".round($senarioWaarden['scenarioVerwachtVermogen'],2)."',
                   scenarioProfiel='".$senarioWaarden['scenarioProfiel']."',
                   ";
        }              

		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
		        $query = "UPDATE
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR'
			               WHERE id = $id ";
		    }
		    else
		    {
			    $query = "INSERT INTO
			                HistorischePortefeuilleIndex
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR',
			                add_date = NOW(),
			                add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	}
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
}

function getScenario($portefeuille,$datum,$huidigeWaarde)
{
  global $__appvar;
  $DB=new DB();
  $query="SELECT check_module_SCENARIO,Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders 
      JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 
      WHERE portefeuille='".$portefeuille."'";
 	$DB->SQL($query);
	$DB->Query();
	$check_module_SCENARIO = $DB->nextRecord(); 
  if($check_module_SCENARIO['check_module_SCENARIO']==0)
    return array();
    
  $query="SELECT id FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
	$DB->SQL($query);
	$DB->Query();
  $crmId = $DB->nextRecord();   
 
  $sc= new scenarioBerekening($crmId['id']);
  $sc->CRMdata['startvermogen']=$huidigeWaarde;
  $sc->CRMdata['startdatum']=$datum;
  if(!$sc->loadMatrix())
    $sc->createNewMatix(true);
  $sc->berekenSimulaties(0,10000);
  $scenarioKansOpDoel=$sc->berekenDoelKans();
  $sc->berekenVerdeling();
  if(isset($sc->verwachteWaarden['Normaal']))
    $scenarioVerwachtVermogen=$sc->verwachteWaarden['Normaal'];
  else
    $scenarioVerwachtVermogen=$sc->gemiddelde;  

  return array('scenarioKansOpDoel'=>$scenarioKansOpDoel,'scenarioVerwachtVermogen'=>$scenarioVerwachtVermogen,'scenarioProfiel'=>$sc->CRMdata['gewenstRisicoprofiel']);

}

	function BerekenScenarios()
	{
	  $einddatum = jul2sql($this->selectData[datumTm]);

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		$extraquery  .= " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		// selectie scherm.
		if($this->selectData[portefeuilleTm])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$this->selectData[portefeuilleTm]."') AND";
		if($this->selectData[vermogensbeheerderTm])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData[vermogensbeheerderTm]."') AND ";
		if($this->selectData[accountmanagerTm])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$this->selectData[accountmanagerTm]."') AND ";
		if($this->selectData[depotbankTm])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$this->selectData[depotbankTm]."') AND ";
		if($this->selectData[AFMprofielTm])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$this->selectData[AFMprofielTm]."') AND ";
		if($this->selectData[RisicoklasseTm])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$this->selectData[RisicoklasseTm]."') AND ";
		if($this->selectData[SoortOvereenkomstTm])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectData[SoortOvereenkomstTm]."') AND ";
		if($this->selectData[RemisierTm])
			$extraquery .= " (Portefeuilles.Remisier >= '".$this->selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$this->selectData[RemisierTm]."') AND ";
		if($this->selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$this->selectData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectData['clientTm']."') AND ";
		if (count($this->selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$this->selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";

		$DBs = new DB();
		$DBs->SQL($query);
		$DBs->Query();

		$DB2 = new DB();
		$records = $DBs->records();
		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


  	while($pdata = $DBs->nextRecord())
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }
      $pstartJul = db2jul($pdata['Startdatum']);
      $julBegin=$pstartJul;
	    //if($pstartJul > $this->selectData['datumVan'])
	    //  $julBegin= $pstartJul;
     // else
      //  $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
   	  $datum = $this->getMaanden($julBegin,$julEind);
      $portefeuille = $pdata['Portefeuille'];
  		$indexAanwezig = array();
      $query = "SELECT datum FROM HistorischeScenarios WHERE portefeuille = '$portefeuille' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['datum'];
	    }

			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     //$data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
         	$startjaar=substr($datum[$i]['start'],0,4);
		      if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		        $beginjaar = true;
		      else
	        	 $beginjaar = false;

		      $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],$beginjaar,$valuta,$datum[$i]['start']);
          $data['waardeHuidige']=0;
          foreach ($fondswaarden['eindmaand'] as $regel)
	        {
	          $data['waardeHuidige']+=$regel['actuelePortefeuilleWaardeEuro'];
          }
          $senarioWaarden=$this->getScenario($portefeuille,$datum[$i]['stop'],$data['waardeHuidige']);
  	 	    $query = "SELECT id FROM HistorischeScenarios WHERE portefeuille = '$portefeuille' AND datum = '".substr($datum[$i]['stop'],0,10)."' ";
	  	    $DB2->SQL($query);
		      $DB2->Query();
		      $records = $DB2->records();
		      if($records > 1)
		      {
		       echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		      }
          $qBody="portefeuille = '$portefeuille',
                  datum='".$datum[$i]['stop']."',
                   scenarioKansOpDoel='".round($senarioWaarden['scenarioKansOpDoel'],2)."', 
                   scenarioVerwachtVermogen='".round($senarioWaarden['scenarioVerwachtVermogen'],2)."',
                   scenarioProfiel='".$senarioWaarden['scenarioProfiel']."',
                   ";
    
		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
		        $query = "UPDATE
			                HistorischeScenarios
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR'
			               WHERE id = $id ";
		    }
		    else
		    {
			    $query = "INSERT INTO
			                HistorischeScenarios
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR',
			                add_date = NOW(),
			                add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
}

function getKwartalen($julBegin, $julEind)
{
   if($julBegin > $julEind )
     return array();
   $beginjaar = date("Y",$julBegin);
   $eindjaar = date("Y",$julEind);
   $maandenStap=3;
   $stap=1;
   $n=0;
   $teller=$julBegin;
   $kwartaalGrenzen=array();
   $datum=array();

   while ($teller < $julEind)
   {
     $teller = mktime (0,0,0,$stap,0,$beginjaar);
     $stap +=$maandenStap;
     if($teller > $julBegin && $teller < $julEind)
     {
     $grensDatum=date("d-m-Y",$teller);
     $kwartaalGrenzen[] = $teller;
     }
   }
   if(count($kwartaalGrenzen) > 0)
   {
     $datum[$n]['start']=date('Y-m-d',$julBegin);
     foreach ($kwartaalGrenzen as $grens)
     {
       $datum[$n]['stop']=date('Y-m-d',$grens);
       $n++;
       $start=date('Y-m-d',$grens);
       if(substr($start,-5)=='12-31')
        $start=(substr($start,0,4)+1).'-01-01';

       $datum[$n]['start']=$start;
     }
     $datum[$n]['stop']=date('Y-m-d',$julEind);
   }
   else
   {
     $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
   }
 	 return $datum;
}

function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getJaren($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,1,0,$beginjaar+$i);
	    $counterEnd   = mktime (0,0,0,1,0,$beginjaar+1+$i);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if(db2jul($datum[$i]['stop']) < db2jul($datum[$i]['start']))
         unset($datum[$i]);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getHalveMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $i=0;
	  $j=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$j,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$j+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $tusenCounter= mktime (0,0,0,$beginmaand+$j,15,$beginjaar);
	    if($tusenCounter > $counterEnd)
	    {
	      $datum[$i]['stop']=date('Y-m-d',$julEind);
        break;
	    }
      if($tusenCounter > $julBegin)
      {
	      $datum[$i]['stop']=date('Y-m-d',$tusenCounter);
	      $i++;
	      $datum[$i]['start']=date('Y-m-d',$tusenCounter);
      }
	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
        
        
      $i++;
      $j++;
	  }
	  return $datum;
}

  function getDagen2($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $einddag= date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $counterStart=$julBegin;
	  $i=0;
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
       $i++;
	  }
    return $datum;
  }
  
function getDagen($julBegin, $julEind,$periode='kwartaal')
{

  if($periode=='kwartaal')
    $blokken=$this->getKwartalen($julBegin, $julEind);
  elseif($periode=='maanden')
    $blokken=$this->getMaanden($julBegin, $julEind);
  elseif($periode=='jaar')
    $blokken=$this->getJaren($julBegin, $julEind);
  else
    $blokken=array('start'=>date("Y-m-d",$julBegin),'stop'=>date("Y-m-d",$julEind));

  foreach ($blokken as $blok=>$periode)
  {
    $julBegin=db2jul($periode['start']);
    $julEind=db2jul($periode['stop']);
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $einddag= date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $counterStart=$julBegin;
	  $i=0;
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd),'blok'=>$blok);
       $i++;
	  }
  }
  return $datum;
}

  function getTWRstortingsdagen($portefeuille,$julBegin, $julEind)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='$portefeuille'  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."' AND  Rekeningmutaties.Grootboekrekening IN('STORT','ONTTR')
    GROUP BY Rekeningmutaties.Boekdatum
    ORDER BY Boekdatum";

    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$i=0;
		$start =date('Y-m-d',$julBegin);
		$eind =date('Y-m-d',$julEind);
		$lastdatum=$start;
	  while($mutaties = $DB->nextRecord())
		{
		  if($lastdatum <> $mutaties['datum'])
		  {
		    $datum[$i]['start'] = $lastdatum;
		    $datum[$i]['stop']  =$mutaties['datum'];
		  }
		  $lastdatum=$mutaties['datum'];
		  $i++;
		}

		if($lastdatum <> $eind)
		{
		  $datum[$i]['start'] = $lastdatum;
		  $datum[$i]['stop']  =$eind;
		}
		return $datum;
  }

	function getWeken($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$i]['start'],5,5)=='12-31')
	        $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'] || db2jul($datum[$i]['start']) > db2jul($datum[$i]['stop']) )
	      unset($datum[$i]);
       $i=$i+7;
	  }

	  return $datum;
  }


}

?>