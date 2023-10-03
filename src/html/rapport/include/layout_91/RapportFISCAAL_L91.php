<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportFISCAAL_L91
{
	function RapportFISCAAL_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "FISCAAL";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Fiscaal overzicht";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


		$this->pdf->excelData[]=array("Fondsomschrijving",'Aantal','Per stuk in valuta','Portefeuille in valuta',"Portefeuille in ".$this->pdf->rapportageValuta,'Per stuk in valuta','Portefeuille in valuta',"Portefeuille in ".$this->pdf->rapportageValuta,"Fiscale Waardering");


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
	       if ($decimaal != '0' && !isset($newDec))
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

	function printSubTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$begin = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		//$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10]+ $this->pdf->widthB[11];
		//$totaal5 = $totaal4 + $this->pdf->widthB[12];

		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];
    
    $extra=0;
		$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
		if(!empty($totaalA))
		{
			$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
		}

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);

		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}

		if(!empty($totaalD2))
		{
			$totaalD2txt = $this->formatGetal($totaalD2,$this->pdf->rapport_VHO_decimaal_proc)."%";

		}
    
    
    if(!empty($totaalB)&&!empty($totaalD))
    {
      $totaalBDtxt = $this->formatGetal($totaalB-$totaalD,$this->pdf->rapport_VHO_decimaal);
      $this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
      
    }

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}



		$this->pdf->SetX(0);
		$this->pdf->Cell($begin,4, $title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[12],4,$totaalD2txt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[15],4,'', 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[16],4,$totaalBDtxt, 0,1, "R");
	}

	function printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE, $totaalF=0, $grandtotaal=false)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
    $extra=0;
		$begin 	 = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$actueel = $begin + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8];
		$totaal4 = $actueel + $this->pdf->widthB[9]+ $this->pdf->widthB[10];
		$totaal5 = $totaal4 + $this->pdf->widthB[11] + $this->pdf->widthB[12];
    $totaal6 = $totaal5 + $this->pdf->widthB[13] + $this->pdf->widthB[14];


		if(!empty($totaalA))
		{
			$totaalAtxt = $this->formatGetal($totaalA,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());

		}
		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());

		}


		if(!empty($totaalC))
			$totaalCtxt = $this->formatGetal($totaalC,$this->pdf->rapport_VHO_decimaal_proc)."%";

		if(!empty($totaalD))
		{
			$totaalDtxt = $this->formatGetal($totaalD,$this->pdf->rapport_VHO_decimaal);
					if($this->pdf->rapport_layout != 14)
			$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
		}
    
    
    if(!empty($totaalB)&&!empty($totaalD))
    {
      $totaalBDtxt = $this->formatGetal($totaalB-$totaalD,$this->pdf->rapport_VHO_decimaal);
      $this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
    }

		if(!empty($totaalE))
		{
			$totaalEtxt = $this->formatGetal($totaalE,$this->pdf->rapport_VHO_decimaal);
			$this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
		}

		if(!empty($totaalF))
		{
			$totaalFtxt = $this->formatGetal($totaalF,$this->pdf->rapport_VHO_decimaal_proc);
		}


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($begin-$this->pdf->widthB[4],4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[4],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[6] + $this->pdf->widthB[7] + $this->pdf->widthB[8]+ $this->pdf->widthB[9],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[10],4,$totaalCtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[11],4,$totaalDtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[12],4,"", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[13],4,$totaalEtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[14],4,$totaalFtxt, 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[15],4,'', 0,0, "R");
    $this->pdf->Cell($this->pdf->widthB[16],4,$totaalBDtxt, 0,1, "R");
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
				if(!empty($totaalB))
				{
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
					$this->pdf->Line($actueel+$extra,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[9],$this->pdf->GetY()+1);
				}
				if(!empty($totaalA))
				{
					$this->pdf->Line($begin+$extra,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
					$this->pdf->Line($begin+$extra,$this->pdf->GetY()+1,$begin + $this->pdf->widthB[5],$this->pdf->GetY()+1);
				}
				if(!empty($totaalD))
				{
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
					$this->pdf->Line($totaal4+$extra,$this->pdf->GetY()+1,$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY()+1);
				}
      if(!empty($totaalB)&&!empty($totaalD))
        {
          $this->pdf->Line($totaal5+$extra,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
          $this->pdf->Line($totaal5+$extra,$this->pdf->GetY()+1,$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY()+1);
        }
		}
		else
		{
			$this->pdf->setDash(1,1);
				if(!empty($totaalB))
					$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[9],$this->pdf->GetY());
				if(!empty($totaalA))
					$this->pdf->Line($begin,$this->pdf->GetY(),$begin + $this->pdf->widthB[5],$this->pdf->GetY());
				if(!empty($totaalD))
					$this->pdf->Line($totaal4,$this->pdf->GetY(),$totaal4 + $this->pdf->widthB[11],$this->pdf->GetY());
      if(!empty($totaalB)&&!empty($totaalD))
          $this->pdf->Line($totaal5,$this->pdf->GetY(),$totaal5 + $this->pdf->widthB[13],$this->pdf->GetY());
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
	//	$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();
    
    
    $this->pdf->widthB = array(10,50,18,20,23,23,10,24,24,23,5,23,5,23);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');



		$this->pdf->AddPage();
    //$this->pdf->templateVars['FISCAALPaginas']=$this->pdf->page;
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
    
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];

		$actueleWaardePortefeuille = 0;
    $totaalhistorisch = 0;
    $totaalactueel = 0;
    $totaalfiscaleWaardering=0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid * TijdelijkeRapportage.historischeValutakoers / TijdelijkeRapportage.historischeRapportageValutakoers) AS subtotaalhistorisch, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." AS subtotaalactueel ".
		" FROM TijdelijkeRapportage ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.type = 'fondsen' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'" .$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

    $fiscaleWaardeEffecten=0;
    $subtotaal=array();
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			// print totaal op hele categorie.
			if(!empty($lastCategorie) && $lastCategorie <> $categorien['Omschrijving'])
			{

					$percentageVanTotaal = "";
			

				$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				if($totaalhistorisch < 0)
					$procentResultaat = -1 * $procentResultaat;
				// attica ?
				//$procentResultaat = ($totaalvalutaresultaat / $totaalhistorisch) *100;

				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal);
				//function $this->printTotaal($title, $totaalA, $totaalB, $totaalC, $totaalD, $totaalE)
			  $actueleWaardePortefeuille += $this->printTotaal($title, $totaalhistorisch, $totaalactueel,'', $totaalfiscaleWaardering ,'','');

				$totaalhistorisch = 0;
				$totaalactueel = 0;
				$totaalvalutaresultaat = 0;
        $totaalfiscaleWaardering=0;
				$totaalGecombeneerdResultaat =0;
			}

			if($lastCategorie <> $categorien['Omschrijving'])
			{
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal), "bi");
			}
			// subkop (valuta)

				$tekst = vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'];
				$this->printKop($tekst, "");
		

			// print detail (select from tijdelijkeRapportage)
			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."' AND ".
			" TijdelijkeRapportage.type =  'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery); //echo $subquery.'<br><br>';exit();
			$DB2->Query();

			while($subdata = $DB2->NextRecord())
			{
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['historischeWaardeTotaal']) * 100;

				if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta']) / ($subdata['historischeWaardeTotaalValuta'] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);


				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
			

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
				$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving']);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);
				$percentageTotaalTekst = "";
        
        $fiscaleWaardering=min($subdata['historischeWaardeTotaalValuta'],$subdata['actuelePortefeuilleWaardeEuro']);

				  $this->pdf->row(array("",
												"",
												$this->formatAantal($subdata['totaalAantal'],0,$this->pdf->rapport_VHO_aantalVierDecimaal),
												$this->formatGetal($subdata['historischeWaarde'],2),
												$this->formatGetal($subdata['historischeWaardeTotaal'],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($subdata['historischeWaardeTotaalValuta'],$this->pdf->rapport_VHO_decimaal),
												"",
												$this->formatGetal($subdata['actueleFonds'],2),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
                        '',
												$this->formatGetal($fiscaleWaardering,$this->pdf->rapport_VHO_decimaal),
												'',
                        $this->formatGetal($subdata['actuelePortefeuilleWaardeEuro']-$fiscaleWaardering,$this->pdf->rapport_VHO_decimaal),
													)
												);
				$this->pdf->excelData[]=array($subdata['fondsOmschrijving'],
					round($subdata['totaalAantal'],6),
					round($subdata['historischeWaarde'],2),
					round($subdata['historischeWaardeTotaal'],0),
					round($subdata['historischeWaardeTotaalValuta'],0),
					round($subdata['actueleFonds'],2),
					round($subdata['actuelePortefeuilleWaardeInValuta'],0),
					round($subdata['actuelePortefeuilleWaardeEuro'],0),
	  			round($fiscaleWaardering,0),
					round($subdata['actuelePortefeuilleWaardeEuro']-$fiscaleWaardering));
			

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];

				$subtotaal['fiscaleWaardering'] = $subtotaal['fiscaleWaardering'] + $fiscaleWaardering;
        $fiscaleWaardeEffecten+=$fiscaleWaardering;
				$subtotaal['valutaResultaat'] = $subtotaal['valutaResultaat'] + $valutaResultaat;
				$subtotaal['gecombeneerdResultaat'] += $gecombeneerdResultaat;
			}


			$percentageVanTotaal = "";
			$procentResultaat = (($categorien['subtotaalactueel'] - $categorien['subtotaalhistorisch']) / ($categorien['subtotaalhistorisch'] /100));
			if($categorien['subtotaalhistorisch'] < 0)
				$procentResultaat = -1 * $procentResultaat;

      $this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal), $categorien['subtotaalhistorisch'], $categorien['subtotaalactueel'],'', $subtotaal['fiscaleWaardering']);
			// totaal op categorie tellen
			$totaalhistorisch += $categorien['subtotaalhistorisch'];
			$totaalactueel += $categorien['subtotaalactueel'];

			$totaalfiscaleWaardering += $subtotaal['fiscaleWaardering'];
			$totaalvalutaresultaat += $subtotaal['valutaResultaat'];

		  $totaalGecombeneerdResultaat += $subtotaal['gecombeneerdResultaat'];
			$lastCategorie = $categorien['Omschrijving'];
			$subtotaal = array();
		}

		// totaal voor de laatste categorie
		$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
		if($totaalhistorisch < 0)
			$procentResultaat = -1 * $procentResultaat;
		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorie,$this->pdf->rapport_taal), $totaalhistorisch, $totaalactueel,'',$totaalfiscaleWaardering,'' , '');
    $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal)." ".vertaalTekst("Fiscale waarde effecten",$this->pdf->rapport_taal),  '',  '','',$fiscaleWaardeEffecten,'' , '');

		// selecteer rente
		$query = "SELECT TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersStart." subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/".$this->pdf->ValutaKoersEind." subtotaalactueel FROM ".
		" TijdelijkeRapportage  ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND  ".
		" TijdelijkeRapportage.type = 'rente'  AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.valuta ".
		" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{

			$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),"bi");

			$totaalRenteInValuta = 0 ;

			while($categorien = $DB->NextRecord())
			{
				if(!$this->pdf->rapport_HSE_geenrentespec)
				{
					$subtotaalRenteInValuta = 0;
					$subtotaalPercentageVanTotaal = 0;

					$this->printKop(vertaalTekst("Waarden",$this->pdf->rapport_taal)." ".$categorien['valuta'],"");

					// print detail (select from tijdelijkeRapportage)

					$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.rentedatum, ".
					" TijdelijkeRapportage.renteperiode, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.valuta =  '".$categorien['valuta']."'".
					" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.fondsOmschrijving asc";
					debugSpecial($subquery,__FILE__,__LINE__);
					$DB2 = new DB();
					$DB2->SQL($subquery);
					$DB2->Query();
					while($subdata = $DB2->NextRecord())
					{

						if($this->pdf->rapport_HSE_rentePeriode)
						{
							$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['rentedatum']));
							if($subdata['renteperiode'] <> 12 && $subdata['renteperiode'] <> 0)
								$rentePeriodetxt .= " / ".$subdata['renteperiode'];
						}

						$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($totaalWaarde/100);

						$percentageTotaalTekst = "";



						$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];

						$this->pdf->SetWidths($this->pdf->widthB);
						$this->pdf->SetAligns($this->pdf->alignB);

						// print fondsomschrijving appart ivm met apparte fontkleur
						$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
					//	$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
						$this->pdf->setX($this->pdf->marge);

						$this->pdf->Cell($this->pdf->widthB[0],4,"");
						$this->pdf->Cell($this->pdf->widthB[1],4,$subdata['fondsOmschrijving'].$rentePeriodetxt );

						$this->pdf->setX($this->pdf->marge);

				//		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
						$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

						$this->pdf->row(array("","","","","","","","",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
														$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
														$percentageTotaalTekst));
				
		
					}

						$percentageVanTotaal = 0;
						$this->printSubTotaal(vertaalTekst("Subtotaal:",$this->pdf->rapport_taal),"", $subtotaalRenteInValuta, $percentageVanTotaal, "", "");

					$totaalRenteInValuta += $subtotaalRenteInValuta;
				}
				else
				{
					$totaalRenteInValuta += $categorien['subtotaalactueel'];
				}
			}

			$percentageVanTotaal = 0;

			$actueleWaardePortefeuille 		+= $this->printTotaal(vertaalTekst("Subtotaal Opgelopen rente:",$this->pdf->rapport_taal),"", $totaalRenteInValuta, $percentageVanTotaal,"");
		}

		// Liquiditeiten

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." AS actuelePortefeuilleWaardeEuro , ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.fondsOmschrijving, TijdelijkeRapportage.valuta asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();
    
    $totaalLiquiditeitenEuro=0;
		if($DB1->records() >0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),"bi");
			$totaalLiquiditeitenInValuta = 0;

			while($data = $DB1->NextRecord())
			{

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				$percentageVanTotaalTekst = "";

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				// print fondsomschrijving appart ivm met apparte fontkleur
				$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		//		$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				$this->pdf->setX($this->pdf->marge);

				$this->pdf->Cell($this->pdf->widthB[0],4,"");
				$this->pdf->Cell($this->pdf->widthB[1],4,$omschrijving);

				$this->pdf->setX($this->pdf->marge);

				$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


										  $this->pdf->row(array("",
												"",
												"",
												"",
												"",
												"",
												"",
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_VHO_decimaal),
												$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal),
												$percentageVanTotaalTekst));
			
			}
		}



			$percentageVanTotaal = 0;

		// totaal liquiditeiten
		$actueleWaardePortefeuille += $this->printTotaal("", "", $totaalLiquiditeitenEuro,$percentageVanTotaal,"","");


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			  alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		if($this->pdf->rapport_VHO_percentageTotaal ==1)
		{
			$percentageVanTotaal = 100;
		}
		else
			$percentageVanTotaal = 0;


		// print grandtotaal
		$this->printTotaal(vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille, $percentageVanTotaal,"","","",true);


		$this->pdf->ln();

		if($this->pdf->rapport_VHO_valutaoverzicht == 1)
		{
	//		$this->pdf->ln();
			// in PDFRapport.php
	//		$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_VHO_valutaoverzicht == 2)
		{
			$this->pdf->ln();
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}


		if($this->pdf->rapport_VHO_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		// index vergelijking afdrukken
		if($this->pdf->portefeuilledata['AEXVergelijking'] > 0 && $this->pdf->rapport_VHO_indexUit == 0)
		{
		  if(!$this->pdf->rapport_VHO_geenIndex)
			  $this->pdf->printAEXVergelijking($this->pdf->portefeuilledata['Vermogensbeheerder'], $this->rapportageDatumVanaf, $this->rapportageDatum);
		}

	}
}
?>
