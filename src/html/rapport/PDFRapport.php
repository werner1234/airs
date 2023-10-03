<?php
/*
    AE-ICT sourcemodule created 07 okt. 2022
    Author              : Chris van Santen
    Filename            : PDFRapport.php


*/

include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
include_once($__appvar["basedir"].'/classes/fpdi/fpdi.php');
class PDFRapport extends FPDI
{
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;
  var $rowHeight = 4;

	var $tablewidths;
	var $marge;
	var $widths;
	var $aligns;

  function Header()
  {
    
    
    if (file_exists('rapport/include/PDFRapport_headers_L'.$this->rapport_layout.".php"))
		{ 
  	  include_once('rapport/include/PDFRapport_headers_L'.$this->rapport_layout.".php");
  	}
  	elseif (file_exists('rapport/include/layout_'.$this->rapport_layout.'/PDFRapport_headers_L'.$this->rapport_layout.".php"))
    {
      include_once('rapport/include/layout_'.$this->rapport_layout.'/PDFRapport_headers_L'.$this->rapport_layout.".php");
    }

	  $functionBaseString = 'Header_basis_L'.$this->rapport_layout;
 	  $functionString = 'Header'.$this->rapport_type.'_L'.$this->rapport_layout;
  //echo "$functionBaseString $functionString <br>\n";
  if(function_exists($functionBaseString)  && function_exists($functionString))
  {
    $functionBaseString($this);
	  $functionString($this);
	}
	elseif($this->fondsRapport == true)
	{
	  	 switch($this->rapport_type)
	  	{
	  	  case "MODEL":
			    $this->HeaderMODEL();
			  break;
	  	}

	}
  else
  {

    if ($this->rapport_type == "BRIEF")
    {
      $this->HeaderFACTUUR();
    }
    elseif ($this->rapport_type == "FACTUUR")
    {
      $this->HeaderFACTUUR();
    }
    elseif ($this->rapport_type == "ORDERP" || $this->rapport_type == "ORDERL")
    {
      $this->SetFont('arial','B',10);

			if($this->rapport_type == 'ORDERP')
				$width=210;
			else
				$width=297;

			$query="SELECT Adres,Woonplaats FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'";
			$db=new DB();
			$db->SQL($query);
			$verm=$db->lookupRecord();

      if($this->page==1)
      $this->MultiCell($width-2*$this->marge,5,$this->portefeuilledata['VermogensbeheerderNaam'].
																	    "\n".$verm['Adres'].
																			"\n".$verm['Woonplaats'].
                                      "\n T:".$this->portefeuilledata['VermogensbeheerderTelefoon'].
                                      "\n E:".$this->portefeuilledata['VermogensbeheerderEmail'],0,'R');
     else
        $this->MultiCell($width-2*$this->marge,5,$this->portefeuilledata['VermogensbeheerderNaam'],0,'R');
      $this->SetY(25);
    }
    elseif ($this->rapport_type == "FRONT")
    {
		$this->SetTextColor($this->rapport_kop2_fontcolor['r'],$this->rapport_kop2_fontcolor['g'],$this->rapport_kop2_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

		if($this->rapportCounter <> $this->rapportCounterLast  && $this->rapport_layout != 16)
  		$this->customPageNo = 0;
    }
    else
    {
  	if($this->rapportCounter <> $this->rapportCounterLast)
  		$this->customPageNo = 0;

		$this->customPageNo++;

		$this->SetLineWidth($this->lineWidth);

		if(empty($this->top_marge))
			$this->top_marge = $this->marge;
		$this->SetY($this->top_marge);

		$this->SetTextColor($this->rapport_kop2_fontcolor['r'],$this->rapport_kop2_fontcolor['g'],$this->rapport_kop2_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$y = $this->GetY();

		// default header stuff
		$this->SetX($this->marge);

		if($this->rapport_layout == 17 && $this->rapport_type == "OIBS2")
		  $this->rapport_koptext = $this->rapport_koptext_old;

		if($this->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
		$this->rapport_koptext = $this->rapport_consolidatieKoptext;
		$this->rapport_koptext = str_replace("{PortefeuilleFormat}", $this->rapport_portefeuilleFormat, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Portefeuille}", $this->rapport_portefeuille, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $this->rapport_portefeuilleVoorzet, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Depotbank}", $this->rapport_depotbank, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{DepotbankOmschrijving}", $this->rapport_depotbankOmschrijving, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Risicoklasse}", $this->rapport_risicoklasse, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Risicoprofiel}", $this->rapport_risicoprofiel, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Client}", $this->rapport_client, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $this->rapport_clientVermogensbeheerder, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Naam1}", $this->__appvar['consolidatie']['portefeuillenaam1'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Naam2}", $this->__appvar['consolidatie']['portefeuillenaam2'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Accountmanager}", $this->rapport_accountmanager, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $this->portefeuilledata['VermogensbeheerderNaam'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{crm.naam}", $this->portefeuilledata['crm.naam'], $this->rapport_koptext);
		}
		else
		{
		$this->rapport_koptext = str_replace("{PortefeuilleFormat}", $this->rapport_portefeuilleFormat, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Portefeuille}", $this->rapport_portefeuille, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{PortefeuilleVoorzet}", $this->rapport_portefeuilleVoorzet, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Depotbank}", $this->rapport_depotbank, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{DepotbankOmschrijving}", $this->rapport_depotbankOmschrijving, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Risicoklasse}", $this->rapport_risicoklasse, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Risicoprofiel}", $this->rapport_risicoprofiel, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Client}", $this->rapport_client, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{ClientVermogensbeheerder}", $this->rapport_clientVermogensbeheerder, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Naam1}", $this->rapport_naam1, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Naam2}", $this->rapport_naam2, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{Accountmanager}", $this->rapport_accountmanager, $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{ModelPortefeuille}", $this->portefeuilledata['ModelPortefeuille'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{VermogensbeheerderNaam}", $this->portefeuilledata['VermogensbeheerderNaam'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{SoortOvereenkomst}", $this->portefeuilledata['SoortOvereenkomst'], $this->rapport_koptext);
		$this->rapport_koptext = str_replace("{crm.naam}", $this->portefeuilledata['crm.naam'], $this->rapport_koptext);
		}

		$this->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $this->rapport_portefeuilleVoorzet, $this->rapport_liquiditeiten_omschr);

		if($this->rapport_type == "MOD" || $this->rapport_type == "CASHY" || $this->rapport_type == "ORDERP")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		//rapport_risicoklasse


		if(is_file($this->rapport_logo))
		{
		  if($this->rapport_layout == 5 || $this->rapport_layout == 25)
		  {
			  $this->Image($this->rapport_logo, $logopos -33, 5, 108, 15);
		  }
      if($this->rapport_layout == 12 )
      {
        $this->Image($this->rapport_logo, $logopos - 20, 8, 86);
      }
		  elseif($this->rapport_layout == 7)
		  {
		    $factor=0.04;
		    $this->Image($this->rapport_logo, $logopos, 5, 1029*$factor, 632*$factor);
		  }
		  elseif($this->rapport_layout == 30)
		  {
		    if($this->rapport_type == "MOD" || $this->rapport_type == "CASHY")
		    	$logopos = 80;
		    else
		      $logopos = 115;
		    $factor=0.04;
		    $this->Image($this->rapport_logo, $logopos, 2, 1691*$factor, 586*$factor);
		  }
		  elseif($this->rapport_layout == 31)
		  {
		    $factor=0.08;
		    $this->Image($this->rapport_logo, $logopos -25 , 5, 1074*$factor, 192*$factor);
		 	}
		  elseif($this->rapport_layout == 14 )
		  {
			  //$this->Image($this->rapport_logo, 220, 5, 65, 20);
			  //$factor=0.09;
		    //$xSize=492*$factor;
		    //$ySize=211*$factor;
        $factor=0.05;
		    $xSize=983*$factor;
		    $ySize=288*$factor;
		    $this->Image($this->rapport_logo, 235, 5, $xSize, $ySize);
		  }
		  elseif ($this->rapport_layout == 16 )
		  {
		    if($this->rapport_type == "MOD" || $this->rapport_type == "CASHY")
		      $logopos = 100;
				else
		    	$logopos = 185;
			//  $this->Image($this->rapport_logo, 260, 5, 27, 20); //kei
			$this->Image($this->rapport_logo, $logopos, 5, 101, 12);//duis 1050,125
		  }
		  elseif ($this->rapport_layout == 17 )
		  {
			  $this->Image($this->rapport_logo, 242, 191, 45, 10);
		  }
		  elseif($this->rapport_layout == 1)
		  {
			  $this->Image($this->rapport_logo, $logopos, 7, 43, 15);
		  }
		  else
		    $this->Image($this->rapport_logo, $logopos, 5, 43, 15);
		}
		else if(!empty($this->rapport_logo_tekst))
		{
			$this->SetX(110);
			$this->SetTextColor($this->rapport_logo_fontcolor['r'],$this->rapport_logo_fontcolor['g'],$this->rapport_logo_fontcolor['b']);
			$this->SetFont($this->rapport_logo_font,$this->rapport_logo_fontstyle,$this->rapport_logo_fontsize);
			$this->MultiCell(85	,4,$this->rapport_logo_tekst,0, "C");

			if ($this->rapport_logo_tekst2)
			{
			$this->SetX(110);
			$this->SetTextColor($this->rapport_logo_fontcolor2['r'],$this->rapport_logo_fontcolor2['g'],$this->rapport_logo_fontcolor2['b']);
			$this->SetFont($this->rapport_logo_font2,$this->rapport_logo_fontstyle2,$this->rapport_logo_fontsize2);
			$this->MultiCell(85	,4,$this->rapport_logo_tekst2,0, "C");
			}

			$this->SetTextColor($this->rapport_kop2_fontcolor['r'],$this->rapport_kop2_fontcolor['g'],$this->rapport_kop2_fontcolor['b']);
			$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		}

		if ($this->rapport_layout != 17 )
		  $this->MultiCell(90,4,$this->rapport_koptext,0,'L');
		$this->SetY($y);

		if($this->rapport_type == "MOD" || $this->rapport_type == "CASHY" || $this->rapport_type == "PORTAAL")
		{
			$x = 160;
		}
		else
		{
			$x = 250;
		}

		$this->SetY($y);
		$this->SetX($x);

		if ($this->rapport_layout == 14)
	  {

		$this->MultiCell(40,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo."\n".vertaalTekst("Rapportagedatum:",$this->rapport_taal)."\n".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'R');
	  $this->SetXY(100,$y);

		$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		$this->MultiCell(100,4,vertaalTekst("\n".$this->rapport_titel,$this->rapport_taal),0,'C');

		$this->SetXY(100,$y+18);

	  }
	  elseif ($this->rapport_layout == 15)
	  {
	    //lege pagina
		  $this->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
		  $this->SetFillColor(255,255,255);
		  $this->Rect(8.5, 8.5, 280, 193, 'D');
		  $this->Rect(9.5, 9.5, 278, 191, 'D');
      $this->SetFillColor(255,255,153);
		 	$this->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
		  $this->Rect(14, 14, 268, 182, 'DF');
		  $this->Rect(15, 15, 266, 180, 'D');
		  $this->SetLineStyle(array('width' => 0.3 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
		  $this->Rect(160, 20, 110, 30, 'D');
			$this->SetLineStyle(array('width' => 0.6 , 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,128)));
			$this->SetFillColor(255,255,255);
		  $this->Rect(161, 21, 108, 28, 'DF');
		  if(is_file($this->rapport_afbeelding))
		  {
			  $this->Image($this->rapport_afbeelding, 162, 22, 106, 26);
		  }
		  $this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		  $this->SetFont('arial','B',11);
		  $this->SetXY(30,30);
		  $this->SetAligns(array('L','C'));
		  $this->SetWidths(array(75,40));
		  $this->ln();
		  $this->row(array('Clientnummer',$this->rapport_clientVermogensbeheerder));

	    $i=1;
      $portefeuilleString='';
	  	for($j=0;$j<strlen($this->rapport_portefeuille);$j++)
	  	{
		   if($i>2 && $j < 7)
	  	 {
	  	  $portefeuilleString.='.';
		    $i=1;
	  	 }
	  	 $portefeuilleString.= $this->rapport_portefeuille[$j];
		   $i++;
	  	}
		  $this->row(array('Rekeningnummer '.$this->rapport_depotbank.' Bank',$portefeuilleString));
		  $this->ln(12);
		  $this->SetFont('arial','B',14);
		  $this->Cell(100,8,$this->rapport_titel);
		  $this->SetFont('arial','',14);
		  $this->Cell(40,8,jul2form($this->rapport_datum));
		  $this->SetFont('arial','',14);
		  $this->Cell(100,8,'Client: '.$this->rapport_naam1);

		  $this->ln(12);
	  }
	  elseif ($this->rapport_layout == 16)
	  {
	    $this->MultiCell(40,4,"\n\n\n",0,'R');

	    $this->SetX(100);
		  $this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		  $this->MultiCell(100,4,vertaalTekst($this->rapport_titel,$this->rapport_taal),0,'C');
	  }
	  elseif ($this->rapport_layout == 17)
	  {

	 //   $this->CellBorders = array();
		//  $this->fillCell = array();
		  $this->SetFont($this->rapport_font,$this->rapport_fontstyle,$this->rapport_fontsize);
		  $this->SetTextColor($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['g'],$this->rapport_fonds_fontcolor['b']);
		  $this->SetDrawColor($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['g'],$this->rapport_fonds_fontcolor['b']);

	    $this->SetXY($this->marge,$this->marge);
	    $this->MultiCell(90,4,$this->rapport_koptext,0,'L');
	    $this->SetXY($x,$this->marge);
	    $this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
	    $this->MultiCell(40,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo,0,'R');
	    $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
	    $this->SetX($x);
      $this->MultiCell(40,4,"\n".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'R');
	    $this->SetXY(100+$this->marge,15);
		  $this->SetFont($this->rapport_font,'b',12);
		  $this->MultiCell(100,4,vertaalTekst($this->rapport_titel,$this->rapport_taal),0,'L');

	  }
	  else
	  {
	    $this->MultiCell(40,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$this->rapport_taal)."\n".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'R');
	    $this->SetX(100);
		  $this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		  $this->MultiCell(100,4,vertaalTekst($this->rapport_titel,$this->rapport_taal),0,'C');
	  }

	 switch($this->rapport_type)
	  	{
			case "MOD" :
				$this->HeaderMOD();
			break;
			case "HSE" :
				$this->HeaderHSE();
			break;
			case "HSEP" :
				$this->HeaderHSEP();
			break;
			case "VOLK" :
				$this->HeaderVOLK();
			break;
			case "VOLKD" :
				$this->HeaderVOLKD();
			break;
			case "OIB" :
				$this->HeaderOIB();
			break;
			case "OIBS" :
				$this->HeaderOIBS();
			break;
			case "OIBS2" :
				$this->HeaderOIBS2();
			break;
			case "OIH" :
				$this->HeaderOIH();
			break;
			case "OIV" :
				$this->HeaderOIV();
			break;
			case "PERF" :
				$this->HeaderPERF();
			break;
			case "PERFG" :
				$this->HeaderPERFG();
			break;
			case "VHO" :
				$this->HeaderVHO();
			break;
			case "FISCAAL" :
				$this->HeaderFISCAAL();
			break;      
			case "VHO_L15" :
			  $this->HeaderVHO_L15();
			break;
			case "TRANS" :
				$this->HeaderTRANS();
			break;
			case "MUT" :
				$this->HeaderMUT();
			break;
			case "MUT2" :
				$this->HeaderMUT2();
			break;
			case "Risico" :
				$this->HeaderRisico();
			break;
			case "OIR" :
				$this->HeaderOIR();
			break;
			case "ATT" :
				$this->HeaderATT();
			break;
			case "CASH" :
				$this->HeaderCASH();
			break;
			case "CASHY" :
				$this->HeaderCASHY();
			break;
			case "GRAFIEK":
			  $this->HeaderGRAFIEK();
			break;
			case "SMV":
			  $this->HeaderSMV();
			break;
      case "ZORG":
        $this->HeaderZORG();
      break;  
		  case "VKM":
			  $this->HeaderVKM();
			break;
      case "VKMA":
       $this->HeaderVKMA();
       break;
		  case "RESTRICTIES":
			 $this->HeaderRESTRICTIES();
			break;
			case "waardeprognose":
			 $this->HeaderWaardePrognose();
		  break;
			}

		$this->headerStart = $this->getY()+4;

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);

		$this->rapportCounterLast = $this->rapportCounter;
    }
  }
  $this->lastPortefeuille=$this->portefeuilledata['Portefeuille'];
}


	//Page footer
	function Footer()
	{

	  if (file_exists('rapport/include/PDFRapport_footers_L'.$this->rapport_layout.".php"))
		{
  	  include_once('rapport/include/PDFRapport_footers_L'.$this->rapport_layout.".php");
		}
    elseif (file_exists('rapport/include/layout_'.$this->rapport_layout.'/PDFRapport_footers_L'.$this->rapport_layout.".php"))
    {
      include_once('rapport/include/layout_'.$this->rapport_layout.'/PDFRapport_footers_L'.$this->rapport_layout.".php");
    }
    
    
    $basisFunctionString = 'Footer_basis_L'.$this->rapport_layout;
    
	if(function_exists($basisFunctionString) && $this->rapport_type <> "ORDERP")
    {
    $basisFunctionString($this);

    $functionString = 'Footer'.$this->last_rapport_type.'_L'.$this->rapport_layout;

     if(function_exists($functionString))
       $functionString($this);
	  }
    elseif ($this->rapport_type == "ORDERP")
    {
			global $USR;
      $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
      $this->SetY(-15);
      $this->MultiCell(210-2*$this->marge,4,"Printinfo: aangemaakt op ".date('d-m-Y H:i:s').' '.$USR,0,'R');
    }
    else if ($this->nextFactuur == true )
    {
      $this->FooterFACTUUR();
      $this->nextFactuur = false;
    }
    elseif ($this->frontPage == true)
    {
       $this->frontPage = false;
    }
    elseif ($this->rapport_layout == 16)
    {
      $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
      $this->SetY(-15);
      $this->MultiCell(280,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo,0,'R');
      if($this->rapport_type == "FRONT" && $this->rapportCounter <> $this->rapportCounterLast)
        $this->customPageNo =1;

    }
    elseif ($this->rapport_layout == 18)
    {
      $this->Rect(8,200,280,2,'F','F',$this->rapport_voet_bgcolor);
      $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
      $this->SetY(-15);
      $this->MultiCell(280,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo,0,'R');
      if($this->rapport_type == "FRONT" && $this->rapportCounter <> $this->rapportCounterLast)
        $this->customPageNo =1;

    }
    elseif ($this->rapport_layout == 8)
    {
    //  $this->AliasNbPages();
     $this->SetFont($this->rapport_font,'',$this->rapport_voetfontsize);
      $this->SetY(-8);
      $this->MultiCell(240,4,$this->rapport_voettext,'0','L');
      $this->SetY(-8);
  //    $this->MultiCell(273,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo." van de {nb}",0,'R');
      $this->MultiCell(273,4,vertaalTekst("Pagina",$this->rapport_taal)." ".$this->customPageNo,0,'R');
      if($this->rapport_type == "FRONT" && $this->rapportCounter <> $this->rapportCounterLast)
        $this->customPageNo =1;
    }
    else
    {
      if ($this->rapport_type != "FACTUUR")
      {
        $this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
	      $this->SetY(-15);
	      $this->SetFont($this->rapport_font,'',$this->rapport_voetfontsize);
	      $this->MultiCell(240,4,$this->rapport_voettext,'0','L');
	      $this->Cell(25,4,$this->rapport_voettext_rechts,'0','L');
      }
    }

    if ($this->rapport_type == "FACTUUR")
    {
      $this->nextFactuur = true;
    }
}


	function HeaderFACTUUR()
	{

	  if ($this->rapport_layout == 5)
	  {
		  if(is_file($this->rapport_factuurHeader))
		  {
			$this->Image($this->rapport_factuurHeader, 0, 10, 210, 34);
		  }
	  }
  }

	function FooterFACTUUR()
	{
	  if ($this->rapport_layout == 5)
	  {
		  if(is_file($this->rapport_factuurFooter))
		  {
			$this->Image($this->rapport_factuurFooter, 5, 255, 200, 37);
		  }
	  }
	}

	function HeaderVKM()
	{
		$this->ln();
		$widthBackup=$this->widths;
		$dataWidth=array(28,50,20,20,20,20,20,18,18,18,18,18,15);
		$this->SetWidths($dataWidth);
		$this->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R'));
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$this->ln();
		$lastColors=$this->CellFontColor;
		unset($this->CellFontColor);
    unset($this->CellBorders);
		if(!isset($this->vmkHeaderOnderdrukken))
		{
			$this->Row(array(vertaalTekst("Risico\ncategorie", $this->rapport_taal),
									 "\n" . vertaalTekst("Fonds", $this->rapport_taal),
									 "\n" . date('d-m-Y', $this->rapport_datumvanaf),
									 "\n" . date('d-m-Y', $this->rapport_datum),
									 "\n" . vertaalTekst("Mutaties", $this->rapport_taal),
									 "\n" . vertaalTekst("Resultaat", $this->rapport_taal),
									 vertaalTekst("Gemiddeld vermogen", $this->rapport_taal),
									 vertaalTekst("Doorl. kosten %", $this->rapport_taal),
									 vertaalTekst("Trans Cost %", $this->rapport_taal),
									 vertaalTekst("Perf Fee %", $this->rapport_taal),
									 vertaalTekst("Fondskost. absoluut", $this->rapport_taal),
									 "\n" . vertaalTekst("Weging", $this->rapport_taal),
									 vertaalTekst("VKM\nBijdrage", $this->rapport_taal)));
			unset($this->vmkHeaderOnderdrukken);
			$this->Line(($this->marge),$this->GetY(),$this->marge + array_sum($dataWidth),$this->GetY());
		}
		$this->widths=$widthBackup;
		$this->CellFontColor=$lastColors;
		$this->SetLineWidth(0.1);
	}

  function HeaderZORG()
  {
    $this->ln();
		$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		$this->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Percentage','ZorgWaarde'));
		$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
  }
  
  function HeaderEND()
  {
    $this->ln();
  }
  
  function HeaderVKMA()
  {
    $this->ln();
    $widthBackup=$this->widths;
    $dataWidth=array(28,50,28,28,28,28,28,28,28);
    $this->SetWidths($dataWidth);
    $this->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R'));
    $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
    $this->ln();
    $lastColors=$this->CellFontColor;
    unset($this->CellFontColor);
    unset($this->CellBorders);
    if(!isset($this->vmkHeaderOnderdrukken))
    {
      $this->Row(array(vertaalTekst("Risico/categorie", $this->rapport_taal),
                        "" . vertaalTekst("Fonds", $this->rapport_taal),
                        "" . date('d-m-Y', $this->rapport_datum),
                        vertaalTekst("Doorl. kosten %", $this->rapport_taal),
                        vertaalTekst("Trans Cost %", $this->rapport_taal),
                        vertaalTekst("Perf Fee %", $this->rapport_taal),
                        vertaalTekst("Prognose dl kosten absoluut", $this->rapport_taal),
                        "" . vertaalTekst("Weging", $this->rapport_taal),
                        "" . vertaalTekst("VKM Bijdrage", $this->rapport_taal)));
      unset($this->vmkHeaderOnderdrukken);
      $this->Line(($this->marge),$this->GetY(),$this->marge + array_sum($dataWidth),$this->GetY());
    }
    $this->widths=$widthBackup;
    $this->CellFontColor=$lastColors;
    $this->SetLineWidth(0.1);
  }

  function HeaderMOD()
  {
		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= array_sum($this->widthB);

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 12 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		$this->SetX($this->marge+$huidige+5);
		$this->MultiCell(90,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0, "C");

		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());

		$tmpY = $this->GetY();

		$this->SetY($tmpY);
		$this->SetX($this->marge);

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin EUR",$this->rapport_taal),
											($this->rapport_inprocent)?vertaalTekst("In % Totaal",$this->rapport_taal):""),
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin EUR",$this->rapport_taal));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->SetFont($this->rapport_font,"bi",$this->rapport_fontsize);
		$this->setY($this->GetY()-8);
		$this->row(array(vertaalTekst("Categorie",$this->rapport_taal)));
		$this->ln();

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
  }

  function HeaderHSE()
  {
		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		if($this->rapport_layout == 4)
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1];
			$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5] +$this->widthB[6];
			$actueel 			= $eindhuidige + $this->widthB[7];
			$eindactueel 	= array_sum($this->widthB);
		}
		else
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
			$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];
			$actueel 			= $eindhuidige + $this->widthB[6];
			$eindactueel 	= array_sum($this->widthB);
		}

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 12 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		$tmpY = $this->GetY();
		$this->SetX($this->marge+$huidige+5);
		if($this->rapport_HSE_volgorde_beginwaarde == 0)
			$this->MultiCell(90,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0, "C");
		else if($this->rapport_layout == 4)
			$this->MultiCell(90,4, vertaalTekst("Fonds",$this->rapport_taal), 0, "C");
		else
			$this->MultiCell($eindhuidige - $huidige - 5 ,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0, "C");
