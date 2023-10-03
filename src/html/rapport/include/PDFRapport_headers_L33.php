<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/04/27 18:30:34 $
 		File Versie					: $Revision: 1.71 $

 		$Log: PDFRapport_headers_L33.php,v $
 		Revision 1.71  2019/04/27 18:30:34  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2019/02/20 16:50:39  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2019/02/16 19:23:35  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2019/02/09 18:40:16  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2019/01/26 19:33:28  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2018/12/15 17:49:14  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2018/09/08 17:43:29  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2018/09/06 15:32:16  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2018/05/12 15:46:42  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2018/01/31 17:21:26  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2018/01/27 17:31:22  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2018/01/06 18:10:41  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2017/08/02 18:23:27  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.57  2016/10/19 11:07:36  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2016/10/14 09:56:18  rvv
 		*** empty log message ***
 		
 	
*/
function Header_basis_L33($object)
{
   $pdfObject = &$object;
   
    $DB=new DB();
    if(!isset($pdfObject->RapportagetenaamstellingFound))
    {
      $pdfObject->RapportagetenaamstellingFound=false;
      $query="SHOW COLUMNS FROM CRM_naw";
      $DB->SQL($query);
      $DB->Query();
      while($data=$DB->NextRecord())
      {
        if($data['Field']=='Rapportagetenaamstelling')
          $pdfObject->RapportagetenaamstellingFound=true;
      }
    }
    if($pdfObject->RapportagetenaamstellingFound==true)
    { 
      $query = "SELECT CRM_naw.Rapportagetenaamstelling FROM CRM_naw WHERE Portefeuille = '".$pdfObject->portefeuilledata['Portefeuille']."'";
      $DB->SQL($query);
      $CRM_naw = $DB->lookupRecord();
      $pdfObject->Rapportagetenaamstelling=$CRM_naw['Rapportagetenaamstelling'];
    }
    
 
		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
      
      if($pdfObject->rapport_naam1=='')
        $pdfObject->rapport_naam1='Geconsolideerd';      
		}   
    
    if($CRM_naw['Rapportagetenaamstelling']=='')
      $CRM_naw['Rapportagetenaamstelling']=$pdfObject->rapport_naam1;


  if($pdfObject->lastPortefeuille != $pdfObject->portefeuilledata['Portefeuille'] && !empty($pdfObject->lastPortefeuille))
  {
    $pdfObject->rapportNewPage = $pdfObject->page;
    unset($pdfObject->pageBottom);

  }


    if ($pdfObject->rapport_type == "BRIEF")
    {
      $pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "BLANK")
    {
      //
    }
    elseif ($pdfObject->rapport_type == "JOURNAAL")
    {
      //
    }
    elseif ($pdfObject->rapport_type == "FACTUUR")
    {
      //$pdfObject->HeaderFACTUUR();
    }
    elseif ($pdfObject->rapport_type == "FRONT")
    {
		  $pdfObject->SetTextColor($pdfObject->rapport_kop2_fontcolor[r],$pdfObject->rapport_kop2_fontcolor[g],$pdfObject->rapport_kop2_fontcolor[b]);
		  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      //if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		  $pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;
      unset($pdfObject->pageTop);
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


		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" )
			$logopos = 45;
		else
			$logopos = 90;

		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
      $logoYpos=10;
		  $xSize=19;
	    $pdfObject->Image($pdfObject->rapport_logo,297/2-$xSize/2, $logoYpos, $xSize);
    }


		if($pdfObject->rapport_type == "MOD" )
			$x = 60;
		else
			$x = 150;
      
    $pdfObject->SetDrawColor(0,0,0);
    $pdfObject->SetFillColor(0,0,0);
    
