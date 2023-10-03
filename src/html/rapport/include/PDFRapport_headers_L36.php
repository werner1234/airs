<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/27 19:17:25 $
 		File Versie					: $Revision: 1.16 $

 		$Log: PDFRapport_headers_L36.php,v $
 		Revision 1.16  2019/11/27 19:17:25  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2019/11/27 15:55:39  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2017/12/06 16:50:06  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2017/11/08 17:12:56  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2017/08/02 18:23:27  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2017/05/06 17:29:53  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2015/08/30 11:44:35  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2014/09/14 15:15:29  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2014/03/29 16:22:37  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/03/19 16:39:09  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/03/01 14:01:38  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/04/21 15:38:14  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/04/16 17:56:27  rvv
 		*** empty log message ***

 		Revision 1.2  2012/04/08 08:14:05  rvv
 		*** empty log message ***

 		Revision 1.1  2012/03/25 13:27:46  rvv
 		*** empty log message ***


*/
function Header_basis_L36($object)
{
   $pdfObject = &$object;
   global $__appvar;

	 if ($pdfObject->rapport_type == "BRIEF")
   {
     $pdfObject->HeaderFACTUUR();
   }
   elseif ($pdfObject->rapport_type == "FACTUUR")
   {
     $pdfObject->HeaderFACTUUR();
   }
	 elseif ($pdfObject->rapport_type == "KERNZ")
	 {

	 }
   elseif ($pdfObject->rapport_type == "FRONT")
   {
  	  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  	  $hkop=210*0.21;
	    $hvoet=210*0.09;
	    $pdfObject->Rect(0,0,297*0.8,$hkop,'F','F',array($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']));
	    $pdfObject->Rect(0,$hkop,297*0.041,210-$hkop-$hvoet,'F','F',array($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']));
	    $pdfObject->Rect(297*0.8,0,297*0.2,$hkop,'F','F',array($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']));
      $startKleur=array(80,112,16);
	    $eindkleur=array(120,136,24);
	    $stappen=20;
	    foreach ($startKleur as $index=>$waarde)
	      $stap[$index]=($eindkleur[$index] - $waarde)/$stappen;
	    $yStart=210-$hvoet;
	    $yStap=$hvoet/$stappen;
	    for($i=0;$i<=$stappen;$i++)
	    {
	      $kleur=array(intval($startKleur[0]+($stap[0]*$i)),intval($startKleur[1]+($stap[1]*$i)),intval($startKleur[2]+($stap[2]*$i)));
        $pdfObject->Rect(0,$yStart+($yStap*$i),297*0.8,$yStart+($yStap*($i+1)),'F','F',$kleur);
	    }
	 		$front_image=$__appvar["basedir"]."/html/rapport/logo/front_".strtolower($pdfObject->portefeuilledata['Vermogensbeheerder']).".jpg";

  		if(is_file($front_image))
	    {
 		    $factor=0.235;
		    $xSize=240*$factor*1.05;
		    $ySize=706*$factor;
	      $pdfObject->Image($front_image, 297*0.8, $hkop, $xSize, $ySize);
		  }

		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
 		  $pdfObject->customPageNo = 1;
  		$pdfObject->rapportNewPage = $pdfObject->page;
    }
    else
    {

     $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  	  $hkop=210*0.10;
	    $hvoet=210*0.05;
	    $pdfObject->Rect(0,0,297*0.8,$hkop,'F','F',array($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']));
	   // $pdfObject->Rect(0,$hkop,297*0.041,210-$hkop-$hvoet,'F','F',array($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']));
	    $pdfObject->Rect(297*0.8,0,297*0.2,$hkop,'F','F',array($pdfObject->rapport_kop2_fontcolor['r'],$pdfObject->rapport_kop2_fontcolor['g'],$pdfObject->rapport_kop2_fontcolor['b']));
      $startKleur=array(80,112,16);
	    $eindkleur=array(120,136,24);
	    $stappen=20;
	    foreach ($startKleur as $index=>$waarde)
	      $stap[$index]=($eindkleur[$index] - $waarde)/$stappen;
	    $yStart=210-$hvoet;
	    $yStap=$hvoet/$stappen;
	    for($i=0;$i<=$stappen;$i++)
	    {
	      $kleur=array(intval($startKleur[0]+($stap[0]*$i)),intval($startKleur[1]+($stap[1]*$i)),intval($startKleur[2]+($stap[2]*$i)));
        $pdfObject->Rect(0,$yStart+($yStap*$i),297,$yStart+($yStap*($i+1)),'F','F',$kleur);
	    }

    	if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  	  	$pdfObject->customPageNo = 1;

  	  if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
  	  	$pdfObject->rapportNewPage = $pdfObject->page;

		$pdfObject->customPageNo++;
    $pdfObject->lastCustomPageNo=$pdfObject->customPageNo;

		$pdfObject->SetLineWidth($pdfObject->lineWidth);

		if(empty($pdfObject->top_marge))
			$pdfObject->top_marge = $pdfObject->marge;
		$pdfObject->SetY($pdfObject->top_marge);

		$pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY()-5;

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

		if($pdfObject->rapport_layout == 17 && $pdfObject->rapport_type == "OIBS2")
		  $pdfObject->rapport_koptext = $pdfObject->rapport_koptext_old;

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
		}

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

		$logopos = 125.5;//96

		//rapport_risicoklasse

		if(is_file($pdfObject->rapport_logo))
		{

 		    $factor=0.10;
		    $xSize=461*$factor;
		    $ySize=189*$factor;
	      $pdfObject->Image($pdfObject->rapport_logo, $logopos, 2, $xSize, $ySize);
		}

		if($pdfObject->rapport_type == "MOD" )
			$x = 60;
		else
			$x = 150;


    $pdfObject->SetY($y)+2;
		$widthsBackup=$pdfObject->widths;
		$pdfObject->SetWidths(array(30,10,200));
		$pdfObject->SetAligns(array('L','C','L'));
		$pdfObject->Row(array('Cliënt',':',$pdfObject->portefeuilledata['Naam']));// .' '. $pdfObject->rapport_naam2
	  $pdfObject->ln(1);
    $pdfObject->Row(array('Portefeuille',':',$pdfObject->portefeuilledata['Portefeuille']));
	  $pdfObject->ln(1);

	  $van=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf);
    $tot=date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);

    $pdfObject->Row(array('Rapportageperiode',':',$van." - ".$tot));
		$pdfObject->widths=$widthsBackup;

/*
		//$pdfObject->Line($pdfObject->marge,30,$x+140,30);


		$pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');

		$pdfObject->SetWidths(array(35,10,200));
		$pdfObject->SetAligns(array('L','C','L'));
		$pdfObject->SetXY($pdfObject->marge,32);

		$pdfObject->SetXY($x,32);
		$pdfObject->SetXY(50,32);
//	  $pdfObject->MultiCell($x+50,4,vertaalTekst(vertaalTekst("Verslagperiode:",$pdfObject->rapport_taal)." ".
//	  date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." - ".
//	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)),0,'C');
		$pdfObject->SetXY($x,$y);
	  $pdfObject->MultiCell(140,4,"\n\n".vertaalTekst(vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".
	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)),0,'R');
		 */
	  $pdfObject->SetXY(50,26);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
	  $pdfObject->MultiCell($x+50,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
	//	$pdfObject->headerStart = $pdfObject->getY()+4;
	//	$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
	//	 $pdfObject->Line($pdfObject->marge,$y+18,$x+140,$y+18);

    }
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    $pdfObject->SetXY($pdfObject->marge,33);
    $pdfObject->headerStart=50;


}

	function HeaderVKM_L36($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
	
  function HeaderFRONT_L36($object)
	{
	    $pdfObject = &$object;
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

function HeaderEND_L36($object)
{
	$pdfObject = &$object;
	//$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderKERNZ_L36($object)
{
	$pdfObject = &$object;
	//$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}
  
  function HeaderSCENARIO_L36($object)
	{
	    $pdfObject = &$object;
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  function HeaderRISK_L36($object)
	{
	    $pdfObject = &$object;
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
	function HeaderOIH_L36($object)
	{
	    $pdfObject = &$object;
	     $pdfObject->HeaderOIH();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderOIS_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderOIS();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderOIR_L36($object)
	{
	    $pdfObject = &$object;
	    //$pdfObject->HeaderOIR();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderHSE_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderHSE();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
 	function HeaderOIB_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	}
  
  function HeaderAFM_L36($object)
	{
	    $pdfObject = &$object;
      $pdfObject->HeaderOIB();
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	}
	 function HeaderOIV_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderOIV();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderPERF_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderPERF();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderPERFD_L36($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
	function HeaderVOLK_L36($object)
	{
	  $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);

		$huidige 			= $pdfObject->widthB[0]+$pdfObject->widthB[1]+$pdfObject->widthB[2];
		$eindhuidige 	= $huidige +$pdfObject->widthB[3]+$pdfObject->widthB[4]+$pdfObject->widthB[5];

		$actueel 			= $eindhuidige + $pdfObject->widthB[6];
		$eindactueel 	= $actueel + $pdfObject->widthB[7] + $pdfObject->widthB[8] + $pdfObject->widthB[9];

		$resultaat 		= $eindactueel + $pdfObject->widthB[10];
		$eindresultaat = $resultaat +  $pdfObject->widthB[11] +  $pdfObject->widthB[12] +  $pdfObject->widthB[13] +  $pdfObject->widthB[14];;

		// achtergrond kleur
		//$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
	//	$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
		//$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);

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
										vertaalTekst("Directe\nopbrengsten",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("Valuta-\nresultaat",$pdfObject->rapport_taal),
										vertaalTekst("in %",$pdfObject->rapport_taal))
										);



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

	function HeaderVOLKD_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderVOLKD();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderVHO_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderVHO();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderTRANS_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderTRANS();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderMUT_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderMUT();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderGRAFIEK_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderGRAFIEK();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderATT_L36($object)
  {
    $pdfObject = &$object;
     $pdfObject->HeaderATT();
  }
  
  function HeaderPERFG_L36($object)
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
  function HeaderCASH_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderCASH();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderCASHY_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderCASHY();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderMODEL_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderMODEL();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderSMV_L36($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->HeaderSMV();
	    //$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}





?>