//			$this->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0, "C");
// Bovenstaande cel was standaard 90 Breed. Nu dynamische ivm tekstcentreren wanneer kolombreedte smaller.
		$this->SetY($tmpY);
		$this->SetX($this->marge+$actueel);

		if($this->rapport_HSE_volgorde_beginwaarde == 0)
			$this->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0, "C");
		else if($this->rapport_layout == 4)
			$this->MultiCell(90,4, vertaalTekst("Waarde",$this->rapport_taal), 0, "C");
		else
			$this->MultiCell(90,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0, "C");


		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());


		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		if($this->rapport_layout == 4)
		{
			$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
											vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
											"",
											vertaalTekst("Beginwaarde verslagperiode",$this->rapport_taal),
											"",
											vertaalTekst("Koers (valuta)",$this->rapport_taal),
											"",
											vertaalTekst("Valuta",$this->rapport_taal),
											vertaalTekst($this->rapportageValuta,$this->rapport_taal),
											($this->rapport_inprocent)?vertaalTekst("In % Totaal",$this->rapport_taal):""));
		}
		else if($this->rapport_layout == 10)
		{
			$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											"",
											vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
											"",
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											"",
											vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
											($this->rapport_inprocent)?vertaalTekst("In % Totaal",$this->rapport_taal):""));

		}
		else
		{
			$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
											"",
											vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
											vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
											($this->rapport_inprocent)?vertaalTekst("In % Totaal",$this->rapport_taal):""));
		}

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->SetFont($this->rapport_font,"bi",$this->rapport_fontsize);
		$this->setY($this->GetY()-8);
		$this->row(array(vertaalTekst("Categorie",$this->rapport_taal)));
		$this->ln();

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
  }



	function HeaderRESTRICTIES()
	{
		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
		$huidige=0;
		$eindhuidige=0;
		$actueel=0;
		$eindactueel=0;
		foreach($this->widthB as $index=>$waarde)
		{

			if($index<3)
				$huidige +=$waarde;
			if($index<6)
				$eindhuidige +=$waarde;
			if($index<7)
				$actueel += $waarde;
			$eindactueel += $waarde;
		}
		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 12 , 'F');
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar
		$tmpY = $this->GetY();
		$this->SetX($this->marge+$huidige);
		$this->MultiCell(90,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0, "C");
		$this->SetY($tmpY);
		$this->SetX($this->marge+$actueel);
		$this->MultiCell(90,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0, "C");

		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
									 vertaalTekst("Aantal",$this->rapport_taal),
									 vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
									 vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
									 vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
									 "",
									 vertaalTekst("Per stuk \nin valuta",$this->rapport_taal),
									 vertaalTekst("Portefeuille \nin valuta",$this->rapport_taal),
									 vertaalTekst("Portefeuille \nin ".$this->rapportageValuta,$this->rapport_taal),
									 ""));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->SetFont($this->rapport_font,"bi",$this->rapport_fontsize);
		$this->setY($this->GetY()-8);
		$this->row(array(vertaalTekst("Categorie",$this->rapport_taal)));
		$this->ln();

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
	}

  function HeaderHSEP()
  {
	//	$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
	//	$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

//		$actueel 			= $eindhuidige + $this->widthB[6];
//		$eindactueel 	= array_sum($this->widthB);
	 $widthsBackup= $this->widths;
	 $alignsBackup= $this->aligns;

   // $this->SetFont('Arial');
		$this->SetY(10);
		$this->MultiCell(90,4,$this->portefeuilledata['VermogensbeheerderNaam'],0,'L');
		$this->SetXY(155,10);
		$now = time();
	  $this->MultiCell(50,4,date("j/n/Y",$now)." Blad : ".$this->customPageNo .'/{LastPage}',0,'R');
	if($this->customPageNo < 2)
	{
	  $this->SetXY(130,45);

	  $adres .= $this->portefeuilledata['Naam'];
	  if(strlen($this->portefeuilledata['Naam1']) > 0)
	    $adres .= "\n".$this->portefeuilledata['Naam1'];
	  if(strlen($this->portefeuilledata['Adres']) > 0)
	    $adres .= "\n".$this->portefeuilledata['Adres'];
	  if(strlen($this->portefeuilledata['Woonplaats']) > 0)
	    $adres .= "\n".$this->portefeuilledata['Woonplaats'];
	  if(strlen($this->portefeuilledata['Land']) > 0)
	    $adres .= "\n".$this->portefeuilledata['Land'];
    $this->MultiCell(80,4,$adres ,0,'L');
/*
	  if(strlen($this->portefeuilledata['Naam1']) > 0)
	    $this->MultiCell(80,4,$this->portefeuilledata['Naam']."\n".$this->portefeuilledata['Naam1']."\n".$this->portefeuilledata['Adres']."\n".$this->portefeuilledata['Woonplaats'] ,0,'L');
	  else
	    $this->MultiCell(80,4,$this->portefeuilledata['Naam']."\n".$this->portefeuilledata['Adres']."\n".$this->portefeuilledata['Woonplaats'] ,0,'L');
*/
	 if(!$this->memoOnderdrukken)
    $this->MultiCell(190,4,$this->portefeuilledata['Memo'],0,'L');

  	$this->SetXY(10,80);
  	$this->MultiCell(190,4,'POSITIE-overzicht per '.date("j/n/Y",$this->rapport_datum) ,0,'C');
  	$this->SetY($this->GetY()+1);
  	$saldoText = "Saldo geldrekeningen ".$this->saldoGeldrekeningen." ".$this->rapportageValuta;
	}
	else
	{
	  $this->SetXY(130,45);
	  $adres .= $this->portefeuilledata['Naam'];
	  if(strlen($this->portefeuilledata['Naam1']) > 0)
	    $adres .= "\n".$this->portefeuilledata['Naam1'];
	  $this->MultiCell(80,4,$adres,0,'L');
	  $this->SetXY(10,60);
	  $saldoText ='';
	}
  	$this->Line($this->marge ,$this->GetY(), $this->marge + 194,$this->GetY());
    $this->SetY($this->GetY()+1);
		$this->SetWidths(array(60,75,60));
		$this->SetAligns(array('L','C','R'));

		$oldPortefeuilleString = strval($this->rapport_portefeuille);
	  $i=1;
	  $puntenAantal=0;
		for($j=0;$j<strlen($oldPortefeuilleString);$j++)
		{
		 if($i>2 && $puntenAantal <3)
		 {
		  $portefeuilleString.='.';
		  $i=1;
		  $puntenAantal ++;
		 }
		 $portefeuilleString.= $oldPortefeuilleString[$j];
		 $i++;
		}
  	$this->row(array($portefeuilleString,
  	                 $saldoText,
  	                 'Slotkoersen per '.date("j/n/Y",$this->rapport_datum)));
  	$this->SetY($this->GetY()+1);
  	$this->Line($this->marge ,$this->GetY()+1, $this->marge + 194,$this->GetY()+1);


  	$this->setY($this->GetY()+2);

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$this->SetFont($this->rapport_font,$this->rapport_subtotaal_fontstyle,$this->rapport_fontsize);

		if($this->customPageNo < 2)
  		$this->row(array("",
		                  "Aantal",
											"  Fonds",
											"Vervaldatum",
											"Actueel",
											"",
											"Waarde EUR",
											""));
		else
	  	$this->row(array(''));

	 $this->SetWidths($widthsBackup);
	 $this->SetAligns($alignsBackup);

  }

  function HeaderVOLK()
  {
		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		if($this->rapport_VOLK_volgorde_beginwaarde == 2 )
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1];
			$eindhuidige 	= $huidige +$this->widthB[2]+$this->widthB[3]+$this->widthB[4];

			$actueel 			= $eindhuidige + $this->widthB[5];
			$eindactueel 	= $actueel + $this->widthB[6] + $this->widthB[7];

			$resultaat 		= $eindactueel + $this->widthB[8] ;
			$eindresultaat = $resultaat +  $this->widthB[9] +  $this->widthB[10] +  $this->widthB[11]	+  $this->widthB[12];
		}
		else if ($this->rapport_layout == 8)
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3];
			$eindhuidige 	= $huidige +$this->widthB[4]+$this->widthB[5]+$this->widthB[6];

			$actueel 			= $eindhuidige + $this->widthB[7] + $this->widthB[8] ;
			$eindactueel 	= $actueel + $this->widthB[9] + $this->widthB[10] + $this->widthB[11];

			$resultaat 		= $eindactueel +  $this->widthB[12] - 10;
			$eindresultaat = $resultaat  +  $this->widthB[13] + $this->widthB[14]+ $this->widthB[15]+ $this->widthB[16] + $this->widthB[17] +10;

		}
		else
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
			$eindhuidige 	= $huidige +$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

			$actueel 			= $eindhuidige + $this->widthB[6];
			$eindactueel 	= $actueel + $this->widthB[7] + $this->widthB[8] + $this->widthB[9];

			$resultaat 		= $eindactueel + $this->widthB[10];
			$eindresultaat = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13];
		}


		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 16 , 'F');
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);


		// lijntjes onder beginwaarde in het lopende jaar
		if($this->rapport_VOLK_volgorde_beginwaarde == 2 )
		{
			$this->SetX($this->marge+$huidige+5);
			$this->Cell(65,4, vertaalTekst("Actuele waardes",$this->rapport_taal), 0,0, "C");
			$this->SetX($this->marge+$actueel);
			if(substr(jul2form($this->rapport_datumvanaf),0,5) == '01-01')
			  $this->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$this->rapport_taal), 0,0,"L");
			else
			  $this->Cell(50,4, vertaalTekst("Beginwaarde rapportage periode",$this->rapport_taal), 0,0,"L");
			$this->SetX($this->marge+$resultaat);
			$this->Cell(60,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,1, "C");
		}
		else
		{
			$this->SetX($this->marge+$huidige+5);
			if($this->rapport_VOLK_volgorde_beginwaarde == 0 ||$this->rapport_VOLK_volgorde_beginwaarde == 2 )
				$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
			else
			{
				//$this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");

				if(substr(jul2form($this->rapport_datumvanaf),0,5) == '01-01')
			    $this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");
			  else
			    $this->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$this->rapport_taal), 0,0,"C");
			}
			$this->SetX($this->marge+$actueel);
			if($this->rapport_VOLK_volgorde_beginwaarde == 0 ||$this->rapport_VOLK_volgorde_beginwaarde == 2 )
			{
			//  $this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");
							if(substr(jul2form($this->rapport_datumvanaf),0,5) == '01-01')
			    $this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");
			  else
			    $this->Cell(65,4, vertaalTekst("Beginwaarde rapportage periode",$this->rapport_taal), 0,0,"C");
			}
			else
				$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
			$this->SetX($this->marge+$resultaat);
			$this->Cell(60,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,1, "C");
		}

		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());
		$this->Line(($this->marge+$resultaat),$this->GetY(),$this->marge + $eindresultaat,$this->GetY());


		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$y = $this->getY();

		if($this->rapport_VOLK_volgorde_beginwaarde == 2)
		{
      if ($this->rapport_layout == 16)
		  {
			$this->row(array(vertaalTekst("Aantal",$this->rapport_taal),
										"\n".vertaalTekst("Effect",$this->rapport_taal),
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										vertaalTekst("in % van vermogen",$this->rapport_taal),
										vertaalTekst("Opgelopen Rente",$this->rapport_taal),
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										vertaalTekst("",$this->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		  }
			else
			$this->row(array(vertaalTekst("Aantal",$this->rapport_taal),
										"\n".vertaalTekst("Effect",$this->rapport_taal),
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										vertaalTekst("in % van vermogen",$this->rapport_taal),
										"",
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										vertaalTekst("",$this->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 1)
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 4)
		{
			$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
										vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
										"",
										vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
										vertaalTekst("In %\ntotaal",$this->rapport_taal),
										vertaalTekst("Koers- \nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta- \nresultaat",$this->rapport_taal),
										vertaalTekst("In % \ntotaal",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 2 || $this->rapport_layout == 12)
		{
		  if($this->rapport_layout == 12)
		  {
		    $this->setX($this->marge+$this->widthB[0]+$this->widthB[1]-10);
				$this->Cell($this->widthB[1],4,vertaalTekst("Bewaarder",$this->rapport_taal),null,null,null,null,null);
		    $this->setX($this->marge);
		  }

			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
										$eindresultaat += 15 ;
		}
		else if ($this->rapport_layout == 8)
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										'',
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Aandeel op totale waarde",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),

										vertaalTekst("Koers-\nresultaat",$this->rapport_taal),
										'',//"%",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										'',//"%",
										'',//vertaalTekst("Totaal Resultaat %",$this->rapport_taal),
										vertaalTekst("Totaal\nBijdrage %",$this->rapport_taal))
										);
		}
		else
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										vertaalTekst("Aandeel op totale waarde",$this->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}


		$this->setY($y);
		if($this->rapport_VOLK_volgorde_beginwaarde == 2)
		{
			$this->SetFont($this->rapport_font,"i",$this->rapport_fontsize);
			$this->row(array("",vertaalTekst("Categorie\n",$this->rapport_taal)));
		}
		else
		{
			$this->SetFont($this->rapport_font,"bi",$this->rapport_fontsize);
			$this->SetWidths($this->widthA);
			$this->SetAligns($this->alignA);
			$this->row(array(vertaalTekst("Categorie\n",$this->rapport_taal)));
		}
		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->ln();
		$this->ln();

		$this->Line($this->marge,$this->GetY(),$this->marge + $eindresultaat,$this->GetY());
		$this->ln();
  }


  function HeaderVOLKD()
  {
		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		if($this->rapport_VOLKD_volgorde_beginwaarde == 2 )
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1];
			$eindhuidige 	= $huidige +$this->widthB[2]+$this->widthB[3]+$this->widthB[4];

			$actueel 			= $eindhuidige + $this->widthB[5];
			$eindactueel 	= $actueel + $this->widthB[6] + $this->widthB[7];

			$resultaat 		= $eindactueel + $this->widthB[8] ;
			$eindresultaat = $resultaat +  $this->widthB[9] +  $this->widthB[10] +  $this->widthB[11]	+  $this->widthB[12];
		}
		else
		{
			$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
			$eindhuidige 	= $huidige +$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

			$actueel 			= $eindhuidige + $this->widthB[6];
			$eindactueel 	= $actueel + $this->widthB[7] + $this->widthB[8] + $this->widthB[9];

			$resultaat 		= $eindactueel + $this->widthB[10];
			$eindresultaat = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13];
		}


		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 16 , 'F');
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);


		// lijntjes onder beginwaarde in het lopende jaar
		if($this->rapport_VOLKD_volgorde_beginwaarde == 2 )
		{
			$this->SetX($this->marge+$huidige+5);
			$this->Cell(65,4, vertaalTekst("Actuele waardes",$this->rapport_taal), 0,0, "C");
			$this->SetX($this->marge+$actueel);
			$this->Cell(50,4, vertaalTekst("Beginwaarde van lopend jaar",$this->rapport_taal), 0,0,"L");
			$this->SetX($this->marge+$resultaat);
			$this->Cell(60,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,1, "C");
		}
		else
		{
			$this->SetX($this->marge+$huidige+5);
			if($this->rapport_VOLKD_volgorde_beginwaarde == 0 ||$this->rapport_VOLKD_volgorde_beginwaarde == 2 )
				$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
			else
				$this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");
			$this->SetX($this->marge+$actueel);
			if($this->rapport_VOLKD_volgorde_beginwaarde == 0 ||$this->rapport_VOLKD_volgorde_beginwaarde == 2 )
				$this->Cell(65,4, vertaalTekst("Beginwaarde in het lopende jaar",$this->rapport_taal), 0,0,"C");
			else
				$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
			$this->SetX($this->marge+$resultaat);
			$this->Cell(60,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,1, "C");
		}

		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());
		$this->Line(($this->marge+$resultaat),$this->GetY(),$this->marge + $eindresultaat,$this->GetY());


		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$y = $this->getY();

		if($this->rapport_VOLKD_volgorde_beginwaarde == 2)
		{
			$this->row(array(vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("\nEffect",$this->rapport_taal),
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										vertaalTekst("in % van vermogen",$this->rapport_taal),
										"",
										vertaalTekst("Koers",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										vertaalTekst("",$this->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 1)
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 4)
		{
			$this->row(array("","\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
										vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(EUR)",$this->rapport_taal),
										"",
										vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										vertaalTekst("Waarde \n(EUR)",$this->rapport_taal),
										vertaalTekst("In %\ntotaal",$this->rapport_taal),
										vertaalTekst("Koers- \nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta- \nresultaat",$this->rapport_taal),
										vertaalTekst("In % \ntotaal",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 2 || $this->rapport_layout == 12)
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else if ($this->rapport_layout == 8)
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										vertaalTekst("Aandeel op totale waarde",$this->rapport_taal),
										vertaalTekst("Koers-\nresultaat",$this->rapport_taal),
										"%",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}
		else
		{
			$this->row(array("",
										"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										vertaalTekst("Aantal",$this->rapport_taal),
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										"",
										vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										vertaalTekst("Portefeuille in EUR",$this->rapport_taal),
										vertaalTekst("Aandeel op totale waarde",$this->rapport_taal),
										vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
										"",
										vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
										vertaalTekst("in %",$this->rapport_taal))
										);
		}


		$this->setY($y);
		if($this->rapport_VOLKD_volgorde_beginwaarde == 2)
		{
			$this->SetFont($this->rapport_font,"i",$this->rapport_fontsize);
			$this->row(array("",vertaalTekst("Categorie\n",$this->rapport_taal)));
		}
		else
		{
			$this->SetFont($this->rapport_font,"bi",$this->rapport_fontsize);
			$this->SetWidths($this->widthA);
			$this->SetAligns($this->alignA);
			$this->row(array(vertaalTekst("Categorie\n",$this->rapport_taal)));
		}
		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->ln();
		$this->ln();

		$this->Line($this->marge,$this->GetY(),$this->marge + $eindresultaat,$this->GetY());
		$this->ln();
  }

  function HeaderOIB()
  {

		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$lijn1 			= $this->widthB[0]+$this->widthB[1];
		$lijn1eind 	= $lijn1+$this->widthB[2] + $this->widthB[3] + $this->widthB[4] + $this->widthB[5];

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar

		if($this->rapport_OIB_specificatie == 1)
		{
		  $this->SetX($this->marge+$lijn1+5);
		  $this->MultiCell(90,4, vertaalTekst("Waarden",$this->rapport_taal), 0, "C");

		  $this->Line(($this->marge+$lijn1+5),$this->GetY(),$this->marge + $lijn1eind,$this->GetY());

		  $this->SetWidths($this->widthA);
		  $this->SetAligns($this->alignA);


			if($this->rapport_layout == 7)
			{
				$this->row(array(vertaalTekst("Vermogenscategorie",$this->rapport_taal),
											vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
			else if($this->rapport_layout == 1)
			{
				$this->row(array(vertaalTekst("Beleggingscategorieën",$this->rapport_taal),
											vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("%",$this->rapport_taal)));
			}
			else if($this->rapport_layout == 10)
			{
				$this->row(array(vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
			else if($this->rapport_layout == 12)
			{
				$this->row(array(vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("Regio",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
			else
			{
				$this->row(array(vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
		}
		else if($this->rapport_layout == 16)
		{
				$this->row(array(""));
		}
		else
		{
		  $lijn1 =80;
		  $lijn1eind = 125;
		  $this->SetX($this->marge+$lijn1+5);
		  $this->MultiCell(45,4, vertaalTekst("Waarden",$this->rapport_taal), 0, "C");

		  $this->Line(($this->marge+$lijn1+5),$this->GetY(),$this->marge + $lijn1eind,$this->GetY());

		  $this->SetWidths($this->widthA);
		  $this->SetAligns($this->alignA);


	    if($this->rapport_layout == 14)
			{
			$this->row(array(vertaalTekst("Beleggingscategorie",$this->rapport_taal),
										vertaalTekst("",$this->rapport_taal),

											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
			else
			{
			$this->row(array(vertaalTekst("Beleggingscategorie",$this->rapport_taal),
										vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("",$this->rapport_taal),
											vertaalTekst("",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
			}
		}

		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

  function HeaderOIBS()
  {
		$this->ln();
		// achtergrond kleur
		if($this->rapport_layout != 17)
		{
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');
		}
		$this->ln();

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= array_sum($this->widthB);


		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

		if($this->rapport_layout == 4)
		{
			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal)));

		}
		else if ($this->rapport_layout == 5)
		{

			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("In % totaal",$this->rapport_taal)));
		}
		else if ($this->rapport_layout == 10)
		{
			// zelfde als 5
			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("In % totaal",$this->rapport_taal)));
		}
		else if ($this->rapport_layout == 2 || $this->rapport_layout == 12)
		{

			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("In %",$this->rapport_taal)));
		}
		else
		{

			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal)));
		}
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

	function HeaderDOORKIJK()
	{
		$this->ln();

	}
  function HeaderOIBS2()
  {
    		  $backupStyle = $this->lastStyle ;
		  $this->fillCellBackup=$this->fillCell ;
		$this->ln();
		// achtergrond kleur
		if($this->rapport_layout != 170)
		{
		  $this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
	  	$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');
		}
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= array_sum($this->widthB);


		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

		if($this->rapport_layout == 170)
		{
		$this->switchFont('rapportKop');
		$this->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$this->row(array(vertaalTekst(" \n ",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving \n ",$this->rapport_taal),
												vertaalTekst("Aantal \n ",$this->rapport_taal),
												vertaalTekst("Kostprijs per\naandeel",$this->rapport_taal),
												vertaalTekst("Koers \n ",$this->rapport_taal),
												vertaalTekst("Valuta \n ",$this->rapport_taal),
												vertaalTekst("Waarde in\n".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("Ongerealiseerd\nresultaat",$this->rapport_taal),
												vertaalTekst("Ongerealiseerd\nresultaat in %",$this->rapport_taal),
												vertaalTekst("Aandeel op\ntotaal %",$this->rapport_taal),
												vertaalTekst("Risico\npercentage",$this->rapport_taal)));
		$this->fillCell = array();
		}
		else
		$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Kostprijs per aandeel",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("Ongerealiseerd resultaat",$this->rapport_taal),
												vertaalTekst("Ongerealiseerd resultaat in %",$this->rapport_taal),
												vertaalTekst("Aandeel op totaal %",$this->rapport_taal),
												vertaalTekst("Risicopercentage",$this->rapport_taal)));


		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
		$this->setY($this->GetY()+2);
		$this->switchFont($backupStyle);
		$this->fillCell =$this->fillCellBackup;
		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
  }

  function HeaderOIH()
  {
		$this->ln();
		// achtergrond kleur
    	if($this->rapport_layout != 17)
		{
		  $this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
	  	$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');
		}

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= array_sum($this->widthB);


		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);


		if($this->rapport_layout == 4)
		{
			$this->row(array("",
												vertaalTekst("Sectoren",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
												vertaalTekst("Koers",$this->rapport_taal),
												"",
												vertaalTekst("Valuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal)));

		}
		elseif($this->rapport_layout == 17)
		{
		$this->switchFont('rapportKop');
		$this->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		$this->row(array(vertaalTekst(" \n ",$this->rapport_taal),
												vertaalTekst("Fondsomschrijving \n ",$this->rapport_taal),
												vertaalTekst("Aantal \n ",$this->rapport_taal),
												vertaalTekst("Kostprijs per\naandeel",$this->rapport_taal),
												vertaalTekst("Koers \n ",$this->rapport_taal),
												vertaalTekst("Valuta \n ",$this->rapport_taal),
												vertaalTekst("Waarde in\n".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst("Ongerealiseerd\nresultaat",$this->rapport_taal),
												vertaalTekst("Ongerealiseerd\nresultaat in %",$this->rapport_taal),
												vertaalTekst("Aandeel op\ntotaal %",$this->rapport_taal),
												vertaalTekst("Risico\npercentage",$this->rapport_taal)));
		$this->fillCell = array();
		}
		else
		{

			$this->row(array("",
												vertaalTekst("\nSectoren",$this->rapport_taal),
												vertaalTekst("\nFondsomschrijving",$this->rapport_taal),
												vertaalTekst("\nAantal",$this->rapport_taal),
												vertaalTekst("\nKoers",$this->rapport_taal),
												"",
												vertaalTekst("\nValuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal)));
		}
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }



  function HeaderOIV()
  {
		$this->ln();



		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$lijn1 			= $this->marge + $this->widthB[0] + $this->widthB[1] + $this->widthB[2];
		$lijn1einde	= $lijn1 + $this->widthB[3]+ $this->widthB[4]+ $this->widthB[5];

		if($this->rapport_layout == 8)
		 	$this->widthB[6]=$this->widthB[6]-10;

		 	// achtergrond kleur
		if($this->rapport_layout == 4)
			$hoogte = 12;
		else
			$hoogte = 8;
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);

	  $this->Rect($this->marge, $this->getY(), array_sum($this->widthB), $hoogte  , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar
		$this->SetX($lijn1);

		if($this->rapport_layout == 4)
			$this->MultiCell($lijn1einde-$lijn1,4, vertaalTekst("Waarde",$this->rapport_taal), 0, "C");
		else
			$this->MultiCell($lijn1einde-$lijn1,4, vertaalTekst("Waarden",$this->rapport_taal), 0, "C");

		// lijntjes onder beginwaarde in het lopende jaar
		$this->Line(($lijn1+4),$this->GetY(),$lijn1einde,$this->GetY());



		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		if($this->rapport_layout == 4)
		{
			$this->row(array(vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("In valuta",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in % Totaal",$this->rapport_taal)));
		}
		else if($this->rapport_layout == 7)
		{
			$this->row(array(vertaalTekst("Valuta",$this->rapport_taal),
											vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("In valuta",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
		}
		else
		{
			$this->row(array(vertaalTekst("Valutasoort",$this->rapport_taal),
											vertaalTekst("Beleggingscategorie",$this->rapport_taal),
											vertaalTekst("In valuta",$this->rapport_taal),
											vertaalTekst("in valuta",$this->rapport_taal),
											vertaalTekst("in ".$this->rapportageValuta,$this->rapport_taal),
											vertaalTekst("in %",$this->rapport_taal)));
		}

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

  function HeaderOIR()
  {
		$this->ln();
		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= array_sum($this->widthB);


		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);


			$this->row(array("",
												vertaalTekst("\nRegio",$this->rapport_taal),
												vertaalTekst("\nFondsomschrijving",$this->rapport_taal),
												vertaalTekst("\nAantal",$this->rapport_taal),
												vertaalTekst("\nKoers",$this->rapport_taal),
												"",
												vertaalTekst("\nValuta",$this->rapport_taal),
												"",
												vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal)));
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());


  }

  function HeaderPERF()
  {
		// achtergrond kleur
		if($this->rapport_layout != 17 )
		{
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8, 'F');
		}
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);


		$this->ln(2);
		if($this->rapport_layout == 16 )
		  $this->Cell(100,4, '',0,0);
		else
		  $this->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$this->rapport_taal),0,0);

		if($this->rapport_layout == 7 )
		{
			$this->Cell(100,4, date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,0);
		}
		elseif($this->rapport_layout == 17 )
		{
		  $this->SetDrawColor(0,0,0);
		  $this->Cell(100,4, vertaalTekst("Verslagperiode",$this->rapport_taal)." ".$this->getKwartaal($this->rapport_datum)."e kwartaal",0,0);

		}
		else
		{
			$this->Cell(100,4, vertaalTekst("Verslagperiode",$this->rapport_taal)." ".date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,0);

		}

		$this->ln(2);

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$this->row(array("",
										 "",
										 "",
										 "",
										 "",
										 ""));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

  function HeaderPERFG()
  {

  }

  function HeaderVHO()
  {

		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= $actueel + $this->widthB[7] + $this->widthB[8] + $this->widthB[9];

		$resultaat 		= $eindactueel + $this->widthB[10];
		$eindresultaat = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13] +  $this->widthB[14];
		$eindresultaat2 = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13] ;

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 16 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);


		// lijntjes onder beginwaarde in het lopende jaar
		if($this->rapport_layout == 32)
		  $this->SetX($this->marge+$huidige);
		else
		  $this->SetX($this->marge+$huidige+5);

		if($this->rapport_layout == 16)
		{
		  $this->ln(6);
		}
		elseif($this->rapport_layout == 14)
		{
		  $this->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
		  $this->SetX($this->marge+65);
			$this->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$this->rapport_taal), 0,0,"C");
  		$this->SetX($this->marge+135);
			$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
  		$this->SetX($this->marge+195);
	  	$this->Cell(70,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,1, "C");
	  	$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
		}
		else
		{
		if($this->rapport_VHO_volgorde_beginwaarde == 0)
			$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
		else
			$this->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$this->rapport_taal), 0,0,"C");
		$this->SetX($this->marge+$actueel);
		if($this->rapport_VHO_volgorde_beginwaarde == 0)
			$this->Cell(65,4, vertaalTekst("Gemiddelde historische inkoopprijs",$this->rapport_taal), 0,0,"C");
		else
			$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");

		$this->SetX($this->marge+$resultaat);
		$this->Cell(70,4, vertaalTekst("Rendement",$this->rapport_taal), 0,1, "C");

		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());
		$this->Line(($this->marge+$resultaat),$this->GetY(),$this->marge + $eindresultaat,$this->GetY());
		}

		if($this->rapport_layout == 1)
		{
			$y = $this->getY();
			$this->SetX($this->marge+$resultaat);
			$this->Cell(60,4, vertaalTekst("Absoluut",$this->rapport_taal), 0,0, "C");
			$this->Line(($this->marge+$resultaat),$this->GetY()+4,$this->marge + $eindresultaat2,$this->GetY()+4);
			$this->setY($y);
			$this->setX($this->marge);
		}


		if($this->rapport_VHO_percentageTotaal == 1)
		{
			if($this->rapport_layout == 8)
				$aandeel = "In % totaal";
			else
				$aandeel = "Aandeel op totale waarde";
		}

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);


		$y = $this->getY();

		if($this->rapport_layout == 4)
		{
			$this->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal / Nominaal",$this->rapport_taal),
												vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
												vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
												vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
												"",
												vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
												vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
												vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
												vertaalTekst("In % \ntotaal",$this->rapport_taal),
												vertaalTekst("Koers- \nresultaat",$this->rapport_taal),
												"",
												vertaalTekst("Valuta- \nresultaat",$this->rapport_taal),
												vertaalTekst("In % \ntotaal",$this->rapport_taal)));
		}
		else if($this->rapport_layout == 8)
		{
			$this->row(array("",
												"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												"",
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst($aandeel,$this->rapport_taal),
												vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
												"%",
												vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
												vertaalTekst("in %",$this->rapport_taal)));
		}
		elseif($this->rapport_layout == 14)
		{
	  // voor data
		$this->widthB = array(10,50,18,22,5,22,1,15,22,22,1,10,20,20,15,60);
		$this->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->widthA = array(60,18,15,22,22,1,15,25,22,12,22,15,22,15);
		$this->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);


		$this->row(array("",
												"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("koers in EUR",$this->rapport_taal),
								        "",
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												"",
												vertaalTekst(" ",$this->rapport_taal),
												vertaalTekst("koers in EUR",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												'',
												'',
												vertaalTekst("Aandeel in\n portefeuille",$this->rapport_taal),
												vertaalTekst("Absoluut",$this->rapport_taal),
												vertaalTekst("in %",$this->rapport_taal)));
		}
		elseif($this->rapport_layout == 16)
		{
	  // voor data
		$this->widthB = array(90,15,15,25,25,25,25,20,20,20);
		$this->alignB = array('L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->widthA = $this->widthB;//array(60,18,15,22,22,1,15,25,22,12,22,15,22,15);
		$this->alignA = $this->alignB;//array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);


		$this->row(array(		vertaalTekst("Fonds",$this->rapport_taal),
												vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Valuta",$this->rapport_taal),

												vertaalTekst("Actuele Koers",$this->rapport_taal),
								     		vertaalTekst("Gemiddelde \nKostprijs ",$this->rapport_taal),
												vertaalTekst("Waarde in\nEuro",$this->rapport_taal),
												vertaalTekst("Ongerealiseerd \nWinst/Verlies",$this->rapport_taal),
												vertaalTekst("Perc. \nW/V",$this->rapport_taal),
												vertaalTekst("Opgelopen \nRente",$this->rapport_taal),
												vertaalTekst("Perc. \nPortf.",$this->rapport_taal)));
		}
		else if($this->rapport_layout == 1)
		{
			$this->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
											vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												"",
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst($aandeel,$this->rapport_taal),
												vertaalTekst("\nFonds-\nresultaat",$this->rapport_taal),
												"",
												vertaalTekst("\nValuta-\nresultaat",$this->rapport_taal),
												vertaalTekst("in %",$this->rapport_taal)));
		}
		else
		{
			$this->row(array("",
											"\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
											vertaalTekst("Aantal",$this->rapport_taal),
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												"",
												vertaalTekst("Per stuk in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
												vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
												vertaalTekst($aandeel,$this->rapport_taal),
												vertaalTekst("Fonds-\nresultaat",$this->rapport_taal),
												"",
												vertaalTekst("Valuta-\nresultaat",$this->rapport_taal),
												vertaalTekst("in %",$this->rapport_taal)));
		}
		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		if($this->rapport_layout == 14)
		  $this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		else
		  $this->SetFont($this->rapport_font,'bi',$this->rapport_fontsize);
		$this->setY($y);
		if($this->rapport_layout != 16)
		  $this->row(array("Categorie\n"));
		$this->ln();
		$this->ln();

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }
  
  
  function HeaderFISCAAL()
  {

		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$huidige 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$eindhuidige 	= $this->widthB[0]+$this->widthB[1]+$this->widthB[2]+$this->widthB[3]+$this->widthB[4]+$this->widthB[5];

		$actueel 			= $eindhuidige + $this->widthB[6];
		$eindactueel 	= $actueel + $this->widthB[7] + $this->widthB[8] + $this->widthB[9];

		$resultaat 		= $eindactueel + $this->widthB[10];
		$eindresultaat = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13] +  $this->widthB[14];
		$eindresultaat2 = $resultaat +  $this->widthB[11] +  $this->widthB[12] +  $this->widthB[13] ;

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 12 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
	  $this->SetX($this->marge+$huidige+5);
		$this->Cell(65,4, vertaalTekst("Gemiddelde historische kostprijs",$this->rapport_taal), 0,0,"C");
		$this->SetX($this->marge+$actueel);
  	$this->Cell(65,4, vertaalTekst("Actuele koers",$this->rapport_taal), 0,0, "C");
		$this->SetX($this->marge+$resultaat);
    $this->Ln();
		//$this->Cell(70,4, vertaalTekst("Rendement",$this->rapport_taal), 0,1, "C");
		$this->Line(($this->marge+$huidige+5),$this->GetY(),$this->marge + $eindhuidige,$this->GetY());
		$this->Line(($this->marge+$actueel),$this->GetY(),$this->marge + $eindactueel,$this->GetY());
		//$this->Line(($this->marge+$resultaat),$this->GetY(),$this->marge + $eindresultaat,$this->GetY());
		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$y = $this->getY();
  
		$this->row(array("",
										 "\n".vertaalTekst("Fondsomschrijving",$this->rapport_taal),
										 vertaalTekst("Aantal",$this->rapport_taal),
										 vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										 vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										 vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										 "",
										 vertaalTekst("Per stuk in valuta",$this->rapport_taal),
										 vertaalTekst("Portefeuille in valuta",$this->rapport_taal),
										 vertaalTekst("Portefeuille in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst('',$this->rapport_taal),
										 vertaalTekst("Fiscale\nWaardering",$this->rapport_taal),
										 "",
                     vertaalTekst("Herwaarderings\nreserve",$this->rapport_taal),
                     ''));
	
		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
	  $this->SetFont($this->rapport_font,'bi',$this->rapport_fontsize);
		$this->setY($y);
	  $this->row(array("Categorie\n"));
		$this->ln();
	
		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

  function HeaderVHO_L15()
  {
		  $this->Rect(20, 65, 256, 6, 'DF');
		  $this->SetWidths(array(70,30,35,30,35,55));
		  $this->SetAligns(array('L','C','R','R','R','R'));
	//	  $this->SetAligns(array('L','C','C','C','C','R'));
		   $this->SetFont('arial','B',14);
		  $this->row(array('Belegging','','Inleg','Koers','waarde','kapitaalgarantie'));
		  $this->ln();
  }

  function HeaderTRANS()
  {
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		if($this->rapport_layout ==7 || $this->rapport_layout == 1)
		{
			$y = $this->GetY();
			$this->setY($y-8);
			$this->SetX(110);
			$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
			$this->Write(4,vertaalTekst("Verslagperiode",$this->rapport_taal)." ");
			$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
			$this->Write(4,date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ");
			$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
			$this->Write(4,vertaalTekst("tot en met",$this->rapport_taal)." ");
			$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
			$this->Write(4,date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum)." ");
			$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

			$this->setY($y);
			$this->ln(2);
			$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
			$this->ln();
		}
		elseif($this->rapport_layout == 8 )
		{
		  $this->ln();
		}
		elseif($this->rapport_layout == 17 )
		{
		  $this->SetX(100+$this->marge);
		  $this->Cell(100,4, vertaalTekst("Verslagperiode",$this->rapport_taal)." ".$this->getKwartaal($this->rapport_datum)."e kwartaal",0,1);
		  $this->ln();



		}
		else
		{
			$this->SetX(100);
			$this->MultiCell(100,4,vertaalTekst("Verslagperiode",$this->rapport_taal)." ".date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'C');
			$this->ln();
		}

		// achtergrond kleur

		if($this->rapport_layout != 17)
		{
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 16 , 'F');
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

				// afdrukken header groups
		$inkoop			= $this->marge + $this->widthB[0] + $this->widthB[1] + $this->widthB[2] + $this->widthB[3];
		$inkoopEind = $inkoop + $this->widthB[4] + $this->widthB[5] + $this->widthB[6];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $this->widthB[7] + $this->widthB[8] + $this->widthB[9];

		}
		else
		{

		// afdrukken header groups
		$inkoop			= $this->marge + $this->widthB[0] + $this->widthB[1] + $this->widthB[2] + $this->widthB[3]+$this->widthB[4];
		$inkoopEind = $inkoop + $this->widthB[5] + $this->widthB[6] + $this->widthB[7] + $this->widthB[8];

		$verkoop			= $inkoopEind;
		$verkoopEind = $verkoop + $this->widthB[9] + $this->widthB[10] + $this->widthB[11]+ $this->widthB[12];
		}
		$resultaat			= $verkoopEind;
		$resultaatEind = $this->marge + array_sum($this->widthB);

		if($this->rapport_layout ==7)
		{
			$this->SetX($inkoop);
			$this->Cell(65,4, vertaalTekst("Uitgaven",$this->rapport_taal), 0,0, "C");
			$this->SetX($verkoop);
			$this->Cell(65,4, vertaalTekst("Ontvangsten",$this->rapport_taal), 0,0, "C");
			$this->SetX($resultaat);
			$this->Cell(65,4, vertaalTekst("Resultaat",$this->rapport_taal), 0,0, "C");
			$this->ln();
		}
		else if($this->rapport_layout == 14)
		{

		}
		else if($this->rapport_layout == 17)
		{
		  $backupStyle = $this->lastStyle ;
		  $this->fillCellBackup=$this->fillCell ;

		  $this->switchFont(2);
		  $this->SetX($inkoop);
			$this->Cell($inkoopEind-$inkoop-1,4, vertaalTekst("GEGEVENS INZAKE AANKOOP",$this->rapport_taal), 0,0, "L",1);
			$this->SetX($verkoop);
			$this->Cell($verkoopEind-$verkoop-1,4, vertaalTekst("GEGEVENS INZAKE VERKOOP",$this->rapport_taal), 0,0, "L",1);
			$this->SetX($resultaat);
			$this->Cell($resultaatEind-$resultaat,4, vertaalTekst("RESULTAAT OP TRANSACTIEBASIS",$this->rapport_taal), 0,0, "L",1);
			$this->ln();
		//	$this->switchFont('fonds');


		}
		else
		{// Formaat van de kopcellen dynamisch gemaakt aan de hand van de kolombreedte.
//			echo "$inkoopEind - $inkoop en $verkoopEind - $verkoop en $resultaatEind - $resultaat ";
			$this->SetX($inkoop);
//			$this->Cell(65,4, vertaalTekst("Gegevens inzake aankoop",$this->rapport_taal), 0,0, "C"); //60 ipv 65
			$this->Cell($inkoopEind - $inkoop,4, vertaalTekst("Gegevens inzake aankoop",$this->rapport_taal), 0,0, "C");
			$this->SetX($verkoop);
//			$this->Cell(65,4, vertaalTekst("Gegevens inzake verkoop",$this->rapport_taal), 0,0, "C"); //60 ipv 65
			$this->Cell($verkoopEind - $verkoop,4, vertaalTekst("Gegevens inzake verkoop",$this->rapport_taal), 0,0, "C");
			$this->SetX($resultaat);
//			$this->Cell(65,4, vertaalTekst("Resultaat bepaling",$this->rapport_taal), 0,0, "C"); //81 ipv 65
			$this->Cell($resultaatEind - $resultaat,4, vertaalTekst("Resultaat bepaling",$this->rapport_taal), 0,0, "C");
			$this->ln();
		}
if($this->rapport_layout != 14 && $this->rapport_layout != 17)
{
		$this->Line(($inkoop+2),$this->GetY(),$inkoopEind,$this->GetY());
		$this->Line(($verkoop+2),$this->GetY(),$verkoopEind,$this->GetY());
		$this->Line(($resultaat+2),$this->GetY(),$resultaatEind,$this->GetY());
}

		// bij layout 1 zit het % totaal
		if($this->rapport_TRANS_procent == 1)
			$procentTotaal = "%";

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		if($this->rapport_layout == 4)
		{
			$this->row(array(vertaalTekst("Datum",$this->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$this->rapport_taal),
										 vertaalTekst("Aantal",$this->rapport_taal),
										 vertaalTekst("Fonds",$this->rapport_taal),
										 vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										 vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										 vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
										 vertaalTekst("Koers \n(valuta)",$this->rapport_taal),
										 vertaalTekst("Waarde \n(valuta)",$this->rapport_taal),
										 vertaalTekst("Waarde \n(".$this->rapportageValuta.")",$this->rapport_taal),
										 vertaalTekst("Historische \nkostprijs \n(".$this->rapportageValuta.")",$this->rapport_taal),
										 vertaalTekst("Resultaat \nvoorgaande \njaren",$this->rapport_taal),
										 vertaalTekst("Resultaat \nlopende jaar\n absoluut",$this->rapport_taal),
										 "\n\n".$procentTotaal));
		}
		else if($this->rapport_layout == 7)
		{
			$this->row(array(vertaalTekst("Datum",$this->rapport_taal),
										 vertaalTekst("Soort\ntrans-actie",$this->rapport_taal),
										 vertaalTekst("Aantal",$this->rapport_taal),
										 vertaalTekst("Effect",$this->rapport_taal),
										 vertaalTekst("Koers in valuta",$this->rapport_taal),
										 vertaalTekst("Waarde in valuta",$this->rapport_taal),
										 vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Koers in valuta",$this->rapport_taal),
										 vertaalTekst("Waarde in valuta",$this->rapport_taal),
										 vertaalTekst("Waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Resultaat voorgaande jaren",$this->rapport_taal),
										 vertaalTekst("Resultaat lopend jaar",$this->rapport_taal),
										 $procentTotaal));
		}
		else if($this->rapport_layout == 10)
		{
			$this->row(array(vertaalTekst("Datum",$this->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$this->rapport_taal),
										 vertaalTekst("Aantal",$this->rapport_taal),
										 vertaalTekst("Fonds",$this->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("Resultaat lopende jaar in ".$this->rapportageValuta,$this->rapport_taal),
										 $procentTotaal));
		}
		else if($this->rapport_layout == 14)
		{
			$this->row(array(vertaalTekst("Datum",$this->rapport_taal),
										 vertaalTekst("Transactie",$this->rapport_taal),
										 vertaalTekst("Fonds",$this->rapport_taal),
									 	 vertaalTekst("Aantal",$this->rapport_taal),
									 	 vertaalTekst("Koers in Valuta",$this->rapport_taal),
										 vertaalTekst("Koers in Euro",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("Provisie ",$this->rapport_taal),
										 vertaalTekst("Totaal",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 vertaalTekst("",$this->rapport_taal),
										 $procentTotaal));
		}
		elseif ($this->rapport_layout == 17)
		{
		  $this->SetWidths($this->widthB);
		  $this->SetAligns($this->alignB);
		  $this->switchFont('rapportKop');
		  $this->fillCell = array(1,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1);
		  $this->ln(2);
			$this->row(array(vertaalTekst("Datum \n \n ",$this->rapport_taal),
										 vertaalTekst("Aan/ Verkoop\n ",$this->rapport_taal),
										 vertaalTekst("Aantal \n \n ",$this->rapport_taal),
										 vertaalTekst("Fondsomschrijving \n \n ",$this->rapport_taal),
										 '',
										 vertaalTekst("Aankoop\nkoers in\nvaluta",$this->rapport_taal),
										 vertaalTekst("Aankoop\nwaarde in\nvaluta",$this->rapport_taal),
										 vertaalTekst("Aankoop\nwaarde in\n".$this->rapportageValuta,$this->rapport_taal),
										 '',
										 vertaalTekst("Verkoop\nkoers in\nvaluta",$this->rapport_taal),
										 vertaalTekst("Verkoop\nwaarde in\nvaluta",$this->rapport_taal),
										 vertaalTekst("Verkoop\nwaarde in\n".$this->rapportageValuta,$this->rapport_taal),
										 '',
										 vertaalTekst("Historische\nkostprijs\nin ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Resultaat\n \n ",$this->rapport_taal),

										 $procentTotaal."\n \n "));

										 $this->switchFont($backupStyle);
										 $this->fillCell = $this->fillCellBackup;
		//	$this->switchFont('fonds');

		//  $this->SetWidths($this->widthB);
		//  $this->SetAligns($this->alignB);
		}
		else
		{
			$this->row(array(vertaalTekst("Datum",$this->rapport_taal),
										 vertaalTekst("Aan/ Ver Koop",$this->rapport_taal),
										 vertaalTekst("Aantal",$this->rapport_taal),
										 vertaalTekst("Fonds",$this->rapport_taal),
										 vertaalTekst("Aankoop koers in valuta",$this->rapport_taal),
										 vertaalTekst("Aankoop waarde in valuta",$this->rapport_taal),
										 vertaalTekst("Aankoop waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Verkoop koers in valuta",$this->rapport_taal),
										 vertaalTekst("Verkoop waarde in valuta",$this->rapport_taal),
										 vertaalTekst("Verkoop waarde in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Historische kostprijs in ".$this->rapportageValuta,$this->rapport_taal),
										 vertaalTekst("Resultaat voorafgaand verslagperiode",$this->rapport_taal),
										 vertaalTekst("Resultaat gedurende verslagperiode",$this->rapport_taal),
										 $procentTotaal));
		}
    if ($this->rapport_layout != 17)
    {
	   	$this->SetWidths($this->widthA);
	   	$this->SetAligns($this->alignA);
    	$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
    }
    else
      $this->ln(1);
  }

  function HeaderMUT()
  {

		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		 if ($this->rapport_layout != 8)
		 {
  		$this->SetX(100);
	  	$this->MultiCell(100,4,vertaalTekst("Verslagperiode",$this->rapport_taal)." ".date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'C');
		 }
		  $this->ln();
		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$this->row(array(vertaalTekst("Periode",$this->rapport_taal),
										 vertaalTekst("Bankafschrift",$this->rapport_taal),
										 vertaalTekst("Omschrijving",$this->rapport_taal),
										 vertaalTekst("Boekdatum",$this->rapport_taal),
										 vertaalTekst("Rekening",$this->rapport_taal),
										 "",
										 vertaalTekst("Debet",$this->rapport_taal),
										 vertaalTekst("Credit",$this->rapport_taal),
										 ""));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		$this->ln();
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

  }

  function HeaderMUT2()
  {
		$this->SetX(110);
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
		$this->Write(4,vertaalTekst("Verslagperiode",$this->rapport_taal)." ");
		$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		$this->Write(4,date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ");
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);
		$this->Write(4,vertaalTekst("tot en met",$this->rapport_taal)." ");
		$this->SetFont($this->rapport_font,'b',$this->rapport_fontsize);
		$this->Write(4,date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum)." ");
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$this->ln();

		$this->setX(($this->marge + $this->widthB[0]+ $this->widthB[1]+ $this->widthB[2]));
		//$this->Cell(110,4,vertaalTekst("Inkomsten",$this->rapport_taal),0,1,"C");
		$this->Line(($this->marge + $this->widthB[0]+ $this->widthB[1]+ $this->widthB[2]),$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
		$this->ln(1);
		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);


		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

		$this->row(array(vertaalTekst("Boekdatum",$this->rapport_taal),
										 vertaalTekst("Omschrijving",$this->rapport_taal),
										 vertaalTekst("Uitgaven",$this->rapport_taal),
										 vertaalTekst("Bruto",$this->rapport_taal),
										 vertaalTekst("Provisie",$this->rapport_taal),
										 vertaalTekst("Kosten",$this->rapport_taal),
										 vertaalTekst("Belasting",$this->rapport_taal),
										 vertaalTekst("Netto",$this->rapport_taal)));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
  }


  function HeaderRisico()
  {

		$this->ln();
		$this->SetFont($this->rapport_font,$this->rapport_kop_fontstyle,$this->rapport_fontsize);

		$lijn1 			= $this->widthB[0]+$this->widthB[1]+$this->widthB[2];
		$lijn1eind 	= $lijn1+$this->widthB[3] + $this->widthB[4] + $this->widthB[5] + $this->widthB[6] + $this->widthB[7];

		// achtergrond kleur
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8 , 'F');

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);

		// lijntjes onder beginwaarde in het lopende jaar
		$this->SetX($this->marge+$lijn1+5);
		$this->MultiCell(90,4, "Waarden", 0, "C");

		$this->Line(($this->marge+$lijn1+5),$this->GetY(),$this->marge + $lijn1eind,$this->GetY());

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
		if($this->rapport_OIB_specificatie == 1)
		{
			$this->row(array("Beleggingscategorie",
											"",
										"Valutasoort",
											"in valuta",
											"in EUR",
											"in EUR",
											"Risico %",
											"Risicobedrag"));
		}
		else
		{
			$this->row(array("Beleggingscategorie",
										"",
										"Valutasoort",
											"",
											"",
											"in EUR",
											"Risico %",
											"Risicobedrag"));
		}

		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

    function HeaderATT()
  {
		// achtergrond kleur
		if($this->rapport_layout == 12 )
		{
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge, $this->getY(), array_sum($this->widthB), 8, 'F');
		}

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

		$this->ln(2);



		if($this->rapport_layout == 7 )
		{
		  $this->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$this->rapport_taal),0,0);
			$this->Cell(100,4, date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,0);
		}
		elseif($this->rapport_layout == 17 )
		{
			$this->Cell(100,4, vertaalTekst("Vanaf ",$this->rapport_taal)." 1 ".vertaalTekst($this->__appvar["Maanden"][1],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,0);
		}
		else
		{
		  $this->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$this->rapport_taal),0,0);
			$this->Cell(100,4, vertaalTekst("Verslagperiode",$this->rapport_taal)." ".date("j",$this->rapport_datumvanaf)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datumvanaf)],$this->rapport_taal)." ".date("Y",$this->rapport_datumvanaf)." ".vertaalTekst("tot en met",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,0);
		}

		$this->ln(2);

		$this->SetWidths($this->widthB);
		$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
    $this->fillCell = array();
		$this->row(array("", "", "", "", "", "", "", "", "", ""));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthB),$this->GetY());
  }

  function HeaderCASH()
  {

		$this->ln(2);

	//	$this->SetWidths($this->widthB);
	//	$this->SetAligns($this->alignB);
		$this->SetFont($this->rapport_font,'B',$this->rapport_fontsize);

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
 if($this->debug)
		$this->row(array("Datum","Instrument", "Coupon/lossing", "Bedrag",'jaar','PV','PV*T'));
else
  $this->row(array("Datum","Instrument", "Coupon/lossing", "Bedrag"));
		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthA),$this->GetY());
   
  }

  function HeaderCASHY()
  {

		$this->ln(2);
		$this->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);
    $this->row(array('','Jaar',"Lossing","Rente","Totaal"));

		$this->SetWidths($this->widthA);
		$this->SetAligns($this->alignA);

		$this->Line($this->marge,$this->GetY(),$this->marge + array_sum($this->widthA),$this->GetY());
  }

  function HeaderGRAFIEK()
  {
  	if($this->rapport_layout == 17 )
		{
		  $this->setX(100+$this->marge);
		  $this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		  $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
		  $this->MultiCell(100,6,vertaalTekst("Per",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,'L');
//			$this->Cell(100,4, vertaalTekst("Per",$this->rapport_taal)." ".date("j",$this->rapport_datum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->rapport_datum)],$this->rapport_taal)." ".date("Y",$this->rapport_datum),0,1);
		$this->SetDrawColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->Line($this->marge,$this->GetY()+1,$this->marge + 280,$this->GetY()+1);
		}
  }

  function HeaderSMV()
  {
     $this->ln();
     $this->SetFont($this->rapport_font,'B',$this->rapport_fontsize);
     $this->Row(array('Boekdatum','Saldo','Bedrag','C/D','GB','Omschrijving'));
     $this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
  }

  function HeaderMODEL()
  {

    	$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Modelcontrole", $this->rapport_taal) ,0,1,"L");
				$this->SetX(250);

				$this->SetFont("Times","b",10);
				$this->SetX($this->marge);
				//rij 3
				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Controledatum: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, date("j",$this->selectData['datumTm'])." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->selectData[datumTm]),0,1,"L");

				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Modelportefeuille: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, $this->selectData['modelcontrole_portefeuille'],0,1,"L");
				$this->SetFont("Times","b",10);

		if($this->selectData['modelcontrole_rapport'] == "vastbedrag")
		{
			$extraTekst =" Vast bedrag: ".$this->selectData['modelcontrole_vastbedrag'];
		}
		elseif($this->selectData["modelcontrole_filter"] != "gekoppeld")
		{
			$extraTekst = " : niet gekoppeld depot";
		}
		else
			$extraTekst = "";

					$this->Cell(70,4, "Client: ",0,0,"R");
					$this->SetFont("Times","",10);
					$this->Cell(50,4, $this->clientOmschrijving,0,1,"L");

					$this->SetFont("Times","b",10);
					$this->Cell(70,4, "Naam: ",0,0,"R");
					$this->SetFont("Times","",10);
					$this->Cell(50,4, $this->naamOmschrijving.$extraTekst,0,1,"L");

				

				$this->ln();
	     	$this->SetWidths(array(60,20,20,20,25,25,25,30,25,25));
	    	$this->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
		    $this->Row(array("Fonds",
								 "Model Percentage",
								 "Werkelijk Percentage",
								 "Afwijkings Percentage",
								 "Afwijkings in EUR",
								 "Kopen",
								 "Verkopen",
								 "Waarde volgens model",
								 "Koers in locale valuta",
								 "Geschat orderbedrag"));

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);

  }

	function HeaderWaardePrognose()
	{
		$this->setY(10);

		$this->SetFont($this->rapport_font,'B',16);
		
		$this->SetX($this->marge);
		$this->Cell(200,8, vertaalTekst("Waardeprognose", $this->rapport_taal) ,0,1,"L");
		$this->SetX(250);

		/*



  $this->ln();
  $this->SetWidths(array(60,20,20,20,25,25,25,30,25,25));
  $this->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
  $this->Row(array("Fonds",
               "Model Percentage",
               "Werkelijk Percentage",
               "Afwijkings Percentage",
               "Afwijkings in EUR",
               "Kopen",
               "Verkopen",
               "Waarde volgens model",
               "Koers in locale valuta",
               "Geschat orderbedrag"));
  */
		$this->ln(10);
		$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());

		$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);

	}

  function HeaderRISK()
  {

  }


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function printValutaoverzicht($portefeuille, $rapportageDatum,$omkeren=false,$NlgOnderdrukken=false)
  {
 		global $__appvar;
    $extra='';
    if($NlgOnderdrukken==true)
      $extra="AND TijdelijkeRapportage.valuta <> 'NLG'";
		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ".
		" TijdelijkeRapportage.valuta <> '".$this->rapportageValuta."' $extra AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
		  $this->ln();
		  $this->ln();
			$t=0;
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$t] = $valuta;
				$t++;
			}

      $regels = ceil((count($valutas)));
			if(count($valutas) > 4)
			{
				$regels = ceil((count($valutas) / 2));
			}
  		$hoogte = ($regels * 4) + 4;
	  	if(($this->GetY() + $hoogte) > $this->pagebreak)
			{
				$this->AddPage();
				$this->ln();
			}

			$kop = "Actuele koersen";

			if($this->rapport_layout == 1 || $this->rapport_layout == 17)
			{
				$kop = "Valuta koersen";
			}

			$this->SetTextColor($this->rapport_kop4_fontcolor['r'],$this->rapport_kop4_fontcolor['g'],$this->rapport_kop4_fontcolor['b']);
			$this->SetFont($this->rapport_kop4_font,$this->rapport_kop4_fontstyle,$this->rapport_kop4_fontsize);
			$this->Cell($this->widthB[1],4, vertaalTekst($kop,$this->rapport_taal), 0,1, "L");

			$plusmarge = 0;

			$y = $this->getY();
			$start = false;
			//while ($valuta = $DB->NextRecord())
			for($a=0; $a < count($valutas); $a++)
			{
				if($this->rapport_valutaoverzicht_rev)
				{
					if($valutas[$a]['actueleValuta'] <> 0 )
					$valutas[$a]['actueleValuta'] = 1 / $valutas[$a]['actueleValuta'];
				}

				if(count($valutas) > 4)
				{
					if($a >= $regels && $start == false)
					{
						$y2 = $this->getY();
						$this->setY($y);
						$plusmarge = 60;
						$start = true;
					}
				}

				$this->SetX($this->marge+$plusmarge);
				$this->SetFont($this->rapport_font,$this->rapport_fontstyle,$this->rapport_fontsize);
				$this->SetTextColor($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['g'],$this->rapport_fonds_fontcolor['b']);
				$this->Cell(35,4, vertaalTekst($valutas[$a]['ValutaOmschrijving'],$this->rapport_taal), 0,0, "L");
				$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);
				$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);


				if($this->ValutaKoersEind > 0)
				  $valutas[$a]['actueleValuta'] = $valutas[$a]['actueleValuta'] / $this->ValutaKoersEind ; 

        if($omkeren==true)
          $this->Cell(20,4, $this->formatGetal(1/$valutas[$a]['actueleValuta'],4), 0,1, "R");
        else
			  	$this->Cell(20,4, $this->formatGetal($valutas[$a]['actueleValuta'],4), 0,1, "R");

			}

			if($start == true)
				$this->setY($y2);
		}

  }

    function printValutaPerformanceOverzicht($portefeuille, $rapportageDatum, $rapportageDatumVanaf,$omkeren=false,$kop='Valuta')
  {
  	global $__appvar;
		$this->ln();

	 $metJanuari = $this->rapport_valutaPerformanceJanuari;

	 if($metJanuari == true)
	 {
	   $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	   $rapJaar = date('Y',$julRapDatumVanaf);
	   $dagMaand = date('d-m',$julRapDatumVanaf);
	   $januariDatum = $rapJaar.'-01-01';
	   if($dagMaand =='01-01')
       $metJanuari = false;
	 }

	 if($this->printValutaPerformanceOverzichtProcentTeken)
	   $teken = '%';
   else
     $teken = '';

		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta,  TijdelijkeRapportage.rapportageDatum".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ". //OR TijdelijkeRapportage.rapportageDatum = '".$rapportageDatumVanaf."' )
		" TijdelijkeRapportage.valuta <> '".$this->rapportageValuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc, TijdelijkeRapportage.rapportageDatum";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$valuta['val']][$valuta['rapportageDatum']]['omschrijving'] = $valuta['ValutaOmschrijving'];
				$valutas[$valuta['val']][$valuta['rapportageDatum']]['koers'] = $valuta['actueleValuta'] / $this->ValutaKoersEind;
			}

			$valutaKeys = array_keys($valutas);
      foreach ($valutaKeys as $valuta)
      {
       $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
               FROM Valutas ,Valutakoersen
               WHERE Valutas.valuta = Valutakoersen.valuta AND
               Valutakoersen.datum <= date '".$rapportageDatumVanaf."' AND
               Valutas.valuta = '".$valuta."'
               ORDER BY Valutakoersen.datum desc LIMIT 1";
       $DB->SQL($query);
       $DB->Query();
       $valutawaarden = $DB->NextRecord();

       $valutas[$valuta][$rapportageDatumVanaf]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
			 $valutas[$valuta][$rapportageDatumVanaf]['koers'] = $valutawaarden['Koers'] / $this->ValutaKoersBegin;

			 if($metJanuari == true)
			 {
			   $query="SELECT Valutas.Omschrijving AS ValutaOmschrijving, Valutakoersen.Koers
                 FROM Valutas ,Valutakoersen
                 WHERE Valutas.valuta = Valutakoersen.valuta AND
                 Valutakoersen.datum <= date '$januariDatum' AND
                 Valutas.valuta = '".$valuta."'
                 ORDER BY Valutakoersen.datum desc LIMIT 1";
         $DB->SQL($query);
         $DB->Query();
         $valutawaarden = $DB->NextRecord();

         $valutas[$valuta][$januariDatum]['omschrijving'] = $valutawaarden['ValutaOmschrijving'];
			   $valutas[$valuta][$januariDatum]['koers'] = $valutawaarden['Koers'] / $this->ValutaKoersStart;
			   $extraBreedte = 50;
			 }
      }
	//listarray($valutas);
		//$kop = "Valuta";

		$regels = count($valutas);
		$hoogte = ($regels * 4) + 8;
		if(($this->GetY() + $hoogte) > $this->pagebreak)
		{
			$this->AddPage();
			$this->ln();
		}

		$this->ln();
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge,$this->getY(),110+$extraBreedte,$hoogte,'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge,$this->getY(),110+$extraBreedte,$hoogte);
		$this->SetX($this->marge);

		// kopfontcolor
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->SetFont($this->rapport_kop4_font,$this->rapport_kop4_fontstyle,$this->rapport_kop4_fontsize);
		$this->Cell(40,4, vertaalTekst($kop,$this->rapport_taal), 0,0, "L");

		$this->SetFont($this->rapport_font,$this->rapport_fontstyle,$this->rapport_fontsize);
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		if($metJanuari == true)
			$this->Cell(23,4, date("d-m-Y",db2jul($januariDatum)), 0,0, "R");
		$this->Cell(23,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), 0,0, "R");
		$this->Cell(23,4, date("d-m-Y",db2jul($rapportageDatum)), 0,0, "R");
		if($metJanuari == true)
		{
		  $this->Cell(23,4, vertaalTekst("Performance",$this->rapport_taal), 0,0, "R");
			$this->Cell(23,4, vertaalTekst("Jaar Perf.",$this->rapport_taal), 0,1, "R");
		}
		else
			$this->Cell(23,4, vertaalTekst("Performance",$this->rapport_taal), 0,1, "R");



		while (list($key, $data) = each($valutas))
		{
			$performance = ($data[$rapportageDatum]['koers'] - $data[$rapportageDatumVanaf]['koers']) / ($data[$rapportageDatumVanaf]['koers']/100 );
//echo 		"	$performance = (".$data[$rapportageDatum]['koers']." - ".$data[$rapportageDatumVanaf]['koers'].") / (".$data[$rapportageDatumVanaf]['koers']."/100 );";
			$this->Cell(40,4, vertaalTekst($data[$rapportageDatumVanaf]['omschrijving'],$this->rapport_taal), 0,0, "L");
			if($metJanuari == true)
			{
			  if($omkeren==true)
			    $this->Cell(23,4, $this->formatGetal(1/$data[$januariDatum]['koers'],4), 0,0, "R");
			  else
			  	$this->Cell(23,4, $this->formatGetal($data[$januariDatum]['koers'],4), 0,0, "R");
			}
			if($omkeren==true)
			  $this->Cell(23,4, $this->formatGetal(1/$data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			else
			  $this->Cell(23,4, $this->formatGetal($data[$rapportageDatumVanaf]['koers'],4), 0,0, "R");
			if($omkeren==true)
			  $this->Cell(23,4, $this->formatGetal(1/$data[$rapportageDatum]['koers'],4), 0,0, "R");
			else
			  $this->Cell(23,4, $this->formatGetal($data[$rapportageDatum]['koers'],4), 0,0, "R");
			if($metJanuari == true)
			{
			  $this->Cell(23,4, $this->formatGetal($performance,2).$teken, 0,0, "R");
			  $performanceJaar = ($data[$rapportageDatum]['koers'] - $data[$januariDatum]['koers']) / ($data[$januariDatum]['koers']/100 );
			  $this->Cell(23,4, $this->formatGetal($performanceJaar,2).$teken, 0,1, "R");
			}
			else
			  $this->Cell(23,4, $this->formatGetal($performance,2).$teken, 0,1, "R");
		}
		$this->ln();
		$this->ln();
		}
  }

  function bepaalRisicoWaarde($portefeuille,$rapportageDatum)
  {
    global $__appvar;
    $query = "SELECT SUM(BeleggingscategoriePerFonds.RisicoPercentageFonds * ABS(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / 100) as risicoWaarde
              FROM Portefeuilles,BeleggingscategoriePerFonds ,TijdelijkeRapportage
              WHERE
              Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder AND
              TijdelijkeRapportage.Fonds = BeleggingscategoriePerFonds.Fonds  AND
              Portefeuilles.Portefeuille = TijdelijkeRapportage.Portefeuille AND
              TijdelijkeRapportage.Portefeuille = '$portefeuille' AND TijdelijkeRapportage.type = 'fondsen' AND
              TijdelijkeRapportage.rapportageDatum = '$rapportageDatum'
              ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $db = new DB();
		$db->SQL($query);
		$db->Query();
		$data = $db->lookupRecord();
		return $data['risicoWaarde'];
  }


  function printRisico($portefeuille, $risicoTotaal, $actueleWaardePortefeuille,$small=false)
  {
//echo "$portefeuille, $risicoTotaal, $actueleWaardePortefeuille <br>";

  if ($small == true)
  {
    $cellw1=48;
    $cellw2=30;
    $extraMarge=7;
  }
  else
  {
    $cellw1=80;
    $cellw2=30;
    $extraMarge=0;
  }


		$query = "SELECT  ".
		" Risicoklassen.Risicoklasse, ".
		" Risicoklassen.Minimaal, ".
		" Risicoklassen.Maximaal ".
		" FROM Risicoklassen, Portefeuilles WHERE ".
		" Risicoklassen.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
		" Portefeuilles.Portefeuille = '".$portefeuille."' AND ".
		" Portefeuilles.Risicoklasse = Risicoklassen.Risicoklasse " ;

		$db = new DB();
		$db->SQL($query);
		$db->Query();
		$risicodata = $db->nextRecord();

		$risicoScore = $risicoTotaal / ($actueleWaardePortefeuille/100);
//echo "$risicoScore = $risicoTotaal / ($actueleWaardePortefeuille/100);";
		$this->ln(2);
		if(($this->GetY() + 22) >= $this->pagebreak) {
			$this->AddPage();
			$this->ln();
		}

		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		//$this->SetX($this->marge + $this->widthB[0]);
		$this->Rect($this->marge+$extraMarge,$this->getY(),$cellw1+$cellw2,16,'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge+$extraMarge,$this->getY(),$cellw1+$cellw2,16);
		$this->ln(2);
		//$this->SetX($this->marge);
		$this->SetX($this->marge+$extraMarge);
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->Cell($cellw1,4, vertaalTekst("Risico range ",$this->rapport_taal), 0,0, "L");
		$this->Cell($cellw2,4, "min. ".$risicodata[Minimaal]." max. ".$risicodata[Maximaal], 0,1, "R");
	  $this->ln();

		$this->SetX($this->marge+$extraMarge);
		$this->Cell($cellw1,4, vertaalTekst("Risico score ",$this->rapport_taal), 0,0, "L");
		$this->Cell($cellw2,4, $this->formatGetal($risicoScore,2), 0,1, "R");
		$this->ln(2);
  }

  function printRendement($portefeuille, $rapportageDatum, $rapportageDatumVanaf, $kort=false)
  {
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
		$vergelijkWaarde = $vergelijkWaarde['totaal'] /  getValutaKoers($this->rapportageValuta,$rapportageDatumVanaf);

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
    	debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille = $DB->nextRecord();
		$actueleWaardePortefeuille = $actueleWaardePortefeuille[totaal]  / $this->ValutaKoersEind;

		$resultaat = ($actueleWaardePortefeuille -
									$vergelijkWaarde -
									getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->rapportageValuta) +
									getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->rapportageValuta)
									);

		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->portefeuilledata['PerformanceBerekening'],$this->rapportageValuta);

		$this->ln(2);

		if($kort)
			$min = 8;

		if(($this->GetY() + 22 - $min) >= $this->pagebreak) {
			$this->AddPage();
			$this->ln();
		}

		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		//$this->SetX($this->marge + $this->widthB[0]);
		$this->Rect($this->marge,$this->getY(),110,(16-$min),'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge,$this->getY(),110,(16-$min));
		$this->ln(2);
		//$this->SetX($this->marge);
		$this->SetX($this->marge);

		// kopfontcolor
		if(!$kort)
		{
			$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
      if ($this->rapport_resultaatText)
		    $this->Cell(80,4, vertaalTekst($this->rapport_resultaatText,$this->rapport_taal), 0,0, "L");
		  else
			  $this->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$this->rapport_taal), 0,0, "L");
			$this->Cell(30,4, $this->formatGetal($resultaat,2), 0,1, "R");
			$this->ln();
		}
		$this->SetX($this->marge);
		if ($this->rapport_rendementText)
		  $this->Cell(80,4, vertaalTekst($this->rapport_rendementText,$this->rapport_taal), 0,0, "L");
		else
		  $this->Cell(80,4, vertaalTekst("Rendement lopende kalenderjaar",$this->rapport_taal), 0,0, "L");
		$this->Cell(30,4, $this->formatGetal($performance,2)."%", 0,1, "R");
		$this->ln(2);
  }

  function bepaalValutaKoersen($portefeuille,$rapportageDatum,$valuta='EUR')
  {
    		// selecteer distinct valuta.
		$q = "SELECT DISTINCT(TijdelijkeRapportage.valuta) AS val, Valutas.Omschrijving AS ValutaOmschrijving, TijdelijkeRapportage.actueleValuta".
		" FROM TijdelijkeRapportage, Valutas ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND ".
		" TijdelijkeRapportage.valuta <> '".$valuta."' AND ".
		" TijdelijkeRapportage.valuta = Valutas.Valuta "
		 .$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($q,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		if($DB->records() > 0)
		{
			$t=0;
			while ($valuta = $DB->NextRecord())
			{
				$valutas[$t] = $valuta;
				$t++;
			}

			for($a=0; $a < count($valutas); $a++)
			{
				if($this->rapport_valutaoverzicht_rev)
				{
					if($valutas[$a][actueleValuta] <> 0 )
					$valutas[$a][actueleValuta] = 1 / $valutas[$a][actueleValuta];
				}
			}
		}
		return $valutas;
  }


  function printSamenstellingResultaat($portefeuille, $rapportageDatum, $rapportageDatumVanaf,$valuta = 'EUR')
  {
  		global $__appvar;


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
		$vergelijkWaarde = $vergelijkWaarde[totaal] /  getValutaKoers($this->rapportageValuta,$rapportageDatumVanaf);

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
    	debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$actueleWaardePortefeuille = $DB->nextRecord();
		$actueleWaardePortefeuille = $actueleWaardePortefeuille[totaal]  / $this->ValutaKoersEind;

		$resultaat = ($actueleWaardePortefeuille -
									$vergelijkWaarde -
									getStortingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->rapportageValuta) +
									getOnttrekkingen($portefeuille,$rapportageDatumVanaf,$rapportageDatum,$this->rapportageValuta)
									);

		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $this->portefeuilledata['PerformanceBerekening'],$this->rapportageValuta);

		$this->ln(2);

		if($kort)
			$min = 8;

		if(($this->GetY() + 22 - $min) >= $this->pagebreak) {
			$this->AddPage();
			$this->ln();
		}
			$begin = $this->GetY();

		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		//$this->SetX($this->marge + $this->widthB[0]);
		$this->Rect($this->marge,$this->getY(),110,(16-$min),'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge,$this->getY(),110,(16-$min));
		$this->ln(2);
		//$this->SetX($this->marge);
		$this->SetX($this->marge);


		// kopfontcolor
		if(!$kort)
		{
			$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
			$this->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$this->rapport_taal), 0,0, "L");
			$this->Cell(30,4, $this->formatGetal($resultaat,2), 0,1, "R");
			$this->ln();
		}
		$this->SetX($this->marge);
		if ($this->rapport_rendementText)
		  $this->Cell(80,4, vertaalTekst($this->rapport_rendementText,$this->rapport_taal), 0,0, "L");
		else
		  $this->Cell(80,4, vertaalTekst("Rendement lopende kalenderjaar",$this->rapport_taal), 0,0, "L");
		$this->Cell(30,4, $this->formatGetal($performance,2)."%", 0,1, "R");
		$this->ln(2);
    $eind = $this->GetY();

		$valutas = $this->bepaalValutaKoersen($portefeuille,$rapportageDatum,$valuta);
	  $this->setY($begin);
		if(count($valutas) > 4)
		{
			$regels = ceil((count($valutas) / 2));
		}
		else
		  $regels = count($valutas);


		$hoogte = ($regels * 5) + 5;
		if(($this->GetY() + $hoogte) > $this->pagebreak)
		{
			$this->AddPage();
			$this->ln();
		}
		$tweedeCol = 170;
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge+$tweedeCol,$this->getY(),110,$hoogte,'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge+$tweedeCol,$this->getY(),110,$hoogte);

			$kop = "Actuele koersen";

					$this->ln(2);
		$this->SetX($this->marge+$tweedeCol);

		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
			$this->SetFont($this->rapport_kop4_font,$this->rapport_kop4_fontstyle,$this->rapport_kop4_fontsize);
			$this->Cell(100,4, vertaalTekst($kop,$this->rapport_taal), 0,1, "L");

			$plusmarge = 0;
			$y = $this->getY();
			$start = false;
			for($a=0; $a < count($valutas); $a++)
			{
				if(count($valutas) > 4)
				{
					if($a >= $regels && $start == false)
					{
						$y2 = $this->getY();
						$this->setY($y);
						$plusmarge = 60;
						$start = true;
					}
				}
				$this->SetX($this->marge+$tweedeCol+$plusmarge);
				$this->SetFont($this->rapport_font,$this->rapport_fontstyle,$this->rapport_fontsize);

				if($this->portefeuilledata['Layout'] == 8)
				  $celWidth=30;
				else
			    $celWidth=35;

				$this->Cell($celWidth,4, vertaalTekst($valutas[$a][ValutaOmschrijving],$this->rapport_taal), 0,0, "L");
  			$this->SetFont($this->rapport_font,'',$this->rapport_fontsize);
				$this->Cell(20,4, $this->formatGetal($valutas[$a][actueleValuta],4), 0,1, "R");
			}

		$this->SetY($eind);

		//$valutas
  }

	function printAEXVergelijking($vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  $query = "SELECT Indices.Beursindex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($this->GetY() + $hoogte) > $this->pagebreak)
		{
			$this->AddPage();
			$this->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($this->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $this->rapport_perfIndexJanuari = false;
	  }
		if($this->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($this->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($this->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($this->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$this->ln();
		$this->SetFillColor($this->rapport_kop_bgcolor['r'],$this->rapport_kop_bgcolor['g'],$this->rapport_kop_bgcolor['b']);
		$this->Rect($this->marge,$this->getY(),110+9+$extraX,$hoogte,'F');
		$this->SetFillColor(0);
		$this->Rect($this->marge,$this->getY(),110+9+$extraX,$hoogte);
		$this->SetX($this->marge);

		// kopfontcolor
		//$this->SetTextColor($this->rapport_kop4_fontcolor['r'],$this->rapport_kop4_fontcolor['g'],$this->rapport_kop4_fontcolor['b']);
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		$this->SetFont($this->rapport_kop4_font,$this->rapport_kop4_fontstyle,$this->rapport_kop4_fontsize);
		$this->Cell(40,4, vertaalTekst("Index-vergelijking",$this->rapport_taal), 0,0, "L");

		$this->SetFont($this->rapport_font,$this->rapport_fontstyle,$this->rapport_fontsize);
		//$this->SetTextColor($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['g'],$this->rapport_fonds_fontcolor['b']);
		$this->SetTextColor($this->rapport_kop_fontcolor['r'],$this->rapport_kop_fontcolor['g'],$this->rapport_kop_fontcolor['b']);
		if($this->rapport_perfIndexJanuari == true)
			$this->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$this->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$this->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");

		if($this->portefeuilledata['Layout']==30 || $this->portefeuilledata['Layout']==14 || $this->portefeuilledata['Layout']==25)
		  $this->Cell(26,4, vertaalTekst("Perf in %",$this->rapport_taal), $border,$perfVal, "R");
		else
	  	$this->Cell(26,4, vertaalTekst("Performance in %",$this->rapport_taal), $border,$perfVal, "R");
		if($this->rapport_printAEXVergelijkingEur == 1)
		  $this->Cell(26,4, vertaalTekst("Perf in % in EUR",$this->rapport_taal), $border,$perfEur, "R");
		if($this->rapport_perfIndexJanuari == true)
			$this->Cell(26,4, vertaalTekst("Jaar Perf.",$this->rapport_taal), $border,$perfJan, "R");

		while($perf = $DB->nextRecord())
		{
		  if($perf['Valuta'] != 'EUR')
		  {
		    if($this->rapport_perfIndexJanuari == true)
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

		  if($this->rapport_perfIndexJanuari == true)
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
			$this->Cell(40,4, $perf[Omschrijving], $border,0, "L");
		  if($this->rapport_perfIndexJanuari == true)
		     $this->Cell(26,4, $this->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->Cell(26,4, $this->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->Cell(26,4, $this->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->Cell(26,4, $this->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->rapport_printAEXVergelijkingEur == 1)
		    $this->Cell(26,4, $this->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->rapport_perfIndexJanuari == true)
		    $this->Cell(26,4, $this->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}

		$query2 = "SELECT Portefeuilles.SpecifiekeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Portefeuilles, Fondsen WHERE Portefeuilles.SpecifiekeIndex = Fondsen.Fonds AND Portefeuilles.Portefeuille = '". $this->rapport_portefeuille."' ";
		$DB->SQL($query2);
		$DB->Query();

		while($perf = $DB->nextRecord())
		{

		  if($perf['Valuta'] != 'EUR')
		  {

		    if($this->rapport_perfIndexJanuari == true)
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

		  	if($this->rapport_perfIndexJanuari == true)
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


			$this->Cell(40,4, $perf[Omschrijving], 0,0, "L");
			if($this->rapport_perfIndexJanuari == true)
		     $this->Cell(26,4, $this->formatGetal($koers0[Koers],2), $border,0, "R");
			$this->Cell(26,4, $this->formatGetal($koers1[Koers],2), $border,0, "R");
			$this->Cell(26,4, $this->formatGetal($koers2[Koers],2), $border,0, "R");
		  $this->Cell(26,4, $this->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($this->rapport_printAEXVergelijkingEur == 1)
		    $this->Cell(26,4, $this->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($this->rapport_perfIndexJanuari == true)
		    $this->Cell(26,4, $this->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");
		}
	}

	function printPie($pieData,$kleurdata)
	{



		// default colors
		// custom maken zet de kleuren in config/rapportage.php , en laad deze hier als ze bestaand, anders deze als default .
		if (is_array($this->customPieColors))
		{
		  $col1=$this->customPieColors["col1"];
		  $col2=$this->customPieColors["col2"];
		  $col3=$this->customPieColors["col3"];
		  $col4=$this->customPieColors["col4"];
		  $col5=$this->customPieColors["col5"];
		  $col6=$this->customPieColors["col6"];
		  $col7=$this->customPieColors["col7"];
		  $col8=$this->customPieColors["col8"];
		  $col9=$this->customPieColors["col9"];
		  $col0=$this->customPieColors["col0"];
		  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		}
		else
		{
		  $col1=array(255,0,0); // rood
		  $col2=array(0,255,0); // groen
		  $col3=array(255,128,0); // oranje
		  $col4=array(0,0,255); // blauw
		  $col5=array(255,255,0); // geel
		  $col6=array(255,0,255); // paars
		  $col7=array(128,128,128); // grijs
		  $col8=array(128,64,64); // bruin
		  $col9=array(255,255,255); // wit
		  $col0=array(0,0,0); //zwart
		  $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
		}

// standaardkleuren vervangen voor eigen kleuren.

		if($kleurdata)
		{
		  if(!$this->rapport_dontsortpie)
		  {
   			 $sorted 		= array();
   			 $percentages 	= array();
   			 $kleur			= array();
   			 $valuta 		= array();

  			while (list($key, $data) = each($kleurdata))
   			{
   			  $percentages[] 	= $data[percentage];
   			  $kleur[] 			= $data[kleur];
   			  $valuta[] 		= $key;
   			}
   			arsort($percentages);

   			while (list($key, $percentage) = each($percentages))
   			{
   			  $sorted[$valuta[$key]]['kleur']=$kleur[$key];
   			  $sorted[$valuta[$key]]['percentage']=$percentage;
   			}
			$kleurdata = $sorted; //columnSort($kleurdata, 'pecentage');
		  }

		  $pieData=array();
		  $grafiekKleuren = array();

		  $a=0;
		  while (list($key, $value) = each($kleurdata))
			{
			if ($value['kleur']['R']['value'] == 0 && $value['kleur']['G']['value'] == 0 && $value['kleur']['B']['value'] == 0)
			  {
			  $grafiekKleuren[]=$standaardKleuren[$a];
			  }
			else
			  {
			  $grafiekKleuren[] = array($value['kleur']['R']['value'],$value['kleur']['G']['value'],$value['kleur']['B']['value']);
			  }
			$pieData[$key] = $value[percentage];
			$a++;
			}
		}
		else
		  $grafiekKleuren = $standaardKleuren;

		$this->SetTextColor($this->rapport_fontcolor['r'],$this->rapport_fontcolor['g'],$this->rapport_fontcolor['b']);

		$this->rapport_printpie = true;

		while (list($key, $value) = each($pieData))
		{
			if ($value < 0)
			{
				if($this->rapport_layout == 8 || $this->rapport_layout == 10 )
					$pieData[$key] = -1 * $value;
				else
					$this->rapport_printpie = false;
			}
		}

		if($this->rapport_printpie)
		{
	//		if(!$this->rapport_dontsortpie)
	//		{
	//			asort($pieData, SORT_NUMERIC);
	//			$pieData = array_reverse($pieData,true);
	//		}
			$this->SetXY(210, $this->headerStart);
			$y = $this->getY();
			$this->SetFont($this->pdf->rapport_font,'b',10);
			$this->Cell(50,4,vertaalTekst($this->rapport_titel, $this->rapport_taal),0,1,"C");
			$this->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->SetX(210);
			$this->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
			$hoogte = ($this->getY() - $y) + 8;
			$this->setY($y);

			$this->SetLineWidth($this->lineWidth);

			if($this->rapport_type == "OIB")
			{
				$this->Rect(175,$this->getY(),113,$hoogte);
			}
			else
			{
				$this->Rect(190,$this->getY(),90,$hoogte);
			}
		}
	}

	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}

	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=$this->rowHeight*$nb;
	    //Issue a page break first if needed
	    if($this->AutoPageBreak)
	      $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        //$lines = $this->NbLines($this->widths[$i],$data[$i]);
	        // fill lines
	        if(is_array($this->CellFontStyle[$i]))
	        {
 	          $this->SetFont($this->CellFontStyle[$i][0],$this->CellFontStyle[$i][1],$this->CellFontStyle[$i][2]);
          }

	        if(is_array($this->CellFontColor[$i]))
	        {
	          $oldColor=$this->TextColor;
	          $this->SetTextColor($this->CellFontColor[$i]['r'],$this->CellFontColor[$i]['g'],$this->CellFontColor[$i]['b']);
	        }

	        $this->MultiCell($w,$this->rowHeight,$data[$i],false,$a,$this->fillCell[$i]);

	        if($this->CellBorders[$i])
	        {
	          $borders = array();
	          if(is_array($this->CellBorders[$i]))
	            $borders = $this->CellBorders[$i];
	          else
	            $borders[] = $this->CellBorders[$i];

	          foreach ($borders as $border)
	          {
	            if(isset($this->underlinePercentage) && $this->underlinePercentage != 1)
	              $shrink = $w-$w*$this->underlinePercentage;
	            else
	              $shrink=0;
	            if($border == 'U')
	              $this->Line($x,$y+$h,$x+$w,$y+$h);
	            elseif($border == 'US')
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	            elseif($border == 'SUB')
	            {
	              $this->Line($x+$shrink,$y,$x+$w,$y);
	              $this->setDash(1,1);
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	              $this->setDash();
	            }
	            elseif($border == 'T')
	              $this->Line($x,$y,$x+$w,$y);
	            elseif($border == 'TS')
	              $this->Line($x+$shrink,$y,$x+$w,$y);
	            elseif($border == 'L')
	              $this->Line($x,$y,$x,$y+$h);
              elseif($border == 'LU')
	              $this->Line($x,$y+$h,$x,$y+$h+1);  
	            elseif($border == 'R')
	              $this->Line($x+$w,$y,$x+$w,$y+$h);
	            elseif($border == 'RU')
	              $this->Line($x+$w,$y+$h,$x+$w,$y+$h+1);                
	            elseif($border == 'UU')
	            {
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	              $this->Line($x+$shrink,$y+$h+1,$x+$w,$y+$h+1);
	            }
	          }
	        }
	        if($this->CellDot[$i])
	        {
             $this->Circle($x+$w*.5,$y+$h*.5*.9,$h*.5*.9,0,360,'DF','','');
	        }

	        if(is_array($this->CellFontColor[$i]))
	          $this->TextColor=$oldColor;

         //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}

	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

  function setDash($black=false,$white=false)
  {
      if($black and $white)
          $s=sprintf('[%.3f %.3f] 0 d',$black*$this->k,$white*$this->k);
      else
          $s='[] 0 d';
      $this->_out($s);
  }

  function PieChart($w, $h, $data, $format, $colors=null)
  {

      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->GetX();
      $YPage = $this->GetY();
      $margin = 2;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend , $h - $margin * 2); //
      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;
      if($colors == null) {
          for($i = 0;$i < $this->NbVal; $i++) {
              $gray = $i * intval(255 / $this->NbVal);
              $colors[$i] = array($gray,$gray,$gray);
          }
      }

      //Sectors
      $this->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      foreach($data as $val) {
          $angle = floor(($val * 360) / doubleval($this->sum));
          if ($angle != 0) {
              $angleEnd = $angleStart + $angle;
              $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
              $this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
              $angleStart += $angle;
          }
          $i++;
      }
      if ($angleEnd != 360) {
          $this->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
      }

      //Legends
      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);

      $x1 = $XPage ;
      $x2 = $x1 + $hLegend + $margin - 12;
      $y1 = $YDiag + ($radius) + $margin;

      for($i=0; $i<$this->NbVal; $i++) {
          $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->Rect($x1-12, $y1, $hLegend, $hLegend, 'DF');
          $this->SetXY($x2,$y1);
          $this->Cell(0,$hLegend,$this->legends[$i]);
          $y1+=$hLegend + $margin;
      }

  }

  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['Datum'];
      $data = $data['Index'];

      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->GetX();
      $YPage = $this->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
      {
        $maxVal = ceil(max($data));
      }
      $minVal = floor(min($data));


      $minVal = $minVal * 2;
      $maxVal = $maxVal * 2;

      if ($maxVal <0)
       $maxVal=0;

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

//      $this->Line($XstartGrafiek, $YstartGrafiek + $nulYpos, $XstartGrafiek + $bGrafiek ,$YstartGrafiek + $nulYpos,array('dash' => 1,'color'=>array(0,0,0)));

      $horDiv = 10;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->SetFont($this->rapport_font, '', 6);
      $this->SetTextColor(0,0,0);



  $stapgrootte = ceil(abs($bereik)/$horDiv);



  $top = $YstartGrafiek-$h;
  $bodem = $YstartGrafiek;
  $absUnit =abs($unit);

$nulpunt = $YstartGrafiek + $nulYpos;
$n=0;

  for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
  {
    //echo "$nulpunt < $bodem; $i-= $absUnit*$stapgrootte <br>";// exit;
      $skipNull = true;
      $this->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
       break;
  }

$n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
   // echo "$nulpunt > $top; $i-= $absUnit*$stapgrootte <br>";// exit;
      $this->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      $this->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");

      $n++;
      if($n >20)
       break;
  }

  /*
  for ($i = 0; $i <= $horDiv; $i++)
  {
    if($i * $stapgrootte <=  abs($bereik))
    {
      $ypos = $YstartGrafiek - ($unit * $stapgrootte * $i *-1) ;

      $this->Line($XstartGrafiek, $ypos, $XstartGrafiek+$bGrafiek, $ypos,array('dash' => 1,'color'=>array(0,0,0)));
      $this->Text($XstartGrafiek-7, $ypos, ($stapgrootte * $i)+$minVal ." %");
    }
  }
  */


/*
      for ($i = 0; $i <= $horDiv; $i++) //y-as verdeling
      {
        if($minVal < 0)
         $val = (($horInterval * $i)/ $hGrafiek) * $bereik - $minVal;
        else
         $val = (($horInterval * $i)/ $hGrafiek) * $bereik ;
        $val = number_format($val*-1,1);
        $ypos = $YstartGrafiek - $horInterval * $i;
        $this->Line($XstartGrafiek, $ypos, $XstartGrafiek+$bGrafiek, $ypos,array('dash' => 1));
        $this->Text($XstartGrafiek-7, $ypos, $val." %");
      }
*/
if($numBars > 0)
  $this->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->NbVal + 1));
        $bGrafiek = $vBar * ($this->NbVal + 1);
        $eBaton = ($vBar * 80 / 100);


      $this->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->SetLineWidth(0.2);

      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      foreach($data as $val)
      {
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          //Legend
          $this->SetXY(0, $yval);
  //        $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
          $i++;
      }

      $this->SetFont($this->rapport_font, '', 6);
      //datum onder grafiek
      $datumStart = db2jul($legendDatum[0]);
      $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
      $datumStop  = db2jul($legendDatum[count($legendDatum)-1]);

      $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
      $ypos = $YstartGrafiek + $margin*2;
      $xpos = $XstartGrafiek;

      $this->Text($xpos, $YstartGrafiek+4,$datumStart);
      //$this->Text($xpos, $ypos,$datumStart);
      $xpos = $XPage+$w - $this->GetStringWidth($datumStop);
      //$this->Text($xpos, $ypos,$datumStop);
      $this->Text($xpos, $YstartGrafiek+4,$datumStop);




/*
      //Scales
      for ($i = 0; $i <= $nbDiv; $i++)
      {
          $ypos= $YDiag + $hRepere * $i + 2;
          $this->Line($XDiag, $ypos, $XDiag + $lDiag ,$ypos );
          $val = $i * $valIndRepere;
          $xpos = $XDiag + $lDiag + $margin ;
          $ypos = $YDiag + $hRepere * $i  - $margin  ;
          $this->Text($xpos, $ypos, $val);

          echo "\n<br>$xpos, $ypos, $val";
      }
*/
  }

  function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
  {

      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends($data,$format);

      $XPage = $this->GetX();
      $YPage = $this->GetY();
      $margin = 2;
      $YDiag = $YPage + $margin;
      $hDiag = floor($h - $margin * 2);
      $XDiag = $XPage + $margin * 2 + $this->wLegend;
      $lDiag = floor($w - $margin * 3 - $this->wLegend);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data);
      }
      $valIndRepere = ceil($maxVal / $nbDiv);
      $maxVal = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $lDiag = $lRepere * $nbDiv;
      $unit = $lDiag / $maxVal;
      $hBar = floor($hDiag / ($this->NbVal + 1));
      $hDiag = $hBar * ($this->NbVal + 1);
      $eBaton = floor($hBar * 80 / 100);

      $this->SetLineWidth(0.2);
      $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

      $this->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      foreach($data as $val) {
          //Bar
          $xval = $XDiag;
          $lval = (int)($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->Rect($xval, $yval, $lval, $hval, 'DF');
          //Legend
          $this->SetXY(0, $yval);
          $this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      for ($i = 0; $i <= $nbDiv; $i++) {
          $xpos = $XDiag + $lRepere * $i;
          $this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
          $val = $i * $valIndRepere;
          $xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
          $ypos = $YDiag + $hDiag - $margin;
          $this->Text($xpos, $ypos, $val);
      }
  }

  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['Datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->GetX();
    $YPage = $this->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->SetLineWidth(0.2);
  //  $this->Rect($XDiag, $YDiag, $lDiag, $hDiag);


  $this->SetFont($this->rapport_font,''.$kopStyle,$this->rapport_fontsize);

    $this->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 100)
        $maxVal = 101;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 100)
        $minVal = 99;
    }

    $minVal = 100 + ($minVal-100) * 2;
    $maxVal = 100 + ($maxVal-100) * 2;


     $legendYstep = ($maxVal - $minVal) / $horDiv;

     $verInterval = ($lDiag / $verDiv);
     $horInterval = ($hDiag / $horDiv);

     $waardeCorrectie = $hDiag / ($maxVal - $minVal);

     $unit = $lDiag / count($data);

     if($jaar)
       $unit = $lDiag / 12;

      for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      {
        $xpos = $XDiag + $verInterval * $i;
      }

      $this->SetFont($this->rapport_font, '', 6);
      $this->SetTextColor(0,0,0);
       $this->SetDrawColor(0,0,0);


  $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
   $unith = $hDiag / (-1 * $minVal + $maxVal);
  //$honderdLijn = false;





  $top = $YPage;
  $bodem = $YDiag+$hDiag;
  $absUnit =abs($unith);

$nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
$n=0;
//echo "$i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte ";
  for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
  {
  //  echo "$XDiag, $i, $XPage+$w ,$i <br>";
      $skipNull = true;
      $this->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->Text($XDiag-7, $i, 100-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;


  }

$n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
    $this->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
    if($skipNull == true)
      $skipNull = false;
    else
      $this->Text($XDiag-7, $i, ($n*$stapgrootte)+100 ." %");

    $n++;
    if($n >20)
       break;
  }



  /*
  for ($i = 0; $i <= $horDiv; $i++)
  {
    if($i * $stapgrootte <=  abs($maxVal - $minVal))
    {
      $ypos = $YDiag+$hDiag - ($unith * $stapgrootte * $i) ;

      $this->Line($XDiag, $ypos, $XPage+$w, $ypos,array('dash' => 1));
      $this->Text($XDiag-7, $ypos, ($stapgrootte * $i)+$minVal ." %");
      if(($stapgrootte * $i)+$minVal == 100)
        $honderdLijn = true;
    }
  }
  */
 /*
      $yval =  $YDiag + (($maxVal-100) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);

      //100 lijn
      if($honderdLijn == false)
      {
        $ypos = $YDiag + (($maxVal-100) * $waardeCorrectie);
        $this->Line($XDiag, $ypos, $XPage+$w, $ypos);
        $this->Text($XDiag-7, $ypos, "100 %");
      }
*/
      //datum onder grafiek
      $datumStart = db2jul($legendDatum[0]);
      $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
      $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
      $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
      $ypos = $YDiag + $hDiag + $margin*2;
      $xpos = $XDiag;
      $this->Text($xpos, $ypos,$datumStart);
      $xpos = $XPage+$w - $this->GetStringWidth($datumStop);
      $this->Text($xpos, $ypos,$datumStop);

      $yval = $YDiag + (($maxVal-100) * $waardeCorrectie) ;
      for ($i=0; $i<count($data); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
        $this->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
         $this->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
        $yval = $yval2;
      }

if(is_array($data1))
{
   // listarray($data1);
    $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
     $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
         $this->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        $yval = $yval2;
      }
}
      $this->SetLineStyle(array('color'=>array(0,0,0)));
      $this->SetFillColor(0,0,0);
  }

  function SetLegends($data, $format)
  {
      $this->legends=array();
      $this->wLegend=0;

      $this->sum=array_sum($data);

      $this->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          $p=sprintf('%.1f',$val).'%';
          $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          $this->legends[]=$legend;
          $this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
      }
  }

	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
  {
      if($cw){
          $d = $b;
          $b = $o - $a;
          $a = $o - $d;
      }else{
          $b += $o;
          $a += $o;
      }
      $a = ($a%360)+360;
      $b = ($b%360)+360;
      if ($a > $b)
          $b +=360;
      $b = $b/360*2*M_PI;
      $a = $a/360*2*M_PI;
      $d = $b-$a;
      if ($d == 0 )
          $d =2*M_PI;
      $k = $this->k;
      $hp = $this->h;
      if($style=='F')
          $op='f';
      elseif($style=='FD' or $style=='DF')
          $op='b';
      else
          $op='s';
      if (sin($d/2))
          $MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
      //first put the center
      $this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
      //put the first point
      $this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
      //draw the arc
      if ($d < M_PI/2){
          $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                      $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                      $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                      $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                      $xc+$r*cos($b),
                      $yc-$r*sin($b)
                      );
      }else{
          $b = $a + $d/4;
          $MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
          $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                      $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                      $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                      $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                      $xc+$r*cos($b),
                      $yc-$r*sin($b)
                      );
          $a = $b;
          $b = $a + $d/4;
          $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                      $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                      $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                      $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                      $xc+$r*cos($b),
                      $yc-$r*sin($b)
                      );
          $a = $b;
          $b = $a + $d/4;
          $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                      $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                      $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                      $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                      $xc+$r*cos($b),
                      $yc-$r*sin($b)
                      );
          $a = $b;
          $b = $a + $d/4;
          $this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
                      $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
                      $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
                      $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
                      $xc+$r*cos($b),
                      $yc-$r*sin($b)
                      );
      }
      //terminate drawing
      $this->_out($op);
  }

  function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
  {
      $h = $this->h;
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
          $x1*$this->k,
          ($h-$y1)*$this->k,
          $x2*$this->k,
          ($h-$y2)*$this->k,
          $x3*$this->k,
          ($h-$y3)*$this->k));
  }

	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
		    $x=$this->x;

		if($y==-1)
		    $y=$this->y;

		if($this->angle!=0)
		{
		    $this->_out('Q');
		}

		$this->angle=$angle;

		if($angle!=0)
		{
		    $angle*=M_PI/180;
		    $c=cos($angle);
		    $s=sin($angle);
		    $cx=$x*$this->k;
		    $cy=($this->h-$y)*$this->k;
		    $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function _endpage()
	{
	  if($this->angle!=0)
	  {
	      $this->angle=0;
	      $this->_out('Q');
	  }
	  parent::_endpage();
	}
	//rvv
//draw
    // Sets line style
    // Parameters:
    // - style: Line style. Array with keys among the following:
    //   . width: Width of the line in user units
    //   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
    //   . join: miter, round or bevel
    //   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
    //           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
    //                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
    //   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
    //   . color: Draw color. Array with components (red, green, blue)
    function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                if(ereg('^.+,', $dash))
                    $tab = explode(',', $dash);
                else
                    $tab = array($dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2f', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }

    // Draws a line
    // Parameters:
    // - x1, y1: Start point
    // - x2, y2: End point
    // - style: Line style. Array like for SetLineStyle
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    // Draws a rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
    // - border_style: Border style of rectangle. Array with some of this index
    //   . all: Line style of all borders. Array like for SetLineStyle
    //   . L: Line style of left border. null (no border) or array like for SetLineStyle
    //   . T: Line style of top border. null (no border) or array like for SetLineStyle
    //   . R: Line style of right border. null (no border) or array like for SetLineStyle
    //   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $border_style = null;
                parent::Rect($x, $y, $w, $h, $style);
                break;
            case 'DF': case 'FD':
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all'])) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                } else
                    $style = 'F';
                parent::Rect($x, $y, $w, $h, $style);
                break;
            default:
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all']) && $border_style['all']) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                    parent::Rect($x, $y, $w, $h, $style);
                }
                break;
        }
        if ($border_style) {
            if (isset($border_style['L']) && $border_style['L'])
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            if (isset($border_style['T']) && $border_style['T'])
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            if (isset($border_style['R']) && $border_style['R'])
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            if (isset($border_style['B']) && $border_style['B'])
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
        }
    }

    // Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
    // Parameters:
    // - x0, y0: Start point
    // - x1, y1: Control point 1
    // - x2, y2: Control point 2
    // - x3, y3: End point
    // - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
    // - line_style: Line style for curve. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $op = 'f';
                $line_style = null;
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        if ($line_style)
            $this->SetLineStyle($line_style);

        $this->_Point($x0, $y0);
        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
        $this->_out($op);
    }

    // Draws an ellipse
    // Parameters:
    // - x0, y0: Center point
    // - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
    // - angle: Orientation angle (anti-clockwise)
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
    // - line_style: Line style for ellipse. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        if ($rx) {
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($r, $g, $b) = $fill_color;
                $this->SetFillColor($r, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $op = 'f';
                    $line_style = null;
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                case 'C':
                    $op = 's'; // small 's' means closing the path as well
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($line_style)
                $this->SetLineStyle($line_style);
            if (!$ry)
                $ry = $rx;
            $rx *= $this->k;
            $ry *= $this->k;
            if ($nSeg < 2)
                $nSeg = 2;

            $astart = deg2rad((float) $astart);
            $afinish = deg2rad((float) $afinish);
            $totalAngle = $afinish - $astart;

            $dt = $totalAngle/$nSeg;
            $dtm = $dt/3;

            $x0 *= $this->k;
            $y0 = ($this->h - $y0) * $this->k;
            if ($angle != 0) {
                $a = -deg2rad((float) $angle);
                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
                $x0 = 0;
                $y0 = 0;
            }

            $t1 = $astart;
            $a0 = $x0 + ($rx * cos($t1));
            $b0 = $y0 + ($ry * sin($t1));
            $c0 = -$rx * sin($t1);
            $d0 = $ry * cos($t1);
            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
            for ($i = 1; $i <= $nSeg; $i++) {
                // Draw this bit of the total curve
                $t1 = ($i * $dt) + $astart;
                $a1 = $x0 + ($rx * cos($t1));
                $b1 = $y0 + ($ry * sin($t1));
                $c1 = -$rx * sin($t1);
                $d1 = $ry * cos($t1);
                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
                            ($a1 - ($c1 * $dtm)) / $this->k,
                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
                            $a1 / $this->k,
                            $this->h - ($b1 / $this->k));
                $a0 = $a1;
                $b0 = $b1;
                $c0 = $c1;
                $d0 = $d1;
            }
            $this->_out($op);
            if ($angle !=0)
                $this->_out('Q');
        }
    }

    // Draws a circle
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
    // - line_style: Line style for circle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
    }

    // Draws a polygon
    // Parameters:
    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
        $np = count($p) / 2;
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $line_style = null;
                $op = 'f';
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        $draw = true;
        if ($line_style)
            if (isset($line_style['all']))
                $this->SetLineStyle($line_style['all']);
            else { // 0 .. (np - 1), op = {B, S}
                $draw = false;
                if ('B' == $op) {
                    $op = 'f';
                    $this->_Point($p[0], $p[1]);
                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
                        $this->_Line($p[$i], $p[$i + 1]);
                    $this->_Line($p[0], $p[1]);
                    $this->_out($op);
                }
                $p[$np * 2] = $p[0];
                $p[($np * 2) + 1] = $p[1];
                for ($i = 0; $i < $np; $i++)
                    if (!empty($line_style[$i]))
                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
            }

        if ($draw) {
            $this->_Point($p[0], $p[1]);
            for ($i = 2; $i < ($np * 2); $i = $i + 2)
                $this->_Line($p[$i], $p[$i + 1]);
            $this->_Line($p[0], $p[1]);
            $this->_out($op);
        }
    }

    // Draws a regular polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - ns: Number of sides
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($ns < 3)
            $ns = 3;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p = null;
        for ($i = 0; $i < $ns; $i++) {
            $a = $angle + ($i * 360 / $ns);
            $a_rad = deg2rad((float) $a);
            $p[] = $x0 + ($r * sin($a_rad));
            $p[] = $y0 + ($r * cos($a_rad));
        }
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a star polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - nv: Number of vertices
    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($nv < 2)
            $nv = 2;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p2 = null;
        $visited = null;
        for ($i = 0; $i < $nv; $i++) {
            $a = $angle + ($i * 360 / $nv);
            $a_rad = deg2rad((float) $a);
            $p2[] = $x0 + ($r * sin($a_rad));
            $p2[] = $y0 + ($r * cos($a_rad));
            $visited[] = false;
        }
        $p = null;
        $i = 0;
        do {
            $p[] = $p2[$i * 2];
            $p[] = $p2[($i * 2) + 1];
            $visited[$i] = true;
            $i += $ng;
            $i %= $nv;
        } while (!$visited[$i]);
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a rounded rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - r: Radius of the rounded corners
    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
    // - border_style: Border style of rectangle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
        if ('0000' == $round_corner) // Not rounded
            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
        else { // Rounded
            if (!(false === strpos($style, 'F')) && $fill_color)
            {
                list($red, $g, $b) = $fill_color;
                $this->SetFillColor($red, $g, $b);
            }
            switch ($style)
            {
                case 'F':
                    $border_style = null;
                    $op = 'f';
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($border_style)
                $this->SetLineStyle($border_style);

            $MyArc = 4 / 3 * (sqrt(2) - 1);

            $this->_Point($x + $r, $y);
            $xc = $x + $w - $r;
            $yc = $y + $r;
            $this->_Line($xc, $y);
            if ($round_corner[0])
                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
            else
                $this->_Line($x + $w, $y);

            $xc = $x + $w - $r ;
            $yc = $y + $h - $r;
            $this->_Line($x + $w, $yc);

            if ($round_corner[1])
                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
            else
                $this->_Line($x + $w, $y + $h);

            $xc = $x + $r;
            $yc = $y + $h - $r;
            $this->_Line($xc, $y + $h);
            if ($round_corner[2])
                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
            else
                $this->_Line($x, $y + $h);

            $xc = $x + $r;
            $yc = $y + $r;
            $this->_Line($x, $yc);
            if ($round_corner[3])
                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
            else
            {
                $this->_Line($x, $y);
                $this->_Line($x + $r, $y);
            }
            $this->_out($op);
        }
    }

    /* PRIVATE METHODS */

    // Sets a draw point
    // Parameters:
    // - x, y: Point
    function _Point($x, $y) {
        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
    }

    // Draws a line from last draw point
    // Parameters:
    // - x, y: End point
    function _Line($x, $y) {
        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    // Draws a Bézier curve from last draw point
    // Parameters:
    // - x1, y1: Control point 1
    // - x2, y2: Control point 2
    // - x3, y3: End point
    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }
//end draw
    function Pie3DSlice($pdf,$xc,$yc,$w,$h,$sa,$ea,$z,$fillcolor,$shadow=0.65) {
	// Due to the way the 3D Pie algorithm works we are
	// guaranteed that any slice we get into this method
	// belongs to either the left or right side of the
	// pie ellipse. Hence, no slice will cross 90 or 270
	// point.
	if( ($sa < 90 && $ea > 90) || ( ($sa > 90 && $sa < 270) && $ea > 270) ) {
	    JpGraphError::RaiseL(14003);//('Internal assertion failed. Pie3D::Pie3DSlice');
	    exit(1);
	}

	$p[] = array();

	// Setup pre-calculated values
	$rsa = $sa/180*M_PI;	// to Rad
	$rea = $ea/180*M_PI;	// to Rad
	$sinsa = sin($rsa);
	$cossa = cos($rsa);
	$sinea = sin($rea);
	$cosea = cos($rea);

	// p[] is the points for the overall slice and
	// pt[] is the points for the top pie

	// Angular step when approximating the arc with a polygon train.
	$step = 0.051;

	if( $sa >= 270 ) {
	    if( $ea > 360 || ($ea > 0 && $ea <= 90) ) {
		if( $ea > 0 && $ea <= 90 ) {
		    // Adjust angle to simplify conditions in loops
		    $rea += 2*M_PI;
		}

		$p = array($xc,$yc,$xc,$yc+$z,
			   $xc+$w*$cossa,$z+$yc-$h*$sinsa);
		$pt = array($xc,$yc,$xc+$w*$cossa,$yc-$h*$sinsa);

		for( $a=$rsa; $a < 2*M_PI; $a += $step ) {
		    $tca = cos($a);
		    $tsa = sin($a);
		    $p[] = $xc+$w*$tca;
		    $p[] = $z+$yc-$h*$tsa;
		    $pt[] = $xc+$w*$tca;
		    $pt[] = $yc-$h*$tsa;
		}

		$pt[] = $xc+$w;
		$pt[] = $yc;

		$p[] = $xc+$w;
		$p[] = $z+$yc;
		$p[] = $xc+$w;
		$p[] = $yc;
		$p[] = $xc;
		$p[] = $yc;

		for( $a=2*M_PI+$step; $a < $rea; $a += $step ) {
		    $pt[] = $xc + $w*cos($a);
		    $pt[] = $yc - $h*sin($a);
		}

  		$pt[] = $xc+$w*$cosea;
		$pt[] = $yc-$h*$sinea;
		$pt[] = $xc;
		$pt[] = $yc;

	    }
	    else {
		$p = array($xc,$yc,$xc,$yc+$z,
			   $xc+$w*$cossa,$z+$yc-$h*$sinsa);
		$pt = array($xc,$yc,$xc+$w*$cossa,$yc-$h*$sinsa);

		$rea = $rea == 0.0 ? 2*M_PI : $rea;
		for( $a=$rsa; $a < $rea; $a += $step ) {
		    $tca = cos($a);
		    $tsa = sin($a);
		    $p[] = $xc+$w*$tca; //rechts onder bocht
		    $p[] = $z+$yc-$h*$tsa;
		    $pt[] = $xc+$w*$tca; //recht boven bocht
		    $pt[] = $yc-$h*$tsa;
		}

		$pt[] = $xc+$w*$cosea;
		$pt[] = $yc-$h*$sinea;
		$pt[] = $xc;
		$pt[] = $yc;

		$p[] = $xc+$w*$cosea;
		$p[] = $z+$yc-$h*$sinea;
		$p[] = $xc+$w*$cosea;
		$p[] = $yc-$h*$sinea;
		$p[] = $xc;
		$p[] = $yc;
	    }
	}
	elseif( $sa >= 180 ) {
	    $p = array($xc,$yc,$xc,$yc+$z,$xc+$w*$cosea,$z+$yc-$h*$sinea);
	    $pt = array($xc,$yc,$xc+$w*$cosea,$yc-$h*$sinea);
	    for( $a=$rea; $a>$rsa; $a -= $step ) {
		$tca = cos($a);
		$tsa = sin($a);
		$p[] = $xc+$w*$tca;
		$p[] = $z+$yc-$h*$tsa;
		$pt[] = $xc+$w*$tca;
		$pt[] = $yc-$h*$tsa;
	    }

	    $pt[] = $xc+$w*$cossa;
	    $pt[] = $yc-$h*$sinsa;
	    $pt[] = $xc;
	    $pt[] = $yc;

	    $p[] = $xc+$w*$cossa;
	    $p[] = $z+$yc-$h*$sinsa;
	    $p[] = $xc+$w*$cossa;
	    $p[] = $yc-$h*$sinsa;
	    $p[] = $xc;
	    $p[] = $yc;

	}
	elseif( $sa >= 90 ) {
	    if( $ea > 180 ) {
		$p = array($xc,$yc,$xc,$yc+$z,$xc+$w*$cosea,$z+$yc-$h*$sinea);
		$pt = array($xc,$yc,$xc+$w*$cosea,$yc-$h*$sinea);
		for( $a=$rea; $a > M_PI; $a -= $step ) {
		    $tca = cos($a);
		    $tsa = sin($a);
		    $p[] = $xc+$w*$tca;
		    $p[] = $z + $yc - $h*$tsa; //bocht links onder
		    $pt[] = $xc+$w*$tca;
		    $pt[] = $yc-$h*$tsa;
		}

		$p[] = $xc-$w;
		$p[] = $z+$yc;
		$p[] = $xc-$w;
		$p[] = $yc;
		$p[] = $xc;
		$p[] = $yc;

		$pt[] = $xc-$w;
		$pt[] = $z+$yc;
		$pt[] = $xc-$w;
		$pt[] = $yc;

		for( $a=M_PI-$step; $a > $rsa; $a -= $step ) {
		    $pt[] = $xc + $w*cos($a);
		    $pt[] = $yc - $h*sin($a);
		}

		$pt[] = $xc+$w*$cossa;
		$pt[] = $yc-$h*$sinsa;
		$pt[] = $xc;
		$pt[] = $yc;

	    }
	    else { // $sa >= 90 && $ea <= 180
		$p = array($xc,$yc,$xc,$yc+$z,
			   $xc+$w*$cosea,$z+$yc-$h*$sinea,
			   $xc+$w*$cosea,$yc-$h*$sinea,
			   $xc,$yc);

		$pt = array($xc,$yc,$xc+$w*$cosea,$yc-$h*$sinea);

		for( $a=$rea; $a>$rsa; $a -= $step ) {
		    $pt[] = $xc + $w*cos($a);
		    $pt[] = $yc - $h*sin($a);
		}

		$pt[] = $xc+$w*$cossa;
		$pt[] = $yc-$h*$sinsa;
		$pt[] = $xc;
		$pt[] = $yc;

	    }
	}
	else { // sa > 0 && ea < 90

	    $p = array($xc,$yc,$xc,$yc+$z,
		       $xc+$w*$cossa,$z+$yc-$h*$sinsa,
		       $xc+$w*$cossa,$yc-$h*$sinsa,
		       $xc,$yc);
	    $pt = array($xc,$yc,$xc+$w*$cossa,$yc-$h*$sinsa);

	    for( $a=$rsa; $a < $rea; $a += $step ) {
		$pt[] = $xc + $w*cos($a);
		$pt[] = $yc - $h*sin($a);
	    }

	    $pt[] = $xc+$w*$cosea;
	    $pt[] = $yc-$h*$sinea;
	    $pt[] = $xc;
	    $pt[] = $yc;
	}

   $schaduw = 40;

   $this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($fillcolor['0']-$schaduw, $fillcolor['1']-$schaduw,$fillcolor['2']-$schaduw)));
   $this->Polygon($p,'DF',"" , array($fillcolor['0']-$schaduw,$fillcolor['1']-$schaduw,$fillcolor['2']-$schaduw));

   $this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($fillcolor['0'], $fillcolor['1'],$fillcolor['2'])));
   $this->Polygon($pt,'DF',"", array($fillcolor['0'],$fillcolor['1'] ,$fillcolor['2']));

   $this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

   }


