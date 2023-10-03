<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/12/30 08:17:59 $
 		File Versie					: $Revision: 1.14 $

 		$Log: PDFRapport_headers_L34.php,v $
 		Revision 1.14  2016/12/30 08:17:59  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/11/07 16:45:15  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2014/07/23 15:44:04  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2014/02/22 18:43:38  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/02/05 16:02:14  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/02/20 15:12:14  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/09/05 18:19:11  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/04/25 15:20:45  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2011/11/16 19:22:09  rvv
 		*** empty log message ***

 		Revision 1.5  2011/11/09 18:56:32  rvv
 		*** empty log message ***

 		Revision 1.4  2011/11/05 16:05:17  rvv
 		*** empty log message ***

 		Revision 1.3  2011/10/09 16:54:45  rvv
 		*** empty log message ***

 		Revision 1.2  2011/09/25 16:23:28  rvv
 		*** empty log message ***

 		Revision 1.1  2011/04/19 16:41:39  rvv
 		*** empty log message ***

 		Revision 1.11  2011/04/09 14:35:27  rvv
 		*** empty log message ***


*/
function Header_basis_L34($object)
{
   $pdfObject = &$object;
   $pdfObject->last_rapport_type=$pdfObject->rapport_type;
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
      //if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
  		  $pdfObject->customPageNo = 0;
  		$pdfObject->rapportNewPage = $pdfObject->page;
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

		if($pdfObject->__appvar['consolidatie']['rekeningOnderdrukken'])
		{
  		$pdfObject->rapport_naam1=$pdfObject->__appvar['consolidatie']['portefeuillenaam1'];
  		$pdfObject->rapport_naam2=$pdfObject->__appvar['consolidatie']['portefeuillenaam2'];
		}

		$pdfObject->rapport_liquiditeiten_omschr = str_replace("{PortefeuilleVoorzet}",  $pdfObject->rapport_portefeuilleVoorzet, $pdfObject->rapport_liquiditeiten_omschr);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
			$logopos = 45;
		else
			$logopos = 120;

		//rapport_risicoklasse
		if(is_file($pdfObject->rapport_logo))
		{
		  $factor=0.04;
		  $xSize=1390*$factor;
		  $ySize=533*$factor;
	    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, $xSize, $ySize);
		}

		if(isset($pdfObject->__appvar['consolidatie']))
		{
		  $db=new DB();
		  $pdfObject->rapport_naam1='';
      foreach ($pdfObject->portefeuilles as $index=>$portefeuille)
      {
        if(!isset($pdfObject->ALPConsolidatie[$portefeuille]))
        {
          $query="SELECT Client,Accountmanager,tweedeAanspreekpunt FROM Portefeuilles WHERE Portefeuille='$portefeuille'";
          $db->SQL($query);
          $pdata=$db->lookupRecord();
          $query="SELECT Accountmanager,Naam FROM Accountmanagers WHERE Accountmanager IN('".$pdata['Accountmanager']."','".$pdata['tweedeAanspreekpunt']."')";
          $db->SQL($query);
          $db->Query();
          while ($data=$db->nextRecord())
          {
            if($data['Accountmanager'] == $pdata['Accountmanager'])
            	$pdata['Accountmanager']=$data['Naam'];
            elseif($data['Accountmanager'] == $pdata['tweedeAanspreekpunt'])
            	$pdata['tweedeAanspreekpunt']=$data['Naam'];
          }
          $pdfObject->ALPConsolidatie[$portefeuille]=$pdata;
        }

        if($index==0)
        {
          $pdfObject->portefeuilledata['AccountmanagerNaam']= $pdfObject->ALPConsolidatie[$portefeuille]['Accountmanager'];
          $pdfObject->portefeuilledata['AccountmanagerNaam2']= $pdfObject->ALPConsolidatie[$portefeuille]['tweedeAanspreekpunt'];
        }

        $pdfObject->rapport_naam1 .= $pdfObject->ALPConsolidatie[$portefeuille]['Client'];
        if($index != count($pdfObject->portefeuilles)-1)
          $pdfObject->rapport_naam1.=", ";

      }
		}
    

		if($pdfObject->rapport_type <> $pdfObject->lastRapport || $pdfObject->lastPortefeuille <> $pdfObject->portefeuilledata['Portefeuille'])
		{
		  		$pdfObject->Line($pdfObject->marge,30,290,30);
		$pdfObject->SetY($y);


		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 60;
		else
			$x = 150;

		$pdfObject->SetWidths(array(35,10,200));
		$pdfObject->SetAligns(array('L','C','L'));
		$pdfObject->SetXY($pdfObject->marge,32);
	  $pdfObject->Row(array('Cliënt',':',$pdfObject->rapport_naam1));// .' '. $pdfObject->rapport_naam2
	  $pdfObject->ln(2);
	  $pdfObject->Row(array('Contact',':',"1. ".$pdfObject->portefeuilledata['AccountmanagerNaam']." 2. ".$pdfObject->portefeuilledata['AccountmanagerNaam2']));
		$pdfObject->SetXY(150,32);
	  $pdfObject->MultiCell(140,4,vertaalTekst(vertaalTekst("Rapportageperiode:",$pdfObject->rapport_taal)." ".
	  date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." - ".
	  date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum)),0,'R');
		$pdfObject->SetXY(150,38);
	  $pdfObject->MultiCell(140,4,vertaalTekst(vertaalTekst("Datum rapport:",$pdfObject->rapport_taal)." ".
	  date("j")." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n")],$pdfObject->rapport_taal)." ".date("Y")),0,'R');
	  $pdfObject->Line($pdfObject->marge,$pdfObject->getY()+3,290,$pdfObject->getY()+3);
	  $pdfObject->SetXY(100,48);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',14);
	  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');
		$pdfObject->headerStart = $pdfObject->getY()+16;
		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);
		
    }
    else
    {
      $pdfObject->SetXY(8,25);
    }
		$pdfObject->lastRapport=$pdfObject->rapport_type;
    $pdfObject->lastPortefeuille=$pdfObject->portefeuilledata['Portefeuille'];
    }
   $pdfObject->SetTextColor($pdfObject->rapport_default_fontcolor['r'],$pdfObject->rapport_default_fontcolor['g'],$pdfObject->rapport_default_fontcolor['b']);
}

	function HeaderVKM_L34($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}
  function HeaderATT_L34($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->ln();
	}
  function HeaderPERF_L34($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
	}

	function HeaderVHO_L34($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetWidths(array(63,18,18,25,30,30,30,23,20,20));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','L'));
    $pdfObject->ln();

	}

		function HeaderVOLK_L34($object)
	{
    $pdfObject = &$object;
    $pdfObject->SetWidths(array(63,22,18,25,30,30,30,23,20,20));
    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','L'));
    $pdfObject->ln();

	}
  
  function HeaderAFM_L34($object)
	{
    $pdfObject = &$object;
 //   $pdfObject->HeaderOIB();
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

	  $pdfObject->Line(($pdfObject->marge+$lijn1+5),$pdfObject->GetY(),$pdfObject->marge + $lijn1eind,$pdfObject->GetY());

	  $pdfObject->SetWidths($pdfObject->widthA);
	  $pdfObject->SetAligns($pdfObject->alignA);
		$pdfObject->row(array(vertaalTekst("AFM categorie",$pdfObject->rapport_taal),
											vertaalTekst("Valutasoort",$pdfObject->rapport_taal),
											vertaalTekst("in valuta",$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in ".$pdfObject->rapportageValuta,$pdfObject->rapport_taal),
											vertaalTekst("in %",$pdfObject->rapport_taal)));
 		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->Line($pdfObject->marge,$pdfObject->GetY(),$pdfObject->marge + array_sum($pdfObject->widthB),$pdfObject->GetY());                     
       
      
	}

  function HeaderOIB_L34($object)
	{
    $pdfObject = &$object;
    if($pdfObject->rapport_titel == "Onderverdeling in AFM categorien")
    {
      $pdfObject->HeaderOIB();
    }
    else
    {
    $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
    $pdfObject->widthA = array(30,30,30,25,30,25,25,25,25,30);//,23,23
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
    $pdfObject->ln();
		$pdfObject->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopenrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n "));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());
    unset($pdfObject->fillCell);
    }

	}




?>
