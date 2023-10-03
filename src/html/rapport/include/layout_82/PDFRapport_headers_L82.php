<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/30 05:33:19 $
 		File Versie					: $Revision: 1.5 $

 		$Log: PDFRapport_headers_L82.php,v $
 		Revision 1.5  2020/04/30 05:33:19  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2020/04/29 09:44:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2020/03/18 17:45:34  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2020/03/14 19:11:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/01/12 17:10:16  rvv
 		*** empty log message ***
 		


*/
function Header_basis_L82($object)
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

		if($pdfObject->rapportCounter <> $pdfObject->rapportCounterLast)
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
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$y = $pdfObject->GetY();

		// default header stuff
		$pdfObject->SetX($pdfObject->marge);

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

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY")
		{
			$logopos = 85;
		}
		else
		{
			$logopos = 130;
		}

		//rapport_risicoklasse


		if(is_file($pdfObject->rapport_logo))
		{

		    $pdfObject->Image($pdfObject->rapport_logo, $logopos, 5, 54);
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

		if ($pdfObject->rapport_layout != 17 )
		  $pdfObject->MultiCell(90,4,$pdfObject->rapport_koptext,0,'L');
		$pdfObject->SetY($y);

		if($pdfObject->rapport_type == "MOD" || $pdfObject->rapport_type == "CASHY" )
			$x = 160;
		else
			$x = 250;

		$pdfObject->SetY($y);
		$pdfObject->SetX($x);


	  $pdfObject->MultiCell(40,4,vertaalTekst("Pagina",$pdfObject->rapport_taal)." ".$pdfObject->customPageNo."\n\n".vertaalTekst("Rapportagedatum:",$pdfObject->rapport_taal)."\n".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'R');
	  $pdfObject->SetX(100);
	  $pdfObject->SetFont($pdfObject->rapport_font,'b',$pdfObject->rapport_fontsize);
	  $pdfObject->MultiCell(100,4,vertaalTekst($pdfObject->rapport_titel,$pdfObject->rapport_taal),0,'C');



		$pdfObject->headerStart = $pdfObject->getY()+4;

		$pdfObject->SetTextColor($pdfObject->rapport_fontcolor['r'],$pdfObject->rapport_fontcolor['g'],$pdfObject->rapport_fontcolor['b']);

		$pdfObject->rapportCounterLast = $pdfObject->rapportCounter;
    }

}

	function HeaderVKM_L82($object)
	{
		$pdfObject = &$object;
		$pdfObject->HeaderVKM();
	}

function HeaderVKMS_L82($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderVKM();
}

function HeaderOIS_L82($object)
{
  $pdfObject = &$object;
  $pdfObject->HeaderOIS();
}