//   function Pie3D($aaoption,$pdf,$data,$colors,$xc,$yc,$d,$angle,$z,
//		   $shadow=0.65,$startangle=0,$edgecolor="",$edgeweight=1) {
   function Pie3D($data,$colors,$xc,$yc,$d,$angle,$z,$titel,$kader = 1)
   {
   //Teken rechthoek om grafiek.
   $this->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
   if ($kader == 1)
   {
   $this->Rect($xc-$d-25,$yc-$d+13,$d*2+50,$d*2+5);
   $this->SetXY($xc-50,$yc-$d+13);
   $this->SetFont($this->rapport_font, 'B', 11);
   $this->MultiCell(100,10,$titel,0,"C");
   }
   elseif($kader == 'titel')
   {
     $this->SetXY($xc-50,$yc-$d+13);
     $this->SetFont($this->rapport_font, 'B', 11);
     $this->MultiCell(100,10,$titel,0,"C");
   }
   elseif ($kader == 'geen')
   {

   }
   else
   {
   $this->Rect($xc-$d-5,$yc-$d,$d*2+60,$d*2);
   $this->SetXY($xc-25,$yc-$d);
   $this->SetFont($this->rapport_font, 'B', 11);
   $this->MultiCell(50,10,$titel,0,"C");

   }
	//---------------------------------------------------------------------------
	// As usual the algorithm get more complicated than I originally
	// envisioned. I believe that this is as simple as it is possible
	// to do it with the features I want. It's a good exercise to start
	// thinking on how to do this to convince your self that all this
	// is really needed for the general case.
	//
	// The algorithm two draw 3D pies without "real 3D" is done in
	// two steps.
	// First imagine the pie cut in half through a thought line between
	// 12'a clock and 6'a clock. It now easy to imagine that we can plot
	// the individual slices for each half by starting with the topmost
	// pie slice and continue down to 6'a clock.
	//
	// In the algortithm this is done in three principal steps
	// Step 1. Do the knife cut to ensure by splitting slices that extends
	// over the cut line. This is done by splitting the original slices into
	// upto 3 subslices.
	// Step 2. Find the top slice for each half
	// Step 3. Draw the slices from top to bottom
	//
	// The thing that slightly complicates this scheme with all the
	// angle comparisons below is that we can have an arbitrary start
	// angle so we must take into account the different equivalence classes.
	// For the same reason we must walk through the angle array in a
	// modulo fashion.
	//
	// Limitations of algorithm:
	// * A small exploded slice which crosses the 270 degree point
	//   will get slightly nagged close to the center due to the fact that
	//   we print the slices in Z-order and that the slice left part
	//   get printed first and might get slightly nagged by a larger
	//   slice on the right side just before the right part of the small
	//   slice. Not a major problem though.
	//---------------------------------------------------------------------------


	// Determine the height of the ellippse which gives an
	// indication of the inclination angle
	$h = ($angle/90.0)*$d;
	$sum = 0;
	for($i=0; $i<count($data); ++$i )
	{
	    $sum += $data[$i];
	}

	// Special optimization
	if( $sum==0 ) return;

	if( $this->labeltype == 2 )
	{
	    $this->adjusted_data = $this->AdjPercentage($data);
	}

	// Setup the start
	$accsum = 0;
	$a = $startangle;
	$a = $this->NormAngle($a);

	//
	// Step 1 . Split all slices that crosses 90 or 270
	//

	$idx=0;
	$adjexplode=array();
	$numcolors = count($colors);
	for($i=0; $i<count($data); ++$i, ++$idx )
	{
	    $da = $data[$i]/$sum * 360;

	    if( empty($this->explode_radius[$i]) )
		$this->explode_radius[$i]=0;

	    $expscale=1;
	    if( $aaoption == 1 )
		$expscale=2;

	    $la = $a + $da/2;
	    $explode = array( $xc + $this->explode_radius[$i]*cos($la*M_PI/180)*$expscale,
		              $yc - $this->explode_radius[$i]*sin($la*M_PI/180) * ($h/$d) *$expscale );
	    $adjexplode[$idx] = $explode;
	    $labeldata[$i] = array($la,$explode[0],$explode[1]);
	    $originalangles[$i] = array($a,$a+$da);

	    $ne = $this->NormAngle($a+$da);

	    if( $da <= 180 )
	    {
		// If the slice size is <= 90 it can at maximum cut across
		// one boundary (either 90 or 270) where it needs to be split
		$split=-1; // no split
		if( ($da<=90 && ($a <= 90 && $ne > 90)) ||
		    (($da <= 180 && $da >90)  && (($a < 90 || $a >= 270) && $ne > 90)) ) {
		    $split = 90;
		}
		elseif( ($da<=90 && ($a <= 270 && $ne > 270)) ||
		        (($da<=180 && $da>90) && ($a >= 90 && $a < 270 && ($a+$da) > 270 )) )
		{
		    $split = 270;
		}
		if( $split > 0 )
		{ // split in two
		    $angles[$idx] = array($a,$split);
		    $adjcolors[$idx] = $colors[$i % $numcolors];
		    $adjexplode[$idx] = $explode;
		    $angles[++$idx] = array($split,$ne);
		    $adjcolors[$idx] = $colors[$i % $numcolors];
		    $adjexplode[$idx] = $explode;
		}
		else
		{ // no split
		    $angles[$idx] = array($a,$ne);
		    $adjcolors[$idx] = $colors[$i  % $numcolors];
		    $adjexplode[$idx] = $explode;
		}

	    }
	    else
	    {
		// da>180
		// Slice may, depending on position, cross one or two
		// bonudaries

		if( $a < 90 )
		    $split = 90;
		elseif( $a <= 270 )
		    $split = 270;
		else
		    $split = 90;

		$angles[$idx] = array($a,$split);
		$adjcolors[$idx] = $colors[$i % $numcolors];
		$adjexplode[$idx] = $explode;
		//if( $a+$da > 360-$split ) {
		// For slices larger than 270 degrees we might cross
		// another boundary as well. This means that we must
		// split the slice further. The comparison gets a little
		// bit complicated since we must take into accound that
		// a pie might have a startangle >0 and hence a slice might
		// wrap around the 0 angle.
		// Three cases:
		//  a) Slice starts before 90 and hence gets a split=90, but
		//     we must also check if we need to split at 270
		//  b) Slice starts after 90 but before 270 and slices
		//     crosses 90 (after a wrap around of 0)
		//  c) If start is > 270 (hence the firstr split is at 90)
		//     and the slice is so large that it goes all the way
		//     around 270.
		if( ($a < 90 && ($a+$da > 270)) ||
		    ($a > 90 && $a<=270 && ($a+$da>360+90) ) ||
		    ($a > 270 && $this->NormAngle($a+$da)>270) )
		{
		    $angles[++$idx] = array($split,360-$split);
		    $adjcolors[$idx] = $colors[$i % $numcolors];
		    $adjexplode[$idx] = $explode;
		    $angles[++$idx] = array(360-$split,$ne);
		    $adjcolors[$idx] = $colors[$i % $numcolors];
		    $adjexplode[$idx] = $explode;
		}
		else
		{
		    // Just a simple split to the previous decided
		    // angle.
		    $angles[++$idx] = array($split,$ne);
		    $adjcolors[$idx] = $colors[$i % $numcolors];
		    $adjexplode[$idx] = $explode;
		}
	    }
	    $a += $da;
	    $a = $this->NormAngle($a);
	}
	// Total number of slices
	$n = count($angles);

	for($i=0; $i<$n; ++$i)
	{
	    list($dbgs,$dbge) = $angles[$i];
	}

	//
	// Step 2. Find start index (first pie that starts in upper left quadrant)
	//
	$minval = $angles[0][0];
	$min = 0;
	for( $i=0; $i<$n; ++$i )
	{
	    if( $angles[$i][0] < $minval ) {
		$minval = $angles[$i][0];
		$min = $i;
	    }
	}
	$j = $min;
	$cnt = 0;
	while( $angles[$j][1] <= 90 )
	{
	    $j++;
	    if( $j>=$n) {
		$j=0;
	    }
	    if( $cnt > $n ) {
//		JpGraphError::RaiseL(14005);
//("Pie3D Internal error (#1). Trying to wrap twice when looking for start index");
	    }
	    ++$cnt;
	}
	$start = $j;

	//
	// Step 3. Print slices in z-order
	//
	$cnt = 0;

	// First stroke all the slices between 90 and 270 (left half circle)
	// counterclockwise

	while( $angles[$j][0] < 270  && $aaoption !== 2 )
	{

	    list($x,$y) = $adjexplode[$j];

	    $this->Pie3DSlice($pdf,$x,$y,$d,$h,$angles[$j][0],$angles[$j][1],
			      $z,$adjcolors[$j],$shadow);

	    $last = array($x,$y,$j);

	    $j++;
	    if( $j >= $n ) $j=0;
	    if( $cnt > $n ) {
//		JpGraphError::RaiseL(14006);
//("Pie3D Internal Error: Z-Sorting algorithm for 3D Pies is not working properly (2). Trying to wrap twice while stroking.");
	    }
	    ++$cnt;
	}

	$slice_left = $n-$cnt;
	$j=$start-1;
	if($j<0) $j=$n-1;
	$cnt = 0;


	// The stroke all slices from 90 to -90 (right half circle)
	// clockwise
	while( $cnt < $slice_left  && $aaoption !== 2 ) {

	    list($x,$y) = $adjexplode[$j];

	    $this->Pie3DSlice($pdf,$x,$y,$d,$h,$angles[$j][0],$angles[$j][1],
			      $z,$adjcolors[$j],$shadow);
	    $j--;
	    if( $cnt > $n ) {
//		JpGraphError::RaiseL(14006);
//("Pie3D Internal Error: Z-Sorting algorithm for 3D Pies is not working properly (2). Trying to wrap twice while stroking.");
	    }
	    if($j<0) $j=$n-1;
	    $cnt++;
	}

	// Now do a special thing. Stroke the last slice on the left
	// halfcircle one more time.  This is needed in the case where
	// the slice close to 270 have been exploded. In that case the
	// part of the slice close to the center of the pie might be
	// slightly nagged.

	if( $aaoption == 1 ) //Uitgezet labels vanuit de grafiek fucntie tekenen.
	{
	    // Now print possible labels and add csim
	    for($i=0; $i < count($data); ++$i ) {
		$la = $labeldata[$i][0];
		$x = $labeldata[$i][1] + cos($la*M_PI/180)*($d+$margin)*$this->ilabelposadj;
		$y = $labeldata[$i][2] - sin($la*M_PI/180)*($h+$margin)*$this->ilabelposadj;
		if( $this->ilabelposadj >= 1.0 )
		{
		    if( $la > 180 && $la < 360 ) $y += $z;
		}
		if( $this->labeltype == 0 )
		{
		    if( $sum > 0 )
			$l = number_format(100*$data[$i]/$sum,1,",","."); //number_format($waarde,$dec,",",".");
		    else
			$l = 0;
		}
		elseif( $this->labeltype == 1 ) {
		    $l = $data[$i];
		}
		else {
		    $l = $this->adjusted_data[$i];
		}
		if( isset($this->labels[$i]) && is_string($this->labels[$i]) )
		    $l=sprintf($this->labels[$i],$l);
		$this->StrokeLabels($l,$img,$labeldata[$i][0]*M_PI/180,$x,$y,$z);
	    }
	}

	//
	// Finally add potential lines in pie
	//

	if( $edgecolor=="" || $aaoption !== 0 ) return;

	$accsum = 0;
	$a = $startangle;
	$a = $this->NormAngle($a);

	$a *= M_PI/180.0;

	$idx=0;

	$fulledge = true;
	for($i=0; $i < count($data) && $fulledge; ++$i )
	{
	    if( empty($this->explode_radius[$i]) )
		$this->explode_radius[$i]=0;
	    if( $this->explode_radius[$i] > 0 ) {
		$fulledge = false;
	    }
	}

	for($i=0; $i < count($data); ++$i, ++$idx )
	{

	    $da = $data[$i]/$sum * 2*M_PI;
	    $a += $da;
	}
    }

    function NormAngle($a)
    {
	// Normalize anle to 0 to 2M_PI
	//
	if( $a > 0 )
	{
	    while($a > 360) $a -= 360;
	}
	else
	{
	    while($a < 0) $a += 360;
	}
	if( $a < 0 )
	    $a = 360 + $a;

	if( $a == 360 ) $a=0;
	return $a;
    }

    function StrokeLabels($label,$img,$a,$xp,$yp,$z)
    {
	$this->value->halign="left";
	$this->value->valign="top";

	$h = 50;
	$w=90;

	while( $a > 2*M_PI ) $a -= 2*M_PI;
	if( $a>=7*M_PI/4 || $a <= M_PI/4 ) $dx=0;
	if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dx=($a-M_PI/4)*2/M_PI;
	if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dx=1;
	if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dx=(1-($a-M_PI*5/4)*2/M_PI);

	if( $a>=7*M_PI/4 ) $dy=(($a-M_PI)-3*M_PI/4)*2/M_PI;
	if( $a<=M_PI/4 ) $dy=(1-$a*2/M_PI);
	if( $a>=M_PI/4 && $a <= 3*M_PI/4 ) $dy=1;
	if( $a>=3*M_PI/4 && $a <= 5*M_PI/4 ) $dy=(1-($a-3*M_PI/4)*2/M_PI);
	if( $a>=5*M_PI/4 && $a <= 7*M_PI/4 ) $dy=0;

	$x = round($xp-$dx*$w);
	$y = round($yp-$dy*$h);

	$oldmargin = $this->value->margin;
	$this->value->margin=0;
	$this->SetXY($x+10,$y+30);
    $this->MultiCell(60,1, $label, 0, "C");
	$this->value->margin=$oldmargin;
    }

    function set3dLabels($labels,$x,$y,$colors,$xcor=-55,$xcor2=5,$ycor= 27,$kort = 0,$colMaxInput=0,$maxAantal=0)
    {
        $aantal = count($labels);
        if($kort == 0)
        {
          if($maxAantal==0)
            $maxAantal = 12;
          $colMax = 6;
        }
        else
        {
          $aantal = min(16,$aantal);
          if($maxAantal==0)
            $maxAantal = 16;
          $colMax = 16;
          if($kort == 2)
            $ycor = $ycor - ( $aantal * 2 ) +6;
          else
            $ycor = $ycor - ( $aantal * 2  );
        }
			 if($colMaxInput<>0)
			 {
				 $colMax = $colMaxInput;
				 $yCorrectie=6;
			 }

    	  for($i=0; $i<$aantal; $i++)
    	  {
    	    $hLegend=3;
    	    if ($i < $colMax)
    	    {
    	    $x1=$xcor+$x;
    	    $x2=$xcor+$x+4;
    	    $y1=$ycor+$y+$i*4;
    	    $y2=$ycor+$y+$i*4;
    	    }
    	    else if($i < $colMax *2 && $i >$colMax -1)
		      {
		      $y1=$ycor+$y+($i-$colMax)*4;
    	    $y2=$ycor+$y+($i-$colMax)*4;
     	    $x1=$xcor2+$x;
    	    $x2=$xcor2+$x+4;
		      }

		      if ($i<$maxAantal )
		      {
		      $this->SetFont($this->rapport_font, '', 6);
		      $this->SetTextColor($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['b'],$this->rapport_fonds_fontcolor['b']);
		      $this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array($this->rapport_fonds_fontcolor['r'],$this->rapport_fonds_fontcolor['g'],$this->rapport_fonds_fontcolor['b'])));

          $this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
          $this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
          $this->SetXY($x2,$y1);
          $this->Cell(0,$hLegend,$labels[$i]);
          $y1+=$hLegend + $margin;
		      }
        }
    }

    //----------------------END OF FLOWING BLOCK------------------------------------//