//    $pdfObject->SetDrawColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);
//    $pdfObject->SetFillColor($pdfObject->kopkleur[0],$pdfObject->kopkleur[1],$pdfObject->kopkleur[2]);

   // echo $x+140;exit;
		$pdfObject->Line(5,34,$x+142,34);
		$pdfObject->SetY($y);
    $widthsBackup=$pdfObject->widths;
    $alignBackup=$pdfObject->aligns;
		$pdfObject->SetWidths(array(35,10,140,30,5,60));
		$pdfObject->SetAligns(array('L','C','L','L','C','L'));
		$pdfObject->SetXY($pdfObject->marge,35);


    $periode=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." - ".
	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);
    
    
    
	  $pdfObject->Row(array(vertaalTekst('Cliënt',$pdfObject->rapport_taal).':','',$CRM_naw['Rapportagetenaamstelling'],
    vertaalTekst("Rapportageperiode",$pdfObject->rapport_taal).':','',$periode));// .' '. $pdfObject->rapport_naam2
	  //$pdfObject->ln(1);

	  if($pdfObject->rapport_taal == 1)
	  {
	    $pdfObject->portefeuilledata['AccountmanagerNaam']=str_replace("De heer","Mr.",$pdfObject->portefeuilledata['AccountmanagerNaam']);
	    $pdfObject->portefeuilledata['AccountmanagerNaam2']=str_replace("De heer","Mr.",$pdfObject->portefeuilledata['AccountmanagerNaam2']);
	  }


    if($pdfObject->portefeuilledata['AccountmanagerNaam2'] <> '')
      $accountmanagers=$pdfObject->portefeuilledata['AccountmanagerNaam']." ".vertaalTekst("en",$pdfObject->rapport_taal)." ".strtolower(substr($pdfObject->portefeuilledata['AccountmanagerNaam2'],0,1)).substr($pdfObject->portefeuilledata['AccountmanagerNaam2'],1);
    else
      $accountmanagers=$pdfObject->portefeuilledata['AccountmanagerNaam'];



    
    
    
    
	  $pdfObject->Row(array(vertaalTekst("Vermogensbeheerders",$pdfObject->rapport_taal).':','',$accountmanagers,
    vertaalTekst("Datum rapport",$pdfObject->rapport_taal).':',"",date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y")));

	  $pdfObject->Line(5,$pdfObject->getY()+1,$x+142,$pdfObject->getY()+1);
	  $pdfObject->SetXY(50,52);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
	  $pdfObject->MultiCell($x+50,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+4;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }
    $pdfObject->lastRapport_type = $pdfObject->rapport_type ;
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    $pdfObject->widths=$widthsBackup;
    $pdfObject->aligns=$alignBackup;
//echo $pdfObject->rapport_type." ".$pdfObject->customPageNo." <br>\n";
}


function HeaderJOURNAAL_L33($object)
{
  $pdfObject = &$object;

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

  //rapport_risicoklasse
  if(is_file($pdfObject->rapport_logo))
  {
    $logoYpos=10;
    $xSize=19;
    $pdfObject->Image($pdfObject->rapport_logo,297/2-$xSize/2, $logoYpos, $xSize);
  }

  $pdfObject->SetDrawColor(0,0,0);
  $pdfObject->SetFillColor(0,0,0);


  // echo $x+140;exit;
  $x = 150;

  $pdfObject->Line(5,34,$x+142,34);
  $pdfObject->SetY($y);

  $pdfObject->SetWidths(array(35,10,140,30,5,60));
  $pdfObject->SetAligns(array('L','C','L','L','C','L'));
  $pdfObject->SetXY($pdfObject->marge,35);


  $periode=date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." - ".
    date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum);



  $pdfObject->Row(array('','','',
                    vertaalTekst("Rapportageperiode",$pdfObject->rapport_taal).':','',$periode));// .' '. $pdfObject->rapport_naam2

  $pdfObject->Row(array('','','',
                    vertaalTekst("Datum rapport",$pdfObject->rapport_taal).':',"",date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y")));

  $pdfObject->Line(5,$pdfObject->getY()+1,$x+142,$pdfObject->getY()+1);
  $pdfObject->SetXY(50,52);
  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
  $pdfObject->MultiCell($x+50,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
  $pdfObject->headerStart = $pdfObject->getY()+4;
  $pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
  $pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
//echo $pdfObject->rapport_type." ".$pdfObject->customPageNo." <br>\n";
}

	function HeaderVKM_L33($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMD_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVKM();
}

function HeaderTRANSFEE_L33($object)
{
  $pdfObject = &$object;

}
	  function HeaderHSE_L33($object)
	  {
	    $pdfObject = &$object;

      $pdfObject->ln();
      $dataWidth=array(28,60,21,21,21,21,22,22,22,22,22);
 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','L','L','R','R','R','R','R','R','R','R','R'));
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->ln();
      $lastColors=$pdfObject->CellFontColor;
      unset($pdfObject->CellFontColor);
      $pdfObject->Row(array(vertaalTekst("Risico\ncategorie",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Fonds",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
      "\n".date('d-m-Y',$pdfObject->rapport_datumvanaf),
      "\n".date('d-m-Y',$pdfObject->rapport_datum),
      "\n".vertaalTekst("Stortingen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
      vertaalTekst("Gemiddeld vermogen",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Resultaat %",$pdfObject->rapport_taal),
      "\n".vertaalTekst("Weging",$pdfObject->rapport_taal),
      "".vertaalTekst("Bijdrage",$pdfObject->rapport_taal)."\n".vertaalTekst("rendement",$pdfObject->rapport_taal)));
      $pdfObject->CellFontColor=$lastColors;
      $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->SetLineWidth(0.1);
    }

  function HeaderBLANK_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
  function HeaderDEF_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderOIH_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

function HeaderSCENARIO_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

function HeaderCASH_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
}

	function HeaderZORG_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  
	function HeaderCASHY_L33($object)
	{
    $pdfObject = &$object;


	}

		function HeaderCASHYV_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetWidths($pdfObject->widthA);
	    $pdfObject->SetAligns($pdfObject->alignA);
    	$pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    	$pdfObject->ln();
    	if($pdfObject->templateVars['CASHYVPaginas'] =='')
    	{
    	  $pdfObject->SetWidths(array(25+50+25+25,50,15+50+25));
    	  $pdfObject->SetAligns(array('C','C','C'));
        $pdfObject->row(array( vertaalTekst("De komende twee jaar",$pdfObject->rapport_taal) ,'', vertaalTekst("Totalen per jaar",$pdfObject->rapport_taal)));
	      $pdfObject->SetWidths($pdfObject->widthA);
	      $pdfObject->SetAligns($pdfObject->alignA);
 		    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
 		    $pdfObject->Line($pdfObject->marge+ (25+50+25+25+50),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
 		    //$pdfObject->row(array('Datum',"Instrument","Coupon/Lossing","Bedrag",'','Jaar',"Lossing","Rente","Totaal"));

 		    $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),
 		    vertaalTekst("Instrument",$pdfObject->rapport_taal),
 		    vertaalTekst("Coupon/Lossing",$pdfObject->rapport_taal),
 		    vertaalTekst("Bedrag",$pdfObject->rapport_taal),'',
 		    vertaalTekst("Jaar",$pdfObject->rapport_taal),
 		    vertaalTekst("Lossing",$pdfObject->rapport_taal),
 		    vertaalTekst("Rente",$pdfObject->rapport_taal),
 		    vertaalTekst("Totaal",$pdfObject->rapport_taal)));

 		    $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
 		    $pdfObject->Line($pdfObject->marge+ (25+50+25+25+50),$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
    	}
    	else
    	{
    	  $pdfObject->row(array(vertaalTekst("Datum",$pdfObject->rapport_taal),vertaalTekst("Instrument",$pdfObject->rapport_taal),vertaalTekst("Coupon/Lossing",$pdfObject->rapport_taal),vertaalTekst("Bedrag",$pdfObject->rapport_taal)));
    	  $pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + (25+50+25+25),$pdfObject->GetY());
    	  //$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthA),$pdfObject->GetY());
    	}
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

	}

  function HeaderFRONT_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	  function HeaderINDEX_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderEND_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderOIB_L33($object)
	{
	    $pdfObject = &$object;
	    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

  function HeaderATT_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}
  function HeaderPERF_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

function HeaderSMV_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->headerSMV();
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

}

function HeaderHUIS_L33($object)
{
  $pdfObject = &$object;
 HeaderOIS_L33($pdfObject);
  
}

function HeaderRISK_L33($object)
{
  $pdfObject = &$object;
}

function HeaderDUURZAAM_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  //$pdfObject->SetWidths(array(63,15,18,18,23, 20,  25,25,25,25,20)); //20
  $pdfObject->SetWidths(array(63,15,26,20,23, 18,  21,28,20));
  $positie=array();
  foreach ($pdfObject->widths as $id=>$waarde)
  {
    if($id < 1)
      $positie['fondsStart'] +=$waarde;
    if($id < 5)
      $positie['fondsEind'] +=$waarde;
    if($id < 6)
    {
      $positie['waardeStart'] +=$waarde;
      if($id==5)
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
  $pdfObject->setXY($positie['fondsStart'],$y);
  $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
  $pdfObject->setXY($positie['waardeStart'],$y);
  if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
  else
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

  $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
  $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

  $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R'));
  $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  //$pdfObject->row(array("\nNaam","\nValuta","\nAantal","\nKoers","Begin\nKoers",'',"\nMarktwaarde","\nBeginwaarde","Ongerealiseerd\nresultaat","%\nportf.","Opgelopen\nrente"));

  $pdfObject->row(array(
                    "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Begin",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
                    '',
                    "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Resultaat",$pdfObject->rapport_taal),
                    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal)));

  unset($pdfObject->CellBorders);
}

  function HeaderOIS_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    //$pdfObject->SetWidths(array(63,15,18,18,23, 20,  25,25,25,25,20)); //20
    $pdfObject->SetWidths(array(63,15,26,20,23, 18,  21,28,28,20,20));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 1)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
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
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);
   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nNaam","\nValuta","\nAantal","\nKoers","Begin\nKoers",'',"\nMarktwaarde","\nBeginwaarde","Ongerealiseerd\nresultaat","%\nportf.","Opgelopen\nrente"));

    if($pdfObject->rapport_type=='HUIS')
    {
      $pdfObject->row(array(
                        "\n" . vertaalTekst("Naam", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Valuta", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Aantal", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                        vertaalTekst("Begin", $pdfObject->rapport_taal) . "\n" . vertaalTekst("koers", $pdfObject->rapport_taal),
                        '',
                        "\n" . vertaalTekst("Marktwaarde", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
                        '','',
                        vertaalTekst("%", $pdfObject->rapport_taal) . "   \n" . vertaalTekst("portf.", $pdfObject->rapport_taal)));
    }
    else
    {
      $pdfObject->row(array(
                        "\n" . vertaalTekst("Naam", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Valuta", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Aantal", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Koers", $pdfObject->rapport_taal),
                        vertaalTekst("Begin", $pdfObject->rapport_taal) . "\n" . vertaalTekst("koers", $pdfObject->rapport_taal),
                        '',
                        "\n" . vertaalTekst("Marktwaarde", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Resultaat", $pdfObject->rapport_taal),
                        "\n" . vertaalTekst("Rendement", $pdfObject->rapport_taal),
                        vertaalTekst("Bijdrage", $pdfObject->rapport_taal) . "\n" . vertaalTekst("rendement", $pdfObject->rapport_taal),
                        vertaalTekst("%", $pdfObject->rapport_taal) . "   \n" . vertaalTekst("portf.", $pdfObject->rapport_taal)));
    }
     unset($pdfObject->CellBorders);
	}

	function HeaderVHO_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    //$pdfObject->SetWidths(array(63,15,18,20,23, 18,  29,28,28,20,20));
    $pdfObject->SetWidths(array(63,15,26,20,23, 18,  21,28,28,20,20));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 1)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
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
   $pdfObject->setXY($positie['fondsStart'],$y);

   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);

   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');


   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

		$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal),
		'',
		"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Gemiddelde",$pdfObject->rapport_taal)."\n".vertaalTekst("aankoopwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal)."",
    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal).""));