function HeaderMUT_L82($object)
{
  $pdfObject = &$object;
  $pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
  
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
  
  $pdfObject->SetWidths($pdfObject->widthB);
  $pdfObject->SetAligns($pdfObject->alignB);
  $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
  $pdfObject->row(array(vertaalTekst("Periode",$pdfObject->rapport_taal),
                    vertaalTekst("Datum",$pdfObject->rapport_taal),
                    '',
               vertaalTekst("Omschrijving",$pdfObject->rapport_taal),
              
              '' ,
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

  function HeaderVOLK_L82($object)
	{
	    $pdfObject = &$object;
      $dataWidth=array(70,23,15,25,25,10,23,22,23,10,20,16);
      $splits=array(2,4,5,8,9,11);
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
		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

      $pdfObject->ln();
      $pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
      $pdfObject->SetWidths($kopWidth);
	    $pdfObject->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
	    $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
      $pdfObject->SetAligns(array('L','C','L','C','L','C'));
      $pdfObject->CellBorders = array('','U','','U','','U');
      $pdfObject->Row(array('','Beginwaarde rapportage periode','','Actuele koers','','Resultaat'));
      $pdfObject->CellBorders = array();

 	 	  $pdfObject->SetWidths($dataWidth);
	    $pdfObject->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R'));
	    $pdfObject->Row(array("Categorie/Effect\n ","Aantal\n ","Valuta\n ",
	    "Per stuk\nin valuta","Portefeuille\nin EUR",
	    " \n ",
	    "Per stuk\nin valuta","Portefeuille\nin EUR","in % van\nVermogen",

	    " \n ","Absoluut\n ","in %\n "));//,"Historische\nkostprijs"

	    $pdfObject->Line(($pdfObject->marge),$pdfObject->GetY(),$pdfObject->marge + array_sum($dataWidth),$pdfObject->GetY());
	    $pdfObject->fillCell = $oldFill;
	    $pdfObject->rowHeight=$oldrowHeight;
	    //$pdfObject->HeaderVOLK();
  }

function HeaderOIB_L82($object)
{
  $pdfObject = &$object;
  $pdfObject->ln();
  $pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
  $pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), 297-$pdfObject->marge*2, 8 , 'F');
  $pdfObject->SetTextColor($pdfObject->rapport_kop_fontcolor['r'],$pdfObject->rapport_kop_fontcolor['g'],$pdfObject->rapport_kop_fontcolor['b']);
  
}


  function HeaderATT_L82($object)
	{
    $pdfObject = &$object;
    $pdfObject->widthA = array(26,25,24,24,24,20,20,25,24,24,23,23);
		$pdfObject->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$pdfObject->SetWidths($pdfObject->widthA);
		$pdfObject->SetAligns($pdfObject->alignA);

		for($i=0;$i<count($pdfObject->widthA);$i++)
		  $pdfObject->fillCell[] = 1;

		$pdfObject->SetFont($pdfObject->rapport_font,'',$pdfObject->rapport_fontsize);
		$pdfObject->ln(1);
		$pdfObject->Cell(100,4, vertaalTekst("Overzicht resultaat over verslagperiode",$pdfObject->rapport_taal),0,0);
		$pdfObject->Cell(100,4, vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,0);
    $pdfObject->ln(1);

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
		                     	"Eindvermogen\n ",
		                      "Rendement\n(maand)",
		                      "Rendement\n(Cumulatief)"));
    $sumWidth = array_sum($pdfObject->widthA);
	  $pdfObject->Line($pdfObject->marge+$pdfObject->widthB[0],$pdfObject->GetY(),$pdfObject->marge+$sumWidth,$pdfObject->GetY());

	}

	function HeaderPERFG_L82($object)
	{
    $pdfObject = &$object;
	}


  function HeaderTRANS_L82($object)
	{
    $pdfObject = &$object;
		$pdfObject->SetFont($pdfObject->rapport_font,$pdfObject->rapport_kop_fontstyle,$pdfObject->rapport_fontsize);
    $pdfObject->SetX(100);
		$pdfObject->MultiCell(100,4,vertaalTekst("Verslagperiode",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datumvanaf)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datumvanaf)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datumvanaf)." ".vertaalTekst("tot en met",$pdfObject->rapport_taal)." ".date("j",$pdfObject->rapport_datum)." ".vertaalTekst($pdfObject->__appvar["Maanden"][date("n",$pdfObject->rapport_datum)],$pdfObject->rapport_taal)." ".date("Y",$pdfObject->rapport_datum),0,'C');
		$pdfObject->ln();
		$pdfObject->SetFillColor($pdfObject->rapport_kop_bgcolor['r'],$pdfObject->rapport_kop_bgcolor['g'],$pdfObject->rapport_kop_bgcolor['b']);
		$pdfObject->Rect($pdfObject->marge, $pdfObject->getY(), array_sum($pdfObject->widthB), 16 , 'F');
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
										 vertaalTekst("Resultaat",$pdfObject->rapport_taal),
										 $procentTotaal));
      $pdfObject->ln(1);
  }