//EDITEI
//Thanks to Ron Korving for the WordWrap() function
function WordWrap($text, $maxwidth)
{
    $biggestword=0;//EDITEI
    $toonarrow=false;//EDITEI

    $text = trim($text);
    if ($text==='') return 0;
    $space = $this->GetStringWidth(' ');
    $lines = explode("\n", $text);
    $text = '';
    $count = 0;

    foreach ($lines as $line)
    {
        $words = preg_split('/ +/', $line);
        $width = 0;

        foreach ($words as $word)
        {
            $wordwidth = $this->GetStringWidth($word);

	          //EDITEI
	          //Warn user that maxwidth is insufficient
	          if ($wordwidth > $maxwidth)
	          {
  		         if ($wordwidth > $biggestword) $biggestword = $wordwidth;
    		       $toonarrow=true;//EDITEI
	          }
            if ($width + $wordwidth <= $maxwidth)
            {
                $width += $wordwidth + $space;
                $text .= $word.' ';
            }
            else
            {
                $width = $wordwidth + $space;
                $text = rtrim($text)."\n".$word.' ';
                $count++;
            }
        }
        $text = rtrim($text)."\n";
        $count++;
    }
    $text = rtrim($text);

    //Return -(wordsize) if word is bigger than maxwidth
    if ($toonarrow) return -$biggestword;
    else return $count;
}