/*
    $pdfObject->SetAligns(array('L','C','C','C','C', 'C'  ,'C','C','C','C','C'));
    		$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal),
		'',
		"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Gemiddelde",$pdfObject->rapport_taal)."\n".vertaalTekst("aankoopwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
    vertaalTekst("%",$pdfObject->rapport_taal)."\n".vertaalTekst("portf.",$pdfObject->rapport_taal),
    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)));
*/

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		unset($pdfObject->CellBorders);

	}
function HeaderOIV_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  //$pdfObject->SetWidths(array(63,15,18,20,23, 18,  29,28,28,20,20));
  $pdfObject->SetWidths(array(62,15,24,20,20, 18,  20,23,20,20,20,20));

  foreach ($pdfObject->widths as $id=>$waarde)
  {
    if($id < 1)
      $positie['fondsStart'] +=$waarde;
    if($id < 5)
      $positie['fondsEind'] +=$waarde;
    if($id < 6)
    {
      $positie['waardeStart'] +=$waarde;
      if($id==5)
      {
        $positie['midden'] = $positie['waardeStart'] ;
        $positie['midden'] -=$waarde/2;
      }
    }
    if($id < 12)
      $positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
  }
  foreach ($positie as $key=>$value)
    $positie[$key]+=$pdfObject->marge;

  $y=$pdfObject->GetY()+5;
  $pdfObject->pageTop=array($positie['midden'],$y+1);
  $pdfObject->setXY($positie['fondsStart'],$y);

  $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
  $pdfObject->setXY($positie['waardeStart'],$y);

  if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
  else
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');


  $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
  $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

  $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
  $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);

  $pdfObject->row(array(
                    "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal),
                    '',
                    "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
                    vertaalTekst("Gemiddelde",$pdfObject->rapport_taal)."\n".vertaalTekst("aankoopwaarde",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal)."",
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal)."",
                    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
                    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal).""));

  /*
      $pdfObject->SetAligns(array('L','C','C','C','C', 'C'  ,'C','C','C','C','C'));
          $pdfObject->row(array(
       "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
       "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
       "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
       "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
       vertaalTekst("Gemiddelde kostprijs",$pdfObject->rapport_taal),
      '',
      "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
      vertaalTekst("Gemiddelde",$pdfObject->rapport_taal)."\n".vertaalTekst("aankoopwaarde",$pdfObject->rapport_taal),
      vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
      vertaalTekst("%",$pdfObject->rapport_taal)."\n".vertaalTekst("portf.",$pdfObject->rapport_taal),
      vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)));
  */

  $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
  unset($pdfObject->CellBorders);

}