class RapportHulpVKM_L82
{
	function RapportHulpVKM_L82($portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->rapport_datum = db2jul($rapportageDatum);
		$this->rapport_jaar = date('Y', $this->rapport_datum);
		$this->ValutaKoersEind=1;
		$this->vanafDatum=($this->rapport_jaar-1).date('-m-d',$this->rapport_datum);
		$this->vanafJul=db2jul($this->vanafDatum);
		$this->pdf->rapport_datumvanaf=$this->vanafJul;
		$portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
		$this->melding="";
		$this->perioden=array();
		$this->queryVanaf=$this->vanafDatum;
		if($portefeuilleStartJul>$this->vanafJul)
		{
			$oldstart=$this->vanafDatum;
			$this->queryVanaf=date('Y-m-d',$portefeuilleStartJul);
			$this->pdf->rapport_datumvanaf =$portefeuilleStartJul;//+86400
			$this->vanafDatum=date('Y-m-d',$portefeuilleStartJul);//+86400
			$dagen=($this->pdf->rapport_datum-$portefeuilleStartJul)/86400;//+86400
			$this->vanafJul=$portefeuilleStartJul;//+86400;
			$this->melding="Door onvoldoende historie bedraagt de rapportage periode ".round($dagen)." dagen.";
		}
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $this->vanafDatum;
		$this->rapportageDatum = $rapportageDatum;
		$this->excelData[]=array('Categorie','Fonds',date('d-m-Y',$this->pdf->rapport_datumvanaf),
			date('d-m-Y',$this->pdf->rapport_datum),'Mutaties','Resultaat','Gemiddeld vermogen',
			'transactie kosten','dl kosten %','dl kosten absoluut','Weging','VKM bijdrage');
		$this->verdelingTotaal=array();
		$this->verdelingFondsen=array();
		$this->skipSummary=false;
		$this->skipDetail=true;
		$this->writeRapport();
	}
	
	
	
