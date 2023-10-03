<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/03/04 19:21:42 $
File Versie					: $Revision: 1.1 $

$Log: RapportHSE_L24.php,v $
Revision 1.1  2017/03/04 19:21:42  rvv
*** empty log message ***

Revision 1.41  2014/12/21 13:24:42  rvv
*** empty log message ***

Revision 1.40  2012/03/14 17:29:35  rvv
*** empty log message ***

Revision 1.39  2012/01/15 11:03:37  rvv
*** empty log message ***

Revision 1.38  2011/12/24 16:36:57  rvv
*** empty log message ***

Revision 1.37  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.36  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.35  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.34  2010/09/15 16:27:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportHSE_L24
{
	function RapportHSE_L24($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
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

	  $query="SELECT Vermogensbeheerders.VerouderdeKoersDagen
    FROM Vermogensbeheerders Inner Join Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
    WHERE portefeuille = '".$this->portefeuille."' ";
		$DB->SQL($query);
		$DB->Query();
		$dagen = $DB->nextRecord();
    $maxDagenOud=$dagen['VerouderdeKoersDagen'];


		//Layout 10
		if (file_exists("./rapport/include/RapportHSE_L".$this->pdf->rapport_layout."_Kolom.php"))
		{
		   include("./rapport/include/RapportHSE_L".$this->pdf->rapport_layout."_Kolom.php");
		}
		else
		{
		// voor data
		if($this->pdf->rapport_layout == 4)
		  $this->pdf->widthB = array(10,55,20,15,30,15,30,20,30,30,20);
		else
		  $this->pdf->widthB = array(10,55,20,20,30,30,15,20,30,30,15);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(65,20,20,30,30,15,20,30,30,15);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R');
		}

		$this->pdf->AddPage();
    $this->pdf->templateVars['HSEPaginas']=$this->pdf->page;
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
		$totaalWaarde = $totaalWaarde[totaal];

		$actueleWaardePortefeuille = 0;

//		if ($this->pdf->rapport_layout == 12 && file_exists("./rapport/include/RapportHSE_L".$this->pdf->rapport_layout.".php"))
//		{
//		    include("./rapport/include/RapportHSE_L".$this->pdf->rapport_layout.".php");
//		}
//		else
//		{
		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
 		"IF (TijdelijkeRapportage.valuta = '".$this->pdf->rapportageValuta."',
    SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro / beginwaardeValutaLopendeJaar),
    SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin. ") as subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, ".
		" SUM(TijdelijkeRapportage.beginPortefeuillewaardeInValuta) AS subtotaalValutaBegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalValutaActueel, ".
		" ValutaOmschrijving".
		" FROM (TijdelijkeRapportage) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'".
		$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";

		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{

		  if($categorien['Valuta'] == $this->pdf->rapportageValuta)
			  $beginQuery = 'beginwaardeValutaLopendeJaar';
			else
			  $beginQuery = $this->pdf->ValutaKoersBegin;

		  $DB2 = new DB();

			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['BeleggingscategorieOmschrijving'] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);

				if($this->pdf->rapport_layout == 4 )
					$totaalbegin = 0;
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
			}

			if($lastCategorie <> $categorien['BeleggingscategorieOmschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['BeleggingscategorieOmschrijving'],$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			}
			// subkop (valuta)
		   $this->printKop(vertaalTekst($this->pdf->rapport_valuta_voorzet,$this->pdf->rapport_taal)." ".$categorien[valuta], $this->pdf->rapport_kop4_fontstyle);

			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro / $beginQuery as beginPortefeuilleWaardeEuro,
			TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			round((UNIX_TIMESTAMP(TijdelijkeRapportage.rapportageDatum) - UNIX_TIMESTAMP(TijdelijkeRapportage.koersDatum))/86400) as koersLeeftijd ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);

			$DB2->SQL($subquery);
			$DB2->Query();
			while($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving]);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

				$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

				if($this->pdf->rapport_layout == 32 && $subdata['koersLeeftijd'] > $maxDagenOud)
				  $markering="*";
			  else
				  $markering="";


						$this->pdf->row(array("",
													"",
													$this->formatAantal($subdata[totaalAantal],0,$this->pdf->rapport_HSE_aantalVierDecimaal),
													$this->formatGetal($subdata[beginwaardeLopendeJaar],2),
													$this->formatGetal($subdata[beginPortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($subdata[beginPortefeuilleWaardeEuro],$this->pdf->rapport_decimaal,true),
													"",
													$this->formatGetal($subdata['actueleFonds'],2).$markering,
													$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
													$this->formatGetalKoers($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
													($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));





				$valutaWaarden[$categorien[valuta]] = $subdata[actueleValuta];
				$valutaOmschrijving[$categorien[valuta]] = $categorien[ValutaOmschrijving];
			}

			// print categorie footers
			$percentageVanTotaal = $categorien[subtotaalactueel] / ($totaalWaarde/100);

				$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien[subtotaalbegin], $categorien[subtotaalactueel], $percentageVanTotaal);

			// totaal op categorie tellen
			$totaalbegin += $categorien[subtotaalbegin];
			$totaalactueel += $categorien[subtotaalactueel];

			$lastCategorie = $categorien['BeleggingscategorieOmschrijving'];
		}

		// totaal voor de laatste categorie
		$percentageVanTotaal 				 = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille  += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal);

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
		" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
		$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'],$this->pdf->rapport_kop4_fontstyle);

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien[valuta]."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
					$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata[rentedatum]));
							if($subdata[renteperiode] <> 12 && $subdata[renteperiode] <> 0)
								$rentePeriodetxt .= " / ".$subdata[renteperiode];
						}
						$subtotaalRenteInValuta += $subdata[actuelePortefeuilleWaardeEuro];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

						$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata[fondsOmschrijving].$rentePeriodetxt);

						$this->pdf->setX($this->pdf->marge);

						$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

						$percentageVanTotaal = $subdata[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);

						$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
														$this->formatGetalKoers($subdata[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
														($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));

					}

					// print subtotaal
					$percentageVanTotaal = $subtotaalRenteInValuta / ($totaalWaarde/100);
					$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), "", $subtotaalRenteInValuta, $percentageVanTotaal);

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien[subtotaalactueel];
				}

			}

			// totaal op rente
			$percentageVanTotaal = $totaalRenteInValuta / ($totaalWaarde/100);
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal Opgelopen rente",$this->pdf->rapport_taal), "", $totaalRenteInValuta,$percentageVanTotaal);
		}

		// Liquiditeiten
		$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.rekening , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille, Rekeningen.IBANnr  ".
			" FROM TijdelijkeRapportage 
			 JOIN Rekeningen on Rekeningen.rekening = TijdelijkeRapportage.rekening  AND Rekeningen.Portefeuille = TijdelijkeRapportage.portefeuille
			 WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{
			$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
			if($data['IBANnr'] <> '')
				$data['rekening']=$data['IBANnr']." (".$data['valuta'].")";
			$omschrijving = vertaalTekst(str_replace("{Rekening}",$data[rekening],$omschrijving),$this->pdf->rapport_taal);
			$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data[fondsOmschrijving],$this->pdf->rapport_taal),$omschrijving);

			$omschrijving = vertaalTekst(str_replace("{Valuta}",$data[valuta],$omschrijving),$this->pdf->rapport_taal);

			$totaalLiquiditeitenEuro += $data[actuelePortefeuilleWaardeEuro];

			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);

			// print fondsomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

			$percentageVanTotaal = $data[actuelePortefeuilleWaardeEuro] / ($totaalWaarde/100);


					$this->pdf->row(array("",
										"",
										"",
										"",
										"",
										"",
										"",
										"",
										$this->formatGetal($data[actuelePortefeuilleWaardeInValuta],$this->pdf->rapport_decimaal),
										$this->formatGetalKoers($data[actuelePortefeuilleWaardeEuro],$this->pdf->rapport_decimaal),
										($this->pdf->rapport_inprocent)?$this->formatGetal($percentageVanTotaal,2)." %":""));


		}
		// totaal liquiditeiten
		$percentageVanTotaal = $totaalLiquiditeitenEuro / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro, $percentageVanTotaal);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille,100,true);

		$this->pdf->ln();
	//	}

		  $kaderVolgorde = array('valutaoverzicht','rendement','AEX');

    foreach($kaderVolgorde as $key)
    {
      if($key == 'valutaoverzicht')
      {
		    if($this->pdf->rapport_HSE_valutaoverzicht == 1)
		    {
			   $this->pdf->ln();
			   // in PDFRapport.php
			   $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		    }
		    elseif($this->pdf->rapport_HSE_valutaoverzicht == 2)
		    {
			   $this->pdf->ln();
			   // in PDFRapport.php
			   if($this->pdf->rapport_layout == 25)
			     $omkeren=true;
			   else
			     $omkeren=false;
			   $this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,$omkeren);
		    }
      }
      if($key == 'rendement')
      {
		    if($this->pdf->rapport_HSE_rendement == 1)
		    {
			   $this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf,false,$this->pdf->rapportageValuta);
		    }
      }
      if($key == 'AEX')
      {
		    // index vergelijking afdrukken
		    if($this->pdf->portefeuilledata[AEXVergelijking] > 0)
		    {
		      if(!$this->pdf->rapport_HSE_geenIndex == 1)
			      $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata[Vermogensbeheerder], $this->rapportageDatumVanaf, $this->rapportageDatum);
		    }
      }
    }

	}
}
?>