function newFlowingBlock( $w, $h, $b = 0, $a = 'J', $f = 0 , $is_table = false )
{
   // cell width in points
   if ($is_table)  $this->flowingBlockAttr[ 'width' ] = ($w * $this->k);
   else $this->flowingBlockAttr[ 'width' ] = ($w * $this->k) - (2*$this->cMargin*$this->k);
   // line height in user units
   $this->flowingBlockAttr[ 'is_table' ] = $is_table;
   $this->flowingBlockAttr[ 'height' ] = $h;
   $this->flowingBlockAttr[ 'lineCount' ] = 0;
   $this->flowingBlockAttr[ 'border' ] = $b;
   $this->flowingBlockAttr[ 'align' ] = $a;
   $this->flowingBlockAttr[ 'fill' ] = $f;
   $this->flowingBlockAttr[ 'font' ] = array();
   $this->flowingBlockAttr[ 'content' ] = array();
   $this->flowingBlockAttr[ 'contentWidth' ] = 0;
}


function finishFlowingBlock($outofblock=false)
{
   if (!$outofblock) $currentx = $this->x; //EDITEI - in order to make the Cell method work better
   //prints out the last chunk
   $is_table = $this->flowingBlockAttr[ 'is_table' ];
   $maxWidth =& $this->flowingBlockAttr[ 'width' ];
   $lineHeight =& $this->flowingBlockAttr[ 'height' ];
   $border =& $this->flowingBlockAttr[ 'border' ];
   $align =& $this->flowingBlockAttr[ 'align' ];
   $fill =& $this->flowingBlockAttr[ 'fill' ];
   $content =& $this->flowingBlockAttr[ 'content' ];
   $font =& $this->flowingBlockAttr[ 'font' ];
   $contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
   $lineCount =& $this->flowingBlockAttr[ 'lineCount' ];

   // set normal spacing
   $this->_out( sprintf( '%.3f Tw', 0 ) );
   $this->ws = 0;

   // the amount of space taken up so far in user units
   $usedWidth = 0;

   // Print out each chunk
   //EDITEI - Print content according to alignment
   $empty = $maxWidth - $contentWidth;
   $empty /= $this->k;
   $b = ''; //do not use borders
   $arraysize = count($content);
   $margins = (2*$this->cMargin);
   if ($outofblock)
   {
      $align = 'C';
      $empty = 0;
      $margins = $this->cMargin;
   }
   switch($align)
   {
      case 'R':
          foreach ( $content as $k => $chunk )
          {
              $this->restoreFont( $font[ $k ] );
              $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
              // determine which borders should be used
              $b = '';
              if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';
              if ( $k == count( $content ) - 1 && is_int( strpos( $border, 'R' ) ) ) $b .= 'R';

              if ($k == $arraysize-1 and !$outofblock) $skipln = 1;
              else $skipln = 0;

              if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, $skipln, $align, $fill, $this->HREF , $currentx ); //mono-style line
              elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2) + $empty, $lineHeight, $chunk, $b, 0, 'R', $fill, $this->HREF );//first part
              elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2), $lineHeight, $chunk, $b, $skipln, '', $fill, $this->HREF, $currentx );//last part
              else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, 0, '', $fill, $this->HREF );//middle part
          }
          break;
      case 'L':
      case 'J':
          foreach ( $content as $k => $chunk )
          {
              $this->restoreFont( $font[ $k ] );
              $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
              // determine which borders should be used
              $b = '';
              if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';
              if ( $k == 0 && is_int( strpos( $border, 'L' ) ) ) $b .= 'L';

              if ($k == $arraysize-1 and !$outofblock) $skipln = 1;
              else $skipln = 0;

              if (!$is_table and !$outofblock and !$fill and $align=='L' and $k == 0) {$align='';$margins=0;} //Remove margins in this special (though often) case

              if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, $skipln, $align, $fill, $this->HREF , $currentx ); //mono-style line
              elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2), $lineHeight, $chunk, $b, $skipln, $align, $fill, $this->HREF );//first part
              elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2) + $empty, $lineHeight, $chunk, $b, $skipln, '', $fill, $this->HREF, $currentx );//last part
              else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, $skipln, '', $fill, $this->HREF );//middle part
          }
          break;
      case 'C':
          foreach ( $content as $k => $chunk )
          {
              $this->restoreFont( $font[ $k ] );
              $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
              // determine which borders should be used
              $b = '';
              if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';

              if ($k == $arraysize-1 and !$outofblock) $skipln = 1;
              else $skipln = 0;

              if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, $skipln, $align, $fill, $this->HREF , $currentx ); //mono-style line
              elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2) + ($empty/2), $lineHeight, $chunk, $b, 0, 'R', $fill, $this->HREF );//first part
              elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2) + ($empty/2), $lineHeight, $chunk, $b, $skipln, 'L', $fill, $this->HREF, $currentx );//last part
              else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, 0, '', $fill, $this->HREF );//middle part
          }
          break;
     default: break;
   }
}