	function vulVorigJaar()
	{
		if(substr($this->vanafDatum,5,5)=='01-01')
			$startjaar=true;
		else
			$startjaar=false;
		$fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille, $this->vanafDatum,$startjaar);
		vulTijdelijkeTabel($fondswaarden ,$this->portefeuille, $this->vanafDatum);
		$this->extraVulling = true;
		
	}
	
	function kostenKader($totaalDoorlopendekosten,$perfTotaal)
	{
		
		$query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening AND Grootboekrekeningen.Kosten=1
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<'".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		$DB=new DB();
		$DB->SQL($query);
		$DB->Query();
		$gemiddelde=$this->verdelingTotaal['totaal']['gemiddelde'];
		$doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['gemWaarde'];
//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['gemWaarde']."<br>\n";exit;

			$this->excelData[]=array();
			$this->excelData[]=array('','Doorlopende kosten', '','',round($totaalDoorlopendekosten, 0), 'EUR');
			$this->excelData[]=array('','Doorlopende kosten ten opzichte van onderliggend vermogen','','', round($doorlopendeKostenPercentage * 100, 2) ,'%');
			$this->excelData[]=array();
			$this->perfData['Doorlopende kosten']=array('eur'=>$totaalDoorlopendekosten,'percentage'=>$doorlopendeKostenPercentage * 100);

		
		$percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
		$herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
		$aandeelIndirect=$perfTotaal['gemWaarde']/$gemiddelde;
		$vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
			$this->excelData[]=array('',"Percentage van het gemiddeld indirect vermogen met een kostenfactor",'','', round($percentage * 100, 2),'%');
			$this->excelData[]=array('',"Herrekende doorlopende kosten", '','',round($herrekendeKosten * 100, 2),'%');
			$this->excelData[]=array('','Aandeel indirecte beleggingen','','',round($aandeelIndirect * 100, 2),'%');
			$this->excelData[]=array();
			$this->excelData[]=array('','Gemiddeld vermogen','','',round($gemiddelde,0),'EUR');
			$barData=array();
			$barData['Doorlopende kosten']=$vkmPercentagePortefeuille;
			$this->excelData[]=array('','Doorlopende kosten factor van de portefeuille','','',round($vkmPercentagePortefeuille, 2),'%');
			$this->excelData[]=array();
			$this->excelData[]=array('','Directe kosten vanaf ' . date('d-m-Y', db2jul($this->vanafDatum)),'', 'EUR', 'Percentage');
		$totaal=0;


		while($data = $DB->nextRecord())
		{
				$kostenProcent=$data['totaal']/$gemiddelde*100;
				$this->excelData[]=array('',$data['Omschrijving'],'',round($data['totaal'],0),round($kostenProcent,2) );
				$barData[$data['Omschrijving']]=$kostenProcent;

			$totaal+=$data['totaal'];
		}
		$kostenPercentage=$totaal/$gemiddelde*100;
		$vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;
			$this->excelData[]=array('','Totaal directe kosten','', round($totaal, 0), round($kostenPercentage, 2));
		$this->perfData['Directe kosten']=array('eur'=>$totaal,'percentage'=>$kostenPercentage);
			$this->excelData[]=array();
			$this->excelData[]=array('','Vergelijkende kostenmaatstaf','', round($vkmWaarde*$gemiddelde/100,2),round($vkmWaarde, 2));
		$this->perfData['Vergelijkende kostenmaatstaf']=array('eur'=>$vkmWaarde*$gemiddelde/100,'percentage'=>$vkmWaarde);
		
		$this->vkmWaarde=array('vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde);
		
	}
	
	function getGewogenStortingenOnttrekkingen($van,$tot)
	{
		$DB=new DB();
		$query = "SELECT " .
			"SUM(((TO_DAYS('".$tot."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$tot."') - TO_DAYS('".$van."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, " .
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal " .
			"FROM  (Rekeningen, Portefeuilles)
	       Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening " .
			"WHERE " .
			"Rekeningen.Portefeuille = '" . $this->portefeuille . "' AND " .
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
			"Rekeningmutaties.Verwerkt = '1' AND " .
			"Rekeningmutaties.Boekdatum > '".$van."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);
		$DB->Query();
		$weging = $DB->NextRecord();
		return $weging;
	}
	
	function getGewogenStortingenOnttrekkingenFondsen($datumBegin,$datumEind,$rekeningFondsenWhere,$koersQuery)
	{
		$DB=new DB();
		$queryAttributieStortingenOntrekkingen = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
			"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
			"FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
			"Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Grootboekrekening='FONDS' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			" $rekeningFondsenWhere ";
		$DB->SQL($queryAttributieStortingenOntrekkingen);//echo $queryAttributieStortingenOntrekkingen;
		$DB->Query();
		$weging = $DB->NextRecord();
		return $weging;
	}
	
	function writeRapport()
	{
		global $__appvar,$USR;
		
		$this->vulVorigJaar();
		
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
		
		
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q = "SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
		$gewensteKleuren = $allekleuren['Grootboek'];
		$mogelijkeKleuren=array();
		

		if($this->skipDetail==true)
			$this->pdfVullen=false;
		
		
		
		
		$indexberekening=new indexHerberekening();
		$julvanaf=db2jul($this->rapportageDatumVanaf);
		$jultot=db2jul($this->rapportageDatum);
		$dagenTotaal=round(($jultot-$julvanaf)/86400);
		$this->perioden=$indexberekening->getMaanden($julvanaf,$jultot);
		foreach($this->perioden as $periode)
		{
			$portefeuileWaarde=array();
			$dagenPeriode=round((db2jul($periode['stop'])-db2jul($periode['start']))/86400);
			
			if(substr($this->vanafDatum,5,5)=='01-01')
				$startjaar=true;
			else
				$startjaar=false;
			$fondswaardenStart=berekenPortefeuilleWaarde($this->portefeuille, $periode['start'],$startjaar,'EUR',$periode['start']);

			$storingen=$this->getGewogenStortingenOnttrekkingen($periode['start'], $periode['stop']);
			foreach($fondswaardenStart as $waarden)
			{
				$portefeuileWaarde['start']+=$waarden['actuelePortefeuilleWaardeEuro'];
				$this->verdelingFondsen[$periode['start']][$waarden['fonds']]['start']+=$waarden['actuelePortefeuilleWaardeEuro'];
			}
			$portefeuileWaarde['gemiddelde']=$portefeuileWaarde['start']+$storingen['gewogen'];
			$portefeuileWaarde['aandeel']=$dagenPeriode/$dagenTotaal;
			$this->verdelingTotaal['perioden'][$periode['stop']]=$portefeuileWaarde;
			$this->verdelingTotaal['totaal']['gemiddelde']+=$portefeuileWaarde['aandeel']*$portefeuileWaarde['gemiddelde'];
		}

		
		$query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,Rekeningmutaties.Fonds) as fondsVolgorde,
Fondsen.OptieBovenliggendFonds,
BeleggingssectorPerFonds.Regio,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta,
Fondsen.VKM
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
LEFT JOIN KeuzePerVermogensbeheerder as BeleggingscategorienVolgorde ON BeleggingscategoriePerFonds.Beleggingscategorie = BeleggingscategorienVolgorde.waarde AND BeleggingscategorienVolgorde.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND BeleggingscategorienVolgorde.categorie='Beleggingscategorien'
LEFT JOIN KeuzePerVermogensbeheerder as HoofdcategorienVolgorde ON HoofdBeleggingscategorien.Beleggingscategorie = HoofdcategorienVolgorde.waarde AND HoofdcategorienVolgorde.Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND HoofdcategorienVolgorde.categorie='Beleggingscategorien'
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->queryVanaf."' AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
AND Rekeningmutaties.Fonds <> '' AND Fondsen.VKM=1
GROUP BY Rekeningmutaties.Fonds 
ORDER BY HoofdcategorienVolgorde.Afdrukvolgorde, HoofdBeleggingscategorien.Afdrukvolgorde,BeleggingscategorienVolgorde.Afdrukvolgorde, BeleggingscategorienVolgorde.Afdrukvolgorde, Beleggingscategorien.Afdrukvolgorde,fondsVolgorde,OptieBovenliggendFonds,FondsOmschrijving ";
		
		$heeftOptie=array();
		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
			$perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
			$perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][]=$data['FondsOmschrijving'];//[$data['Regio']]
			$perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][]=$data['Valuta'];//[$data['Regio']]
			$alleData['fondsen'][]=$data['Fonds'];
			$fondsGegevens[$data['Fonds']]=$data;
			
			if($data['OptieBovenliggendFonds'] <> '' && !in_array($data['OptieBovenliggendFonds'],$heeftOptie))
				$heeftOptie[]=$data['OptieBovenliggendFonds'];
		}
		
		$this->totalen['gemiddeldeWaarde']=0;
		$totaalBijdrageVKM=0;
		$totaalDoorlopendekosten=0;
		$perfTotaal=$this->fondsPerformance($alleData,true);
		
		$this->totalen['gemiddeldeWaarde']=$perfTotaal['gemWaarde'];
		
		
		
		foreach ($perHoofdcategorie as $hoofdCategorie=>$hoofdcategorieData)
			$perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($hoofdcategorieData);
		
		
		foreach ($perCategorie as $hoofdCategorie=>$regioData)
			foreach ($regioData as $categorie=>$categorieData)
				$perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->fondsPerformance($categorieData); //[$regio]
		
		foreach ($perHoofdcategorie as $hoofdcategorie=>$hoofdcategorieData)
		{
			$data=$hoofdcategorieData['perf'];
			$totaalSom['beginwaarde'] += $data['beginwaarde'];
			$totaalSom['eindwaarde'] += $data['eindwaarde'];
			$totaalSom['stort'] += $data['stort'];
			$totaalSom['gerealiseerd'] += $data['gerealiseerd'];
			$totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
			$totaalSom['kosten'] += $data['kosten'];
			$totaalSom['resultaat'] += $data['resultaat'];
			$totaalSom['gemWaarde'] += $data['gemWaarde'];
			$totaalSom['weging'] += $data['weging'];
			$totaalSom['bijdrage'] += $data['bijdrage'];
		}
		$perfTotaal = $totaalSom;
		$percentageIndirectVermogenMetKostenfactor=0;

		foreach ($perCategorie as $hoofdcategorie=>$categorieData)
		{
			foreach ($categorieData as $categorie=>$fondsData)
			{
				
				$somVelden=array('beginwaarde','eindwaarde','stort','resultaat','gemWaarde','weging','bijdrage','kosten');
				foreach ($fondsData['fondsen'] as $id=>$fonds)
				{
					
					$lastLn=false;
					$tmp=array();
					$tmp['fondsen']=array($fonds);
					$tmp['categorie']=$categorie;
					$data=$this->fondsPerformance($tmp);
					
					if($fondsGegevens[$fonds]['Fonds']!=$fondsGegevens[$fonds]['fondsVolgorde'] && $fondsGegevens[$fonds]['OptieBovenliggendFonds']==$laatste)
					{
						foreach($somVelden as $veld)
							$sub[$veld]+=$data[$veld];
						$sub['aantal']++;
					}
					
					if($fondsGegevens[$fonds]['OptieBovenliggendFonds'] == '')
						$laatste=$fonds;
					
					if($fondsGegevens[$fonds]['Fonds']==$fondsGegevens[$fonds]['fondsVolgorde'] || (isset($lastfondsVolgorde) && $fondsGegevens[$fonds]['fondsVolgorde'] <> $lastfondsVolgorde))
					{ //echo " $laatsteFonds ".$sub['aantal']."<br>\n";ob_flush();
						if($sub['aantal']>1 )
						{
							$bijdrageVKM=$sub['weging']*100*$kostenPercentage['percentage'];
							$perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
							$perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
						}
						$sub=array('aantal'=>1);
						foreach($somVelden as $veld)
							$sub[$veld]+=$data[$veld];
						
						$laatsteFonds=substr($fondsData['fondsOmschrijving'][$id],0,30);
						
					}
					$lastfondsVolgorde=$fondsGegevens[$fonds]['fondsVolgorde'];
					
					
					if($data['beginwaarde'] < 0 || $data['eindwaarde'] < 0)
						$spiegeling=-1;
					else
						$spiegeling=1;

					$query="SELECT fondskosten.percentage FROM fondskosten 
                       JOIN Fondsen ON fondskosten.fonds=Fondsen.Fonds 
                       WHERE fondskosten.fonds='$fonds' AND Fondsen.VKM=1 AND datum <= '".$this->rapportageDatum."'
                       ORDER BY datum desc";
					$DB->SQL($query);
					$DB->Query();
					$kostenPercentage = $DB->NextRecord();
					$bijdrageVKM=$sub['weging']*$kostenPercentage['percentage'];
					$dlkostenAbsoluut=$sub['gemWaarde']*$kostenPercentage['percentage']/100;
					if($DB->records()>0)
					{//$kostenPercentage['percentage']<>0
						$percentageIndirectVermogenMetKostenfactor += $sub['weging'];
					}

					
					if($this->pdfVullen==true)
					{
						$this->excelData[]=array($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $fondsData['fondsOmschrijving'][$id],
							round($data['beginwaarde'], 0),
							round($data['eindwaarde'], 0),
							round($data['stort'], 0),
							round($data['resultaat'], 0),
							round($data['gemWaarde'], 0),
							round($data['kosten'], 0),
							round($kostenPercentage['percentage'], 2),
							round($dlkostenAbsoluut, 0),
							round($sub['weging'] * 100, 2),
							round($bijdrageVKM, 2));
					}
					$totaalBijdrageVKM+=$bijdrageVKM;
					$totaalDoorlopendekosten+=$sub['gemWaarde']*$kostenPercentage['percentage']/100;
					
					$perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
					$perHoofdcategorie[$hoofdcategorie]['perf']['transkosten'] +=$data['kosten'];
					$perHoofdcategorie[$hoofdcategorie]['perf']['dlkostenAbsoluut'] +=$dlkostenAbsoluut;
					$perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] +=$bijdrageVKM;
					$perCategorie[$hoofdcategorie][$categorie]['perf']['transkosten'] +=$data['kosten'];
					$perCategorie[$hoofdcategorie][$categorie]['perf']['dlkostenAbsoluut'] +=$dlkostenAbsoluut;
					
					$totaalKosten+=$data['kosten'];
					$totaaldlKosten+=$dlkostenAbsoluut;
					// listarray($data);

				}
				$rekeningData=array();
				$totaalRekeningen=0;
				foreach ($fondsData['rekeningen'] as $id=>$rekening)
				{
					$tmp=array();
					$tmp['rekeningen']=array($rekening);
					$data=$this->fondsPerformance($tmp);
					$rekeningData[$id]=array('perf'=>$data,'rekening'=>$rekening);
					$rekeningWaarde[$id]=$data['eindwaarde'];
					$totaalRekeningen+=$data['eindwaarde'];
				}
				arsort($rekeningWaarde);
				
				
				$query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten=1";
				$DB->SQL($query);
				$DB->Query();
				$n=0;
				$grootboekKleuren=array();
				while($data=$DB->nextRecord())
				{
					$mogelijkeKleuren[$n];
					if($gewensteKleuren[$data['Grootboekrekening']]['R']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['G']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['B']['value'])
						$grootboekKleuren[$data['Omschrijving']]=array($gewensteKleuren[$data['Grootboekrekening']]['R']['value'],$gewensteKleuren[$data['Grootboekrekening']]['G']['value'],$gewensteKleuren[$data['Grootboekrekening']]['B']['value']);
					else
						$grootboekKleuren[$data['Omschrijving']]=$mogelijkeKleuren[$n];
					$n++;
				}
				
				$key='Doorlopende kosten';
				if($gewensteKleuren[$key]['R']['value'] || $gewensteKleuren[$key]['G']['value'] || $gewensteKleuren[$key]['B']['value'])
					$grootboekKleuren[$key]=array($gewensteKleuren[$key]['R']['value'],$gewensteKleuren[$key]['G']['value'],$gewensteKleuren[$key]['B']['value']);
				else
					$grootboekKleuren['Doorlopende kosten']=$mogelijkeKleuren[$n];
				
				
				$this->grootboekKleuren=$grootboekKleuren;

			}
			$lastHoofdcategorie=$hoofdcategorie;
		}
		
		$perfTotaal['bijdrageVKM']=$totaalBijdrageVKM;
		$perfTotaal['transkosten']=$totaalKosten;
		$perfTotaal['dlkostenAbsoluut']=$totaaldlKosten;
		$perfTotaal['percentageIndirectVermogenMetKostenfactor']=$percentageIndirectVermogenMetKostenfactor;
		
		$this->excelData[]=array('Totaal', '',
			round($perfTotaal['beginwaarde'], 0),
			round($perfTotaal['eindwaarde'], 0),
			round($perfTotaal['stort'], 0),
			round($perfTotaal['resultaat'], 0),
			round($perfTotaal['gemWaarde'], 0),
			round($perfTotaal['kosten'], 0),
			round($perfTotaal['percentage'], 2),
			round($perfTotaal['dlkostenAbsoluut'], 0),
			round($perfTotaal['weging'] * 100, 2),
			round($perfTotaal['bijdrageVKM'], 2));
		


		if($this->skipSummary==false)
		{
			$this->kostenKader($totaalDoorlopendekosten, $perfTotaal);
			
			if ($this->pdfVullen == true)
			{
				if ($this->melding <> '')
				{
					$this->excelData[] = array();
					$this->excelData[] = array('', $this->melding);
				}

			}
		}
	}
	
	
	
	
	
	function fondsKostenOpbrengsten($fonds,$datumBegin,$datumEind)
	{
		$DB=new DB();
		$query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
		$DB->SQL($query); //echo "$fonds $query  <br>\n";
		$DB->Query();
		$totaalWaarde = $DB->NextRecord();
		
		return $totaalWaarde['totaalWaarde'];
	}
	
	
	function fondsPerformance($fondsData,$totaal=false)
	{
		$datumBegin=$this->vanafDatum;
		$weegDatum=$datumBegin;
		$datumEind=$this->rapportageDatum;
		
		global $__appvar;
		$DB=new DB();
		$totaalPerf = 100;
		
		if(!$fondsData['fondsen'])
			$fondsData['fondsen']=array('geen');
		if(!$fondsData['rekeningen'])
			$fondsData['rekeningen']=array('geen');
		

			$koersQuery = "";
			$startValutaKoers= 1;
			$eindValutaKoers= 1;

		
		
		$fondsenWhere = " Fondsen.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
		$tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
		$rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
		$tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
		$rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
		
		
		
		
		$query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
               SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as liqWaarde,
               SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$start = $DB->NextRecord();
		$beginwaarde = $start['actuelePortefeuilleWaardeEuro'];
		
		$query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2/$eindValutaKoers  as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
		$DB->SQL($query);
		$DB->Query();
		$eind = $DB->NextRecord();
		$ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
		$eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];
		
		
		$queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM((if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
       SUM((if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0))) as opbrengstTotaal ,
       SUM((if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0))) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
		$DB->SQL($queryFondsDirecteKostenOpbrengsten);
		$DB->Query();
		$FondsDirecteKostenOpbrengsten = $DB->NextRecord();
		
		
		$queryAttributieStortingenOntrekkingen = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
			"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
			"FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			" $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
		$DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
		$DB->Query();
		$AttributieStortingenOntrekkingen = $DB->NextRecord();
		
		//   $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
		
		
		$queryKostenOpbrengsten = "SELECT
          SUM((if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
          SUM((if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$this->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
		$DB->SQL($queryKostenOpbrengsten);
		$DB->Query();
		$nietToegerekendeKosten = $DB->NextRecord();
		$AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
		
		
		
		// $indexData=$this->indexPerformance($fondsData['categorie'],$weegDatum,$datumEind);
		$gemiddelde=0;
		foreach($this->perioden as $periode)
		{
			$aandeelPeriode=$this->verdelingTotaal['perioden'][$periode['stop']]['aandeel'];
			
			$stortingen=$this->getGewogenStortingenOnttrekkingenFondsen($periode['start'],$periode['stop'],$rekeningFondsenWhere,$koersQuery);
			$startwaarde=0;
			foreach($fondsData['fondsen'] as $fonds)
			{
				$startwaarde += $this->verdelingFondsen[$periode['start']][$fonds]['start'];
			}
			$gemiddeldeMaand=$startwaarde+$stortingen['gewogen'];
			
			//if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
			//  echo $fondsData['fondsen'][0]." ".$periode['stop']." $aandeelPeriode*($startwaarde+".$stortingen['gewogen'].")=".($aandeelPeriode*$gemiddeldeMaand)."<br>\n";
			
			$gemiddelde+=$aandeelPeriode*$gemiddeldeMaand;
		}
		//if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
		// echo "<br>\n$gemiddelde";
		if($totaal==false)
			$weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'];
		else
			$weging=$gemiddelde/$this->verdelingTotaal['totaal']['gemiddelde'];
		$resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
		$bijdrage=$resultaat/$gemiddelde*$weging;
		
		
		
		return array(
			'beginwaarde'=>$beginwaarde,
			'eindwaarde'=>$eindwaarde,
			
			'stort'=>$AttributieStortingenOntrekkingen['totaal'],
			'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
			'storting'=>$AttributieStortingenOntrekkingen['storting'],
			'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
			'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
			'resultaat'=>$resultaat,
			'gemWaarde'=>$gemiddelde,
			
			'weging'=>$weging,
			'bijdrage'=>$bijdrage);
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
			{
				$datum[$i]['start'] =date('Y-m-d',$counterStart);
				if(substr($datum[$i]['start'],5,5)=='12-31')
					$datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
			}
			
			$datum[$i]['stop']=date('Y-m-d',$counterEnd);
			
			if($datum[$i]['start'] ==  $datum[$i]['stop'])
				unset($datum[$i]);
			$i++;
		}
		return $datum;
	}
	
	function fondsPerf($fonds,$van,$tot)
	{
		$DB=new DB();
		$query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
		$DB->SQL($query);
		$DB->Query();
		$verdeling=array();
		while($data=$DB->nextRecord())
			$verdeling[$data['fonds']]=$data['percentage'];
		
		if(count($verdeling)==0)
			$verdeling[$fonds]=100;
		
		$totalPerf=0;
		foreach($verdeling as $fonds=>$percentage)
		{
			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$janKoers=$DB->lookupRecord();
			
			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$startKoers=$DB->lookupRecord();
			
			$query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
			$DB->SQL($query);
			$eindKoers=$DB->lookupRecord();
			$perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
			$perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
			$perf=$perfJaar-$perfVoorPeriode;
			
			if($this->pdf->debug==true)
			{
				echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
				echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
				echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
				echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
				echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
				echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
			}
			$totalPerf+=($perf*$percentage/100);
		}
		//echo "t $fonds $totalPerf $van,$tot<br>\n";
		
		return $totalPerf;
	}

}



?>