function HeaderDOORKIJK_L33($object)
{
  $pdfObject = &$object;
}
	function HeaderVOLK_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(63,15,26,20,23, 18,  21,28,28,20,20));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 1)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
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
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);
   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nNaam","\nValuta","\nAantal","\nKoers","Begin\nKoers",'',"\nMarktwaarde","\nBeginwaarde","Ongerealiseerd\nresultaat","%\nportf.","Opgelopen\nrente"));

		$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 vertaalTekst("Begin",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
		 '',
		"\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Beginwaarde",$pdfObject->rapport_taal),
		vertaalTekst("Ongerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)));

     unset($pdfObject->CellBorders);
	}


function HeaderVOLKD_L33($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
  $pdfObject->SetWidths(array(63,15,26,20,20, 18,  20,20,20,20,20,20));

  foreach ($pdfObject->widths as $id=>$waarde)
  {
    if($id < 1)
      $positie['fondsStart'] +=$waarde;
    if($id < 5)
      $positie['fondsEind'] +=$waarde;
    if($id < 6)
    {
      $positie['waardeStart'] +=$waarde;
      if($id==5)
      {
        $positie['midden'] = $positie['waardeStart'] ;
        $positie['midden'] -=$waarde/2;
      }
    }
    if($id < 12)
      $positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
  }
  foreach ($positie as $key=>$value)
    $positie[$key]+=$pdfObject->marge;

  $y=$pdfObject->GetY()+5;
  $pdfObject->pageTop=array($positie['midden'],$y+1);
  $pdfObject->setXY($positie['fondsStart'],$y);
  $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
  $pdfObject->setXY($positie['waardeStart'],$y);
  if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
  else
    $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

  $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
  $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

  $pdfObject->SetAligns(array('L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
  $pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  //$pdfObject->row(array("\nNaam","\nValuta","\nAantal","\nKoers","Begin\nKoers",'',"\nMarktwaarde","\nBeginwaarde","Ongerealiseerd\nresultaat","%\nportf.","Opgelopen\nrente"));

  $pdfObject->row(array(
                    "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
                    vertaalTekst("Begin",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
                    '',
                    "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),
                    "\n".vertaalTekst("Beginwaarde",$pdfObject->rapport_taal),
                    vertaalTekst("Fonds",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
                    vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
                    vertaalTekst("%",$pdfObject->rapport_taal)."   \n".vertaalTekst("portf.",$pdfObject->rapport_taal),
                    vertaalTekst("Opgelopen",$pdfObject->rapport_taal)."\n".vertaalTekst("rente",$pdfObject->rapport_taal)));

  unset($pdfObject->CellBorders);
}

function headerPERFG_L33($object)
{
  $pdfObject = &$object;
  HeaderVOLKV_L33($pdfObject);
}

	function HeaderVOLKV_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(60,16,16,16,21,21,25, 5,  20,20,20,20,20));

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
 //  $pdfObject->setXY($positie['fondsStart'],$y);
  // $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,"FONDS VALUTA",0,'C');
 //  $pdfObject->setXY($positie['waardeStart'],$y);
//   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
//     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
//   else
//     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');

//   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
//   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('L','L','L','R','R','R','R', 'C'  ,'R','R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln();
		//$pdfObject->row(array("\nNaam","Rating instr.","Rating debiteur","\nValuta","\nNominaal","\nKoers","\nMarktwaarde",'',"Coupon\nYield","Yield to\nMaturity","Macaulay\nduration","Resterende\nlooptijd","%\nport."));


				$pdfObject->row(array(
		 "\n".vertaalTekst("Naam",$pdfObject->rapport_taal),
		 vertaalTekst("Rating instr.",$pdfObject->rapport_taal),
		 vertaalTekst("Rating debiteur",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Nominaal",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		 "\n".vertaalTekst("Marktwaarde",$pdfObject->rapport_taal),'',
		 vertaalTekst("Coupon",$pdfObject->rapport_taal)."\n".vertaalTekst("Yield",$pdfObject->rapport_taal),
		 vertaalTekst("Yield to",$pdfObject->rapport_taal)."\n".vertaalTekst("Maturity",$pdfObject->rapport_taal),
		 vertaalTekst("Modified",$pdfObject->rapport_taal)."\n".vertaalTekst("duration",$pdfObject->rapport_taal),
		  vertaalTekst("Resterende",$pdfObject->rapport_taal)."\n".vertaalTekst("looptijd",$pdfObject->rapport_taal),
		   vertaalTekst("%",$pdfObject->rapport_taal)."  \n".vertaalTekst("port.",$pdfObject->rapport_taal)));


		unset($pdfObject->CellBorders);//"Modified\nduration",
	}

	function HeaderMUT_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(25,80,25,25,35, 20  ,30,30));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 2)
       $positie['fondsStart'] +=$waarde;
     if($id < 5)
       $positie['fondsEind'] +=$waarde;
     if($id < 6)
     {
       $positie['waardeStart'] +=$waarde;
       if($id==5)
       {
         $positie['midden'] = $positie['waardeStart'] ;
         $positie['midden'] -=$waarde/2;
       }
     }
     if($id < 10)
       $positie['waardeEind'] +=$waarde;
//      echo "$id => $waarde \n<br>";
    }
    foreach ($positie as $key=>$value)
      $positie[$key]+=$pdfObject->marge;

   $y=$pdfObject->GetY()+5;
    $pdfObject->pageTop=array($positie['midden'],$y+1);
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);

   if($pdfObject->rapportageValuta == '' || $pdfObject->rapportageValuta == 'EUR')
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"EURO",0,'C');
   else
     $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,$pdfObject->rapportageValuta,0,'C');


   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('R','L','R','R','R', 'R'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nDatum","\nOmschrijving","\nValuta","Valuta-\nkoers ","\nBedrag",'',"\nBedrag",''));

		$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		vertaalTekst("Valuta-",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),'',
		"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),''));

     unset($pdfObject->CellBorders);

	}

