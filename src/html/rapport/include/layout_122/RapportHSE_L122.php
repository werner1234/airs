<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L122
{
	function RapportHSE_L122($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_HSE_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_HSE_titel;
		else
			$this->pdf->rapport_titel = "Huidige samenstelling effectenportefeuille";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];



		if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
		{
		  if(!empty($totaalA))
			  $totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal,true);//Koers
		  if(!empty($totaalB))
			  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			if(!empty($totaalA))
				$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		}
		else
		{
		  if(!empty($totaalA))
			  $totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_decimaal,true);
		  if(!empty($totaalB))
			  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			if(!empty($totaalA))
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		}


		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor['r'],$this->pdf->rapport_subtotaal_omschr_fontcolor['g'],$this->pdf->rapport_subtotaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_layout == 4)
		{
			$this->pdf->Cell($begin ,4, $title, 0,0, "R");
		}
		else
		{
			$this->pdf->Cell($begin,4, $title, 0,0, "R");
		}

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_fontcolor['r'],$this->pdf->rapport_subtotaal_fontcolor['g'],$this->pdf->rapport_subtotaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_subtotaal_fontstyle,$this->pdf->rapport_fontsize);


		if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
		{
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");


			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalAtxt, 0,0, "R");
			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	}

	function printSubTotaalLay12($totaalA, $totaalB, $totaalVb, $totaalVa)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		if(!empty($totaalA))
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_decimaal,true);

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

		if(!empty($totaalVb))
			$totaalVbtxt = $this->formatGetalKoers($totaalVb,$this->pdf->rapport_decimaal);

		if(!empty($totaalVa))
			$totaalVatxt = $this->formatGetalKoers($totaalVa,$this->pdf->rapport_decimaal,true);


		$this->pdf->SetY($this->pdf->GetY()-4);
		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->Cell($begin,4, $totaalVbtxt, 0,0, "R");



		// color + font

		if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
		{
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8],4,$totaalVatxt, 0,0, "R"); //rvv
			$this->pdf->Cell($this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");

			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalAtxt, 0,0, "R");



			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
	}

	function printTotaal($title, $totaalA, $totaalB, $procent, $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];

		// lege regel
		$this->pdf->ln();

		if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
		{
		  if(!empty($totaalA))
			  $totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_decimaal,true);//Koers
	    if(!empty($totaalB))
			  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			if(!empty($totaalA))
				$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
		}
		else
		{
		  if(!empty($totaalA))
			  $totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_decimaal,true);
	    if(!empty($totaalB))
			  $totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_decimaal);

			$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			if(!empty($totaalA))
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		}

		$this->pdf->SetX(0);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
		{
			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}
		else
		{
			$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[5],4,$totaalBtxt, 0,0, "R");
			$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalAtxt, 0,0, "R");
			if($this->pdf->rapport_inprocent == 1)
				$procenttxt = $this->formatGetal($procent,2)." %";
			$this->pdf->Cell($this->pdf->widthB[10],4,$procenttxt, 0,1, "R");
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
			if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
			{
				if(!empty($totaalA))
				{
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
			}
			else
			{
				if(!empty($totaalA))
				{
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
					$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
				}
				$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				$this->pdf->Line($begin,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
			}
		}
		else
		{
			$this->pdf->setDash(1,1);
			if($this->pdf->rapport_HSE_volgorde_beginwaarde == 1)
			{
				if(!empty($totaalA))
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
			}
			else
			{
				if(!empty($totaalA))
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			}
			$this->pdf->setDash();
		}

		$this->pdf->ln();

		return $totaalB;
	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();



		// voor data
	  $this->pdf->widthA = array(60,30,30,30,30,30,70);
		$this->pdf->alignA = array('L','R','R','R','R','R','R');
    
    
    // print categorie headers



		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->templateVars['HSEPaginas']=$this->pdf->page;
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
		// haal totaalwaarde op om % te berekenen
		$DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$query = "SELECT
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.valuta,
TijdelijkeRapportage.koersDatum,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.valutaOmschrijving,
TijdelijkeRapportage.fondsOmschrijving

FROM
	TijdelijkeRapportage
		 WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'".
		$__appvar['TijdelijkeRapportageMaakUniek'].
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    
    $verdeling=array();
    $subtotalen=array();
		while($subdata = $DB->NextRecord())
		{
		  $verdeling[$subdata['beleggingscategorieOmschrijving']][$subdata['valutaOmschrijving']][]=$subdata;
      $subtotalen[$subdata['beleggingscategorieOmschrijving']]['actuelePortefeuilleWaardeEuro']+=$subdata['actuelePortefeuilleWaardeEuro'];
		}

		foreach($verdeling as $beleggingscategorie=>$valutaData)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array($beleggingscategorie));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      foreach($valutaData as $valuta=>$fondsRegels)
      {
        $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
        $this->pdf->row(array("Waarden ".$valuta));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        foreach($fondsRegels as $fonds)
        {
          $percentage=$subdata['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
          $this->pdf->row(array("     ".$fonds['fondsOmschrijving'],
                              $this->formatAantal($fonds['totaalAantal'],0,$this->pdf->rapport_HSE_aantalVierDecimaal),
                              $this->formatGetal($fonds['actueleFonds'],2),
                              date('d-m-Y',db2jul($fonds['koersDatum'])),
                              $this->formatGetal($fonds['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_decimaal),
                              $this->formatGetal($fonds['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal),
                              ($this->pdf->rapport_inprocent)?$this->formatGetal($percentage*100,2)." %":""));
        }
      }
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders=array('','','','','',array('SUB'));
      $this->pdf->row(array("",'','','','subtotaal '.$beleggingscategorie,$this->formatGetal($subtotalen[$beleggingscategorie]['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_decimaal)));
      unset($this->pdf->CellBorders);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->ln();
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('','','','','',array('SUB'));
    $this->pdf->row(array("Totaal",'','','','',$this->formatGetal($totaalWaarde,$this->pdf->rapport_decimaal)));
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		

	}
}
?>