function WriteFlowingBlock( $s , $outofblock = false )
{
    if (!$outofblock) $currentx = $this->x; //EDITEI - in order to make the Cell method work better
    $is_table = $this->flowingBlockAttr[ 'is_table' ];
    // width of all the content so far in points
    $contentWidth =& $this->flowingBlockAttr[ 'contentWidth' ];
    // cell width in points
    $maxWidth =& $this->flowingBlockAttr[ 'width' ];
    $lineCount =& $this->flowingBlockAttr[ 'lineCount' ];
    // line height in user units
    $lineHeight =& $this->flowingBlockAttr[ 'height' ];
    $border =& $this->flowingBlockAttr[ 'border' ];
    $align =& $this->flowingBlockAttr[ 'align' ];
    $fill =& $this->flowingBlockAttr[ 'fill' ];
    $content =& $this->flowingBlockAttr[ 'content' ];
    $font =& $this->flowingBlockAttr[ 'font' ];

    $font[] = $this->saveFont();
    $content[] = '';

    $currContent =& $content[ count( $content ) - 1 ];

    // where the line should be cutoff if it is to be justified
    $cutoffWidth = $contentWidth;

    // for every character in the string
    for ( $i = 0; $i < strlen( $s ); $i++ )
    {
       // extract the current character
       $c = $s{$i};
       // get the width of the character in points
       $cw = $this->CurrentFont[ 'cw' ][ $c ] * ( $this->FontSizePt / 1000 );

       if ( $c == ' ' )
       {
           $currContent .= ' ';
           $cutoffWidth = $contentWidth;
           $contentWidth += $cw;
           continue;
       }
       // try adding another char
       if ( $contentWidth + $cw > $maxWidth )
       {
           // it won't fit, output what we already have
           $lineCount++;
           //Readjust MaxSize in order to use the whole page width
           if ($outofblock and ($lineCount == 1) ) $maxWidth = $this->pgwidth * $this->k;
           // contains any content that didn't make it into this print
           $savedContent = '';
           $savedFont = array();
           // first, cut off and save any partial words at the end of the string
           $words = explode( ' ', $currContent );

           // if it looks like we didn't finish any words for this chunk
           if ( count( $words ) == 1 )
           {
              // save and crop off the content currently on the stack
              $savedContent = array_pop( $content );
              $savedFont = array_pop( $font );

              // trim any trailing spaces off the last bit of content
              $currContent =& $content[ count( $content ) - 1 ];
              $currContent = rtrim( $currContent );
           }
           else // otherwise, we need to find which bit to cut off
           {
              $lastContent = '';
              for ( $w = 0; $w < count( $words ) - 1; $w++) $lastContent .= "{$words[ $w ]} ";

              $savedContent = $words[ count( $words ) - 1 ];
              $savedFont = $this->saveFont();
              // replace the current content with the cropped version
              $currContent = rtrim( $lastContent );
           }
           // update $contentWidth and $cutoffWidth since they changed with cropping
           $contentWidth = 0;
           foreach ( $content as $k => $chunk )
           {
              $this->restoreFont( $font[ $k ] );
              $contentWidth += $this->GetStringWidth( $chunk ) * $this->k;
           }
           $cutoffWidth = $contentWidth;
           // if it's justified, we need to find the char spacing
           if( $align == 'J' )
           {
              // count how many spaces there are in the entire content string
              $numSpaces = 0;
              foreach ( $content as $chunk ) $numSpaces += substr_count( $chunk, ' ' );
              // if there's more than one space, find word spacing in points
              if ( $numSpaces > 0 ) $this->ws = ( $maxWidth - $cutoffWidth ) / $numSpaces;
              else $this->ws = 0;
              $this->_out( sprintf( '%.3f Tw', $this->ws ) );
           }
           // otherwise, we want normal spacing
           else $this->_out( sprintf( '%.3f Tw', 0 ) );

           //EDITEI - Print content according to alignment
           if (!isset($numSpaces)) $numSpaces = 0;
           $contentWidth -= ($this->ws*$numSpaces);
           $empty = $maxWidth - $contentWidth - 2*($this->ws*$numSpaces);
           $empty /= $this->k;
           $b = ''; //do not use borders
           /*'If' below used in order to fix "first-line of other page with justify on" bug*/
           if($this->y+$this->divheight>$this->PageBreakTrigger and !$this->InFooter and $this->AcceptPageBreak())
	         {
           		$bak_x=$this->x;//Current X position
             	$ws=$this->ws;//Word Spacing
		          if($ws>0)
		          {
			         $this->ws=0;
			         $this->_out('0 Tw');
		          }
		          $this->AddPage($this->CurOrientation);
		          $this->x=$bak_x;
		          if($ws>0)
		          {
			         $this->ws=$ws;
			         $this->_out(sprintf('%.3f Tw',$ws));
            	}
	         }
           $arraysize = count($content);
           $margins = (2*$this->cMargin);
           if ($outofblock)
           {
              $align = 'C';
              $empty = 0;
              $margins = $this->cMargin;
           }
           switch($align)
           {
             case 'R':
                 foreach ( $content as $k => $chunk )
                 {
                     $this->restoreFont( $font[ $k ] );
                     $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
                     // determine which borders should be used
                     $b = '';
                     if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';
                     if ( $k == count( $content ) - 1 && is_int( strpos( $border, 'R' ) ) ) $b .= 'R';

                     if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, 1, $align, $fill, $this->HREF , $currentx ); //mono-style line
                     elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2) + $empty, $lineHeight, $chunk, $b, 0, 'R', $fill, $this->HREF );//first part
                     elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2), $lineHeight, $chunk, $b, 1, '', $fill, $this->HREF, $currentx );//last part
                     else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, 0, '', $fill, $this->HREF );//middle part
                 }
                break;
             case 'L':
             case 'J':
                 foreach ( $content as $k => $chunk )
                 {
                     $this->restoreFont( $font[ $k ] );
                     $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
                     // determine which borders should be used
                     $b = '';
                     if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';
                     if ( $k == 0 && is_int( strpos( $border, 'L' ) ) ) $b .= 'L';

                     if (!$is_table and !$outofblock and !$fill and $align=='L' and $k == 0)
                     {
                         //Remove margins in this special (though often) case
                         $align='';
                         $margins=0;
                     }

                     if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, 1, $align, $fill, $this->HREF , $currentx ); //mono-style line
                     elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2), $lineHeight, $chunk, $b, 0, $align, $fill, $this->HREF );//first part
                     elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2) + $empty, $lineHeight, $chunk, $b, 1, '', $fill, $this->HREF, $currentx );//last part
                     else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, 0, '', $fill, $this->HREF );//middle part

                     if (!$is_table and !$outofblock and !$fill and $align=='' and $k == 0)
                     {
                         $align = 'L';
                         $margins = (2*$this->cMargin);
                     }
                 }
                 break;
             case 'C':
                 foreach ( $content as $k => $chunk )
                 {
                     $this->restoreFont( $font[ $k ] );
                     $stringWidth = $this->GetStringWidth( $chunk ) + ( $this->ws * substr_count( $chunk, ' ' ) / $this->k );
                     // determine which borders should be used
                     $b = '';
                     if ( $lineCount == 1 && is_int( strpos( $border, 'T' ) ) ) $b .= 'T';

                     if ($arraysize == 1) $this->Cell( $stringWidth + $margins + $empty, $lineHeight, $chunk, $b, 1, $align, $fill, $this->HREF , $currentx ); //mono-style line
                     elseif ($k == 0) $this->Cell( $stringWidth + ($margins/2) + ($empty/2), $lineHeight, $chunk, $b, 0, 'R', $fill, $this->HREF );//first part
                     elseif ($k == $arraysize-1 ) $this->Cell( $stringWidth + ($margins/2) + ($empty/2), $lineHeight, $chunk, $b, 1, 'L', $fill, $this->HREF, $currentx );//last part
                     else $this->Cell( $stringWidth , $lineHeight, $chunk, $b, 0, '', $fill, $this->HREF );//middle part
                 }
                 break;
                 default: break;
           }
           // move on to the next line, reset variables, tack on saved content and current char
           $this->restoreFont( $savedFont );
           $font = array( $savedFont );
           $content = array( $savedContent . $s{ $i } );

           $currContent =& $content[ 0 ];
           $contentWidth = $this->GetStringWidth( $currContent ) * $this->k;
           $cutoffWidth = $contentWidth;
       }
       // another character will fit, so add it on
       else
       {
           $contentWidth += $cw;
           $currContent .= $s{ $i };
       }
    }
}

function saveFont()
{
   $saved = array();
   $saved[ 'family' ] = $this->FontFamily;
   $saved[ 'style' ] = $this->FontStyle;
   $saved[ 'sizePt' ] = $this->FontSizePt;
   $saved[ 'size' ] = $this->FontSize;
   $saved[ 'curr' ] =& $this->CurrentFont;
   $saved[ 'color' ] = $this->TextColor; //EDITEI
   $saved[ 'bgcolor' ] = $this->FillColor; //EDITEI
   $saved[ 'HREF' ] = $this->HREF; //EDITEI
   $saved[ 'underline' ] = $this->underline; //EDITEI
   $saved[ 'strike' ] = $this->strike; //EDITEI
   $saved[ 'SUP' ] = $this->SUP; //EDITEI
   $saved[ 'SUB' ] = $this->SUB; //EDITEI
   $saved[ 'linewidth' ] = $this->LineWidth; //EDITEI
   $saved[ 'drawcolor' ] = $this->DrawColor; //EDITEI
   $saved[ 'is_outline' ] = $this->outline_on; //EDITEI

   return $saved;
}

function restoreFont( $saved )
{
   $this->FontFamily = $saved[ 'family' ];
   $this->FontStyle = $saved[ 'style' ];
   $this->FontSizePt = $saved[ 'sizePt' ];
   $this->FontSize = $saved[ 'size' ];
   $this->CurrentFont =& $saved[ 'curr' ];
   $this->TextColor = $saved[ 'color' ]; //EDITEI
   $this->FillColor = $saved[ 'bgcolor' ]; //EDITEI
   $this->ColorFlag = ($this->FillColor != $this->TextColor); //Restore ColorFlag as well
   $this->HREF = $saved[ 'HREF' ]; //EDITEI
   $this->underline = $saved[ 'underline' ]; //EDITEI
   $this->strike = $saved[ 'strike' ]; //EDITEI
   $this->SUP = $saved[ 'SUP' ]; //EDITEI
   $this->SUB = $saved[ 'SUB' ]; //EDITEI
   $this->LineWidth = $saved[ 'linewidth' ]; //EDITEI
   $this->DrawColor = $saved[ 'drawcolor' ]; //EDITEI
   $this->outline_on = $saved[ 'is_outline' ]; //EDITEI

   if( $this->page > 0)
      $this->_out( sprintf( 'BT /F%d %.2f Tf ET', $this->CurrentFont[ 'i' ], $this->FontSizePt ) );
}
function SetTextOutline($width, $r=0, $g=-1, $b=-1) //EDITEI
{
  if ($width == false) //Now resets all values
  {
    $this->outline_on = false;
    $this->SetLineWidth(0.2);
    $this->SetDrawColor(0);
    $this->_setTextRendering(0);
    $this->_out('0 Tr');
  }
  else
  {
    $this->SetLineWidth($width);
    $this->SetDrawColor($r, $g , $b);
    $this->_out('2 Tr'); //Fixed
  }
}
function _SetTextRendering($mode) {
if (!(($mode == 0) || ($mode == 1) || ($mode == 2)))
$this->Error("Text rendering mode should be 0, 1 or 2 (value : $mode)");
$this->_out($mode.' Tr');
}

	//endrvv
  function verwijderNulwaarden($dataArray)
  {
	   $nulldata = array();//Loop over array om nullen te bepalen.
	   for($regel = 1; $regel < count($dataArray); $regel++ )
	   {
		    for($col = 0; $col < count($dataArray[$regel]); $col++)
		    {
			    if (!is_array($this->excelData[$regel][$col]) && $dataArray[$regel][$col] != '0' && $dataArray[$regel][$col] != '')
			    {
				    $nulldata[$col]="1";
			    }
		    }
	   }
	   $dataZonderNul = array();//Kopie van array maken zonder de nullen
	   for($regel = 0; $regel < count($dataArray); $regel++ )
	   {
		   for($col = 0; $col < count($dataArray[$regel]); $col++)
		   {
			   if ($nulldata[$col] == "1")
			   {
			   $dataZonderNul[$regel][]=$dataArray[$regel][$col];
			   }
		   }
	   }
	   return $dataZonderNul;
  }


  function OutputCSV($filename, $type)
	{

	  if ($this->nullenOnderdrukken == 1)
    {
      $this->excelData =  $this->verwijderNulwaarden($this->excelData);
    }

    if($fp = fopen($filename,"w+"))
    {
      $csvdata = generateCSV($this->excelData);
      fwrite($fp,$csvdata);
      fclose($fp);
    }
    else
    {
      echo "Fout: kan niet schrijven naar ".$filename;
    }

	}
/////////////////////////////////////////////////////////
  function OutputExact($filename,$type="")  // ExactGlobe
  {
//    debug($this->excelData);
//    exit;
    global $__exact;

    switch (ceil(date("n")/4))
    {
      case 2:
        $ditKwartaal = "tweede";
        break;
      case 3:
        $ditKwartaal = "derde";
        break;
      case 4:
        $ditKwartaal = "vierde";
        break;
      default:
        $ditKwartaal = "eerste";
        break;
    }
    $ditJaar = date("Y");

    $__grootboek    = $__exact["grootboek_omzet"]["def"];
    $boekstukoffset = 140000;
    $__dagboek      = $__exact["dagboek"];
    $__omschrijving = $__exact["omschrijving"];
    $__omschrijving = str_replace("{Q}",$ditKwartaal, $__omschrijving);
    $__omschrijving = str_replace("{Y}",$ditJaar,     $__omschrijving);
    $seperator = ";";

    $datum = date("dmY");
    $boekjaar = date("Y");
    $periode = date("m");
    $sep = ";";
    $n = array(
     "",                    //1
     "Dag boek type",       //2
     "Dag boek",            //3
     "Periode",             //4
     "Boek jaar",           //5
     "Boekstuknummer",      //6
     "Omschrijving",        //7
     "",                    //8
     "Grb Rek",             //9
     "Debiteur",            //10
     "Crediteur",           //11
     "Onze ref",            //12
     "Bedrag",              //13
     "Journaliseren in VV", //14
     "Valuta",              //15
     "Wisselkoers",         //16
     "Bet korting",         //17
     "Bet Korting",         //18
     "Vervaldatum factuur", //19
     "Vervaldatum Bet korting",//20
     "BTW code	BTW",       //21
     "bedrag",              //22
     ""                     //23
    );
//    $csvArray[] = $n;

    $db = new DB();

    $row = -1;
    $tel=1;
	  for($regel = 1; $regel < count($this->excelData); $regel++ )
    {
      // repopulate array
      $r = $this->excelData[$regel];
      $n = array();
      $row++;

      $query="SELECT CRM_naw.* FROM CRM_naw WHERE CRM_naw.portefeuille='".$r[12]."'";
      $CRMrec = $db->lookupRecordByQuery($query);

      $query="SELECT * FROM Portefeuilles WHERE Portefeuille='".$r[12]."'";
      $PORTrec = $db->lookupRecordByQuery($query);

      $land = strtolower($CRMrec[land]);

      switch($this->portefeuilledata["Layout"])
      {
        case 12:  // Waterland
          $factuurnr  = $r[11];
          $bedragIncl = $r[29];
          $bedragExcl = $r[27];
          $bedragBTW  = $r[28];

          break;
        default:  // DOO = 33
          $factuurnr  = $r[11];
          $bedragIncl = $r[25];
          $bedragExcl = $r[23];
          $bedragBTW  = $r[24];
      }

       if ($CRMrec["debiteurnr"] == "")
       {
         $debnr = "XXXXX";
       }
       else
       {
         $debnr = $CRMrec["debiteurnr"];
       }
      $__grootboek    = $__exact["grootboek_omzet"]["def"];
      switch ($PORTrec["afwijkendeOmzetsoort"])
      {
        case "VRIJ":          //  Vrijgestelde prestatie
          $__BTWcode = $__exact["BTWcode"]["vrij"];
          $__grootboek = $__exact["grootboek_omzet"]["vrij"];
          break;
        case "EXP":           //  Export buiten de EU
          $__BTWcode = $__exact["BTWcode"]["vrij"];
          $__grootboek = $__exact["grootboek_omzet"]["exp"];
          break;
        case "ICP":           //  Intracommunautaire dienst
          $__BTWcode = $__exact["BTWcode"]["vrij"];
          $__grootboek = $__exact["grootboek_omzet"]["icp"];
          break;
        default:
          switch ((int)$PORTrec["BeheerfeeBTW"])
          {
            case 21:
              $__BTWcode = $__exact["BTWcode"][21];
              break;
            case 6:
              $__BTWcode = $__exact["BTWcode"][6];
              break;
            default:
              $__BTWcode = $__exact["BTWcode"][0];
          }
          $__grootboek    = $__exact["grootboek_omzet"]["def"];
      }

      $periode = "";
      $boekjaar = "";
      $omschrijving = $__omschrijving;

       $n = array(
         "0", //1
         "V", // 2
         $__dagboek,//3
         $periode,//4
         $boekjaar, //5
         $factuurnr, //6
         $omschrijving, //7
         $datum,//8
         "",//9
         $debnr,//10
         "",//11
         "",//12
         $bedragIncl, //13
         "N",//14
         "EUR", //15
         "", //16
         "",//17
         "",//18
         $datum, //19
         "", //20
         "", //21
         "", //22
         "" //23
       );

      $csvArray[] = $n;

      $row++;
      if ($bedragBTW == 0)
      {
        $__BTWcode = $__exact["BTWcode"][0];
      }
      $n = array(
        "1", //1
        "V", // 2
        $__dagboek,//3
        "", //4
        $boekjaar, //5
        $factuurnr, //6
        $omschrijving, //7
        $datum,//8
        $__grootboek,//9
        "",//10
        "",//11
        "",//12
        $bedragIncl, //13
        "N",//14
        "EUR", //15
        "", //16
        "",//17
        "",//18
        "", //19
        "", //20
        $__BTWcode, //21
        $bedragBTW, //22
        "" //23
      );
      $csvArray[] = $n;
      $tel++;
    }

    if($fp = fopen($filename,"w+"))
    {
      foreach ($csvArray as $row)
      {
        fputcsv($fp, $row, $seperator);
      }
      fclose($fp);
    }
    else
    {
      echo "Fout: kan niet schrijven naar ".$filename;
    }

    return;

  }