function HeaderTRANS_L33($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'B',$pdfObject->rapport_fontsize);
    $pdfObject->SetWidths(array(25,55,15,20,20,20,25,  15  ,30,30,25));

    foreach ($pdfObject->widths as $id=>$waarde)
    {
      if($id < 2)
       $positie['fondsStart'] +=$waarde;
     if($id < 7)
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

    if ($pdfObject->rapportageValuta == "EUR" )
	    $valuta = 'EURO';
	  else
	    $valuta = $pdfObject->rapportageValuta ;

   $y=$pdfObject->GetY()+5;
   $pdfObject->pageTop=array($positie['midden'],$y+1);
   $pdfObject->setXY($positie['fondsStart'],$y);
   $pdfObject->MultiCell($positie['fondsEind']-$positie['fondsStart'],5,vertaalTekst("FONDS VALUTA",$pdfObject->rapport_taal),0,'C');
   $pdfObject->setXY($positie['waardeStart'],$y);
   $pdfObject->MultiCell($positie['waardeEind']-$positie['waardeStart'],5,"$valuta",0,'C');

   $pdfObject->Line($positie['fondsStart'],$pdfObject->GetY(),$positie['fondsEind'],$pdfObject->GetY());
   $pdfObject->Line($positie['waardeStart'],$pdfObject->GetY(),$positie['waardeEind'],$pdfObject->GetY());

    $pdfObject->SetAligns(array('R','L','R','R','R', 'R'  ,'R','R','R','R','R'));
		$pdfObject->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U');
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		//$pdfObject->row(array("\nDatum","\nOmschrijving","\nValuta","\nAantal","\nKoers","Valuta-\nkoers ","\nBedrag",'',"\nBedrag","Gerealiseerd\nresultaat   ","\nProvisie"));
		$pdfObject->row(array("\n".vertaalTekst("Datum",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Valuta",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Aantal",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Koers",$pdfObject->rapport_taal),
		vertaalTekst("Valuta",$pdfObject->rapport_taal)."\n".vertaalTekst("koers",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),
		'',"\n".vertaalTekst("Bedrag",$pdfObject->rapport_taal),
		vertaalTekst("Gerealiseerd",$pdfObject->rapport_taal)."\n".vertaalTekst("resultaat",$pdfObject->rapport_taal),
		"\n".vertaalTekst("Kosten",$pdfObject->rapport_taal)));

    unset($pdfObject->CellBorders);
	}

	function HeaderMODEL_L33($object)
	{
    $pdfObject = &$object;

		$pdfObject->SetFont($pdfObject->rapport_font,"b",10);
		$pdfObject->Cell(70,4, "Modelportefeuille: ",0,0,"R");
		$pdfObject->SetFont($pdfObject->rapport_font,"",10);
		$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_portefeuille'],0,1,"L");
		$pdfObject->SetFont($pdfObject->rapport_font,"b",10);

		if($pdfObject->selectData[modelcontrole_rapport] == "vastbedrag")
		{
			$pdfObject->Cell(70,4, "Vast bedrag: ",0,0,"R");
			$pdfObject->SetFont($pdfObject->rapport_font,"",10);
			$pdfObject->Cell(50,4, $pdfObject->selectData['modelcontrole_vastbedrag'],0,1,"L");
		}
		else
		{
			if($pdfObject->selectData["modelcontrole_filter"] != "gekoppeld")
				$extraTekst = " : niet gekoppeld depot";
			else
				$extraTekst = "";

			$pdfObject->Cell(70,4, "Client: ",0,0,"R");
			$pdfObject->SetFont($pdfObject->rapport_font,"",10);
			$pdfObject->Cell(50,4, $pdfObject->clientOmschrijving,0,1,"L");
		}
    
    if($pdfObject->overigeBeperkingen<>'')
    {
      $y=$pdfObject->getY();
      $pdfObject->SetXY(190,$y-12);
      $pdfObject->SetFont($pdfObject->rapport_font,"b",10);
      $pdfObject->Cell(70, 4, "Overige beperkingen: ", 0, 1, "L");
      $pdfObject->SetFont($pdfObject->rapport_font, "", 10);
      $pdfObject->SetXY(190,$y-8);
      $pdfObject->MultiCell(90,4, $pdfObject->overigeBeperkingen,0,'L');
      $pdfObject->setY($y);
    }

		$pdfObject->ln();
		//$pdfObject->SetWidths(array(60,25,25,25,25,25,25,10,27,25));
		//$pdfObject->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
		//$pdfObject->Row(array("Fonds","Model Percentage","Werkelijk Percentage","Grootste afwijking","Kopen","Verkopen","Overschrijding waarde EUR","","Waarde volgens percentage model","Koers in locale valuta"));
		$pdfObject->SetWidths(array(28,60,25,15,21,25,25,25,25,25,25));
		$pdfObject->SetAligns(array("L","L","R","R","R","R","R","R","R","R","R","R","R"));
		$pdfObject->Row(array("ISIN Code","Fonds","Werkelijke waarde", "in %","Model Percentage","Afwijking","Aantal kopen","Waarde kopen","Aantal verkopen","Waarde verkopen"));

		$pdfObject->Line($pdfObject->marge ,$pdfObject->GetY(), $pdfObject->marge + 280,$pdfObject->GetY());
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
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

  function printSamenstellingResultaat_L33($object,$portefeuille, $rapportageDatum, $rapportageDatumVanaf)
  {
  	global $__appvar;
    $pdfObject = &$object;
 		$DB= new DB();
 		$query="SELECT Omschrijving FROM Fondsen WHERE Fonds='".$pdfObject->portefeuilledata['SpecifiekeIndex']."'";
 		$DB->SQL($query);
 		$indexOmschrijving=$DB->lookupRecord();
 		$indexOmschrijving=$indexOmschrijving['Omschrijving'];

		$performance = performanceMeting($portefeuille, $rapportageDatumVanaf, $rapportageDatum, $pdfObject->portefeuilledata['PerformanceBerekening'],$pdfObject->rapportageValuta);
		$pdfObject->ln(2);
		if(($pdfObject->GetY() + 22 - $min) >= $pdfObject->pagebreak)
		{
			$pdfObject->AddPage();
			$pdfObject->ln();
		}

		$begin = $pdfObject->GetY();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110,14,'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110,14);
		$pdfObject->ln(2);
		$pdfObject->SetX($pdfObject->marge);

		$koers1=getFondsKoers($pdfObject->portefeuilledata['SpecifiekeIndex'],$rapportageDatumVanaf);
		$koers2=getFondsKoers($pdfObject->portefeuilledata['SpecifiekeIndex'],$rapportageDatum);
		$indexPerformance = ($koers2 - $koers1) / ($koers1/100);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->Cell(80,4, vertaalTekst("Resultaat over verslagperiode",$pdfObject->rapport_taal), 0,0, "L");
		$pdfObject->Cell(30,4, $pdfObject->formatGetal($performance,2)."%", 0,1, "R");
		$pdfObject->ln(2);
		$pdfObject->Cell(80,4, $indexOmschrijving, 0,0, "L");
		$pdfObject->Cell(30,4, $pdfObject->formatGetal($indexPerformance,2)."%", 0,1, "R");
		$pdfObject->ln(2);


  }

  function printAEXVergelijking_L33($object,$vermogensbeheerder, $rapportageDatumVanaf, $rapportageDatum)
	{
	  global $__appvar;
    $pdfObject = &$object;
	  $query = "SELECT Indices.Beursindex,Indices.specialeIndex, Fondsen.Omschrijving, Fondsen.Valuta FROM Indices, Fondsen WHERE Indices.Beursindex = Fondsen.Fonds AND Vermogensbeheerder = '".$pdfObject->portefeuilledata['Vermogensbeheerder']."' ORDER BY Indices.specialeIndex desc,Afdrukvolgorde";
    $border=0;
		$DB  = new DB();
		$DB2 = new DB();

		$DB->SQL($query);
		$DB->Query();
		$regels = $DB->records();
		$hoogte = ($regels * 4) + 8;
		if(($pdfObject->GetY() + $hoogte) > $pdfObject->pagebreak)
		{
			$pdfObject->AddPage();
			$pdfObject->ln();
		}

		$perfEur = 0;
		$perfVal = 1;
		$perfJan = 0;

		if($pdfObject->rapport_perfIndexJanuari == true)
	  {
	    $julRapDatumVanaf = db2jul($rapportageDatumVanaf);
	    $rapJaar = date('Y',$julRapDatumVanaf);
	    $dagMaand = date('d-m',$julRapDatumVanaf);
	    $januariDatum = $rapJaar.'-01-01';
	    	    if($dagMaand =='01-01')
        $pdfObject->rapport_perfIndexJanuari = false;
	  }
		if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		{
		  $extraX = 26;
		  $perfEur = 1;
		  $perfVal = 0;
		  $perfJan = 0;
		}
		if($pdfObject->rapport_perfIndexJanuari == true)
	  {
		  $perfEur = 0;
		  $perfVal = 0;
		  $perfJan = 1;
	  }

	  if($pdfObject->printAEXVergelijkingProcentTeken)
	    $teken = '%';
	  else
	    $teken = '';


		if($pdfObject->rapport_perfIndexJanuari == true)
		  $extraX += 51;

		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor[r],$pdfObject->rapport_kop_bgcolor[g],$pdfObject->rapport_kop_bgcolor[b]);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110+9+$extraX,$hoogte,'F');
		$pdfObject->SetFillColor(0);
		$pdfObject->Rect($pdfObject->marge,$pdfObject->getY(),110+9+$extraX,$hoogte);
		$pdfObject->SetX($pdfObject->marge);

		// kopfontcolor
		//$pdfObject->SetTextColor($pdfObject->rapport_kop4_fontcolor[r],$pdfObject->rapport_kop4_fontcolor[g],$pdfObject->rapport_kop4_fontcolor[b]);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		$pdfObject->SetFont($pdfObject->rapport_kop4_font,$pdfObject->rapport_kop4_fontstyle,$pdfObject->rapport_kop4_fontsize);
		$pdfObject->Cell(40,4, vertaalTekst("Index-vergelijking",$pdfObject->rapport_taal), 0,0, "L");

		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_fontstyle,$pdfObject->rapport_fontsize);
		//$pdfObject->SetTextColor($pdfObject->rapport_fonds_fontcolor[r],$pdfObject->rapport_fonds_fontcolor[g],$pdfObject->rapport_fonds_fontcolor[b]);
		$pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor[r],$pdfObject->rapport_kop_fontcolor[g],$pdfObject->rapport_kop_fontcolor[b]);
		if($pdfObject->rapport_perfIndexJanuari == true)
			$pdfObject->Cell(26,4, date("d-m-Y",db2jul($januariDatum)), $border,0, "R");
		$pdfObject->Cell(26,4, date("d-m-Y",db2jul($rapportageDatumVanaf)), $border,0, "R");
		$pdfObject->Cell(26,4, date("d-m-Y",db2jul($rapportageDatum)), $border,0, "R");

		if($pdfObject->portefeuilledata['Layout']==30 || $pdfObject->portefeuilledata['Layout']==14)
		  $pdfObject->Cell(26,4, vertaalTekst("Perf in %",$pdfObject->rapport_taal), $border,$perfVal, "R");
		else
	  	$pdfObject->Cell(26,4, vertaalTekst("Performance in %",$pdfObject->rapport_taal), $border,$perfVal, "R");
		if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		  $pdfObject->Cell(26,4, vertaalTekst("Perf in % in EUR",$pdfObject->rapport_taal), $border,$perfEur, "R");
		if($pdfObject->rapport_perfIndexJanuari == true)
			$pdfObject->Cell(26,4, vertaalTekst("Jaar Perf.",$pdfObject->rapport_taal), $border,$perfJan, "R");

		while($perf = $DB->nextRecord())
		{

		  if(isset($lastCat) && $lastCat != $perf['specialeIndex'])
		    $pdfObject->ln();

		  if($perf['Valuta'] != 'EUR')
		  {
		    if($pdfObject->rapport_perfIndexJanuari == true)
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

		  if($pdfObject->rapport_perfIndexJanuari == true)
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
			$pdfObject->Cell(40,4, $perf[Omschrijving], $border,0, "L");
		  if($pdfObject->rapport_perfIndexJanuari == true)
		     $pdfObject->Cell(26,4, $pdfObject->formatGetal($koers0[Koers],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers1[Koers],2), $border,0, "R");
			$pdfObject->Cell(26,4, $pdfObject->formatGetal($koers2[Koers],2), $border,0, "R");
		  $pdfObject->Cell(26,4, $pdfObject->formatGetal($performance,2).$teken, $border,$perfVal, "R");
		  if($pdfObject->rapport_printAEXVergelijkingEur == 1)
		    $pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceEur,2).$teken, $border,$perfEur, "R");
		  if($pdfObject->rapport_perfIndexJanuari == true)
		    $pdfObject->Cell(26,4, $pdfObject->formatGetal($performanceJaar,2).$teken, $border,$perfJan, "R");

		   $lastCat=$perf['specialeIndex'];
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
      $pdfObject->Cell(0, $hLegend, $pdfObject->legends[$i]);
      $y1 += $hLegend + $margin;
    }

  }
}




?>