//////////////////////////////////////////////////////////

  function OutputExactOnline($filename,$type="")
  {
    $afwijkendeOmzetSoorten = Array(
      "ICP",   //  Intracommunautaire dienst
      "EXP",   //  Export buiten de EU
      "VRIJ"   //  Vrijgestelde prestatie
    );

    global $__exactOnline, $__appvar;


    $db = new DB();

    $row = 1;
    $dagboek      = $__exactOnline["dagboek"];

    $seperator = ";";

    $datum = date("d-m-Y");
    $boekjaar = date("Y", $this->rapport_datum);
    $periode = ceil(date("n",$this->rapport_datum)/3);

    $sep = ";";
    $n = array(
      "Volgnr",                   //1
      "Dagboek",                  //2
      "Ordernummer",              //3
      "Deb.nr.",                  //4
      "Boekstuknummer",           //5
      "Kopregel",                 //6
      "Uw referentie",            //7
      "Betalingsreferentie",      //8
      "Factuurdatum",             //9
      "BTW-code",                 //10
      "Bedrag incl. BTW",         //11
      "Grootboekrekeningnummer",  //12
      "Omschrijving grootboek",   //13
    );
    $csvArray[] = $n;


    for($regel = 1; $regel < count($this->excelData); $regel++ )
    {
      // populate array
      $r = $this->excelData[$regel];
      $n = array();


      $query="SELECT CRM_naw.* FROM CRM_naw WHERE CRM_naw.portefeuille='".$r[12]."'";
      $CRMrec = $db->lookupRecordByQuery($query);

      $query="SELECT * FROM Portefeuilles WHERE Portefeuille='".$r[12]."'";
      $PORTrec = $db->lookupRecordByQuery($query);

      $land = strtolower($CRMrec[land]);

      $portefeuille = $r[12];
      $factuurnr    = $r[11];
      $bedragIncl   = $r[25];
      $bedragExcl   = $r[23];
      $bedragBTW    = $r[24];
      $afwijkendeOmzetsoort = $PORTrec["afwijkendeOmzetsoort"];
      $soortOvereenkomst = $PORTrec["SoortOvereenkomst"];




      switch($this->portefeuilledata["Layout"])
      {
        case 12:  // Waterland
          if ($factuurnr < 10000)
          {
            $factuurnummer = substr(10000+$factuurnr,1);
          }
          else
          {
            $factuurnummer =   $this->factuurnummer;
          }

          $bedragIncl = $r[29];
          $bedragExcl = $r[27];
          $bedragBTW  = $r[28];
          $boekstuknr=date("y")."70".$factuurnummer;
          break;
        default:
          $boekstuknr = str_replace(".", "", $factuurnr);
      }

      $debnr = (trim($CRMrec["debiteurnr"]) == "")?"XXXXX":$CRMrec["debiteurnr"];


      switch ($afwijkendeOmzetsoort)
      {
        case "VRIJ":
          $BTWcode = $__exactOnline["BTWcode"]["vrij"];
          $grootboek = $__exactOnline["grootboek_omzet"]["vrij"];
          break;
        case "EXP":
          $BTWcode = $__exactOnline["BTWcode"]["vrij"];
          $grootboek = $__exactOnline["grootboek_omzet"]["exp"];
          break;
        case "ICP":
          $BTWcode = $__exactOnline["BTWcode"]["vrij"];
          $grootboek = $__exactOnline["grootboek_omzet"]["icp"];
          break;
        default:
          switch ((int) $PORTrec["BeheerfeeBTW"])
          {
            case 21:
              $BTWcode = $__exactOnline["BTWcode"]["21"];
              $grootboek = $__exactOnline["grootboek_omzet"]["21"];
              break;
            default:
              $BTWcode = $__exactOnline["BTWcode"]["0"];
              $grootboek = $__exactOnline["grootboek_omzet"]["0"];
          }

      }

      if ($__appvar["bedrijf"] == "WAT")
      {

        if (  $afwijkendeOmzetsoort == "EXP" OR
              $afwijkendeOmzetsoort == "ICP" )
        {
          switch ((int) $PORTrec["BeheerfeeBTW"])
          {
            case 21:
              $BTWcode = $__exactOnline["BTWcode"]["21"];
              $grootboek = $__exactOnline["grootboek_omzet"]["21"];
              break;
            default:
              $BTWcode = $__exactOnline["BTWcode"]["0"];
              $grootboek = $__exactOnline["grootboek_omzet"]["0"];
          }
        }
//Portefeuilleadvies
//Overige advieswerkzaamheden
        switch ($soortOvereenkomst)  // call 8101 Waterland
        {

          case "Overige advieswerkzaamheden":  //

            $omschrijving = "Fee advieswerkzaamh. Q$periode $boekjaar";
            switch ((int)$PORTrec["BeheerfeeBTW"])
            {
              case 21:
//                $BTWcode = $__exactOnline["BTWcode"]["21"];
                $grootboek = $__exactOnline["grootboek_overigeAdvieswerkzaamheden"]["21"];
                break;
              default:
//                $BTWcode = $__exactOnline["BTWcode"]["0"];
                $grootboek = $__exactOnline["grootboek_overigeAdvieswerkzaamheden"]["0"];
            }
            break;
          case "Portefeuilleadvies":  //

            $omschrijving = $__exactOnline["grootboek_portefeuilleadvies"]["omschrijving"] . " Q$periode $boekjaar";
            switch ((int)$PORTrec["BeheerfeeBTW"])
            {
              case 21:
//                $BTWcode = $__exactOnline["BTWcode"]["21"];
                $grootboek = $__exactOnline["grootboek_portefeuilleadvies"]["21"];
                break;
              default:
//                $BTWcode = $__exactOnline["BTWcode"]["0"];
                $grootboek = $__exactOnline["grootboek_portefeuilleadvies"]["0"];
            }
            break;
          case "Beleggingsadvies":
          case "Vermogensbegeleiding":
            $omschrijving = $__exactOnline["grootboek_vermogensbegeleiding"]["omschrijving"] . " Q$periode $boekjaar";
            switch ((int)$PORTrec["BeheerfeeBTW"])
            {
              case 21:
//                $BTWcode = $__exactOnline["BTWcode"]["21"];
                $grootboek = $__exactOnline["grootboek_vermogensbegeleiding"]["21"];
                break;
              default:
//                $BTWcode = $__exactOnline["BTWcode"]["0"];
                $grootboek = $__exactOnline["grootboek_vermogensbegeleiding"]["0"];
            }
            break;
          default:
            $omschrijving = $__exactOnline["omschrijving"] . " Q$periode $boekjaar";
            break;
        }
      }
      else
      {
        $omschrijving = $__exactOnline["omschrijving"] . " Q$periode $boekjaar";
      }


//      if ($afwijkendeOmzetsoort == "VRIJ")
//      {
//        $BTWcode = $__exactOnline["BTWcode"]["vrij"];
//        $grootboek = $__exactOnline["grootboek_omzet"]["vrij"];
//      }
//      else
//      {
//        switch ((int) $PORTrec["BeheerfeeBTW"])
//        {
//          case 21:
//            $BTWcode = $__exactOnline["BTWcode"]["21"];
//            $grootboek = $__exactOnline["grootboek_omzet"]["21"];
//            break;
//          default:
//            $BTWcode = $__exactOnline["BTWcode"]["0"];
//            $grootboek = $__exactOnline["grootboek_omzet"]["0"];
//        }
//      }

      $n = array(
        $row,               //1
        $dagboek,           //2
        $portefeuille,      //3
        $debnr,             //4
        $boekstuknr,        //5
        $omschrijving,      //6
        $omschrijving,      //7
        $boekstuknr,        //8
        $datum,             //9
        $BTWcode,           //10
        $bedragIncl,        //11
        $grootboek,         //12
        $omschrijving       //13
      );

      $csvArray[] = $n;

      $row++;
    }



    $this->excelData = $csvArray;

    $this->OutputXls($filename);

//    if($fp = fopen($filename,"w+"))
//    {
//      foreach ($csvArray as $row)
//      {
//        fputcsv($fp, $row, $seperator);
//      }
//      fclose($fp);
//    }
//    else
//    {
//      echo "Fout: kan niet schrijven naar ".$filename;
//    }

    return;

  }


//////////////////////////////////////////////////////////

	function OutputTwinfield($filename,$type="")
	{
    global $__twinfield;
    
    $n[] = array(
      "Code",  
      "Valuta",  
      "Factuurdatum",  
      "Periode",  
      "Factuurnummer",  
      "Vervaldatum",  
      "Nummer",  
      "Grtboekrek",  
      "Rel/KPL",  
      "Prj/activa",  
      "Bedrag",  
      "DebitCredit",  
      "Omschrijving",  
      "Btwcode"
    );
    
    $db = new DB();
	  include_once('../classes/excel/Writer.php');
	  if($type=='S')
		  $workbook = new Spreadsheet_Excel_Writer();
		else
		  $workbook = new Spreadsheet_Excel_Writer($filename);

    $worksheet =& $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }



    if ($this->nullenOnderdrukken == 1)
	  {
	   $this->excelData =  $this->verwijderNulwaarden($this->excelData);
	  }
$row = 0;
	   for($regel = 1; $regel < count($this->excelData); $regel++ )
	   {
       // repopulate array
       $r = $this->excelData[$regel];
       $n = array();
       
       $btw0 = ( (int)$r[24] == 0);
       
       $query="SELECT CRM_naw.* FROM CRM_naw WHERE CRM_naw.portefeuille='".$r[12]."'"; 
       $CRMrec = $db->lookupRecordByQuery($query);
       
       $land = strtolower($CRMrec[land]);
       //debug($r);
       
       if ($CRMrec["debiteurnr"] == "")
       {
         $debnr = "XXXXX";
       }
       else
       {
         $debnr = $CRMrec["debiteurnr"];   
       }

       $grootboek = $__twinfield["grootboek_omzet"];
       $btwCode   = $__twinfield["BTWcode"];

       
       if ($btw0)
       {
         if ($land == "nederland")
         {
            $grootboek = $__twinfield["grootboek_omzetBTW0NL"];
            $btwCode   = $__twinfield["BTWcode0"];
         }
         else
         {
           $grootboek = $__twinfield["grootboek_omzetBTW0"];
           $btwCode   = $__twinfield["BTWcode0"];
         }
       }
       
       $n = array("VRK",
                  "EUR",
                  $r[10],
                  str_replace("-","/",substr($r[10],0,7)),
                  $r[11],
                  $r[10],
                  $regel,
                  $__twinfield["grootboek_debiteur"],
                  $debnr,
                  "",
                  $r[25],
                  "debit",
                  $__twinfield["omschrijving"].$r[12],
                  ""
                  );

       for($col = 0; $col < count($n); $col++)
       {
         $waarde= $n[$col];
         if(($col == 2 or $col == 5) AND $row <> 0)
         {
           $datum=round((adodb_db2jul($waarde)+(86400 * 25569))/86400);
           $worksheet->write($row, $col, $datum,$opmaak['date']);
         }
         else
           $worksheet->write($row, $col, $waarde);
       }
       $row++;

       $n = array("VRK",
                  "EUR",
                  $r[10],
                  str_replace("-","/",substr($r[10],0,7)),
                  $r[11],
                  $r[10],
                  $regel,
                  $grootboek,
                  "",
                  "",
                  $r[23],
                  "credit",
                  $__twinfield["omschrijving"].$r[12],
                  $btwCode
                  );
       for($col = 0; $col < count($n); $col++)
       {
         $waarde= $n[$col];
         if(($col == 2 or $col == 5) AND $row <> 0)
         {
           $datum=round((adodb_db2jul($waarde)+(86400 * 25569))/86400);
           $worksheet->write($row, $col, $datum,$opmaak['date']);
         }
         else
           $worksheet->write($row, $col, $waarde);
       }
       $row++;

        
	   }
     if($type=='S')
       $workbook->send($filename);
	   $workbook->close();
	}
  //////////////////////////////////////////////////////////

	function OutputSnelstart($filename,$type="")
	{
    global $__snelstart;

    $n = array(
      "fldDagboek",
      "fldBoekingcode",
      "Datum",
      "Grootboeknummer",
      "Debet",
      "Credit",
      "ImportBoekingID",
      "Volgnummer",
      "Boekstuk",
      "Omschrijving",
      "Relatiecode",
      "Factuurnummer",
      "Kostenplaatsnummer"
    );
    $row = 0;

    $db = new DB();
	  include_once('../classes/excel/Writer.php');
	  if($type=='S')
		  $workbook = new Spreadsheet_Excel_Writer();
		else
		  $workbook = new Spreadsheet_Excel_Writer($filename);

    $worksheet = $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }

    for($col = 0; $col < count($n); $col++)
    {
      $waarde= $n[$col];
      $worksheet->write($row, $col, $waarde);
    }

    $row++;

    if ($this->nullenOnderdrukken == 1)
    {
     $this->excelData =  $this->verwijderNulwaarden($this->excelData);
    }

	   for($regel = 1; $regel < count($this->excelData); $regel++ )
	   {
       // repopulate array
       $r = $this->excelData[$regel];
       $n = array();
       $portefeuille = $r[12];
       $bedrag_excl  = $r[23];
       $bedrag_btw   = $r[24];
       $bedrag_incl  = $r[25];
       $facnr        = $r[11];
       $datumTot     = explode("-",$r[10]);

       //$facDatum     = str_replace("-","/",substr($r[10],0,7));
//       $facDatum     = substr($r[10],8,2)."-".substr($r[10],5,2)."-".substr($r[10],0,4);
       $facDatum     = date("d-m-Y"); // call 6784

       $query="SELECT CRM_naw.* FROM CRM_naw WHERE CRM_naw.portefeuille='".$portefeuille."'";
       $CRMrec = $db->lookupRecordByQuery($query);

       $query="SELECT * FROM Portefeuilles WHERE Portefeuille='$portefeuille'";
       $PORTrec = $db->lookupRecordByQuery($query);

       $afwijkendeOmzetsoort = $PORTrec["afwijkendeOmzetsoort"];

       $omschrijving = $CRMrec["naam"]. " ". $__snelstart["omschrijving"]." ".$portefeuille.". Q".ceil($datumTot[1]/3)." ".$datumTot[0];  // call 6784

       if ($CRMrec["debiteurnr"] == "")
       {
         $debnr = "XXXXX";
       }
       else
       {
         $debnr = $CRMrec["debiteurnr"];
       }

       // Inclusief BTW boeking
       $grootboek = $__snelstart["gb"]["inclusief"];

       $bedrag = $bedrag_incl;
       if ($bedrag > 0)
       {
         $credit = abs($bedrag);
         $debet  = 0;
       }
       else
       {
         $credit = 0;
         $debet  = abs($bedrag);
       }

       $n = array(
                    $__snelstart["dagboek"],
                    $regel,
                    $facDatum,
                    $grootboek,
                    $credit,
                    $debet,
                    "",  // leeg
                    "", // leeg
                    $facnr,
                    $omschrijving,
                    $debnr,
                    $facnr,
                    ""  // leeg

                 );

       for($col = 0; $col < count($n); $col++)
       {
         $waarde= $n[$col];
//         if(($col == 2 ) AND $row <> 0)
//         {
//           $datum=round((adodb_db2jul($waarde)+(86400 * 25569))/86400);
//           $worksheet->write($row, $col, $datum,$opmaak['date']);
//         }
//         else
           $worksheet->write($row, $col, $waarde);
       }
       $row++;

       // Exclusief BTW boeking
       $grootboek = $__snelstart["gb"]["exclusief"];

       if ($afwijkendeOmzetsoort == "VRIJ")
       {
         $grootboek = $__snelstart["gb"]["VRIJ"];
       }

       if ($afwijkendeOmzetsoort == "ICP")
       {
         $grootboek = $__snelstart["gb"]["ICP"];
       }

       if ($afwijkendeOmzetsoort == "EXP")
       {
         $grootboek = $__snelstart["gb"]["EXP"];
       }


       $bedrag = $bedrag_excl * -1;
       if ($bedrag > 0)
       {
         $credit = abs($bedrag);
         $debet  = 0;
       }
       else
       {
         $credit = 0;
         $debet  = abs($bedrag);
       }

       $n = array(
         $__snelstart["dagboek"],
         $regel,
         $facDatum,
         $grootboek,
         $credit,
         $debet,
         "",  // leeg
         "", // leeg
         $facnr,
         $omschrijving,
         $debnr,
         $facnr,
         ""  // leeg

       );

       for($col = 0; $col < count($n); $col++)
       {
         $waarde= $n[$col];
//         if(($col == 2 ) AND $row <> 0)
//         {
//           $datum=round((adodb_db2jul($waarde)+(86400 * 25569))/86400);
//           $worksheet->write($row, $col, $datum,$opmaak['date']);
//         }
//         else
           $worksheet->write($row, $col, $waarde);
       }
       $row++;

       /// BTW boeking
       $grootboek = $__snelstart["gb"]["btw"];
       $bedrag = $bedrag_btw * -1;
       if ($bedrag > 0)
       {
         $credit = abs($bedrag);
         $debet  = 0;
       }
       else
       {
         $credit = 0;
         $debet  = abs($bedrag);
       }

       $n = array(
         $__snelstart["dagboek"],
         $regel,
         $facDatum,
         $grootboek,
         $credit,
         $debet,
         "",  // leeg
         "", // leeg
         $facnr,
         $omschrijving,
         $debnr,
         $facnr,
         ""  // leeg

       );

       for($col = 0; $col < count($n); $col++)
       {
         $waarde= $n[$col];
//         if(($col == 2 ) AND $row <> 0)
//         {
//           $datum=round((adodb_db2jul($waarde)+(86400 * 25569))/86400);
//           $worksheet->write($row, $col, $datum,$opmaak['date']);
//         }
//         else
           $worksheet->write($row, $col, $waarde);
       }
       $row++;

	   }
     if($type=='S')
       $workbook->send($filename);
	   $workbook->close();
	}


  function belGirAddToTRM($data, $settings)
  {
    global $USR;
    $db = new DB();
    $dp = explode("-", $data[10]);
    $query = "
          INSERT INTO TijdelijkeRekeningmutaties SET
            add_date            = NOW()
          , add_user            = '{$USR}'
          , change_date         = NOW()
          , change_user         = '{$USR}'  
          , `bankTransactieId`  = '".$settings["kenmerk"].str_replace("-", "", $data[10])."'
          , `Boekdatum`         = (NOW() - INTERVAL 1 DAY);
          , `settlementDatum`   = (NOW() - INTERVAL 1 DAY);
          , `Rekening`          = '{$data[12]}EUR'
          , `Valuta`            = 'EUR'
          , `Valutakoers`       = '1'
          , `Fonds`             = ''
          , `Aantal`            = 0
          , `Fondskoers`        = 0
          , `Grootboekrekening` = '{$settings["grootboekrekening"]}'
          , `Debet`             = '{$data[25]}'
          , `Credit`            = 0
          , `Bedrag`            = '".($data[25]*-1)."'
          , `Omschrijving`      = '{$settings["omschrijving"]} ".$dp[1]."-".$dp[0]."'
          , `Transactietype`    = ''
          , `Verwerkt`          = 0
          , `Memoriaalboeking`  = 0
          
          ";
    $db->executeQuery($query);
  }

 	function OutputXls($filename,$type="",$fileFormat)
	{
		global $__appvar, $__excelFee;
	  if($fileFormat=='xlsx')
    {
			writeXlsx($this->excelData,$filename);
    }
    else
    {
	  include_once('../classes/excel/Writer.php');
	  if($type=='S')
		  $workbook = new Spreadsheet_Excel_Writer();
		else
		  $workbook = new Spreadsheet_Excel_Writer($filename);

    $worksheet =& $workbook->addWorksheet();
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }

    if ($this->nullenOnderdrukken == 1)
	  {
	   $this->excelData =  $this->verwijderNulwaarden($this->excelData);
	  }
     $grootboekIndex = -1;
	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {

		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     ////////////////////////////////////////////////////////
		     // call 7126 start
		     if ($this->excelData[$regel][$col] == "afwijkendeOmzetsoort" AND $regel == 0)
         {
           $grootboekIndex = $col;
         }
         if ($grootboekIndex == $col AND $regel > 0 AND count($__excelFee["gb"]) == 4)
         {
           $act = $this->excelData[$regel][$col];
           switch ($act)
           {
             case "ICP":
             case "EXP":
             case "VRIJ":
               $this->excelData[$regel][$col] = $__excelFee["gb"][$act];
               break;
             default:
               $this->excelData[$regel][$col] = $__excelFee["gb"]["LEEG"];
               break;
           }
         }
         // call 7126 einde
         ////////////////////////////////////////////////////////
         ///
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $waarde=$this->excelData[$regel][$col];
	         $worksheet->write($regel, $col, $waarde);
		     }
		   }
	   }
     if($type=='S')
       $workbook->send($filename);
	   $workbook->close();
     }
	}


 	function OutputBeleggersgiro($filename,$type="",$fileFormat)
	{
		global $__appvar, $__excelFee, $__beleggersgiro;
	  if($fileFormat=='xlsx')
    {
			writeXlsx($this->excelData,$filename);
    }
    else
    {
	    include_once('../classes/excel/Writer.php');
	    if($type=='S')
      {
        $workbook = new Spreadsheet_Excel_Writer();
      }
  		else
      {
        $workbook = new Spreadsheet_Excel_Writer($filename);
      }

      $worksheet =& $workbook->addWorksheet();
      $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
      while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
      {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
      }

      if ($this->nullenOnderdrukken == 1)
	    {
	    $this->excelData =  $this->verwijderNulwaarden($this->excelData);
	    }

      $knmCol = 55;
      $this->excelData[0][$knmCol] = "Kenmerk";
      for($regel = 1; $regel < count($this->excelData); $regel++ )
      {
        $this->excelData[$regel][$knmCol] = $__beleggersgiro["kenmerk"];
      }
//      debug($this->excelData);
//      exit;
      $grootboekIndex = -1;
      for($regel = 0; $regel < count($this->excelData); $regel++ )
      {

        if ($regel > 0)
        {
          $this->belGirAddToTRM($this->excelData[$regel], $__beleggersgiro);
        }

        for($col = 0; $col < count($this->excelData[$regel]); $col++)
        {
          if (is_array($this->excelData[$regel][$col]))
          {
            //$opmaak[$opmaakSleutel]
            $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
            $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
          }
          else
          {
            $waarde=$this->excelData[$regel][$col];
            $worksheet->write($regel, $col, $waarde);
          }
        }

      }

       if($type=='S')
       {
         $workbook->send($filename);
       }

       $workbook->close();
     }
	}



	function fillXlsSheet($worksheet,$workbook='')
	{
	  if($workbook=='')
	    $workbook = new Spreadsheet_Excel_Writer();
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }

    }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col]);
		     }
		   }
	   }
	}

	function switchFont($style)
	{

	  if($this->rapport_style[$style]['bgcolor'])
	    $this->SetFillColor($this->rapport_style[$style]['bgcolor']['r'],$this->rapport_style[$style]['bgcolor']['g'],$this->rapport_style[$style]['bgcolor']['b']);
	  if($this->rapport_style[$style]['fontcolor'])
		  $this->SetTextColor($this->rapport_style[$style]['fontcolor']['r'],$this->rapport_style[$style]['fontcolor']['g'],$this->rapport_style[$style]['fontcolor']['b']);
		if($this->rapport_style[$style]['font'])
		  $this->SetFont($this->rapport_style[$style]['font']['font'],$this->rapport_style[$style]['font']['style'],$this->rapport_style[$style]['font']['fontSize']);

	 if($this->rapport_style[$style]['rowHeight'])
		  $this->rowHeight = $this->rapport_style[$style]['rowHeight'];
		else
		  $this->rowHeight = 4;

		if($this->rapport_style[$style]['line'])
		  $this->SetLineStyle($this->rapport_style[$style]['line']);

		  $this->lastStyle = $style;
	}

	function TextWithDirection($x,$y,$txt,$direction='R')
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
    if ($direction=='R')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='L')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='U')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    elseif ($direction=='D')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
    else
        $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);
    if ($this->ColorFlag)
        $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

function TextWithRotation($x,$y,$txt,$txt_angle,$font_angle=0)
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

    $font_angle+=90+$txt_angle;
    $txt_angle*=M_PI/180;
    $font_angle*=M_PI/180;

    $txt_dx=cos($txt_angle);
    $txt_dy=sin($txt_angle);
    $font_dx=cos($font_angle);
    $font_dy=sin($font_angle);

    $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',
             $txt_dx,$txt_dy,$font_dx,$font_dy,
             $x*$this->k,($this->h-$y)*$this->k,$txt);
    if ($this->ColorFlag)
        $s='q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}


function getKwartaal($rapportageJul)
{
   return(floor(date("n",$rapportageJul)/3));
}

  function preFillColumn($startY,$stopY)
  {
	  $sum = $this->marge;
	  foreach ($this->widths as $key=>$value)
	  {
      if($this->fillCell[$key] == 1)
      {
        $this->Rect($sum, $startY,$value, $stopY-$startY, 'F');
      }
      $sum += $value;
    }
  }

  function OutputDatabase()
	{
	  global $USR;
	  $db=new DB();
	  $table="reportbuilder_$USR";
	  $query="SHOW TABLES like '$table'";
	  if($db->QRecords($query) > 0)
	  {
	    $db->SQL("DROP table $table");
	    $db->Query();
	  }
    if($this->dbTable)
    {
      $db->SQL($this->dbTable);
	    $db->Query();
	    $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }
    if(is_array($this->dbWaarden))
    {
      foreach ($this->dbWaarden as $regel=>$waarden)
      {
        $query="INSERT INTO $table SET add_date=now() ";
        //listarray($waarden);
        foreach ($waarden as $key=>$value)
        {
          if (isNumeric(substr($key,0,1)))
          {
            // skip variabele met cijfer
          }
          else
          {
            $query.=",$key='".addslashes($value)."' ";
          }



        }
        $db->SQL($query);
	      $db->Query();
      }
    }

	}
}

?>